<?php

namespace App\Http\Livewire\NippoSeitai;

use App\Models\MsMachine;
use App\Models\MsProduct;
use Carbon\Carbon;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\NippoSeitaiExport;
use App\Helpers\phpspreadsheet;
use Livewire\Attributes\Session;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class LossSeitaiController extends Component
{
    protected $paginationTheme = 'bootstrap';
    public $products;
    #[Session]
    public $tglMasuk;
    #[Session]
    public $tglKeluar;
    public $machine;
    #[Session]
    public $transaksi;
    #[Session]
    public $machineid;
    #[Session]
    public $searchTerm;
    // #[Session]
    public $lpk_no;
    #[Session]
    public $idProduct;
    #[Session]
    public $status;
    #[Session]
    public $sortingTable;

    use WithPagination, WithoutUrlPagination;

    public function mount()
    {
        $this->products = MsProduct::get();
        // $this->buyer = MsBuyer::get();
        $this->machine = MsMachine::where('machineno',  'LIKE', '00S%')->orderBy('machineno')->get();

        if (empty($this->transaksi)) {
            $this->transaksi = 1;
        }
        if (empty($this->tglMasuk)) {
            $this->tglMasuk = Carbon::now()->format('d M Y');
        }
        if (empty($this->tglKeluar)) {
            $this->tglKeluar = Carbon::now()->format('d M Y');
        }
        if (empty($this->sortingTable)) {
            $this->sortingTable = [[1, 'asc']];
        }
    }

    public function updateSortingTable($value)
    {
        $this->sortingTable = $value;
        $this->skipRender();
    }

    public function search()
    {
        $this->render();
    }

    public function print()
    {
        return Excel::download(new NippoSeitaiExport(
            $this->tglMasuk,
            $this->tglKeluar,
            // $this->searchTerm,
            // $this->idProduct,
            // $this->idBuyer,
            // $this->status,
        ), 'NippoSeitai-CheckList.xlsx');
    }

    public function export()
    {
        // pengecekan inputan jam awal dan jam akhir
        // if (is_array($this->jamMasuk)) {
        //     $this->jamMasuk = $this->jamMasuk['value'];
        // } else {
        //     $this->jamMasuk = $this->jamMasuk;
        // }

        // if (is_array($this->jamAkhir)) {
        //     $this->jamAkhir = $this->jamAkhir['value'];
        // } else {
        //     $this->jamAkhir = $this->jamAkhir;
        // }

        $tglAwal = Carbon::parse($this->tglMasuk . ' ' . '00:00:00');
        $tglAkhir = Carbon::parse($this->tglKeluar . ' ' . '23:59:59');

        if ($this->transaksi == 'produksi') {
            $fieldDate = 'tdpg.production_date';
            $filterDate = "tdpg.production_date BETWEEN '$tglAwal' AND '$tglAkhir'";
        } else {
            $fieldDate = 'tdpg.created_on';
            $filterDate = "tdpg.created_on BETWEEN '$tglAwal' AND '$tglAkhir'";
        }
        $filterNoLPK = $this->lpk_no ? " AND (tdol.lpk_no = '$this->lpk_no')" : '';
        $machineid = $this->machineid ? (is_array($this->machineid) ? $this->machineid['value'] : $this->machineid) : '';
        $filterMachine = $machineid ? " AND (tdpg.machine_id = '$machineid')" : '';
        $filterProductionNo = "";
        $filterProductID = "";
        $filterNomorPalet = "";

        if (isset($this->searchTerm) && $this->searchTerm != '') {
            $filterNoLPK .= " AND (tdol.lpk_no ILIKE '%$this->searchTerm%')";
            $filterProductionNo .= " AND (tdpg.production_no ILIKE '%$this->searchTerm%')";
            $filterProductID .= " AND (tdpg.product_id ILIKE '%$this->searchTerm%')";
            $filterNomorPalet .= " AND (tdpg.nomor_palet ILIKE '%$this->searchTerm%')";
            $filterMachine .= " AND (tdpg.machine_id ILIKE '%$this->searchTerm%')";
        }

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
                LEFT JOIN goodasy ON tdpg.ID = goodasy.product_goods_id
                INNER JOIN lossgoods ON tdpg.ID = lossgoods.product_goods_id
                INNER JOIN tdOrderLpk AS tdol ON tdpg.lpk_id = tdol.
                ID INNER JOIN msmachine AS mm ON mm.ID = tdpg.machine_id
                INNER JOIN msemployee AS maPetugas ON maPetugas.ID = tdpg.employee_id
                LEFT JOIN msemployee AS maInfure ON tdpg.employee_id_infure = maInfure.
                ID INNER JOIN msDepartment AS msd ON msd.ID = maPetugas.department_id
                INNER JOIN msProduct AS mp ON mp.ID = tdpg.product_id
            WHERE
                $filterDate
                $filterNoLPK
                $filterMachine
                $filterNomorPalet
                $filterProductionNo
                $filterProductID
            ORDER BY $fieldDate, tdpg.seq_no
        ");

        if (count($data) == 0) {
            $response = [
                'status' => 'error',
                'message' => "Data pada periode tanggal tersebut tidak ditemukan"
            ];

            $this->dispatch('notification', ['type' => 'warning', 'message' => 'Data pada periode tanggal tersebut tidak ditemukan']);
            return;
        }

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

        // Judul
        $startColumn = 'A';
        $endColumn = 'L';
        $rowTitleCardStart = 1;
        $rowTitleCardEnd = 2;
        $spreadsheet->getActiveSheet()->mergeCells($startColumn . $rowTitleCardStart . ':' . $endColumn . $rowTitleCardStart);
        $activeWorksheet->setCellValue($startColumn . $rowTitleCardStart, 'CHECKLIST LOSS SEITAI');
        $spreadsheet->getActiveSheet()->mergeCells($startColumn . $rowTitleCardEnd . ':' . $endColumn . $rowTitleCardEnd);
        $activeWorksheet->setCellValue($startColumn . $rowTitleCardEnd, 'Tanggal ' . ucwords($this->transaksi) . ' : ' . $tglAwal . '  ~  ' . $tglAkhir);
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

        // Quantity
        // $columnQty = 'G';
        // $spreadsheet->getActiveSheet()->mergeCells($columnQty . $rowHeaderStart . ':' . $columnQty . $rowHeaderEnd);
        // $activeWorksheet->setCellValue($columnQty . $rowHeaderStart, 'Quantity (Lembar)');
        // phpspreadsheet::styleFont($spreadsheet, $columnQty . $rowHeaderStart . ':' . $columnQty . $rowHeaderEnd, true, 9, 'Calibri');
        // phpspreadsheet::textAlignCenter($spreadsheet, $columnQty . $rowHeaderStart . ':' . $columnQty . $rowHeaderEnd);
        // $activeWorksheet->getStyle($columnQty . $rowHeaderStart)->getAlignment()->setWrapText(true);

        // Loss Infure
        // $columnLoss = 'H';
        // $activeWorksheet->setCellValue($columnLoss . $rowHeaderStart, 'Loss Infure');
        // $activeWorksheet->setCellValue($columnLoss . $rowHeaderEnd, 'NIK');
        // phpspreadsheet::styleFont($spreadsheet, $columnLoss . $rowHeaderStart . ':' . $columnLoss . $rowHeaderEnd, true, 9, 'Calibri');
        // phpspreadsheet::textAlignCenter($spreadsheet, $columnLoss . $rowHeaderStart . ':' . $columnLoss . $rowHeaderEnd);
        // $activeWorksheet->getStyle($columnLoss . $rowHeaderStart)->getAlignment()->setWrapText(true);

        // // Nomor palet
        // $columnPalet = 'I';
        // $activeWorksheet->setCellValue($columnPalet . $rowHeaderStart, 'Nomor Palet');
        // $activeWorksheet->setCellValue($columnPalet . $rowHeaderEnd, 'Nomor LOT');
        // phpspreadsheet::styleFont($spreadsheet, $columnPalet . $rowHeaderStart . ':' . $columnPalet . $rowHeaderEnd, true, 9, 'Calibri');
        // phpspreadsheet::textAlignCenter($spreadsheet, $columnPalet . $rowHeaderStart . ':' . $columnPalet . $rowHeaderEnd);

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
                // Quantity
                // $activeWorksheet->setCellValue($columnQty . $rowItemStart, $item['qty_produksi']);
                // phpspreadsheet::styleFont($spreadsheet, $columnQty . $rowItemStart, false, 8, 'Calibri');
                // phpSpreadsheet::numberFormatThousands($spreadsheet, $columnQty . $rowItemStart);
                // Loss Infure
                // $activeWorksheet->setCellValue($columnLoss . $rowItemStart, $item['infure_berat_loss']);
                // phpspreadsheet::styleFont($spreadsheet, $columnLoss . $rowItemStart, false, 8, 'Calibri');
                // // NIK
                // $activeWorksheet->setCellValue($columnLoss . $rowItemEnd, $item['nikpetugasinfure']);
                // phpspreadsheet::styleFont($spreadsheet, $columnLoss . $rowItemEnd, false, 8, 'Calibri');
                // // Nomor Palet
                // $activeWorksheet->setCellValue($columnPalet . $rowItemStart, $item['nomor_palet']);
                // phpspreadsheet::styleFont($spreadsheet, $columnPalet . $rowItemStart, false, 8, 'Calibri');
                // phpspreadsheet::textAlignCenter($spreadsheet, $columnPalet . $rowItemStart);
                // // Nomor LOT
                // $activeWorksheet->setCellValue($columnPalet . $rowItemEnd, $item['nomor_lot']);
                // phpspreadsheet::styleFont($spreadsheet, $columnPalet . $rowItemEnd, false, 8, 'Calibri');
                // phpspreadsheet::textAlignCenter($spreadsheet, $columnPalet . $rowItemEnd);

                // border
                phpspreadsheet::addFullBorder($spreadsheet, $startColumn . $rowItemStart . ':' . $columnPetugas . $rowItemEnd);

                // Nomor Gentan
                // $rowGentan = $rowItemStart;
                // foreach ($dataGentan[$productionDate][$id_tdpg] as $gentan) {
                //     $activeWorksheet->setCellValue($columnNamaLoss . $rowGentan, $gentan->gentannomorline);
                //     phpspreadsheet::styleFont($spreadsheet, $columnNamaLoss . $rowGentan, false, 8, 'Calibri');
                //     phpspreadsheet::textAlignCenter($spreadsheet, $columnNamaLoss . $rowGentan);
                //     $rowGentan++;
                // }

                // Nama Loss
                $rowLoss = $rowItemStart;
                foreach ($dataLoss[$productionDate][$id_tdpg] as $itemLoss) {
                    // Nama Loss
                    $activeWorksheet->setCellValue($columnNamaLoss . $rowLoss, $itemLoss->lossname);
                    phpspreadsheet::styleFont($spreadsheet, $columnNamaLoss . $rowLoss, false, 8, 'Calibri');
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

        // // total quantity
        // $totalQty = array_reduce($dataFiltered, function ($carry, $item) {
        //     $carry += array_sum(array_column($item, 'qty_produksi'));
        //     return $carry;
        // }, 0);
        // $activeWorksheet->setCellValue($columnQty . $rowGrandTotal, $totalQty);
        // phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnQty . $rowGrandTotal);
        // $columnGrandTotalEnd++;

        // total loss
        // $totalLoss = array_reduce($dataFiltered, function ($carry, $item) {
        //     $carry += array_sum(array_column($item, 'infure_berat_loss'));
        //     return $carry;
        // }, 0);
        // $activeWorksheet->setCellValue($columnLoss . $rowGrandTotal, $totalLoss);
        // phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnQty . $rowGrandTotal);
        // $columnGrandTotalEnd++;

        // berat loss
        $columnBerat = 'H';
        $spreadsheet->getActiveSheet()->mergeCells($columnGrandTotalEnd . $rowGrandTotal . ':' . $columnBerat . $rowGrandTotal);
        $columnBerat++;
        $totalBeratLoss = array_sum(array_column($data, 'berat_loss'));
        $activeWorksheet->setCellValue($columnBerat . $rowGrandTotal, $totalBeratLoss);
        phpspreadsheet::addFullBorder($spreadsheet, 'A' . $rowGrandTotal . ':' . $columnBerat . $rowGrandTotal);
        // phpSpreadsheet::numberFormatCommaThousandsOrZero($spreadsheet, $columnQty . $rowGrandTotal);

        phpspreadsheet::styleFont($spreadsheet, 'A' . $rowGrandTotal . ':' . $columnBerat . $rowGrandTotal, true, 9, 'Calibri');

        // size auto
        while ($startColumn !== $columnBerat) {

            switch ($startColumn) {
                case $columnNamaLoss:
                    $spreadsheet->getActiveSheet()->getColumnDimension($columnNamaLoss)->setWidth(180, 'px');
                    // wrap text
                    $activeWorksheet->getStyle($columnNamaLoss . $rowHeaderStart)->getAlignment()->setWrapText(true);
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
        $writer->save('asset/report/LossSeitai-Checklist.xlsx');
        return response()->download('asset/report/LossSeitai-Checklist.xlsx');
    }

    public function render()
    {
        if ($this->transaksi == 2) {
            $data = DB::table('tdproduct_goods AS tdpg')
                ->select([
                    'tdpg.id AS id',
                    'tdpg.production_no AS production_no',
                    'tdpg.production_date AS production_date',
                    'tdpg.employee_id AS employee_id',
                    'tdpg.employee_id_infure AS employee_id_infure',
                    'tdpg.work_shift AS work_shift',
                    'tdpg.work_hour AS work_hour',
                    'tdpg.machine_id AS machine_id',
                    'tdpg.lpk_id AS lpk_id',
                    'tdpg.product_id AS product_id',
                    'tdpg.qty_produksi AS qty_produksi',
                    'tdpg.seitai_berat_loss AS seitai_berat_loss',
                    'tdpg.infure_berat_loss AS infure_berat_loss',
                    'tdpg.nomor_palet AS nomor_palet',
                    'tdpg.nomor_lot AS nomor_lot',
                    'tdpg.seq_no AS seq_no',
                    'tdpg.status_production AS status_production',
                    'tdpg.status_warehouse AS status_warehouse',
                    'tdpg.kenpin_qty_loss AS kenpin_qty_loss',
                    'tdpg.kenpin_qty_loss_proses AS kenpin_qty_loss_proses',
                    'tdpg.created_by AS created_by',
                    'tdpg.created_on AS created_on',
                    'tdpg.updated_by AS updated_by',
                    'tdpg.updated_on AS updated_on',
                    'tdol.order_id AS order_id',
                    'tdol.lpk_no AS lpk_no',
                    'tdol.lpk_date AS lpk_date',
                    'tdol.panjang_lpk AS panjang_lpk',
                    'tdol.qty_gentan AS qty_gentan',
                    'tdol.qty_gulung AS qty_gulung',
                    'tdol.qty_lpk AS qty_lpk',
                    'tdol.total_assembly_qty AS total_assembly_qty',
                    DB::raw('tdol.qty_lpk - tdol.total_assembly_qty AS selisih'),
                    'mp.name AS product_name',
                    'mp.code',
                    'msm.machineno'
                ])
                ->join('tdorderlpk AS tdol', 'tdpg.lpk_id', '=', 'tdol.id')
                ->leftJoin('msproduct AS mp', 'mp.id', '=', 'tdol.product_id')
                ->leftJoin('tdproduct_goods_assembly AS tga', 'tga.product_goods_id', '=', 'tdpg.id')
                ->leftJoin('msmachine AS msm', 'msm.id', '=', 'tdpg.machine_id')
                ->leftJoin('tdproduct_assembly AS ta', 'ta.id', '=', 'tga.product_assembly_id');

            if (isset($this->tglMasuk) && $this->tglMasuk != '') {
                $data = $data->where('tdpg.production_date', '>=', $this->tglMasuk);
            }
            if (isset($this->tglKeluar) && $this->tglKeluar != '') {
                $data = $data->where('tdpg.production_date', '<=', $this->tglKeluar);
            }
            if (isset($this->lpk_no) && $this->lpk_no != "" && $this->lpk_no != "undefined") {
                $data = $data->where('tdol.lpk_no', 'ilike', "%{$this->lpk_no}%");
            }
            if (isset($this->searchTerm) && $this->searchTerm != '') {
                $data = $data->where(function ($query) {
                    $query->where('tdol.lpk_no', 'ilike', '%' . $this->searchTerm . '%')
                        ->orWhere('tdpg.production_no', 'ilike', '%' . $this->searchTerm . '%')
                        ->orWhere('tdpg.product_id', 'ilike', '%' . $this->searchTerm . '%')
                        ->orWhere('tdpg.machine_id', 'ilike', '%' . $this->searchTerm . '%')
                        ->orWhere('tdpg.nomor_palet', 'ilike', '%' . $this->searchTerm . '%')
                        ->orWhere('tdpg.nomor_lot', 'ilike', '%' . $this->searchTerm . '%');
                });
            }
            if (isset($this->idProduct) && $this->idProduct['value'] != "" && $this->idProduct != "undefined") {
                $data = $data->where('tdpg.product_id', $this->idProduct['value']);
            }
            if (isset($this->machineid) && $this->machineid['value'] != "" && $this->machineid != "undefined") {
                $data = $data->where('tdpg.machine_id', $this->machineid['value']);
            }
            if (isset($this->gentan_no) && $this->gentan_no != "" && $this->gentan_no != "undefined") {
                $data = $data->where('ta.gentan_no', $this->gentan_no);
            }
            if (isset($this->status) && $this->status['value'] != "" && $this->status != "undefined") {
                if ($this->status['value'] == 0) {
                    $data->where('tdpg.status_production', 0)
                        ->where('tdpg.status_warehouse', 0);
                } elseif ($this->status['value'] == 1) {
                    $data->where('tdpg.status_production', 1);
                } elseif ($this->status['value'] == 2) {
                    $data->where('tdpg.status_warehouse', 1);
                }
            }
            $data = $data->paginate(8);
        } else {
            $data = DB::table('tdproduct_goods AS tdpg')
                ->select(
                    'tdpg.id AS id',
                    'tdpg.production_no AS production_no',
                    'tdpg.production_date AS production_date',
                    'tdpg.employee_id AS employee_id',
                    'tdpg.employee_id_infure AS employee_id_infure',
                    'tdpg.work_shift AS work_shift',
                    'tdpg.work_hour AS work_hour',
                    'tdpg.machine_id AS machine_id',
                    'tdpg.lpk_id AS lpk_id',
                    'tdpg.product_id AS product_id',
                    'tdpg.qty_produksi AS qty_produksi',
                    'tdpg.seitai_berat_loss AS seitai_berat_loss',
                    'tdpg.infure_berat_loss AS infure_berat_loss',
                    'tdpg.nomor_palet AS nomor_palet',
                    'tdpg.nomor_lot AS nomor_lot',
                    'tdpg.seq_no AS seq_no',
                    'tdpg.status_production AS status_production',
                    'tdpg.status_warehouse AS status_warehouse',
                    'tdpg.kenpin_qty_loss AS kenpin_qty_loss',
                    'tdpg.kenpin_qty_loss_proses AS kenpin_qty_loss_proses',
                    'tdpg.created_by AS created_by',
                    'tdpg.created_on AS created_on',
                    'tdpg.updated_by AS updated_by',
                    'tdpg.updated_on AS updated_on',
                    'tdol.order_id AS order_id',
                    'tdol.lpk_no AS lpk_no',
                    'tdol.lpk_date AS lpk_date',
                    'tdol.panjang_lpk AS panjang_lpk',
                    'tdol.qty_gentan AS qty_gentan',
                    'tdol.qty_gulung AS qty_gulung',
                    'tdol.qty_lpk AS qty_lpk',
                    'tdol.total_assembly_qty AS total_assembly_qty',
                    DB::raw('tdol.qty_lpk - tdol.total_assembly_qty AS selisih'),
                    'mp.name AS product_name',
                    'mp.code',
                    'msm.machineno'
                )
                ->join('tdorderlpk AS tdol', 'tdpg.lpk_id', '=', 'tdol.id')
                ->leftJoin('msproduct AS mp', 'mp.id', '=', 'tdol.product_id')
                ->leftJoin('msmachine AS msm', 'msm.id', '=', 'tdpg.machine_id');

            if (isset($this->tglMasuk) && $this->tglMasuk != '') {
                $data = $data->where('tdpg.production_date', '>=', $this->tglMasuk);
            }
            if (isset($this->tglKeluar) && $this->tglKeluar != '') {
                $data = $data->where('tdpg.production_date', '<=', $this->tglKeluar);
            }
            if (isset($this->lpk_no) && $this->lpk_no != "" && $this->lpk_no != "undefined") {
                $data = $data->where('tdol.lpk_no', 'ilike', "%{$this->lpk_no}%");
            }
            if (isset($this->searchTerm) && $this->searchTerm != '') {
                $data = $data->where(function ($query) {
                    $query->where('tdol.lpk_no', 'ilike', '%' . $this->searchTerm . '%')
                        ->orWhere('tdpg.production_no', 'ilike', '%' . $this->searchTerm . '%')
                        ->orWhere('tdpg.product_id', 'ilike', '%' . $this->searchTerm . '%')
                        ->orWhere('tdpg.machine_id', 'ilike', '%' . $this->searchTerm . '%')
                        ->orWhere('tdpg.nomor_palet', 'ilike', '%' . $this->searchTerm . '%')
                        ->orWhere('tdpg.nomor_lot', 'ilike', '%' . $this->searchTerm . '%');
                });
            }
            if (isset($this->idProduct) && $this->idProduct['value'] != "" && $this->idProduct != "undefined") {
                $data = $data->where('tdpg.product_id', $this->idProduct['value']);
            }
            if (isset($this->machineid) && $this->machineid['value'] != "" && $this->machineid != "undefined") {
                $data = $data->where('tdpg.machine_id', $this->machineid['value']);
            }
            if (isset($this->gentan_no) && $this->gentan_no != "" && $this->gentan_no != "undefined") {
                $data = $data->where('ta.gentan_no', $this->gentan_no);
            }
            if (isset($this->status) && $this->status['value'] != "" && $this->status != "undefined") {
                if ($this->status['value'] == 0) {
                    $data->where('tdpg.status_production', 0)
                        ->where('tdpg.status_warehouse', 0);
                } elseif ($this->status['value'] == 1) {
                    $data->where('tdpg.status_production', 1);
                } elseif ($this->status['value'] == 2) {
                    $data->where('tdpg.status_warehouse', 1);
                }
            }
            $data->orderBy('tdpg.production_date', 'desc');
            $data = $data->get();
        }
        return view('livewire.nippo-seitai.loss-seitai', [
            'data' => $data,
        ])->extends('layouts.master');
    }

    public function rendered()
    {
        $this->dispatch('initDataTable');
    }
}
