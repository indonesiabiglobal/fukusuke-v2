<?php

namespace App\Http\Livewire\Administration;

use App\Models\UserRoles;
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

    use WithPagination, WithoutUrlPagination;

    public function mount(){
        $this->userrole = UserRoles::get();
    }

    public function search(){
        $this->resetPage();
        $this->render();
    }

    public function render()
    {
        $data = DB::table('users')
            ->select(
                'id',
                'username',
                'email',
                'empname',
                'status as role',
                DB::raw("'' AS job"),
                DB::raw("CASE WHEN status = 0 THEN 'Inactive' ELSE 'Active' END AS status")
            )
            ->join('useraccess_role AS uar', 'users.id', '=', 'uar.userid');
            if (isset($this->idRole) && $this->idRole['value'] != "" && $this->idRole != "undefined") {
                $data = $data->where('uar.roleid', $this->idRole);
            }
            // if (isset($this->status) && $this->status['value'] != "" && $this->status != "undefined") {
            //     if($this->status['value'] == 0){
            //         $data = $data->where('status', 0);
            //     } else {
            //         $data = $data->where('status', 1);
            //     }
            // }
            $data = $data->get();

        return view('livewire.administration.security-management', [
            'data' => $data
        ])->extends('layouts.master');
    }

    public function rendered()
    {
        $this->dispatch('initDataTable');
    }
}
