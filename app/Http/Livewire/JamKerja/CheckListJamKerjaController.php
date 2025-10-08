<?php

namespace App\Http\Livewire\JamKerja;

use App\Helpers\departmentHelper;
use Carbon\Carbon;
use Livewire\Component;
use App\Helpers\phpspreadsheet;
use App\Helpers\formatTime;
use App\Http\Livewire\MasterTabel\WorkingShift;
use App\Models\MsDepartment;
use App\Models\MsMachine;
use App\Models\MsWorkingShift;
use App\Models\TdJamKerjaMesin;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class CheckListJamKerjaController extends Component
{
    public $tglAwal;
    public $tglAkhir;
    public $jamAwal;
    public $jamAkhir;
    public $workingShiftHour;
    public $machine;
    public $machineInfure;
    public $machineSeitai;
    public $noprosesawal;
    public $noprosesakhir;
    public $lpk_no;
    public $nomorOrder;
    public $divisions;
    public $divisionId = 2; // default INFURE
    public $departments;
    public $infureDepartments;
    public $seitaiDepartments;
    public $departmentId;
    public $jenisReport = "Checklist";
    public $machineId;
    public $nomorHan;
    public $transaksi = 1;
    public $products;
    public $productId;
    public $status;
    public $searchTerm;

    public function mount()
    {
        $this->tglAwal = Carbon::now()->format('Y-m-d');
        $this->tglAkhir = Carbon::now()->format('Y-m-d');
        $this->workingShiftHour = MsWorkingShift::select('id', 'work_hour_from', 'work_hour_till')->active()->orderBy('work_hour_from', 'ASC')->get();
        $this->jamAwal = $this->workingShiftHour[0]->work_hour_from;
        $this->jamAkhir = $this->workingShiftHour[count($this->workingShiftHour) - 1]->work_hour_till;
        $this->machineInfure = MsMachine::infureDepartment()->orderBy('machineno')->get();
        $this->machineSeitai = MsMachine::seitaiDepartment()->orderBy('machineno')->get();
        $this->machine = $this->machineInfure;
        $this->divisions = MsDepartment::division()->get();
        $this->infureDepartments = departmentHelper::infurePabrikDepartment();
        $this->departments = $this->infureDepartments;
        $this->seitaiDepartments = departmentHelper::seitaiPabrikDepartment();
    }

    public function updatedDivisionId($value)
    {
        if ($value == 2) {
            $this->departments = $this->infureDepartments;
            $this->machine = $this->machineInfure;
        } else if ($value == 7) {
            $this->departments = $this->seitaiDepartments;
            $this->machine = $this->machineSeitai;
        }
    }

    public function export()
    {
        $rules = [
            'tglAwal' => 'required',
            'tglAkhir' => 'required',
            'jamAwal' => 'required',
            'jamAkhir' => 'required',
        ];

        $messages = [
            'tglAwal.required' => 'Tanggal Awal tidak boleh kosong',
            'tglAkhir.required' => 'Tanggal Akhir tidak boleh kosong',
            'jamAwal.required' => 'Jam Awal tidak boleh kosong',
            'jamAkhir.required' => 'Jam Akhir tidak boleh kosong',
        ];

        $validate = Validator::make([
            'tglAwal' => $this->tglAwal,
            'tglAkhir' => $this->tglAkhir,
            'jamAwal' => $this->jamAwal,
            'jamAkhir' => $this->jamAkhir,
        ], $rules, $messages);

        if ($validate->fails()) {
            $this->dispatch('notification', ['type' => 'warning', 'message' => $validate->errors()->first()]);
            return;
        }

        if ($this->tglAwal > $this->tglAkhir) {
            $this->dispatch('notification', ['type' => 'warning', 'message' => 'Tanggal akhir tidak boleh kurang dari tanggal awal']);
            return;
        }

        // pengecekan inputan jam awal dan jam akhir
        if (is_array($this->jamAwal)) {
            $this->jamAwal = $this->jamAwal['value'];
        } else {
            $this->jamAwal = $this->jamAwal;
        }

        if (is_array($this->jamAkhir)) {
            $this->jamAkhir = $this->jamAkhir['value'];
        } else {
            $this->jamAkhir = $this->jamAkhir;
        }

        $tglAwal = Carbon::parse($this->tglAwal . ' ' . $this->jamAwal);
        $tglAkhir = Carbon::parse($this->tglAkhir . ' ' . $this->jamAkhir);

        $filter = [
            'machine_id' => $this->machineId['value'] ?? null,
            'transaksi' => $this->transaksi ?? 1,
            'department_id' => $this->departmentId ?? null,
        ];

        $response = $this->checklistJamKerja($tglAwal, $tglAkhir, $filter, $this->divisionId == 2 ? 'INFURE' : 'SEITAI', true);
        if ($response['status'] == 'success') {
            return response()->download($response['filename'])->deleteFileAfterSend(true);
        } else if ($response['status'] == 'error') {
            $this->dispatch('notification', ['type' => 'warning', 'message' => $response['message']]);
            return;
        }
    }

    public function checklistJamKerja($tglAwal, $tglAkhir, $filter = null, $nippo = 'INFURE', $isChecklist = false)
    {
        ini_set('max_execution_time', '300');
        $spreadsheet = new Spreadsheet();

        // Buat sheet pertama (General)
        $generalWorksheet = $spreadsheet->getActiveSheet();
        $generalWorksheet->setTitle('General');
        $generalWorksheet->setShowGridlines(false);

        // Buat sheet kedua (Detail)
        $detailWorksheet = $spreadsheet->createSheet();
        $detailWorksheet->setTitle('Detail');
        $detailWorksheet->setShowGridlines(false);

        // Set locale agar tanggal indonesia
        Carbon::setLocale('id');

        // Setup General Sheet (Ringkasan)
        $this->setupGeneralSheet($generalWorksheet, $nippo, $filter, $tglAwal, $tglAkhir, $spreadsheet);

        // Setup Detail Sheet (Detail dengan Jam Mati Mesin)
        $this->setupDetailSheet($detailWorksheet, $nippo, $filter, $tglAwal, $tglAkhir, $spreadsheet);

        // Get data
        $data = $this->getData($tglAwal, $tglAkhir, $filter, $nippo, $isChecklist);

        if (count($data) == 0) {
            $response = [
                'status' => 'error',
                'message' => "Data pada periode tanggal tersebut tidak ditemukan"
            ];
            return $response;
        }

        // Fill General Sheet (ringkasan tanpa detail jam mati)
        $this->fillGeneralSheet($generalWorksheet, $data, $spreadsheet);

        // Fill Detail Sheet (dengan detail jam mati mesin)
        $this->fillDetailSheet($detailWorksheet, $data, $spreadsheet);

        // Set active sheet to General
        $spreadsheet->setActiveSheetIndex(0);

        $writer = new Xlsx($spreadsheet);
        $filename = 'asset/report/JamKerja-' . $nippo . '.xlsx';
        $writer->save($filename);
        $response = [
            'status' => 'success',
            'filename' => $filename
        ];
        return $response;
    }

    private function setupGeneralSheet($worksheet, $nippo, $filter, $tglAwal, $tglAkhir, $spreadsheet)
    {
        // Judul
        $worksheet->setCellValue('A1', 'CHECKLIST JAM KERJA ' . strtoupper($nippo) . ' DEPARTMENT :' . (isset($filter['department_id']) ? MsDepartment::find($filter['department_id'])->name : 'ALL'));
        $worksheet->setCellValue('A2', 'Periode: ' . $tglAwal->translatedFormat('d-M-Y H:i') . '  ~  ' . $tglAkhir->translatedFormat('d-M-Y H:i'));

        // Style Judul
        $worksheet->getStyle('A1:A2')->getFont()->setBold(true)->setName('Calibri')->setSize(11);

        // Header General (tanpa detail jam mati mesin)
        $rowHeaderStart = 3;
        $header = [
            'No.',
            'Tanggal',
            'Shift',
            'Nomor Mesin',
            'NIK',
            'Petugas',
            'Jam Kerja',
            'Jam Mati',
            'Jam Jalan',
            '% Jalan Mesin'
        ];

        $columnHeaderEnd = 'A';
        foreach ($header as $value) {
            $worksheet->setCellValue($columnHeaderEnd . $rowHeaderStart, $value);
            $columnHeaderEnd++;
        }
        $columnHeaderEnd = chr(ord($columnHeaderEnd) - 1);

        // Setup halaman
        $this->setupPageSettings($worksheet);

        // Style header
        $worksheet->getStyle('A' . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart)
            ->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $worksheet->getStyle('A' . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart)
            ->getFont()->setBold(true)->setName('Calibri')->setSize(9);
        $worksheet->getStyle('A' . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart)
            ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $worksheet->getStyle('A' . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart)->getAlignment()->setWrapText(true);
        phpspreadsheet::textAlignCenter($spreadsheet, 'A' . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);
    }

    private function setupDetailSheet($worksheet, $nippo, $filter, $tglAwal, $tglAkhir, $spreadsheet)
    {
        // Judul
        $worksheet->setCellValue('A1', 'CHECKLIST JAM KERJA ' . strtoupper($nippo) . ' DEPARTMENT :' . (isset($filter['department_id']) ? MsDepartment::find($filter['department_id'])->name : 'ALL'));
        $worksheet->setCellValue('A2', 'Periode: ' . $tglAwal->translatedFormat('d-M-Y H:i') . '  ~  ' . $tglAkhir->translatedFormat('d-M-Y H:i'));

        // Style Judul
        $worksheet->getStyle('A1:A2')->getFont()->setBold(true)->setName('Calibri')->setSize(11);

        // Header Detail (dengan detail jam mati mesin)
        $rowHeaderStart = 3;
        $header = [
            'No.',
            'Tanggal',
            'Shift',
            'Nomor Mesin',
            'NIK',
            'Petugas',
            'Jam Kerja',
            'Jam Mati',
            'Jam Jalan',
            '% Jalan Mesin',
            'Kode Jam Mati Mesin',
            'Nama Jam Mati Mesin',
            'Jam Mati Mesin',
            'Dari Jam',
            'Sampai Jam'
        ];

        $columnHeaderEnd = 'A';
        foreach ($header as $value) {
            $worksheet->setCellValue($columnHeaderEnd . $rowHeaderStart, $value);
            $columnHeaderEnd++;
        }
        $columnHeaderEnd = chr(ord($columnHeaderEnd) - 1);

        // Setup halaman
        $this->setupPageSettings($worksheet);

        // Style header
        $worksheet->getStyle('A' . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart)
            ->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $worksheet->getStyle('A' . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart)
            ->getFont()->setBold(true)->setName('Calibri')->setSize(9);
        $worksheet->getStyle('A' . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart)
            ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $worksheet->getStyle('A' . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart)->getAlignment()->setWrapText(true);
        phpspreadsheet::textAlignCenter($spreadsheet, 'A' . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);
    }

    private function setupPageSettings($worksheet)
    {
        $worksheet->freezePane('A4');
        $worksheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
        $worksheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT);
        $worksheet->getPageSetup()->setFitToWidth(1);
        $worksheet->getPageSetup()->setFitToHeight(0);
        $worksheet->getPageSetup()->setRowsToRepeatAtTop([1, 3]);
        $worksheet->getPageSetup()->setFitToPage(true);

        // Margins
        $worksheet->getPageMargins()->setTop(1.1 / 2.54);
        $worksheet->getPageMargins()->setBottom(1.0 / 2.54);
        $worksheet->getPageMargins()->setLeft(0.75 / 2.54);
        $worksheet->getPageMargins()->setRight(0.75 / 2.54);
        $worksheet->getPageMargins()->setHeader(0.4 / 2.54);
        $worksheet->getPageMargins()->setFooter(0.5 / 2.54);

        $worksheet->getDefaultRowDimension()->setRowHeight(-1);

        // Header Footer
        $worksheet->getHeaderFooter()->setOddHeader('&L&"Calibri,Bold"&14Fukusuke - Production Control');
        $currentDate = date('d M Y - H:i');
        $footerLeft = '&L&"Calibri"&10Printed: ' . $currentDate . ', by: ' . auth()->user()->username;
        $footerRight = '&R&"Calibri"&10Page: &P of: &N';
        $worksheet->getHeaderFooter()->setOddFooter($footerLeft . $footerRight);
    }

    private function getData($tglAwal, $tglAkhir, $filter, $nippo, $isChecklist)
    {
        if (!$isChecklist) {
            $tglAkhir = Carbon::parse($tglAkhir)->subDay();
        }

        $query = TdJamKerjaMesin::with(['machine' => function ($q) {
            $q->select('id', 'machineno', 'machinename');
        }, 'employee' => function ($q) {
            $q->select('id', 'employeeno', 'empname');
        }, 'workingShift' => function ($q) {
            $q->select('id', 'work_hour_from', 'work_hour_till');
        }, 'jamKerjaJamMatiMesin', 'jamKerjaJamMatiMesin.jamMatiMesin'])
            ->select('id', 'working_date', 'work_shift', 'machine_id', 'employee_id', 'work_hour', 'off_hour', 'on_hour');

        $tableName = (new TdJamKerjaMesin)->getTable();
        if (isset($filter['transaksi']) && $filter['transaksi'] != '') {
            if ($filter['transaksi'] == 1) {
                $query = $query->whereRaw(
                    "({$tableName}.working_date + (select work_hour_from from msworkingshift where id = {$tableName}.work_shift)) BETWEEN ? AND ?",
                    [
                        $tglAwal->toDateTimeString(),
                        $tglAkhir->toDateTimeString()
                    ]
                );
            } elseif ($filter['transaksi'] == 2) {
                $query = $query->whereBetween('created_on', [$tglAwal, $tglAkhir]);
            }
        }

        if ($nippo == 'INFURE') {
            $query = $query->infureDepartment();
        } elseif ($nippo == 'SEITAI') {
            $query = $query->seitaiDepartment();
        }

        if (isset($filter['department_id']) && $filter['department_id'] != '') {
            $query = $query->whereHas('machine', function ($q) use ($filter) {
                $q->where('department_id', $filter['department_id']);
            });
        }

        if (isset($filter['machine_id']) && $filter['machine_id'] != '') {
            $query = $query->where('machine_id', $filter['machine_id']);
        }
        if (isset($filter['work_shift']) && $filter['work_shift'] != '') {
            $query = $query->where('work_shift', $filter['work_shift']);
        }
        if (isset($filter['searchTerm']) && $filter['searchTerm'] != '') {
            $query = $query->where(function ($query) use ($filter) {
                $query->whereHas('machine', function ($q) use ($filter) {
                    $q->where('machineno', 'ilike', '%' . $filter['searchTerm'] . '%')
                        ->orWhere('machinename', 'ilike', '%' . $filter['searchTerm'] . '%');
                });
            });
        }

        return $query
            ->orderBy('working_date', 'asc')
            ->orderBy('machine_id', 'asc')
            ->orderBy('work_shift', 'asc')
            ->get();
    }

    private function fillGeneralSheet($worksheet, $data, $spreadsheet)
    {
        $rowItemStart = 4;
        $rowItem = $rowItemStart;

        $totalJamKerja = 0;
        $totalJamMati = 0;
        $totalJamJalan = 0;

        foreach ($data as $key => $dataItem) {
            $columnItemEnd = 'A';

            // No
            $worksheet->setCellValue($columnItemEnd . $rowItem, $key + 1);
            $columnItemEnd++;
            // Tanggal
            $worksheet->setCellValue($columnItemEnd . $rowItem, Carbon::parse($dataItem['working_date'])->translatedFormat('d-M-Y'));
            $columnItemEnd++;
            // Shift
            $worksheet->setCellValue($columnItemEnd . $rowItem, $dataItem['work_shift']);
            $columnItemEnd++;
            // Nomor mesin
            $worksheet->setCellValue($columnItemEnd . $rowItem, $dataItem['machine']['machineno'] ?? '');
            $columnItemEnd++;
            // NIK
            $worksheet->setCellValue($columnItemEnd . $rowItem, $dataItem['employee']['employeeno'] ?? '');
            $columnItemEnd++;
            // Nama petugas
            $worksheet->setCellValue($columnItemEnd . $rowItem, $dataItem['employee']['empname'] ?? '');
            $columnItemEnd++;
            // Jam kerja
            $worksheet->setCellValue($columnItemEnd . $rowItem, Carbon::parse($dataItem['work_hour'])->translatedFormat('H:i'));
            $totalJamKerja += formatTime::timeToMinutes($dataItem['work_hour']);
            $columnItemEnd++;
            // Jam mati
            $worksheet->setCellValue($columnItemEnd . $rowItem, Carbon::parse($dataItem['off_hour'])->translatedFormat('H:i'));
            $totalJamMati += formatTime::timeToMinutes($dataItem['off_hour']);
            $columnItemEnd++;
            // Jam jalan
            $worksheet->setCellValue($columnItemEnd . $rowItem, Carbon::parse($dataItem['on_hour'])->translatedFormat('H:i'));
            $totalJamJalan += formatTime::timeToMinutes($dataItem['on_hour']);
            $columnItemEnd++;
            // % Jalan mesin
            $percentage = $dataItem['on_hour'] && $dataItem['work_hour'] && formatTime::timeToMinutes($dataItem['work_hour']) > 0
                ? (formatTime::timeToMinutes($dataItem['on_hour']) / formatTime::timeToMinutes($dataItem['work_hour']))
                : 0;
            $worksheet->setCellValue($columnItemEnd . $rowItem, $percentage);
            $worksheet->getStyle($columnItemEnd . $rowItem)->getNumberFormat()->setFormatCode('0.00%');

            $rowItem++;
        }

        // Style data
        $worksheet->getStyle('A' . $rowItemStart . ':E' . $rowItem)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $worksheet->getStyle('G' . $rowItemStart . ':J' . $rowItem)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $worksheet->getStyle('A' . $rowItemStart . ':J' . $rowItem)->getFont()->setName('Calibri')->setSize(8);

        // style header
        phpspreadsheet::addFullBorder($spreadsheet, 'A' . $rowItemStart . ':J' . $rowItem);
        phpspreadsheet::styleFont($spreadsheet, 'A' . $rowItemStart . ':J' . $rowItem, false, 8, 'Calibri');
        phpspreadsheet::textAlignCenter($spreadsheet, 'A' . $rowItemStart . ':J' . $rowItem);

        // Total
        $this->addGeneralTotal($worksheet, $rowItem, $totalJamKerja, $totalJamMati, $totalJamJalan);

        // Auto size columns
        $worksheet->getColumnDimension('E')->setAutoSize(true);
        $worksheet->getColumnDimension('F')->setAutoSize(true);
    }

    private function fillDetailSheet($worksheet, $data, $spreadsheet)
    {
        $rowItemStart = 4;
        $rowItem = $rowItemStart;

        $totalJamKerja = 0;
        $totalJamMati = 0;
        $totalJamJalan = 0;

        foreach ($data as $key => $dataItem) {
            $rowDataStart = $rowItem;
            $columnItemEnd = 'A';

            // No
            $worksheet->setCellValue($columnItemEnd . $rowItem, $key + 1);
            $columnItemEnd++;
            // Tanggal
            $worksheet->setCellValue($columnItemEnd . $rowItem, Carbon::parse($dataItem['working_date'])->translatedFormat('d-M-Y'));
            $columnItemEnd++;
            // Shift
            $worksheet->setCellValue($columnItemEnd . $rowItem, $dataItem['work_shift']);
            $columnItemEnd++;
            // Nomor mesin
            $worksheet->setCellValue($columnItemEnd . $rowItem, $dataItem['machine']['machineno'] ?? '');
            $columnItemEnd++;
            // NIK
            $worksheet->setCellValue($columnItemEnd . $rowItem, $dataItem['employee']['employeeno'] ?? '');
            $columnItemEnd++;
            // Nama petugas
            $worksheet->setCellValue($columnItemEnd . $rowItem, $dataItem['employee']['empname'] ?? '');
            $columnItemEnd++;
            // Jam kerja
            $worksheet->setCellValue($columnItemEnd . $rowItem, Carbon::parse($dataItem['work_hour'])->translatedFormat('H:i'));
            $totalJamKerja += formatTime::timeToMinutes($dataItem['work_hour']);
            $columnItemEnd++;
            // Jam mati
            $worksheet->setCellValue($columnItemEnd . $rowItem, Carbon::parse($dataItem['off_hour'])->translatedFormat('H:i'));
            $totalJamMati += formatTime::timeToMinutes($dataItem['off_hour']);
            $columnItemEnd++;
            // Jam jalan
            $worksheet->setCellValue($columnItemEnd . $rowItem, Carbon::parse($dataItem['on_hour'])->translatedFormat('H:i'));
            $totalJamJalan += formatTime::timeToMinutes($dataItem['on_hour']);
            $columnItemEnd++;
            // % Jalan mesin
            $percentage = $dataItem['on_hour'] && $dataItem['work_hour'] && formatTime::timeToMinutes($dataItem['work_hour']) > 0
                ? (formatTime::timeToMinutes($dataItem['on_hour']) / formatTime::timeToMinutes($dataItem['work_hour']))
                : 0;
            $worksheet->setCellValue($columnItemEnd . $rowItem, $percentage);
            $worksheet->getStyle($columnItemEnd . $rowItem)->getNumberFormat()->setFormatCode('0.00%');
            $columnItemEnd++;
            phpspreadsheet::addFullBorder($spreadsheet, 'A' . $rowItem . ':O' . $rowItem);

            // Detail jam mati mesin
            $columnDetailStart = $columnItemEnd;
            if (isset($dataItem['jamKerjaJamMatiMesin']) && count($dataItem['jamKerjaJamMatiMesin']) > 0) {
                foreach ($dataItem['jamKerjaJamMatiMesin'] as $detail) {
                    $columnItemEnd = $columnDetailStart;
                    // Kode Jam Mati Mesin
                    $worksheet->setCellValue($columnItemEnd . $rowItem, $detail->jamMatiMesin->code ?? '');
                    $columnItemEnd++;
                    // Nama Jam Mati Mesin
                    $worksheet->setCellValue($columnItemEnd . $rowItem, $detail->jamMatiMesin->name ?? '');
                    $columnItemEnd++;
                    // Jam Mati Mesin
                    $worksheet->setCellValue($columnItemEnd . $rowItem, Carbon::parse($detail->off_hour)->translatedFormat('H:i'));
                    $columnItemEnd++;
                    // Dari Jam
                    $worksheet->setCellValue($columnItemEnd . $rowItem, Carbon::parse($detail->from)->translatedFormat('H:i'));
                    $columnItemEnd++;
                    // Sampai Jam
                    $worksheet->setCellValue($columnItemEnd . $rowItem, Carbon::parse($detail->to)->translatedFormat('H:i'));
                    $columnItemEnd++;
                    phpspreadsheet::addFullBorder($spreadsheet, 'K' . $rowItem . ':O' . $rowItem);
                    $rowItem++;
                }
            } else {
                $rowItem++;
            }
        }
        phpspreadsheet::styleFont($spreadsheet, 'A' . $rowItem . ':O' . $rowItem, false, 8, 'Calibri');
        phpspreadsheet::textAlignCenter($spreadsheet, 'A' . $rowItem . ':O' . $rowItem);

        // Style data
        $worksheet->getStyle('A' . $rowItemStart . ':E' . $rowItem)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $worksheet->getStyle('G' . $rowItemStart . ':I' . $rowItem)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $worksheet->getStyle('K' . $rowItemStart . ':K' . $rowItem)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $worksheet->getStyle('A' . $rowItemStart . ':O' . $rowItem)->getFont()->setName('Calibri')->setSize(8);
        // Total
        $this->addDetailTotal($worksheet, $rowItem, $totalJamKerja, $totalJamMati, $totalJamJalan);

        // Auto size columns
        $worksheet->getColumnDimension('E')->setAutoSize(true);
        $worksheet->getColumnDimension('F')->setAutoSize(true);
        $worksheet->getColumnDimension('L')->setAutoSize(true);
    }

    private function addGeneralTotal($worksheet, $rowItem, $totalJamKerja, $totalJamMati, $totalJamJalan)
    {
        // Merge cells for TOTAL
        $worksheet->mergeCells('A' . $rowItem . ':F' . ($rowItem + 1));
        $worksheet->setCellValue('A' . $rowItem, 'TOTAL');
        $worksheet->getStyle('A' . $rowItem)->getFont()->setBold(true)->setName('Calibri')->setSize(8);

        // Total jam kerja
        $worksheet->setCellValue('G' . $rowItem, formatTime::minutesToTime($totalJamKerja));
        $worksheet->mergeCells('G' . $rowItem . ':G' . ($rowItem + 1));

        // Total jam mati
        $worksheet->setCellValue('H' . $rowItem, formatTime::minutesToTime($totalJamMati));
        $percentageJamMati = ($totalJamMati / ($totalJamKerja ?: 1));
        $worksheet->setCellValue('H' . ($rowItem + 1), $percentageJamMati);
        $worksheet->getStyle('H' . ($rowItem + 1))->getNumberFormat()->setFormatCode('0.00%');

        // Total jam jalan
        $worksheet->setCellValue('I' . $rowItem, formatTime::minutesToTime($totalJamJalan));
        $percentageJamJalan = ($totalJamJalan / ($totalJamKerja ?: 1));
        $worksheet->setCellValue('I' . ($rowItem + 1), $percentageJamJalan);
        $worksheet->getStyle('I' . ($rowItem + 1))->getNumberFormat()->setFormatCode('0.00%');

        // % Total
        $worksheet->setCellValue('J' . $rowItem, ($totalJamJalan / ($totalJamKerja ?: 1)));
        $worksheet->setCellValue('J' . ($rowItem + 1), ($totalJamJalan / ($totalJamKerja ?: 1)));
        $worksheet->getStyle('J' . $rowItem . ':J' . ($rowItem + 1))->getNumberFormat()->setFormatCode('0.00%');

        // Style total
        $worksheet->getStyle('A' . $rowItem . ':J' . ($rowItem + 1))->getFont()->setBold(true)->setName('Calibri')->setSize(8);
        $worksheet->getStyle('A' . $rowItem . ':J' . ($rowItem + 1))
            ->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $worksheet->getStyle('A' . $rowItem . ':J' . ($rowItem + 1))
            ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    }

    private function addDetailTotal($worksheet, $rowItem, $totalJamKerja, $totalJamMati, $totalJamJalan)
    {
        // Merge cells for TOTAL
        $worksheet->mergeCells('A' . $rowItem . ':F' . ($rowItem + 1));
        $worksheet->setCellValue('A' . $rowItem, 'TOTAL');
        $worksheet->getStyle('A' . $rowItem)->getFont()->setBold(true)->setName('Calibri')->setSize(8);

        // Total jam kerja
        $worksheet->setCellValue('G' . $rowItem, formatTime::minutesToTime($totalJamKerja));
        $worksheet->mergeCells('G' . $rowItem . ':G' . ($rowItem + 1));

        // Total jam mati
        $worksheet->setCellValue('H' . $rowItem, formatTime::minutesToTime($totalJamMati));
        $percentageJamMati = ($totalJamMati / ($totalJamKerja ?: 1));
        $worksheet->setCellValue('H' . ($rowItem + 1), $percentageJamMati);
        $worksheet->getStyle('H' . ($rowItem + 1))->getNumberFormat()->setFormatCode('0.00%');

        // Total jam jalan
        $worksheet->setCellValue('I' . $rowItem, formatTime::minutesToTime($totalJamJalan));
        $percentageJamJalan = ($totalJamJalan / ($totalJamKerja ?: 1));
        $worksheet->setCellValue('I' . ($rowItem + 1), $percentageJamJalan);
        $worksheet->getStyle('I' . ($rowItem + 1))->getNumberFormat()->setFormatCode('0.00%');

        // Style total
        $worksheet->getStyle('A' . $rowItem . ':O' . ($rowItem + 1))->getFont()->setBold(true)->setName('Calibri')->setSize(8);
        $worksheet->getStyle('A' . $rowItem . ':O' . ($rowItem + 1))
            ->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $worksheet->getStyle('A' . $rowItem . ':O' . ($rowItem + 1))
            ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    }

    public function render()
    {
        return view('livewire.jam-kerja.check-list')->extends('layouts.master');
    }
}
