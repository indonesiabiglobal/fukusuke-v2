<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\TdOrder;
use App\Models\MsBuyer;
use App\Models\MsProduct;
use App\Models\TdOrders;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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


    // data master
    public $masterKatanuki;

    // data add produk
    public $photoKatanuki;
    public $productNomorOrder;
    public $code;
    public $name;
    public $product_type_id;
    public $product_type_name;
    public $code_alias;
    public $codebarcode;
    public $ketebalan;
    public $diameterlipat;
    public $productlength;
    public $unit_weight;
    public $product_unit;
    public $inflation_thickness;
    public $inflation_fold_diameter;
    public $one_winding_m_number;
    public $material_classification;
    public $embossed_classification;
    public $surface_classification;
    public $coloring_1;
    public $coloring_2;
    public $coloring_3;
    public $coloring_4;
    public $coloring_5;
    public $inflation_notes;
    public $gentan_classification;
    public $gazette_classification;
    public $gazette_dimension_a;
    public $gazette_dimension_b;
    public $gazette_dimension_c;
    public $gazette_dimension_d;
    public $katanuki_id;
    public $extracted_dimension_a;
    public $extracted_dimension_b;
    public $extracted_dimension_c;
    public $number_of_color;
    public $color_spec_1;
    public $color_spec_2;
    public $color_spec_3;
    public $color_spec_4;
    public $color_spec_5;
    public $back_color_number;
    public $back_color_1;
    public $back_color_2;
    public $back_color_3;
    public $back_color_4;
    public $back_color_5;
    public $print_type;
    public $ink_characteristic;
    public $endless_printing;
    public $winding_direction_of_the_web;
    public $seal_classification;
    public $from_seal_design;
    public $lower_sealing_length;
    public $palet_jumlah_baris;
    public $palet_isi_baris;
    public $pack_gaiso_id;
    public $pack_box_id;
    public $pack_inner_id;
    public $pack_layer_id;
    public $manufacturing_summary;
    public $case_gaiso_count;
    public $case_gaiso_count_unit;
    public $case_box_count;
    public $case_box_count_unit;
    public $case_inner_count;
    public $case_inner_count_unit;
    public $lakbanseitaiid;
    public $lakbaninfureid;
    public $stampelseitaiid;
    public $hagataseitaiid;
    public $jenissealseitaiid;

    protected $rules = [
        'po_no' => 'required|unique:tdorder,po_no',
        'order_qty' => 'required|integer',
        'order_date' => 'required',
        'stufingdate' => 'required',
        'etddate' => 'required',
        'etadate' => 'required',
        'product_id' => 'required',
        'buyer_id' => 'required',
        'unit_id' => 'required'
    ];

    protected $messages = [
        'po_no.required' => 'PO Number tidak boleh kosong',
        'po_no.unique' => 'PO Number sudah terdaftar',
        'order_qty.required' => 'Jumlah order tidak boleh kosong',
        'order_qty.integer' => 'Jumlah order harus berupa angka',
        'order_date.required' => 'Tanggal order tidak boleh kosong',
        'stufingdate.required' => 'Tanggal stufing tidak boleh kosong',
        'etddate.required' => 'Tanggal ETD tidak boleh kosong',
        'etadate.required' => 'Tanggal ETA tidak boleh kosong',
        'product_id.required' => 'Nomor Order tidak boleh kosong',
        'buyer_id.required' => 'Buyer tidak boleh kosong',
        'unit_id.required' => 'Satuan tidak boleh kosong'
    ];

    public function mount()
    {
        $this->process_date = Carbon::now()->format('Y-m-d');
        $this->buyer = MsBuyer::get();

        // master produk
    }

    public function showModalNoOrder()
    {
        if (isset($this->product_id) && $this->product_id != '') {
            $product = MsProduct::where('code', $this->product_id)->first();
            if ($product == null) {
                $this->dispatch('notification', ['type' => 'warning', 'message' => 'Nomor Order ' . $this->product_id . ' Tidak Terdaftar']);
            } else {
                // nomor order produk
                $this->productNomorOrder = DB::table('msproduct')->where('code', $this->product_id)->first();

                $this->masterKatanuki = DB::table('mskatanuki')->where('id', $this->productNomorOrder->katanuki_id)->first(['name', 'filename']);

                $this->code = $this->productNomorOrder->code;
                $this->name = $this->productNomorOrder->name;
                $this->product_type_id = DB::table('msproduct_type')->where('id', $this->productNomorOrder->product_type_id)->first(['name'])->name ?? '';
                $this->code_alias = $this->productNomorOrder->code_alias;
                $this->codebarcode = $this->productNomorOrder->codebarcode;
                $this->ketebalan = $this->productNomorOrder->ketebalan;
                $this->diameterlipat = $this->productNomorOrder->diameterlipat;
                $this->productlength = $this->productNomorOrder->productlength;
                $this->unit_weight = $this->productNomorOrder->unit_weight;
                $this->product_unit = DB::table('msunit')->where('id', $this->productNomorOrder->product_unit_id)->first(['name'])->name ?? '';
                $this->inflation_thickness = $this->productNomorOrder->inflation_thickness;
                $this->inflation_fold_diameter = $this->productNomorOrder->inflation_fold_diameter;
                $this->one_winding_m_number = $this->productNomorOrder->one_winding_m_number;
                $this->material_classification = DB::table('msmaterial')->where('id', $this->productNomorOrder->material_classification_id)->first(['name'])->name ?? '';
                $this->embossed_classification = DB::table('msembossedclassification')->where('id', $this->productNomorOrder->embossed_classification_id)->first(['name'])->name ?? '';
                $this->surface_classification = DB::table('mssurfaceclassification')->where('id', $this->productNomorOrder->surface_classification_id)->first(['name'])->name ?? '';
                $this->coloring_1 = $this->productNomorOrder->coloring_1;
                $this->coloring_2 = $this->productNomorOrder->coloring_2;
                $this->coloring_3 = $this->productNomorOrder->coloring_3;
                $this->coloring_4 = $this->productNomorOrder->coloring_4;
                $this->coloring_5 = $this->productNomorOrder->coloring_5;
                $this->inflation_notes = $this->productNomorOrder->inflation_notes;
                $this->gentan_classification = DB::table('msgentanclassification')->where('id', $this->productNomorOrder->gentan_classification_id)->first(['name'])->name ?? '';
                $this->gazette_classification = DB::table('msgazetteclassification')->where('id', $this->productNomorOrder->gazette_classification_id)->first(['name'])->name ?? '';
                $this->gazette_dimension_a = $this->productNomorOrder->gazette_dimension_a;
                $this->gazette_dimension_b = $this->productNomorOrder->gazette_dimension_b;
                $this->gazette_dimension_c = $this->productNomorOrder->gazette_dimension_c;
                $this->gazette_dimension_d = $this->productNomorOrder->gazette_dimension_d;
                $this->katanuki_id = $this->masterKatanuki->name ?? '';
                $this->photoKatanuki = $this->masterKatanuki->filename ?? '';
                $this->extracted_dimension_a = $this->productNomorOrder->extracted_dimension_a;
                $this->extracted_dimension_b = $this->productNomorOrder->extracted_dimension_b;
                $this->extracted_dimension_c = $this->productNomorOrder->extracted_dimension_c;
                $this->number_of_color = $this->productNomorOrder->number_of_color;
                $this->color_spec_1 = $this->productNomorOrder->color_spec_1;
                $this->color_spec_2 = $this->productNomorOrder->color_spec_2;
                $this->color_spec_3 = $this->productNomorOrder->color_spec_3;
                $this->color_spec_4 = $this->productNomorOrder->color_spec_4;
                $this->color_spec_5 = $this->productNomorOrder->color_spec_5;
                $this->back_color_number = $this->productNomorOrder->back_color_number;
                $this->back_color_1 = $this->productNomorOrder->back_color_1;
                $this->back_color_2 = $this->productNomorOrder->back_color_2;
                $this->back_color_3 = $this->productNomorOrder->back_color_3;
                $this->back_color_4 = $this->productNomorOrder->back_color_4;
                $this->back_color_5 = $this->productNomorOrder->back_color_5;
                $this->print_type = DB::table('msjeniscetak')->where('id', $this->productNomorOrder->print_type_id)->first(['name'])->name ?? '';
                $this->ink_characteristic = DB::table('mssifattinta')->where('id', $this->productNomorOrder->ink_characteristic_id)->first(['name'])->name ?? '';
                $this->endless_printing = DB::table('msendless')->where('id', $this->productNomorOrder->endless_printing_id)->first(['name'])->name ?? '';
                $this->winding_direction_of_the_web = DB::table('msarahgulung')->where('id', $this->productNomorOrder->winding_direction_of_the_web_id)->first(['name'])->name ?? '';
                $this->seal_classification = DB::table('msklasifikasiseal')->where('id', $this->productNomorOrder->seal_classification_id)->first(['name'])->name ?? '';
                $this->from_seal_design = $this->productNomorOrder->from_seal_design;
                $this->lower_sealing_length = $this->productNomorOrder->lower_sealing_length;
                $this->palet_jumlah_baris = $this->productNomorOrder->palet_jumlah_baris;
                $this->palet_isi_baris = $this->productNomorOrder->palet_isi_baris;
                $this->pack_gaiso_id = DB::table('mspackaginggaiso')->where('id', $this->productNomorOrder->pack_gaiso_id)->first(['name'])->name ?? '';
                $this->pack_box_id = DB::table('mspackagingbox')->where('id', $this->productNomorOrder->pack_box_id)->first(['name'])->name ?? '';
                $this->pack_inner_id = DB::table('mspackaginginner')->where('id', $this->productNomorOrder->pack_inner_id)->first(['name'])->name ?? '';
                $this->pack_layer_id = DB::table('mspackaginglayer')->where('id', $this->productNomorOrder->pack_layer_id)->first(['name'])->name ?? '';
                $this->manufacturing_summary = $this->productNomorOrder->manufacturing_summary;
                $this->case_gaiso_count = $this->productNomorOrder->case_gaiso_count;
                $this->case_gaiso_count_unit = DB::table('msunit')->where('id', $this->productNomorOrder->case_gaiso_count_unit)->first(['name'])->name ?? '';
                $this->case_box_count = $this->productNomorOrder->case_box_count;
                $this->case_box_count_unit = DB::table('msunit')->where('id', $this->productNomorOrder->case_box_count_unit)->first(['name'])->name ?? '';
                $this->case_inner_count = $this->productNomorOrder->case_inner_count;
                $this->case_inner_count_unit = DB::table('msunit')->where('id', $this->productNomorOrder->case_inner_count_unit)->first(['name'])->name ?? '';
                $this->lakbaninfureid = DB::table('mslakbaninfure')->where('id', $this->productNomorOrder->lakbaninfureid)->first(['name'])->name ?? '';
                $this->lakbanseitaiid = DB::table('mslakbanseitai')->where('id', $this->productNomorOrder->lakbanseitaiid)->first(['name'])->name ?? '';
                $this->stampelseitaiid = DB::table('msstampleseitai')->where('id', $this->productNomorOrder->stampelseitaiid)->first(['name'])->name ?? '';
                $this->hagataseitaiid = DB::table('mshagataseitai')->where('id', $this->productNomorOrder->hagataseitaiid)->first(['name'])->name ?? '';
                $this->jenissealseitaiid = DB::table('msjenissealseitai')->where('id', $this->productNomorOrder->jenissealseitaiid)->first(['name'])->name ?? '';

                // show modal
                $this->dispatch('showModalNoOrder');
            }
        } else {
            $this->dispatch('notification', ['type' => 'warning', 'message' => 'Nomor Order tidak boleh kosong']);
        }
    }

    public function save()
    {
        // merubah format order_qty
        $this->order_qty = (int)str_replace(',', '', $this->order_qty);

        $this->validate();

        DB::beginTransaction();
        try {
            $maxProcessSeq = TdOrders::where('order_date', $this->order_date)->max('processseq');
            $product = MsProduct::where('code', $this->product_id)->first();
            $order = new TdOrders();
            $order->processdate = $this->process_date;
            $order->po_no = $this->po_no;
            $order->order_date = $this->order_date;
            $order->product_id = $product->id;
            $order->product_code = $product->code;
            $order->order_qty = $this->order_qty;
            $order->order_unit = $this->unit_id;
            $order->buyer_id = $this->buyer_id['value'];
            $order->stufingdate = $this->stufingdate;
            $order->etddate = $this->etddate;
            $order->etadate = $this->etadate;
            $order->processseq = $maxProcessSeq + 1;
            $order->created_on = Carbon::now();
            $order->created_by = auth()->user()->username;
            $order->updated_on = Carbon::now();
            $order->updated_by = auth()->user()->username;
            $order->save();

            DB::commit();
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Order saved successfully.']);
            return redirect()->route('order-lpk');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to save order: ' . $e->getMessage());
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Failed to save the order: ' . $e->getMessage()]);
        }
    }

    public function cancel()
    {
        return redirect()->route('order-lpk');
    }

    public function render()
    {
        if (isset($this->product_id) && $this->product_id != '') {
            $product = MsProduct::where('code', $this->product_id)->first();
            if ($product == null) {
                $this->dispatch('notification', ['type' => 'warning', 'message' => 'Nomor Order ' . $this->product_id . ' Tidak Terdaftar']);
                $this->product_name = '';
                $this->dimensi = '';
                $this->product_id = '';
            } else {
                $this->product_name = $product->name;
                $this->dimensi = $product->ketebalan . 'x' . $product->diameterlipat . 'x' . $product->productlength;
            }
        }
        if (isset($this->order_date) && $this->order_date != '') {
            $this->stufingdate = $this->order_date;
            $this->etddate = $this->order_date;
            $this->etadate = $this->order_date;
        }
        return view('livewire.order-lpk.add-order')->extends('layouts.master');
    }
}
