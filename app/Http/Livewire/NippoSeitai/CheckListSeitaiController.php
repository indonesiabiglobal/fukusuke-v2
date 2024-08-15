<?php

namespace App\Http\Livewire\NippoSeitai;

use App\Exports\SeitaiExport;
use App\Helpers\phpspreadsheet;
use App\Models\MsDepartment;
use App\Models\MsMachine;
use App\Models\MsWorkingShift;
use Livewire\Component;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class CheckListSeitaiController extends Component
{
    public $tglMasuk;
    public $tglKeluar;
    public $jamMasuk;
    public $jamKeluar;
    public $machine;
    public $machineId;
    public $noprosesawal = 1;
    public $noprosesakhir = 1000;
    public $lpk_no;
    public $noorder;
    public $department;
    public $departmentId;
    public $transaksi;
    public $nomorPalet;
    public $nomorLot;
    public $jenisReport = 'CheckList';
    public $dataJamMasuk;
    public $dataJamKeluar;

    public function mount()
    {
        $this->tglMasuk = Carbon::now()->format('Y-m-d');
        $this->tglKeluar = Carbon::now()->format('Y-m-d');
        $this->dataJamMasuk = MsWorkingShift::orderBy('work_hour_from')->get();
        $this->dataJamKeluar = MsWorkingShift::orderBy('work_hour_from','desc')->get();
        $this->machine = MsMachine::where('machineno',  'LIKE', '00S%')->get();
        $this->department = MsDepartment::where('division_code', 20)->get();
    }

    public function print()
    {
        // return Excel::download(new SeitaiExport(
        //     $this->tglMasuk,
        //     $this->tglKeluar,
        // ), 'checklist-infure.xlsx');

        if ($this->jenisReport == 2) {
            $tglMasuk = $this->tglMasuk;
            $tglKeluar = $this->tglKeluar;

            $this->dispatch('printSeitai', "tdpg.created_on >= '$tglMasuk 00:00' and tdpg.created_on <= '$tglKeluar 23:59'");
        } else {
            $tglMasuk = $this->tglMasuk;
            $tglKeluar = $this->tglKeluar;

            $this->dispatch('printNippo', "tdpg.created_on >= '$tglMasuk 00:00' and tdpg.created_on <= '$tglKeluar 23:59'");
        }
    }

    public function export()
    {
        // filter
        $jamMasuk = '00:00:00';
        if(isset($this->jamMasuk)){
            $jamMasuk = $this->jamMasuk['value'];
        }
        $jamKeluar = '23:59:00';
        if(isset($this->jamKeluar)){
            $jamKeluar = $this->jamKeluar['value'];
        }

        $tglMasuk = Carbon::parse($this->tglMasuk)->format('Y-m-d ') .  $jamMasuk;
        $tglKeluar = Carbon::parse($this->tglKeluar)->format('Y-m-d ') . $jamKeluar;
        
        if ($this->transaksi == 'produksi') {
            $filterDate = "tdpg.production_date BETWEEN '$tglMasuk' AND '$tglKeluar'";
        } else {
            $filterDate = "tdpg.created_on BETWEEN '$tglMasuk' AND '$tglKeluar'";
        }
        $filterNoLPK = $this->lpk_no ? " AND (tdol.lpk_no = '$this->lpk_no')" : '';
        $filterNoOrder = $this->noorder ? " AND (mp.code = '$this->noorder')" : '';
        $this->departmentId = $this->departmentId ? (is_array($this->departmentId) ? $this->departmentId['value'] : $this->departmentId) : '';
        $filterDepartment = $this->departmentId ? " AND (mm.department_id = '$this->departmentId')" : '';
        $this->machineId = $this->machineId ? (is_array($this->machineId) ? $this->machineId['value'] : $this->machineId) : '';
        $filterMachine = $this->machineId ? " AND (tdpg.machine_id = '$this->machineId')" : '';
        $filterNomorPalet = $this->nomorPalet ? " AND (tdpg.nomor_palet = '$this->nomorPalet')" : '';
        $filterNomorLot = $this->nomorLot ? " AND (tdpg.nomor_lot = '$this->nomorLot')" : '';
        $filterNoproses = $this->noprosesawal && $this->noprosesakhir ? " AND (tdpg.seq_no BETWEEN '$this->noprosesawal' AND '$this->noprosesakhir')" : '';

        $data = collect(
            DB::select("
                WITH goodasy AS (
                    SELECT
                        tpga.product_goods_id,
                        tdpa.gentan_no || '-' || tpga.gentan_line AS gentannomor,
                        tdpa.panjang_produksi,
                        tdpa.production_date AS tglproduksi,
                        tdpa.work_shift,
                        tdpa.work_hour,
                        msm.machineno AS nomesin,
                        tdpa.nomor_han,
                        mse.employeeno AS nik,
                        mse.empname AS namapetugas,
                        msd.NAME AS deptpetugas 
                    FROM
                        tdproduct_goods_assembly AS tpga
                        INNER JOIN tdproduct_assembly AS tdpa ON tdpa.ID = tpga.product_assembly_id
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
                    tdpg.ID,
                    tdpg.production_no AS production_no,
                    tdpg.production_date AS tglproduksi,
                    tdpg.created_on AS tglproses,
                    tdpg.employee_id AS employee_id,
                    maPetugas.empname AS namapetugas,
                    maPetugas.employeeno AS nikpetugas,
                    maInfure.employeeno AS nikpetugasinfure,
                    msd.NAME AS deptpetugas,
                    tdpg.work_shift AS work_shift,
                    tdpg.work_hour AS work_hour,
                    tdpg.machine_id AS machine_id,
                    mm.machineno AS mesinno,
                    mm.machinename AS mesinnama,
                    tdpg.lpk_id AS lpk_id,
                    tdol.lpk_no AS nolpk,
                    tdpg.product_id AS product_id,
                    mp.NAME AS namaproduk,
                    mp.code AS noorder,
                    tdpg.qty_produksi AS qty_produksi,
                    tdpg.seitai_berat_loss AS seitai_berat_loss,
                    tdpg.infure_berat_loss AS infure_berat_loss,
                    tdpg.nomor_palet AS nomor_palet,
                    tdpg.nomor_lot AS nomor_lot,
                    tdpg.seq_no AS noproses,
                    lossgoods.code,
                    lossgoods.namaloss,
                    lossgoods.berat_loss,
                    goodasy.gentannomor,
                    goodasy.panjang_produksi,
                    goodasy.tglproduksi AS tglproduksiasy,
                    goodasy.work_shift AS work_shiftasy,
                    goodasy.work_hour AS work_hourasy,
                    goodasy.nomesin AS nomesinasy,
                    goodasy.nomor_han,
                    goodasy.nik AS nikasy,
                    goodasy.namapetugas AS namapetugasasy,
                    goodasy.deptpetugas AS deptpetugasasy 
                FROM
                    tdProduct_Goods AS tdpg
                    LEFT JOIN goodasy ON tdpg.ID = goodasy.product_goods_id
                    LEFT JOIN lossgoods ON tdpg.ID = lossgoods.product_goods_id
                    INNER JOIN tdOrderLpk AS tdol ON tdpg.lpk_id = tdol.
                    ID INNER JOIN msmachine AS mm ON mm.ID = tdpg.machine_id
                    INNER JOIN msemployee AS maPetugas ON maPetugas.ID = tdpg.employee_id
                    LEFT JOIN msemployee AS maInfure ON tdpg.employee_id_infure = maInfure.
                    ID INNER JOIN msDepartment AS msd ON msd.ID = maPetugas.department_id
                    INNER JOIN msProduct AS mp ON mp.ID = tdpg.product_id
                WHERE
                    $filterDate
                    $filterNoproses
                    $filterNoLPK
                    $filterNoOrder
                    $filterMachine
                    $filterNomorPalet
                    $filterDepartment
                    $filterNomorLot
                "),
        );

        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();
        // Menghilangkan gridline
        $activeWorksheet->setShowGridlines(false);

        // Judul
        $startColumn = 'A';
        $endColumn = 'L';
        $rowTitleCardStart = 1;
        $rowTitleCardEnd = 2;
        $spreadsheet->getActiveSheet()->mergeCells($startColumn . $rowTitleCardStart . ':' . $endColumn . $rowTitleCardStart);
        $activeWorksheet->setCellValue($startColumn . $rowTitleCardStart, 'CHECK LIST NIPPO SEITAI');
        $spreadsheet->getActiveSheet()->mergeCells($startColumn . $rowTitleCardEnd . ':' . $endColumn . $rowTitleCardEnd);
        $activeWorksheet->setCellValue($startColumn . $rowTitleCardEnd, 'Tanggal Produksi : ' . $tglMasuk . ' s/d ' . $tglKeluar);
        phpspreadsheet::styleFont($spreadsheet, $startColumn . $rowTitleCardStart . ':' . $startColumn . $rowTitleCardEnd, true, 11, 'Calibri');

        /* Header */
        $rowHeaderStart = 3;
        $rowHeaderEnd = 4;
        // proses
        $activeWorksheet->setCellValue($startColumn . $rowHeaderStart, 'Tanggal Proses');
        $activeWorksheet->setCellValue($startColumn . $rowHeaderEnd, 'No. Proses');
        phpspreadsheet::styleFont($spreadsheet, $startColumn . $rowHeaderStart . ':' . $startColumn . $rowHeaderEnd, true, 9, 'Calibri');
        phpspreadsheet::textAlignCenter($spreadsheet, $startColumn . $rowHeaderStart . ':' . $startColumn . $rowHeaderEnd);

        // produksi
        $columnProduksi = 'B';
        $activeWorksheet->setCellValue($columnProduksi . $rowHeaderStart, 'Tanggal Produksi');
        $activeWorksheet->setCellValue($columnProduksi . $rowHeaderEnd, 'Shift');
        phpspreadsheet::styleFont($spreadsheet, $columnProduksi . $rowHeaderStart . ':' . $columnProduksi . $rowHeaderEnd, true, 9, 'Calibri');
        phpspreadsheet::textAlignCenter($spreadsheet, $columnProduksi . $rowHeaderStart . ':' . $columnProduksi . $rowHeaderEnd);

        // petugas
        $columnPetugas = 'C';
        $activeWorksheet->setCellValue($columnPetugas . $rowHeaderStart, 'NIK');
        $activeWorksheet->setCellValue($columnPetugas . $rowHeaderEnd, 'Petugas');
        phpspreadsheet::styleFont($spreadsheet, $columnPetugas . $rowHeaderStart . ':' . $columnPetugas . $rowHeaderEnd, true, 9, 'Calibri');
        phpspreadsheet::textAlignCenter($spreadsheet, $columnPetugas . $rowHeaderStart . ':' . $columnPetugas . $rowHeaderEnd);

        // Nomor mesin
        $columnMesin = 'D';
        $spreadsheet->getActiveSheet()->mergeCells($columnMesin . $rowHeaderStart . ':' . $columnMesin . $rowHeaderEnd);
        $activeWorksheet->setCellValue($columnMesin . $rowHeaderStart, 'Nomor Mesin');
        phpspreadsheet::styleFont($spreadsheet, $columnMesin . $rowHeaderStart . ':' . $columnMesin . $rowHeaderEnd, true, 9, 'Calibri');
        phpspreadsheet::textAlignCenter($spreadsheet, $columnMesin . $rowHeaderStart . ':' . $columnMesin . $rowHeaderEnd);
        $activeWorksheet->getStyle($columnMesin . $rowHeaderStart)->getAlignment()->setWrapText(true);

        // Nomor LPK
        $columnLpk = 'E';
        $spreadsheet->getActiveSheet()->mergeCells($columnLpk . $rowHeaderStart . ':' . $columnLpk . $rowHeaderEnd);
        $activeWorksheet->setCellValue($columnLpk . $rowHeaderStart, 'Nomor LPK');
        phpspreadsheet::styleFont($spreadsheet, $columnLpk . $rowHeaderStart . ':' . $columnLpk . $rowHeaderEnd, true, 9, 'Calibri');
        phpspreadsheet::textAlignCenter($spreadsheet, $columnLpk . $rowHeaderStart . ':' . $columnLpk . $rowHeaderEnd);

        // Nama Produk
        $columnProduk = 'F';
        $activeWorksheet->setCellValue($columnProduk . $rowHeaderStart, 'Nama Produk');
        $activeWorksheet->setCellValue($columnProduk . $rowHeaderEnd, 'Nomor Order');
        phpspreadsheet::styleFont($spreadsheet, $columnProduk . $rowHeaderStart . ':' . $columnProduk . $rowHeaderEnd, true, 9, 'Calibri');
        phpspreadsheet::textAlignCenter($spreadsheet, $columnProduk . $rowHeaderStart . ':' . $columnProduk . $rowHeaderEnd);

        // Quantity
        $columnQty = 'G';
        $spreadsheet->getActiveSheet()->mergeCells($columnQty . $rowHeaderStart . ':' . $columnQty . $rowHeaderEnd);
        $activeWorksheet->setCellValue($columnQty . $rowHeaderStart, 'Quantity (Lembar)');
        phpspreadsheet::styleFont($spreadsheet, $columnQty . $rowHeaderStart . ':' . $columnQty . $rowHeaderEnd, true, 9, 'Calibri');
        phpspreadsheet::textAlignCenter($spreadsheet, $columnQty . $rowHeaderStart . ':' . $columnQty . $rowHeaderEnd);
        $activeWorksheet->getStyle($columnQty . $rowHeaderStart)->getAlignment()->setWrapText(true);

        // Loss Infure
        $columnLoss = 'H';
        $activeWorksheet->setCellValue($columnLoss . $rowHeaderStart, 'Loss Infure');
        $activeWorksheet->setCellValue($columnLoss . $rowHeaderEnd, 'NIK');
        phpspreadsheet::styleFont($spreadsheet, $columnLoss . $rowHeaderStart . ':' . $columnLoss . $rowHeaderEnd, true, 9, 'Calibri');
        phpspreadsheet::textAlignCenter($spreadsheet, $columnLoss . $rowHeaderStart . ':' . $columnLoss . $rowHeaderEnd);
        $activeWorksheet->getStyle($columnLoss . $rowHeaderStart)->getAlignment()->setWrapText(true);

        // Nomor palet
        $columnPalet = 'I';
        $activeWorksheet->setCellValue($columnPalet . $rowHeaderStart, 'Nomor Palet');
        $activeWorksheet->setCellValue($columnPalet . $rowHeaderEnd, 'Nomor LOT');
        phpspreadsheet::styleFont($spreadsheet, $columnPalet . $rowHeaderStart . ':' . $columnPalet . $rowHeaderEnd, true, 9, 'Calibri');
        phpspreadsheet::textAlignCenter($spreadsheet, $columnPalet . $rowHeaderStart . ':' . $columnPalet . $rowHeaderEnd);

        // nomor gentan
        $columnGentan = 'J';
        $spreadsheet->getActiveSheet()->mergeCells($columnGentan . $rowHeaderStart . ':' . $columnGentan . $rowHeaderEnd);
        $activeWorksheet->setCellValue($columnGentan . $rowHeaderStart, 'Nomor Gentan');
        phpspreadsheet::styleFont($spreadsheet, $columnGentan . $rowHeaderStart . ':' . $columnGentan . $rowHeaderEnd, true, 9, 'Calibri');
        phpspreadsheet::textAlignCenter($spreadsheet, $columnGentan . $rowHeaderStart . ':' . $columnGentan . $rowHeaderEnd);
        $activeWorksheet->getStyle($columnGentan . $rowHeaderStart)->getAlignment()->setWrapText(true);

        // Nama Loss
        $columnNamaLoss = 'K';
        $spreadsheet->getActiveSheet()->mergeCells($columnNamaLoss . $rowHeaderStart . ':' . $columnNamaLoss . $rowHeaderEnd);
        $activeWorksheet->setCellValue($columnNamaLoss . $rowHeaderStart, 'Nama Loss');
        phpspreadsheet::styleFont($spreadsheet, $columnNamaLoss . $rowHeaderStart . ':' . $columnNamaLoss . $rowHeaderEnd, true, 9, 'Calibri');
        phpspreadsheet::textAlignCenter($spreadsheet, $columnNamaLoss . $rowHeaderStart . ':' . $columnNamaLoss . $rowHeaderEnd);

        // Berat
        $columnBerat = 'L';
        $spreadsheet->getActiveSheet()->mergeCells($columnBerat . $rowHeaderStart . ':' . $columnBerat . $rowHeaderEnd);
        $activeWorksheet->setCellValue($columnBerat . $rowHeaderStart, 'Berat (Kg)');
        phpspreadsheet::styleFont($spreadsheet, $columnBerat . $rowHeaderStart . ':' . $columnBerat . $rowHeaderEnd, true, 9, 'Calibri');
        phpspreadsheet::textAlignCenter($spreadsheet, $columnBerat . $rowHeaderStart . ':' . $columnBerat . $rowHeaderEnd);
        $activeWorksheet->getStyle($columnBerat . $rowHeaderStart)->getAlignment()->setWrapText(true);

        // border header
        phpspreadsheet::addFullBorder($spreadsheet, $startColumn . $rowHeaderStart . ':' . $columnBerat . $rowHeaderEnd);

        /**
         * Header Value
         *  */
        $rowItemStart = 5;
        $rowItemEnd = 6;
        foreach ($data as $item) {
            // mengecek apakah ada loss
            $loss = collect(
                DB::select("
                select
                pgl.product_goods_id,ls.name as namaloss,ls.code as codeloss, pgl.berat_loss
                FROM tdproduct_goods AS tdpg
                inner join tdproduct_goods_loss as pgl on tdpg.id=pgl.product_goods_id
                left join mslossseitai as ls on pgl.loss_seitai_id=ls.id
                WHERE
                pgl.product_goods_id = '$item->id'
                "),
            );
            if ($this->jenisReport == 'LossSeitai' && $loss->isEmpty()) {
                continue;
            }

            // Tanggal Proses
            $activeWorksheet->setCellValue($startColumn . $rowItemStart, Carbon::parse($item->tglproses)->format('d-M-Y'));
            phpspreadsheet::styleFont($spreadsheet, $startColumn . $rowItemStart, false, 8, 'Calibri');
            phpspreadsheet::textAlignCenter($spreadsheet, $startColumn . $rowItemStart);
            // No Proses
            $activeWorksheet->setCellValue($startColumn . $rowItemEnd, $item->noproses);
            phpspreadsheet::styleFont($spreadsheet, $startColumn . $rowItemEnd, false, 8, 'Calibri');
            phpspreadsheet::textAlignCenter($spreadsheet, $startColumn . $rowItemEnd);
            // Tangga Produksi
            $activeWorksheet->setCellValue($columnProduksi . $rowItemStart, Carbon::parse($item->tglproduksi)->format('d-M-Y'));
            phpspreadsheet::styleFont($spreadsheet, $columnProduksi . $rowItemStart, false, 8, 'Calibri');
            phpspreadsheet::textAlignCenter($spreadsheet, $columnProduksi . $rowItemStart);
            // Shift
            $activeWorksheet->setCellValue($columnProduksi . $rowItemEnd, $item->shift);
            phpspreadsheet::styleFont($spreadsheet, $columnProduksi . $rowItemEnd, false, 8, 'Calibri');
            phpspreadsheet::textAlignCenter($spreadsheet, $columnProduksi . $rowItemEnd);
            // NIK
            $activeWorksheet->setCellValue($columnPetugas . $rowItemStart, $item->nikpetugas);
            phpspreadsheet::styleFont($spreadsheet, $columnPetugas . $rowItemStart, false, 8, 'Calibri');
            phpspreadsheet::textAlignCenter($spreadsheet, $columnPetugas . $rowItemStart);
            // Petugas
            $activeWorksheet->setCellValue($columnPetugas . $rowItemEnd, $item->namapetugas);
            $spreadsheet->getActiveSheet()->mergeCells($columnPetugas . $rowItemEnd . ':' . $columnLpk . $rowItemEnd);
            phpspreadsheet::styleFont($spreadsheet, $columnPetugas . $rowItemEnd, false, 8, 'Calibri');
            phpspreadsheet::textAlignCenter($spreadsheet, $columnPetugas . $rowItemEnd . ':' . $columnLpk . $rowItemEnd);
            // Nomor Mesin
            $activeWorksheet->setCellValue($columnMesin . $rowItemStart, $item->mesinno . ' - ' . $item->mesinnama);
            phpspreadsheet::styleFont($spreadsheet, $columnMesin . $rowItemStart, false, 8, 'Calibri');
            phpspreadsheet::textAlignCenter($spreadsheet, $columnMesin . $rowItemStart);
            // Nomor LPK
            $activeWorksheet->setCellValue($columnLpk . $rowItemStart, $item->nolpk);
            phpspreadsheet::styleFont($spreadsheet, $columnLpk . $rowItemStart, false, 8, 'Calibri');
            phpspreadsheet::textAlignCenter($spreadsheet, $columnLpk . $rowItemStart);
            // Nama Produk
            $activeWorksheet->setCellValue($columnProduk . $rowItemStart, $item->namaproduk);
            phpspreadsheet::styleFont($spreadsheet, $columnProduk . $rowItemStart, false, 8, 'Calibri');
            phpspreadsheet::textAlignCenter($spreadsheet, $columnProduk . $rowItemStart);
            // Nomor Order
            $activeWorksheet->setCellValue($columnProduk . $rowItemEnd, $item->noorder);
            phpspreadsheet::styleFont($spreadsheet, $columnProduk . $rowItemEnd, false, 8, 'Calibri');
            phpspreadsheet::textAlignCenter($spreadsheet, $columnProduk . $rowItemEnd);
            // Quantity
            $activeWorksheet->setCellValue($columnQty . $rowItemStart, $item->qty_produksi);
            phpspreadsheet::styleFont($spreadsheet, $columnQty . $rowItemStart, false, 8, 'Calibri');
            phpSpreadsheet::numberFormatThousands($spreadsheet, $columnQty . $rowItemStart);
            // Loss Infure
            $activeWorksheet->setCellValue($columnLoss . $rowItemStart, $item->infure_berat_loss);
            phpspreadsheet::styleFont($spreadsheet, $columnLoss . $rowItemStart, false, 8, 'Calibri');
            // NIK
            $activeWorksheet->setCellValue($columnLoss . $rowItemEnd, $item->nikpetugasinfure);
            phpspreadsheet::styleFont($spreadsheet, $columnLoss . $rowItemEnd, false, 8, 'Calibri');
            // Nomor Palet
            $activeWorksheet->setCellValue($columnPalet . $rowItemStart, $item->nomor_palet);
            phpspreadsheet::styleFont($spreadsheet, $columnPalet . $rowItemStart, false, 8, 'Calibri');
            phpspreadsheet::textAlignCenter($spreadsheet, $columnPalet . $rowItemStart);
            // Nomor LOT
            $activeWorksheet->setCellValue($columnPalet . $rowItemEnd, $item->nomor_lot);
            phpspreadsheet::styleFont($spreadsheet, $columnPalet . $rowItemEnd, false, 8, 'Calibri');
            phpspreadsheet::textAlignCenter($spreadsheet, $columnPalet . $rowItemEnd);

            // border
            phpspreadsheet::addFullBorder($spreadsheet, $startColumn . $rowItemStart . ':' . $columnPalet . $rowItemEnd);

            // Nomor Gentan
            $gentanno = collect(
                DB::select("
                    SELECT
                        pga.product_goods_id,
                        tdpa.gentan_no || '-' || pga.gentan_line AS nogentan
                    FROM
                        tdproduct_goods_assembly AS pga
                        LEFT JOIN tdProduct_Goods AS tdpg ON tdpg.ID = pga.product_goods_id
                        LEFT JOIN tdproduct_assembly AS tdpa ON tdpa.ID = pga.product_assembly_id
                    WHERE
                        pga.product_goods_id = '$item->id'
                "),
            );
            $rowGentan = $rowItemStart;
            foreach ($gentanno as $gentan) {
                $gentan->nogentan;
                $activeWorksheet->setCellValue($columnGentan . $rowGentan, $gentan->nogentan);
                phpspreadsheet::styleFont($spreadsheet, $columnGentan . $rowGentan, false, 8, 'Calibri');
                phpspreadsheet::textAlignCenter($spreadsheet, $columnGentan . $rowGentan);
                $rowGentan++;
            }

            // Nama Loss
            $rowLoss = $rowItemStart;
            foreach ($loss as $itemLoss) {
                $activeWorksheet->setCellValue($columnNamaLoss . $rowLoss, $itemLoss->codeloss . '. ' . $itemLoss->namaloss);
                phpspreadsheet::styleFont($spreadsheet, $columnNamaLoss . $rowLoss, false, 8, 'Calibri');
                // Berat
                $activeWorksheet->setCellValue($columnBerat . $rowLoss, $itemLoss->berat_loss);
                phpspreadsheet::styleFont($spreadsheet, $columnBerat . $rowLoss, false, 8, 'Calibri');
                $rowLoss++;
            }

            // border
            phpspreadsheet::addFullBorder($spreadsheet, $columnGentan . $rowItemStart . ':' . $columnBerat . ($rowLoss > $rowGentan ? $rowLoss : $rowGentan));

            $rowItemStart = ($rowLoss < $rowGentan) ? $rowGentan + 2 : $rowLoss + 2;
            $rowItemEnd = $rowItemStart + 1;
        }

        // size auto
        while ($startColumn !== $columnBerat) {

            switch ($startColumn) {
                case $columnQty:
                    $spreadsheet->getActiveSheet()->getColumnDimension($columnQty)->setWidth(90, 'px');
                    break;
                case $columnGentan:
                    $spreadsheet->getActiveSheet()->getColumnDimension($columnGentan)->setWidth(54, 'px');
                    break;
                case $columnBerat:
                    $spreadsheet->getActiveSheet()->getColumnDimension($columnBerat)->setWidth(80, 'px');
                    break;
                default:
                    $spreadsheet->getActiveSheet()->getColumnDimension($startColumn)->setAutoSize(true);
                    break;
            }

            $startColumn++;
        }
        // $spreadsheet->getActiveSheet()->getColumnDimension($columnMesin)->setWidth(72, 'px');

        $writer = new Xlsx($spreadsheet);
        $writer->save('asset/report/NippoSeitai-' . $this->jenisReport . '.xlsx');
        return response()->download('asset/report/NippoSeitai-' . $this->jenisReport . '.xlsx');
    }

    public function render()
    {
        return view('livewire.nippo-seitai.check-list-seitai')->extends('layouts.master');
    }
}