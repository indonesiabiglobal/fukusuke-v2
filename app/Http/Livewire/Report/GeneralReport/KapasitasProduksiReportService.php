<?php

namespace App\Http\Livewire\Report\GeneralReport;

use App\Helpers\phpspreadsheet;
use App\Models\MsJamMatiMesin;
use App\Models\MsMachine;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class KapasitasProduksiReportService
{
    public static function kapasitasProduksiInfure($nippon, $jenisReport, $tglMasuk, $tglKeluar)
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
        $activeWorksheet->freezePane('A4');
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
        $activeWorksheet->setCellValue('B1', 'KAPASITAS PRODUKSI INFURE');
        $activeWorksheet->setCellValue('B2', 'Periode : ' . $tglMasuk->format('d-M-Y H:i') . '  ~  ' . $tglKeluar->format('d-M-Y H:i'));
        // Style Judul
        phpspreadsheet::styleFont($spreadsheet, 'B1:B2', true, 11, 'Calibri');
        // hide column A
        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setVisible(false);

        // Header
        $columnMesin = 'B';
        $columnMesinEnd = 'C';
        $spreadsheet->getActiveSheet()->mergeCells($columnMesin . '3:' . $columnMesinEnd . '3');
        $activeWorksheet->setCellValue('B3', 'Mesin');

        // header
        $rowHeaderStart = 3;
        $columnHeaderStart = 'D';
        $columnHeaderEnd = 'D';
        $header = [
            'Hari Kerja (Hari)',
            'Kapasitas',
            'Kapasitas (Kg)',
            'Produksi (Kg)',
            'Rasio Produksi (%)',
            'Kapasitas',
            'Kapasitas (Meter)',
            'Produksi (Meter)',
            'Rasio Produksi (%)',
        ];

        foreach ($header as $key => $value) {
            $activeWorksheet->setCellValue($columnHeaderEnd . $rowHeaderStart, $value);
            $columnHeaderEnd++;
        }
        $columnHeaderEnd = chr(ord($columnHeaderEnd) - 1);

        // style header
        phpspreadsheet::addFullBorder($spreadsheet, $columnMesin . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);
        phpspreadsheet::styleFont($spreadsheet, $columnMesin . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart, true, 9, 'Calibri');
        phpspreadsheet::textAlignCenter($spreadsheet, $columnMesin . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);

        $data = DB::select("
            SELECT
                max(prGrp.code) AS product_group_code,
                max(prGrp.name) AS product_group_name,
                MAX(mac.machineNo) AS machine_no,
                MAX(mac.machineName) AS machine_name,
                SUM(asy.panjang_produksi) AS panjang_produksi,
                SUM(asy.berat_produksi) AS berat_produksi,
                MAX(mac.capacity_kg) AS capacity_kg,
                MAX(mac.capacity_lembar) AS capacity_lembar--,
                --@day AS seq_no
            FROM tdProduct_Assembly AS asy
            INNER JOIN msProduct AS prd ON asy.product_id = prd.id
            INNER JOIN msProduct_type AS prTip ON prd.product_type_id = prTip.id
            INNER JOIN msProduct_group AS prGrp ON prTip.product_group_id = prGrp.id
            INNER JOIN msMachine AS mac ON asy.machine_id = mac.id
            WHERE asy.production_date BETWEEN '$tglMasuk' AND '$tglKeluar'
            GROUP BY prGrp.id, asy.machine_id
            ORDER BY machine_no;
        ");

        if (count($data) == 0) {
            $response = [
                'status' => 'error',
                'message' => "Data pada periode tanggal tersebut tidak ditemukan"
            ];

            return $response;
        }

        // list group produk
        $listProductGroup = array_reduce($data, function ($carry, $item) {
            $carry[$item->product_group_code] = [
                'product_group_code' => $item->product_group_code,
                'product_group_name' => $item->product_group_name
            ];
            return $carry;
        }, []);

        // list mesin berdasarkan tanggal pertama dan departemen
        $listMachine = array_reduce($data, function ($carry, $item) {
            if (!isset($carry[$item->product_group_code])) {
                $carry[$item->product_group_code] = [];
            }
            $carry[$item->product_group_code][$item->machine_no] = $item->machine_name;
            return $carry;
        }, []);

        $dataFilter = array_reduce($data, function ($carry, $item) {
            $carry[$item->product_group_code][$item->machine_no] = $item;
            return $carry;
        }, []);

        // index
        $startColumnItem = 'B';
        $endColumnItem = $columnHeaderEnd;
        $startColumnItemData = 'D';
        $columnMachineNo = 'B';
        $columnMachineName = 'C';
        $columnHariKerja = 'D';
        $columnKapasitasKg = 'E';
        $columnKapasitasHariKg = 'F';
        $columnProduksiKg = 'G';
        $columnKapasitasMeter = 'I';
        $columnKapasitasHariMeter = 'J';
        $columnProduksiMeter = 'K';
        $startRowItem = 4;
        $rowItem = $startRowItem;

        $grandTotal = [
            'kapasitas_kg' => 0,
            'berat_produksi' => 0,
            'kapasitas_meter' => 0,
            'panjang_produksi' => 0,
        ];
        // daftar departemen
        foreach ($listProductGroup as $productGroup) {
            // Menulis data departemen
            $activeWorksheet->setCellValue($startColumnItem . $rowItem, $productGroup['product_group_name']);
            $spreadsheet->getActiveSheet()->mergeCells($startColumnItem . $rowItem . ':' . $endColumnItem . $rowItem);
            phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowItem, true, 9, 'Calibri');
            $rowItem++;

            $isKapasitasKgZero = true;
            $isKapasitasMeterZero = true;
            // daftar mesin
            foreach ($listMachine[$productGroup['product_group_code']] as $machineNo => $machineName) {
                if ($dataFilter[$productGroup['product_group_code']][$machineNo] == null) {
                    continue;
                }
                $columnItem = $startColumnItemData;

                // Menulis data mesin
                $spreadsheet->getActiveSheet()->setCellValue($columnMachineNo . $rowItem, $machineNo);
                $spreadsheet->getActiveSheet()->setCellValue($columnMachineName . $rowItem, $machineName);
                // phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);

                // memasukkan data
                $dataItem = $dataFilter[$productGroup['product_group_code']][$machineNo];

                // hari kerja
                $hariKerja = $tglMasuk->diffInDays($tglKeluar) + 1;
                $activeWorksheet->setCellValue($columnItem . $rowItem, $hariKerja);
                $columnItem++;
                // kapasitas
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->capacity_kg);
                phpSpreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // kapasitas (kg)
                $activeWorksheet->setCellValue($columnItem . $rowItem, '=ROUND(' . $columnKapasitasKg . $rowItem . '*' . $columnHariKerja . $rowItem . '*24,0)');
                phpSpreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // produksi (kg)
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_produksi);
                phpSpreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // rasio produksi (%)
                if ($dataItem->capacity_kg == 0) {
                    $activeWorksheet->setCellValue($columnItem . $rowItem, 0);
                    $activeWorksheet->getStyle($columnItem . $rowItem)->getNumberFormat()->setFormatCode('0;-0;"-"');
                } else {
                    $isKapasitasKgZero = false;
                    $activeWorksheet->setCellValue($columnItem . $rowItem, '=ROUND((' . $columnProduksiKg . $rowItem . '/' . $columnKapasitasHariKg . $rowItem . ')*100,0)');
                    phpSpreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
                }
                $columnItem++;
                // kapasitas
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->capacity_lembar);
                phpSpreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // kapasitas (meter)
                $activeWorksheet->setCellValue($columnItem . $rowItem, '=ROUND(' . $columnKapasitasMeter . $rowItem . '*' . $columnHariKerja . $rowItem . '*24,0)');
                phpSpreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // produksi (meter)
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->panjang_produksi);
                phpSpreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // rasio produksi (%)
                if ($dataItem->capacity_lembar == 0) {
                    $activeWorksheet->setCellValue($columnItem . $rowItem, 0);
                    $activeWorksheet->getStyle($columnItem . $rowItem)->getNumberFormat()->setFormatCode('0;-0;"-"');
                } else {
                    $isKapasitasMeterZero = false;
                    $activeWorksheet->setCellValue($columnItem . $rowItem, '=ROUND((' . $columnProduksiMeter . $rowItem . '/' . $columnKapasitasHariMeter . $rowItem . ')*100,0)');
                    phpSpreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
                }
                phpSpreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);
                $columnItem++;

                phpSpreadsheet::styleFont($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem, false, 8, 'Calibri');
                $rowItem++;
            }
            // perhitungan jumlah berdasarkan departemen
            $spreadsheet->getActiveSheet()->mergeCells($columnMachineNo . $rowItem . ':' . $columnMachineName . $rowItem);
            // $activeWorksheet->setCellValue($columnMachineNo . $rowItem, 'Total ' . $department['department_name']);
            $columnItem = $startColumnItemData;
            $columnItemEnd = chr(ord($columnItem) + count($header) - 1);
            // hari kerja
            $columnItem++;
            // kapasitas
            $columnItem++;
            // kapasitas (kg)
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listMachine[$productGroup['product_group_code']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            $grandTotal['kapasitas_kg'] += $spreadsheet->getActiveSheet()->getCell($columnItem . $rowItem)->getCalculatedValue();
            phpSpreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // produksi (kg)
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listMachine[$productGroup['product_group_code']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            $grandTotal['berat_produksi'] += $spreadsheet->getActiveSheet()->getCell($columnItem . $rowItem)->getCalculatedValue();
            phpSpreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // rasio produksi (%)
            if ($isKapasitasKgZero) {
                $activeWorksheet->setCellValue($columnItem . $rowItem, 0);
                $activeWorksheet->getStyle($columnItem . $rowItem)->getNumberFormat()->setFormatCode('0;-0;"-"');
            } else {
                $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=ROUND((' . $columnProduksiKg . $rowItem . '/' . $columnKapasitasHariKg . $rowItem . ')*100,0)');
                phpSpreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
            }
            $columnItem++;
            // kapasitas
            $columnItem++;
            // kapasitas (meter)
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listMachine[$productGroup['product_group_code']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            $grandTotal['kapasitas_meter'] += $spreadsheet->getActiveSheet()->getCell($columnItem . $rowItem)->getCalculatedValue();
            phpSpreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // produksi (meter)
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listMachine[$productGroup['product_group_code']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            $grandTotal['panjang_produksi'] += $spreadsheet->getActiveSheet()->getCell($columnItem . $rowItem)->getCalculatedValue();
            phpSpreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // rasio produksi (%)
            if ($isKapasitasMeterZero) {
                $activeWorksheet->setCellValue($columnItem . $rowItem, 0);
                $activeWorksheet->getStyle($columnItem . $rowItem)->getNumberFormat()->setFormatCode('0;-0;"-"');
            } else {
                $activeWorksheet->setCellValue($columnItem . $rowItem, '=ROUND((' . $columnProduksiMeter . $rowItem . '/' . $columnKapasitasHariMeter . $rowItem . ')*100,0)');
                phpSpreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
            }

            phpspreadsheet::addFullBorder($spreadsheet, $columnMachineNo . $rowItem . ':' . $columnItem . $rowItem);
            phpspreadsheet::styleFont($spreadsheet, $columnMachineNo . $rowItem . ':' . $columnItem . $rowItem, true, 8, 'Calibri');
            $columnItem++;


            $rowItem++;
            $rowItem++;
        }

        // Grand total
        $rowGrandTotal = $rowItem;
        $spreadsheet->getActiveSheet()->mergeCells($columnMachineNo . $rowGrandTotal . ':' . $columnMachineName . $rowGrandTotal);
        $spreadsheet->getActiveSheet()->setCellValue($columnMachineNo . $rowGrandTotal, 'GRAND TOTAL');
        phpspreadsheet::styleFont($spreadsheet, $columnMachineNo . $rowGrandTotal . ':' . $columnHeaderEnd . $rowGrandTotal, true, 8, 'Calibri');

        $columnItem = $startColumnItemData;
        // hari kerja
        $columnItem++;
        // kapasitas
        $columnItem++;
        // kapasitas (kg)
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['kapasitas_kg']);
        phpSpreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
        $columnItem++;
        // produksi (kg)
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_produksi']);
        phpSpreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
        $columnItem++;
        // rasio produksi (%)
        if ($grandTotal['kapasitas_kg'] == 0) {
            $activeWorksheet->setCellValue($columnItem . $rowGrandTotal, 0);
            $activeWorksheet->getStyle($columnItem . $rowGrandTotal)->getNumberFormat()->setFormatCode('0;-0;"-"');
        } else {
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, '=ROUND((' . $columnProduksiKg . $rowGrandTotal . '/' . $columnKapasitasHariKg . $rowGrandTotal . ')*100,0)');
            phpSpreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
        }
        $columnItem++;
        // kapasitas
        $columnItem++;
        // kapasitas (meter)
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['kapasitas_meter']);
        phpSpreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
        $columnItem++;
        // produksi (meter)
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['panjang_produksi']);
        phpSpreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
        $columnItem++;
        // rasio produksi (%)
        if ($grandTotal['kapasitas_meter'] == 0) {
            $activeWorksheet->setCellValue($columnItem . $rowGrandTotal, 0);
            $activeWorksheet->getStyle($columnItem . $rowGrandTotal)->getNumberFormat()->setFormatCode('0;-0;"-"');
        } else {
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, '=ROUND((' . $columnProduksiMeter . $rowGrandTotal . '/' . $columnKapasitasHariMeter . $rowGrandTotal . ')*100,0)');
            phpSpreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
        }
        phpspreadsheet::addFullBorder($spreadsheet, $columnMachineNo . $rowGrandTotal . ':' . $columnItem . $rowGrandTotal);
        $columnItem++;

        $activeWorksheet->getStyle($columnMesin . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart)->getAlignment()->setWrapText(true);

        // auto size
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);

        $writer = new Xlsx($spreadsheet);
        $filename = 'asset/report/' . $nippon . '-' . $jenisReport . '.xlsx';
        $writer->save($filename);
        $response = [
            'status' => 'success',
            'filename' => $filename
        ];
        return $response;
    }

    public static function kapasitasProduksiSeitai($nippon, $jenisReport, $tglMasuk, $tglKeluar)
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

        $activeWorksheet->freezePane('A4');
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
        $activeWorksheet->setCellValue('B1', 'KAPASITAS PRODUKSI SEITAI');
        $activeWorksheet->setCellValue('B2', 'Periode : ' . $tglMasuk->format('d-M-Y H:i') . '  ~  ' . $tglKeluar->format('d-M-Y H:i'));
        // Style Judul
        phpspreadsheet::styleFont($spreadsheet, 'B1:B2', true, 11, 'Calibri');
        // hide column A
        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setVisible(false);

        // Header
        $columnMesin = 'B';
        $columnMesinEnd = 'C';
        $spreadsheet->getActiveSheet()->mergeCells($columnMesin . '3:' . $columnMesinEnd . '3');
        $activeWorksheet->setCellValue('B3', 'Mesin');

        // header
        $rowHeaderStart = 3;
        $columnHeaderStart = 'D';
        $columnHeaderEnd = 'D';
        $header = [
            'Hari Kerja (Hari)',
            'Kapasitas',
            'Kapasitas (Kg)',
            'Produksi (Kg)',
            'Rasio Produksi (%)',
            'Kapasitas',
            'Kapasitas (Lembar)',
            'Produksi (Lembar)',
            'Rasio Produksi (%)',
        ];

        foreach ($header as $key => $value) {
            $activeWorksheet->setCellValue($columnHeaderEnd . $rowHeaderStart, $value);
            $columnHeaderEnd++;
        }
        $columnHeaderEnd = chr(ord($columnHeaderEnd) - 1);

        // style header
        phpspreadsheet::addFullBorder($spreadsheet, $columnMesin . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);
        phpspreadsheet::styleFont($spreadsheet, $columnMesin . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart, true, 9, 'Calibri');
        phpspreadsheet::textAlignCenter($spreadsheet, $columnMesin . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);

        $data = DB::select("
            SELECT
                MAX(prGrp.code) AS product_group_code,
                MAX(prGrp.name) AS product_group_name,
                MAX(mac.machineNo) AS machine_no,
                MAX(mac.machineName) AS machine_name,
                SUM(good.qty_produksi) AS qty_produksi,
                SUM(good.qty_produksi * prd.unit_weight * 0.001) AS berat_produksi,
                MAX(mac.capacity_kg) AS capacity_kg,
                MAX(mac.capacity_lembar) AS capacity_lembar--,
                --@day AS seq_no
            FROM tdProduct_Goods AS good
            INNER JOIN msProduct AS prd ON good.product_id = prd.id
            INNER JOIN msProduct_type AS prT ON prd.product_type_id = prT.id
            INNER JOIN msProduct_group AS prGrp ON prT.product_group_id = prGrp.id
            INNER JOIN msMachine AS mac ON good.machine_id = mac.id
            WHERE good.production_date BETWEEN '$tglMasuk' AND '$tglKeluar'
            GROUP BY prGrp.name, good.machine_id
            ORDER BY machine_no;
        ");

        if (count($data) == 0) {
            $response = [
                'status' => 'error',
                'message' => "Data pada periode tanggal tersebut tidak ditemukan"
            ];

            return $response;
        }

        // list group produk
        $listProductGroup = array_reduce($data, function ($carry, $item) {
            $carry[$item->product_group_code] = [
                'product_group_code' => $item->product_group_code,
                'product_group_name' => $item->product_group_name
            ];
            return $carry;
        }, []);

        // list mesin berdasarkan tanggal pertama dan departemen
        $listMachine = array_reduce($data, function ($carry, $item) {
            if (!isset($carry[$item->product_group_code])) {
                $carry[$item->product_group_code] = [];
            }
            $carry[$item->product_group_code][$item->machine_no] = $item->machine_name;
            return $carry;
        }, []);

        $dataFilter = array_reduce($data, function ($carry, $item) {
            $carry[$item->product_group_code][$item->machine_no] = $item;
            return $carry;
        }, []);

        // index
        $startColumnItem = 'B';
        $endColumnItem = $columnHeaderEnd;
        $startColumnItemData = 'D';
        $columnMachineNo = 'B';
        $columnMachineName = 'C';
        $columnHariKerja = 'D';
        $columnKapasitasKg = 'E';
        $columnKapasitasHariKg = 'F';
        $columnProduksiKg = 'G';
        $columnKapasitasLembar = 'I';
        $columnKapasitasHariLembar = 'J';
        $columnProduksiLembar = 'K';
        $startRowItem = 4;
        $rowItem = $startRowItem;

        // grand total
        $grandTotal = [
            'kapasitas_kg' => 0,
            'berat_produksi' => 0,
            'qty_produksi' => 0,
            'kapasitas_lembar' => 0
        ];
        // daftar departemen
        foreach ($listProductGroup as $productGroup) {
            // Menulis data departemen
            $activeWorksheet->setCellValue($startColumnItem . $rowItem, $productGroup['product_group_name']);
            $spreadsheet->getActiveSheet()->mergeCells($startColumnItem . $rowItem . ':' . $endColumnItem . $rowItem);
            phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowItem, true, 9, 'Calibri');
            $rowItem++;
            // daftar mesin

            $isKapasitasKgZero = true;
            $isKapasitasLembarZero = true;
            foreach ($listMachine[$productGroup['product_group_code']] as $machineNo => $machineName) {
                if ($dataFilter[$productGroup['product_group_code']][$machineNo] == null) {
                    continue;
                }
                $columnItem = $startColumnItemData;

                // Menulis data mesin
                $spreadsheet->getActiveSheet()->setCellValue($columnMachineNo . $rowItem, $machineNo);
                $spreadsheet->getActiveSheet()->setCellValue($columnMachineName . $rowItem, $machineName);

                // memasukkan data
                $dataItem = $dataFilter[$productGroup['product_group_code']][$machineNo];

                // hari kerja
                $hariKerja = $tglMasuk->diffInDays($tglKeluar) + 1;
                $activeWorksheet->setCellValue($columnItem . $rowItem, $hariKerja);
                $columnItem++;
                // kapasitas
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->capacity_kg);
                $activeWorksheet->getStyle($columnItem . $rowItem)->getNumberFormat()->setFormatCode('#,##0;-#,##0;"-"');
                $columnItem++;
                // Kapasitas (kg)
                $activeWorksheet->setCellValue($columnItem . $rowItem, '=ROUND(' . $columnKapasitasKg . $rowItem . '*' . $columnHariKerja . $rowItem . '*24,0)');
                phpSpreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // produksi (kg)
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_produksi);
                $activeWorksheet->getStyle($columnItem . $rowItem)->getNumberFormat()->setFormatCode('#,##0;-#,##0;"-"');
                $columnItem++;
                // rasio produksi (%)
                if ($dataItem->capacity_kg == 0) {
                    $activeWorksheet->setCellValue($columnItem . $rowItem, 0);
                    $activeWorksheet->getStyle($columnItem . $rowItem)->getNumberFormat()->setFormatCode('0;-0;"-"');
                } else {
                    $isKapasitasKgZero = false;
                    $activeWorksheet->setCellValue($columnItem . $rowItem, '=ROUND((' . $columnProduksiKg . $rowItem . '/' . $columnKapasitasHariKg . $rowItem . ')*100,0)');
                    phpSpreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
                }
                $columnItem++;
                // kapasitas
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->capacity_lembar);
                $activeWorksheet->getStyle($columnItem . $rowItem)->getNumberFormat()->setFormatCode('#,##0;-#,##0;"-"');
                $columnItem++;
                // kapasitas (lembar)
                $activeWorksheet->setCellValue($columnItem . $rowItem, '=ROUND(' . $columnKapasitasLembar . $rowItem . '*' . $columnHariKerja . $rowItem . '*24,0)');
                phpSpreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // produksi (lembar)
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->qty_produksi);
                $activeWorksheet->getStyle($columnItem . $rowItem)->getNumberFormat()->setFormatCode('#,##0;-#,##0;"-"');
                $columnItem++;
                // rasio produksi (%)
                if ($dataItem->capacity_lembar == 0) {
                    $activeWorksheet->setCellValue($columnItem . $rowItem, 0);
                    $activeWorksheet->getStyle($columnItem . $rowItem)->getNumberFormat()->setFormatCode('0;-0;"-"');
                } else {
                    $isKapasitasLembarZero = false;
                    $activeWorksheet->setCellValue($columnItem . $rowItem, '=ROUND((' . $columnProduksiLembar . $rowItem . '/' . $columnKapasitasHariLembar . $rowItem . ')*100,0)');
                    phpSpreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
                }
                phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);
                $columnItem++;

                phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem, false, 8, 'Calibri');
                $rowItem++;
            }
            // perhitungan jumlah berdasarkan departemen
            $spreadsheet->getActiveSheet()->mergeCells($columnMachineNo . $rowItem . ':' . $columnMachineName . $rowItem);
            // $activeWorksheet->setCellValue($columnMachineNo . $rowItem, 'Total ' . $department['department_name']);
            $columnItem = $startColumnItemData;
            $columnItemEnd = chr(ord($columnItem) + count($header) - 1);
            // hari kerja
            $columnItem++;
            // kapasitas
            $columnItem++;
            // kapasitas (kg)
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listMachine[$productGroup['product_group_code']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            $grandTotal['kapasitas_kg'] += $spreadsheet->getActiveSheet()->getCell($columnItem . $rowItem)->getCalculatedValue();
            $activeWorksheet->getStyle($columnItem . $rowItem)->getNumberFormat()->setFormatCode('#,##0;-#,##0;"-"');
            $columnItem++;
            // produksi (kg)
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listMachine[$productGroup['product_group_code']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            $grandTotal['berat_produksi'] += $spreadsheet->getActiveSheet()->getCell($columnItem . $rowItem)->getCalculatedValue();
            $activeWorksheet->getStyle($columnItem . $rowItem)->getNumberFormat()->setFormatCode('#,##0;-#,##0;"-"');
            $columnItem++;
            // rasio produksi (%)
            if ($isKapasitasKgZero) {
                $activeWorksheet->setCellValue($columnItem . $rowItem, 0);
                $activeWorksheet->getStyle($columnItem . $rowItem)->getNumberFormat()->setFormatCode('0;-0;"-"');
            } else {
                $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=ROUND((' . $columnProduksiKg . $rowItem . '/' . $columnKapasitasHariKg . $rowItem . ')*100,0)');
                $activeWorksheet->getStyle($columnItem . $rowItem)->getNumberFormat()->setFormatCode('#,##0;-#,##0;"-"');
            }
            $columnItem++;
            // kapasitas
            $columnItem++;
            // kapasitas (lembar)
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listMachine[$productGroup['product_group_code']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            $grandTotal['kapasitas_lembar'] += $spreadsheet->getActiveSheet()->getCell($columnItem . $rowItem)->getCalculatedValue();
            $activeWorksheet->getStyle($columnItem . $rowItem)->getNumberFormat()->setFormatCode('#,##0;-#,##0;"-"');
            $columnItem++;
            // produksi (lembar)
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listMachine[$productGroup['product_group_code']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            $grandTotal['qty_produksi'] += $spreadsheet->getActiveSheet()->getCell($columnItem . $rowItem)->getCalculatedValue();
            $activeWorksheet->getStyle($columnItem . $rowItem)->getNumberFormat()->setFormatCode('#,##0;-#,##0;"-"');
            $columnItem++;
            // rasio produksi (%)
            if ($isKapasitasLembarZero) {
                $activeWorksheet->setCellValue($columnItem . $rowItem, 0);
                $activeWorksheet->getStyle($columnItem . $rowItem)->getNumberFormat()->setFormatCode('0;-0;"-"');
            } else {
                $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=ROUND((' . $columnProduksiLembar . $rowItem . '/' . $columnKapasitasHariLembar . $rowItem . ')*100,0)');
                $activeWorksheet->getStyle($columnItem . $rowItem)->getNumberFormat()->setFormatCode('#,##0;-#,##0;"-"');
            }
            phpspreadsheet::addFullBorder($spreadsheet, $columnMachineNo . $rowItem . ':' . $columnItem . $rowItem);
            phpspreadsheet::styleFont($spreadsheet, $columnMachineNo . $rowItem . ':' . $columnItem . $rowItem, true, 8, 'Calibri');
            $columnItem++;


            $rowItem++;
            $rowItem++;
        }

        // Grand total
        $rowGrandTotal = $rowItem;
        $spreadsheet->getActiveSheet()->mergeCells($columnMachineNo . $rowGrandTotal . ':' . $columnMachineName . $rowGrandTotal);
        $spreadsheet->getActiveSheet()->setCellValue($columnMachineNo . $rowGrandTotal, 'GRAND TOTAL');
        phpspreadsheet::styleFont($spreadsheet, $columnMachineNo . $rowGrandTotal . ':' . $columnHeaderEnd . $rowGrandTotal, true, 8, 'Calibri');

        $columnItem = $startColumnItemData;
        $columnItemEnd = chr(ord($columnItem) + count($header) - 1);
        // hari kerja
        $columnItem++;
        // kapasitas
        $columnItem++;
        // kapasitas (kg)
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['kapasitas_kg']);
        phpSpreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // produksi (kg)
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_produksi']);
        phpSpreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // rasio produksi (%)
        if ($grandTotal['kapasitas_kg'] == 0) {
            $activeWorksheet->setCellValue($columnItem . $rowGrandTotal, 0);
            $activeWorksheet->getStyle($columnItem . $rowGrandTotal)->getNumberFormat()->setFormatCode('0;-0;"-"');
        } else {
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, '=ROUND((' . $columnProduksiKg . $rowGrandTotal . '/' . $columnKapasitasHariKg . $rowGrandTotal . ')*100,0)');
            phpSpreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowGrandTotal);
        }
        $columnItem++;
        // kapasitas
        $columnItem++;
        // kapasitas (lembar)
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['kapasitas_lembar']);
        phpSpreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // produksi (lembar)
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['qty_produksi']);
        phpSpreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // rasio produksi (%)
        if ($grandTotal['kapasitas_lembar'] == 0) {
            $activeWorksheet->setCellValue($columnItem . $rowGrandTotal, 0);
            $activeWorksheet->getStyle($columnItem . $rowGrandTotal)->getNumberFormat()->setFormatCode('0;-0;"-"');
        } else {
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, '=ROUND((' . $columnProduksiLembar . $rowGrandTotal . '/' . $columnKapasitasHariLembar . $rowGrandTotal . ')*100,0)');
            phpSpreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowGrandTotal);
        }
        phpspreadsheet::addFullBorder($spreadsheet, $columnMachineNo . $rowGrandTotal . ':' . $columnItem . $rowGrandTotal);
        $columnItem++;

        $activeWorksheet->getStyle($columnMesin . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart)->getAlignment()->setWrapText(true);

        // auto size
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);

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
