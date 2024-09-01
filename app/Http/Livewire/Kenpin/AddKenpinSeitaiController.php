<?php

namespace App\Http\Livewire\Kenpin;

use Livewire\Component;
use App\Models\TdOrder;
use App\Models\MsBuyer;
use App\Models\MsEmployee;
use App\Models\MsProduct;
use App\Models\TdKenpinGoods;
use App\Models\TdKenpinGoodsDetail;
use App\Models\TdProductGoods;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Validate;

class AddKenpinSeitaiController extends Component
{
    public $kenpin_no;
    public $kenpin_date;
    public $name;
    public $code;
    public $empname;
    public $employeeno;
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

    // data master produk
    public $masterKatanuki;
    public $product;
    public $photoKatanuki;
    public $katanuki_id;

    public function mount()
    {
        $this->details = collect([]);
        $this->kenpin_date = Carbon::now()->format('d-m-Y');
        $today = Carbon::now();
        $lastKenpinGoods = TdKenpinGoods::where('kenpin_no', 'like', $today->format('ym') . '%')->orderBy('kenpin_no', 'desc')->first();
        $this->kenpin_no = $today->format('ym') .'-'. str_pad((int)substr($lastKenpinGoods->kenpin_no ?? 0, 5, 3) + 1, 3, '0', STR_PAD_LEFT);
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
                // dd($this->product);

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
        $this->dispatch('notification', ['type' => 'success', 'message' => 'Data Berhasil di Simpan']);

        $this->dispatch('closeModal');
    }

    public function save()
    {
        try {
            $this->validate();
            // Kode Anda jika validasi berhasil
        } catch (ValidationException $e) {
            // Tangani validasi yang gagal
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Data belum lengkap']);

            // Mengirimkan pesan error ke view Livewire secara manual jika diperlukan
            $this->setErrorBag($e->validator->errors());

            return;
        }

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

            $data->save();

            // update pada kenpin goods detail
            foreach ($this->details as $detail) {
                $kenpinGoodsDetail = new TdKenpinGoodsDetail();
                $kenpinGoodsDetail->product_goods_id = $detail->id;
                $kenpinGoodsDetail->kenpin_goods_id = $data->id;
                $kenpinGoodsDetail->qty_loss = $data->qty_loss ?? 0;
                $kenpinGoodsDetail->trial468 = 'T';
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

    public function cancel()
    {
        return redirect()->route('kenpin-seitai-kenpin');
    }

    public function rules()
    {
        return [
            'code' => 'required',
            'employeeno' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'code.required' => 'Nomor Order tidak boleh kosong',
            'employeeno.required' => 'Petugas tidak boleh kosong',
        ];
    }


    public function addPalet()
    {
        try {
            $this->validate();
            // Kode Anda jika validasi berhasil
        } catch (ValidationException $e) {
            // Tangani validasi yang gagal
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Data belum lengkap']);

            // Mengirimkan pesan error ke view Livewire secara manual jika diperlukan
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
                // session()->flash('error', 'Nomor PO ' . $this->po_no . ' Tidak Terdaftar');
                $this->dispatch('notification', ['type' => 'error', 'message' => 'Nomor Order ' . $this->code . ' Tidak Terdaftar']);
                $this->code = '';
                $this->name = '';
            } else {
                $this->resetValidation('code');
                $this->code = $product->code;
                $this->name = $product->name;
            }
        }

        if (isset($this->employeeno) && $this->employeeno != '' && strlen($this->employeeno) >= 2) {
            $msemployee = MsEmployee::where('employeeno', 'ilike', '%' . $this->employeeno . '%')->active()->first();

            if ($msemployee == null) {
                // session()->flash('error', 'Nomor PO ' . $this->po_no . ' Tidak Terdaftar');
                $this->dispatch('notification', ['type' => 'error', 'message' => 'Petugas ' . $this->employeeno . ' Tidak Terdaftar']);
                $this->empname = '';
            } else {
                $this->resetValidation('employeeno');
                $this->empname = $msemployee->empname;
                $this->employeeno = $msemployee->employeeno;
            }
        }

        return view('livewire.kenpin.add-kenpin-seitai')->extends('layouts.master');
    }
}
