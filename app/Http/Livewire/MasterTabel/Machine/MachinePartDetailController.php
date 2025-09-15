<?php

namespace App\Http\Livewire\MasterTabel\Machine;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;
use Livewire\Attributes\Session;

class MachinePartDetailController extends Component
{
    use WithPagination, WithoutUrlPagination;

    protected $paginationTheme = 'bootstrap';
    protected $listeners = ['delete', 'edit'];

    // Fields untuk ms_machine_part_detail
    public $code;
    public $machine_part_id;
    public $name;
    public $status = 1;

    public $idUpdate;
    public $idDelete;

    public $statusIsVisible = false;

    // Optional utility
    public $searchTerm;

    #[Session]
    public $sortingTable;

    public function mount()
    {
        if (empty($this->sortingTable)) {
            // default sort by Part Machine (kolom 1) asc
            $this->sortingTable = [[1, 'asc']];
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
        $this->machine_part_id = '';
        $this->name = '';
        $this->status = 1;

        $this->idUpdate = null;
        $this->idDelete = null;
        $this->statusIsVisible = false;
    }

    public function showModalCreate()
    {
        $this->resetFields();
        $this->dispatch('showModalCreate');
        $this->skipRender();
    }

    public function store()
    {
        $this->validate([
            'code'            => 'required|unique:ms_machine_part_detail,code',
            'machine_part_id' => 'required|exists:ms_machine_part,id',
            'name'    => 'required',
        ]);

        DB::beginTransaction();
        try {
            $statusActive = 1;
            DB::table('ms_machine_part_detail')->insert([
                'code'            => strtoupper($this->code),
                'machine_part_id' => is_array($this->machine_part_id)
                    ? ($this->machine_part_id['value'] ?? null)
                    : $this->machine_part_id,
                'name'    => $this->name,
                'status'          => $statusActive,
                'created_by'      => auth()->user()->username,
                'created_on'      => now(),
                'updated_by'      => auth()->user()->username,
                'updated_on'      => now(),
            ]);

            DB::commit();

            $this->resetFields();
            $this->dispatch('closeModalCreate');
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Machine Part Detail created successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to save Machine Part Detail: ' . $e->getMessage());
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Failed to save the Machine Part Detail: ' . $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $detail = DB::table('ms_machine_part_detail')->where('id', $id)->first();
        if (!$detail) {
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Machine Part Detail not found.']);
            return;
        }

        $this->idUpdate        = $detail->id;
        $this->code            = $detail->code;
        $this->machine_part_id = $detail->machine_part_id;
        $this->name    = $detail->name;
        $this->status          = $detail->status;
        $this->statusIsVisible = ((int)$detail->status === 0);

        $this->skipRender();
        $this->dispatch('showModalUpdate');
    }

    // Disisakan untuk kompatibilitas tombol jadwal
    public function jadwal($id)
    {
        $this->edit($id);
    }

    public function update()
    {
        $this->validate([
            'code'            => 'required|unique:ms_machine_part_detail,code,' . $this->idUpdate,
            'machine_part_id' => 'required|exists:ms_machine_part,id',
            'name'    => 'required',
        ]);

        DB::beginTransaction();
        try {
            DB::table('ms_machine_part_detail')
                ->where('id', $this->idUpdate)
                ->update([
                    'code'            => strtoupper($this->code),
                    'machine_part_id' => is_array($this->machine_part_id)
                        ? ($this->machine_part_id['value'] ?? null)
                        : $this->machine_part_id,
                    'name'    => $this->name,
                    'status'          => $this->status ?? 1,
                    'updated_by'      => auth()->user()->username,
                    'updated_on'      => now(),
                ]);

            DB::commit();

            $this->resetFields();
            $this->dispatch('closeModalUpdate');
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Machine Part Detail updated successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update Machine Part Detail: ' . $e->getMessage());
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Failed to update the Machine Part Detail: ' . $e->getMessage()]);
        }
    }

    public function delete($id)
    {
        $this->idDelete = $id;
        $this->dispatch('showModalDelete');
        $this->skipRender();
    }

    public function destroy()
    {
        DB::beginTransaction();
        try {
            $statusInactive = 0;
            DB::table('ms_machine_part_detail')
                ->where('id', $this->idDelete)
                ->update([
                    'status'     => $statusInactive,
                    'updated_by' => auth()->user()->username,
                    'updated_on' => now(),
                ]);

            DB::commit();
            $this->dispatch('closeModalDelete');
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Machine Part Detail deleted successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete Machine Part Detail: ' . $e->getMessage());
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Failed to delete the Machine Part Detail: ' . $e->getMessage()]);
        }
    }

    public function render()
    {
        $data = DB::table('ms_machine_part_detail as mspd')
            ->select(
                'mspd.id',
                'mspd.code',
                'mspd.name',
                'msp.part_machine',
                'msd.name as departmentname',
                'mspd.status',
                'mspd.updated_by',
                'mspd.updated_on'
            )
            ->leftJoin('ms_machine_part as msp', 'msp.id', '=', 'mspd.machine_part_id')
            ->leftJoin('msdepartment as msd', 'msd.id', '=', 'msp.department_id')
            ->get();

        return view('livewire.master-tabel.machine.machine-part-detail', [
            'data' => $data
        ])->extends('layouts.master');
    }

    public function rendered()
    {
        $this->dispatch('initDataTable');
    }
}
