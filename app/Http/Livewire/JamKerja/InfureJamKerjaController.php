<?php

namespace App\Http\Livewire\jamKerja;

use App\Helpers\departmentHelper;
use App\Helpers\formatTime;
use App\Models\MsEmployee;
use App\Models\MsJamMatiMesin;
use App\Models\MsMachine;
use App\Models\MsWorkingShift;
use App\Models\TdJamKerjaJamMatiMesin;
use App\Models\TdJamKerjaMesin;
use Carbon\Carbon;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;
use Livewire\Attributes\Session;
use PhpParser\Node\Expr\FuncCall;

class InfureJamKerjaController extends Component
{
    protected $paginationTheme = 'bootstrap';
    protected $listeners = ['edit', 'delete'];
    #[Session]
    public $tglMasuk;
    #[Session]
    public $tglKeluar;
    public $data = [];
    public $machinename;
    public $machineno;
    public $machine;
    public $msemployee;
    public $employeeno;
    public $jamMatiMesinId;
    public $jamMatiMesinCode;
    public $jamMatiMesinName;
    public $off_hour;
    public $dataJamMatiMesin = [];
    public $transaksi;
    public $working_date;
    public $empname;
    public $work_shift;
    #[Session]
    public $work_shift_filter;
    #[Session]
    public $machine_id;
    public $employee_id;
    public $work_hour;
    public $totalOffHour = "00:00";
    public $on_hour;
    public $orderid;
    public $workShift;
    #[Session]
    public $searchTerm;
    public $idDelete;
    #[Session]
    public $sortingTable;
    public $isUpdatingSorting = false;
    public $jamMatiDataUpdated = false;

    use WithPagination, WithoutUrlPagination;

    public function mount()
    {
        $this->getData();
        if (empty($this->tglMasuk)) {
            $this->tglMasuk = Carbon::now()->format('d-m-Y');
        }
        if (empty($this->tglKeluar)) {
            $this->tglKeluar = Carbon::now()->format('d-m-Y');
        }
        $this->machine  = MsMachine::whereIn('department_id', [10, 12, 15, 2, 4, 10])->get();
        $this->workShift  = MsWorkingShift::where('status', 1)->get();
        $this->working_date = Carbon::now()->format('d-m-Y');
        if (empty($this->sortingTable)) {
            $this->sortingTable = [[1, 'asc']];
        }
    }

    public function updateSortingTable($value)
    {
        $this->isUpdatingSorting = true;
        $this->sortingTable = $value;

        // Tidak skip render jika ada update data jam mati
        if (!$this->jamMatiDataUpdated) {
            $this->skipRender();
        }

        $this->isUpdatingSorting = false;
    }

    public function search()
    {
        $this->getData();
    }

    public function showModalCreate()
    {
        if ($this->orderid) {
            $this->dataJamMatiMesin = [];
            $this->orderid = null;
            $this->resetInput();
        }
        $this->working_date = Carbon::now()->format('d-m-Y');
        $this->dispatch('showModalCreate');
    }

