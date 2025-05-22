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
use PhpOffice\PhpSpreadsheet\Shared\Date;

class OrderEntryImport implements ToModel, WithHeadingRow
{
    // public function __construct()
    // {

    // }

    public function model(array $row)
    {
        try {
            // $poNumber = TdOrders::where('po_no', $row['po_number'])->exists();
            // if ($poNumber) {
            //     throw new \Exception('PO Number ' . $row['po_number'] . ' sudah ada');
            // }

            // mengecek format tanggal proses
            if (is_numeric($row['tg_proses'])) {
                $tanggalProses = Date::excelToDateTimeObject($row['tg_proses']);
            } else {
                $tanggalProses = $row['tg_proses'];
            }

            // mengecek format tanggal order
            if (is_numeric($row['tg_order'])) {
                $tanggalOrder = Date::excelToDateTimeObject($row['tg_order']);
            } else {
                $tanggalOrder = $row['tg_order'];
            }

            // mengecek format tanggal stufing
            if (is_numeric($row['tg_stufing'])) {
                $tanggalStufing = Date::excelToDateTimeObject($row['tg_stufing']);
            } else {
                $tanggalStufing = $row['tg_stufing'];
            }

            // mengecek format tanggal etd
            if (is_numeric($row['tg_etd'])) {
                $tanggalEtd = Date::excelToDateTimeObject($row['tg_etd']);
            } else {
                $tanggalEtd = $row['tg_etd'];
            }

            // mengecek format tanggal eta
            if (is_numeric($row['tg_eta'])) {
                $tanggalEta = Date::excelToDateTimeObject($row['tg_eta']);
            } else {
                $tanggalEta = $row['tg_eta'];
            }

            $product = MsProduct::where('code', $row['no_order'])->first();
            if (!$product) {
                throw new \Exception('Product dengan kode ' . $row['no_order'] . ' tidak ditemukan');
            }

            $buyer = MsBuyer::where('code', $row['kode_buyer'])->first();
            if (!$buyer) {
                throw new \Exception('Buyer dengan kode ' . $row['kode_buyer'] . ' tidak ditemukan');
            }

            $maxProcessSeq = TdOrders::where('order_date', $tanggalOrder)->max('processseq');


            return new TdOrders([
                'processseq' => $maxProcessSeq + 1,
                'processdate' => $tanggalProses,
                'po_no' => $row['po_number'],
                'order_date' => $tanggalOrder,
                'product_id' => $product->id,
                'product_code' => $row['no_order'],
                'order_qty' => $row['qty_order'],
                'order_unit' => $row['unit'],
                'stufingdate' => $tanggalStufing,
                'etddate' => $tanggalEtd,
                'etadate' => $tanggalEta,
                'buyer_id' => $buyer->id,
                'created_on' => Carbon::now(),
                'created_by' => auth()->user()->username,
                'updated_on' => Carbon::now(),
                'updated_by' => auth()->user()->username,
            ]);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
