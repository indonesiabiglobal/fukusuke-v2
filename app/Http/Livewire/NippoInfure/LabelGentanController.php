<?php

namespace App\Http\Livewire\NippoInfure;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LabelGentanController extends Component
{
    public $lpk_no;
    public $code;
    public $product_name;
    public $product_panjang;
    public $qty_gentan;
    public $berat_produksi;
    public $product_panjanggulung;
    public $berat_standard;
    public $lpk_date;
    public $qty_lpk;
    public $gentan_no;
    public $produk_asemblyid;
    public $statusPrint = false;

    public function print()
    {
        // Generate data untuk thermal printer
        $printData = $this->generateThermalPrintData();

        // Dispatch event untuk print via Bluetooth
        $this->dispatch('printThermalLabel', $printData);

        $this->statusPrint = false;
    }

    private function generateThermalPrintData()
    {
        return [
            'type' => 'label_gentan',
            'lpk_no' => $this->lpk_no,
            'gentan_no' => $this->gentan_no,
            'code' => $this->code,
            'product_name' => $this->product_name,
            'panjang_produksi' => number_format($this->product_panjang, 0, ',', '.'),
            'berat_produksi' => $this->berat_produksi,
            'berat_standard' => $this->berat_standard,
            'lpk_date' => $this->lpk_date,
            'qty_lpk' => number_format($this->qty_lpk, 0, ',', '.'),
            'timestamp' => Carbon::now()->format('d/m/Y H:i:s'),
        ];
    }

        public function printNormal()
    {
        $this->dispatch('redirectToPrint', $this->produk_asemblyid);
        $this->statusPrint = false;
    }

    public function resetLpkNo()
    {
        $this->lpk_no = '';
        $this->code = '';
        $this->product_name = '';
        $this->product_panjanggulung = '';
        $this->qty_lpk = '';
        $this->lpk_date = '';
    }

    public function resetGentanNo()
    {
        $this->produk_asemblyid = '';
        $this->product_panjang = '';
        $this->berat_produksi = '';
        $this->berat_standard = '';
        $this->gentan_no = '';
    }

    public function render()
    {
        if (isset($this->lpk_no) && $this->lpk_no != '' && strlen($this->lpk_no) == 10) {
            $data = DB::table('tdorderlpk as tod')
                ->join('msproduct as mp', 'mp.id', '=', 'tod.product_id')
                ->select(
                    'tod.lpk_no',
                    'mp.code',
                    'mp.name as product_name',
                    'tod.product_panjang',
                    'tod.qty_gentan',
                    'tod.product_panjanggulung',
                    'tod.qty_lpk',
                    'tod.lpk_date',
                    'tod.reprint_no as reprint_no'
                )
                ->where('lpk_no', $this->lpk_no)
                ->first();
            if ($data == null) {
                $this->dispatch('notification', ['type' => 'warning', 'message' => 'Nomor LPK ' . $this->lpk_no . ' Tidak Terdaftar']);
                $this->resetLpkNo();
                $this->resetGentanNo();
                $this->statusPrint = false;
            } else {
                $this->lpk_no = $data->lpk_no;
                $this->code = $data->code;
                $this->product_name = $data->product_name;
                // $this->product_panjang = $data->product_panjang;
                // $this->qty_gentan = $data->qty_gentan;
                $this->product_panjanggulung = $data->product_panjanggulung;
                $this->qty_lpk = $data->qty_lpk;
                $this->lpk_date = Carbon::parse($data->lpk_date)->format('d/M/Y');
            }
        }

        if (isset($this->gentan_no) && $this->gentan_no != '') {
            $data2 = DB::table('tdproduct_assembly as tpa')
                ->leftjoin('tdorderlpk as tod', 'tpa.lpk_id', '=', 'tod.id')
                ->leftjoin('msproduct as mp', 'mp.id', '=', 'tod.product_id')
                ->select(
                    'tod.lpk_no',
                    'mp.code',
                    'mp.name as product_name',
                    'tod.product_panjang',
                    'tod.qty_gentan',
                    'tod.product_panjanggulung',
                    'tod.qty_lpk',
                    'tod.lpk_date',
                    'tpa.panjang_produksi',
                    'tpa.berat_produksi',
                    'tpa.berat_standard',
                    'tpa.id as produk_asembly_id',
                    'tpa.gentan_no',
                    'tod.reprint_no as reprint_no',

                )
                ->where('tod.lpk_no', $this->lpk_no)
                ->where('tpa.gentan_no', $this->gentan_no)
                ->get();

            if ($data2->isEmpty()) {
                $this->dispatch('notification', ['type' => 'warning', 'message' => 'Nomor Gentan ' . $this->gentan_no . ' Tidak Terdaftar']);
                $this->resetGentanNo();
                $this->statusPrint = false;
            } else {
                $firstItem = $data2->first();
                $this->produk_asemblyid = $firstItem->produk_asembly_id;
                $this->product_panjang = $firstItem->panjang_produksi;
                $this->berat_produksi = $firstItem->berat_produksi;
                $this->berat_standard = $firstItem->berat_standard;
                $this->statusPrint = true;
            }
        }

        return view('livewire.nippo-infure.label-gentan')->extends('layouts.master');
    }
}
