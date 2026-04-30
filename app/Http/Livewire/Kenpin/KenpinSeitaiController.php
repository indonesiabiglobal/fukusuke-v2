<?php

namespace App\Http\Livewire\Kenpin;

use App\Models\MsProduct;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Livewire\WithoutUrlPagination;
use Livewire\Attributes\Session;

class KenpinSeitaiController extends Component
{
    use WithPagination, WithoutUrlPagination;

    public $products;
    #[Session]
    public $tglMasuk;
    #[Session]
    public $tglKeluar;
    #[Session]
    public $status;
    #[Session]
    public $searchTerm;
    #[Session]
    public $idProduct;
    #[Session]
    public $lpk_no;
    #[Session]
    public $nomor_palet;
    #[Session]
    public $nomor_lot;
    #[Session]
    public $sortingTable;


    public function mount()
    {
        $this->products = MsProduct::active()
            ->orderBy('code_alias', 'ASC')
            ->orderBy('name', 'ASC')->get();
        if (empty($this->tglMasuk)) {
            $this->tglMasuk = Carbon::now()->format('d-m-Y');
        }
        if (empty($this->tglKeluar)) {
            $this->tglKeluar = Carbon::now()->format('d-m-Y');
        }
        if (empty($this->sortingTable)) {
            $this->sortingTable = [[1, 'asc']];
        }
    }

    public function updateSortingTable($value)
    {
        $this->sortingTable = $value;
        $this->skipRender();
    }

    public function search()
    {
        $this->render();
    }

    public function render()
    {
        // aggregate goods per kenpin to avoid duplicate tdkg rows
        $pg = DB::table('tdkenpin_goods_detail AS tdkgd')
            ->join('tdproduct_goods AS tdpg', 'tdkgd.product_goods_id', '=', 'tdpg.id')
            ->join('tdorderlpk AS tdol', 'tdpg.lpk_id', '=', 'tdol.id')
            ->select(
                'tdkgd.kenpin_id',
                DB::raw('MIN(tdpg.nomor_palet) AS nomor_palet'),
                DB::raw('MIN(tdpg.nomor_lot) AS nomor_lot'),
                DB::raw('MIN(tdol.lpk_no) AS lpk_no')
            )
            ->groupBy('tdkgd.kenpin_id');

        $data = DB::table('tdkenpin AS tdkg')
            ->leftJoinSub($pg, 'pg', function ($join) {
                $join->on('pg.kenpin_id', '=', 'tdkg.id');
            })
            ->join('msdepartment AS msd', 'msd.id', '=', 'tdkg.department_id')
            ->join('msproduct AS msp', 'tdkg.product_id', '=', 'msp.id')
            ->join('msemployee AS mse', 'mse.id', '=', 'tdkg.employee_id')
            ->select(
                'tdkg.id',
                'tdkg.kenpin_no',
                'tdkg.kenpin_date',
                'tdkg.employee_id',
                'tdkg.product_id',
                'tdkg.qty_loss',
                'tdkg.status_kenpin',
                'tdkg.created_by',
                'tdkg.created_on',
                'tdkg.updated_by',
                'tdkg.updated_on',
                'msp.code',
                'msp.name AS namaproduk',
                'mse.empname AS namapetugas',
                'msd.name AS nama_department',
                'pg.nomor_palet',
                'pg.nomor_lot'
            );

        if (isset($this->tglMasuk) && $this->tglMasuk != "" && $this->tglMasuk != "undefined") {
            $data = $data->where('tdkg.kenpin_date', '>=', $this->tglMasuk . ' 00:00:00');
        }
        if (isset($this->tglKeluar) && $this->tglKeluar != "" && $this->tglKeluar != "undefined") {
            $data = $data->where('tdkg.kenpin_date', '<=', $this->tglKeluar . ' 23:59:59');
        }
        if (isset($this->lpk_no) && $this->lpk_no != "" && $this->lpk_no != "undefined") {
            $data = $data->where('tdol.lpk_no', 'ilike', "%{$this->lpk_no}%");
        }
        if (isset($this->searchTerm) && $this->searchTerm != "" && $this->searchTerm != "undefined") {
            $data = $data->where('tdkg.kenpin_no', 'ilike', "%{$this->searchTerm}%");
        }
        if (isset($this->idProduct) && $this->idProduct['value'] != "" && $this->idProduct != "undefined") {
            $data = $data->where('tdkg.product_id', $this->idProduct['value']);
        }
        if (isset($this->status) && $this->status['value'] != "" && $this->status != "undefined") {
            $data = $data->where('tdkg.status_kenpin', $this->status['value']);
        }
        $data = $data->get();

        return view('livewire.kenpin.kenpin-seitai', [
            'data' => $data
        ])->extends('layouts.master');
    }

    public function rendered()
    {
        $this->dispatch('initDataTable');
    }
}
