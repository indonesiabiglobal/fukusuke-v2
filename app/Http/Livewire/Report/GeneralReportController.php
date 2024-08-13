<?php

namespace App\Http\Livewire\Report;

use Carbon\Carbon;
use Livewire\Component;
use App\Models\MsMachine;
use App\Models\MsWorkingShift;
use App\Helpers\phpspreadsheet;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Exports\GeneralReportExport;
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
                    $file = $this->daftarProduksiPerMesinSeitai($tglMasuk, $tglKeluar);
                    return response()->download($file);
                }
                break;
            case 'Daftar Produksi Per Tipe Per Mesin':
                if ($this->nipon == 'Infure') {
                    $file = $this->daftarProduksiPerTipePerMesinInfure($tglMasuk, $tglKeluar);
                    return response()->download($file);
                } else {
                    $file = $this->daftarProduksiPerTipePerMesinSeitai($tglMasuk, $tglKeluar);
                    return response()->download($file);
                }
                break;
            case 'Daftar Produksi Per Jenis':
                if ($this->nipon == 'Infure') {
                    $file = $this->daftarProduksiPerJenisInfure($tglMasuk, $tglKeluar);
                    return response()->download($file);
                } else {
                    $file = $this->daftarProduksiPerJenisSeitai($tglMasuk, $tglKeluar);
                    return response()->download($file);
                }
                break;
            case 'Daftar Produksi Per Tipe':
                if ($this->nipon == 'Infure') {
                    $file = $this->daftarProduksiPerTipeInfure($tglMasuk, $tglKeluar);
                    return response()->download($file);
                } else {
                    $file = $this->daftarProduksiPerTipeSeitai($tglMasuk, $tglKeluar);
                    return response()->download($file);
                }
                break;
            case 'Daftar Produksi Per Produk':
                if ($this->nipon == 'Infure') {
                    $file = $this->daftarProduksiPerProdukInfure($tglMasuk, $tglKeluar);
                    return response()->download($file);
                } else {
                    $file = $this->daftarProduksiPerProdukSeitai($tglMasuk, $tglKeluar);
                    return response()->download($file);
                }
                break;
            case 'Daftar Produksi Per Departemen Per Jenis':
                if ($this->nipon == 'Infure') {
                    $file = $this->daftarProduksiPerDepartemenPerJenisInfure($tglMasuk, $tglKeluar);
                    return response()->download($file);
                } else {
                    $file = $this->daftarProduksiPerDepartemenPerJenisSeitai($tglMasuk, $tglKeluar);
                    return response()->download($file);
                }
                break;
            case 'Daftar Produksi Per Departemen & Tipe':
                if ($this->nipon == 'Infure') {
                    $file = $this->daftarProduksiPerDepartemenPerTypeInfure($tglMasuk, $tglKeluar);
                    return response()->download($file);
                } else {
                    $file = $this->daftarProduksiPerDepartemenPerTypeSeitai($tglMasuk, $tglKeluar);
                    return response()->download($file);
                }
                break;
            case 'Daftar Produksi Per Departemen & Petugas':
                if ($this->nipon == 'Infure') {
                    $file = $this->daftarProduksiPerDepartemenPerPetugasInfure($tglMasuk, $tglKeluar);
                    return response()->download($file);
                } else {
                    $file = $this->daftarProduksiPerDepartemenPerPetugasSeitai($tglMasuk, $tglKeluar);
                    return response()->download($file);
                }
                break;
            case 'Daftar Loss Per Departemen':
                if ($this->nipon == 'Infure') {
                    $file = $this->daftarLossPerDepartemenInfure($tglMasuk, $tglKeluar);
                    return response()->download($file);
                } else {
                    $file = $this->daftarLossPerDepartemenSeitai($tglMasuk, $tglKeluar);
                    return response()->download($file);
                }
                break;
            case 'Daftar Loss Per Departemen & Jenis':
                if ($this->nipon == 'Infure') {
                    $file = $this->daftarLossPerDepartemenPerJenisInfure($tglMasuk, $tglKeluar);
                    return response()->download($file);
                } else {
                    $file = $this->daftarLossPerDepartemenPerJenisSeitai($tglMasuk, $tglKeluar);
                    return response()->download($file);
                }
                break;
        }
    }

    public function daftarProduksiPerMesinInfure($tglMasuk, $tglKeluar)
    {
        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();
        $activeWorksheet->setShowGridlines(false);

        // Judul
        $activeWorksheet->setCellValue('B1', 'DAFTAR PRODUKSI PER MESIN INFURE');
        $activeWorksheet->setCellValue('B2', 'Periode : ' . $tglMasuk . ' s/d ' . $tglKeluar);
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
                phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_produksi);
                phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_standard > 0 ? $dataItem->berat_produksi / $dataItem->berat_standard : 0);
                phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->infure_cost);
                phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->infure_berat_loss);
                phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_produksi > 0 ? $dataItem->infure_berat_loss / $dataItem->berat_produksi : 0);
                phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->panjang_produksi);
                phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->panjang_printing_inline);
                phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->infure_cost_printing);
                phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->infure_cost + $dataItem->infure_cost_printing);
                phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
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
            phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listMachine[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listMachine[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')/COUNTIF(' . $columnItem . ($rowItem - count($listMachine[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ', "<>0")');
            phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listMachine[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listMachine[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listMachine[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')/COUNTIF(' . $columnItem . ($rowItem - count($listMachine[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ', "<>0")');
            phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listMachine[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listMachine[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listMachine[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listMachine[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
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

    public function daftarProduksiPerMesinSeitai($tglMasuk, $tglKeluar)
    {
        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();
        $activeWorksheet->setShowGridlines(false);

        // Judul
        $activeWorksheet->setCellValue('B1', 'DAFTAR PRODUKSI PER MESIN SEITAI');
        $activeWorksheet->setCellValue('B2', 'Periode : ' . $tglMasuk . ' s/d ' . $tglKeluar);
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
                WHERE jam_.working_date BETWEEN  '$tglMasuk' AND '$tglKeluar'
                GROUP BY jam_.machine_id
            ) jam ON good.machine_id = jam.machine_id
            INNER JOIN msMachine AS mac ON good.machine_id = mac.id
            INNER JOIN msDepartment AS dep ON mac.department_id = dep.id
            INNER JOIN msProduct AS prd ON good.product_id = prd.id
            INNER JOIN msProduct_type AS prT ON prd.product_type_id = prT.id
            WHERE good.production_date BETWEEN  '$tglMasuk' AND '$tglKeluar'
            GROUP BY good.machine_id
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
                $columnItem++;
                // produksi per jam
                $workHours = $dataItem->work_hour_mm; // Ambil nilai menit dari data
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->work_hour_mm > 0 ? $dataItem->qty_produksi / ($dataItem->workHours / 60) : 0);
                $columnItem++;
                // $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_standard > 0 ? $dataItem->berat_produksi / $dataItem->berat_standard : 0);
                // phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowItem);
                // $columnItem++;
                // $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->infure_cost_printing);
                // $columnItem++;
                // $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->infure_cost + $dataItem->infure_cost_printing);
                // $columnItem++;

                // jam kerja
                $workHours = $dataItem->work_hour_mm; // Ambil nilai menit dari data
                $hours = floor($workHours / 60); // Hitung jumlah jam
                $minutes = $workHours % 60; // Hitung sisa menit
                $formatedWorkHours = sprintf('%d:%02d', $hours, $minutes);

                // jam mati
                $offHours = $dataItem->work_hour_off_mm; // Ambil nilai menit dari data
                $hours = floor($offHours / 60); // Hitung jumlah jam
                $minutes = $offHours % 60; // Hitung sisa menit
                $formatedOffHours = sprintf('%d:%02d', $hours, $minutes);

                // jalan mesin %
                $activeWorksheet->setCellValue($columnItem . $rowItem, $workHours > 0 ? 100 - ($offHours / $workHours) : 0);
                phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // jam kerja
                $activeWorksheet->setCellValue($columnItem . $rowItem, $formatedWorkHours);
                $columnItem++;
                // jam mati
                $activeWorksheet->setCellValue($columnItem . $rowItem, $formatedOffHours);
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
            // jumlah produksi
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listMachine[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpspreadsheet::numberFormatThousands($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // berat_produksi
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listMachine[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // seitai_berat_loss
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listMachine[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // seitai loss %
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listMachine[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')/COUNTIF(' . $columnItem . ($rowItem - count($listMachine[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ', "<>0")');
            phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // seitai_cost
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listMachine[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // seitai_berat_loss_ponsu
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listMachine[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // infure_berat_loss
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listMachine[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // produksi per jam
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listMachine[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;

            $jamKerjaMesin = array_reduce(array_keys($listMachine[$department['department_id']]->toArray()), function ($carry, $item) use ($dataFilter) {
                $dataItem = $dataFilter[$item] ?? (object)[
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
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, $jamKerjaMesin['workHours'] > 0 ? 100 - ($jamKerjaMesin['offHours'] / $jamKerjaMesin['workHours']) : 0);
            phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowItem);
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
            phpspreadsheet::addFullBorder($spreadsheet, $columnMachineNo . $rowItem . ':' . $columnItem . $rowItem);
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
            $carry['qty_produksi'] += $dataItem->qty_produksi;
            $carry['berat_produksi'] += $dataItem->berat_produksi;
            $carry['seitai_berat_loss'] += $dataItem->seitai_berat_loss;
            $carry['seitai_cost'] += $dataItem->seitai_cost;
            $carry['seitai_berat_loss_ponsu'] += $dataItem->seitai_berat_loss_ponsu;
            $carry['infure_berat_loss'] += $dataItem->infure_berat_loss;
            $carry['work_hour_mm'] += $dataItem->work_hour_mm;
            $carry['work_hour_off_mm'] += $dataItem->work_hour_off_mm;
            return $carry;
        }, [
            'qty_produksi' => 0,
            'berat_produksi' => 0,
            'seitai_berat_loss' => 0,
            'seitai_cost' => 0,
            'seitai_berat_loss_ponsu' => 0,
            'infure_berat_loss' => 0,
            'work_hour_mm' => 0,
            'work_hour_off_mm' => 0,
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
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['qty_produksi'] > 0 ? $grandTotal['infure_berat_loss'] / $grandTotal['qty_produksi'] : 0);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;

        // jalan mesin %
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['work_hour_mm'] > 0 ? 100 - ($grandTotal['work_hour_off_mm'] / $grandTotal['work_hour_mm']) : 0);
        phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowGrandTotal);
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

        phpspreadsheet::addFullBorder($spreadsheet, $columnMachineNo . $rowGrandTotal . ':' . $columnItem . $rowGrandTotal);

        $activeWorksheet->getStyle($columnMesin . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart)->getAlignment()->setWrapText(true);

        // size auto
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);

        $writer = new Xlsx($spreadsheet);
        $filename = 'asset/report/' . $this->nipon . '-' . $this->jenisreport . '.xlsx';
        $writer->save($filename);
        return $filename;
    }

    public function daftarProduksiPerTipePerMesinInfure($tglMasuk, $tglKeluar)
    {
        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();
        $activeWorksheet->setShowGridlines(false);

        // Judul
        $activeWorksheet->setCellValue('B1', 'DAFTAR PRODUKSI PER TIPE PER MESIN INFURE');
        $activeWorksheet->setCellValue('B2', 'Periode : ' . $tglMasuk . ' s/d ' . $tglKeluar);
        // Style Judul
        phpspreadsheet::styleFont($spreadsheet, 'B1:B2', true, 11, 'Calibri');

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
        phpspreadsheet::styleFont($spreadsheet, $columnMesin . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart, true, 9, 'Calibri');
        phpspreadsheet::textAlignCenter($spreadsheet, $columnMesin . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);

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
            $carry[$item->product_type_id][$item->machine_no] = $item->machine_name;
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
                $rowItem++;
                // daftar mesin
                foreach ($listMachine[$productType['product_type_id']] as $machineNo => $machineName) {
                    $columnItem = $startColumnItemData;

                    // Menulis data mesin
                    $spreadsheet->getActiveSheet()->setCellValue($columnMachineNo . $rowItem, $machineNo);
                    $spreadsheet->getActiveSheet()->setCellValue($columnMachineName . $rowItem, $machineName);
                    // phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);

                    // memasukkan data
                    $dataItem = $dataFilter[$department['department_id']][$productType['product_type_id']][$machineNo] ?? (object)[
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
                    $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_standard > 0 ? $dataItem->berat_produksi / $dataItem->berat_standard : 0);
                    phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowItem);
                    $columnItem++;
                    // infure cost
                    $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->infure_cost);
                    phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
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
                    phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                    $columnItem++;
                    // panjang inline printing
                    $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->panjang_printing_inline);
                    phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                    $columnItem++;
                    // inline printing cost
                    $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->infure_cost_printing);
                    phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                    $columnItem++;
                    // process cost
                    $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->infure_cost + $dataItem->infure_cost_printing);
                    phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                    phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);
                    $columnItem++;

                    phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem, false, 8, 'Calibri');
                    $rowItem++;
                }
                // perhitungan jumlah berdasarkan type produk
                $spreadsheet->getActiveSheet()->mergeCells($columnMachineNo . $rowItem . ':' . $columnMachineName . $rowItem);
                // $activeWorksheet->setCellValue($columnMachineNo . $rowItem, 'Total ' . $department['department_name']);
                $columnItem = $startColumnItemData;
                $columnItemEnd = chr(ord($columnItem) + count($header) - 1);
                $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listMachine[$productType['product_type_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
                phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listMachine[$productType['product_type_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
                phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listMachine[$productType['product_type_id']])) . ':' . $columnItem . ($rowItem - 1) . ')/COUNTIF(' . $columnItem . ($rowItem - count($listMachine[$productType['product_type_id']])) . ':' . $columnItem . ($rowItem - 1) . ', "<>0")');
                phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listMachine[$productType['product_type_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
                phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listMachine[$productType['product_type_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
                phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listMachine[$productType['product_type_id']])) . ':' . $columnItem . ($rowItem - 1) . ')/COUNTIF(' . $columnItem . ($rowItem - count($listMachine[$productType['product_type_id']])) . ':' . $columnItem . ($rowItem - 1) . ', "<>0")');
                phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listMachine[$productType['product_type_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
                phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listMachine[$productType['product_type_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
                phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listMachine[$productType['product_type_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
                phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listMachine[$productType['product_type_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
                phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                phpspreadsheet::addFullBorder($spreadsheet, $columnMachineNo . $rowItem . ':' . $columnItem . $rowItem);
                $columnItem++;
                phpspreadsheet::styleFont($spreadsheet, $columnMachineNo . $rowItem . ':' . $columnHeaderEnd . $rowItem, true, 8, 'Calibri');

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

        $activeWorksheet->getStyle($columnMesin . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart)->getAlignment()->setWrapText(true);

        $writer = new Xlsx($spreadsheet);
        $filename = 'asset/report/' . $this->nipon . '-' . $this->jenisreport . '.xlsx';
        $writer->save($filename);
        return $filename;
    }

    public function daftarProduksiPerTipePerMesinSeitai($tglMasuk, $tglKeluar)
    {
        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();
        $activeWorksheet->setShowGridlines(false);

        // Judul
        $activeWorksheet->setCellValue('A1', 'DAFTAR PRODUKSI PER TIPE PER MESIN SEITAI');
        $activeWorksheet->setCellValue('A2', 'Periode : ' . $tglMasuk . ' s/d ' . $tglKeluar);
        // Style Judul
        phpspreadsheet::styleFont($spreadsheet, 'B1:B2', true, 11, 'Calibri');

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
        phpspreadsheet::styleFont($spreadsheet, $columnMesin . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart, true, 9, 'Calibri');
        phpspreadsheet::textAlignCenter($spreadsheet, $columnMesin . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);

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
            $carry[$item->product_type_id][$item->machine_no] = $item->machine_name;
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
                phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowItem, false, 8, 'Calibri');
                $rowItem++;
                // daftar mesin
                foreach ($listMachine[$productType['product_type_id']] as $machineNo => $machineName) {
                    $columnItem = $startColumnItemData;

                    // Menulis data mesin
                    $spreadsheet->getActiveSheet()->setCellValue($columnMachineNo . $rowItem, $machineNo);
                    $spreadsheet->getActiveSheet()->setCellValue($columnMachineName . $rowItem, $machineName);
                    // phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);

                    // memasukkan data
                    $dataItem = $dataFilter[$department['department_id']][$productType['product_type_id']][$machineNo] ?? (object)[
                        'qty_produksi' => 0,
                        'berat_produksi' => 0,
                        'seitai_cost' => 0,
                        'seitai_berat_loss' => 0,
                        'seitai_berat_loss_ponsu' => 0,
                        'infure_berat_loss' => 0
                    ];
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
                    phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);
                    $columnItem++;

                    phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem, false, 8, 'Calibri');
                    $rowItem++;
                }
                // perhitungan jumlah berdasarkan type produk
                $spreadsheet->getActiveSheet()->mergeCells($columnMachineNo . $rowItem . ':' . $columnMachineName . $rowItem);
                // $activeWorksheet->setCellValue($columnMachineNo . $rowItem, 'Total ' . $department['department_name']);
                $columnItem = $startColumnItemData;
                $columnItemEnd = chr(ord($columnItem) + count($header) - 1);
                // jumlah produksi
                $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listMachine[$productType['product_type_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
                phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // berat produksi
                $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listMachine[$productType['product_type_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
                phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // Loss
                $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listMachine[$productType['product_type_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
                phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // loss %
                $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listMachine[$productType['product_type_id']])) . ':' . $columnItem . ($rowItem - 1) . ')/COUNTIF(' . $columnItem . ($rowItem - count($listMachine[$productType['product_type_id']])) . ':' . $columnItem . ($rowItem - 1) . ', "<>0")');
                phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // Seitai cost
                $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listMachine[$productType['product_type_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
                phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // Ponsu Loss
                $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listMachine[$productType['product_type_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
                phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // Infure Loss
                $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listMachine[$productType['product_type_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
                phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                phpspreadsheet::addFullBorder($spreadsheet, $columnMachineNo . $rowItem . ':' . $columnItem . $rowItem);
                $columnItem++;
                phpspreadsheet::styleFont($spreadsheet, $columnMachineNo . $rowItem . ':' . $columnHeaderEnd . $rowItem, true, 8, 'Calibri');

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

                    // dd($dataItems);
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
            // dd($totalByDepartment);

            // jumlah produksi
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, $totalByDepartment['qty_produksi']);
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // berat produksi
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, $totalByDepartment['berat_produksi']);
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // seitai cost
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, $totalByDepartment['seitai_cost']);
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

        $activeWorksheet->getStyle($columnMesin . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart)->getAlignment()->setWrapText(true);

        $writer = new Xlsx($spreadsheet);
        $filename = 'asset/report/' . $this->nipon . '-' . $this->jenisreport . '.xlsx';
        $writer->save($filename);
        return $filename;
    }

    public function daftarProduksiPerJenisInfure($tglMasuk, $tglKeluar)
    {
        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();
        $activeWorksheet->setShowGridlines(false);

        // Judul
        $activeWorksheet->setCellValue('B1', 'DAFTAR PRODUKSI PER JENIS INFURE');
        $activeWorksheet->setCellValue('B2', 'Periode : ' . $tglMasuk . ' s/d ' . $tglKeluar);
        // Style Judul
        phpspreadsheet::styleFont($spreadsheet, 'B1:B2', true, 11, 'Calibri');

        // Header
        $columnJenisProduk = 'B';
        $columnJenisProdukEnd = 'C';
        $spreadsheet->getActiveSheet()->mergeCells($columnJenisProduk . '3:' . $columnJenisProdukEnd . '3');
        $activeWorksheet->setCellValue('B3', 'JENIS PRODUK');

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

        $data = DB::select("
            SELECT max(prGrp.code) AS product_group_code,
                max(prGrp.name) AS product_group_name,
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
            INNER JOIN msProduct_group AS prGrp ON prTip.product_group_id = prGrp.id
            WHERE asy.production_date BETWEEN '$tglMasuk' AND '$tglKeluar'
            GROUP BY prGrp.id
        ");

        // list jenis produk
        $listProductType = array_reduce($data, function ($carry, $item) {
            $carry[$item->product_group_code] = [
                'product_group_code' => $item->product_group_code,
                'product_group_name' => $item->product_group_name
            ];
            return $carry;
        }, []);

        $dataFilter = array_reduce($data, function ($carry, $item) {
            $carry[$item->product_group_code] = $item;
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
            // daftar mesin
            $columnItem = $startColumnItemData;

            // Menulis data mesin
            $spreadsheet->getActiveSheet()->setCellValue($columnProductGroupCode . $rowItem, $productGroup['product_group_code']);
            $spreadsheet->getActiveSheet()->setCellValue($columnProductGroupName . $rowItem, $productGroup['product_group_name']);
            // phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);

            // memasukkan data
            $dataItem = $dataFilter[$productGroupCode] ?? (object)[
                'berat_standard' => 0,
                'berat_produksi' => 0,
                'infure_cost' => 0,
                'infure_berat_loss' => 0,
                'panjang_produksi' => 0,
                'panjang_printing_inline' => 0,
                'infure_cost_printing' => 0
            ];
            $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_standard);
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_produksi);
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_standard > 0 ? $dataItem->berat_produksi / $dataItem->berat_standard : 0);
            phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->infure_cost);
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->infure_berat_loss);
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_produksi > 0 ? $dataItem->infure_berat_loss / $dataItem->berat_produksi : 0);
            phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowItem);
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
            phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);
            $columnItem++;

            phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem, false, 8, 'Calibri');
            $rowItem++;
        }

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

        $activeWorksheet->getStyle($columnJenisProduk . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart)->getAlignment()->setWrapText(true);


        // size auto
        while ($startColumnItemData !== $endColumnItem) {
            $spreadsheet->getActiveSheet()->getColumnDimension($startColumnItemData)->setAutoSize(true);
            $startColumnItemData++;
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'asset/report/' . $this->nipon . '-' . $this->jenisreport . '.xlsx';
        $writer->save($filename);
        return $filename;
    }

    public function daftarProduksiPerJenisSeitai($tglMasuk, $tglKeluar)
    {
        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();
        $activeWorksheet->setShowGridlines(false);

        // Judul
        $activeWorksheet->setCellValue('B1', 'DAFTAR PRODUKSI PER JENIS SEITAI');
        $activeWorksheet->setCellValue('B2', 'Periode : ' . $tglMasuk . ' s/d ' . $tglKeluar);
        // Style Judul
        phpspreadsheet::styleFont($spreadsheet, 'B1:B2', true, 11, 'Calibri');

        // Header
        $columnJenisProduk = 'B';
        $columnJenisProdukEnd = 'C';
        $spreadsheet->getActiveSheet()->mergeCells($columnJenisProduk . '3:' . $columnJenisProdukEnd . '3');
        $activeWorksheet->setCellValue('B3', 'JENIS PRODUK');

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

        $data = DB::select("
            SELECT
                MAX(prGrp.code) AS product_group_code,
                MAX(prGrp.name) AS product_group_name,
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
            INNER JOIN msProduct_group AS prGrp ON prT.product_group_id = prGrp.id
            WHERE good.production_date BETWEEN '$tglMasuk' AND '$tglKeluar'
            GROUP BY prGrp.name
        ");

        // list jenis produk
        $listProductGroup = array_reduce($data, function ($carry, $item) {
            $carry[$item->product_group_code] = [
                'product_group_code' => $item->product_group_code,
                'product_group_name' => $item->product_group_name
            ];
            return $carry;
        }, []);

        $dataFilter = array_reduce($data, function ($carry, $item) {
            $carry[$item->product_group_code] = $item;
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
            // daftar mesin
            $columnItem = $startColumnItemData;

            // Menulis data mesin
            $spreadsheet->getActiveSheet()->setCellValue($columnProductGroupCode . $rowItem, $productGroup['product_group_code']);
            $spreadsheet->getActiveSheet()->setCellValue($columnProductGroupName . $rowItem, $productGroup['product_group_name']);
            // phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);

            // memasukkan data
            $dataItem = $dataFilter[$productGroupCode] ?? (object)[
                'qty_produksi' => 0,
                'berat_produksi' => 0,
                'seitai_cost' => 0,
                'seitai_berat_loss' => 0,
                'seitai_berat_loss_ponsu' => 0,
                'infure_berat_loss' => 0
            ];
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
            phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);
            $columnItem++;

            phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem, false, 8, 'Calibri');
            $rowItem++;
        }

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

        $activeWorksheet->getStyle($columnJenisProduk . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart)->getAlignment()->setWrapText(true);

        // size auto
        while ($startColumnItemData !== $endColumnItem) {
            $spreadsheet->getActiveSheet()->getColumnDimension($startColumnItemData)->setAutoSize(true);
            $startColumnItemData++;
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'asset/report/' . $this->nipon . '-' . $this->jenisreport . '.xlsx';
        $writer->save($filename);
        return $filename;
    }

    public function daftarProduksiPerTipeInfure($tglMasuk, $tglKeluar)
    {
        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();
        $activeWorksheet->setShowGridlines(false);

        // Judul
        $activeWorksheet->setCellValue('B1', 'DAFTAR PRODUKSI PER TIPE INFURE');
        $activeWorksheet->setCellValue('B2', 'Periode : ' . $tglMasuk . ' s/d ' . $tglKeluar);
        // Style Judul
        phpspreadsheet::styleFont($spreadsheet, 'B1:B2', true, 11, 'Calibri');

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
        ");

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
            // daftar mesin
            $columnItem = $startColumnItemData;

            // Menulis data mesin
            $spreadsheet->getActiveSheet()->setCellValue($columnProductGroupCode . $rowItem, $productGroup['product_type_code']);
            $spreadsheet->getActiveSheet()->setCellValue($columnProductGroupName . $rowItem, $productGroup['product_type_name']);
            // phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);

            // memasukkan data
            $dataItem = $dataFilter[$productGroupCode] ?? (object)[
                'berat_standard' => 0,
                'berat_produksi' => 0,
                'infure_cost' => 0,
                'infure_berat_loss' => 0,
                'panjang_produksi' => 0,
                'panjang_printing_inline' => 0,
                'infure_cost_printing' => 0
            ];
            $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_standard);
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_produksi);
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_standard > 0 ? $dataItem->berat_produksi / $dataItem->berat_standard : 0);
            phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->infure_cost);
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->infure_berat_loss);
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_produksi > 0 ? $dataItem->infure_berat_loss / $dataItem->berat_produksi : 0);
            phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowItem);
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
            phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);
            $columnItem++;

            phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem, false, 8, 'Calibri');
            $rowItem++;
        }

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

        $activeWorksheet->getStyle($columnJenisProduk . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart)->getAlignment()->setWrapText(true);


        // size auto
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $endColumnItem++;
        while ($startColumnItemData !== $endColumnItem) {
            $spreadsheet->getActiveSheet()->getColumnDimension($startColumnItemData)->setAutoSize(true);
            $startColumnItemData++;
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'asset/report/' . $this->nipon . '-' . $this->jenisreport . '.xlsx';
        $writer->save($filename);
        return $filename;
    }

    public function daftarProduksiPerTipeSeitai($tglMasuk, $tglKeluar)
    {
        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();
        $activeWorksheet->setShowGridlines(false);

        // Judul
        $activeWorksheet->setCellValue('B1', 'DAFTAR PRODUKSI PER TIPE SEITAI');
        $activeWorksheet->setCellValue('B2', 'Periode : ' . $tglMasuk . ' s/d ' . $tglKeluar);
        // Style Judul
        phpspreadsheet::styleFont($spreadsheet, 'B1:B2', true, 11, 'Calibri');

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
        ");

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
            // daftar mesin
            $columnItem = $startColumnItemData;

            // Menulis data mesin
            $spreadsheet->getActiveSheet()->setCellValue($columnProductGroupCode . $rowItem, $productGroup['product_type_code']);
            $spreadsheet->getActiveSheet()->setCellValue($columnProductGroupName . $rowItem, $productGroup['product_type_name']);
            // phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);

            // memasukkan data
            $dataItem = $dataFilter[$productGroupCode] ?? (object)[
                'qty_produksi' => 0,
                'berat_produksi' => 0,
                'seitai_cost' => 0,
                'seitai_berat_loss' => 0,
                'seitai_berat_loss_ponsu' => 0,
                'infure_berat_loss' => 0
            ];
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
            phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);
            $columnItem++;

            phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem, false, 8, 'Calibri');
            $rowItem++;
        }

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

        $activeWorksheet->getStyle($columnJenisProduk . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart)->getAlignment()->setWrapText(true);

        // size auto
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $endColumnItem++;
        while ($startColumnItemData !== $endColumnItem) {
            $spreadsheet->getActiveSheet()->getColumnDimension($startColumnItemData)->setAutoSize(true);
            $startColumnItemData++;
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'asset/report/' . $this->nipon . '-' . $this->jenisreport . '.xlsx';
        $writer->save($filename);
        return $filename;
    }

    public function daftarProduksiPerProdukInfure($tglMasuk, $tglKeluar)
    {
        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();
        $activeWorksheet->setShowGridlines(false);

        // Judul
        $activeWorksheet->setCellValue('B1', 'DAFTAR PRODUKSI PER PRODUK INFURE');
        $activeWorksheet->setCellValue('B2', 'Periode : ' . $tglMasuk . ' s/d ' . $tglKeluar);
        // Style Judul
        phpspreadsheet::styleFont($spreadsheet, 'B1:B2', true, 11, 'Calibri');

        // Header
        $columnJenisProduk = 'B';
        $columnJenisProdukEnd = 'C';
        $spreadsheet->getActiveSheet()->mergeCells($columnJenisProduk . '3:' . $columnJenisProdukEnd . '3');
        $activeWorksheet->setCellValue('B3', 'Nama Produk');

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

        $data = DB::select("
            SELECT
                max(prd.code) AS product_code,
                max(prd.name) AS product_name,
                SUM(asy.berat_standard) AS berat_standard,
                SUM(asy.berat_produksi) AS berat_produksi,
                SUM(asy.infure_cost) AS infure_cost,
                SUM(asy.infure_berat_loss) AS infure_berat_loss,
                SUM(asy.panjang_produksi) AS panjang_produksi,
                SUM(asy.panjang_printing_inline) AS panjang_printing_inline,
                SUM(asy.infure_cost_printing) AS infure_cost_printing
            FROM tdProduct_Assembly AS asy
            INNER JOIN msProduct AS prd ON asy.product_id = prd.id
            WHERE asy.production_date BETWEEN '$tglMasuk' AND '$tglKeluar'
            GROUP BY prd.id
            ORDER BY prd.name
        ");

        // list jenis produk
        $listProduct = array_reduce($data, function ($carry, $item) {
            $carry[$item->product_code] = [
                'product_code' => $item->product_code,
                'product_name' => $item->product_name
            ];
            return $carry;
        }, []);

        $dataFilter = array_reduce($data, function ($carry, $item) {
            $carry[$item->product_code] = $item;
            return $carry;
        }, []);

        // index
        $startColumnItem = 'B';
        $endColumnItem = $columnHeaderEnd;
        $startColumnItemData = 'D';
        $columnProductCode = 'B';
        $columnProductName = 'C';
        $startRowItem = 4;
        $rowItem = $startRowItem;
        // daftar departemen
        foreach ($listProduct as $productCode => $product) {
            // daftar mesin
            $columnItem = $startColumnItemData;

            // Menulis data mesin
            $spreadsheet->getActiveSheet()->setCellValue($columnProductCode . $rowItem, $product['product_code']);
            $spreadsheet->getActiveSheet()->setCellValue($columnProductName . $rowItem, $product['product_name']);
            // phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);

            // memasukkan data
            $dataItem = $dataFilter[$productCode] ?? (object)[
                'berat_standard' => 0,
                'berat_produksi' => 0,
                'infure_cost' => 0,
                'infure_berat_loss' => 0,
                'panjang_produksi' => 0,
                'panjang_printing_inline' => 0,
                'infure_cost_printing' => 0
            ];
            $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_standard);
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_produksi);
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_standard > 0 ? $dataItem->berat_produksi / $dataItem->berat_standard : 0);
            phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->infure_cost);
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->infure_berat_loss);
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_produksi > 0 ? $dataItem->infure_berat_loss / $dataItem->berat_produksi : 0);
            phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowItem);
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
            phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);
            $columnItem++;

            phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem, false, 8, 'Calibri');
            $rowItem++;
        }

        // Grand total
        $rowGrandTotal = $rowItem;
        $spreadsheet->getActiveSheet()->mergeCells($columnProductCode . $rowGrandTotal . ':' . $columnProductName . $rowGrandTotal);
        $spreadsheet->getActiveSheet()->setCellValue($columnProductCode . $rowGrandTotal, 'GRAND TOTAL');
        phpspreadsheet::styleFont($spreadsheet, $columnProductCode . $rowGrandTotal . ':' . $columnHeaderEnd . $rowGrandTotal, true, 8, 'Calibri');
        // $this->addFullBorder($spreadsheet, $columnProductCode . $rowGrandTotal . ':' . $columnValueAvg . $rowGrandTotal);

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
        phpspreadsheet::addFullBorder($spreadsheet, $columnProductCode . $rowGrandTotal . ':' . $columnItem . $rowGrandTotal);
        $columnItem++;

        $activeWorksheet->getStyle($columnJenisProduk . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart)->getAlignment()->setWrapText(true);


        // size auto
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $endColumnItem++;
        while ($startColumnItemData !== $endColumnItem) {
            $spreadsheet->getActiveSheet()->getColumnDimension($startColumnItemData)->setAutoSize(true);
            $startColumnItemData++;
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'asset/report/' . $this->nipon . '-' . $this->jenisreport . '.xlsx';
        $writer->save($filename);
        return $filename;
    }

    public function daftarProduksiPerProdukSeitai($tglMasuk, $tglKeluar)
    {
        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();
        $activeWorksheet->setShowGridlines(false);

        // Judul
        $activeWorksheet->setCellValue('B1', 'DAFTAR PRODUKSI PER PRODUK SEITAI');
        $activeWorksheet->setCellValue('B2', 'Periode : ' . $tglMasuk . ' s/d ' . $tglKeluar);
        // Style Judul
        phpspreadsheet::styleFont($spreadsheet, 'B1:B2', true, 11, 'Calibri');

        // Header
        $columnJenisProduk = 'B';
        $columnJenisProdukEnd = 'C';
        $spreadsheet->getActiveSheet()->mergeCells($columnJenisProduk . '3:' . $columnJenisProdukEnd . '3');
        $activeWorksheet->setCellValue('B3', 'Nama Produk');

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

        $data = DB::select("
            SELECT
                MAX(prd.code) AS product_code,
                MAX(prd.name) AS product_name,
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
            GROUP BY prd.id
            ORDER BY prd.name
        ");

        // list jenis produk
        $listProduct = array_reduce($data, function ($carry, $item) {
            $carry[$item->product_code] = [
                'product_code' => $item->product_code,
                'product_name' => $item->product_name
            ];
            return $carry;
        }, []);

        $dataFilter = array_reduce($data, function ($carry, $item) {
            $carry[$item->product_code] = $item;
            return $carry;
        }, []);

        // index
        $startColumnItem = 'B';
        $endColumnItem = $columnHeaderEnd;
        $startColumnItemData = 'D';
        $columnProductCode = 'B';
        $columnProductName = 'C';
        $startRowItem = 4;
        $rowItem = $startRowItem;
        // daftar departemen
        foreach ($listProduct as $productCode => $product) {
            // daftar mesin
            $columnItem = $startColumnItemData;

            // Menulis data mesin
            $spreadsheet->getActiveSheet()->setCellValue($columnProductCode . $rowItem, $product['product_code']);
            $spreadsheet->getActiveSheet()->setCellValue($columnProductName . $rowItem, $product['product_name']);
            // phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);

            // memasukkan data
            $dataItem = $dataFilter[$productCode] ?? (object)[
                'qty_produksi' => 0,
                'berat_produksi' => 0,
                'seitai_cost' => 0,
                'seitai_berat_loss' => 0,
                'seitai_berat_loss_ponsu' => 0,
                'infure_berat_loss' => 0
            ];
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
            phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);
            $columnItem++;

            phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem, false, 8, 'Calibri');
            $rowItem++;
        }

        // Grand total
        $rowGrandTotal = $rowItem;
        $spreadsheet->getActiveSheet()->mergeCells($columnProductCode . $rowGrandTotal . ':' . $columnProductName . $rowGrandTotal);
        $spreadsheet->getActiveSheet()->setCellValue($columnProductCode . $rowGrandTotal, 'GRAND TOTAL');
        phpspreadsheet::styleFont($spreadsheet, $columnProductCode . $rowGrandTotal . ':' . $columnHeaderEnd . $rowGrandTotal, true, 8, 'Calibri');
        // $this->addFullBorder($spreadsheet, $columnProductCode . $rowGrandTotal . ':' . $columnValueAvg . $rowGrandTotal);

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
        phpspreadsheet::addFullBorder($spreadsheet, $columnProductCode . $rowGrandTotal . ':' . $columnItem . $rowGrandTotal);
        $columnItem++;

        $activeWorksheet->getStyle($columnJenisProduk . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart)->getAlignment()->setWrapText(true);

        // size auto
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $endColumnItem++;
        while ($startColumnItemData !== $endColumnItem) {
            $spreadsheet->getActiveSheet()->getColumnDimension($startColumnItemData)->setAutoSize(true);
            $startColumnItemData++;
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'asset/report/' . $this->nipon . '-' . $this->jenisreport . '.xlsx';
        $writer->save($filename);
        return $filename;
    }

    public function daftarProduksiPerDepartemenPerJenisInfure($tglMasuk, $tglKeluar)
    {
        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();
        $activeWorksheet->setShowGridlines(false);

        // Judul
        $activeWorksheet->setCellValue('B1', 'DAFTAR PRODUKSI PER DEPARTEMEN PER JENIS INFURE');
        $activeWorksheet->setCellValue('B2', 'Periode : ' . $tglMasuk . ' s/d ' . $tglKeluar);
        // Style Judul
        phpspreadsheet::styleFont($spreadsheet, 'B1:B2', true, 11, 'Calibri');

        // Header
        $columnMesin = 'B';
        $columnMesinEnd = 'C';
        $spreadsheet->getActiveSheet()->mergeCells($columnMesin . '3:' . $columnMesinEnd . '3');
        $activeWorksheet->setCellValue('B3', 'Jenis Produk');

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
                max(dep.name) AS department_name,
                max(dep.id) AS department_id,
                max(prGrp.code) AS product_group_code,
                max(prGrp.name) AS product_group_name,
                SUM(asy.berat_standard) AS berat_standard,
                SUM(asy.berat_produksi) AS berat_produksi,
                SUM(asy.infure_cost) AS infure_cost,
                SUM(asy.infure_berat_loss) AS infure_berat_loss,
                SUM(asy.panjang_produksi) AS panjang_produksi,
                SUM(asy.panjang_printing_inline) AS panjang_printing_inline,
                SUM(asy.infure_cost_printing) AS infure_cost_printing
            FROM tdProduct_Assembly AS asy
            INNER JOIN msMachine AS mac ON asy.machine_id = mac.id
            INNER JOIN msDepartment AS dep ON mac.department_id = dep.id
            INNER JOIN msProduct AS prd ON asy.product_id = prd.id
            INNER JOIN msProduct_type AS prTip ON prd.product_type_id = prTip.id
            INNER JOIN msProduct_group AS prGrp ON prTip.product_group_id = prGrp.id
            WHERE asy.production_date BETWEEN '$tglMasuk' AND '$tglKeluar'
            GROUP BY dep.id, prGrp.id
        ");

        $listDepartment = array_reduce($data, function ($carry, $item) {
            $carry[$item->department_id] = [
                'department_id' => $item->department_id,
                'department_name' => $item->department_name
            ];
            return $carry;
        }, []);

        // list jenis produk
        $listProductGroup = array_reduce($data, function ($carry, $item) {
            $carry[$item->department_id][$item->product_group_code] = $item->product_group_name;
            return $carry;
        }, []);

        $dataFilter = array_reduce($data, function ($carry, $item) {
            $carry[$item->product_group_code] = $item;
            return $carry;
        }, []);

        // index
        $startColumnItem = 'B';
        $endColumnItem = $columnHeaderEnd;
        $startColumnItemData = 'D';
        $columnTypeCode = 'B';
        $columnTypeName = 'C';
        $startRowItem = 4;
        $rowItem = $startRowItem;
        // daftar departemen
        foreach ($listDepartment as $department) {
            // Menulis data departemen
            $activeWorksheet->setCellValue($startColumnItem . $rowItem, $department['department_name']);
            $spreadsheet->getActiveSheet()->mergeCells($startColumnItem . $rowItem . ':' . $endColumnItem . $rowItem);
            phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowItem, true, 9, 'Calibri');
            $rowItem++;
            // daftar mesin
            foreach ($listProductGroup[$department['department_id']] as $typeCode => $typeName) {
                $columnItem = $startColumnItemData;

                // Menulis data mesin
                $spreadsheet->getActiveSheet()->setCellValue($columnTypeCode . $rowItem, $typeCode);
                $spreadsheet->getActiveSheet()->setCellValue($columnTypeName . $rowItem, $typeName);
                // phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);

                // memasukkan data
                $dataItem = $dataFilter[$typeCode] ?? (object)[
                    'berat_standard' => 0,
                    'berat_produksi' => 0,
                    'infure_cost' => 0,
                    'infure_berat_loss' => 0,
                    'panjang_produksi' => 0,
                    'panjang_printing_inline' => 0,
                    'infure_cost_printing' => 0,
                ];
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_standard);
                phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_produksi);
                phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_standard > 0 ? $dataItem->berat_produksi / $dataItem->berat_standard : 0);
                phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->infure_cost);
                phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->infure_berat_loss);
                phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_produksi > 0 ? $dataItem->infure_berat_loss / $dataItem->berat_produksi : 0);
                phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->panjang_produksi);
                phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->panjang_printing_inline);
                phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->infure_cost_printing);
                phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->infure_cost + $dataItem->infure_cost_printing);
                phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);
                $columnItem++;

                phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem, false, 8, 'Calibri');
                $rowItem++;
            }
            // perhitungan jumlah berdasarkan departemen
            $spreadsheet->getActiveSheet()->mergeCells($columnTypeCode . $rowItem . ':' . $columnTypeName . $rowItem);
            $activeWorksheet->setCellValue($columnTypeCode . $rowItem, 'Sub Total');
            $columnItem = $startColumnItemData;
            $columnItemEnd = chr(ord($columnItem) + count($header) - 1);
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listProductGroup[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listProductGroup[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listProductGroup[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')/COUNTIF(' . $columnItem . ($rowItem - count($listProductGroup[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ', "<>0")');
            phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listProductGroup[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listProductGroup[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listProductGroup[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')/COUNTIF(' . $columnItem . ($rowItem - count($listProductGroup[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ', "<>0")');
            phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listProductGroup[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listProductGroup[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listProductGroup[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listProductGroup[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            phpspreadsheet::addFullBorder($spreadsheet, $columnTypeCode . $rowItem . ':' . $columnItem . $rowItem);
            phpspreadsheet::styleFont($spreadsheet, $columnTypeCode . $rowItem . ':' . $columnItem . $rowItem, true, 8, 'Calibri');
            $columnItem++;

            $rowItem++;
            $rowItem++;
        }

        // Grand total
        $rowGrandTotal = $rowItem;
        $spreadsheet->getActiveSheet()->mergeCells($columnTypeCode . $rowGrandTotal . ':' . $columnTypeName . $rowGrandTotal);
        $spreadsheet->getActiveSheet()->setCellValue($columnTypeCode . $rowGrandTotal, 'GRAND TOTAL');
        phpspreadsheet::styleFont($spreadsheet, $columnTypeCode . $rowGrandTotal . ':' . $columnHeaderEnd . $rowGrandTotal, true, 8, 'Calibri');
        // $this->addFullBorder($spreadsheet, $columnTypeCode . $rowGrandTotal . ':' . $columnValueAvg . $rowGrandTotal);

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
        phpspreadsheet::addFullBorder($spreadsheet, $columnTypeCode . $rowGrandTotal . ':' . $columnItem . $rowGrandTotal);
        $columnItem++;

        $activeWorksheet->getStyle($columnMesin . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart)->getAlignment()->setWrapText(true);

        // size auto
        $endColumnItem++;
        while ($startColumnItemData !== $endColumnItem) {
            $spreadsheet->getActiveSheet()->getColumnDimension($startColumnItemData)->setAutoSize(true);
            $startColumnItemData++;
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'asset/report/' . $this->nipon . '-' . $this->jenisreport . '.xlsx';
        $writer->save($filename);
        return $filename;
    }

    public function daftarProduksiPerDepartemenPerJenisSeitai($tglMasuk, $tglKeluar)
    {
        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();
        $activeWorksheet->setShowGridlines(false);

        // Judul
        $activeWorksheet->setCellValue('B1', 'DAFTAR PRODUKSI PER DEPARTEMEN PER JENIS SEITAI');
        $activeWorksheet->setCellValue('B2', 'Periode : ' . $tglMasuk . ' s/d ' . $tglKeluar);
        // Style Judul
        phpspreadsheet::styleFont($spreadsheet, 'B1:B2', true, 11, 'Calibri');

        // Header
        $columnMesin = 'B';
        $columnMesinEnd = 'C';
        $spreadsheet->getActiveSheet()->mergeCells($columnMesin . '3:' . $columnMesinEnd . '3');
        $activeWorksheet->setCellValue('B3', 'Jenis Produk');

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
                MAX(dep.name) AS department_name,
                MAX(dep.id) AS department_id,
                MAX(prGrp.code) AS product_group_code,
                MAX(prGrp.name) AS product_group_name,
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
            INNER JOIN msMachine AS mac ON good.machine_id = mac.id
            INNER JOIN msDepartment AS dep ON mac.department_id = dep.id
            INNER JOIN msProduct AS prd ON good.product_id = prd.id
            INNER JOIN msProduct_type AS prT ON prd.product_type_id = prT.id
            INNER JOIN msProduct_group AS prGrp ON prT.product_group_id = prGrp.id
            WHERE good.production_date BETWEEN '$this->tglMasuk' AND '$this->tglKeluar'
            GROUP BY dep.id, prGrp.id
        ");

        $listDepartment = array_reduce($data, function ($carry, $item) {
            $carry[$item->department_id] = [
                'department_id' => $item->department_id,
                'department_name' => $item->department_name
            ];
            return $carry;
        }, []);

        // list jenis produk
        $listProductGroup = array_reduce($data, function ($carry, $item) {
            $carry[$item->department_id][$item->product_group_code] = $item->product_group_name;
            return $carry;
        }, []);

        $dataFilter = array_reduce($data, function ($carry, $item) {
            $carry[$item->product_group_code] = $item;
            return $carry;
        }, []);

        // index
        $startColumnItem = 'B';
        $endColumnItem = $columnHeaderEnd;
        $startColumnItemData = 'D';
        $columnTypeCode = 'B';
        $columnTypeName = 'C';
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
            foreach ($listProductGroup[$department['department_id']] as $TypeCode => $TypeName) {
                $columnItem = $startColumnItemData;

                // Menulis data mesin
                $spreadsheet->getActiveSheet()->setCellValue($columnTypeCode . $rowItem, $TypeCode);
                $spreadsheet->getActiveSheet()->setCellValue($columnTypeName . $rowItem, $TypeName);
                // phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);

                // memasukkan data
                $dataItem = $dataFilter[$TypeCode] ?? (object)[
                    'qty_produksi' => 0,
                    'berat_produksi' => 0,
                    'seitai_berat_loss' => 0,
                    'seitai_cost' => 0,
                    'seitai_berat_loss_ponsu' => 0,
                    'infure_berat_loss' => 0,
                ];
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
                phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);
                $columnItem++;

                phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem, false, 8, 'Calibri');
                $rowItem++;
            }
            // perhitungan jumlah berdasarkan departemen
            $spreadsheet->getActiveSheet()->mergeCells($columnTypeCode . $rowItem . ':' . $columnTypeName . $rowItem);
            // $activeWorksheet->setCellValue($columnTypeCode . $rowItem, 'Total ' . $department['department_name']);
            $columnItem = $startColumnItemData;
            $columnItemEnd = chr(ord($columnItem) + count($header) - 1);
            // jumlah produksi
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listProductGroup[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpspreadsheet::numberFormatThousands($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // berat_produksi
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listProductGroup[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // seitai_berat_loss
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listProductGroup[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // seitai loss %
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listProductGroup[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')/COUNTIF(' . $columnItem . ($rowItem - count($listProductGroup[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ', "<>0")');
            phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // seitai_cost
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listProductGroup[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // seitai_berat_loss_ponsu
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listProductGroup[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // infure_berat_loss
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listProductGroup[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            phpspreadsheet::addFullBorder($spreadsheet, $columnTypeCode . $rowItem . ':' . $columnItem . $rowItem);
            phpspreadsheet::styleFont($spreadsheet, $columnTypeCode . $rowItem . ':' . $columnItem . $rowItem, true, 8, 'Calibri');
            $columnItem++;


            $rowItem++;
            $rowItem++;
        }

        // Grand total
        $rowGrandTotal = $rowItem;
        $spreadsheet->getActiveSheet()->mergeCells($columnTypeCode . $rowGrandTotal . ':' . $columnTypeName . $rowGrandTotal);
        $spreadsheet->getActiveSheet()->setCellValue($columnTypeCode . $rowGrandTotal, 'GRAND TOTAL');
        phpspreadsheet::styleFont($spreadsheet, $columnTypeCode . $rowGrandTotal . ':' . $columnHeaderEnd . $rowGrandTotal, true, 8, 'Calibri');
        // $this->addFullBorder($spreadsheet, $columnTypeCode . $rowGrandTotal . ':' . $columnValueAvg . $rowGrandTotal);

        $grandTotal = array_reduce(array_keys($dataFilter), function ($carry, $item) use ($dataFilter) {
            $dataItem = $dataFilter[$item] ?? (object)[
                'qty_produksi' => 0,
                'berat_produksi' => 0,
                'seitai_berat_loss' => 0,
                'seitai_cost' => 0,
                'seitai_berat_loss_ponsu' => 0,
                'infure_berat_loss' => 0,
            ];
            $carry['qty_produksi'] += $dataItem->qty_produksi;
            $carry['berat_produksi'] += $dataItem->berat_produksi;
            $carry['seitai_berat_loss'] += $dataItem->seitai_berat_loss;
            $carry['seitai_cost'] += $dataItem->seitai_cost;
            $carry['seitai_berat_loss_ponsu'] += $dataItem->seitai_berat_loss_ponsu;
            $carry['infure_berat_loss'] += $dataItem->infure_berat_loss;
            return $carry;
        }, [
            'qty_produksi' => 0,
            'berat_produksi' => 0,
            'seitai_berat_loss' => 0,
            'seitai_cost' => 0,
            'seitai_berat_loss_ponsu' => 0,
            'infure_berat_loss' => 0,
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
        phpspreadsheet::addFullBorder($spreadsheet, $columnTypeCode . $rowGrandTotal . ':' . $columnItem . $rowGrandTotal);
        $columnItem++;

        $activeWorksheet->getStyle($columnMesin . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart)->getAlignment()->setWrapText(true);

        // size auto
        $endColumnItem++;
        while ($startColumnItemData !== $endColumnItem) {
            $spreadsheet->getActiveSheet()->getColumnDimension($startColumnItemData)->setAutoSize(true);
            $startColumnItemData++;
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'asset/report/' . $this->nipon . '-' . $this->jenisreport . '.xlsx';
        $writer->save($filename);
        return $filename;
    }

    public function daftarProduksiPerDepartemenPerTypeInfure($tglMasuk, $tglKeluar)
    {
        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();
        $activeWorksheet->setShowGridlines(false);

        // Judul
        $activeWorksheet->setCellValue('B1', 'DAFTAR PRODUKSI PER DEPARTEMEN PER TIPE INFURE');
        $activeWorksheet->setCellValue('B2', 'Periode : ' . $tglMasuk . ' s/d ' . $tglKeluar);
        // Style Judul
        phpspreadsheet::styleFont($spreadsheet, 'B1:B2', true, 11, 'Calibri');

        // Header
        $columnMesin = 'B';
        $columnMesinEnd = 'C';
        $spreadsheet->getActiveSheet()->mergeCells($columnMesin . '3:' . $columnMesinEnd . '3');
        $activeWorksheet->setCellValue('B3', 'Tipe Produk');

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
                max(dep.name) AS department_name,
                max(dep.id) AS department_id,
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
            INNER JOIN msMachine AS mac ON asy.machine_id = mac.id
            INNER JOIN msDepartment AS dep ON mac.department_id = dep.id
            INNER JOIN msProduct AS prd ON asy.product_id = prd.id
            INNER JOIN msProduct_type AS prTip ON prd.product_type_id = prTip.id
            WHERE asy.production_date BETWEEN '$tglMasuk' AND '$tglKeluar'
            GROUP BY dep.id, prTip.id
        ");

        $listDepartment = array_reduce($data, function ($carry, $item) {
            $carry[$item->department_id] = [
                'department_id' => $item->department_id,
                'department_name' => $item->department_name
            ];
            return $carry;
        }, []);

        // list jenis produk
        $listProductType = array_reduce($data, function ($carry, $item) {
            $carry[$item->department_id][$item->product_type_code] = $item->product_type_name;
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
        $columnTypeCode = 'B';
        $columnTypeName = 'C';
        $startRowItem = 4;
        $rowItem = $startRowItem;
        // daftar departemen
        foreach ($listDepartment as $department) {
            // Menulis data departemen
            $activeWorksheet->setCellValue($startColumnItem . $rowItem, $department['department_name']);
            $spreadsheet->getActiveSheet()->mergeCells($startColumnItem . $rowItem . ':' . $endColumnItem . $rowItem);
            phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowItem, true, 9, 'Calibri');
            $rowItem++;
            // daftar mesin
            foreach ($listProductType[$department['department_id']] as $typeCode => $typeName) {
                $columnItem = $startColumnItemData;

                // Menulis data mesin
                $spreadsheet->getActiveSheet()->setCellValue($columnTypeCode . $rowItem, $typeCode);
                $spreadsheet->getActiveSheet()->setCellValue($columnTypeName . $rowItem, $typeName);
                // phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);

                // memasukkan data
                $dataItem = $dataFilter[$typeCode] ?? (object)[
                    'berat_standard' => 0,
                    'berat_produksi' => 0,
                    'infure_cost' => 0,
                    'infure_berat_loss' => 0,
                    'panjang_produksi' => 0,
                    'panjang_printing_inline' => 0,
                    'infure_cost_printing' => 0,
                ];
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_standard);
                phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_produksi);
                phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_standard > 0 ? $dataItem->berat_produksi / $dataItem->berat_standard : 0);
                phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->infure_cost);
                phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->infure_berat_loss);
                phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_produksi > 0 ? $dataItem->infure_berat_loss / $dataItem->berat_produksi : 0);
                phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->panjang_produksi);
                phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->panjang_printing_inline);
                phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->infure_cost_printing);
                phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->infure_cost + $dataItem->infure_cost_printing);
                phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);
                $columnItem++;

                phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem, false, 8, 'Calibri');
                $rowItem++;
            }
            // perhitungan jumlah berdasarkan departemen
            $spreadsheet->getActiveSheet()->mergeCells($columnTypeCode . $rowItem . ':' . $columnTypeName . $rowItem);
            $activeWorksheet->setCellValue($columnTypeCode . $rowItem, 'Sub Total');
            $columnItem = $startColumnItemData;
            $columnItemEnd = chr(ord($columnItem) + count($header) - 1);
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listProductType[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listProductType[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listProductType[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')/COUNTIF(' . $columnItem . ($rowItem - count($listProductType[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ', "<>0")');
            phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listProductType[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listProductType[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listProductType[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')/COUNTIF(' . $columnItem . ($rowItem - count($listProductType[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ', "<>0")');
            phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listProductType[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listProductType[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listProductType[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listProductType[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            phpspreadsheet::addFullBorder($spreadsheet, $columnTypeCode . $rowItem . ':' . $columnItem . $rowItem);
            phpspreadsheet::styleFont($spreadsheet, $columnTypeCode . $rowItem . ':' . $columnItem . $rowItem, true, 8, 'Calibri');
            $columnItem++;

            $rowItem++;
            $rowItem++;
        }

        // Grand total
        $rowGrandTotal = $rowItem;
        $spreadsheet->getActiveSheet()->mergeCells($columnTypeCode . $rowGrandTotal . ':' . $columnTypeName . $rowGrandTotal);
        $spreadsheet->getActiveSheet()->setCellValue($columnTypeCode . $rowGrandTotal, 'GRAND TOTAL');
        phpspreadsheet::styleFont($spreadsheet, $columnTypeCode . $rowGrandTotal . ':' . $columnHeaderEnd . $rowGrandTotal, true, 8, 'Calibri');
        // $this->addFullBorder($spreadsheet, $columnTypeCode . $rowGrandTotal . ':' . $columnValueAvg . $rowGrandTotal);

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
        phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_produksi']);
        phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_standard'] > 0 ? $grandTotal['berat_produksi'] / $grandTotal['berat_standard'] : 0);
        phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['infure_cost']);
        phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['infure_berat_loss']);
        phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_produksi'] > 0 ? $grandTotal['infure_berat_loss'] / $grandTotal['berat_produksi'] : 0);
        phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['panjang_produksi']);
        phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['panjang_printing_inline']);
        phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['infure_cost_printing']);
        phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['infure_cost'] + $grandTotal['infure_cost_printing']);
        phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        phpspreadsheet::addFullBorder($spreadsheet, $columnTypeCode . $rowGrandTotal . ':' . $columnItem . $rowGrandTotal);
        $columnItem++;

        $activeWorksheet->getStyle($columnMesin . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart)->getAlignment()->setWrapText(true);

        // size auto
        $endColumnItem++;
        while ($startColumnItemData !== $endColumnItem) {
            $spreadsheet->getActiveSheet()->getColumnDimension($startColumnItemData)->setAutoSize(true);
            $startColumnItemData++;
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'asset/report/' . $this->nipon . '-' . $this->jenisreport . '.xlsx';
        $writer->save($filename);
        return $filename;
    }

    public function daftarProduksiPerDepartemenPerTypeSeitai($tglMasuk, $tglKeluar)
    {
        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();
        $activeWorksheet->setShowGridlines(false);

        // Judul
        $activeWorksheet->setCellValue('B1', 'DAFTAR PRODUKSI PER DEPARTEMEN PER TIPE SEITAI');
        $activeWorksheet->setCellValue('B2', 'Periode : ' . $tglMasuk . ' s/d ' . $tglKeluar);
        // Style Judul
        phpspreadsheet::styleFont($spreadsheet, 'B1:B2', true, 11, 'Calibri');

        // Header
        $columnMesin = 'B';
        $columnMesinEnd = 'C';
        $spreadsheet->getActiveSheet()->mergeCells($columnMesin . '3:' . $columnMesinEnd . '3');
        $activeWorksheet->setCellValue('B3', 'Tipe Produk');

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
                MAX(dep.name) AS department_name,
                MAX(dep.id) AS department_id,
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
            INNER JOIN msMachine AS mac ON good.machine_id = mac.id
            INNER JOIN msDepartment AS dep ON mac.department_id = dep.id
            INNER JOIN msProduct AS prd ON good.product_id = prd.id
            INNER JOIN msProduct_type AS prT ON prd.product_type_id = prT.id
            WHERE good.production_date BETWEEN '$tglMasuk' AND '$tglKeluar'
            GROUP BY dep.id, prT.id
        ");

        $listDepartment = array_reduce($data, function ($carry, $item) {
            $carry[$item->department_id] = [
                'department_id' => $item->department_id,
                'department_name' => $item->department_name
            ];
            return $carry;
        }, []);

        // list jenis produk
        $listProductType = array_reduce($data, function ($carry, $item) {
            $carry[$item->department_id][$item->product_type_code] = $item->product_type_name;
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
        $columnTypeCode = 'B';
        $columnTypeName = 'C';
        $startRowItem = 4;
        $rowItem = $startRowItem;
        // daftar departemen
        foreach ($listDepartment as $department) {
            // Menulis data departemen
            $activeWorksheet->setCellValue($startColumnItem . $rowItem, $department['department_name']);
            $spreadsheet->getActiveSheet()->mergeCells($startColumnItem . $rowItem . ':' . $endColumnItem . $rowItem);
            phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowItem, true, 9, 'Calibri');
            $rowItem++;
            // daftar mesin
            foreach ($listProductType[$department['department_id']] as $TypeCode => $TypeName) {
                $columnItem = $startColumnItemData;

                // Menulis data mesin
                $spreadsheet->getActiveSheet()->setCellValue($columnTypeCode . $rowItem, $TypeCode);
                $spreadsheet->getActiveSheet()->setCellValue($columnTypeName . $rowItem, $TypeName);
                // phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);

                // memasukkan data
                $dataItem = $dataFilter[$TypeCode] ?? (object)[
                    'qty_produksi' => 0,
                    'berat_produksi' => 0,
                    'seitai_berat_loss' => 0,
                    'seitai_cost' => 0,
                    'seitai_berat_loss_ponsu' => 0,
                    'infure_berat_loss' => 0,
                ];
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
                phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);
                $columnItem++;

                phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem, false, 8, 'Calibri');
                $rowItem++;
            }
            // perhitungan jumlah berdasarkan departemen
            $spreadsheet->getActiveSheet()->mergeCells($columnTypeCode . $rowItem . ':' . $columnTypeName . $rowItem);
            $activeWorksheet->setCellValue($columnTypeCode . $rowItem, 'Sub Total');
            $columnItem = $startColumnItemData;
            $columnItemEnd = chr(ord($columnItem) + count($header) - 1);
            // jumlah produksi
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listProductType[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpspreadsheet::numberFormatThousands($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // berat_produksi
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listProductType[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // seitai_berat_loss
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listProductType[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // seitai loss %
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listProductType[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')/COUNTIF(' . $columnItem . ($rowItem - count($listProductType[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ', "<>0")');
            phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // seitai_cost
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listProductType[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // seitai_berat_loss_ponsu
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listProductType[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // infure_berat_loss
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listProductType[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            phpspreadsheet::addFullBorder($spreadsheet, $columnTypeCode . $rowItem . ':' . $columnItem . $rowItem);
            phpspreadsheet::styleFont($spreadsheet, $columnTypeCode . $rowItem . ':' . $columnItem . $rowItem, true, 8, 'Calibri');
            $columnItem++;


            $rowItem++;
            $rowItem++;
        }

        // Grand total
        $rowGrandTotal = $rowItem;
        $spreadsheet->getActiveSheet()->mergeCells($columnTypeCode . $rowGrandTotal . ':' . $columnTypeName . $rowGrandTotal);
        $spreadsheet->getActiveSheet()->setCellValue($columnTypeCode . $rowGrandTotal, 'GRAND TOTAL');
        phpspreadsheet::styleFont($spreadsheet, $columnTypeCode . $rowGrandTotal . ':' . $columnHeaderEnd . $rowGrandTotal, true, 8, 'Calibri');
        // $this->addFullBorder($spreadsheet, $columnTypeCode . $rowGrandTotal . ':' . $columnValueAvg . $rowGrandTotal);

        $grandTotal = array_reduce(array_keys($dataFilter), function ($carry, $item) use ($dataFilter) {
            $dataItem = $dataFilter[$item] ?? (object)[
                'qty_produksi' => 0,
                'berat_produksi' => 0,
                'seitai_berat_loss' => 0,
                'seitai_cost' => 0,
                'seitai_berat_loss_ponsu' => 0,
                'infure_berat_loss' => 0,
            ];
            $carry['qty_produksi'] += $dataItem->qty_produksi;
            $carry['berat_produksi'] += $dataItem->berat_produksi;
            $carry['seitai_berat_loss'] += $dataItem->seitai_berat_loss;
            $carry['seitai_cost'] += $dataItem->seitai_cost;
            $carry['seitai_berat_loss_ponsu'] += $dataItem->seitai_berat_loss_ponsu;
            $carry['infure_berat_loss'] += $dataItem->infure_berat_loss;
            return $carry;
        }, [
            'qty_produksi' => 0,
            'berat_produksi' => 0,
            'seitai_berat_loss' => 0,
            'seitai_cost' => 0,
            'seitai_berat_loss_ponsu' => 0,
            'infure_berat_loss' => 0,
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
        phpspreadsheet::addFullBorder($spreadsheet, $columnTypeCode . $rowGrandTotal . ':' . $columnItem . $rowGrandTotal);
        $columnItem++;

        $activeWorksheet->getStyle($columnMesin . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart)->getAlignment()->setWrapText(true);

        // size auto
        $endColumnItem++;
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        while ($startColumnItemData !== $endColumnItem) {
            $spreadsheet->getActiveSheet()->getColumnDimension($startColumnItemData)->setAutoSize(true);
            $startColumnItemData++;
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'asset/report/' . $this->nipon . '-' . $this->jenisreport . '.xlsx';
        $writer->save($filename);
        return $filename;
    }

    public function daftarProduksiPerDepartemenPerPetugasInfure($tglMasuk, $tglKeluar)
    {
        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();
        $activeWorksheet->setShowGridlines(false);

        // Judul
        $activeWorksheet->setCellValue('B1', 'DAFTAR PRODUKSI PER DEPARTEMEN PER PETUGAS INFURE');
        $activeWorksheet->setCellValue('B2', 'Periode : ' . $tglMasuk . ' s/d ' . $tglKeluar);
        // Style Judul
        phpspreadsheet::styleFont($spreadsheet, 'B1:B2', true, 11, 'Calibri');

        // Header
        $columnMesin = 'B';
        $columnMesinEnd = 'C';
        $spreadsheet->getActiveSheet()->mergeCells($columnMesin . '3:' . $columnMesinEnd . '3');
        $activeWorksheet->setCellValue('B3', 'Petugas');

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
                max(dep.name) AS department_name,
                max(dep.id) AS department_id,
                max(man.lossCode) AS employeeNo,
                max(man.empName) AS empName,
                SUM(asy.berat_standard) AS berat_standard,
                SUM(asy.berat_produksi) AS berat_produksi,
                SUM(asy.infure_cost) AS infure_cost,
                SUM(asy.infure_berat_loss) AS infure_berat_loss,
                SUM(asy.panjang_produksi) AS panjang_produksi,
                SUM(asy.panjang_printing_inline) AS panjang_printing_inline,
                SUM(asy.infure_cost_printing) AS infure_cost_printing
            FROM tdProduct_Assembly AS asy
            INNER JOIN msEmployee AS man ON asy.employee_id = man.id
            INNER JOIN msDepartment AS dep ON man.department_id = dep.id
            WHERE asy.production_date BETWEEN '$tglMasuk' AND '$tglKeluar'
            GROUP BY dep.id, asy.employee_id
        ");

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
            $carry[$item->employeeno] = $item;
            return $carry;
        }, []);

        // index
        $startColumnItem = 'B';
        $endColumnItem = $columnHeaderEnd;
        $startColumnItemData = 'D';
        $columnemployeeno = 'B';
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
            // daftar mesin
            foreach ($listEmployee[$department['department_id']] as $employeeno => $EmployeeName) {
                $columnItem = $startColumnItemData;

                // Menulis data mesin
                $spreadsheet->getActiveSheet()->setCellValue($columnemployeeno . $rowItem, $employeeno);
                $spreadsheet->getActiveSheet()->setCellValue($columnEmployeeName . $rowItem, $EmployeeName);
                // phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);

                // memasukkan data
                $dataItem = $dataFilter[$employeeno] ?? (object)[
                    'berat_standard' => 0,
                    'berat_produksi' => 0,
                    'infure_cost' => 0,
                    'infure_berat_loss' => 0,
                    'panjang_produksi' => 0,
                    'panjang_printing_inline' => 0,
                    'infure_cost_printing' => 0,
                ];
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_standard);
                phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_produksi);
                phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_standard > 0 ? $dataItem->berat_produksi / $dataItem->berat_standard : 0);
                phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->infure_cost);
                phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->infure_berat_loss);
                phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_produksi > 0 ? $dataItem->infure_berat_loss / $dataItem->berat_produksi : 0);
                phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->panjang_produksi);
                phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->panjang_printing_inline);
                phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->infure_cost_printing);
                phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->infure_cost + $dataItem->infure_cost_printing);
                phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);
                $columnItem++;

                phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem, false, 8, 'Calibri');
                $rowItem++;
            }
            // perhitungan jumlah berdasarkan departemen
            $spreadsheet->getActiveSheet()->mergeCells($columnemployeeno . $rowItem . ':' . $columnEmployeeName . $rowItem);
            $activeWorksheet->setCellValue($columnemployeeno . $rowItem, 'Sub Total');
            $columnItem = $startColumnItemData;
            $columnItemEnd = chr(ord($columnItem) + count($header) - 1);
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listEmployee[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listEmployee[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listEmployee[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')/COUNTIF(' . $columnItem . ($rowItem - count($listEmployee[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ', "<>0")');
            phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listEmployee[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listEmployee[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listEmployee[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')/COUNTIF(' . $columnItem . ($rowItem - count($listEmployee[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ', "<>0")');
            phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listEmployee[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listEmployee[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listEmployee[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listEmployee[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            phpspreadsheet::addFullBorder($spreadsheet, $columnemployeeno . $rowItem . ':' . $columnItem . $rowItem);
            phpspreadsheet::styleFont($spreadsheet, $columnemployeeno . $rowItem . ':' . $columnItem . $rowItem, true, 8, 'Calibri');
            $columnItem++;

            $rowItem++;
            $rowItem++;
        }

        // Grand total
        $rowGrandTotal = $rowItem;
        $spreadsheet->getActiveSheet()->mergeCells($columnemployeeno . $rowGrandTotal . ':' . $columnEmployeeName . $rowGrandTotal);
        $spreadsheet->getActiveSheet()->setCellValue($columnemployeeno . $rowGrandTotal, 'GRAND TOTAL');
        phpspreadsheet::styleFont($spreadsheet, $columnemployeeno . $rowGrandTotal . ':' . $columnHeaderEnd . $rowGrandTotal, true, 8, 'Calibri');
        // $this->addFullBorder($spreadsheet, $columnemployeeno . $rowGrandTotal . ':' . $columnValueAvg . $rowGrandTotal);

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
        phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_produksi']);
        phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_standard'] > 0 ? $grandTotal['berat_produksi'] / $grandTotal['berat_standard'] : 0);
        phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['infure_cost']);
        phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['infure_berat_loss']);
        phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_produksi'] > 0 ? $grandTotal['infure_berat_loss'] / $grandTotal['berat_produksi'] : 0);
        phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['panjang_produksi']);
        phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['panjang_printing_inline']);
        phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['infure_cost_printing']);
        phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['infure_cost'] + $grandTotal['infure_cost_printing']);
        phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        phpspreadsheet::addFullBorder($spreadsheet, $columnemployeeno . $rowGrandTotal . ':' . $columnItem . $rowGrandTotal);
        $columnItem++;

        $activeWorksheet->getStyle($columnMesin . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart)->getAlignment()->setWrapText(true);

        // size auto
        $endColumnItem++;
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        while ($startColumnItemData !== $endColumnItem) {
            $spreadsheet->getActiveSheet()->getColumnDimension($startColumnItemData)->setAutoSize(true);
            $startColumnItemData++;
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'asset/report/' . $this->nipon . '-' . $this->jenisreport . '.xlsx';
        $writer->save($filename);
        return $filename;
    }

    public function daftarProduksiPerDepartemenPerPetugasSeitai($tglMasuk, $tglKeluar)
    {
        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();
        $activeWorksheet->setShowGridlines(false);

        // Judul
        $activeWorksheet->setCellValue('B1', 'DAFTAR PRODUKSI PER DEPARTEMEN PER PETUGAS SEITAI');
        $activeWorksheet->setCellValue('B2', 'Periode : ' . $tglMasuk . ' s/d ' . $tglKeluar);
        // Style Judul
        phpspreadsheet::styleFont($spreadsheet, 'B1:B2', true, 11, 'Calibri');

        // Header
        $columnMesin = 'B';
        $columnMesinEnd = 'C';
        $spreadsheet->getActiveSheet()->mergeCells($columnMesin . '3:' . $columnMesinEnd . '3');
        $activeWorksheet->setCellValue('B3', 'Petugas');

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
                MAX(dep.name) AS department_name,
                MAX(dep.id) AS department_id,
                MAX(man.employeeNo) AS employeeNo,
                MAX(man.empName) AS empName,
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
            INNER JOIN msEmployee AS man ON good.employee_id = man.id
            INNER JOIN msDepartment AS dep ON man.department_id = dep.id
            INNER JOIN msProduct AS prd ON good.product_id = prd.id
            INNER JOIN msProduct_type AS prT ON prd.product_type_id = prT.id
            WHERE good.production_date BETWEEN '$tglMasuk' AND '$tglKeluar'
            GROUP BY dep.id, good.employee_id
        ");

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
            $carry[$item->employeeno] = $item;
            return $carry;
        }, []);

        // index
        $startColumnItem = 'B';
        $endColumnItem = $columnHeaderEnd;
        $startColumnItemData = 'D';
        $columnEmployeeNo = 'B';
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
            // daftar mesin
            foreach ($listEmployee[$department['department_id']] as $employeeNo => $employeeName) {
                $columnItem = $startColumnItemData;

                // Menulis data mesin
                $spreadsheet->getActiveSheet()->setCellValue($columnEmployeeNo . $rowItem, $employeeNo);
                $spreadsheet->getActiveSheet()->setCellValue($columnEmployeeName . $rowItem, $employeeName);
                // phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);

                // memasukkan data
                $dataItem = $dataFilter[$employeeNo] ?? (object)[
                    'qty_produksi' => 0,
                    'berat_produksi' => 0,
                    'seitai_berat_loss' => 0,
                    'seitai_cost' => 0,
                    'seitai_berat_loss_ponsu' => 0,
                    'infure_berat_loss' => 0,
                ];
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
                phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);
                $columnItem++;

                phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem, false, 8, 'Calibri');
                $rowItem++;
            }
            // perhitungan jumlah berdasarkan departemen
            $spreadsheet->getActiveSheet()->mergeCells($columnEmployeeNo . $rowItem . ':' . $columnEmployeeName . $rowItem);
            $activeWorksheet->setCellValue($columnEmployeeNo . $rowItem, 'Sub Total');
            $columnItem = $startColumnItemData;
            $columnItemEnd = chr(ord($columnItem) + count($header) - 1);
            // jumlah produksi
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listEmployee[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpspreadsheet::numberFormatThousands($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // berat_produksi
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listEmployee[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // seitai_berat_loss
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listEmployee[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // seitai loss %
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listEmployee[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')/COUNTIF(' . $columnItem . ($rowItem - count($listEmployee[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ', "<>0")');
            phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // seitai_cost
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listEmployee[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // seitai_berat_loss_ponsu
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listEmployee[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // infure_berat_loss
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listEmployee[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            phpspreadsheet::addFullBorder($spreadsheet, $columnEmployeeNo . $rowItem . ':' . $columnItem . $rowItem);
            phpspreadsheet::styleFont($spreadsheet, $columnEmployeeNo . $rowItem . ':' . $columnItem . $rowItem, true, 8, 'Calibri');
            $columnItem++;


            $rowItem++;
            $rowItem++;
        }

        // Grand total
        $rowGrandTotal = $rowItem;
        $spreadsheet->getActiveSheet()->mergeCells($columnEmployeeNo . $rowGrandTotal . ':' . $columnEmployeeName . $rowGrandTotal);
        $spreadsheet->getActiveSheet()->setCellValue($columnEmployeeNo . $rowGrandTotal, 'GRAND TOTAL');
        phpspreadsheet::styleFont($spreadsheet, $columnEmployeeNo . $rowGrandTotal . ':' . $columnHeaderEnd . $rowGrandTotal, true, 8, 'Calibri');
        // $this->addFullBorder($spreadsheet, $columnEmployeeNo . $rowGrandTotal . ':' . $columnValueAvg . $rowGrandTotal);

        $grandTotal = array_reduce(array_keys($dataFilter), function ($carry, $item) use ($dataFilter) {
            $dataItem = $dataFilter[$item] ?? (object)[
                'qty_produksi' => 0,
                'berat_produksi' => 0,
                'seitai_berat_loss' => 0,
                'seitai_cost' => 0,
                'seitai_berat_loss_ponsu' => 0,
                'infure_berat_loss' => 0,
            ];
            $carry['qty_produksi'] += $dataItem->qty_produksi;
            $carry['berat_produksi'] += $dataItem->berat_produksi;
            $carry['seitai_berat_loss'] += $dataItem->seitai_berat_loss;
            $carry['seitai_cost'] += $dataItem->seitai_cost;
            $carry['seitai_berat_loss_ponsu'] += $dataItem->seitai_berat_loss_ponsu;
            $carry['infure_berat_loss'] += $dataItem->infure_berat_loss;
            return $carry;
        }, [
            'qty_produksi' => 0,
            'berat_produksi' => 0,
            'seitai_berat_loss' => 0,
            'seitai_cost' => 0,
            'seitai_berat_loss_ponsu' => 0,
            'infure_berat_loss' => 0,
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
        phpspreadsheet::addFullBorder($spreadsheet, $columnEmployeeNo . $rowGrandTotal . ':' . $columnItem . $rowGrandTotal);
        $columnItem++;

        $activeWorksheet->getStyle($columnMesin . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart)->getAlignment()->setWrapText(true);

        // size auto
        $endColumnItem++;
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        while ($startColumnItemData !== $endColumnItem) {
            $spreadsheet->getActiveSheet()->getColumnDimension($startColumnItemData)->setAutoSize(true);
            $startColumnItemData++;
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'asset/report/' . $this->nipon . '-' . $this->jenisreport . '.xlsx';
        $writer->save($filename);
        return $filename;
    }

    public function daftarLossPerDepartemenInfure($tglMasuk, $tglKeluar)
    {
        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();
        $activeWorksheet->setShowGridlines(false);

        // Judul
        $activeWorksheet->setCellValue('B1', 'DAFTAR LOSS PER DEPARTEMEN INFURE');
        $activeWorksheet->setCellValue('B2', 'Periode : ' . $tglMasuk . ' s/d ' . $tglKeluar);
        // Style Judul
        phpspreadsheet::styleFont($spreadsheet, 'B1:B2', true, 11, 'Calibri');

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
                max(dep.id) AS department_id,
                max(dep.name) AS department_name,
                max(mslosCls.name) AS loss_class_name,
                max(mslos.code) AS loss_code,
                max(mslos.name) AS loss_name,
                SUM(CASE WHEN mslos.loss_category_code <> '1' THEN det.berat_loss ELSE 0 END) AS berat_loss_produksi,
                SUM(CASE WHEN mslos.loss_category_code = '1' THEN det.berat_loss ELSE 0 END) AS berat_loss_kebutuhan
            FROM tdProduct_Assembly AS asy
            INNER JOIN tdProduct_Assembly_Loss AS det ON asy.id = det.product_assembly_id
            INNER JOIN msLossInfure AS mslos ON det.loss_infure_id = mslos.id
            INNER JOIN msLossClass AS mslosCls ON mslos.loss_class_id = mslosCls.id
            INNER JOIN msMachine AS mac ON asy.machine_id = mac.id
            INNER JOIN msDepartment AS dep ON mac.department_id = dep.id
            WHERE asy.production_date BETWEEN '$tglMasuk' AND '$tglKeluar'
            GROUP BY dep.id, det.loss_infure_id
        ");

        $listDepartment = array_reduce($data, function ($carry, $item) {
            $carry[$item->department_id] = [
                'department_id' => $item->department_id,
                'department_name' => $item->department_name
            ];
            return $carry;
        }, []);

        // list klasifikasi
        $listLossClass = array_reduce($data, function ($carry, $item) {
            $carry[$item->department_id][$item->loss_class_name] = $item->loss_class_name;
            return $carry;
        }, []);

        $dataFilter = array_reduce($data, function ($carry, $item) {
            // Periksa apakah department_id sudah ada
            if (!isset($carry[$item->department_id])) {
                $carry[$item->department_id] = [];
            }

            // Periksa apakah loss_class_name sudah ada di department_id tersebut
            if (!isset($carry[$item->department_id][$item->loss_class_name])) {
                $carry[$item->department_id][$item->loss_class_name] = [
                    'loss_class_name' => $item->loss_class_name,
                    'losses' => []  // Buat array untuk menampung beberapa loss_name
                ];
            }

            // Tambahkan loss_name ke dalam array 'losses'
            $carry[$item->department_id][$item->loss_class_name]['losses'][] = [
                'loss_code' => $item->loss_code,
                'loss_name' => $item->loss_name,
                'berat_loss_produksi' => $item->berat_loss_produksi,
                'berat_loss_kebutuhan' => $item->berat_loss_kebutuhan
            ];

            return $carry;
        }, []);


        // index
        $startColumnItem = 'B';
        $endColumnItem = $columnHeaderEnd;
        $startColumnItemData = 'D';
        $columnLossClass = 'B';
        $columnLossClassName = 'C';
        $startRowItem = 5;
        $rowItem = $startRowItem;
        // daftar departemen
        foreach ($listDepartment as $department) {
            // Menulis data departemen
            $activeWorksheet->setCellValue($startColumnItem . $rowItem, $department['department_name']);
            $spreadsheet->getActiveSheet()->mergeCells($startColumnItem . $rowItem . ':' . $endColumnItem . $rowItem);
            phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowItem, true, 9, 'Calibri');
            $rowItem++;
            $startRowItemSum = $rowItem;
            // daftar mesin
            foreach ($listLossClass[$department['department_id']] as $lossClass) {
                // Menulis data mesin
                $spreadsheet->getActiveSheet()->mergeCells($columnLossClass . $rowItem . ':' . $columnLossClassName . $rowItem);
                $spreadsheet->getActiveSheet()->setCellValue($columnLossClass . $rowItem, $lossClass);
                // phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);

                // memasukkan data
                $dataItem = $dataFilter[$department['department_id']][$lossClass] ?? [
                    'loss_class_name' => $lossClass,
                    'losses' => [
                        [
                            'loss_code' => '',
                            'loss_name' => '',
                            'berat_loss_produksi' => 0,
                            'berat_loss_kebutuhan' => 0
                        ]
                    ]
                ];

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
                    // Terapkan custom format untuk mengganti tampilan 0 dengan -
                    phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);
                    $columnItem++;

                    phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem, false, 8, 'Calibri');
                    $rowItem++;
                }
            }
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
        ];

        foreach ($dataFilter as $departmentId => $lossClasses) {
            foreach ($listLossClass[$departmentId] as $lossClass => $lossClassName) {
                if (isset($lossClasses[$lossClass])) {
                    $dataItem = $lossClasses[$lossClass];
                    foreach ($dataItem['losses'] as $item) {
                        $grandTotal['berat_loss_produksi'] += $item['berat_loss_produksi'];
                        $grandTotal['berat_loss_kebutuhan'] += $item['berat_loss_kebutuhan'];
                    }
                } else {
                    // Tambahkan default value jika $lossClass tidak ditemukan
                    $grandTotal['berat_loss_produksi'] += 0;
                    $grandTotal['berat_loss_kebutuhan'] += 0;
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
        $filename = 'asset/report/' . $this->nipon . '-' . $this->jenisreport . '.xlsx';
        $writer->save($filename);
        return $filename;
    }

    public function daftarLossPerDepartemenSeitai($tglMasuk, $tglKeluar)
    {
        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();
        $activeWorksheet->setShowGridlines(false);

        // Judul
        $activeWorksheet->setCellValue('B1', 'DAFTAR LOSS PER DEPARTEMEN SEITAI');
        $activeWorksheet->setCellValue('B2', 'Periode : ' . $tglMasuk . ' s/d ' . $tglKeluar);
        // Style Judul
        phpspreadsheet::styleFont($spreadsheet, 'B1:B2', true, 11, 'Calibri');

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
                max(dep.name) AS department_name,
                max(dep.id) AS department_id,
                max(mslosCls.name) AS loss_class_name,
                max(mslos.code) AS loss_code,
                max(mslos.name) AS loss_name,
                SUM(CASE WHEN mslos.loss_category_code <> '1' THEN det.berat_loss ELSE 0 END) AS berat_loss_produksi,
                SUM(CASE WHEN mslos.loss_category_code = '1' THEN det.berat_loss ELSE 0 END) AS berat_loss_kebutuhan
            FROM tdProduct_Goods AS good
            INNER JOIN tdProduct_Goods_Loss AS det ON good.id = det.product_goods_id
            INNER JOIN msLossSeitai AS mslos ON det.loss_seitai_id = mslos.id
            INNER JOIN msLossClass AS mslosCls ON mslos.loss_class_id = mslosCls.id
            INNER JOIN msMachine AS mac ON good.machine_id = mac.id
            INNER JOIN msDepartment AS dep ON mac.department_id = dep.id
            WHERE good.production_date BETWEEN '$tglMasuk' AND '$tglKeluar'
            GROUP BY dep.id, det.loss_seitai_id
            ORDER BY loss_code ASC
        ");

        $listDepartment = array_reduce($data, function ($carry, $item) {
            $carry[$item->department_id] = [
                'department_id' => $item->department_id,
                'department_name' => $item->department_name
            ];
            return $carry;
        }, []);

        // list klasifikasi
        $listLossClass = array_reduce($data, function ($carry, $item) {
            $carry[$item->department_id][$item->loss_class_name] = $item->loss_class_name;
            return $carry;
        }, []);

        $dataFilter = array_reduce($data, function ($carry, $item) {
            // Periksa apakah department_id sudah ada
            if (!isset($carry[$item->department_id])) {
                $carry[$item->department_id] = [];
            }

            // Periksa apakah loss_class_name sudah ada di department_id tersebut
            if (!isset($carry[$item->department_id][$item->loss_class_name])) {
                $carry[$item->department_id][$item->loss_class_name] = [
                    'loss_class_name' => $item->loss_class_name,
                    'losses' => []  // Buat array untuk menampung beberapa loss_name
                ];
            }

            // Tambahkan loss_name ke dalam array 'losses'
            $carry[$item->department_id][$item->loss_class_name]['losses'][] = [
                'loss_code' => $item->loss_code,
                'loss_name' => $item->loss_name,
                'berat_loss_produksi' => $item->berat_loss_produksi,
                'berat_loss_kebutuhan' => $item->berat_loss_kebutuhan
            ];

            return $carry;
        }, []);


        // index
        $startColumnItem = 'B';
        $endColumnItem = $columnHeaderEnd;
        $startColumnItemData = 'D';
        $columnLossClass = 'B';
        $columnLossClassName = 'C';
        $startRowItem = 5;
        $rowItem = $startRowItem;
        // daftar departemen
        foreach ($listDepartment as $department) {
            // Menulis data departemen
            $activeWorksheet->setCellValue($startColumnItem . $rowItem, $department['department_name']);
            $spreadsheet->getActiveSheet()->mergeCells($startColumnItem . $rowItem . ':' . $endColumnItem . $rowItem);
            phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowItem, true, 9, 'Calibri');
            $rowItem++;
            $startRowItemSum = $rowItem;
            // daftar mesin
            foreach ($listLossClass[$department['department_id']] as $lossClass) {
                // Menulis data mesin
                $spreadsheet->getActiveSheet()->mergeCells($columnLossClass . $rowItem . ':' . $columnLossClassName . $rowItem);
                $spreadsheet->getActiveSheet()->setCellValue($columnLossClass . $rowItem, $lossClass);
                // phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);

                // memasukkan data
                $dataItem = $dataFilter[$department['department_id']][$lossClass] ?? [
                    'loss_class_name' => $lossClass,
                    'losses' => [
                        [
                            'loss_code' => '',
                            'loss_name' => '',
                            'berat_loss_produksi' => 0,
                            'berat_loss_kebutuhan' => 0
                        ]
                    ]
                ];

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
                    // Terapkan custom format untuk mengganti tampilan 0 dengan -
                    phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);
                    $columnItem++;

                    phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem, false, 8, 'Calibri');
                    $rowItem++;
                }
            }
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
        ];

        foreach ($dataFilter as $departmentId => $lossClasses) {
            foreach ($listLossClass[$departmentId] as $lossClass => $lossClassName) {
                if (isset($lossClasses[$lossClass])) {
                    $dataItem = $lossClasses[$lossClass];
                    foreach ($dataItem['losses'] as $item) {
                        $grandTotal['berat_loss_produksi'] += $item['berat_loss_produksi'];
                        $grandTotal['berat_loss_kebutuhan'] += $item['berat_loss_kebutuhan'];
                    }
                } else {
                    // Tambahkan default value jika $lossClass tidak ditemukan
                    $grandTotal['berat_loss_produksi'] += 0;
                    $grandTotal['berat_loss_kebutuhan'] += 0;
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
        $filename = 'asset/report/' . $this->nipon . '-' . $this->jenisreport . '.xlsx';
        $writer->save($filename);
        return $filename;
    }

    public function daftarLossPerDepartemenPerJenisInfure($tglMasuk, $tglKeluar)
    {
        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();
        $activeWorksheet->setShowGridlines(false);

        // Judul
        $activeWorksheet->setCellValue('B1', 'DAFTAR LOSS PER DEPARTEMEN PER JENIS INFURE');
        $activeWorksheet->setCellValue('B2', 'Periode : ' . $tglMasuk . ' s/d ' . $tglKeluar);
        // Style Judul
        phpspreadsheet::styleFont($spreadsheet, 'B1:B2', true, 11, 'Calibri');

        // Header
        $columnFirstHeader = 'B';
        $columnFirstHeaderEnd = 'C';
        $spreadsheet->getActiveSheet()->mergeCells($columnFirstHeader . '3:' . $columnFirstHeaderEnd . '3');
        $activeWorksheet->setCellValue('B3', 'Jenis Produk');

        // header
        $rowHeaderStart = 3;
        $columnHeaderStart = 'D';
        $columnHeaderEnd = 'D';
        $header = [
            'Klasifikasi',
            'Kode Loss',
            'Nama Loss',
            'Loss Produksi (Kg)',
            'Loss Kebutuhan (Kg)',
        ];

        foreach ($header as $key => $value) {
            $activeWorksheet->setCellValue($columnHeaderEnd . $rowHeaderStart, $value);
            $columnHeaderEnd++;
        }
        $columnHeaderEnd = chr(ord($columnHeaderEnd) - 1);

        // style header
        phpspreadsheet::addFullBorder($spreadsheet, $columnFirstHeader . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);
        phpspreadsheet::styleFont($spreadsheet, $columnFirstHeader . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart, true, 9, 'Calibri');
        phpspreadsheet::textAlignCenter($spreadsheet, $columnFirstHeader . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);

        $data = DB::select("
            SELECT
                max(dep.name) AS department_name,
                max(dep.id) AS department_id,
                max(prGrp.code || ' : ' || prGrp.name) AS product_group_name,
                max(mslosCls.name) AS loss_class_name,
                max(mslos.code) AS loss_code,
                max(mslos.name) AS loss_name,
                SUM(CASE WHEN mslos.loss_category_code <> '1' THEN det.berat_loss ELSE 0 END) AS berat_loss_produksi,
                SUM(CASE WHEN mslos.loss_category_code = '1' THEN det.berat_loss ELSE 0 END) AS berat_loss_kebutuhan
            FROM tdProduct_Assembly AS asy
            INNER JOIN tdProduct_Assembly_Loss AS det ON asy.id = det.product_assembly_id
            INNER JOIN msLossInfure AS mslos ON det.loss_infure_id = mslos.id
            INNER JOIN msLossClass AS mslosCls ON mslos.loss_class_id = mslosCls.id
            INNER JOIN msMachine AS mac ON asy.machine_id = mac.id
            INNER JOIN msDepartment AS dep ON mac.department_id = dep.id
            INNER JOIN msProduct AS prd ON asy.product_id = prd.id
            INNER JOIN msProduct_type AS prTip ON prd.product_type_id = prTip.id
            INNER JOIN msProduct_group AS prGrp ON prTip.product_group_id = prGrp.id
            WHERE asy.production_date BETWEEN '$tglMasuk' AND '$tglKeluar'
            GROUP BY dep.id, prGrp.id, det.loss_infure_id
        ");

        $listDepartment = array_reduce($data, function ($carry, $item) {
            $carry[$item->department_id] = [
                'department_id' => $item->department_id,
                'department_name' => $item->department_name
            ];
            return $carry;
        }, []);

        // list jenis produk
        $listProductGroup = array_reduce($data, function ($carry, $item) {
            $carry[$item->department_id][$item->product_group_name] = $item->product_group_name;
            return $carry;
        }, []);

        // list klasifikasi
        $listLossClass = array_reduce($data, function ($carry, $item) {
            $carry[$item->department_id][$item->product_group_name][$item->loss_class_name] = $item->loss_class_name;
            return $carry;
        }, []);

        $dataFilter = array_reduce($data, function ($carry, $item) {
            // Periksa apakah department_id sudah ada
            if (!isset($carry[$item->department_id])) {
                $carry[$item->department_id] = [];
            }

            // Periksa apakah product_group_name sudah ada di department_id tersebut
            if (!isset($carry[$item->department_id][$item->product_group_name])) {
                $carry[$item->department_id][$item->product_group_name] = [];
            }

            // Periksa apakah loss_class_name sudah ada di product_group_name tersebut
            if (!isset($carry[$item->department_id][$item->product_group_name][$item->loss_class_name])) {
                $carry[$item->department_id][$item->product_group_name][$item->loss_class_name] = [
                    'loss_class_name' => $item->loss_class_name,
                    'losses' => []  // Buat array untuk menampung beberapa loss_name
                ];
            }

            // Tambahkan loss_name ke dalam array 'losses'
            $carry[$item->department_id][$item->product_group_name][$item->loss_class_name]['losses'][] = [
                'loss_code' => $item->loss_code,
                'loss_name' => $item->loss_name,
                'berat_loss_produksi' => $item->berat_loss_produksi,
                'berat_loss_kebutuhan' => $item->berat_loss_kebutuhan
            ];

            return $carry;
        }, []);

        // index
        $startColumnItem = 'B';
        $endColumnItem = $columnHeaderEnd;
        $startColumnItemData = 'E';
        $columnProductGroup = 'B';
        $columnProductGroupEnd = 'C';
        $columnLossClass = 'D';
        $startRowItem = 5;
        $rowItem = $startRowItem;
        // daftar departemen
        foreach ($listDepartment as $department) {
            // Menulis data departemen
            $activeWorksheet->setCellValue($startColumnItem . $rowItem, $department['department_name']);
            $spreadsheet->getActiveSheet()->mergeCells($startColumnItem . $rowItem . ':' . $endColumnItem . $rowItem);
            phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowItem, true, 9, 'Calibri');
            $rowItem++;
            $startRowItemSum = $rowItem;
            foreach ($listProductGroup[$department['department_id']] as $productGroup) {
                // Menulis data tipe produk
                $activeWorksheet->setCellValue($columnProductGroup . $rowItem, $productGroup);
                $spreadsheet->getActiveSheet()->mergeCells($columnProductGroup . $rowItem . ':' . $columnProductGroupEnd . $rowItem);
                // phpspreadsheet::styleFont($spreadsheet, $columnProductGroup . $rowItem, true, 9, 'Calibri');
                // $rowItem++;
                // daftar loss class
                foreach ($listLossClass[$department['department_id']][$productGroup] as $lossClass) {
                    // Menulis data loss class
                    $spreadsheet->getActiveSheet()->setCellValue($columnLossClass . $rowItem, $lossClass);
                    // phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);

                    // memasukkan data
                    $dataItem = $dataFilter[$department['department_id']][$productGroup][$lossClass] ?? [
                        'loss_class_name' => $lossClass,
                        'losses' => [
                            [
                                'loss_code' => '',
                                'loss_name' => '',
                                'berat_loss_produksi' => 0,
                                'berat_loss_kebutuhan' => 0
                            ]
                        ]
                    ];

                    foreach ($dataItem['losses'] as $item) {
                        $spreadsheet->getActiveSheet()->mergeCells($columnProductGroup . $rowItem . ':' . $columnProductGroupEnd . $rowItem);
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
                        // Terapkan custom format untuk mengganti tampilan 0 dengan -
                        phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);
                        $columnItem++;

                        phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem, false, 8, 'Calibri');
                        $rowItem++;
                    }
                }
            }
            // perhitungan jumlah berdasarkan departemen
            $spreadsheet->getActiveSheet()->mergeCells($startColumnItem . $rowItem . ':' . 'F' . $rowItem);
            $activeWorksheet->setCellValue($startColumnItem . $rowItem, 'Total');
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
            phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);
            phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem, true, 8, 'Calibri');
            $columnItem++;

            $rowItem++;
            $rowItem++;
        }

        // Grand total
        $rowGrandTotal = $rowItem;
        $spreadsheet->getActiveSheet()->mergeCells($startColumnItem . $rowGrandTotal . ':' . 'E' . $rowGrandTotal);
        $spreadsheet->getActiveSheet()->setCellValue($startColumnItem . $rowGrandTotal, 'GRAND TOTAL');
        phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowGrandTotal . ':' . $columnHeaderEnd . $rowGrandTotal, true, 8, 'Calibri');
        // $this->addFullBorder($spreadsheet, $startColumnItem . $rowGrandTotal . ':' . $columnValueAvg . $rowGrandTotal);

        $grandTotal = [
            'berat_loss_produksi' => 0,
            'berat_loss_kebutuhan' => 0,
        ];

        foreach ($dataFilter as $departmentId => $lossClasses) {
            foreach ($listProductGroup[$departmentId] as $productGroup) {
                foreach ($listLossClass[$departmentId][$productGroup] as $lossClass => $lossClassName) {
                    if (isset($lossClasses[$productGroup])) {
                        $dataItem = $lossClasses[$productGroup][$lossClass];
                        foreach ($dataItem['losses'] as $item) {
                            $grandTotal['berat_loss_produksi'] += $item['berat_loss_produksi'];
                            $grandTotal['berat_loss_kebutuhan'] += $item['berat_loss_kebutuhan'];
                        }
                    } else {
                        // Tambahkan default value jika $lossClass tidak ditemukan
                        $grandTotal['berat_loss_produksi'] += 0;
                        $grandTotal['berat_loss_kebutuhan'] += 0;
                    }
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
        phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowGrandTotal . ':' . $columnItem . $rowGrandTotal);
        $columnItem++;

        $activeWorksheet->getStyle($columnFirstHeader . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart)->getAlignment()->setWrapText(true);

        // size auto
        $endColumnItem++;
        while ($startColumnItemData !== $endColumnItem) {
            $spreadsheet->getActiveSheet()->getColumnDimension($startColumnItemData)->setAutoSize(true);
            $startColumnItemData++;
        }

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
