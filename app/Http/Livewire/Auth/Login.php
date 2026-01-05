<?php

namespace App\Http\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Login extends Component
{
    public $email;
    public $password;
    public $userRoles = [];

    protected $rules = [
        'email' => 'required|string|email|max:255',
        'password' => 'required',
    ];

    public function mount()
    {
        if (auth()->user()) {
            $this->userRoles = auth()->user()->roles->pluck('code')->toArray();

            if (in_array('ADMIN', $this->userRoles) || in_array('DASHBOARD-INFURE', $this->userRoles)) {
                return redirect()->intended('/dashboard-infure');
            } elseif (in_array('DASHBOARD-SEITAI', $this->userRoles)) {
                return redirect()->intended('/dashboard-seitai');
            }
        }
    }

    public function submit()
    {
        // validate the data
        $this->validate();

        $user = array(
            'email' => $this->email,
            'password' => $this->password,
        );

        if (Auth::attempt($user)) {
            if (in_array('ADMIN', auth()->user()->roles->pluck('code')->toArray()) || in_array('DASHBOARD-INFURE', auth()->user()->roles->pluck('code')->toArray())) {
                return redirect()->intended('/dashboard-infure');
            } elseif (in_array('DASHBOARD-SEITAI', auth()->user()->roles->pluck('code')->toArray())) {
                return redirect()->intended('/dashboard-seitai');
            }
        } else {
            $this->addError('email', trans('auth.failed'));
            return redirect()->back();
        }
    }

    public function render()
    {
        return view('livewire.auth.login')->extends('layouts.master-without-nav');
    }
}
