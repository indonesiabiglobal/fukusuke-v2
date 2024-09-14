<?php

namespace App\Http\Livewire\MasterTabel;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;

class JadwalMachineController extends Component
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
    public $jadwal_mesin;
    public $jam;
    public $percent;
    public $startDate;
    public $endDate;
    public $statusIsVisible = false;


    // public function mount()
    // {
    //     $this->machines = DB::table('msmachine')
    //         ->get(['id', 'machinename', 'machineno', 'department_id', 'product_group_id', 'capacity_kg', 'capacity_lembar', 'status', 'updated_by', 'updated_on']);
    // }

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

    public function update()
    {
        $this->validate([
            'jadwal_mesin' => 'required',
            'jam' => 'required',
            'percent' => 'required',
        ]);

        $dates = explode(' to ', $this->jadwal_mesin);

        $startDate = isset($dates[0]) ? Carbon::createFromFormat('d M, Y', trim($dates[0])) : null;
        $endDate = isset($dates[1]) ? Carbon::createFromFormat('d M, Y', trim($dates[1])) : null;


        DB::beginTransaction();
        try {
            while ($startDate->lte($endDate)) {
                DB::table('msjadwalmachine')->insert([
                    'jam' => $this->jam,
                    'percent' => $this->percent,
                    'jadwal' => $startDate->format('j M Y'),
                    'idmachine' => $this->idUpdate,
                ]);
                DB::commit();

                // $dates[] = $startDate->format('j M Y');
                $startDate->addDay();
            }
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
                'msm.capacity_kg',
                'msm.capacity_lembar',
                'msm.status',
                'msj.jam',
                'msj.jadwal',
                'msj.percent'
            )
            ->Join('msjadwalmachine as msj', 'msj.idmachine', '=', 'msm.id')
            ->where('msm.status', 1)
            ->get();

        return view('livewire.master-tabel.jadwal-machine', [
            'data' => $data
        ])->extends('layouts.master');
    }

    public function rendered()
    {
        $this->dispatch('initDataTable');
    }
}
