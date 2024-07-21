<?php

namespace App\Http\Livewire\Kenpin;

use App\Models\MsProduct;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class KenpinSeitaiController extends Component
{
    use WithPagination;

    public $data=[];
    public $product;
    // public $buyer;
    public $tglMasuk;
    public $tglKeluar;
    public $status;

    public function mount()
    {
        $this->product = MsProduct::limit(10)->get();
        $this->tglMasuk = Carbon::now()->format('Y-m-d');
        $this->tglKeluar = Carbon::now()->format('Y-m-d');
    }

    public function search(){
        $tglMasuk = '';
        if (isset($this->tglMasuk) && $this->tglMasuk != '') {
            $tglMasuk = "tdkg.kenpin_date >= '" . $this->tglMasuk . " 00:00:00'";
        }
        $tglKeluar = '';
        if (isset($this->tglKeluar) && $this->tglKeluar != '') {
            $tglKeluar = "tdkg.kenpin_date <= '" . $this->tglKeluar . " 23:59:59'";
        }
        $status = '';
        if (isset($this->status) && $this->status != '') {
            $status = "AND tdkg.status_kenpin = '" . $this->status . "'";
        }
        $searchTerm = '';
        if (isset($this->searchTerm) && $this->searchTerm != '') {
            $searchTerm = "WHERE (tdpg.nomor_palet ilike '%" . $this->searchTerm . 
            "%' OR tdpg.nomor_lot ilike '%" . $this->searchTerm . 
            "%')";
        }

        $this->data = DB::select("
        SELECTa
            tdkg.id,
            tdkg.kenpin_no,
            tdkg.kenpin_date,
            tdkg.employee_id,
            tdkg.product_id,
            tdkg.qty_loss,
            tdkg.remark,
            tdkg.status_kenpin,
            tdkg.created_by,
            tdkg.created_on,
            tdkg.updated_by,
            tdkg.updated_on,
            msp.code,
            msp.NAME AS namaproduk,
            mse.empname AS namapetugas 
        FROM
            tdkenpin_goods AS tdkg
            INNER JOIN (
            SELECT DISTINCT
                tdkgd.kenpin_goods_id AS kenpin_goods_id 
            FROM
                tdkenpin_goods_detail AS tdkgd
                INNER JOIN tdproduct_goods AS tdpg ON tdkgd.product_goods_id = tdpg.
                ID INNER JOIN tdorderlpk AS tdol ON tdpg.lpk_id = tdol.ID 
            $searchTerm
            ) AS distinct1 ON tdkg.ID = distinct1.kenpin_goods_id
            INNER JOIN msproduct AS msp ON tdkg.product_id = msp.
            ID INNER JOIN msemployee AS mse ON mse.ID = tdkg.employee_id 
        WHERE
            $tglMasuk 
            AND $tglKeluar 
        -- 	AND tdkg.kenpin_no = '' 
        -- 	AND msp.ID = 
            $status
        ");
    }

    public function render()
    {
        return view('livewire.kenpin.kenpin-seitai')->extends('layouts.master');
    }
}
