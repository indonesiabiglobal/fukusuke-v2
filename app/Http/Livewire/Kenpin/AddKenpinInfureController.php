<?php

namespace App\Http\Livewire\Kenpin;

use Livewire\Component;
use App\Models\TdOrder;
use App\Models\MsEmployee;
use App\Models\MsLossKenpin;
use App\Models\MsProduct;
use App\Models\TdKenpinAssembly;
use App\Models\TdKenpinAssemblyDetail;
use App\Models\TdOrderLpk;
use App\Models\TdProductAssembly;
use App\Models\MsMachinePartDetail;
use App\Models\MsMasalahKenpinInfure;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AddKenpinInfureController extends Component
{
    public $kenpin_date;
    public $kenpin_no;
    public $lpk_no;
    public $lpk_date;
    public $panjang_lpk;
    public $productId;
    public $productAssemblyId;
    public $code;
    public $name;
    public $employeeno;
    public $empname;
    public $remark;
    public $status_kenpin = 1;
    public $details;
    public $lpk_id;
    public $gentan_no;
    public $machineno;
    public $namapetugas;
    public $workShift;
    public $nomorHan;
    public $tglproduksi;
    public $berat_loss;
    public $beratLossTotal;
    public $orderid;
    public $msLossKenpin;
    public $code_loss;
    public $berat;
    public $frekuensi;
    public $idKenpinAssemblyDetailUpdate;
    public $currentId = 1;

    public $masalahInfure;
    public $kode_ng;
    public $nama_ng;
    public $bagian_mesin_id;
    public $bagianMesinList;
    public $penyebab;
    public $keterangan_penyebab;
    public $penanggulangan;

    // data master produk
    public $masterKatanuki;
    public $product;
    public $photoKatanuki;
    public $katanuki_id;

    // data LPK
    public $orderLPK;

    public function mount()
    {
        $this->details = collect([]);
        $this->kenpin_date = Carbon::now()->format('d-m-Y');
        $today = Carbon::now();

        $latestKenpin = TdKenpinAssembly::whereRaw("kenpin_no LIKE ?", ['INF' . $today->format('ym') . '%'])
            ->orderBy('kenpin_no', 'desc')
            ->first();

        if ($latestKenpin) {
            $lastNumber = (int)substr($latestKenpin->kenpin_no, -3);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        $this->kenpin_no = 'INF' . $today->format('ym') . '-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

        $this->msLossKenpin = MsLossKenpin::get();
        $this->bagianMesinList = MsMachinePartDetail::whereHas('machinePart', function ($query) {
            $query->where('department_id', 2);
        })->get();
    }

    public function updatedKodeNg()
    {
        if (!empty($this->kode_ng)) {
            $this->masalahInfure = MsMasalahKenpinInfure::where('code', $this->kode_ng)->first();
            if ($this->masalahInfure) {
                $this->nama_ng = $this->masalahInfure->name;
            } else {
                $this->kode_ng = '';
                $this->nama_ng = '';
                $this->dispatch('notification', ['type' => 'warning', 'message' => 'Kode NG tidak ditemukan']);
            }
        } else {
            $this->nama_ng = '';
        }
    }

    public function updatedLpkNo()
    {
        if (isset($this->lpk_no) && $this->lpk_no != '' && strlen($this->lpk_no) >= 10) {
            $tdorderlpk = DB::table('tdorderlpk as tolp')
                ->select(
                    'tolp.lpk_no',
                    'tolp.id',
                    'tolp.lpk_date',
                    'tolp.panjang_lpk',
                    'tolp.created_on',
                    'mp.id as productId',
                    'mp.code',
                    'mp.name',
                    'mp.ketebalan',
                    'mp.diameterlipat',
                    'tolp.qty_gulung',
                    'tolp.qty_gentan'
                )
                ->join('msproduct as mp', 'mp.id', '=', 'tolp.product_id')
                ->where('tolp.lpk_no', 'ilike', '%' . $this->lpk_no . '%')
                ->first();

            if ($tdorderlpk == null) {
                $this->lpk_date = '';
                $this->panjang_lpk = '';
                $this->code = '';
                $this->name = '';
                $this->lpk_id = '';
                $this->lpk_no = '';
                $this->dispatch('notification', ['type' => 'warning', 'message' => 'Nomor LPK ' . $this->lpk_no . ' Tidak Terdaftar']);
            } else {
                $this->resetValidation('lpk_no');

                $this->lpk_date = Carbon::parse($tdorderlpk->lpk_date)->format('d M Y');
                $this->panjang_lpk = number_format($tdorderlpk->panjang_lpk);
                $this->productId = $tdorderlpk->productId;
                $this->code = $tdorderlpk->code;
                $this->name = $tdorderlpk->name;
                $this->lpk_id = $tdorderlpk->id;
                $this->lpk_no = $tdorderlpk->lpk_no;
            }
        }
    }

    public function updatedEmployeeno()
    {
        if (isset($this->employeeno) && $this->employeeno != '' && strlen($this->employeeno) >= 2) {
            $msemployee = MsEmployee::where('employeeno', 'ilike', '%' . $this->employeeno . '%')->first();

            if ($msemployee == null) {
                $this->dispatch('notification', ['type' => 'warning', 'message' => 'Employee ' . $this->employeeno . ' Tidak Terdaftar']);
                $this->employeeno = '';
                $this->empname = '';
            } else {
                $this->employeeno = $msemployee->employeeno;
                $this->empname = $msemployee->empname;
                $this->resetValidation('employeeno');
            }
        }
    }

    public function updatedGentanNo()
    {

        if (isset($this->gentan_no) && $this->gentan_no != '') {
            $gentan = DB::table('tdproduct_assembly AS tdpa')
                ->select(
                    'tdpa.id AS id',
                    'tdpa.work_shift',
                    'tdpa.nomor_han',
                    'tdpa.production_date',
                    'mse.empname AS namapetugas',
                    'msm.machineno AS nomesin',
                )
                ->join('msemployee AS mse', 'mse.id', '=', 'tdpa.employee_id')
                ->join('msmachine AS msm', 'msm.id', '=', 'tdpa.machine_id')
                ->where('tdpa.lpk_id', $this->lpk_id)
                ->where('tdpa.gentan_no', $this->gentan_no)
                ->first();

            if ($gentan == null) {
                $this->dispatch('notification', ['type' => 'warning', 'message' => 'Nomor Gentan ' . $this->gentan_no . ' Tidak Terdaftar']);
            } else {
                $this->productAssemblyId = $gentan->id;
                $this->machineno = $gentan->nomesin;
                $this->namapetugas = $gentan->namapetugas;
                $this->workShift = $gentan->work_shift;
                $this->nomorHan = $gentan->nomor_han;
                $this->tglproduksi = $gentan->production_date;
            }
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

                // show modal
                $this->dispatch('showModalNoOrder');
            }
        } else {
            $this->dispatch('notification', ['type' => 'warning', 'message' => 'Nomor Order tidak boleh kosong']);
        }
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

    public function addGentan()
    {
        $validatedData = $this->validate([
            'kenpin_date' => 'required',
            'lpk_no' => 'required',
            'employeeno' => 'required',
        ], [
            'kenpin_date.required' => 'Tanggal Kenpin tidak boleh kosong',
            'lpk_no.required' => 'Nomor LPK tidak boleh kosong',
            'employeeno.required' => 'Petugas tidak boleh kosong',
        ]);

        if ($validatedData) {
            $this->gentan_no = '';
            $this->machineno = '';
            $this->namapetugas = '';
            $this->berat_loss = '';
            $this->berat = '';
            // $this->frekuensi = '';
            $this->dispatch('showModalAddGentan');
        }
    }

    public function edit($idKenpinAssemblyDetailUpdate)
    {
        $this->idKenpinAssemblyDetailUpdate = $idKenpinAssemblyDetailUpdate;
        array_map(function ($detail) use ($idKenpinAssemblyDetailUpdate) {
            if ($detail['id'] == $idKenpinAssemblyDetailUpdate) {
                $this->gentan_no = $detail['gentan_no'];
                $this->machineno = $detail['machineno'];
                $this->namapetugas = $detail['namapetugas'];
                $this->berat_loss = $detail['berat_loss'];
                $this->frekuensi = $detail['frekuensi'];
            }
        }, $this->details->toArray());
    }

    public function saveGentan()
    {
        $validatedData = $this->validate([
            'gentan_no' => 'required',
            'berat_loss' => 'required',
        ]);

        if ($this->idKenpinAssemblyDetailUpdate) {
            $this->details = $this->details->map(function ($detail) {
                if ($detail['id'] == $this->idKenpinAssemblyDetailUpdate) {
                    $detail['gentan_no'] = $this->gentan_no;
                    $detail['machineno'] = $this->machineno;
                    $detail['namapetugas'] = $this->namapetugas;
                    $detail['work_shift'] = $this->workShift;
                    $detail['nomor_han'] = $this->nomorHan;
                    $detail['berat_loss'] = $this->berat_loss;
                    $detail['tglproduksi'] = $this->tglproduksi;
                    $detail['frekuensi'] = $this->frekuensi;
                }
                return $detail;
            });
            $this->dispatch('closeModalEditGentan');
        } else {
            $this->details->push([
                'id' => $this->nextId(),
                'gentan_no' => $this->gentan_no,
                'machineno' => $this->machineno,
                'namapetugas' => $this->namapetugas,
                'work_shift' => $this->workShift,
                'nomor_han' => $this->nomorHan,
                'tglproduksi' => $this->tglproduksi,
                'berat_loss' => (int)str_replace(',', '', $this->berat_loss),
                'frekuensi' => $this->frekuensi,
            ]);
            $this->dispatch('closeModalAddGentan');
        }

        $this->beratLossTotal = $this->details->sum('berat_loss');
        $this->dispatch('notification', ['type' => 'success', 'message' => 'Data Berhasil di Simpan']);
    }

    public function nextId()
    {
        return $this->currentId++;
    }

    public function deleteInfure($idKenpinGoodDetailUpdate)
    {
        // delete item from details
        $this->details = $this->details->filter(function ($detail) use ($idKenpinGoodDetailUpdate) {
            return $detail['id'] != $idKenpinGoodDetailUpdate;
        });

        $this->dispatch('notification', ['type' => 'success', 'message' => 'Data Berhasil di Hapus']);
    }

    public function save()
    {
        $validatedData = $this->validate([
            'employeeno' => 'required',
            'status_kenpin' => 'required',
            'lpk_no' => 'required',
            'kode_ng' => 'required',
            'penyebab' => 'required',
            'keterangan_penyebab' => 'required',
            'penanggulangan' => 'required',
            'bagian_mesin_id' => 'required'
        ]);

        DB::beginTransaction();
        try {
            $mspetugas = MsEmployee::where('employeeno', $this->employeeno)->first();

            $product = new TdKenpinAssembly();
            $product->kenpin_no = $this->kenpin_no;
            $product->kenpin_date = $this->kenpin_date;
            $product->employee_id = $mspetugas->id;
            $product->lpk_id = $this->lpk_id;
            $product->total_berat_loss = $this->beratLossTotal;
            $product->status_kenpin = $this->status_kenpin;
            $product->masalah_infure_id = $this->masalahInfure->id;
            $product->machine_part_detail_id = $this->bagian_mesin_id;
            $product->penyebab = $this->penyebab;
            $product->keterangan_penyebab = $this->keterangan_penyebab;
            $product->penanggulangan = $this->penanggulangan;
            $product->created_on = Carbon::now();
            $product->created_by = auth()->user()->username;
            $product->updated_on = Carbon::now();
            $product->updated_by = auth()->user()->username;
            $product->save();

            foreach ($this->details as $item) {
                $details = new TdKenpinAssemblyDetail();
                $details->kenpin_assembly_id = $product->id;
                $details->product_assembly_id = $this->productAssemblyId;
                $details->berat_loss = $item['berat_loss'];
                $details->frekuensi = $item['frekuensi'];
                $details->created_on = Carbon::now();
                $details->created_by = auth()->user()->username;
                $details->updated_on = Carbon::now();
                $details->updated_by = auth()->user()->username;
                $details->save();

                // update status kenpin on td_product_assembly
                $productAssembly = TdProductAssembly::find($this->productAssemblyId);
                if ($productAssembly) {
                    $productAssembly->status_kenpin = 1; // Mark as kenpin processed
                    $productAssembly->updated_on = Carbon::now();
                    $productAssembly->updated_by = auth()->user()->username;
                    $productAssembly->save();
                }
            }

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
        return view('livewire.kenpin.add-kenpin')->extends('layouts.master');
    }
}
