<?php

namespace App\Http\Livewire\Kenpin;

use App\Helpers\phpspreadsheet;
use Livewire\Component;
use App\Models\TdOrder;
use App\Models\MsBuyer;
use App\Models\MsEmployee;
use App\Models\MsProduct;
use App\Models\TdKenpinGoods;
use App\Models\TdKenpinGoodsDetail;
use App\Models\TdProductGoods;
use App\Models\MsMachinePartDetail;
use App\Models\MsMasalahKenpinSeitai;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Validate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class AddKenpinSeitaiController extends Component
{
    public $kenpin_no;
    public $kenpin_date;
    public $departemen = 'seitai';
    public $kode_produk;
    public $nama_produk;
    public $name;
    public $code;
    public $code_alias;
    public $empname;
    public $employeeno;
    public $kode_ng;
    public $nama_ng;
    public $penyebab;
    public $keterangan_penyebab;
    public $penanggulangan;
    public $bagian_mesin_id;
    public $bagianMesinList;
    public $details;
    public $nomor_palet;
    public $orderid;
    public $no_palet;
    public $no_lot;
    public $no_lpk;
    public $quantity;
    public $qty_loss;
    public $remark;
    public $status = 1;
    public $idKenpinGoodDetailUpdate;
    public $beratLossTotal;
    public $qtyProduksiTotal = 0;

    // data master produk
    public $masterKatanuki;
    public $product;
    public $photoKatanuki;
    public $katanuki_id;

    // Master data for NG codes
    public $masalahSeitai;

    public function mount()
    {
        $this->details = collect([]);
        $this->kenpin_date = Carbon::now()->format('d-m-Y');
        $today = Carbon::now();
        $lastKenpinGoods = TdKenpinGoods::where('kenpin_no', 'like', $today->format('ym') . '%')->orderBy('kenpin_no', 'desc')->first();
        $this->kenpin_no = $today->format('ym') . '-' . str_pad((int)substr($lastKenpinGoods->kenpin_no ?? 0, 5, 3) + 1, 3, '0', STR_PAD_LEFT);

        // Load bagian mesin list for Seitai department (department_id = 3)
        $this->bagianMesinList = MsMachinePartDetail::whereHas('machinePart', function ($query) {
            $query->where('department_id', 3);
        })->get();
    }

    public function edit($idKenpinGoodDetailUpdate)
    {
        $this->idKenpinGoodDetailUpdate = $idKenpinGoodDetailUpdate;
        array_map(function ($detail) use ($idKenpinGoodDetailUpdate) {
            if ($detail->id == $idKenpinGoodDetailUpdate) {
                $this->orderid = $detail->id;
                $this->no_palet = $detail->nomor_palet;
                $this->no_lot = $detail->nomor_lot;
                $this->no_lpk = $detail->lpk_no;
                $this->quantity = number_format($detail->qty_produksi);
                $this->qty_loss = number_format($detail->qty_loss);
            }
        }, $this->details->toArray());
    }

    public function updatedCode()
    {
        if (isset($this->code) && $this->code != '') {
            $product = MsProduct::where('code', 'ilike', $this->code . '%')->first();

            if ($product == null) {
                $this->dispatch('notification', ['type' => 'warning', 'message' => 'Nomor Order ' . $this->code . ' Tidak Terdaftar']);
                $this->name = '';
                $this->kode_produk = '';
                $this->nama_produk = '';
            } else {
                $this->code = $product->code;
                $this->name = $product->name;
                $this->kode_produk = $product->code;
                $this->nama_produk = $product->name;
                $this->resetValidation('code');
            }
        }
    }

    public function updatedEmployeeno()
    {
        if (isset($this->employeeno) && $this->employeeno != '' && strlen($this->employeeno) >= 2) {
            $msemployee = MsEmployee::where('employeeno', 'ilike', '%' . $this->employeeno . '%')->active()->first();

            if ($msemployee == null) {
                $this->dispatch('notification', ['type' => 'warning', 'message' => 'Employee ' . $this->employeeno . ' Tidak Terdaftar']);
                $this->employeeno = '';
                $this->empname = '';
            } else {
                $this->employeeno = $msemployee->employeeno;
                $this->empname = $msemployee->empname;
                $this->resetValidation('employeeno');
            }
        }
    }

    public function updatedKodeNg()
    {
        if (!empty($this->kode_ng)) {
            $this->masalahSeitai = MsMasalahKenpinSeitai::where('code', $this->kode_ng)->first();
            if ($this->masalahSeitai) {
                $this->nama_ng = $this->masalahSeitai->name;
                $this->resetValidation('kode_ng');
            } else {
                $this->kode_ng = '';
                $this->nama_ng = '';
                $this->dispatch('notification', ['type' => 'warning', 'message' => 'Kode NG tidak ditemukan']);
            }
        } else {
            $this->nama_ng = '';
        }
    }

    public function showModalNoOrder()
    {
        if (isset($this->code) && $this->code != '') {
            $this->product = MsProduct::where('code', $this->code)->first();
            if ($this->product == null) {
                $this->name = '';
                $this->dispatch('notification', ['type' => 'warning', 'message' => 'Nomor Order ' . $this->code . ' Tidak Terdaftar']);
            } else {
                // nomor order produk
                // $this->productNomorOrder = DB::table('msproduct')->where('code', $this->product_id)->first();
                $this->masterKatanuki = DB::table('mskatanuki')->where('id', $this->product->katanuki_id)->first(['name', 'filename']);

                // $this->code = $this->product->code;
                // $this->name = $this->product->name;
                $this->product->product_type_id = DB::table('msproduct_type')->where('id', $this->product->product_type_id)->first(['name'])->name ?? '';
                $this->product->product_unit = DB::table('msunit')->where('code', $this->product->product_unit)->first(['name'])->name ?? '';
                $this->product->material_classification = DB::table('msmaterial')->where('id', $this->product->material_classification)->first(['name'])->name ?? '';
                $this->product->embossed_classification = DB::table('msembossedclassification')->where('id', $this->product->embossed_classification)->first(['name'])->name ?? '';
                $this->product->surface_classification = DB::table('mssurfaceclassification')->where('id', $this->product->surface_classification)->first(['name'])->name ?? '';
                $this->product->gentan_classification = DB::table('msgentanclassification')->where('id', $this->product->gentan_classification)->first(['name'])->name ?? '';
                $this->product->gazette_classification = DB::table('msgazetteclassification')->where('id', $this->product->gazette_classification)->first(['name'])->name ?? '';
                $this->katanuki_id = $this->masterKatanuki->name ?? '';
                $this->photoKatanuki = $this->masterKatanuki->filename ?? '';
                $this->product->print_type = DB::table('msjeniscetak')->where('code', $this->product->print_type)->first(['name'])->name ?? '';
                $this->product->ink_characteristic = DB::table('mssifattinta')->where('code', $this->product->ink_characteristic)->first(['name'])->name ?? '';
                $this->product->endless_printing = DB::table('msendless')->where('code', $this->product->endless_printing)->first(['name'])->name ?? '';
                $this->product->winding_direction_of_the_web = DB::table('msarahgulung')->where('code', $this->product->winding_direction_of_the_web)->first(['name'])->name ?? '';
                $this->product->seal_classification = DB::table('msklasifikasiseal')->where('code', $this->product->seal_classification)->first(['name'])->name ?? '';
                $this->product->pack_gaiso_id = DB::table('mspackaginggaiso')->where('id', $this->product->pack_gaiso_id)->first(['name'])->name ?? '';
                $this->product->pack_box_id = DB::table('mspackagingbox')->where('id', $this->product->pack_box_id)->first(['name'])->name ?? '';
                $this->product->pack_inner_id = DB::table('mspackaginginner')->where('id', $this->product->pack_inner_id)->first(['name'])->name ?? '';
                $this->product->pack_layer_id = DB::table('mspackaginglayer')->where('id', $this->product->pack_layer_id)->first(['name'])->name ?? '';
                $this->product->case_gaiso_count_unit = DB::table('msunit')->where('id', $this->product->case_gaiso_count_unit)->first(['name'])->name ?? '';
                $this->product->case_box_count_unit = DB::table('msunit')->where('id', $this->product->case_box_count_unit)->first(['name'])->name ?? '';
                $this->product->case_inner_count_unit = DB::table('msunit')->where('id', $this->product->case_inner_count_unit)->first(['name'])->name ?? '';
                $this->product->lakbaninfureid = DB::table('mslakbaninfure')->where('id', $this->product->lakbaninfureid)->first(['name'])->name ?? '';
                $this->product->lakbanseitaiid = DB::table('mslakbanseitai')->where('id', $this->product->lakbanseitaiid)->first(['name'])->name ?? '';
                $this->product->stampelseitaiid = DB::table('msstampleseitai')->where('id', $this->product->stampelseitaiid)->first(['name'])->name ?? '';
                $this->product->hagataseitaiid = DB::table('mshagataseitai')->where('id', $this->product->hagataseitaiid)->first(['name'])->name ?? '';
                $this->product->jenissealseitaiid = DB::table('msjenissealseitai')->where('id', $this->product->jenissealseitaiid)->first(['name'])->name ?? '';

                // show modal
                $this->dispatch('showModalNoOrder');
            }
        } else {
            $this->dispatch('notification', ['type' => 'warning', 'message' => 'Nomor Order tidak boleh kosong']);
        }
    }

    public function deleteSeitai($orderId)
    {
        $data = TdKenpinGoodsDetail::where('product_goods_id', $orderId)->first();
        if ($data) {
            $data->delete();
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Data berhasil dihapus.']);
        } else {
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Data tidak ditemukan.']);
        }
    }

    public function saveSeitai()
    {
        $validatedData = $this->validate([
            'qty_loss' => 'required',
        ]);

        // update pada details
        foreach ($this->details as &$detail) {
            if ($detail->id == $this->idKenpinGoodDetailUpdate) {
                // Perform the update you need here
                $detail->qty_loss = (int)str_replace(',', '', $validatedData['qty_loss']);
                break;
            }
        }

        // menghitung total berat loss
        $this->beratLossTotal = $this->details->sum('qty_loss');
        $this->qtyProduksiTotal = $this->details->sum('qty_produksi');
        $this->dispatch('notification', ['type' => 'success', 'message' => 'Data Berhasil di Simpan']);

        $this->dispatch('closeModal');
    }

    public function save()
    {
        $validatedData = $this->validate([
            'code' => 'required',
            'employeeno' => 'required',
            'kode_ng' => 'required',
            'penyebab' => 'required',
            'keterangan_penyebab' => 'required',
            'penanggulangan' => 'required',
            'bagian_mesin_id' => 'required'
        ], [
            'code.required' => 'Nomor Order tidak boleh kosong',
            'employeeno.required' => 'Petugas tidak boleh kosong',
            'kode_ng.required' => 'Kode NG tidak boleh kosong',
            'penyebab.required' => 'Penyebab tidak boleh kosong',
            'keterangan_penyebab.required' => 'Keterangan penyebab tidak boleh kosong',
            'penanggulangan.required' => 'Penanggulangan tidak boleh kosong',
            'bagian_mesin_id.required' => 'Bagian mesin tidak boleh kosong'
        ]);

        DB::beginTransaction();
        try {
            $mspetugas = MsEmployee::where('employeeno', $this->employeeno)->first();
            $product = MsProduct::where('code', $this->code)->first();
            $productGoods = TdProductGoods::where('id', $this->orderid)->get();

            $data = new TdKenpinGoods();
            $data->kenpin_no = $this->kenpin_no;
            $data->kenpin_date = $this->kenpin_date;
            $data->employee_id = $mspetugas->id;
            $data->product_id = $product->id;
            $qtyLoss = $this->details->sum('qty_loss');
            $data->qty_loss = $qtyLoss;
            $data->remark = $this->remark;
            $data->status_kenpin = $this->status;

            // Add new fields for Seitai
            if ($this->masalahSeitai) {
                $data->masalah_seitai_id = $this->masalahSeitai->id;
            }
            $data->machine_part_detail_id = $this->bagian_mesin_id;
            $data->penyebab = $this->penyebab;
            $data->keterangan_penyebab = $this->keterangan_penyebab;
            $data->penanggulangan = $this->penanggulangan;

            $data->created_on = Carbon::now();
            $data->created_by = auth()->user()->username;
            $data->updated_on = Carbon::now();
            $data->updated_by = auth()->user()->username;

            $data->save();

            // update pada kenpin goods detail
            foreach ($this->details as $detail) {
                $kenpinGoodsDetail = new TdKenpinGoodsDetail();
                $kenpinGoodsDetail->product_goods_id = $detail->id;
                $kenpinGoodsDetail->kenpin_goods_id = $data->id;
                $kenpinGoodsDetail->qty_loss = $data->qty_loss ?? 0;
                $kenpinGoodsDetail->trial468 = 'T';
                $kenpinGoodsDetail->created_on = Carbon::now();
                $kenpinGoodsDetail->created_by = auth()->user()->username;
                $kenpinGoodsDetail->updated_on = Carbon::now();
                $kenpinGoodsDetail->updated_by = auth()->user()->username;
                $kenpinGoodsDetail->save();
            }

            DB::commit();
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Order saved successfully.']);
            return redirect()->route('kenpin-seitai-kenpin');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Failed to save the order: ' . $e->getMessage()]);
        }
    }

    public function export()
    {
        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();

        // Menghilangkan gridline
        $activeWorksheet->setShowGridlines(false);
        $activeWorksheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);

        $activeWorksheet->getPageSetup()->setFitToWidth(1);
        $activeWorksheet->getPageSetup()->setFitToHeight(0);
        // Jika ingin memastikan rasio tetap terjaga
        $activeWorksheet->getPageSetup()->setFitToPage(true);
        // Mengatur margin halaman menjadi 0.75 cm di semua sisi
        $activeWorksheet->getPageMargins()->setTop(0.75 / 2.54);
        $activeWorksheet->getPageMargins()->setBottom(0.75 / 2.54);
        $activeWorksheet->getPageMargins()->setLeft(0.75 / 2.54);
        $activeWorksheet->getPageMargins()->setRight(0.75 / 2.54);
        $activeWorksheet->getPageMargins()->setHeader(0.75 / 2.54);
        $activeWorksheet->getPageMargins()->setFooter(0.75 / 2.54);

        $startColumn = 'B';
        $endColumn = 'U';
        // Set Title Kenpin
        $rowTitleCard = 2;
        $spreadsheet->getActiveSheet()->setCellValue($startColumn . $rowTitleCard, 'KARTU KENPIN SEITAI');
        phpspreadsheet::styleFont($spreadsheet, $startColumn . $rowTitleCard, false, 20, 'Tahoma');
        phpspreadsheet::addBottomBorder($spreadsheet, $startColumn . $rowTitleCard . ':' . $endColumn . $rowTitleCard, 'FF000000');

        /**
         * Header Kenpin
         */
        // header nomor kenpin
        $columnHeaderNoKenpinStart = 'B';
        $columnHeaderNoKenpinEnd = 'E';
        $rowItem = 3;
        $spreadsheet->getActiveSheet()->mergeCells($columnHeaderNoKenpinStart . $rowItem . ':' . $columnHeaderNoKenpinEnd . $rowItem);
        $spreadsheet->getActiveSheet()->setCellValue($columnHeaderNoKenpinStart . $rowItem, 'Nomor Kenpin');

        // header tanggal kenpin
        $columnHeaderTanggalKenpinStart = 'F';
        $columnHeaderTanggalKenpinEnd = 'I';
        $spreadsheet->getActiveSheet()->mergeCells($columnHeaderTanggalKenpinStart . $rowItem . ':' . $columnHeaderTanggalKenpinEnd . $rowItem);
        $spreadsheet->getActiveSheet()->setCellValue($columnHeaderTanggalKenpinStart . $rowItem, 'Tanggal Kenpin');

        // header pic
        $columnHeaderPicStart = 'J';
        $columnHeaderPicEnd = 'P';
        $spreadsheet->getActiveSheet()->mergeCells($columnHeaderPicStart . $rowItem . ':' . $columnHeaderPicEnd . $rowItem);
        $spreadsheet->getActiveSheet()->setCellValue($columnHeaderPicStart . $rowItem, 'PIC');
        phpspreadsheet::addBottomBorderDotted($spreadsheet, $startColumn . $rowItem . ':' . $endColumn . $rowItem);

        // header kenpin kosong
        $columnHeaderKosongStart = 'Q';
        $columnHeaderKosongEnd = 'U';
        $spreadsheet->getActiveSheet()->mergeCells($columnHeaderKosongStart . $rowItem . ':' . $columnHeaderKosongEnd . $rowItem);

        phpSpreadsheet::styleFont($spreadsheet, $columnHeaderNoKenpinStart . $rowItem . ':' . $columnHeaderPicEnd . $rowItem, false, 8, 'Tahoma');
        phpSpreadsheet::textAlignCenter($spreadsheet, $columnHeaderNoKenpinStart . $rowItem . ':' . $columnHeaderPicEnd . $rowItem);
        $rowItem++;

        /**
         * Value kenpin
         */
        // value nomor kenpin
        $spreadsheet->getActiveSheet()->mergeCells($columnHeaderNoKenpinStart . $rowItem . ':' . $columnHeaderNoKenpinEnd . $rowItem);
        $spreadsheet->getActiveSheet()->setCellValue($columnHeaderNoKenpinStart . $rowItem, $this->kenpin_no);
        phpspreadsheet::styleFont($spreadsheet, $columnHeaderNoKenpinStart . $rowItem, true, 14, 'Tahoma', 'FFFFFFFF');
        phpspreadsheet::styleCell($spreadsheet, $columnHeaderNoKenpinStart . $rowItem, 'FF000000');
        phpspreadsheet::textAlignCenter($spreadsheet, $columnHeaderNoKenpinStart . $rowItem);

        // value tanggal kenpin
        $spreadsheet->getActiveSheet()->mergeCells($columnHeaderTanggalKenpinStart . $rowItem . ':' . $columnHeaderTanggalKenpinEnd . $rowItem);
        $spreadsheet->getActiveSheet()->setCellValue($columnHeaderTanggalKenpinStart . $rowItem, $this->kenpin_date);
        phpspreadsheet::styleFont($spreadsheet, $columnHeaderTanggalKenpinStart . $rowItem, false, 14, 'Tahoma');
        phpspreadsheet::textAlignCenter($spreadsheet, $columnHeaderTanggalKenpinStart . $rowItem);

        // value pic
        $spreadsheet->getActiveSheet()->mergeCells($columnHeaderPicStart . $rowItem . ':' . $columnHeaderPicEnd . $rowItem);
        $spreadsheet->getActiveSheet()->setCellValue($columnHeaderPicStart . $rowItem, $this->empname);
        phpspreadsheet::styleFont($spreadsheet, $columnHeaderPicStart . $rowItem, false, 14, 'Tahoma');
        phpspreadsheet::textAlignCenter($spreadsheet, $columnHeaderPicStart . $rowItem);
        phpspreadsheet::addBottomBorderDotted($spreadsheet, $startColumn . $rowItem . ':' . $endColumn . $rowItem);
        $rowItem++;

        /**
         * Header Order
         */
        // header no order
        $endColumnNoOrder = 'D';
        $spreadsheet->getActiveSheet()->mergeCells($startColumn . $rowItem . ':' . $endColumnNoOrder . $rowItem);
        $spreadsheet->getActiveSheet()->setCellValue($startColumn . $rowItem, 'No Order');
        phpspreadsheet::styleFont($spreadsheet, $startColumn . $rowItem, false, 8, 'Tahoma');

        // header kode produk
        $startColumnKodeProduk = 'E';
        $endColumnKodeProduk = 'G';
        $spreadsheet->getActiveSheet()->mergeCells($startColumnKodeProduk . $rowItem . ':' . $endColumnKodeProduk . $rowItem);
        $spreadsheet->getActiveSheet()->setCellValue($startColumnKodeProduk . $rowItem, 'Kode Produk');
        phpspreadsheet::styleFont($spreadsheet, $startColumnKodeProduk . $rowItem, false, 8, 'Tahoma');

        // header nama produk
        $startColumnNamaProduk = 'H';
        $endColumnNamaProduk = 'U';
        $spreadsheet->getActiveSheet()->mergeCells($startColumnNamaProduk . $rowItem . ':' . $endColumnNamaProduk . $rowItem);
        $spreadsheet->getActiveSheet()->setCellValue($startColumnNamaProduk . $rowItem, 'Nama Produk');
        phpspreadsheet::styleFont($spreadsheet, $startColumnNamaProduk . $rowItem, false, 8, 'Tahoma');
        phpspreadsheet::addBottomBorderDotted($spreadsheet, $startColumn . $rowItem . ':' . $endColumn . $rowItem);
        $rowItem++;

        /**
         * Value Order
         */
        // value no order
        $spreadsheet->getActiveSheet()->mergeCells($startColumn . $rowItem . ':' . $endColumnNoOrder . $rowItem);
        $spreadsheet->getActiveSheet()->setCellValue($startColumn . $rowItem, $this->code);
        phpspreadsheet::styleFont($spreadsheet, $startColumn . $rowItem, false, 14, 'Tahoma');

        // value kode produk
        $spreadsheet->getActiveSheet()->mergeCells($startColumnKodeProduk . $rowItem . ':' . $endColumnKodeProduk . $rowItem);
        $spreadsheet->getActiveSheet()->setCellValue($startColumnKodeProduk . $rowItem, $this->code_alias);
        phpspreadsheet::styleFont($spreadsheet, $startColumnKodeProduk . $rowItem, false, 14, 'Tahoma');

        // value nama produk
        $spreadsheet->getActiveSheet()->mergeCells($startColumnNamaProduk . $rowItem . ':' . $endColumnNamaProduk . $rowItem);
        $spreadsheet->getActiveSheet()->setCellValue($startColumnNamaProduk . $rowItem, $this->name);
        phpspreadsheet::styleFont($spreadsheet, $startColumnNamaProduk . $rowItem, false, 14, 'Tahoma');
        phpspreadsheet::addBottomBorderDotted($spreadsheet, $startColumn . $rowItem . ':' . $endColumn . $rowItem);
        $rowItem++;

        /**
         * Masalah
         */
        // header masalah
        $spreadsheet->getActiveSheet()->mergeCells($startColumn . $rowItem . ':' . $endColumnNoOrder . $rowItem);
        $spreadsheet->getActiveSheet()->setCellValue($startColumn . $rowItem, 'Masalah :');
        phpspreadsheet::styleFont($spreadsheet, $startColumn . $rowItem, false, 10, 'Tahoma');

        // value masalah
        $startColumnMasalah = 'E';
        $spreadsheet->getActiveSheet()->mergeCells($startColumnMasalah . $rowItem . ':' . $endColumn . $rowItem);
        $spreadsheet->getActiveSheet()->setCellValue($startColumnMasalah . $rowItem, $this->remark);
        phpspreadsheet::styleFont($spreadsheet, $startColumnMasalah . $rowItem, false, 14, 'Tahoma');
        phpspreadsheet::addBottomBorderDotted($spreadsheet, $startColumn . $rowItem . ':' . $endColumn . $rowItem);
        $rowItem++;

        /**
         * Header Jumlah
         */
        // header jumlah
        $startColumnJumlah = 'O';
        $endColumnJumlah = 'R';
        $spreadsheet->getActiveSheet()->mergeCells($startColumnJumlah . $rowItem . ':' . $endColumnJumlah . $rowItem);
        $spreadsheet->getActiveSheet()->setCellValue($startColumnJumlah . $rowItem, 'Jumlah');
        phpspreadsheet::styleFont($spreadsheet, $startColumnJumlah . $rowItem, false, 14, 'Tahoma');
        phpspreadsheet::textAlignCenter($spreadsheet, $startColumnJumlah . $rowItem);

        /**
         * Value Jumlah
         */
        // value jumlah
        $startColumnValueJumlah = 'S';
        $spreadsheet->getActiveSheet()->mergeCells($startColumnValueJumlah . $rowItem . ':' . $endColumn . $rowItem);
        $spreadsheet->getActiveSheet()->setCellValue($startColumnValueJumlah . $rowItem, number_format($this->qtyProduksiTotal));
        phpspreadsheet::styleFont($spreadsheet, $startColumnValueJumlah . $rowItem, false, 14, 'Tahoma');
        phpspreadsheet::textAlignCenter($spreadsheet, $startColumnValueJumlah . $rowItem);
        phpspreadsheet::addBottomBorder($spreadsheet, $startColumn . $rowItem . ':' . $endColumn . $rowItem);
        $rowItem++;

        /**
         * Header Details
         */
        // no lpk
        $endColumnNoLPK = 'E';
        $spreadsheet->getActiveSheet()->mergeCells($startColumn . $rowItem . ':' . $endColumnNoOrder . $rowItem);
        $spreadsheet->getActiveSheet()->setCellValue($startColumn . $rowItem, 'No LPK');
        phpspreadsheet::styleFont($spreadsheet, $startColumn . $rowItem, false, 11, 'Tahoma');
        phpspreadsheet::textAlignCenter($spreadsheet, $startColumn . $rowItem);

        // no palet
        $startColumnNoPalet = 'F';
        $endColumnNoPalet = 'H';
        $spreadsheet->getActiveSheet()->mergeCells($startColumnNoPalet . $rowItem . ':' . $endColumnNoPalet . $rowItem);
        $spreadsheet->getActiveSheet()->setCellValue($startColumnNoPalet . $rowItem, 'No Palet');
        phpspreadsheet::styleFont($spreadsheet, $startColumnNoPalet . $rowItem, false, 11, 'Tahoma');
        phpspreadsheet::textAlignCenter($spreadsheet, $startColumnNoPalet . $rowItem);

        // tanggal produksi
        $startColumnTanggalProduksi = 'I';
        $endColumnTanggalProduksi = 'L';
        $spreadsheet->getActiveSheet()->mergeCells($startColumnTanggalProduksi . $rowItem . ':' . $endColumnTanggalProduksi . $rowItem);
        $spreadsheet->getActiveSheet()->setCellValue($startColumnTanggalProduksi . $rowItem, 'Tanggal Produksi');
        phpspreadsheet::styleFont($spreadsheet, $startColumnTanggalProduksi . $rowItem, false, 11, 'Tahoma');
        phpspreadsheet::textAlignCenter($spreadsheet, $startColumnTanggalProduksi . $rowItem);

        // SHift
        $startColumnShift = 'M';
        $endColumnShift = 'N';
        $spreadsheet->getActiveSheet()->mergeCells($startColumnShift . $rowItem . ':' . $endColumnShift . $rowItem);
        $spreadsheet->getActiveSheet()->setCellValue($startColumnShift . $rowItem, 'Shift');
        phpspreadsheet::styleFont($spreadsheet, $startColumnShift . $rowItem, false, 11, 'Tahoma');
        phpspreadsheet::textAlignCenter($spreadsheet, $startColumnShift . $rowItem);

        // Nomor lot
        $startColumnNomorLot = 'O';
        $endColumnNomorLot = 'R';
        $spreadsheet->getActiveSheet()->mergeCells($startColumnNomorLot . $rowItem . ':' . $endColumnNomorLot . $rowItem);
        $spreadsheet->getActiveSheet()->setCellValue($startColumnNomorLot . $rowItem, 'Nomor Lot');
        phpspreadsheet::styleFont($spreadsheet, $startColumnNomorLot . $rowItem, false, 11, 'Tahoma');
        phpspreadsheet::textAlignCenter($spreadsheet, $startColumnNomorLot . $rowItem);

        // quantity
        $startColumnQuantity = 'S';
        $endColumnQuantity = 'U';
        $spreadsheet->getActiveSheet()->mergeCells($startColumnQuantity . $rowItem . ':' . $endColumnQuantity . $rowItem);
        $spreadsheet->getActiveSheet()->setCellValue($startColumnQuantity . $rowItem, 'Quantity');
        phpspreadsheet::styleFont($spreadsheet, $startColumnQuantity . $rowItem, false, 11, 'Tahoma');
        phpspreadsheet::textAlignCenter($spreadsheet, $startColumnQuantity . $rowItem);
        phpspreadsheet::addBottomBorder($spreadsheet, $startColumn . $rowItem . ':' . $endColumn . $rowItem);
        $rowItem++;

        /**
         * Value detail
         */
        foreach ($this->details as $detail) {
            // value no lpk
            $spreadsheet->getActiveSheet()->mergeCells($startColumn . $rowItem . ':' . $endColumnNoOrder . $rowItem);
            $spreadsheet->getActiveSheet()->setCellValue($startColumn . $rowItem, $detail->lpk_no);
            phpspreadsheet::styleFont($spreadsheet, $startColumn . $rowItem, false, 11, 'Tahoma');
            phpspreadsheet::textAlignCenter($spreadsheet, $startColumn . $rowItem);

            // value no palet
            $spreadsheet->getActiveSheet()->mergeCells($startColumnNoPalet . $rowItem . ':' . $endColumnNoPalet . $rowItem);
            $spreadsheet->getActiveSheet()->setCellValue($startColumnNoPalet . $rowItem, $detail->nomor_palet);
            phpspreadsheet::styleFont($spreadsheet, $startColumnNoPalet . $rowItem, false, 11, 'Tahoma');
            phpspreadsheet::textAlignCenter($spreadsheet, $startColumnNoPalet . $rowItem);

            // value tanggal produksi
            $spreadsheet->getActiveSheet()->mergeCells($startColumnTanggalProduksi . $rowItem . ':' . $endColumnTanggalProduksi . $rowItem);
            $spreadsheet->getActiveSheet()->setCellValue($startColumnTanggalProduksi . $rowItem, Carbon::parse($detail->production_date)->format('d-m-Y'));
            phpspreadsheet::styleFont($spreadsheet, $startColumnTanggalProduksi . $rowItem, false, 11, 'Tahoma');
            phpspreadsheet::textAlignCenter($spreadsheet, $startColumnTanggalProduksi . $rowItem);

            // value shift
            $spreadsheet->getActiveSheet()->mergeCells($startColumnShift . $rowItem . ':' . $endColumnShift . $rowItem);
            $spreadsheet->getActiveSheet()->setCellValue($startColumnShift . $rowItem, $detail->work_shift);
            phpspreadsheet::styleFont($spreadsheet, $startColumnShift . $rowItem, false, 11, 'Tahoma');
            phpspreadsheet::textAlignCenter($spreadsheet, $startColumnShift . $rowItem);

            // value nomor lot
            $spreadsheet->getActiveSheet()->mergeCells($startColumnNomorLot . $rowItem . ':' . $endColumnNomorLot . $rowItem);
            $spreadsheet->getActiveSheet()->setCellValue($startColumnNomorLot . $rowItem, $detail->nomor_lot);
            phpspreadsheet::styleFont($spreadsheet, $startColumnNomorLot . $rowItem, false, 11, 'Tahoma');
            phpspreadsheet::textAlignCenter($spreadsheet, $startColumnNomorLot . $rowItem);

            // value quantity
            $spreadsheet->getActiveSheet()->mergeCells($startColumnQuantity . $rowItem . ':' . $endColumnQuantity . $rowItem);
            $spreadsheet->getActiveSheet()->setCellValue($startColumnQuantity . $rowItem, number_format($detail->qty_produksi));
            phpspreadsheet::styleFont($spreadsheet, $startColumnQuantity . $rowItem, false, 11, 'Tahoma');
            phpspreadsheet::textAlignCenter($spreadsheet, $startColumnQuantity . $rowItem);
            phpspreadsheet::addBottomBorderDotted($spreadsheet, $startColumn . $rowItem . ':' . $endColumn . $rowItem);
            $rowItem++;
        }

        // membuat border untuk seluruh cell
        while ($startColumn !== $endColumn) {
            $spreadsheet->getActiveSheet()->getColumnDimension($startColumn)->setWidth(35, 'px');

            $startColumn++;
        }

        $this->save();

        $writer = new Xlsx($spreadsheet);
        $writer->save('asset/report/KKSeitai-' . $this->kenpin_no . '.xlsx');
        return response()->download('asset/report/KKSeitai-' . $this->kenpin_no . '.xlsx');
    }

    public function cancel()
    {
        return redirect()->route('kenpin-seitai-kenpin');
    }

    public function rules()
    {
        return [
            'code' => 'required',
            'employeeno' => 'required',
            'kode_ng' => 'required',
            'penyebab' => 'required',
            'keterangan_penyebab' => 'required',
            'penanggulangan' => 'required',
            'bagian_mesin_id' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'code.required' => 'Nomor Order tidak boleh kosong',
            'employeeno.required' => 'Petugas tidak boleh kosong',
            'kode_ng.required' => 'Kode NG tidak boleh kosong',
            'penyebab.required' => 'Penyebab tidak boleh kosong',
            'keterangan_penyebab.required' => 'Keterangan penyebab tidak boleh kosong',
            'penanggulangan.required' => 'Penanggulangan tidak boleh kosong',
            'bagian_mesin_id.required' => 'Bagian mesin tidak boleh kosong'
        ];
    }


    public function addPalet()
    {
        try {
            $this->validate();
        } catch (ValidationException $e) {
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Data belum lengkap']);
            $this->setErrorBag($e->validator->errors());

            return;
        }

        if (isset($this->nomor_palet) && $this->nomor_palet != '') {
            $product = MsProduct::where('code', $this->code)->first();
            $this->details = DB::table('tdproduct_goods AS tdpg')
                ->select(
                    'tdpg.id AS id',
                    'tdpg.production_no AS production_no',
                    'tdpg.production_date AS production_date',
                    'tdpg.lpk_id AS lpk_id',
                    'tdpg.product_id AS product_id',
                    'tdpg.work_shift AS work_shift',
                    'msp.code AS code',
                    'msp.name AS namaproduk',
                    'tdpg.qty_produksi AS qty_produksi',
                    'tdpg.nomor_palet AS nomor_palet',
                    'tdpg.nomor_lot AS nomor_lot',
                    'tdol.order_id AS order_id',
                    'tdol.lpk_no AS lpk_no',
                    'tdol.lpk_date AS lpk_date',
                    'tgd.qty_loss'
                )
                ->join('tdorderlpk AS tdol', 'tdpg.lpk_id', '=', 'tdol.id')
                ->join('msproduct AS msp', 'tdpg.product_id', '=', 'msp.id')
                ->leftJoin('tdkenpin_goods_detail AS tgd', 'tgd.product_goods_id', '=', 'tdpg.id')
                ->where('tdpg.product_id', $product->id)
                ->where('tdpg.nomor_palet', $this->nomor_palet)
                ->get();

            $this->qtyProduksiTotal = $this->details->sum('qty_produksi');

            if ($this->details == null) {
                // session()->flash('error', 'Nomor PO ' . $this->po_no . ' Tidak Terdaftar');
                $this->dispatch('notification', ['type' => 'error', 'message' => 'Employee ' . $this->details . ' Tidak Terdaftar']);
            }
        } else {
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Nomor Palet yang dicari tidak boleh kosong']);
        }
    }

    public function search()
    {
        $this->render();
    }

    public function render()
    {
        if (isset($this->code) && $this->code != '') {
            $product = MsProduct::where('code', 'ilike', $this->code . '%')->first();

            if ($product == null) {
                $this->dispatch('notification', ['type' => 'error', 'message' => 'Nomor Order ' . $this->code . ' Tidak Terdaftar']);
                $this->code = '';
                $this->name = '';
                $this->kode_produk = '';
                $this->nama_produk = '';
            } else {
                $this->resetValidation('code');
                $this->code = $product->code;
                $this->name = $product->name;
                $this->kode_produk = $product->code;
                $this->nama_produk = $product->name;
            }
        }

        if (isset($this->employeeno) && $this->employeeno != '' && strlen($this->employeeno) >= 2) {
            $msemployee = MsEmployee::where('employeeno', 'ilike', '%' . $this->employeeno . '%')->active()->first();

            if ($msemployee == null) {
                $this->dispatch('notification', ['type' => 'error', 'message' => 'Petugas ' . $this->employeeno . ' Tidak Terdaftar']);
                $this->empname = '';
            } else {
                $this->resetValidation('employeeno');
                $this->empname = $msemployee->empname;
                $this->employeeno = $msemployee->employeeno;
            }
        }

        return view('livewire.kenpin.add-kenpin-seitai');
    }
}
