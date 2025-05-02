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
use App\Models\MsProduct;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PHPUnit\TextUI\Configuration\Php;

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
    public $products;
    public $productId;
    public $status;
    public $searchTerm;

    public function mount()
    {
        $this->tglAwal = Carbon::now()->format('Y-m-d');
        $this->tglAkhir = Carbon::now()->format('Y-m-d');
        $this->workingShiftHour = MsWorkingShift::select('id', 'work_hour_from', 'work_hour_till')->where('status', 1)->orderBy('work_hour_from', 'ASC')->get();
        $this->jamAwal = $this->workingShiftHour[0]->work_hour_from;
        $this->jamAkhir = $this->workingShiftHour[count($this->workingShiftHour) - 1]->work_hour_till;
        $this->machine = MsMachine::where('machineno',  'LIKE', '00I%')->orderBy('machineno')->get();
        $this->department = MsDepartment::where('division_code', 10)->get();
        $this->products = MsProduct::get();
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

        $response = $this->checklistInfure($tglAwal, $tglAkhir, $this->jenisReport);
        if ($response['status'] == 'success') {
            return response()->download($response['filename']);
        } else if ($response['status'] == 'error') {
            $this->dispatch('notification', ['type' => 'warning', 'message' => $response['message']]);
            return;
        }
    }

    public function checklistInfure($tglAwal, $tglAkhir, $jenisReport = 'Checklist', $isNippo = false, $filter = null)
    {
        ini_set('max_execution_time', '300');
        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();
        $activeWorksheet->setShowGridlines(false);

        // Set locale agar tanggal indonesia
        Carbon::setLocale('id');

        // Judul
        $activeWorksheet->setCellValue('A1', ($jenisReport == 'Checklist' ? 'CHECKLIST ' : 'LOSS ') . 'NIPPO INFURE');
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
            'Nama Loss',
            'Berat Loss (Kg)',
        ];

        foreach ($header as $key => $value) {
            $activeWorksheet->setCellValue($columnHeaderEnd . $rowHeaderStart, $value);
            $columnHeaderEnd++;
        }

        /**
         * Mengatur halaman
         */
        $activeWorksheet->freezePane('D4');
        // Mengatur ukuran kertas menjadi A4
        $activeWorksheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
        // Mengatur orientasi menjadi landscape
        $activeWorksheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $activeWorksheet->getStyle($columnHeaderStart . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart)->getAlignment()->setWrapText(true);
        // Mengatur agar semua kolom muat dalam satu halaman
        $activeWorksheet->getPageSetup()->setFitToWidth(1);
        $activeWorksheet->getPageSetup()->setFitToHeight(0); // Biarkan tinggi menyesuaikan otomatis

        // Jika ingin memastikan rasio tetap terjaga
        $activeWorksheet->getPageSetup()->setFitToPage(true);
        // Mengatur margin halaman menjadi 0.75 cm di semua sisi
        $activeWorksheet->getPageMargins()->setTop(0.75 / 2.54);
        $activeWorksheet->getPageMargins()->setBottom(0.75 / 2.54);
        $activeWorksheet->getPageMargins()->setLeft(0.75 / 2.54);
        $activeWorksheet->getPageMargins()->setRight(0.75 / 2.54);
        $activeWorksheet->getPageMargins()->setHeader(0.75 / 2.54);
        $activeWorksheet->getPageMargins()->setFooter(0.75 / 2.54);

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

        // filter print nippo
        $filterSearchTerm = '';
        $filterStatus = '';
        if ($isNippo) {
            $this->lpk_no = $filter['lpk_no'];
            $this->machineId = $filter['machineId'];
            $this->productId = $filter['idProduct'];
            $this->status = $filter['status'];
            $this->searchTerm = $filter['searchTerm'];

            $filterStatus = $this->status == 0 ? " AND (tdpa.status_production = 0 AND tdpa.status_kenpin = 0)" :
                ($this->status == 1 ? " AND (tdpa.status_production = 1)" : " AND (tdpa.status_kenpin = 1)");
            $filterSearchTerm = $this->searchTerm ? " AND (tdpa.production_no ILIKE '%$this->searchTerm%' OR msp.code ILIKE '%$this->searchTerm%' OR msp.name ILIKE '%$this->searchTerm%' OR tdpa.machine_id ILIKE '%$this->searchTerm%' OR tdpa.nomor_han ILIKE '%$this->searchTerm%')" : '';
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
        $this->productId = $this->productId ? (is_array($this->productId) ? $this->productId['value'] : $this->productId) : '';
        $filterProduct = $this->productId ? " AND (tdpa.product_id = '$this->productId')" : '';

        if ($jenisReport == 'Checklist') {
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
                        INNER JOIN msDepartment AS msd ON msd.ID = msm.department_id
                        LEFT JOIN tdProduct_Assembly_Loss AS tdpal ON tdpal.product_assembly_id = tdpa.
                        ID LEFT JOIN msLossInfure AS msli ON msli.ID = tdpal.loss_infure_id
                    WHERE
                        $filterDate
                        $filterNoLPK
                        $nomorOrder
                        $filterDepartment
                        $filterMachine
                        $filterNomorHan
                        $filterProduct
                        $filterSeqNo
                        $filterStatus
                        $filterSearchTerm
                    ORDER BY nomesin ASC, tglproduksi ASC, jam ASC
                    ",
            );
        } else if ($jenisReport == 'Loss') {
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
                        INNER JOIN msDepartment AS msd ON msd.ID = msm.department_id
                        INNER JOIN tdProduct_Assembly_Loss AS tdpal ON tdpal.product_assembly_id = tdpa.id
                        LEFT JOIN msLossInfure AS msli ON msli.ID = tdpal.loss_infure_id
                    WHERE
                        $filterDate
                        $filterNoLPK
                        $nomorOrder
                        $filterDepartment
                        $filterMachine
                        $filterNomorHan
                        $filterProduct
                        $filterSeqNo
                    ORDER BY nomesin ASC, tglproduksi ASC, jam ASC
                    ",
            );
        }
        // dd($data);

        if (count($data) == 0) {
            $response = [
                'status' => 'error',
                'message' => "Data pada periode tanggal tersebut tidak ditemukan"
            ];

            return $response;
        }

        $dataMap = [];

        foreach ($data as $item) {
            $key = $item->nomesin . '_' . $item->tglproduksi . '_' . $item->gentan_no;

            if (!isset($dataMap[$key])) {
                $dataMap[$key] = [
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
                    'loss_data' => []
                ];
            }

            // Add loss data to the corresponding key
            $dataMap[$key]['loss_data'][$item->losscode] = [
                'losscode' => $item->losscode,
                'lossname' => $item->lossname,
                'berat_loss' => $item->berat_loss,
            ];
        }

        // After processing, we have a $dataMap (HashMap) ready for use.

        // Now, let's write the data into Excel
        $rowItemStart = 4;
        $columnItemStart = 'A';
        $rowItem = $rowItemStart;
        foreach ($dataMap as $key => $dataItem) {
            $columnItemEnd = $columnItemStart;

            // tanggal proses
            $activeWorksheet->setCellValue($columnItemEnd . $rowItem, Carbon::parse($dataItem['tanggal_proses'])->translatedFormat('d-M-Y'));
            $columnItemEnd++;

            // no proses
            $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $dataItem['seq_no']);
            $columnItemEnd++;

            // tanggal produksi
            $activeWorksheet->setCellValue($columnItemEnd . $rowItem, Carbon::parse($dataItem['tglproduksi'])->translatedFormat('d-M-Y'));
            $columnItemEnd++;

            // shift
            $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $dataItem['shift']);
            $columnItemEnd++;

            // jam
            $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $dataItem['jam']);
            $columnItemEnd++;

            // nik
            $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $dataItem['nik']);

            phpspreadsheet::textAlignCenter($spreadsheet, $columnItemStart . $rowItem . ':' . $columnItemEnd . $rowItem);
            $columnItemEnd++;

            // nama petugas
            $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $dataItem['nama_petugas']);
            $columnItemEnd++;

            // nomor_mesin
            $columnNoMesin = $columnItemEnd;
            $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $dataItem['nomor_mesin']);
            $columnItemEnd++;

            // no lpk
            $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $dataItem['lpk_no']);
            $columnItemEnd++;

            // nomer order
            $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $dataItem['produkcode']);
            phpSpreadsheet::textAlignCenter($spreadsheet, $columnNoMesin . $rowItem . ':' . $columnItemEnd . $rowItem);
            $columnItemEnd++;

            // nama produk
            $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $dataItem['nama_produk']);
            $columnItemEnd++;

            // nomor gentan
            $columnNoGentan = $columnItemEnd;
            $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $dataItem['gentan_no']);
            $columnItemEnd++;

            // nomor han
            $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $dataItem['nomor_han']);
            phpspreadsheet::textAlignCenter($spreadsheet, $columnNoGentan . $rowItem . ':' . $columnItemEnd . $rowItem);
            $columnItemEnd++;

            // panjang produksi
            $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $dataItem['panjang_produksi']);
            $columnItemEnd++;

            // berat standard
            $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $dataItem['berat_standard']);
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItemEnd . $rowItem . ':' . $columnItemEnd . $rowItem, 1);
            $columnItemEnd++;

            // berat produksi
            $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $dataItem['berat_produksi']);
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItemEnd . $rowItem . ':' . $columnItemEnd . $rowItem, 1);
            phpspreadsheet::addBorderDottedHorizontal($spreadsheet, $columnItemStart . $rowItem . ':' . $columnItemEnd . $rowItem);
            $columnItemEnd++;

            // Write loss data from HashMap
            foreach ($dataItem['loss_data'] as $losscode => $lossItem) {
                $columnLossStart = 'Q';
                $columnLoss = $columnLossStart;

                if ($losscode === '' && $lossItem['lossname'] == null  && $lossItem['berat_loss'] == null) {
                    $columnLoss++;
                    phpspreadsheet::addBorderDottedHorizontal($spreadsheet, $columnLossStart . $rowItem . ':' . $columnLoss . $rowItem);
                    break;
                }

                $activeWorksheet->setCellValue($columnLoss . $rowItem, $lossItem['losscode'] . '. ' . $lossItem['lossname']);
                $columnLoss++;
                $activeWorksheet->setCellValue($columnLoss . $rowItem, $lossItem['berat_loss']);
                phpspreadsheet::addBorderDottedHorizontal($spreadsheet, $columnLossStart . $rowItem . ':' . $columnLoss . $rowItem);
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
        $columnItemEnd = 'M';
        $spreadsheet->getActiveSheet()->mergeCells($columnItemStart . $rowItem . ':' . $columnItemEnd . $rowItem);
        $activeWorksheet->setCellValue($columnItemStart . $rowItem, 'GRAND TOTAL');
        phpSpreadsheet::styleFont($spreadsheet, $columnItemStart . $rowItem, true, 9, 'Calibri');
        $columnItemEnd++;

        // inisialisasi total
        $totalPanjangProduksi = 0;
        $totalBeratStandard = 0;
        $totalBeratProduksi = 0;
        $totalLoss = 0;

        // menghitung total
        foreach ($dataMap as $item) {
            $totalPanjangProduksi += $item['panjang_produksi'];
            $totalBeratStandard += $item['berat_standard'];
            $totalBeratProduksi += $item['berat_produksi'];
            $totalLoss += array_sum(array_column($item['loss_data'], 'berat_loss'));
        }

        // panjang produksi
        $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $totalPanjangProduksi);
        phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItemEnd . $rowItem);
        $columnItemEnd++;

        // berat standard
        $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $totalBeratStandard);
        phpspreadsheet::numberFormatCommaThousandsOrZero($spreadsheet, $columnItemEnd . $rowItem, 1);
        $columnItemEnd++;

        // berat produksi
        $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $totalBeratProduksi);
        phpspreadsheet::numberFormatCommaThousandsOrZero($spreadsheet, $columnItemEnd . $rowItem, 1);
        $columnItemEnd++;

        // Loss
        $columnItemEnd = 'R';
        $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $totalLoss);
        phpspreadsheet::numberFormatCommaThousandsOrZero($spreadsheet, $columnItemEnd . $rowItem, 1);
        phpspreadsheet::styleFont($spreadsheet, $columnItemStart . $rowItem . ':' . $columnItemEnd . $rowItem, true, 8, 'Calibri');
        phpspreadsheet::addFullBorder($spreadsheet, $columnItemStart . $rowItem . ':' . $columnItemEnd . $rowItem);
        $columnItemEnd++;


        // footer keterangan tanggal, jam, dan nama petugas
        $rowFooterStart = $rowItem + 2;
        $activeWorksheet->setCellValue('A' . $rowFooterStart, 'Dicetak pada: ' . Carbon::now()->translatedFormat('d-M-Y H:i:s') . ', oleh: ' . auth()->user()->empname);
        phpspreadsheet::styleFont($spreadsheet, 'A' . $rowFooterStart . ':A' . ($rowFooterStart + 1), false, 9, 'Calibri');


        // mengatur lebar kolom
        // $spreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);

        $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(3.00);
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(8.50);
        $activeWorksheet->getStyle('B' . $rowItemStart . ':' . 'B' . $rowFooterStart)->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(3.78);
        $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(4.0);
        $spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(15.50);
        $activeWorksheet->getStyle('G' . $rowItemStart . ':' . 'G' . $rowFooterStart)->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(5.50);
        $spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(7.8);
        $spreadsheet->getActiveSheet()->getColumnDimension('J')->setWidth(6.5);
        $spreadsheet->getActiveSheet()->getColumnDimension('K')->setWidth(18.00);
        $activeWorksheet->getStyle('K' . $rowItemStart . ':' . 'K' . $rowFooterStart)->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('L')->setWidth(6.70);
        $spreadsheet->getActiveSheet()->getColumnDimension('M')->setWidth(9.25);
        $spreadsheet->getActiveSheet()->getColumnDimension('N')->setWidth(7.50);
        $spreadsheet->getActiveSheet()->getColumnDimension('O')->setWidth(6.70);
        $spreadsheet->getActiveSheet()->getColumnDimension('P')->setWidth(6.70);
        $spreadsheet->getActiveSheet()->getColumnDimension('Q')->setWidth(11.00);
        $activeWorksheet->getStyle('Q' . $rowItemStart . ':' . 'Q' . $rowFooterStart)->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('R')->setWidth(5.10);

        $writer = new Xlsx($spreadsheet);
        $filename = 'NippoInfure-' . $jenisReport . '.xlsx';
        $writer->save($filename);
        $response = [
            'status' => 'success',
            'filename' => $filename
        ];
        return $response;
    }

    public function render()
    {
        if (isset($this->lpk_no) && $this->lpk_no != '') {
            if (strlen($this->lpk_no) >= 9 && !str_contains($this->lpk_no, '-')) {
                $this->lpk_no = substr_replace($this->lpk_no, '-', 6, 0);
            }
        }

        return view('livewire.nippo-infure.check-list')->extends('layouts.master');
    }
}
