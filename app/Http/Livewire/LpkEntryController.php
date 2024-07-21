<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Carbon\Carbon;
use App\Models\MsProduct;
use App\Models\MsBuyer;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;

class LpkEntryController extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $products;
    public $buyer;
    public $tglMasuk;
    public $tglKeluar;
    public $searchTerm;
    public $transaksi;
    public $idBuyer;
    public $status;

    public function mount()
    {
        $this->products = MsProduct::get();
        $this->buyer = MsBuyer::get();
        $this->tglMasuk = Carbon::now()->format('Y-m-d');
        $this->tglKeluar = Carbon::now()->format('Y-m-d'); 
    }

    public function search(){
        $this->render();
    }

    public function add()
    {
        return redirect()->route('add-order');
    }

    public function render()
    {
        $data = DB::table('tdorderlpk AS tolp')
            ->selectRaw("
                tolp.id,
                tolp.lpk_no,
                tolp.lpk_date,
                tolp.panjang_lpk,
                tolp.qty_lpk,
                tolp.qty_gentan,
                tolp.qty_gulung,
                tolp.total_assembly_line AS infure,
                tolp.total_assembly_qty,
                tod.po_no,
                mp.NAME AS product_name,
                tod.product_code,
                mm.machineno AS machine_no,
                mbu.NAME AS buyer_name,
                tolp.created_on AS tglproses,
                tolp.seq_no,
                tolp.updated_by,
                tolp.updated_on AS updatedt
            ")
            ->join('tdorder AS tod', 'tod.id', '=', 'tolp.order_id')
            ->join('msproduct AS mp', 'mp.id', '=', 'tolp.product_id')
            ->join('msmachine AS mm', 'mm.id', '=', 'tolp.machine_id')
            ->join('msbuyer AS mbu', 'mbu.id', '=', 'tod.buyer_id');

        if($this->transaksi == 2){
            if (isset($this->tglMasuk) && $this->tglMasuk != "" && $this->tglMasuk != "undefined") {
                $data = $data->where('tolp.lpk_date', '>=', $this->tglMasuk);
            }
    
            if (isset($this->tglKeluar) && $this->tglKeluar != "" && $this->tglKeluar != "undefined") {
                $data = $data->where('tolp.lpk_date', '<=', $this->tglKeluar);
            }
        } else {
            if (isset($this->tglMasuk) && $this->tglMasuk != "" && $this->tglMasuk != "undefined") {
                $data = $data->where('tolp.created_on', '>=', $this->tglMasuk);
            }
    
            if (isset($this->tglKeluar) && $this->tglKeluar != "" && $this->tglKeluar != "undefined") {
                $data = $data->where('tolp.created_on', '<=', $this->tglKeluar);
            }
        }

        if (isset($this->searchTerm) && $this->searchTerm != "" && $this->searchTerm != "undefined") {
            $data = $data->where(function($query) {
                $query->where('mp.name', 'ilike', "%{$this->searchTerm}%")
                        ->orWhere('tolp.lpk_no', 'ilike', "%{$this->searchTerm}%")
                        ->orWhere('tod.po_no', 'ilike', "%{$this->searchTerm}%");
            });
        }

        if (isset($this->idProduct) && $this->idProduct != "" && $this->idProduct != "undefined") {
            $data = $data->where('mp.id', $this->idProduct);
        }

        if (isset($this->idBuyer) && $this->idBuyer != "" && $this->idBuyer != "undefined") {
            $data = $data->where('tod.buyer_id', $this->idBuyer);
        }

        if (isset($this->status) && $this->status != "" && $this->status != "undefined") {
            if ($this->status == 0){
                $data = $data->where('tolp.reprint_no', $this->status);
            } else if ($this->status == 1){
                $data = $data->where('tolp.reprint_no', $this->status);
            } else if ($this->status == 2){
                $data = $data->where('tolp.reprint_no', '>', 1);
            } else if ($this->status == 3){
                $data = $data->where('tolp.status_lpk', 0);
            } else if ($this->status == 4){
                $data = $data->where('tolp.status_lpk', 1);
            }
            
        }

        $data = $data->paginate(8);

        return view('livewire.order-lpk.lpk-entry', [
            'data' => $data,
        ])->extends('layouts.master');
    }
}
