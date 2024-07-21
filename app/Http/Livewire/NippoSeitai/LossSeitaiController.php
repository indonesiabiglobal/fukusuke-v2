<?php

namespace App\Http\Livewire\NippoSeitai;

use App\Models\MsMachine;
use Carbon\Carbon;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class LossSeitaiController extends Component
{
    public $loss = [];
    // public $product;
    // public $buyer;
    public $tglMasuk;
    public $tglKeluar;
    public $machine = [];
    public $transaksi;
    public $machine_id;

    public function mount()
    {
        // $this->product = MsProduct::limit(10)->get();
        // $this->buyer = MsBuyer::limit(10)->get();
        $this->machine = MsMachine::limit(10)->get();
        $this->tglMasuk = Carbon::now()->format('Y-m-d');
        $this->tglKeluar = Carbon::now()->format('Y-m-d'); 
    }

        // public function updatedStreetAddress()
        // {
        //     dd($this->machine_id);
        //     if($this->streetAddress != '') {
        //         $this->machine = MsMachine::searchAddress($this->machine_id);
        //     } else {
        //         $this->machine = [];
        //     }
        // }

    public function search(){
        if($this->transaksi == 2){
            $tglMasuk = '';
            if (isset($this->tglMasuk) && $this->tglMasuk != '') {
                $tglMasuk = "WHERE tdpg.production_date >= '" . $this->tglMasuk . "'";
            }
            $tglKeluar = '';
            if (isset($this->tglKeluar) && $this->tglKeluar != '') {
                $tglKeluar = "AND tdpg.production_date <= '" . $this->tglKeluar . "'";
            }
            // produk
            $machineid = '';
            if(isset($this->machineid) && $this->machineid != ''){
                $machineid = "AND tdpg.machine_id='".$this->machineid."'";
            }
            // status
            $searchTerm = '';
            if (isset($this->searchTerm) && $this->searchTerm != '') {
                $searchTerm = "AND (tdol.lpk_no ilike '%" . $this->searchTerm . 
                "%' OR tdpg.production_no ilike '%" . $this->searchTerm . 
                "%' OR tdpg.product_id ilike '%" . $this->searchTerm .
                "%' OR tdpg.machine_id ilike '%" . $this->searchTerm . 
                "%')";
            }
            $gentan_no = '';
            if(isset($this->gentan_no) && $this->gentan_no != ''){
                $gentan_no = "AND ta.gentan_no='".$this->gentan_no."'";
            }

            $this->loss = DB::select("
            SELECT
                tdpg.ID AS ID,
                tdpg.production_no AS production_no,
                tdpg.production_date AS production_date,
                tdpg.employee_id AS employee_id,
                tdpg.employee_id_infure AS employee_id_infure,
                tdpg.work_shift AS work_shift,
                tdpg.work_hour AS work_hour,
                tdpg.machine_id AS machine_id,
                tdpg.lpk_id AS lpk_id,
                tdpg.product_id AS product_id,
                tdpg.qty_produksi AS qty_produksi,
                tdpg.seitai_berat_loss AS seitai_berat_loss,
                tdpg.infure_berat_loss AS infure_berat_loss,
                tdpg.nomor_palet AS nomor_palet,
                tdpg.nomor_lot AS nomor_lot,
                tdpg.seq_no AS seq_no,
                tdpg.status_production AS status_production,
                tdpg.status_warehouse AS status_warehouse,
                tdpg.kenpin_qty_loss AS kenpin_qty_loss,
                tdpg.kenpin_qty_loss_proses AS kenpin_qty_loss_proses,
                tdpg.created_by AS created_by,
                tdpg.created_on AS created_on,
                tdpg.updated_by AS updated_by,
                tdpg.updated_on AS updated_on,
                tdol.order_id AS order_id,
                tdol.lpk_no AS lpk_no,
                tdol.lpk_date AS lpk_date,
                tdol.panjang_lpk AS panjang_lpk,
                tdol.qty_gentan AS qty_gentan,
                tdol.qty_gulung AS qty_gulung,
                tdol.qty_lpk AS qty_lpk,
                tdol.total_assembly_qty AS total_assembly_qty 
            FROM
                tdproduct_goods AS tdpg
                INNER JOIN tdorderlpk AS tdol ON tdpg.lpk_id = tdol.id
                LEFT JOIN tdproduct_goods_assembly AS tga ON tga.product_goods_id = tdpg.id 
                LEFT JOIN tdproduct_assembly as ta on ta.id = tga.product_assembly_id
            $tglMasuk
            $tglKeluar
            $searchTerm
            $gentan_no
            $machineid
            ");
        } else {
            $tglMasuk = '';
            if (isset($this->tglMasuk) && $this->tglMasuk != '') {
                $tglMasuk = "WHERE tdpg.created_on >= '" . $this->tglMasuk . "'";
            }
            $tglKeluar = '';
            if (isset($this->tglKeluar) && $this->tglKeluar != '') {
                $tglKeluar = "AND tdpg.created_on <= '" . $this->tglKeluar . "'";
            }
            // produk
            $machineid = '';
            if(isset($this->machineid) && $this->machineid != ''){
                $machineid = "AND tdpg.machine_id='".$this->machineid."'";
            }
            // status
            $searchTerm = '';
            if (isset($this->searchTerm) && $this->searchTerm != '') {
                $searchTerm = "AND (tdol.lpk_no ilike '%" . $this->searchTerm . 
                "%' OR tdpg.production_no ilike '%" . $this->searchTerm . 
                "%' OR tdpg.product_id ilike '%" . $this->searchTerm .
                "%' OR tdpg.machine_id ilike '%" . $this->searchTerm . 
                "%')";
            }

            $this->loss = DB::select("
            SELECT 
                tdpg.id AS id, 
                tdpg.production_no AS production_no, 
                tdpg.production_date AS production_date, 
                tdpg.employee_id AS employee_id, 
                tdpg.employee_id_infure AS employee_id_infure, 
                tdpg.work_shift AS work_shift, 
                tdpg.work_hour AS work_hour, 
                tdpg.machine_id AS machine_id, 
                tdpg.lpk_id AS lpk_id, 
                tdpg.product_id AS product_id, 
                tdpg.qty_produksi AS qty_produksi, 
                tdpg.seitai_berat_loss AS seitai_berat_loss, 
                tdpg.infure_berat_loss AS infure_berat_loss, 
                tdpg.nomor_palet AS nomor_palet, 
                tdpg.nomor_lot AS nomor_lot, 
                tdpg.seq_no AS seq_no, 
                tdpg.status_production AS status_production, 
                tdpg.status_warehouse AS status_warehouse, 
                tdpg.kenpin_qty_loss AS kenpin_qty_loss, 
                tdpg.kenpin_qty_loss_proses AS kenpin_qty_loss_proses, 
                tdpg.created_by AS created_by, 
                tdpg.created_on AS created_on, 
                tdpg.updated_by AS updated_by, 
                tdpg.updated_on AS updated_on, 
                tdol.order_id AS order_id, 
                tdol.lpk_no AS lpk_no, 
                tdol.lpk_date AS lpk_date, 
                tdol.panjang_lpk AS panjang_lpk, 
                tdol.qty_gentan AS qty_gentan, 
                tdol.qty_gulung AS qty_gulung, 
                tdol.qty_lpk AS qty_lpk, 
                tdol.total_assembly_qty AS total_assembly_qty
            FROM tdProduct_Goods AS tdpg
                INNER JOIN tdOrderLpk AS tdol ON tdpg.lpk_id = tdol.id
            $tglMasuk
            $tglKeluar
            $searchTerm
            $machineid
            ");
        }
    }

    public function render()
    {
        return view('livewire.nippo-seitai.loss-seitai')->extends('layouts.master');
    }
}
