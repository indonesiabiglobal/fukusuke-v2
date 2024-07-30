<?php

namespace App\Http\Livewire\MasterTabel\Kemasan;

use Livewire\Component;

class LayerController extends Component
{
    public $searchTerm;
    public $code;
    public $name;
    public $loss_class_id;
    public $loss_category_code;
    public $idUpdate;
    public $idDelete;
    public $class;

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
        return view('livewire.master-tabel.kemasan.layer')->extends('layouts.master');
    }
}
