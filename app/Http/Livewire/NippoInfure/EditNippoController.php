<?php

namespace App\Http\Livewire\NippoInfure;

use Livewire\Component;
use App\Models\TdOrder;
use App\Models\MsBuyer;
use App\Models\MsEmployee;
use App\Models\MsLossInfure;
use App\Models\MsMachine;
use App\Models\MsProduct;
use App\Models\MsWorkingShift;
use App\Models\TdOrderLpk;
use App\Models\TdProductAssembly;
use App\Models\TdProductAssemblyLoss;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class EditNippoController extends Component
{
    public $orderId;
    public $production_no;
    public $production_date;
    public $created_on;
    public $lpk_no;
    public $lpk_date;
    public $panjang_lpk;
    public $dimensiinfure;
    public $code;
    public $codebarcode;
    public $name;
    public $machineno;
    public $machinename;
    public $empname;
    public $employeeno;
    public $qty_gulung;
    public $qty_gentan;
    public $work_hour;
    public $work_shift;
    public $gentan_no;
    public $nomor_han;
    public $nomor_barcode;
    public $details = [];
    public $orderid;
    public $panjang_produksi;
    public $loss_infure_code;
    public $loss_infure_id;
    public $name_infure;
    public $berat_loss;
    public $berat;
    public $frekuensi;
    public $statusSeitai;
    public $berat_standard;
    public $total_assembly_line;
    public $total_assembly_line_old;
    public $rasio;
    public $selisih;
    public $selisih_old;
    public $berat_produksi;
    public $seq_no;
    public $ketebalan;
    public $diameterlipat;
    public $berat_jenis;
    public $editing_id = null;

    // data master produk
    public $masterKatanuki;
    public $product;
    public $photoKatanuki;
    public $katanuki_id;

    // data LPK
    public $orderLPK;
    public $statusEditLoss = false;

    public $tglAwal;
    public $tglKeluar;

    public function mount(Request $request)
    {
        $data = DB::table('tdproduct_assembly AS tda')
            ->join('tdorderlpk AS tdol', 'tda.lpk_id', '=', 'tdol.id')
            ->join('msmachine AS msm', 'msm.id', '=', 'tda.machine_id')
            ->join('msemployee AS mse', 'mse.id', '=', 'tda.employee_id')
            ->join('msproduct AS msp', 'msp.id', '=', 'tda.product_id')
            ->join('tdorder AS tdo', 'tdol.order_id', '=', 'tdo.id')
            ->leftJoin('msproduct_type as mt', 'mt.id', '=', 'msp.product_type_id')
            ->leftJoin('tdproduct_goods as tdpg', 'tdpg.lpk_id', '=', 'tdol.id')
            ->select(
                'tda.id AS id',
                'tda.production_no AS production_no',
                'tda.production_date AS production_date',
                'tda.employee_id AS employee_id',
                'tda.work_shift AS work_shift',
                'tda.work_hour AS work_hour',
                'tda.machine_id AS machine_id',
                'tda.lpk_id AS lpk_id',
                'tda.product_id AS product_id',
                'tda.panjang_produksi AS panjang_produksi',
                'tda.panjang_printing_inline AS panjang_printing_inline',
                'tda.berat_standard AS berat_standard',
                'tda.berat_produksi AS berat_produksi',
                'tda.nomor_han AS nomor_han',
                'tda.gentan_no AS gentan_no',
                'tda.seq_no AS seq_no',
                'tda.status_production AS status_production',
                'tda.status_kenpin AS status_kenpin',
                'tda.infure_cost AS infure_cost',
                'tda.infure_cost_printing AS infure_cost_printing',
                'tda.infure_berat_loss AS infure_berat_loss',
                'tda.kenpin_berat_loss AS kenpin_berat_loss',
                'tda.kenpin_meter_loss AS kenpin_meter_loss',
                'tda.kenpin_meter_loss_proses AS kenpin_meter_loss_proses',
                'tda.created_by AS created_by',
                'tda.created_on AS created_on',
                'tda.updated_by AS updated_by',
                'tda.updated_on AS updated_on',
                'tdol.order_id AS order_id',
                'tdol.lpk_no AS lpk_no',
                'tdol.lpk_date AS lpk_date',
                'tdol.panjang_lpk AS panjang_lpk',
                'tdol.qty_gentan',
                'tdol.qty_gulung',
                'tdol.qty_lpk',
                'tdol.total_assembly_line',
                'tdol.total_assembly_qty',
                'msm.machineno',
                'msm.machinename',
                'tdo.product_code',
                'mse.employeeno',
                'mse.empname',
                'msp.code',
                'msp.name',
                'msp.ketebalan',
                'msp.diameterlipat',
                'msp.codebarcode',
                'mt.berat_jenis',
                // DB::raw("CASE WHEN tdpg.id IS NOT NULL THEN 1 ELSE 0 END as tdpg")
            )
            ->where('tda.id', $request->query('orderId'))
            ->first();

        $this->statusEditLoss = $request->query('status');

        // History
        $this->tglAwal = $request->query('tglAwal');
        $this->tglKeluar = $request->query('tglKeluar');
        // $this->lpk_no = $request->query('lpk_no');
        // $this->tglKeluar = $request->query('tglKeluar');

        $this->orderId = $request->query('orderId');
        $this->production_no = $data->production_no;
        $this->production_date = Carbon::parse($data->production_date)->format('d/m/Y');
        $this->created_on = Carbon::parse($data->created_on)->format('d/m/Y') . ' - Nomor: ' . $data->seq_no;
        $this->lpk_no = $data->lpk_no;
        $this->lpk_date = Carbon::parse($data->lpk_date)->format('d/M/Y');
        $this->panjang_lpk = $data->panjang_lpk;
        $this->machineno = $data->machineno;
        $this->machinename = $data->machinename;
        $this->code = $data->code;
        $this->codebarcode = $data->codebarcode;
        $this->nomor_barcode = $data->codebarcode;
        $this->name = $data->name;
        $this->employeeno = $data->employeeno;
        $this->empname = $data->empname;
        $this->work_hour = $data->work_hour;
        $this->work_shift = $data->work_shift;
        $this->gentan_no = $data->gentan_no;
        $this->nomor_han = $data->nomor_han;
        $this->seq_no = $data->seq_no;
        $this->panjang_produksi = $data->panjang_produksi;
        $this->ketebalan = $data->ketebalan;
        $this->diameterlipat = $data->diameterlipat;
        $this->dimensiinfure = $data->ketebalan . 'x' . $data->diameterlipat;
        $this->qty_gulung = number_format($data->qty_gulung, 0, ',', ',');
        $this->berat_standard = round($data->berat_standard, 2);
        $selisih = $data->total_assembly_line - $data->panjang_lpk - $data->panjang_produksi;
        $this->selisih_old = $selisih;
        $this->selisih = $selisih;

        $totalAssemblyLine = $data->total_assembly_line - $data->panjang_produksi;
        $this->total_assembly_line_old = $totalAssemblyLine;
        $this->total_assembly_line = $totalAssemblyLine;
        $this->qty_gentan = number_format($data->qty_gentan, 0, ',', ',');
        $this->berat_produksi = $data->berat_produksi;
        $this->berat_jenis = $data->berat_jenis;

        $this->details = DB::table('tdproduct_assembly_loss as tal')
            ->select(
                'tal.loss_infure_id',
                'msi.code as loss_infure_code',
                'tal.berat_loss',
                'tal.id',
                'tal.berat',
                'tal.frekuensi',
                'msi.name as name_infure'
            )
            ->join('mslossinfure as msi', 'msi.id', '=', 'tal.loss_infure_id')
            ->where('tal.product_assembly_id', $this->orderId)
            ->get();

        $productGoodsAssembly = DB::table('tdproduct_goods_assembly AS tdpg')
            ->where('tdpg.product_assembly_id', $data->id)
            ->get();

        if ($productGoodsAssembly->count() > 0) {
            $this->statusSeitai = 1;
        } else {
            $this->statusSeitai = 0;
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

    public function save()
    {
        $this->panjang_produksi = (int)str_replace(',', '', $this->panjang_produksi);
        $this->berat_produksi = (float)str_replace(',', '', $this->berat_produksi);

        DB::beginTransaction();
        try {
            $lpkid = TdOrderLpk::where('lpk_no', $this->lpk_no)->first();
            $machine = MsMachine::where('machineno', $this->machineno)->first();
            $employe = MsEmployee::where('employeeno', $this->employeeno)->first();
            $products = MsProduct::where('code', $this->code)->first();
            $totalBerat = TdProductAssemblyLoss::where('product_assembly_id', $this->orderId)
                ->sum('berat_loss');

            $maxGentan = TdProductAssembly::where('lpk_id', $lpkid->id)
                ->orderBy('gentan_no', 'DESC')
                ->first();

            // mengecek apakah nomor barcode sesuai dengan barcode produk
            if (isset($this->nomor_barcode)) {
                if ($products->codebarcode != $this->nomor_barcode) {
                    $this->dispatch('notification', ['type' => 'warning', 'message' => 'Nomor Barcode ' . $this->nomor_barcode . ' Tidak Sesuai']);
                }
            } else {
                $this->dispatch('notification', ['type' => 'success', 'message' => 'Nomor Barcode ' . $this->nomor_barcode . ' Harus diisi']);
            }

            $product = TdProductAssembly::findOrFail($this->orderId);
            $product->production_date = $this->production_date . ' ' . $this->work_hour;
            // $product->created_on = $this->created_on;
            $product->machine_id = $machine->id;
            $product->employee_id = $employe->id;
            $product->work_shift = $this->work_shift;
            $product->work_hour = $this->work_hour;
            $product->lpk_id = $lpkid->id;
            if ($this->gentan_no == 0) {
                $this->gentan_no = $maxGentan->gentan_no + 1;
            }
            $product->gentan_no = $this->gentan_no;
            $product->nomor_han = $this->nomor_han;
            $product->product_id = $products->id;
            $product->panjang_produksi = $this->panjang_produksi;
            $product->berat_produksi = $this->berat_produksi;
            $product->berat_standard = $this->berat_standard;
            $product->infure_cost = $this->berat_produksi * $products->harga_sat_infure;

            $totalAssembly = DB::select("
                SELECT
                    CASE WHEN x.A1 IS NULL THEN 0 ELSE x.A1 END AS C1
                FROM
                    (
                    SELECT SUM(panjang_produksi) AS A1
                    FROM
                        tdproduct_assembly AS ta
                    WHERE
                        lpk_id = $lpkid->id
                ) AS x
            ");
            $product->save();

            TdProductAssembly::where('id', $this->orderId)->update([
                'infure_berat_loss' => $totalBerat,
            ]);

            TdOrderLpk::where('id', $lpkid->id)->update([
                'total_assembly_line' => $totalAssembly[0]->c1,
            ]);

            DB::commit();
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Order saved successfully.']);
            return redirect()->route('nippo-infure');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Failed to save the order: ' . $e->getMessage()]);
        }
    }

    public function addLossInfure()
    {
        $validatedData = $this->validate([
            'lpk_no' => 'required',
            'machineno' => 'required',
            'employeeno' => 'required',
            // 'panjang_produksi' => 'required',
            // 'qty_gentan' => 'required'
        ]);

        if ($validatedData) {
            $this->loss_infure_id = '';
            $this->loss_infure_code = '';
            $this->name_infure = '';
            $this->berat_loss = 0;
            $this->berat = 0;
            $this->frekuensi = 0;
            $this->dispatch('showModal');
        }
    }

    public function saveInfure()
    {
        $datas = new TdProductAssemblyLoss();
        $datas->product_assembly_id = $this->orderId;
        $datas->loss_infure_id = $this->loss_infure_id;
        $datas->berat_loss = $this->berat_loss;
        $datas->berat = $this->berat;
        $datas->frekuensi = $this->frekuensi;

        $datas->save();

        $this->dispatch('closeModal');
    }

    public function deleteInfure($loss_infure_id)
    {
        $data = TdProductAssemblyLoss::where('product_assembly_id', $this->orderId)->where('loss_infure_id', $loss_infure_id)->get();
        $data->each(function ($item) {
            $item->delete();
        });

        $this->dispatch('notification', ['type' => 'success', 'message' => 'Data Berhasil di Hapus']);
    }

    public function cancel()
    {
        return redirect()->route(
            'nippo-infure',
            [
                'tglAwal' => $this->tglAwal,
                'tglKeluar' => $this->tglKeluar
            ]
        );
    }

    public function delete()
    {
        $this->dispatch('showModalDelete');
        $this->skipRender();
    }

    public function destroy()
    {
        DB::beginTransaction();
        try {
            $order = TdProductAssembly::where('id', $this->orderId)->first();
            $order->delete();

            DB::commit();
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Order Deleted successfully.']);
            return redirect()->route('nippo-infure');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Failed to save the order: ' . $e->getMessage()]);
        }
    }

    public function print()
    {
        $this->dispatch('redirectToPrint', $this->lpk_no);
    }

    public function render()
    {
        $this->details = DB::table('tdproduct_assembly_loss as tal')
            ->select(
                'tal.loss_infure_id',
                'msi.code as loss_infure_code',
                'tal.berat_loss',
                'tal.id',
                'tal.berat',
                'tal.frekuensi',
                'msi.name as name_infure'
            )
            ->join('mslossinfure as msi', 'msi.id', '=', 'tal.loss_infure_id')
            ->where('tal.product_assembly_id', $this->orderId)
            ->get();

        if (isset($this->loss_infure_code) && $this->loss_infure_code != '') {
            $lossinfure = MsLossInfure::where('code', $this->loss_infure_code)->first();

            if ($lossinfure == null) {
                $this->dispatch('notification', ['type' => 'warning', 'message' => 'Loss Infure ' . $this->loss_infure_code . ' Tidak Terdaftar']);
            } else {
                $this->name_infure = $lossinfure->name;
                $this->loss_infure_id = $lossinfure->id;
            }
        }

        if (isset($this->machineno) && $this->machineno != '') {
            $machine = MsMachine::where('machineno', 'ilike', '%' . $this->machineno . '%')->whereIn('department_id', [10, 12, 15, 2, 4, 10])->first();

            if ($machine == null) {
                $this->dispatch('notification', ['type' => 'error', 'message' => 'Machine ' . $this->machineno . ' Tidak Terdaftar']);
                $this->machineno = '';
                $this->machinename = '';
            } else {
                $this->machineno = $machine->machineno;
                $this->machinename = $machine->machinename;
            }
        }

        if (isset($this->work_hour) && $this->work_hour != '') {
            if (
                Carbon::createFromFormat('d/m/Y', $this->production_date)->isSameDay(Carbon::now())
                && Carbon::parse($this->work_hour)->format('H:i') > Carbon::now()->format('H:i')
            ) {
                $this->dispatch('notification', ['type' => 'warning', 'message' => 'Jam Kerja Tidak Boleh Melebihi Jam Sekarang']);
                $this->work_hour = Carbon::now()->format('H:i');
            }

            $workHourFormatted = Carbon::parse($this->work_hour)->format('H:i:s');
            $workingShift = DB::select("
            SELECT *
            FROM msworkingshift
            WHERE (
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
            )
            ORDER BY work_hour_till ASC
            LIMIT 1;
        ")[0];

            $this->work_shift = $workingShift->id;
        }

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

        if (isset($this->panjang_produksi) && $this->panjang_produksi != '') {
            $total_assembly_line = (int)$this->total_assembly_line_old + (int)str_replace(',', '', $this->panjang_produksi);
            $this->total_assembly_line = $total_assembly_line;

            $this->berat_standard = ($this->ketebalan * $this->diameterlipat * (int)str_replace(',', '', $this->panjang_produksi) * 2 * $this->berat_jenis) / 1000;

            $this->selisih = (int)$this->selisih_old + (int)str_replace(',', '', $this->panjang_produksi);
        }

        if (isset($this->berat_produksi) && isset($this->berat_standard)) {
            if ($this->berat_standard == 0) {
                $this->rasio = 0;
            } else {
                $this->rasio = round(((float)str_replace(',', '', $this->berat_produksi) / $this->berat_standard) * 100, 2);
            }
        }

        if (isset($this->nomor_barcode) && $this->nomor_barcode != '') {
            if ($this->codebarcode != $this->nomor_barcode) {
                $this->dispatch('notification', ['type' => 'warning', 'message' => 'Nomor Barcode ' . $this->nomor_barcode . ' Tidak Terdaftar']);
            }
        }

        return view('livewire.nippo-infure.edit-nippo')->extends('layouts.master');
    }

    public function editLossInfure($id)
    {
        $infureItem = DB::table('tdproduct_assembly_loss')
            ->where('id', $id)
            ->first();

        if ($infureItem) {
            $this->editing_id = $infureItem->id;
            $this->loss_infure_id = $infureItem->loss_infure_id;
            $this->loss_infure_code = DB::table('mslossinfure')->where('id', $infureItem->loss_infure_id)->value('code');
            $this->name_infure = DB::table('mslossinfure')->where('id', $infureItem->loss_infure_id)->value('name');
            $this->berat_loss = $infureItem->berat_loss;
            $this->frekuensi = $infureItem->frekuensi;

            // Buka modal edit
            $this->dispatch('openModal', 'modal-edit');
        } else {
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Data tidak ditemukan']);
        }
    }

    public function updateLossInfure()
    {
        $this->validate([
            'loss_infure_id' => 'required',
            'berat_loss' => 'required|numeric',
            'frekuensi' => 'required|numeric',
        ]);

        DB::table('tdproduct_assembly_loss')
            ->where('id', $this->editing_id)
            ->update([
                'loss_infure_id' => $this->loss_infure_id,
                'berat_loss' => $this->berat_loss,
                'frekuensi' => $this->frekuensi,
                // 'updated_at' => now(),
            ]);

        $this->resetInputFields();
        $this->dispatch('notification', ['type' => 'success', 'message' => 'Loss Infure berhasil diupdate']);
        $this->dispatch('closeModal', 'modal-edit');
    }

    public function resetForm()
    {
        $this->resetInputFields();
    }

    private function resetInputFields()
    {
        $this->editing_id = null;
        $this->loss_infure_id = null;
        $this->loss_infure_code = '';
        $this->name_infure = '';
        $this->berat_loss = '';
        $this->frekuensi = '';
    }
}
