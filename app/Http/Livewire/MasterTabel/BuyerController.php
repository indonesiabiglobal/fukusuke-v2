<?php

namespace App\Http\Livewire\MasterTabel;

use App\Models\MsBuyer;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;

class BuyerController extends Component
{
    use WithPagination, WithoutUrlPagination;
    protected $paginationTheme = 'bootstrap';
    protected $listeners = ['delete','edit'];
    public $buyers;
    public $searchTerm;
    public $code;
    public $name;
    public $address;
    public $city;
    public $country;
    public $idUpdate;
    public $idDelete;

    public $perPage = 10;

    protected $rules = [
        'code' => 'required',
        'name' => 'required',
        'address' => 'required',
        'city' => 'required',
        'country' => 'required',
    ];

    public function mount()
    {
        $this->buyers = MsBuyer::get(['id', 'code', 'name', 'address', 'country', 'status', 'updated_by', 'updated_on']);
    }

    public function resetFields()
    {
        $this->code = '';
        $this->name = '';
        $this->address = '';
        $this->city = '';
        $this->country = '';
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
            $msbuyer = MsBuyer::create([
                'code' => $this->code,
                'name' => $this->name,
                'city' => $this->city,
                'address' => $this->address,
                'country' => $this->country,
                'status' => $statusActive,
                'created_by' => Auth::user()->username,
                'created_on' => Carbon::now(),
                'updated_by' => Auth::user()->username,
                'updated_on' => Carbon::now(),
            ]);
            DB::commit();
            $this->buyers = MsBuyer::get(['id', 'code', 'name', 'address', 'country', 'status', 'updated_by', 'updated_on']);
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
        $buyer = MsBuyer::where('id', $id)->first();
        $this->idUpdate = $id;
        $this->code = $buyer->code;
        $this->name = $buyer->name;
        $this->address = $buyer->address;
        $this->city = $buyer->city;
        $this->country = $buyer->country;

        $this->dispatch('showModalUpdate');
    }

    public function update()
    {
        $this->validate();

        DB::beginTransaction();
        try {
            $statusActive = 1;
            $msbuyer = MsBuyer::where('id', $this->idUpdate)->first();
            $msbuyer->code = $this->code;
            $msbuyer->name = $this->name;
            $msbuyer->city = $this->city;
            $msbuyer->address = $this->address;
            $msbuyer->country = $this->country;
            $msbuyer->status = $statusActive;
            $msbuyer->updated_by = Auth::user()->username;
            $msbuyer->updated_on = Carbon::now();
            $msbuyer->save();
            DB::commit();
            $this->dispatch('closeModalUpdate');
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Master Buyer updated successfully.']);
            $this->search();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update master buyer: ' . $e->getMessage());
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Failed to update the buyer: ' . $e->getMessage()]);
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
            $msbuyer = MsBuyer::where('id', $this->idDelete)->first();
            $msbuyer->status = $statusInactive;
            $msbuyer->updated_by = Auth::user()->username;
            $msbuyer->updated_on = Carbon::now();
            $msbuyer->save();
            DB::commit();
            $this->dispatch('closeModalDelete');
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Master Buyer deleted successfully.']);
            $this->search();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete master buyer: ' . $e->getMessage());
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Failed to delete the buyer: ' . $e->getMessage()]);
        }
    }

    public function search()
    {
        $this->resetPage();
        $this->render();

        // $this->buyers = MsBuyer::select('id', 'code', 'name', 'address', 'country', 'status', 'updated_by', 'updated_on')
        //     ->when(isset($this->searchTerm) && $this->searchTerm != "" && $this->searchTerm != "undefined", function ($query) {
        //         $query->where(function ($queryWhere) {
        //             $queryWhere->where('code', 'ilike', "%" . $this->searchTerm . "%")
        //                 ->orWhere('name', 'ilike', "%" . $this->searchTerm . "%")
        //                 ->orWhere('address', 'ilike', "%" . $this->searchTerm . "%")
        //                 ->orWhere('country', 'ilike', "%" . $this->searchTerm . "%");
        //         });
        //     })
        //     ->get();
        // $this->render();
    }

    public function render()
    {
        $data = DB::table('msbuyer AS mb')
            ->select('mb.id', 'mb.code', 'mb.name', 'mb.address', 'mb.country', 'mb.status', 'mb.updated_by', 'mb.updated_on');
        $data = $data->get();

        return view('livewire.master-tabel.buyer', [
            'data' => $data,
        ])->extends('layouts.master');
    }

    public function rendered()
    {
        $this->dispatch('initDataTable');
    }
}
