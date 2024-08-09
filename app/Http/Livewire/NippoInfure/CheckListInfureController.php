<?php

namespace App\Http\Livewire\NippoInfure;

use App\Exports\LossInfureExport;
use App\Exports\NippoInfureExport;
use App\Models\MsDepartment;
use App\Models\MsMachine;
use Livewire\Component;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class CheckListInfureController extends Component
{
    public $tglMasuk;
    public $tglKeluar;
    public $jamMasuk;
    public $jamKeluar;
    public $machine;
    public $noprosesawal;
    public $noprosesakhir;
    public $lpk_no;
    public $code;
    public $department;
    public $jenisReport = 1;
    public $departemenId;
    public $machineId;
    public $nomor_han;
    public $transaksi = 1;

    public function mount()
    {
        $this->tglMasuk = Carbon::now()->format('d/m/Y');
        $this->tglKeluar = Carbon::now()->format('d/m/Y');
        $this->jamMasuk = Carbon::today()->format('H:i');
        $this->jamKeluar = Carbon::today()->addDay()->subMinute()->format('H:i');
        $this->machine = MsMachine::get();
        $this->department = MsDepartment::get();
    }

    public function printReport()
    {
        $this->validate([
            'tglMasuk' => 'required',
            'tglKeluar' => 'required',
            'jenisReport' => 'required',
            'transaksi' => 'required',
        ]);

        return redirect()->route('nippo-infure-print', [
            'tglMasuk' => $this->tglMasuk,
            'tglKeluar' => $this->tglKeluar,
            'jenisReport' => $this->jenisReport,
            'noprosesawal' => $this->noprosesawal,
            'noprosesakhir' => $this->noprosesakhir,
            'lpk_no' => $this->lpk_no,
            'code' => $this->code,
            'departemenId' => $this->departemenId,
            'machineId' => $this->machineId,
            'nomor_han' => $this->nomor_han,
            'transaksi' => $this->transaksi,
        ]);
    }

    public function export()
    {
        if($this->jenisReport == 2){
            return Excel::download(new LossInfureExport(
                $this->tglMasuk,
                $this->tglKeluar,
                $this->noprosesawal,
                $this->noprosesakhir,
                $this->lpk_no,
                $this->code,
            ), 'LossInfure-Checklist.xlsx');
        } else {
            return Excel::download(new NippoInfureExport(
                $this->tglMasuk,
                $this->tglKeluar,
                $this->noprosesawal,
                $this->noprosesakhir,
                $this->lpk_no,
                $this->code,
                $this->departemenId,
                $this->machineId,
                $this->nomor_han,
            ), 'NippoInfure-Checklist.xlsx');
        }
    }

    public function render()
    {
        return view('livewire.nippo-infure.check-list')->extends('layouts.master');
    }
}
