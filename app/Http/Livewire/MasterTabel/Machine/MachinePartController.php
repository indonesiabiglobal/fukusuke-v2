<?php

namespace App\Http\Livewire\MasterTabel\Machine;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;
use Livewire\Attributes\Session;

class MachinePartController extends Component
{
    use WithPagination, WithoutUrlPagination;

    protected $paginationTheme = 'bootstrap';
    protected $listeners = ['delete', 'edit'];

    // Fields (disesuaikan dengan ms_machine_part)
    public $code;
    public $part_machine;
    public $department_id;
    public $status;
    public $idUpdate;
    public $idDelete;

    public $statusIsVisible = false;

    // Optional utility (dipertahankan agar kompatibel dengan layout lama)
    public $searchTerm;

    #[Session]
    public $sortingTable;

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
        $this->part_machine = '';
        $this->department_id = '';
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
            'code'          => 'required|unique:ms_machine_part,code',
            'part_machine'  => 'required',
            'department_id' => 'required',
        ]);

        DB::beginTransaction();
        try {
            $statusActive = 1;
            DB::table('ms_machine_part')->insert([
                'code'          => $this->code,
                'part_machine'  => $this->part_machine,
                'department_id' => $this->department_id['value'] ?? $this->department_id,
                'status'        => $statusActive,
                'created_by'    => auth()->user()->username,
                'created_on'    => now(),
                'updated_by'    => auth()->user()->username,
                'updated_on'    => now(),
            ]);

            DB::commit();

            $this->resetFields();
            $this->dispatch('closeModalCreate');
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Machine Part created successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to save Machine Part: ' . $e->getMessage());
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Failed to save the Machine Part: ' . $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $mp = DB::table('ms_machine_part')->where('id', $id)->first();
        if (!$mp) {
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Machine Part not found.']);
            return;
        }

        $this->idUpdate       = $mp->id;
        $this->code           = $mp->code;
        $this->part_machine   = $mp->part_machine;
        $this->department_id  = $mp->department_id;
        $this->status         = $mp->status;
        $this->statusIsVisible = ((int)$mp->status === 0);

        $this->skipRender();
        $this->dispatch('showModalUpdate');
    }

    // (Opsional) Dipertahankan jika ada tombol/flow yang memanggil jadwal()
    public function jadwal($id)
    {
        $this->edit($id);
    }

    public function update()
    {
        $this->validate([
            'code'          => 'required|unique:ms_machine_part,code,' . $this->idUpdate,
            'part_machine'  => 'required',
            'department_id' => 'required',
        ]);

        DB::beginTransaction();
        try {
            DB::table('ms_machine_part')
                ->where('id', $this->idUpdate)
                ->update([
                    'code'          => $this->code,
                    'part_machine'  => $this->part_machine,
                    'department_id' => $this->department_id['value'] ?? $this->department_id,
                    'status'        => $this->status ?? 1,
                    'updated_by'    => auth()->user()->username,
                    'updated_on'    => now(),
                ]);

            DB::commit();

            $this->resetFields();
            $this->dispatch('closeModalUpdate');
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Machine Part updated successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update Machine Part: ' . $e->getMessage());
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Failed to update the Machine Part: ' . $e->getMessage()]);
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
            DB::table('ms_machine_part')
                ->where('id', $this->idDelete)
                ->update([
                    'status'     => $statusInactive,
                    'updated_by' => auth()->user()->username,
                    'updated_on' => now(),
                ]);

            DB::commit();
            $this->dispatch('closeModalDelete');
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Machine Part deleted successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete Machine Part: ' . $e->getMessage());
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Failed to delete the Machine Part: ' . $e->getMessage()]);
        }
    }

    public function render()
    {
        $data = DB::table('ms_machine_part as msp')
            ->select(
                'msp.id',
                'msp.code',
                'msp.part_machine',
                'msd.name as departmentname',
                'msp.status',
                'msp.updated_by',
                'msp.updated_on'
            )
            ->leftJoin('msdepartment as msd', 'msd.id', '=', 'msp.department_id')
            ->get();

        return view('livewire.master-tabel.machine.machine-part', [
            'data' => $data
        ])->extends('layouts.master');
    }

    public function rendered()
    {
        $this->dispatch('initDataTable');
    }
}
