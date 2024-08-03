<?php

namespace App\Http\Livewire\NippoInfure;

use Livewire\Component;
use Carbon\Carbon;
use App\Models\MsProduct;
use App\Models\MsBuyer;
use App\Models\MsMachine;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;

class LossInfureController extends Component
{
    protected $paginationTheme = 'bootstrap';
    public $products;
    public $buyer;
    public $machine;
    public $tglMasuk;
    public $tglKeluar;
    public $transaksi;
    public $machineId;
    public $status;
    public $lpk_no;
    public $searchTerm;
    public $idProduct;

    use WithPagination, WithoutUrlPagination;

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
        $this->render();
    }

    public function print(){
        $tglMasuk = $this->tglMasuk;
        $tglKeluar = $this->tglKeluar;

        $this->dispatch('redirectToPrint', "'$tglMasuk 00:00' and tdpa.created_on <= '$tglKeluar 23:59'");
    }

    public function render()
    {
        $data = DB::table('tdproduct_assembly AS tdpa')
        ->select([
            'tdpa.id AS id',
            'tdpa.production_no AS production_no',
            'tdpa.production_date AS production_date',
            'tdpa.employee_id AS employee_id',
            'tdpa.work_shift AS work_shift',
            'tdpa.work_hour AS work_hour',
            'tdpa.machine_id AS machine_id',
            'tdpa.lpk_id AS lpk_id',
            'tdpa.product_id AS product_id',
            'tdpa.panjang_produksi AS panjang_produksi',
            'tdpa.panjang_printing_inline AS panjang_printing_inline',
            'tdpa.berat_standard AS berat_standard',
            'tdpa.berat_produksi AS berat_produksi',
            'tdpa.nomor_han AS nomor_han',
            'tdpa.gentan_no AS gentan_no',
            'tdpa.seq_no AS seq_no',
            'tdpa.status_production AS status_production',
            'tdpa.status_kenpin AS status_kenpin',
            'tdpa.infure_cost AS infure_cost',
            'tdpa.infure_cost_printing AS infure_cost_printing',
            'tdpa.infure_berat_loss AS infure_berat_loss',
            'tdpa.kenpin_berat_loss AS kenpin_berat_loss',
            'tdpa.kenpin_meter_loss AS kenpin_meter_loss',
            'tdpa.kenpin_meter_loss_proses AS kenpin_meter_loss_proses',
            'tdpa.created_by AS created_by',
            'tdpa.created_on AS created_on',
            'tdpa.updated_by AS updated_by',
            'tdpa.updated_on AS updated_on',
            'tdol.order_id AS order_id',
            'tdol.lpk_no AS lpk_no',
            'tdol.lpk_date AS lpk_date',
            'tdol.panjang_lpk AS panjang_lpk',
            'tdol.qty_gentan AS qty_gentan',
            'tdol.qty_gulung AS qty_gulung',
            'tdol.qty_lpk AS qty_lpk',
            'tdol.total_assembly_line AS total_assembly_line',
            'tdol.total_assembly_qty AS total_assembly_qty',
        ])
        ->join('tdorderlpk AS tdol', 'tdpa.lpk_id', '=', 'tdol.id')
        ->join('msmachine AS msm', 'msm.id', '=', 'tdpa.machine_id');

        if($this->transaksi == 2){
            if (isset($this->tglMasuk) && $this->tglMasuk != "" && $this->tglMasuk != "undefined") {
                $data = $data->where('tdpa.production_date', '>=', $this->tglMasuk);
            }
    
            if (isset($this->tglKeluar) && $this->tglKeluar != "" && $this->tglKeluar != "undefined") {
                $data = $data->where('tdpa.production_date', '<=', $this->tglKeluar);
            }
        } else {
            if (isset($this->tglMasuk) && $this->tglMasuk != "" && $this->tglMasuk != "undefined") {
                $data = $data->where('tdpa.created_on', '>=', $this->tglMasuk);
            }
    
            if (isset($this->tglKeluar) && $this->tglKeluar != "" && $this->tglKeluar != "undefined") {
                $data = $data->where('tdpa.created_on', '<=', $this->tglKeluar);
            }
        }
        if (isset($this->machineId) && $this->machineId['value'] != "" && $this->machineId != "undefined") {            
            $data = $data->where('msm.id', $this->machineId['value']);
        }

        if (isset($this->lpk_no) && $this->lpk_no != "" && $this->lpk_no != "undefined") {
            $data = $data->where('tdol.lpk_no', 'ilike', "%{$this->lpk_no}%");
        }

        if (isset($this->idProduct) && $this->idProduct['value'] != "" && $this->idProduct != "undefined") {
            $data = $data->where('tdpa.product_id', $this->idProduct['value']);
        }

        if (isset($this->status) && $this->status['value'] != "" && $this->status != "undefined") {
            if ($this->status['value'] == 0) {
                $data->where('tdpa.status_production', 0)
                      ->where('tdpa.status_kenpin', 0);
            } elseif ($this->status['value'] == 1) {
                $data->where('tdpa.status_production', 1);
            } elseif ($this->status['value'] == 2) {
                $data->where('tdpa.status_kenpin', 1);
            }
        }
        
        if (isset($this->searchTerm) && $this->searchTerm != "" && $this->searchTerm != "undefined") {
            $data = $data->where(function($query) {
                $query->where('tdpa.production_no', 'ilike', "%{$this->searchTerm}%")
                      ->orWhere('tdpa.product_id', 'ilike', "%{$this->searchTerm}%")
                      ->orWhere('tdpa.machine_id', 'ilike', "%{$this->searchTerm}%")
                      ->orWhere('tdpa.nomor_han', 'ilike', "%{$this->searchTerm}%");
            });
        }
        $data = $data->paginate(8);

        return view('livewire.nippo-infure.loss-infure', [
            'data' => $data
        ])->extends('layouts.master');
    }
}
