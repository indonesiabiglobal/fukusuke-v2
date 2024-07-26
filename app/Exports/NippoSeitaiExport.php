<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class NippoSeitaiExport implements FromCollection, WithHeadings
{
    protected $tglMasuk;
    protected $tglKeluar;
    protected $searchTerm;
    protected $idProduct;
    protected $idBuyer;
    protected $status;

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
            tdol.lpk_date,
            tdpg.production_no,
            tdpg.production_date,
            tdpg.employee_id,
            tdpg.machine_id,
            tdol.lpk_no
        from tdproduct_goods as tdpg 
            inner join tdorderlpk as tdol on tdpg.lpk_id = tdol.id 
            left join tdproduct_goods_assembly as tga on tga.product_goods_id = tdpg.id 
            left join tdproduct_assembly as ta on ta.id = tga.product_assembly_id 
        where tdpg.production_date >= '$tglMasuk' and tdpg.production_date <= '$tglKeluar'
        "));
    }

    public function headings(): array
    {
        return [
            'Tanggal Proses', 'Nomor Proses', 'Tanggal Produksi', 'Petugas', 'Nomor Mesin', 'Nomor LPK', 'Nomor Order', 'Nama Produk', 'Quantity (Lembar)', 'Loss Infure', 'NIK Infure', 'Nomor Palet', 'Nomor LOT', 'Nomor Gentan', 'Nama Loss', 'Berat (kg)'
        ];
    }
}
