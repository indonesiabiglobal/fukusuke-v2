<?php

namespace App\Http\Livewire\NippoInfure;

use Livewire\Component;
use Carbon\Carbon;
use App\Models\MsProduct;
use App\Models\MsBuyer;
use App\Models\MsMachine;
use Illuminate\Support\Facades\DB;

class LossInfureController extends Component
{
    public $loss = [];
    public $products;
    public $buyer;
    public $machine;
    public $tglMasuk;
    public $tglKeluar;
    public $transaksi;
    public $machineId;
    public $status;
    public $searchTerm;

    public function mount()
    {
        $this->products = MsProduct::get();
        $this->buyer = MsBuyer::get();
        $this->machine = MsMachine::get();
        $this->tglMasuk = Carbon::now()->format('Y-m-d');
        $this->tglKeluar = Carbon::now()->format('Y-m-d'); 
    }

    public function add()
    {
        return redirect()->route('add-order');
    }

    public function search(){
        if($this->transaksi == 2){
            $tglMasuk = '';
            if (isset($this->tglMasuk) && $this->tglMasuk != '') {
                $tglMasuk = "WHERE tdpa.production_date >= '" . $this->tglMasuk . "'";
            }
            $tglKeluar = '';
            if (isset($this->tglKeluar) && $this->tglKeluar != '') {
                $tglKeluar = "AND tdpa.production_date <= '" . $this->tglKeluar . "'";
            }
        } else {
            $tglMasuk = '';
            if (isset($this->tglMasuk) && $this->tglMasuk != '') {
                $tglMasuk = "WHERE tdpa.created_on >= '" . $this->tglMasuk . " 00:00'";
            }
            $tglKeluar = '';
            if (isset($this->tglKeluar) && $this->tglKeluar != '') {
                $tglKeluar = "AND tdpa.created_on <= '" . $this->tglKeluar . " 23:59'";
            }
        }
        // produk
        
        $machineId = '';
        if (isset($this->machineId) && $this->machineId != "" && $this->machineId != "undefined") {
            $machineId = "AND msm.id = '" . $this->machineId . "'";
        }

        $status = '';
        if (isset($this->status) && $this->status != "" && $this->status != "undefined") {
            if ($this->status == 0){
                $status = "AND tdpa.status_production = 0 AND tdpa.status_kenpin = 0";
            } else if ($this->status == 1){
                $status = "AND tdpa.status_production = 1";
            } else if ($this->status == 2){
                $status = "AND tdpa.status_kenpin = 1";
            }
            
        }

        $searchTerm = '';
        if (isset($this->searchTerm) && $this->searchTerm != '') {
            $searchTerm = "AND (tdol.lpk_no ilike '%" . $this->searchTerm . 
            "%' OR tdpa.production_no ilike '%" . $this->searchTerm . 
            "%')";
        }

        $this->loss = DB::select("
        SELECT
            tdpa.ID AS ID,
            tdpa.production_no AS production_no,
            tdpa.production_date AS production_date,
            tdpa.employee_id AS employee_id,
            tdpa.work_shift AS work_shift,
            tdpa.work_hour AS work_hour,
            tdpa.machine_id AS machine_id,
            tdpa.lpk_id AS lpk_id,
            tdpa.product_id AS product_id,
            tdpa.panjang_produksi AS panjang_produksi,
            tdpa.panjang_printing_inline AS panjang_printing_inline,
            tdpa.berat_standard AS berat_standard,
            tdpa.berat_produksi AS berat_produksi,
            tdpa.nomor_han AS nomor_han,
            tdpa.gentan_no AS gentan_no,
            tdpa.seq_no AS seq_no,
            tdpa.status_production AS status_production,
            tdpa.status_kenpin AS status_kenpin,
            tdpa.infure_cost AS infure_cost,
            tdpa.infure_cost_printing AS infure_cost_printing,
            tdpa.infure_berat_loss AS infure_berat_loss,
            tdpa.kenpin_berat_loss AS kenpin_berat_loss,
            tdpa.kenpin_meter_loss AS kenpin_meter_loss,
            tdpa.kenpin_meter_loss_proses AS kenpin_meter_loss_proses,
            tdpa.created_by AS created_by,
            tdpa.created_on AS created_on,
            tdpa.updated_by AS updated_by,
            tdpa.updated_on AS updated_on,
            tdol.order_id AS order_id,
            tdol.lpk_no AS lpk_no,
            tdol.lpk_date AS lpk_date,
            tdol.panjang_lpk AS panjang_lpk,
            tdol.qty_gentan AS qty_gentan,
            tdol.qty_gulung AS qty_gulung,
            tdol.qty_lpk AS qty_lpk,
            tdol.total_assembly_line AS total_assembly_line,
            tdol.total_assembly_qty AS total_assembly_qty 
        FROM
            tdproduct_assembly AS tdpa
            INNER JOIN tdorderlpk AS tdol ON tdpa.lpk_id = tdol.ID 
            inner join msmachine as msm on msm.id=tdpa.machine_id
        $tglMasuk
        $tglKeluar
        $searchTerm
        $machineId
        $status
        ");
    }

    public function render()
    {
        return view('livewire.nippo-infure.loss-infure', [
            'data' => $this->loss
        ])->extends('layouts.master');
    }
}
