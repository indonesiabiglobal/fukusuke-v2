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
use App\Traits\HandlesHeavyJob;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;
use Livewire\Attributes\Session;
use Illuminate\Support\Str;

class InfureJamKerjaController extends Component
{
    use HandlesHeavyJob, WithPagination, WithoutUrlPagination;

    protected $paginationTheme = 'bootstrap';
    protected $listeners = ['edit', 'delete'];

    public bool $isLoaded = false;

    #[Session] public $tglMasuk;
    #[Session] public $tglKeluar;
    #[Session] public $transaksi;
    #[Session] public $work_shift_filter;
    #[Session] public $machine_id;
    #[Session] public $searchTerm;
    #[Session] public $perPage      = 10;
    #[Session] public $sortColumn   = 'tdjkm.updated_on';
    #[Session] public $sortDirection = 'desc';

    public $machinename;
    public $machineno;
    public $msemployee;
    public $employeeno;
    public $jamMatiMesinId;
    public $jamMatiMesinCode;
    public $jamMatiMesinName;
    public $jamMatiFrom;
    public $jamMatiTo;
    public $off_hour;
    public $dataJamMatiMesin = [];
    public $working_date;
    public $empname;
    public $work_shift;
    public $employee_id;
    public $work_hour = '08:00';
    public $totalOffHour = '00:00';
    public $totalOnHour  = '08:00';
    public $on_hour;
    public $orderid;
    public $idDelete;
    public $jamMatiDataUpdated = false;

    public function sortBy(string $column): void
    {
        $allowed = [
            'tdjkm.working_date', 'tdjkm.work_shift', 'msm.machineno',
            'mse.employeeno', 'mse.empname', 'tdjkm.work_hour',
            'tdjkm.off_hour', 'tdjkm.on_hour', 'tdjkm.updated_on', 'tdjkm.created_on',
        ];
        if (!in_array($column, $allowed)) return;
        if ($this->sortColumn === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortColumn    = $column;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function loadData(): void
    {
        $this->isLoaded = true;
    }

    public function mount(): void
    {
        $this->shouldForgetSession();

        if (is_array($this->machine_id))        { $this->machine_id        = $this->machine_id['value']        ?? null; }
        if (is_array($this->work_shift_filter)) { $this->work_shift_filter = $this->work_shift_filter['value'] ?? null; }

        if (empty($this->tglMasuk) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $this->tglMasuk)) {
            $this->tglMasuk = Carbon::now()->format('Y-m-d');
        }
        if (empty($this->tglKeluar) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $this->tglKeluar)) {
            $this->tglKeluar = Carbon::now()->format('Y-m-d');
        }
        if (empty($this->transaksi)) {
            $this->transaksi = 1;
        }

        $this->working_date = Carbon::now()->format('Y-m-d');
    }

    protected function shouldForgetSession(): void
    {
        $previousUrl = last(explode('/', url()->previous()));
        if (!Str::contains($previousUrl, 'infure-jam-kerja')) {
            $this->reset('tglMasuk', 'tglKeluar', 'transaksi', 'machine_id', 'work_shift_filter', 'searchTerm', 'perPage', 'sortColumn', 'sortDirection');
        }
    }

    public function search(): void
    {
        $this->resetPage();
    }

    public function showModalCreate(): void
    {
        if ($this->orderid) {
            $this->dataJamMatiMesin = [];
            $this->orderid = null;
            $this->resetInput();
        }
        $this->working_date = Carbon::now()->format('Y-m-d');
        $this->dispatch('showModalCreate');
        $this->jamMatiDataUpdated = true;
    }

