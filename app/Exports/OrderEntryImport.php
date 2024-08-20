<?php

namespace App\Exports;

use App\Models\MsBuyer;
use App\Models\MsProduct;
use App\Models\TdOrders;
use Carbon\Carbon;
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
        try {
            $poNumber = TdOrders::where('po_no', $row['po_number'])->exists();
            if ($poNumber) {
                throw new \Exception('PO Number '. $row['po_number'] .' sudah ada');
            }
            $row['tg_order'] = Carbon::parse(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['tg_order']));
            $row['tg_proses'] = Carbon::parse(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['tg_proses']));
            $row['tg_stufing'] = Carbon::parse(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['tg_stufing']));
            $row['tg_etd'] = Carbon::parse(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['tg_etd']));
            $row['tg_eta'] = Carbon::parse(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['tg_eta']));

            $product = MsProduct::where('code', $row['no_order'])->first();
            $buyer = MsBuyer::where('code', $row['kode_buyer'])->first();

            $maxProcessSeq = TdOrders::where('order_date', Carbon::now())->max('processseq');


            return new TdOrders([
                'processseq' => $maxProcessSeq + 1,
                'processdate' => $row['tg_proses'],
                'po_no' => $row['po_number'],
                'order_date' => $row['tg_order'],
                'product_id' => $product->id,
                'product_code' => $row['no_order'],
                'order_qty' => $row['qty_order'],
                'order_unit' => $row['unit'],
                'stufingdate' => $row['tg_stufing'],
                'etddate' => $row['tg_etd'],
                'etadate' => $row['tg_eta'],
                'buyer_id' => $buyer->id,
            ]);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
