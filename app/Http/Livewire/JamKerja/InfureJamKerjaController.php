<?php

namespace App\Http\Livewire\jamKerja;

use App\Helpers\departmentHelper;
use App\Helpers\formatTime;
use App\Http\Livewire\JamKerja\CheckListJamKerjaController;
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
use Illuminate\Support\Str;

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
    public $jamMatiFrom;
    public $jamMatiTo;
    public $off_hour;
    public $dataJamMatiMesin = [];
    #[Session]
    public $transaksi;
    public $working_date;
    public $empname;
    public $work_shift;
    #[Session]
    public $work_shift_filter;
    #[Session]
    public $machine_id;
    public $employee_id;
    public $work_hour = '08:00';
    public $totalOffHour = "00:00";
    public $totalOnHour = "08:00";
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
        $this->shouldForgetSession();

        if (empty($this->tglMasuk)) {
            $this->tglMasuk = Carbon::now()->format('d-m-Y');
        }
        if (empty($this->tglKeluar)) {
            $this->tglKeluar = Carbon::now()->format('d-m-Y');
        }
        if (empty($this->transaksi)) {
            $this->transaksi = 1;
        }
        $this->machine  = MsMachine::whereIn('department_id', departmentHelper::infurePabrikDepartment()->pluck('id'))->get();
        $this->workShift  = MsWorkingShift::active()->get();
        $this->working_date = Carbon::now()->format('d-m-Y');
        if (empty($this->sortingTable)) {
            $this->sortingTable = [[1, 'asc']];
        }
        $this->getData();
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

    protected function shouldForgetSession()
    {
        $previousUrl = url()->previous();
        $previousUrl = last(explode('/', $previousUrl));
        if (!(Str::contains($previousUrl, 'infure-jam-kerja'))) {
            $this->reset('tglMasuk', 'tglKeluar', 'transaksi', 'machine_id', 'work_shift_filter', 'searchTerm', 'sortingTable');
        }
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
        $this->jamMatiDataUpdated = true;
    }

    public function edit($id)
    {
        $this->jamMatiDataUpdated = true;
        $item = TdJamKerjaMesin::find($id);
        $machine = MsMachine::where('id', $item->machine_id)->first();
        $msemployee = MsEmployee::where('id', $item->employee_id)->first();
        $jamMatiMesin = TdJamKerjaJamMatiMesin::with('jamMatiMesin')
            ->where('jam_kerja_mesin_id', $id)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'jam_mati_mesin_id' => $item->jamMatiMesin->id,
                    'code' => trim($item->jamMatiMesin->code, 'I'),
                    'name' => $item->jamMatiMesin->name,
                    'off_hour' => Carbon::parse($item->off_hour)->format('H:i'),
                    'off_hour_minutes' => formatTime::timeToMinutes($item->off_hour),
                    'from' => isset($item->from) ? Carbon::parse($item->from)->format('H:i') : null,
                    'to' => isset($item->to) ? Carbon::parse($item->to)->format('H:i') : null,
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
        $this->totalOnHour = Carbon::parse($item->on_hour)->format('H:i');
        $this->totalOffHour = formatTime::minutesToTime($jamMatiMesin->sum('off_hour_minutes'));

        $this->dataJamMatiMesin = $jamMatiMesin->toArray();

        $this->dispatch('showModalUpdate');
    }

    public function closeModal()
    {
        $this->jamMatiDataUpdated = false;
    }

    public function resetInput()
    {
        $this->working_date = '';
        $this->work_shift = '';
        $this->machineno = '';
        $this->machinename = '';
        $this->employeeno = '';
        $this->empname = '';
        $this->work_hour = '08:00';
        $this->totalOffHour = '00:00';
        $this->totalOnHour = '08:00';
    }

    public function resetInputJamMatiMesin()
    {
        $this->jamMatiMesinId = '';
        $this->jamMatiMesinCode = '';
        $this->jamMatiMesinName = '';
        $this->off_hour = '00:00';
        $this->jamMatiFrom = '';
        $this->jamMatiTo = '';
    }

    protected function computeOffHourFromTimes(): void
    {
        if (empty($this->jamMatiFrom) || empty($this->jamMatiTo)) {
            $this->off_hour = '00:00';
            return;
        }

        try {
            $from = Carbon::createFromFormat('H:i', $this->jamMatiFrom);
            $to = Carbon::createFromFormat('H:i', $this->jamMatiTo);

            if ($to->lessThan($from)) {
                $to->addDay();
            }

            $minutes = $to->diffInMinutes($from);
            $this->off_hour = formatTime::minutesToTime($minutes);
        } catch (\Exception $e) {
            $this->off_hour = '00:00';
        }
    }

    public function updatedJamMatiFrom($value)
    {
        $this->jamMatiFrom = $value;
        $this->computeOffHourFromTimes();
        $this->skipRender();
    }

    public function updatedJamMatiTo($value)
    {
        $this->jamMatiTo = $value;
        $this->computeOffHourFromTimes();
        $this->skipRender();
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
            // compute off_hour from provided times first
            $this->computeOffHourFromTimes();

            $validatedData = $this->validate([
                'jamMatiMesinId' => 'required',
                'jamMatiMesinCode' => 'required',
                'jamMatiMesinName' => 'required',
                'jamMatiFrom' => 'required',
                'jamMatiTo' => 'required',
                'off_hour' => 'required',
            ], [
                'jamMatiMesinId.required' => 'Jam Mati Mesin harus diisi',
                'jamMatiMesinCode.required' => 'Jam Mati Mesin harus diisi',
                'jamMatiMesinName.required' => 'Nama Jam Mati Mesin harus diisi',
                'jamMatiFrom.required' => 'Field Dari harus diisi',
                'jamMatiTo.required' => 'Field Sampai harus diisi',
                'off_hour.required' => 'Lama Mesin Mati harus diisi',
            ]);

            $workHourMinutes = formatTime::timeToMinutes($this->work_hour);
            $offHourMinutes = formatTime::timeToMinutes($this->off_hour);
            $totalOffHourMinutes = formatTime::timeToMinutes($this->totalOffHour);

            // Validasi terhadap batas maksimum (8 jam = 480 menit)
            if ($totalOffHourMinutes + $offHourMinutes > 480) {
                return $this->dispatch('notification', ['type' => 'warning', 'message' => 'Total Lama Mesin Mati melebihi 8 jam']);
            }

            $newTotalOffMinutes = $totalOffHourMinutes + $offHourMinutes;
            $this->totalOffHour = formatTime::minutesToTime($newTotalOffMinutes);
            $this->totalOnHour = formatTime::minutesToTime($workHourMinutes - $newTotalOffMinutes);

            $data = [
                'id' => count($this->dataJamMatiMesin) + 1,
                'jam_mati_mesin_id' => $this->jamMatiMesinId,
                'code' => $this->jamMatiMesinCode,
                'name' => $this->jamMatiMesinName,
                'off_hour' => $this->off_hour,
                'from' => $this->jamMatiFrom,
                'to' => $this->jamMatiTo,
            ];
            $this->dataJamMatiMesin[] = $data;

            // jika dilakukan pada update
            if ($this->orderid) {
                $data =  [
                    'jam_kerja_mesin_id' => $this->orderid,
                    'jam_mati_mesin_id' => $this->jamMatiMesinId,
                    'off_hour' => $data['off_hour'],
                    'from' => $this->jamMatiFrom,
                    'to' => $this->jamMatiTo,
                ];
                TdJamKerjaJamMatiMesin::insert($data);
                TdJamKerjaMesin::where('id', $this->orderid)->update(['off_hour' => $this->totalOffHour, 'on_hour' => $this->totalOnHour]);

                $this->getData();
            }

            $this->dispatch('notification', ['type' => 'success', 'message' => 'Order saved successfully.']);
            $this->dispatch('closeModalJamMatiMesin');
            $this->resetInputJamMatiMesin();
        } catch (\Exception $e) {
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Failed to save the order: ' . $e->getMessage()]);
        }
    }

    public function deleteJamMatiMesin($orderId)
    {
        $index = array_search($orderId, array_column($this->dataJamMatiMesin, 'id'));

        if ($index !== false) {
            // mengurangi dari total lama mesin mati
            $this->totalOffHour = formatTime::minutesToTime(formatTime::timeToMinutes($this->totalOffHour) - formatTime::timeToMinutes($this->dataJamMatiMesin[$index]['off_hour']));
            $workHourMinutes = formatTime::timeToMinutes($this->work_hour);
            $this->totalOnHour = formatTime::minutesToTime($workHourMinutes - formatTime::timeToMinutes($this->totalOffHour));
            array_splice($this->dataJamMatiMesin, $index, 1);
        }

        if ($this->orderid) {
            $exist = TdJamKerjaJamMatiMesin::where('id', $orderId)->first();
            if ($exist) {
                TdJamKerjaJamMatiMesin::where('id', $orderId)->delete();

                // update total lama mesin mati
                TdJamKerjaMesin::where('id', $this->orderid)->update(['off_hour' => $this->totalOffHour, 'on_hour' => $this->totalOnHour]);
                $this->getData();
            }
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
                    'department_id' => departmentHelper::infureDivision()->id,
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

                // Upsert menggunakan kombinasi unique (working_date, machine_id, work_shift)
                $now = Carbon::now();
                $insertRow = [
                    'working_date'   => $this->working_date,
                    'work_shift'     => $this->work_shift,
                    'machine_id'     => $machine->id,
                    'employee_id'    => $msemployee->id,
                    'department_id'  => departmentHelper::infureDivision()->id,
                    'work_hour'      => $this->work_hour,
                    'off_hour'       => $this->totalOffHour,
                    'on_hour'        => $onHour,
                    'created_on'     => $now,
                    'created_by'     => auth()->user()->username,
                    'updated_on'     => $now,
                    'updated_by'     => auth()->user()->username,
                ];

                // Kolom unik sebagai constraint upsert
                $uniqueBy = ['working_date', 'machine_id', 'work_shift'];

                // Kolom yang akan diupdate jika record sudah ada
                $updateCols = ['employee_id', 'department_id', 'work_hour', 'off_hour', 'on_hour', 'updated_on', 'updated_by'];

                TdJamKerjaMesin::upsert([$insertRow], $uniqueBy, $updateCols);

                // Ambil record yang sekarang tersimpan/diupdate
                $jamKerjaMesin = TdJamKerjaMesin::where('working_date', $this->working_date)
                    ->where('machine_id', $machine->id)
                    ->where('work_shift', $this->work_shift)
                    ->first();

                // Simpan/replace jam mati mesin: hapus dulu yg lama lalu insert yg baru
                TdJamKerjaJamMatiMesin::where('jam_kerja_mesin_id', $jamKerjaMesin->id)->delete();

                if (!empty($this->dataJamMatiMesin)) {
                    $dataJamMatiMesin = array_map(function ($item) use ($jamKerjaMesin) {
                        return [
                            'jam_kerja_mesin_id' => $jamKerjaMesin->id,
                            'jam_mati_mesin_id'  => $item['jam_mati_mesin_id'],
                            'off_hour'           => $item['off_hour'],
                            'from'               => $item['from'] ?? null,
                            'to'                 => $item['to'] ?? null,
                        ];
                    }, $this->dataJamMatiMesin);

                    TdJamKerjaJamMatiMesin::insert($dataJamMatiMesin);
                }

                // Pastikan orderid diarahkan ke record yang tersimpan/diupdate
                $this->orderid = $jamKerjaMesin->id;
                $this->resetInputJamMatiMesin();
                $this->dataJamMatiMesin = [];

                $this->reset(['employeeno', 'empname', 'machineno', 'machinename', 'working_date', 'work_shift']);
                $this->resetInput();
                $this->dispatch('closeModalCreate');
            }
            $this->getData();
            $this->jamMatiDataUpdated = false;


            DB::commit();
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Work hour saved successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Failed to save the order: ' . $e->getMessage()]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->dispatch('notification', ['type' => 'warning', 'message' => $e->validator->errors()]);
        }
    }

    public function updatedJamMatiMesinCode($value)
    {
        // Normalize
        $code = trim((string) $value);
        $this->jamMatiMesinCode = $code;

        // Guard: kosong
        if ($code === '') {
            return $this->notify('warning', 'Jam Mati Mesin Tidak Boleh Kosong');
        }

        // Guard: minimal 3 char
        if (Str::length($code) < 3) {
            return $this->notify('warning', 'Jam Mati Mesin Minimal 3 Karakter');
        }

        // Pastikan prefix "I" hanya sekali
        $fullCode = Str::startsWith($code, 'I') ? $code : 'I' . $code;

        // Query hemat kolom
        $jam = MsJamMatiMesin::query()
            ->select('id', 'name')
            ->where('code', $fullCode)
            ->first();

        if (!$jam) {
            $this->jamMatiMesinId = '';
            $this->jamMatiMesinName = '';
            $this->jamMatiMesinCode = '';

            return $this->notify('warning', "Jam Mati Mesin {$code} Tidak Terdaftar");
        }

        // OK
        $this->jamMatiMesinId   = $jam->id;
        $this->jamMatiMesinName = $jam->name;
    }

    public function updatedMachineno($machineno)
    {
        $this->machineno = $machineno;

        if (isset($this->machineno) && $this->machineno != '') {
            $machine = MsMachine::where('machineno', 'ilike', '%' . $this->machineno)
                ->whereIn('department_id', departmentHelper::infureDepartment())
                ->orderBy('machineno', 'ASC')
                ->first();
            if ($machine == null) {
                $this->dispatch('notification', ['type' => 'warning', 'message' => 'Machine ' . $this->machineno . ' Tidak Terdaftar']);
                $this->machinename = '';
                $this->machineno = '';
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
                'tdjkm.created_on',
                'msm.machineno',
                'mse.empname',
                'mse.employeeno',
            )
            ->join('msmachine AS msm', 'msm.id', '=', 'tdjkm.machine_id')
            ->join('msemployee AS mse', 'mse.id', '=', 'tdjkm.employee_id')
            ->whereIn('tdjkm.department_id', departmentHelper::infureDivision());

        if ($this->transaksi == 1) {
            if (isset($this->tglMasuk) && $this->tglMasuk != "" && $this->tglMasuk != "undefined") {
                $data = $data->where('tdjkm.working_date', '>=', $this->tglMasuk);
            }

            if (isset($this->tglKeluar) && $this->tglKeluar != "" && $this->tglKeluar != "undefined") {
                $data = $data->where('tdjkm.working_date', '<=', $this->tglKeluar);
            }
        } else if ($this->transaksi == 2) {
            if (isset($this->tglMasuk) && $this->tglMasuk != "" && $this->tglMasuk != "undefined") {
                $data = $data->where('tdjkm.created_on', '>=', $this->tglMasuk . " 00:00:00");
            }

            if (isset($this->tglKeluar) && $this->tglKeluar != "" && $this->tglKeluar != "undefined") {
                $data = $data->where('tdjkm.created_on', '<=', $this->tglKeluar . " 23:59:59");
            }
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

    public function export()
    {
        // validasi
        if ($this->tglMasuk == $this->tglKeluar) {
            $this->dispatch('notification', ['type' => 'warning', 'message' => 'Tanggal Masuk dan Tanggal Keluar tidak boleh sama']);
            return;
        } else if (Carbon::parse($this->tglMasuk) > Carbon::parse($this->tglKeluar)) {
            $this->dispatch('notification', ['type' => 'warning', 'message' => 'Tanggal Masuk tidak boleh lebih besar dari Tanggal Keluar']);
            return;
        }

        $tglMasuk = Carbon::parse($this->tglMasuk . " 07:01:00");
        $tglKeluar = Carbon::parse($this->tglKeluar . " 07:00:00");
        $filter = [
            'machine_id' => $this->machine_id['value'] ?? null,
            'work_shift' => $this->work_shift_filter['value'] ?? null,
            'searchTerm' => $this->searchTerm ?? null,
            'transaksi' => $this->transaksi ?? 1,
        ];

        $checklist = new CheckListJamKerjaController();
        $response = $checklist->checklistJamKerja($tglMasuk, $tglKeluar, $filter, 'INFURE');
        if ($response['status'] == 'success') {
            return response()->download($response['filename']);
        } else if ($response['status'] == 'error') {
            $this->dispatch('notification', ['type' => 'warning', 'message' => $response['message']]);
            return;
        }
    }

    public function render()
    {
        return view('livewire.jam-kerja.infure', [
            'data' => $this->data
        ])->extends('layouts.master');
    }

    public function rendered()
    {
        if (!$this->isUpdatingSorting && !$this->jamMatiDataUpdated) {
            $this->dispatch('initDataTable');
        }
    }

    protected function notify(string $type, string $message): void
    {
        $this->dispatch('notification', compact('type', 'message'));
    }
}
