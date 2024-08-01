<?php

namespace App\Http\Livewire\Auth;

use Livewire\Component;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class NewPassword extends Component
{
    public $userId;
    public $email;
    // public $token;
    public $password;
    public $password_confirmation1;
    public $password_confirmation2;

    // protected $rules = [
    //     'password' => 'required|string|min:8|confirmed'
    // ];

   public function mount(){
       $this->userId = Auth::user()->id;
    //    $this->token = $token;
   }

    public function resetPassword() {
        
        // $this->validate();

        if($this->userId != null) {
            User::where('id', $this->userId)->update([
                'password' => Hash::make($this->password_confirmation2)
            ]);
            return redirect('/login')->with('success', 'Your password has been reseted.!');
        }

        return redirect()->back()->with('error', 'Something went wrong!');
    }

    public function render()
    {
        return view('livewire.auth.new-password')->extends('layouts.master');
    }
}
