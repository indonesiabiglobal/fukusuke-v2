<?php

namespace App\Http\Livewire\MasterTabel;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;
use Livewire\Attributes\Session;

class Machine extends Component
{
    use WithPagination, WithoutUrlPagination;
    protected $paginationTheme = 'bootstrap';
    protected $listeners = ['delete', 'edit'];
    public $machines;
    public $searchTerm;
    public $machineno;
    public $machinename;
    public $department_id;
    public $product_group_id;
    public $capacity_kg;
    public $capacity_lembar;
    public $capacity_size;
    public $idUpdate;
    public $idDelete;
    public $status;
    public $statusIsVisible = false;
    #[Session]
    public $sortingTable;


    public function mount()
    {
        //     $this->machines = DB::table('msmachine')
        //         ->get(['id', 'machinename', 'machineno', 'department_id', 'product_group_id', 'capacity_kg', 'capacity_lembar', 'status', 'updated_by', 'updated_on']);

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
        $this->machineno = '';
        $this->machinename = '';
        $this->department_id = '';
        $this->product_group_id = '';
        $this->capacity_kg = '';
        $this->capacity_lembar = '';
        $this->capacity_size = '';
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
            'machineno' => 'required|unique:msmachine,machineno',
            'machinename' => 'required',
            'department_id' => 'required',
        ]);

        DB::beginTransaction();
        try {
            $statusActive = 1;
            DB::table('msmachine')->insert([
                'machineno' => $this->machineno,
                'machinename' => $this->machinename,
                'department_id' => $this->department_id['value'],
                'product_group_id' => $this->product_group_id['value'],
                'capacity_kg' => $this->capacity_kg,
                'capacity_lembar' => $this->capacity_lembar,
                'capacity_size' => $this->capacity_size,
                'status' => $statusActive,
                'created_by' => auth()->user()->username,
                'created_on' => now(),
                'updated_by' => auth()->user()->username,
                'updated_on' => now(),
            ]);
            DB::commit();
            $this->resetFields();
            $this->dispatch('closeModalCreate');
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Machine created successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to save master Machine: ' . $e->getMessage());
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Failed to save the Machine: ' . $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $machine = DB::table('msmachine')->where('id', $id)->first();
        $this->idUpdate = $machine->id;
        $this->machineno = $machine->machineno;
        $this->machinename = $machine->machinename;
        $this->department_id = $machine->department_id;
        $this->product_group_id = $machine->product_group_id;
        $this->capacity_kg = $machine->capacity_kg;
        $this->capacity_lembar = $machine->capacity_lembar;
        $this->capacity_size = $machine->capacity_size;
        $this->status = $machine->status;
        $this->statusIsVisible = $machine->status == 0 ? true : false;
        $this->skipRender();
        $this->dispatch('showModalUpdate');
    }

    public function jadwal($id)
    {
        $machine = DB::table('msmachine')->where('id', $id)->first();
        $this->idUpdate = $machine->id;
        $this->machineno = $machine->machineno;
        $this->machinename = $machine->machinename;
        $this->department_id = $machine->department_id;
        $this->product_group_id = $machine->product_group_id;
        $this->capacity_kg = $machine->capacity_kg;
        $this->capacity_lembar = $machine->capacity_lembar;
        $this->capacity_size = $machine->capacity_size;
        $this->status = $machine->status;
        $this->statusIsVisible = $machine->status == 0 ? true : false;
        $this->skipRender();
        $this->dispatch('showModalUpdate');
    }

    public function update()
    {
        $this->validate([
            'machineno' => 'required|unique:msmachine,machineno,' . $this->idUpdate,
            'machinename' => 'required',
            'department_id' => 'required',
        ]);

        DB::beginTransaction();
        try {
            DB::table('msmachine')->where('id', $this->idUpdate)->update([
                'machineno' => $this->machineno,
                'machinename' => $this->machinename,
                'department_id' => $this->department_id,
                'product_group_id' => $this->product_group_id,
                'capacity_kg' => $this->capacity_kg,
                'capacity_lembar' => $this->capacity_lembar,
                'capacity_size' => $this->capacity_size,
                'status' => $this->status,
                'updated_by' => auth()->user()->username,
                'updated_on' => now(),
            ]);
            DB::commit();
            $this->resetFields();
            $this->dispatch('closeModalUpdate');
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Machine updated successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update master Machine: ' . $e->getMessage());
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Failed to update the Machine: ' . $e->getMessage()]);
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
            DB::table('msmachine')->where('id', $this->idDelete)->update([
                'status' => $statusInactive,
                'updated_by' => auth()->user()->username,
                'updated_on' => now(),
            ]);
            DB::commit();
            $this->dispatch('closeModalDelete');
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Machine deleted successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete master Machine: ' . $e->getMessage());
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Failed to delete the Machine: ' . $e->getMessage()]);
        }
    }

    public function render()
    {
        $data = DB::table('msmachine as msm')
            ->select(
                'msm.id',
                'msm.machinename',
                'msm.machineno',
                'msd.name as departmentname',
                'mpg.name as productgroupname',
                'msm.capacity_kg',
                'msm.capacity_lembar',
                'msm.status',
                'msm.updated_by',
                'msm.updated_on'
            )
            ->leftJoin('msdepartment as msd', 'msd.id', '=', 'msm.department_id')
            ->leftJoin('msproduct_group as mpg', 'mpg.id', '=', 'msm.product_group_id')
            ->get();

        return view('livewire.master-tabel.machine', [
            'data' => $data
        ])->extends('layouts.master');
    }

    public function rendered()
    {
        $this->dispatch('initDataTable');
    }
}
