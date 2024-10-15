<?php

namespace App\Exports;

use Carbon\Carbon;
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

    public function __construct(
        $tglMasuk,
        $tglKeluar,
        // $searchTerm, $idProduct, $idBuyer, $status
    ) {
        $this->tglMasuk = $tglMasuk;
        $this->tglKeluar = $tglKeluar;
        // $this->searchTerm = $searchTerm;
        // $this->idProduct = $idProduct;
        // $this->idBuyer = $idBuyer;
        // $this->status = $status;
    }

    public function collection()
    {
        $tglMasuk = Carbon::now();
        if (isset($this->tglMasuk) && $this->tglMasuk != '') {
            $tglMasuk = Carbon::createFromFormat('d M Y', $this->tglMasuk)->startOfDay();
        }
        $tglKeluar = Carbon::now();
        if (isset($this->tglKeluar) && $this->tglKeluar != '') {
            $tglKeluar = Carbon::createFromFormat('d M Y', $this->tglKeluar)->endOfDay();
        }

        $data =  collect(DB::select("
        select
        tod.processdate,
        tod.processseq,
        tod.order_date,
        tod.po_no,
        tod.product_code,
        mp.name as produk_name,
        mp.product_type_code,
        tod.order_qty,
        CASE
            WHEN tod.order_unit = '0' THEN 'Set'
            WHEN tod.order_unit = '1' THEN 'Lembar'
            WHEN tod.order_unit = '2' THEN 'Meter'
            ELSE 'PCS'
        END as unit,
        tod.product_code,
        tod.stufingdate,
        tod.etddate,
        tod.etadate,
        mbu.name as buyer_name,
        CASE
            WHEN todl.status_lpk = '0' THEN 'Belum LPK'
            WHEN todl.status_lpk = '1' THEN 'Sudah LPK'
            ELSE ''
        END as status_lpk
        from tdorder as tod
        left join tdorderlpk as todl on todl.order_id = tod.id
        left join msproduct as mp on mp.id = tod.product_id
        left join msbuyer as mbu on mbu.id = tod.buyer_id
        where tod.processdate >= '$tglMasuk' and tod.processdate <= '$tglKeluar'
        "));

        // Menambahkan kolom nomor urut di awal array
        $data = $data->map(function ($item, $key) {
            $item = (array) $item;
            $item = array_merge(['no' => $key + 1], $item);
            return $item;
        });

        return $data;
    }

    public function headings(): array
    {
        return [
            'No', 'Tanggal Proses', 'Nomor Proces', 'Tanggal Order', 'PO Number', 'Order No', 'Nama Produk', 'Kode Tipe', 'Jumlah Order', 'Unit', 'Tanggal Stufing', 'Tanggal Etd', 'Tanggal ETA', 'Buyer'
        ];
    }
}
