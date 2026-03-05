<?php

namespace App\Http\Livewire\Report\GeneralReport;

use App\Helpers\phpspreadsheet;
use App\Models\MsJamMatiMesin;
use App\Models\MsMachine;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class LossPerPetugasReportService
{
    public static function daftarLossPerPetugasInfure($nippon, $jenisReport, $tglMasuk, $tglKeluar)
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
        $activeWorksheet->setCellValue('B1', 'DAFTAR LOSS PER PETUGAS INFURE');
        $activeWorksheet->setCellValue('B2', 'Periode : ' . $tglMasuk->format('d-M-Y H:i') . '  ~  ' . $tglKeluar->format('d-M-Y H:i'));
        // Style Judul
        phpspreadsheet::styleFont($spreadsheet, 'B1:B2', true, 11, 'Calibri');
        // hide column A
        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setVisible(false);

        // Header
        $columnMesin = 'B';
        $columnMesinEnd = 'C';
        $spreadsheet->getActiveSheet()->mergeCells($columnMesin . '3:' . $columnMesinEnd . '3');
        $activeWorksheet->setCellValue('B3', 'Petugas (NIK, Nama)');

        // header
        $rowHeaderStart = 3;
        $columnHeaderStart = 'D';
        $columnHeaderEnd = 'D';
        $header = [
            'Produksi (Kg)',
            'Total Loss (Kg)',
            'Presentase Loss(%)',
            'Katagae (Kg)',
            'Kualitas (Kg)',
            'Lain-lain (Kg)',
            'Mesin (Kg)',
            'Orang (Kg)',
            'Printing (Kg)',
            'Tachiage (Kg)',
            'Loss Infure di Seitai (Kg)',
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
            WITH loss_summary AS (
                SELECT
                    los_.product_assembly_id,
                    SUM ( CASE WHEN mslosCls.code = '01' THEN los_.berat_loss ELSE 0 END ) AS berat_loss_katagae,
                    SUM ( CASE WHEN mslosCls.code = '03' THEN los_.berat_loss ELSE 0 END ) AS berat_loss_kualitas,
                    SUM ( CASE WHEN mslosCls.code = '09' THEN los_.berat_loss ELSE 0 END ) AS berat_loss_lainlain,
                    SUM ( CASE WHEN mslosCls.code = '07' THEN los_.berat_loss ELSE 0 END ) AS berat_loss_mesin,
                    SUM ( CASE WHEN mslosCls.code = '08' THEN los_.berat_loss ELSE 0 END ) AS berat_loss_orang,
                    SUM ( CASE WHEN mslosCls.code = '05' THEN los_.berat_loss ELSE 0 END ) AS berat_loss_printing,
                    SUM ( CASE WHEN mslosCls.code = '02' THEN los_.berat_loss ELSE 0 END ) AS berat_loss_tachiage,
                    SUM ( los_.frekuensi ) AS frekuensi
                FROM
                    tdProduct_Assembly_Loss AS los_
                    INNER JOIN msLossInfure AS mslos ON los_.loss_infure_id = mslos.
                    ID INNER JOIN msLossClass AS mslosCls ON mslos.loss_class_id = mslosCls.ID
                GROUP BY
                    los_.product_assembly_id
                ),
                loss_sitai_summary AS (
                SELECT
                    good.employee_id_infure,
                    SUM ( good.infure_berat_loss ) AS infure_berat_loss
                FROM
                    tdProduct_Goods AS good
                WHERE
                    good.production_date BETWEEN '$tglMasuk' AND '$tglKeluar'
                GROUP BY
                    good.employee_id_infure
                ) SELECT
                dep.NAME AS department_name,
                dep.id AS department_id,
                mac.employeeNo,
                mac.empName,
                SUM ( asy.berat_produksi ) AS berat_produksi,
                SUM ( asy.infure_berat_loss ) AS infure_berat_loss,
                COALESCE ( SUM ( loss_summary.berat_loss_katagae ), 0 ) AS berat_loss_katagae,
                COALESCE ( SUM ( loss_summary.berat_loss_kualitas ), 0 ) AS berat_loss_kualitas,
                COALESCE ( SUM ( loss_summary.berat_loss_lainlain ), 0 ) AS berat_loss_lainlain,
                COALESCE ( SUM ( loss_summary.berat_loss_mesin ), 0 ) AS berat_loss_mesin,
                COALESCE ( SUM ( loss_summary.berat_loss_orang ), 0 ) AS berat_loss_orang,
                COALESCE ( SUM ( loss_summary.berat_loss_printing ), 0 ) AS berat_loss_printing,
                COALESCE ( SUM ( loss_summary.berat_loss_tachiage ), 0 ) AS berat_loss_tachiage,
                COALESCE ( SUM ( loss_sitai_summary.infure_berat_loss ), 0 ) AS seitai_infure_berat_loss,
                COALESCE ( SUM ( loss_summary.frekuensi ), 0 ) AS frekuensi
            FROM
                tdProduct_Assembly AS asy
                LEFT JOIN loss_summary ON asy.ID = loss_summary.product_assembly_id
                LEFT JOIN loss_sitai_summary ON asy.employee_id = loss_sitai_summary.employee_id_infure
                INNER JOIN msEmployee AS mac ON asy.employee_id = mac.
                ID INNER JOIN msDepartment AS dep ON mac.department_id = dep.ID
            WHERE
                asy.production_date BETWEEN '$tglMasuk' AND '$tglKeluar'
            GROUP BY
                dep.NAME,
                dep.id,
                mac.employeeNo,
                mac.empName
            ORDER BY
                dep.NAME,
                mac.employeeNo;
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

        // list petugas
        $listEmployee = array_reduce($data, function ($carry, $item) {
            $carry[$item->department_id][$item->employeeno] = $item->empname;
            return $carry;
        }, []);

        $dataFilter = array_reduce($data, function ($carry, $item) {
            // Periksa apakah department_id sudah ada
            if (!isset($carry[$item->department_id])) {
                $carry[$item->department_id] = [];
            }

            // Periksa apakah employeeno sudah ada di department_id tersebut
            if (!isset($carry[$item->department_id][$item->employeeno])) {
                $carry[$item->department_id][$item->employeeno] = [
                    'employeeno' => $item->employeeno,
                    'empname' => $item->empname,
                    'berat_produksi' => $item->berat_produksi,
                    'infure_berat_loss' => $item->infure_berat_loss,
                    'berat_loss_katagae' => $item->berat_loss_katagae,
                    'berat_loss_tachiage' => $item->berat_loss_tachiage,
                    'berat_loss_kualitas' => $item->berat_loss_kualitas,
                    'berat_loss_printing' => $item->berat_loss_printing,
                    'berat_loss_mesin' => $item->berat_loss_mesin,
                    'berat_loss_orang' => $item->berat_loss_orang,
                    'berat_loss_lainlain' => $item->berat_loss_lainlain,
                    'seitai_infure_berat_loss' => $item->seitai_infure_berat_loss,
                    'frekuensi' => $item->frekuensi
                ];
            }

            return $carry;
        }, []);


        // index
        $startColumnItem = 'B';
        $endColumnItem = $columnHeaderEnd;
        $startColumnItemData = 'D';
        $columnEmployee = 'B';
        $columnEmployeeName = 'C';
        $startRowItem = 4;
        $rowItem = $startRowItem;
        // daftar departemen
        foreach ($listDepartment as $department) {
            // Menulis data departemen
            $activeWorksheet->setCellValue($startColumnItem . $rowItem, $department['department_name']);
            $spreadsheet->getActiveSheet()->mergeCells($startColumnItem . $rowItem . ':' . $endColumnItem . $rowItem);
            phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowItem, true, 9, 'Calibri');
            $rowItem++;
            $startRowItemSum = $rowItem;
            // daftar petugas
            foreach ($listEmployee[$department['department_id']] as $employeeNo => $employeeName) {
                if ($dataFilter[$department['department_id']][$employeeNo] == null) {
                    continue;
                }
                // Menulis data petugas
                // $spreadsheet->getActiveSheet()->mergeCells($columnEmployee . $rowItem . ':' . $columnEmployeeName . $rowItem);
                $spreadsheet->getActiveSheet()->setCellValue($columnEmployee . $rowItem, $employeeNo);
                phpspreadsheet::textAlignCenter($spreadsheet, $columnEmployee . $rowItem);
                $spreadsheet->getActiveSheet()->setCellValue($columnEmployeeName . $rowItem, $employeeName);
                // phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);

                // memasukkan data
                $dataItem = $dataFilter[$department['department_id']][$employeeNo];

                // $spreadsheet->getActiveSheet()->mergeCells($columnEmployee . $rowItem . ':' . $columnEmployeeName . $rowItem);
                $columnItem = $startColumnItemData;
                // produksi
                $columnProduksi = $columnItem;
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem['berat_produksi']);
                phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // total loss
                $columnTotalLoss = $columnItem;
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem['infure_berat_loss']);
                phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // presentase loss
                $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=IF(' . $columnProduksi . $rowItem . '=0, 0, ' . $columnTotalLoss . $rowItem . '/(' . $columnProduksi . $rowItem . ' + ' . $columnTotalLoss . $rowItem . ')*100)');
                phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // katagae
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem['berat_loss_katagae']);
                if ($dataItem['berat_loss_katagae'] == 0) {
                    $activeWorksheet->getStyle($columnItem . $rowItem)->getNumberFormat()->setFormatCode('0;-0;"-"');
                } else {
                    phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                }
                $columnItem++;
                // kualitas
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem['berat_loss_kualitas']);
                if ($dataItem['berat_loss_kualitas'] == 0) {
                    $activeWorksheet->getStyle($columnItem . $rowItem)->getNumberFormat()->setFormatCode('0;-0;"-"');
                } else {
                    phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                }
                $columnItem++;
                // lain-lain
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem['berat_loss_lainlain']);
                if ($dataItem['berat_loss_lainlain'] == 0) {
                    $activeWorksheet->getStyle($columnItem . $rowItem)->getNumberFormat()->setFormatCode('0;-0;"-"');
                } else {
                    phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                }
                $columnItem++;
                // mesin
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem['berat_loss_mesin']);
                if ($dataItem['berat_loss_mesin'] == 0) {
                    $activeWorksheet->getStyle($columnItem . $rowItem)->getNumberFormat()->setFormatCode('0;-0;"-"');
                } else {
                    phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                }
                $columnItem++;
                // orang
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem['berat_loss_orang']);
                if ($dataItem['berat_loss_orang'] == 0) {
                    $activeWorksheet->getStyle($columnItem . $rowItem)->getNumberFormat()->setFormatCode('0;-0;"-"');
                } else {
                    phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                }
                $columnItem++;
                // printing
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem['berat_loss_printing']);
                if ($dataItem['berat_loss_printing'] == 0) {
                    $activeWorksheet->getStyle($columnItem . $rowItem)->getNumberFormat()->setFormatCode('0;-0;"-"');
                } else {
                    phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                }
                $columnItem++;
                // tachiage
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem['berat_loss_tachiage']);
                if ($dataItem['berat_loss_tachiage'] == 0) {
                    $activeWorksheet->getStyle($columnItem . $rowItem)->getNumberFormat()->setFormatCode('0;-0;"-"');
                } else {
                    phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                }
                $columnItem++;
                // loss infure di seitai
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem['seitai_infure_berat_loss']);
                if ($dataItem['seitai_infure_berat_loss'] == 0) {
                    $activeWorksheet->getStyle($columnItem . $rowItem)->getNumberFormat()->setFormatCode('0;-0;"-"');
                } else {
                    phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                }
                $columnItem++;
                // frekuensi
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem['frekuensi']);
                phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
                $rowItem++;
            }
            phpspreadsheet::addBorderDottedMiddleHorizontal($spreadsheet, $startColumnItem . $startRowItemSum . ':' . $columnItem . $rowItem);
            phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $startRowItemSum . ':' . $columnItem . $rowItem, false, 8, 'Calibri');
            // perhitungan jumlah berdasarkan departemen
            $spreadsheet->getActiveSheet()->mergeCells($columnEmployee . $rowItem . ':' . 'C' . $rowItem);
            $activeWorksheet->setCellValue($columnEmployee . $rowItem, 'Total');
            $columnItem = $startColumnItemData;
            // produksi
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . $startRowItemSum . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // total loss
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . $startRowItemSum . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // presentase loss
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=IF(' . $columnProduksi . $rowItem . '=0, 0, ' . $columnTotalLoss . $rowItem . '/(' . $columnProduksi . $rowItem . ' + ' . $columnTotalLoss . $rowItem . ')*100)');
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // katagae
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . $startRowItemSum . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // kualitas
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . $startRowItemSum . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // lain-lain
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . $startRowItemSum . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // mesin
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . $startRowItemSum . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // orang
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . $startRowItemSum . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // printing
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . $startRowItemSum . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // tachiage
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . $startRowItemSum . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // loss infure di seitai
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . $startRowItemSum . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // frekuensi
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . $startRowItemSum . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
            phpspreadsheet::addFullBorder($spreadsheet, $columnEmployee . $rowItem . ':' . $columnItem . $rowItem);
            phpspreadsheet::styleFont($spreadsheet, $columnEmployee . $rowItem . ':' . $columnItem . $rowItem, true, 8, 'Calibri');
            $columnItem++;

            $rowItem++;
            $rowItem++;
        }

        // Grand total
        $rowGrandTotal = $rowItem;
        $spreadsheet->getActiveSheet()->mergeCells($columnEmployee . $rowGrandTotal . ':' . 'C' . $rowGrandTotal);
        $spreadsheet->getActiveSheet()->setCellValue($columnEmployee . $rowGrandTotal, 'GRAND TOTAL');
        phpspreadsheet::styleFont($spreadsheet, $columnEmployee . $rowGrandTotal . ':' . $columnHeaderEnd . $rowGrandTotal, true, 8, 'Calibri');
        // $this->addFullBorder($spreadsheet, $columnEmployee . $rowGrandTotal . ':' . $columnValueAvg . $rowGrandTotal);

        $grandTotal = [
            'berat_produksi' => 0,
            'infure_berat_loss' => 0,
            'berat_loss_katagae' => 0,
            'berat_loss_tachiage' => 0,
            'berat_loss_kualitas' => 0,
            'berat_loss_printing' => 0,
            'berat_loss_mesin' => 0,
            'berat_loss_orang' => 0,
            'berat_loss_lainlain' => 0,
            'seitai_infure_berat_loss' => 0,
            'frekuensi' => 0
        ];

        foreach ($dataFilter as $departmentId => $Employeees) {
            foreach ($listEmployee[$departmentId] as $EmployeeNo => $EmployeeName) {
                if (isset($Employeees[$EmployeeNo])) {
                    $dataItem = $Employeees[$EmployeeNo];
                    $grandTotal['berat_produksi'] += $dataItem['berat_produksi'];
                    $grandTotal['infure_berat_loss'] += $dataItem['infure_berat_loss'];
                    $grandTotal['berat_loss_katagae'] += $dataItem['berat_loss_katagae'];
                    $grandTotal['berat_loss_tachiage'] += $dataItem['berat_loss_tachiage'];
                    $grandTotal['berat_loss_kualitas'] += $dataItem['berat_loss_kualitas'];
                    $grandTotal['berat_loss_printing'] += $dataItem['berat_loss_printing'];
                    $grandTotal['berat_loss_mesin'] += $dataItem['berat_loss_mesin'];
                    $grandTotal['berat_loss_orang'] += $dataItem['berat_loss_orang'];
                    $grandTotal['berat_loss_lainlain'] += $dataItem['berat_loss_lainlain'];
                    $grandTotal['seitai_infure_berat_loss'] += $dataItem['seitai_infure_berat_loss'];
                    $grandTotal['frekuensi'] += $dataItem['frekuensi'];
                } else {
                    // Tambahkan default value jika $Employee tidak ditemukan
                    $grandTotal['berat_produksi'] += 0;
                    $grandTotal['infure_berat_loss'] += 0;
                    $grandTotal['berat_loss_katagae'] += 0;
                    $grandTotal['berat_loss_tachiage'] += 0;
                    $grandTotal['berat_loss_kualitas'] += 0;
                    $grandTotal['berat_loss_printing'] += 0;
                    $grandTotal['berat_loss_mesin'] += 0;
                    $grandTotal['berat_loss_orang'] += 0;
                    $grandTotal['berat_loss_lainlain'] += 0;
                    $grandTotal['seitai_infure_berat_loss'] += 0;
                    $grandTotal['frekuensi'] += 0;
                }
            }
        }

        $columnItem = $startColumnItemData;
        // produksi
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_produksi']);
        phpspreadsheet::numberFormatCommaThousandsOrZero($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // total loss
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['infure_berat_loss']);
        phpspreadsheet::numberFormatCommaThousandsOrZero($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // presentase loss
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, '=IF(' . $columnProduksi . $rowGrandTotal . '=0, 0, ' . $columnTotalLoss . $rowGrandTotal . '/(' . $columnProduksi . $rowGrandTotal . ' + ' . $columnTotalLoss . $rowGrandTotal . ')*100)');
        phpspreadsheet::numberFormatCommaThousandsOrZero($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // katagae
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_loss_katagae']);
        phpspreadsheet::numberFormatCommaThousandsOrZero($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // kualitas
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_loss_kualitas']);
        phpspreadsheet::numberFormatCommaThousandsOrZero($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // lain-lain
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_loss_lainlain']);
        phpspreadsheet::numberFormatCommaThousandsOrZero($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // mesin
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_loss_mesin']);
        phpspreadsheet::numberFormatCommaThousandsOrZero($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // orang
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_loss_orang']);
        phpspreadsheet::numberFormatCommaThousandsOrZero($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // printing
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_loss_printing']);
        phpspreadsheet::numberFormatCommaThousandsOrZero($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // tachiage
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_loss_tachiage']);
        phpspreadsheet::numberFormatCommaThousandsOrZero($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // loss infure di seitai
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['seitai_infure_berat_loss']);
        phpspreadsheet::numberFormatCommaThousandsOrZero($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // frekuensi
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['frekuensi']);
        phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowGrandTotal);
        phpspreadsheet::addFullBorder($spreadsheet, $columnEmployee . $rowGrandTotal . ':' . $columnItem . $rowGrandTotal);
        $columnItem++;

        $activeWorksheet->getStyle($columnMesin . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart)->getAlignment()->setWrapText(true);

        // set specific column widths (pixels -> approximate Excel column width)
        $pixelToExcelWidth = function ($pixels) {
            // Approx conversion: excelColumnWidth ≈ (pixels - 5) / 7
            $w = ($pixels - 5) / 7;
            return $w > 0 ? $w : 1;
        };

        $pixelWidths = [
            'B' => 72,
            'D' => 84,
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

    public static function daftarLossPerPetugasSeitai($nippon, $jenisReport, $tglMasuk, $tglKeluar)
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
        $activeWorksheet->setCellValue('B1', 'DAFTAR LOSS PER PETUGAS SEITAI');
        $activeWorksheet->setCellValue('B2', 'Periode : ' . $tglMasuk->format('d-M-Y H:i') . '  ~  ' . $tglKeluar->format('d-M-Y H:i'));
        // Style Judul
        phpspreadsheet::styleFont($spreadsheet, 'B1:B2', true, 11, 'Calibri');
        // hide column A
        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setVisible(false);

        // Header
        $columnMesin = 'B';
        $columnMesinEnd = 'C';
        $spreadsheet->getActiveSheet()->mergeCells($columnMesin . '3:' . $columnMesinEnd . '3');
        $activeWorksheet->setCellValue('B3', 'Petugas (NIK, Nama)');

        // header
        $rowHeaderStart = 3;
        $columnHeaderStart = 'D';
        $columnHeaderEnd = 'D';
        $header = [
            'Produksi (Kg)',
            'Total Loss (Kg)',
            'Presentase Loss (%)',
            'Katanuki (Kg)',
            'Kualitas (Kg)',
            'Mesin (Kg)',
            'Lain-lain (Kg)',
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
            WITH LossAggregates AS (
                SELECT
                    los_.product_goods_id,
                    SUM(CASE WHEN mslosCls.code = '24' THEN los_.berat_loss ELSE 0 END) AS berat_loss_katanuki,
                    SUM(CASE WHEN mslosCls.code = '03' THEN los_.berat_loss ELSE 0 END) AS berat_loss_kualitas,
                    SUM(CASE WHEN mslosCls.code = '07' THEN los_.berat_loss ELSE 0 END) AS berat_loss_mesin,
                    SUM(CASE WHEN mslosCls.code = '09' THEN los_.berat_loss ELSE 0 END) AS berat_loss_lainlain,
                    SUM(los_.frekuensi) AS frekuensi
                FROM tdProduct_Goods_Loss AS los_
                INNER JOIN msLossSeitai AS mslos ON los_.loss_seitai_id = mslos.id
                INNER JOIN msLossClass AS mslosCls ON mslos.loss_class_id = mslosCls.id
                WHERE mslos.id <> 1
                GROUP BY los_.product_goods_id
            )
            SELECT
                dep.name AS department_name,
                dep.id AS department_id,
                mac.employeeNo AS employeeNo,
                mac.empName AS empName,
                SUM(good.qty_produksi * prd.unit_weight * 0.001) AS berat_produksi,
                SUM(good.seitai_berat_loss) AS seitai_berat_loss,
                COALESCE(SUM(loss.berat_loss_katanuki), 0) AS berat_loss_katanuki,
                COALESCE(SUM(loss.berat_loss_kualitas), 0) AS berat_loss_kualitas,
                COALESCE(SUM(loss.berat_loss_mesin), 0) AS berat_loss_mesin,
                COALESCE(SUM(loss.berat_loss_lainlain), 0) AS berat_loss_lainlain,
                COALESCE(SUM(loss.frekuensi), 0) AS frekuensi
            FROM tdProduct_Goods AS good
            INNER JOIN msEmployee AS mac ON good.employee_id = mac.id
            INNER JOIN msDepartment AS dep ON mac.department_id = dep.id
            INNER JOIN msProduct AS prd ON good.product_id = prd.id
            LEFT JOIN LossAggregates AS loss ON good.id = loss.product_goods_id
            WHERE good.production_date BETWEEN '$tglMasuk' AND '$tglKeluar'
            GROUP BY dep.name,dep.id, mac.employeeNo, mac.empName
            ORDER BY dep.name, mac.employeeNo;
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

        // list petugas
        $listEmployee = array_reduce($data, function ($carry, $item) {
            $carry[$item->department_id][$item->employeeno] = $item->empname;
            return $carry;
        }, []);

        $dataFilter = array_reduce($data, function ($carry, $item) {
            // Periksa apakah department_id sudah ada
            if (!isset($carry[$item->department_id])) {
                $carry[$item->department_id] = [];
            }

            // Periksa apakah employeeno sudah ada di department_id tersebut
            if (!isset($carry[$item->department_id][$item->employeeno])) {
                $carry[$item->department_id][$item->employeeno] = [
                    'employeeno' => $item->employeeno,
                    'empname' => $item->empname,
                    'berat_produksi' => $item->berat_produksi,
                    'seitai_berat_loss' => $item->seitai_berat_loss,
                    'berat_loss_katanuki' => $item->berat_loss_katanuki,
                    'berat_loss_kualitas' => $item->berat_loss_kualitas,
                    'berat_loss_mesin' => $item->berat_loss_mesin,
                    'berat_loss_lainlain' => $item->berat_loss_lainlain,
                    'frekuensi' => $item->frekuensi
                ];
            }

            return $carry;
        }, []);


        // index
        $startColumnItem = 'B';
        $endColumnItem = $columnHeaderEnd;
        $startColumnItemData = 'D';
        $columnEmployee = 'B';
        $columnEmployeeName = 'C';
        $startRowItem = 4;
        $rowItem = $startRowItem;
        // daftar departemen
        foreach ($listDepartment as $department) {
            // Menulis data departemen
            $activeWorksheet->setCellValue($startColumnItem . $rowItem, $department['department_name']);
            $spreadsheet->getActiveSheet()->mergeCells($startColumnItem . $rowItem . ':' . $endColumnItem . $rowItem);
            phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowItem, true, 9, 'Calibri');
            $rowItem++;
            $startRowItemSum = $rowItem;
            // daftar petugas
            foreach ($listEmployee[$department['department_id']] as $employeeNo => $employeeName) {
                if ($dataFilter[$department['department_id']][$employeeNo] == null) {
                    continue;
                }
                // Menulis data petugas
                $spreadsheet->getActiveSheet()->setCellValue($columnEmployee . $rowItem, $employeeNo);
                phpspreadsheet::textAlignCenter($spreadsheet, $columnEmployee . $rowItem);
                $spreadsheet->getActiveSheet()->setCellValue($columnEmployeeName . $rowItem, $employeeName);

                // memasukkan data
                $dataItem = $dataFilter[$department['department_id']][$employeeNo];

                $columnItem = $startColumnItemData;
                // produksi
                $columnProduksi = $columnItem;
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem['berat_produksi']);
                phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // total loss
                $columnTotalLoss = $columnItem;
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem['seitai_berat_loss']);
                phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // presentase loss
                $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=IF(' . $columnProduksi . $rowItem . '=0, 0, ' . $columnTotalLoss . $rowItem . '/(' . $columnProduksi . $rowItem . ' + ' . $columnTotalLoss . $rowItem . ')*100)');
                phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // katanuki
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem['berat_loss_katanuki']);
                if ($dataItem['berat_loss_katanuki'] == 0) {
                    $activeWorksheet->getStyle($columnItem . $rowItem)->getNumberFormat()->setFormatCode('0;-0;"-"');
                } else {
                    phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                }
                $columnItem++;
                // kualitas
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem['berat_loss_kualitas']);
                if ($dataItem['berat_loss_kualitas'] == 0) {
                    $activeWorksheet->getStyle($columnItem . $rowItem)->getNumberFormat()->setFormatCode('0;-0;"-"');
                } else {
                    phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                }
                $columnItem++;
                // mesin
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem['berat_loss_mesin']);
                if ($dataItem['berat_loss_mesin'] == 0) {
                    $activeWorksheet->getStyle($columnItem . $rowItem)->getNumberFormat()->setFormatCode('0;-0;"-"');
                } else {
                    phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                }
                $columnItem++;
                // lain-lain
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem['berat_loss_lainlain']);
                if ($dataItem['berat_loss_lainlain'] == 0) {
                    $activeWorksheet->getStyle($columnItem . $rowItem)->getNumberFormat()->setFormatCode('0;-0;"-"');
                } else {
                    phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                }
                $columnItem++;
                // frekuensi
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem['frekuensi']);
                phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
                // Terapkan custom format untuk mengganti tampilan 0 dengan -
                $rowItem++;
            }
            phpspreadsheet::addBorderDottedMiddleHorizontal($spreadsheet, $startColumnItem . $startRowItemSum . ':' . $columnItem . $rowItem);
            phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $startRowItemSum . ':' . $columnItem . $rowItem, false, 8, 'Calibri');

            // perhitungan jumlah berdasarkan departemen
            $spreadsheet->getActiveSheet()->mergeCells($columnEmployee . $rowItem . ':' . 'C' . $rowItem);
            $activeWorksheet->setCellValue($columnEmployee . $rowItem, 'Total');
            $columnItem = $startColumnItemData;
            // produksi
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . $startRowItemSum . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // total loss
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . $startRowItemSum . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // presentase loss
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=IF(' . $columnProduksi . $rowItem . '=0, 0, ' . $columnTotalLoss . $rowItem . '/(' . $columnProduksi . $rowItem . ' + ' . $columnTotalLoss . $rowItem . ')*100)');
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // katanuki
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . $startRowItemSum . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // kualitas
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . $startRowItemSum . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // mesin
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . $startRowItemSum . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // lain-lain
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . $startRowItemSum . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // frekuensi
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . $startRowItemSum . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
            phpspreadsheet::addFullBorder($spreadsheet, $columnEmployee . $rowItem . ':' . $columnItem . $rowItem);
            phpspreadsheet::styleFont($spreadsheet, $columnEmployee . $rowItem . ':' . $columnItem . $rowItem, true, 8, 'Calibri');
            $columnItem++;

            $rowItem++;
            $rowItem++;
        }

        // Grand total
        $rowGrandTotal = $rowItem;
        $spreadsheet->getActiveSheet()->mergeCells($columnEmployee . $rowGrandTotal . ':' . 'C' . $rowGrandTotal);
        $spreadsheet->getActiveSheet()->setCellValue($columnEmployee . $rowGrandTotal, 'GRAND TOTAL');
        phpspreadsheet::styleFont($spreadsheet, $columnEmployee . $rowGrandTotal . ':' . $columnHeaderEnd . $rowGrandTotal, true, 8, 'Calibri');

        $grandTotal = [
            'berat_produksi' => 0,
            'seitai_berat_loss' => 0,
            'berat_loss_katanuki' => 0,
            'berat_loss_kualitas' => 0,
            'berat_loss_mesin' => 0,
            'berat_loss_lainlain' => 0,
            'frekuensi' => 0
        ];

        foreach ($dataFilter as $departmentId => $Employeees) {
            foreach ($listEmployee[$departmentId] as $EmployeeNo => $EmployeeName) {
                if (isset($Employeees[$EmployeeNo])) {
                    $dataItem = $Employeees[$EmployeeNo];
                    $grandTotal['berat_produksi'] += $dataItem['berat_produksi'];
                    $grandTotal['seitai_berat_loss'] += $dataItem['seitai_berat_loss'];
                    $grandTotal['berat_loss_katanuki'] += $dataItem['berat_loss_katanuki'];
                    $grandTotal['berat_loss_kualitas'] += $dataItem['berat_loss_kualitas'];
                    $grandTotal['berat_loss_mesin'] += $dataItem['berat_loss_mesin'];
                    $grandTotal['berat_loss_lainlain'] += $dataItem['berat_loss_lainlain'];
                    $grandTotal['frekuensi'] += $dataItem['frekuensi'];
                } else {
                    // Tambahkan default value jika $Employee tidak ditemukan
                    $grandTotal['berat_produksi'] += 0;
                    $grandTotal['seitai_berat_loss'] += 0;
                    $grandTotal['berat_loss_katanuki'] += 0;
                    $grandTotal['berat_loss_kualitas'] += 0;
                    $grandTotal['berat_loss_mesin'] += 0;
                    $grandTotal['berat_loss_lainlain'] += 0;
                    $grandTotal['frekuensi'] += 0;
                }
            }
        }

        $columnItem = $startColumnItemData;
        // produksi
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_produksi']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // total loss
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['seitai_berat_loss']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // presentase loss
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, '=IF(' . $columnProduksi . $rowGrandTotal . '=0, 0, ' . $columnTotalLoss . $rowGrandTotal . '/(' . $columnProduksi . $rowGrandTotal . ' + ' . $columnTotalLoss . $rowGrandTotal . ')*100)');
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // katanuki
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_loss_katanuki']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // kualitas
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_loss_kualitas']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // mesin
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_loss_mesin']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // lain-lain
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_loss_lainlain']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // frekuensi
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['frekuensi']);
        phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowGrandTotal);

        phpspreadsheet::addFullBorder($spreadsheet, $columnEmployee . $rowGrandTotal . ':' . $columnItem . $rowGrandTotal);
        $columnItem++;

        $activeWorksheet->getStyle($columnMesin . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart)->getAlignment()->setWrapText(true);

        // set specific column widths (pixels -> approximate Excel column width)
        $pixelToExcelWidth = function ($pixels) {
            // Approx conversion: excelColumnWidth ≈ (pixels - 5) / 7
            $w = ($pixels - 5) / 7;
            return $w > 0 ? $w : 1;
        };

        $pixelWidths = [
            'B' => 72,
            'D' => 84,
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
