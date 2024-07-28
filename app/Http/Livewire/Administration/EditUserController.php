<?php

namespace App\Http\Livewire\Administration;

use Livewire\Component;

class EditUserController extends Component
{
    public function cancel()
    {
        return redirect()->route('security-management');
    }
    
    public function render()
    {
        return view('livewire.administration.edit-user')->extends('layouts.master');
    }
}
