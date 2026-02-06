<?php

namespace App\Http\Livewire\Report\GeneralReport;

use App\Helpers\phpspreadsheet;
use App\Models\MsJamMatiMesin;
use App\Models\MsMachine;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ProduksiPerMesinReportService
{
    public static function daftarProduksiPerMesinInfure($nippon, $jenisReport, $tglMasuk, $tglKeluar)
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
        // hide column A
        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setVisible(false);

        // Judul
        $activeWorksheet->setCellValue('B1', 'DAFTAR PRODUKSI PER MESIN INFURE');
        $activeWorksheet->setCellValue('B2', 'Periode : ' . $tglMasuk->format('d-M-Y H:i') . '  ~  ' . $tglKeluar->format('d-M-Y H:i'));
        // Style Judul
        phpspreadsheet::styleFont($spreadsheet, 'B1:B2', true, 11, 'Calibri');

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
            'Berat Standar (Kg)',
            'Berat Produksi (Kg)',
            'Weight Rate',
            'Infure Cost',
            'Loss (Kg)',
            'Loss (%)',
            'Panjang Infure (meter)',
            'Inline Printing (meter)',
            'Inline Printing Cost',
            'Process Cost',
            'Jam Jalan (h:m)',
            'Jam Off (h:m)',
            'Jalan Mesin (%)',
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
            WITH jam AS (
                SELECT
                    jam_.machine_id,
                    SUM(EXTRACT(EPOCH FROM work_hour) / 60) AS work_hour,
                    SUM(EXTRACT(EPOCH FROM off_hour) / 60) AS off_hour,
                    SUM(EXTRACT(EPOCH FROM on_hour) / 60) AS on_hour
                FROM
                    tdJamKerjaMesin AS jam_
                INNER JOIN
                    msworkingshift AS ws ON jam_.work_shift = ws.id
                WHERE
                    (working_date || ' ' || work_hour_from)::TIMESTAMP
                    BETWEEN '$tglMasuk' AND '$tglKeluar'
                GROUP BY
                    jam_.machine_id
            )
            SELECT
                MAX(mac.machineNo) AS machine_no,
                MAX(mac.machineName) AS machine_name,
                MAX(dep.ID) AS department_id,
                MAX(dep.NAME) AS department_name,
                SUM(asy.berat_standard) AS berat_standard,
                SUM(asy.berat_produksi) AS berat_produksi,
                SUM(asy.infure_cost) AS infure_cost,
                SUM(asy.infure_berat_loss) AS infure_berat_loss,
                SUM(asy.panjang_produksi) AS panjang_produksi,
                SUM(asy.panjang_printing_inline) AS panjang_printing_inline,
                SUM(asy.infure_cost_printing) AS infure_cost_printing,
                COALESCE(MAX(jam.work_hour), 0) AS work_hour_mm,
                COALESCE(MAX(jam.off_hour), 0) AS work_hour_off_mm,
                COALESCE(MAX(jam.on_hour), 0) AS work_hour_on_mm
            FROM
                tdProduct_Assembly AS asy
            LEFT JOIN
                jam ON asy.machine_id = jam.machine_id
            LEFT JOIN
                msMachine AS mac ON asy.machine_id = mac.ID
            LEFT JOIN
                msDepartment AS dep ON mac.department_id = dep.ID
            WHERE
                asy.production_date BETWEEN '$tglMasuk' AND '$tglKeluar'
            GROUP BY
                asy.machine_id
            ORDER BY
                machine_no;
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
        $listMachine = MsMachine::where('status', 1)
            ->whereIn('department_id', array_keys($listDepartment))
            ->orderBy('machineno')
            ->get()
            ->groupBy('department_id')
            ->map(function ($item) {
                return $item->pluck('machinename', 'machineno');
            });

        $dataFilter = array_reduce($data, function ($carry, $item) {
            $carry[$item->department_id][$item->machine_no] = $item;
            return $carry;
        }, []);

        // index
        $startColumnItem = 'B';
        $endColumnItem = $columnHeaderEnd;
        $columnMachineNo = 'B';
        $columnMachineName = 'C';
        $columnBeratStandart = 'D';
        $columnBeratProduksi = 'E';
        $columnInfureBeratLoss = 'H';
        $startColumnItemData = 'D';
        $startRowItem = 5;
        $rowItem = $startRowItem;
        // daftar departemen
        foreach ($listDepartment as $department) {
            // Menulis data departemen
            $activeWorksheet->setCellValue($startColumnItem . $rowItem, $department['department_name']);
            $spreadsheet->getActiveSheet()->mergeCells($startColumnItem . $rowItem . ':' . $endColumnItem . $rowItem);
            phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowItem, true, 9, 'Calibri');
            $rowItem++;
            $startRowDepartment = $rowItem;
            $countMachine = 0;
            // daftar mesin
            foreach ($listMachine[$department['department_id']] as $machineNo => $machineName) {
                // memasukkan data
                $dataItem = $dataFilter[$department['department_id']][$machineNo] ?? (object)[
                    'berat_standard' => 0,
                    'berat_produksi' => 0,
                    'infure_cost' => 0,
                    'infure_berat_loss' => 0,
                    'panjang_produksi' => 0,
                    'panjang_printing_inline' => 0,
                    'infure_cost_printing' => 0,
                    'work_hour_mm' => 0,
                    'work_hour_off_mm' => 0,
                    'work_hour_on_mm' => 0,
                ];
                if ($dataItem->berat_standard == 0 && $dataItem->berat_produksi == 0) {
                    continue;
                }
                $countMachine++;
                $columnItem = $startColumnItemData;

                // Menulis data mesin
                $spreadsheet->getActiveSheet()->setCellValue($columnMachineNo . $rowItem, $machineNo);
                $spreadsheet->getActiveSheet()->setCellValue($columnMachineName . $rowItem, $machineName);

                // berat standard
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_standard);
                // phpSpreadsheet::numberFormatCommaThousandsOrZero($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // berat produksi
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_produksi);
                // phpSpreadsheet::numberFormatCommaThousandsOrZero($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // weight rate
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_standard > 0 ? '=' . $columnBeratProduksi . $rowItem . '/' . $columnBeratStandart . $rowItem : 0);
                phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem, 3);
                $columnItem++;
                // infure cost
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->infure_cost);
                phpSpreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // infure berat loss
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->infure_berat_loss);
                phpSpreadsheet::numberFormatCommaThousandsOrZero($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // loss %
                $lossPercentage = $dataItem->berat_produksi > 0 ? '=' . '(' . $columnInfureBeratLoss . $rowItem . '/' . '(' . $columnBeratProduksi . $rowItem . '+' . $columnInfureBeratLoss . $rowItem . ')) * 100' : 0;
                $activeWorksheet->setCellValue($columnItem . $rowItem, $lossPercentage);
                phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem, 2);
                $columnItem++;
                // panjang produksi
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->panjang_produksi);
                phpSpreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // panjang printing inline
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->panjang_printing_inline ?? 0);
                phpSpreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // infure cost printing
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->infure_cost_printing ?? 0);
                phpSpreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // process cost
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->infure_cost + $dataItem->infure_cost_printing);
                phpSpreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
                $columnItem++;

                // jam kerja
                $workHours = $dataItem->work_hour_on_mm; // Ambil nilai menit dari data
                $hours = floor($workHours / 60); // Hitung jumlah jam
                $minutes = $workHours % 60; // Hitung sisa menit
                if ($hours == 0 && $minutes == 0) {
                    $activeWorksheet->setCellValue($columnItem . $rowItem, '');
                } else {
                    $formatedWorkHours = sprintf('%d:%02d', $hours, $minutes);
                    $activeWorksheet->setCellValue($columnItem . $rowItem, $formatedWorkHours == '0:00' ? '-' : $formatedWorkHours);
                }
                $columnItem++;

                // jam mati
                $offHours = $dataItem->work_hour_off_mm; // Ambil nilai menit dari data
                $hours = floor($offHours / 60); // Hitung jumlah jam
                $minutes = $offHours % 60; // Hitung sisa menit
                $formatedOffHours = sprintf('%d:%02d', $hours, $minutes);
                $activeWorksheet->setCellValue($columnItem . $rowItem, $formatedOffHours == '0:00' ? '-' : $formatedOffHours);
                $columnItem++;

                // jalan mesin
                $activeWorksheet->setCellValue($columnItem . $rowItem, $workHours > 0 ? $workHours / ($offHours + $workHours) * 100 : 0);
                phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem, 2);
                $columnItem++;

                phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem, false, 8, 'Calibri');
                $rowItem++;
            }
            $columnItem = $startColumnItemData;
            $columnItemEnd = chr(ord($columnItem) + count($header) - 1);
            phpspreadsheet::addBorderDottedMiddleHorizontal($spreadsheet, $columnMachineNo . $startRowDepartment . ':' . $columnItemEnd . $rowItem - 1);

            // perhitungan jumlah berdasarkan departemen
            $spreadsheet->getActiveSheet()->mergeCells($columnMachineNo . $rowItem . ':' . $columnMachineName . $rowItem);
            // berat standard
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - $countMachine) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatCommaThousandsOrZero($spreadsheet, $columnItem . $startRowDepartment . ':' . $columnItem . $rowItem);

            $columnItem++;
            // berat produksi
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - $countMachine) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatCommaThousandsOrZero($spreadsheet, $columnItem . $startRowDepartment . ':' . $columnItem . $rowItem);
            $columnItem++;
            // weight rate
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - $countMachine) . ':' . $columnItem . ($rowItem - 1) . ')/COUNTIF(' . $columnItem . ($rowItem - $countMachine) . ':' . $columnItem . ($rowItem - 1) . ', "<>0")');
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem, 3);
            $columnItem++;
            // infure cost
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - $countMachine) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // infure berat loss
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - $countMachine) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatCommaThousandsOrZero($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // loss %
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=' . '(' . $columnInfureBeratLoss . $rowItem . '/' . '(' . $columnBeratProduksi . $rowItem . '+' . $columnInfureBeratLoss . $rowItem . ')) * 100');
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem, 2);
            $columnItem++;
            // panjang produksi
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - $countMachine) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // panjang printing inline
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - $countMachine) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // infure cost printing
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - $countMachine) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // process cost
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - $countMachine) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // jam kerja
            $jamKerjaMesin = array_reduce(array_keys($listMachine[$department['department_id']]->toArray()), function ($carry, $item) use ($dataFilter, $department) {
                $dataItem = $dataFilter[$department['department_id']][$item] ?? (object)[
                    'berat_standard' => 0,
                    'berat_produksi' => 0,
                    'infure_cost' => 0,
                    'infure_berat_loss' => 0,
                    'panjang_produksi' => 0,
                    'panjang_printing_inline' => 0,
                    'infure_cost_printing' => 0,
                    'work_hour_mm' => 0,
                    'work_hour_off_mm' => 0,
                    'work_hour_on_mm' => 0,
                ];
                $carry['workHours'] += $dataItem->work_hour_on_mm;
                $carry['offHours'] += $dataItem->work_hour_off_mm;
                return $carry;
            }, ['workHours' => 0, 'offHours' => 0]);
            $hours = floor($jamKerjaMesin['workHours'] / 60); // Hitung jumlah jam
            $minutes = $jamKerjaMesin['workHours'] % 60; // Hitung sisa menit
            $formatedWorkHours = sprintf('%d:%02d', $hours, $minutes);
            $activeWorksheet->setCellValue($columnItem . $rowItem, $formatedWorkHours == '0:00' ? '-' : $formatedWorkHours);
            $columnItem++;

            // jam mati
            $hours = floor($jamKerjaMesin['offHours'] / 60); // Hitung jumlah jam
            $minutes = $jamKerjaMesin['offHours'] % 60; // Hitung sisa menit
            $formatedOffHours = sprintf('%d:%02d', $hours, $minutes);
            $activeWorksheet->setCellValue($columnItem . $rowItem, $formatedOffHours == '0:00' ? '-' : $formatedOffHours);
            $columnItem++;

            $avgJamKerjaMesin = $jamKerjaMesin['workHours'] > 0 ? $jamKerjaMesin['workHours'] / ($jamKerjaMesin['offHours'] + $jamKerjaMesin['workHours']) * 100 : 0;
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, $avgJamKerjaMesin);
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem, 2);
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
        // $this->addFullBorder($spreadsheet, $columnMachineNo . $rowGrandTotal . ':' . $columnValueAvg . $rowGrandTotal);

        // grand total
        $grandTotal = [
            'berat_standard' => 0,
            'berat_produksi' => 0,
            'infure_cost' => 0,
            'infure_berat_loss' => 0,
            'panjang_produksi' => 0,
            'panjang_printing_inline' => 0,
            'infure_cost_printing' => 0,
            'work_hour_mm' => 0,
            'work_hour_on_mm' => 0,
            'work_hour_off_mm' => 0,
        ];

        foreach ($dataFilter as $departmentId => $department) {
            foreach ($department as $machineNo => $machine) {
                $grandTotal['berat_standard'] += $machine->berat_standard;
                $grandTotal['berat_produksi'] += $machine->berat_produksi;
                $grandTotal['infure_cost'] += $machine->infure_cost;
                $grandTotal['infure_berat_loss'] += $machine->infure_berat_loss;
                $grandTotal['panjang_produksi'] += $machine->panjang_produksi;
                $grandTotal['panjang_printing_inline'] += $machine->panjang_printing_inline;
                $grandTotal['infure_cost_printing'] += $machine->infure_cost_printing;
                $grandTotal['work_hour_mm'] += $machine->work_hour_mm;
                $grandTotal['work_hour_on_mm'] += $machine->work_hour_on_mm;
                $grandTotal['work_hour_off_mm'] += $machine->work_hour_off_mm;
            }
        }

        $columnItem = $startColumnItemData;
        $columnItemEnd = chr(ord($columnItem) + count($header) - 1);
        // berat standard
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_standard']);
        phpSpreadsheet::numberFormatCommaThousandsOrZero($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // berat produksi
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_produksi']);
        phpSpreadsheet::numberFormatCommaThousandsOrZero($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // weight rate
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_standard'] > 0 ? $grandTotal['berat_produksi'] / $grandTotal['berat_standard'] : 0);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // infure cost
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['infure_cost']);
        phpSpreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // infure berat loss
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['infure_berat_loss']);
        phpSpreadsheet::numberFormatCommaThousandsOrZero($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // loss %
        $lossPercentageGrandTotal = $grandTotal['berat_produksi'] > 0 ? $grandTotal['infure_berat_loss'] / ($grandTotal['berat_produksi'] + $grandTotal['infure_berat_loss']) * 100 : 0;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $lossPercentageGrandTotal);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal, 2);
        $columnItem++;
        // panjang produksi
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['panjang_produksi']);
        phpSpreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // panjang printing inline
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['panjang_printing_inline']);
        phpSpreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // infure cost printing
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['infure_cost_printing']);
        phpSpreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // process cost
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['infure_cost'] + $grandTotal['infure_cost_printing']);
        phpSpreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;

        // jam kerja
        $hours = floor($grandTotal['work_hour_on_mm'] / 60); // Hitung jumlah jam
        $minutes = $grandTotal['work_hour_on_mm'] % 60; // Hitung sisa menit
        $formatedWorkHours = sprintf('%d:%02d', $hours, $minutes);
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $formatedWorkHours == '0:00' ? '-' : $formatedWorkHours);
        $columnItem++;

        // jam mati
        $hours = floor($grandTotal['work_hour_off_mm'] / 60); // Hitung jumlah jam
        $minutes = $grandTotal['work_hour_off_mm'] % 60; // Hitung sisa menit
        $formatedOffHours = sprintf('%d:%02d', $hours, $minutes);
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $formatedOffHours == '0:00' ? '-' : $formatedOffHours);
        $columnItem++;

        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['work_hour_on_mm'] > 0 ? $grandTotal['work_hour_on_mm'] / ($grandTotal['work_hour_off_mm'] + $grandTotal['work_hour_on_mm']) * 100 : 0);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal, 2);
        phpspreadsheet::addFullBorder($spreadsheet, $columnMachineNo . $rowGrandTotal . ':' . $columnItem . $rowGrandTotal);

        $activeWorksheet->getStyle($columnMesin . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart)->getAlignment()->setWrapText(true);
        // buatkan agar auto size pada column nama mesin
        $spreadsheet->getActiveSheet()->getColumnDimension($columnMesinEnd)->setAutoSize(true);

        $writer = new Xlsx($spreadsheet);
        $filename = 'asset/report/' . $nippon . '-' . $jenisReport . '.xlsx';
        $writer->save($filename);
        $response = [
            'status' => 'success',
            'filename' => $filename
        ];
        return $response;
    }

    public static function daftarProduksiPerMesinSeitai($nippon, $jenisReport, $tglMasuk, $tglKeluar)
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
        $activeWorksheet->setCellValue('B1', 'DAFTAR PRODUKSI PER MESIN SEITAI');
        $activeWorksheet->setCellValue('B2', 'Periode : ' . $tglMasuk->format('d-M-Y H:i') . '  ~  ' . $tglKeluar->format('d-M-Y H:i'));
        // Style Judul
        phpspreadsheet::styleFont($spreadsheet, 'B1:B2', true, 11, 'Calibri');
        // hide column A
        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setVisible(false);
        $spreadsheet->getActiveSheet()->freezePane('B5');

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
            'Jumlah Produksi (Lembar)',
            'Berat Standar (Kg)',
            'Loss (Kg)',
            'Loss (%)',
            'Seitai Cost',
            'Ponsu Loss (Kg)',
            'Infure Loss (Kg)',
            'Produksi per jam (Lembar)',
            'Jalan Mesin (%)',
            'Jam Jalan (h:m)',
            'Jam Off (h:m)',
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
                MAX(mac.machineNo) AS machine_no,
                MAX(mac.machineName) AS machine_name,
                MAX(dep.name) AS department_name,
                MAX(dep.id) AS department_id,
                SUM(good.qty_produksi) AS qty_produksi,
                SUM(good.qty_produksi * prd.unit_weight * 0.001) AS berat_produksi,
                SUM(good.seitai_berat_loss) - COALESCE(SUM(ponsu.berat_loss), 0) AS seitai_berat_loss,
                SUM(good.qty_produksi * prT.harga_sat_seitai) AS seitai_cost,
                COALESCE(SUM(ponsu.berat_loss), 0) AS seitai_berat_loss_ponsu,
                SUM(good.infure_berat_loss) AS infure_berat_loss,
                COALESCE(MAX(jam.work_hour), 0) AS work_hour_mm,
                COALESCE(MAX(jam.off_hour), 0) AS work_hour_off_mm,
                COALESCE(MAX(jam.on_hour), 0) AS work_hour_on_mm
            FROM tdProduct_Goods AS good
                LEFT JOIN (
                    SELECT
                        los_.product_goods_id,
                        SUM(los_.berat_loss) AS berat_loss
                    FROM tdProduct_Goods_Loss AS los_
                    WHERE los_.loss_seitai_id = 1
                    GROUP BY los_.product_goods_id
                ) ponsu ON good.id = ponsu.product_goods_id
                LEFT JOIN (
                    SELECT
                        jam_.machine_id,
                        SUM(EXTRACT(EPOCH FROM work_hour) / 60) AS work_hour,
                        SUM(EXTRACT(EPOCH FROM off_hour) / 60) AS off_hour,
                        SUM(EXTRACT(EPOCH FROM on_hour) / 60) AS on_hour
                    FROM tdJamKerjaMesin AS jam_
                    INNER JOIN msworkingshift AS ws ON jam_.work_shift = ws.id
                    WHERE
                        ( working_date :: TEXT || ' ' || work_hour_from :: TEXT ) :: TIMESTAMP BETWEEN '$tglMasuk' AND '$tglKeluar'
                    GROUP BY jam_.machine_id
                ) jam ON good.machine_id = jam.machine_id
                INNER JOIN msMachine AS mac ON good.machine_id = mac.id
                INNER JOIN msDepartment AS dep ON mac.department_id = dep.id
                INNER JOIN msProduct AS prd ON good.product_id = prd.id
                INNER JOIN msProduct_type AS prT ON prd.product_type_id = prT.id
            WHERE good.production_date BETWEEN  '$tglMasuk' AND '$tglKeluar'
            GROUP BY good.machine_id
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
        $listMachine = MsMachine::where('status', 1)
            ->whereIn('department_id', array_keys($listDepartment))
            ->orderBy('machineno')
            ->get()
            ->groupBy('department_id')
            ->map(function ($item) {
                return $item->pluck('machinename', 'machineno');
            });

        $dataFilter = array_reduce($data, function ($carry, $item) {
            $carry[$item->department_id][$item->machine_no] = $item;
            return $carry;
        }, []);

        // index
        $startColumnItem = 'B';
        $endColumnItem = $columnHeaderEnd;
        $startColumnItemData = 'D';
        $columnMachineNo = 'B';
        $columnMachineName = 'C';
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
            $countMachine = 0;
            // daftar mesin
            foreach ($listMachine[$department['department_id']] as $machineNo => $machineName) {
                // memasukkan data
                $dataItem = $dataFilter[$department['department_id']][$machineNo] ?? (object)[
                    'qty_produksi' => 0,
                    'berat_produksi' => 0,
                    'seitai_berat_loss' => 0,
                    'seitai_cost' => 0,
                    'seitai_berat_loss_ponsu' => 0,
                    'infure_berat_loss' => 0,
                    'work_hour_mm' => 0,
                    'work_hour_off_mm' => 0,
                    'work_hour_on_mm' => 0,
                ];
                if ($dataItem->qty_produksi == 0 && $dataItem->berat_produksi == 0) {
                    continue;
                }
                $countMachine++;
                $columnItem = $startColumnItemData;

                // Menulis data mesin
                $spreadsheet->getActiveSheet()->setCellValue($columnMachineNo . $rowItem, $machineNo);
                $spreadsheet->getActiveSheet()->setCellValue($columnMachineName . $rowItem, $machineName);
                // phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);

                // jumlah produksi
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->qty_produksi);
                phpspreadsheet::numberFormatCommaThousandsOrZero($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // berat_produksi
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_produksi);
                phpspreadsheet::numberFormatCommaThousandsOrZero($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // seitai_berat_loss
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->seitai_berat_loss);
                phpspreadsheet::numberFormatCommaThousandsOrZero($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // seitai loss %
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_produksi > 0 ? $dataItem->seitai_berat_loss / ($dataItem->berat_produksi + $dataItem->seitai_berat_loss) : 0);
                phpspreadsheet::numberPercentageOrZero($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // seitai_cost
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->seitai_cost);
                phpspreadsheet::numberFormatCommaThousandsOrZero($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // seitai_berat_loss_ponsu
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->seitai_berat_loss_ponsu);
                phpspreadsheet::numberFormatCommaThousandsOrZero($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // infure_berat_loss
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->infure_berat_loss);
                phpspreadsheet::numberFormatCommaThousandsOrZero($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // produksi per jam
                $workHours = $dataItem->work_hour_on_mm; // Ambil nilai menit dari data
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->work_hour_on_mm > 0 ? $dataItem->qty_produksi / ($workHours / 60) : 0);
                phpspreadsheet::numberFormatCommaThousandsOrZero($spreadsheet, $columnItem . $rowItem);
                $columnItem++;

                // jam kerja
                $workHours = $dataItem->work_hour_on_mm; // Ambil nilai menit dari data
                $hours = floor($workHours / 60); // Hitung jumlah jam
                $minutes = $workHours % 60; // Hitung sisa menit
                $formatedWorkHours = sprintf('%d:%02d', $hours, $minutes);

                // jam mati
                $offHours = $dataItem->work_hour_off_mm; // Ambil nilai menit dari data
                $hours = floor($offHours / 60); // Hitung jumlah jam
                $minutes = $offHours % 60; // Hitung sisa menit
                $formatedOffHours = sprintf('%d:%02d', $hours, $minutes);

                // jalan mesin %
                $activeWorksheet->setCellValue($columnItem . $rowItem, $workHours > 0 ? $workHours / ($offHours + $workHours) : 0);
                phpspreadsheet::numberPercentageOrZero($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // jam kerja
                $activeWorksheet->setCellValue($columnItem . $rowItem, $formatedWorkHours);
                $columnItem++;
                // jam mati
                $activeWorksheet->setCellValue($columnItem . $rowItem, $formatedOffHours);
                phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem, false, 8, 'Calibri');
                $rowItem++;
            }
            phpspreadsheet::addBorderDottedMiddleHorizontal($spreadsheet, $startColumnItem . $startRowItem . ':' . $columnItem . $rowItem);
            // perhitungan jumlah berdasarkan departemen
            $spreadsheet->getActiveSheet()->mergeCells($columnMachineNo . $rowItem . ':' . $columnMachineName . $rowItem);
            // $activeWorksheet->setCellValue($columnMachineNo . $rowItem, 'Total ' . $department['department_name']);
            $columnItem = $startColumnItemData;
            $columnItemEnd = chr(ord($columnItem) + count($header) - 1);
            // jumlah produksi
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - $countMachine) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpspreadsheet::numberFormatThousands($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // berat_produksi
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - $countMachine) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpspreadsheet::numberFormatCommaThousandsOrZero($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // seitai_berat_loss
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - $countMachine) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpspreadsheet::numberFormatCommaThousandsOrZero($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // seitai loss %
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - $countMachine) . ':' . $columnItem . ($rowItem - 1) . ')/COUNTIF(' . $columnItem . ($rowItem - $countMachine) . ':' . $columnItem . ($rowItem - 1) . ', "<>0")');
            phpspreadsheet::numberPercentageOrZero($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // seitai_cost
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - $countMachine) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpspreadsheet::numberFormatCommaThousandsOrZero($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // seitai_berat_loss_ponsu
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - $countMachine) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpspreadsheet::numberFormatCommaThousandsOrZero($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // infure_berat_loss
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - $countMachine) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpspreadsheet::numberFormatCommaThousandsOrZero($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // produksi per jam
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - $countMachine) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpspreadsheet::numberFormatCommaThousandsOrZero($spreadsheet, $columnItem . $rowItem);
            $columnItem++;

            $jamKerjaMesin = array_reduce(array_keys($listMachine[$department['department_id']]->toArray()), function ($carry, $item) use ($dataFilter, $department) {
                $dataItem = $dataFilter[$department['department_id']][$item] ?? (object)[
                    'qty_produksi' => 0,
                    'berat_produksi' => 0,
                    'seitai_berat_loss' => 0,
                    'seitai_cost' => 0,
                    'seitai_berat_loss_ponsu' => 0,
                    'infure_berat_loss' => 0,
                    'work_hour_mm' => 0,
                    'work_hour_off_mm' => 0,
                    'work_hour_on_mm' => 0,
                ];
                $carry['workHours'] += $dataItem->work_hour_mm;
                $carry['offHours'] += $dataItem->work_hour_off_mm;
                return $carry;
            }, ['workHours' => 0, 'offHours' => 0]);

            // total jalan mesin % berdasarkan departemen
            $totalJalanMesin = $jamKerjaMesin['workHours'] + $jamKerjaMesin['offHours'];
            $hours = floor($totalJalanMesin / 60); // Hitung jumlah jam
            $minutes = $totalJalanMesin % 60; // Hitung sisa menit

            $avgJamKerjaMesin = $jamKerjaMesin['workHours'] > 0 ? $jamKerjaMesin['workHours'] / $totalJalanMesin : 0;
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, $avgJamKerjaMesin);
            phpspreadsheet::numberPercentageOrZero($spreadsheet, $columnItem . $rowItem);
            $columnItem++;

            // jam kerja
            $hours = floor($jamKerjaMesin['workHours'] / 60); // Hitung jumlah jam
            $minutes = $jamKerjaMesin['workHours'] % 60; // Hitung sisa menit
            $formatedWorkHours = sprintf('%d:%02d', $hours, $minutes);
            $activeWorksheet->setCellValue($columnItem . $rowItem, $formatedWorkHours);
            $columnItem++;

            // jam mati
            $hours = floor($jamKerjaMesin['offHours'] / 60); // Hitung jumlah jam
            $minutes = $jamKerjaMesin['offHours'] % 60; // Hitung sisa menit
            $formatedOffHours = sprintf('%d:%02d', $hours, $minutes);
            $activeWorksheet->setCellValue($columnItem . $rowItem, $formatedOffHours);

            phpspreadsheet::styleFont($spreadsheet, $columnMachineNo . $rowItem . ':' . $columnItem . $rowItem, true, 8, 'Calibri');
            phpspreadsheet::addBorderDottedMiddleHorizontal($spreadsheet, $columnMachineNo . $rowItem . ':' . $columnItem . $rowItem);
            $columnItem++;

            $rowItem++;
            $rowItem++;
        }

        // Grand total
        $rowGrandTotal = $rowItem;
        $spreadsheet->getActiveSheet()->mergeCells($columnMachineNo . $rowGrandTotal . ':' . $columnMachineName . $rowGrandTotal);
        $spreadsheet->getActiveSheet()->setCellValue($columnMachineNo . $rowGrandTotal, 'GRAND TOTAL');
        phpspreadsheet::styleFont($spreadsheet, $columnMachineNo . $rowGrandTotal . ':' . $columnHeaderEnd . $rowGrandTotal, true, 8, 'Calibri');

        // grand total
        $grandTotal = [
            'qty_produksi' => 0,
            'berat_produksi' => 0,
            'seitai_berat_loss' => 0,
            'seitai_cost' => 0,
            'seitai_berat_loss_ponsu' => 0,
            'infure_berat_loss' => 0,
            'work_hour_mm' => 0,
            'work_hour_on_mm' => 0,
            'work_hour_off_mm' => 0,
        ];

        foreach ($dataFilter as $departmentId => $department) {
            foreach ($department as $machineNo => $machine) {
                $grandTotal['qty_produksi'] += $machine->qty_produksi;
                $grandTotal['berat_produksi'] += $machine->berat_produksi;
                $grandTotal['seitai_berat_loss'] += $machine->seitai_berat_loss;
                $grandTotal['seitai_cost'] += $machine->seitai_cost;
                $grandTotal['seitai_berat_loss_ponsu'] += $machine->seitai_berat_loss_ponsu;
                $grandTotal['infure_berat_loss'] += $machine->infure_berat_loss;
                $grandTotal['work_hour_mm'] += $machine->work_hour_mm;
                $grandTotal['work_hour_on_mm'] += $machine->work_hour_on_mm;
                $grandTotal['work_hour_off_mm'] += $machine->work_hour_off_mm;
            }
        }

        $columnItem = $startColumnItemData;
        $columnItemEnd = chr(ord($columnItem) + count($header) - 1);
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['qty_produksi']);
        phpspreadsheet::numberFormatThousands($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_produksi']);
        phpspreadsheet::numberFormatCommaThousandsOrZero($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['seitai_berat_loss']);
        phpspreadsheet::numberFormatCommaThousandsOrZero($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // seitai loss %
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_produksi'] > 0 ? $grandTotal['seitai_berat_loss'] / ($grandTotal['berat_produksi'] + $grandTotal['seitai_berat_loss']) : 0);
        phpspreadsheet::numberPercentageOrZero($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // seitai_cost
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['seitai_cost']);
        phpspreadsheet::numberFormatCommaThousandsOrZero($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // seitai_berat_loss_ponsu
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['seitai_berat_loss_ponsu']);
        phpspreadsheet::numberFormatCommaThousandsOrZero($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // infure_berat_loss
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['infure_berat_loss']);
        phpspreadsheet::numberFormatCommaThousandsOrZero($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // produksi per jam
        $hours = floor($grandTotal['work_hour_on_mm'] / 60); // Hitung jumlah jam
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $hours > 0 ? $grandTotal['qty_produksi'] / $hours : 0);
        phpspreadsheet::numberFormatCommaThousandsOrZero($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;

        // jalan mesin %
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['work_hour_on_mm'] > 0 ? $grandTotal['work_hour_on_mm'] / ($grandTotal['work_hour_off_mm'] + $grandTotal['work_hour_on_mm']) : 0);
        phpspreadsheet::numberPercentageOrZero($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;

        // jam kerja
        $minutes = $grandTotal['work_hour_on_mm'] % 60; // Hitung sisa menit
        $formatedWorkHours = sprintf('%d:%02d', $hours, $minutes);
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $formatedWorkHours);
        $columnItem++;

        // jam mati
        $hours = floor($grandTotal['work_hour_off_mm'] / 60); // Hitung jumlah jam
        $minutes = $grandTotal['work_hour_off_mm'] % 60; // Hitung sisa menit
        $formatedOffHours = sprintf('%d:%02d', $hours, $minutes);
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $formatedOffHours);

        phpspreadsheet::addFullBorder($spreadsheet, $columnMachineNo . $rowGrandTotal . ':' . $columnItem . $rowGrandTotal);

        $activeWorksheet->getStyle($columnMesin . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart)->getAlignment()->setWrapText(true);

        // size auto
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);

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
