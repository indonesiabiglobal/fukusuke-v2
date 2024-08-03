<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\TdOrderLpk;
use App\Models\MsBuyer;
use App\Models\MsMachine;
use App\Models\MsProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EditLpkController extends Component
{
    public $buyer;
    public $orderId;
    public $lpk_date;
    public $lpk_no;
    public $po_no;
    public $order_id;
    public $machineno;
    public $machinename;
    public $qty_lpk;
    public $qty_gentan;
    public $qty_gulung;
    public $panjang_lpk;
    public $processdate;
    public $tgl_po;
    public $buyer_name;
    public $product_name;
    public $order_date;
    public $no_order;
    public $total_assembly_line;
    public $productlength;
    public $defaultgulung;
    public $selisihkurang;
    public $dimensi;
    public $remark;
    public $warnalpkid;

    public $masterWarnaLPK;

    protected $rules = [
        'lpk_date' => 'required',
        'lpk_no' => 'required',
        'po_no' => 'required',
        'order_id' => 'required',
        'machineno' => 'required',
        'qty_lpk' => 'required',
        'qty_gentan' => 'required',
        'qty_gulung' => 'required',
        'panjang_lpk' => 'required',
        'warnalpkid' => 'required',
        // 'tglproses' => 'required',
        // 'buyer_id' => 'required',
        // 'product_id' => 'required',
    ];

    public function mount(Request $request)
    {
        $this->total_assembly_line=0;
        $this->productlength=1;
        $this->defaultgulung=1;
        // master warna LPK
        $this->masterWarnaLPK = DB::table('mswarnalpk')->get();

        $order = DB::table('tdorderlpk as tolp')
        ->select(
            'tolp.id',
            'tolp.order_id',
            'tolp.lpk_no',
            'tolp.lpk_date',
            'tolp.panjang_lpk',
            'tolp.qty_lpk',
            'tolp.qty_gentan',
            'tolp.qty_gulung',
            'tolp.total_assembly_line as infure',
            'tolp.total_assembly_qty',
            'tolp.total_assembly_line',
            'tolp.warnalpkid',
            'tolp.remark',
            'tod.po_no',
            'mp.name as product_name',
            'mp.code',
            'mp.ketebalan',
            'mp.diameterlipat',
            'mp.productlength',
            'tod.product_code',
            'tod.order_date',
            'mm.machineno',
            'mm.machinename',
            'mbu.id as buyer_id',
            'mbu.name as buyer_name',
            'tolp.created_on as tglproses',
            'mp.productlength',
            'tolp.seq_no',
            'tolp.updated_by',
            'tolp.updated_on as updatedt'
        )
        ->join('tdorder as tod', 'tod.id', '=', 'tolp.order_id')
        ->leftJoin('msproduct as mp', 'mp.id', '=', 'tolp.product_id')
        ->join('msmachine as mm', 'mm.id', '=', 'tolp.machine_id')
        ->join('msbuyer as mbu', 'mbu.id', '=', 'tod.buyer_id')
        ->where('tolp.id', $request->query('orderId'))
        ->first();

        $this->lpk_date = Carbon::parse($order->lpk_date)->format('Y-m-d');
        $this->orderId = $order->id;
        $this->lpk_no = $order->lpk_no;
        $this->po_no = $order->po_no;
        $this->order_id = $order->order_id;
        $this->machineno = $order->machineno;
        $this->machinename = $order->machinename;
        $this->qty_lpk = $order->qty_lpk;
        $this->qty_gentan = $order->qty_gentan;
        $this->qty_gulung = $order->qty_gulung;
        $this->panjang_lpk = $order->panjang_lpk;
        $this->processdate = Carbon::parse($order->tglproses)->format('Y-m-d');
        $this->order_date = Carbon::parse($order->order_date)->format('Y-m-d');
        $this->buyer_name = $order->buyer_name;
        $this->product_name = $order->product_name;
        $this->no_order = $order->code;
        $this->dimensi = $order->ketebalan.'x'.$order->diameterlipat.'x'.$order->productlength;
        $this->total_assembly_line = $order->total_assembly_line;
        $this->productlength = $order->productlength;
        $this->remark = $order->remark;
        $this->warnalpkid['value'] = $order->warnalpkid;
    }

    public function save()
    {
        $this->validate();

        DB::beginTransaction();
        try {
            $machine=MsMachine::where('machineno', $this->machineno)->first();

            $orderlpk = TdOrderLpk::findOrFail($this->orderId);
            $orderlpk->lpk_no = $this->lpk_no;
            $orderlpk->lpk_date = $this->lpk_date;
            $orderlpk->order_id = $orderlpk->id;
            $orderlpk->product_id = $orderlpk->product_id;
            $orderlpk->machine_id = $machine->id;
            $orderlpk->qty_lpk = $this->qty_lpk;
            if(isset($this->remark)){
                $orderlpk->remark = $this->remark;
            }
            $orderlpk->qty_gentan = $this->qty_gentan;
            $orderlpk->panjang_lpk = $this->panjang_lpk;
            $orderlpk->total_assembly_line = $this->total_assembly_line;
            $orderlpk->qty_gulung = $this->qty_gulung;
            $orderlpk->warnalpkid = $this->warnalpkid['value'];
            $orderlpk->updated_by = auth()->user()->username;
            $orderlpk->updated_on = Carbon::now()->format('Y-m-d H:i:s');

            $orderlpk->save();

            DB::commit();
            $this->dispatch('notification', ['type' => 'success', 'message' => 'LPK updated successfully.']);
            return redirect()->route('lpk-entry');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Failed to save the order: ' . $e->getMessage()]);
        }
    }

    public function delete()
    {
        DB::beginTransaction();
        try {
            $order = TdOrderLpk::where('id', $this->orderId)->first();
            $order->delete();

            DB::commit();
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Order deleted successfully.']);
            return redirect()->route('lpk-entry');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Failed to save the order: ' . $e->getMessage()]);
        }
    }

    public function cancel()
    {
        return redirect()->route('lpk-entry');
    }

    public function print()
    {
        $lpk_id= $this->orderId;
        $this->dispatch('redirectToPrint', $lpk_id);
    }

    public function render()
    {
        if(isset($this->po_no) && $this->po_no != ''){
            $tdorder = DB::table('tdorder as tod')
            ->join('msproduct as mp', 'mp.id', '=', 'tod.product_id')
            ->join('msbuyer as mbu', 'mbu.id', '=', 'tod.buyer_id')
            ->select(
                'tod.id',
                'tod.product_code',
                'tod.processdate',
                'tod.order_date',
                'mp.name as produk_name',
                'mbu.name as buyer_name',
                'mp.ketebalan',
                'mp.diameterlipat',
                'mp.productlength',
                'mp.one_winding_m_number'
            )
            ->where('po_no', $this->po_no)
            ->first();


            if($tdorder == null){
                $this->dispatch('notification', ['type' => 'warning', 'message' => 'Nomor PO ' . $this->po_no . ' Tidak Terdaftar']);
            } else {
                $this->no_order = $tdorder->product_code;
                $this->processdate = $tdorder->processdate;
                $this->order_date = $tdorder->order_date;
                $this->buyer_name = $tdorder->buyer_name;
                $this->product_name = $tdorder->produk_name;
                $this->productlength = $tdorder->productlength;
                $this->defaultgulung = $tdorder->one_winding_m_number;
                $this->dimensi = $tdorder->ketebalan.'x'.$tdorder->diameterlipat.'x'.$tdorder->productlength;
            }
        }

        if(isset($this->machineno) && $this->machineno != ''){
            $machine=MsMachine::where('machineno', $this->machineno)->first();
            if($machine == null){
                $this->dispatch('notification', ['type' => 'warning', 'message' => 'Mesin ' . $this->machineno . ' Tidak Terdaftar']);
            } else {
                $this->machinename = $machine->machinename;
            }
        }
        // if(isset($this->qty_lpk) && isset($this->productlength)){
            $this->total_assembly_line = $this->qty_lpk * $this->productlength;
            $this->qty_gentan = $this->productlength / $this->defaultgulung;
            $this->qty_gulung = $this->productlength * $this->qty_gentan;
            $this->panjang_lpk = $this->qty_gentan * $this->qty_gulung;
            $this->selisihkurang = $this->productlength - $this->panjang_lpk;
        // }

        return view('livewire.order-lpk.edit-lpk')->extends('layouts.master');
    }
}

