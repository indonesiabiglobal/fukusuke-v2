<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\MsMachine;
use App\Models\TdOrder;
use App\Models\TdOrderLpk;
use App\Models\TdOrders;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AddLpkController extends Component
{
    public $lpk_date;
    public $processdate;
    public $lpk_no;
    public $po_no;
    public $no_order;
    public $machineno;
    public $qty_lpk;
    public $qty_gentan;
    public $panjang_lpk;
    public $remark;
    public $order_date;
    public $buyer_name;
    public $product_name;
    public $machinename;
    public $dimensi;
    public $total_assembly_line;
    public $productlength;
    public $defaultgulung;
    public $qty_gulung;
    public $selisihkurang;

    protected $rules = [
        'lpk_date' => 'required',
        'lpk_no' => 'required',
        'po_no' => 'required',
        'machineno' => 'required',
        'qty_lpk' => 'required',
        'qty_gentan' => 'required',
        'panjang_lpk' => 'required',
        'processdate' => 'required',
        // 'qty_gulung' => 'required'
    ];

    public function mount()
    {
        $this->lpk_date = Carbon::now()->format('Y-m-d');
        $this->processdate = Carbon::now()->format('Y-m-d');
        $today = Carbon::now();
        $lastLPK = TdOrderLpk::whereDate('lpk_date', Carbon::today())
                    ->orderBy('lpk_no', 'desc')
                    ->first();
        if($lastLPK != null){
            $lastNoLPK = explode('-', $lastLPK->lpk_no);
            $lastNoLPK = $lastNoLPK[1] + 1;
            $this->lpk_no = $today->format('ymd').'-'.str_pad($lastNoLPK, 3, '0', STR_PAD_LEFT);
        } else {
            $this->lpk_no = $today->format('ymd').'-000';
        }
        $this->total_assembly_line = 0;
        $this->productlength=1;
        $this->defaultgulung=1;
    }

    public function save()
    {
        $this->validate();

        DB::beginTransaction();
        try {
            $order = TdOrders::where('po_no', $this->po_no)->first();
            $machine=MsMachine::where('machineno', $this->machineno)->first();
            $lastSeq = TdOrderLpk::whereDate('lpk_date', Carbon::today())
                    ->orderBy('seq_no', 'desc')
                    ->first();

            $seqno = 1;
            if(!empty($lastSeq)){
                $seqno = $lastSeq->seq_no + 1;
            }

            $orderlpk = new TdOrderLpk();
            $orderlpk->lpk_no = $this->lpk_no;
            $orderlpk->lpk_date = $this->lpk_date;
            $orderlpk->order_id = $order->id;
            $orderlpk->product_id = $order->product_id;
            $orderlpk->machine_id = $machine->id;
            $orderlpk->qty_lpk = $this->qty_lpk;
            if(isset($this->remark)){
                $orderlpk->remark = $this->remark;
            }
            $orderlpk->qty_gentan = $this->qty_gentan;
            $orderlpk->panjang_lpk = $this->panjang_lpk;
            $orderlpk->total_assembly_line = $this->total_assembly_line;
            $orderlpk->seq_no = $seqno;
            $orderlpk->remark = $order->machine_id;
            $orderlpk->qty_gulung = $this->qty_gulung;
            $orderlpk->created_on = Carbon::now()->format('Y-m-d H:i:s');

            $orderlpk->save();

            TdOrders::where('po_no', $this->po_no)
            ->update(
                ['status_order' => 1]
            );

            DB::commit();
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Order saved successfully.']);
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
        if(isset($this->qty_lpk) && isset($this->productlength)){
            $this->total_assembly_line = $this->qty_lpk * $this->productlength;
            $this->qty_gentan = $this->productlength / $this->defaultgulung;
            $this->qty_gulung = $this->productlength * $this->qty_gentan;
            $this->panjang_lpk = $this->qty_gentan * $this->qty_gulung;
            $this->selisihkurang = $this->productlength - $this->panjang_lpk;
        }

        return view('livewire.order-lpk.add-lpk')->extends('layouts.master');
    }
}

