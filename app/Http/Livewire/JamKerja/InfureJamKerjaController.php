<?php

namespace App\Http\Livewire\jamKerja;

use App\Models\MsEmployee;
use App\Models\MsMachine;
use App\Models\MsWorkingShift;
use App\Models\TdJamKerjaMesin;
use Carbon\Carbon;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class InfureJamKerjaController extends Component
{
    public $tglMasuk;
    public $tglKeluar;
    // public $jamkerja = [];
    public $machinename;
    public $machineno;
    public $machine;
    public $msemployee;
    public $employeeno;
    public $transaksi;
    public $working_date;
    public $empname;
    public $work_shift;
    public $work_shift_filter;
    public $machine_id;
    public $employee_id;
    public $work_hour;
    public $off_hour;
    public $on_hour;
    public $orderid;
    public $workShift;

    public function mount()
    {
        $this->tglMasuk = Carbon::now()->format('d-m-Y');
        $this->tglKeluar = Carbon::now()->format('d-m-Y');
        $this->machine  = MsMachine::get();
        $this->workShift  = MsWorkingShift::get();
        $this->working_date = Carbon::now()->format('d-m-Y');
    }

    public function search()
    {
        $this->render();
    }

    public function edit($orderid)
    {
        $item = TdJamKerjaMesin::find($orderid);
        if ($item) {
            $machine = MsMachine::where('id', $item->machine_id)->first();
            $msemployee = MsEmployee::where('id', $item->employee_id)->first();

            $this->orderid = $item->id;
            $this->working_date = $item->working_date;
            $this->work_shift = $item->work_shift;
            $this->machineno = $machine->machineno;
            $this->machinename = $machine->machinename;
            $this->employeeno = $msemployee->employeeno;
            $this->empname = $msemployee->empname;
            $this->work_hour = Carbon::parse($item->work_hour)->format('H:i');
            $this->on_hour = Carbon::parse($item->on_hour)->format('H:i');
        } else {
            return redirect()->to('jam-kerja/infure');
        }
    }

    public function closeModal()
    {
        $this->resetInput();
    }

    public function resetInput()
    {
        $this->working_date = '';
        $this->work_shift = '';
        $this->machineno = '';
        $this->employeeno = '';
        $this->work_hour = '';
        $this->on_hour = '';
    }

    public function save()
    {
        $validatedData = $this->validate([
            'working_date' => 'required',
            'work_shift' => 'required',
            'machineno' => 'required',
            'employeeno' => 'required',
            'work_hour' => 'required',
            'off_hour' => 'required'
        ]);

        try {
            // menghitung waktu on hour
            $workHour = Carbon::parse($this->work_hour);
            $offHour = Carbon::parse($this->off_hour);
            // Menghitung perbedaan dalam menit
            // Menghitung perbedaan waktu
            $interval = $workHour->diff($offHour);
            $onHour = $interval->format('%H:%I');

            if (isset($this->orderid)) {
                $machine = MsMachine::where('machineno', $this->machineno)->first();
                $msemployee = MsEmployee::where('employeeno', $this->employeeno)->first();

                TdJamKerjaMesin::where('id', $this->orderid)->update([
                    'working_date' => $this->working_date,
                    'work_shift' => $this->work_shift,
                    'machine_id' => $machine->id,
                    'employee_id' => $msemployee->id,
                    'work_hour' => $this->work_hour,
                    'off_hour' => $this->off_hour,
                    'on_hour' => $onHour
                ]);
                $this->reset(['employeeno', 'empname', 'machineno', 'machinename', 'working_date', 'work_shift']);
                $this->dispatch('notification', ['type' => 'success', 'message' => 'Order saved successfully.']);
            } else {
                $machine = MsMachine::where('machineno', $this->machineno)->first();
                $msemployee = MsEmployee::where('employeeno', $this->employeeno)->first();

                $orderlpk = new TdJamKerjaMesin();
                $orderlpk->working_date = $this->working_date;
                $orderlpk->work_shift = $this->work_shift;
                $orderlpk->machine_id = $machine->id;
                $orderlpk->employee_id = $msemployee->id;
                $orderlpk->work_hour = $this->work_hour;
                $orderlpk->off_hour =  $this->off_hour;
                $orderlpk->on_hour = $onHour;

                $orderlpk->save();
            }

            $this->reset(['employeeno', 'empname', 'machineno', 'machinename', 'working_date', 'work_shift']);
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Order saved successfully.']);
            return redirect()->route('infure-jam-kerja');
        } catch (\Exception $e) {
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Failed to save the order: ' . $e->getMessage()]);
        }
    }

    public function render()
    {
        if (isset($this->machineno) && $this->machineno != '') {
            $machine = MsMachine::where('machineno', $this->machineno)->first();

            if ($machine == null) {
                $this->dispatch('notification', ['type' => 'warning', 'message' => 'Machine ' . $this->machineno . ' Tidak Terdaftar']);
            } else {
                $this->machinename = $machine->machinename;
            }
        }

        if (isset($this->employeeno) && $this->employeeno != '') {
            $msemployee = MsEmployee::where('employeeno', $this->employeeno)->first();

            if ($msemployee == null) {
                $this->dispatch('notification', ['type' => 'warning', 'message' => 'Employee ' . $this->employeeno . ' Tidak Terdaftar']);
            } else {
                $this->empname = $msemployee->empname;
            }
        }

        $data = DB::table('tdjamkerjamesin AS tdjkm')
            ->select(
                'tdjkm.id',
                'tdjkm.working_date',
                'tdjkm.work_shift',
                'tdjkm.machine_id',
                'tdjkm.department_id',
                'tdjkm.employee_id',
                'tdjkm.work_hour',
                'tdjkm.off_hour',
                'tdjkm.on_hour',
                'tdjkm.created_by',
                'tdjkm.created_on',
                'tdjkm.updated_by',
                'tdjkm.updated_on'
            );

        if (isset($this->tglMasuk) && $this->tglMasuk != "" && $this->tglMasuk != "undefined") {
            $data = $data->where('tdjkm.working_date', '>=', $this->tglMasuk);
        }

        if (isset($this->tglKeluar) && $this->tglKeluar != "" && $this->tglKeluar != "undefined") {
            $data = $data->where('tdjkm.working_date', '<=', $this->tglKeluar);
        }

        if (isset($this->machine_id) && $this->machine_id['value'] != "" && $this->machine_id != "undefined") {
            $data = $data->where('tdjkm.machine_id', $this->machine_id['value']);
        }
        if (isset($this->work_shift_filter) && $this->work_shift_filter['value'] != "" && $this->work_shift_filter != "undefined") {
            $data = $data->where('tdjkm.work_shift', $this->work_shift_filter);
        }

        $data = $data->paginate(8);
        // dd($this->work_shift);

        return view('livewire.jam-kerja.infure', [
            'data' => $data
        ])->extends('layouts.master');
    }
}
