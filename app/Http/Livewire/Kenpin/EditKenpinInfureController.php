<?php

namespace App\Http\Livewire\Kenpin;

use Livewire\Component;
use App\Models\MsEmployee;
use App\Models\MsProduct;
use App\Models\TdKenpin;
use App\Models\TdKenpinAssemblyDetail;
use App\Models\TdProductAssembly;
use App\Models\MsMachinePartDetail;
use App\Models\MsMasalahKenpin;
use App\Models\MsLossKenpin;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class EditKenpinInfureController extends Component
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
    public $idKenpinAssemblyDetailUpdate;
    public $currentId = 1;

    public $productAssemblyId;
    public $workShift;
    public $nomorHan;
    public $tglproduksi;

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

    public function mount(Request $request)
    {
        $data = DB::table('tdkenpin AS tda')
            ->join('tdorderlpk AS tdo', 'tdo.id', '=', 'tda.lpk_id')
            ->join('msproduct AS msp', 'msp.id', '=', 'tdo.product_id')
            ->join('msemployee AS mse', 'mse.id', '=', 'tda.employee_id')
            ->leftJoin('msmasalahkenpin AS mmi', 'mmi.id', '=', 'tda.masalah_kenpin_id')
            ->where('tda.id', $request->query('orderId'))
            ->select(
                'tda.id',
                'tda.kenpin_date',
                'tda.kenpin_no',
                'tda.lpk_id',
                'tdo.lpk_no',
                'tdo.lpk_date',
                'tdo.panjang_lpk',
                'msp.code',
                'msp.name',
                'mse.employeeno',
                'mse.empname',
                'tda.status_kenpin',
                'tda.machine_part_detail_id',
                'tda.penyebab',
                'tda.keterangan_penyebab',
                'tda.penanggulangan',
                'mmi.code as kode_ng',
                'mmi.name as nama_ng',
                'mmi.id as masalah_kenpin_id'
            )
            ->first();

        // Inisialisasi data dasar
        $this->orderid = $data->id;
        $this->kenpin_date = Carbon::parse($data->kenpin_date)->format('d-m-Y');
        $this->kenpin_no = $data->kenpin_no;
        $this->lpk_id = $data->lpk_id;
        $this->lpk_no = $data->lpk_no;
        $this->lpk_date = Carbon::parse($data->lpk_date)->format('d-m-Y');
        $this->panjang_lpk = $data->panjang_lpk;
        $this->code = $data->code;
        $this->name = $data->name;
        $this->employeeno = $data->employeeno;
        $this->empname = $data->empname;
        $this->status_kenpin = $data->status_kenpin;

        // Inisialisasi field baru
        $this->bagian_mesin_id = $data->machine_part_detail_id;
        $this->penyebab = $data->penyebab;
        $this->keterangan_penyebab = $data->keterangan_penyebab;
        $this->penanggulangan = $data->penanggulangan;
        $this->kode_ng = $data->kode_ng;
        $this->nama_ng = $data->nama_ng;

        // Load master data
        $this->bagianMesinList = MsMachinePartDetail::whereHas('machinePart', function ($query) {
            $query->where('department_id', 2);
        })->get();

        if ($data->masalah_kenpin_id) {
            $this->masalahInfure = MsMasalahKenpin::find($data->masalah_kenpin_id);
        }

        $this->details = DB::table('tdkenpin_assembly_detail AS tkad')
            ->join('tdproduct_assembly AS tpa', 'tpa.id', '=', 'tkad.product_assembly_id')
            ->join('msemployee AS mse', 'mse.id', '=', 'tpa.employee_id')
            ->join('msmachine AS msm', 'msm.id', '=', 'tpa.machine_id')
            ->where('tkad.kenpin_id', $this->orderid)
            ->select(
                'tkad.id',
                'tkad.berat_loss',
                'tkad.product_assembly_id',
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

    public function updatedKodeNg()
    {
        if (!empty($this->kode_ng)) {
            $this->masalahInfure = MsMasalahKenpin::where('code', $this->kode_ng)->first();
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

    public function updatedLpkNo()
    {
        if (!empty($this->lpk_no)) {
            $data = DB::table('tdorderlpk as tolp')
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

            if ($data) {
                $this->lpk_date = Carbon::parse($data->lpk_date)->format('d-m-Y');
                $this->panjang_lpk = $data->panjang_lpk;
                $this->code = $data->code;
                $this->name = $data->name;
                $this->lpk_id = $data->id;
            } else {
                $this->lpk_date = '';
                $this->panjang_lpk = '';
                $this->code = '';
                $this->name = '';
                $this->lpk_id = '';
                $this->addError('lpk_no', 'Nomor LPK tidak ditemukan');
            }
        } else {
            $this->lpk_date = '';
            $this->panjang_lpk = '';
            $this->code = '';
            $this->name = '';
            $this->lpk_id = '';
            $this->resetErrorBag('lpk_no');
        }
    }

    public function updatedGentanNo()
    {
        if (!empty($this->gentan_no) && !empty($this->lpk_id)) {
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

            if ($gentan) {
                $this->productAssemblyId = $gentan->id;
                $this->machineno = $gentan->nomesin;
                $this->namapetugas = $gentan->namapetugas;
                $this->workShift = $gentan->work_shift;
                $this->nomorHan = $gentan->nomor_han;
                $this->tglproduksi = $gentan->production_date;
            } else {
                $this->machineno = '';
                $this->namapetugas = '';
                $this->addError('gentan_no', 'Nomor Gentan tidak ditemukan');
            }
        } else {
            $this->machineno = '';
            $this->namapetugas = '';
            $this->resetErrorBag('gentan_no');
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


        try {
            DB::beginTransaction();
            $datas = new TdKenpinAssemblyDetail();
            $datas->product_assembly_id = $this->productAssemblyId;
            $datas->berat_loss = $this->berat_loss;
            $datas->frekuensi = $this->frekuensi;
            $datas->kenpin_id = $this->orderid;

            $datas->created_on = Carbon::now();
            $datas->created_by = auth()->user()->username;
            $datas->updated_on = Carbon::now();
            $datas->updated_by = auth()->user()->username;

            $datas->save();

            // update total berat loss di table utama
            $product = TdKenpin::find($this->orderid);
            $this->beratLossTotal = $this->details->sum('berat_loss') + (int)str_replace(',', '', $this->berat_loss);
            $product->total_berat_loss = $this->beratLossTotal;
            $product->updated_on = Carbon::now();
            $product->updated_by = auth()->user()->username;
            $product->save();

            // update status pada tabel tdproduct_assembly
            TdProductAssembly::where('id', $this->productAssemblyId)->update(['status_kenpin' => 1]); // 1 = Dalam proses kenpin

            $detail = new \stdClass();
            $detail->id = $datas->id;
            $detail->berat_loss = (int)str_replace(',', '', $this->berat_loss);
            $detail->product_assembly_id = $this->productAssemblyId;
            $detail->tglproduksi = $this->tglproduksi;
            $detail->work_shift = $this->workShift;
            $detail->nomesin = $this->machineno;
            $detail->namapetugas = $this->namapetugas;
            $detail->nomor_han = $this->nomorHan;
            $detail->gentan_no = $this->gentan_no;

            $this->details->push($detail);
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Data Berhasil di Simpan']);

            $this->dispatch('closeModal');
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Gagal menyimpan data: ' . $e->getMessage()]);
        }
    }

    public function deleteInfure($id)
    {
        try {
            $data = TdKenpinAssemblyDetail::where('id', $id)->first();
            $data->delete();

            // menghitung ulang total berat loss
            $product = TdKenpin::find($this->orderid);
            $this->beratLossTotal = $this->details->where('id', '!=', $id)->sum('berat_loss');
            $product->total_berat_loss = $this->beratLossTotal;
            $product->updated_on = Carbon::now();
            $product->updated_by = auth()->user()->username;
            $product->save();

            // update status pada tabel tdproduct_assembly
            TdProductAssembly::where('id', $data->product_assembly_id)->update(['status_kenpin' => 0]); // 0 = Tidak dalam proses kenpin

            // update data details
            $this->details = $this->details->filter(function ($item) use ($id) {
                return $item->id !== $id;
            });

            $this->dispatch('notification', ['type' => 'success', 'message' => 'Data Berhasil di Hapus']);
        } catch (\Exception $e) {
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Gagal menghapus data: ' . $e->getMessage()]);
        }
    }

    public function save()
    {
        $validatedData = $this->validate([
            'employeeno' => 'required',
            'status_kenpin' => 'required',
            'lpk_no' => 'required'
        ]);

        if ($this->status_kenpin == 2) {
            // Validasi tambahan jika status kenpin adalah 2 "Finish"
            // validasi untuk field penyebab, keterangan_penyebab, penanggulangan, kode_ng, bagian_mesin_id
            $additionalValidation = $this->validate([
                'penyebab' => 'required',
                'keterangan_penyebab' => 'required',
                'penanggulangan' => 'required',
                'kode_ng' => 'required',
                'bagian_mesin_id' => 'required',
            ]);
        }

        DB::beginTransaction();
        try {
            $mspetugas = MsEmployee::where('employeeno', $this->employeeno)->first();

            $product = TdKenpin::find($this->orderid);
            $product->kenpin_no = $this->kenpin_no;
            $product->kenpin_date = $this->kenpin_date;
            $product->employee_id = $mspetugas->id;
            $product->lpk_id = $this->lpk_id;
            // $product->berat_loss = $this->berat_loss;
            $product->status_kenpin = $this->status_kenpin;

            // Field baru yang ditambahkan
            $product->machine_part_detail_id = $this->bagian_mesin_id;
            $product->penyebab = $this->penyebab;
            $product->keterangan_penyebab = $this->keterangan_penyebab;
            $product->penanggulangan = $this->penanggulangan;

            // Set masalah_kenpin_id berdasarkan kode_ng
            if (!empty($this->kode_ng) && $this->masalahInfure) {
                $product->masalah_kenpin_id = $this->masalahInfure->id;
            } else {
                $product->masalah_kenpin_id = null;
            }

            $product->done_at = Carbon::now();

            $product->updated_on = Carbon::now();
            $product->updated_by = auth()->user()->username;
            $product->save();

            TdKenpinAssemblyDetail::where('product_assembly_id', $this->productAssemblyId)->update([
                'kenpin_id' => $product->id,
            ]);

            // Jika status kenpin adalah 2 "Finish", maka update status pada tabel tdproduct_assembly
            if ($this->status_kenpin == 2) {
                TdProductAssembly::whereIn('id', function ($query) use ($product) {
                    $query->select('product_assembly_id')
                        ->from('tdkenpin_assembly_detail')
                        ->where('kenpin_id', $product->id);
                })->update(['status_kenpin' => 0]); // 0 = Tidak dalam proses kenpin
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
        return view('livewire.kenpin.edit-kenpin')->extends('layouts.master');
    }
}
