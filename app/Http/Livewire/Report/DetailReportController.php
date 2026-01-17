<?php

namespace App\Http\Livewire\Report;

use Carbon\Carbon;
use Livewire\Component;
use App\Models\MsMachine;
use App\Models\MsDepartment;
use App\Models\MsWorkingShift;
use App\Helpers\phpspreadsheet;
use Illuminate\Support\Facades\DB;
use App\Exports\DetailReportExport;
use App\Exports\InfureReportExport;
use App\Helpers\departmentHelper;
use App\Helpers\MachineHelper;
use App\Http\Livewire\Report\DetailReportInfureController;
use Exception;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PHPUnit\TextUI\Configuration\Merger;
use PHPUnit\TextUI\Configuration\Php;

class DetailReportController extends Component
{
    public $tglAwal;
    public $tglAkhir;
    public $jamAwal;
    public $jamAkhir;
    public $workingShiftHour;
    public $nippo = 'Infure';
    public $lpk_no;
    public $nomorOrder;
    public $department;
    public $departmentId;
    public $machine;
    public $machineId;
    public $nomorHan;
    public $nomorPalet;
    public $nomorLot;

    public function mount()
    {
        $this->tglAwal = Carbon::now()->format('Y-m-d');
        $this->tglAkhir = Carbon::now()->format('Y-m-d');
        $this->workingShiftHour = MsWorkingShift::select('work_hour_from', 'work_hour_till')->active()->orderBy('work_hour_from', 'ASC')->get();
        $this->jamAwal = $this->workingShiftHour[0]->work_hour_from;
        $this->jamAkhir = $this->workingShiftHour[count($this->workingShiftHour) - 1]->work_hour_till;
        $this->department = departmentHelper::infurePabrikDepartment();
        $this->machine = MachineHelper::getInfureMachine();
    }

    public function export()
    {
        $rules = [
            'tglAwal' => 'required',
            'tglAkhir' => 'required',
            'jamAwal' => 'required',
            'jamAkhir' => 'required',
            'nippo' => 'required',
        ];

        $messages = [
            'tglAwal.required' => 'Tanggal Awal tidak boleh kosong',
            'tglAkhir.required' => 'Tanggal Akhir tidak boleh kosong',
            'jamAwal.required' => 'Jam Awal tidak boleh kosong',
            'jamAkhir.required' => 'Jam Akhir tidak boleh kosong',
            'nippo.required' => 'Jenis Report tidak boleh kosong',
        ];

        $validate = Validator::make([
            'tglAwal' => $this->tglAwal,
            'tglAkhir' => $this->tglAkhir,
            'jamAwal' => $this->jamAwal,
            'jamAkhir' => $this->jamAkhir,
            'nippo' => $this->nippo,
        ], $rules, $messages);

        if ($validate->fails()) {
            $this->dispatch('notification', ['type' => 'warning', 'message' => $validate->errors()->first()]);
            return;
        }

        if ($this->tglAwal > $this->tglAkhir) {
            $this->dispatch('notification', ['type' => 'warning', 'message' => 'Tanggal akhir tidak boleh kurang dari tanggal awal']);
            return;
        }

        $tglAwal = Carbon::parse($this->tglAwal . ' ' . $this->jamAwal);
        $tglAkhir = Carbon::parse($this->tglAkhir . ' ' . $this->jamAkhir);

        if ($this->nippo == 'Infure') {
            $report = new DetailReportInfureController();
            try {
                $result = $report->generateReport(
                    $tglAwal,
                    $tglAkhir,
                    [
                        'lpk_no' => $this->lpk_no,
                        'machineId' => $this->machineId,
                        'nippo' => $this->nippo,
                        'nomorOrder' => $this->nomorOrder,
                        'departmentId' => $this->departmentId,
                        'nomorHan' => $this->nomorHan,
                    ]
                );

                return response()->download($result['filename'])->deleteFileAfterSend(true);
            } catch (Exception $e) {
                $this->dispatch('notification', ['type' => 'error', 'message' => 'Terjadi kesalahan saat generate report: ' . $e->getMessage()]);
                return;
            }
        } else if ($this->nippo == 'Seitai') {
            $report = new DetailReportSeitaiController();

            try {
            $result = $report->generateReport(
                $tglAwal,
                $tglAkhir,
                [
                    'lpk_no' => $this->lpk_no,
                    'machineId' => $this->machineId,
                    'nippo' => $this->nippo,
                    'nomorOrder' => $this->nomorOrder,
                    'departmentId' => $this->departmentId,
                    'nomorPalet' => $this->nomorPalet,
                    'nomorLot' => $this->nomorLot,
                ]
            );

            return response()->download($result['filename'])->deleteFileAfterSend(true);
            } catch (Exception $e) {
                $this->dispatch('notification', ['type' => 'error', 'message' => 'Terjadi kesalahan saat generate report: ' . $e->getMessage()]);
                return;
            }
        }
    }

