<?php

namespace App\Http\Livewire\Report;

use App\Exports\DetailReportExport;
use App\Models\MsWorkingShift;
use Carbon\Carbon;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;

class DetailReportController extends Component
{
    public $tglAwal;
    public $tglAkhir;
    public $jamAwal;
    public $jamAkhir;
    public $workingShiftHour;

    public function mount()
    {
        $this->tglAwal = Carbon::now()->format('Y-m-d');
        $this->tglAkhir = Carbon::now()->format('Y-m-d');
        $this->workingShiftHour = MsWorkingShift::select('work_hour_from', 'work_hour_till')->where('status', 1)->orderBy('work_hour_from', 'ASC')->get();
        $this->jamAwal = $this->workingShiftHour[0]->work_hour_from;
        $this->jamAkhir = $this->workingShiftHour[count($this->workingShiftHour) - 1]->work_hour_till;
    }

    public function export()
    {
        // return Excel::download(new DetailReportExport(
        //     $this->tglAwal,
        //     $this->tglAkhir
        // ), 'Detail_Report.xlsx');
    }
    public function render()
    {
        return view('livewire.report.detail-report')->extends('layouts.master');
    }
}
