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
        return new Collection([
            [
                'TG_PROSES' => 15032018, 
                'PO_NUMBER' => 'PO001', 
                'TG_ORDER' => '14032018',
                'NO_ORDER' => 'UH244R1',
                'QTY_ORDER' => '12',
                'UNIT' => '1',
                'TG_STUFING' => '16032018',
                'TG_ETD' => '16032018',
                'TG_ETA' => '16032018',
                'KODE_BUYER' => '1001',
            ],
        ]);
        // return $data = DB::table('tdorder AS tod')
        //     ->select('tod.id', 'tod.product_code','tod.processdate', 'tod.order_date', 'tod.po_no','tod.product_code', 'mp.name AS produk_name', 'mp.id as mp_id', 'tod.order_qty', 'mbu.name AS buyer_name', 'tod.stufingdate', 'tod.etddate', 'tod.etadate', 'mbu.name AS buyer_name')
        //     ->leftjoin('msproduct AS mp', 'mp.id', '=', 'tod.product_id')
        //     ->leftjoin('msbuyer AS mbu', 'mbu.id', '=', 'tod.buyer_id');            

        // if (isset($this->tglMasuk) && $this->tglMasuk != "" && $this->tglMasuk != "undefined") {
        //     $data = $data->where('tod.order_date', '>=', $this->tglMasuk);
        // }

        // if (isset($this->tglKeluar) && $this->tglKeluar != "" && $this->tglKeluar != "undefined") {
        //     $data = $data->where('tod.order_date', '<=', $this->tglKeluar);
        // }

        // if (isset($this->searchTerm) && $this->searchTerm != "" && $this->searchTerm != "undefined") {
        //     $data = $data->where(function($query) {
        //         $query->where('mp.name', 'ilike', "%{$this->searchTerm}%")
        //               ->orWhere('mbu.name', 'ilike', "%{$this->searchTerm}%")
        //               ->orWhere('tod.po_no', 'ilike', "%{$this->searchTerm}%");
        //     });
        // }

        // if (isset($this->idProduct) && $this->idProduct['value'] != "" && $this->idProduct != "undefined") {
            
        //     $data = $data->where('mp.id', $this->idProduct);
        // }

        // if (isset($this->idBuyer) && $this->idBuyer['value'] != "" && $this->idBuyer != "undefined") {
        //     $data = $data->where('tod.buyer_id', $this->idBuyer);
        // }

        // if (isset($this->status) && $this->status['value'] != "" && $this->status != "undefined") {
        //     $data = $data->where('tod.status_order', $this->status);
        // }

        // $data = $data->get();
    }

    public function headings(): array
    {
        return [
            'No', 'Tanggal Proses', 'Nomor Proces', 'Tanggal Order', 'PO Number', 'Order No', 'Nama Produk', 'Kode Tipe', 'Jumlah Order', 'Unit', 'Tanggal Stufing', 'Tanggal Etd', 'Tanggal ETA', 'Buyer'
        ];
    }
}
