<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class OrderLpkExport implements FromCollection, WithHeadings
{
    protected $tglMasuk;
    protected $tglKeluar;
    protected $searchTerm;
    protected $idProduct;
    protected $idBuyer;
    protected $status;

    public function __construct($tglMasuk, $tglKeluar, 
    // $searchTerm, $idProduct, $idBuyer, $status
    )
    {
        $this->tglMasuk = $tglMasuk;
        $this->tglKeluar = $tglKeluar;
        // $this->searchTerm = $searchTerm;
        // $this->idProduct = $idProduct;
        // $this->idBuyer = $idBuyer;
        // $this->status = $status;
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
        select tod.id, tod.po_no, mp.name as produk_name, tod.product_code, mbu.name as buyer_name, tod.order_qty, tod.order_date, tod.stufingdate, tod.etddate, tod.etadate, tod.processdate, tod.processseq, tod.updated_by, tod.updated_on 
        from tdorder as tod left join msproduct as mp on mp.id = tod.product_id left join msbuyer as mbu on mbu.id = tod.buyer_id 
        where tod.processdate >= '$tglMasuk' and tod.processdate <= '$tglKeluar'
        "));
    }

    public function headings(): array
    {
        return [
            'No', 'Tanggal Proses', 'Nomor Proces', 'Tanggal Order', 'PO Number', 'Order No', 'Nama Produk', 'Kode Tipe', 'Jumlah Order', 'Unit', 'Tanggal Stufing', 'Tanggal Etd', 'Tanggal ETA', 'Buyer'
        ];
    }
}
