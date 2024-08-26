<?php

namespace App\Http\Livewire\Kenpin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;
use Carbon\Carbon;
use App\Models\MsProduct;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Session;

class KenpinInfureController extends Component
{
    use WithPagination, WithoutUrlPagination;

    protected $paginationTheme = 'bootstrap';
    public $products;
    #[Session]
    public $tglMasuk;
    #[Session]
    public $tglKeluar;
    #[Session]
    public $searchTerm;
    #[Session]
    public $idProduct;
    #[Session]
    public $lpk_no;
    #[Session]
    public $status;
    #[Session]
    public $no_han;

    public function mount()
    {
        $this->products = MsProduct::get();
        if (empty($this->tglMasuk)) {
            $this->tglMasuk = Carbon::now()->format('d-m-Y');
        }
        if (empty($this->tglKeluar)) {
            $this->tglKeluar = Carbon::now()->format('d-m-Y');
        }
    }

    public function search()
    {
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
            ->leftJoin('tdkenpin_assembly_detail as tdkad', 'tdkad.kenpin_assembly_id', '=', 'tdka.id')
            ->leftJoin('tdproduct_assembly AS tdpa', 'tdpa.id', '=', 'tdkad.product_assembly_id')
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
                'msp.name AS namaproduk',
                'tdpa.nomor_han'
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
        if (isset($this->no_han) && $this->no_han != "" && $this->no_han != "undefined") {
            $data = $data->where('tdpa.nomor_han', 'ilike', "%{$this->no_han}%");
        }
        if (isset($this->status) && $this->status['value'] != "" && $this->status != "undefined") {
            $data = $data->where('tdka.status_kenpin', $this->status['value']);
        }

        $data = $data->get();

        return view('livewire.kenpin.kenpin-infure', [
            'data' => $data
        ])->extends('layouts.master');
    }

    public function rendered()
    {
        $this->dispatch('initDataTable');
    }
}
