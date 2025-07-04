<?php

namespace App\Http\Livewire\MasterTabel;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;
use Livewire\Attributes\Session;

class Employee extends Component
{
    use WithPagination, WithoutUrlPagination;
    protected $paginationTheme = 'bootstrap';
    protected $listeners = ['delete', 'edit'];
    public $data;
    public $searchTerm;
    public $employeeno;
    public $empname;
    public $department_id;
    public $department_name;
    public $idUpdate;
    public $idDelete;
    public $status;
    public $statusIsVisible = false;
    public $paginate = 10;
    #[Session]
    public $sortingTable;

    public function mount()
    {
        $this->data =  DB::table('msemployee as mse')
            ->select(
                'mse.id',
                'mse.employeeno',
                'mse.empname',
                'mse.department_id',
                'mse.status',
                'mse.updated_by',
                'mse.updated_on',
                'msd.name as department_name'
            )
            ->leftJoin('msdepartment as msd', 'mse.department_id', '=', 'msd.id')
            ->get();

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
        $this->employeeno = '';
        $this->empname = '';
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
            'employeeno' => 'required|unique:msemployee,employeeno',
            'empname' => 'required',
            'department_id' => 'required',
        ]);

        DB::beginTransaction();
        try {
            $statusActive = 1;
            DB::table('msemployee')->insert([
                'employeeno' => $this->employeeno,
                'empname' => $this->empname,
                'department_id' => is_array($this->department_id) ? $this->department_id['value'] : $this->department_id,
                'status' => $statusActive,
                'created_by' => auth()->user()->username,
                'created_on' => now(),
                'updated_by' => auth()->user()->username,
                'updated_on' => now(),
            ]);
            DB::commit();
            $this->resetFields();
            $this->dispatch('closeModalCreate');
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Employee created successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to save master Employee: ' . $e->getMessage());
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Failed to save the Employee: ' . $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $employee = DB::table('msemployee as mse')
            ->join('msdepartment as msd', 'mse.department_id', '=', 'msd.id')
            ->where('mse.id', $id)
            ->first(['mse.employeeno', 'mse.empname', 'mse.department_id', 'mse.status', 'msd.name as department_name']);
        $this->employeeno = $employee->employeeno;
        $this->empname = $employee->empname;
        $this->department_id = $employee->department_id;
        $this->department_name = $employee->department_name;
        $this->idUpdate = $id;
        $this->status = $employee->status;
        $this->statusIsVisible = $employee->status == 0 ? true : false;
        $this->skipRender();
        $this->dispatch('showModalUpdate');
    }

    public function update()
    {
        $this->validate([
            'employeeno' => 'required|unique:msemployee,employeeno,' . $this->idUpdate,
            'empname' => 'required',
            'department_id' => 'required',
        ]);

        DB::beginTransaction();
        try {
            DB::table('msemployee')
                ->where('id', $this->idUpdate)
                ->update([
                    'employeeno' => $this->employeeno,
                    'empname' => $this->empname,
                    'department_id' => $this->department_id,
                    'status' => $this->status,
                    'updated_by' => auth()->user()->username,
                    'updated_on' => now(),
                ]);
            DB::commit();
            $this->resetFields();
            $this->dispatch('closeModalUpdate');
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Employee updated successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            $this->skipRender();
            Log::error('Failed to update master Employee: ' . $e->getMessage());
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Failed to update the Employee: ' . $e->getMessage()]);
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
            DB::table('msemployee')->where('id', $this->idDelete)->update([
                'status' => $statusInactive,
                'updated_by' => auth()->user()->username,
                'updated_on' => now(),
            ]);
            DB::commit();
            $this->dispatch('closeModalDelete');
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Employee deleted successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete master Employee: ' . $e->getMessage());
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Failed to delete the Employee: ' . $e->getMessage()]);
        }
    }

    public function render()
    {
        $this->data =  DB::table('msemployee as mse')
            ->select(
                'mse.id',
                'mse.employeeno',
                'mse.empname',
                'mse.department_id',
                'mse.status',
                'mse.updated_by',
                'mse.updated_on',
                'msd.name as department_name'
            )
            ->leftJoin('msdepartment as msd', 'mse.department_id', '=', 'msd.id')
            ->get();

        return view('livewire.master-tabel.employee')->extends('layouts.master');
    }

    public function rendered()
    {
        $this->dispatch('initDataTable');
    }
}
