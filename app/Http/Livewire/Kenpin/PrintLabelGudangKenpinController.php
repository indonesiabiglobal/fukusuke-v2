<?php

namespace App\Http\Livewire\Kenpin;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class PrintLabelGudangKenpinController extends Component
{
    public $nomor_palet;
    public $data = [];
    public $code;
    public $name;

    public function search ()
    {
        $this->render();
    }

    public function print()
    {
        $data = collect(DB::select("
        SELECT
            tod.processdate,
            tod.po_no,
            tod.order_date,
            mp.code,
            mp.name,
            mp.ketebalan||'x'||mp.diameterlipat||'x'||mp.productlength as dimensi,
            tod.order_qty,
            tod.stufingdate,
            tod.etddate,
            tod.etadate,
            mbu.name as namabuyer
        FROM
            tdorder AS tod
            INNER JOIN msproduct AS mp ON mp.ID = tod.product_id
            INNER JOIN msbuyer AS mbu ON mbu.ID = tod.buyer_id
        WHERE
            tod.id = '12766'
        "))->first();

        $this->dispatch('redirectToPrint', $data);

    }

    public function render()
    {
        if(isset($this->nomor_palet) && $this->nomor_palet != ''){
            $this->data = DB::select("
            SELECT
                tdpg.nomor_lot,
                msm.machinename,
                tdpg.production_date,
                tdpg.qty_produksi,
                tdpg.nomor_palet,
                msp.name,
                msp.code
            FROM
                tdproduct_goods AS tdpg
                INNER JOIN msmachine AS msm ON msm.id = tdpg.machine_id
                INNER JOIN msproduct AS msp ON msp.id = tdpg.product_id
            WHERE
                tdpg.nomor_palet='$this->nomor_palet'");

            if($this->data == null){
                $this->dispatchBrowserEvent('notification', ['type' => 'error', 'message' => 'Palet ' . $this->nomor_palet . ' Tidak Terdaftar']);
            } else {
                $this->code = $this->data[0]->code;
                $this->name = $this->data[0]->name;
            }
        }

        return view('livewire.kenpin.print-label-gudang')->extends('layouts.master');
    }
}
