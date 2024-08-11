<?php

namespace App\Http\Livewire\Report;

use App\Exports\GeneralReportExport;
use App\Helpers\phpspreadsheet;
use App\Models\MsMachine;
use App\Models\MsWorkingShift;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class GeneralReportController extends Component
{
    public $jenisreport;
    public $tglMasuk;
    public $tglKeluar;
    public $jamMasuk;
    public $jamKeluar;
    public $workingShiftHour;
    public $nipon = 'Infure';


    public function mount()
    {
        $this->tglMasuk = Carbon::now()->format('Y-m-d');
        $this->tglKeluar = Carbon::now()->format('Y-m-d');
        $this->workingShiftHour = MsWorkingShift::select('work_hour_from', 'work_hour_till')->where('status', 1)->orderBy('work_hour_from', 'ASC')->get();
        $this->jamMasuk = $this->workingShiftHour[0]->work_hour_from;
        $this->jamKeluar = $this->workingShiftHour[count($this->workingShiftHour) - 1]->work_hour_till;
    }

    public function exportOld()
    {
        // mengecek apakah jenis report sudah dipilih atau belum
        if ($this->jenisreport == null) {
            $this->dispatch('notification', ['type' => 'warning', 'message' => 'Pilih Jenis Report.']);
            return;
        }
        switch ($this->jenisreport) {
            case 'Daftar Produksi Per Mesin':
                if ($this->nipon == 'Infure') {
                    return Excel::download(new GeneralReportExport(
                        $this->tglMasuk,
                        $this->tglKeluar,
                        $this->nipon,
                        $this->jenisreport,
                    ), 'Infure_Daftar Produksi Per Mesin.xlsx');
                } else {
                    return Excel::download(new GeneralReportExport(
                        $this->tglMasuk,
                        $this->tglKeluar,
                        $this->nipon,
                        $this->jenisreport,
                    ), 'Seitai_Daftar Produksi Per Mesin.xlsx');
                }

                break;

            case 'Daftar Produksi Per Tipe Per Mesin':
                if ($this->nipon == 'Infure') {
                    return Excel::download(new GeneralReportExport(
                        $this->tglMasuk,
                        $this->tglKeluar,
                        $this->nipon,
                        $this->jenisreport,
                    ), 'Infure_Daftar Produksi Per Mesin dan Type.xlsx');
                } else {
                    return Excel::download(new GeneralReportExport(
                        $this->tglMasuk,
                        $this->tglKeluar,
                        $this->nipon,
                        $this->jenisreport,
                    ), 'Seitai_Daftar Produksi Per Mesin dan Type.xlsx');
                }

                break;

            case 'Daftar Produksi Per Jenis':
                if ($this->nipon == 'Infure') {
                    return Excel::download(new GeneralReportExport(
                        $this->tglMasuk,
                        $this->tglKeluar,
                        $this->nipon,
                        $this->jenisreport,
                    ), 'Infure_Daftar Produksi Per Jenis.xlsx');
                } else {
                    return Excel::download(new GeneralReportExport(
                        $this->tglMasuk,
                        $this->tglKeluar,
                        $this->nipon,
                        $this->jenisreport,
                    ), 'Seitai_Daftar Produksi Per Jenis.xlsx');
                }

                break;
            case 'Daftar Produksi Per Tipe':
                if ($this->nipon == 'Infure') {
                    return Excel::download(new GeneralReportExport(
                        $this->tglMasuk,
                        $this->tglKeluar,
                        $this->nipon,
                        $this->jenisreport,
                    ), 'Infure_Daftar Produksi Per Tipe.xlsx');
                } else {
                    return Excel::download(new GeneralReportExport(
                        $this->tglMasuk,
                        $this->tglKeluar,
                        $this->nipon,
                        $this->jenisreport,
                    ), 'Seitai_Daftar Produksi Per Tipe.xlsx');
                }

                break;
            case 'Daftar Produksi Per Produk':
                if ($this->nipon == 'Infure') {
                    return Excel::download(new GeneralReportExport(
                        $this->tglMasuk,
                        $this->tglKeluar,
                        $this->nipon,
                        $this->jenisreport,
                    ), 'Infure_Daftar Produksi Per Produk.xlsx');
                } else {
                    return Excel::download(new GeneralReportExport(
                        $this->tglMasuk,
                        $this->tglKeluar,
                        $this->nipon,
                        $this->jenisreport,
                    ), 'Seitai_Daftar Produksi Per Produk.xlsx');
                }

                break;
            case 'Daftar Produksi Per Departemen Per Jenis':
                if ($this->nipon == 'Infure') {
                    return Excel::download(new GeneralReportExport(
                        $this->tglMasuk,
                        $this->tglKeluar,
                        $this->nipon,
                        $this->jenisreport,
                    ), 'Infure_Daftar Produksi Per Departemen Per Jenis.xlsx');
                } else {
                    return Excel::download(new GeneralReportExport(
                        $this->tglMasuk,
                        $this->tglKeluar,
                        $this->nipon,
                        $this->jenisreport,
                    ), 'Seitai_Daftar Produksi Per Departemen Per Jenis.xlsx');
                }

                break;
            case 'Daftar Produksi Per Departemen & Tipe':
                if ($this->nipon == 'Infure') {
                    return Excel::download(new GeneralReportExport(
                        $this->tglMasuk,
                        $this->tglKeluar,
                        $this->nipon,
                        $this->jenisreport,
                    ), 'Infure_Daftar Produksi Per Departemen & Tipe.xlsx');
                } else {
                    return Excel::download(new GeneralReportExport(
                        $this->tglMasuk,
                        $this->tglKeluar,
                        $this->nipon,
                        $this->jenisreport,
                    ), 'Seitai_Daftar Produksi Per Departemen & Tipe.xlsx');
                }

                break;
            case 'Daftar Produksi Per Departemen & Petugas':
                if ($this->nipon == 'Infure') {
                    return Excel::download(new GeneralReportExport(
                        $this->tglMasuk,
                        $this->tglKeluar,
                        $this->nipon,
                        $this->jenisreport,
                    ), 'Infure_Daftar Produksi Per Departemen & Petugas.xlsx');
                } else {
                    return Excel::download(new GeneralReportExport(
                        $this->tglMasuk,
                        $this->tglKeluar,
                        $this->nipon,
                        $this->jenisreport,
                    ), 'Seitai_Daftar Produksi Per Departemen & Petugas.xlsx');
                }

                break;
            case 'Daftar Produksi Per Palet':
                // dd($this->nipon);
                if ($this->nipon == 2) {
                    return Excel::download(new GeneralReportExport(
                        $this->tglMasuk,
                        $this->tglKeluar,
                        $this->nipon,
                        $this->jenisreport,
                    ), 'Infure_Daftar Produksi Per Palet.xlsx');
                }

                break;
            case 'Daftar Loss Per Departemen':
                if ($this->nipon == 'Infure') {
                    return Excel::download(new GeneralReportExport(
                        $this->tglMasuk,
                        $this->tglKeluar,
                        $this->nipon,
                        $this->jenisreport,
                    ), 'Infure_Daftar Loss Per Departemen.xlsx');
                } else {
                    return Excel::download(new GeneralReportExport(
                        $this->tglMasuk,
                        $this->tglKeluar,
                        $this->nipon,
                        $this->jenisreport,
                    ), 'Seitai_Daftar Loss Per Departemen.xlsx');
                }

                break;
            case 'Daftar Loss Per Departemen & Jenis':
                if ($this->nipon == 'Infure') {
                    return Excel::download(new GeneralReportExport(
                        $this->tglMasuk,
                        $this->tglKeluar,
                        $this->nipon,
                        $this->jenisreport,
                    ), 'Infure_Daftar Loss Per Departemen & Jenis.xlsx');
                } else {
                    return Excel::download(new GeneralReportExport(
                        $this->tglMasuk,
                        $this->tglKeluar,
                        $this->nipon,
                        $this->jenisreport,
                    ), 'Seitai_Daftar Loss Per Departemen & Jenis.xlsx');
                }

                break;
            case 'Daftar Loss Per Petugas':
                if ($this->nipon == 'Infure') {
                    return Excel::download(new GeneralReportExport(
                        $this->tglMasuk,
                        $this->tglKeluar,
                        $this->nipon,
                        $this->jenisreport,
                    ), 'Infure_Daftar Loss Per Petugas.xlsx');
                } else {
                    return Excel::download(new GeneralReportExport(
                        $this->tglMasuk,
                        $this->tglKeluar,
                        $this->nipon,
                        $this->jenisreport,
                    ), 'Seitai_Daftar Loss Per Petugas.xlsx');
                }

                break;
            case 'Daftar Loss Per Mesin':
                if ($this->nipon == 'Infure') {
                    return Excel::download(new GeneralReportExport(
                        $this->tglMasuk,
                        $this->tglKeluar,
                        $this->nipon,
                        $this->jenisreport,
                    ), 'Infure_Daftar Loss Per Mesin.xlsx');
                } else {
                    return Excel::download(new GeneralReportExport(
                        $this->tglMasuk,
                        $this->tglKeluar,
                        $this->nipon,
                        $this->jenisreport,
                    ), 'Seitai_Daftar Loss Per Mesin.xlsx');
                }

                break;
            case 'Kapasitas Produksi':
                if ($this->nipon == 'Infure') {
                    return Excel::download(new GeneralReportExport(
                        $this->tglMasuk,
                        $this->tglKeluar,
                        $this->nipon,
                        $this->jenisreport,
                    ), 'Infure_Kapasitas Produksi.xlsx');
                } else {
                    return Excel::download(new GeneralReportExport(
                        $this->tglMasuk,
                        $this->tglKeluar,
                        $this->nipon,
                        $this->jenisreport,
                    ), 'Seitai_Kapasitas Produksi.xlsx');
                }

                break;
            default:
                // dd('ini percobaan');
                session()->flash('notification', ['type' => 'warning', 'message' => 'Pilih Jenis Report.']);
        }
    }

    public function export()
    {
        // mengecek apakah jenis report sudah dipilih atau belum
        if ($this->jenisreport == null) {
            $this->dispatch('notification', ['type' => 'warning', 'message' => 'Pilih Jenis Report.']);
            return;
        }

        $tglMasuk = Carbon::parse($this->tglMasuk . ' ' . $this->jamMasuk);
        $tglKeluar = Carbon::parse($this->tglKeluar . ' ' . $this->jamKeluar);

        switch ($this->jenisreport) {
            case 'Daftar Produksi Per Mesin':
                if ($this->nipon == 'Infure') {
                    $file = $this->daftarProduksiPerMesinInfure($tglMasuk, $tglKeluar);
                    return response()->download($file);
                } else {
                    $this->daftarProduksiPerMesinSeitai($tglMasuk, $tglKeluar);
                }
                break;
        }
    }

    public function daftarProduksiPerMesinInfure($tglMasuk, $tglKeluar)
    {
        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();

        // Judul
        $activeWorksheet->setCellValue('B1', 'DAFTAR PRODUKSI PER MESIN INFURE');
        $activeWorksheet->setCellValue('B2', 'Periode : ' . $tglMasuk . ' s/d ' . $tglKeluar);
        // Style Judul
        phpspreadsheet::styleFont($spreadsheet, 'B1:B2', true, 11, 'Calibri');

        // Header
        $columnMesin = 'B';
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
            SELECT max(mac.machineNo) AS machine_no,
            max(mac.machineName) AS machine_name,
            max(dep.id) AS department_id,
            max(dep.name) AS department_name,
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
            FROM tdProduct_Assembly AS asy
            LEFT JOIN LATERAL (
                SELECT
                    SUM(EXTRACT(EPOCH FROM work_hour) / 60) AS work_hour,
                    SUM(EXTRACT(EPOCH FROM off_hour) / 60) AS off_hour,
                    SUM(EXTRACT(EPOCH FROM on_hour) / 60) AS on_hour
                FROM tdJamKerjaMesin AS jam_
                WHERE asy.machine_id = jam_.machine_id
                AND jam_.working_date BETWEEN '$tglMasuk' AND '$tglKeluar'
                GROUP BY jam_.machine_id
            ) AS jam ON true
            left JOIN msMachine AS mac ON asy.machine_id = mac.id
            left JOIN msDepartment AS dep ON mac.department_id = dep.id
            WHERE asy.production_date BETWEEN '$tglMasuk' AND '$tglKeluar'
            GROUP BY asy.machine_id
        ");

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
            ->get()
            ->groupBy('department_id')
            ->map(function ($item) {
                return $item->pluck('machinename', 'machineno');
            });

        $dataFilter = array_reduce($data, function ($carry, $item) {
            $carry[$item->machine_no] = $item;
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
            // daftar mesin
            foreach ($listMachine[$department['department_id']] as $machineNo => $machineName) {
                $columnItem = $startColumnItemData;

                // Menulis data mesin
                $spreadsheet->getActiveSheet()->setCellValue($columnMachineNo . $rowItem, $machineNo);
                $spreadsheet->getActiveSheet()->setCellValue($columnMachineName . $rowItem, $machineName);
                // phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);

                // memasukkan data
                $dataItem = $dataFilter[$machineNo] ?? (object)[
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
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_standard);
                $columnItem++;
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_produksi);
                $columnItem++;
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_standard > 0 ? $dataItem->berat_produksi / $dataItem->berat_standard : 0);
                phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->infure_cost);
                $columnItem++;
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->infure_berat_loss);
                $columnItem++;
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_produksi > 0 ? $dataItem->infure_berat_loss / $dataItem->berat_produksi : 0);
                phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->panjang_produksi);
                $columnItem++;
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->panjang_printing_inline);
                $columnItem++;
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->infure_cost_printing);
                $columnItem++;
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->infure_cost + $dataItem->infure_cost_printing);
                $columnItem++;

                // jam kerja
                $workHours = $dataItem->work_hour_mm; // Ambil nilai menit dari data
                $hours = floor($workHours / 60); // Hitung jumlah jam
                $minutes = $workHours % 60; // Hitung sisa menit
                $formatedWorkHours = sprintf('%d:%02d', $hours, $minutes);
                $activeWorksheet->setCellValue($columnItem . $rowItem, $formatedWorkHours);
                $columnItem++;

                // jam mati
                $offHours = $dataItem->work_hour_off_mm; // Ambil nilai menit dari data
                $hours = floor($offHours / 60); // Hitung jumlah jam
                $minutes = $offHours % 60; // Hitung sisa menit
                $formatedOffHours = sprintf('%d:%02d', $hours, $minutes);
                $activeWorksheet->setCellValue($columnItem . $rowItem, $formatedOffHours);
                $columnItem++;

                $activeWorksheet->setCellValue($columnItem . $rowItem, $workHours > 0 ? 100 - ($offHours / $workHours) : 0);
                phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowItem);
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
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listMachine[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            $columnItem++;
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listMachine[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            $columnItem++;
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listMachine[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')/COUNTIF(' . $columnItem . ($rowItem - count($listMachine[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ', "<>0")');
            phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listMachine[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            $columnItem++;
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listMachine[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            $columnItem++;
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listMachine[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')/COUNTIF(' . $columnItem . ($rowItem - count($listMachine[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ', "<>0")');
            phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listMachine[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            $columnItem++;
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listMachine[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            $columnItem++;
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listMachine[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            $columnItem++;
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listMachine[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            $columnItem++;
            // jam kerja
            $jamKerjaMesin = array_reduce(array_keys($listMachine[$department['department_id']]->toArray()), function ($carry, $item) use ($dataFilter) {
                $dataItem = $dataFilter[$item] ?? (object)[
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
                $carry['workHours'] += $dataItem->work_hour_mm;
                $carry['offHours'] += $dataItem->work_hour_off_mm;
                return $carry;
            }, ['workHours' => 0, 'offHours' => 0]);
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
            $columnItem++;

            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=IF(' . $columnItem . ($rowItem - 1) . '=0, 0, 100 - (' . $columnItem . ($rowItem - 2) . '/' . $columnItem . ($rowItem - 1) . '))');
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

        $grandTotal = array_reduce(array_keys($dataFilter), function ($carry, $item) use ($dataFilter) {
            $dataItem = $dataFilter[$item] ?? (object)[
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
            $carry['berat_standard'] += $dataItem->berat_standard;
            $carry['berat_produksi'] += $dataItem->berat_produksi;
            $carry['infure_cost'] += $dataItem->infure_cost;
            $carry['infure_berat_loss'] += $dataItem->infure_berat_loss;
            $carry['panjang_produksi'] += $dataItem->panjang_produksi;
            $carry['panjang_printing_inline'] += $dataItem->panjang_printing_inline;
            $carry['infure_cost_printing'] += $dataItem->infure_cost_printing;
            $carry['work_hour_mm'] += $dataItem->work_hour_mm;
            $carry['work_hour_off_mm'] += $dataItem->work_hour_off_mm;
            return $carry;
        }, [
            'berat_standard' => 0,
            'berat_produksi' => 0,
            'infure_cost' => 0,
            'infure_berat_loss' => 0,
            'panjang_produksi' => 0,
            'panjang_printing_inline' => 0,
            'infure_cost_printing' => 0,
            'work_hour_mm' => 0,
            'work_hour_off_mm' => 0,
        ]);

        $columnItem = $startColumnItemData;
        $columnItemEnd = chr(ord($columnItem) + count($header) - 1);
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_standard']);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_produksi']);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_standard'] > 0 ? $grandTotal['berat_produksi'] / $grandTotal['berat_standard'] : 0);
        phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['infure_cost']);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['infure_berat_loss']);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_produksi'] > 0 ? $grandTotal['infure_berat_loss'] / $grandTotal['berat_produksi'] : 0);
        phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['panjang_produksi']);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['panjang_printing_inline']);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['infure_cost_printing']);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['infure_cost'] + $grandTotal['infure_cost_printing']);
        $columnItem++;

        // jam kerja
        $hours = floor($grandTotal['work_hour_mm'] / 60); // Hitung jumlah jam
        $minutes = $grandTotal['work_hour_mm'] % 60; // Hitung sisa menit
        $formatedWorkHours = sprintf('%d:%02d', $hours, $minutes);
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $formatedWorkHours);
        $columnItem++;

        // jam mati
        $hours = floor($grandTotal['work_hour_off_mm'] / 60); // Hitung jumlah jam
        $minutes = $grandTotal['work_hour_off_mm'] % 60; // Hitung sisa menit
        $formatedOffHours = sprintf('%d:%02d', $hours, $minutes);
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $formatedOffHours);
        $columnItem++;

        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['work_hour_mm'] > 0 ? 100 - ($grandTotal['work_hour_off_mm'] / $grandTotal['work_hour_mm']) : 0);
        phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowGrandTotal);
        phpspreadsheet::addFullBorder($spreadsheet, $columnMachineNo . $rowGrandTotal . ':' . $columnItem . $rowGrandTotal);

        $activeWorksheet->getStyle($columnMesin . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart)->getAlignment()->setWrapText(true);

        $writer = new Xlsx($spreadsheet);
        $filename = 'asset/report/' . $this->nipon . '-' . $this->jenisreport . '.xlsx';
        $writer->save($filename);
        return $filename;
    }

    public function render()
    {
        return view('livewire.report.general-report')->extends('layouts.master');
    }
}
