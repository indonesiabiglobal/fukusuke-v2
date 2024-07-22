<?php

namespace App\Http\Livewire\NippoInfure;

use Livewire\Component;
use Carbon\Carbon;
use App\Models\TdOrderLpk;
use App\Models\MsProduct;
use App\Models\MsBuyer;
use App\Models\MsMachine;
use Illuminate\Support\Facades\DB;

class NippoInfureController extends Component
{
    public $nippo = [];
    public $tdOrderLpk;
    public $products;
    public $buyer;
    public $machine;
    public $tglMasuk;
    public $tglKeluar;
    public $transaksi;
    public $machineId;
    public $status;

    public function mount()
    {
        $this->products = MsProduct::get();
        $this->tdOrderLpk = TdOrderLpk::get();
        $this->buyer = MsBuyer::get();
        $this->machine = MsMachine::get();
        $this->tglMasuk = Carbon::now()->format('Y-m-d');
        $this->tglKeluar = Carbon::now()->format('Y-m-d');  
    }

    public function search(){
        if($this->transaksi == 2){
            $tglMasuk = '';
            if (isset($this->tglMasuk) && $this->tglMasuk != '') {
                $tglMasuk = "WHERE tda.production_date >= '" . $this->tglMasuk . "'";
            }
            $tglKeluar = '';
            if (isset($this->tglKeluar) && $this->tglKeluar != '') {
                $tglKeluar = "AND tda.production_date <= '" . $this->tglKeluar . "'";
            }
        } else {
            $tglMasuk = '';
            if (isset($this->tglMasuk) && $this->tglMasuk != '') {
                $tglMasuk = "WHERE tda.created_on >= '" . $this->tglMasuk . " 00:00'";
            }
            $tglKeluar = '';
            if (isset($this->tglKeluar) && $this->tglKeluar != '') {
                $tglKeluar = "AND tda.created_on <= '" . $this->tglKeluar . " 23:59'";
            }
        }

        $machineId = '';
        if (isset($this->machineId) && $this->machineId != "" && $this->machineId != "undefined") {
            $machineId = "AND msm.id = '" . $this->machineId . "'";
        }

        $status = '';
        if (isset($this->status) && $this->status != "" && $this->status != "undefined") {
            if ($this->status == 0){
                $status = "AND tda.status_production = 0 AND tda.status_kenpin = 0";
            } else if ($this->status == 1){
                $status = "AND tda.status_production = 1";
            } else if ($this->status == 2){
                $status = "AND tda.status_kenpin = 1";
            }
            
        }
        
        // produk
        $searchTerm = '';
        if (isset($this->searchTerm) && $this->searchTerm != '') {
            $searchTerm = "AND (tdol.lpk_no ilike '%" . $this->searchTerm . 
            "%' OR tda.production_no ilike '%" . $this->searchTerm . 
            "%' OR tda.product_id ilike '%" . $this->searchTerm .
            "%' OR tda.machine_id ilike '%" . $this->searchTerm . 
            "%')";
        }

        $this->nippo = DB::select("
        SELECT
            tda.ID AS ID,
            tda.production_no AS production_no,
            tda.production_date AS production_date,
            tda.employee_id AS employee_id,
            tda.work_shift AS work_shift,
            tda.work_hour AS work_hour,
            tda.machine_id AS machine_id,
            tda.lpk_id AS lpk_id,
            tda.product_id AS product_id,
            tda.panjang_produksi AS panjang_produksi,
            tda.panjang_printing_inline AS panjang_printing_inline,
            tda.berat_standard AS berat_standard,
            tda.berat_produksi AS berat_produksi,
            tda.nomor_han AS nomor_han,
            tda.gentan_no AS gentan_no,
            tda.seq_no AS seq_no,
            tda.status_production AS status_production,
            tda.status_kenpin AS status_kenpin,
            tda.infure_cost AS infure_cost,
            tda.infure_cost_printing AS infure_cost_printing,
            tda.infure_berat_loss AS infure_berat_loss,
            tda.kenpin_berat_loss AS kenpin_berat_loss,
            tda.kenpin_meter_loss AS kenpin_meter_loss,
            tda.kenpin_meter_loss_proses AS kenpin_meter_loss_proses,
            tda.created_by AS created_by,
            tda.created_on AS created_on,
            tda.updated_by AS updated_by,
            tda.updated_on AS updated_on,
            tdol.order_id AS order_id,
            tdol.lpk_no AS lpk_no,
            tdol.lpk_date AS lpk_date,
            tdol.panjang_lpk AS panjang_lpk,
            tdol.qty_gentan AS qty_gentan,
            tdol.qty_gulung AS qty_gulung,
            tdol.qty_lpk AS qty_lpk,
            tdol.total_assembly_line AS total_assembly_line,
            tdol.total_assembly_qty AS total_assembly_qty,
            msm.machineno,
            tdo.product_code 
        FROM
            tdProduct_assembly AS tda
            INNER JOIN tdOrderLpk AS tdol ON tda.lpk_id = tdol.ID 
            inner join msmachine as msm on msm.id=tda.machine_id
            INNER JOIN tdOrder AS tdo ON tdol.order_id=tdo.id
        $tglMasuk
        $tglKeluar
        $searchTerm
        $machineId
        $status
        ");
    }

    public function add()
    {
        return redirect()->route('add-order');
    }

    public function render()
    {
        return view('livewire.nippo-infure.nippo-infure', [
            'data' => $this->nippo
        ])->extends('layouts.master');
    }
}
