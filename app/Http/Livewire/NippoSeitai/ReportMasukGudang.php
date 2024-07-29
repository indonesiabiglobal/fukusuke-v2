<?php

namespace App\Http\Livewire\NippoSeitai;

use Livewire\Component;

class ReportMasukGudang extends Component
{
    public $nomor_palet;


    public function render()
    {

        $this->nomor_palet=$nomor_palet;

        dd( $this->nomor_palet);
        $data = collect(DB::select("
        SELECT 
        tdpg.production_date AS production_date, 
        tdpg.nomor_palet AS nomor_palet, 
            tdpg.nomor_lot AS nomor_lot,
                tdpg.work_shift AS work_shift,  
                tdpg.employee_id AS employee_id,
                me.empname as namapetugas,
                tdpg.product_id AS product_id,
                mp.name as namaproduk,
                mp.palet_jumlah_baris as tinggi,
                mp.palet_isi_baris as jmlbaris,
                tdpg.qty_produksi AS qty_produksi
            FROM  tdProduct_Goods AS tdpg
            left JOIN tdOrderLpk AS tdol ON tdpg.lpk_id = tdol.id
                left join msproduct as mp on mp.id=tdpg.product_id
                left join msemployee as me on me.id=tdpg.employee_id
            WHERE tdpg.nomor_palet = (LTRIM(RTRIM('G2524-040724')))
        "))->first();

        return view('livewire.nippo-seitai.report-masuk-gudang');
    }
}
