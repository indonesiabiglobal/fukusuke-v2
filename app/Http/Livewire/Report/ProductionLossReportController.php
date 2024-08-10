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
    public $nipon = '1';


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
        if ($this->nipon == 1) {
            $filterDateStart = Carbon::parse($this->tglAwal)->format('Y-m-d 00:00:00');
            $filterDateEnd = Carbon::parse($this->tglAkhir)->endOfMonth()->format('Y-m-d 23:59:59');
            $divisionCodeInfure = MsDepartment::where('name', 'INFURE')->first()->division_code;
            $data = DB::select(
                "SELECT x.*,loss.berat_loss,
            CASE
               WHEN x.tot_produksi = 0 THEN 0
               ELSE (loss.berat_loss / x.tot_produksi) * 100
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
                    where tpa.production_date BETWEEN :filterDateStart1 and :filterDateEnd1
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
                    where tpa.production_date BETWEEN :filterDateStart1 and :filterDateEnd1
                    GROUP BY
                        tpa.machine_id,to_char(tpa.production_date, 'MM yyyy')
                ) AS loss ON x.id = loss.machine_id and x.bulan=loss.bulan",
                [
                    'filterDateStart1' => $filterDateStart,
                    'filterDateEnd1' => $filterDateEnd,
                    'divisionCode' => $divisionCodeInfure,
                ]
            );
            // dd($data);
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
            // dd($dataFilter);

            // list mesin berdasarkan tanggal pertama dan departemen
            $listMachine = MsMachine::where('status', 1)
                ->whereIn('department_id', array_keys($listDepartment))
                ->get()
                ->groupBy('department_id')
                ->map(function ($item) {
                    return $item->pluck('machinename', 'machineno');
                });
        } else {
        }
        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();

        // Judul
        $activeWorksheet->setCellValue('B1', 'Report produksi pertahun infure');
        $activeWorksheet->setCellValue('B2', 'Periode : ' . $this->tglAwal . ' s/d ' . $this->tglAkhir);

        // Header
        $spreadsheet->getActiveSheet()->mergeCells('B3:C4');
        $activeWorksheet->setCellValue('B3', 'Mesin');

        // Tambahkan full border untuk header 'Mesin'
        $this->addFullBorder($spreadsheet, 'B3:C4');

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

            // Tambahkan full border untuk header keterangan
            $rowHeaderDesc = 4;
            $this->addFullBorder($spreadsheet, "{$columnHeaderStartDate}{$rowHeaderDesc}:{$columnHeaderEndDate}{$rowHeaderDesc}");
            // nomer kolom header keterangan
            $columnProduksi = $columnHeaderStartDate;
            $columnLoss = ++$columnHeaderStartDate;
            $columnPersenLoss = $columnHeaderEndDate;

            // header keterangan
            $spreadsheet->getActiveSheet()->setCellValue($columnProduksi . $rowHeaderDesc, 'Produksi');
            $spreadsheet->getActiveSheet()->setCellValue($columnLoss . $rowHeaderDesc, 'Loss');
            $spreadsheet->getActiveSheet()->setCellValue($columnPersenLoss . $rowHeaderDesc, '%Loss');

            $columnMachineNo = 'B';
            $columnMachineName = 'C';
            $startRowItem = 5;
            $rowItem = $startRowItem;
            // daftar departemen
            foreach ($listDepartment as $department) {
                $spreadsheet->getActiveSheet()->setCellValue($columnMachineNo . $rowItem, $department['department_name']);
                $rowItem++;
                // daftar mesin
                foreach ($listMachine[$department['department_id']] as $machineno => $machinename) {
                    if ($month == 0) {
                        // Menulis data mesin
                        $spreadsheet->getActiveSheet()->setCellValue($columnMachineNo . $rowItem, $machineno);
                        $spreadsheet->getActiveSheet()->setCellValue($columnMachineName . $rowItem, $machinename);
                        // Mengatur lebar kolom agar sesuai dengan isi data
                        $spreadsheet->getActiveSheet()->getColumnDimension($columnMachineName)->setAutoSize(true);
                        $this->addFullBorder($spreadsheet, "{$columnMachineNo}{$startRowItem}:{$columnMachineName}{$rowItem}");
                    }
                    // Menulis data produksi dan loss
                    $item = $dataFilter[$tglAwalHeader->format('m Y')][$machineno] ?? ['tot_produksi' => 0, 'berat_loss' => 0, 'persenloss' => 0];
                    $spreadsheet->getActiveSheet()->setCellValue($columnProduksi . $rowItem, $item['tot_produksi']);
                    $spreadsheet->getActiveSheet()->setCellValue($columnLoss . $rowItem, $item['berat_loss']);
                    $spreadsheet->getActiveSheet()->setCellValue($columnPersenLoss . $rowItem, $item['persenloss']);

                    // border
                    $this->addFullBorder($spreadsheet, "{$columnProduksi}{$startRowItem}:{$columnPersenLoss}{$rowItem}");
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
            $spreadsheet->getActiveSheet()->setCellValue($columnLoss . $rowItem, $sumLossMonth);
            $spreadsheet->getActiveSheet()->setCellValue($columnPersenLoss . $rowItem, $avgLossMonth);

            // Menambahkan 1 bulan
            $tglAwalHeader->addMonth();
            $columnHeaderStartDate = ++$columnHeaderEndDate;
        }
        // cell value total pada kolom terakhir
        $cellValueTotal = $columnHeaderEndDate;
        $spreadsheet->getActiveSheet()->mergeCells($cellValueTotal . $rowHeaderDate . ':' . $cellValueTotal . $rowHeaderDesc);
        $spreadsheet->getActiveSheet()->setCellValue($cellValueTotal . $rowHeaderDate, 'Total');
        $this->addFullBorder($spreadsheet, $cellValueTotal . $rowHeaderDate .  ':' . $cellValueTotal . $rowHeaderDesc);

        // cell value avg pada kolom terakhir
        $cellValueAvg = $cellValueTotal;
        $cellValueAvg++;
        $spreadsheet->getActiveSheet()->mergeCells($cellValueAvg . $rowHeaderDate . ':' . $cellValueAvg . $rowHeaderDesc);
        $spreadsheet->getActiveSheet()->setCellValue($cellValueAvg . $rowHeaderDate, 'AVG');
        $this->addFullBorder($spreadsheet, $cellValueAvg . $rowHeaderDate . ':' . $cellValueAvg . $rowHeaderDesc);

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

                $spreadsheet->getActiveSheet()->setCellValue($cellValueTotal . $rowItemSum, $sumProduksiMachine);
                $spreadsheet->getActiveSheet()->setCellValue($cellValueAvg . $rowItemSum, $avgProduksiMachine);
                $rowItemSum++;
            }
            $rowItemSum = $rowItemSum + 2;
        }
        // cell value grand total pada baris terakhir
        $rowGrandTotal = $rowItem;
        $spreadsheet->getActiveSheet()->mergeCells($columnMachineNo . $rowGrandTotal . ':' . $columnMachineName . $rowGrandTotal);
        $spreadsheet->getActiveSheet()->setCellValue($columnMachineNo . $rowGrandTotal, 'Grand Total');
        $this->addFullBorder($spreadsheet, $columnMachineNo . $rowGrandTotal . ':' . $cellValueAvg . $rowGrandTotal);

        $writer = new Xlsx($spreadsheet);
        $writer->save('asset/report/Report-Infure.xlsx');
        return response()->download('asset/report/Report-Infure.xlsx');
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


    public function render()
    {
        return view('livewire.report.production-loss-report')->extends('layouts.master');
    }
}
