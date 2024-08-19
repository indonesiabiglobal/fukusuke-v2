<?php

namespace App\Http\Livewire\Administration;

use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Illuminate\Http\Request;

class EditUserController extends Component
{
    public function mount(Request $request)
    {
        $order = DB::table('tdorderlpk as tolp')
            ->select(
                'tolp.id',
                'tolp.order_id',
                'tolp.lpk_no',
                'tolp.lpk_date',
                'tolp.panjang_lpk',
                'tolp.qty_lpk',
                'tolp.qty_gentan',
                'tolp.qty_gulung',
                'tolp.total_assembly_line as infure',
                'tolp.total_assembly_qty',
                'tolp.total_assembly_line',
                'tolp.warnalpkid',
                'tolp.remark',
                'tod.po_no',
                'mp.name as product_name',
                'mp.code',
                'mp.ketebalan',
                'mp.diameterlipat',
                'mp.productlength',
                'tod.product_code',
                'tod.order_date',
                'mm.machineno',
                'mm.machinename',
                'mbu.id as buyer_id',
                'mbu.name as buyer_name',
                'tolp.created_on as tglproses',
                'mp.productlength',
                'tolp.seq_no',
                'tolp.updated_by',
                'tolp.updated_on as updatedt',
                'tolp.status_lpk'
            )
            ->join('tdorder as tod', 'tod.id', '=', 'tolp.order_id')
            ->leftJoin('msproduct as mp', 'mp.id', '=', 'tolp.product_id')
            ->join('msmachine as mm', 'mm.id', '=', 'tolp.machine_id')
            ->join('msbuyer as mbu', 'mbu.id', '=', 'tod.buyer_id')
            ->where('tolp.id', $request->query('orderId'))
            ->first();
    }

    public function cancel()
    {
        return redirect()->route('security-management');
    }

    public function render()
    {
        return view('livewire.administration.edit-user')->extends('layouts.master');
    }
}
