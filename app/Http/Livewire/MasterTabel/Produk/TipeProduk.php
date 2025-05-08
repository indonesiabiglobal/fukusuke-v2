<?php

namespace App\Http\Livewire\MasterTabel\Produk;

use App\Models\MsProductType;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;
use Livewire\Attributes\Session;

class TipeProduk extends Component
{
    use WithPagination, WithoutUrlPagination;
    protected $paginationTheme = 'bootstrap';
    protected $listeners = ['delete','edit'];
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
    public $statusIsVisible = false;
    public $idUpdate;
    public $idDelete;
    public $paginate = 10;
    #[Session]
    public $sortingTable;

    public $rules = [
        'code' => 'required|numeric|unique:msproduct_type,code',
        'name' => 'required',
        'product_group_id' => 'required|exists:msproduct_group,id',
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
        if (empty($this->sortingTable)) {
            $this->sortingTable = [[2, 'asc']];
        }
    }

    public function updateSortingTable($value)
    {
        $this->sortingTable = $value;
        $this->skipRender();
    }

    public function resetFields()
    {
        $this->code = '';
        $this->name = '';
        $this->product_group_id = '';
        $this->harga_sat_infure = '';
        $this->harga_sat_infure_loss = '';
        $this->harga_sat_inline = '';
        $this->harga_sat_cetak = '';
        $this->harga_sat_seitai = '';
        $this->harga_sat_seitai_loss = '';
        $this->berat_jenis = '';
    }

    public function showModalCreate()
    {
        $this->resetFields();
        $this->dispatch('showModalCreate');
        // Mencegah render ulang
        $this->skipRender();
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
                'harga_sat_infure' => (float)str_replace(',', '', $this->harga_sat_infure),
                'harga_sat_infure_loss' => (float)str_replace(',', '', $this->harga_sat_infure_loss),
                'harga_sat_inline' => (float)str_replace(',', '', $this->harga_sat_inline),
                'harga_sat_cetak' => (float)str_replace(',', '', $this->harga_sat_cetak),
                'harga_sat_seitai' => (float)str_replace(',', '', $this->harga_sat_seitai),
                'harga_sat_seitai_loss' => (float)str_replace(',', '', $this->harga_sat_seitai_loss),
                'berat_jenis' => (float)str_replace(',', '', $this->berat_jenis),
                'status' => $statusActive,
                'created_by' => auth()->user()->username,
                'created_on' => Carbon::now(),
                'updated_by' => auth()->user()->username,
                'updated_on' => Carbon::now(),
                'trial464' => 'T',
            ]);

            DB::commit();
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Master Tipe Produk saved successfully.']);
            $this->dispatch('closeModalCreate');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to save master tipe produk: ' . $e->getMessage());
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Failed to save the Tipe Produk: ' . $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $typeProduct = MsProductType::where('id', $id)->first();
        $this->idUpdate = $id;
        $this->code = $typeProduct->code;
        $this->name = $typeProduct->name;
        $this->product_group_id = $typeProduct->product_group_id;
        $this->harga_sat_infure = number_format($typeProduct->harga_sat_infure, 2);
        $this->harga_sat_infure_loss = number_format($typeProduct->harga_sat_infure_loss, 2);
        $this->harga_sat_inline = number_format($typeProduct->harga_sat_inline, 2);
        $this->harga_sat_cetak = number_format($typeProduct->harga_sat_cetak, 2);
        $this->harga_sat_seitai = number_format($typeProduct->harga_sat_seitai, 2);
        $this->harga_sat_seitai_loss = number_format($typeProduct->harga_sat_seitai_loss, 2);
        $this->berat_jenis = number_format($typeProduct->berat_jenis, 2);
        $this->status = $typeProduct->status;
        $this->statusIsVisible = $typeProduct->status == 0 ? true : false;
        $this->skipRender();

        $this->dispatch('showModalUpdate');
    }

    public function update()
    {
        $this->validate([
            'code' => 'required|numeric|unique:msproduct_type,code,' . $this->idUpdate,
            'name' => 'required',
            'product_group_id' => 'required|exists:msproduct_group,id',
            'harga_sat_infure' => 'required',
            'harga_sat_infure_loss' => 'required',
            'harga_sat_inline' => 'required',
            'harga_sat_cetak' => 'required',
            'harga_sat_seitai' => 'required',
            'harga_sat_seitai_loss' => 'required',
            'berat_jenis' => 'required',
        ]);

        try {
            DB::beginTransaction();

            MsProductType::where('id', $this->idUpdate)->update([
                'code' => $this->code,
                'name' => $this->name,
                'product_group_id' => $this->product_group_id,
                'harga_sat_infure' => (float)str_replace(',', '', $this->harga_sat_infure),
                'harga_sat_infure_loss' => (float)str_replace(',', '', $this->harga_sat_infure_loss),
                'harga_sat_inline' => (float)str_replace(',', '', $this->harga_sat_inline),
                'harga_sat_cetak' => (float)str_replace(',', '', $this->harga_sat_cetak),
                'harga_sat_seitai' => (float)str_replace(',', '', $this->harga_sat_seitai),
                'harga_sat_seitai_loss' => (float)str_replace(',', '', $this->harga_sat_seitai_loss),
                'berat_jenis' => (float)str_replace(',', '', $this->berat_jenis),
                'status' => $this->status,
                'updated_by' => auth()->user()->username,
                'updated_on' => Carbon::now(),
                'trial464' => 'T',
            ]);

            DB::commit();
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Master Tipe Produk updated successfully.']);
            $this->dispatch('closeModalUpdate');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->skipRender();
            Log::error('Failed to update master tipe produk: ' . $e->getMessage());
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Failed to update the Tipe Produk: ' . $e->getMessage()]);
        }
    }

    public function delete($id)
    {
        $this->idDelete = $id;
        $this->dispatch('showModalDelete');
        // Mencegah render ulang
        $this->skipRender();
    }

    public function destroy()
    {
        try {
            DB::beginTransaction();
            $statusInactive = 0;
            MsProductType::where('id', $this->idDelete)->update([
                'status' => $statusInactive,
                'updated_by' => auth()->user()->username,
                'updated_on' => Carbon::now(),
            ]);
            DB::commit();
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Master Tipe Produk deleted successfully.']);
            $this->dispatch('closeModalDelete');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete master tipe produk: ' . $e->getMessage());
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Failed to delete the Tipe Produk: ' . $e->getMessage()]);
        }
    }

    public function render()
    {
        $data = DB::table('msproduct_type as mspt')
            ->select(
                'mspt.id',
                'mspt.code',
                'mspt.name',
                'mspt.product_group_id',
                'msproduct_group.name as jenisproduk',
                'mspt.harga_sat_infure',
                'mspt.harga_sat_infure_loss',
                'mspt.harga_sat_inline',
                'mspt.harga_sat_cetak',
                'mspt.harga_sat_seitai',
                'mspt.harga_sat_seitai_loss',
                'mspt.berat_jenis',
                'mspt.status',
                'mspt.updated_by',
                'mspt.updated_on'
            )
            ->Join('msproduct_group', 'msproduct_group.id', 'mspt.product_group_id')
            ->get();

        $productGroups = DB::select("SELECT id, name FROM msproduct_group");

        return view('livewire.master-tabel.produk.tipe-produk', [
            'data' => $data,
            'productGroups' => $productGroups
        ])->extends('layouts.master');
    }

    public function rendered()
    {
        $this->dispatch('initDataTable');
    }
}
