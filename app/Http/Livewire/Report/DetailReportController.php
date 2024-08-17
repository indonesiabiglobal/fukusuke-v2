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
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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
        $this->workingShiftHour = MsWorkingShift::select('work_hour_from', 'work_hour_till')->where('status', 1)->orderBy('work_hour_from', 'ASC')->get();
        $this->jamAwal = $this->workingShiftHour[0]->work_hour_from;
        $this->jamAkhir = $this->workingShiftHour[count($this->workingShiftHour) - 1]->work_hour_till;
        $this->machine = MsMachine::get();
        $this->department = MsDepartment::get();
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
            $response = $this->reportInfure($tglAwal, $tglAkhir);
            if ($response['status'] == 'success') {
                return response()->download($response['filename']);
            } else if ($response['status'] == 'error') {
                $this->dispatch('notification', ['type' => 'warning', 'message' => $response['message']]);
                return;
            }
        } else if ($this->nippo == 'Seitai'){
            $response = $this->reportSeitai($tglAwal, $tglAkhir);
            if ($response['status'] == 'success') {
                return response()->download($response['filename']);
            } else if ($response['status'] == 'error') {
                $this->dispatch('notification', ['type' => 'warning', 'message' => $response['message']]);
                return;
            }
        }
    }

    public function reportInfure($tglAwal, $tglAkhir)
    {
        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();
        $activeWorksheet->setShowGridlines(false);

        // Set locale agar tanggal indonesia
        Carbon::setLocale('id');

        // Judul
        $activeWorksheet->setCellValue('A1', 'DETAIL PRODUKSI INFURE');
        $activeWorksheet->setCellValue('A2', 'Periode: ' . $tglAwal->translatedFormat('d-M-Y H:i') . ' s/d ' . $tglAkhir->translatedFormat('d-M-Y H:i'));
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

        // qeury belum bener
        $data = collect(DB::select(
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
                    $filterNomorHan",
        ));

        if (count($data) == 0) {
            $response = [
                'status' => 'error',
                'message' => "Data pada periode tanggal atau pembeli tersebut tidak ditemukan"
            ];

            return $response;
        }

        $activeWorksheet->getStyle($columnHeaderStart . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart)->getAlignment()->setWrapText(true);

        // size auto
        // $columnSizeStart = $columnItemStart;
        // $columnSizeStart++;
        // while ($columnSizeStart !== $columnItemEnd) {
        //     $spreadsheet->getActiveSheet()->getColumnDimension($columnSizeStart)->setAutoSize(true);
        //     $columnSizeStart++;
        // }

        $writer = new Xlsx($spreadsheet);
        $filename = 'Detail-Produksi-'. $this->nippo . '.xlsx';
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

        // Set locale agar tanggal indonesia
        Carbon::setLocale('id');

        // Judul
        $activeWorksheet->setCellValue('A1', 'DETAIL PRODUKSI SEITAI');
        $activeWorksheet->setCellValue('A2', 'Periode: ' . $tglAwal->translatedFormat('d-M-Y H:i') . ' s/d ' . $tglAkhir->translatedFormat('d-M-Y H:i'));
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
            'NIK',
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
        $columnHeaderEnd = chr(ord($columnHeaderEnd) - 1);

        // style header
        phpspreadsheet::addFullBorder($spreadsheet, $columnHeaderStart . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);
        phpspreadsheet::styleFont($spreadsheet, $columnHeaderStart . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart, true, 9, 'Calibri');
        phpspreadsheet::textAlignCenter($spreadsheet, $columnHeaderStart . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);

        // Filter Query
        $filterDate = "tdpg.created_on BETWEEN '$tglAwal' AND '$tglAkhir'";
        $filterNoLPK = $this->lpk_no ? " AND (tdol.lpk_no = '$this->lpk_no')" : '';
        $nomorOrder = $this->nomorOrder ? " AND (mp.code = '$this->nomorOrder')" : '';
        $this->departmentId = $this->departmentId ? (is_array($this->departmentId) ? $this->departmentId['value'] : $this->departmentId) : '';
        $filterDepartment = $this->departmentId ? " AND (mm.department_id = '$this->departmentId')" : '';
        $this->machineId = $this->machineId ? (is_array($this->machineId) ? $this->machineId['value'] : $this->machineId) : '';
        $filterMachine = $this->machineId ? " AND (tdpg.machine_id = '$this->machineId')" : '';
        $filterNomorPalet = $this->nomorPalet ? " AND (tdpg.nomor_palet = '$this->nomorPalet')" : '';
        $filterNomorLot = $this->nomorLot ? " AND (tdpg.nomor_lot = '$this->nomorLot')" : '';

        // qeury belum bener
        $data = collect(DB::select(
            "
                SELECT
                    tod.id,
                    tod.po_no,
                    mp.code,
                    mp.name AS produk_name,
                    tod.product_code,
                    tod.order_qty,
                    tod.order_unit,
                    tod.stufingdate,
                    tod.etddate,
                    tod.etadate,
                    mbu.NAME AS buyer_name
                FROM
                    tdorder AS tod
                INNER JOIN msproduct AS mp ON mp.id = tod.product_id
                INNER JOIN msbuyer AS mbu ON mbu.id = tod.buyer_id",
        ));

        if (count($data) == 0) {
            $response = [
                'status' => 'error',
                'message' => "Data pada periode tanggal atau pembeli tersebut tidak ditemukan"
            ];

            return $response;
        }

        $activeWorksheet->getStyle($columnHeaderStart . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart)->getAlignment()->setWrapText(true);

        // size auto
        // $columnSizeStart = $columnItemStart;
        // $columnSizeStart++;
        // while ($columnSizeStart !== $columnItemEnd) {
        //     $spreadsheet->getActiveSheet()->getColumnDimension($columnSizeStart)->setAutoSize(true);
        //     $columnSizeStart++;
        // }

        $writer = new Xlsx($spreadsheet);
        $filename = 'Detail-Produksi-'. $this->nippo . '.xlsx';
        $writer->save($filename);
        $response = [
            'status' => 'success',
            'filename' => $filename
        ];
        return $response;
    }



    public function render()
    {
        return view('livewire.report.detail-report')->extends('layouts.master');
    }
}
