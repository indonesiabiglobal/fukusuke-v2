<?php

namespace App\Exports;

use App\Models\TdOrders;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
// use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class OrderEntryImport implements ToModel, WithHeadingRow
{
    // public function __construct()
    // {

    // }

    public function model(array $row)
    {
        return new TdOrders([
            'processdate' => $row['tg_proses'],
            'po_no' => $row['po_number'],
            'order_date' => $row['tg_order'],
            'product_code' => $row['no_order'],
            'order_qty' => $row['qty_order'],
            'order_unit' => $row['unit'],
            'stufingdate' => $row['tg_stufing'],
            'etddate' => $row['tg_etd'],
            'etadate' => $row['tg_eta'],
            'buyer_id' => $row['kode_buyer'],
        ]);
    }
}

