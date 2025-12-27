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

    // Tambahkan properties baru untuk data lengkap
    public $code_alias;
    public $production_date;
    public $work_hour;
    public $work_shift;
    public $machineno;
    public $selisih;
    public $nomor_han;
    public $nik;
    public $empname;

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

        // Reset data tambahan
        $this->code_alias = '';
        $this->production_date = '';
        $this->work_hour = '';
        $this->work_shift = '';
        $this->machineno = '';
        $this->selisih = '';
        $this->nomor_han = '';
        $this->nik = '';
        $this->empname = '';
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
                $this->product_panjanggulung = $data->product_panjanggulung;
                $this->qty_lpk = $data->qty_lpk;
                $this->lpk_date = Carbon::parse($data->lpk_date)->format('d/M/Y');
            }
        }

        if (isset($this->gentan_no) && $this->gentan_no != '') {
            // ===== QUERY LENGKAP - SAMA SEPERTI report-gentan.blade.php =====
            $data2 = DB::table('tdproduct_assembly as tpa')
                ->join('tdorderlpk as tod', 'tpa.lpk_id', '=', 'tod.id')
                ->join('msproduct as mp', 'mp.id', '=', 'tod.product_id')
                ->leftJoin('msworkingshift as msw', 'msw.id', '=', 'tpa.work_shift')
                ->join('msmachine as msm', 'msm.id', '=', 'tpa.machine_id')
                ->join('msemployee as mse', 'mse.id', '=', 'tpa.employee_id')
                ->select(
                    'tod.lpk_no',
                    'mp.code',
                    'mp.code_alias',
                    'mp.product_type_code',
                    'mp.name as product_name',
                    'tod.product_panjang',
                    'tod.total_assembly_line',
                    'tod.panjang_lpk',
                    'tpa.panjang_produksi',
                    'tpa.berat_produksi',
                    'tpa.berat_standard',
                    'tpa.id as produk_asembly_id',
                    'tpa.gentan_no',
                    'tpa.production_date',
                    'tpa.work_hour',
                    'tpa.work_shift',
                    'tpa.nomor_han',
                    'msm.machineno',
                    'mse.employeeno as nik',
                    'mse.empname',
                    DB::raw('(tod.total_assembly_line - tod.panjang_lpk) as selisih')
                )
                ->where('tod.lpk_no', $this->lpk_no)
                ->where('tpa.gentan_no', $this->gentan_no)
                ->first();

            if (!$data2) {
                $this->dispatch('notification', ['type' => 'warning', 'message' => 'Nomor Gentan ' . $this->gentan_no . ' Tidak Terdaftar']);
                $this->resetGentanNo();
                $this->statusPrint = false;
            } else {
                // ===== ASSIGN SEMUA DATA =====
                $this->produk_asemblyid = $data2->produk_asembly_id;
                $this->product_panjang = $data2->panjang_produksi;
                $this->berat_produksi = $data2->berat_produksi;
                $this->berat_standard = $data2->berat_standard;

                // ===== DATA TAMBAHAN UNTUK THERMAL PRINT =====
                $this->code_alias = $data2->code_alias;
                $this->production_date = Carbon::parse($data2->production_date)->format('d-m-Y');
                $this->work_hour = $data2->work_hour;
                $this->work_shift = $data2->work_shift;
                $this->machineno = $data2->machineno;
                $this->selisih = $data2->selisih;
                $this->nomor_han = $data2->nomor_han;
                $this->nik = $data2->nik;
                $this->empname = $data2->empname;

                $this->statusPrint = true;
            }
        }

        return view('livewire.nippo-infure.label-gentan')->extends('layouts.master');
    }
}
