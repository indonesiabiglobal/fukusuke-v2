<?php

namespace App\Http\Livewire\MasterTabel\Produk;

use App\Models\MsBuyer;
use App\Models\MsProduct;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class AddMasterProduk extends Component
{
    // data master
    public $masterGazetteClassifications;
    public $masterGentanClassifications;
    public $masterKatanuki;
    public $photoKatanuki;
    public $masterPrintType;
    public $masterInkCharacteristics;
    public $masterEndlessPrinting;
    public $masterArahGulung;
    public $masterKlasifikasiSeal;

    // data add produk
    public $katanuki_id;
    public $material_classification;
    public $embossed_classification;
    public $gentan_classification;
    public $gazette_classification;
    public $print_type;
    public $ink_characteristic;
    public $endless_printing;
    public $winding_direction_of_the_web;
    public $seal_classification;

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
        $this->masterGazetteClassifications = collect(
            [
                (object)['id' => 0, 'name' => 'Gazet Biasa'],
                (object)['id' => 1, 'name' => 'Hineri Gazet'],
                (object)['id' => 2, 'name' => 'Kata Gazet'],
                (object)['id' => 3, 'name' => 'Ato Gazet'],
                (object)['id' => 4, 'name' => 'Soko Gazet'],
                (object)['id' => 9, 'name' => 'Lainnya'],
            ]
        );
        $this->masterGentanClassifications = collect(
            [
                (object)['id' => 1, 'name' => 'Tube'],
                (object)['id' => 2, 'name' => 'Film'],
            ]
        );
        $this->masterKatanuki = DB::table('mskatanuki')->get(['id', 'code', 'name']);
        // $this->masterPrintType = DB::table('msjenis_cetak')->get(['id', 'code', 'name']);
        $this->masterPrintType = collect(
            [
                (object)['id' => 0, 'name' => 'Gravure'],
                (object)['id' => 1, 'name' => 'Flexo'],
            ]
        );
        $this->masterInkCharacteristics = collect(
            [
                (object)['id' => 0, 'name' => 'Tahan Panas & Air'],
                (object)['id' => 1, 'name' => 'Tahan Cahaya'],
                (object)['id' => 2, 'name' => 'Tahan Gores'],
                (object)['id' => 3, 'name' => 'Tahan Oli'],
                (object)['id' => 4, 'name' => 'Tahan Laminating'],
            ]
        );
        // $this->masterEndlessPrinting = DB::table('msendless_printing')->get(['id', 'code', 'name']);
        $this->masterEndlessPrinting = collect(
            [
                (object)['id' => 0, 'name' => 'Tidak Ada'],
                (object)['id' => 1, 'name' => 'Ya (Ada)'],
            ]
        );
        // $this->masterArahGulung = DB::table('msarahgulung')->get(['id', 'code', 'name']);
        $this->masterArahGulung = collect(
            [
                (object)['id' => 0, 'name' => 'Gulung Kepala Depan'],
                (object)['id' => 1, 'name' => 'Zugara atama dashi insatsu-men ura maki'],
                (object)['id' => 2, 'name' => 'Zugara shiri dashi insatsu-men ura maki'],
                (object)['id' => 3, 'name' => 'Zugara shiri dashi insatsu-men-hyo maki'],
            ]
        );
        // $this->masterKlasifikasiSeal = DB::table('msklasifikasiseal')->get(['id', 'code', 'name']);
        $this->masterKlasifikasiSeal = collect(
            [
                (object)['id' => 0, 'name' => '1 Seal'],
                (object)['id' => 1, 'name' => '2 Seal'],
                (object)['id' => 2, 'name' => 'Cut Only'],
            ]
        );
    }

    public function noorder()
    {
        if (isset($this->product_id) && $this->product_id != '') {
            $product = MsProduct::where('code', $this->product_id)->first();
            if ($product == null) {
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
            $order = new TdOrder();
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
        return redirect()->route('order-entry');
    }

    public function render()
    {
        if (isset($this->katanuki_id) && $this->katanuki_id != '') {
            $this->katanuki_id = is_array($this->katanuki_id) ? $this->katanuki_id['value'] : $this->katanuki_id;
            $this->photoKatanuki = DB::table('mskatanuki')->where('id', $this->katanuki_id)->first()->filename;
        }
        return view('livewire.master-tabel.Produk.add-master-produk')->extends('layouts.master');
    }
}
