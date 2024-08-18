<?php

namespace App\Http\Livewire\Kenpin;

use App\Exports\KenpinExport;
use App\Helpers\phpspreadsheet;
use App\Models\MsDepartment;
use App\Models\MsProduct;
use App\Models\MsWorkingShift;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ReportKenpinController extends Component
{
    public $tglAwal;
    public $tglAkhir;
    public $jamAwal;
    public $jamAkhir;
    public $workingShiftHour;
    public $product;
    public $productId;
    public $department;
    public $nippo;
    public $buyer;
    public $buyer_id;
    public $lpk_no;
    public $nomorKenpin;
    public $nomorHan;
    public $nomorPalet;
    public $nomorLot;
    public $status;

    public function mount()
    {
        $this->tglAwal = Carbon::now()->format('Y-m-d');
        $this->tglAkhir = Carbon::now()->format('Y-m-d');
        $this->workingShiftHour = MsWorkingShift::select('work_hour_from', 'work_hour_till')->where('status', 1)->orderBy('work_hour_from', 'ASC')->get();
        $this->jamAwal = $this->workingShiftHour[0]->work_hour_from;
        $this->jamAkhir = $this->workingShiftHour[count($this->workingShiftHour) - 1]->work_hour_till;
        $this->product = MsProduct::get();
        $this->department = MsDepartment::whereIn('id', [2, 7])->get();
        $this->nippo = $this->department[0]->name;
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

        if ($this->nippo == 'INFURE') {
            $response = $this->reportInfure($tglAwal, $tglAkhir);
            if ($response['status'] == 'success') {
                return response()->download($response['filename']);
            } else if ($response['status'] == 'error') {
                $this->dispatch('notification', ['type' => 'warning', 'message' => $response['message']]);
                return;
            }
        } else if ($this->nippo == 'SEITAI') {
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
        $activeWorksheet->setCellValue('A1', 'DAFTAR KENPIN INFURE');
        $activeWorksheet->setCellValue('A2', 'Periode: ' . $tglAwal->translatedFormat('d-M-Y H:i') . ' s/d ' . $tglAkhir->translatedFormat('d-M-Y H:i'));
        // Style Judul
        phpspreadsheet::styleFont($spreadsheet, 'A1:A2', true, 11, 'Calibri');

        // header
        $rowHeaderStart = 3;
        $columnHeaderStart = 'A';
        $columnHeaderEnd = 'A';

        $header = [
            'Nomor Kenpin',
            'Status',
            'Tanggal Kenpin',
            'PIC Kenpin',
            'Nomor LPK',
            'NG',
            'Nomor Gentan',
            'Nomor Mesin',
            'Nomor Han',
            'Tanggal Produksi',
            'Kode Shift',
            'Panjang Infure (meter)',
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
        $filterDate = "tdka.kenpin_date BETWEEN '$tglAwal' AND '$tglAkhir'";
        $filterNoLPK = $this->lpk_no ? " AND (tdol.lpk_no = '$this->lpk_no')" : '';
        $filterProduct = $this->productId ? " AND (tdpa.product_id = '$this->productId')" : '';
        $filterNomorKenpin = $this->nomorKenpin ? " AND (tdka.kenpin_no = '$this->nomorKenpin')" : '';
        $filterStatus = $this->status ? " AND (tdka.status_kenpin = '$this->status')" : '';
        $filterNomorHan = $this->nomorHan ? " AND (tdpa.nomor_han = '$this->nomorHan')" : '';

        // query masih salah
        $data = DB::select(
            "
                SELECT
                    tdka.ID AS ID,
                    tdka.kenpin_no AS kenpin_no,
                    tdka.kenpin_date AS kenpin_date,
                    tdka.employee_id AS employee_id,
                    tdka.lpk_id AS lpk_id,
                    -- tdka.berat_loss AS berat_loss,
                    tdka.remark AS remark,
                    tdka.status_kenpin AS status_kenpin,
                    tdka.created_by AS created_by,
                    tdka.created_on AS created_on,
                    tdka.updated_by AS updated_by,
                    tdka.updated_on AS updated_on,
                    tdol.lpk_no AS lpk_no,
                    tdkad.berat_loss AS berat_loss,
                    tdpa.product_id AS product_id,
                    msp.code as produkcode,
                    msp.NAME AS namaproduk,
                    mse.empname AS nama_petugas,
                    tdpa.gentan_no AS gentan_no,
                    tdpa.panjang_produksi AS panjang_produksi,
                    tdpa.machine_id AS machine_id,
                    msm.machineno AS nomesin,
                    tdpa.production_date AS production_date,
                    tdpa.work_shift AS work_shift,
                    tdpa.nomor_han AS nomor_han
                FROM
                    tdKenpin_Assembly AS tdka
                    INNER JOIN tdKenpin_Assembly_Detail AS tdkad ON tdka.ID = tdkad.kenpin_assembly_id
                    INNER JOIN tdProduct_Assembly AS tdpa ON tdkad.product_assembly_id = tdpa.
                    ID INNER JOIN tdOrderLpk AS tdol ON tdka.lpk_id = tdol.
                    ID INNER JOIN msProduct AS msp ON tdpa.product_id = msp.
                    ID INNER JOIN msemployee AS mse ON mse.ID = tdka.employee_id
                    INNER JOIN msmachine AS msm ON msm.ID = tdpa.machine_id
                WHERE
                    $filterDate
                    $filterNoLPK
                    $filterProduct
                    $filterNomorKenpin
                    $filterStatus
                    $filterNomorHan
                ORDER BY tdka.kenpin_date ASC",
        );

        if (count($data) == 0) {
            $response = [
                'status' => 'error',
                'message' => "Data pada periode tanggal tersebut tidak ditemukan"
            ];

            return $response;
        }

        $dataFiltered = [];
        foreach ($data as $item) {
            if (!isset($dataFiltered[$item->product_id])) {
                $dataFiltered[$item->product_id] = [
                    'produkcode' => $item->produkcode,
                    'namaproduk' => $item->namaproduk,
                    'kenpin' => [],
                ];
            }

            // Tambahkan data kenpin ke dalam array
            if (!isset($dataFiltered[$item->product_id]['kenpin'][$item->kenpin_no])) {
                $dataFiltered[$item->product_id]['kenpin'][$item->kenpin_no] = [
                    'kenpin_no' => $item->kenpin_no,
                    'status_kenpin' => $item->status_kenpin,
                    'kenpin_date' => $item->kenpin_date,
                    'nama_petugas' => $item->nama_petugas,
                    'lpk_no' => $item->lpk_no,
                    'remark' => $item->remark,
                    'gentan' => [],
                ];
            }

            $dataFiltered[$item->product_id]['kenpin'][$item->kenpin_no]['gentan'][$item->gentan_no] = [
                'gentan_no' => $item->gentan_no,
                'nomesin' => $item->nomesin,
                'nomor_han' => $item->nomor_han,
                'production_date' => $item->production_date,
                'work_shift' => $item->work_shift,
                'panjang_produksi' => $item->panjang_produksi,
                'berat_loss' => $item->berat_loss,
            ];
        }

        // index
        $rowItemStart = 4;
        $columnItemStart = 'A';
        $columnGentanStart = 'G';
        $rowItem = $rowItemStart;
        foreach ($dataFiltered as $productId => $itemProduct) {
            // Menulis data produk
            $activeWorksheet->setCellValue($columnItemStart . $rowItem, $itemProduct['produkcode'] . ' - ' . $itemProduct['namaproduk']);
            // $spreadsheet->getActiveSheet()->mergeCells($columnItemStart . $rowItem . ':' . $endColumnItem . $rowItem);
            phpspreadsheet::styleFont($spreadsheet, $columnItemStart . $rowItem, true, 8, 'Calibri');
            $columnItemEnd = $columnItemStart;
            $rowItem++;
            foreach ($itemProduct['kenpin'] as $kenpinNo => $itemKenpin) {
                $columnItemEnd = $columnItemStart;
                // nomor kenpin
                $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $itemKenpin['kenpin_no']);
                phpspreadsheet::textAlignCenter($spreadsheet, $columnItemEnd . $rowItem);
                $columnItemEnd++;
                // status kenpin
                $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $itemKenpin['status_kenpin'] == 1 ? 'Proses' : ($itemKenpin['status_kenpin'] == 2 ? 'Finish' : ''));
                phpspreadsheet::textAlignCenter($spreadsheet, $columnItemEnd . $rowItem);
                $columnItemEnd++;
                // tanggal kenpin
                $activeWorksheet->setCellValue($columnItemEnd . $rowItem, Carbon::parse($itemKenpin['kenpin_date'])->translatedFormat('d-M-Y'));
                phpspreadsheet::textAlignCenter($spreadsheet, $columnItemEnd . $rowItem);
                $columnItemEnd++;
                // pic kenpin
                $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $itemKenpin['nama_petugas']);
                phpspreadsheet::textAlignCenter($spreadsheet, $columnItemEnd . $rowItem);
                $columnItemEnd++;
                // nomor lpk
                $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $itemKenpin['lpk_no']);
                phpspreadsheet::textAlignCenter($spreadsheet, $columnItemEnd . $rowItem);
                $columnItemEnd++;
                // ng
                $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $itemKenpin['remark']);
                $columnItemEnd++;
                $rowItemSumStart = $rowItem;

                foreach ($itemKenpin['gentan'] as $gentanNo => $itemGentan) {
                    $columnItemEnd = $columnGentanStart;
                    // nomor gentan
                    $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $gentanNo);
                    phpspreadsheet::textAlignCenter($spreadsheet, $columnItemEnd . $rowItem);
                    $columnItemEnd++;
                    // nomor mesin
                    $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $itemGentan['nomesin']);
                    phpspreadsheet::textAlignCenter($spreadsheet, $columnItemEnd . $rowItem);
                    $columnItemEnd++;
                    // nomor han
                    $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $itemGentan['nomor_han']);
                    phpspreadsheet::textAlignCenter($spreadsheet, $columnItemEnd . $rowItem);
                    $columnItemEnd++;
                    // tanggal produksi
                    $activeWorksheet->setCellValue($columnItemEnd . $rowItem, Carbon::parse($itemGentan['production_date'])->translatedFormat('d-M-Y'));
                    phpspreadsheet::textAlignCenter($spreadsheet, $columnItemEnd . $rowItem);
                    $columnItemEnd++;
                    // kode shift
                    $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $itemGentan['work_shift']);
                    phpspreadsheet::textAlignCenter($spreadsheet, $columnItemEnd . $rowItem);
                    $columnItemEnd++;
                    // panjang infure
                    $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $itemGentan['panjang_produksi']);
                    phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItemEnd . $rowItem);
                    $columnItemEnd++;
                    // berat loss
                    $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $itemGentan['berat_loss']);
                    phpspreadsheet::numberFormatCommaThousandsOrZero($spreadsheet, $columnItemEnd . $rowItem);
                    phpspreadsheet::addFullBorder($spreadsheet, $columnItemStart . $rowItem . ':' . $columnItemEnd . $rowItem);

                    $columnItemEnd++;
                    phpspreadsheet::styleFont($spreadsheet, $columnItemStart . $rowItem . ':' . $columnItemEnd . $rowItem, false, 8, 'Calibri');

                    $rowItem++;
                }
                // Total
                $columnTotalEnd = 'K';
                $spreadsheet->getActiveSheet()->mergeCells($columnItemStart . $rowItem . ':' . $columnTotalEnd . $rowItem);
                $activeWorksheet->setCellValue($columnItemStart . $rowItem, 'TOTAL');
                $columnTotalEnd++;

                // panjang infure
                $activeWorksheet->setCellValue($columnTotalEnd . $rowItem, '=SUM(' . $columnTotalEnd . $rowItemSumStart . ':' . $columnTotalEnd . ($rowItem - 1) . ')');
                phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnTotalEnd . $rowItem);
                $columnTotalEnd++;

                // berat loss
                $activeWorksheet->setCellValue($columnTotalEnd . $rowItem, '=SUM(' . $columnTotalEnd . $rowItemSumStart . ':' . $columnTotalEnd . ($rowItem - 1) . ')');
                phpspreadsheet::numberFormatCommaThousandsOrZero($spreadsheet, $columnTotalEnd . $rowItem);
                phpspreadsheet::addFullBorder($spreadsheet, $columnItemStart . $rowItem . ':' . $columnTotalEnd . $rowItem);
                $columnTotalEnd++;
                phpspreadsheet::styleFont($spreadsheet, $columnItemStart . $rowItem . ':' . $columnTotalEnd . $rowItem, true, 8, 'Calibri');

                $rowItem++;
                $rowItem++;
            }

        }

        // grand total
        $columnGrandTotalEnd = 'K';
        $spreadsheet->getActiveSheet()->mergeCells($columnItemStart . $rowItem . ':' . $columnGrandTotalEnd . $rowItem);
        $activeWorksheet->setCellValue($columnItemStart . $rowItem, 'GRAND TOTAL');
        phpSpreadsheet::styleFont($spreadsheet, $columnItemStart . $rowItem, true, 9, 'Calibri');
        $columnGrandTotalEnd++;

        // panjang produksi
        $totalPanjangProduksi = array_reduce($data, function ($carry, $item) {
            $carry += $item->panjang_produksi;
            return $carry;
        }, 0);
        $activeWorksheet->setCellValue($columnGrandTotalEnd . $rowItem, $totalPanjangProduksi);
        phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnGrandTotalEnd . $rowItem);
        $columnGrandTotalEnd++;

        // berat Loss
        $totalLoss = array_reduce($data, function ($carry, $item) {
            $carry += $item->berat_loss;
            return $carry;
        }, 0);
        $activeWorksheet->setCellValue($columnGrandTotalEnd . $rowItem, $totalLoss);
        phpspreadsheet::numberFormatCommaThousandsOrZero($spreadsheet, $columnGrandTotalEnd . $rowItem);
        phpspreadsheet::styleFont($spreadsheet, $columnItemStart . $rowItem . ':' . $columnGrandTotalEnd . $rowItem, true, 8, 'Calibri');
        phpspreadsheet::addFullBorder($spreadsheet, $columnItemStart . $rowItem . ':' . $columnGrandTotalEnd . $rowItem);
        $columnGrandTotalEnd++;

        $activeWorksheet->getStyle($columnHeaderStart . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart)->getAlignment()->setWrapText(true);

        // size auto
        $columnSizeStart = $columnItemStart;
        $columnSizeStart++;
        while ($columnSizeStart !== $columnGrandTotalEnd) {
            $spreadsheet->getActiveSheet()->getColumnDimension($columnSizeStart)->setAutoSize(true);
            $columnSizeStart++;
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'Kenpin-' . $this->nippo . '.xlsx';
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
        $activeWorksheet->setCellValue('A1', 'DAFTAR KENPIN SEITAI');
        $activeWorksheet->setCellValue('A2', 'Periode: ' . $tglAwal->translatedFormat('d-M-Y H:i') . ' s/d ' . $tglAkhir->translatedFormat('d-M-Y H:i'));
        // Style Judul
        phpspreadsheet::styleFont($spreadsheet, 'A1:A2', true, 11, 'Calibri');

        // header
        $rowHeaderStart = 3;
        $columnHeaderStart = 'A';
        $columnHeaderEnd = 'A';

        $header = [
            'Nomor Kenpin',
            'Status',
            'Tanggal Kenpin',
            'PIC Kenpin',
            'Nomor LPK',
            'NG',
            'Nomor Palet',
            'Nomor LOT',
            'Nomor Mesin',
            'Tanggal Produksi',
            'Kode Shift',
            'Quantity (Lembar)',
            'Quantity Loss (Lembar)',
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
        $filterDate = "tdkg.kenpin_date BETWEEN '$tglAwal' AND '$tglAkhir'";
        $filterNoLPK = $this->lpk_no ? " AND (tdol.lpk_no = '$this->lpk_no')" : '';
        $filterProduct = $this->productId ? " AND (tdkg.product_id = '$this->productId')" : '';
        $filterNomorKenpin = $this->nomorKenpin ? " AND (tdkg.kenpin_no = '$this->nomorKenpin')" : '';
        $filterStatus = $this->status ? " AND (tdkg.status_kenpin = '$this->status')" : '';
        $filterNomorPalet = $this->nomorPalet ? " AND (tdpg.nomor_palet = '$this->nomorPalet')" : '';
        $filterNomorLot = $this->nomorLot ? " AND (tdpg.nomor_lot = '$this->nomorLot')" : '';

        // query masih salah
        $data = DB::select(
            "
                SELECT
                    tdkg.ID AS ID,
                    tdkg.kenpin_no AS kenpin_no,
                    tdkg.kenpin_date AS kenpin_date,
                    tdkg.employee_id AS employee_id,
                    mse.empname AS nama_petugas,
                    -- tdkg.product_id AS product_id,
                    -- tdkg.qty_loss AS qty_loss,
                    tdkg.remark AS remark,
                    tdkg.status_kenpin AS status_kenpin,
                    tdol.lpk_no AS lpk_no,
                    tdkgd.qty_loss AS qty_loss,
                    tdpg.product_id AS product_id,
                    msp.code AS produkcode,
                    msp.NAME AS namaproduk,
                    tdpg.id AS product_goods_id,
                    tdpg.nomor_palet AS nomor_palet,
                    tdpg.nomor_lot AS nomor_lot,
                    tdpg.machine_id AS machine_id,
                    tdpg.production_date AS production_date,
                    tdpg.work_shift AS work_shift,
                    tdpg.qty_produksi AS qty_produksi,
                    msm.machineno AS nomesin
                FROM
                    tdKenpin_Goods AS tdkg
                    INNER JOIN tdKenpin_Goods_Detail AS tdkgd ON tdkg.ID = tdkgd.kenpin_goods_id
                    INNER JOIN tdProduct_Goods AS tdpg ON tdkgd.product_goods_id = tdpg.
                    ID INNER JOIN tdOrderLpk AS tdol ON tdpg.lpk_id = tdol.
                    ID INNER JOIN msproduct AS msp ON tdkg.product_id = msp.
                    ID INNER JOIN msemployee AS mse ON mse.ID = tdkg.employee_id
                    INNER JOIN msmachine AS msm ON msm.ID = tdpg.machine_id
                WHERE
                    $filterDate
                    $filterNoLPK
                    $filterProduct
                    $filterNomorKenpin
                    $filterStatus
                    $filterNomorPalet
                    $filterNomorLot
                ORDER BY tdkg.kenpin_date ASC",
        );

        if (count($data) == 0) {
            $response = [
                'status' => 'error',
                'message' => "Data pada periode tanggal tersebut tidak ditemukan"
            ];

            return $response;
        }

        $dataFiltered = [];
        foreach ($data as $item) {
            if (!isset($dataFiltered[$item->product_id])) {
                $dataFiltered[$item->product_id] = [
                    'produkcode' => $item->produkcode,
                    'namaproduk' => $item->namaproduk,
                    'kenpin' => [],
                ];
            }

            // Tambahkan data kenpin ke dalam array
            if (!isset($dataFiltered[$item->product_id]['kenpin'][$item->kenpin_no])) {
                $dataFiltered[$item->product_id]['kenpin'][$item->kenpin_no] = [
                    'kenpin_no' => $item->kenpin_no,
                    'status_kenpin' => $item->status_kenpin,
                    'kenpin_date' => $item->kenpin_date,
                    'nama_petugas' => $item->nama_petugas,
                    'lpk_no' => $item->lpk_no,
                    'remark' => $item->remark,
                    'product_goods' => [],
                ];
            }

            $dataFiltered[$item->product_id]['kenpin'][$item->kenpin_no]['product_goods'][$item->product_goods_id] = [
                'nomesin' => $item->nomesin,
                'nomor_palet' => $item->nomor_palet,
                'nomor_lot' => $item->nomor_lot,
                'production_date' => $item->production_date,
                'work_shift' => $item->work_shift,
                'qty_produksi' => $item->qty_produksi,
                'qty_loss' => $item->qty_loss,
            ];
        }

        // index
        $rowItemStart = 4;
        $columnItemStart = 'A';
        $columnGentanStart = 'G';
        $rowItem = $rowItemStart;
        foreach ($dataFiltered as $productId => $itemProduct) {
            // Menulis data produk
            $activeWorksheet->setCellValue($columnItemStart . $rowItem, $itemProduct['produkcode'] . ' - ' . $itemProduct['namaproduk']);
            // $spreadsheet->getActiveSheet()->mergeCells($columnItemStart . $rowItem . ':' . $endColumnItem . $rowItem);
            phpspreadsheet::styleFont($spreadsheet, $columnItemStart . $rowItem, true, 9, 'Calibri');
            $columnItemEnd = $columnItemStart;
            $rowItem++;
            foreach ($itemProduct['kenpin'] as $kenpinNo => $itemKenpin) {
                $columnItemEnd = $columnItemStart;
                // nomor kenpin
                $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $itemKenpin['kenpin_no']);
                phpspreadsheet::textAlignCenter($spreadsheet, $columnItemEnd . $rowItem);
                $columnItemEnd++;
                // status kenpin
                $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $itemKenpin['status_kenpin'] == 1 ? 'Proses' : ($itemKenpin['status_kenpin'] == 2 ? 'Finish' : ''));
                phpspreadsheet::textAlignCenter($spreadsheet, $columnItemEnd . $rowItem);
                $columnItemEnd++;
                // tanggal kenpin
                $activeWorksheet->setCellValue($columnItemEnd . $rowItem, Carbon::parse($itemKenpin['kenpin_date'])->translatedFormat('d-M-Y'));
                phpspreadsheet::textAlignCenter($spreadsheet, $columnItemEnd . $rowItem);
                $columnItemEnd++;
                // pic kenpin
                $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $itemKenpin['nama_petugas']);
                phpspreadsheet::textAlignCenter($spreadsheet, $columnItemEnd . $rowItem);
                $columnItemEnd++;
                // nomor lpk
                $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $itemKenpin['lpk_no']);
                phpspreadsheet::textAlignCenter($spreadsheet, $columnItemEnd . $rowItem);
                $columnItemEnd++;
                // ng
                $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $itemKenpin['remark']);
                $columnItemEnd++;
                $rowItemSumStart = $rowItem;

                foreach ($itemKenpin['product_goods'] as $productGoodsId => $itemProductGoods) {
                    $columnItemEnd = $columnGentanStart;
                    // nomor palet
                    $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $itemProductGoods['nomor_palet']);
                    phpspreadsheet::textAlignCenter($spreadsheet, $columnItemEnd . $rowItem);
                    $columnItemEnd++;
                    // nomor lot
                    $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $itemProductGoods['nomor_lot']);
                    phpspreadsheet::textAlignCenter($spreadsheet, $columnItemEnd . $rowItem);
                    $columnItemEnd++;
                    // nomor mesin
                    $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $itemProductGoods['nomesin']);
                    phpspreadsheet::textAlignCenter($spreadsheet, $columnItemEnd . $rowItem);
                    $columnItemEnd++;
                    // tanggal produksi
                    $activeWorksheet->setCellValue($columnItemEnd . $rowItem, Carbon::parse($itemProductGoods['production_date'])->translatedFormat('d-M-Y'));
                    phpspreadsheet::textAlignCenter($spreadsheet, $columnItemEnd . $rowItem);
                    $columnItemEnd++;
                    // kode shift
                    $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $itemProductGoods['work_shift']);
                    phpspreadsheet::textAlignCenter($spreadsheet, $columnItemEnd . $rowItem);
                    $columnItemEnd++;
                    // panjang infure
                    $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $itemProductGoods['qty_produksi']);
                    phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItemEnd . $rowItem);
                    $columnItemEnd++;
                    // berat loss
                    $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $itemProductGoods['qty_loss']);
                    phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItemEnd . $rowItem);
                    phpspreadsheet::addFullBorder($spreadsheet, $columnItemStart . $rowItem . ':' . $columnItemEnd . $rowItem);

                    $columnItemEnd++;
                    phpspreadsheet::styleFont($spreadsheet, $columnItemStart . $rowItem . ':' . $columnItemEnd . $rowItem, false, 8, 'Calibri');

                    $rowItem++;
                }

                // Total
                $columnTotalEnd = 'K';
                $spreadsheet->getActiveSheet()->mergeCells($columnItemStart . $rowItem . ':' . $columnTotalEnd . $rowItem);
                $activeWorksheet->setCellValue($columnItemStart . $rowItem, 'TOTAL');
                $columnTotalEnd++;

                // panjang infure
                $activeWorksheet->setCellValue($columnTotalEnd . $rowItem, '=SUM(' . $columnTotalEnd . $rowItemSumStart . ':' . $columnTotalEnd . ($rowItem - 1) . ')');
                phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnTotalEnd . $rowItem);
                $columnTotalEnd++;

                // berat loss
                $activeWorksheet->setCellValue($columnTotalEnd . $rowItem, '=SUM(' . $columnTotalEnd . $rowItemSumStart . ':' . $columnTotalEnd . ($rowItem - 1) . ')');
                phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnTotalEnd . $rowItem);
                phpspreadsheet::addFullBorder($spreadsheet, $columnItemStart . $rowItem . ':' . $columnTotalEnd . $rowItem);
                $columnTotalEnd++;
                phpspreadsheet::styleFont($spreadsheet, $columnItemStart . $rowItem . ':' . $columnTotalEnd . $rowItem, true, 8, 'Calibri');

                $rowItem++;
                $rowItem++;
            }
        }

        // grand total
        $columnGrandTotalEnd = 'K';
        $spreadsheet->getActiveSheet()->mergeCells($columnItemStart . $rowItem . ':' . $columnGrandTotalEnd . $rowItem);
        $activeWorksheet->setCellValue($columnItemStart . $rowItem, 'GRAND TOTAL');
        phpSpreadsheet::styleFont($spreadsheet, $columnItemStart . $rowItem, true, 9, 'Calibri');
        $columnGrandTotalEnd++;

        // panjang produksi
        $totalPanjangProduksi = array_reduce($data, function ($carry, $item) {
            $carry += $item->qty_produksi;
            return $carry;
        }, 0);
        $activeWorksheet->setCellValue($columnGrandTotalEnd . $rowItem, $totalPanjangProduksi);
        phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnGrandTotalEnd . $rowItem);
        $columnGrandTotalEnd++;

        // berat Loss
        $totalLoss = array_reduce($data, function ($carry, $item) {
            $carry += $item->qty_loss;
            return $carry;
        }, 0);
        $activeWorksheet->setCellValue($columnGrandTotalEnd . $rowItem, $totalLoss);
        phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnGrandTotalEnd . $rowItem);
        phpspreadsheet::styleFont($spreadsheet, $columnItemStart . $rowItem . ':' . $columnGrandTotalEnd . $rowItem, true, 8, 'Calibri');
        phpspreadsheet::addFullBorder($spreadsheet, $columnItemStart . $rowItem . ':' . $columnGrandTotalEnd . $rowItem);
        $columnGrandTotalEnd++;

        $activeWorksheet->getStyle($columnHeaderStart . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart)->getAlignment()->setWrapText(true);

        // size auto
        $columnSizeStart = $columnItemStart;
        $columnSizeStart++;
        while ($columnSizeStart !== $columnGrandTotalEnd) {
            $spreadsheet->getActiveSheet()->getColumnDimension($columnSizeStart)->setAutoSize(true);
            $columnSizeStart++;
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'Kenpin-' . $this->nippo . '.xlsx';
        $writer->save($filename);
        $response = [
            'status' => 'success',
            'filename' => $filename
        ];
        return $response;
    }

    public function render()
    {
        return view('livewire.kenpin.report-kenpin')->extends('layouts.master');
    }
}
