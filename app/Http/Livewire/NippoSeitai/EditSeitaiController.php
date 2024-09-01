<?php

namespace App\Http\Livewire\NippoSeitai;

use Livewire\Component;
use App\Models\TdOrder;
use App\Models\MsBuyer;
use App\Models\MsEmployee;
use App\Models\MsLossSeitai;
use App\Models\MsMachine;
use App\Models\MsProduct;
use App\Models\MsWorkingShift;
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
    public $loss_seitai_id;
    public $berat_loss;
    public $berat;
    public $frekuensi;
    public $berat_fr;
    public $frekuensi_fr;
    public $jumlahBeratProduksi;
    public $jumlahBeratLoss;

    // data master produk
    public $masterKatanuki;
    public $product;
    public $photoKatanuki;
    public $katanuki_id;
    public $total_assembly_qty;
    public $selisih;

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
        $this->production_date = Carbon::parse($data->production_date)->format('Y-m-d');
        $this->created_on = Carbon::parse($data->created_on)->format('Y-m-d H:i:s');
        $this->lpk_no = $data->lpk_no;
        $this->lpk_date = $data->lpk_date;
        $this->qty_lpk = $data->qty_lpk;
        $this->code = $data->code;
        $this->name = $data->name;
        $this->machineno = $data->machineno;
        $this->machinename = $data->machinename;
        $this->employeeno = $data->employeeno;
        $this->empname = $data->empname;
        $this->qty_produksi = $data->qty_produksi;
        $this->nomor_palet = $data->nomor_palet;
        $this->nomor_lot = $data->nomor_lot;
        $this->infure_berat_loss = $data->infure_berat_loss;
        $this->employeenoinfure = $data->employeenoinfure;
        $this->empnameinfure = $data->empnameinfure;
        $this->work_hour = $data->work_hour;
        $this->work_shift = $data->work_shift;

        $this->detailsGentan = DB::table('tdproduct_assembly as tdpa')
            ->join('tdproduct_goods_assembly as tga', 'tga.product_assembly_id', '=', 'tdpa.id')
            ->leftJoin('msmachine as mm', 'mm.id', '=', 'tdpa.machine_id')
            ->leftJoin('msemployee as mse', 'mse.id', '=', 'tdpa.employee_id')
            ->select(
                'tga.id',
                'tdpa.gentan_no',
                'tga.gentan_line',
                'tga.frekuensi',
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
            'lpk_no' => 'required',
            'nomor_palet' => 'required',
            'nomor_lot' => 'required',
        ]);

        if ($validatedData) {
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
            $this->dispatch('showModalLoss');
        }
    }

    public function deleteGentan($orderId)
    {
        $data = TdProductGoodsAssembly::findOrFail($orderId);
        $data->delete();

        $this->dispatch('notification', ['type' => 'success', 'message' => 'Data Berhasil di Hapus']);
    }

    public function deleteLoss($orderId)
    {
        $data = TdProductGoodsLoss::findOrFail($orderId);
        $data->delete();

        // mengurangi dari tdproduct_goods
        $tdproductgoods = TdProductGoods::where('id', $this->tdpgId)->update([
            'seitai_berat_loss' => $this->jumlahBeratLoss - $data->berat_loss
        ]);

        $this->dispatch('notification', ['type' => 'success', 'message' => 'Data Berhasil di Hapus']);
    }

    public function saveGentan()
    {
        $lpkid = TdOrderLpk::where('lpk_no', $this->lpk_no)->first();
        $assembly = TdProductAssembly::where('lpk_id', $lpkid->id)
            ->first();

        $datas = new TdProductGoodsAssembly();
        $datas->product_goods_id = $this->tdpgId;
        $datas->product_assembly_id = $assembly->id;
        $datas->gentan_line = $this->gentan_line;
        // $datas->berat = $this->berat;
        // $datas->frekuensi = $this->frekuensi;
        $datas->lpk_id = $lpkid->id;

        $datas->save();

        $this->dispatch('closeModalGentan');
        $this->dispatch('notification', ['type' => 'success', 'message' => 'Data Berhasil di Simpan']);
    }

    public function clearLoss()
    {
        $this->loss_seitai_id = '';
        $this->berat_loss = '';
        $this->berat_fr = '';
        $this->frekuensi_fr = '';
    }

    public function saveLoss()
    {
        $lpkid = TdOrderLpk::where('lpk_no', $this->lpk_no)->first();
        $loss = MsLossSeitai::where('code', $this->loss_seitai_id)
            ->first();

        $datas = new TdProductGoodsLoss();
        $datas->product_goods_id = $this->tdpgId;
        $datas->loss_seitai_id = $loss->id;
        $datas->berat_loss = $this->berat_loss;
        $datas->berat = $this->berat_fr;
        $datas->frekuensi = $this->frekuensi_fr;
        $datas->lpk_id = $lpkid->id;

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
            'nomor_lot' => 'required'
        ]);

        try {
            $machine = MsMachine::where('machineno', $this->machineno)->first();
            $employe = MsEmployee::where('employeeno', $this->employeeno)->first();
            $employeinfure = MsEmployee::where('employeeno', $this->employeenoinfure)->first();

            $data = TdProductGoods::findOrFail($this->orderId);
            $data->production_date = Carbon::parse($this->production_date . ' ' . $this->work_hour)->format('Y-m-d H:i:s');
            $data->machine_id = $machine->id;
            $data->employee_id = $employe->id;
            if (isset($this->employeenoinfure)) {
                $data->employee_id_infure = $employeinfure->id;
            }
            $data->qty_produksi = $this->qty_produksi;
            $data->nomor_palet = $this->nomor_palet;
            $data->nomor_lot = $this->nomor_lot;
            $data->infure_berat_loss = $this->infure_berat_loss;
            $data->work_shift = $this->work_shift;
            $data->work_hour = $this->work_hour;

            $data->save();

            DB::commit();
            session()->flash('notification', ['type' => 'success', 'message' => 'Order saved successfully.']);
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

    public function render()
    {
        if (isset($this->lpk_no) && $this->lpk_no != '') {
            $tdorderlpk = DB::table('tdorderlpk as tolp')
                ->select(
                    'tolp.lpk_date',
                    'tolp.panjang_lpk',
                    'tolp.created_on',
                    'tolp.qty_lpk',
                    'mp.code',
                    'mp.name',
                    'mp.ketebalan',
                    'mp.diameterlipat',
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
                $this->created_on = Carbon::parse($tdorderlpk->created_on)->format('Y-m-d H:i:s');
                $this->code = $tdorderlpk->code;
                $this->name = $tdorderlpk->name;
                $this->dimensiinfure = $tdorderlpk->ketebalan . 'x' . $tdorderlpk->diameterlipat;
                $this->qty_gulung = $tdorderlpk->qty_gulung;
                $this->qty_gentan = $tdorderlpk->qty_gentan;
                $this->qty_lpk = $tdorderlpk->qty_lpk;
                $this->total_assembly_qty = $tdorderlpk->total_assembly_qty;
                $this->selisih = $tdorderlpk->panjang_lpk - $tdorderlpk->total_assembly_qty;

                $this->detailsGentan = DB::table('tdproduct_assembly as tdpa')
                    ->join('tdproduct_goods_assembly as tga', 'tga.product_assembly_id', '=', 'tdpa.id')
                    ->leftJoin('msmachine as mm', 'mm.id', '=', 'tdpa.machine_id')
                    ->leftJoin('msemployee as mse', 'mse.id', '=', 'tdpa.employee_id')
                    ->select(
                        'tga.id',
                        'tdpa.gentan_no',
                        'tga.gentan_line',
                        'tga.frekuensi',
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

        if (isset($this->machineno) && $this->machineno != '') {
            $machine = MsMachine::where('machineno', 'ilike', '%' . $this->machineno . '%')->first();
            if ($machine == null) {
                $this->dispatch('notification', ['type' => 'error', 'message' => 'Machine ' . $this->machineno . ' Tidak Terdaftar']);
            } else {
                $this->machineno = $machine->machineno;
                $this->machinename = $machine->machinename;
            }
        }

        if (isset($this->employeeno) && $this->employeeno != '' && strlen($this->employeeno) >= 3) {
            $msemployee = MsEmployee::where('employeeno', 'ilike', '%' . $this->employeeno . '%')->first();

            if ($msemployee == null) {
                $this->dispatch('notification', ['type' => 'warning', 'message' => 'Employee ' . $this->employeeno . ' Tidak Terdaftar']);
            } else {
                $this->employeeno = $msemployee->employeeno;
                $this->empname = $msemployee->empname;
            }
        }

        if (isset($this->employeenoinfure) && $this->employeenoinfure != '' && strlen($this->employeenoinfure) >= 3) {
            $msemployeeinfure = MsEmployee::where('employeeno', 'ilike', '%' . $this->employeenoinfure . '%')->first();

            if ($msemployeeinfure == null) {
                // session()->flash('error', 'Nomor PO ' . $this->po_no . ' Tidak Terdaftar');
                $this->dispatch('notification', ['type' => 'error', 'message' => 'Employee ' . $this->employeenoinfure . ' Tidak Terdaftar']);
            } else {
                $this->empnameinfure = $msemployeeinfure->empname;
            }
        }

        if (isset($this->gentan_no) && $this->gentan_no != '') {
            $lpkid = TdOrderLpk::where('lpk_no', $this->lpk_no)->first();
            // $tdProduct=TdProductAssembly::where('gentan_no', $this->gentan_no)->where('lpk_id', $lpkid->id)->first();
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
                $this->dispatch('notification', ['type' => 'warning', 'message' => 'Nomor Gentan ' . $this->employeenoinfure . ' Tidak Terdaftar']);
            } else {
                $this->petugas = $tdProduct->empname;
                $this->machine_no = $tdProduct->machineno;
                $this->berat_produksi = $tdProduct->berat_produksi;
            }
        }

        if (isset($this->loss_seitai_id) && $this->loss_seitai_id != '') {
            $lossSeitai = MsLossSeitai::where('code', $this->loss_seitai_id)->first();

            if ($lossSeitai == null) {
                $this->dispatch('notification', ['type' => 'warning', 'message' => 'Kode Loss ' . $this->employeenoinfure . ' Tidak Terdaftar']);
            } else {
                $this->namaloss = $lossSeitai->name;
            }
        }

        // $lpkid = TdOrderLpk::where('lpk_no', $this->lpk_no)->first();

        // $this->gentan_no = 1;
        // if (!empty($lpkid)) {
        //     $lastGentan = TdProductAssembly::where('lpk_id', $lpkid->lpk_id)
        //         ->max('gentan_no');

        //     $nogentan = 1;
        //     if(!empty($lastGentan)){
        //         $nogentan = $lastGentan->seq_no + 1;
        //     }
        //     $this->gentan_no=$nogentan;
        // }

        return view('livewire.nippo-seitai.edit-seitai')->extends('layouts.master');
    }
}
