<?php

namespace App\Http\Livewire\Warehouse\LabelMasukGudangReport;

use Carbon\Carbon;
use Livewire\Component;
use App\Models\MsWorkingShift;
use Exception;
use Illuminate\Support\Facades\Validator;

class LabelMasukGudangReportController extends Component
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
        $this->tglAwal = Carbon::now()->subday()->format('Y-m-d');
        $this->tglAkhir = Carbon::now()->format('Y-m-d');
        $this->workingShiftHour = MsWorkingShift::select('work_hour_from', 'work_hour_till')->active()->orderBy('work_hour_from', 'ASC')->get();
        $this->jamAwal = $this->workingShiftHour[0]->work_hour_from;
        $this->jamAkhir = $this->workingShiftHour[count($this->workingShiftHour) - 1]->work_hour_till;
    }

    public function export()
    {
        $rules = [
            'tglAwal' => 'required',
            'tglAkhir' => 'required',
            'jamAwal' => 'required',
            'jamAkhir' => 'required',
        ];

        $messages = [
            'tglAwal.required' => 'Tanggal Awal tidak boleh kosong',
            'tglAkhir.required' => 'Tanggal Akhir tidak boleh kosong',
            'jamAwal.required' => 'Jam Awal tidak boleh kosong',
            'jamAkhir.required' => 'Jam Akhir tidak boleh kosong',
        ];

        $validate = Validator::make([
            'tglAwal' => $this->tglAwal,
            'tglAkhir' => $this->tglAkhir,
            'jamAwal' => $this->jamAwal,
            'jamAkhir' => $this->jamAkhir,
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

        $report = new LabelMasukGudangReportExport();
        try {
            $result = $report->generateReport(
                $tglAwal,
                $tglAkhir,
            );

            return response()->download($result['filename'])->deleteFileAfterSend(true);
        } catch (Exception $e) {
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Terjadi kesalahan saat generate report: ' . $e->getMessage()]);
            return;
        }
    }

    public function render()
    {
        return view('livewire.warehouse.label-masuk-gudang-report')->extends('layouts.master');
    }
}
