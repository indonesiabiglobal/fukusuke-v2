<?php

namespace App\Http\Livewire;

use App\Exports\LpkEntryExport;
use App\Exports\LpkEntryImport;
use App\Helpers\formatAngka;
use Livewire\Component;
use App\Models\MsMachine;
use App\Models\MsProduct;
use App\Models\TdOrder;
use App\Models\TdOrderLpk;
use App\Models\TdOrders;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;

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
    public $qty_gentan_old;
    public $panjang_lpk;
    public $remark;
    public $order_date;
    public $buyer_name;
    public $product_name;
    public $machinename;
    public $dimensi;
    public $total_assembly_line;
    public $panjang_total;
    public $productlength;
    public $defaultgulung;
    public $qty_gulung;
    public $qty_gulung_old;
    public $selisihkurang;
    public $warnalpkid;
    public $case_box_count;
    public $code;

    public $masterWarnaLPK;

    // data master produk
    public $masterKatanuki;
    public $product;
    public $photoKatanuki;
    public $katanuki_id;

    use WithFileUploads;
    public $file;

    protected $rules = [
        'lpk_date' => 'required',
        'lpk_no' => 'required',
        'po_no' => 'required',
        'machineno' => 'required',
        'qty_lpk' => 'required',
        'qty_gentan' => 'required',
        'panjang_lpk' => 'required',
        'processdate' => 'required',
        // 'warnalpkid' => 'required',
        'buyer_name' => 'required'
        // 'qty_gulung' => 'required'
    ];

    public function mount()
    {
        $this->lpk_date = Carbon::now()->format('d-m-Y');
        $this->processdate = Carbon::now()->format('d-m-Y');
        $today = Carbon::now();
        $lastLPK = TdOrderLpk::whereDate('lpk_date', Carbon::today())
            ->orderBy('lpk_no', 'desc')
            ->first();
        if ($lastLPK != null) {
            $lastNoLPK = explode('-', $lastLPK->lpk_no);
            $lastNoLPK = $lastNoLPK[1] + 1;
            $this->lpk_no = $today->format('ymd') . '-' . str_pad($lastNoLPK, 3, '0', STR_PAD_LEFT);
        } else {
            $this->lpk_no = $today->format('ymd') . '-001';
        }
        $this->total_assembly_line = 0;
        $this->productlength = 1;
        $this->defaultgulung = 1;

        // master warna LPK
        $this->masterWarnaLPK = DB::table('mswarnalpk')->get();
    }

    public function updatedFile()
    {
        $this->import();
    }

    public function import()
    {
        $this->validate([
            'file' => 'required|mimes:xls,xlsx',
        ]);

        try {
            Excel::import(new LpkEntryImport, $this->file->path());

            $this->dispatch('notification', ['type' => 'success', 'message' => 'Excel imported successfully.']);
            return redirect()->route('lpk-entry');
        } catch (\Exception $e) {
            $this->dispatch('notification', ['type' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function download()
    {
        return Excel::download(new LpkEntryExport, 'Template_LPK.xlsx');
    }

    public function printLPK()
    {
        // foreach ($this->checkListLPK as $lpk_id) {
        $this->dispatch('redirectToPrint', $this->orderId);
        // }
    }

    public function showModalNoOrder()
    {
        if (isset($this->no_order) && $this->no_order != '') {
            $this->product = MsProduct::where('code', $this->no_order)->first();
            if ($this->product == null) {
                $this->dispatch('notification', ['type' => 'warning', 'message' => 'Nomor Order ' . $this->no_order . ' Tidak Terdaftar']);
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

                $this->dispatch('showModalNoOrder');
            }
        } else {
            $this->dispatch('notification', ['type' => 'warning', 'message' => 'Nomor Order tidak boleh kosong']);
        }
    }

    public function save()
    {
        $this->validate();

        DB::beginTransaction();
        try {
            $order = TdOrders::where('po_no', $this->po_no)->first();
            $machine = MsMachine::where('machineno', $this->machineno)->first();
            $lastSeq = TdOrderLpk::whereDate('lpk_date', Carbon::today())
                ->orderBy('seq_no', 'desc')
                ->first();

            $seqno = 1;
            if (!empty($lastSeq)) {
                $seqno = $lastSeq->seq_no + 1;
            }

            $orderlpk = new TdOrderLpk();
            $orderlpk->lpk_no = $this->lpk_no;
            $orderlpk->lpk_date = $this->lpk_date;
            $orderlpk->order_id = $order->id;
            $orderlpk->product_id = $order->product_id;
            $orderlpk->machine_id = $machine->id;
            $orderlpk->qty_lpk = (int)str_replace(',', '', $this->qty_lpk);
            if (isset($this->remark)) {
                $orderlpk->remark = $this->remark;
            }
            $orderlpk->qty_gentan = (int)str_replace(',', '', $this->qty_gentan);
            $orderlpk->panjang_lpk = (int)str_replace(',', '', $this->panjang_lpk);
            $orderlpk->total_assembly_line = (int)str_replace(',', '', $this->total_assembly_line);
            $orderlpk->seq_no = $seqno;
            $orderlpk->qty_gulung = (int)str_replace(',', '', $this->qty_gulung);
            $orderlpk->product_panjang = $this->productlength;
            $orderlpk->product_panjanggulung = (int)str_replace(',', '', $this->defaultgulung);
            // $orderlpk->warnalpkid = $this->warnalpkid['value'];
            $orderlpk->created_on = Carbon::now()->format('d-m-Y H:i:s');
            $orderlpk->created_by = auth()->user()->username;
            $orderlpk->updated_on = Carbon::now()->format('d-m-Y H:i:s');
            $orderlpk->updated_by = auth()->user()->username;

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

    public function resetPoNo()
    {
        $this->po_no = '';
        $this->no_order = '';
        $this->processdate = '';
        $this->order_date = '';
        $this->buyer_name = '';
        $this->product_name = '';
        $this->productlength = '';
        $this->case_box_count = '';
        $this->defaultgulung = '';
        $this->dimensi = '';
    }

    public function render()
    {
        if (isset($this->po_no) && $this->po_no != '') {
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
                    'mp.one_winding_m_number',
                    'mp.case_box_count'
                )
                ->where('po_no', $this->po_no)
                ->first();


            if ($tdorder == null) {
                $this->dispatch('notification', ['type' => 'warning', 'message' => 'Nomor PO ' . $this->po_no . ' Tidak Terdaftar']);
                $this->resetPoNo();
            } else {
                $this->no_order = $tdorder->product_code;
                $this->processdate = $tdorder->processdate;
                $this->order_date = $tdorder->order_date;
                $this->buyer_name = $tdorder->buyer_name;
                $this->product_name = $tdorder->produk_name;
                $this->productlength = $tdorder->productlength;
                $this->case_box_count = $tdorder->case_box_count;
                $this->defaultgulung = $tdorder->one_winding_m_number;
                $this->dimensi = $tdorder->ketebalan . 'x' . $tdorder->diameterlipat . 'x' . $tdorder->productlength;
            }
        }

        if (isset($this->machineno) && $this->machineno != '') {
            $machine = MsMachine::where('machineno', 'ilike', '%' . $this->machineno . '%')->whereIn('department_id', [10, 12, 15, 2, 4, 10])->first();
            if ($machine == null) {
                $this->dispatch('notification', ['type' => 'warning', 'message' => 'Mesin ' . $this->machineno . ' Tidak Terdaftar']);
                $this->machineno = '';
                $this->machinename = '';
            } else {
                $this->machineno = $machine->machineno;
                $this->machinename = $machine->machinename;
            }
        }

        if (isset($this->qty_gentan) && $this->qty_gentan != $this->qty_gentan_old) {
            $this->qty_gentan_old = $this->qty_gentan;
            $qty_gulung = floor((int)str_replace(',', '', $this->panjang_total) / (int)str_replace(',', '', $this->qty_gentan) / 10) * 10;
            $this->qty_gulung = $qty_gulung;

            $this->panjang_lpk = (int)str_replace(',', '', $this->qty_gentan) * (int)str_replace(',', '', $this->qty_gulung);

            $this->selisihkurang = (int)str_replace(',', '', $this->panjang_total) - (int)str_replace(',', '', $this->panjang_lpk);
        } else if (isset($this->qty_gulung) && $this->qty_gulung != $this->qty_gulung_old) {
            $this->qty_gulung_old = $this->qty_gulung;

            $this->panjang_lpk = (int)str_replace(',', '', $this->qty_gentan) * (int)str_replace(',', '', $this->qty_gulung);
            $this->selisihkurang = (int)str_replace(',', '', $this->panjang_total) - (int)str_replace(',', '', $this->panjang_lpk);
        } else if (isset($this->qty_lpk) && $this->qty_lpk != '') {
            $this->panjang_total = (int)str_replace(',', '', $this->qty_lpk) * ((int)str_replace(',', '', $this->productlength) / 1000);

            $qty_gentan = (int)str_replace(',', '', $this->panjang_total) / (int)str_replace(',', '', $this->defaultgulung);
            $this->qty_gentan = (round(round($qty_gentan) / 2)) * 2;

            if ($this->qty_gentan < 2) {
                $this->qty_gentan = 2;
            }

            $this->qty_gentan_old = $this->qty_gentan;

            $qty_gulung = floor((int)str_replace(',', '', $this->panjang_total) / (int)str_replace(',', '', $this->qty_gentan) / 10) * 10;
            $this->qty_gulung = $qty_gulung;
            $this->qty_gulung_old = $this->qty_gulung;

            $this->panjang_lpk = (int)str_replace(',', '', $this->qty_gentan) * (int)str_replace(',', '', $this->qty_gulung);

            $this->selisihkurang = (int)str_replace(',', '', $this->panjang_total) - (int)str_replace(',', '', $this->panjang_lpk);
        }

        $this->panjang_total = formatAngka::ribuan($this->panjang_total);
        $this->defaultgulung = formatAngka::ribuan($this->defaultgulung);

        // if (isset($this->qty_gentan) && isset($this->qty_gulung)) {
        //     $this->panjang_lpk = (int)str_replace(',', '', $this->qty_gentan) * (int)str_replace(',', '', (int)$this->qty_gulung);
        // }
        // if (isset($this->panjang_lpk) && isset($this->panjang_total)) {
        //     $this->selisihkurang = (int)str_replace(',', '', $this->panjang_total) - (int)str_replace(',', '', $this->panjang_lpk);
        // }

        return view('livewire.order-lpk.add-lpk')->extends('layouts.master');
    }
}
