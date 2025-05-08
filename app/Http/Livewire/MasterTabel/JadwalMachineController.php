<?php

namespace App\Http\Livewire\MasterTabel;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;
use Livewire\Attributes\Session;

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
    #[Session]
    public $sortingTable;

    public function mount()
    {
        // $this->machines = DB::table('msmachine')
        //     ->get(['id', 'machinename', 'machineno', 'department_id', 'product_group_id', 'capacity_kg', 'capacity_lembar', 'status', 'updated_by', 'updated_on']);
        
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
        $this->jadwal_mesin = '';
        $this->jam = '';
        $this->percent = '';
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
            'machineno' => 'required',
            'jadwal_mesin' => 'required',
            'jam' => 'required',
            'percent' => 'required',
        ]);

        $machine = DB::table('msmachine')->where('machineno', $this->machineno)->first();

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
                    'idmachine' => $machine->id,
                ]);
                DB::commit();
                $startDate->addDay();
            }

            $this->resetFields();
            $this->dispatch('closeModalCreate');
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Machine created successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to save master Machine: ' . $e->getMessage());
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Nomor Mesin tidak ditemukan']);
        }
    }

    public function edit($id)
    {
        $jadwalMachine = DB::table('msjadwalmachine')->where('id', $id)->first();
        $machine = DB::table('msmachine')->where('id', $jadwalMachine->idmachine)->first();
        $this->idUpdate = $jadwalMachine->id;
        $this->machineno = $machine->machineno;
        $this->machinename = $machine->machinename;
        $this->jam = $jadwalMachine->jam;
        $this->jadwal_mesin = $jadwalMachine->jadwal;
        $this->percent = $jadwalMachine->percent;
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

        // $dates = explode(' to ', $this->jadwal_mesin);

        // $startDate = isset($dates[0]) ? Carbon::createFromFormat('d M, Y', trim($dates[0])) : null;
        // $endDate = isset($dates[1]) ? Carbon::createFromFormat('d M, Y', trim($dates[1])) : null;


        DB::beginTransaction();
        try {
            // while ($startDate->lte($endDate)) {
            DB::table('msjadwalmachine')->where('id', $this->idUpdate)->update([
                'jam' => $this->jam,
                'percent' => $this->percent,
                // 'jadwal' => $startDate->format('j M Y'),
                'jadwal' => $this->jadwal_mesin,
                'idmachine' => $this->idUpdate,
            ]);
            DB::commit();
            // $startDate->addDay();
            // }
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
            DB::table('msjadwalmachine')->where('id', $this->idDelete)->delete();

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
                'msj.id',
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
