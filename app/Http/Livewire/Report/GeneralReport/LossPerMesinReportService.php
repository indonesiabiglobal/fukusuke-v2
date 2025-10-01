<?php

namespace App\Http\Livewire\Report\GeneralReport;

use App\Helpers\phpspreadsheet;
use App\Models\MsJamMatiMesin;
use App\Models\MsMachine;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class LossPerMesinReportService
{
    public static function daftarLossPerMesinInfure($nippon, $jenisReport, $tglMasuk, $tglKeluar)
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
        $activeWorksheet->setCellValue('B1', 'DAFTAR LOSS PER MESIN INFURE');
        $activeWorksheet->setCellValue('B2', 'Periode : ' . $tglMasuk->format('d-M-Y H:i') . '  ~  ' . $tglKeluar->format('d-M-Y H:i'));
        // Style Judul
        phpspreadsheet::styleFont($spreadsheet, 'B1:B2', true, 11, 'Calibri');
        // hide column A
        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setVisible(false);

        // Header
        $columnMesin = 'B';
        $columnMesinEnd = 'C';
        $spreadsheet->getActiveSheet()->mergeCells($columnMesin . '3:' . $columnMesinEnd . '3');
        $activeWorksheet->setCellValue('B3', 'Klasifikasi');

        // header
        $rowHeaderStart = 3;
        $columnHeaderStart = 'D';
        $columnHeaderEnd = 'D';
        $header = [
            'Kode Loss',
            'Nama Loss',
            'Loss Produksi (Kg)',
            'Loss Kebutuhan (Kg)',
            'Frekuensi'
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
                max(mac.machineNo) AS machine_no,
                max(mac.machineNo || ' : ' || mac.machineName) AS machine_name,
                max(mslosCls.name) AS loss_class_name,
                max(mslos.code) AS loss_code,
                max(mslos.name) AS loss_name,
                SUM(CASE WHEN mslos.loss_category_code <> '1' THEN det.berat_loss ELSE 0 END) AS berat_loss_produksi,
                SUM(CASE WHEN mslos.loss_category_code = '1' THEN det.berat_loss ELSE 0 END) AS berat_loss_kebutuhan,
                SUM(det.frekuensi) AS frekuensi
            FROM tdProduct_Assembly AS asy
            INNER JOIN tdProduct_Assembly_Loss AS det ON asy.id = det.product_assembly_id
            INNER JOIN msLossInfure AS mslos ON det.loss_infure_id = mslos.id
            INNER JOIN msLossClass AS mslosCls ON mslos.loss_class_id = mslosCls.id
            INNER JOIN msMachine AS mac ON asy.machine_id = mac.id
            WHERE asy.production_date BETWEEN '$tglMasuk' AND '$tglKeluar'
            GROUP BY mac.id, det.loss_infure_id
            ORDER BY machine_no
        ");

        if (count($data) == 0) {
            $response = [
                'status' => 'error',
                'message' => "Data pada periode tanggal tersebut tidak ditemukan"
            ];

            return $response;
        }

        // list mesin
        $listMachine = array_reduce($data, function ($carry, $item) {
            $carry[$item->machine_no] = [
                'machine_no' => $item->machine_no,
                'machine_name' => $item->machine_name
            ];
            return $carry;
        }, []);

        // list klasifikasi
        $listLossClass = array_reduce($data, function ($carry, $item) {
            $carry[$item->machine_no][$item->loss_class_name] = $item->loss_class_name;
            return $carry;
        }, []);

        $dataFilter = array_reduce($data, function ($carry, $item) {
            // Periksa apakah machine_no sudah ada
            if (!isset($carry[$item->machine_no])) {
                $carry[$item->machine_no] = [];
            }

            // Periksa apakah loss_class_name sudah ada di machine_no tersebut
            if (!isset($carry[$item->machine_no][$item->loss_class_name])) {
                $carry[$item->machine_no][$item->loss_class_name] = [
                    'loss_class_name' => $item->loss_class_name,
                    'losses' => []  // Buat array untuk menampung beberapa loss_name
                ];
            }

            // Tambahkan loss_name ke dalam array 'losses'
            $carry[$item->machine_no][$item->loss_class_name]['losses'][] = [
                'loss_code' => $item->loss_code,
                'loss_name' => $item->loss_name,
                'berat_loss_produksi' => $item->berat_loss_produksi,
                'berat_loss_kebutuhan' => $item->berat_loss_kebutuhan,
                'frekuensi' => $item->frekuensi
            ];

            return $carry;
        }, []);


        // index
        $startColumnItem = 'B';
        $endColumnItem = $columnHeaderEnd;
        $startColumnItemData = 'D';
        $columnLossClass = 'B';
        $columnLossClassName = 'C';
        $startRowItem = 4;
        $rowItem = $startRowItem;
        // daftar departemen
        foreach ($listMachine as $machine) {
            // Menulis data departemen
            $activeWorksheet->setCellValue($startColumnItem . $rowItem, $machine['machine_name']);
            $spreadsheet->getActiveSheet()->mergeCells($startColumnItem . $rowItem . ':' . $endColumnItem . $rowItem);
            phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowItem, true, 9, 'Calibri');
            $rowItem++;
            $startRowItemSum = $rowItem;
            $startRowDepartment = $rowItem;
            // daftar mesin
            foreach ($listLossClass[$machine['machine_no']] as $lossClass) {
                if ($dataFilter[$machine['machine_no']][$lossClass] == null) {
                    continue;
                }
                // Menulis data mesin
                $spreadsheet->getActiveSheet()->mergeCells($columnLossClass . $rowItem . ':' . $columnLossClassName . $rowItem);
                $spreadsheet->getActiveSheet()->setCellValue($columnLossClass . $rowItem, $lossClass);

                // memasukkan data
                $dataItem = $dataFilter[$machine['machine_no']][$lossClass];

                foreach ($dataItem['losses'] as $item) {
                    $spreadsheet->getActiveSheet()->mergeCells($columnLossClass . $rowItem . ':' . $columnLossClassName . $rowItem);
                    $columnItem = $startColumnItemData;
                    // kode loss
                    $activeWorksheet->setCellValue($columnItem . $rowItem, $item['loss_code'] ?? '');
                    phpspreadsheet::textAlignCenter($spreadsheet, $columnItem . $rowItem);
                    $columnItem++;
                    // nama loss
                    $activeWorksheet->setCellValue($columnItem . $rowItem, $item['loss_name'] ?? '');
                    $columnItem++;
                    // loss produksi
                    $activeWorksheet->setCellValue($columnItem . $rowItem, $item['berat_loss_produksi']);
                    if ($item['berat_loss_produksi'] == 0) {
                        $activeWorksheet->getStyle($columnItem . $rowItem)->getNumberFormat()->setFormatCode('0;-0;"-"');
                    } else {
                        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                    }
                    $columnItem++;
                    // loss kebutuhan
                    $activeWorksheet->setCellValue($columnItem . $rowItem, $item['berat_loss_kebutuhan']);
                    if ($item['berat_loss_kebutuhan'] == 0) {
                        $activeWorksheet->getStyle($columnItem . $rowItem)->getNumberFormat()->setFormatCode('0;-0;"-"');
                    } else {
                        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                    }
                    $columnItem++;
                    // frekuensi
                    $activeWorksheet->setCellValue($columnItem . $rowItem, $item['frekuensi']);
                    phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
                    $rowItem++;
                }
            }
            phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $startRowDepartment . ':' . $columnItem . $rowItem, false, 8, 'Calibri');
            phpspreadsheet::addBorderDottedMiddleHorizontal($spreadsheet, $startColumnItem . $startRowDepartment . ':' . $columnItem . $rowItem);

            // perhitungan jumlah berdasarkan departemen
            $spreadsheet->getActiveSheet()->mergeCells($columnLossClass . $rowItem . ':' . 'E' . $rowItem);
            $activeWorksheet->setCellValue($columnLossClass . $rowItem, 'Total');
            $columnItem = $startColumnItemData;
            $columnItem++;
            // loss produksi
            $columnItem++;
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . $startRowItemSum . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // loss kebutuhan
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . $startRowItemSum . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // frekuensi
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . $startRowItemSum . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
            phpspreadsheet::addFullBorder($spreadsheet, $columnLossClass . $rowItem . ':' . $columnItem . $rowItem);
            phpspreadsheet::styleFont($spreadsheet, $columnLossClass . $rowItem . ':' . $columnItem . $rowItem, true, 8, 'Calibri');
            $columnItem++;

            $rowItem++;
            $rowItem++;
        }

        // Grand total
        $rowGrandTotal = $rowItem;
        $spreadsheet->getActiveSheet()->mergeCells($columnLossClass . $rowGrandTotal . ':' . 'E' . $rowGrandTotal);
        $spreadsheet->getActiveSheet()->setCellValue($columnLossClass . $rowGrandTotal, 'GRAND TOTAL');
        phpspreadsheet::styleFont($spreadsheet, $columnLossClass . $rowGrandTotal . ':' . $columnHeaderEnd . $rowGrandTotal, true, 8, 'Calibri');
        // $this->addFullBorder($spreadsheet, $columnLossClass . $rowGrandTotal . ':' . $columnValueAvg . $rowGrandTotal);

        $grandTotal = [
            'berat_loss_produksi' => 0,
            'berat_loss_kebutuhan' => 0,
            'frekuensi' => 0
        ];

        foreach ($dataFilter as $machineNo => $lossClasses) {
            foreach ($listLossClass[$machineNo] as $lossClass => $lossClassName) {
                if (isset($lossClasses[$lossClass])) {
                    $dataItem = $lossClasses[$lossClass];
                    foreach ($dataItem['losses'] as $item) {
                        $grandTotal['berat_loss_produksi'] += $item['berat_loss_produksi'];
                        $grandTotal['berat_loss_kebutuhan'] += $item['berat_loss_kebutuhan'];
                        $grandTotal['frekuensi'] += $item['frekuensi'];
                    }
                } else {
                    // Tambahkan default value jika $lossClass tidak ditemukan
                    $grandTotal['berat_loss_produksi'] += 0;
                    $grandTotal['berat_loss_kebutuhan'] += 0;
                    $grandTotal['frekuensi'] += 0;
                }
            }
        }

        $columnItem = $startColumnItemData;
        $columnItem++;
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_loss_produksi']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_loss_kebutuhan']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // frekuensi
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['frekuensi']);
        phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowGrandTotal);
        phpspreadsheet::addFullBorder($spreadsheet, $columnLossClass . $rowGrandTotal . ':' . $columnItem . $rowGrandTotal);
        phpspreadsheet::addFullBorder($spreadsheet, $columnLossClass . $rowGrandTotal . ':' . $columnItem . $rowGrandTotal);
        $columnItem++;

        $activeWorksheet->getStyle($columnMesin . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart)->getAlignment()->setWrapText(true);

        // size auto
        $endColumnItem++;
        while ($startColumnItemData !== $endColumnItem) {
            $spreadsheet->getActiveSheet()->getColumnDimension($startColumnItemData)->setAutoSize(true);
            $startColumnItemData++;
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

    public static function daftarLossPerMesinSeitai($nippon, $jenisReport, $tglMasuk, $tglKeluar)
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
        $activeWorksheet->setCellValue('B1', 'DAFTAR LOSS PER MESIN SEITAI');
        $activeWorksheet->setCellValue('B2', 'Periode : ' . $tglMasuk->format('d-M-Y H:i') . '  ~  ' . $tglKeluar->format('d-M-Y H:i'));
        // Style Judul
        phpspreadsheet::styleFont($spreadsheet, 'B1:B2', true, 11, 'Calibri');
        // hide column A
        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setVisible(false);

        // Header
        $columnMesin = 'B';
        $columnMesinEnd = 'C';
        $spreadsheet->getActiveSheet()->mergeCells($columnMesin . '3:' . $columnMesinEnd . '3');
        $activeWorksheet->setCellValue('B3', 'Klasifikasi');

        // header
        $rowHeaderStart = 3;
        $columnHeaderStart = 'D';
        $columnHeaderEnd = 'D';
        $header = [
            'Kode Loss',
            'Nama Loss',
            'Loss Produksi (Kg)',
            'Loss Kebutuhan (Kg)',
            'Frekuensi'
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
                max(mac.machineNo) AS machine_no,
                max(mac.machineNo || ' : ' || mac.machineName) AS machine_name,
                max(mslosCls.name) AS loss_class_name,
                max(mslos.code) AS loss_code,
                max(mslos.name) AS loss_name,
                SUM(CASE WHEN mslos.loss_category_code <> '1' THEN det.berat_loss ELSE 0 END) AS berat_loss_produksi,
                SUM(CASE WHEN mslos.loss_category_code = '1' THEN det.berat_loss ELSE 0 END) AS berat_loss_kebutuhan,
                SUM(det.frekuensi) AS frekuensi
            FROM tdProduct_Goods AS good
            INNER JOIN tdProduct_Goods_Loss AS det ON good.id = det.product_goods_id
            INNER JOIN msLossSeitai AS mslos ON det.loss_seitai_id = mslos.id
            INNER JOIN msLossClass AS mslosCls ON mslos.loss_class_id = mslosCls.id
            INNER JOIN msMachine AS mac ON good.machine_id = mac.id
            WHERE good.production_date BETWEEN '$tglMasuk' AND '$tglKeluar'
            GROUP BY mac.id, det.loss_seitai_id
            ORDER BY machine_no
        ");

        if (count($data) == 0) {
            $response = [
                'status' => 'error',
                'message' => "Data pada periode tanggal tersebut tidak ditemukan"
            ];

            return $response;
        }

        // list mesin
        $listMachine = array_reduce($data, function ($carry, $item) {
            $carry[$item->machine_no] = [
                'machine_no' => $item->machine_no,
                'machine_name' => $item->machine_name
            ];
            return $carry;
        }, []);

        // list klasifikasi
        $listLossClass = array_reduce($data, function ($carry, $item) {
            $carry[$item->machine_no][$item->loss_class_name] = $item->loss_class_name;
            return $carry;
        }, []);

        $dataFilter = array_reduce($data, function ($carry, $item) {
            // Periksa apakah machine_no sudah ada
            if (!isset($carry[$item->machine_no])) {
                $carry[$item->machine_no] = [];
            }

            // Periksa apakah loss_class_name sudah ada di machine_no tersebut
            if (!isset($carry[$item->machine_no][$item->loss_class_name])) {
                $carry[$item->machine_no][$item->loss_class_name] = [
                    'loss_class_name' => $item->loss_class_name,
                    'losses' => []  // Buat array untuk menampung beberapa loss_name
                ];
            }

            // Tambahkan loss_name ke dalam array 'losses'
            $carry[$item->machine_no][$item->loss_class_name]['losses'][] = [
                'loss_code' => $item->loss_code,
                'loss_name' => $item->loss_name,
                'berat_loss_produksi' => $item->berat_loss_produksi,
                'berat_loss_kebutuhan' => $item->berat_loss_kebutuhan,
                'frekuensi' => $item->frekuensi
            ];

            return $carry;
        }, []);


        // index
        $startColumnItem = 'B';
        $endColumnItem = $columnHeaderEnd;
        $startColumnItemData = 'D';
        $columnLossClass = 'B';
        $columnLossClassName = 'C';
        $startRowItem = 4;
        $rowItem = $startRowItem;
        // daftar departemen
        foreach ($listMachine as $machine) {
            // Menulis data departemen
            $activeWorksheet->setCellValue($startColumnItem . $rowItem, $machine['machine_name']);
            $spreadsheet->getActiveSheet()->mergeCells($startColumnItem . $rowItem . ':' . $endColumnItem . $rowItem);
            phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowItem, true, 9, 'Calibri');
            $rowItem++;
            $startRowItemSum = $rowItem;
            // daftar mesin
            $startRowDepartment = $rowItem;
            foreach ($listLossClass[$machine['machine_no']] as $lossClass) {
                if ($dataFilter[$machine['machine_no']][$lossClass] == null) {
                    continue;
                }
                // Menulis data mesin
                $spreadsheet->getActiveSheet()->mergeCells($columnLossClass . $rowItem . ':' . $columnLossClassName . $rowItem);
                $spreadsheet->getActiveSheet()->setCellValue($columnLossClass . $rowItem, $lossClass);
                // phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);

                // memasukkan data
                $dataItem = $dataFilter[$machine['machine_no']][$lossClass];

                foreach ($dataItem['losses'] as $item) {
                    $spreadsheet->getActiveSheet()->mergeCells($columnLossClass . $rowItem . ':' . $columnLossClassName . $rowItem);
                    $columnItem = $startColumnItemData;
                    // kode loss
                    $activeWorksheet->setCellValue($columnItem . $rowItem, $item['loss_code'] ?? '');
                    phpspreadsheet::textAlignCenter($spreadsheet, $columnItem . $rowItem);
                    $columnItem++;
                    // nama loss
                    $activeWorksheet->setCellValue($columnItem . $rowItem, $item['loss_name'] ?? '');
                    $columnItem++;
                    // loss produksi
                    $activeWorksheet->setCellValue($columnItem . $rowItem, $item['berat_loss_produksi']);
                    if ($item['berat_loss_produksi'] == 0) {
                        $activeWorksheet->getStyle($columnItem . $rowItem)->getNumberFormat()->setFormatCode('0;-0;"-"');
                    } else {
                        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                    }
                    $columnItem++;
                    // loss kebutuhan
                    $activeWorksheet->setCellValue($columnItem . $rowItem, $item['berat_loss_kebutuhan']);
                    if ($item['berat_loss_kebutuhan'] == 0) {
                        $activeWorksheet->getStyle($columnItem . $rowItem)->getNumberFormat()->setFormatCode('0;-0;"-"');
                    } else {
                        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                    }
                    $columnItem++;
                    // frekuensi
                    $activeWorksheet->setCellValue($columnItem . $rowItem, $item['frekuensi']);
                    phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
                    // Terapkan custom format untuk mengganti tampilan 0 dengan -
                    $rowItem++;
                }
            }
            phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $startRowDepartment . ':' . $columnItem . $rowItem, false, 8, 'Calibri');
            phpspreadsheet::addBorderDottedMiddleHorizontal($spreadsheet, $startColumnItem . $startRowDepartment . ':' . $columnItem . $rowItem);

            // perhitungan jumlah berdasarkan departemen
            $spreadsheet->getActiveSheet()->mergeCells($columnLossClass . $rowItem . ':' . 'E' . $rowItem);
            $activeWorksheet->setCellValue($columnLossClass . $rowItem, 'Total');
            $columnItem = $startColumnItemData;
            $columnItem++;
            // loss produksi
            $columnItem++;
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . $startRowItemSum . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // loss kebutuhan
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . $startRowItemSum . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // frekuensi
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . $startRowItemSum . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
            phpspreadsheet::addFullBorder($spreadsheet, $columnLossClass . $rowItem . ':' . $columnItem . $rowItem);
            phpspreadsheet::styleFont($spreadsheet, $columnLossClass . $rowItem . ':' . $columnItem . $rowItem, true, 8, 'Calibri');
            $columnItem++;

            $rowItem++;
            $rowItem++;
        }

        // Grand total
        $rowGrandTotal = $rowItem;
        $spreadsheet->getActiveSheet()->mergeCells($columnLossClass . $rowGrandTotal . ':' . 'E' . $rowGrandTotal);
        $spreadsheet->getActiveSheet()->setCellValue($columnLossClass . $rowGrandTotal, 'GRAND TOTAL');
        phpspreadsheet::styleFont($spreadsheet, $columnLossClass . $rowGrandTotal . ':' . $columnHeaderEnd . $rowGrandTotal, true, 8, 'Calibri');
        // $this->addFullBorder($spreadsheet, $columnLossClass . $rowGrandTotal . ':' . $columnValueAvg . $rowGrandTotal);

        $grandTotal = [
            'berat_loss_produksi' => 0,
            'berat_loss_kebutuhan' => 0,
            'frekuensi' => 0
        ];

        foreach ($dataFilter as $machineNo => $lossClasses) {
            foreach ($listLossClass[$machineNo] as $lossClass => $lossClassName) {
                if (isset($lossClasses[$lossClass])) {
                    $dataItem = $lossClasses[$lossClass];
                    foreach ($dataItem['losses'] as $item) {
                        $grandTotal['berat_loss_produksi'] += $item['berat_loss_produksi'];
                        $grandTotal['berat_loss_kebutuhan'] += $item['berat_loss_kebutuhan'];
                        $grandTotal['frekuensi'] += $item['frekuensi'];
                    }
                } else {
                    // Tambahkan default value jika $lossClass tidak ditemukan
                    $grandTotal['berat_loss_produksi'] += 0;
                    $grandTotal['berat_loss_kebutuhan'] += 0;
                    $grandTotal['frekuensi'] += 0;
                }
            }
        }

        $columnItem = $startColumnItemData;
        $columnItem++;
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_loss_produksi']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_loss_kebutuhan']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // frekuensi
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['frekuensi']);
        phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowGrandTotal);
        phpspreadsheet::addFullBorder($spreadsheet, $columnLossClass . $rowGrandTotal . ':' . $columnItem . $rowGrandTotal);
        phpspreadsheet::addFullBorder($spreadsheet, $columnLossClass . $rowGrandTotal . ':' . $columnItem . $rowGrandTotal);
        $columnItem++;

        $activeWorksheet->getStyle($columnMesin . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart)->getAlignment()->setWrapText(true);

        // size auto
        $endColumnItem++;
        while ($startColumnItemData !== $endColumnItem) {
            $spreadsheet->getActiveSheet()->getColumnDimension($startColumnItemData)->setAutoSize(true);
            $startColumnItemData++;
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
