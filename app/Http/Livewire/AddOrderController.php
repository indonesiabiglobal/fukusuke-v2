<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\TdOrder;
use App\Models\MsBuyer;
use App\Models\MsProduct;
use App\Models\TdOrders;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AddOrderController extends Component
{
    public $process_date;
    public $product;
    public $product_id;
    public $buyer_id;
    public $buyer;
    public $po_no;
    public $order_date;
    public $product_name;
    public $dimensi;
    public $order_qty;
    public $unit_id;
    public $stufingdate;
    public $etddate;
    public $etadate;

    public function mount()
    {
        $this->process_date = Carbon::now()->format('Y-m-d');
        $this->buyer = MsBuyer::limit(10)->get();
    }

    public function noorder(){
        if(isset($this->product_id) && $this->product_id != ''){
            $product=MsProduct::where('code', $this->product_id)->first();
            if($product == null){
                $this->dispatchBrowserEvent('notification', ['type' => 'warning', 'message' => 'Nomor Order ' . $this->product_id . ' Tidak Terdaftar']);
            } else {
                $this->product_name = $product->name;
            }
        }
    }

    public function save()
    {
        $validatedData = $this->validate([
            'po_no' => 'required',
            'order_qty' => 'required|integer',
            'order_date' => 'required',
            'stufingdate' => 'required',
            'etddate' => 'required',
            'etadate' => 'required',
            'product_id' => 'required',
            'buyer_id' => 'required',
        ]);

        try {
            $product = MsProduct::where('code', $this->product_id)->first();
            $order = new TdOrders();
            $order->processdate = $this->process_date;
            $order->po_no = $this->po_no;
            $order->order_date = $this->order_date;
            $order->product_id = $product->id;
            $order->product_code = $product->code;
            $order->order_qty = $this->order_qty;
            $order->order_unit = $this->unit_id;
            $order->buyer_id = $this->buyer_id;
            $order->stufingdate = $this->stufingdate;
            $order->etddate = $this->etddate;
            $order->etadate = $this->etadate;
            $order->save();
    
            // session()->flash('message', 'Order saved successfully.');
            session()->flash('notification', ['type' => 'success', 'message' => 'Order saved successfully.']);
            return redirect()->route('order-entry');
        } catch (\Exception $e) {
            $this->dispatchBrowserEvent('notification', ['type' => 'error', 'message' => 'Failed to save order: ' . $e->getMessage()]);
        }
        
    }

    public function cancel()
    {
        return redirect()->route('order-lpk');
    }

    public function render()
    {
        if(isset($this->product_id) && $this->product_id != ''){
            $product=MsProduct::where('code', $this->product_id)->first();
            // dd($product);
            if($product == null){
                $this->dispatchBrowserEvent('notification', ['type' => 'warning', 'message' => 'Nomor Order ' . $this->product_id . ' Tidak Terdaftar']);
            } else {
                $this->product_name = $product->name;
                $this->dimensi = $product->ketebalan.'x'.$product->diameterlipat.'x'.$product->productlength;
            }
        }
        return view('livewire.order-lpk.add-order')->extends('layouts.master');
    }
}

