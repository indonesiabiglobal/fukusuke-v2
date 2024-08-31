<?php

namespace App\Http\Livewire\MasterTabel;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;

class Warehouse extends Component
{
    use WithPagination, WithoutUrlPagination;
    protected $paginationTheme = 'bootstrap';
    protected $listeners = ['delete', 'edit'];
    public $warehouses;
    public $searchTerm;
    public $name;
    public $city;
    public $country;
    public $description;
    public $province;
    public $address;
    public $idUpdate;
    public $idDelete;
    public $status;
    public $statusIsVisible;

    public function resetFields()
    {
        $this->name = '';
        $this->city = '';
        $this->country = '';
        $this->description = '';
        $this->province = '';
        $this->address = '';
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
            'name' => 'required|unique:mswarehouse,name',
            'city' => 'required',
            'country' => 'required',
            'description' => 'required',
            'province' => 'required',
            'address' => 'required',
        ]);

        DB::beginTransaction();
        try {
            $statusActive = 1;
            DB::table('mswarehouse')->insert([
                'name' => $this->name,
                'city' => $this->city,
                'country' => $this->country,
                'description' => $this->description,
                'province' => $this->province,
                'address' => $this->address,
                'status' => $statusActive,
                'created_by' => auth()->user()->username,
                'created_on' => now(),
                'updated_by' => auth()->user()->username,
                'updated_on' => now(),
            ]);
            DB::commit();
            $this->resetFields();
            $this->dispatch('closeModalCreate');
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Warehouse created successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Failed to create warehouse.']);
        }
    }

    public function edit($id)
    {
        $warehouse = DB::table('mswarehouse')->where('id', $id)->first();
        $this->idUpdate = $id;
        $this->name = $warehouse->name;
        $this->city = $warehouse->city;
        $this->country = $warehouse->country;
        $this->description = $warehouse->description;
        $this->province = $warehouse->province;
        $this->address = $warehouse->address;
        $this->status = $warehouse->status;

        $this->statusIsVisible = $data->status == 0 ? true : false;
        $this->dispatch('showModalUpdate');
        $this->skipRender();
    }

    public function update()
    {
        $this->validate([
            'name' => 'required|unique:mswarehouse,name,' . $this->idUpdate,
            'city' => 'required',
            'country' => 'required',
            'description' => 'required',
            'province' => 'required',
            'address' => 'required',
        ]);

        DB::beginTransaction();
        try {
            DB::table('mswarehouse')->where('id', $this->idUpdate)->update([
                'name' => $this->name,
                'city' => $this->city,
                'country' => $this->country,
                'description' => $this->description,
                'province' => $this->province,
                'address' => $this->address,
                'status' => $this->status,
                'updated_by' => auth()->user()->username,
                'updated_on' => now(),
            ]);
            DB::commit();
            $this->resetFields();
            $this->dispatch('closeModalUpdate');
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Warehouse updated successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Failed to update warehouse.']);
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
            DB::table('mswarehouse')->where('id', $this->idDelete)->update([
                'status' => $statusInactive,
                'updated_by' => auth()->user()->username,
                'updated_on' => now(),
            ]);
            DB::commit();
            $this->dispatch('closeModalDelete');
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Warehouse deleted successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Failed to delete warehouse.']);
        }
    }

    public function render()
    {
        $this->warehouses = DB::table('mswarehouse')
            ->select('id', 'name', 'city', 'address', 'status', 'updated_by', 'updated_on')
            ->get();

        return view('livewire.master-tabel.warehouse', [
            'data' => $this->warehouses,
        ])->extends('layouts.master');
    }

    public function rendered()
    {
        $this->dispatch('initDataTable');
    }
}
