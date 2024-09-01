<?php

namespace App\Http\Livewire\MasterTabel\Kemasan;

use App\Models\MsPackagingGaiso;
use Livewire\Component;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;
class GaisoController extends Component
{
    use WithPagination, WithoutUrlPagination;
    protected $paginationTheme = 'bootstrap';
    protected $listeners = ['delete','edit'];
    public $searchTerm;
    public $code;
    public $name;
    public $box_class;
    public $panjang;
    public $lebar;
    public $tinggi;
    public $idUpdate;
    public $idDelete;
    public $class;
    public $status;
    public $statusIsVisible = false;

    protected $rules = [
        'code' => 'required',
        'name' => 'required',
        'box_class' => 'required',
        'panjang' => 'required',
        'lebar' => 'required',
    ];

    public function resetFields()
    {
        $this->code = '';
        $this->name = '';
        $this->panjang = '';
        $this->lebar = '';
        $this->tinggi = '';
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
            $statusActive = 1;
            $data = MsPackagingGaiso::create([
                'code' => $this->code,
                'name' => $this->name,
                'box_class' => $this->box_class['value'],
                'panjang' => (int)str_replace(',', '', $this->panjang),
                'lebar' => (int)str_replace(',', '', $this->lebar),
                'tinggi' => (int)str_replace(',', '', $this->tinggi),
                'status' => $statusActive,
                'created_by' => Auth::user()->username,
                'created_on' => Carbon::now(),
                'updated_by' => Auth::user()->username,
                'updated_on' => Carbon::now(),
            ]);

            DB::commit();
            $this->dispatch('closeModalCreate');
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Master Gaiso saved successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to save master Gaiso: ' . $e->getMessage());
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Failed to save the Gaiso: ' . $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $data = MsPackagingGaiso::where('id', $id)->first();
        $this->idUpdate = $id;
        $this->code = $data->code;
        $this->name = $data->name;
        $this->box_class = $data->box_class;
        $this->panjang = number_format($data->panjang);
        $this->lebar = number_format($data->lebar);
        $this->tinggi = number_format($data->tinggi);
        $this->status = $data->status;
        $this->statusIsVisible = $data->status == 0 ? true : false;
        $this->skipRender();

        $this->dispatch('showModalUpdate');
    }

    public function update()
    {
        $this->validate();

        DB::beginTransaction();
        try {
            $statusActive = 1;
            $data = MsPackagingGaiso::where('id', $this->idUpdate)->first();
            $data->code = $this->code;
            $data->name = $this->name;
            $data->box_class = is_array($this->box_class) ? $this->box_class['value'] : $this->box_class;
            $data->panjang = (int)str_replace(',', '', $this->panjang);
            $data->lebar = (int)str_replace(',', '', $this->lebar);
            $data->tinggi = (int)str_replace(',', '', $this->tinggi);
            $data->status = $this->status;
            $data->updated_by = Auth::user()->username;
            $data->updated_on = Carbon::now();
            $data->save();

            DB::commit();
            $this->dispatch('closeModalUpdate');
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Master Gaiso updated successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            $this->skipRender();
            Log::error('Failed to update master Gaiso: ' . $e->getMessage());
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Failed to update the Gaiso: ' . $e->getMessage()]);
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
            $data = MsPackagingGaiso::where('id', $this->idDelete)->first();
            $data->status = $statusInactive;
            $data->updated_by = Auth::user()->username;
            $data->updated_on = Carbon::now();
            $data->save();

            DB::commit();
            $this->dispatch('closeModalDelete');
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Master Gaiso deleted successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete master Gaiso: ' . $e->getMessage());
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Failed to delete the Gaiso: ' . $e->getMessage()]);
        }
    }

    public function render()
    {
        $result = MsPackagingGaiso::get();

        return view('livewire.master-tabel.kemasan.gaiso', [
            'result' => $result
        ])->extends('layouts.master');
    }

    public function rendered()
    {
        $this->dispatch('initDataTable');
    }
}