    public function edit($id)
    {
        $item = TdJamKerjaMesin::find($id);
        $machine = MsMachine::where('id', $item->machine_id)->first();
        $msemployee = MsEmployee::where('id', $item->employee_id)->first();
        $jamMatiMesin = TdJamKerjaJamMatiMesin::with('jamMatiMesin')
            ->where('jam_kerja_mesin_id', $id)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'id' => $item->jamMatiMesin->id,
                    'code' => trim($item->jamMatiMesin->code, 'I'),
                    'name' => $item->jamMatiMesin->name,
                    'off_hour' => Carbon::parse($item->off_hour)->format('H:i'),
                    'off_hour_minutes' => formatTime::timeToMinutes($item->off_hour),
                ];
            });

        $this->orderid = $item->id;
        $this->working_date = $item->working_date;
        $this->work_shift = $item->work_shift;
        $this->machineno = $machine->machineno;
        $this->machinename = $machine->machinename;
        $this->employeeno = $msemployee->employeeno;
        $this->empname = $msemployee->empname;
        $this->work_hour = Carbon::parse($item->work_hour)->format('H:i');
        $this->totalOffHour = formatTime::minutesToTime($jamMatiMesin->sum('off_hour_minutes'));

        $this->dataJamMatiMesin = $jamMatiMesin->toArray();

        $this->dispatch('showModalUpdate');
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
        $this->totalOffHour = '00:00';
    }

    public function resetInputJamMatiMesin()
    {
        $this->jamMatiMesinId = '';
        $this->jamMatiMesinCode = '';
        $this->jamMatiMesinName = '';
        $this->off_hour = '';
    }

    public function showModalJamMatiMesin()
    {
        $this->resetInputJamMatiMesin();
        $this->dispatch('showModalJamMatiMesin');
        // Mencegah render ulang
        $this->skipRender();
    }

    public function addJamMatiMesin()
    {
        try {
            $validatedData = $this->validate([
                'jamMatiMesinId' => 'required',
                'jamMatiMesinCode' => 'required',
                'jamMatiMesinName' => 'required',
                'off_hour' => 'required',
            ], [
                'jamMatiMesinId.required' => 'Jam Mati Mesin harus diisi',
                'jamMatiMesinCode.required' => 'Jam Mati Mesin harus diisi',
                'jamMatiMesinName.required' => 'Nama Jam Mati Mesin harus diisi',
                'off_hour.required' => 'Lama Mesin Mati harus diisi',
            ]);

            // validasi apakah jam mati mesin sudah ada
            foreach ($this->dataJamMatiMesin as $item) {
                if ($item['id'] == $this->jamMatiMesinId) {
                    return $this->dispatch('notification', ['type' => 'warning', 'message' => 'Jam Mati Mesin sudah ada']);
                }
            }

            $offHourMinutes = formatTime::timeToMinutes($this->off_hour);
            $totalOffHourMinutes = formatTime::timeToMinutes($this->totalOffHour);

            // Validasi terhadap batas maksimum (8 jam = 480 menit)
            if ($totalOffHourMinutes + $offHourMinutes > 480) {
                return $this->dispatch('notification', ['type' => 'warning', 'message' => 'Total Lama Mesin Mati melebihi 8 jam']);
            }

            $newTotalMinutes = $totalOffHourMinutes + $offHourMinutes;
            $this->totalOffHour = formatTime::minutesToTime($newTotalMinutes);

            $data = [
                'id' => $this->jamMatiMesinId,
                'code' => $this->jamMatiMesinCode,
                'name' => $this->jamMatiMesinName,
                'off_hour' => $this->off_hour,
            ];
            $this->dataJamMatiMesin[] = $data;
            $this->jamMatiDataUpdated = true;

            // jika dilakukan pada update
            if ($this->orderid) {
                $dataJamMatiMesin =  [
                    'jam_kerja_mesin_id' => $this->orderid,
                    'jam_mati_mesin_id' => $data['id'],
                    'off_hour' => $data['off_hour'],
                ];
                TdJamKerjaJamMatiMesin::upsert($dataJamMatiMesin, ['jam_kerja_mesin_id', 'jam_mati_mesin_id'], ['off_hour']);
                TdJamKerjaMesin::where('id', $this->orderid)->update(['off_hour' => $this->totalOffHour]);

                $this->getData();
            }

            $this->dispatch('notification', ['type' => 'success', 'message' => 'Order saved successfully.']);
            $this->dispatch('closeModalJamMatiMesin');
            $this->resetInputJamMatiMesin();
        } catch (\Exception $e) {
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Failed to save the order: ' . $e->getMessage()]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->dispatch('notification', ['type' => 'warning', 'message' => $e->validator->errors()]);
        }
    }

    public function deleteJamMatiMesin($orderId)
    {
        $index = array_search($orderId, array_column($this->dataJamMatiMesin, 'id'));

        if ($index !== false) {
            // mengurangi dari total lama mesin mati
            $this->totalOffHour = formatTime::minutesToTime(formatTime::timeToMinutes($this->totalOffHour) - formatTime::timeToMinutes($this->dataJamMatiMesin[$index]['off_hour']));
            array_splice($this->dataJamMatiMesin, $index, 1);
        }

        $exist = TdJamKerjaJamMatiMesin::where('jam_mati_mesin_id', $orderId)->where('jam_kerja_mesin_id', $this->orderid)->first();
        if ($exist) {
            TdJamKerjaJamMatiMesin::where('jam_mati_mesin_id', $orderId)->where('jam_kerja_mesin_id', $this->orderid)->delete();

            // update total lama mesin mati
            TdJamKerjaMesin::where('id', $this->orderid)->update(['off_hour' => $this->totalOffHour]);
            $this->getData();
        }

        $this->dispatch('notification', ['type' => 'success', 'message' => 'Data Berhasil di Hapus']);
    }

    public function validateWorkHour()
    {
        if (isset($this->work_hour) && $this->work_hour > '08:00') {
            $this->work_hour = '08:00';
            $this->dispatch('notification', ['type' => 'warning', 'message' => 'Jam Kerja Tidak Boleh Lebih Dari 8 Jam']);
        }
    }

    public function delete($id)
    {
        $this->idDelete = $id;
        $this->dispatch('showModalDelete');
        $this->skipRender();
    }

    public function destroy()
    {
        try {
            TdJamKerjaMesin::where('id', $this->idDelete)->delete();
            $this->getData();
            $this->dispatch('closeModalDelete');
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Order deleted successfully.']);
        } catch (\Exception $e) {
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Failed to delete the order: ' . $e->getMessage()]);
        }
    }

    public function save()
    {
        try {
            $validatedData = $this->validate([
                'working_date' => 'required',
                'work_shift' => 'required',
                'machineno' => 'required',
                'employeeno' => 'required',
                'work_hour' => 'required',
            ], [
                'working_date.required' => 'Tanggal Produksi harus diisi',
                'work_shift.required' => 'Shift Kerja harus diisi',
                'machineno.required' => 'Nomor Mesin harus diisi',
                'employeeno.required' => 'Nomor Karyawan harus diisi',
                'work_hour.required' => 'Jam Kerja harus diisi',
            ]);

            // menghitung waktu on hour
            $workHour = Carbon::parse($this->work_hour);
            $offHour = Carbon::parse($this->totalOffHour);
            $interval = $workHour->diff($offHour);
            $onHour = $interval->format('%H:%I');

            DB::beginTransaction();
            if (isset($this->orderid)) {
                $machine = MsMachine::where('machineno', $this->machineno)->first();
                $msemployee = MsEmployee::where('employeeno', $this->employeeno)->first();

                TdJamKerjaMesin::where('id', $this->orderid)->update([
                    'working_date' => $this->working_date,
                    'work_shift' => $this->work_shift,
                    'machine_id' => $machine->id,
                    'department_id' => departmentHelper::infureDivisiom()->id,
                    'employee_id' => $msemployee->id,
                    'work_hour' => $this->work_hour,
                    'off_hour' => $this->totalOffHour,
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

                $jamKerjaMesin = new TdJamKerjaMesin();
                $jamKerjaMesin->working_date = $this->working_date;
                $jamKerjaMesin->work_shift = $this->work_shift;
                $jamKerjaMesin->machine_id = $machine->id;
                $jamKerjaMesin->employee_id = $msemployee->id;
                $jamKerjaMesin->work_hour = $this->work_hour;
                $jamKerjaMesin->off_hour =  $this->totalOffHour;
                $jamKerjaMesin->on_hour = $onHour;
                $jamKerjaMesin->created_on = Carbon::now();
                $jamKerjaMesin->created_by = auth()->user()->username;
                $jamKerjaMesin->updated_on = Carbon::now();
                $jamKerjaMesin->updated_by = auth()->user()->username;

                $jamKerjaMesin->save();

                $dataJamMatiMesin = array_map(function ($item) use ($jamKerjaMesin) {
                    return [
                        'jam_kerja_mesin_id' => $jamKerjaMesin->id,
                        'jam_mati_mesin_id' => $item['id'],
                        'off_hour' => $item['off_hour'],
                    ];
                }, $this->dataJamMatiMesin);
                TdJamKerjaJamMatiMesin::upsert($dataJamMatiMesin, ['jam_kerja_mesin_id', 'jam_mati_mesin_id'], ['off_hour']);
                $this->resetInputJamMatiMesin();
                $this->dataJamMatiMesin = [];

                $this->reset(['employeeno', 'empname', 'machineno', 'machinename', 'working_date', 'work_shift']);
                $this->resetInput();
                $this->dispatch('closeModalCreate');
            }
            $this->getData();


            DB::commit();
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Order saved successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Failed to save the order: ' . $e->getMessage()]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->dispatch('notification', ['type' => 'warning', 'message' => $e->validator->errors()]);
        }
    }

    public function updatedJamMatiMesinCode($jamMatiMesinCode)
    {
        $this->jamMatiMesinCode = $jamMatiMesinCode;

        if (isset($this->jamMatiMesinCode) && $this->jamMatiMesinCode != '' && strlen($this->jamMatiMesinCode) >= 3) {
            $jamMatiMesin = MsJamMatiMesin::where('code', "I" . $this->jamMatiMesinCode)->first();
            if ($jamMatiMesin == null) {
                $this->jamMatiMesinId = '';
                $this->jamMatiMesinName = '';
                $this->jamMatiMesinCode = '';
                $this->dispatch('notification', ['type' => 'warning', 'message' => 'Jam Mati Mesin ' . $jamMatiMesinCode . ' Tidak Terdaftar']);
            } else {
                $this->jamMatiMesinId = $jamMatiMesin->id;
                $this->jamMatiMesinName = $jamMatiMesin->name;
            }
        }
    }

    public function updatedMachineno($machineno)
    {
        $this->machineno = $machineno;

        if (isset($this->machineno) && $this->machineno != '') {
            $machine = MsMachine::where('machineno', 'ilike', '%' . $this->machineno)->whereIn('department_id', [10, 12, 15, 2, 4])->first();
            if ($machine == null) {
                $this->machinename = '';
                $this->dispatch('notification', ['type' => 'warning', 'message' => 'Machine ' . $this->machineno . ' Tidak Terdaftar']);
            } else {
                $this->machineno = $machine->machineno;
                $this->machinename = $machine->machinename;
            }
        }
    }

    public function updatedEmployeeno($employeeno)
    {
        $this->employeeno = $employeeno;

        if (isset($this->employeeno) && $this->employeeno != '' && strlen($this->employeeno) >= 3) {
            $msemployee = MsEmployee::where('employeeno', 'ilike', '%' . $this->employeeno)->active()->first();

            if ($msemployee == null) {
                $this->empname = '';
                $this->dispatch('notification', ['type' => 'warning', 'message' => 'Employee ' . $this->employeeno . ' Tidak Terdaftar']);
            } else {
                $this->employeeno = $msemployee->employeeno;
                $this->empname = $msemployee->empname;
            }
        }
    }

    public function getData()
    {
        $data = DB::table('tdjamkerjamesin AS tdjkm')
            ->select(
                'tdjkm.id',
                'tdjkm.working_date',
                'tdjkm.work_shift',
                'tdjkm.work_hour',
                'tdjkm.off_hour',
                'tdjkm.on_hour',
                'tdjkm.updated_by',
                'tdjkm.updated_on',
                'msm.machineno',
                'mse.empname',
                'mse.employeeno',
            )
            ->join('msmachine AS msm', 'msm.id', '=', 'tdjkm.machine_id')
            ->join('msemployee AS mse', 'mse.id', '=', 'tdjkm.employee_id')
            ->join('msdepartment AS msd', 'msd.id', '=', 'tdjkm.department_id')
            ->whereIn('msd.division_code', [2, 10]);

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
        $this->data = $data->orderBy('tdjkm.updated_on', 'DESC')->get();
    }

    public function render()
    {
        return view('livewire.jam-kerja.infure', [
            'data' => $this->data
        ])->extends('layouts.master');
    }

    public function rendered()
    {
        if (!$this->isUpdatingSorting) {
            $this->dispatch('initDataTable');
        }

        // Reset flag setelah render
        $this->jamMatiDataUpdated = false;
    }
}
