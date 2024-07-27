<?php

namespace App\Http\Livewire\Kenpin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;
use Carbon\Carbon;
use App\Models\MsProduct;
use Illuminate\Support\Facades\DB;

class KenpinInfureController extends Component
{
    use WithPagination, WithoutUrlPagination;
    
    protected $paginationTheme = 'bootstrap';
    public $product;
    // public $buyer;
    public $tglMasuk;
    public $tglKeluar;

    public function mount()
    {
        $this->product = MsProduct::limit(10)->get();
        $this->tglMasuk = Carbon::now()->format('Y-m-d');
        $this->tglKeluar = Carbon::now()->format('Y-m-d');
    }

    public function search(){        
        $this->resetPage();
        $this->render();
        // $tglMasuk = '';
        // if (isset($this->tglMasuk) && $this->tglMasuk != '') {
        //     $tglMasuk = "WHERE tdka.kenpin_date >= '" . $this->tglMasuk . "'";
        // }
        // $tglKeluar = '';
        // if (isset($this->tglKeluar) && $this->tglKeluar != '') {
        //     $tglKeluar = "AND tdka.kenpin_date <= '" . $this->tglKeluar . "'";
        // }
        // $searchTerm = '';
        // if (isset($this->searchTerm) && $this->searchTerm != '') {
        //     $searchTerm = "AND (tdol.lpk_no ilike '%" . $this->searchTerm . 
        //     "%' OR tdka.kenpin_no ilike '%" . $this->searchTerm . 
        //     "%' OR msp.name ilike '%" . $this->searchTerm .
        //     "%')";
        // }

        // $this->data = DB::select("
        // SELECT 
        //     tdka.id AS id, 
        //     tdka.kenpin_no AS kenpin_no, 
        //     tdka.kenpin_date AS kenpin_date, 
        //     tdka.employee_id AS employee_id,
        //     mse.empname as namapetugas,
        //     tdka.lpk_id AS lpk_id, 
        //     tdka.berat_loss AS berat_loss, 
        //     tdka.remark AS remark, 
        //     CASE WHEN tdka.status_kenpin = 1 THEN 'Proses' ELSE 'Finish' END AS status_kenpin, 
        //     tdka.created_by AS created_by, 
        //     tdka.created_on AS created_on, 
        //     tdka.updated_by AS updated_by, 
        //     tdka.updated_on AS updated_on, 
        //     tdol.order_id AS order_id, 
        //     tdol.product_id AS product_id, 
        //     tdol.lpk_no AS lpk_no, 
        //     tdol.lpk_date AS lpk_date, 
        //     tdol.panjang_lpk AS panjang_lpk, 
        //     tdol.qty_gentan AS qty_gentan, 
        //     tdol.qty_gulung AS qty_gulung, 
        //     tdol.qty_lpk AS qty_lpk, 
        //     tdol.total_assembly_line AS total_assembly_line, 
        //     tdol.total_assembly_qty AS total_assembly_qty, 
        //     msp.id AS id1, 
        //     msp.code AS code, 
        //     msp.name AS namaproduk
        // FROM  tdKenpin_assembly AS tdka
        //     INNER JOIN tdOrderLpk AS tdol ON tdka.lpk_id = tdol.id
        //     INNER JOIN msProduct AS msp ON tdol.product_id = msp.id
        //     inner join msemployee as mse on mse.id=tdka.employee_id
        // $tglMasuk
        // $tglKeluar
        // $searchTerm
        // ");
    }

    public function add()
    {
        return redirect()->route('add-order');
    }

    public function render()
    {
        $data = DB::table('tdkenpin_assembly AS tdka')
            ->join('tdorderlpk AS tdol', 'tdka.lpk_id', '=', 'tdol.id')
            ->join('msproduct AS msp', 'tdol.product_id', '=', 'msp.id')
            ->join('msemployee AS mse', 'mse.id', '=', 'tdka.employee_id')
            ->select(
                'tdka.id', 
                'tdka.kenpin_no', 
                'tdka.kenpin_date', 
                'tdka.employee_id',
                'mse.empname',
                'tdka.lpk_id', 
                'tdka.berat_loss', 
                'tdka.remark', 
                DB::raw("CASE WHEN tdka.status_kenpin = 1 THEN 'Proses' ELSE 'Finish' END AS status_kenpin"), 
                'tdka.created_by', 
                'tdka.created_on', 
                'tdka.updated_by', 
                'tdka.updated_on', 
                'tdol.order_id', 
                'tdol.product_id', 
                'tdol.lpk_no', 
                'tdol.lpk_date', 
                'tdol.panjang_lpk', 
                'tdol.qty_gentan', 
                'tdol.qty_gulung', 
                'tdol.qty_lpk', 
                'tdol.total_assembly_line', 
                'tdol.total_assembly_qty', 
                'msp.id AS id1', 
                'msp.code', 
                'msp.name AS namaproduk'
            );
            
        if (isset($this->status) && $this->status['value'] != "" && $this->status != "undefined") {
            $data = $data->where('tod.status_order', $this->status);
        }

        $data = $data->paginate();

        return view('livewire.kenpin.kenpin-infure',[
            'data' => $data
        ])->extends('layouts.master');
    }
}
