<?php

namespace App\Http\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Services\AccessService;

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
            $this->userRoles = Cache::remember(
                'user_roles_' . auth()->id(),
                600,
                fn() => auth()->user()->roles->pluck('code')->toArray()
            );

            if (in_array('ADMIN', $this->userRoles) || in_array('NIPPO-INFURE', $this->userRoles)) {
                return redirect()->intended('/nippo-infure');
            } elseif (in_array('NIPPO-SEITAI', $this->userRoles)) {
                return redirect()->intended('/nippo-seitai');
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
            if ((int) auth()->user()->status === 0) {
                Auth::logout();
                $this->addError('email', trans('auth.failed'));
                return;
            }

            // Cegah session fixation: ganti session ID setelah login berhasil
            session()->regenerate();

            // Cache access saat login agar sidebar tidak query ulang saat halaman pertama dibuka
            $userAccess = app(AccessService::class)->codesFor(auth()->user());
            if (in_array('DASHBOARD-SEITAI', $userAccess)) {
                return redirect()->intended('/nippo-seitai');
            } else {
                return redirect()->intended('/nippo-infure');
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
