<?php

namespace App\Http\Livewire\Administration;

use App\Models\User;
use App\Models\Role;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AddUserController extends Component
{
    public $username;
    public $email;
    public $empname;
    public $password;
    public $password_confirmation;
    public $selectedRoles = [];
    public $status = 1;
    public $empid;
    public $code;
    public $territory_ix;

    public $userroles;

    public function mount()
    {
        $this->userroles = Role::where('status', 1)->get();
    }

    public function rules()
    {
        return [
            'username' => 'required|min:3|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'empname' => 'required|min:3',
            'password' => 'required|min:6|confirmed',
            'selectedRoles' => 'required|array|min:1',
            'selectedRoles.*' => 'exists:roles,id',
            'status' => 'required|in:0,1',
        ];
    }

    public function messages()
    {
        return [
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
            'selectedRoles.required' => 'Minimal pilih 1 role',
            'selectedRoles.*.exists' => 'Role tidak valid',
            'status.required' => 'Status wajib dipilih',
        ];
    }

    public function save()
    {
        // Validate
        $this->validate();

        DB::beginTransaction();
        try {
            // Log untuk debugging
            Log::info('Creating user', [
                'username' => $this->username,
                'email' => $this->email,
                'selected_roles' => $this->selectedRoles,
            ]);

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
                'createby' => Auth::check() ? Auth::user()->username : 'system',
                'createdt' => now(),
            ]);

            Log::info('User created successfully', ['user_id' => $user->id]);

            // Sync multiple roles to user
            $user->roles()->sync($this->selectedRoles);

            Log::info('User roles synced', ['user_id' => $user->id, 'roles' => $this->selectedRoles]);

            DB::commit();

            // Dispatch notification (sama seperti AddNippoController)
            $this->dispatch('notification', ['type' => 'success', 'message' => 'User berhasil ditambahkan!']);

            // Redirect
            return redirect()->route('security-management');

        } catch (\Exception $e) {
            DB::rollBack();

            // Log error
            Log::error('Failed to create user', [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);

            // Dispatch notification error (jangan redirect, biar user lihat error)
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Gagal menambahkan user: ' . $e->getMessage()]);
        }
    }

    public function cancel()
    {
        return redirect()->route('security-management');
    }

    public function render()
    {
        return view('livewire.administration.add-user')
            ->extends('layouts.master');
    }
}
