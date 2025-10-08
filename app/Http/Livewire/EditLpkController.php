<?php

namespace App\Http\Livewire;

use App\Exports\LpkEntryExport;
use App\Exports\LpkEntryImport;
use App\Helpers\formatAngka;
use Livewire\Component;
use App\Models\TdOrderLpk;
use App\Models\MsBuyer;
use App\Models\MsMachine;
use App\Models\MsProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Calculation\TextData\Format;

class EditLpkController extends Component
{
    public $code;
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
    public $qty_gentan_old;
    public $qty_gulung;
    public $qty_gulung_old;
    public $panjang_lpk;
    public $processdate;
    public $tgl_po;
    public $buyer_name;
    public $product_name;
    public $order_date;
    public $no_order;
    public $total_assembly_line;
    public $panjang_total;
    public $productlength;
    public $defaultgulung;
    public $selisihkurang;
    public $dimensi;
    public $remark;
    public $warnalpkid;
    public $case_box_count;
    public $status_lpk;
    public $seq_no;
    public $reprint_no;

    public $masterWarnaLPK;

    // data master produk
    public $masterKatanuki;
    public $product;
    public $photoKatanuki;
    public $katanuki_id;

    use WithFileUploads;
    public $file;

    protected $rules = [
        // 'lpk_date' => 'required',
        'lpk_no' => 'required',
        'po_no' => 'required',
        'order_id' => 'required',
        'machineno' => 'required',
        'qty_lpk' => 'required',
        'qty_gentan' => 'required',
        'qty_gulung' => 'required',
        'panjang_lpk' => 'required',
        // 'warnalpkid' => 'required',
        // 'tglproses' => 'required',
        // 'buyer_id' => 'required',
        // 'product_id' => 'required',
    ];

    public function mount(Request $request)
    {
        $this->total_assembly_line = 0;
        $this->productlength = 1;
        $this->defaultgulung = 1;
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
                'tolp.total_assembly_qty',
                'tolp.total_assembly_line',
                // 'tolp.warnalpkid',
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
                // 'tolp.created_on as tglproses',
                DB::raw("tolp.created_on || ' - Nomor: ' || tolp.seq_no as tglproses"),
                'mp.productlength',
                'tolp.seq_no',
                'tolp.updated_by',
                'tolp.updated_on as updatedt',
                'tolp.reprint_no',
                'tolp.status_lpk'
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
        $this->processdate = $order->tglproses;
        $this->order_date = Carbon::parse($order->order_date)->format('Y-m-d');
        $this->buyer_name = $order->buyer_name;
        $this->product_name = $order->product_name;
        $this->no_order = $order->code;
        $this->dimensi = $order->ketebalan . 'x' . $order->diameterlipat . 'x' . $order->productlength;
        $this->total_assembly_line = $order->total_assembly_line;
        $this->productlength = $order->productlength;
        $this->remark = $order->remark;
        $this->reprint_no = $order->reprint_no;
        // $this->warnalpkid['value'] = $order->warnalpkid;
        $this->status_lpk = $order->status_lpk;
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

    public function save()
    {
        $this->validate();

        DB::beginTransaction();
        try {
            $machine = MsMachine::where('machineno', $this->machineno)->first();

            $orderlpk = TdOrderLpk::findOrFail($this->orderId);
            $orderlpk->lpk_no = $this->lpk_no;
            $orderlpk->order_id = $orderlpk->order_id;
            $orderlpk->product_id = $orderlpk->product_id;
            $orderlpk->machine_id = $machine->id;
            $orderlpk->qty_lpk = (int)str_replace(',', '', $this->qty_lpk);
            if (isset($this->remark)) {
                $orderlpk->remark = $this->remark;
            }
            $orderlpk->qty_gentan = (int)str_replace(',', '', $this->qty_gentan);
            $orderlpk->panjang_lpk = (int)str_replace(',', '', $this->panjang_lpk);
            $orderlpk->total_assembly_line = (int)str_replace(',', '', $this->total_assembly_line);
            $orderlpk->qty_gulung = (int)str_replace(',', '', $this->qty_gulung);
            // $orderlpk->warnalpkid = $this->warnalpkid['value'];
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
        $lpk_id = $this->orderId;
        $this->dispatch('redirectToPrint', $lpk_id);
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

                // show modal
                $this->dispatch('showModalNoOrder');
            }
        } else {
            $this->dispatch('notification', ['type' => 'warning', 'message' => 'Nomor Order tidak boleh kosong']);
        }
    }

