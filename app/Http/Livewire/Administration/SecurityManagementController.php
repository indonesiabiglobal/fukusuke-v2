<?php

namespace App\Http\Livewire\Administration;

use App\Models\UserRoles;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;

class SecurityManagementController extends Component
{
    public $userrole;
    public $idRole;
    public $searchTerm;

    use WithPagination, WithoutUrlPagination;
    // public $data=[];

    public function mount(){
        $this->userrole = UserRoles::get();
    }

    public function render()
    {
        $data = DB::table('tdorder AS tod')
            ->select('tod.id', 'tod.po_no', 'mp.name AS produk_name', 'tod.product_code',
                     'mbu.name AS buyer_name', 'tod.order_qty', 'tod.order_date',
                     'tod.stufingdate', 'tod.etddate', 'tod.etadate',
                     'tod.processdate', 'tod.processseq', 'tod.updated_by', 'tod.updated_on')
            ->leftjoin('msproduct AS mp', 'mp.id', '=', 'tod.product_id')
            ->leftjoin('msbuyer AS mbu', 'mbu.id', '=', 'tod.buyer_id')
            ->paginate();

        return view('livewire.administration.security-management')->extends('layouts.master');
    }
}
