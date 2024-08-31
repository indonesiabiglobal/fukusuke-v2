<?php

namespace App\Http\Livewire\jamKerja;

use App\Models\MsEmployee;
use App\Models\MsMachine;
use App\Models\MsWorkingShift;
use App\Models\TdJamKerjaMesin;
use Carbon\Carbon;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;
use Livewire\Attributes\Session;

class SeitaiJamKerjaController extends Component
{
    protected $paginationTheme = 'bootstrap';
    protected $listeners = ['edit','delete'];
    #[Session]
    public $tglMasuk;
    #[Session]
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
    #[Session]
    public $machine_id;
    #[Session]
    public $work_shift_filter;
    public $employee_id;
    public $work_hour;
    public $off_hour;
    public $on_hour;
    public $orderid;
    public $workShift;
    #[Session]
    public $searchTerm;
    public $idDelete;

    use WithPagination, WithoutUrlPagination;

    public function mount()
    {
        if (empty($this->tglMasuk)) {
            $this->tglMasuk = Carbon::now()->format('d-m-Y');
        }
        if (empty($this->tglKeluar)) {
            $this->tglKeluar = Carbon::now()->format('d-m-Y');
        }
        $this->machine  = MsMachine::whereNotIn('department_id', [10, 12, 15, 2, 4, 10])->orderBy('machineno', 'ASC')->get();
        $this->workShift  = MsWorkingShift::where('status', 1)->get();
    }

    public function search()
    {
        $this->resetPage();
        $this->render();
    }

    public function showModalCreate()
    {
        // $this->resetInput();
        $this->working_date = Carbon::now()->format('d-m-Y');
        $this->dispatch('showModalCreate');
        // Mencegah render ulang
        $this->skipRender();
    }

    public function edit($id)
    {
        $item = TdJamKerjaMesin::find($id);
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
            $this->off_hour = Carbon::parse($item->off_hour)->format('H:i');

            $this->dispatch('showModalUpdate');
        } else {
            return redirect()->to('jam-kerja/seitai');
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
        $this->machinename = '';
        $this->employeeno = '';
        $this->empname = '';
        $this->work_hour = '';
        $this->off_hour = '';
    }

    public function validateWorkHour()
    {
        if ($this->work_hour > '08:00') {
            $this->work_hour = '08:00';
            $this->dispatch('notification', ['type' => 'warning', 'message' => 'Jam Kerja Tidak Boleh Lebih Dari 8 Jam']);
        }

        if (isset($this->off_hour) && $this->off_hour > '08:00') {
            $this->off_hour = '08:00';
            $this->dispatch('notification', ['type' => 'warning', 'message' => 'Jam Off Kerja Tidak Boleh Lebih Dari 8 jam']);
        }
    }

    public function save()
    {
        $validatedData = $this->validate([
            'working_date' => 'required',
            'work_shift' => 'required',
            'machineno' => 'required',
            'employeeno' => 'required',
            'work_hour' => 'required',
            'off_hour' => 'required',
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
                    'department_id' => $machine->department_id,
                    'employee_id' => $msemployee->id,
                    'work_hour' => $this->work_hour,
                    'off_hour' => $this->off_hour,
                    'on_hour' => $onHour,
                    'created_on' => Carbon::now(),
                    'created_by' => auth()->user()->username,
                    'updated_on' => Carbon::now(),
                    'updated_by' => auth()->user()->username,
                ]);
                $this->reset(['employeeno', 'empname', 'machineno', 'machinename', 'working_date', 'work_shift']);
                $this->resetInput();
                $this->dispatch('closeModalUpdate');
            } else {
                $machine = MsMachine::where('machineno', $this->machineno)->first();
                $msemployee = MsEmployee::where('employeeno', $this->employeeno)->first();

                $orderlpk = new TdJamKerjaMesin();
                $orderlpk->working_date = $this->working_date;
                $orderlpk->work_shift = $this->work_shift;
                $orderlpk->machine_id = $machine->id;
                $orderlpk->department_id = $machine->department_id;
                $orderlpk->employee_id = $msemployee->id;
                $orderlpk->work_hour = $this->work_hour;
                $orderlpk->off_hour =  $this->off_hour;
                $orderlpk->on_hour = $onHour;
                $orderlpk->created_on = Carbon::now();
                $orderlpk->created_by = auth()->user()->username;
                $orderlpk->updated_on = Carbon::now();
                $orderlpk->updated_by = auth()->user()->username;

                $orderlpk->save();
                $this->resetInput();
                $this->reset(['employeeno', 'empname', 'machineno', 'machinename', 'working_date', 'work_shift']);
                $this->dispatch('closeModalCreate');
            }
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Order saved successfully.']);

        } catch (\Exception $e) {
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Failed to save the order: ' . $e->getMessage()]);
        }
    }

    public function delete($id)
    {
        $this->idDelete = $id;
        $this->dispatch('showModalDelete');
        $this->skipRender();
    }

    public function destroy ()
    {
        try {
            TdJamKerjaMesin::where('id', $this->idDelete)->delete();
            $this->dispatch('closeModalDelete');
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Order deleted successfully.']);
        } catch (\Exception $e) {
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Failed to delete the order: ' . $e->getMessage()]);
        }
    }

    public function render()
    {
        if (isset($this->machineno) && $this->machineno != '') {
            $machine = MsMachine::where('machineno', 'ilike', '%' . $this->machineno)->whereNotIn('department_id', [10, 12, 15, 2, 4, 10])->first();

            if ($machine == null) {
                $this->machinename = '';
                $this->dispatch('notification', ['type' => 'warning', 'message' => 'Machine ' . $this->machineno . ' Tidak Terdaftar']);
            } else {
                $this->machineno = $machine->machineno;
                $this->machinename = $machine->machinename;
            }
        }

        if (isset($this->employeeno) && $this->employeeno != '' && strlen($this->employeeno) >= 3) {
            $msemployee = MsEmployee::where('employeeno', 'ilike', '%' . $this->employeeno)->active()->first();

            if ($msemployee == null) {
                $this->empname = '';
                $this->dispatch('notification', ['type' => 'error', 'message' => 'Employee ' . $this->employeeno . ' Tidak Terdaftar']);
            } else {
                $this->employeeno = $msemployee->employeeno;
                $this->empname = $msemployee->empname;
            }
        }

        $data = DB::table('tdjamkerjamesin AS tdjkm')
            ->select(
                'tdjkm.id as orderid',
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
                'tdjkm.updated_on',
                'msm.machineno',
                'msm.machinename',
                'mse.empname',
                'mse.employeeno'
            )
            ->join('msmachine AS msm', 'msm.id', '=', 'tdjkm.machine_id')
            ->join('msemployee AS mse', 'mse.id', '=', 'tdjkm.employee_id')
            ->join('msdepartment AS msd', 'msd.id', '=', 'tdjkm.department_id')
            ->where('msd.division_code','20');

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
        if (isset($this->searchTerm) && $this->searchTerm != '') {
            $data = $data->where(function ($query) {
                $query->where('msm.machineno', 'ilike', '%' . $this->searchTerm . '%')
                    ->orWhere('msm.machinename', 'ilike', '%' . $this->searchTerm . '%');
            });
        }

        $data = $data->get();

        return view(
            'livewire.jam-kerja.seitai',
            ['data' => $data]
        )->extends('layouts.master');
    }

    public function rendered()
    {
        $this->dispatch('initDataTable');
    }
}
