<?php

namespace App\Http\Livewire\Report\GeneralReport;

use App\Helpers\phpspreadsheet;
use App\Models\MsJamMatiMesin;
use App\Models\MsMachine;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ProduksiPerTypePerMesinReportService
{
    public static function daftarProduksiPerTipePerMesinInfure($nippon, $jenisReport, $tglMasuk, $tglKeluar)
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
        $activeWorksheet->setCellValue('B1', 'DAFTAR PRODUKSI PER TIPE PER MESIN INFURE');
        $activeWorksheet->setCellValue('B2', 'Periode : ' . $tglMasuk->format('d-M-Y H:i') . '  ~  ' . $tglKeluar->format('d-M-Y H:i'));
        // Style Judul
        phpspreadsheet::styleFont($spreadsheet, 'B1:B2', true, 11, 'Calibri');
        // hide column A
        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setVisible(false);

        // Header
        $columnTipeProduk = 'A';
        $columnTipeProdukEnd = 'B';
        $spreadsheet->getActiveSheet()->mergeCells($columnTipeProduk . '3:' . $columnTipeProdukEnd . '3');
        $activeWorksheet->setCellValue('A3', 'Tipe Produk');

        $columnMesin = 'C';
        $columnMesinEnd = 'D';
        $spreadsheet->getActiveSheet()->mergeCells($columnMesin . '3:' . $columnMesinEnd . '3');
        $activeWorksheet->setCellValue('C3', 'Mesin');

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
        $columnHeaderEnd = chr(ord($columnHeaderEnd) - 1);

        // style header
        phpspreadsheet::addFullBorder($spreadsheet, $columnTipeProduk . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);
        phpspreadsheet::styleFont($spreadsheet, $columnTipeProduk . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart, true, 9, 'Calibri');
        phpspreadsheet::textAlignCenter($spreadsheet, $columnTipeProduk . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);

        $data = DB::select("
            select max(dep.name) AS department_name,
                max(dep.id) AS department_id,
                max(prTip.id) AS product_type_id,
                max(prTip.name) AS product_type_name,
                max(mac.machineNo) AS machine_no,
                max(mac.machineName) AS machine_name,
                SUM(asy.berat_standard) AS berat_standard,
                SUM(asy.berat_produksi) AS berat_produksi,
                SUM(asy.infure_cost) AS infure_cost,
                SUM(asy.infure_berat_loss) AS infure_berat_loss,
                SUM(asy.panjang_produksi) AS panjang_produksi,
                SUM(asy.panjang_printing_inline) AS panjang_printing_inline,
                SUM(asy.infure_cost_printing) AS infure_cost_printing
            FROM tdProduct_Assembly AS asy
            left JOIN msMachine AS mac ON asy.machine_id = mac.id
            left JOIN msDepartment AS dep ON mac.department_id = dep.id
            left JOIN msProduct AS prd ON asy.product_id = prd.id
            left JOIN msProduct_type AS prTip ON prd.product_type_id = prTip.id
            WHERE asy.production_date BETWEEN '$tglMasuk' AND '$tglKeluar'
            GROUP BY dep.id, asy.machine_id, prTip.id
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

        $listProductType = array_reduce($data, function ($carry, $item) {
            $carry[$item->department_id][$item->product_type_id] = [
                'product_type_id' => $item->product_type_id,
                'product_type_name' => $item->product_type_name
            ];
            return $carry;
        }, []);

        // list mesin berdasarkan tanggal pertama dan departemen
        $listMachine = array_reduce($data, function ($carry, $item) {
            $carry[$item->department_id][$item->product_type_id][$item->machine_no] = $item->machine_name;
            return $carry;
        }, []);

        $dataFilter = array_reduce($data, function ($carry, $item) {
            $carry[$item->department_id][$item->product_type_id][$item->machine_no] = $item;
            return $carry;
        }, []);

        // index
        $startColumnItem = 'A';
        $endColumnItem = $columnHeaderEnd;
        $startColumnItemData = 'E';
        $columnMachineNo = 'C';
        $columnMachineName = 'D';
        $startRowItem = 5;
        $rowItem = $startRowItem;
        // daftar departemen
        foreach ($listDepartment as $department) {
            // Menulis data departemen
            $activeWorksheet->setCellValue($startColumnItem . $rowItem, $department['department_name']);
            $spreadsheet->getActiveSheet()->mergeCells($startColumnItem . $rowItem . ':' . $endColumnItem . $rowItem);
            phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowItem, true, 9, 'Calibri');
            $rowItem++;

            // daftar tipe produk
            foreach ($listProductType[$department['department_id']] as $productType) {
                $spreadsheet->getActiveSheet()->mergeCells($startColumnItem . $rowItem . ':' . $endColumnItem . $rowItem);
                $activeWorksheet->setCellValue($startColumnItem . $rowItem, $productType['product_type_name']);
                phpspreadsheet::styleFont($spreadsheet, $columnMachineNo . $rowItem, false, 8, 'Calibri');
                phpspreadsheet::addFullBorder($spreadsheet, $columnTipeProduk . $rowItem . ':' . $endColumnItem . $rowItem);
                $rowItem++;
                $startRowProductType = $rowItem;
                // daftar mesin
                foreach ($listMachine[$department['department_id']][$productType['product_type_id']] as $machineNo => $machineName) {

                    if ($dataFilter[$department['department_id']][$productType['product_type_id']][$machineNo] == null) {
                        continue;
                    }
                    // memasukkan data
                    $dataItem = $dataFilter[$department['department_id']][$productType['product_type_id']][$machineNo];
                    $columnItem = $startColumnItemData;

                    // Menulis data mesin
                    $spreadsheet->getActiveSheet()->setCellValue($columnMachineNo . $rowItem, $machineNo);
                    $spreadsheet->getActiveSheet()->setCellValue($columnMachineName . $rowItem, $machineName);
                    // phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);

                    // berat standar
                    $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_standard);
                    phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                    $columnItem++;
                    // berat produksi
                    $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_produksi);
                    phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                    $columnItem++;
                    // weight rate
                    $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_standard > 0 ? $dataItem->berat_produksi / $dataItem->berat_standard : 0);
                    phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowItem);
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
                    $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_produksi > 0 ? $dataItem->infure_berat_loss / $dataItem->berat_produksi : 0);
                    phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowItem);
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
                    // phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);
                    $columnItem++;

                    phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem, false, 8, 'Calibri');
                    $rowItem++;
                }
                // perhitungan jumlah berdasarkan type produk
                $spreadsheet->getActiveSheet()->mergeCells($columnMachineNo . $rowItem . ':' . $columnMachineName . $rowItem);
                // $activeWorksheet->setCellValue($columnMachineNo . $rowItem, 'Total ' . $department['department_name']);
                $columnItem = $startColumnItemData;
                $columnItemEnd = chr(ord($columnItem) + count($header) - 1);
                // berat standar
                $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listMachine[$department['department_id']][$productType['product_type_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
                phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // berat produksi
                $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listMachine[$department['department_id']][$productType['product_type_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
                phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // weight rate
                $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listMachine[$department['department_id']][$productType['product_type_id']])) . ':' . $columnItem . ($rowItem - 1) . ')/COUNTIF(' . $columnItem . ($rowItem - count($listMachine[$department['department_id']][$productType['product_type_id']])) . ':' . $columnItem . ($rowItem - 1) . ', "<>0")');
                phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // infure cost
                $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listMachine[$department['department_id']][$productType['product_type_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
                phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // loss
                $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listMachine[$department['department_id']][$productType['product_type_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
                phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // loss %
                $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listMachine[$department['department_id']][$productType['product_type_id']])) . ':' . $columnItem . ($rowItem - 1) . ')/COUNTIF(' . $columnItem . ($rowItem - count($listMachine[$department['department_id']][$productType['product_type_id']])) . ':' . $columnItem . ($rowItem - 1) . ', "<>0")');
                phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // panjang infure
                $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listMachine[$department['department_id']][$productType['product_type_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
                phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // panjang inline printing
                $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listMachine[$department['department_id']][$productType['product_type_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
                phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // inline printing cost
                $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listMachine[$department['department_id']][$productType['product_type_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
                phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // process cost
                $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listMachine[$department['department_id']][$productType['product_type_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
                phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
                phpspreadsheet::addBorderDottedMiddleHorizontal($spreadsheet, $columnTipeProduk . $startRowProductType . ':' . $columnItem . ($rowItem - 1));
                phpspreadsheet::addFullBorder($spreadsheet, $columnTipeProduk . $rowItem . ':' . $columnItem . $rowItem);
                $columnItem++;
                phpspreadsheet::styleFont($spreadsheet, $columnTipeProduk . $rowItem . ':' . $columnHeaderEnd . $rowItem, true, 8, 'Calibri');

                $rowItem++;
                $rowItem++;
            }
            // total berdasarkan departemen
            $columnTotalDepartment = $startColumnItem;
            $columnTotalDepartmentEnd = 'D';
            $spreadsheet->getActiveSheet()->mergeCells($startColumnItem . $rowItem . ':' . $columnTotalDepartmentEnd . $rowItem);
            $activeWorksheet->setCellValue($columnTotalDepartment . $rowItem, 'Total');
            phpspreadsheet::styleFont($spreadsheet, $columnTotalDepartment . $rowItem . ':' . $columnHeaderEnd . $rowItem, true, 8, 'Calibri');

            $columnItem = $startColumnItemData;
            $columnItemEnd = chr(ord($columnItem) + count($header) - 1);
            $totalByDepartment = array_reduce(
                array_keys($listProductType[$department['department_id']]),
                function ($carry, $productType) use ($dataFilter, $department) {
                    $dataItems = $dataFilter[$department['department_id']][$productType] ?? [];

                    foreach ($dataItems as $item) {
                        $carry['berat_standard'] += $item->berat_standard;
                        $carry['berat_produksi'] += $item->berat_produksi;
                        $carry['infure_cost'] += $item->infure_cost;
                        $carry['infure_berat_loss'] += $item->infure_berat_loss;
                        $carry['panjang_produksi'] += $item->panjang_produksi;
                        $carry['panjang_printing_inline'] += $item->panjang_printing_inline;
                        $carry['infure_cost_printing'] += $item->infure_cost_printing;
                    }

                    return $carry;
                },
                [
                    'berat_standard' => 0,
                    'berat_produksi' => 0,
                    'infure_cost' => 0,
                    'infure_berat_loss' => 0,
                    'panjang_produksi' => 0,
                    'panjang_printing_inline' => 0,
                    'infure_cost_printing' => 0
                ]
            );

            // berat standar
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, $totalByDepartment['berat_standard']);
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // berat produksi
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, $totalByDepartment['berat_produksi']);
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // weight rate
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, $totalByDepartment['berat_produksi'] > 0 ? $totalByDepartment['berat_produksi'] / $totalByDepartment['berat_standard'] : 0);
            phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // infure cost
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, $totalByDepartment['infure_cost']);
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // loss
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, $totalByDepartment['infure_berat_loss']);
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // loss %
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, $totalByDepartment['berat_produksi'] > 0 ? $totalByDepartment['infure_berat_loss'] / $totalByDepartment['berat_produksi'] : 0);
            phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // panjang infure
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, $totalByDepartment['panjang_produksi']);
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // panjang inline printing
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, $totalByDepartment['panjang_printing_inline']);
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // inline printing cost
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, $totalByDepartment['infure_cost_printing']);
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // process cost
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, $totalByDepartment['infure_cost'] + $totalByDepartment['infure_cost_printing']);
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            phpspreadsheet::styleFont($spreadsheet, $columnMachineNo . $rowItem . ':' . $columnItem . $rowItem, true, 8, 'Calibri');
            phpspreadsheet::addBorderDottedMiddleHorizontal($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);
            $columnItem++;

            $rowItem++;
            $rowItem++;
        }

        // Grand total
        $rowGrandTotal = $rowItem;
        $spreadsheet->getActiveSheet()->mergeCells($startColumnItem . $rowGrandTotal . ':' . $columnMachineName . $rowGrandTotal);
        $spreadsheet->getActiveSheet()->setCellValue($startColumnItem . $rowGrandTotal, 'GRAND TOTAL');
        phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowGrandTotal . ':' . $columnHeaderEnd . $rowGrandTotal, true, 8, 'Calibri');
        // $this->addFullBorder($spreadsheet, $columnMachineNo . $rowGrandTotal . ':' . $columnValueAvg . $rowGrandTotal);

        $grandTotal = array_reduce(array_keys($listDepartment), function ($carry, $department) use ($dataFilter, $listProductType) {
            $productType = $listProductType[$department];
            foreach ($productType as $type) {
                $dataItem = $dataFilter[$department][$type['product_type_id']] ?? [];
                $carry['berat_standard'] += array_sum(array_column($dataItem, 'berat_standard'));
                $carry['berat_produksi'] += array_sum(array_column($dataItem, 'berat_produksi'));
                $carry['infure_cost'] += array_sum(array_column($dataItem, 'infure_cost'));
                $carry['infure_berat_loss'] += array_sum(array_column($dataItem, 'infure_berat_loss'));
                $carry['panjang_produksi'] += array_sum(array_column($dataItem, 'panjang_produksi'));
                $carry['panjang_printing_inline'] += array_sum(array_column($dataItem, 'panjang_printing_inline'));
                $carry['infure_cost_printing'] += array_sum(array_column($dataItem, 'infure_cost_printing'));
            }
            return $carry;
        }, [
            'berat_standard' => 0,
            'berat_produksi' => 0,
            'infure_cost' => 0,
            'infure_berat_loss' => 0,
            'panjang_produksi' => 0,
            'panjang_printing_inline' => 0,
            'infure_cost_printing' => 0
        ]);

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
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_standard'] > 0 ? $grandTotal['berat_produksi'] / $grandTotal['berat_standard'] : 0);
        phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // infure cost
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['infure_cost']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // loss
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['infure_berat_loss']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // loss %
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_produksi'] > 0 ? $grandTotal['infure_berat_loss'] / $grandTotal['berat_produksi'] : 0);
        phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // panjang infure
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['panjang_produksi']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // panjang inline printing
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['panjang_printing_inline']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // inline printing cost
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['infure_cost_printing']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // process cost
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['infure_cost'] + $grandTotal['infure_cost_printing']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowGrandTotal . ':' . $columnItem . $rowGrandTotal);
        $columnItem++;

        $activeWorksheet->getStyle($columnTipeProduk . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart)->getAlignment()->setWrapText(true);

        $writer = new Xlsx($spreadsheet);
        $filename = 'asset/report/' . $nippon . '-' . $jenisReport . '.xlsx';
        $writer->save($filename);
        $response = [
            'status' => 'success',
            'filename' => $filename
        ];
        return $response;
    }

    public static function daftarProduksiPerTipePerMesinSeitai($nippon, $jenisReport, $tglMasuk, $tglKeluar)
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
        $activeWorksheet->setCellValue('A1', 'DAFTAR PRODUKSI PER TIPE PER MESIN SEITAI');
        $activeWorksheet->setCellValue('A2', 'Periode : ' . $tglMasuk->format('d-M-Y H:i') . '  ~  ' . $tglKeluar->format('d-M-Y H:i'));
        // Style Judul
        phpspreadsheet::styleFont($spreadsheet, 'A1:A2', true, 11, 'Calibri');

        // Header
        $columnTipeProduk = 'A';
        $columnTipeProdukEnd = 'B';
        $spreadsheet->getActiveSheet()->mergeCells($columnTipeProduk . '3:' . $columnTipeProdukEnd . '3');
        $activeWorksheet->setCellValue('A3', 'Tipe Produk');

        $columnMesin = 'C';
        $columnMesinEnd = 'D';
        $spreadsheet->getActiveSheet()->mergeCells($columnMesin . '3:' . $columnMesinEnd . '3');
        $activeWorksheet->setCellValue('C3', 'Mesin');

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
        $columnHeaderEnd = chr(ord($columnHeaderEnd) - 1);

        // style header
        phpspreadsheet::addFullBorder($spreadsheet, $columnTipeProduk . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);
        phpspreadsheet::styleFont($spreadsheet, $columnTipeProduk . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart, true, 9, 'Calibri');
        phpspreadsheet::textAlignCenter($spreadsheet, $columnTipeProduk . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);

        $data = DB::select("
            SELECT
                MAX(dep.id) AS department_id,
                MAX(dep.name) AS department_name,
                MAX(prT.id) AS product_type_id,
                MAX(prT.name) AS product_type_name,
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
            GROUP BY dep.id, prT.name, good.machine_id
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

        $listProductType = array_reduce($data, function ($carry, $item) {
            $carry[$item->department_id][$item->product_type_id] = [
                'product_type_id' => $item->product_type_id,
                'product_type_name' => $item->product_type_name
            ];
            return $carry;
        }, []);

        // list mesin berdasarkan tanggal pertama dan departemen
        $listMachine = array_reduce($data, function ($carry, $item) {
            $carry[$item->department_id][$item->product_type_id][$item->machine_no] = $item->machine_name;
            return $carry;
        }, []);

        $dataFilter = array_reduce($data, function ($carry, $item) {
            $carry[$item->department_id][$item->product_type_id][$item->machine_no] = $item;
            return $carry;
        }, []);

        // index
        $startColumnItem = 'A';
        $endColumnItem = $columnHeaderEnd;
        $startColumnItemData = 'E';
        $columnMachineNo = 'C';
        $columnMachineName = 'D';
        $startRowItem = 5;
        $rowItem = $startRowItem;
        // daftar departemen
        foreach ($listDepartment as $department) {
            // Menulis data departemen
            $activeWorksheet->setCellValue($startColumnItem . $rowItem, $department['department_name']);
            $spreadsheet->getActiveSheet()->mergeCells($startColumnItem . $rowItem . ':' . $endColumnItem . $rowItem);
            phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowItem, true, 9, 'Calibri');
            $rowItem++;

            // daftar tipe produk
            foreach ($listProductType[$department['department_id']] as $productType) {
                $spreadsheet->getActiveSheet()->mergeCells($startColumnItem . $rowItem . ':' . $endColumnItem . $rowItem);
                $activeWorksheet->setCellValue($startColumnItem . $rowItem, $productType['product_type_name']);
                phpspreadsheet::styleFont($spreadsheet, $columnMachineNo . $rowItem, false, 8, 'Calibri');
                phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowItem . ':' . $endColumnItem . $rowItem);
                $rowItem++;
                $startRowProductType = $rowItem;
                // daftar mesin
                foreach ($listMachine[$department['department_id']][$productType['product_type_id']] as $machineNo => $machineName) {
                    if ($dataFilter[$department['department_id']][$productType['product_type_id']][$machineNo] == null) {
                        continue;
                    }
                    $columnItem = $startColumnItemData;

                    // Menulis data mesin
                    $spreadsheet->getActiveSheet()->setCellValue($columnMachineNo . $rowItem, $machineNo);
                    $spreadsheet->getActiveSheet()->setCellValue($columnMachineName . $rowItem, $machineName);
                    // phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);

                    // memasukkan data
                    $dataItem = $dataFilter[$department['department_id']][$productType['product_type_id']][$machineNo];
                    // jumlah produksi
                    $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->qty_produksi);
                    phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
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
                    $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_produksi > 0 ? $dataItem->seitai_berat_loss / $dataItem->berat_produksi : 0);
                    phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowItem);
                    $columnItem++;
                    // Seitai cost
                    $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->seitai_cost);
                    phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                    $columnItem++;
                    // Ponsu Loss
                    $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->seitai_berat_loss_ponsu);
                    phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                    $columnItem++;
                    // Infure Loss
                    $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->infure_berat_loss);
                    phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                    $columnItem++;

                    phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem, false, 8, 'Calibri');
                    $rowItem++;
                }
                // perhitungan jumlah berdasarkan type produk
                $spreadsheet->getActiveSheet()->mergeCells($columnMachineNo . $rowItem . ':' . $columnMachineName . $rowItem);
                $columnItem = $startColumnItemData;
                $columnItemEnd = chr(ord($columnItem) + count($header) - 1);
                // jumlah produksi
                $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listMachine[$department['department_id']][$productType['product_type_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
                phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // berat produksi
                $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listMachine[$department['department_id']][$productType['product_type_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
                phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // Loss
                $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listMachine[$department['department_id']][$productType['product_type_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
                phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // loss %
                $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listMachine[$department['department_id']][$productType['product_type_id']])) . ':' . $columnItem . ($rowItem - 1) . ')/COUNTIF(' . $columnItem . ($rowItem - count($listMachine[$department['department_id']][$productType['product_type_id']])) . ':' . $columnItem . ($rowItem - 1) . ', "<>0")');
                phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // Seitai cost
                $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listMachine[$department['department_id']][$productType['product_type_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
                phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // Ponsu Loss
                $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listMachine[$department['department_id']][$productType['product_type_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
                phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // Infure Loss
                $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listMachine[$department['department_id']][$productType['product_type_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
                phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                phpspreadsheet::addBorderDottedMiddleHorizontal($spreadsheet, $columnTipeProduk . $startRowProductType . ':' . $columnItem . $rowItem);
                phpspreadsheet::addFullBorder($spreadsheet, $columnMachineNo . $rowItem . ':' . $columnItem . $rowItem);
                $columnItem++;
                phpspreadsheet::styleFont($spreadsheet, $columnMachineNo . $rowItem . ':' . $columnHeaderEnd . $rowItem, true, 8, 'Calibri');

                $rowItem++;
                $rowItem++;
            }
            // total berdasarkan departemen
            $columnTotalDepartment = $startColumnItem;
            $columnTotalDepartmentEnd = 'D';
            $spreadsheet->getActiveSheet()->mergeCells($startColumnItem . $rowItem . ':' . $columnTotalDepartmentEnd . $rowItem);
            $activeWorksheet->setCellValue($columnTotalDepartment . $rowItem, 'Total');
            phpspreadsheet::styleFont($spreadsheet, $columnTotalDepartment . $rowItem . ':' . $columnHeaderEnd . $rowItem, true, 8, 'Calibri');

            $columnItem = $startColumnItemData;
            $columnItemEnd = chr(ord($columnItem) + count($header) - 1);
            $totalByDepartment = array_reduce(
                array_keys($listProductType[$department['department_id']]),
                function ($carry, $productType) use ($dataFilter, $department) {
                    $dataItems = $dataFilter[$department['department_id']][$productType] ?? [];

                    foreach ($dataItems as $item) {
                        $carry['qty_produksi'] += $item->qty_produksi;
                        $carry['berat_produksi'] += $item->berat_produksi;
                        $carry['seitai_cost'] += $item->seitai_cost;
                        $carry['seitai_berat_loss'] += $item->seitai_berat_loss;
                        $carry['seitai_berat_loss_ponsu'] += $item->seitai_berat_loss_ponsu;
                        $carry['infure_berat_loss'] += $item->infure_berat_loss;
                    }

                    return $carry;
                },
                [
                    'qty_produksi' => 0,
                    'berat_produksi' => 0,
                    'seitai_cost' => 0,
                    'seitai_berat_loss' => 0,
                    'seitai_berat_loss_ponsu' => 0,
                    'infure_berat_loss' => 0
                ]
            );

            // jumlah produksi
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, $totalByDepartment['qty_produksi']);
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // berat produksi
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, $totalByDepartment['berat_produksi']);
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // loss
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, $totalByDepartment['seitai_berat_loss']);
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // loss %
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, $totalByDepartment['berat_produksi'] > 0 ? $totalByDepartment['seitai_berat_loss'] / $totalByDepartment['berat_produksi'] : 0);
            phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // seitai cost
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, $totalByDepartment['seitai_cost']);
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            //  berat loss ponsu
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, $totalByDepartment['seitai_berat_loss_ponsu']);
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // berat loss infure
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, $totalByDepartment['infure_berat_loss']);
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            phpspreadsheet::styleFont($spreadsheet, $columnMachineNo . $rowItem . ':' . $columnItem . $rowItem, true, 8, 'Calibri');
            phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);
            $columnItem++;

            $rowItem++;
            $rowItem++;
        }

        // Grand total
        $rowGrandTotal = $rowItem;
        $spreadsheet->getActiveSheet()->mergeCells($startColumnItem . $rowGrandTotal . ':' . $columnMachineName . $rowGrandTotal);
        $spreadsheet->getActiveSheet()->setCellValue($startColumnItem . $rowGrandTotal, 'GRAND TOTAL');
        phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowGrandTotal . ':' . $columnHeaderEnd . $rowGrandTotal, true, 8, 'Calibri');
        // $this->addFullBorder($spreadsheet, $columnMachineNo . $rowGrandTotal . ':' . $columnValueAvg . $rowGrandTotal);

        $grandTotal = array_reduce(array_keys($listDepartment), function ($carry, $department) use ($dataFilter, $listProductType) {
            $productType = $listProductType[$department];
            foreach ($productType as $type) {
                $dataItem = $dataFilter[$department][$type['product_type_id']] ?? [];
                $carry['qty_produksi'] += array_sum(array_column($dataItem, 'qty_produksi'));
                $carry['berat_produksi'] += array_sum(array_column($dataItem, 'berat_produksi'));
                $carry['seitai_cost'] += array_sum(array_column($dataItem, 'seitai_cost'));
                $carry['seitai_berat_loss'] += array_sum(array_column($dataItem, 'seitai_berat_loss'));
                $carry['seitai_berat_loss_ponsu'] += array_sum(array_column($dataItem, 'seitai_berat_loss_ponsu'));
                $carry['infure_berat_loss'] += array_sum(array_column($dataItem, 'infure_berat_loss'));
            }
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
        // jumlah produksi
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['qty_produksi']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // berat produksi
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_produksi']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // seitai cost
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['seitai_cost']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // loss
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['seitai_berat_loss']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // loss %
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_produksi'] > 0 ? $grandTotal['seitai_berat_loss'] / $grandTotal['berat_produksi'] : 0);
        phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // berat loss ponsu
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['seitai_berat_loss_ponsu']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // berat loss infure
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['infure_berat_loss']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowGrandTotal . ':' . $columnItem . $rowGrandTotal);
        $columnItem++;

        $activeWorksheet->getStyle($columnTipeProduk . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart)->getAlignment()->setWrapText(true);

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