    public function edit($id): void
    {
        $this->jamMatiDataUpdated = true;
        $item      = TdJamKerjaMesin::find($id);
        $machine   = MsMachine::where('id', $item->machine_id)->first();
        $msemployee = MsEmployee::where('id', $item->employee_id)->first();
        $jamMatiMesin = TdJamKerjaJamMatiMesin::with('jamMatiMesin')
            ->where('jam_kerja_mesin_id', $id)
            ->get()
            ->map(function ($item) {
                return [
                    'id'                => $item->id,
                    'jam_mati_mesin_id' => $item->jamMatiMesin->id,
                    'code'              => trim($item->jamMatiMesin->code, 'I'),
                    'name'              => $item->jamMatiMesin->name,
                    'off_hour'          => Carbon::parse($item->off_hour)->format('H:i'),
                    'off_hour_minutes'  => formatTime::timeToMinutes($item->off_hour),
                    'from'              => isset($item->from) ? Carbon::parse($item->from)->format('H:i') : null,
                    'to'                => isset($item->to)   ? Carbon::parse($item->to)->format('H:i')   : null,
                ];
            });

        $this->orderid      = $item->id;
        $this->working_date = $item->working_date;
        $this->work_shift   = $item->work_shift;
        $this->machineno    = $machine->machineno;
        $this->machinename  = $machine->machinename;
        $this->employeeno   = $msemployee->employeeno ?? '';
        $this->empname      = $msemployee->empname ?? '';
        $this->work_hour    = Carbon::parse($item->work_hour)->format('H:i');
        $this->totalOnHour  = Carbon::parse($item->on_hour)->format('H:i');
        $this->totalOffHour = formatTime::minutesToTime($jamMatiMesin->sum('off_hour_minutes'));
        $this->dataJamMatiMesin = $jamMatiMesin->toArray();

        $this->dispatch('showModalUpdate');
    }

    public function closeModal(): void
    {
        $this->jamMatiDataUpdated = false;
    }

    public function resetInput(): void
    {
        $this->working_date = '';
        $this->work_shift   = '';
        $this->machineno    = '';
        $this->machinename  = '';
        $this->employeeno   = '';
        $this->empname      = '';
        $this->work_hour    = '08:00';
        $this->totalOffHour = '00:00';
        $this->totalOnHour  = '08:00';
    }

    public function resetInputJamMatiMesin(): void
    {
        $this->jamMatiMesinId   = '';
        $this->jamMatiMesinCode = '';
        $this->jamMatiMesinName = '';
        $this->off_hour         = '00:00';
        $this->jamMatiFrom      = '';
        $this->jamMatiTo        = '';
    }

    protected function computeOffHourFromTimes(): void
    {
        if (empty($this->jamMatiFrom) || empty($this->jamMatiTo)) {
            $this->off_hour = '00:00';
            return;
        }
        try {
            $from = Carbon::createFromFormat('H:i', $this->jamMatiFrom);
            $to   = Carbon::createFromFormat('H:i', $this->jamMatiTo);
            if ($to->lessThan($from)) { $to->addDay(); }
            $this->off_hour = formatTime::minutesToTime($to->diffInMinutes($from));
        } catch (\Exception $e) {
            $this->off_hour = '00:00';
        }
    }

    public function updatedJamMatiFrom($value): void
    {
        $this->jamMatiFrom = $value;
        $this->computeOffHourFromTimes();
        $this->skipRender();
    }

    public function updatedJamMatiTo($value): void
    {
        $this->jamMatiTo = $value;
        $this->computeOffHourFromTimes();
        $this->skipRender();
    }

    public function showModalJamMatiMesin(): void
    {
        $this->resetInputJamMatiMesin();
        $this->dispatch('showModalJamMatiMesin');
        $this->skipRender();
    }

