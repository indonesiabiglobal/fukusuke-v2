<?php

namespace App\Http\Livewire\Report\GeneralReport;

use App\Helpers\phpspreadsheet;
use App\Models\MsJamMatiMesin;
use App\Models\MsMachine;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ProduksiPerTypeReportService
{
    public static function daftarProduksiPerTipeInfure($nippon, $jenisReport, $tglMasuk, $tglKeluar)
    {
        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();
        $activeWorksheet->setShowGridlines(false);
        $activeWorksheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        // Mengatur ukuran kertas menjadi A4
        $activeWorksheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
        // Mengatur agar semua kolom muat dalam satu halaman
        $activeWorksheet->getPageSetup()->setFitToWidth(1);
        $activeWorksheet->getPageSetup()->setFitToHeight(0); // Biarkan tinggi menyesuaikan otomatis
        // Set header berulang untuk print
        $activeWorksheet->getPageSetup()->setRowsToRepeatAtTop([1, 3]);

        // Jika ingin memastikan rasio tetap terjaga
        $activeWorksheet->getPageSetup()->setFitToPage(true);
        $activeWorksheet->freezePane('D4');

        // Mengatur margin halaman menjadi 0.75 cm di semua sisi
        $activeWorksheet->getPageMargins()->setTop(1.1 / 2.54);
        $activeWorksheet->getPageMargins()->setBottom(1.0 / 2.54);
        $activeWorksheet->getPageMargins()->setLeft(0.75 / 2.54);
        $activeWorksheet->getPageMargins()->setRight(0.75 / 2.54);
        $activeWorksheet->getPageMargins()->setHeader(0.4 / 2.54);
        $activeWorksheet->getPageMargins()->setFooter(0.5 / 2.54);
        // Mengatur tinggi sel agar otomatis menyesuaikan dengan konten
        $activeWorksheet->getDefaultRowDimension()->setRowHeight(-1);

        // Header yang hanya muncul saat print
        $activeWorksheet->getHeaderFooter()->setOddHeader('&L&"Calibri,Bold"&14Fukusuke - Production Control');
        // Footer
        $currentDate = date('d M Y - H:i');
        $footerLeft = '&L&"Calibri"&10Printed: ' . $currentDate . ', by: ' . auth()->user()->username;
        $footerRight = '&R&"Calibri"&10Page: &P of: &N';
        $activeWorksheet->getHeaderFooter()->setOddFooter($footerLeft . $footerRight);

        // Judul
        $activeWorksheet->setCellValue('B1', 'DAFTAR PRODUKSI PER TIPE INFURE');
        $activeWorksheet->setCellValue('B2', 'Periode : ' . $tglMasuk->format('d-M-Y H:i') . '  ~  ' . $tglKeluar->format('d-M-Y H:i'));
        // Style Judul
        phpspreadsheet::styleFont($spreadsheet, 'B1:B2', true, 11, 'Calibri');
        // hide column A
        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setVisible(false);

        // Header
        $columnJenisProduk = 'B';
        $columnJenisProdukEnd = 'C';
        $spreadsheet->getActiveSheet()->mergeCells($columnJenisProduk . '3:' . $columnJenisProdukEnd . '3');
        $activeWorksheet->setCellValue('B3', 'Type Produk');

        // header
        $rowHeaderStart = 3;
        $columnHeaderStart = 'D';
        $columnHeaderEnd = 'D';
        $header = [
            'Berat Standar (Kg)',
            'Berat Produksi (Kg)',
            'Weight Rate',
            'Infure Cost',
            'Loss (Kg)',
            'Loss (%)',
            'Panjang Infure (meter)',
            'Panjang Inline Printing (meter)',
            'Inline Printing Cost',
            'Process Cost',
        ];

        foreach ($header as $key => $value) {
            $activeWorksheet->setCellValue($columnHeaderEnd . $rowHeaderStart, $value);
            $columnHeaderEnd++;
        }
        $columnHeaderEnd = chr(ord($columnHeaderEnd) - 1);

        // style header
        phpspreadsheet::addFullBorder($spreadsheet, $columnJenisProduk . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);
        phpspreadsheet::styleFont($spreadsheet, $columnJenisProduk . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart, true, 9, 'Calibri');
        phpspreadsheet::textAlignCenter($spreadsheet, $columnJenisProduk . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);
        $activeWorksheet->getStyle($columnJenisProduk . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart)->getAlignment()->setWrapText(true);

        $data = DB::select("
            SELECT
                max(prTip.code) AS product_type_code,
                max(prTip.name) AS product_type_name,
                SUM(asy.berat_standard) AS berat_standard,
                SUM(asy.berat_produksi) AS berat_produksi,
                SUM(asy.infure_cost) AS infure_cost,
                SUM(asy.infure_berat_loss) AS infure_berat_loss,
                SUM(asy.panjang_produksi) AS panjang_produksi,
                SUM(asy.panjang_printing_inline) AS panjang_printing_inline,
                SUM(asy.infure_cost_printing) AS infure_cost_printing
            FROM tdProduct_Assembly AS asy
            INNER JOIN msProduct AS prd ON asy.product_id = prd.id
            INNER JOIN msProduct_type AS prTip ON prd.product_type_id = prTip.id
            WHERE asy.production_date BETWEEN '$tglMasuk' AND '$tglKeluar'
            GROUP BY prTip.id
            ORDER BY product_type_code
        ");

        if (count($data) == 0) {
            $response = [
                'status' => 'error',
                'message' => "Data pada periode tanggal tersebut tidak ditemukan"
            ];

            return $response;
        }

        // list jenis produk
        $listProductGroup = array_reduce($data, function ($carry, $item) {
            $carry[$item->product_type_code] = [
                'product_type_code' => $item->product_type_code,
                'product_type_name' => $item->product_type_name
            ];
            return $carry;
        }, []);

        $dataFilter = array_reduce($data, function ($carry, $item) {
            $carry[$item->product_type_code] = $item;
            return $carry;
        }, []);

        // index
        $startColumnItem = 'B';
        $endColumnItem = $columnHeaderEnd;
        $startColumnItemData = 'D';
        $columnProductGroupCode = 'B';
        $columnProductGroupName = 'C';
        $columnBeratStandard = 'D';
        $columnBeratProduksi = 'E';
        $startRowItem = 4;
        $rowItem = $startRowItem;
        // daftar departemen
        foreach ($listProductGroup as $productGroupCode => $productGroup) {
            if ($dataFilter[$productGroupCode] == null) {
                continue;
            }
            // daftar mesin
            $columnItem = $startColumnItemData;

            // Menulis data mesin
            $spreadsheet->getActiveSheet()->setCellValue($columnProductGroupCode . $rowItem, $productGroup['product_type_code']);
            $spreadsheet->getActiveSheet()->setCellValue($columnProductGroupName . $rowItem, $productGroup['product_type_name']);
            // phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);

            // memasukkan data
            $dataItem = $dataFilter[$productGroupCode];
            $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_standard);
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_produksi);
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_standard > 0 ? '=' . $columnBeratProduksi . $rowItem . '/' . $columnBeratStandard . $rowItem : 0);
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem, 3);
            $columnItem++;
            $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->infure_cost);
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->infure_berat_loss);
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_produksi > 0 ? $dataItem->infure_berat_loss / $dataItem->berat_produksi * 100 : 0);
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem, 1);
            $columnItem++;
            $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->panjang_produksi);
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->panjang_printing_inline);
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->infure_cost_printing);
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->infure_cost + $dataItem->infure_cost_printing);
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);

            phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem, false, 8, 'Calibri');
            $rowItem++;
        }
        phpspreadsheet::addBorderDottedMiddleHorizontal($spreadsheet, $startColumnItem . $startRowItem . ':' . $columnItem . $rowItem);

        // Grand total
        $rowGrandTotal = $rowItem;
        $spreadsheet->getActiveSheet()->mergeCells($columnProductGroupCode . $rowGrandTotal . ':' . $columnProductGroupName . $rowGrandTotal);
        $spreadsheet->getActiveSheet()->setCellValue($columnProductGroupCode . $rowGrandTotal, 'GRAND TOTAL');
        phpspreadsheet::styleFont($spreadsheet, $columnProductGroupCode . $rowGrandTotal . ':' . $columnHeaderEnd . $rowGrandTotal, true, 8, 'Calibri');
        // $this->addFullBorder($spreadsheet, $columnProductGroupCode . $rowGrandTotal . ':' . $columnValueAvg . $rowGrandTotal);

        $grandTotal = array_reduce(array_keys($dataFilter), function ($carry, $item) use ($dataFilter) {
            $dataItem = $dataFilter[$item] ?? (object)[
                'berat_standard' => 0,
                'berat_produksi' => 0,
                'infure_cost' => 0,
                'infure_berat_loss' => 0,
                'panjang_produksi' => 0,
                'panjang_printing_inline' => 0,
                'infure_cost_printing' => 0,
            ];
            $carry['berat_standard'] += $dataItem->berat_standard;
            $carry['berat_produksi'] += $dataItem->berat_produksi;
            $carry['infure_cost'] += $dataItem->infure_cost;
            $carry['infure_berat_loss'] += $dataItem->infure_berat_loss;
            $carry['panjang_produksi'] += $dataItem->panjang_produksi;
            $carry['panjang_printing_inline'] += $dataItem->panjang_printing_inline;
            $carry['infure_cost_printing'] += $dataItem->infure_cost_printing;
            return $carry;
        }, [
            'berat_standard' => 0,
            'berat_produksi' => 0,
            'infure_cost' => 0,
            'infure_berat_loss' => 0,
            'panjang_produksi' => 0,
            'panjang_printing_inline' => 0,
            'infure_cost_printing' => 0,
        ]);

        $columnItem = $startColumnItemData;
        $columnItemEnd = chr(ord($columnItem) + count($header) - 1);
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_standard']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_produksi']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_standard'] > 0 ? $grandTotal['berat_produksi'] / $grandTotal['berat_standard'] : 0);
        phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['infure_cost']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['infure_berat_loss']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_produksi'] > 0 ? $grandTotal['infure_berat_loss'] / $grandTotal['berat_produksi'] : 0);
        phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['panjang_produksi']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['panjang_printing_inline']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['infure_cost_printing']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['infure_cost'] + $grandTotal['infure_cost_printing']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
        phpspreadsheet::addFullBorder($spreadsheet, $columnProductGroupCode . $rowGrandTotal . ':' . $columnItem . $rowGrandTotal);
        $columnItem++;

        // size auto - mengatur lebar kolom sesuai konten
        $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(10); // Type code
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(25); // Type name

        // Mengatur lebar kolom data berdasarkan header
        $columnWidths = [12, 12, 12, 12, 12, 12, 12, 12, 12, 12]; // Lebar untuk setiap kolom data
        $tempColumn = $startColumnItemData;
        foreach ($columnWidths as $width) {
            if (ord($tempColumn) <= ord($endColumnItem)) {
                $spreadsheet->getActiveSheet()->getColumnDimension($tempColumn)->setWidth($width);
                $tempColumn++;
            }
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'asset/report/' . $nippon . '-' . $jenisReport . '.xlsx';
        $writer->save($filename);
        $response = [
            'status' => 'success',
            'filename' => $filename
        ];
        return $response;
    }

    public static function daftarProduksiPerTipeSeitai($nippon, $jenisReport, $tglMasuk, $tglKeluar)
    {
        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();
        $activeWorksheet->setShowGridlines(false);
        $activeWorksheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        // Mengatur ukuran kertas menjadi A4
        $activeWorksheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
        // Mengatur agar semua kolom muat dalam satu halaman
        $activeWorksheet->getPageSetup()->setFitToWidth(1);
        $activeWorksheet->getPageSetup()->setFitToHeight(0); // Biarkan tinggi menyesuaikan otomatis
        // Set header berulang untuk print
        $activeWorksheet->getPageSetup()->setRowsToRepeatAtTop([1, 3]);

        // Jika ingin memastikan rasio tetap terjaga
        $activeWorksheet->getPageSetup()->setFitToPage(true);
        $activeWorksheet->freezePane('D4');

        // Mengatur margin halaman menjadi 0.75 cm di semua sisi
        $activeWorksheet->getPageMargins()->setTop(1.1 / 2.54);
        $activeWorksheet->getPageMargins()->setBottom(1.0 / 2.54);
        $activeWorksheet->getPageMargins()->setLeft(0.75 / 2.54);
        $activeWorksheet->getPageMargins()->setRight(0.75 / 2.54);
        $activeWorksheet->getPageMargins()->setHeader(0.4 / 2.54);
        $activeWorksheet->getPageMargins()->setFooter(0.5 / 2.54);
        // Mengatur tinggi sel agar otomatis menyesuaikan dengan konten
        $activeWorksheet->getDefaultRowDimension()->setRowHeight(-1);

        // Header yang hanya muncul saat print
        $activeWorksheet->getHeaderFooter()->setOddHeader('&L&"Calibri,Bold"&14Fukusuke - Production Control');
        // Footer
        $currentDate = date('d M Y - H:i');
        $footerLeft = '&L&"Calibri"&10Printed: ' . $currentDate . ', by: ' . auth()->user()->username;
        $footerRight = '&R&"Calibri"&10Page: &P of: &N';
        $activeWorksheet->getHeaderFooter()->setOddFooter($footerLeft . $footerRight);

        // Judul
        $activeWorksheet->setCellValue('B1', 'DAFTAR PRODUKSI PER TIPE SEITAI');
        $activeWorksheet->setCellValue('B2', 'Periode : ' . $tglMasuk->format('d-M-Y H:i') . '  ~  ' . $tglKeluar->format('d-M-Y H:i'));
        // Style Judul
        phpspreadsheet::styleFont($spreadsheet, 'B1:B2', true, 11, 'Calibri');
        // hide column A
        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setVisible(false);

        // Header
        $columnJenisProduk = 'B';
        $columnJenisProdukEnd = 'C';
        $spreadsheet->getActiveSheet()->mergeCells($columnJenisProduk . '3:' . $columnJenisProdukEnd . '3');
        $activeWorksheet->setCellValue('B3', 'Type Produk');

        // header
        $rowHeaderStart = 3;
        $columnHeaderStart = 'D';
        $columnHeaderEnd = 'D';
        $header = [
            'Jumlah Produksi (Lembar)',
            'Berat Produksi (Kg)',
            'Loss (Kg)',
            'Loss (%)',
            'Seitai Cost',
            'Loss Ponsu (Kg)',
            'Infure Loss (Kg)',
        ];

        foreach ($header as $key => $value) {
            $activeWorksheet->setCellValue($columnHeaderEnd . $rowHeaderStart, $value);
            $columnHeaderEnd++;
        }
        $columnHeaderEnd = chr(ord($columnHeaderEnd) - 1);

        // style header
        phpspreadsheet::addFullBorder($spreadsheet, $columnJenisProduk . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);
        phpspreadsheet::styleFont($spreadsheet, $columnJenisProduk . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart, true, 9, 'Calibri');
        phpspreadsheet::textAlignCenter($spreadsheet, $columnJenisProduk . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);
        $activeWorksheet->getStyle($columnJenisProduk . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart)->getAlignment()->setWrapText(true);

        $data = DB::select("
            SELECT
                MAX(prT.code) AS product_type_code,
                MAX(prT.name) AS product_type_name,
                SUM(good.qty_produksi) AS qty_produksi,
                SUM(good.qty_produksi * prd.unit_weight * 0.001) AS berat_produksi,
                SUM(good.qty_produksi * prT.harga_sat_seitai) AS seitai_cost,
                SUM(good.seitai_berat_loss) - COALESCE(SUM(ponsu.berat_loss), 0) AS seitai_berat_loss,
                COALESCE(SUM(ponsu.berat_loss), 0) AS seitai_berat_loss_ponsu,
                SUM(good.infure_berat_loss) AS infure_berat_loss
            FROM tdProduct_Goods AS good
            LEFT JOIN (
                SELECT
                    los_.product_goods_id,
                    SUM(los_.berat_loss) AS berat_loss
                FROM tdProduct_Goods_Loss AS los_
                WHERE los_.loss_seitai_id = 1 -- ponsu
                GROUP BY los_.product_goods_id
            ) ponsu ON good.id = ponsu.product_goods_id
            INNER JOIN msProduct AS prd ON good.product_id = prd.id
            INNER JOIN msProduct_type AS prT ON prd.product_type_id = prT.id
            WHERE good.production_date BETWEEN '$tglMasuk' AND '$tglKeluar'
            GROUP BY prT.id
            ORDER BY product_type_code
        ");

        if (count($data) == 0) {
            $response = [
                'status' => 'error',
                'message' => "Data pada periode tanggal tersebut tidak ditemukan"
            ];

            return $response;
        }

        // list jenis produk
        $listProductGroup = array_reduce($data, function ($carry, $item) {
            $carry[$item->product_type_code] = [
                'product_type_code' => $item->product_type_code,
                'product_type_name' => $item->product_type_name
            ];
            return $carry;
        }, []);

        $dataFilter = array_reduce($data, function ($carry, $item) {
            $carry[$item->product_type_code] = $item;
            return $carry;
        }, []);

        // index
        $startColumnItem = 'B';
        $endColumnItem = $columnHeaderEnd;
        $startColumnItemData = 'D';
        $columnProductGroupCode = 'B';
        $columnProductGroupName = 'C';
        $startRowItem = 4;
        $rowItem = $startRowItem;
        // daftar departemen
        foreach ($listProductGroup as $productGroupCode => $productGroup) {
            if ($dataFilter[$productGroupCode] == null) {
                continue;
            }
            // daftar mesin
            $columnItem = $startColumnItemData;

            // Menulis data mesin
            $spreadsheet->getActiveSheet()->setCellValue($columnProductGroupCode . $rowItem, $productGroup['product_type_code']);
            $spreadsheet->getActiveSheet()->setCellValue($columnProductGroupName . $rowItem, $productGroup['product_type_name']);
            // phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);

            // memasukkan data
            $dataItem = $dataFilter[$productGroupCode];
            // jumlah produksi
            $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->qty_produksi);
            phpspreadsheet::numberFormatThousands($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // berat_produksi
            $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_produksi);
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // seitai_berat_loss
            $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->seitai_berat_loss);
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // seitai loss %
            $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_produksi > 0 ? $dataItem->seitai_berat_loss / $dataItem->berat_produksi : 0);
            phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // seitai_cost
            $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->seitai_cost);
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // seitai_berat_loss_ponsu
            $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->seitai_berat_loss_ponsu);
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // infure_berat_loss
            $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->infure_berat_loss);
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);

            phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem, false, 8, 'Calibri');
            $rowItem++;
        }
        phpspreadsheet::addBorderDottedMiddleHorizontal($spreadsheet, $startColumnItem . $startRowItem . ':' . $columnItem . $rowItem);

        // Grand total
        $rowGrandTotal = $rowItem;
        $spreadsheet->getActiveSheet()->mergeCells($columnProductGroupCode . $rowGrandTotal . ':' . $columnProductGroupName . $rowGrandTotal);
        $spreadsheet->getActiveSheet()->setCellValue($columnProductGroupCode . $rowGrandTotal, 'GRAND TOTAL');
        phpspreadsheet::styleFont($spreadsheet, $columnProductGroupCode . $rowGrandTotal . ':' . $columnHeaderEnd . $rowGrandTotal, true, 8, 'Calibri');
        // $this->addFullBorder($spreadsheet, $columnProductGroupCode . $rowGrandTotal . ':' . $columnValueAvg . $rowGrandTotal);

        $grandTotal = array_reduce(array_keys($dataFilter), function ($carry, $item) use ($dataFilter) {
            $dataItem = $dataFilter[$item] ?? (object)[
                'qty_produksi' => 0,
                'berat_produksi' => 0,
                'seitai_cost' => 0,
                'seitai_berat_loss' => 0,
                'seitai_berat_loss_ponsu' => 0,
                'infure_berat_loss' => 0
            ];
            $carry['qty_produksi'] += $dataItem->qty_produksi;
            $carry['berat_produksi'] += $dataItem->berat_produksi;
            $carry['seitai_cost'] += $dataItem->seitai_cost;
            $carry['seitai_berat_loss'] += $dataItem->seitai_berat_loss;
            $carry['seitai_berat_loss_ponsu'] += $dataItem->seitai_berat_loss_ponsu;
            $carry['infure_berat_loss'] += $dataItem->infure_berat_loss;
            return $carry;
        }, [
            'qty_produksi' => 0,
            'berat_produksi' => 0,
            'seitai_cost' => 0,
            'seitai_berat_loss' => 0,
            'seitai_berat_loss_ponsu' => 0,
            'infure_berat_loss' => 0
        ]);


        $columnItem = $startColumnItemData;
        $columnItemEnd = chr(ord($columnItem) + count($header) - 1);
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['qty_produksi']);
        phpspreadsheet::numberFormatThousands($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_produksi']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['seitai_berat_loss']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // seitai loss %
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_produksi'] > 0 ? $grandTotal['seitai_berat_loss'] / $grandTotal['berat_produksi'] : 0);
        phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['seitai_cost']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['seitai_berat_loss_ponsu']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['infure_berat_loss']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        phpspreadsheet::addFullBorder($spreadsheet, $columnProductGroupCode . $rowGrandTotal . ':' . $columnItem . $rowGrandTotal);
        $columnItem++;


        // size auto - mengatur lebar kolom sesuai konten
        $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(10); // Type code
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(25); // Type name

        // Mengatur lebar kolom data berdasarkan header Seitai
        $columnWidths = [20, 15, 12, 10, 15, 15, 15]; // Lebar untuk setiap kolom data Seitai
        $tempColumn = $startColumnItemData;
        foreach ($columnWidths as $width) {
            if (ord($tempColumn) <= ord($endColumnItem)) {
                $spreadsheet->getActiveSheet()->getColumnDimension($tempColumn)->setWidth($width);
                $tempColumn++;
            }
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'asset/report/' . $nippon . '-' . $jenisReport . '.xlsx';
        $writer->save($filename);
        $response = [
            'status' => 'success',
            'filename' => $filename
        ];
        return $response;
    }
}
