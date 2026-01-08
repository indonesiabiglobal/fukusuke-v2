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
            $userAccess = auth()->user()->roles->flatMap->access->pluck('code')->unique()->toArray();
            if (in_array('DASHBOARD-SEITAI', $userAccess)) {
                return redirect()->intended('/dashboard-seitai');
            } else {
                return redirect()->intended('/dashboard-infure');
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
