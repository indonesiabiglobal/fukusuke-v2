<?php

namespace App\Http\Livewire\MasterTabel;

use App\Models\MsBuyer;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class BuyerController extends Component
{
    public $buyers;
    public $searchTerm;
    public $code;
    public $name;
    public $address;
    public $city;
    public $country;

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

    public function showModalCreate()
    {
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

        $this->code = $buyer->code;
        $this->name = $buyer->name;
        $this->address = $buyer->address;
        $this->city = $buyer->city;
        $this->country = $buyer->country;

        // $this->dispatch('showModalUpdate', $buyer);
    }

    public function update()
    {
        $this->validate();

        DB::beginTransaction();
        try {
            $statusActive = 1;
            $msbuyer = MsBuyer::where('code', $this->code)->first();
            $msbuyer->name = $this->name;
            $msbuyer->city = $this->city;
            $msbuyer->address = $this->address;
            $msbuyer->country = $this->country;
            $msbuyer->status = $statusActive;
            $msbuyer->updated_by = Auth::user()->username;
            $msbuyer->updated_on = Carbon::now();
            $msbuyer->save();
            DB::commit();
            $this->buyers = MsBuyer::get(['id', 'code', 'name', 'address', 'country', 'status', 'updated_by', 'updated_on']);
            $this->dispatch('closeModalUpdate');
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Master Buyer updated successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update master buyer: ' . $e->getMessage());
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Failed to update the buyer: ' . $e->getMessage()]);
        }
    }

    public function search()
    {
        $this->buyers = MsBuyer::select('id', 'code', 'name', 'address', 'country', 'status', 'updated_by', 'updated_on')
            ->when(isset($this->searchTerm) && $this->searchTerm != "" && $this->searchTerm != "undefined", function ($query) {
                $query->where(function ($queryWhere) {
                    $queryWhere->where('code', 'ilike', "%" . $this->searchTerm . "%")
                        ->orWhere('name', 'ilike', "%" . $this->searchTerm . "%")
                        ->orWhere('address', 'ilike', "%" . $this->searchTerm . "%")
                        ->orWhere('country', 'ilike', "%" . $this->searchTerm . "%");
                });
            })
            ->get();
        $this->render();
    }

    public function render()
    {
        return view('livewire.master-tabel.buyer')->extends('layouts.master');
    }
}