    public function addJamMatiMesin(): void
    {
        try {
            $this->computeOffHourFromTimes();

            $this->validate([
                'jamMatiMesinId'   => 'required',
                'jamMatiMesinCode' => 'required',
                'jamMatiMesinName' => 'required',
                'jamMatiFrom'      => 'required',
                'jamMatiTo'        => 'required',
                'off_hour'         => 'required',
            ], [
                'jamMatiMesinId.required'   => 'Jam Mati Mesin harus diisi',
                'jamMatiMesinCode.required' => 'Jam Mati Mesin harus diisi',
                'jamMatiMesinName.required' => 'Nama Jam Mati Mesin harus diisi',
                'jamMatiFrom.required'      => 'Field Dari harus diisi',
                'jamMatiTo.required'        => 'Field Sampai harus diisi',
                'off_hour.required'         => 'Lama Mesin Mati harus diisi',
            ]);

            $workHourMinutes     = formatTime::timeToMinutes($this->work_hour);
            $offHourMinutes      = formatTime::timeToMinutes($this->off_hour);
            $totalOffHourMinutes = formatTime::timeToMinutes($this->totalOffHour);

            if ($totalOffHourMinutes + $offHourMinutes > 480) {
                $this->dispatch('notification', ['type' => 'warning', 'message' => 'Total Lama Mesin Mati melebihi 8 jam']);
                return;
            }

            $newTotalOffMinutes = $totalOffHourMinutes + $offHourMinutes;
            $this->totalOffHour = formatTime::minutesToTime($newTotalOffMinutes);
            $this->totalOnHour  = formatTime::minutesToTime($workHourMinutes - $newTotalOffMinutes);

            $entry = [
                'id'                => count($this->dataJamMatiMesin) + 1,
                'jam_mati_mesin_id' => $this->jamMatiMesinId,
                'code'              => $this->jamMatiMesinCode,
                'name'              => $this->jamMatiMesinName,
                'off_hour'          => $this->off_hour,
                'from'              => $this->jamMatiFrom,
                'to'                => $this->jamMatiTo,
            ];
            $this->dataJamMatiMesin[] = $entry;

            if ($this->orderid) {
                TdJamKerjaJamMatiMesin::insert([
                    'jam_kerja_mesin_id' => $this->orderid,
                    'jam_mati_mesin_id'  => $this->jamMatiMesinId,
                    'off_hour'           => $entry['off_hour'],
                    'from'               => $this->jamMatiFrom,
                    'to'                 => $this->jamMatiTo,
                ]);
                TdJamKerjaMesin::where('id', $this->orderid)->update([
                    'off_hour' => $this->totalOffHour,
                    'on_hour'  => $this->totalOnHour,
                ]);
            }

            $this->dispatch('notification', ['type' => 'success', 'message' => 'Order saved successfully.']);
            $this->dispatch('closeModalJamMatiMesin');
            $this->resetInputJamMatiMesin();
        } catch (\Exception $e) {
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Failed to save the order: ' . $e->getMessage()]);
        }
    }

    public function deleteJamMatiMesin($orderId): void
    {
        $index = array_search($orderId, array_column($this->dataJamMatiMesin, 'id'));
        if ($index !== false) {
            $this->totalOffHour = formatTime::minutesToTime(
                formatTime::timeToMinutes($this->totalOffHour) - formatTime::timeToMinutes($this->dataJamMatiMesin[$index]['off_hour'])
            );
            $this->totalOnHour = formatTime::minutesToTime(
                formatTime::timeToMinutes($this->work_hour) - formatTime::timeToMinutes($this->totalOffHour)
            );
            array_splice($this->dataJamMatiMesin, $index, 1);
        }

        if ($this->orderid) {
            $exist = TdJamKerjaJamMatiMesin::where('id', $orderId)->first();
            if ($exist) {
                TdJamKerjaJamMatiMesin::where('id', $orderId)->delete();
                TdJamKerjaMesin::where('id', $this->orderid)->update([
                    'off_hour' => $this->totalOffHour,
                    'on_hour'  => $this->totalOnHour,
                ]);
            }
        }

        $this->dispatch('notification', ['type' => 'success', 'message' => 'Data Berhasil di Hapus']);
    }

    public function validateWorkHour(): void
    {
        if (isset($this->work_hour) && $this->work_hour > '08:00') {
            $this->work_hour = '08:00';
            $this->dispatch('notification', ['type' => 'warning', 'message' => 'Jam Kerja Tidak Boleh Lebih Dari 8 Jam']);
        }
    }

