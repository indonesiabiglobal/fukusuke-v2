<?php

namespace App\Http\Livewire\NippoSeitai;

use App\Exports\NippoSeitaiExport;
use Livewire\Component;
use Carbon\Carbon;
use App\Models\MsProduct;
use App\Models\MsBuyer;
use App\Models\MsMachine;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;
use Maatwebsite\Excel\Facades\Excel;

class NippoSeitaiController extends Component
{
    protected $paginationTheme = 'bootstrap';
    public $products;
    public $buyer;
    public $tglMasuk;
    public $tglKeluar;
    public $machine;
    public $transaksi;
    public $gentan_no;
    public $machineid;
    public $searchTerm;
    public $lpk_no;
    public $idProduct;
    public $status;

    use WithPagination, WithoutUrlPagination;

    public function mount()
    {
        $this->products = MsProduct::get();
        $this->buyer = MsBuyer::get();
        $this->machine = MsMachine::get();
        $this->tglMasuk = Carbon::now()->format('d-m-Y');
        $this->tglKeluar = Carbon::now()->format('d-m-Y'); 
    }

    public function add()
    {
        return redirect()->route('add-order');
    }

    public function search(){
        $this->render();
    }

    public function print()
    {
        return Excel::download(new NippoSeitaiExport(
            $this->tglMasuk,
            $this->tglKeluar,
            // $this->searchTerm,
            // $this->idProduct,
            // $this->idBuyer,
            // $this->status,
        ), 'NippoSeitai-CheckList.xlsx');
    }

