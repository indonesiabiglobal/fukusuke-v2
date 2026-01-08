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
        $this->userrole = User::where('status', 1)->get();
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
        $query = User::with('roles');

        // Filter by status
        if (isset($this->status) && $this->status != "" && $this->status != "undefined") {
            $query->where('status', $this->status);
        }

        // Filter by search term
        if (isset($this->searchTerm) && $this->searchTerm != "") {
            $query->where(function($q) {
                $q->where('username', 'like', '%' . $this->searchTerm . '%')
                  ->orWhere('empname', 'like', '%' . $this->searchTerm . '%')
                  ->orWhere('email', 'like', '%' . $this->searchTerm . '%');
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