    public function delete($id): void
    {
        $this->idDelete = $id;
        $this->dispatch('showModalDelete');
        $this->skipRender();
    }

    public function destroy(): void
    {
        try {
            TdJamKerjaMesin::where('id', $this->idDelete)->delete();
            $this->dispatch('closeModalDelete');
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Order deleted successfully.']);
        } catch (\Exception $e) {
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Failed to delete the order: ' . $e->getMessage()]);
        }
    }

    public function save(): void
    {
        try {
            $this->validate([
                'working_date' => 'required',
                'work_shift'   => 'required',
                'machineno'    => 'required',
                'employeeno'   => 'required',
                'work_hour'    => 'required',
            ], [
                'working_date.required' => 'Tanggal Produksi harus diisi',
                'work_shift.required'   => 'Shift Kerja harus diisi',
                'machineno.required'    => 'Nomor Mesin harus diisi',
                'employeeno.required'   => 'Petugas harus diisi',
                'work_hour.required'    => 'Jam Kerja harus diisi',
            ]);

            $workHour = Carbon::parse($this->work_hour);
            $offHour  = Carbon::parse($this->totalOffHour);
            $onHour   = $workHour->diff($offHour)->format('%H:%I');

            DB::beginTransaction();

            if (isset($this->orderid)) {
                $machine    = MsMachine::where('machineno', $this->machineno)->first();
                $msemployee = MsEmployee::where('employeeno', $this->employeeno)->first();

                TdJamKerjaMesin::where('id', $this->orderid)->update([
                    'working_date' => $this->working_date,
                    'work_shift'   => $this->work_shift,
                    'machine_id'   => $machine->id,
                    'department_id' => departmentHelper::infureDivision()->id,
                    'employee_id'  => $msemployee->id,
                    'work_hour'    => $this->work_hour,
                    'off_hour'     => $this->totalOffHour,
                    'on_hour'      => $onHour,
                    'created_on'   => Carbon::now(),
                    'created_by'   => auth()->user()->username,
                    'updated_on'   => Carbon::now(),
                    'updated_by'   => auth()->user()->username,
                ]);
                $this->reset(['employeeno', 'empname', 'machineno', 'machinename', 'working_date', 'work_shift']);
                $this->resetInput();
                $this->dispatch('closeModalUpdate');
            } else {
                $machine    = MsMachine::where('machineno', $this->machineno)->first();
                $msemployee = MsEmployee::where('employeeno', $this->employeeno)->first();

                $existing = TdJamKerjaMesin::with('jamKerjaJamMatiMesin')
                    ->where('working_date', $this->working_date)
                    ->where('machine_id', $machine->id)
                    ->where('work_shift', $this->work_shift)
                    ->whereDoesntHave('jamKerjaJamMatiMesin', function ($query) {
                        $query->whereHas('jamMatiMesin', function ($q) { $q->where('id', 10); });
                    })
                    ->first();
                if ($existing) {
                    $this->dispatch('notification', ['type' => 'warning', 'message' => 'Data sudah ada. Silakan edit data tersebut jika ingin mengubahnya.']);
                    return;
                }

                $now = Carbon::now();
                TdJamKerjaMesin::upsert([[
                    'working_date'  => $this->working_date,
                    'work_shift'    => $this->work_shift,
                    'machine_id'    => $machine->id,
                    'employee_id'   => $msemployee->id,
                    'department_id' => departmentHelper::infureDivision()->id,
                    'work_hour'     => $this->work_hour,
                    'off_hour'      => $this->totalOffHour,
                    'on_hour'       => $onHour,
                    'created_on'    => $now,
                    'created_by'    => auth()->user()->username,
                    'updated_on'    => $now,
                    'updated_by'    => auth()->user()->username,
                ]], ['working_date', 'machine_id', 'work_shift'], [
                    'employee_id', 'department_id', 'work_hour', 'off_hour', 'on_hour', 'updated_on', 'updated_by',
                ]);

                $jamKerjaMesin = TdJamKerjaMesin::where('working_date', $this->working_date)
                    ->where('machine_id', $machine->id)
                    ->where('work_shift', $this->work_shift)
                    ->first();

                TdJamKerjaJamMatiMesin::where('jam_kerja_mesin_id', $jamKerjaMesin->id)->delete();

                if (!empty($this->dataJamMatiMesin)) {
                    TdJamKerjaJamMatiMesin::insert(array_map(function ($item) use ($jamKerjaMesin) {
                        return [
                            'jam_kerja_mesin_id' => $jamKerjaMesin->id,
                            'jam_mati_mesin_id'  => $item['jam_mati_mesin_id'],
                            'off_hour'           => $item['off_hour'],
                            'from'               => $item['from'] ?? null,
                            'to'                 => $item['to'] ?? null,
                        ];
                    }, $this->dataJamMatiMesin));
                }

                $this->orderid = $jamKerjaMesin->id;
                $this->resetInputJamMatiMesin();
                $this->dataJamMatiMesin = [];
                $this->reset(['employeeno', 'empname', 'machineno', 'machinename', 'working_date', 'work_shift']);
                $this->resetInput();
                $this->dispatch('closeModalCreate');
            }

            $this->jamMatiDataUpdated = false;
            DB::commit();
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Work hour saved successfully.']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->dispatch('notification', ['type' => 'warning', 'message' => $e->validator->errors()]);
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Failed to save the order: ' . $e->getMessage()]);
        }
    }

