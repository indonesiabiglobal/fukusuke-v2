<?php

namespace App\Http\Livewire\Report;

use App\Exports\GeneralReportExport;
use App\Models\MsDepartment;
use App\Models\MsMachine;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ProductionLossReportController extends Component
{
    public $jenisreport;
    public $tglAwal;
    public $tglAkhir;
    public $nipon = 'infure';


    public function mount()
    {
        $this->tglAwal = Carbon::now()->subYear()->format('Y-m');
        $this->tglAkhir = Carbon::now()->format('Y-m');
    }

    public function export()
    {
        if ($this->tglAwal > $this->tglAkhir) {
            session()->flash('error', 'Tanggal akhir tidak boleh kurang dari tanggal awal');
            return;
        }

        $filterDateStart = Carbon::parse($this->tglAwal)->format('Y-m-d 00:00:00');
        $filterDateEnd = Carbon::parse($this->tglAkhir)->endOfMonth()->format('Y-m-d 23:59:59');
        if ($this->nipon == 'infure') {
            $divisionCodeInfure = MsDepartment::where('name', 'INFURE')->first()->division_code;
            $data = DB::select(
                "SELECT x.*,loss.berat_loss,
                CASE
                WHEN x.tot_produksi = 0 THEN 0
                ELSE (loss.berat_loss / x.tot_produksi)
                END as persenloss
                from (
                    SELECT mac.id, mac.machinedivision,
                    mac.machineno AS machine_no,
                        mac.machinename AS machine_name,
                        dep.name AS department_name,
                            dep.id AS department_id,
                        dep.division_code,
                            pro.bulan,
                        COALESCE(pro.tot_produksi, 0) AS tot_produksi

                    FROM
                        msmachine AS mac
                    INNER JOIN
                        msdepartment AS dep ON mac.department_id = dep.id
                    LEFT JOIN (
                        SELECT
                            tpa.machine_id,to_char(tpa.production_date, 'MM yyyy') as bulan,
                                    sum(tpa.berat_produksi) as tot_produksi

                        FROM
                            tdproduct_assembly AS tpa
                        where tpa.production_date BETWEEN :filterDateStart and :filterDateEnd
                        GROUP BY
                            tpa.machine_id,to_char(tpa.production_date, 'MM yyyy')
                    ) AS pro ON mac.id = pro.machine_id
                    WHERE
                        mac.status = 1
                            and dep.division_code= :divisionCode
                        ) as x
                        LEFT JOIN (
                        SELECT
                            tpa.machine_id,to_char(tpa.production_date, 'MM yyyy') as bulan,
                                    sum(tpal.berat_loss) as berat_loss

                        FROM
                            tdproduct_assembly AS tpa
                                    left join tdproduct_assembly_loss as tpal on tpa.id=tpal.product_assembly_id
                        where tpa.production_date BETWEEN :filterDateStart and :filterDateEnd
                        GROUP BY
                            tpa.machine_id,to_char(tpa.production_date, 'MM yyyy')
                    ) AS loss ON x.id = loss.machine_id and x.bulan=loss.bulan",
                [
                    'filterDateStart' => $filterDateStart,
                    'filterDateEnd' => $filterDateEnd,
                    'divisionCode' => $divisionCodeInfure,
                ]
            );
            $listDepartment = array_reduce($data, function ($carry, $item) {
                $carry[$item->department_id] = [
                    'department_id' => $item->department_id,
                    'department_name' => $item->department_name
                ];
                return $carry;
            }, []);

            // data yang dibagi berdasarkan tanggal
            $dataFilter = array_reduce($data, function ($carry, $item) {
                $carry[$item->bulan][$item->machine_no] = [
                    'tot_produksi' => $item->tot_produksi,
                    'berat_loss' => $item->berat_loss,
                    'persenloss' => $item->persenloss,
                    'machine_no' => $item->machine_no,
                    'machine_name' => $item->machine_name,
                    'department_name' => $item->department_name,
                    'department_id' => $item->department_id,
                ];
                return $carry;
            }, []);

            // list mesin berdasarkan tanggal pertama dan departemen
            $listMachine = MsMachine::where('status', 1)
                ->whereIn('department_id', array_keys($listDepartment))
                ->get()
                ->groupBy('department_id')
                ->map(function ($item) {
                    return $item->pluck('machinename', 'machineno');
                });
        } else {
            $divisionCodeSeitai = MsDepartment::where('name', 'SEITAI')->first()->division_code;
            $data = DB::select(
                "SELECT
                    x.*,
                    loss.berat_loss,
                    (loss.berat_loss/x.tot_produksi) as persenloss
                FROM
                    (
                    SELECT
                        mac.ID,
                        mac.machinedivision,
                        mac.machineno AS machine_no,
                        mac.machinename AS machine_name,
                        dep.NAME AS department_name,
                        dep.id AS department_id,
                        dep.division_code,
                        pro.bulan,
                        COALESCE ( pro.tot_produksi, 0 ) AS tot_produksi
                    FROM
                        msmachine AS mac
                        INNER JOIN msdepartment AS dep ON mac.department_id = dep.
                        ID LEFT JOIN (
                        SELECT
                            tpa.machine_id,
                            to_char( tpa.production_date, 'MM yyyy' ) AS bulan,
                            SUM ( tpa.qty_produksi ) AS tot_produksi
                        FROM
                            tdproduct_goods AS tpa
                        WHERE
                            tpa.production_date BETWEEN :filterDateStart
                            AND :filterDateEnd
                        GROUP BY
                            tpa.machine_id,
                            to_char( tpa.production_date, 'MM yyyy' )
                        ) AS pro ON mac.ID = pro.machine_id
                    WHERE
                        mac.status = 1
                        AND dep.division_code = :divisionCode
                    ) AS x
                    LEFT JOIN (
                    SELECT
                        tpa.machine_id,
                        to_char( tpa.production_date, 'MM yyyy' ) AS bulan,
                        SUM ( tpal.berat_loss ) AS berat_loss
                    FROM
                        tdproduct_goods AS tpa
                        LEFT JOIN tdproduct_goods_loss AS tpal ON tpa.ID = tpal.product_goods_id
                    WHERE
                        tpa.production_date BETWEEN :filterDateStart
                        AND :filterDateEnd
                    GROUP BY
                        tpa.machine_id,
                        to_char( tpa.production_date, 'MM yyyy' )
                    ) AS loss ON x.ID = loss.machine_id
                    AND x.bulan = loss.bulan ORDER BY x.bulan,  x.machine_name",
                [
                    'filterDateStart' => $filterDateStart,
                    'filterDateEnd' => $filterDateEnd,
                    'divisionCode' => $divisionCodeSeitai,
                ]
            );
            $listDepartment = array_reduce($data, function ($carry, $item) {
                $carry[$item->department_id] = [
                    'department_id' => $item->department_id,
                    'department_name' => $item->department_name
                ];
                return $carry;
            }, []);

            // data yang dibagi berdasarkan tanggal
            $dataFilter = array_reduce($data, function ($carry, $item) {
                $carry[$item->bulan][$item->machine_no] = [
                    'tot_produksi' => $item->tot_produksi,
                    'berat_loss' => $item->berat_loss,
                    'persenloss' => $item->persenloss,
                    'machine_no' => $item->machine_no,
                    'machine_name' => $item->machine_name,
                    'department_name' => $item->department_name,
                    'department_id' => $item->department_id,
                ];
                return $carry;
            }, []);

            // list mesin berdasarkan tanggal pertama dan departemen
            $listMachine = MsMachine::where('status', 1)
                ->whereIn('department_id', array_keys($listDepartment))
                ->get()
                ->groupBy('department_id')
                ->map(function ($item) {
                    return $item->pluck('machinename', 'machineno');
                });
        }
        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();

        // Judul
        $activeWorksheet->setCellValue('B1', 'REPORT PRODUKSI DAN LOSS MESIN' . ($this->nipon == 'infure' ? ' INFURE' : 'SEITAI'));
        $activeWorksheet->setCellValue('B2', 'Periode : ' . $this->tglAwal . ' s/d ' . $this->tglAkhir);
        // Style Judul
        $this->styleFont($spreadsheet, 'B1:B2', true, 11, 'Calibri');

        // Header
        $spreadsheet->getActiveSheet()->mergeCells('B3:C4');
        $activeWorksheet->setCellValue('B3', 'Mesin');

        // Tambahkan full border untuk header 'Mesin'
        $cellMesin = 'B3:C4';
        $this->addFullBorder($spreadsheet, $cellMesin);
        $this->styleFont($spreadsheet, $cellMesin, true, 9, 'Calibri');
        $this->textAlignCenter($spreadsheet, $cellMesin);

        // buatkan looping dari bulan awal sampai bulan akhir
        $diffMonth = Carbon::parse($this->tglAkhir)->diffInMonths(Carbon::parse($this->tglAwal)) + 1;
        $tglAwalHeader = Carbon::parse($this->tglAwal);
        $columnHeaderStartDate = 'D';
        for ($month = 0; $month < $diffMonth; $month++) {
            // Menghitung cell akhir dengan menambah jumlah yang diinginkan
            $columnHeaderEndDate = $columnHeaderStartDate;
            for ($j = 0; $j < 2; $j++) {
                $columnHeaderEndDate++;
            }
            // header bulan
            $rowHeaderDate = 3;
            $rangeHeaderDate = "{$columnHeaderStartDate}{$rowHeaderDate}:{$columnHeaderEndDate}{$rowHeaderDate}";
            $this->addFullBorder($spreadsheet, $rangeHeaderDate);
            // header bulan
            $spreadsheet->getActiveSheet()->mergeCells($rangeHeaderDate);
            $spreadsheet->getActiveSheet()->setCellValue($columnHeaderStartDate . $rowHeaderDate, $tglAwalHeader->format('M-Y'));
            $this->textAlignCenter($spreadsheet, $rangeHeaderDate);

            // Tambahkan full border untuk header keterangan
            $rowHeaderDesc = 4;
            $this->addFullBorder($spreadsheet, "{$columnHeaderStartDate}{$rowHeaderDesc}:{$columnHeaderEndDate}{$rowHeaderDesc}");
            $this->styleFont($spreadsheet, "{$columnHeaderStartDate}{$rowHeaderDate}:{$columnHeaderEndDate}{$rowHeaderDesc}", true, 9, 'Calibri');
            // nomer kolom header keterangan
            $columnProduksi = $columnHeaderStartDate;
            $columnLoss = ++$columnHeaderStartDate;
            $columnPersenLoss = $columnHeaderEndDate;

            // header keterangan
            $spreadsheet->getActiveSheet()->setCellValue($columnProduksi . $rowHeaderDesc, 'Produksi');
            $spreadsheet->getActiveSheet()->getColumnDimension($columnProduksi)->setAutoSize(true);
            $spreadsheet->getActiveSheet()->setCellValue($columnLoss . $rowHeaderDesc, 'Loss');
            $spreadsheet->getActiveSheet()->setCellValue($columnPersenLoss . $rowHeaderDesc, '%Loss');
            $this->textAlignCenter($spreadsheet, "{$columnProduksi}{$rowHeaderDesc}:{$columnPersenLoss}{$rowHeaderDesc}");

            $columnMachineNo = 'B';
            $columnMachineName = 'C';
            $startRowItem = 5;
            $rowItem = $startRowItem;
            // daftar departemen
            foreach ($listDepartment as $department) {
                $spreadsheet->getActiveSheet()->setCellValue($columnMachineNo . $rowItem, $department['department_name']);
                $this->styleFont($spreadsheet, $columnMachineNo . $rowItem, true, 9, 'Calibri');

                $rowItem++;
                // daftar mesin
                foreach ($listMachine[$department['department_id']] as $machineno => $machinename) {
                    if ($month == 0) {
                        // Menulis data mesin
                        $spreadsheet->getActiveSheet()->setCellValue($columnMachineNo . $rowItem, $machineno);
                        $spreadsheet->getActiveSheet()->setCellValue($columnMachineName . $rowItem, $machinename);
                        // Mengatur lebar kolom agar sesuai dengan isi data
                        $spreadsheet->getActiveSheet()->getColumnDimension($columnMachineName)->setAutoSize(true);
                        $this->addFullBorder($spreadsheet, "{$columnMachineNo}{$rowItem}:{$columnMachineName}{$rowItem}");
                        $this->styleFont($spreadsheet, "{$columnMachineNo}{$rowItem}:{$columnMachineName}{$rowItem}", false, 8, 'Calibri');
                    }
                    // Menulis data produksi dan loss
                    $item = $dataFilter[$tglAwalHeader->format('m Y')][$machineno] ?? ['tot_produksi' => 0, 'berat_loss' => 0, 'persenloss' => 0];
                    $spreadsheet->getActiveSheet()->setCellValue($columnProduksi . $rowItem, $item['tot_produksi']);
                    $this->numberFormatCommaSeparated($spreadsheet, $columnProduksi . $rowItem);
                    $spreadsheet->getActiveSheet()->setCellValue($columnLoss . $rowItem, $item['berat_loss']);
                    $this->numberFormatCommaSeparated($spreadsheet, $columnLoss . $rowItem);
                    $spreadsheet->getActiveSheet()->setCellValue($columnPersenLoss . $rowItem, $item['persenloss']);
                    // Mengatur format sel menjadi persentase
                    $spreadsheet->getActiveSheet()->getStyle($columnPersenLoss . $rowItem)
                        ->getNumberFormat()
                        ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);

                    // border
                    $this->addFullBorder($spreadsheet, "{$columnProduksi}{$rowItem}:{$columnPersenLoss}{$rowItem}");
                    $this->styleFont($spreadsheet, "{$columnProduksi}{$rowItem}:{$columnPersenLoss}{$rowItem}", false, 8, 'Calibri');
                    $rowItem++;
                }
                // perhitungan jumlah berdasarkan departemen dan bulan
                $itemCountDepartment = $dataFilter[$tglAwalHeader->format('m Y')] ?? [];
                $itemCountDepartment = array_filter($itemCountDepartment, function ($item) use ($department) {
                    return $item['department_id'] == $department['department_id'];
                });
                $sumProduksiDepartment = array_sum(array_column($itemCountDepartment, 'tot_produksi'));
                $sumLossDepartment = array_sum(array_column($itemCountDepartment, 'berat_loss'));
                if (count($itemCountDepartment) == 0) {
                    $avgLossDepartment = 0;
                } else {
                    $avgLossDepartment = array_sum(array_column($itemCountDepartment, 'persenloss')) / count($itemCountDepartment);
                }
                $spreadsheet->getActiveSheet()->setCellValue($columnProduksi . $rowItem, $sumProduksiDepartment);
                $spreadsheet->getActiveSheet()->setCellValue($columnLoss . $rowItem, $sumLossDepartment);
                $spreadsheet->getActiveSheet()->setCellValue($columnPersenLoss . $rowItem, $avgLossDepartment);
                $this->addFullBorder($spreadsheet, "{$columnMachineNo}{$rowItem}:{$columnPersenLoss}{$rowItem}");
                $this->styleFont($spreadsheet, "{$columnMachineNo}{$rowItem}:{$columnPersenLoss}{$rowItem}", false, 8, 'Calibri');
                // Mengatur format sel menjadi persentase
                $spreadsheet->getActiveSheet()->getStyle($columnPersenLoss . $rowItem)
                    ->getNumberFormat()
                    ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);

                $spreadsheet->getActiveSheet()->mergeCells($columnMachineNo . $rowItem . ':' . $columnMachineName . $rowItem);
                $rowItem++;

                // border
                // $this->addFullBorder($spreadsheet, "{$columnMachineNo}5:{$columnMachineName}" . ($rowItem - 1));
                $rowItem++;
            }
            // perhitungan jumlah berdasarkan bulan
            $itemCountMonth = $dataFilter[$tglAwalHeader->format('m Y')] ?? [];
            $sumProduksiMonth = array_sum(array_column($itemCountMonth, 'tot_produksi'));
            $sumLossMonth = array_sum(array_column($itemCountMonth, 'berat_loss'));
            if (count($itemCountMonth) == 0) {
                $avgLossMonth = 0;
            } else {
                $avgLossMonth = array_sum(array_column($itemCountMonth, 'persenloss')) / count($itemCountMonth);
            }
            $spreadsheet->getActiveSheet()->setCellValue($columnProduksi . $rowItem, $sumProduksiMonth);
            $this->numberFormatCommaSeparated($spreadsheet, $columnProduksi . $rowItem);
            $spreadsheet->getActiveSheet()->setCellValue($columnLoss . $rowItem, $sumLossMonth);
            $this->numberFormatCommaSeparated($spreadsheet, $columnLoss . $rowItem);
            $spreadsheet->getActiveSheet()->setCellValue($columnPersenLoss . $rowItem, $avgLossMonth);
            $spreadsheet->getActiveSheet()->getStyle($columnPersenLoss . $rowItem)
                ->getNumberFormat()
                ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);

            // Menambahkan 1 bulan
            $tglAwalHeader->addMonth();
            $columnHeaderStartDate = ++$columnHeaderEndDate;
        }
        // cell value total pada kolom terakhir
        $columnValueTotal = $columnHeaderEndDate;
        $spreadsheet->getActiveSheet()->mergeCells($columnValueTotal . $rowHeaderDate . ':' . $columnValueTotal . $rowHeaderDesc);
        $spreadsheet->getActiveSheet()->setCellValue($columnValueTotal . $rowHeaderDate, 'Total');
        $spreadsheet->getActiveSheet()->getColumnDimension($columnValueTotal)->setAutoSize(true);
        // $this->addFullBorder($spreadsheet, $columnValueTotal . $rowHeaderDate .  ':' . $columnValueTotal . $rowHeaderDesc);
        // $this->styleFont($spreadsheet, $columnValueTotal . $rowHeaderDate .  ':' . $columnValueTotal . $rowHeaderDesc, true, 11, 'Calibri');

        // cell value avg pada kolom terakhir
        $columnValueAvg = $columnValueTotal;
        $columnValueAvg++;
        $spreadsheet->getActiveSheet()->mergeCells($columnValueAvg . $rowHeaderDate . ':' . $columnValueAvg . $rowHeaderDesc);
        $spreadsheet->getActiveSheet()->setCellValue($columnValueAvg . $rowHeaderDate, 'AVG');
        $this->addFullBorder($spreadsheet, $columnValueTotal . $rowHeaderDate . ':' . $columnValueAvg . $rowHeaderDesc);
        $this->styleFont($spreadsheet, $columnValueTotal . $rowHeaderDate . ':' . $columnValueAvg . $rowHeaderDesc, true, 11, 'Calibri');
        $this->textAlignCenter($spreadsheet, $columnValueTotal . $rowHeaderDate . ':' . $columnValueAvg . $rowHeaderDesc);

        // perhitungan jumlah produksi berdasarkan mesin
        $rowItemSum = $startRowItem;
        foreach ($listDepartment as $department) {
            $rowItemSum++;
            foreach ($listMachine[$department['department_id']] as $machineno => $machinename) {
                $itemCountMachine = array_filter($data, function ($item) use ($machineno) {
                    return $item->machine_no == $machineno;
                });
                $sumProduksiMachine = array_sum(array_column($itemCountMachine, 'tot_produksi'));
                $avgProduksiMachine = $sumProduksiMachine / count($itemCountMachine);

                $spreadsheet->getActiveSheet()->setCellValue($columnValueTotal . $rowItemSum, $sumProduksiMachine);
                $spreadsheet->getActiveSheet()->setCellValue($columnValueAvg . $rowItemSum, $avgProduksiMachine);
                $this->numberFormatCommaSeparated($spreadsheet, $columnValueTotal . $rowItemSum . ':' . $columnValueAvg . $rowItemSum);
                $this->addFullBorder($spreadsheet, $columnValueTotal . $rowItemSum . ':' . $columnValueAvg . $rowItemSum);
                $this->styleFont($spreadsheet, $columnValueTotal . $rowItemSum . ':' . $columnValueAvg . $rowItemSum, false, 8, 'Calibri');
                $rowItemSum++;
            }
            $this->addFullBorder($spreadsheet, $columnValueTotal . $rowItemSum . ':' . $columnValueAvg . $rowItemSum);
            $rowItemSum = $rowItemSum + 2;
        }
        // cell value grand total pada baris terakhir
        $rowGrandTotal = $rowItem;
        $spreadsheet->getActiveSheet()->mergeCells($columnMachineNo . $rowGrandTotal . ':' . $columnMachineName . $rowGrandTotal);
        $spreadsheet->getActiveSheet()->setCellValue($columnMachineNo . $rowGrandTotal, 'GRAND TOTAL');
        $this->styleFont($spreadsheet, $columnMachineNo . $rowGrandTotal, true, 8, 'Calibri');
        $this->addFullBorder($spreadsheet, $columnMachineNo . $rowGrandTotal . ':' . $columnValueAvg . $rowGrandTotal);
        $this->styleFont($spreadsheet, $columnMachineNo . $rowGrandTotal . ':' . $columnValueAvg . $rowGrandTotal, false, 8, 'Calibri');

        $writer = new Xlsx($spreadsheet);
        if ($this->nipon == 'Infure') {
            $writer->save('asset/report/Report-Infure.xlsx');
            return response()->download('asset/report/Report-Infure.xlsx');
        } else {
            $writer->save('asset/report/Report-Seitai.xlsx');
            return response()->download('asset/report/Report-Seitai.xlsx');
        }
    }

    public function addFullBorder($spreadsheet, $range, $borderStyle = \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, $color = 'FF000000')
    {
        $spreadsheet->getActiveSheet()->getStyle($range)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => $borderStyle,
                    'color' => ['argb' => $color],
                ],
            ],
        ]);
    }

    public function styleFont($spreadsheet, $range, $bold = false, $size = 12, $font = 'Calibri')
    {
        $spreadsheet->getActiveSheet()->getStyle($range)->applyFromArray([
            'font' => [
                'bold' => $bold,
                'size' => $size,
                'name' => $font,
            ],
        ]);
    }

    public function numberFormatCommaSeparated($spreadsheet, $range)
    {
        $spreadsheet->getActiveSheet()->getStyle($range)
            ->getNumberFormat()
            ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
    }

    public function textAlignCenter($spreadsheet, $range)
    {
        $spreadsheet->getActiveSheet()->getStyle($range)->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
    }

    public function render()
    {
        return view('livewire.report.production-loss-report')->extends('layouts.master');
    }
}