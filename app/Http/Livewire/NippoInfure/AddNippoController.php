<?php

namespace App\Http\Livewire\NippoInfure;

use App\Helpers\formatAngka;
use Livewire\Component;
use App\Models\MsEmployee;
use App\Models\MsLossInfure;
use App\Models\MsMachine;
use App\Models\MsProduct;
use App\Models\TdOrderLpk;
use App\Models\TdProductAssembly;
use App\Models\TdProductAssemblyLoss;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
    public $gentan_no;
    public $nomor_han;
    public $name_infure;
    public $loss_infure_code;
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
    public $selisihAwal;
    public $rasio;
    public $ketebalan;
    public $diameterlipat;
    public $berat_jenis;
    public $editing_id = null;

    // data master produk
    public $masterKatanuki;
    public $product;
    public $photoKatanuki;
    public $katanuki_id;
    public $currentId = 1;

    // data LPK
    public $orderLPK;
    public $tdorderlpk = null;
    public $lpkProcessed = false;

    public function mount(Request $request)
    {
        if (!empty($request->query('lpk_no'))) {
            $this->lpk_no = $request->query('lpk_no');
            $this->processLpkNo();
        }

        $this->production_date = Carbon::now()->format('Y-m-d');
        $this->created_on = Carbon::now()->format('d/m/Y H:i:s');
        $this->work_hour = Carbon::now()->format('H:i');

        $this->workShift();
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

    public function checkLpkProcessed()
{
    if ($this->lpkProcessed) {
        $this->lpkProcessed = false; // Reset
        $this->dispatch('focus-machine');
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

    public function rules()
    {
        return [
            'production_date' => 'required',
            'created_on' => 'required',
            'lpk_no' => 'required',
            'machineno' => 'required',
            'employeeno' => 'required',
            'panjang_produksi' => 'required|max:25000',
            'berat_produksi' => 'required|max:900',
            'work_hour' => 'required',
            'nomor_barcode' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'production_date.required' => 'Tanggal Produksi harus diisi',
            'created_on.required' => 'Tanggal Proses harus diisi',
            'lpk_no.required' => 'Nomor LPK harus diisi',
            'machineno.required' => 'Nomor Mesin harus diisi',
            'employeeno.required' => 'Nomor Karyawan harus diisi',
            'panjang_produksi.required' => 'Panjang Produksi harus diisi',
            'panjang_produksi.max' => 'Panjang Produksi maksimal 25000',
            'berat_produksi.required' => 'Berat Produksi harus diisi',
            'berat_produksi.max' => 'Berat Produksi maksimal 900',
            'work_hour.required' => 'Jam Kerja harus diisi',
            'nomor_barcode.required' => 'Nomor Barcode harus diisi',
        ];
    }

    public function save()
    {
        $this->panjang_produksi = (int)str_replace(',', '', $this->panjang_produksi);
        $this->berat_produksi = (float)str_replace(',', '', $this->berat_produksi);

        $this->validate();

        DB::beginTransaction();
        try {
            $lastSeq = TdProductAssembly::whereDate('created_on', Carbon::today())
                ->orderBy('seq_no', 'desc')
                ->first();
            $lpkid = TdOrderLpk::where('lpk_no', $this->lpk_no)->first();

            // check duplicate LPK and gentan_no
            $existingAssembly = TdProductAssembly::where('lpk_id', $lpkid->id)
                ->where('gentan_no', $this->gentan_no)
                ->first();

            if ($existingAssembly) {
                $this->dispatch('notification', ['type' => 'warning', 'message' => 'LPK No: ' . $this->lpk_no . ' dengan Gentan No: ' . $this->gentan_no . ' sudah ada.']);
                return;
            }

            $machine = MsMachine::where('machineno', $this->machineno)->first();
            $employe = MsEmployee::where('employeeno', $this->employeeno)->first();
            $msProduct = MsProduct::select(
                'msproduct.id',
                'mpt.harga_sat_infure',
                'msproduct.codebarcode',
                'mpt.product_cetak_id'
            )
                ->join('msproduct_type as mpt', 'msproduct.product_type_id', '=', 'mpt.id')
                ->where('msproduct.code', $this->code)
                ->first();

            if (!empty($this->work_hour)) {
                $workHourFormatted = strlen($this->work_hour) === 5
                    ? $this->work_hour . ':00'
                    : $this->work_hour;
            } else {
                $workHourFormatted = '00:00:00';
            }

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

            $maxGentan = TdProductAssembly::where('lpk_id', $lpkid->id)
                ->orderBy('gentan_no', 'DESC')
                ->first();

            // mengecek apakah nomor barcode sesuai dengan barcode produk
            if (isset($this->nomor_barcode)) {
                if ($msProduct->codebarcode != $this->nomor_barcode) {
                    $this->dispatch('notification', ['type' => 'warning', 'message' => 'Nomor Barcode ' . $this->nomor_barcode . ' Tidak Sesuai']);
                    return;
                }
            } else {
                $this->dispatch('notification', ['type' => 'success', 'message' => 'Nomor Barcode ' . $this->nomor_barcode . ' Harus diisi']);
            }

            $seqno = 1;
            if (!empty($lastSeq)) {
                $seqno = $lastSeq->seq_no + 1;
            }
            $today = Carbon::now();
            $createdOn = Carbon::createFromFormat('d/m/Y H:i:s', $this->created_on);

             $productionDate = Carbon::createFromFormat('Y-m-d', $this->production_date)
            ->setTimeFromTimeString($this->work_hour)
            ->format('Y-m-d H:i:s');

            $product = new TdProductAssembly();
            $product->production_no = $today->format('dmy') . '-' . $seqno;
            $product->production_date = $productionDate;
            $product->machine_id = $machine->id;
            $product->employee_id = $employe->id;
            $product->work_shift = $this->work_shift;
            $product->work_hour = $workHourFormatted;
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
            $product->product_id = $msProduct->id;
            $product->panjang_produksi = $this->panjang_produksi;
            $product->berat_produksi = $this->berat_produksi;
            $product->berat_standard = $this->berat_standard;

            $product->infure_cost = $this->berat_produksi * 20;
            if ($msProduct->product_cetak_id == 1) {
                $product->panjang_printing_inline = $this->panjang_produksi;
                $product->infure_cost_printing = $product->panjang_printing_inline * 0.2;
            }

            $product->created_on = $createdOn;
            $product->created_by = Auth::user()->username;
            $product->updated_on = $createdOn;
            $product->updated_by = Auth::user()->username;
            $product->save();

            $totalBerat = 0;
            foreach ($this->details as $item) {
                $details = new TdProductAssemblyLoss();
                $details->loss_infure_id = $item['loss_infure_id'];
                $details->berat_loss = $item['berat_loss'];
                $details->frekuensi = $item['frekuensi'];
                $details->product_assembly_id = $product->id;

                $details->created_on = $createdOn;
                $details->created_by = Auth::user()->username;
                $details->updated_on = $createdOn;
                $details->updated_by = Auth::user()->username;

                $totalBerat += $item['berat_loss'];
                $details->save();
            }

            TdProductAssembly::where('id', $product->id)->update([
                'infure_berat_loss' => $totalBerat,
            ]);

            TdOrderLpk::where('id', $lpkid->id)->update([
                'status_lpk' => 1,
            ]);

            DB::commit();

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
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Order saved successfully.']);

            $this->dispatch('redirectToPrint', $product->id);

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
        ]);

        if ($validatedData) {
            $this->loss_infure_id = '';
            $this->loss_infure_code = '';
            $this->name_infure = '';
            $this->berat_loss = '';
            $this->frekuensi = '';

            $this->dispatch('showModal');
        }
    }

    public function validateLossInfure()
    {
        try {
            $this->validate([
                'loss_infure_code' => 'required',
                'berat_loss' => 'required|numeric|min:0|max:1000',
                'frekuensi' => 'required|integer|min:0|max:100',
            ], [
                'loss_infure_code.required' => 'Kode Loss Infure harus diisi',
                'berat_loss.required' => 'Berat Loss harus diisi',
                'berat_loss.numeric' => 'Berat Loss harus berupa angka',
                'berat_loss.min' => 'Berat Loss tidak boleh kurang dari 0',
                'berat_loss.max' => 'Berat Loss tidak boleh lebih dari 1000',
                'frekuensi.required' => 'Frekuensi harus diisi',
                'frekuensi.integer' => 'Frekuensi harus berupa angka bulat',
                'frekuensi.min' => 'Frekuensi tidak boleh kurang dari 0',
                'frekuensi.max' => 'Frekuensi tidak boleh lebih dari 100',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Validasi gagal: ' . implode(', ', $e->validator->errors()->all())]);
            return;
        }
        return true;
    }

    public function saveInfure()
    {
        // validated
        if (!$this->validateLossInfure()) {
            return;
        }

        try {
            DB::beginTransaction();

            $data = [
                'id' => $this->nextId(),
                'loss_infure_id' => $this->loss_infure_id,
                'loss_infure_code' => $this->loss_infure_code,
                'berat_loss' => floatval($this->berat_loss),
                'frekuensi' => $this->frekuensi,
                'name_infure' => $this->name_infure,
            ];

            $this->details[] = $data;

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
        $index = array_search($orderId, array_column($this->details, 'id'));

        if ($index !== false) {
            array_splice($this->details, $index, 1);
        }

        $this->dispatch('closeModalDeleteLossInfure', $orderId);
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
        $this->created_on = Carbon::now()->format('d/m/Y H:i:s');
        $this->code = '';
        $this->name = '';
        $this->dimensiinfure = '';
        $this->qty_gulung = '';
        $this->lpk_no = '';
        $this->qty_gentan = '';
        $this->berat_standard = 0;
        $this->total_assembly_line = 0;
        $this->selisih = '';
    }

    public function processLpkNo($lpkNo = null)
    {
        $lpkNo = $lpkNo ?? $this->lpk_no;

        $this->lpk_no = $lpkNo;

        if (!preg_match('/^\d{6}-\d{3}$/', $this->lpk_no)) {
            $this->addError('lpk_no', 'Format LPK No harus 000000-000');
            return;
        }

        // Clear error jika ada
        $this->resetErrorBag('lpk_no');

        try {
            if (strlen($this->lpk_no) >= 10) {
                $prefix = substr($this->lpk_no, 0, 6);
                $suffix = substr($this->lpk_no, -3);

                $this->tdorderlpk = DB::table('tdorderlpk as tolp')
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
                        'mp.codebarcode',
                        'tolp.qty_gulung',
                        'tolp.qty_gentan',
                        'tda.gentan_no',
                        'tolp.total_assembly_line',
                        'mt.berat_jenis',
                    )
                    ->join('msproduct as mp', 'mp.id', '=', 'tolp.product_id')
                    ->leftJoin('tdproduct_assembly as tda', 'tda.lpk_id', '=', 'tolp.id')
                    ->leftJoin('msproduct_type as mt', 'mt.id', '=', 'mp.product_type_id')
                    ->whereRaw("LEFT(lpk_no, 6) ILIKE ?", ["{$prefix}"])
                    ->whereRaw("RIGHT(lpk_no, 3) ILIKE ?", ["{$suffix}"])
                    ->first();

                if ($this->tdorderlpk == null) {
                    $this->dispatch('notification', ['type' => 'warning', 'message' => 'Nomor LPK ' . $this->lpk_no . ' Tidak Terdaftar']);
                    $this->resetLpkNo();
                    return;
                } else {
                    $this->updateLpkData();

                    // Set flag
                    $this->lpkProcessed = true;
                    $this->dispatch('lpk-processed');
                }
            }
        } catch (\Exception $e) {
            $this->addError('lpk_no', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    private function updateLpkData()
    {
        $this->lpk_date = Carbon::parse($this->tdorderlpk->lpk_date)->format('d/m/Y');
        $this->panjang_lpk = number_format($this->tdorderlpk->panjang_lpk, 0, ',', ',');
        $this->code = $this->tdorderlpk->code;
        $this->name = $this->tdorderlpk->name;
        $this->ketebalan = $this->tdorderlpk->ketebalan;
        $this->diameterlipat = $this->tdorderlpk->diameterlipat;
        $this->berat_jenis = $this->tdorderlpk->berat_jenis;
        $this->dimensiinfure = $this->tdorderlpk->ketebalan . 'x' . $this->tdorderlpk->diameterlipat;
        $this->qty_gulung = formatAngka::ribuan($this->tdorderlpk->qty_gulung);
        $this->lpk_no = $this->tdorderlpk->lpk_no;
        $this->qty_gentan = $this->tdorderlpk->qty_gentan;
        $this->total_assembly_line = $this->tdorderlpk->total_assembly_line;
        $selisih = $this->tdorderlpk->total_assembly_line - $this->tdorderlpk->panjang_lpk;
        $this->selisih = round($selisih, 2);
        $this->selisihAwal = $this->selisih;

        $maxGentan = TdProductAssembly::where('lpk_id', $this->tdorderlpk->id)
            ->orderBy('gentan_no', 'DESC')
            ->first();

        if ($maxGentan == null) {
            $this->gentan_no = 1;
        } else {
            $this->gentan_no = $maxGentan->gentan_no + 1;
        }
    }

    // public function updatedWorkHour($work_hour)
    // {
    //     $this->work_hour = $work_hour;

    //     if (isset($this->work_hour) && $this->work_hour != '') {
    //         if (
    //             Carbon::createFromFormat('d/m/Y', $this->production_date)->isSameDay(Carbon::now())
    //             && Carbon::parse($this->work_hour)->format('H:i') > Carbon::now()->format('H:i')
    //         ) {
    //             $this->dispatch('notification', ['type' => 'warning', 'message' => 'Jam Kerja Tidak Boleh Melebihi Jam Sekarang']);
    //             $this->work_hour = Carbon::now()->format('H:i');
    //         }
    //         $this->workShift();
    //     }
    // }

    public function updatedWorkHour($value)
    {
        try {
            // Pastikan format time yang benar (H:i atau H:i:s)
            if (!empty($value)) {
                // Parse dan format ulang untuk memastikan format konsisten
                $time = Carbon::createFromFormat('H:i', $value);
                $this->work_hour = $time->format('H:i:s');
            }
        } catch (\Exception $e) {
            // Jika parsing gagal, set ke null atau nilai default
            $this->work_hour = null;
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

    public function validateMachine()
    {
        if (isset($this->machineno) && $this->machineno != '') {
            $machine = MsMachine::where('machineno', 'ilike', '%' . $this->machineno . '%')
                ->whereIn('department_id', [10, 12, 15, 2, 4, 10])
                ->first();

            if ($machine == null) {
                $this->dispatch('notification', ['type' => 'warning', 'message' => 'Machine ' . $this->machineno . ' Tidak Terdaftar']);
                $this->machineno = '';
                $this->machinename = '';
            } else {
                $this->machineno = $machine->machineno;
                $this->machinename = $machine->machinename;
            }
        }
    }

    public function validateEmployee()
    {
        if (isset($this->employeeno) && $this->employeeno != '') {
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

    public function validateBarcode()
    {
        if (isset($this->nomor_barcode) && $this->nomor_barcode != '' && $this->tdorderlpk != null) {
            if ($this->tdorderlpk->codebarcode != $this->nomor_barcode) {
                $this->dispatch('notification', ['type' => 'warning', 'message' => 'Nomor Barcode ' . $this->nomor_barcode . ' Tidak Sesuai']);
                $this->nomor_barcode = '';
            } else {
                // Barcode valid, auto focus ke input berikutnya
                $this->dispatch('barcode-validated');
            }
        }
    }

    // public function updatedMachineno($machineno)
    // {
    //     $this->machineno = $machineno;

    //     if (isset($this->machineno) && $this->machineno != '') {
    //         $machine = MsMachine::where('machineno', 'ilike', '%' . $this->machineno . '%')->whereIn('department_id', [10, 12, 15, 2, 4, 10])->first();

    //         if ($machine == null) {
    //             $this->dispatch('notification', ['type' => 'warning', 'message' => 'Machine ' . $this->machineno . ' Tidak Terdaftar']);
    //             $this->machineno = '';
    //             $this->machinename = '';
    //         } else {
    //             $this->machineno = $machine->machineno;
    //             $this->machinename = $machine->machinename;
    //         }
    //     }
    // }

    // public function updatedEmployeeno($employeeno)
    // {
    //     $this->employeeno = $employeeno;

    //     if (isset($this->employeeno) && $this->employeeno != '') {
    //         $msemployee = MsEmployee::where('employeeno', 'ilike', '%' . $this->employeeno . '%')->first();

    //         if ($msemployee == null) {
    //             $this->dispatch('notification', ['type' => 'warning', 'message' => 'Employee ' . $this->employeeno . ' Tidak Terdaftar']);
    //             $this->employeeno = '';
    //             $this->empname = '';
    //         } else {
    //             $this->employeeno = $msemployee->employeeno;
    //             $this->empname = $msemployee->empname;
    //         }
    //     }
    // }

    public function updatedPanjangProduksi($panjang_produksi)
    {
        $this->panjang_produksi = $panjang_produksi;

        if (isset($this->panjang_produksi) && $this->panjang_produksi != '') {
            if (!is_numeric(str_replace(',', '', $this->panjang_produksi))) {
                $this->dispatch('notification', ['type' => 'warning', 'message' => 'Panjang Produksi harus berupa angka']);
                $this->panjang_produksi = '';
            }
        }

        if (isset($this->panjang_produksi) && $this->panjang_produksi != '') {
            $total_assembly_line = (int)$this->total_assembly_line + (int)str_replace(',', '', $this->panjang_produksi);
            $this->total_assembly_line = $total_assembly_line;

            $this->berat_standard = ($this->ketebalan * $this->diameterlipat * (int)str_replace(',', '', $this->panjang_produksi) * 2 * $this->berat_jenis) / 1000;

            $this->selisih = (int)$this->selisihAwal + (int)str_replace(',', '', $this->panjang_produksi);
        }
    }

    public function updatedLossInfureCode($loss_infure_code)
    {
        $this->loss_infure_code = $loss_infure_code;

        if (isset($this->loss_infure_code) && $this->loss_infure_code != '') {
            $lossinfure = MsLossInfure::where('code', $this->loss_infure_code)->first();

            if ($lossinfure == null) {
                $this->dispatch('notification', ['type' => 'warning', 'message' => 'Loss Infure ' . $this->loss_infure_code . ' Tidak Terdaftar']);
                $this->loss_infure_code = '';
            } else {
                $this->loss_infure_id = $lossinfure->id;
                $this->name_infure = $lossinfure->name;
            }
        }
    }

    // public function updatedNomorBarcode($nomor_barcode)
    // {
    //     $this->nomor_barcode = $nomor_barcode;

    //     if (isset($this->nomor_barcode) && $this->nomor_barcode != '' && $this->tdorderlpk != null) {
    //         if ($this->tdorderlpk->codebarcode != $this->nomor_barcode) {
    //             $this->nomor_barcode = '';
    //             $this->dispatch('notification', ['type' => 'warning', 'message' => 'Nomor Barcode ' . $this->nomor_barcode . ' Tidak Sesuai']);
    //         }
    //     }
    // }

    public function updatedBeratProduksi($berat_produksi)
    {
        $this->berat_produksi = $berat_produksi;

        if (isset($this->berat_produksi) && isset($this->berat_standard)) {
            if ($this->berat_standard == 0) {
                $this->rasio = 0;
            } else {
                $this->rasio = round(((float)str_replace(',', '', $this->berat_produksi) / $this->berat_standard) * 100, 2);
            }
        }
    }

    public function render()
    {
        // Set default dengan format Y-m-d
        if (empty($this->production_date)) {
            $this->production_date = Carbon::now()->format('Y-m-d');
        }
        if (empty($this->created_on)) {
            $this->created_on = Carbon::now()->format('d/m/Y H:i:s');
        }
        if (empty($this->work_hour)) {
            $this->work_hour = Carbon::now()->format('H:i');
        }

        return view('livewire.nippo-infure.add-nippo')->extends('layouts.master');
    }

    public function editLossInfure($orderId)
    {
        $index = array_search($orderId, array_column($this->details, 'id'));

        if ($index !== false) {

            $infureItem = $this->details[$index];

            // array_splice($this->details, $index, 1);
            $this->editing_id = $infureItem['id'];
            $this->loss_infure_id = $infureItem['loss_infure_id'];
            $this->loss_infure_code = DB::table('mslossinfure')->where('id', $infureItem['loss_infure_id'])->value('code');
            $this->name_infure = DB::table('mslossinfure')->where('id', $infureItem['loss_infure_id'])->value('name');
            $this->berat_loss = $infureItem['berat_loss'];
            $this->frekuensi = $infureItem['frekuensi'];

            $this->dispatch('openModal', 'modal-edit');
        } else {
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Data tidak ditemukan']);
        }
    }

    public function updateLossInfure()
    {
        // validated
        if (!$this->validateLossInfure()) {
            return;
        }

        $index = array_search($this->editing_id, array_column($this->details, 'id'));
        if ($index !== false) {
            $this->details[$index]['loss_infure_id'] = $this->loss_infure_id;
            $this->details[$index]['berat_loss'] = $this->berat_loss;
            $this->details[$index]['frekuensi'] = $this->frekuensi;
        }

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
