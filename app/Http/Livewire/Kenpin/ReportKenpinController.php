<?php

namespace App\Http\Livewire\Kenpin;

use App\Exports\KenpinExport;
use App\Models\MsDepartment;
use App\Models\MsWorkingShift;
use Carbon\Carbon;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;

class ReportKenpinController extends Component
{
    public $tglAwal;
    public $tglAkhir;
    public $jamAwal;
    public $jamAkhir;
    public $workingShiftHour;
    public $department;
    public $departmentId;
    public $buyer;
    public $buyer_id;
    public $lpk_no;
    public $noorder;
    public $nomorKenpin;
    public $nomorHan;
    public $status;

    public function mount()
    {
        $this->tglAwal = Carbon::now()->format('Y-m-d');
        $this->tglAkhir = Carbon::now()->format('Y-m-d');
        $this->workingShiftHour = MsWorkingShift::select('work_hour_from', 'work_hour_till')->where('status', 1)->orderBy('work_hour_from', 'ASC')->get();
        $this->jamAwal = $this->workingShiftHour[0]->work_hour_from;
        $this->jamAkhir = $this->workingShiftHour[count($this->workingShiftHour) - 1]->work_hour_till;
        $this->department = MsDepartment::get();
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
