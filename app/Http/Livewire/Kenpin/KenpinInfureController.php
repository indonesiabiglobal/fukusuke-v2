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
    public $products;
    public $tglMasuk;
    public $tglKeluar;
    public $searchTerm;
    public $idProduct;
    public $lpk_no;
    public $status;

    public function mount()
    {
        $this->products = MsProduct::get();
        $this->tglMasuk = Carbon::now()->format('Y-m-d');
        $this->tglKeluar = Carbon::now()->format('Y-m-d');
    }

    public function search(){        
        $this->resetPage();
        $this->render();
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
        
        if (isset($this->tglMasuk) && $this->tglMasuk != "" && $this->tglMasuk != "undefined") {
            $data = $data->where('tdka.kenpin_date', '>=', $this->tglMasuk);
        }
        if (isset($this->tglKeluar) && $this->tglKeluar != "" && $this->tglKeluar != "undefined") {
            $data = $data->where('tdka.kenpin_date', '<=', $this->tglKeluar);
        }
        if (isset($this->lpk_no) && $this->lpk_no != "" && $this->lpk_no != "undefined") {
            $data = $data->where('tdol.lpk_no', 'ilike', "%{$this->lpk_no}%");
        }
        if (isset($this->searchTerm) && $this->searchTerm != "" && $this->searchTerm != "undefined") {
            $data = $data->where('tdka.kenpin_no', 'ilike', "%{$this->searchTerm}%");
        }
        if (isset($this->idProduct) && $this->idProduct['value'] != "" && $this->idProduct != "undefined") {
            $data = $data->where('tdol.product_id', $this->idProduct['value']);
        }
        // if (isset($this->lpk_no) && $this->lpk_no != "" && $this->lpk_no != "undefined") {
        //     $data = $data->where('tdol.lpk_no', 'ilike', "%{$this->lpk_no}%");
        // }
        if (isset($this->status) && $this->status['value'] != "" && $this->status != "undefined") {
            $data = $data->where('tdka.status_kenpin', $this->status['value']);
        }

        $data = $data->paginate();

        return view('livewire.kenpin.kenpin-infure',[
            'data' => $data
        ])->extends('layouts.master');
    }
}
