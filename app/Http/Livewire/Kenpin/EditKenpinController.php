<?php

namespace App\Http\Livewire\Kenpin;

use Livewire\Component;
use App\Models\MsEmployee;
use App\Models\MsProduct;
use App\Models\TdKenpinAssembly;
use App\Models\TdKenpinAssemblyDetail;
use App\Models\TdProductAssembly;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class EditKenpinController extends Component
{
    public $kenpin_date;
    public $kenpin_no;
    public $lpk_no;
    public $lpk_date;
    public $panjang_lpk;
    public $code;
    public $name;
    public $employeeno;
    public $empname;
    public $remark;
    public $status_kenpin;
    public $details;
    public $beratLossTotal = 0;
    public $lpk_id;
    public $gentan_no;
    public $machineno;
    public $namapetugas;
    public $berat_loss;
    public $orderid;
    public $berat;
    public $frekuensi;

    // data master produk
    public $masterKatanuki;
    public $product;
    public $photoKatanuki;
    public $katanuki_id;

    // data LPK
    public $orderLPK;

    public function mount(Request $request)
    {
        $data = DB::table('tdkenpin_assembly AS tda')
            ->join('tdorderlpk AS tdo', 'tdo.id', '=', 'tda.lpk_id')
            ->join('msproduct AS msp', 'msp.id', '=', 'tdo.product_id')
            ->join('msemployee AS mse', 'mse.id', '=', 'tda.employee_id')
            ->where('tda.id', $request->query('orderId'))
            ->select(
                'tda.id',
                'tda.kenpin_date',
                'tda.kenpin_no',
                'tdo.lpk_no',
                'tdo.lpk_date',
                'tdo.panjang_lpk',
                'msp.code',
                'msp.name',
                'mse.employeeno',
                'mse.empname',
                'tda.remark',
                'tda.status_kenpin'
            )
            ->first();

        $this->orderid = $data->id;
        $this->kenpin_date = Carbon::parse($data->kenpin_date)->format('d-m-Y');
        $this->kenpin_no = $data->kenpin_no;
        $this->lpk_no = $data->lpk_no;
        $this->lpk_date = Carbon::parse($data->lpk_date)->format('d-m-Y');
        $this->panjang_lpk = $data->panjang_lpk;
        $this->code = $data->code;
        $this->name = $data->name;
        $this->employeeno = $data->employeeno;
        $this->empname = $data->empname;
        $this->remark = $data->remark;
        $this->status_kenpin = $data->status_kenpin;

        $this->details = DB::table('tdkenpin_assembly_detail AS tkad')
            ->join('tdproduct_assembly AS tpa', 'tpa.id', '=', 'tkad.product_assembly_id')
            ->join('tdorderlpk AS tol', 'tol.id', '=', 'tpa.lpk_id')
            ->join('msproduct AS msp', 'msp.id', '=', 'tpa.product_id')
            ->join('msemployee AS mse', 'mse.id', '=', 'tpa.employee_id')
            ->join('msmachine AS msm', 'msm.id', '=', 'tpa.machine_id')
            ->where('tkad.kenpin_assembly_id', $this->orderid)
            ->select(
                'tkad.id',
                'tkad.berat_loss',
                'tpa.production_date AS tglproduksi',
                'tpa.work_shift',
                'msm.machineno AS nomesin',
                'mse.empname AS namapetugas',
                'tpa.nomor_han',
                'tpa.gentan_no',
            )
            ->get();
        $this->beratLossTotal = $this->details->sum('berat_loss');
    }

    public function showModalLPK()
    {
        if (isset($this->lpk_no) && $this->lpk_no != '') {
            $this->orderLPK = DB::table('tdorderlpk as tolp')
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
                    'mwa.name as warnalpkname',
                    'tolp.updated_by',
                    'tolp.updated_on as updatedt',
                    'mp.one_winding_m_number as defaultgulung',
                    'mp.case_box_count',
                )
                ->join('tdorder as tod', 'tod.id', '=', 'tolp.order_id')
                ->leftJoin('msproduct as mp', 'mp.id', '=', 'tolp.product_id')
                ->join('msmachine as mm', 'mm.id', '=', 'tolp.machine_id')
                ->join('msbuyer as mbu', 'mbu.id', '=', 'tod.buyer_id')
                ->leftJoin('mswarnalpk as mwa', 'mwa.id', '=', 'tolp.warnalpkid')
                ->where('tolp.lpk_no', $this->lpk_no)
                ->first();

            if ($this->orderLPK == null) {
                $this->dispatch('notification', ['type' => 'warning', 'message' => 'Nomor LPK ' . $this->lpk_no . ' Tidak Terdaftar']);
            } else {
                $panjangTotal = ($this->orderLPK->qty_lpk * $this->orderLPK->productlength) / $this->orderLPK->case_box_count;
                $panjangLPK = (int)$this->orderLPK->qty_gentan * (int)$this->orderLPK->qty_gulung;
                $selisihKurang = $panjangLPK - $panjangTotal;

                $this->orderLPK->progressInfure = number_format($this->orderLPK->total_assembly_line, 0, ',', '.');
                $this->orderLPK->progressInfureSelisih = number_format($this->orderLPK->total_assembly_line - $panjangTotal - $selisihKurang, 0, ',', '.');
                $this->orderLPK->progressSeitai =  number_format($this->orderLPK->total_assembly_qty, 0, ',', '.');
                $this->orderLPK->progressSeitaiSelisih = number_format($this->orderLPK->total_assembly_qty - $this->orderLPK->qty_lpk, 0, ',', '.');

                $this->orderLPK->lpk_date = Carbon::parse($this->orderLPK->lpk_date)->format('Y-m-d');
                $this->orderLPK->orderId = $this->orderLPK->id;
                $this->orderLPK->lpk_no = $this->orderLPK->lpk_no;
                $this->orderLPK->po_no = $this->orderLPK->po_no;
                $this->orderLPK->order_id = $this->orderLPK->order_id;
                $this->orderLPK->machineno = $this->orderLPK->machineno;
                $this->orderLPK->machinename = $this->orderLPK->machinename;
                $this->orderLPK->qty_lpk = number_format($this->orderLPK->qty_lpk, 0, ',', '.');
                $this->orderLPK->qty_gentan = $this->orderLPK->qty_gentan;
                $this->orderLPK->qty_gulung = number_format($this->orderLPK->qty_gulung, 0, ',', '.');
                $this->orderLPK->processdate = Carbon::parse($this->orderLPK->tglproses)->format('Y-m-d');
                $this->orderLPK->order_date = Carbon::parse($this->orderLPK->order_date)->format('Y-m-d');
                $this->orderLPK->buyer_name = $this->orderLPK->buyer_name;
                $this->orderLPK->product_name = $this->orderLPK->product_name;
                $this->orderLPK->no_order = $this->orderLPK->code;
                $this->orderLPK->dimensi = $this->orderLPK->ketebalan . 'x' . $this->orderLPK->diameterlipat . 'x' . $this->orderLPK->productlength;
                $this->orderLPK->productlength = $this->orderLPK->productlength;
                $this->orderLPK->remark = $this->orderLPK->remark;
                $this->orderLPK->defaultgulung = number_format($this->orderLPK->defaultgulung, 0, ',', '.');

                $this->orderLPK->total_assembly_line =  number_format($panjangTotal, 0, ',', '.');
                $this->orderLPK->panjang_lpk =  number_format($panjangLPK, 0, ',', '.');
                $this->orderLPK->selisihKurang =  number_format($selisihKurang, 0, ',', '.');

                // show modal
                $this->dispatch('showModalLPK');
            }
        } else {
            $this->dispatch('notification', ['type' => 'warning', 'message' => 'Nomor LPK tidak boleh kosong']);
        }
    }

    public function showModalNoOrder()
    {
        if (isset($this->code) && $this->code != '') {
            $this->product = MsProduct::where('code', $this->code)->first();
            if ($this->product == null) {
                $this->dispatch('notification', ['type' => 'warning', 'message' => 'Nomor Order ' . $this->code . ' Tidak Terdaftar']);
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
                // dd($this->product);

                // show modal
                $this->dispatch('showModalNoOrder');
            }
        } else {
            $this->dispatch('notification', ['type' => 'warning', 'message' => 'Nomor Order tidak boleh kosong']);
        }
    }

    public function addGentan()
    {
        $validatedData = $this->validate([
            'kenpin_date' => 'required',
            'lpk_no' => 'required',
            'employeeno' => 'required',
        ]);

        $this->gentan_no = '';
        $this->machineno = '';
        $this->namapetugas = '';
        $this->berat_loss = '';

        if ($validatedData) {
            $this->dispatch('showModal');
        }
    }

    public function saveGentan()
    {
        $validatedData = $this->validate([
            'gentan_no' => 'required',
            'berat_loss' => 'required',
        ]);

        $tdpa = TdProductAssembly::where('lpk_id', $this->lpk_id)->first();

        $datas = new TdKenpinAssemblyDetail();
        $datas->product_assembly_id = $tdpa->id;
        $datas->berat_loss = $this->berat_loss;
        $datas->trial468 = 'T';
        $datas->lpk_id = $this->lpk_id;
        $datas->berat = $this->berat;
        $datas->frekuensi = $this->frekuensi;
        $datas->kenpin_assembly_id = $this->orderid;

        $datas->created_on = Carbon::now();
        $datas->created_by = auth()->user()->username;
        $datas->updated_on = Carbon::now();
        $datas->updated_by = auth()->user()->username;

        $datas->save();
        $this->dispatch('notification', ['type' => 'success', 'message' => 'Data Berhasil di Simpan']);

        $this->dispatch('closeModal');
    }

    public function deleteInfure()
    {
        $data = TdKenpinAssemblyDetail::where('id', $this->orderid)->first();
        $data->delete();

        $this->dispatch('notification', ['type' => 'success', 'message' => 'Data Berhasil di Hapus']);
    }

    public function save()
    {
        $validatedData = $this->validate([
            'employeeno' => 'required',
            'status_kenpin' => 'required',
            'lpk_no' => 'required'
        ]);

        DB::beginTransaction();
        try {
            $mspetugas = MsEmployee::where('employeeno', $this->employeeno)->first();

            $product = TdKenpinAssembly::find($this->orderid);
            $product->kenpin_no = $this->kenpin_no;
            $product->kenpin_date = $this->kenpin_date;
            $product->employee_id = $mspetugas->id;
            $product->lpk_id = $this->lpk_id;
            // $product->berat_loss = $this->berat_loss;
            $product->remark = $this->remark;
            $product->status_kenpin = $this->status_kenpin;
            $product->updated_on = Carbon::now();
            $product->updated_by = auth()->user()->username;
            $product->save();

            TdKenpinAssemblyDetail::where('product_assembly_id', $this->lpk_id)->update([
                'kenpin_assembly_id' => $product->id,
            ]);

            DB::commit();
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Order saved successfully.']);
            return redirect()->route('kenpin-infure');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Failed to save the order: ' . $e->getMessage()]);
        }
    }

    public function cancel()
    {
        return redirect()->route('kenpin-infure');
    }

    public function render()
    {
        if (isset($this->lpk_no) && $this->lpk_no != '') {
            $tdorderlpk = DB::table('tdorderlpk as tolp')
                ->select(
                    'tolp.id',
                    'tolp.lpk_date',
                    'tolp.panjang_lpk',
                    'tolp.created_on',
                    'mp.code',
                    'mp.name',
                    'mp.ketebalan',
                    'mp.diameterlipat',
                    'tolp.qty_gulung',
                    'tolp.qty_gentan'
                )
                ->join('msproduct as mp', 'mp.id', '=', 'tolp.product_id')
                ->where('tolp.lpk_no', $this->lpk_no)
                ->first();

            if ($tdorderlpk == null) {
                $this->dispatch('notification', ['type' => 'warning', 'message' => 'Nomor LPK ' . $this->lpk_no . ' Tidak Terdaftar']);
            } else {
                $this->lpk_date = Carbon::parse($tdorderlpk->lpk_date)->format('Y-m-d');
                $this->panjang_lpk = $tdorderlpk->panjang_lpk;
                $this->code = $tdorderlpk->code;
                $this->name = $tdorderlpk->name;
                $this->lpk_id = $tdorderlpk->id;

                // $this->details = DB::table('tdproduct_assembly AS tdpa')
                //     ->select(
                //         'tad.id AS id',
                //         'tdpa.lpk_id',
                //         'tdol.lpk_no AS lpk_no',
                //         'tdol.lpk_date AS lpk_date',
                //         'tdol.panjang_lpk AS panjang_lpk',
                //         'tdpa.production_date AS tglproduksi',
                //         'tdpa.employee_id AS employee_id',
                //         'mse.empname AS namapetugas',
                //         'tdpa.work_shift AS work_shift',
                //         'tdpa.work_hour AS work_hour',
                //         'tdpa.machine_id AS machine_id',
                //         'msm.machineno AS nomesin',
                //         'msm.machinename AS namamesin',
                //         'tdpa.nomor_han AS nomor_han',
                //         'tdpa.gentan_no AS gentan_no',
                //         'tdpa.product_id',
                //         'msp.code AS code',
                //         'msp.name AS namaproduk',
                //         'tad.berat_loss'
                //     )
                //     ->join('tdorderlpk AS tdol', 'tdpa.lpk_id', '=', 'tdol.id')
                //     ->join('msemployee AS mse', 'mse.id', '=', 'tdpa.employee_id')
                //     ->join('msproduct AS msp', 'msp.id', '=', 'tdpa.product_id')
                //     ->join('msmachine AS msm', 'msm.id', '=', 'tdpa.machine_id')
                //     ->join('tdkenpin_assembly_detail AS tad', 'tad.lpk_id', '=', 'tdol.id')
                //     ->where('tad.kenpin_assembly_id', $this->orderid)
                //     ->get();
            }
        }

        if (isset($this->employeeno) && $this->employeeno != '') {
            $msemployee = MsEmployee::where('employeeno', $this->employeeno)->first();

            if ($msemployee == null) {
                $this->dispatch('notification', ['type' => 'warning', 'message' => 'Employee ' . $this->employeeno . ' Tidak Terdaftar']);
            } else {
                $this->empname = $msemployee->empname;
            }
        }

        // if (isset($this->gentan_no) && $this->gentan_no != '') {
        //     $gentan = DB::table('tdproduct_assembly AS tdpa')
        //         ->select(
        //             'tdpa.id AS id',
        //             'mse.empname AS namapetugas',
        //             'msm.machineno AS nomesin',
        //         )
        //         ->join('msemployee AS mse', 'mse.id', '=', 'tdpa.employee_id')
        //         ->join('msmachine AS msm', 'msm.id', '=', 'tdpa.machine_id')
        //         ->where('tdpa.lpk_id', $this->lpk_id)
        //         ->where('tdpa.gentan_no', $this->gentan_no)
        //         ->first();

        //     if ($gentan == null) {
        //         $this->dispatch('notification', ['type' => 'warning', 'message' => 'Nomor Gentan ' . $this->gentan_no . ' Tidak Terdaftar']);
        //     } else {
        //         $this->machineno = $gentan->nomesin;
        //         $this->namapetugas = $gentan->namapetugas;
        //     }
        // }

        return view('livewire.kenpin.edit-kenpin')->extends('layouts.master');
    }
}
