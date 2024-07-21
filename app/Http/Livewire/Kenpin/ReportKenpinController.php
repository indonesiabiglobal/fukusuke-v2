<?php

namespace App\Http\Livewire\Kenpin;

use App\Exports\KenpinExport;
use Carbon\Carbon;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;

class ReportKenpinController extends Component
{
    public $tglMasuk;
    public $tglKeluar;
    public $buyer;
    public $buyer_id;
    public $filter;

    public function mount()
    {
        // $this->tglMasuk = Carbon::now()->format('Y-m-d');
        $this->tglMasuk = Carbon::now()->format('Y-m-d\T00:00');
        $this->tglKeluar = Carbon::now()->format('Y-m-d\T23:59');
        // $this->buyer = MsBuyer::get();      
    }

    public function export()
    {
        // return Excel::download(new KenpinExport(
        //     $this->tglMasuk, 
        //     $this->tglKeluar
        // ), 'Kenpin_report.xlsx');
    }

    public function render()
    {
        return view('livewire.kenpin.report-kenpin')->extends('layouts.master');
    }
}
