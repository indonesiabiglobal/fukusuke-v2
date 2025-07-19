<?php

namespace App\Http\Livewire\NippoSeitai;

use App\Exports\SeitaiExport;
use App\Helpers\phpspreadsheet;
use App\Models\MsDepartment;
use App\Models\MsMachine;
use App\Models\MsProduct;
use App\Models\MsWorkingShift;
use Livewire\Component;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class CheckListSeitaiController extends Component
{
    public $tglAwal;
    public $tglAkhir;
    public $jamAwal;
    public $jamAkhir;
    public $workingShiftHour;
    public $machine;
    public $machineId;
    public $noprosesawal = 1;
    public $noprosesakhir = 1000;
    public $lpk_no;
    public $noorder;
    public $department;
    public $departmentId;
    public $transaksi = 'produksi';
    public $nomorPalet;
    public $nomorLot;
    public $jenisReport = 'CheckList';
    public $products;
    public $productId;
    public $status;
    public $searchTerm;
    public $gentan_no;

    public function mount()
    {
        $this->tglAwal = Carbon::now()->format('Y-m-d');
        $this->tglAkhir = Carbon::now()->format('Y-m-d');
        $this->workingShiftHour = MsWorkingShift::select('work_hour_from', 'work_hour_till')->where('status', 1)->orderBy('work_hour_from', 'ASC')->get();
        $this->jamAwal = $this->workingShiftHour[0]->work_hour_from;
        $this->jamAkhir = $this->workingShiftHour[count($this->workingShiftHour) - 1]->work_hour_till;
        $this->machine = MsMachine::where('machineno',  'LIKE', '00S%')->get();
        $this->department = MsDepartment::where('division_code', 20)->get();
        $this->products = MsProduct::get();
    }

    public function print()
    {
        if ($this->jenisReport == 2) {
            $tglAwal = $this->tglAwal;
            $tglAkhir = $this->tglAkhir;

            $this->dispatch('printSeitai', "tdpg.created_on >= '$tglAwal 00:00' and tdpg.created_on <= '$tglAkhir 23:59'");
        } else {
            $tglAwal = $this->tglAwal;
            $tglAkhir = $this->tglAkhir;

            $this->dispatch('printNippo', "tdpg.created_on >= '$tglAwal 00:00' and tdpg.created_on <= '$tglAkhir 23:59'");
        }
    }

    public function export()
    {
        if ($this->jenisReport == 'CheckList') {
            $response = $this->checklist();
            if ($response['status'] == 'success') {
                return response()->download($response['filename'])->deleteFileAfterSend(true);
            } else if ($response['status'] == 'error') {
                $this->dispatch('notification', ['type' => 'warning', 'message' => $response['message']]);
                return;
            }
        } else if ($this->jenisReport == 'LossSeitai') {
            $response = $this->loss();
            if ($response['status'] == 'success') {
                return response()->download($response['filename'])->deleteFileAfterSend(true);
            } else if ($response['status'] == 'error') {
                $this->dispatch('notification', ['type' => 'warning', 'message' => $response['message']]);
                return;
            }
        }
    }

    public function checklist($isNippo = false, $filter = null)
    {
        if ($isNippo) {
            $this->tglAwal = $filter['tglAwal'];
            $this->tglAkhir = $filter['tglAkhir'];
            $this->jamAwal = $filter['jamAwal'];
            $this->jamAkhir = $filter['jamAkhir'];
            $this->transaksi = $filter['transaksi'];
            $this->lpk_no = $filter['lpk_no'];
            $this->machineId = $filter['machineId'];
            $this->productId = $filter['idProduct'];
            $this->status = $filter['status'];
            $this->gentan_no = $filter['gentan_no'];
            $this->searchTerm = $filter['searchTerm'];
            $this->jenisReport = $filter['jenisReport'];
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

        if ($this->transaksi == 'produksi') {
            // Buat field tanggal dengan jam yang digabung dari kolom work_hour
            $fieldDate = "(tdpg.production_date::date || ' ' || tdpg.work_hour)::timestamp";

            // Filter berdasarkan datetime hasil gabungan
            $filterDate = "$fieldDate BETWEEN '$tglAwal' AND '$tglAkhir'";
        } else {
            $fieldDate = 'tdpg.created_on';
            $filterDate = "tdpg.created_on BETWEEN '$tglAwal' AND '$tglAkhir'";
        }
        $filterNoLPK = $this->lpk_no ? " AND (tdol.lpk_no = '$this->lpk_no')" : '';
        $filterNoproses = $this->noprosesawal ? " AND (tdpg.seq_no >= '$this->noprosesawal')" : '';
        $filterNoproses .= $this->noprosesakhir ? " AND (tdpg.seq_no <= '$this->noprosesakhir')" : '';
        $filterNoOrder = $this->noorder ? " AND (mp.code = '$this->noorder')" : '';
        $this->departmentId = $this->departmentId ? (is_array($this->departmentId) ? $this->departmentId['value'] : $this->departmentId) : '';
        $filterDepartment = $this->departmentId ? " AND (mm.department_id = '$this->departmentId')" : '';
        $this->machineId = $this->machineId ? (is_array($this->machineId) ? $this->machineId['value'] : $this->machineId) : '';
        $filterMachine = $this->machineId ? " AND (tdpg.machine_id = '$this->machineId')" : '';
        $filterNomorPalet = $this->nomorPalet ? " AND (tdpg.nomor_palet = '$this->nomorPalet')" : '';
        $filterNomorLot = $this->nomorLot ? " AND (tdpg.nomor_lot = '$this->nomorLot')" : '';
        $this->productId = $this->productId ? (is_array($this->productId) ? $this->productId['value'] : $this->productId) : '';
        $filterProduct = $this->productId ? " AND (tdpg.product_id = '$this->productId')" : '';

        $filterStatus = '';
        $filterSearchTerm = '';
        if ($isNippo) {
            $filterStatus = $this->status == 0 ? " AND (tdpg.status_production = 0 AND tdpg.status_warehouse = 0)" : ($this->status == 1 ? " AND (tdpg.status_production = 1)" : " AND (tdpg.status_warehouse = 1)");
            $filterSearchTerm = $this->searchTerm ? " AND (tdpg.production_no ILIKE '%$this->searchTerm%' OR  mp.code ILIKE '%$this->searchTerm%' OR mp.name ILIKE '%$this->searchTerm%' OR tdpa.machine_id ILIKE '%$this->searchTerm%' OR nomor_lot.nomor_lot ILIKE '%$this->searchTerm%')" : '';
        }

        if ($this->jenisReport == 'CheckList') {
            $data = DB::select("
                WITH goodasy AS (
                    SELECT
                        tpga.product_goods_id,
                        tdpa.gentan_no AS gentannomor,
                        tdpa.gentan_no || '-' || tpga.gentan_line AS gentannomorline,
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
                        INNER JOIN msDepartment AS msd ON msd.ID = msm.department_id
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
                    tdpg.ID as id_tdpg,
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
                    lossgoods.code as losscode,
                    lossgoods.namaloss as lossname,
                    lossgoods.berat_loss,
                    goodasy.gentannomor,
                    goodasy.gentannomorline,
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
                    ID INNER JOIN msDepartment AS msd ON msd.ID = mm.department_id
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
                    $filterProduct
                    $filterStatus
                    $filterSearchTerm
                ORDER BY mm.machineno, $fieldDate, tdpg.work_shift, tdpg.seq_no
                ");
        } else {
            $data = DB::select("
                WITH goodasy AS (
                    SELECT
                        tpga.product_goods_id,
                        tdpa.gentan_no AS gentannomor,
                        tdpa.gentan_no || '-' || tpga.gentan_line AS gentannomorline,
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
                        INNER JOIN msDepartment AS msd ON msd.ID = msm.department_id
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
                    tdpg.ID as id_tdpg,
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
                    lossgoods.code as losscode,
                    lossgoods.namaloss as lossname,
                    lossgoods.berat_loss,
                    goodasy.gentannomor,
                    goodasy.gentannomorline,
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
                    INNER JOIN lossgoods ON tdpg.ID = lossgoods.product_goods_id
                    INNER JOIN tdOrderLpk AS tdol ON tdpg.lpk_id = tdol.
                    ID INNER JOIN msmachine AS mm ON mm.ID = tdpg.machine_id
                    INNER JOIN msemployee AS maPetugas ON maPetugas.ID = tdpg.employee_id
                    LEFT JOIN msemployee AS maInfure ON tdpg.employee_id_infure = maInfure.
                    ID INNER JOIN msDepartment AS msd ON msd.ID = mm.department_id
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
                    $filterProduct
                ORDER BY mm.machineno, $fieldDate, tdpg.work_shift, tdpg.seq_no
                ");
        }

        $dataFiltered = [];
        $dataLoss = [];
        $dataGentan = [];

        foreach ($data as $item) {
            $tglProduksi = $item->tglproduksi;

            // Data Utama
            if (!isset($dataFiltered[$item->id_tdpg])) {
                $dataFiltered[$item->id_tdpg] = [
                    'tglproses' => $item->tglproses,
                    'tglproduksi' => $item->tglproduksi,
                    'shift' => $item->work_shift,
                    'nikpetugas' => $item->nikpetugas,
                    'namapetugas' => $item->namapetugas,
                    'mesinno' => $item->mesinno,
                    'mesinnama' => $item->mesinnama,
                    'nolpk' => $item->nolpk,
                    'namaproduk' => $item->namaproduk,
                    'noorder' => $item->noorder,
                    'qty_produksi' => $item->qty_produksi,
                    'infure_berat_loss' => $item->infure_berat_loss,
                    'nikpetugasinfure' => $item->nikpetugasinfure,
                    'nomor_palet' => $item->nomor_palet,
                    'nomor_lot' => $item->nomor_lot,
                    'noproses' => $item->noproses,
                ];
            }

            // Gentan No
            if (!isset($dataGentan[$item->id_tdpg][$item->gentannomor])) {
                $dataGentan[$item->id_tdpg][$item->gentannomor] = (object)[
                    'gentannomorline' => $item->gentannomorline,
                ];
            }

            // Data Loss
            if (!isset($dataLoss[$item->id_tdpg][$item->losscode])) {
                $dataLoss[$item->id_tdpg][$item->losscode] = (object)[
                    'losscode' => $item->losscode,
                    'lossname' => $item->lossname,
                    'berat_loss' => $item->berat_loss,
                ];
            }
        }

        /**
         * Mengatur halaman
         */
        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();
        // Menghilangkan gridline
        $activeWorksheet->setShowGridlines(false);
        $activeWorksheet->freezePane('A5');
        // Mengatur ukuran kertas menjadi A4
        $activeWorksheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
        // Mengatur orientasi menjadi landscape
        $activeWorksheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        // Mengatur agar semua kolom muat dalam satu halaman
        $activeWorksheet->getPageSetup()->setFitToWidth(1);
        $activeWorksheet->getPageSetup()->setFitToHeight(0); // Biarkan tinggi menyesuaikan otomatis

        // Set header berulang untuk print
        $activeWorksheet->getPageSetup()->setRowsToRepeatAtTop([1, 4]);
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

        // Judul
        $startColumn = 'A';
        $endColumn = 'L';
        $rowTitleCardStart = 1;
        $rowTitleCardEnd = 2;
        $spreadsheet->getActiveSheet()->mergeCells($startColumn . $rowTitleCardStart . ':' . $endColumn . $rowTitleCardStart);
        $activeWorksheet->setCellValue($startColumn . $rowTitleCardStart, 'CHECK LIST NIPPO SEITAI');
        $spreadsheet->getActiveSheet()->mergeCells($startColumn . $rowTitleCardEnd . ':' . $endColumn . $rowTitleCardEnd);
        $activeWorksheet->setCellValue($startColumn . $rowTitleCardEnd, 'Tanggal ' . ucfirst($this->transaksi) . ' : ' . $tglAwal . ' s/d ' . $tglAkhir);
        phpspreadsheet::styleFont($spreadsheet, $startColumn . $rowTitleCardStart . ':' . $startColumn . $rowTitleCardEnd, true, 11, 'Calibri');

        /* Header */
        $rowHeaderStart = 3;
        $rowHeaderEnd = 4;

        // Header configuration array
        $headers = [
            'A' => ['text' => 'Nomor Mesin', 'merged' => true],
            'B' => ['text' => ['Tanggal Produksi', 'Shift'], 'merged' => false],
            'C' => ['text' => ['Tanggal Proses', 'No. Proses'], 'merged' => false],
            'D' => ['text' => ['NIK', 'Petugas'], 'merged' => false],
            'E' => ['text' => 'Nomor LPK', 'merged' => true],
            'F' => ['text' => ['Nama Produk', 'Nomor Order'], 'merged' => false],
            'G' => ['text' => 'Quantity (Lembar)', 'merged' => true],
            'H' => ['text' => ['Loss Infure', 'NIK'], 'merged' => false],
            'I' => ['text' => ['Nomor Palet', 'Nomor LOT'], 'merged' => false],
            'J' => ['text' => 'Nomor Gentan', 'merged' => true],
            'K' => ['text' => 'Nama Loss', 'merged' => true],
            'L' => ['text' => 'Berat (Kg)', 'merged' => true]
        ];
        $columnMesin = 'A';
        $columnProduksi = 'B';
        $columnProses = 'C';
        $columnPetugas = 'D';
        $columnLpk = 'E';
        $columnProduk = 'F';
        $columnQty = 'G';
        $columnLoss = 'H';
        $columnPalet = 'I';
        $columnGentan = 'J';
        $columnNamaLoss = 'K';
        $columnBerat = 'L';

        // Generate headers
        foreach ($headers as $column => $config) {
            if ($config['merged']) {
                $range = $column . $rowHeaderStart . ':' . $column . $rowHeaderEnd;
                // Merged cells (single text)
                $spreadsheet->getActiveSheet()->mergeCells($range);
                $activeWorksheet->setCellValue($column . $rowHeaderStart, $config['text']);
            } else {
                // Non-merged cells (two texts)
                $activeWorksheet->setCellValue($column . $rowHeaderStart, $config['text'][0]);
                $activeWorksheet->setCellValue($column . $rowHeaderEnd, $config['text'][1]);
            }
        }

        // Apply styling to headers
        $headerRange = $startColumn . $rowHeaderStart . ':L' . $rowHeaderEnd;
        $activeWorksheet->getStyle($headerRange)->getAlignment()->setWrapText(true);
        phpspreadsheet::addFullBorder($spreadsheet, $headerRange);
        phpspreadsheet::styleFont($spreadsheet, $headerRange, true, 9, 'Calibri');
        phpspreadsheet::textAlignCenter($spreadsheet, $headerRange);

        /**
         * Header Value
         *  */
        $rowDataStart = 5;
        $rowItemStart = 5;
        $rowItemEnd = 6;

        foreach ($dataFiltered as $id_tdpg => $item) {
            // Data mapping configuration
            $cellData = [
                // Row 1 data
                $rowItemStart => [
                    $columnMesin => ['value' => $item['mesinno'], 'align' => 'center', 'wrap' => true],
                    $columnProduksi => ['value' => Carbon::parse($item['tglproduksi'])->format('d-M-Y'), 'align' => 'center'],
                    $columnProses => ['value' => Carbon::parse($item['tglproses'])->format('d-M-Y'), 'align' => 'center'],
                    $columnPetugas => ['value' => $item['nikpetugas'], 'align' => 'center'],
                    $columnLpk => ['value' => $item['nolpk'], 'align' => 'center'],
                    $columnProduk => ['value' => $item['namaproduk'], 'align' => 'center', 'wrap' => true],
                    $columnQty => ['value' => $item['qty_produksi'], 'format' => 'thousands'],
                    $columnLoss => ['value' => $item['infure_berat_loss']],
                    $columnPalet => ['value' => $item['nomor_palet'], 'align' => 'center', 'wrap' => true],
                ],
                // Row 2 data
                $rowItemEnd => [
                    $columnProduksi => ['value' => $item['shift'], 'align' => 'center'],
                    $columnProses => ['value' => $item['noproses'], 'align' => 'center'],
                    $columnPetugas => ['value' => $item['namapetugas'], 'align' => 'center', 'merge' => $columnLpk],
                    $columnProduk => ['value' => $item['noorder'], 'align' => 'center'],
                    $columnLoss => ['value' => $item['nikpetugasinfure']],
                    $columnPalet => ['value' => $item['nomor_lot'], 'align' => 'center'],
                ]
            ];

            // Apply cell data and styling
            foreach ($cellData as $row => $columns) {
                foreach ($columns as $column => $config) {
                    // Set cell value
                    $activeWorksheet->setCellValue($column . $row, $config['value']);

                    // Handle merge if specified
                    if (isset($config['merge'])) {
                        $spreadsheet->getActiveSheet()->mergeCells($column . $row . ':' . $config['merge'] . $row);
                    }
                }
            }

            // Main data border
            phpspreadsheet::addFullBorder($spreadsheet, $startColumn . $rowItemStart . ':' . $columnPalet . $rowItemEnd);

            // Handle Gentan data
            $rowGentan = $rowItemStart;
            foreach ($dataGentan[$id_tdpg] as $gentan) {
                $activeWorksheet->setCellValue($columnGentan . $rowGentan, $gentan->gentannomorline);
                $rowGentan++;
            }
            $rowGentan--;

            // Handle Loss data
            $rowLoss = $rowItemStart;
            foreach ($dataLoss[$id_tdpg] as $itemLoss) {
                $activeWorksheet->setCellValue($columnNamaLoss . $rowLoss, $itemLoss->losscode . '. ' . $itemLoss->lossname);
                $activeWorksheet->setCellValue($columnBerat . $rowLoss, $itemLoss->berat_loss);
                $rowLoss++;
            }
            $rowLoss--;

            // Calculate next row positions
            $rowBorderStart = $rowItemStart;
            if (count($dataGentan[$id_tdpg]) < 2 && count($dataLoss[$id_tdpg]) < 2) {
                $rowBorderEnd = $rowItemStart + 1;
                $rowItemStart = $rowItemStart + 3;
            } else {
                $rowItemStart = ($rowLoss < $rowGentan) ? $rowGentan + 2 : $rowLoss + 2;
                $rowBorderEnd = ($rowLoss < $rowGentan) ? $rowGentan : $rowLoss;
            }

            // Final border
            phpspreadsheet::addFullBorder($spreadsheet, $columnGentan . $rowBorderStart . ':' . $columnBerat . $rowBorderEnd);

            $rowItemEnd = $rowItemStart + 1;
        }

        // Apply styles to the entire data range
        phpspreadsheet::styleFont($spreadsheet, $startColumn . $rowDataStart . ':' . $columnBerat . $rowItemEnd, false, 8, 'Calibri');
        $activeWorksheet->getStyle($columnNamaLoss . $rowDataStart . ':' . $columnNamaLoss . $rowItemEnd)->getAlignment()->setWrapText(true);
        phpspreadsheet::textAlignCenter($spreadsheet, $startColumn . $rowDataStart . ':' . $columnProduk . $rowItemEnd);
        phpspreadsheet::textAlignCenter($spreadsheet, $columnPalet . $rowDataStart . ':' . $columnGentan . $rowItemEnd);
        phpSpreadsheet::numberFormatThousands($spreadsheet, $columnQty . $rowDataStart . ':' . $columnQty . $rowItemEnd);


        // Grand Total
        $rowGrandTotal = $rowItemEnd + 1;
        $columnGrandTotalEnd = 'F';
        // merge
        $spreadsheet->getActiveSheet()->mergeCells('A' . $rowGrandTotal . ':' . $columnGrandTotalEnd . $rowGrandTotal);
        $activeWorksheet->setCellValue($startColumn . $rowGrandTotal, 'Grand Total');
        $columnGrandTotalEnd++;

        // total quantity
        $totalQty = array_sum(array_column($dataFiltered, 'qty_produksi'));
        $activeWorksheet->setCellValue($columnQty . $rowGrandTotal, $totalQty);
        $columnGrandTotalEnd++;

        // total loss
        $totalLoss = array_sum(array_column($dataFiltered, 'infure_berat_loss'));
        $activeWorksheet->setCellValue($columnLoss . $rowGrandTotal, $totalLoss);
        phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnQty . $rowGrandTotal . ':' . $columnLoss . $rowGrandTotal);
        $columnGrandTotalEnd++;

        // berat loss
        $columnBerat = 'K';
        $spreadsheet->getActiveSheet()->mergeCells($columnGrandTotalEnd . $rowGrandTotal . ':' . $columnBerat . $rowGrandTotal);
        $columnBerat++;
        $totalBeratLoss = 0;
        foreach ($dataLoss as $items) {
            foreach ($items as $item) {
                $totalBeratLoss += (float) ($item->berat_loss ?? 0);
            }
        }
        $activeWorksheet->setCellValue($columnBerat . $rowGrandTotal, $totalBeratLoss);
        phpspreadsheet::addFullBorder($spreadsheet, 'A' . $rowGrandTotal . ':' . $columnBerat . $rowGrandTotal);
        phpSpreadsheet::numberFormatCommaThousandsOrZero($spreadsheet, $columnBerat . $rowGrandTotal);

        phpspreadsheet::styleFont($spreadsheet, 'A' . $rowGrandTotal . ':' . $columnBerat . $rowGrandTotal, true, 9, 'Calibri');

        $startColumn++;

        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(15.0);
        $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(10.0);
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(10.0);
        $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(10.0);
        $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(10.0);
        $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(25.0);
        $spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(10.5);
        $spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(7.5);
        $spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(15.0);
        $spreadsheet->getActiveSheet()->getColumnDimension('J')->setWidth(6.0);
        $spreadsheet->getActiveSheet()->getColumnDimension('K')->setWidth(15.0);
        $spreadsheet->getActiveSheet()->getColumnDimension('L')->setWidth(8.2);

        $writer = new Xlsx($spreadsheet);
        $filename = 'asset/report/NippoSeitai-' . $this->jenisReport . '.xlsx';
        $writer->save($filename);
        $response = [
            'status' => 'success',
            'filename' => $filename
        ];
        return $response;
    }

    public function loss()
    {
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

        if ($this->transaksi == 'produksi') {
            $fieldDate = 'tdpg.production_date';
            $filterDate = "tdpg.production_date BETWEEN '$tglAwal' AND '$tglAkhir'";
        } else {
            $fieldDate = 'tdpg.created_on';
            $filterDate = "tdpg.created_on BETWEEN '$tglAwal' AND '$tglAkhir'";
        }
        $filterNoLPK = $this->lpk_no ? " AND (tdol.lpk_no = '$this->lpk_no')" : '';
        $filterNoproses = $this->noprosesawal ? " AND (tdpg.seq_no >= '$this->noprosesawal')" : '';
        $filterNoproses .= $this->noprosesakhir ? " AND (tdpg.seq_no <= '$this->noprosesakhir')" : '';
        $filterNoOrder = $this->noorder ? " AND (mp.code = '$this->noorder')" : '';
        $this->departmentId = $this->departmentId ? (is_array($this->departmentId) ? $this->departmentId['value'] : $this->departmentId) : '';
        $filterDepartment = $this->departmentId ? " AND (mm.department_id = '$this->departmentId')" : '';
        $this->machineId = $this->machineId ? (is_array($this->machineId) ? $this->machineId['value'] : $this->machineId) : '';
        $filterMachine = $this->machineId ? " AND (tdpg.machine_id = '$this->machineId')" : '';
        $filterNomorPalet = $this->nomorPalet ? " AND (tdpg.nomor_palet = '$this->nomorPalet')" : '';
        $filterNomorLot = $this->nomorLot ? " AND (tdpg.nomor_lot = '$this->nomorLot')" : '';
        $this->productId = $this->productId ? (is_array($this->productId) ? $this->productId['value'] : $this->productId) : '';
        $filterProduct = $this->productId ? " AND (tdpg.product_id = '$this->productId')" : '';

        $data = DB::select("
                WITH
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
                    tdpg.ID as id_tdpg,
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
                    lossgoods.code as losscode,
                    lossgoods.namaloss as lossname,
                    lossgoods.berat_loss
                    -- goodasy.gentannomor,
                    -- goodasy.gentannomorline,
                    -- goodasy.panjang_produksi,
                    -- goodasy.tglproduksi AS tglproduksiasy,
                    -- goodasy.work_shift AS work_shiftasy,
                    -- goodasy.work_hour AS work_hourasy,
                    -- goodasy.nomesin AS nomesinasy,
                    -- goodasy.nomor_han,
                    -- goodasy.nik AS nikasy,
                    -- goodasy.namapetugas AS namapetugasasy,
                    -- goodasy.deptpetugas AS deptpetugasasy
                FROM
                    tdProduct_Goods AS tdpg
                    INNER JOIN lossgoods ON tdpg.ID = lossgoods.product_goods_id
                    LEFT JOIN tdOrderLpk AS tdol ON tdpg.lpk_id = tdol.
                    ID LEFT JOIN msmachine AS mm ON mm.ID = tdpg.machine_id
                    LEFT JOIN msemployee AS maPetugas ON maPetugas.ID = tdpg.employee_id
                    LEFT JOIN msemployee AS maInfure ON tdpg.employee_id_infure = maInfure.
                    ID LEFT JOIN msDepartment AS msd ON msd.ID = mm.department_id
                    LEFT JOIN msProduct AS mp ON mp.ID = tdpg.product_id
                WHERE
                    $filterDate
                    $filterNoproses
                    $filterNoLPK
                    $filterNoOrder
                    $filterMachine
                    $filterNomorPalet
                    $filterDepartment
                    $filterNomorLot
                    $filterProduct
                ORDER BY $fieldDate, tdpg.seq_no
                ");

        $dataFiltered = [];
        $dataLoss = [];
        $dataGentan = [];


        $dataFiltered = [];
        $dataLoss = [];
        $dataGentan = [];

        foreach ($data as $item) {
            $tglProduksi = $item->tglproduksi;

            // Data Utama
            if (!isset($dataFiltered[$item->tglproduksi][$item->id_tdpg])) {
                $dataFiltered[$item->tglproduksi][$item->id_tdpg] = [
                    'tglproses' => $item->tglproses,
                    'tglproduksi' => $item->tglproduksi,
                    'shift' => $item->work_shift,
                    'nikpetugas' => $item->nikpetugas,
                    'namapetugas' => $item->namapetugas,
                    'mesinno' => $item->mesinno,
                    'mesinnama' => $item->mesinnama,
                    'nolpk' => $item->nolpk,
                    'namaproduk' => $item->namaproduk,
                    'noorder' => $item->noorder,
                    'qty_produksi' => $item->qty_produksi,
                    'infure_berat_loss' => $item->infure_berat_loss,
                    'nikpetugasinfure' => $item->nikpetugasinfure,
                    'nomor_palet' => $item->nomor_palet,
                    'nomor_lot' => $item->nomor_lot,
                    'noproses' => $item->noproses,
                    // 'tglproduksiasy' => $item->tglproduksiasy,
                ];
            }

            // Data Loss
            if (!isset($dataLoss[$item->tglproduksi][$item->id_tdpg][$item->losscode])) {
                $dataLoss[$item->tglproduksi][$item->id_tdpg][$item->losscode] = (object)[
                    'losscode' => $item->losscode,
                    'lossname' => $item->lossname,
                    'berat_loss' => $item->berat_loss,
                ];
            }
        }

        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();
        // Menghilangkan gridline
        $activeWorksheet->setShowGridlines(false);
        $activeWorksheet->freezePane('A5');

        // Mengatur ukuran kertas menjadi A4
        $activeWorksheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
        // Mengatur orientasi menjadi landscape
        $activeWorksheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        // Mengatur agar semua kolom muat dalam satu halaman
        $activeWorksheet->getPageSetup()->setFitToWidth(1);
        $activeWorksheet->getPageSetup()->setFitToHeight(0); // Biarkan tinggi menyesuaikan otomatis
        // Set header berulang untuk print
        $activeWorksheet->getPageSetup()->setRowsToRepeatAtTop([1, 4]);

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

        // Judul
        $startColumn = 'A';
        $endColumn = 'L';
        $rowTitleCardStart = 1;
        $rowTitleCardEnd = 2;
        $spreadsheet->getActiveSheet()->mergeCells($startColumn . $rowTitleCardStart . ':' . $endColumn . $rowTitleCardStart);
        $activeWorksheet->setCellValue($startColumn . $rowTitleCardStart, 'CHECKLIST LOSS SEITAI');
        $spreadsheet->getActiveSheet()->mergeCells($startColumn . $rowTitleCardEnd . ':' . $endColumn . $rowTitleCardEnd);
        $activeWorksheet->setCellValue($startColumn . $rowTitleCardEnd, 'Tanggal ' . ucwords($this->transaksi) . ' : ' . $tglAwal . ' s/d ' . $tglAkhir);
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

        // Nomor LPK
        $columnLpk = 'C';
        $spreadsheet->getActiveSheet()->mergeCells($columnLpk . $rowHeaderStart . ':' . $columnLpk . $rowHeaderEnd);
        $activeWorksheet->setCellValue($columnLpk . $rowHeaderStart, 'Nomor LPK');
        phpspreadsheet::styleFont($spreadsheet, $columnLpk . $rowHeaderStart . ':' . $columnLpk . $rowHeaderEnd, true, 9, 'Calibri');
        phpspreadsheet::textAlignCenter($spreadsheet, $columnLpk . $rowHeaderStart . ':' . $columnLpk . $rowHeaderEnd);

        // Nama Produk
        $columnProduk = 'D';
        $activeWorksheet->setCellValue($columnProduk . $rowHeaderStart, 'Nama Produk');
        $activeWorksheet->setCellValue($columnProduk . $rowHeaderEnd, 'Nomor Order');
        phpspreadsheet::styleFont($spreadsheet, $columnProduk . $rowHeaderStart . ':' . $columnProduk . $rowHeaderEnd, true, 9, 'Calibri');
        phpspreadsheet::textAlignCenter($spreadsheet, $columnProduk . $rowHeaderStart . ':' . $columnProduk . $rowHeaderEnd);

        // Nomor mesin
        $columnMesin = 'E';
        $spreadsheet->getActiveSheet()->mergeCells($columnMesin . $rowHeaderStart . ':' . $columnMesin . $rowHeaderEnd);
        $activeWorksheet->setCellValue($columnMesin . $rowHeaderStart, 'Nomor Mesin');
        phpspreadsheet::styleFont($spreadsheet, $columnMesin . $rowHeaderStart . ':' . $columnMesin . $rowHeaderEnd, true, 9, 'Calibri');
        phpspreadsheet::textAlignCenter($spreadsheet, $columnMesin . $rowHeaderStart . ':' . $columnMesin . $rowHeaderEnd);
        $activeWorksheet->getStyle($columnMesin . $rowHeaderStart)->getAlignment()->setWrapText(true);

        // petugas
        $columnPetugas = 'F';
        $activeWorksheet->setCellValue($columnPetugas . $rowHeaderStart, 'NIK');
        $activeWorksheet->setCellValue($columnPetugas . $rowHeaderEnd, 'Petugas');
        phpspreadsheet::styleFont($spreadsheet, $columnPetugas . $rowHeaderStart . ':' . $columnPetugas . $rowHeaderEnd, true, 9, 'Calibri');
        phpspreadsheet::textAlignCenter($spreadsheet, $columnPetugas . $rowHeaderStart . ':' . $columnPetugas . $rowHeaderEnd);


        // Nama Loss
        $columnNamaLoss = 'G';
        $spreadsheet->getActiveSheet()->mergeCells($columnNamaLoss . $rowHeaderStart . ':' . $columnNamaLoss . $rowHeaderEnd);
        $activeWorksheet->setCellValue($columnNamaLoss . $rowHeaderStart, 'Nama Loss');
        phpspreadsheet::styleFont($spreadsheet, $columnNamaLoss . $rowHeaderStart . ':' . $columnNamaLoss . $rowHeaderEnd, true, 9, 'Calibri');
        phpspreadsheet::textAlignCenter($spreadsheet, $columnNamaLoss . $rowHeaderStart . ':' . $columnNamaLoss . $rowHeaderEnd);

        // kode loss
        $columnKodeLoss = 'H';
        $spreadsheet->getActiveSheet()->mergeCells($columnKodeLoss . $rowHeaderStart . ':' . $columnKodeLoss . $rowHeaderEnd);
        $activeWorksheet->setCellValue($columnKodeLoss . $rowHeaderStart, 'Kode Loss');
        phpspreadsheet::styleFont($spreadsheet, $columnKodeLoss . $rowHeaderStart . ':' . $columnKodeLoss . $rowHeaderEnd, true, 9, 'Calibri');
        phpspreadsheet::textAlignCenter($spreadsheet, $columnKodeLoss . $rowHeaderStart . ':' . $columnKodeLoss . $rowHeaderEnd);
        $activeWorksheet->getStyle($columnKodeLoss . $rowHeaderStart)->getAlignment()->setWrapText(true);

        // Berat
        $columnBerat = 'I';
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
        foreach ($dataFiltered as $productionDate => $dataItem) {
            foreach ($dataItem as $id_tdpg => $item) {
                // Tanggal Proses
                $activeWorksheet->setCellValue($startColumn . $rowItemStart, Carbon::parse($item['tglproses'])->format('d-M-Y'));
                phpspreadsheet::styleFont($spreadsheet, $startColumn . $rowItemStart, false, 8, 'Calibri');
                phpspreadsheet::textAlignCenter($spreadsheet, $startColumn . $rowItemStart);
                // No Proses
                $activeWorksheet->setCellValue($startColumn . $rowItemEnd, $item['noproses']);
                phpspreadsheet::styleFont($spreadsheet, $startColumn . $rowItemEnd, false, 8, 'Calibri');
                phpspreadsheet::textAlignCenter($spreadsheet, $startColumn . $rowItemEnd);
                // Tangga Produksi
                $activeWorksheet->setCellValue($columnProduksi . $rowItemStart, Carbon::parse($item['tglproduksi'])->format('d-M-Y'));
                phpspreadsheet::styleFont($spreadsheet, $columnProduksi . $rowItemStart, false, 8, 'Calibri');
                phpspreadsheet::textAlignCenter($spreadsheet, $columnProduksi . $rowItemStart);
                // Shift
                $activeWorksheet->setCellValue($columnProduksi . $rowItemEnd, $item['shift']);
                phpspreadsheet::styleFont($spreadsheet, $columnProduksi . $rowItemEnd, false, 8, 'Calibri');
                phpspreadsheet::textAlignCenter($spreadsheet, $columnProduksi . $rowItemEnd);
                // Nomor LPK
                $activeWorksheet->setCellValue($columnLpk . $rowItemStart, $item['nolpk']);
                phpspreadsheet::styleFont($spreadsheet, $columnLpk . $rowItemStart, false, 8, 'Calibri');
                phpspreadsheet::textAlignCenter($spreadsheet, $columnLpk . $rowItemStart);
                // Nama Produk
                $activeWorksheet->setCellValue($columnProduk . $rowItemStart, $item['namaproduk']);
                phpspreadsheet::styleFont($spreadsheet, $columnProduk . $rowItemStart, false, 8, 'Calibri');
                // phpspreadsheet::textAlignCenter($spreadsheet, $columnProduk . $rowItemStart);
                // Nomor Order
                $activeWorksheet->setCellValue($columnProduk . $rowItemEnd, $item['noorder']);
                phpspreadsheet::styleFont($spreadsheet, $columnProduk . $rowItemEnd, false, 8, 'Calibri');
                phpspreadsheet::textAlignCenter($spreadsheet, $columnProduk . $rowItemEnd);
                // Nomor Mesin
                $activeWorksheet->setCellValue($columnMesin . $rowItemStart, $item['mesinno']);
                phpspreadsheet::styleFont($spreadsheet, $columnMesin . $rowItemStart, false, 8, 'Calibri');
                phpspreadsheet::textAlignCenter($spreadsheet, $columnMesin . $rowItemStart);
                // NIK
                $activeWorksheet->setCellValue($columnPetugas . $rowItemStart, $item['nikpetugas']);
                phpspreadsheet::styleFont($spreadsheet, $columnPetugas . $rowItemStart, false, 8, 'Calibri');
                phpspreadsheet::textAlignCenter($spreadsheet, $columnPetugas . $rowItemStart);
                // Petugas
                $activeWorksheet->setCellValue($columnPetugas . $rowItemEnd, $item['namapetugas']);
                // $spreadsheet->getActiveSheet()->mergeCells($columnPetugas . $rowItemEnd . ':' . $columnLpk . $rowItemEnd);
                phpspreadsheet::styleFont($spreadsheet, $columnPetugas . $rowItemEnd, false, 8, 'Calibri');
                // phpspreadsheet::textAlignCenter($spreadsheet, $columnPetugas . $rowItemEnd);

                // border
                phpspreadsheet::addFullBorder($spreadsheet, $startColumn . $rowItemStart . ':' . $columnPetugas . $rowItemEnd);

                // Nama Loss
                $rowLoss = $rowItemStart;
                foreach ($dataLoss[$productionDate][$id_tdpg] as $itemLoss) {
                    // Nama Loss
                    $activeWorksheet->setCellValue($columnNamaLoss . $rowLoss, $itemLoss->lossname);
                    phpspreadsheet::styleFont($spreadsheet, $columnNamaLoss . $rowLoss, false, 8, 'Calibri');
                    $activeWorksheet->getStyle($columnNamaLoss . $rowLoss)->getAlignment()->setWrapText(true);
                    // kode loss
                    $activeWorksheet->setCellValue($columnKodeLoss . $rowLoss, $itemLoss->losscode);
                    phpspreadsheet::styleFont($spreadsheet, $columnKodeLoss . $rowLoss, false, 8, 'Calibri');
                    // Berat
                    $activeWorksheet->setCellValue($columnBerat . $rowLoss, $itemLoss->berat_loss);
                    phpspreadsheet::styleFont($spreadsheet, $columnBerat . $rowLoss, false, 8, 'Calibri');
                    $rowLoss++;
                }

                // border
                phpspreadsheet::addFullBorder($spreadsheet, $columnNamaLoss . $rowItemStart . ':' . $columnBerat . $rowLoss);

                $rowItemStart = $rowLoss + 2;
                $rowItemEnd = $rowItemStart + 1;
            }
        }

        // Grand Total
        $rowGrandTotal = $rowItemEnd;
        $columnGrandTotalEnd = 'F';
        // merge
        $spreadsheet->getActiveSheet()->mergeCells('A' . $rowGrandTotal . ':' . $columnGrandTotalEnd . $rowGrandTotal);
        $activeWorksheet->setCellValue($startColumn . $rowGrandTotal, 'Grand Total');
        $columnGrandTotalEnd++;

        // berat loss
        $columnBerat = 'H';
        $spreadsheet->getActiveSheet()->mergeCells($columnGrandTotalEnd . $rowGrandTotal . ':' . $columnBerat . $rowGrandTotal);
        $columnBerat++;
        $totalBeratLoss = 0;
        foreach ($dataLoss as $productionDate) {
            foreach ($productionDate as $productGoods) {
                foreach ($productGoods as $itemLoss) {
                    $totalBeratLoss += $itemLoss->berat_loss;
                }
            }
        }
        $activeWorksheet->setCellValue($columnBerat . $rowGrandTotal, $totalBeratLoss);
        phpspreadsheet::addFullBorder($spreadsheet, 'A' . $rowGrandTotal . ':' . $columnBerat . $rowGrandTotal);
        // phpSpreadsheet::numberFormatCommaThousandsOrZero($spreadsheet, $columnQty . $rowGrandTotal);

        phpspreadsheet::styleFont($spreadsheet, 'A' . $rowGrandTotal . ':' . $columnBerat . $rowGrandTotal, true, 9, 'Calibri');

        // size auto
        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(15.0);
        $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(15.0);
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(12.0);
        $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(27.0);
        $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(12.0);
        $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(15.0);
        $spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(25.0);
        // $activeWorksheet->getStyle('G' . $rowHeaderStart)->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(10.5);
        $spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(12.0);

        $writer = new Xlsx($spreadsheet);
        $filename = 'NippoSeitai-' . $this->jenisReport . '.xlsx';
        $writer->save($filename);
        $response = [
            'status' => 'success',
            'filename' => $filename
        ];
        return $response;
    }

    public function render()
    {
        return view('livewire.nippo-seitai.check-list-seitai')->extends('layouts.master');
    }
}
