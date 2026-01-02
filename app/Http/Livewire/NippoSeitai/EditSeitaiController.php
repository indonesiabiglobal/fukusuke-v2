<?php

namespace App\Http\Livewire\NippoSeitai;

use Livewire\Component;
use App\Models\TdOrder;
use App\Models\MsBuyer;
use App\Models\MsEmployee;
use App\Models\MsLossSeitai;
use App\Models\MsMachine;
use App\Models\MsProduct;
use App\Models\TdOrderLpk;
use App\Models\TdProductAssembly;
use App\Models\TdProductGoods;
use App\Models\TdProductGoodsAssembly;
use App\Models\TdProductGoodsLoss;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class EditSeitaiController extends Component
{
    public $orderId;
    public $production_date;
    public $created_on;
    public $work_hour;
    public $lpk_date;
    public $panjang_lpk;
    public $code;
    public $name;
    public $dimensiinfure;
    public $qty_gulung;
    public $qty_gentan;
    public $machinename;
    public $empname;
    public $gentan_no;
    public $lpk_no;
    public $qty_lpk;
    public $machineno;
    public $employeeno;
    public $employeenoinfure;
    public $empnameinfure;
    public $work_shift;
    public $nomor_lot;
    public $qty_produksi;
    public $qty_produksi_old;
    public $nomor_palet;
    public $infure_berat_loss;
    public $production_no;
    public $detailsGentan = [];
    public $detailsLoss = [];
    public $tdpgId;
    public $berat_produksi;
    public $petugas;
    public $machine_no;
    public $namaloss;
    public $gentan_line;
    public $code_loss;
    public $loss_seitai_id;
    public $berat_loss;
    public $berat;
    public $frekuensi;
    public $berat_fr;
    public $jumlahBeratProduksi;
    public $jumlahBeratLoss;
    public $seq_no;
    public $selisih;
    public $jumlah_box;
    public $jumlah_box_product;
    public $caseBoxCount;
    public $selisihOld;
    public $idDelete;
    public $editing_id;
    public $activeTab = 'Gentan';
    public $start_box;
    public $end_box;

    // data master produk
    public $masterKatanuki;
    public $product;
    public $photoKatanuki;
    public $katanuki_id;
    public $total_assembly_qty;
    public $selisihMaster;

    // data LPK
    public $orderLPK;

    public function mount(Request $request)
    {
        $data = DB::table('tdproduct_goods AS tdpg')
            ->leftJoin('msproduct AS msp', 'tdpg.product_id', '=', 'msp.id')
            ->leftJoin('msmachine AS msm', 'tdpg.machine_id', '=', 'msm.id')
            ->leftJoin('tdorderlpk AS tdol', 'tdpg.lpk_id', '=', 'tdol.id')
            ->leftJoin('msemployee AS mse', 'tdpg.employee_id', '=', 'mse.id')
            ->leftJoin('msemployee AS mse2', 'tdpg.employee_id_infure', '=', 'mse2.id')
            ->select(
                'tdpg.id AS id',
                'tdpg.production_no AS production_no',
                'tdpg.production_date AS production_date',
                'tdpg.employee_id AS employee_id',
                'tdpg.employee_id_infure AS employee_id_infure',
                'tdpg.work_shift AS work_shift',
                'tdpg.work_hour AS work_hour',
                'tdpg.machine_id AS machine_id',
                'tdpg.lpk_id AS lpk_id',
                'tdpg.product_id AS product_id',
                'tdpg.qty_produksi AS qty_produksi',
                'tdpg.seitai_berat_loss AS seitai_berat_loss',
                'tdpg.infure_berat_loss AS infure_berat_loss',
                'tdpg.nomor_palet AS nomor_palet',
                'tdpg.nomor_lot AS nomor_lot',
                'tdpg.seq_no AS seq_no',
                'tdpg.status_production AS status_production',
                'tdpg.status_warehouse AS status_warehouse',
                'tdpg.kenpin_qty_loss AS kenpin_qty_loss',
                'tdpg.kenpin_qty_loss_proses AS kenpin_qty_loss_proses',
                'tdpg.start_box AS start_box',
                'tdpg.end_box AS end_box',
                'tdpg.created_by AS created_by',
                'tdpg.created_on AS created_on',
                'tdpg.updated_by AS updated_by',
                'tdpg.updated_on AS updated_on',
                'tdol.order_id AS order_id',
                'tdol.lpk_no AS lpk_no',
                'tdol.lpk_date AS lpk_date',
                'tdol.panjang_lpk AS panjang_lpk',
                'tdol.qty_gentan AS qty_gentan',
                'tdol.qty_gulung AS qty_gulung',
                'tdol.qty_lpk AS qty_lpk',
                'tdol.total_assembly_qty AS total_assembly_qty',
                'msp.code',
                'msp.name',
                'msp.case_box_count',
                'msm.machineno',
                'msm.machinename',
                'mse.employeeno',
                'mse.empname',
                'mse2.employeeno as employeenoinfure',
                'mse2.empname as empnameinfure'
            )
            ->where('tdpg.id', $request->query('orderId'))
            ->first();

        $this->orderId = $request->query('orderId');
        $this->tdpgId = $data->id;
        $this->production_no = $data->production_no;
        $this->production_date = Carbon::parse($data->production_date)->format('d/m/Y');
        $this->created_on = Carbon::parse($data->created_on)->format('d/m/Y') . ' - Nomor: ' . $data->seq_no;
        $this->lpk_no = $data->lpk_no;
        $this->lpk_date = Carbon::parse($data->lpk_date)->format('d/M/Y');
        $this->qty_lpk = $data->qty_lpk;
        $this->code = $data->code;
        $this->name = $data->name;
        $this->machineno = $data->machineno;
        $this->machinename = $data->machinename;
        $this->employeeno = $data->employeeno;
        $this->empname = $data->empname;
        $this->qty_produksi = number_format($data->qty_produksi);
        $this->qty_produksi_old = $data->qty_produksi;
        $this->nomor_palet = $data->nomor_palet;
        $this->nomor_lot = $data->nomor_lot;
        $this->infure_berat_loss = $data->infure_berat_loss;
        $this->employeenoinfure = $data->employeenoinfure;
        $this->empnameinfure = $data->empnameinfure;
        $this->work_hour = $data->work_hour;
        $this->work_shift = $data->work_shift;
        $this->start_box = $data->start_box;
        $this->end_box = $data->end_box;

        $this->detailsGentan = DB::table('tdproduct_assembly as tdpa')
            ->join('tdproduct_goods_assembly as tga', 'tga.product_assembly_id', '=', 'tdpa.id')
            ->leftJoin('msmachine as mm', 'mm.id', '=', 'tdpa.machine_id')
            ->leftJoin('msemployee as mse', 'mse.id', '=', 'tdpa.employee_id')
            ->select(
                'tga.id',
                'tdpa.gentan_no',
                'tga.gentan_line',
                // 'tga.frekuensi',
                'mm.machineno',
                'tdpa.work_shift',
                'mse.empname',
                'tdpa.production_date',
                'tdpa.berat_produksi'
            )
            ->where('tga.product_goods_id', $request->query('orderId'))
            ->get();
        $this->jumlahBeratProduksi = $this->detailsGentan->sum('berat_produksi');

        $this->detailsLoss = DB::table('tdproduct_goods_loss as tgl')
            ->join('mslossseitai as mss', 'mss.id', '=', 'tgl.loss_seitai_id')
            ->select(
                'tgl.id',
                'mss.code',
                'mss.name',
                'tgl.berat_loss'
            )
            ->where('tgl.product_goods_id', $request->query('orderId'))
            ->get();
        $this->jumlahBeratLoss = $this->detailsLoss->sum('berat_loss');

        $this->caseBoxCount = isset($data->case_box_count) ? (int) $data->case_box_count : 0;
        $this->jumlah_box_product = $this->caseBoxCount > 0 ? (int) ceil($data->qty_produksi / $this->caseBoxCount) : 0;
    }

    public function showModalNoOrder()
    {
        if (isset($this->code) && $this->code != '') {
            $this->product = MsProduct::where('code', $this->code)->first();
            if ($this->product == null) {
                $this->dispatch('notification', ['type' => 'warning', 'message' => 'Nomor Order ' . $this->code . ' Tidak Terdaftar']);
            } else {
                // nomor order produk
                $this->masterKatanuki = DB::table('mskatanuki')->where('id', $this->product->katanuki_id)->first(['name', 'filename']);
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

    public function changeTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function addGentan()
    {
        $validatedData = $this->validate([
            'lpk_no' => 'required',
            'nomor_palet' => 'required',
            'nomor_lot' => 'required',
        ]);

        if ($validatedData) {
            $this->gentan_no = '';
            $this->dispatch('showModalGentan');
        }
    }

    public function addLoss()
    {
        $validatedData = $this->validate([
            'lpk_no' => 'required',
            'nomor_palet' => 'required',
            'nomor_lot' => 'required',
        ]);

        if ($validatedData) {
            $this->code_loss = '';
            $this->berat_loss = 0;
            $this->frekuensi = 0;
            $this->dispatch('showModalLoss');
        }
    }

    public function delete()
    {
        $this->dispatch('showModalDelete');
        $this->skipRender();
    }

    public function deleteGentan($orderId)
    {
        // mengecek apakah data gentan tersisa 1
        if (count($this->detailsGentan) <= 1) {
            $this->dispatch('notification', ['type' => 'warning', 'message' => 'Minimal 1 data gentan harus tersisa']);
            return;
        }
        $data = TdProductGoodsAssembly::findOrFail($orderId);
        $data->delete();

        // update status production menjadi 0 pada TdProductAssembly
        TdProductAssembly::where('id', $data->product_assembly_id)->update([
            'status_production' => 0,
        ]);

        $this->dispatch('closeModalDeleteGentan', $orderId);
        $this->dispatch('notification', ['type' => 'success', 'message' => 'Data Berhasil di Hapus']);
    }

    public function deleteLoss($orderId)
    {
        $data = TdProductGoodsLoss::findOrFail($orderId);
        // mengurangi dari tdproduct_goods
        $tdproductgoods = TdProductGoods::where('id', $this->tdpgId)->update([
            'seitai_berat_loss' => $this->jumlahBeratLoss - $data->berat_loss
        ]);

        $data->delete();

        $this->dispatch('closeModalDeleteLoss', $orderId);
        $this->dispatch('notification', ['type' => 'success', 'message' => 'Data Berhasil di Hapus']);
    }

    public function editLoss($orderId)
    {
        $data = TdProductGoodsLoss::find($orderId);
        if ($data) {
            $this->editing_id = $data->id;
            $this->loss_seitai_id = $data->loss_seitai_id;

            $loss = MsLossSeitai::find($data->loss_seitai_id);
            $this->code_loss = $loss->code ?? '';
            $this->namaloss = $loss->name ?? '';

            $this->berat_loss = $data->berat_loss;
            $this->frekuensi = $data->frekuensi;

            $this->dispatch('openModalEditLoss');
        } else {
            $this->dispatch('notification', ['type' => 'warning', 'message' => 'Data Tidak Ditemukan']);
        }
    }

    public function updateLoss()
    {
        $validatedData = $this->validate([
            'loss_seitai_id' => 'required',
            'berat_loss' => 'required|numeric',
            'frekuensi' => 'required',
        ]);

        try {
            $data = TdProductGoodsLoss::findOrFail($this->editing_id);
            $oldBerat = $data->berat_loss;

            $data->loss_seitai_id = $this->loss_seitai_id;
            $data->berat_loss = $this->berat_loss;
            $data->frekuensi = $this->frekuensi;
            $data->updated_on = Carbon::now();
            $data->updated_by = auth()->user()->username;
            $data->save();

            // update total berat loss on tdproduct_goods
            $newTotal = $this->jumlahBeratLoss - $oldBerat + $this->berat_loss;
            TdProductGoods::where('id', $this->tdpgId)->update([
                'seitai_berat_loss' => $newTotal
            ]);

            $this->editing_id = null;
            $this->clearLoss();

            $this->dispatch('closeModal', 'modal-edit-loss');
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Data Berhasil di Update']);
        } catch (\Exception $e) {
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Gagal Update Data']);
        }
    }

    public function resetGentan()
    {
        $this->gentan_no = '';
        $this->gentan_line = '';
        $this->machine_no = '';
        $this->empname = '';
        $this->petugas = '';
        $this->berat_produksi = '';
        $this->gentan_line = '';
    }

    public function saveGentan()
    {
        $lpkid = TdOrderLpk::where('lpk_no', $this->lpk_no)->first();
        $assembly = TdProductAssembly::where('gentan_no', $this->gentan_no)
            ->where('lpk_id', $lpkid->id)
            ->first();

        // gentan tidak boleh sama dengan yang sudah ada
        $existingGentan = TdProductGoodsAssembly::where('product_goods_id', $this->tdpgId)
            ->where('gentan_line', $this->gentan_line)
            ->where('product_assembly_id', $assembly->id)
            ->first();

        if ($existingGentan) {
            $this->dispatch('notification', ['type' => 'warning', 'message' => 'Gentan sudah ada']);
            return;
        }

        $datas = new TdProductGoodsAssembly();
        $datas->product_goods_id = $this->tdpgId;
        $datas->product_assembly_id = $assembly->id;
        $datas->gentan_line = $this->gentan_line;
        $datas->lpk_id = $lpkid->id;

        $datas->created_on = Carbon::now();
        $datas->created_by = auth()->user()->username;
        $datas->updated_on = Carbon::now();
        $datas->updated_by = auth()->user()->username;

        $datas->save();

        // update status production pada TdProductAssembly
        $assembly->status_production = '1';
        $assembly->save();

        $this->resetGentan();

        $this->dispatch('closeModalGentan');
        $this->dispatch('notification', ['type' => 'success', 'message' => 'Data Berhasil di Simpan']);
    }

    public function clearLoss()
    {
        $this->loss_seitai_id = '';
        $this->code_loss = '';
        $this->namaloss = '';
        $this->berat_loss = '';
        $this->frekuensi = '';
    }

    public function saveLoss()
    {
        try {
            // validate
            $this->validate([
                'code_loss' => 'required',
                'berat_loss' => 'required|numeric',
                'frekuensi' => 'required',
            ], [
                'code_loss.required' => 'Kode Loss harus diisi',
                'berat_loss.required' => 'Berat Loss harus diisi',
                'berat_loss.numeric' => 'Berat Loss harus berupa angka',
                'frekuensi.required' => 'Frekuensi harus diisi',
            ]);
        } catch (\Exception $e) {
            $this->dispatch('notification', ['type' => 'warning', 'message' => 'Data Tidak Valid: ' . $e->getMessage()]);
            return;
        }

        $lpkid = TdOrderLpk::where('lpk_no', $this->lpk_no)->first();
        $loss = MsLossSeitai::where('code', $this->code_loss)
            ->first();

        $datas = new TdProductGoodsLoss();
        $datas->product_goods_id = $this->tdpgId;
        $datas->loss_seitai_id = $loss->id;
        $datas->berat_loss = $this->berat_loss;
        $datas->frekuensi = $this->frekuensi;
        $datas->lpk_id = $lpkid->id;

        $datas->created_on = Carbon::now();
        $datas->created_by = auth()->user()->username;
        $datas->updated_on = Carbon::now();
        $datas->updated_by = auth()->user()->username;

        // menambahkan ke tdproduct_goods
        $tdproductgoods = TdProductGoods::where('id', $this->tdpgId)->update([
            'seitai_berat_loss' => $this->jumlahBeratLoss + $this->berat_loss
        ]);

        $datas->save();

        $this->clearLoss();

        $this->dispatch('closeModalLoss');
        $this->dispatch('notification', ['type' => 'success', 'message' => 'Data Berhasil di Simpan']);
    }

    public function destroy()
    {
        DB::beginTransaction();
        try {
            $order = TdProductGoods::where('id', $this->orderId)->first();
            $order->delete();

            // update status production pada TdProductAssembly
            TdProductAssembly::where('lpk_id', $order['lpk_id'])->orderBy('seq_no', 'ASC')
                ->update([
                    'status_production' => 0,
                ]);

            DB::commit();
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Order Deleted successfully.']);
            return redirect()->route('nippo-seitai');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Failed to save the order: ' . $e->getMessage()]);
        }
    }

    public function save()
    {
        $this->qty_produksi = (int)str_replace(',', '', $this->qty_produksi);
        $validatedData = $this->validate([
            'lpk_no' => 'required',
            'nomor_palet' => 'required',
            'nomor_lot' => 'required',
            'work_hour' => 'required|regex:/^[0-9]{2}:[0-9]{2}$/',
            'start_box' => 'nullable',
            'end_box' => 'nullable',
        ]);

        try {
            $machine = MsMachine::where('machineno', $this->machineno)->first();
            $employe = MsEmployee::where('employeeno', $this->employeeno)->first();
            $employeinfure = MsEmployee::where('employeeno', $this->employeenoinfure)->first();

            $data = TdProductGoods::findOrFail($this->orderId);
            $data->production_date = $this->production_date . ' ' . $this->work_hour;
            $data->machine_id = $machine->id;
            $data->employee_id = $employe->id;
            if (isset($this->employeenoinfure)) {
                $data->employee_id_infure = $employeinfure->id;
            }
            $data->qty_produksi = $this->qty_produksi;
            $data->nomor_palet = $this->nomor_palet;
            $data->nomor_lot = $this->nomor_lot;
            // start and end box (Nomor Box range)
            if (isset($this->start_box)) {
                $data->start_box = $this->start_box ? $this->start_box : null;
            }
            if (isset($this->end_box)) {
                $data->end_box = $this->end_box ? $this->end_box : null;
            }
            $data->infure_berat_loss = $this->infure_berat_loss == '' ? 0 : $this->infure_berat_loss;
            $data->work_shift = $this->work_shift;
            $data->work_hour = $this->work_hour;

            $data->updated_on = Carbon::now();
            $data->updated_by = auth()->user()->username;

            $data->save();

            DB::commit();
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Order saved successfully.']);
            return redirect()->route('nippo-seitai');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Failed to save the order: ' . $e->getMessage()]);
        }
    }

    public function cancel()
    {
        return redirect()->route('nippo-seitai');
    }

    public function updatedMachineno($machineno)
    {
        $this->machineno = $machineno;

        if (isset($this->machineno) && $this->machineno != '') {
            $machine = MsMachine::where('machineno', 'ilike', '%' . $this->machineno . '%')->first();
            if ($machine == null) {
                $this->dispatch('notification', ['type' => 'error', 'message' => 'Machine ' . $this->machineno . ' Tidak Terdaftar']);
            } else {
                $this->machineno = $machine->machineno;
                $this->machinename = $machine->machinename;
            }
        }
    }

    public function updatedWorkHour($work_hour)
    {
        $this->work_hour = $work_hour;

        if (isset($this->work_hour) && $this->work_hour != '') {
            if (
                Carbon::createFromFormat('d/m/Y', $this->production_date)->isSameDay(Carbon::now())
                && Carbon::parse($this->work_hour)->format('H:i') > Carbon::now()->format('H:i')
            ) {
                $this->dispatch('notification', ['type' => 'warning', 'message' => 'Jam Kerja Tidak Boleh Melebihi Jam Sekarang']);
                $this->work_hour = Carbon::now()->format('H:i');
            }

            $this->workShift();
        }
    }

    public function workShift()
    {
        if (isset($this->work_hour) && $this->work_hour != '') {
            $workHourFormatted = Carbon::parse($this->work_hour)->format('H:i:s');

            $workingShift = DB::select("
                SELECT *
                FROM msworkingshift
                WHERE status = 1
                AND ((
                    -- Shift does not cross midnight
                    work_hour_from <= work_hour_till
                    AND '$workHourFormatted' BETWEEN work_hour_from AND work_hour_till
                ) OR (
                    -- Shift crosses midnight
                    work_hour_from > work_hour_till
                    AND (
                        '$workHourFormatted' BETWEEN work_hour_from AND '23:59:59'
                        OR
                        '$workHourFormatted' BETWEEN '00:00:00' AND work_hour_till
                    )
                ))
                ORDER BY work_hour_till ASC
                LIMIT 1;
            ")[0];

            $this->work_shift = $workingShift->id;
        }
    }

    public function updatedEmployeeno($employeeno)
    {
        $this->employeeno = $employeeno;

        if (isset($this->employeeno) && $this->employeeno != '' && strlen($this->employeeno) >= 3) {
            $msemployee = MsEmployee::where('employeeno', 'ilike', '%' . $this->employeeno . '%')->first();

            if ($msemployee == null) {
                $this->dispatch('notification', ['type' => 'warning', 'message' => 'Employee ' . $this->employeeno . ' Tidak Terdaftar']);
                $this->employeeno = '';
                $this->empname = '';
            } else {
                $this->employeeno = $msemployee->employeeno;
                $this->empname = $msemployee->empname;
            }
        }
    }

    public function updatedEmployeenoinfure($employeenoinfure)
    {
        $this->employeenoinfure = $employeenoinfure;

        if (isset($this->employeenoinfure) && $this->employeenoinfure != '' && strlen($this->employeenoinfure) >= 3) {
            $msemployeeinfure = MsEmployee::where('employeeno', 'ilike', '%' . $this->employeenoinfure . '%')->first();

            if ($msemployeeinfure == null) {
                $this->dispatch('notification', ['type' => 'warning', 'message' => 'Employee ' . $this->employeenoinfure . ' Tidak Terdaftar']);
                $this->employeenoinfure = '';
                $this->empnameinfure = '';
            } else {
                $this->employeenoinfure = $msemployeeinfure->employeeno;
                $this->empnameinfure = $msemployeeinfure->empname;
            }
        } else {
            $this->employeenoinfure = null;
            $this->empnameinfure = null;
        }
    }

    public function updatedGentanNo($gentan_no)
    {
        $this->gentan_no = $gentan_no;

        if (isset($this->gentan_no) && $this->gentan_no != '') {
            $lpkid = TdOrderLpk::where('lpk_no', $this->lpk_no)->first();
            $tdProduct = DB::table('tdproduct_assembly as tdpa')
                ->leftJoin('msmachine as mm', 'mm.id', '=', 'tdpa.machine_id')
                ->leftJoin('msemployee as mse', 'mse.id', '=', 'tdpa.employee_id')
                ->select(
                    'mm.machineno',
                    'mse.empname',
                    'tdpa.berat_produksi'
                )
                ->where('lpk_id', $lpkid->id)
                ->where('gentan_no', $this->gentan_no)
                ->first();

            if ($tdProduct == null) {
                $this->dispatch('notification', ['type' => 'warning', 'message' => 'Nomor Gentan ' . $this->gentan_no . ' Tidak Terdaftar']);
                $this->resetGentan();
            } else {
                $this->petugas = $tdProduct->empname;
                $this->machine_no = $tdProduct->machineno;
                $this->berat_produksi = $tdProduct->berat_produksi;
            }
        }
    }

    public function updatedLossSeitaiId($loss_seitai_id)
    {
        $this->loss_seitai_id = $loss_seitai_id;

        if (isset($this->loss_seitai_id) && $this->loss_seitai_id != '') {
            $lossSeitai = MsLossSeitai::where('code', $this->loss_seitai_id)->first();

            if ($lossSeitai == null) {
                $this->dispatch('notification', ['type' => 'warning', 'message' => 'Kode Loss ' . $this->loss_seitai_id . ' Tidak Terdaftar']);
                $this->resetSeitai();
            } else {
                $this->namaloss = $lossSeitai->name;
            }
        }
    }

    public function updatedCodeLoss($code_loss)
    {
        $this->code_loss = $code_loss;

        if (isset($this->code_loss) && $this->code_loss != '') {
            $lossSeitai = MsLossSeitai::where('code', $this->code_loss)->first();

            if ($lossSeitai == null) {
                $this->dispatch('notification', ['type' => 'warning', 'message' => 'Kode Loss ' . $this->code_loss . ' Tidak Terdaftar']);
                $this->clearLoss();
            } else {
                $this->namaloss = $lossSeitai->name;
                $this->loss_seitai_id = $lossSeitai->id;
            }
        }
    }

    public function updatedStartBox($start_box)
    {
        $this->start_box = $start_box;

        if (isset($this->start_box) && isset($this->end_box)) {
            if ($this->start_box > $this->end_box) {
                $this->dispatch('notification', ['type' => 'warning', 'message' => 'Start Box tidak boleh lebih besar dari End Box']);
                $this->start_box = null;
            }

            $this->jumlah_box = ($this->end_box - $this->start_box) + 1;
        }
    }

    public function updatedEndBox($end_box)
    {
        $this->end_box = $end_box;

        if (isset($this->start_box) && isset($this->end_box)) {
            if ($this->start_box > $this->end_box) {
                $this->dispatch('notification', ['type' => 'warning', 'message' => 'End Box tidak boleh lebih kecil dari Start Box']);
                $this->end_box = null;
            }

            $this->jumlah_box = ($this->end_box - $this->start_box) + 1;
        }
    }

    public function updatedInfureBeratLoss($infure_berat_loss)
    {
        $this->infure_berat_loss = $infure_berat_loss;

        if (!isset($this->infure_berat_loss) || $this->infure_berat_loss == 0 || $this->infure_berat_loss == '') {
            $this->infure_berat_loss = 0;
            $this->employeenoinfure = null;
            $this->empnameinfure = null;
        }
    }

    public function render()
    {
        if (isset($this->lpk_no) && $this->lpk_no != '') {
            $tdorderlpk = DB::table('tdorderlpk as tolp')
                ->select(
                    'tolp.lpk_date',
                    'tolp.panjang_lpk',
                    'tolp.created_on',
                    'tolp.qty_lpk',
                    'tolp.seq_no',
                    'mp.code',
                    'mp.name',
                    'mp.ketebalan',
                    'mp.diameterlipat',
                    'mp.case_box_count',
                    'tolp.qty_gulung',
                    'tolp.qty_gentan',
                    'tolp.total_assembly_qty'
                )
                ->join('msproduct as mp', 'mp.id', '=', 'tolp.product_id')
                ->where('tolp.lpk_no', $this->lpk_no)
                ->first();

            if ($tdorderlpk == null) {
                // session()->flash('error', 'Nomor PO ' . $this->po_no . ' Tidak Terdaftar');
                $this->dispatch('notification', ['type' => 'error', 'message' => 'Nomor LPK ' . $this->lpk_no . ' Tidak Terdaftar']);
            } else {
                $this->lpk_date = Carbon::parse($tdorderlpk->lpk_date)->format('Y-m-d');
                $this->panjang_lpk = $tdorderlpk->panjang_lpk;
                // $this->created_on = Carbon::parse($tdorderlpk->created_on)->format('Y-m-d') . ' - Nomor: ' . $tdorderlpk->seq_no;
                $this->code = $tdorderlpk->code;
                $this->name = $tdorderlpk->name;
                $this->dimensiinfure = $tdorderlpk->ketebalan . 'x' . $tdorderlpk->diameterlipat;
                $this->qty_gulung = $tdorderlpk->qty_gulung;
                $this->qty_gentan = $tdorderlpk->qty_gentan;
                $this->qty_lpk = $tdorderlpk->qty_lpk;
                $this->total_assembly_qty = number_format($tdorderlpk->total_assembly_qty - $this->qty_produksi_old);
                $this->selisihOld = number_format($tdorderlpk->qty_lpk - $tdorderlpk->total_assembly_qty + $this->qty_produksi_old);

                $this->detailsGentan = DB::table('tdproduct_assembly as tdpa')
                    ->join('tdproduct_goods_assembly as tga', 'tga.product_assembly_id', '=', 'tdpa.id')
                    ->leftJoin('msmachine as mm', 'mm.id', '=', 'tdpa.machine_id')
                    ->leftJoin('msemployee as mse', 'mse.id', '=', 'tdpa.employee_id')
                    ->select(
                        'tga.id',
                        'tdpa.gentan_no',
                        'tga.gentan_line',
                        // 'tga.frekuensi',
                        'tga.berat',
                        'mm.machineno',
                        'tdpa.work_shift',
                        'mse.empname',
                        'tdpa.production_date',
                        'tdpa.berat_produksi'
                    )
                    ->where('tga.product_goods_id', $this->tdpgId)
                    ->get();
                $this->jumlahBeratProduksi = $this->detailsGentan->sum('berat_produksi');

                $this->detailsLoss = DB::table('tdproduct_goods_loss as tgl')
                    ->join('mslossseitai as mss', 'mss.id', '=', 'tgl.loss_seitai_id')
                    ->select(
                        'tgl.id',
                        'mss.code',
                        'mss.name',
                        'tgl.frekuensi',
                        'tgl.berat_loss'
                    )
                    ->where('tgl.product_goods_id', $this->tdpgId)
                    ->get();
                $this->jumlahBeratLoss = $this->detailsLoss->sum('berat_loss');
            }
        }

        if (isset($this->qty_produksi) && $this->qty_produksi != '' && isset($this->qty_lpk) && $this->qty_lpk != '') {
            $qty = (int) str_replace(',', '', $this->qty_produksi);
            $this->total_assembly_qty = number_format((int)str_replace(',', '', $this->total_assembly_qty) + $qty);
            $this->selisih = (int)str_replace(',', '', $this->selisihOld) - $qty;

            $this->jumlah_box_product = $this->caseBoxCount > 0 ? (int) ceil($qty / $this->caseBoxCount) : 0;
        }

        return view('livewire.nippo-seitai.edit-seitai')->extends('layouts.master');
    }

    public function print()
    {
        $response = CheckListSeitaiController::dataProduksi($this->orderId);
        if ($response['status'] == 'success') {
            return response()->download($response['filename'])->deleteFileAfterSend(true);
        } else if ($response['status'] == 'error') {
            $this->dispatch('notification', ['type' => 'warning', 'message' => $response['message']]);
            return;
        }
    }
}
