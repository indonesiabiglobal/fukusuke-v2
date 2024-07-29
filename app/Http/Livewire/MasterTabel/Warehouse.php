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

    public function mount()
    {
        $this->warehouses = DB::table('mswarehouse')
            ->get(['id', 'name', 'city', 'country', 'description', 'province', 'address', 'status', 'updated_by', 'updated_on']);
    }

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
            $this->render();
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
        $this->dispatch('showModalUpdate');
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
                'updated_by' => auth()->user()->username,
                'updated_on' => now(),
            ]);
            DB::commit();
            $this->resetFields();
            $this->render();
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
            $this->render();
            $this->dispatch('closeModalDelete');
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Warehouse deleted successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Failed to delete warehouse.']);
        }
    }

    public function search()
    {
        $this->resetPage();
        $this->render();
    }

    public function render()
    {
        $this->warehouses = DB::table('mswarehouse')
            ->select('id', 'name', 'city', 'address', 'status', 'updated_by', 'updated_on')
            ->when(isset($this->searchTerm) && $this->searchTerm != "" && $this->searchTerm != "undefined", function ($query) {
                $query->where(function ($query) {
                    $query->where('name', 'ilike', '%' . $this->searchTerm . '%')
                        ->orWhere('city', 'ilike', '%' . $this->searchTerm . '%')
                        ->orWhere('country', 'ilike', '%' . $this->searchTerm . '%')
                        ->orWhere('description', 'ilike', '%' . $this->searchTerm . '%')
                        ->orWhere('province', 'ilike', '%' . $this->searchTerm . '%')
                        ->orWhere('address', 'ilike', '%' . $this->searchTerm . '%');
                });
            })
            // ->paginate(2);
        ->get();

        return view('livewire.master-tabel.warehouse', [
            'data' => $this->warehouses,
        ])->extends('layouts.master');
    }
}
