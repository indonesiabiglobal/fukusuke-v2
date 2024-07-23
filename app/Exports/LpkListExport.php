<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\DB;

class LpkListExport implements FromCollection, WithHeadings
{
    protected $tglMasuk;
    protected $tglKeluar;

    public function __construct($tglMasuk, $tglKeluar)
    {
        $this->tglMasuk = $tglMasuk;
        $this->tglKeluar = $tglKeluar;
    }

    public function collection()
    {
        $tglMasuk = '';
        if (isset($this->tglMasuk) && $this->tglMasuk != '') {
            $tglMasuk = $this->tglMasuk . " 00:00:00";
        }
        $tglKeluar = '';
        if (isset($this->tglKeluar) && $this->tglKeluar != '') {
            $tglKeluar = $this->tglKeluar . " 23:59:59";
        }

        return collect(DB::select("
        select 
                tolp.id,
                tolp.created_on AS tglproses,
                tod.product_code,
                tolp.lpk_date,
                tolp.lpk_no,
                tod.po_no,
                tolp.seq_no,
                mp.NAME AS product_name,
                mm.machineno AS machine_no,
                tolp.qty_lpk,
                
                tolp.panjang_lpk,
                tolp.total_assembly_qty,
                tolp.qty_gulung,
                tolp.total_assembly_line AS infure,

                tolp.qty_gentan
            from tdorderlpk as tolp 
            inner join tdorder as tod on tod.id = tolp.order_id 
            inner join msproduct as mp on mp.id = tolp.product_id 
            inner join msmachine as mm on mm.id = tolp.machine_id 
            inner join msbuyer as mbu on mbu.id = tod.buyer_id 
            where tolp.created_on >= '$tglMasuk' and tolp.created_on <= '$tglKeluar'
        "));
    }

    public function headings(): array
    {
        return [
            'No', 'Tanggal Proses', 'Nomor Proces', 'Tanggal LPK', 'No LPK', 'PO Number', 'Order No', 'Nama Produk', 'No Mesin', 'Jumlah LPK', 'Unit', 'Jumlah Order', 'Total Meter', 'Panjang Gulung', 'Meter Gulung', 'Jumlah Gentan'
        ];
    }
}
