<?php

namespace App\Http\Livewire\MasterTabel\MasalahKenpin;

use App\Helpers\departmentHelper;
use App\Models\MsMasalahKenpinSeitai;
use Livewire\Component;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;
use Livewire\Attributes\Session;

class MasalahKenpinSeitaiController extends Component
{
    use WithPagination, WithoutUrlPagination;
    protected $paginationTheme = 'bootstrap';
    protected $listeners = [
        'delete',
        'edit',
        'refreshData',
        'filterChanged'
    ];
    public $searchTerm;
    public $code;
    public $name;
    public $listDepartments;
    public $department;
    public $idUpdate;
    public $idDelete;
    public $status;
    public $statusIsVisible = false;
    #[Session]
    public $sortingTable;
    public $statusFilter = 'all';
    public $data;

    // Loading states
    public $isLoading = false;

    protected $rules = [
        'code' => 'required',
        'name' => 'required',
        'department' => 'required',
    ];

    public function mount()
    {
        if (empty($this->sortingTable)) {
            $this->sortingTable = [[2, 'asc']];
        }
        $this->listDepartments = departmentHelper::masalahKenpinSeitaiDepartment();
    }

    public function filterByStatus($status)
    {
        $this->isLoading = true;

        // Validate status
        $allowedStatuses = ['all', 'active', 'inactive'];
        if (in_array($status, $allowedStatuses)) {
            $this->statusFilter = $status;
        }

        $this->isLoading = false;

        // Show toast notification
        $this->dispatch('notification', ['type' => 'success', 'message' => 'Filter applied: ' . ucfirst($status)]);
    }

    public function clearFilter()
    {
        $this->statusFilter = 'all';

        $this->dispatch('notification', ['type' => 'success', 'message' => 'Filter cleared']);
    }

    public function getTotalRecordsProperty()
    {
        return MsMasalahKenpinSeitai::count(); // Ganti dengan model yang sesuai
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
        $this->department["value"] = '';
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

        DB::beginTransaction();
        try {
            $data = MsMasalahKenpinSeitai::create([
                'code' => $this->code,
                'masalah' => $this->name,
                'department' => $this->department["value"] ?? $this->department,
                'status' => 1,
                'created_by' => Auth::user()->username,
                'updated_by' => Auth::user()->username,
            ]);

            DB::commit();
            $this->dispatch('closeModalCreate');
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Master Jam Mati Mesin Seitai saved successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to save master Box: ' . $e->getMessage());
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Failed to save the Box: ' . $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $data = MsMasalahKenpinSeitai::where('id', $id)->first();
        $this->idUpdate = $id;
        $this->code = $data->code;
        $this->name = $data->masalah;
        $this->department = $data->department;
        $this->status = $data->status;
        $this->statusIsVisible = $data->status == 0 ? true : false;
        $this->skipRender();
        // dd($this->department["value"]);

        $this->dispatch('showModalUpdate');
    }

    public function update()
    {
        $this->validate();

        DB::beginTransaction();
        try {
            $data = MsMasalahKenpinSeitai::where('id', $this->idUpdate)->first();
            $data->code = $this->code;
            $data->masalah = $this->name;
            $data->department = $this->department["value"] ?? $this->department;
            $data->status = $this->status;
            $data->updated_by = Auth::user()->username;
            $data->save();

            DB::commit();
            $this->dispatch('closeModalUpdate');
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Master Jam Mati Mesin Seitai updated successfully.']);
        } catch (\Exception $e) {
            $this->skipRender();
            DB::rollBack();
            Log::error('Failed to update master Box: ' . $e->getMessage());
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Failed to update the Box: ' . $e->getMessage()]);
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
            $data = MsMasalahKenpinSeitai::where('id', $this->idDelete)->first();
            $data->status = $statusInactive;
            $data->updated_by = Auth::user()->username;
            $data->save();

            DB::commit();
            $this->dispatch('closeModalDelete');
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Master Jam Mati Mesin Seitai deleted successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete master Box: ' . $e->getMessage());
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Failed to delete the Box: ' . $e->getMessage()]);
        }
    }

    public function render()
    {
        $query = MsMasalahKenpinSeitai::query();

        // Apply status filter
        if ($this->statusFilter !== 'all') {
            $status = $this->statusFilter === 'active' ? 1 : 0;
            $query->where('status', $status);
        }

        $result = $query->get();

        return view('livewire.master-tabel.masalah-kenpin.seitai', [
            'result' => $result,
            'totalRecords' => $this->totalRecords,
        ])->extends('layouts.master');
    }

    public function rendered()
    {
        $this->dispatch('initDataTable');
    }
}
