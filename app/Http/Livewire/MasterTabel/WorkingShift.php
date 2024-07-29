<?php

namespace App\Http\Livewire\MasterTabel;

use App\Models\MsWorkingShift;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;

class WorkingShift extends Component
{
    use WithPagination, WithoutUrlPagination;
    protected $paginationTheme = 'bootstrap';
    public $workingShifts;
    public $searchTerm;
    public $work_shift;
    public $work_hour_from;
    public $work_hour_till;
    public $idUpdate;
    public $idDelete;

    public function mount()
    {
        $this->workingShifts = MsWorkingShift::get(['id', 'work_shift', 'work_hour_from', 'work_hour_till', 'status', 'updated_by', 'updated_on']);
    }

    public function resetFields()
    {
        $this->work_shift = '';
        $this->work_hour_from = '';
        $this->work_hour_till = '';
    }

    public function showModalCreate()
    {
        $this->resetFields();
        $this->dispatch('showModalCreate');
    }

    public function store()
    {
        $this->validate([
            'work_shift' => 'required|unique:msworkingshift,work_shift',
            'work_hour_from' => 'required',
            'work_hour_till' => 'required',
        ]);

        DB::beginTransaction();
        try {
            // check jika work_hour_from lebih besar dari work_hour_till
            if ($this->work_hour_from > $this->work_hour_till) {
                $this->dispatch('notification', ['type' => 'error', 'message' => 'Work Hour From cannot be greater than Work Hour Till.']);
                return;
            }

            // check jika work_hour_from sama dengan work_hour_till
            if ($this->work_hour_from == $this->work_hour_till) {
                $this->dispatch('notification', ['type' => 'error', 'message' => 'Work Hour From cannot be equal to Work Hour Till.']);
                return;
            }

            $statusActive = 1;
            $msworkingshift = MsWorkingShift::create([
                'work_shift' => $this->work_shift,
                'work_hour_from' => $this->work_hour_from,
                'work_hour_till' => $this->work_hour_till,
                'status' => $statusActive,
                'created_by' => auth()->user()->username,
                'created_on' => Carbon::now(),
                'updated_by' => auth()->user()->username,
                'updated_on' => Carbon::now(),
                'trial464' => 'T'
            ]);
            DB::commit();
            $this->resetFields();
            $this->search();
            $this->dispatch('closeModalCreate');
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Master Working Shift saved successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to save master Working Shift: ' . $e->getMessage());
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Failed to save the Working Shift: ' . $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $workingShift = MsWorkingShift::find($id);
        $this->idUpdate = $id;
        $this->work_shift = $workingShift->work_shift;
        $this->work_hour_from = $workingShift->work_hour_from;
        $this->work_hour_till = $workingShift->work_hour_till;
        $this->dispatch('showModalUpdate');
    }

    public function update()
    {
        $this->validate([
            'work_shift' => 'required|unique:msworkingshift,work_shift,' . $this->idUpdate,
            'work_hour_from' => 'required',
            'work_hour_till' => 'required',
        ]);

        DB::beginTransaction();
        try {
            // check jika work_hour_from lebih besar dari work_hour_till
            if ($this->work_hour_from > $this->work_hour_till) {
                $this->dispatch('notification', ['type' => 'error', 'message' => 'Work Hour From cannot be greater than Work Hour Till.']);
                return;
            }

            // check jika work_hour_from sama dengan work_hour_till
            if ($this->work_hour_from == $this->work_hour_till) {
                $this->dispatch('notification', ['type' => 'error', 'message' => 'Work Hour From cannot be equal to Work Hour Till.']);
                return;
            }
            $statusActive = 1;
            MsWorkingShift::where('id', $this->idUpdate)->update([
                'work_shift' => $this->work_shift,
                'work_hour_from' => $this->work_hour_from,
                'work_hour_till' => $this->work_hour_till,
                'status' => $statusActive,
                'updated_by' => auth()->user()->username,
                'updated_on' => Carbon::now(),
            ]);
            DB::commit();
            $this->resetFields();
            $this->search();
            $this->dispatch('closeModalUpdate');
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Master Working Shift updated successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update master Working Shift: ' . $e->getMessage());
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Failed to update the Working Shift: ' . $e->getMessage()]);
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
            MsWorkingShift::where('id', $this->idDelete)->update([
                'status' => $statusInactive,
                'updated_by' => auth()->user()->username,
                'updated_on' => Carbon::now(),
            ]);
            DB::commit();
            $this->search();
            $this->dispatch('closeModalDelete');
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Master Working Shift deleted successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete master Working Shift: ' . $e->getMessage());
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Failed to delete the Working Shift: ' . $e->getMessage()]);
        }
    }

    public function search()
    {
        $this->render();
    }
    public function render()
    {
        $workingShifts = MsWorkingShift::where('work_shift', 'like', '%' . $this->searchTerm . '%')
            ->paginate(10);

        return view('livewire.master-tabel.working-shift', [
            'data' => $workingShifts
        ])->extends('layouts.master');
    }
}
