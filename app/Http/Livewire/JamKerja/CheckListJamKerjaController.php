<?php

namespace App\Http\Livewire\JamKerja;

use Carbon\Carbon;
use Livewire\Component;
use App\Models\MsMachine;
use App\Models\MsDepartment;
use App\Models\MsWorkingShift;
use App\Helpers\phpspreadsheet;
use App\Exports\LossInfureExport;
use App\Exports\NippoInfureExport;
use App\Helpers\formatTime;
use App\Models\MsProduct;
use App\Models\TdJamKerjaMesin;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PHPUnit\TextUI\Configuration\Php;

class CheckListJamKerjaController extends Component
{
    public $tglAwal;
    public $tglAkhir;
    public $jamAwal;
    public $jamAkhir;
    public $workingShiftHour;
    public $machine;
    public $noprosesawal;
    public $noprosesakhir;
    public $lpk_no;
    public $nomorOrder;
    public $department;
    public $jenisReport = "Checklist";
    public $departmentId;
    public $machineId;
    public $nomorHan;
    public $transaksi = 1;
    public $products;
    public $productId;
    public $status;
    public $searchTerm;

    public function checklistJamKerja($tglAwal, $tglAkhir, $filter = null, $nippo = 'INFURE')
    {
        ini_set('max_execution_time', '300');
        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();
        $activeWorksheet->setShowGridlines(false);

        // Set locale agar tanggal indonesia
        Carbon::setLocale('id');

        // Judul
        $activeWorksheet->setCellValue('A1', 'CHECKLIST JAM KERJA ' . strtoupper($nippo));
        $activeWorksheet->setCellValue('A2', 'Periode: ' . $tglAwal->translatedFormat('d-M-Y H:i') . '  ~  ' . $tglAkhir->translatedFormat('d-M-Y H:i'));
        // Style Judul
        phpspreadsheet::styleFont($spreadsheet, 'A1:A2', true, 11, 'Calibri');

        // header
        $rowHeaderStart = 3;
        $columnHeaderStart = 'A';
        $columnHeaderEnd = 'A';

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
        ];

        foreach ($header as $key => $value) {
            $activeWorksheet->setCellValue($columnHeaderEnd . $rowHeaderStart, $value);
            $columnHeaderEnd++;
        }

