<?php

namespace App\Http\Livewire\MasterTabel\Kemasan;

use App\Models\MsPackagingInner;
use Livewire\Component;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;

class InnerController extends Component
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

    protected $rules = [
        'code' => 'required',
        'name' => 'required',
        'box_class' => 'required',
        'panjang' => 'required',
        'lebar' => 'required',
        'tinggi' => 'required',
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
            $data = MsPackagingInner::create([
                'code' => $this->code,
                'name' => $this->name,
                'box_class' => $this->box_class['value'],
                'panjang' => $this->panjang,
                'lebar' => $this->lebar,
                'tinggi' => $this->tinggi,
                'status' => $statusActive,
                'created_by' => Auth::user()->username,
                'created_on' => Carbon::now(),
                'updated_by' => Auth::user()->username,
                'updated_on' => Carbon::now(),
            ]);

            DB::commit();
            $this->dispatch('closeModalCreate');
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Master Inner saved successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to save master Inner: ' . $e->getMessage());
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Failed to save the Inner: ' . $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $data = MsPackagingInner::where('id', $id)->first();
        $this->idUpdate = $id;
        $this->code = $data->code;
        $this->name = $data->name;
        $this->box_class = $data->box_class;
        $this->panjang = $data->panjang;
        $this->lebar = $data->lebar;
        $this->tinggi = $data->tinggi;

        $this->dispatch('showModalUpdate');
    }

    public function update()
    {
        $this->validate();

        DB::beginTransaction();
        try {
            $statusActive = 1;
            $data = MsPackagingInner::where('id', $this->idUpdate)->first();
            $data->code = $this->code;
            $data->name = $this->name;
            $data->box_class = $this->box_class;
            $data->panjang = $this->panjang;
            $data->lebar = $this->lebar;
            $data->tinggi = $this->tinggi;
            $data->status = $statusActive;
            $data->updated_by = Auth::user()->username;
            $data->updated_on = Carbon::now();
            $data->save();

            DB::commit();
            $this->dispatch('closeModalUpdate');
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Master Inner updated successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update master Inner: ' . $e->getMessage());
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Failed to update the Inner: ' . $e->getMessage()]);
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
            $data = MsPackagingInner::where('id', $this->idDelete)->first();
            $data->status = $statusInactive;
            $data->updated_by = Auth::user()->username;
            $data->updated_on = Carbon::now();
            $data->save();

            DB::commit();
            $this->dispatch('closeModalDelete');
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Master Inner deleted successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete master Inner: ' . $e->getMessage());
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Failed to delete the Inner: ' . $e->getMessage()]);
        }
    }

    public function render()
    {
        $result = MsPackagingInner::get();

        return view('livewire.master-tabel.kemasan.inner', [
            'result' => $result
        ])->extends('layouts.master');
    }

    public function rendered()
    {
        $this->dispatch('initDataTable');
    }
}
