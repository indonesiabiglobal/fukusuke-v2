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

class AddKenpinSeitaiController extends Component
{
    public $kenpin_no;
    public $kenpin_date;
    public $name;
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

    public function mount()
    {
        $this->kenpin_date = Carbon::now()->format('Y-m-d');
        $today = Carbon::now();
        $this->kenpin_no = $today->format('ym').'-001';
    }

    public function edit($orderid)
    {
        $item = DB::table('tdproduct_goods AS tdpg')
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
            'tdol.lpk_date AS lpk_date'
        )
        ->join('tdorderlpk AS tdol', 'tdpg.lpk_id', '=', 'tdol.id')
        ->join('msproduct AS msp', 'tdpg.product_id', '=', 'msp.id')
        ->where('tdpg.id', $orderid)
        ->first();
        if($item){
            $this->orderid = $item->id;
            $this->no_palet = $item->nomor_palet;
            $this->no_lot = $item->nomor_lot;
            $this->no_lpk = $item->lpk_no;
            $this->quantity = $item->qty_produksi;
        }
    }

    public function deleteSeitai($orderId)
    {        
        $data = TdKenpinGoodsDetail::where('product_goods_id', $orderId)->first();
        if ($data) {
            $data->delete();
            $this->dispatchBrowserEvent('notification', ['type' => 'success', 'message' => 'Data berhasil dihapus.']);
        } else {
            $this->dispatchBrowserEvent('notification', ['type' => 'error', 'message' => 'Data tidak ditemukan.']);
        }
    }

    public function saveSeitai()
    {
        $validatedData = $this->validate([
            'qty_loss' => 'required',
        ]);
        
        $productGoods = TdProductGoods::where('id', $this->orderid)->first();
        
        $datas = new TdKenpinGoodsDetail();
        $datas->product_goods_id = $productGoods->id;
        $datas->qty_loss = $this->qty_loss;
        $datas->trial468 = 'T';
        
        $datas->save();
        $this->dispatchBrowserEvent('notification', ['type' => 'success', 'message' => 'Data Berhasil di Simpan']);

        $this->emit('closeModal');
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
            $mspetugas=MsEmployee::where('employeeno', $this->employeeno)->first();
            $product=MsProduct::where('code', $this->code)->first();
            $productGoods = TdProductGoods::where('id', $this->orderid)->first();
            
            $data = new TdKenpinGoods();
            $data->kenpin_no = $this->kenpin_no;
            $data->kenpin_date = $this->kenpin_date;
            $data->employee_id = $mspetugas->id;
            $data->product_id = $product->id;
            $data->qty_loss = $this->qty_loss;
            $data->remark = $this->remark;
            $data->status_kenpin = $this->status;
            
            $data->save();

            TdKenpinGoodsDetail::where('product_goods_id', $productGoods->id)->update([
                'kenpin_goods_id' => $data->id,
            ]);
            
            DB::commit();
            $transStatus = 'true';            
        } catch (\Exception $e) {            
            $transStatus = 'false';
            DB::rollBack();
        }
        
        if ($transStatus == 'true') {
            session()->flash('notification', ['type' => 'success', 'message' => 'Order saved successfully.']);
            return redirect()->route('kenpin-infure');
        } else {
            $this->dispatchBrowserEvent('notification', ['type' => 'error', 'message' => 'Failed to save the order: ' . $e->getMessage()]);
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
        if(isset($this->code) && $this->code != ''){
            $product=MsProduct::where('code', $this->code)->first();

            if($product == null){
                // session()->flash('error', 'Nomor PO ' . $this->po_no . ' Tidak Terdaftar');
                $this->dispatchBrowserEvent('notification', ['type' => 'error', 'message' => 'Code ' . $this->code . ' Tidak Terdaftar']);
            } else {
                $this->name = $product->name;
            }
        }

        if(isset($this->employeeno) && $this->employeeno != ''){
            $msemployee=MsEmployee::where('employeeno', $this->employeeno)->first();

            if($msemployee == null){
                // session()->flash('error', 'Nomor PO ' . $this->po_no . ' Tidak Terdaftar');
                $this->dispatchBrowserEvent('notification', ['type' => 'error', 'message' => 'Employee ' . $this->employeeno . ' Tidak Terdaftar']);
            } else {
                $this->empname = $msemployee->empname;
            }
        }

        if(isset($this->nomor_palet) && $this->nomor_palet != ''){
            $product=MsProduct::where('code', $this->code)->first();
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

            if($this->details == null){
                // session()->flash('error', 'Nomor PO ' . $this->po_no . ' Tidak Terdaftar');
                $this->dispatchBrowserEvent('notification', ['type' => 'error', 'message' => 'Employee ' . $this->details . ' Tidak Terdaftar']);
            }
        }

        return view('livewire.kenpin.add-kenpin-seitai')->extends('layouts.master');
    }
}