        /**
         * Mengatur halaman
         */
        $activeWorksheet->freezePane('A4');
        // Mengatur ukuran kertas menjadi A4
        $activeWorksheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
        // Mengatur orientasi menjadi landscape
        $activeWorksheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT);
        $activeWorksheet->getStyle($columnHeaderStart . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart)->getAlignment()->setWrapText(true);
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

        $columnHeaderEnd = chr(ord($columnHeaderEnd) - 1);

        // style header
        phpspreadsheet::addFullBorder($spreadsheet, $columnHeaderStart . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);
        phpspreadsheet::styleFont($spreadsheet, $columnHeaderStart . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart, true, 9, 'Calibri');
        phpspreadsheet::textAlignCenter($spreadsheet, $columnHeaderStart . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);


        $query = TdJamKerjaMesin::with(['machine' => function ($q) {
            $q->select('id', 'machineno', 'machinename');
        }, 'employee' => function ($q) {
            $q->select('id', 'employeeno', 'empname');
        }])
            ->select('id', 'working_date', 'work_shift', 'machine_id', 'employee_id', 'work_hour', 'off_hour', 'on_hour');

        if (isset($filter['transaksi']) && $filter['transaksi'] != '') {
            if ($filter['transaksi'] == 1) {
                $query = $query->whereBetween('working_date', [$tglAwal, Carbon::parse($tglAkhir)->subDay()]);
            } elseif ($filter['transaksi'] == 2) {
                $query = $query->whereBetween('created_on', [$tglAwal, $tglAkhir]);
            }
        }

        if ($nippo == 'INFURE') {
            $query = $query->infureDepartment();
        } elseif ($nippo == 'SEITAI') {
            $query = $query->seitaiDepartment();
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

        $data = $query
            ->orderBy('working_date', 'asc')
            ->orderBy('machine_id', 'asc')
            ->orderBy('work_shift', 'asc')
            ->get();

        if (count($data) == 0) {
            $response = [
                'status' => 'error',
                'message' => "Data pada periode tanggal tersebut tidak ditemukan"
            ];

            return $response;
        }


        $rowItemStart = 4;
        $columnItemStart = 'A';
        $rowItem = $rowItemStart;

        $columnNIK = 'E';
        $columnJamKerja = 'G';
        $columnJamJalan = 'I';

        // inisialisasi total
        $totalJamKerja = 0;
        $totalJamMati = 0;
        $totalJamJalan = 0;

        foreach ($data as $key => $dataItem) {
            $columnItemEnd = $columnItemStart;

            // No
            $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $key + 1);
            $columnItemEnd++;

            // tanggal
            $activeWorksheet->setCellValue($columnItemEnd . $rowItem, Carbon::parse($dataItem['working_date'])->translatedFormat('d-M-Y'));
            $columnItemEnd++;

            // shift
            $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $dataItem['work_shift']);
            $columnItemEnd++;

            // nomor mesin
            $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $dataItem['machine']['machineno'] ?? '');
            $columnItemEnd++;

            // nik
            $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $dataItem['employee']['employeeno'] ?? '');
            $columnItemEnd++;

            // nama petugas
            $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $dataItem['employee']['empname'] ?? '');
            $columnItemEnd++;

            // jam kerja
            $activeWorksheet->setCellValue($columnItemEnd . $rowItem, Carbon::parse($dataItem['work_hour'])->translatedFormat('H:i'));
            $totalJamKerja += formatTime::timeToMinutes($dataItem['work_hour']);
            $columnItemEnd++;

            // jam mati
            $activeWorksheet->setCellValue($columnItemEnd . $rowItem, Carbon::parse($dataItem['off_hour'])->translatedFormat('H:i'));
            $totalJamMati += formatTime::timeToMinutes($dataItem['off_hour']);
            $columnItemEnd++;

            // jam jalan
            $activeWorksheet->setCellValue($columnItemEnd . $rowItem, Carbon::parse($dataItem['on_hour'])->translatedFormat('H:i'));
            $totalJamJalan += formatTime::timeToMinutes($dataItem['on_hour']);
            $columnItemEnd++;

            $columnItemEnd++;
            $rowItem++;
        }

        phpspreadsheet::textAlignCenter($spreadsheet, $columnItemStart . $rowItemStart . ':' . $columnNIK . $rowItem);
        phpSpreadsheet::textAlignCenter($spreadsheet, $columnJamKerja . $rowItemStart . ':' . $columnJamJalan . $rowItem);
        phpspreadsheet::styleFont($spreadsheet, $columnItemStart . $rowItemStart . ':' . $columnItemEnd . $rowItem, false, 8, 'Calibri');

        // grand total
        $columnItemEnd = 'F';
        $spreadsheet->getActiveSheet()->mergeCells($columnItemStart . $rowItem . ':' . $columnItemEnd . $rowItem + 1);
        $activeWorksheet->setCellValue($columnItemStart . $rowItem, 'TOTAL');
        phpSpreadsheet::styleFont($spreadsheet, $columnItemStart . $rowItem, true, 8, 'Calibri');
        $columnItemEnd++;

        // total jam kerja
        $activeWorksheet->setCellValue($columnItemEnd . $rowItem, formatTime::minutesToTime($totalJamKerja));
        $spreadsheet->getActiveSheet()->mergeCells($columnItemEnd . $rowItem . ':' . $columnItemEnd . $rowItem + 1);
        $columnItemEnd++;

        // total jam mati
        $activeWorksheet->setCellValue($columnItemEnd . $rowItem, formatTime::minutesToTime($totalJamMati));
        $percentageJamMati = ($totalJamMati / $totalJamKerja);
        $activeWorksheet->setCellValue($columnItemEnd . $rowItem + 1, $percentageJamMati);
        phpspreadsheet::numberPercentageOrZero($spreadsheet, $columnItemEnd . $rowItem + 1);
        $columnItemEnd++;

        // total jam jalan
        $activeWorksheet->setCellValue($columnItemEnd . $rowItem, formatTime::minutesToTime($totalJamJalan));
        $percentageJamJalan = ($totalJamJalan / $totalJamKerja);
        $activeWorksheet->setCellValue($columnItemEnd . $rowItem + 1, $percentageJamJalan);
        phpspreadsheet::numberPercentageOrZero($spreadsheet, $columnItemEnd . $rowItem + 1);

        phpspreadsheet::styleFont($spreadsheet, $columnItemStart . $rowItem . ':' . $columnItemEnd . $rowItem + 1, true, 8, 'Calibri');
        phpspreadsheet::addFullBorder($spreadsheet, $columnItemStart . $rowItemStart . ':' . $columnItemEnd . $rowItem + 1);
        phpSpreadsheet::textAlignCenter($spreadsheet, $columnItemStart . $rowItem . ':' . $columnItemEnd . $rowItem + 1);

        // mengatur lebar kolom
        $spreadsheet->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);

        $writer = new Xlsx($spreadsheet);
        $filename = 'asset/report/JamKerja-' . $nippo . '.xlsx';
        $writer->save($filename);
        $response = [
            'status' => 'success',
            'filename' => $filename
        ];
        return $response;
    }
}
