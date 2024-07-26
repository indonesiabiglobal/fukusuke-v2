<?php

namespace App\Http\Livewire;

use App\Models\MsBuyer;
use App\Models\MsProduct;
use App\Models\TdOrders;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class EditOrderController extends Component
{
    public $buyer;
    public $orderId;
    public $po_no;
    public $product_id;
    public $product_code;
    public $order_qty;
    public $process_date;
    public $order_date;
    public $stufingdate;
    public $etddate;
    public $etadate;
    public $buyer_id;
    public $unit_id;
    public $status_order;
    public $dimensi;
    public $product_name;
    public $tglMasuk;

    protected $rules = [
        'po_no' => 'required',
        'product_code' => 'required',
        'order_qty' => 'required',
        'process_date' => 'required',
        'order_date' => 'required',
        'stufingdate' => 'required',
        'etddate' => 'required',
        'etadate' => 'required',
        'unit_id' => 'required',
        'buyer_id' => 'required',
    ];

    public function mount(Request $request)
    {
        $this->tglMasuk = Carbon::now()->format('Y-m-d');
        $this->buyer = MsBuyer::get();        

        $order = TdOrders::where('id', $request->query('orderId'))->first();
        $this->orderId = $order->id;
        $this->po_no = $order->po_no;
        $this->product_code = $order->product_code;
        $this->order_qty = $order->order_qty;
        $this->process_date = Carbon::parse($order->processdate)->format('Y-m-d');
        $this->order_date = Carbon::parse($order->order_date)->format('Y-m-d');
        $this->stufingdate = Carbon::parse($order->stufingdate)->format('Y-m-d');
        $this->etddate = Carbon::parse($order->etddate)->format('Y-m-d');
        $this->etadate = Carbon::parse($order->etadate)->format('Y-m-d');
        $product = MsProduct::where('id', $order->product_id)->first();
        $this->product_id = $product->code;
        $this->product_name = $product->name;
        $this->dimensi = $product->ketebalan.'x'.$product->diameterlipat.'x'.$product->productlength;
        $this->buyer_id['value'] = $order->buyer_id;
        $this->unit_id = $order->order_unit;
        $this->status_order = $order->status_order;
    }

    public function save()
    {
        $this->validate();

        DB::beginTransaction();
        try {
            $product = MsProduct::where('code', $this->product_id)->first();
            $order = TdOrders::findOrFail($this->orderId);
            $order->po_no = $this->po_no;
            $order->product_id = $product->id;
            $order->product_code = $product->code;
            $order->order_qty = $this->order_qty;
            $order->processdate = $this->process_date;
            $order->stufingdate = $this->stufingdate;
            $order->etddate = $this->etddate;
            $order->etadate = $this->etadate;
            $order->order_unit = $this->unit_id;
            $order->buyer_id = $this->buyer_id['value'];
            $order->save();
            
            DB::commit();
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Order saved successfully.']);
            return redirect()->route('order-lpk');
        } catch (\Exception $e) {
            DB::rollBack();
            // Log::error('Failed to save order: ' . $e->getMessage());
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Failed to save the order: ' . $e->getMessage()]);
        }
    }

    public function delete()
    {
        try {
            $order = TdOrders::where('id', $this->orderId)->first();
            $order->delete();

            $this->dispatch('notification', ['type' => 'success', 'message' => 'Order saved successfully.']);
            return redirect()->route('order-lpk');
        } catch (\Exception $e) {
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Failed to save order: ' . $e->getMessage()]);
        }
    }

    public function cancel()
    {
        return redirect()->route('order-lpk');
    }

    public function print()
    {        
        $data = collect(DB::select("
        SELECT
            tod.processdate,
            tod.po_no,
            tod.order_date,
            mp.code,
            mp.name,
            mp.ketebalan||'x'||mp.diameterlipat||'x'||mp.productlength as dimensi,
            tod.order_qty,
            tod.stufingdate,
            tod.etddate,
            tod.etadate,
            mbu.name as namabuyer
        FROM
            tdorder AS tod
            INNER JOIN msproduct AS mp ON mp.ID = tod.product_id
            INNER JOIN msbuyer AS mbu ON mbu.ID = tod.buyer_id 
        WHERE
            tod.id = $this->orderId
        "))->first();
        // dd($data);
        $this->emit('redirectToPrint', $data);
    }

    public function render()
    {
        if(isset($this->product_id) && $this->product_id != ''){
            $product=MsProduct::where('code', $this->product_id)->first();
            if($product == null){
                $this->dispatch('notification', ['type' => 'warning', 'message' => 'Nomor Order ' . $this->product_id . ' Tidak Terdaftar']);
            } else {
                $this->product_name = $product->name;
                $this->dimensi = $product->ketebalan.'x'.$product->diameterlipat.'x'.$product->productlength;
            }
        }
        return view('livewire.order-lpk.edit-order')->extends('layouts.master');
    }
}