    public function render()
    {
        if (isset($this->po_no) && $this->po_no != '') {
            $tdorder = DB::table('tdorder as tod')
                ->join('msproduct as mp', 'mp.id', '=', 'tod.product_id')
                ->join('msbuyer as mbu', 'mbu.id', '=', 'tod.buyer_id')
                ->join('tdorderlpk as tolp', 'tod.id', '=', 'tolp.order_id')
                ->select(
                    'tod.id',
                    'tod.product_code',
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
            } else {
                $this->no_order = $tdorder->product_code;
                $this->order_date = $tdorder->order_date;
                $this->buyer_name = $tdorder->buyer_name;
                $this->product_name = $tdorder->produk_name;
                $this->productlength = $tdorder->productlength;
                $this->defaultgulung = $tdorder->one_winding_m_number;
                $this->case_box_count = $tdorder->case_box_count;
                $this->dimensi = $tdorder->ketebalan . 'x' . $tdorder->diameterlipat . 'x' . $tdorder->productlength;
            }
        }

        if (isset($this->machineno) && $this->machineno != '') {
            $machine = MsMachine::where('machineno', $this->machineno)->first();
            if ($machine == null) {
                $this->dispatch('notification', ['type' => 'warning', 'message' => 'Mesin ' . $this->machineno . ' Tidak Terdaftar']);
            } else {
                $this->machinename = $machine->machinename;
            }
        }

        if (isset($this->qty_gentan) && $this->qty_gentan != $this->qty_gentan_old) {
            if ($this->qty_gentan == 0 || $this->qty_gentan == '') {
                $this->qty_gentan = 0;
                $this->dispatch('notification', ['type' => 'warning', 'message' => 'jumlah Gentan tidak boleh kosong']);
            } else if (isset($this->qty_lpk) && isset($this->qty_gentan) && isset($this->qty_gulung)) {
                $this->panjang_total = (int) str_replace(',', '', $this->qty_lpk) * ((int) str_replace(',', '', $this->productlength) / 1000);

                $this->panjang_lpk = (int)str_replace(',', '', $this->qty_gentan) * (int)str_replace(',', '', $this->qty_gulung);

                $this->selisihkurang = (int)str_replace(',', '', $this->panjang_lpk) - (int)str_replace(',', '', $this->panjang_total);
            } else {
                $this->qty_gentan_old = $this->qty_gentan;
                $qty_gulung = floor((int) str_replace(',', '', $this->panjang_total) / (int) str_replace(',', '', $this->qty_gentan) / 10) * 10;
                $this->qty_gulung = $qty_gulung;
                $this->panjang_lpk = (int) str_replace(',', '', $this->qty_gentan) * (int) str_replace(',', '', $this->qty_gulung);

                $this->selisihkurang = (int)str_replace(',', '', $this->panjang_lpk) - (int)str_replace(',', '', $this->panjang_total);
            }
        } else if (isset($this->qty_gulung) && $this->qty_gulung != $this->qty_gulung_old) {
            $this->qty_gulung_old = $this->qty_gulung;

            $this->panjang_lpk = (int) str_replace(',', '', $this->qty_gentan) * (int) str_replace(',', '', $this->qty_gulung);
            $this->selisihkurang = (int)str_replace(',', '', $this->panjang_lpk) - (int)str_replace(',', '', $this->panjang_total);
        } else if (isset($this->qty_lpk) && $this->qty_lpk != '') {
            $this->panjang_total = (int) str_replace(',', '', $this->qty_lpk) * ((int) str_replace(',', '', $this->productlength) / 1000);

            $qty_gentan = (int) str_replace(',', '', $this->panjang_total) / (int) str_replace(',', '', $this->defaultgulung);
            $this->qty_gentan = (round(round($qty_gentan) / 2)) * 2;

            if ($this->qty_gentan < 2) {
                $this->qty_gentan = 2;
            }

            $this->qty_gentan_old = $this->qty_gentan;

            $qty_gulung = floor((int)str_replace(',', '', $this->panjang_total) / (int)str_replace(',', '', $this->qty_gentan) / 10) * 10;
            $this->qty_gulung = $qty_gulung;
            $this->qty_gulung_old = $this->qty_gulung;

            $this->panjang_lpk = (int)str_replace(',', '', $this->qty_gentan) * (int)str_replace(',', '', $this->qty_gulung);

            $this->selisihkurang = (int)str_replace(',', '', $this->panjang_lpk) - (int)str_replace(',', '', $this->panjang_total);
        }

        // merubah format angka
        $this->panjang_total = formatAngka::ribuan($this->panjang_total);
        $this->qty_lpk = formatAngka::ribuan((int) str_replace(',', '', $this->qty_lpk));
        $this->qty_gulung = formatAngka::ribuan((int) str_replace(',', '', $this->qty_gulung));
        $this->panjang_lpk = formatAngka::ribuan($this->panjang_lpk);
        $this->defaultgulung = formatAngka::ribuan($this->defaultgulung);
        $this->selisihkurang = formatAngka::ribuan($this->selisihkurang);

        // if (isset($this->qty_gentan) && isset($this->qty_gulung)) {
        //     $this->panjang_lpk = (int)str_replace(',', '', $this->qty_gentan) * (int)str_replace(',', '', (int)$this->qty_gulung);
        // }
        // if (isset($this->panjang_lpk) && isset($this->total_assembly_line)) {
        //     $this->selisihkurang = (int)str_replace(',', '', $this->total_assembly_line) - (int)str_replace(',', '', $this->panjang_lpk);
        // }

        return view('livewire.order-lpk.edit-lpk')->extends('layouts.master');
    }
}