    public function updatedJamMatiMesinCode($value): void
    {
        $code = trim((string) $value);
        $this->jamMatiMesinCode = $code;

        if ($code === '') { $this->notify('warning', 'Jam Mati Mesin Tidak Boleh Kosong'); return; }
        if (Str::length($code) < 3) { $this->notify('warning', 'Jam Mati Mesin Minimal 3 Karakter'); return; }

        $fullCode = Str::startsWith($code, 'I') ? $code : 'I' . $code;
        $jam = MsJamMatiMesin::select('id', 'name')->where('code', $fullCode)->first();

        if (!$jam) {
            $this->jamMatiMesinId = $this->jamMatiMesinName = $this->jamMatiMesinCode = '';
            $this->notify('warning', "Jam Mati Mesin {$code} Tidak Terdaftar");
            return;
        }

        $this->jamMatiMesinId   = $jam->id;
        $this->jamMatiMesinName = $jam->name;
    }

    public function updatedMachineno($machineno): void
    {
        $this->machineno = $machineno;
        if (empty($machineno)) return;

        $machine = MsMachine::where('machineno', 'ilike', '%' . $machineno)
            ->whereIn('department_id', departmentHelper::infureDepartment())
            ->orderBy('machineno', 'ASC')
            ->first();

        if (!$machine) {
            $this->dispatch('notification', ['type' => 'warning', 'message' => 'Machine ' . $machineno . ' Tidak Terdaftar']);
            $this->machinename = $this->machineno = '';
        } else {
            $this->machineno   = $machine->machineno;
            $this->machinename = $machine->machinename;
        }
    }

    public function updatedEmployeeno($employeeno): void
    {
        $this->employeeno = $employeeno;
        if (empty($employeeno) || strlen($employeeno) < 3) return;

        $msemployee = MsEmployee::where('employeeno', 'ilike', '%' . $employeeno)->active()->first();
        if (!$msemployee) {
            $this->empname = '';
            $this->dispatch('notification', ['type' => 'warning', 'message' => 'Employee ' . $employeeno . ' Tidak Terdaftar']);
        } else {
            $this->employeeno = $msemployee->employeeno;
            $this->empname    = $msemployee->empname;
        }
    }

