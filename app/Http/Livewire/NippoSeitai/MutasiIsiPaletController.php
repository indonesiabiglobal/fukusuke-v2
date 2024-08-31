<?php

namespace App\Http\Livewire\NippoSeitai;

use App\Models\MsProduct;
use App\Models\TdProductGoods;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class MutasiIsiPaletController extends Component
{
    public $searchOld;
    public $searchNew;
    public $data = [];
    public $result = [];
    public $nomor_lot;
    public $qty_seitai;
    public $qty_mutasi;
    public $orderId;
    public $products;
    public $case_box_count;

    public function mount()
    {
        $this->products = MsProduct::get();
    }

    public function search()
    {
        $this->render();
    }

    public function searchTujuan()
    {
        $this->render();
    }

    public function import()
    {
        // $validatedData = $this->validate([
        //     'lpk_no' => 'required',
        //     'machineno' => 'required',
        //     'employeeno' => 'required',
        //     // 'panjang_produksi' => 'required',
        //     // 'qty_gentan' => 'required'
        // ]);

        // $paletTujuan = TdProductGoods::where('nomor_palet', $this->searchOld)->first();
        $paletTujuan = DB::table('tdproduct_goods as tdpg')
            ->select([
                'tdpg.id',
                'tdpg.nomor_lot',
                'msm.machineno',
                'tdpg.production_date',
                'msp.case_box_count',
                DB::raw('tdpg.qty_produksi / msp.case_box_count as qty_produksi'),
                'tdpg.nomor_palet'
            ])
            ->join('msmachine as msm', 'msm.id', '=', 'tdpg.machine_id')
            ->join('msproduct as msp', 'msp.id', '=', 'tdpg.product_id')
            ->where('tdpg.nomor_palet', $this->searchOld)
            ->first();

        $this->orderId = $paletTujuan->id;
        $this->nomor_lot = $paletTujuan->nomor_lot;
        $this->qty_seitai = $paletTujuan->qty_produksi;
        $this->qty_mutasi = $paletTujuan->qty_produksi;
        $this->case_box_count = $paletTujuan->case_box_count;

        if (!$this->searchNew) {
            $this->dispatch('notification', ['type' => 'warning', 'message' => 'Nomor Palet Tujuan ' . $this->searchNew . ' Tidak Terisi']);
        }
    }

    public function saveMutasi()
    {
        if ($this->qty_seitai == $this->qty_mutasi) {
            $save = TdProductGoods::where('id', $this->orderId)->update([
                'nomor_palet' => $this->searchNew
            ]);
        } else {
            $save = TdProductGoods::where('id', $this->orderId)->update([
                'nomor_palet' => $this->searchOld,
                'qty_produksi' => ((int)$this->qty_seitai * (int)$this->case_box_count) - ((int)$this->qty_mutasi * (int)$this->case_box_count)
            ]);

            $data = TdProductGoods::where('id', $this->orderId)->first();

            $datas = new TdProductGoods();
            $datas->production_no = $data['production_no'];
            $datas->production_date = $data['production_date'];
            $datas->employee_id = $data['employee_id'];
            $datas->employee_id_infure = $data['employee_id_infure'];;
            $datas->work_shift = $data['work_shift'];
            $datas->work_hour = $data['work_hour'];
            $datas->machine_id = $data['machine_id'];
            $datas->lpk_id = $data['lpk_id'];
            $datas->product_id = $data['product_id'];
            $datas->qty_produksi = (int)$this->qty_mutasi * (int)$this->case_box_count;
            $datas->seitai_berat_loss = $data['seitai_berat_loss'];
            $datas->infure_berat_loss = $data['infure_berat_loss'];
            $datas->seq_no = $data['seq_no'];
            $datas->nomor_palet = $this->searchNew;
            $datas->nomor_lot = $data['nomor_lot'];
            $datas->status_production = $data['status_production'];
            $datas->status_warehouse = $data['status_warehouse'];
            $datas->kenpin_qty_loss = $data['kenpin_qty_loss'];
            $datas->kenpin_qty_loss_proses = $data['kenpin_qty_loss_proses'];
            $datas->created_by = $data['created_by'];
            $datas->created_on = $data['created_on'];
            $datas->updated_by = $data['updated_by'];
            $datas->updated_on = $data['updated_on'];
            $datas->save();

            // $save = TdProductGoods::where('id', $this->orderId)->update([
            //     'nomor_palet' => $this->searchNew,
            //     'qty_produksi' => ((int)$this->qty_mutasi * (int)$this->case_box_count)
            // ]);
        }

        if ($save) {
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Data Berhasil di Simpan']);
        }
    }

    public function delete()
    {
        $save = TdProductGoods::where('id', $this->orderId)->update([
            'nomor_palet' => $this->searchOld
        ]);

        if ($save) {
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Data Berhasil di Simpan']);
        }
    }

    public function cancel()
    {
        $this->searchOld = '';
        $this->searchNew = '';
        $this->data = [];
        $this->result = [];
        $this->nomor_lot = '';
        $this->qty_seitai = '';
        $this->qty_mutasi = '';
    }

    public function render()
    {
        if (isset($this->searchOld) && $this->searchOld != '') {
            $this->data = DB::select("
            SELECT
                tdpg.id,
                tdpg.nomor_lot,
                msm.machineno,
                tdpg.production_date,
                tdpg.qty_produksi / msp.case_box_count as qty_produksi,
                tdpg.nomor_palet
            FROM
                tdproduct_goods AS tdpg
                INNER JOIN msmachine AS msm ON msm.id = tdpg.machine_id
                INNER JOIN msproduct as msp on msp.id = tdpg.product_id
            WHERE
                tdpg.nomor_palet = '$this->searchOld'");

            if ($this->data == null) {
                $this->dispatch('notification', ['type' => 'error', 'message' => 'Palet ' . $this->searchOld . ' Tidak Terdaftar']);
            }
        }

        if (isset($this->searchNew) && $this->searchNew != '') {
            $this->result = DB::select("
            SELECT
                tdpg.id,
                tdpg.nomor_lot,
                msm.machineno,
                tdpg.production_date,
                tdpg.qty_produksi / msp.case_box_count as qty_produksi,
                tdpg.nomor_palet
            FROM
                tdproduct_goods AS tdpg
                INNER JOIN msmachine AS msm ON msm.id = tdpg.machine_id
                INNER JOIN msproduct as msp on msp.id = tdpg.product_id
            WHERE
                tdpg.nomor_palet = '$this->searchNew'");

            if ($this->result == null) {
                $this->dispatch('notification', ['type' => 'error', 'message' => 'Palet ' . $this->searchNew . ' Tidak Terdaftar']);
            }
        }

        return view('livewire.nippo-seitai.mutasi-isi-palet')->extends('layouts.master');
    }
}
