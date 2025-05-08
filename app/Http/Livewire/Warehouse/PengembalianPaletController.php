<?php

namespace App\Http\Livewire\Warehouse;

use App\Models\MsProduct;
use Carbon\Carbon;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

use Livewire\Attributes\Session;

class PengembalianPaletController extends Component
{
    public $data = [];
    public $tglMasuk;
    public $tglKeluar;
    public $machine;
    public $transaksi;
    public $nomor_palet;
    public $products;
    public $product_id;
    #[Session]
    public $sortingTable;

    public function mount()
    {
        $this->products = MsProduct::get();
        $this->tglMasuk = Carbon::now()->format('Y-m-d');
        $this->tglKeluar = Carbon::now()->format('Y-m-d');
        if (empty($this->sortingTable)) {
            $this->sortingTable = [[2, 'asc']];
        }
    }

    public function updateSortingTable($value)
    {
        $this->sortingTable = $value;
        $this->skipRender();
    }

    public function search()
    {
        $product_id = '';
        if (isset($this->product_id) && $this->product_id) {
            $product_id = "AND msp.id = '" . $this->product_id . "'";
        }
        $nomor_palet = '';
        if (isset($this->nomor_palet) && $this->nomor_palet) {
            $nomor_palet = "WHERE tdpg.nomor_palet = '" . $this->nomor_palet . "'";
        }
        // $searchTerm = '';
        // if (isset($this->searchTerm) && $this->searchTerm != '') {
        //     $searchTerm = " WHERE tdpg.nomor_palet ilike '%" . $this->searchTerm .
        //     "%'";
        // }

        $this->data = DB::select("
        SELECT
            X.product_id,
            X.nomor_palet,
            X.code,
            X.name
        FROM
            (
            SELECT DISTINCT
                tdpg.product_id,
                tdpg.nomor_palet,
                msp.code,
                msp.name
            FROM
                tdProduct_Goods AS tdpg
                INNER JOIN msproduct as msp on msp.id = tdpg.product_id
            $nomor_palet
            $product_id
            ) AS X
        ");
    }

    public function render()
    {
        return view('livewire.warehouse.pengembalian-palet')->extends('layouts.master');
    }

    public function rendered()
    {
        $this->dispatch('initDataTable');
    }
}
