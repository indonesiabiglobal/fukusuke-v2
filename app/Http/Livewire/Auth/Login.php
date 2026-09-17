<?php

namespace App\Http\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Services\AccessService;

class Login extends Component
{
    public $email;
    public $password;

    protected $rules = [
        'email' => 'required|string|email|max:255',
        'password' => 'required',
    ];

    public function mount()
    {
        if (auth()->user()) {
            // Same landing resolver as submit() below — an already-authenticated
            // user revisiting /login must land on a route their Access codes
            // actually grant, not a stale Role-code guess that can now 403
            // (e.g. Role 'ADMIN' without Access 'NIPPO-INFURE').
            return redirect()->intended(app(AccessService::class)->landingRouteFor(auth()->user()));
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
            return redirect()->intended(app(AccessService::class)->landingRouteFor(auth()->user()));
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
