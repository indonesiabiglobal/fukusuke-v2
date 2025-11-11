<?php

namespace App\Http\Livewire\Administration;

use App\Models\User;
use App\Models\UserRoles;
use App\Models\UserAccessRole;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class EditUserController extends Component
{
    public $userId;
    public $username;
    public $email;
    public $empname;
    public $password;
    public $password_confirmation;
    public $roleid;
    public $rolemode = 'readwrite';
    public $status;
    public $empid;
    public $code;
    public $territory_ix;

    public $userroles;
    public $user;

    protected function rules()
    {
        return [
            'username' => 'required|min:3|unique:users,username,' . $this->userId,
            'email' => 'required|email|unique:users,email,' . $this->userId,
            'empname' => 'required|min:3',
            'password' => 'nullable|min:6|confirmed',
            'roleid' => 'required|exists:userroles,id',
            'status' => 'required|in:0,1',
        ];
    }

    protected $messages = [
        'username.required' => 'Username wajib diisi',
        'username.min' => 'Username minimal 3 karakter',
        'username.unique' => 'Username sudah digunakan',
        'email.required' => 'Email wajib diisi',
        'email.email' => 'Format email tidak valid',
        'email.unique' => 'Email sudah digunakan',
        'empname.required' => 'Nama karyawan wajib diisi',
        'empname.min' => 'Nama karyawan minimal 3 karakter',
        'password.min' => 'Password minimal 6 karakter',
        'password.confirmed' => 'Konfirmasi password tidak cocok',
        'roleid.required' => 'Role wajib dipilih',
        'roleid.exists' => 'Role tidak valid',
        'status.required' => 'Status wajib dipilih',
    ];

    public function mount()
    {
        $this->userId = request()->query('orderId');

        if (!$this->userId) {
            return redirect()->route('security-management');
        }

        $this->user = User::with('accessRoles')->find($this->userId);

        if (!$this->user) {
            session()->flash('error', 'User tidak ditemukan');
            return redirect()->route('security-management');
        }

        // Load data
        $this->username = $this->user->username;
        $this->email = $this->user->email;
        $this->empname = $this->user->empname;
        $this->empid = $this->user->empid;
        $this->code = $this->user->code;
        $this->territory_ix = $this->user->territory_ix;
        $this->status = $this->user->status;

        // Load role data
        $accessRole = $this->user->accessRoles->first();
        if ($accessRole) {
            $this->roleid = $accessRole->roleid;
            $this->rolemode = $accessRole->rolemode;
        }

        $this->userroles = UserRoles::where('status', 1)->get();
    }

    public function update()
    {
        $this->validate();

        try {
            DB::beginTransaction();

            // Update user data
            $updateData = [
                'username' => $this->username,
                'email' => $this->email,
                'empname' => $this->empname,
                'empid' => $this->empid,
                'code' => $this->code,
                'territory_ix' => $this->territory_ix,
                'status' => $this->status,
                'updateby' => Auth::user()->username ?? 'system',
                'updatedt' => now(),
            ];

            // Update password if provided
            if ($this->password) {
                $updateData['password'] = Hash::make($this->password);
            }

            $this->user->update($updateData);

            // Update or create user access role
            UserAccessRole::updateOrCreate(
                ['userid' => $this->userId],
                [
                    'roleid' => $this->roleid,
                    'rolemode' => $this->rolemode,
                ]
            );

            DB::commit();

            session()->flash('success', 'User berhasil diupdate!');

            return redirect()->route('security-management');

        } catch (\Exception $e) {
            DB::rollBack();

            session()->flash('error', 'Gagal mengupdate user: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.administration.edit-user')
            ->extends('layouts.master');
    }
}
