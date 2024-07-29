<?php

namespace App\Http\Livewire\MasterTabel\Loss;

use App\Models\MsLossClass;
use Livewire\Component;

class MenuLossSeitaiController extends Component
{
    public $searchTerm;
    public $code;
    public $name;
    public $loss_class_id;
    public $loss_category_code;
    public $idUpdate;
    public $idDelete;
    public $class;

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
    
    public function render()
    {
        return view('livewire.master-tabel.loss.menu-loss-seitai')->extends('layouts.master');
    }
}
