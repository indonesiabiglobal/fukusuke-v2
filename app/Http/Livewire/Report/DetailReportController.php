<?php

namespace App\Http\Livewire\Report;

use Carbon\Carbon;
use Livewire\Component;
use App\Models\MsMachine;
use App\Models\MsDepartment;
use App\Models\MsWorkingShift;
use App\Helpers\phpspreadsheet;
use Illuminate\Support\Facades\DB;
use App\Exports\DetailReportExport;
use App\Exports\InfureReportExport;
use App\Helpers\departmentHelper;
use App\Helpers\MachineHelper;
use App\Http\Livewire\Report\DetailReportInfureController;
use Exception;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PHPUnit\TextUI\Configuration\Merger;
use PHPUnit\TextUI\Configuration\Php;

class DetailReportController extends Component
{
    public $tglAwal;
    public $tglAkhir;
    public $jamAwal;
    public $jamAkhir;
    public $workingShiftHour;
    public $nippo = 'Infure';
    public $lpk_no;
    public $nomorOrder;
    public $department;
    public $departmentId;
    public $machine;
    public $machineId;
    public $nomorHan;
    public $nomorPalet;
    public $nomorLot;

    public function mount()
    {
        $this->tglAwal = Carbon::now()->format('Y-m-d');
        $this->tglAkhir = Carbon::now()->format('Y-m-d');
        $this->workingShiftHour = MsWorkingShift::select('work_hour_from', 'work_hour_till')->active()->orderBy('work_hour_from', 'ASC')->get();
        $this->jamAwal = $this->workingShiftHour[0]->work_hour_from;
        $this->jamAkhir = $this->workingShiftHour[count($this->workingShiftHour) - 1]->work_hour_till;
        $this->department = departmentHelper::infurePabrikDepartment();
        $this->machine = MachineHelper::getInfureMachine();
    }

    public function export()
    {
        $rules = [
            'tglAwal' => 'required',
            'tglAkhir' => 'required',
            'jamAwal' => 'required',
            'jamAkhir' => 'required',
            'nippo' => 'required',
        ];

        $messages = [
            'tglAwal.required' => 'Tanggal Awal tidak boleh kosong',
            'tglAkhir.required' => 'Tanggal Akhir tidak boleh kosong',
            'jamAwal.required' => 'Jam Awal tidak boleh kosong',
            'jamAkhir.required' => 'Jam Akhir tidak boleh kosong',
            'nippo.required' => 'Jenis Report tidak boleh kosong',
        ];

        $validate = Validator::make([
            'tglAwal' => $this->tglAwal,
            'tglAkhir' => $this->tglAkhir,
            'jamAwal' => $this->jamAwal,
            'jamAkhir' => $this->jamAkhir,
            'nippo' => $this->nippo,
        ], $rules, $messages);

        if ($validate->fails()) {
            $this->dispatch('notification', ['type' => 'warning', 'message' => $validate->errors()->first()]);
            return;
        }

        if ($this->tglAwal > $this->tglAkhir) {
            $this->dispatch('notification', ['type' => 'warning', 'message' => 'Tanggal akhir tidak boleh kurang dari tanggal awal']);
            return;
        }

        $tglAwal = Carbon::parse($this->tglAwal . ' ' . $this->jamAwal);
        $tglAkhir = Carbon::parse($this->tglAkhir . ' ' . $this->jamAkhir);

        if ($this->nippo == 'Infure') {
            $report = new DetailReportInfureController();
            try {
                $result = $report->generateReport(
                    $tglAwal,
                    $tglAkhir,
                    [
                        'lpk_no' => $this->lpk_no,
                        'machineId' => $this->machineId,
                        'nippo' => $this->nippo,
                        'nomorOrder' => $this->nomorOrder,
                        'departmentId' => $this->departmentId,
                        'nomorHan' => $this->nomorHan,
                    ]
                );

                return response()->download($result['filename'])->deleteFileAfterSend(true);
            } catch (Exception $e) {
                $this->dispatch('notification', ['type' => 'error', 'message' => 'Terjadi kesalahan saat generate report: ' . $e->getMessage()]);
                return;
            }
        } else if ($this->nippo == 'Seitai') {
            $report = new DetailReportSeitaiController();

            try {
            $result = $report->generateReport(
                $tglAwal,
                $tglAkhir,
                [
                    'lpk_no' => $this->lpk_no,
                    'machineId' => $this->machineId,
                    'nippo' => $this->nippo,
                    'nomorOrder' => $this->nomorOrder,
                    'departmentId' => $this->departmentId,
                    'nomorPalet' => $this->nomorPalet,
                    'nomorLot' => $this->nomorLot,
                ]
            );

            return response()->download($result['filename'])->deleteFileAfterSend(true);
            } catch (Exception $e) {
                $this->dispatch('notification', ['type' => 'error', 'message' => 'Terjadi kesalahan saat generate report: ' . $e->getMessage()]);
                return;
            }
        }
    }

    public function updatedNippo($value)
    {
        $this->machineId = null;
        $this->departmentId = null;

        if ($value == 'Infure') {
            $this->department = departmentHelper::infurePabrikDepartment();
            $this->machine = MachineHelper::getInfureMachine();
        } else if ($value == 'Seitai') {
            $this->department = departmentHelper::seitaiPabrikDepartment();
            $this->machine = MachineHelper::getSeitaiMachine();
        }
    }

    public function updatedDepartmentId($value)
    {
        $this->machineId = null;
        if ($value) {
            $this->machine = MachineHelper::getMachineByDepartment($value);
        }
    }

    public function render()
    {
        return view('livewire.report.detail-report')->extends('layouts.master');
    }
}
