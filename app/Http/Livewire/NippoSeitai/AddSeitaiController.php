<?php

namespace App\Http\Livewire\NippoSeitai;

use Livewire\Component;
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

class AddSeitaiController extends Component
{
    public $product_goods_id;
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
    public $start_box;
    public $end_box;
    public $infure_berat_loss;
    public $berat_produksi;
    public $petugas;
    public $machine_no;
    public $gentan_line;
    public $detailsGentan = [];
    public $detailsLoss = [];
    public $orderid;
    public $code_loss;
    public $loss_seitai_id;
    public $berat_loss;
    public $namaloss;
    public $berat;
    public $berat_fr;
    public $frekuensi;
    public $jumlahBeratProduksi;
    public $jumlahBeratLoss;
    public $currentLossId = 1;
    public $currentGentanId = 1;
    public $total_assembly_qty;
    public $selisih;
    public $jumlah_box;
    public $jumlah_box_product;
    public $activeTab = 'Gentan';
    public $editing_id = null;

    // data master produk
    public $masterKatanuki;
    public $product;
    public $photoKatanuki;
    public $katanuki_id;

    // data LPK
    public $orderLPK;
    public $tdorderlpk;

    public function mount(Request $request)
    {
        if (!empty($request->query('lpk_no'))) {
            $this->lpk_no = $request->query('lpk_no');
            $this->processLpkNo();
        }
        $this->production_date = Carbon::now()->format('d/m/Y');
        $this->created_on = Carbon::now()->format('d/m/Y');
        $this->work_hour = Carbon::now()->format('H:i');
        $this->infure_berat_loss = 0;

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
            $this->berat_loss = '';
            $this->frekuensi = '';
            $this->dispatch('showModalLoss');
        }
    }

    public function save()
    {
        $this->qty_produksi = (int)str_replace(',', '', $this->qty_produksi);
        $this->qty_lpk = (int)str_replace(',', '', $this->qty_lpk);
        // $this->infure_berat_loss = str_replace(',', '', $this->infure_berat_loss);
        $validatedData = $this->validate([
            'lpk_no' => 'required',
            'nomor_palet' => 'required',
            'nomor_lot' => 'required',
            'infure_berat_loss' => 'required',
            'work_hour' => 'required|regex:/^[0-9]{2}:[0-9]{2}$/',
            'start_box' => 'nullable',
            'end_box' => 'nullable',
        ]);

        // mengecek detailsGentan yang tidak boleh kosong
        if (count($this->detailsGentan) == 0) {
            $this->dispatch('notification', ['type' => 'warning', 'message' => 'Data Gentan tidak boleh kosong']);
            return;
        }

        try {
            DB::beginTransaction();
            $lastSeq = TdProductGoods::whereDate('created_on', Carbon::today())
                ->orderBy('seq_no', 'desc')
                ->first();
            $lpkid = TdOrderLpk::where('lpk_no', $this->lpk_no)->first();
            $machine = MsMachine::where('machineno', $this->machineno)->first();
            $employe = MsEmployee::where('employeeno', $this->employeeno)->first();
            $employeinfure = MsEmployee::where('employeeno', $this->employeenoinfure)->first();
            $products = MsProduct::where('code', $this->code)->first();

            $lastQty = TdProductGoods::where('lpk_id', $lpkid->id)
                ->sum('qty_produksi');

            $seqno = 1;
            if (!empty($lastSeq)) {
                $seqno = $lastSeq->seq_no + 1;
            }
            $today = Carbon::now();
            $createdOn = Carbon::createFromFormat('d/m/Y H:i:s', $this->created_on . ' ' . now()->format('H:i:s'));

            $data = new TdProductGoods();
            $data->production_no = $today->format('dmy') . '-' . $seqno;
            $data->production_date = $this->production_date . ' ' . $this->work_hour;
            $data->employee_id = $employe->id;
            if (isset($this->employeenoinfure)) {
                $data->employee_id_infure = $employeinfure->id;
            }
            $data->work_shift = $this->work_shift;
            $data->work_hour = $this->work_hour;
            $data->machine_id = $machine->id;
            $data->lpk_id = $lpkid->id;
            $data->product_id = $products->id;
            $data->qty_produksi = $this->qty_produksi;
            $data->infure_berat_loss = $this->infure_berat_loss;
            $data->seq_no = $seqno;
            $data->nomor_palet = $this->nomor_palet;
            $data->nomor_lot = $this->nomor_lot;
            // start and end box (Nomor Box range)
            if (isset($this->start_box)) {
                $data->start_box = $this->start_box ? $this->start_box : null;
            }
            if (isset($this->end_box)) {
                $data->end_box = $this->end_box ? $this->end_box : null;
            }
            $data->created_on = $createdOn;
            $data->created_by = auth()->user()->username;
            $data->updated_on = $createdOn;
            $data->updated_by = auth()->user()->username;

            // jumlah berat loss
            if (isset($this->jumlahBeratLoss)) {
                $data->seitai_berat_loss = $this->jumlahBeratLoss;
            }
            $data->save();

            $this->product_goods_id = $data->id;

            TdOrderLpk::where('id', $lpkid->id)->update([
                'total_assembly_qty' => $lastQty + $this->qty_produksi,
            ]);

            // menginput data gentan
            foreach ($this->detailsGentan as $gentan) {
                $datas = new TdProductGoodsAssembly();
                $datas->product_goods_id = $data->id;
                $datas->product_assembly_id = $gentan['product_assembly_id'];
                $datas->gentan_line = $gentan['gentan_line'];
                $datas->berat = $gentan['berat'];
                $datas->lpk_id = $lpkid->id;
                $datas->created_on = $createdOn;
                $datas->created_by = auth()->user()->username;
                $datas->updated_on = $createdOn;
                $datas->updated_by = auth()->user()->username;
                $datas->save();

                // update status production pada TdProductAssembly
                TdProductAssembly::where('id', $gentan['product_assembly_id'])
                    ->update([
                        'status_production' => 1,
                    ]);
            }

            // menginput data loss
            foreach ($this->detailsLoss as $loss) {
                $datas = new TdProductGoodsLoss();
                $datas->product_goods_id = $data->id;
                $datas->loss_seitai_id = $loss['loss_seitai_id'];
                $datas->berat_loss = $loss['berat_loss'];
                $datas->frekuensi = $loss['frekuensi'];
                $datas->lpk_id = $lpkid->id;
                $datas->save();
            }

            $totalGoods = DB::select("
            SELECT
                CASE WHEN x.A1 IS NULL THEN 0 ELSE x.A1 END AS C1
            FROM
                (
                SELECT SUM(qty_produksi) AS A1
                FROM
                    tdproduct_goods AS ta
                WHERE
                    lpk_id = $lpkid->id
            ) AS x
            ");

            TdOrderLpk::where('id', $lpkid->id)->update([
                'total_assembly_qty' => $totalGoods[0]->c1,
            ]);

            DB::commit();
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Data Berhasil di Simpan']);
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

    public function nextIdGentan()
    {
        return $this->currentGentanId++;
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
        // cek jika no gentan sudah ada di detailsGentan
        if (in_array($this->gentan_no, $this->detailsGentan)) {
            return $this->dispatch('notification', [
                'type' => 'error',
                'message' => 'Data Gagal di Simpan, No Gentan Sudah Ada'
            ]);
        }

        $lpkid = TdOrderLpk::where('lpk_no', $this->lpk_no)->first();
        $assembly = TdProductAssembly::where('lpk_id', $lpkid->id)
            ->where('gentan_no', $this->gentan_no)
            ->first();

        foreach ($this->detailsGentan as $item) {
            if ($this->gentan_no == $item['gentan_no']) {
                $this->resetGentan();
                return $this->dispatch('notification', ['type' => 'error', 'message' => 'Data Gagal di Simpan']);
            }
        }
        $data = [
            'id' => $this->nextIdGentan(),
            'gentan_no' => $this->gentan_no,
            'gentan_line' => $this->gentan_line,
            'machineno' => $this->machine_no,
            'work_shift' => $this->work_shift,
            'empname' => $this->petugas,
            'production_date' => $this->production_date,
            'product_goods_id' => $this->product_goods_id,
            'product_assembly_id' => $assembly->id,
            'berat' => $this->berat_produksi,
            'lpk_id' => $lpkid->id,
        ];
        $this->jumlahBeratProduksi += $this->berat_produksi;

        $this->detailsGentan[] = $data;
        $this->resetGentan();

        $this->dispatch('closeModalGentan');
        $this->dispatch('notification', ['type' => 'success', 'message' => 'Data Berhasil di Simpan']);
    }

    public function resetLoss()
    {
        $this->code_loss = '';
        $this->namaloss = '';
        $this->berat_loss = '';
        $this->frekuensi = '';
    }

    public function nextIdLoss()
    {
        return $this->currentLossId++;
    }

    public function saveLoss()
    {
        $lpkid = TdOrderLpk::where('lpk_no', $this->lpk_no)->first();
        $loss = MsLossSeitai::where('code', $this->code_loss)
            ->first();

        $this->detailsLoss[] = [
            'id' => $this->nextIdLoss(),
            'code_loss' => $loss->code,
            'namaloss' => $loss->name,
            'product_goods_id' => $this->product_goods_id,
            'loss_seitai_id' => $loss->id,
            'berat_loss' => $this->berat_loss,
            'frekuensi' => $this->frekuensi,
            'lpk_id' => $lpkid->id,
        ];

        // menambahkan ke tdproduct_goods
        $this->jumlahBeratLoss += $this->berat_loss;
        $this->resetLoss();


        $this->dispatch('closeModalLoss');
        $this->dispatch('notification', ['type' => 'success', 'message' => 'Data Berhasil di Simpan']);
    }

    public function deleteGentan($orderId)
    {
        // delete gentan
        $index = array_search($orderId, array_column($this->detailsGentan, 'id'));

        if ($index !== false) {
            // mengurangi dari jumlah berat produksi
            $this->jumlahBeratProduksi -= $this->detailsGentan[$index]['berat'];
            array_splice($this->detailsGentan, $index, 1);
        }

        $this->dispatch('closeModalDeleteGentan', $orderId);
        $this->dispatch('notification', ['type' => 'success', 'message' => 'Data Berhasil di Hapus']);
    }

    public function deleteLoss($orderId)
    {
        $index = array_search($orderId, array_column($this->detailsLoss, 'id'));

        if ($index !== false) {
            $this->jumlahBeratLoss -= $this->detailsLoss[$index]['berat_loss'];
            array_splice($this->detailsLoss, $index, 1);
            // mengurangi dari jumlah berat loss
        }

        $this->dispatch('closeModalDeleteLoss', $orderId);
        $this->dispatch('notification', ['type' => 'success', 'message' => 'Data Berhasil di Hapus']);
    }

    public function editLoss($orderId)
    {
        $index = array_search($orderId, array_column($this->detailsLoss, 'id'));

        if ($index !== false) {
            $this->editing_id = $orderId;
            $this->loss_seitai_id = $this->detailsLoss[$index]['loss_seitai_id'];
            $this->code_loss = $this->detailsLoss[$index]['code_loss'];
            $this->namaloss = $this->detailsLoss[$index]['namaloss'];
            $this->berat_loss = $this->detailsLoss[$index]['berat_loss'];
            $this->frekuensi = $this->detailsLoss[$index]['frekuensi'];
            $this->dispatch('openModal', 'modal-edit-loss');
        } else {
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Data tidak ditemukan']);
        }
    }

    public function editGentan($orderId)
    {
        $index = array_search($orderId, array_column($this->detailsGentan, 'id'));

        if ($index !== false) {
            $this->editing_id = $orderId;
            $this->gentan_no = $this->detailsGentan[$index]['gentan_no'];
            $this->gentan_line = $this->detailsGentan[$index]['gentan_line'] ?? '';
            $this->machine_no = $this->detailsGentan[$index]['machineno'] ?? '';
            $this->petugas = $this->detailsGentan[$index]['empname'] ?? '';
            $this->berat_produksi = $this->detailsGentan[$index]['berat'] ?? '';
            $this->dispatch('openModal', 'modal-edit-gentan');
        } else {
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Data tidak ditemukan']);
        }
    }

    public function updateGentan()
    {
        $validatedData = $this->validate([
            'gentan_no' => 'required',
            'gentan_line' => 'required',
            'berat_produksi' => 'required',
        ]);

        if ($validatedData) {
            $index = array_search($this->editing_id, array_column($this->detailsGentan, 'id'));
            if ($index !== false) {
                // adjust jumlah berat produksi
                $oldBerat = $this->detailsGentan[$index]['berat'] ?? 0;
                $this->jumlahBeratProduksi -= $oldBerat;

                $this->detailsGentan[$index]['gentan_no'] = $this->gentan_no;
                $this->detailsGentan[$index]['gentan_line'] = $this->gentan_line;
                $this->detailsGentan[$index]['machineno'] = $this->machine_no;
                $this->detailsGentan[$index]['empname'] = $this->petugas;
                $this->detailsGentan[$index]['berat'] = $this->berat_produksi;

                // add new berat
                $this->jumlahBeratProduksi += $this->berat_produksi;
            }

            $this->resetGentan();
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Gentan berhasil diupdate']);
            $this->dispatch('closeModal', 'modal-edit-gentan');

            // hapus validasi
            $this->resetErrorBag();
        }
    }

    public function updateLoss()
    {
        $validatedData = $this->validate([
            'loss_seitai_id' => 'required',
            'code_loss' => 'required',
            'berat_loss' => 'required',
            'frekuensi' => 'required',
        ]);

        if ($validatedData) {
            $index = array_search($this->editing_id, array_column($this->detailsLoss, 'id'));
            if ($index !== false) {
                $this->detailsLoss[$index]['loss_seitai_id'] = $this->loss_seitai_id;
                $this->detailsLoss[$index]['code_loss'] = $this->code_loss;
                $this->detailsLoss[$index]['namaloss'] = $this->namaloss;
                $this->detailsLoss[$index]['berat_loss'] = $this->berat_loss;
                $this->detailsLoss[$index]['frekuensi'] = $this->frekuensi;
            }

            $this->resetInputFields();
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Loss Seitai berhasil diupdate']);
            $this->dispatch('closeModal', 'modal-edit-loss');

            // hapus validasi
            $this->resetErrorBag();
        }
    }

    public function resetSeitai()
    {
        $this->code_loss = '';
        $this->namaloss = '';
        $this->berat_loss = '';
    }

    public function resetLpkNo()
    {
        $this->lpk_no = '';
        $this->lpk_date = '';
        $this->panjang_lpk = '';
        $this->created_on = '';
        $this->code = '';
        $this->name = '';
        $this->dimensiinfure = '';
        $this->qty_gulung = '';
        $this->qty_gentan = '';
        $this->total_assembly_qty = 0;
        $this->qty_lpk = '';
        $this->selisih = 0;
        $this->start_box = null;
        $this->end_box = null;
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
                $this->tdorderlpk = DB::table('tdorderlpk as tolp')
                    ->select(
                        'tolp.id',
                        'tolp.lpk_date',
                        'tolp.panjang_lpk',
                        'tolp.created_on',
                        'tolp.qty_lpk',
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

                if ($this->tdorderlpk == null) {
                    $this->dispatch('notification', ['type' => 'warning', 'message' => 'Nomor LPK ' . $this->lpk_no . ' Tidak Terdaftar']);
                    $this->resetLpkNo();
                } else {
                    $this->updateLpkData();
                }
            }
        } catch (\Exception $e) {
            $this->addError('lpk_no', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    private function updateLpkData()
    {
        $this->lpk_date = Carbon::parse($this->tdorderlpk->lpk_date)->format('d/m/Y');
        $this->panjang_lpk = $this->tdorderlpk->panjang_lpk;
        $this->code = $this->tdorderlpk->code;
        $this->name = $this->tdorderlpk->name;
        $this->dimensiinfure = $this->tdorderlpk->ketebalan . 'x' . $this->tdorderlpk->diameterlipat;
        $this->qty_gulung = $this->tdorderlpk->qty_gulung;
        $this->qty_gentan = $this->tdorderlpk->qty_gentan;
        $this->total_assembly_qty = number_format($this->tdorderlpk->total_assembly_qty);
        $this->qty_lpk = number_format($this->tdorderlpk->qty_lpk);
        $this->selisih = $this->tdorderlpk->qty_lpk - $this->tdorderlpk->total_assembly_qty;

        // update jumlah_box_product
        $qty = (int) str_replace(',', '', $this->qty_produksi);
        if ($this->qty_produksi) {
            $this->jumlah_box_product = ceil($qty / $this->tdorderlpk->case_box_count);
        }
    }

    public function updatedMachineno($machineno)
    {
        $this->machineno = $machineno;

        if (isset($this->machineno) && $this->machineno != '') {
            $machine = MsMachine::where('machineno', 'ilike', '%' . $this->machineno . '%')->whereNotIn(
                'department_id',
                [10, 12, 15, 2, 4, 10]
            )->first();

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

        if (isset($this->employeenoinfure) && $this->employeenoinfure != '') {
            $msemployeeinfure = MsEmployee::where('employeeno', 'ilike', '%' . $this->employeenoinfure . '%')->first();

            if ($msemployeeinfure == null) {
                $this->dispatch('notification', ['type' => 'warning', 'message' => 'Employee ' . $this->employeenoinfure . ' Tidak Terdaftar']);
                $this->employeenoinfure = '';
                $this->empnameinfure = '';
            } else {
                $this->employeenoinfure = $msemployeeinfure->employeeno;
                $this->empnameinfure = $msemployeeinfure->empname;
            }
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

    public function updatedCodeLoss($code_loss)
    {
        $this->code_loss = $code_loss;

        if (isset($this->code_loss) && $this->code_loss != '') {
            $lossSeitai = MsLossSeitai::where('code', $this->code_loss)->first();

            if ($lossSeitai == null) {
                $this->dispatch('notification', ['type' => 'warning', 'message' => 'Kode Loss ' . $this->code_loss . ' Tidak Terdaftar']);
                $this->resetSeitai();
            } else {
                $this->namaloss = $lossSeitai->name;
                $this->loss_seitai_id = $lossSeitai->id;
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

    public function updatedQtyProduksi($qty_produksi)
    {
        $this->qty_produksi = $qty_produksi;

        if (isset($this->qty_produksi) && $this->qty_produksi != '' && isset($this->qty_lpk) && $this->qty_lpk != '') {
            $qty = (int) str_replace(',', '', $this->qty_produksi);
            $this->total_assembly_qty = number_format((int)str_replace(',', '', $this->total_assembly_qty) + $qty);
            $this->selisih = $this->selisih - $qty;

            $caseBoxCount = isset($this->tdorderlpk->case_box_count) ? (int) $this->tdorderlpk->case_box_count : 0;
            $this->jumlah_box_product = $caseBoxCount > 0 ? (int) ceil($qty / $caseBoxCount) : 0;
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

    private function resetInputFields()
    {
        $this->editing_id = null;
        $this->code_loss = null;
        $this->namaloss = '';
        $this->berat_loss = '';
        $this->frekuensi = '';
        $this->start_box = null;
        $this->end_box = null;
    }

    public function render()
    {
        if (!(isset($this->production_date) && $this->production_date != '')) {
            $this->production_date = Carbon::now()->format('d/m/Y');
        }

        return view('livewire.nippo-seitai.add-seitai')->extends('layouts.master');
    }
}
