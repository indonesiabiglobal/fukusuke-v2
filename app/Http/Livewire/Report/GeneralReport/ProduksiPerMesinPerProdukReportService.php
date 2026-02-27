<?php

namespace App\Http\Livewire\Report\GeneralReport;

use App\Helpers\phpspreadsheet;
use App\Models\MsJamMatiMesin;
use App\Models\MsMachine;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ProduksiPerMesinPerProdukReportService
{
    public static function daftarProduksiPerMesinPerProdukInfure($nippon, $jenisReport, $tglMasuk, $tglKeluar)
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
        $activeWorksheet->setCellValue('A1', 'DAFTAR PRODUKSI PER MESIN PER PRODUK INFURE');
        $activeWorksheet->setCellValue('A2', 'Periode : ' . $tglMasuk->format('d-M-Y H:i') . '  ~  ' . $tglKeluar->format('d-M-Y H:i'));
        // Style Judul
        phpspreadsheet::styleFont($spreadsheet, 'A1:A2', true, 11, 'Calibri');

        // Header
        $columnMachineNo = 'A';
        $columnMachineName = 'B';
        $spreadsheet->getActiveSheet()->mergeCells($columnMachineNo . '3:' . $columnMachineName . '3');
        $activeWorksheet->setCellValue('A3', 'Mesin');

        $columnProduct = 'C';
        $columnProductEnd = 'D';
        $spreadsheet->getActiveSheet()->mergeCells($columnProduct . '3:' . $columnProductEnd . '3');
        $activeWorksheet->setCellValue('C3', 'Produk');

        // header
        $rowHeaderStart = 3;
        $columnHeaderStart = 'E';
        $columnHeaderEnd = 'E';
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
        // freeze pane
        $spreadsheet->getActiveSheet()->freezePane('A4');
        $columnHeaderEnd = chr(ord($columnHeaderEnd) - 1);

        // style header
        phpspreadsheet::addFullBorder($spreadsheet, $columnMachineNo . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);
        phpspreadsheet::styleFont($spreadsheet, $columnMachineNo . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart, true, 9, 'Calibri');
        phpspreadsheet::textAlignCenter($spreadsheet, $columnMachineNo . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);

        $data = DB::select("
            SELECT MAX
                ( dep.NAME ) AS department_name,
                MAX ( dep.id ) AS department_id,
                MAX ( prd.NAME ) AS product_name,
                MAX ( prd.code ) AS noorder,
                MAX ( prd.id ) AS product_id,
                MAX ( mac.machineNo ) AS machine_no,
                MAX ( mac.machineName ) AS machine_name,
                SUM ( asy.berat_standard ) AS berat_standard,
                SUM ( asy.berat_produksi ) AS berat_produksi,
                SUM ( asy.infure_cost ) AS infure_cost,
                SUM ( asy.infure_berat_loss ) AS infure_berat_loss,
                SUM ( asy.panjang_produksi ) AS panjang_produksi,
                SUM ( asy.panjang_printing_inline ) AS panjang_printing_inline,
                SUM ( asy.infure_cost_printing ) AS infure_cost_printing
            FROM
                tdProduct_Assembly AS asy
                INNER JOIN msMachine AS mac ON asy.machine_id = mac.
                ID INNER JOIN msDepartment AS dep ON mac.department_id = dep.
                ID INNER JOIN msProduct AS prd ON asy.product_id = prd.ID
            WHERE
                asy.production_date BETWEEN '$tglMasuk'
                AND '$tglKeluar'
            GROUP BY
                dep.id,
                asy.machine_id,
                asy.product_id
            ORDER BY
                asy.machine_id
        ");

        if (count($data) == 0) {
            $response = [
                'status' => 'error',
                'message' => "Data pada periode tanggal tersebut tidak ditemukan"
            ];

            return $response;
        }

        $listDepartment = array_reduce($data, function ($carry, $item) {
            $carry[$item->department_id] = [
                'department_id' => $item->department_id,
                'department_name' => $item->department_name
            ];
            return $carry;
        }, []);

        // list mesin berdasarkan tanggal pertama dan departemen
        $listMachine = array_reduce($data, function ($carry, $item) {
            $carry[$item->department_id][$item->machine_no] = $item->machine_name;
            return $carry;
        }, []);

        $listProduct = array_reduce($data, function ($carry, $item) {
            $carry[$item->department_id][$item->machine_no][$item->product_id] = [
                'productName' => $item->product_name,
                'productId' => $item->product_id,
                'noorder' => $item->noorder
            ];
            return $carry;
        }, []);

        $dataFilter = array_reduce($data, function ($carry, $item) {
            $carry[$item->department_id][$item->machine_no][$item->product_id] = $item;
            return $carry;
        }, []);

        // index
        $startColumnItem = 'A';
        $columnMachineName = 'B';
        $endColumnItem = $columnHeaderEnd;
        $startColumnItemData = 'E';
        $columnProductNo = 'C';
        $columnProductName = 'D';
        $columnBeratStandar = 'E';
        $columnBeratProduksi = 'F';
        $columnLoss = 'I';
        $startRowItem = 5;
        $rowItem = $startRowItem;
        // daftar departemen
        foreach ($listDepartment as $department) {
            // Menulis data departemen
            $activeWorksheet->setCellValue($startColumnItem . $rowItem, $department['department_name']);
            $spreadsheet->getActiveSheet()->mergeCells($startColumnItem . $rowItem . ':' . $endColumnItem . $rowItem);
            phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowItem, true, 9, 'Calibri');
            $rowItem++;
            $startRowItem = $rowItem;

            // daftar mesin
            foreach ($listMachine[$department['department_id']] as $machineNo => $machineName) {
                foreach ($listProduct[$department['department_id']][$machineNo] as $productId => $product) {
                    // Menulis data mesin
                    $spreadsheet->getActiveSheet()->setCellValue($startColumnItem . $rowItem, $machineNo);
                    $spreadsheet->getActiveSheet()->setCellValue($columnMachineName . $rowItem, $machineName);
                    $columnItem = $startColumnItemData;

                    // Menulis data mesin
                    $spreadsheet->getActiveSheet()->setCellValue($columnProductNo . $rowItem, $product['noorder']);
                    $spreadsheet->getActiveSheet()->setCellValue($columnProductName . $rowItem, $product['productName']);


                    // memasukkan data
                    $dataItem = $dataFilter[$department['department_id']][$machineNo][$productId] ?? (object)[
                        'berat_standard' => 0,
                        'berat_produksi' => 0,
                        'infure_cost' => 0,
                        'infure_berat_loss' => 0,
                        'panjang_produksi' => 0,
                        'panjang_printing_inline' => 0,
                        'infure_cost_printing' => 0
                    ];
                    // berat standar
                    $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_standard);
                    phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                    $columnItem++;
                    // berat produksi
                    $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_produksi);
                    phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                    $columnItem++;
                    // weight rate
                    $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_standard > 0 ? $dataItem->berat_produksi / $dataItem->berat_standard * 100 : 0);
                    phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                    $columnItem++;
                    // infure cost
                    $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->infure_cost);
                    phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
                    $columnItem++;
                    // loss
                    $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->infure_berat_loss);
                    phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                    $columnItem++;
                    // loss %
                    $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_produksi > 0 ? $dataItem->infure_berat_loss / ($dataItem->berat_produksi + $dataItem->infure_berat_loss) * 100 : 0);
                    phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                    $columnItem++;
                    // panjang infure
                    $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->panjang_produksi);
                    phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
                    $columnItem++;
                    // panjang inline printing
                    $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->panjang_printing_inline);
                    phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
                    $columnItem++;
                    // inline printing cost
                    $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->infure_cost_printing);
                    phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
                    $columnItem++;
                    // process cost
                    $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->infure_cost + $dataItem->infure_cost_printing);
                    phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
                    $rowItem++;
                }
            }
            phpspreadsheet::addBorderDottedMiddleHorizontal($spreadsheet, $startColumnItem . $startRowItem . ':' . $columnItem . $rowItem);
            phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $startRowItem . ':' . $columnItem . $rowItem, false, 8, 'Calibri');
            // perhitungan jumlah berdasarkan Departemen
            $spreadsheet->getActiveSheet()->mergeCells($startColumnItem . $rowItem . ':' . $columnProductName . $rowItem);
            $activeWorksheet->setCellValue($startColumnItem . $rowItem, 'Total');

            $columnItem = $startColumnItemData;
            $columnItemEnd = chr(ord($columnItem) + count($header) - 1);
            // berat standar
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($startRowItem) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // berat produksi
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($startRowItem) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // weight rate
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=' . $columnBeratProduksi . $rowItem . '/' . $columnBeratStandar . $rowItem . '*100');
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // infure cost
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($startRowItem) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // loss
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($startRowItem) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // loss %
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=' . $columnLoss . $rowItem . '/(' . $columnLoss . $rowItem . '+' . $columnBeratStandar . $rowItem . ')*100');
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($startRowItem) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($startRowItem) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($startRowItem) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($startRowItem) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
            phpspreadsheet::addBorderDottedMiddleHorizontal($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);
            $columnItem++;
            phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowItem . ':' . $columnHeaderEnd . $rowItem, true, 8, 'Calibri');

            $rowItem++;
            $rowItem++;
        }

        // Grand total
        $rowGrandTotal = $rowItem;
        $spreadsheet->getActiveSheet()->mergeCells($startColumnItem . $rowGrandTotal . ':' . $columnMachineName . $rowGrandTotal);
        $spreadsheet->getActiveSheet()->setCellValue($startColumnItem . $rowGrandTotal, 'GRAND TOTAL');
        phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowGrandTotal . ':' . $columnHeaderEnd . $rowGrandTotal, true, 8, 'Calibri');

        // berat standar
        $grandTotal = [
            'berat_standard' => 0,
            'berat_produksi' => 0,
            'infure_cost' => 0,
            'infure_berat_loss' => 0,
            'panjang_produksi' => 0,
            'panjang_printing_inline' => 0,
            'infure_cost_printing' => 0
        ];

        foreach ($dataFilter as $departmentId => $department) {
            foreach ($department as $machineNo => $machine) {
                foreach ($machine as $productId => $product) {
                    $grandTotal['berat_standard'] += $product->berat_standard;
                    $grandTotal['berat_produksi'] += $product->berat_produksi;
                    $grandTotal['infure_cost'] += $product->infure_cost;
                    $grandTotal['infure_berat_loss'] += $product->infure_berat_loss;
                    $grandTotal['panjang_produksi'] += $product->panjang_produksi;
                    $grandTotal['panjang_printing_inline'] += $product->panjang_printing_inline;
                    $grandTotal['infure_cost_printing'] += $product->infure_cost_printing;
                }
            }
        }

        $columnItem = $startColumnItemData;
        $columnItemEnd = chr(ord($columnItem) + count($header) - 1);
        // berat standar
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_standard']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // berat produksi
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_produksi']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // weight rate
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_standard'] > 0 ? $grandTotal['berat_produksi'] / $grandTotal['berat_standard'] * 100 : 0);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // infure cost
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['infure_cost']);
        phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // loss
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['infure_berat_loss']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // loss %
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_produksi'] > 0 ? $grandTotal['infure_berat_loss'] / ($grandTotal['berat_produksi'] + $grandTotal['infure_berat_loss']) * 100 : 0);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // panjang infure
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['panjang_produksi']);
        phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // panjang inline printing
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['panjang_printing_inline']);
        phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // inline printing cost
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['infure_cost_printing']);
        phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // process cost
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['infure_cost'] + $grandTotal['infure_cost_printing']);
        phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowGrandTotal);
        phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowGrandTotal . ':' . $columnItem . $rowGrandTotal);
        $columnItem++;

        $activeWorksheet->getStyle($startColumnItemData . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart)->getAlignment()->setWrapText(true);

        // set specific column widths (pixels -> approximate Excel column width)
        $pixelToExcelWidth = function ($pixels) {
            // Approx conversion: excelColumnWidth ≈ (pixels - 5) / 7
            $w = ($pixels - 5) / 7;
            return $w > 0 ? $w : 1;
        };

        $pixelWidths = [
            'E' => 84,
            'F' => 84,
            'G' => 84,
            'H' => 84,
            'I' => 84,
            'J' => 84,
            'K' => 84,
            'L' => 84,
            'M' => 84,
            'N' => 84,
        ];

        // apply fixed pixel widths
        foreach ($pixelWidths as $col => $px) {
            $dim = $spreadsheet->getActiveSheet()->getColumnDimension($col);
            $dim->setAutoSize(false);
            $dim->setWidth($pixelToExcelWidth($px));
        }

        // auto-size any remaining columns between startColumnItemData and endColumnItem
        $col = 'B';
        while ($col !== $endColumnItem) {
            if (!isset($pixelWidths[$col])) {
                $spreadsheet->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);
            }
            $col++;
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

    public static function daftarProduksiPerMesinPerProdukSeitai($nippon, $jenisReport, $tglMasuk, $tglKeluar)
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
        $activeWorksheet->setCellValue('A1', 'DAFTAR PRODUKSI PER MESIN PER PRODUK SEITAI');
        $activeWorksheet->setCellValue('A2', 'Periode : ' . $tglMasuk->format('d-M-Y H:i') . '  ~  ' . $tglKeluar->format('d-M-Y H:i'));
        // Style Judul
        phpspreadsheet::styleFont($spreadsheet, 'A1:A2', true, 11, 'Calibri');

        // Header
        $columnMachineNo = 'A';
        $columnMachineName = 'B';
        $spreadsheet->getActiveSheet()->mergeCells($columnMachineNo . '3:' . $columnMachineName . '3');
        $activeWorksheet->setCellValue('A3', 'Mesin');

        $columnProduct = 'C';
        $columnProductEnd = 'D';
        $spreadsheet->getActiveSheet()->mergeCells($columnProduct . '3:' . $columnProductEnd . '3');
        $activeWorksheet->setCellValue('C3', 'Produk');

        // header
        $rowHeaderStart = 3;
        $columnHeaderStart = 'E';
        $columnHeaderEnd = 'E';
        $header = [
            'Jumlah Produksi (Lembar)',
            'Berat Produksi (Kg)',
            'Loss (Kg)',
            'Loss (%)',
            'Seitai Cost',
            'Ponsu Loss (Kg)',
            'Infure Loss (Kg)',
        ];

        foreach ($header as $key => $value) {
            $activeWorksheet->setCellValue($columnHeaderEnd . $rowHeaderStart, $value);
            $columnHeaderEnd++;
        }
        // freeze pane
        $spreadsheet->getActiveSheet()->freezePane('A4');
        $columnHeaderEnd = chr(ord($columnHeaderEnd) - 1);

        // style header
        phpspreadsheet::addFullBorder($spreadsheet, $columnMachineNo . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);
        phpspreadsheet::styleFont($spreadsheet, $columnMachineNo . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart, true, 9, 'Calibri');
        phpspreadsheet::textAlignCenter($spreadsheet, $columnMachineNo . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);

        $divisionCodeSeitai = '20';
        $data = DB::select("
            SELECT
                MAX(dep.name) AS department_name,
                MAX(dep.id) AS department_id,
                MAX(prd.id) AS product_id,
                MAX(prd.code) AS noorder,
                MAX(prd.name) AS product_name,
                MAX(mac.machineNo) AS machine_no,
                MAX(mac.machineName) AS machine_name,
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
                WHERE los_.loss_seitai_id = 1
                GROUP BY los_.product_goods_id
            ) ponsu ON good.id = ponsu.product_goods_id
            INNER JOIN msMachine AS mac ON good.machine_id = mac.id
            INNER JOIN msDepartment AS dep ON mac.department_id = dep.id
            INNER JOIN msProduct AS prd ON good.product_id = prd.id
            INNER JOIN msProduct_type AS prT ON prd.product_type_id = prT.id
            WHERE good.production_date BETWEEN '$tglMasuk' AND '$tglKeluar'
            AND (dep.division_code = '$divisionCodeSeitai')
            GROUP BY dep.id, good.machine_id, prd.name
            ORDER BY good.machine_id;
        ");

        if (count($data) == 0) {
            $response = [
                'status' => 'error',
                'message' => "Data pada periode tanggal tersebut tidak ditemukan"
            ];

            return $response;
        }

        $listDepartment = array_reduce($data, function ($carry, $item) {
            $carry[$item->department_id] = [
                'department_id' => $item->department_id,
                'department_name' => $item->department_name
            ];
            return $carry;
        }, []);

        // list mesin berdasarkan tanggal pertama dan departemen
        $listMachine = array_reduce($data, function ($carry, $item) {
            $carry[$item->department_id][$item->machine_no] = $item->machine_name;
            return $carry;
        }, []);

        $listProduct = array_reduce($data, function ($carry, $item) {
            $carry[$item->department_id][$item->machine_no][$item->product_id] = [
                'productName' => $item->product_name,
                'productId' => $item->product_id,
                'noorder' => $item->noorder
            ];
            return $carry;
        }, []);

        $dataFilter = array_reduce($data, function ($carry, $item) {
            $carry[$item->department_id][$item->machine_no][$item->product_id] = $item;
            return $carry;
        }, []);

        // index
        $startColumnItem = 'A';
        $endColumnItem = $columnHeaderEnd;
        $startColumnItemData = 'E';
        $columnProductNo = 'C';
        $columnProductName = 'D';
        $columnBeratProduksi = 'F';
        $columnLoss = 'G';
        $startRowItem = 5;
        $rowItem = $startRowItem;
        // daftar departemen
        foreach ($listDepartment as $department) {
            // Menulis data departemen
            $activeWorksheet->setCellValue($startColumnItem . $rowItem, $department['department_name']);
            $spreadsheet->getActiveSheet()->mergeCells($startColumnItem . $rowItem . ':' . $endColumnItem . $rowItem);
            phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowItem, true, 9, 'Calibri');
            $rowItem++;

            // daftar mesin
            $startRowItem = $rowItem;
            foreach ($listMachine[$department['department_id']] as $machineNo => $machineName) {
                // daftar mesin
                foreach ($listProduct[$department['department_id']][$machineNo] as $productId => $product) {
                    // Menulis data mesin
                    $spreadsheet->getActiveSheet()->setCellValue($columnMachineNo . $rowItem, $machineNo);
                    $spreadsheet->getActiveSheet()->setCellValue($columnMachineName . $rowItem, $machineName);
                    $columnItem = $startColumnItemData;

                    // Menulis data mesin
                    $spreadsheet->getActiveSheet()->setCellValue($columnProductNo . $rowItem, $product['noorder']);
                    $spreadsheet->getActiveSheet()->setCellValue($columnProductName . $rowItem, $product['productName']);

                    // memasukkan data
                    $dataItem = $dataFilter[$department['department_id']][$machineNo][$productId] ?? (object)[
                        'qty_produksi' => 0,
                        'berat_produksi' => 0,
                        'seitai_cost' => 0,
                        'seitai_berat_loss' => 0,
                        'seitai_berat_loss_ponsu' => 0,
                        'infure_berat_loss' => 0
                    ];
                    // jumlah produksi
                    $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->qty_produksi);
                    phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
                    $columnItem++;
                    // berat produksi
                    $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_produksi);
                    phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                    $columnItem++;
                    // loss
                    $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->seitai_berat_loss);
                    phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                    $columnItem++;
                    // loss %
                    $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_produksi > 0 ? $dataItem->seitai_berat_loss / ($dataItem->berat_produksi + $dataItem->seitai_berat_loss) * 100 : 0);
                    phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                    $columnItem++;
                    // Seitai cost
                    $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->seitai_cost);
                    phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
                    $columnItem++;
                    // Ponsu Loss
                    $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->seitai_berat_loss_ponsu);
                    phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                    $columnItem++;
                    // Infure Loss
                    $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->infure_berat_loss);
                    phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                    $rowItem++;
                }
            }
            phpspreadsheet::addBorderDottedMiddleHorizontal($spreadsheet, $startColumnItem . $startRowItem . ':' . $columnItem . $rowItem);
            phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $startRowItem . ':' . $columnItem . $rowItem, false, 8, 'Calibri');
            // perhitungan jumlah total
            $spreadsheet->getActiveSheet()->mergeCells($startColumnItem . $rowItem . ':' . $columnProductName . $rowItem);
            $activeWorksheet->setCellValue($startColumnItem . $rowItem, 'Total');
            $columnItem = $startColumnItemData;
            $columnItemEnd = chr(ord($columnItem) + count($header) - 1);
            // jumlah produksi
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listProduct[$department['department_id']][$machineNo])) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // berat produksi
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listProduct[$department['department_id']][$machineNo])) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // loss
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listProduct[$department['department_id']][$machineNo])) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // loss %
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=' . $columnLoss . $rowItem . '/(' . $columnLoss . $rowItem . '+' . $columnBeratProduksi . $rowItem . ')*100');
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // Seitai cost
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listProduct[$department['department_id']][$machineNo])) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // Ponsu Loss
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listProduct[$department['department_id']][$machineNo])) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // Infure Loss
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listProduct[$department['department_id']][$machineNo])) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);

            phpspreadsheet::addBorderDottedMiddleHorizontal($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);
            phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowItem . ':' . $columnHeaderEnd . $rowItem, true, 8, 'Calibri');

            $rowItem++;
            $rowItem++;
        }

        // Grand total
        $rowGrandTotal = $rowItem;
        $spreadsheet->getActiveSheet()->mergeCells($startColumnItem . $rowGrandTotal . ':' . $columnProductName . $rowGrandTotal);
        $spreadsheet->getActiveSheet()->setCellValue($startColumnItem . $rowGrandTotal, 'GRAND TOTAL');
        phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowGrandTotal . ':' . $columnHeaderEnd . $rowGrandTotal, true, 8, 'Calibri');

        // berat standar
        $grandTotal = [
            'qty_produksi' => 0,
            'berat_produksi' => 0,
            'seitai_cost' => 0,
            'seitai_berat_loss' => 0,
            'seitai_berat_loss_ponsu' => 0,
            'infure_berat_loss' => 0
        ];

        foreach ($dataFilter as $departmentId => $department) {
            foreach ($department as $machineNo => $machine) {
                foreach ($machine as $productId => $product) {
                    $grandTotal['qty_produksi'] += $product->qty_produksi;
                    $grandTotal['berat_produksi'] += $product->berat_produksi;
                    $grandTotal['seitai_cost'] += $product->seitai_cost;
                    $grandTotal['seitai_berat_loss'] += $product->seitai_berat_loss;
                    $grandTotal['seitai_berat_loss_ponsu'] += $product->seitai_berat_loss_ponsu;
                    $grandTotal['infure_berat_loss'] += $product->infure_berat_loss;
                }
            }
        }

        $columnItem = $startColumnItemData;
        $columnItemEnd = chr(ord($columnItem) + count($header) - 1);
        // jumlah produksi
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['qty_produksi']);
        phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // berat produksi
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_produksi']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // loss
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['seitai_berat_loss']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // loss %
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_produksi'] > 0 ? $grandTotal['seitai_berat_loss'] / ($grandTotal['berat_produksi'] + $grandTotal['seitai_berat_loss']) * 100 : 0);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // Seitai cost
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['seitai_cost']);
        phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // Ponsu Loss
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['seitai_berat_loss_ponsu']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // Infure Loss
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['infure_berat_loss']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowGrandTotal . ':' . $columnItem . $rowGrandTotal);
        $columnItem++;

        $activeWorksheet->getStyle($startColumnItemData . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart)->getAlignment()->setWrapText(true);

        // set specific column widths (pixels -> approximate Excel column width)
        $pixelToExcelWidth = function ($pixels) {
            // Approx conversion: excelColumnWidth ≈ (pixels - 5) / 7
            $w = ($pixels - 5) / 7;
            return $w > 0 ? $w : 1;
        };

        $pixelWidths = [
            'E' => 84,
            'F' => 84,
            'G' => 84,
            'H' => 84,
            'I' => 84,
            'J' => 84,
            'K' => 84,
            'L' => 84,
            'M' => 84,
            'N' => 84,
        ];

        // apply fixed pixel widths
        foreach ($pixelWidths as $col => $px) {
            $dim = $spreadsheet->getActiveSheet()->getColumnDimension($col);
            $dim->setAutoSize(false);
            $dim->setWidth($pixelToExcelWidth($px));
        }

        // auto-size any remaining columns between startColumnItemData and endColumnItem
        $col = 'B';
        while ($col !== $endColumnItem) {
            if (!isset($pixelWidths[$col])) {
                $spreadsheet->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);
            }
            $col++;
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
