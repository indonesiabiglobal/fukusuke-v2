<?php

namespace App\Http\Livewire\MasterTabel\Produk;

use App\Models\MsProductType;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;

class TipeProduk extends Component
{
    use WithPagination, WithoutUrlPagination;
    protected $paginationTheme = 'bootstrap';
    public $types = [];
    public $searchTerm = '';

    public $code;
    public $name;
    public $product_group_id;
    public $harga_sat_infure;
    public $harga_sat_infure_loss;
    public $harga_sat_inline;
    public $harga_sat_cetak;
    public $harga_sat_seitai;
    public $harga_sat_seitai_loss;
    public $berat_jenis;
    public $status;

    public $rules = [
        'code' => 'required',
        'name' => 'required',
        'product_group_id' => 'required',
        'harga_sat_infure' => 'required',
        'harga_sat_infure_loss' => 'required',
        'harga_sat_inline' => 'required',
        'harga_sat_cetak' => 'required',
        'harga_sat_seitai' => 'required',
        'harga_sat_seitai_loss' => 'required',
        'berat_jenis' => 'required',
    ];

    public function mount()
    {
        $this->search();
    }

    public function search()
    {
        // $searchTerm = '';
        // if (isset($this->searchTerm) && $this->searchTerm != '') {
        //     $searchTerm = "where (pt.code ilike '%" . $this->searchTerm .
        //         "%' OR pt.name ilike '%" . $this->searchTerm .
        //         "%' OR  pg.name ilike '%" . $this->searchTerm .
        //         "%'
        //     )";
        // }

        // $this->types = DB::select("
        //     SELECT pt.id,pt.code,pt.name,pt.product_group_id,pg.name as jenisproduk, pt.harga_sat_infure,
        //     pt.harga_sat_infure_loss,pt.harga_sat_inline,
        //     pt.harga_sat_cetak,pt.berat_jenis,pt.status
        //     from msproduct_type as pt
        //     inner join msproduct_group as pg on pt.product_group_id=pg.id
        //     $searchTerm
        // ");

        $this->render();
    }

    public function store()
    {
        $this->validate();

        try {
            DB::beginTransaction();
            $statusActive = 1;

            MsProductType::create([
                'code' => $this->code,
                'name' => $this->name,
                'product_group_id' => $this->product_group_id,
                'harga_sat_infure' => $this->harga_sat_infure,
                'harga_sat_infure_loss' => $this->harga_sat_infure_loss,
                'harga_sat_inline' => $this->harga_sat_inline,
                'harga_sat_cetak' => $this->harga_sat_cetak,
                'harga_sat_seitai' => $this->harga_sat_seitai,
                'harga_sat_seitai_loss' => $this->harga_sat_seitai_loss,
                'berat_jenis' => $this->berat_jenis,
                'status' => $statusActive,
                'created_by' => auth()->user()->username,
                'created_on' => Carbon::now(),
                'updated_by' => auth()->user()->username,
                'updated_on' => Carbon::now(),
                'trial464' => 'T',
            ]);

            DB::commit();
            $this->search();
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Master Tipe Produk saved successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to save master tipe produk: ' . $e->getMessage());
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Failed to save the Tipe Produk: ' . $e->getMessage()]);
        }
    }
    public function render()
    {
        $data = MsProductType::select(
            'msproduct_type.id',
            'msproduct_type.code',
            'msproduct_type.name',
            'msproduct_type.product_group_id',
            'msproduct_group.name as jenisproduk',
            'msproduct_type.harga_sat_infure',
            'msproduct_type.harga_sat_infure_loss',
            'msproduct_type.harga_sat_inline',
            'msproduct_type.harga_sat_cetak',
            'msproduct_type.harga_sat_seitai',
            'msproduct_type.harga_sat_seitai_loss',
            'msproduct_type.berat_jenis',
            'msproduct_type.status'
        )
            ->Join('msproduct_group', 'msproduct_group.id', 'msproduct_type.product_group_id')
            ->where(function ($query) {
                $query->where('msproduct_type.code', 'ilike', '%' . $this->searchTerm . '%')
                    ->orWhere('msproduct_type.name', 'ilike', '%' . $this->searchTerm . '%')
                    ->orWhere('msproduct_type.product_group_id', 'ilike', '%' . $this->searchTerm . '%');
            })
            ->paginate(10);

        $productGroups = DB::select("SELECT id, name FROM msproduct_group");

        return view('livewire.master-tabel.produk.tipe-produk', [
            'data' => $data,
            'productGroups' => $productGroups
        ])->extends('layouts.master');
    }
}
