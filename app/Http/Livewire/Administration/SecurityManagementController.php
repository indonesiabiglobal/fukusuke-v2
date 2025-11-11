<?php

namespace App\Http\Livewire\Administration;

use App\Models\User;
use App\Models\UserRoles;
use App\Models\UserAccessRole;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;

class SecurityManagementController extends Component
{
    protected $paginationTheme = 'bootstrap';

    public $userrole;
    public $idRole;
    public $searchTerm;
    public $status;
    public $deleteId;
    public $sortingTable = [[1, 'asc']];

    use WithPagination, WithoutUrlPagination;

    protected $listeners = ['deleteConfirmed' => 'delete'];

    public function mount()
    {
        $this->userrole = UserRoles::where('status', 1)->get();
    }

    public function search()
    {
        $this->resetPage();
        $this->render();
    }

    public function updateSortingTable($order)
    {
        $this->sortingTable = $order;
    }

    public function setDeleteId($id)
    {
        $this->deleteId = $id;
    }

    public function delete()
    {
        try {
            DB::beginTransaction();

            // Hapus relasi di useraccess_role
            UserAccessRole::where('userid', $this->deleteId)->delete();

            // Hapus user
            User::find($this->deleteId)->delete();

            DB::commit();

            $this->dispatch('showToast', [
                'type' => 'success',
                'message' => 'User berhasil dihapus!'
            ]);

            $this->deleteId = null;
            $this->dispatch('closeModal');

        } catch (\Exception $e) {
            DB::rollBack();

            $this->dispatch('showToast', [
                'type' => 'error',
                'message' => 'Gagal menghapus user: ' . $e->getMessage()
            ]);
        }
    }

    public function render()
    {
        $query = DB::table('users')
            ->select(
                'users.id',
                'users.username',
                'users.email',
                'users.empname',
                'userroles.description as job',
                DB::raw("CASE WHEN users.status = 0 THEN 'Inactive' ELSE 'Active' END AS status")
            )
            ->join('useraccess_role AS uar', 'users.id', '=', 'uar.userid')
            ->join('userroles', 'uar.roleid', '=', 'userroles.id');

        // Filter by role
        if (isset($this->idRole) && $this->idRole != "" && $this->idRole != "undefined") {
            $query->where('uar.roleid', $this->idRole);
        }

        // Filter by status
        if (isset($this->status) && $this->status != "" && $this->status != "undefined") {
            $query->where('users.status', $this->status);
        }

        // Filter by search term
        if (isset($this->searchTerm) && $this->searchTerm != "") {
            $query->where(function($q) {
                $q->where('users.username', 'like', '%' . $this->searchTerm . '%')
                  ->orWhere('users.empname', 'like', '%' . $this->searchTerm . '%')
                  ->orWhere('users.email', 'like', '%' . $this->searchTerm . '%');
            });
        }

        $data = $query->get();

        return view('livewire.administration.security-management', [
            'data' => $data
        ])->extends('layouts.master');
    }

    public function rendered()
    {
        $this->dispatch('initDataTable');
    }
}
