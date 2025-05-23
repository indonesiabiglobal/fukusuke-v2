<?php

namespace App\Http\Livewire\MasterTabel\Produk;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;
use Livewire\Attributes\Session;

class JenisProduk extends Component
{
    use WithPagination, WithoutUrlPagination;
    protected $paginationTheme = 'bootstrap';
    protected $listeners = ['delete','edit'];
    public $groupProducts;
    public $searchTerm;
    public $code;
    public $name;
    public $idUpdate;
    public $idDelete;
    public $status;
    public $statusIsVisible = false;
    #[Session]
    public $sortingTable;

    // public function mount()
    // {
    //     $this->groupProducts = DB::table('msproduct_group')
    //         ->where('status', 1)
    //         ->get(['id', 'code', 'name', 'status', 'updated_by', 'updated_on']);
    // }

    public function resetFields()
    {
        $this->code = '';
        $this->name = '';
    }
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

    public function showModalCreate()
    {
        $this->resetFields();
        $this->dispatch('showModalCreate');
        // Mencegah render ulang
        $this->skipRender();
    }

    public function store()
    {
        $this->validate([
            'code' => 'required|unique:msproduct_group,code',
            'name' => 'required',
        ]);

        DB::beginTransaction();
        try {
            $statusActive = 1;
            DB::table('msproduct_group')->insert([
                'code' => $this->code,
                'name' => $this->name,
                'status' => $statusActive,
                'created_by' => auth()->user()->username,
                'created_on' => Carbon::now(),
                'updated_by' => auth()->user()->username,
                'updated_on' => Carbon::now(),
            ]);
            DB::commit();
            $this->resetFields();
            $this->dispatch('closeModalCreate');
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Master Group Product saved successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to save master Group Product: ' . $e->getMessage());
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Failed to save the Group Product: ' . $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $groupProduct = DB::table('msproduct_group')->where('id', $id)->first();
        $this->idUpdate = $id;
        $this->code = $groupProduct->code;
        $this->name = $groupProduct->name;
        $this->status = $groupProduct->status;
        $this->statusIsVisible = $groupProduct->status == 0 ? true : false;
        $this->skipRender();
        $this->dispatch('showModalUpdate');
    }

    public function update()
    {
        $this->validate([
            'code' => 'required|unique:msproduct_group,code,' . $this->idUpdate,
            'name' => 'required',
        ]);

        DB::beginTransaction();
        try {
            DB::table('msproduct_group')->where('id', $this->idUpdate)->update([
                'code' => $this->code,
                'name' => $this->name,
                'status' => $this->status,
                'updated_by' => auth()->user()->username,
                'updated_on' => Carbon::now(),
            ]);
            DB::commit();
            $this->resetFields();
            $this->dispatch('closeModalUpdate');
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Master Group Product updated successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to save master Group Product: ' . $e->getMessage());
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Failed to save the Group Product: ' . $e->getMessage()]);
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
        DB::beginTransaction();
        try {
            $statusInactive = 0;

            DB::table('msproduct_group')->where('id', $this->idDelete)->update([
                'status' => $statusInactive,
                'updated_by' => auth()->user()->username,
                'updated_on' => Carbon::now(),
            ]);
            DB::commit();
            $this->dispatch('closeModalDelete');
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Master Group Product deleted successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete master Group Product: ' . $e->getMessage());
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Failed to delete the Group Product: ' . $e->getMessage()]);
        }
    }

    public function render()
    {
        $data = DB::table('msproduct_group')
            ->get();

        return view('livewire.master-tabel.produk.jenis-produk', [
            'data' => $data
        ])->extends('layouts.master');
    }

    public function rendered()
    {
        $this->dispatch('initDataTable');
    }
}
