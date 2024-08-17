<?php

namespace App\Http\Livewire\NippoInfure;

use Carbon\Carbon;
use Livewire\Component;
use App\Models\MsMachine;
use App\Models\MsDepartment;
use App\Models\MsWorkingShift;
use App\Helpers\phpspreadsheet;
use App\Exports\LossInfureExport;
use App\Exports\NippoInfureExport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class CheckListInfureController extends Component
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

    public function mount()
    {
        $this->tglAwal = Carbon::now()->format('Y-m-d');
        $this->tglAkhir = Carbon::now()->format('Y-m-d');
        $this->workingShiftHour = MsWorkingShift::select('id', 'work_hour_from', 'work_hour_till')->where('status', 1)->orderBy('work_hour_from', 'ASC')->get();
        $this->jamAwal = $this->workingShiftHour[0]->work_hour_from;
        $this->jamAkhir = $this->workingShiftHour[count($this->workingShiftHour) - 1]->work_hour_till;
        $this->machine = MsMachine::where('machineno',  'LIKE', '00I%')->get();
        $this->department = MsDepartment::where('division_code', 10)->get();
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

        $response = $this->checklistInfure($tglAwal, $tglAkhir);
        if ($response['status'] == 'success') {
            return response()->download($response['filename']);
        } else if ($response['status'] == 'error') {
            $this->dispatch('notification', ['type' => 'warning', 'message' => $response['message']]);
            return;
        }
    }

    public function checklistInfure($tglAwal, $tglAkhir)
    {
        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();
        $activeWorksheet->setShowGridlines(false);

        // Set locale agar tanggal indonesia
        Carbon::setLocale('id');

        // Judul
        $activeWorksheet->setCellValue('A1', ($this->jenisReport == 'Checklist' ? 'CHECKLIST ' : 'LOSS ') . 'NIPPO INFURE');
        $activeWorksheet->setCellValue('A2', 'Periode: ' . $tglAwal->translatedFormat('d-M-Y H:i') . ' s/d ' . $tglAkhir->translatedFormat('d-M-Y H:i'));
        // Style Judul
        phpspreadsheet::styleFont($spreadsheet, 'A1:A2', true, 11, 'Calibri');

        // header
        $rowHeaderStart = 3;
        $columnHeaderStart = 'A';
        $columnHeaderEnd = 'A';

        $header = [
            'Tanggal Proses',
            'No.',
            'Tanggal Produksi',
            'Shift',
            'Jam',
            'NIK',
            'Nama Petugas',
            'Nomor Mesin',
            'Nomor LPK',
            'Nomor Order',
            'Nama Produk',
            'Nomor Gentan',
            'Nomor Han',
            'Panjang Infure (meter)',
            'Berat Standard (Kg)',
            'Berat Produksi (Kg)',
            'Loss',
            'Berat Loss (Kg)',
        ];

        foreach ($header as $key => $value) {
            $activeWorksheet->setCellValue($columnHeaderEnd . $rowHeaderStart, $value);
            $columnHeaderEnd++;
        }

        $activeWorksheet->freezePane('D4');
        $columnHeaderEnd = chr(ord($columnHeaderEnd) - 1);

        // style header
        phpspreadsheet::addFullBorder($spreadsheet, $columnHeaderStart . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);
        phpspreadsheet::styleFont($spreadsheet, $columnHeaderStart . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart, true, 9, 'Calibri');
        phpspreadsheet::textAlignCenter($spreadsheet, $columnHeaderStart . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);

        // filter date
        if ($this->transaksi == '1') {
            $filterDate = "tdpa.production_date BETWEEN '$tglAwal' AND '$tglAkhir'";
        } else if ($this->transaksi == '2') {
            $filterDate = "tdpa.created_on BETWEEN '$tglAwal' AND '$tglAkhir'";
        }
        // Filter Query
        $filterSeqNo = $this->noprosesawal ? " AND (tdpa.seq_no >= '$this->noprosesawal')" : '';
        $filterSeqNo .= $this->noprosesakhir ? " AND (tdpa.seq_no <= '$this->noprosesakhir')" : '';
        $filterNoLPK = $this->lpk_no ? " AND (tdol.lpk_no = '$this->lpk_no')" : '';
        $nomorOrder = $this->nomorOrder ? " AND (msp.code = '$this->nomorOrder')" : '';
        $this->departmentId = $this->departmentId ? (is_array($this->departmentId) ? $this->departmentId['value'] : $this->departmentId) : '';
        $filterDepartment = $this->departmentId ? " AND (msd.id = '$this->departmentId')" : '';
        $this->machineId = $this->machineId ? (is_array($this->machineId) ? $this->machineId['value'] : $this->machineId) : '';
        $filterMachine = $this->machineId ? " AND (tdpa.machine_id = '$this->machineId')" : '';
        $filterNomorHan = $this->nomorHan ? " AND (tdpa.nomor_han = '$this->nomorHan')" : '';

        if ($this->jenisReport == 'Checklist') {
            $data = DB::select(
                "
                    SELECT
                        tdpa.production_date AS tglproduksi,
                        tdpa.created_on AS tanggal_proses,
                        tdpa.seq_no,
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
                        msp.NAME AS nama_produk,
                        tdol.lpk_no AS lpk_no,
                        tdpa.nomor_han AS nomor_han,
                        tdpa.gentan_no AS gentan_no,
                        tdpa.panjang_produksi AS panjang_produksi,
                        tdpa.panjang_printing_inline AS panjang_printing_inline,
                        tdpa.berat_produksi AS berat_produksi,
                        tdpa.berat_standard,
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
                    ",
            );
        } else if ($this->jenisReport == 'Loss') {
            $data = DB::select(
                "
                    SELECT
                        tdpa.production_date AS tglproduksi,
                        tdpa.created_on AS tanggal_proses,
                        tdpa.seq_no,
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
                        msp.NAME AS nama_produk,
                        tdol.lpk_no AS lpk_no,
                        tdpa.nomor_han AS nomor_han,
                        tdpa.gentan_no AS gentan_no,
                        tdpa.panjang_produksi AS panjang_produksi,
                        tdpa.panjang_printing_inline AS panjang_printing_inline,
                        tdpa.berat_produksi AS berat_produksi,
                        tdpa.berat_standard,
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
                        INNER JOIN tdProduct_Assembly_Loss AS tdpal ON tdpal.product_assembly_id = tdpa.id
                        LEFT JOIN msLossInfure AS msli ON msli.ID = tdpal.loss_infure_id
                    WHERE
                        $filterDate
                        $filterNoLPK
                        $nomorOrder
                        $filterDepartment
                        $filterMachine
                        $filterNomorHan
                    ",
            );
        }

        if (count($data) == 0) {
            $response = [
                'status' => 'error',
                'message' => "Data pada periode tanggal tersebut tidak ditemukan"
            ];

            return $response;
        }

        $dataFiltered = [];
        $dataLoss = [];

        foreach ($data as $item) {
            $tglProduksi = $item->tglproduksi;

            // Data Utama
            if (!isset($dataFiltered[$tglProduksi])) {
                $dataFiltered[$tglProduksi] = [
                    'tanggal_proses' => $item->tanggal_proses,
                    'seq_no' => $item->seq_no,
                    'tglproduksi' => $item->tglproduksi,
                    'shift' => $item->shift,
                    'jam' => $item->jam,
                    'nik' => $item->nik,
                    'nama_petugas' => $item->namapetugas,
                    'dept_petugas' => $item->deptpetugas,
                    'nomor_mesin' => $item->nomesin,
                    'lpk_no' => $item->lpk_no,
                    'produkcode' => $item->produkcode,
                    'nama_produk' => $item->nama_produk,
                    'gentan_no' => $item->gentan_no,
                    'nomor_han' => $item->nomor_han,
                    'panjang_produksi' => $item->panjang_produksi,
                    'berat_produksi' => $item->berat_produksi,
                    'berat_standard' => $item->berat_standard,
                    'losscode' => $item->losscode,
                    'lossname' => $item->lossname,
                ];
            }

            // Data Loss
            if (!isset($dataLoss[$tglProduksi][$item->seq_no][$item->losscode])) {
                $dataLoss[$tglProduksi][$item->seq_no][$item->losscode] = (object)[
                    'lossname' => $item->lossname,
                    'berat_loss' => $item->berat_loss,
                ];
            }
        }

        // index
        $rowItemStart = 4;
        $columnItemStart = 'A';
        $rowItem = $rowItemStart;
        foreach ($dataFiltered as $productionDate => $dataItem) {
            $columnItemEnd = $columnItemStart;
            // tanggal proses
            $activeWorksheet->setCellValue($columnItemEnd . $rowItem, Carbon::parse($dataItem['tanggal_proses'])->translatedFormat('d-M-Y H:i'));
            phpspreadsheet::textAlignCenter($spreadsheet, $columnItemEnd . $rowItem);
            $columnItemEnd++;
            // no proses
            $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $dataItem['seq_no']);
            phpspreadsheet::textAlignCenter($spreadsheet, $columnItemEnd . $rowItem);
            $columnItemEnd++;
            // tanggal produksi
            $activeWorksheet->setCellValue($columnItemEnd . $rowItem, Carbon::parse($dataItem['tglproduksi'])->translatedFormat('d-M-Y'));
            phpspreadsheet::textAlignCenter($spreadsheet, $columnItemEnd . $rowItem);
            $columnItemEnd++;
            // shift
            $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $dataItem['shift']);
            phpspreadsheet::textAlignCenter($spreadsheet, $columnItemEnd . $rowItem);
            $columnItemEnd++;
            // jam
            $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $dataItem['jam']);
            phpspreadsheet::textAlignCenter($spreadsheet, $columnItemEnd . $rowItem);
            $columnItemEnd++;
            // nik
            $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $dataItem['nik']);
            phpspreadsheet::textAlignCenter($spreadsheet, $columnItemEnd . $rowItem);
            $columnItemEnd++;
            // nama petugas
            $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $dataItem['nama_petugas']);
            phpspreadsheet::textAlignCenter($spreadsheet, $columnItemEnd . $rowItem);
            $columnItemEnd++;
            // nomor_mesin
            $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $dataItem['nomor_mesin']);
            phpspreadsheet::textAlignCenter($spreadsheet, $columnItemEnd . $rowItem);
            $columnItemEnd++;
            // no lpk
            $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $dataItem['lpk_no']);
            phpspreadsheet::textAlignCenter($spreadsheet, $columnItemEnd . $rowItem);
            $columnItemEnd++;
            // nomer order
            $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $dataItem['produkcode']);
            phpspreadsheet::textAlignCenter($spreadsheet, $columnItemEnd . $rowItem);
            $columnItemEnd++;
            // nama produk
            $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $dataItem['nama_produk']);
            phpspreadsheet::textAlignCenter($spreadsheet, $columnItemEnd . $rowItem);
            $columnItemEnd++;
            // nomor gentan
            $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $dataItem['gentan_no']);
            phpspreadsheet::textAlignCenter($spreadsheet, $columnItemEnd . $rowItem);
            $columnItemEnd++;
            // nomor han
            $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $dataItem['nomor_han']);
            phpspreadsheet::textAlignCenter($spreadsheet, $columnItemEnd . $rowItem);
            $columnItemEnd++;
            // panjang produksi
            $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $dataItem['panjang_produksi']);
            phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItemEnd . $rowItem);
            $columnItemEnd++;
            // berat standard
            $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $dataItem['berat_standard']);
            phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItemEnd . $rowItem);
            $columnItemEnd++;
            // berat produksi
            $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $dataItem['berat_produksi']);
            phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItemEnd . $rowItem);
            $columnItemEnd++;

            // Loss
            foreach ($dataLoss[$productionDate][$dataItem['seq_no']] as $losscode => $item) {
                $columnLoss = 'Q';
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
        $rowItem++;

        // grand total
        $columnItemEnd = 'N';
        $spreadsheet->getActiveSheet()->mergeCells($columnItemStart . $rowItem . ':' . $columnItemEnd . $rowItem);
        $activeWorksheet->setCellValue($columnItemStart . $rowItem, 'GRAND TOTAL');
        phpSpreadsheet::styleFont($spreadsheet, $columnItemStart . $rowItem, true, 9, 'Calibri');
        $columnItemEnd++;

        // panjang produksi
        $totalPanjangProduksi = array_reduce($data, function ($carry, $item) {
            $carry += $item->panjang_produksi;
            return $carry;
        }, 0);
        $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $totalPanjangProduksi);
        phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItemEnd . $rowItem);
        $columnItemEnd++;

        // berat standard
        $totalBeratStandard = array_reduce($data, function ($carry, $item) {
            $carry += $item->berat_standard;
            return $carry;
        }, 0);
        $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $totalBeratStandard);
        phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItemEnd . $rowItem);
        $columnItemEnd++;

        // berat produksi
        $totalBeratProduksi = array_reduce($data, function ($carry, $item) {
            $carry += $item->berat_produksi;
            return $carry;
        }, 0);
        $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $totalBeratProduksi);
        phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItemEnd . $rowItem);
        $columnItemEnd++;

        // Loss
        $columnItemEnd = 'Q';
        $totalLoss = array_reduce($data, function ($carry, $item) {
            $carry += $item->berat_loss;
            return $carry;
        }, 0);
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
        $filename = 'NippoInfure-' . $this->jenisReport . '.xlsx';
        $writer->save($filename);
        $response = [
            'status' => 'success',
            'filename' => $filename
        ];
        return $response;
    }

    public function render()
    {
        return view('livewire.nippo-infure.check-list')->extends('layouts.master');
    }
}