    public function render()
    {
        if($this->transaksi == 2){
            $data = DB::table('tdproduct_goods AS tdpg')
            ->select(
                'tdpg.id AS id',
                'tdpg.production_no AS production_no',
                'tdpg.production_date AS production_date',
                'tdpg.employee_id AS employee_id',
                'tdpg.employee_id_infure AS employee_id_infure',
                'tdpg.work_shift AS work_shift',
                'tdpg.work_hour AS work_hour',
                'tdpg.machine_id AS machine_id',
                'tdpg.lpk_id AS lpk_id',
                'tdpg.product_id AS product_id',
                'tdpg.qty_produksi AS qty_produksi',
                'tdpg.seitai_berat_loss AS seitai_berat_loss',
                'tdpg.infure_berat_loss AS infure_berat_loss',
                'tdpg.nomor_palet AS nomor_palet',
                'tdpg.nomor_lot AS nomor_lot',
                'tdpg.seq_no AS seq_no',
                'tdpg.status_production AS status_production',
                'tdpg.status_warehouse AS status_warehouse',
                'tdpg.kenpin_qty_loss AS kenpin_qty_loss',
                'tdpg.kenpin_qty_loss_proses AS kenpin_qty_loss_proses',
                'tdpg.created_by AS created_by',
                'tdpg.created_on AS created_on',
                'tdpg.updated_by AS updated_by',
                'tdpg.updated_on AS updated_on',
                'tdol.order_id AS order_id',
                'tdol.lpk_no AS lpk_no',
                'tdol.lpk_date AS lpk_date',
                'tdol.panjang_lpk AS panjang_lpk',
                'tdol.qty_gentan AS qty_gentan',
                'tdol.qty_gulung AS qty_gulung',
                'tdol.qty_lpk AS qty_lpk',
                'tdol.total_assembly_qty AS total_assembly_qty',
            )
            ->join('tdorderlpk AS tdol', 'tdpg.lpk_id', '=', 'tdol.id')
            ->leftJoin('tdproduct_goods_assembly AS tga', 'tga.product_goods_id', '=', 'tdpg.id')
            ->leftJoin('tdproduct_assembly AS ta', 'ta.id', '=', 'tga.product_assembly_id');
            if (isset($this->tglMasuk) && $this->tglMasuk != '') {
                $data = $data->where('tdpg.production_date', '>=', $this->tglMasuk);
            }            
            if (isset($this->tglKeluar) && $this->tglKeluar != '') {
                $data = $data->where('tdpg.production_date', '<=', $this->tglKeluar);
            }
            if (isset($this->lpk_no) && $this->lpk_no != "" && $this->lpk_no != "undefined") {
                $data = $data->where('tdol.lpk_no', 'ilike', "%{$this->lpk_no}%");
            }
            if (isset($this->searchTerm) && $this->searchTerm != '') {
                $data = $data->where(function($query) {
                    $query->where('tdol.lpk_no', 'ilike', '%' . $this->searchTerm . '%')
                             ->orWhere('tdpg.production_no', 'ilike', '%' . $this->searchTerm . '%')
                             ->orWhere('tdpg.product_id', 'ilike', '%' . $this->searchTerm . '%')
                             ->orWhere('tdpg.machine_id', 'ilike', '%' . $this->searchTerm . '%');
                });
            }
            if (isset($this->idProduct) && $this->idProduct['value'] != "" && $this->idProduct != "undefined") {
                $data = $data->where('tdpg.product_id', $this->idProduct['value']);
            }
            if (isset($this->machineid) && $this->machineid['value'] != "" && $this->machineid != "undefined") {
                $data = $data->where('tdpg.machine_id', $this->machineid['value']);
            }
            if (isset($this->gentan_no) && $this->gentan_no != "" && $this->gentan_no != "undefined") {
                $data = $data->where('ta.gentan_no', $this->gentan_no);
            }
            if (isset($this->status) && $this->status['value'] != "" && $this->status != "undefined") {
                if ($this->status['value'] == 0) {
                    $data->where('tdpg.status_production', 0)
                          ->where('tdpg.status_warehouse', 0);
                } elseif ($this->status['value'] == 1) {
                    $data->where('tdpg.status_production', 1);
                } elseif ($this->status['value'] == 2) {
                    $data->where('tdpg.status_warehouse', 1);
                }
            }
            $data = $data->paginate(8);
        } else {
            $data = DB::table('tdproduct_goods AS tdpg')
            ->select([
                'tdpg.id AS id',
                'tdpg.production_no AS production_no',
                'tdpg.production_date AS production_date',
                'tdpg.employee_id AS employee_id',
                'tdpg.employee_id_infure AS employee_id_infure',
                'tdpg.work_shift AS work_shift',
                'tdpg.work_hour AS work_hour',
                'tdpg.machine_id AS machine_id',
                'tdpg.lpk_id AS lpk_id',
                'tdpg.product_id AS product_id',
                'tdpg.qty_produksi AS qty_produksi',
                'tdpg.seitai_berat_loss AS seitai_berat_loss',
                'tdpg.infure_berat_loss AS infure_berat_loss',
                'tdpg.nomor_palet AS nomor_palet',
                'tdpg.nomor_lot AS nomor_lot',
                'tdpg.seq_no AS seq_no',
                'tdpg.status_production AS status_production',
                'tdpg.status_warehouse AS status_warehouse',
                'tdpg.kenpin_qty_loss AS kenpin_qty_loss',
                'tdpg.kenpin_qty_loss_proses AS kenpin_qty_loss_proses',
                'tdpg.created_by AS created_by',
                'tdpg.created_on AS created_on',
                'tdpg.updated_by AS updated_by',
                'tdpg.updated_on AS updated_on',
                'tdol.order_id AS order_id',
                'tdol.lpk_no AS lpk_no',
                'tdol.lpk_date AS lpk_date',
                'tdol.panjang_lpk AS panjang_lpk',
                'tdol.qty_gentan AS qty_gentan',
                'tdol.qty_gulung AS qty_gulung',
                'tdol.qty_lpk AS qty_lpk',
                'tdol.total_assembly_qty AS total_assembly_qty',
            ])
            ->join('tdorderlpk AS tdol', 'tdpg.lpk_id', '=', 'tdol.id')
            ->leftJoin('tdproduct_goods_assembly AS tga', 'tga.product_goods_id', '=', 'tdpg.id')
            ->leftJoin('tdproduct_assembly AS ta', 'ta.id', '=', 'tga.product_assembly_id');
            
            if (isset($this->tglMasuk) && $this->tglMasuk != '') {
                $data = $data->where('tdpg.production_date', '>=', $this->tglMasuk);
            }
            if (isset($this->tglKeluar) && $this->tglKeluar != '') {
                $data = $data->where('tdpg.production_date', '<=', $this->tglKeluar);
            }
            if (isset($this->lpk_no) && $this->lpk_no != "" && $this->lpk_no != "undefined") {
                $data = $data->where('tdol.lpk_no', 'ilike', "%{$this->lpk_no}%");
            }
            if (isset($this->searchTerm) && $this->searchTerm != '') {
                $data = $data->where(function($query) {
                    $query->where('tdol.lpk_no', 'ilike', '%' . $this->searchTerm . '%')
                            ->orWhere('tdpg.production_no', 'ilike', '%' . $this->searchTerm . '%')
                            ->orWhere('tdpg.product_id', 'ilike', '%' . $this->searchTerm . '%')
                            ->orWhere('tdpg.machine_id', 'ilike', '%' . $this->searchTerm . '%');
                });
            }
            if (isset($this->idProduct) && $this->idProduct['value'] != "" && $this->idProduct != "undefined") {
                $data = $data->where('tdpg.product_id', $this->idProduct['value']);
            }            
            if (isset($this->machineid) && $this->machineid['value'] != "" && $this->machineid != "undefined") {
                $data = $data->where('tdpg.machine_id', $this->machineid['value']);
            }
            if (isset($this->gentan_no) && $this->gentan_no != '') {
                $data = $data->where('ta.gentan_no', $this->gentan_no);
            }
            if (isset($this->status) && $this->status['value'] != "" && $this->status != "undefined") {
                if ($this->status['value'] == 0) {
                    $data->where('tdpg.status_production', 0)
                          ->where('tdpg.status_warehouse', 0);
                } elseif ($this->status['value'] == 1) {
                    $data->where('tdpg.status_production', 1);
                } elseif ($this->status['value'] == 2) {
                    $data->where('tdpg.status_warehouse', 1);
                }
            }

            $data = $data->paginate(8);
        }
        return view('livewire.nippo-seitai.nippo-seitai', [
            'data' => $data,
        ])->extends('layouts.master');
    }
}
