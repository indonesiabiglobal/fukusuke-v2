<?php

namespace App\Http\Livewire\Administration;

use Livewire\Component;

class AddUserController extends Component
{
    public function cancel()
    {
        return redirect()->route('security-management');
    }

    public function render()
    {
        return view('livewire.administration.add-user')->extends('layouts.master');
    }
}
