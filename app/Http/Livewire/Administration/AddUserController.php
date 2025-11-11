<?php

namespace App\Http\Livewire\Administration;

use App\Models\User;
use App\Models\UserRoles;
use App\Models\UserAccessRole;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AddUserController extends Component
{
    public $username;
    public $email;
    public $empname;
    public $password;
    public $password_confirmation;
    public $roleid;
    public $rolemode = 'readwrite';
    public $status = 1;
    public $empid;
    public $code;
    public $territory_ix;

    public $userroles;

    protected $rules = [
        'username' => 'required|min:3|unique:users,username',
        'email' => 'required|email|unique:users,email',
        'empname' => 'required|min:3',
        'password' => 'required|min:6|confirmed',
        'roleid' => 'required|exists:userroles,id',
        'status' => 'required|in:0,1',
    ];

    protected $messages = [
        'username.required' => 'Username wajib diisi',
        'username.min' => 'Username minimal 3 karakter',
        'username.unique' => 'Username sudah digunakan',
        'email.required' => 'Email wajib diisi',
        'email.email' => 'Format email tidak valid',
        'email.unique' => 'Email sudah digunakan',
        'empname.required' => 'Nama karyawan wajib diisi',
        'empname.min' => 'Nama karyawan minimal 3 karakter',
        'password.required' => 'Password wajib diisi',
        'password.min' => 'Password minimal 6 karakter',
        'password.confirmed' => 'Konfirmasi password tidak cocok',
        'roleid.required' => 'Role wajib dipilih',
        'roleid.exists' => 'Role tidak valid',
        'status.required' => 'Status wajib dipilih',
    ];

    public function mount()
    {
        $this->userroles = UserRoles::where('status', 1)->get();
    }

    public function save()
    {
        $this->validate();

        try {
            DB::beginTransaction();

            // Create user
            $user = User::create([
                'username' => $this->username,
                'email' => $this->email,
                'empname' => $this->empname,
                'password' => Hash::make($this->password),
                'empid' => $this->empid,
                'code' => $this->code,
                'territory_ix' => $this->territory_ix,
                'status' => $this->status,
                'createby' => Auth::user()->username ?? 'system',
                'createdt' => now(),
            ]);

            // Create user access role
            UserAccessRole::create([
                'userid' => $user->id,
                'roleid' => $this->roleid,
                'rolemode' => $this->rolemode,
            ]);

            DB::commit();

            session()->flash('success', 'User berhasil ditambahkan!');

            return redirect()->route('security-management');

        } catch (\Exception $e) {
            DB::rollBack();

            session()->flash('error', 'Gagal menambahkan user: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.administration.add-user')
            ->extends('layouts.master');
    }
}