    public function export()
    {
        $this->startHeavyJob();
        if ($this->tglMasuk == $this->tglKeluar) {
            $this->dispatch('notification', ['type' => 'warning', 'message' => 'Tanggal Masuk dan Tanggal Keluar tidak boleh sama']);
            return;
        }
        if (Carbon::parse($this->tglMasuk) > Carbon::parse($this->tglKeluar)) {
            $this->dispatch('notification', ['type' => 'warning', 'message' => 'Tanggal Masuk tidak boleh lebih besar dari Tanggal Keluar']);
            return;
        }

        $filter = [
            'machine_id' => $this->machine_id ?? null,
            'work_shift' => $this->work_shift_filter ?? null,
            'searchTerm' => $this->searchTerm ?? null,
            'transaksi'  => $this->transaksi ?? 1,
        ];

        $checklist = new CheckListJamKerjaController();
        $response  = $checklist->checklistJamKerja(
            Carbon::parse($this->tglMasuk . ' 07:01:00'),
            Carbon::parse($this->tglKeluar . ' 07:00:00'),
            $filter,
            'INFURE'
        );
        if ($response['status'] === 'success') {
            return response()->download($response['filename']);
        }
        $this->dispatch('notification', ['type' => 'warning', 'message' => $response['message']]);
    }

    public function render()
    {
        $machine = Cache::remember('ms_machines_infure_jk', 3600, fn() =>
            MsMachine::whereIn('department_id', departmentHelper::infurePabrikDepartment()->pluck('id'))
                ->select(['id', 'machineno', 'machinename'])
                ->get()
        );
        $workShift = Cache::remember('ms_workshifts_jk', 3600, fn() =>
            MsWorkingShift::active()->select(['id', 'work_shift'])->get()
        );

        if (!$this->isLoaded) {
            return view('livewire.jam-kerja.infure', [
                'data'      => new \Illuminate\Pagination\LengthAwarePaginator([], 0, $this->perPage),
                'machine'   => $machine,
                'workShift' => $workShift,
            ])->extends('layouts.master');
        }

        try {
            $data = DB::table('tdjamkerjamesin AS tdjkm')
                ->select([
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
                ])
                ->leftJoin('msmachine AS msm', 'msm.id', '=', 'tdjkm.machine_id')
                ->leftJoin('msemployee AS mse', 'mse.id', '=', 'tdjkm.employee_id')
                ->whereIn('tdjkm.department_id', departmentHelper::infureDivision());

            $dateColumn = $this->transaksi == 2 ? 'tdjkm.created_on' : 'tdjkm.working_date';
            if (!empty($this->tglMasuk) && $this->tglMasuk !== 'undefined') {
                $suffix = $this->transaksi == 2 ? ' 00:00:00' : '';
                $data->where($dateColumn, '>=', $this->tglMasuk . $suffix);
            }
            if (!empty($this->tglKeluar) && $this->tglKeluar !== 'undefined') {
                $suffix = $this->transaksi == 2 ? ' 23:59:59' : '';
                $data->where($dateColumn, '<=', $this->tglKeluar . $suffix);
            }
            if (!empty($this->machine_id) && $this->machine_id !== 'undefined') {
                $data->where('tdjkm.machine_id', $this->machine_id);
            }
            if (!empty($this->work_shift_filter) && $this->work_shift_filter !== 'undefined') {
                $data->where('tdjkm.work_shift', $this->work_shift_filter);
            }
            if (!empty($this->searchTerm) && $this->searchTerm !== 'undefined') {
                $data->where(function ($q) {
                    $q->where('msm.machineno', 'ilike', '%' . $this->searchTerm . '%')
                      ->orWhere('msm.machinename', 'ilike', '%' . $this->searchTerm . '%');
                });
            }

            $data = $data->orderBy($this->sortColumn, $this->sortDirection)
                         ->paginate($this->perPage);
        } catch (\Exception $e) {
            $data = new \Illuminate\Pagination\LengthAwarePaginator([], 0, $this->perPage);
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }

        return view('livewire.jam-kerja.infure', [
            'data'      => $data,
            'machine'   => $machine,
            'workShift' => $workShift,
        ])->extends('layouts.master');
    }

    protected function notify(string $type, string $message): void
    {
        $this->dispatch('notification', compact('type', 'message'));
    }
}
