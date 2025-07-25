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
        $lpk_id = $this->lpk_id;
        if (!(isset($this->lpk_id) && $this->lpk_id != '')) {
            $this->dispatch('notification', ['type' => 'warning', 'message' => 'No LPK Tidak Terdaftar']);
            return;
        }
        $this->dispatch('redirectToPrint', $lpk_id);


        $reprint_no = TdOrderLpk::where('lpk_no', $this->lpk_no)->first();
        TdOrderLpk::where('lpk_no', $this->lpk_no)->update([
            'reprint_no' => $reprint_no->reprint_no + 1,
        ]);

        $this->lpk_id =  '';
        $this->lpk_date = '';
        $this->qty_lpk = '';
        $this->code = '';
        $this->product_name = '';
        $this->reprint_no = '';
        $this->lpk_no = '';
    }

    public function render()
    {
        if (isset($this->lpk_no) && $this->lpk_no != '' && strlen($this->lpk_no) == 10) {
            if (!str_contains($this->lpk_no, '-') && strlen($this->lpk_no) >= 9) {
                $this->lpk_no = substr_replace($this->lpk_no, '-', 6, 0);
            }
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
            if ($data == null) {
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
        return view('livewire.order-lpk.cetak-lpk', [
            'results' => $this->results,
        ])->extends('layouts.master');
    }
}
