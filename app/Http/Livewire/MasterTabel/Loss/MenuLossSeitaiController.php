<?php

namespace App\Http\Livewire\MasterTabel\Loss;

use App\Models\MsLossClass;
use App\Models\MsLossSeitai;
use Livewire\Component;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;

class MenuLossSeitaiController extends Component
{
    use WithPagination, WithoutUrlPagination;
    protected $paginationTheme = 'bootstrap';
    public $searchTerm;
    public $code;
    public $name;
    public $loss_class_id;
    public $loss_category_code;
    public $idUpdate;
    public $idDelete;
    public $class;

    protected $rules = [
        'code' => 'required',
        'name' => 'required',
    ];

    public function mount(){
        $this->class = MsLossClass::get();
    }

    public function resetFields()
    {
        $this->code = '';
        $this->name = '';
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
            $data = MsLossSeitai::create([
                'code' => $this->code,
                'name' => $this->name,
                'loss_class_id' => $this->loss_class_id['value'],
                'loss_category_code' => $this->loss_category_code['value'],
                'status' => $statusActive,
                'created_by' => Auth::user()->username,
                'created_on' => Carbon::now(),
                'updated_by' => Auth::user()->username,
                'updated_on' => Carbon::now(),
            ]);

            DB::commit();
            $this->dispatch('closeModalCreate');
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Master Buyer saved successfully.']);
            // return redirect()->route('buyer');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to save master buyer: ' . $e->getMessage());
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Failed to save the buyer: ' . $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $data = MsLossSeitai::where('id', $id)->first();
        $this->idUpdate = $data->id;
        $this->code = $data->code;
        $this->name = $data->name;
        $this->loss_class_id = $data->loss_class_id;
        $this->loss_category_code = $data->loss_category_code;

        // $this->dispatch('showModalUpdate', $buyer);
    }

    public function update()
    {
        $this->validate();

        DB::beginTransaction();
        try {
            $statusActive = 1;
            $data = MsLossSeitai::where('id', $this->idUpdate)->first();
            $data->code = $this->code;
            $data->name = $this->name;
            $data->loss_class_id = $this->loss_class_id['value'];
            $data->loss_category_code = $this->loss_category_code['value'];
            $data->status = $statusActive;
            $data->updated_by = Auth::user()->username;
            $data->updated_on = Carbon::now();
            $data->save();

            DB::commit();
            $this->dispatch('closeModalUpdate');
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Master Loss Infure updated successfully.']);
            $this->search();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update master Loss Infure: ' . $e->getMessage());
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Failed to update the Loss Infure: ' . $e->getMessage()]);
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
            $data = MsLossSeitai::where('id', $this->idDelete)->first();
            $data->status = $statusInactive;
            $data->updated_by = Auth::user()->username;
            $data->updated_on = Carbon::now();
            $data->save();

            DB::commit();
            $this->dispatch('closeModalDelete');
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Master Loss Infure deleted successfully.']);
            $this->search();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete master Loss Infure: ' . $e->getMessage());
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Failed to delete the Loss Infure: ' . $e->getMessage()]);
        }
    }

    public function search()
    {
        $this->render();
    }

    public function render()
    {
        $result = MsLossSeitai::where('status', 1)->get();

        return view('livewire.master-tabel.loss.menu-loss-seitai',[
            'result' => $result
        ])->extends('layouts.master');
    }
}
