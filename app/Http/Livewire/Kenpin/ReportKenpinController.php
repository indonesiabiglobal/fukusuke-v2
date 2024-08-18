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
    public $status;

    public function mount()
    {
        $this->tglAwal = Carbon::now()->format('Y-m-d');
        $this->tglAkhir = Carbon::now()->format('Y-m-d');
        $this->workingShiftHour = MsWorkingShift::select('work_hour_from', 'work_hour_till')->where('status', 1)->orderBy('work_hour_from', 'ASC')->get();
        $this->jamAwal = $this->workingShiftHour[0]->work_hour_from;
        $this->jamAkhir = $this->workingShiftHour[count($this->workingShiftHour) - 1]->work_hour_till;
        $this->product = MsProduct::get();
        $this->department = MsDepartment::whereIn('id', [2,7])->get();
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
                    tdka.berat_loss AS berat_loss,
                    tdka.remark AS remark,
                    tdka.status_kenpin AS status_kenpin,
                    tdka.created_by AS created_by,
                    tdka.created_on AS created_on,
                    tdka.updated_by AS updated_by,
                    tdka.updated_on AS updated_on,
                    tdol.lpk_no AS lpk_no,
                    tdkad.berat_loss AS berat_loss1,
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
                $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $itemKenpin['status_kenpin']);
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
                    phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItemEnd . $rowItem);
                    phpspreadsheet::addFullBorder($spreadsheet, $columnItemStart . $rowItem . ':' . $columnItemEnd . $rowItem);

                    $columnItemEnd++;
                    phpspreadsheet::styleFont($spreadsheet, $columnItemStart . $rowItem . ':' . $columnItemEnd . $rowItem, false, 8, 'Calibri');

                    $rowItem++;
                }
            }

            // Total
            $columnTotalEnd = 'K';
            $spreadsheet->getActiveSheet()->mergeCells($columnItemStart . $rowItem . ':' . $columnTotalEnd . $rowItem);
            $activeWorksheet->setCellValue($columnItemStart . $rowItem, 'TOTAL');
            $columnTotalEnd++;

            // panjang infure
            $activeWorksheet->setCellValue($columnTotalEnd . $rowItem, '=SUM(' . $columnTotalEnd . ($rowItemStart + 1) . ':' . $columnTotalEnd . ($rowItem - 1) . ')');
            phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnTotalEnd . $rowItem);
            $columnTotalEnd++;

            // berat loss
            $activeWorksheet->setCellValue($columnTotalEnd . $rowItem, '=SUM(' . $columnTotalEnd . ($rowItemStart + 1) . ':' . $columnTotalEnd . ($rowItem - 1) . ')');
            phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnTotalEnd . $rowItem);
            phpspreadsheet::addFullBorder($spreadsheet, $columnItemStart . $rowItem . ':' . $columnTotalEnd . $rowItem);
            $columnTotalEnd++;
            phpspreadsheet::styleFont($spreadsheet, $columnItemStart . $rowItem . ':' . $columnTotalEnd . $rowItem, true, 8, 'Calibri');

            $rowItem++;
            $rowItem++;
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

    public function reportSeitai($tglAwal, $tglAkhir)
    {
        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();
        $activeWorksheet->setShowGridlines(false);

        // Set locale agar tanggal indonesia
        Carbon::setLocale('id');

        // Judul
        $activeWorksheet->setCellValue('A1', 'DETAIL PRODUKSI SEITAI');
        $activeWorksheet->setCellValue('A2', 'Periode: ' . $tglAwal->translatedFormat('d-M-Y H:i') . ' s/d ' . $tglAkhir->translatedFormat('d-M-Y H:i') . ' - Mesin: ' . ($this->machineId ? $this->machineId : 'Semua Mesin'));
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
        $filterNomorPalet = $this->nomorPalet ? " AND (tdpg.nomor_palet = '$this->nomorPalet')" : '';
        $filterNomorLot = $this->nomorLot ? " AND (tdpg.nomor_lot = '$this->nomorLot')" : '';
        $filterProduct = $this->productId ? " AND (msp.id = '$this->productId')" : '';
        $filterNomorKenpin = $this->nomorKenpin ? " AND (tdpa.nomor_kenpin = '$this->nomorKenpin')" : '';
        $filterStatus = $this->status ? " AND (tdpa.status = '$this->status')" : '';

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

    public function render()
    {
        return view('livewire.kenpin.report-kenpin')->extends('layouts.master');
    }
}
