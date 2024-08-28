<?php

namespace App\Http\Livewire\MasterTabel;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;

class Department extends Component
{
    use WithPagination, WithoutUrlPagination;
    protected $paginationTheme = 'bootstrap';
    protected $listeners = ['delete','edit'];
    public $departments;
    public $searchTerm;
    public $code;
    public $name;
    public $divisionCode;
    public $idUpdate;
    public $idDelete;

    public function mount()
    {
        $this->departments = DB::table('msdepartment')
            ->get(['id', 'code', 'name', 'division_code', 'status', 'updated_by', 'updated_on']);
    }

    public function resetFields()
    {
        $this->code = '';
        $this->name = '';
        $this->divisionCode = '';
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
            'code' => 'required|unique:msdepartment,code',
            'name' => 'required',
            'divisionCode' => 'required',
        ]);

        DB::beginTransaction();
        try {
            $statusActive = 1;
            DB::table('msdepartment')->insert([
                'code' => $this->code,
                'name' => $this->name,
                'division_code' => $this->divisionCode,
                'status' => $statusActive,
                'created_by' => auth()->user()->username,
                'created_on' => now(),
                'updated_by' => auth()->user()->username,
                'updated_on' => now(),
            ]);
            DB::commit();
            $this->resetFields();
            $this->search();
            $this->dispatch('closeModalCreate');
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Master Department saved successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Failed to save the Department: ' . $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $department = DB::table('msdepartment')->where('id', $id)->first();
        $this->idUpdate = $department->id;
        $this->code = $department->code;
        $this->name = $department->name;
        $this->divisionCode = $department->division_code;
        $this->dispatch('showModalUpdate');
    }

    public function update()
    {
        $this->validate([
            'code' => 'required|unique:msdepartment,code,' . $this->idUpdate,
            'name' => 'required',
            'divisionCode' => 'required',
        ]);

        DB::beginTransaction();
        try {
            DB::table('msdepartment')->where('id', $this->idUpdate)->update([
                'code' => $this->code,
                'name' => $this->name,
                'division_code' => $this->divisionCode,
                'updated_by' => auth()->user()->username,
                'updated_on' => now(),
            ]);
            DB::commit();
            $this->resetFields();
            $this->search();
            $this->dispatch('closeModalUpdate');
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Master Department updated successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Failed to update the Department: ' . $e->getMessage()]);
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

            DB::table('msdepartment')->where('id', $this->idDelete)->update([
                'status' => $statusInactive,
                'updated_by' => auth()->user()->username,
                'updated_on' => now(),
            ]);
            DB::commit();
            $this->search();
            $this->dispatch('closeModalDelete');
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Master Department deleted successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Failed to delete the Department: ' . $e->getMessage()]);
        }
    }

    public function search()
    {
        $this->resetPage();
        $this->render();
    }

    public function render()
    {

        $data = DB::table('msdepartment')
            ->select('id', 'code', 'name', 'division_code', 'status', 'updated_by', 'updated_on')
            ->when(isset($this->searchTerm) && $this->searchTerm != "" && $this->searchTerm != "undefined", function ($query) {
                $query
                    ->where(function ($query) {
                        $query->where('code', 'ilike', '%' . $this->searchTerm . '%')
                            ->orWhere('name', 'ilike', '%' . $this->searchTerm . '%')
                            ->orWhere('division_code', 'ilike', '%' . $this->searchTerm . '%');
                    });
            })
            // ->paginate(10);
            ->get();

        return view('livewire.master-tabel.department', [
            'data' =>  $data
        ])->extends('layouts.master');
    }

    public function rendered()
    {
        $this->dispatch('initDataTable');
    }
}
