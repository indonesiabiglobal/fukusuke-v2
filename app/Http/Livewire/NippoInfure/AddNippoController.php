<?php

namespace App\Http\Livewire\NippoInfure;

use Livewire\Component;
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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AddNippoController extends Component
{
    public $production_date;
    public $created_on;
    public $lpk_no;
    public $lpk_date;
    public $panjang_lpk;
    public $dimensiinfure;
    public $code;
    public $name;
    public $machineno;
    public $machinename;
    public $empname;
    public $employeeno;
    public $qty_gulung;
    public $qty_gentan;
    public $berat_produksi;
    public $work_hour;
    public $work_shift;
    public $gentan_no = 0;
    public $nomor_han;
    public $name_infure;
    public $loss_infure_id;
    public $berat_loss;
    public $berat;
    public $frekuensi;
    public $details = [];
    public $orderid;
    public $nomor_barcode;
    public $panjang_produksi;
    public $berat_standard;
    public $total_assembly_line;
    public $selisih;
    public $rasio;
    public $ketebalan;
    public $diameterlipat;
    public $berat_jenis;

    // data master produk
    public $masterKatanuki;
    public $product;
    public $photoKatanuki;
    public $katanuki_id;
    public $currentId = 1;

    // data LPK
    public $orderLPK;

    public function mount(Request $request)
    {
        if (!empty($request->query('lpk_no'))) {
            $this->lpk_no = $request->query('lpk_no');
        }
        $this->production_date = Carbon::now()->format('d/m/Y');
        $this->created_on = Carbon::now()->format('d/m/Y');
        $this->work_hour = Carbon::now()->format('H:i');

        $workingShift = DB::select("
            SELECT *
            FROM msworkingshift
            WHERE (
                -- Shift does not cross midnight
                work_hour_from <= work_hour_till
                AND '$this->work_hour' BETWEEN work_hour_from AND work_hour_till
            ) OR (
                -- Shift crosses midnight
                work_hour_from > work_hour_till
                AND (
                    '$this->work_hour' BETWEEN work_hour_from AND '23:59:59'
                    OR
                    '$this->work_hour' BETWEEN '00:00:00' AND work_hour_till
                )
            )
            ORDER BY work_hour_till ASC
            LIMIT 1;
        ")[0];

        $this->work_shift = $workingShift->id;
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
        $validatedData = $this->validate([
            'production_date' => 'required',
            'created_on' => 'required',
            'lpk_no' => 'required',
            'machineno' => 'required',
            'employeeno' => 'required',
            'panjang_produksi' => 'required',
            'berat_produksi' => 'required'
        ]);

        DB::beginTransaction();
        try {
            $lastSeq = TdProductAssembly::whereDate('created_on', Carbon::today())
                ->orderBy('seq_no', 'desc')
                ->first();
            $lpkid = TdOrderLpk::where('lpk_no', $this->lpk_no)->first();
            $machine = MsMachine::where('machineno', $this->machineno)->first();
            $employe = MsEmployee::where('employeeno', $this->employeeno)->first();
            $products = MsProduct::select('msproduct.id', 'mpt.harga_sat_infure')
                ->join('msproduct_type as mpt', 'msproduct.product_type_id', '=', 'mpt.id')
                ->where('msproduct.code', $this->code)
                ->first();

            $maxGentan = TdProductAssembly::where('lpk_id', $lpkid->id)
                ->orderBy('gentan_no', 'DESC')
                ->first();

            $seqno = 1;
            if (!empty($lastSeq)) {
                $seqno = $lastSeq->seq_no + 1;
            }
            $today = Carbon::now();

            $product = new TdProductAssembly();
            $product->production_no = $today->format('dmy') . '-' . $seqno;
            $product->production_date = Carbon::parse($this->production_date . ' ' . $this->work_hour)->format('d/m/y H:i:s');
            $product->created_on = $this->created_on;
            $product->machine_id = $machine->id;
            $product->employee_id = $employe->id;
            $product->work_shift = $this->work_shift;
            $product->work_hour = $this->work_hour;
            $product->lpk_id = $lpkid->id;
            $product->seq_no = $seqno;
            if ($this->gentan_no == 0) {
                if ($maxGentan == null) {
                    $this->gentan_no = 1;
                } else {
                    $this->gentan_no = $maxGentan->gentan_no + 1;
                }
            }
            $product->gentan_no = $this->gentan_no;
            $product->nomor_han = $this->nomor_han;
            $product->product_id = $products->id;
            $product->panjang_produksi = $this->panjang_produksi;
            $product->berat_produksi = $this->berat_produksi;
            $product->berat_standard = $this->berat_standard;
            $product->infure_cost = $this->berat_produksi * $products->harga_sat_infure;
            $product->save();

            $totalBerat = 0;
            foreach ($this->details as $item) {
                $details = new TdProductAssemblyLoss();
                $details->loss_infure_id = $item['loss_infure_id'];
                $details->berat_loss = $item['berat_loss'];
                $details->berat = $item['berat'];
                $details->frekuensi = $item['frekuensi'];
                $details->product_assembly_id = $product->id;

                $totalBerat += $item['berat_loss'];
                $details->save();
            }

            TdProductAssembly::where('id', $product->id)->update([
                'infure_berat_loss' => $totalBerat,
            ]);

            TdOrderLpk::where('id', $lpkid->id)->update([
                'status_lpk' => 1,
            ]);

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

            TdOrderLpk::where('id', $lpkid->id)->update([
                'total_assembly_line' => $totalAssembly[0]->c1,
            ]);



            // $product->panjang_printing_inline = $this->panjang_printing_inline;
            // $product->berat_produksi = $this->berat_produksi;
            // $product->status_production = $this->status_production;
            // $product->status_kenpin = $this->status_kenpin;
            // $product->infure_cost = $this->infure_cost;
            // $product->product_id = $this->product_id;

            DB::commit();
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Order saved successfully.']);
            return redirect()->route('nippo-infure');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to save order: ' . $e->getMessage());
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
            $this->name_infure = '';
            $this->berat_loss = 0;
            $this->berat = 0;
            $this->frekuensi = 0;

            $this->dispatch('showModal');
        }
    }

    public function saveInfure()
    {
        $lpkid = TdOrderLpk::where('lpk_no', $this->lpk_no)->first();

        try {
            DB::beginTransaction();

            $data = [
                'id' => $this->nextId(),
                'loss_infure_id' => $this->loss_infure_id,
                'berat_loss' => $this->berat_loss,
                'berat' => $this->berat,
                'frekuensi' => $this->frekuensi,
                'name_infure' => $this->name_infure,
            ];

            $this->details[] = $data;

            // $datas = new TdProductAssemblyLoss();
            // $datas->loss_infure_id = $this->loss_infure_id;
            // $datas->berat_loss = $this->berat_loss;
            // $datas->berat = $this->berat;
            // $datas->frekuensi = $this->frekuensi;
            // $datas->lpk_id = $lpkid->id;

            // $datas->save();
            DB::commit();
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Data Berhasil di Simpan']);
            $this->dispatch('closeModal');
        } catch (\Exception $e) {
            DB::rollBack();
            log::error('Failed to save infure: ' . $e->getMessage());
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Failed to save the infure: ' . $e->getMessage()]);
        }
    }

    public function nextId()
    {
        return $this->currentId++;
    }

    public function deleteInfure($orderId)
    {
        // $data = TdProductAssemblyLoss::findOrFail($orderId);
        // $data->delete();
        // dd($orderId);
        $index = array_search($orderId, array_column($this->details, 'id'));

        if ($index !== false) {
            array_splice($this->details, $index, 1);
        }

        $this->dispatch('notification', ['type' => 'success', 'message' => 'Data Berhasil di Hapus']);
    }

    public function cancel()
    {
        return redirect()->route('nippo-infure');
    }

    public function resetLpkNo()
    {
        $this->lpk_date = Carbon::now()->format('d/m/Y');
        $this->panjang_lpk = '';
        $this->created_on = Carbon::now()->format('d/m/Y');
        $this->code = '';
        $this->name = '';
        $this->dimensiinfure = '';
        $this->qty_gulung = '';
        $this->lpk_no = '';
        $this->qty_gentan = '';
        $this->berat_standard = '';
        $this->total_assembly_line = '';
        $this->selisih = '';
    }

    public function render()
    {
        if (strlen($this->lpk_no) >= 9) {
            $prefix = substr($this->lpk_no, 0, 6);
            $suffix = substr($this->lpk_no, -3);

            $tdorderlpk = DB::table('tdorderlpk as tolp')
                ->select(
                    'tolp.lpk_no',
                    'tolp.id',
                    'tolp.lpk_date',
                    'tolp.panjang_lpk',
                    'tolp.created_on',
                    'mp.code',
                    'mp.name',
                    'mp.ketebalan',
                    'mp.diameterlipat',
                    'tolp.qty_gulung',
                    'tolp.qty_gentan',
                    'tda.gentan_no',
                    'tolp.total_assembly_line',
                    'mt.berat_jenis',
                    // DB::raw("( mp.ketebalan * mp.diameterlipat * tolp.qty_gulung * 2 * mt.berat_jenis ) / 1000 AS berat_standard "),
                )
                ->join('msproduct as mp', 'mp.id', '=', 'tolp.product_id')
                ->leftJoin('tdproduct_assembly as tda', 'tda.lpk_id', '=', 'tolp.id')
                ->leftJoin('msproduct_type as mt', 'mt.id', '=', 'mp.product_type_id')
                // ->where('tolp.lpk_no', $this->lpk_no)
                ->whereRaw("LEFT(lpk_no, 6) ILIKE ?", ["{$prefix}"])
                ->whereRaw("RIGHT(lpk_no, 3) ILIKE ?", ["{$suffix}"])
                ->first();

            if ($tdorderlpk == null) {
                $this->dispatch('notification', ['type' => 'warning', 'message' => 'Nomor LPK ' . $this->lpk_no . ' Tidak Terdaftar']);
                $this->resetLpkNo();
            } else {
                $this->lpk_date = Carbon::parse($tdorderlpk->lpk_date)->format('d/m/Y');
                $this->panjang_lpk = number_format($tdorderlpk->panjang_lpk, 0, ',', ',');;
                $this->created_on = Carbon::parse($tdorderlpk->created_on)->format('d/m/Y');
                $this->code = $tdorderlpk->code;
                $this->name = $tdorderlpk->name;
                $this->ketebalan = $tdorderlpk->ketebalan;
                $this->diameterlipat = $tdorderlpk->diameterlipat;
                $this->berat_jenis = $tdorderlpk->berat_jenis;
                $this->dimensiinfure = $tdorderlpk->ketebalan . 'x' . $tdorderlpk->diameterlipat;
                $this->qty_gulung = $tdorderlpk->qty_gulung;
                $this->lpk_no = $tdorderlpk->lpk_no;
                $this->qty_gentan = $tdorderlpk->qty_gentan;
                // $this->berat_standard = round($tdorderlpk->berat_standard, 2);
                $this->total_assembly_line = $tdorderlpk->total_assembly_line;
                // $selisih = $tdorderlpk->panjang_lpk - $tdorderlpk->total_assembly_line;
                // $this->selisih = round($selisih, 2);

                // $this->details = DB::table('tdproduct_assembly_loss as tal')
                //     ->select(
                //         'tal.loss_infure_id',
                //         'tal.berat_loss',
                //         'tal.id',
                //         'tal.berat',
                //         'tal.frekuensi',
                //         'msi.name as name_infure'
                //     )
                //     ->join('mslossinfure as msi', 'msi.id', '=', 'tal.loss_infure_id')
                //     ->where('tal.lpk_id', $tdorderlpk->id)
                //     ->get();
            }
        }

        if (isset($this->panjang_produksi) && $this->panjang_produksi != '') {
            $total_assembly_line = (int)$this->total_assembly_line - (int)$this->panjang_produksi;
            $this->total_assembly_line = $total_assembly_line;

            $this->berat_standard = ($this->ketebalan * $this->diameterlipat * (int)str_replace(',', '', $this->panjang_produksi) * 2 * $this->berat_jenis) / 1000;
        }

        if (isset($this->berat_produksi) && $this->berat_produksi != '') {
            $selisih = (float)$this->berat_produksi - (float)$this->panjang_lpk;
            $this->selisih = $selisih;
        }

        if (isset($this->machineno) && $this->machineno != '') {
            $machine = MsMachine::where('machineno', 'ilike', '%' . $this->machineno . '%')->whereIn('department_id', [10, 12, 15, 2, 4, 10])->first();

            if ($machine == null) {
                $this->dispatch('notification', ['type' => 'warning', 'message' => 'Machine ' . $this->machineno . ' Tidak Terdaftar']);
                $this->machineno = '';
                $this->machinename = '';
            } else {
                $this->machineno = $machine->machineno;
                $this->machinename = $machine->machinename;
            }
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

        if (isset($this->loss_infure_id) && $this->loss_infure_id != '') {
            $lossinfure = MsLossInfure::where('code', $this->loss_infure_id)->first();

            if ($lossinfure == null) {
                $this->dispatch('notification', ['type' => 'warning', 'message' => 'Employee ' . $this->loss_infure_id . ' Tidak Terdaftar']);
            } else {
                $this->name_infure = $lossinfure->name;
            }
        }

        if (isset($this->nomor_barcode) && $this->nomor_barcode != '') {
            if ($this->code != $this->nomor_barcode) {
                $this->dispatch('notification', ['type' => 'warning', 'message' => 'Nomor Barcode ' . $this->nomor_barcode . ' Tidak Terdaftar']);
            }
        }

        if (isset($this->berat_produksi) && isset($this->berat_standard)) {
            $this->rasio = round(((int)str_replace(',', '', $this->berat_produksi) / $this->berat_standard) * 100, 2);
        }

        // dd($this->details);

        return view('livewire.nippo-infure.add-nippo')->extends('layouts.master');
    }
}
