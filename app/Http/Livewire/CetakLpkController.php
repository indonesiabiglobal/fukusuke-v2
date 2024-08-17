<?php

namespace App\Http\Livewire;

use App\Models\TdOrderLpk;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class CetakLpkController extends Component
{
    public $lpk_no;
    public $lpk_id;
    public $lpk_date;
    public $code;
    public $product_name;
    public $qty_lpk;
    public $reprint_no;
    public $results;

    public function print()
    {
        $lpk_id= $this->lpk_id;
        if ($lpk_id == null) {
            $this->dispatch('notification', ['type' => 'warning', 'message' => 'No LPK Tidak Terdaftar']);
            return;
        }
        $this->dispatch('redirectToPrint', $lpk_id);
    }

    public function render()
    {
        if(isset($this->lpk_no) && $this->lpk_no != ''){
            $data = DB::table('tdorderlpk as tod')
            ->leftJoin('msproduct as mp', 'mp.id', '=', 'tod.product_id')
            ->select(
                'tod.id as lpk_id',
                'tod.lpk_date',
                'tod.qty_lpk',
                'tod.lpk_no',
                'mp.code',
                'mp.name as product_name',
                'tod.reprint_no as reprint_no'
            )
            ->where('tod.lpk_no', $this->lpk_no)
            ->first();
            if($data == null){
                $this->dispatch('notification', ['type' => 'warning', 'message' => 'No LPK ' . $this->lpk_no . ' Tidak Terdaftar']);
            } else {
                $this->lpk_id =  $data->lpk_id;
                $this->lpk_date = $data->lpk_date;
                $this->qty_lpk = $data->qty_lpk;
                $this->code = $data->code;
                $this->product_name = $data->product_name;
                $this->reprint_no = $data->reprint_no;
            }
        }
        // dd($this->results);
        return view('livewire.order-lpk.cetak-lpk', [
            'results' => $this->results,
        ])->extends('layouts.master');
    }
}
