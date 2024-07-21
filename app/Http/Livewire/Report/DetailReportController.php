<?php

namespace App\Http\Livewire\Report;

use App\Exports\DetailReportExport;
use Carbon\Carbon;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;

class DetailReportController extends Component
{
    public $tglMasuk;
    public $tglKeluar;

    public function mount()
    {
        $this->tglMasuk = Carbon::now()->format('Y-m-d') . ' 00:00';
        $this->tglKeluar = Carbon::now()->format('Y-m-d') . ' 23:59';      
    }

    public function export()
    {
        // return Excel::download(new DetailReportExport(
        //     $this->tglMasuk, 
        //     $this->tglKeluar
        // ), 'Detail_Report.xlsx');
    }
    public function render()
    {
        return view('livewire.report.detail-report')->extends('layouts.master');
    }
}
