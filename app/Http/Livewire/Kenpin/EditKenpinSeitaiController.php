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

class EditKenpinSeitaiController extends Component
{
    public $idKenpinGoods;
    public $kenpin_no;
    public $kenpin_date;
    // public $name;
    public $code;
    public $empname;
    public $employeeno;
    public $details = [];
    public $nomor_palet;
    public $orderid;
    public $no_palet;
    public $no_lot;
    public $no_lpk;
    public $quantity;
    public $qty_loss;
    public $remark;
    public $status;
    public $idKenpinGoodDetailUpdate;
    public $beratLossTotal = 0;

    public function mount(Request $request)
    {
        $this->idKenpinGoods = $request->orderId;
        $data = TdKenpinGoods::where('id', $request->orderId)->first();
        $employee = MsEmployee::where('id', $data->employee_id)->first();
        $product = MsProduct::where('id', $data->product_id)->first();

        $this->kenpin_no = $data->kenpin_no;
        $this->kenpin_date = $data->kenpin_date;
        $this->code = $product->code;
        $this->empname = $employee->empname;
        $this->employeeno = $employee->employeeno;
        $this->qty_loss = $data->qty_loss;
        $this->remark = $data->remark;
        $this->status = $data->status_kenpin;


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
            ->where('tgd.kenpin_goods_id', $this->idKenpinGoods)
            ->get();

        $this->beratLossTotal = array_sum(array_map(function ($detail) {
            return $detail->qty_loss;
        }, $this->details->toArray()));
    }

    public function edit($idKenpinGoodDetailUpdate)
    {
        $this->idKenpinGoodDetailUpdate = $idKenpinGoodDetailUpdate;
        array_map(function ($detail) use ($idKenpinGoodDetailUpdate) {
            if ($detail->id == $idKenpinGoodDetailUpdate) {
                $this->no_palet = $detail->nomor_palet;
                $this->no_lot = $detail->nomor_lot;
                $this->no_lpk = $detail->lpk_no;
                $this->quantity = $detail->qty_produksi;
                $this->qty_loss = $detail->qty_loss;
            }
        }, $this->details->toArray());
    }

    public function deleteSeitai($id)
    {
        $data = TdKenpinGoodsDetail::where('product_goods_id', $id)->first();
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
                $detail->qty_loss = $validatedData['qty_loss'];
                break;
            }
        }

        // menghitung total berat loss
        $this->beratLossTotal = array_sum(array_map(function ($detail) {
            return $detail->qty_loss;
        }, $this->details->toArray()));

        $this->dispatch('notification', ['type' => 'success', 'message' => 'Data Berhasil di Simpan']);

        $this->dispatch('closeModal');
    }

    public function save()
    {
        $validatedData = $this->validate([
            'code' => 'required',
            'employeeno' => 'required',
            'status' => 'required'
        ]);

        DB::beginTransaction();
        try {
            $mspetugas = MsEmployee::where('employeeno', $this->employeeno)->first();
            $product = MsProduct::where('code', $this->code)->first();
            $productGoods = TdProductGoods::where('id', $this->orderid)->first();

            $data = TdKenpinGoods::where('id', $this->idKenpinGoods)->first();
            $data->kenpin_no = $this->kenpin_no;
            $data->kenpin_date = $this->kenpin_date;
            $data->employee_id = $mspetugas->id;
            $data->product_id = $product->id;
            $data->remark = $this->remark;
            $data->status_kenpin = $this->status;

            // menghitung total qty loss
            if (is_array($this->details)) {
                $qtyLoss = array_sum(array_column($this->details, 'qty_loss'));
            } else {
                // jika berupa collection
                $qtyLoss = $this->details->sum('qty_loss');
            }
            $data->qty_loss = $qtyLoss;

            $data->save();

            // hapus data pada kenpin goods detail
            TdKenpinGoodsDetail::where('kenpin_goods_id', $this->idKenpinGoods)->delete();

            // update pada kenpin goods detail
            foreach ($this->details as $detail) {
                $kenpinGoodsDetail = new TdKenpinGoodsDetail();
                $kenpinGoodsDetail->product_goods_id = $detail->id;
                $kenpinGoodsDetail->kenpin_goods_id = $data->id;
                $kenpinGoodsDetail->qty_loss = $detail->qty_loss ?? 0;
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

    public function search()
    {
        $this->render();
    }

    public function render()
    {
        if (isset($this->code) && $this->code != '') {
            $product = MsProduct::where('code', $this->code)->first();

            if ($product == null) {
                // session()->flash('error', 'Nomor PO ' . $this->po_no . ' Tidak Terdaftar');
                $this->dispatch('notification', ['type' => 'error', 'message' => 'Code ' . $this->code . ' Tidak Terdaftar']);
            } else {
                $this->name = $product->name;
            }
        }

        if (isset($this->employeeno) && $this->employeeno != '') {
            $msemployee = MsEmployee::where('employeeno', $this->employeeno)->first();

            if ($msemployee == null) {
                // session()->flash('error', 'Nomor PO ' . $this->po_no . ' Tidak Terdaftar');
                $this->dispatch('notification', ['type' => 'error', 'message' => 'Employee ' . $this->employeeno . ' Tidak Terdaftar']);
            } else {
                $this->empname = $msemployee->empname;
            }
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
        }

        return view('livewire.kenpin.edit-kenpin-seitai')->extends('layouts.master');
    }
}
