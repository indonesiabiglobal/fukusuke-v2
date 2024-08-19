<?php

namespace App\Http\Livewire\MasterTabel\Kemasan;

use App\Models\MsPackagingBox;
use Livewire\Component;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;

class BoxController extends Component
{
    use WithPagination, WithoutUrlPagination;
    protected $paginationTheme = 'bootstrap';
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
    }

    public function store()
    {
        $this->validate();

        DB::beginTransaction();
        try {
            $statusActive = 1;
            $data = MsPackagingBox::create([
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
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Master Box saved successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to save master Box: ' . $e->getMessage());
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Failed to save the Box: ' . $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $data = MsPackagingBox::where('id', $id)->first();
        $this->idUpdate = $id;
        $this->code = $data->code;
        $this->name = $data->name;

        // $this->dispatch('showModalUpdate', $buyer);
    }

    public function update()
    {
        $this->validate();

        DB::beginTransaction();
        try {
            $statusActive = 1;
            $data = MsPackagingBox::where('id', $this->idUpdate)->first();
            $data->code = $this->code;
            $data->name = $this->name;
            $data->box_class = $this->box_class['value'];
            $data->panjang = $this->panjang;
            $data->lebar = $this->lebar;
            $data->tinggi = $this->tinggi;
            $data->status = $statusActive;
            $data->updated_by = Auth::user()->username;
            $data->updated_on = Carbon::now();
            $data->save();

            DB::commit();
            $this->dispatch('closeModalUpdate');
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Master Box updated successfully.']);
            $this->search();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update master Box: ' . $e->getMessage());
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Failed to update the Box: ' . $e->getMessage()]);
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
            $data = MsPackagingBox::where('id', $this->idDelete)->first();
            $data->status = $statusInactive;
            $data->updated_by = Auth::user()->username;
            $data->updated_on = Carbon::now();
            $data->save();

            DB::commit();
            $this->dispatch('closeModalDelete');
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Master Box deleted successfully.']);
            $this->search();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete master Box: ' . $e->getMessage());
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Failed to delete the Box: ' . $e->getMessage()]);
        }
    }

    public function search()
    {
        $this->render();
    }

    public function render()
    {
        $result = MsPackagingBox::where('status', 1);

        if (isset($this->searchTerm) && $this->searchTerm != "" && $this->searchTerm != "undefined") {
            $result = $result->where(function ($query) {
                $query->where('code', 'ilike', "%{$this->searchTerm}%")
                    ->orWhere('name', 'ilike', "%{$this->searchTerm}%");
            });
        }

        $result = $result->paginate(8);

        return view('livewire.master-tabel.kemasan.box', [
            'result' => $result
        ])->extends('layouts.master');
    }
}