    public function reportInfure($tglAwal, $tglAkhir)
    {
        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();
        $activeWorksheet->setShowGridlines(false);
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

        // Set locale agar tanggal indonesia
        Carbon::setLocale('id');

        // Judul
        $activeWorksheet->setCellValue('A1', 'DETAIL PRODUKSI INFURE');
        $activeWorksheet->setCellValue('A2', 'Periode: ' . $tglAwal->translatedFormat('d-M-Y H:i') . '  ~  ' . $tglAkhir->translatedFormat('d-M-Y H:i') . ' - Mesin: ' . ($this->machineId ? $this->machineId : 'Semua Mesin'));
        // Style Judul
        phpspreadsheet::styleFont($spreadsheet, 'A1:A2', true, 11, 'Calibri');

        // header
        $rowHeaderStart = 3;
        $columnHeaderStart = 'A';
        $columnHeaderEnd = 'A';

        $header = [
            'Tanggal Produksi',
            'Shift',
            'Jam',
            'NIK',
            'Nama Petugas',
            'Dept. Petugas',
            'Mesin',
            'No LPK',
            'Nomor Gentan',
            'Nomor Han',
            'Panjang Produksi (meter)',
            'Berat Produksi (Kg)',
            'Loss',
            'Berat Loss (Kg)',
        ];

        foreach ($header as $key => $value) {
            $activeWorksheet->setCellValue($columnHeaderEnd . $rowHeaderStart, $value);
            $columnHeaderEnd++;
        }

        $activeWorksheet->freezePane('A4');
        $columnHeaderEnd = chr(ord($columnHeaderEnd) - 1);

        // style header
        phpspreadsheet::addFullBorder($spreadsheet, $columnHeaderStart . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);
        phpspreadsheet::styleFont($spreadsheet, $columnHeaderStart . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart, true, 9, 'Calibri');
        phpspreadsheet::textAlignCenter($spreadsheet, $columnHeaderStart . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);

        // Filter Query
        $filterDate = "tdpa.production_date BETWEEN '$tglAwal' AND '$tglAkhir'";
        $filterNoLPK = $this->lpk_no ? " AND (tdol.lpk_no = '$this->lpk_no')" : '';
        $nomorOrder = $this->nomorOrder ? " AND (msp.code = '$this->nomorOrder')" : '';
        $this->departmentId = $this->departmentId ? (is_array($this->departmentId) ? $this->departmentId['value'] : $this->departmentId) : '';
        $filterDepartment = $this->departmentId ? " AND (msd.id = '$this->departmentId')" : '';
        $this->machineId = $this->machineId ? (is_array($this->machineId) ? $this->machineId['value'] : $this->machineId) : '';
        $filterMachine = $this->machineId ? " AND (tdpa.machine_id = '$this->machineId')" : '';
        $filterNomorHan = $this->nomorHan ? " AND (tdpa.nomor_han = '$this->nomorHan')" : '';

        $data = DB::select(
            "
                SELECT
                    tdpa.production_date AS tglproduksi,
                    tdpa.work_shift AS shift,
                    tdpa.work_hour AS jam,
                    tdpa.employee_id AS employee_id,
                    mse.employeeno AS nik,
                    mse.empname AS namapetugas,
                    msd.NAME AS deptpetugas,
                    tdpa.machine_id AS machine_id,
                    msm.machineno AS nomesin,
                    msm.machinename AS namamesin,
                    tdpa.product_id AS product_id,
                    msp.code AS produkcode,
                    msp.NAME AS namaproduk,
                    tdol.lpk_no AS lpk_no,
                    tdpa.nomor_han AS nomor_han,
                    tdpa.gentan_no AS gentan_no,
                    tdpa.panjang_produksi AS panjang_produksi,
                    tdpa.panjang_printing_inline AS panjang_printing_inline,
                    tdpa.berat_produksi AS berat_produksi,
                    msli.code AS losscode,
                    msli.NAME AS lossname,
                    tdpal.berat_loss
                FROM
                    tdProduct_Assembly AS tdpa
                    INNER JOIN tdOrderLpk AS tdol ON tdpa.lpk_id = tdol.id
                    INNER JOIN msEmployee AS mse ON mse.ID = tdpa.employee_id
                    INNER JOIN msMachine AS msm ON msm.ID = tdpa.machine_id
                    INNER JOIN msProduct AS msp ON msp.ID = tdpa.product_id
                    INNER JOIN msDepartment AS msd ON msd.ID = mse.department_id
                    LEFT JOIN tdProduct_Assembly_Loss AS tdpal ON tdpal.product_assembly_id = tdpa.
                    ID LEFT JOIN msLossInfure AS msli ON msli.ID = tdpal.loss_infure_id
                WHERE
                    $filterDate
                    $filterNoLPK
                    $nomorOrder
                    $filterDepartment
                    $filterMachine
                    $filterNomorHan
                ORDER BY tdpa.production_date ASC",
        );

        if (count($data) == 0) {
            $response = [
                'status' => 'error',
                'message' => "Data pada periode tanggal atau pembeli tersebut tidak ditemukan"
            ];

            return $response;
        }

        $listProduct = [];
        $listProductionDate = [];
        $listWorkHour = [];
        $dataFiltered = [];

        // grand total
        $totalPanjangProduksi = 0;
        $totalBeratProduksi = 0;
        $totalLoss = 0;

        foreach ($data as $item) {
            // Menyusun daftar produk
            if (!isset($listProduct[$item->product_id])) {
                $listProduct[$item->product_id] = $item->produkcode . ' - ' . $item->namaproduk;
            }

            // Menyusun daftar tanggal produksi
            if (!isset($listProductionDate[$item->product_id][$item->tglproduksi])) {
                $listProductionDate[$item->product_id][$item->tglproduksi] = $item->tglproduksi;
            }

            // Menyusun daftar jam kerja
            $listWorkHour[$item->product_id][$item->tglproduksi][$item->jam] = [
                'tglproduksi' => $item->tglproduksi,
                'shift' => $item->shift,
                'jam' => $item->jam,
                'nik' => $item->nik,
                'nama_petugas' => $item->namapetugas,
                'dept_petugas' => $item->deptpetugas,
                'nama_mesin' => $item->nomesin . ' - ' . $item->namamesin,
                'lpk_no' => $item->lpk_no,
                'gentan_no' => $item->gentan_no,
                'nomor_han' => $item->nomor_han,
                'panjang_produksi' => $item->panjang_produksi,
                'berat_produksi' => $item->berat_produksi,
                'losscode' => $item->losscode,
                'lossname' => $item->lossname,
            ];

            // Menyusun data terfilter
            $dataFiltered[$item->product_id][$item->tglproduksi][$item->jam][$item->losscode] = (object)[
                'lossname' => $item->lossname,
                'berat_loss' => $item->berat_loss,
            ];

            $totalPanjangProduksi += $item->panjang_produksi;
            $totalBeratProduksi += $item->berat_produksi;
            $totalLoss += $item->berat_loss;
        }

        // index
        $rowItemStart = 4;
        $columnItemStart = 'A';
        $columnLossStart = 'N';
        $rowItem = $rowItemStart;
        foreach ($listProduct as $productId => $productName) {
            // Menulis data produk
            $activeWorksheet->setCellValue($columnItemStart . $rowItem, $productName);
            phpspreadsheet::styleFont($spreadsheet, $columnItemStart . $rowItem, true, 9, 'Calibri');
            $columnItemEnd = $columnItemStart;
            $rowItem++;
            foreach ($listProductionDate[$productId] as $productionDate) {
                foreach ($listWorkHour[$productId][$productionDate] as $WorkHour => $itemWorkHour) {
                    $columnItemEnd = $columnItemStart;
                    // tanggal produksi
                    $activeWorksheet->setCellValue($columnItemEnd . $rowItem, Carbon::parse($itemWorkHour['tglproduksi'])->translatedFormat('d-M-Y'));
                    phpspreadsheet::textAlignCenter($spreadsheet, $columnItemEnd . $rowItem);
                    $columnItemEnd++;
                    // shift
                    $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $itemWorkHour['shift']);
                    phpspreadsheet::textAlignCenter($spreadsheet, $columnItemEnd . $rowItem);
                    $columnItemEnd++;
                    // jam
                    $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $itemWorkHour['jam']);
                    phpspreadsheet::textAlignCenter($spreadsheet, $columnItemEnd . $rowItem);
                    $columnItemEnd++;
                    // nik
                    $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $itemWorkHour['nik']);
                    phpspreadsheet::textAlignCenter($spreadsheet, $columnItemEnd . $rowItem);
                    $columnItemEnd++;
                    // nama petugas
                    $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $itemWorkHour['nama_petugas']);
                    phpspreadsheet::textAlignCenter($spreadsheet, $columnItemEnd . $rowItem);
                    $columnItemEnd++;
                    // dept petugas
                    $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $itemWorkHour['dept_petugas']);
                    phpspreadsheet::textAlignCenter($spreadsheet, $columnItemEnd . $rowItem);
                    $columnItemEnd++;
                    // mesin
                    $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $itemWorkHour['nama_mesin']);
                    $columnItemEnd++;
                    // no lpk
                    $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $itemWorkHour['lpk_no']);
                    phpspreadsheet::textAlignCenter($spreadsheet, $columnItemEnd . $rowItem);
                    $columnItemEnd++;
                    // nomor gentan
                    $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $itemWorkHour['gentan_no']);
                    phpspreadsheet::textAlignCenter($spreadsheet, $columnItemEnd . $rowItem);
                    $columnItemEnd++;
                    // nomor han
                    $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $itemWorkHour['nomor_han']);
                    phpspreadsheet::textAlignCenter($spreadsheet, $columnItemEnd . $rowItem);
                    $columnItemEnd++;
                    // panjang produksi
                    $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $itemWorkHour['panjang_produksi']);
                    phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItemEnd . $rowItem);
                    $columnItemEnd++;
                    // berat produksi
                    $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $itemWorkHour['berat_produksi']);
                    phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItemEnd . $rowItem);
                    $columnItemEnd++;

                    // Loss
                    $dataItem = $dataFiltered[$productId][$productionDate][$WorkHour];
                    foreach ($dataItem as $losscode => $item) {
                        $columnLoss = 'M';
                        if ($losscode == '') {
                            $columnLoss++;
                            phpspreadsheet::addFullBorder($spreadsheet, $columnItemStart . $rowItem . ':' . $columnLoss . $rowItem);
                            break;
                        }

                        $activeWorksheet->setCellValue($columnLoss . $rowItem, $item->lossname);
                        $columnLoss++;
                        $activeWorksheet->setCellValue($columnLoss . $rowItem, $item->berat_loss);
                        phpspreadsheet::addFullBorder($spreadsheet, $columnItemStart . $rowItem . ':' . $columnLoss . $rowItem);
                        $columnLoss++;
                        phpspreadsheet::styleFont($spreadsheet, $columnItemStart . $rowItem . ':' . $columnLoss . $rowItem, false, 8, 'Calibri');
                        $rowItem++;
                        $columnItemEnd = $columnLoss;
                    }

                    $columnItemEnd++;
                    phpspreadsheet::styleFont($spreadsheet, $columnItemStart . $rowItem . ':' . $columnItemEnd . $rowItem, false, 8, 'Calibri');

                    $rowItem++;
                }
            }
            $rowItem++;
        }

        // grand total
        $columnItemEnd = 'J';
        $spreadsheet->getActiveSheet()->mergeCells($columnItemStart . $rowItem . ':' . $columnItemEnd . $rowItem);
        $activeWorksheet->setCellValue($columnItemStart . $rowItem, 'GRAND TOTAL');
        phpSpreadsheet::styleFont($spreadsheet, $columnItemStart . $rowItem, true, 9, 'Calibri');
        $columnItemEnd++;

        // Set Panjang Produksi
        $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $totalPanjangProduksi);
        phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItemEnd . $rowItem);
        $columnItemEnd++;

        // Set Berat Produksi
        $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $totalBeratProduksi);
        phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItemEnd . $rowItem);
        $columnItemEnd++;

        // Loss
        $columnItemEnd = 'N';
        $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $totalLoss);
        phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItemEnd . $rowItem);
        phpspreadsheet::styleFont($spreadsheet, $columnItemStart . $rowItem . ':' . $columnItemEnd . $rowItem, true, 8, 'Calibri');
        phpspreadsheet::addFullBorder($spreadsheet, $columnItemStart . $rowItem . ':' . $columnItemEnd . $rowItem);
        $columnItemEnd++;

        $activeWorksheet->getStyle($columnHeaderStart . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart)->getAlignment()->setWrapText(true);

        // size auto
        $columnSizeStart = $columnItemStart;
        $columnSizeStart++;
        while ($columnSizeStart !== $columnItemEnd) {
            $spreadsheet->getActiveSheet()->getColumnDimension($columnSizeStart)->setAutoSize(true);
            $columnSizeStart++;
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'Detail-Produksi-' . $this->nippo . '.xlsx';
        $writer->save($filename);
        $response = [
            'status' => 'success',
            'filename' => $filename
        ];
        return $response;
    }

    public function reportSeitai($tglAwal, $tglAkhir)
    {
        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();
        $activeWorksheet->setShowGridlines(false);

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

        // Set locale agar tanggal indonesia
        Carbon::setLocale('id');

        // Judul
        $activeWorksheet->setCellValue('A1', 'DETAIL PRODUKSI SEITAI');
        $activeWorksheet->setCellValue('A2', 'Periode: ' . $tglAwal->translatedFormat('d-M-Y H:i') . '  ~  ' . $tglAkhir->translatedFormat('d-M-Y H:i') . ' - Mesin: ' . ($this->machineId ? $this->machineId : 'Semua Mesin'));
        // Style Judul
        phpspreadsheet::styleFont($spreadsheet, 'A1:A2', true, 11, 'Calibri');

        // header
        $rowHeaderStart = 3;
        $columnHeaderStart = 'A';
        $columnHeaderEnd = 'A';

        $header = [
            'Tanggal Produksi',
            'Nama Petugas',
            'Dept. Petugas',
            'Nomor Mesin',
            'No LPK',
            'Shift',
            'Jam',
            'Nomor Palet',
            'Nomor LOT',
            'Quantity (Lembar)',
            'Loss',
            'Berat Loss (Kg)',
            'Nomor Gentan',
            'Panjang (meter)',
            'Tanggal Produksi Infure',
            'Shift',
            'Jam',
            'Nomor Mesin Infure',
            'Nomor Han Infure',
            'Petugas Infure',
            'Dept. Infure',
            'Loss Infure di Seitai',
        ];

        foreach ($header as $key => $value) {
            $activeWorksheet->setCellValue($columnHeaderEnd . $rowHeaderStart, $value);
            $columnHeaderEnd++;
        }
        $activeWorksheet->freezePane('A4');
        $columnHeaderEnd = chr(ord($columnHeaderEnd) - 1);

        // style header
        phpspreadsheet::addFullBorder($spreadsheet, $columnHeaderStart . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);
        phpspreadsheet::styleFont($spreadsheet, $columnHeaderStart . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart, true, 9, 'Calibri');
        phpspreadsheet::textAlignCenter($spreadsheet, $columnHeaderStart . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);

        // Filter Query
        $filterDate = "tdpg.created_on BETWEEN '$tglAwal' AND '$tglAkhir'";
        $filterNoLPK = $this->lpk_no ? " AND (tdol.lpk_no = '$this->lpk_no')" : '';
        $nomorOrder = $this->nomorOrder ? " AND (msp.code = '$this->nomorOrder')" : '';
        $this->departmentId = $this->departmentId ? (is_array($this->departmentId) ? $this->departmentId['value'] : $this->departmentId) : '';
        $filterDepartment = $this->departmentId ? " AND (msd.department_id = '$this->departmentId')" : '';
        $this->machineId = $this->machineId ? (is_array($this->machineId) ? $this->machineId['value'] : $this->machineId) : '';
        $filterMachine = $this->machineId ? " AND (tdpg.machine_id = '$this->machineId')" : '';
        $filterNomorPalet = $this->nomorPalet ? " AND (tdpg.nomor_palet = '$this->nomorPalet')" : '';
        $filterNomorLot = $this->nomorLot ? " AND (tdpg.nomor_lot = '$this->nomorLot')" : '';

        // Filter Query

        $data = DB::select(
            "
            WITH goodasy AS (
                SELECT
                    tpga.product_goods_id,
                    tdpa.gentan_no || '-' || tpga.gentan_line AS gentannomorline,
                    tdpa.gentan_no AS gentannomor,
                    tdpa.panjang_produksi,
                    tdpa.production_date AS tglproduksi,
                    tdpa.work_shift,
                    tdpa.work_hour,
                    msm.machineno AS nomesin,
                    tdpa.nomor_han,
                    mse.employeeno AS nik,
                    mse.empname AS namapetugas,
                    msd.NAME AS deptpetugas,
                    tdpg.infure_berat_loss AS infure_berat_loss
                FROM
                    tdproduct_goods_assembly AS tpga
                    INNER JOIN tdproduct_assembly AS tdpa ON tdpa.ID = tpga.product_assembly_id
                    INNER JOIN tdproduct_goods AS tdpg ON tdpg.ID = tpga.product_goods_id
                    INNER JOIN msmachine AS msm ON msm.ID = tdpa.machine_id
                    INNER JOIN msemployee AS mse ON mse.ID = tdpa.employee_id
                    INNER JOIN msDepartment AS msd ON msd.ID = mse.department_id
                ),
                lossgoods AS (
                SELECT
                    tpgl.product_goods_id,
                    msls.code,
                    msls.NAME AS namaloss,
                    tpgl.berat_loss
                FROM
                    tdproduct_goods_loss AS tpgl
                    INNER JOIN mslossseitai AS msls ON msls.ID = tpgl.loss_seitai_id
                ) SELECT
                tdpg.production_no AS production_no,
                tdpg.production_date AS production_date,
                msp.code AS produk_code,
                msp.NAME AS namaproduk,
                tdpg.employee_id AS employee_id,
                mse.employeeno AS nik,
                mse.empname AS namapetugas,
                msd.NAME AS deptpetugas,
                tdpg.work_shift AS work_shift,
                tdpg.work_hour AS work_hour,
                tdpg.machine_id AS machine_id,
                msm.machineno AS nomesin,
                msm.machinename AS namamesin,
                tdol.lpk_no,
                tdpg.nomor_palet AS nomor_palet,
                tdpg.nomor_lot AS nomor_lot,
                tdpg.qty_produksi AS qty_produksi,
                lossgoods.code as loss_code_loss,
                lossgoods.namaloss AS loss_name_loss,
                lossgoods.berat_loss AS berat_loss_loss,
                goodasy.gentannomorline AS gentan_no_line_asy,
                goodasy.gentannomor AS gentan_no_asy,
                goodasy.panjang_produksi AS panjang_produksi_asy,
                goodasy.tglproduksi AS tgl_produksi_asy,
                goodasy.work_shift AS work_shift_asy,
                goodasy.work_hour AS work_hour_asy,
                goodasy.nomesin AS no_mesin_asy,
                goodasy.nomor_han AS nomor_han_asy,
                goodasy.nik AS nik_asy,
                goodasy.namapetugas AS nama_petugas_asy,
                goodasy.deptpetugas AS dept_petugas_asy,
                goodasy.infure_berat_loss AS infure_berat_loss
            FROM
                tdProduct_Goods AS tdpg
                LEFT JOIN goodasy ON tdpg.ID = goodasy.product_goods_id
                LEFT JOIN lossgoods ON tdpg.ID = lossgoods.product_goods_id
                INNER JOIN tdOrderLpk AS tdol ON tdpg.lpk_id = tdol.
                ID INNER JOIN msmachine AS msm ON msm.ID = tdpg.machine_id
                INNER JOIN msemployee AS mse ON mse.ID = tdpg.employee_id
                INNER JOIN msDepartment AS msd ON msd.ID = mse.department_id
                INNER JOIN msProduct AS msp ON msp.ID = tdpg.product_id
            WHERE
                $filterDate
                $filterNoLPK
                $nomorOrder
                $filterDepartment
                $filterMachine
                $filterNomorPalet
                $filterNomorLot
            ",
        );

        if (count($data) == 0) {
            $response = [
                'status' => 'error',
                'message' => "Data pada periode tanggal atau pembeli tersebut tidak ditemukan"
            ];

            return $response;
        }

        $listProduct = array_reduce($data, function ($carry, $item) {
            $carry[$item->produk_code] = $item->produk_code . ' - ' . $item->namaproduk;
            return $carry;
        }, []);

        $listProductionDate = array_reduce($data, function ($carry, $item) {
            $carry[$item->produk_code][$item->production_date] = $item->production_date;
            return $carry;
        }, []);

        $dataFilter = array_reduce($data, function ($carry, $item) {
            $carry[$item->produk_code][$item->production_date] = [
                'production_date' => $item->production_date,
                'namapetugas' => $item->namapetugas,
                'deptpetugas' => $item->deptpetugas,
                'nomesin' => $item->nomesin . ' - ' . $item->namamesin,
                'lpk_no' => $item->lpk_no,
                'work_shift' => $item->work_shift,
                'work_hour' => $item->work_hour,
                'nomor_palet' => $item->nomor_palet,
                'nomor_lot' => $item->nomor_lot,
                'qty_produksi' => $item->qty_produksi,
            ];
            return $carry;
        }, []);

        $dataLoss = array_reduce($data, function ($carry, $item) {
            $carry[$item->produk_code][$item->production_date][$item->loss_code_loss] = (object)[
                'loss_name_loss' => $item->loss_name_loss,
                'berat_loss_loss' => $item->berat_loss_loss,
            ];
            return $carry;
        }, []);

        $dataGentan = array_reduce($data, function ($carry, $item) {
            $carry[$item->produk_code][$item->production_date][$item->gentan_no_asy] = [
                'gentan_no_line_asy' => $item->gentan_no_line_asy,
                'panjang_produksi_asy' => $item->panjang_produksi_asy,
                'tgl_produksi_asy' => $item->tgl_produksi_asy,
                'work_shift_asy' => $item->work_shift_asy,
                'work_hour_asy' => $item->work_hour_asy,
                'no_mesin_asy' => $item->no_mesin_asy,
                'nomor_han_asy' => $item->nomor_han_asy,
                'nik_asy' => $item->nik_asy,
                'nama_petugas_asy' => $item->nama_petugas_asy,
                'dept_petugas_asy' => $item->dept_petugas_asy,
                'infure_berat_loss' => $item->infure_berat_loss,
            ];
            return $carry;
        }, []);

        // index
        $rowItemStart = 4;
        $columnItemStart = 'A';
        $columnLossStart = 'K';
        $columnGentanStart = 'M';
        $rowItem = $rowItemStart;
        foreach ($listProduct as $productId => $productName) {
            // Menulis data produk
            $activeWorksheet->setCellValue($columnItemStart . $rowItem, $productName);
            // $spreadsheet->getActiveSheet()->mergeCells($columnItemStart . $rowItem . ':' . $endColumnItem . $rowItem);
            phpspreadsheet::styleFont($spreadsheet, $columnItemStart . $rowItem, true, 9, 'Calibri');
            $columnItem = $columnItemStart;
            $rowItem++;
            foreach ($listProductionDate[$productId] as $productionDate) {
                $columnItem = $columnItemStart;
                $rowItemLossStart = $rowItem;
                $dataItemProductionDate = $dataFilter[$productId][$productionDate];

                // tanggal produksi
                $activeWorksheet->setCellValue($columnItem . $rowItem, Carbon::parse($dataItemProductionDate['production_date'])->translatedFormat('d-M-Y'));
                phpspreadsheet::textAlignCenter($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // nama petugas
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItemProductionDate['namapetugas']);
                phpspreadsheet::textAlignCenter($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // dept petugas
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItemProductionDate['deptpetugas']);
                phpspreadsheet::textAlignCenter($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // nomor mesin
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItemProductionDate['nomesin']);
                $columnItem++;
                // no lpk
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItemProductionDate['lpk_no']);
                phpspreadsheet::textAlignCenter($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // shift
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItemProductionDate['work_shift']);
                phpspreadsheet::textAlignCenter($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // jam
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItemProductionDate['work_hour']);
                phpspreadsheet::textAlignCenter($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // nomor palet
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItemProductionDate['nomor_palet']);
                phpspreadsheet::textAlignCenter($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // nomor lot
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItemProductionDate['nomor_lot']);
                phpspreadsheet::textAlignCenter($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // qty produksi
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItemProductionDate['qty_produksi']);
                phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
                phpspreadsheet::addFullBorder($spreadsheet, $columnItemStart . $rowItem . ':' . $columnItem . $rowItem);
                $columnItem++;
                phpspreadsheet::styleFont($spreadsheet, $columnItemStart . $rowItem . ':' . $columnItem . $rowItem, false, 8, 'Calibri');

                // Loss
                $rowItemLoss = $rowItem;
                foreach ($dataLoss[$productId][$productionDate] as $losscode => $item) {
                    $columnItem = $columnLossStart;
                    if ($losscode == '') {
                        $columnItem++;
                        phpspreadsheet::addFullBorder($spreadsheet, $columnItemStart . $rowItemLoss . ':' . $columnItem . $rowItemLoss);
                        break;
                    }

                    // nama loss
                    $activeWorksheet->setCellValue($columnItem . $rowItemLoss, $item->loss_name_loss);
                    $columnItem++;
                    // berat loss
                    $activeWorksheet->setCellValue($columnItem . $rowItemLoss, $item->berat_loss_loss);
                    // phpspreadsheet::addFullBorder($spreadsheet, $columnLossStart . $rowItemLoss . ':' . $columnItem . $rowItemLoss);
                    $columnItem++;
                    // phpspreadsheet::styleFont($spreadsheet, $columnItemStart . $rowItemLoss . ':' . $columnItem . $rowItemLoss, false, 8, 'Calibri');
                    $rowItemLoss++;
                }

                // Gentan
                $rowItemGentan = $rowItem;
                foreach ($dataGentan[$productId][$productionDate] as $gentanNo => $item) {
                    $columnItem = $columnGentanStart;
                    // nomor gentan
                    $activeWorksheet->setCellValue($columnItem . $rowItemGentan, $item['gentan_no_line_asy']);
                    $columnItem++;
                    // panjang
                    $activeWorksheet->setCellValue($columnItem . $rowItemGentan, $item['panjang_produksi_asy']);
                    phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItemGentan);
                    $columnItem++;
                    // tanggal produksi infure
                    $activeWorksheet->setCellValue($columnItem . $rowItemGentan, Carbon::parse($item['tgl_produksi_asy'])->translatedFormat('d-M-Y'));
                    phpspreadsheet::textAlignCenter($spreadsheet, $columnItem . $rowItemGentan);
                    $columnItem++;
                    // shift
                    $activeWorksheet->setCellValue($columnItem . $rowItemGentan, $item['work_shift_asy']);
                    phpspreadsheet::textAlignCenter($spreadsheet, $columnItem . $rowItemGentan);
                    $columnItem++;
                    // jam
                    $activeWorksheet->setCellValue($columnItem . $rowItemGentan, $item['work_hour_asy']);
                    phpspreadsheet::textAlignCenter($spreadsheet, $columnItem . $rowItemGentan);
                    $columnItem++;
                    // nomor mesin infure
                    $activeWorksheet->setCellValue($columnItem . $rowItemGentan, $item['no_mesin_asy']);
                    $columnItem++;
                    // nomor han infure
                    $activeWorksheet->setCellValue($columnItem . $rowItemGentan, $item['nomor_han_asy']);
                    phpspreadsheet::textAlignCenter($spreadsheet, $columnItem . $rowItemGentan);
                    $columnItem++;
                    // petugas infure
                    $activeWorksheet->setCellValue($columnItem . $rowItemGentan, $item['nama_petugas_asy']);
                    $columnItem++;
                    // dept infure
                    $activeWorksheet->setCellValue($columnItem . $rowItemGentan, $item['dept_petugas_asy']);
                    $columnItem++;
                    // loss infure
                    $activeWorksheet->setCellValue($columnItem . $rowItemGentan, $item['infure_berat_loss']);
                    phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItemGentan);
                    $rowItemGentan++;
                }

                $rowItem = $rowItemGentan > $rowItemLoss ? $rowItemGentan : $rowItemLoss;
                phpspreadsheet::addFullBorder($spreadsheet, $columnLossStart . $rowItemLossStart . ':' . $columnItem . $rowItem);
                phpSpreadsheet::styleFont($spreadsheet, $columnLossStart . $rowItemLossStart . ':' . $columnItem . $rowItem, false, 8, 'Calibri');
                $rowItem++;
            }
        }

        // Grand Total
        $columnItemEnd = 'K';
        $spreadsheet->getActiveSheet()->mergeCells($columnItemStart . $rowItem . ':' . $columnItemEnd . $rowItem);
        $activeWorksheet->setCellValue($columnItemStart . $rowItem, 'GRAND TOTAL');
        phpSpreadsheet::styleFont($spreadsheet, $columnItemStart . $rowItem, true, 9, 'Calibri');
        $columnItemEnd++;

        // qty produksi
        $totalQtyProduksi = array_reduce($data, function ($carry, $item) {
            $carry += $item->qty_produksi;
            return $carry;
        }, 0);
        $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $totalQtyProduksi);
        phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItemEnd . $rowItem);
        $columnItemEnd++;

        // Loss
        $totalBeratLoss = array_reduce($data, function ($carry, $item) {
            $carry += $item->berat_loss_loss;
            return $carry;
        }, 0);
        $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $totalBeratLoss);
        phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItemEnd . $rowItem);
        $columnItemEnd++;

        // panjang produksi
        $totalPanjangProduksi = array_reduce($data, function ($carry, $item) {
            $carry += $item->panjang_produksi_asy;
            return $carry;
        }, 0);
        $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $totalPanjangProduksi);
        phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItemEnd . $rowItem);
        $columnItemEnd++;

        // infure berat loss
        $totalInfureBeratLoss = array_reduce($data, function ($carry, $item) {
            $carry += $item->infure_berat_loss;
            return $carry;
        }, 0);
        $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $totalInfureBeratLoss);
        phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItemEnd . $rowItem);
        $columnItemEnd++;

        $activeWorksheet->getStyle($columnHeaderStart . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart)->getAlignment()->setWrapText(true);

        // size auto
        $columnSizeStart = $columnItemStart;
        $spreadsheet->getActiveSheet()->getColumnDimension($columnSizeStart)->setWidth(110);
        $columnSizeStart++;
        while ($columnSizeStart !== $columnItem) {
            $spreadsheet->getActiveSheet()->getColumnDimension($columnSizeStart)->setAutoSize(true);
            $columnSizeStart++;
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'Detail-Produksi-' . $this->nippo . '.xlsx';
        $writer->save($filename);
        $response = [
            'status' => 'success',
            'filename' => $filename
        ];
        return $response;
    }

    public function updatedNippo($value)
    {
        $this->machineId = null;
        $this->departmentId = null;

        if ($value == 'Infure') {
            $this->department = departmentHelper::infurePabrikDepartment();
            $this->machine = MachineHelper::getInfureMachine();
        } else if ($value == 'Seitai') {
            $this->department = departmentHelper::seitaiPabrikDepartment();
            $this->machine = MachineHelper::getSeitaiMachine();
        }
    }

    public function updatedDepartmentId($value)
    {
        $this->machineId = null;
        if ($value) {
            $this->machine = MachineHelper::getMachineByDepartment($value);
        }
    }

    public function render()
    {
        return view('livewire.report.detail-report')->extends('layouts.master');
    }
}
