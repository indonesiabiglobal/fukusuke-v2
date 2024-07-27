<?php

namespace App\Http\Livewire\NippoSeitai;

use App\Models\TdProductGoods;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class MutasiIsiPaletController extends Component
{
    public $searchOld;
    public $searchNew;
    public $data=[];
    public $result=[];
    public $nomor_lot;
    public $qty_seitai;
    public $qty_mutasi;
    public $orderId;

    public function search ()
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

        $paletTujuan = TdProductGoods::where('nomor_palet',$this->searchOld)->first();
        $this->orderId = $paletTujuan->id;
        $this->nomor_lot=$paletTujuan->nomor_lot;
        $this->qty_seitai=$paletTujuan->qty_produksi;
        $this->qty_mutasi=$paletTujuan->qty_produksi;

        if (!$this->searchNew) {
            $this->dispatch('notification', ['type' => 'warning', 'message' => 'Nomor Palet Tujuan ' . $this->searchNew . ' Tidak Terisi']);
        }
    }

    public function saveMutasi()
    {   
        $save = TdProductGoods::where('id',$this->orderId)->update([
            'nomor_palet'=>$this->searchNew
        ]);

        if($save){
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Data Berhasil di Simpan']);
        }
    }

    public function delete()
    {
        $save = TdProductGoods::where('id',$this->orderId)->update([
            'nomor_palet'=>$this->searchOld
        ]);

        if($save){
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Data Berhasil di Simpan']);
        }
    }

    public function cancel(){
        $this->searchOld='';
        $this->searchNew='';
        $this->data=[];
        $this->result=[];
        $this->nomor_lot='';
        $this->qty_seitai='';
        $this->qty_mutasi='';
    }

    public function render()
    {
        if(isset($this->searchOld) && $this->searchOld != ''){
            $this->data = DB::select("
            SELECT
                tdpg.id,
                tdpg.nomor_lot,
                msm.machinename,
                tdpg.production_date,
                tdpg.qty_produksi,
                tdpg.nomor_palet
            FROM
                tdproduct_goods AS tdpg
                INNER JOIN msmachine AS msm ON msm.id = tdpg.machine_id
            WHERE
                tdpg.nomor_palet = '$this->searchOld'");
            
            if($this->data == null){
                $this->dispatch('notification', ['type' => 'error', 'message' => 'Palet ' . $this->searchOld . ' Tidak Terdaftar']);
            }
        }

        if(isset($this->searchNew) && $this->searchNew != ''){
            $this->result = DB::select("
            SELECT
                tdpg.id,
                tdpg.nomor_lot,
                msm.machinename,
                tdpg.production_date,
                tdpg.qty_produksi,
                tdpg.nomor_palet
            FROM
                tdproduct_goods AS tdpg
                INNER JOIN msmachine AS msm ON msm.id = tdpg.machine_id
            WHERE
                tdpg.nomor_palet = '$this->searchNew'");
            
            if($this->result == null){
                $this->dispatch('notification', ['type' => 'error', 'message' => 'Palet ' . $this->searchNew . ' Tidak Terdaftar']);
            }
        }

        return view('livewire.nippo-seitai.mutasi-isi-palet')->extends('layouts.master');
    }
}
