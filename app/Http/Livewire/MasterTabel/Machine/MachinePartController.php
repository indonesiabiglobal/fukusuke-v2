<?php

namespace App\Http\Livewire\MasterTabel\Machine;

use App\Helpers\phpspreadsheet;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;
use Livewire\Attributes\Session;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Traits\HandlesHeavyJob;

class MachinePartController extends Component
{
    use WithPagination, WithoutUrlPagination;
    use HandlesHeavyJob;

    protected $paginationTheme = 'bootstrap';
    protected $listeners = ['delete', 'edit'];

    // Fields (disesuaikan dengan ms_machine_part)
    public $code;
    public $part_machine;
    public $department_id;
    public $status;
    public $idUpdate;
    public $idDelete;

    public $statusIsVisible = false;

    // Optional utility (dipertahankan agar kompatibel dengan layout lama)
    public $searchTerm;

    #[Session]
    public $sortingTable;

    public function mount()
    {
        if (empty($this->sortingTable)) {
            $this->sortingTable = [[2, 'asc']];
        }
    }

    public function updateSortingTable($value)
    {
        $this->sortingTable = $value;
        $this->skipRender();
    }

    public function resetFields()
    {
        $this->code = '';
        $this->part_machine = '';
        $this->department_id = '';
        $this->status = 1;
        $this->idUpdate = null;
        $this->idDelete = null;
        $this->statusIsVisible = false;
    }

    public function showModalCreate()
    {
        $this->resetFields();
        $this->dispatch('showModalCreate');
        $this->skipRender();
    }

    public function store()
    {
        $this->validate([
            'code'          => 'required|unique:ms_machine_part,code',
            'part_machine'  => 'required',
            'department_id' => 'required',
        ]);

        DB::beginTransaction();
        try {
            $statusActive = 1;
            DB::table('ms_machine_part')->insert([
                'code'          => $this->code,
                'part_machine'  => $this->part_machine,
                'department_id' => $this->department_id['value'] ?? $this->department_id,
                'status'        => $statusActive,
                'created_by'    => auth()->user()->username,
                'created_on'    => now(),
                'updated_by'    => auth()->user()->username,
                'updated_on'    => now(),
            ]);

            DB::commit();

            $this->resetFields();
            $this->dispatch('closeModalCreate');
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Machine Part created successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to save Machine Part: ' . $e->getMessage());
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Failed to save the Machine Part: ' . $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $mp = DB::table('ms_machine_part')->where('id', $id)->first();
        if (!$mp) {
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Machine Part not found.']);
            return;
        }

        $this->idUpdate       = $mp->id;
        $this->code           = $mp->code;
        $this->part_machine   = $mp->part_machine;
        $this->department_id  = $mp->department_id;
        $this->status         = $mp->status;
        $this->statusIsVisible = ((int)$mp->status === 0);

        $this->skipRender();
        $this->dispatch('showModalUpdate');
    }

    // (Opsional) Dipertahankan jika ada tombol/flow yang memanggil jadwal()
    public function jadwal($id)
    {
        $this->edit($id);
    }

    public function update()
    {
        $this->validate([
            'code'          => 'required|unique:ms_machine_part,code,' . $this->idUpdate,
            'part_machine'  => 'required',
            'department_id' => 'required',
        ]);

        DB::beginTransaction();
        try {
            DB::table('ms_machine_part')
                ->where('id', $this->idUpdate)
                ->update([
                    'code'          => $this->code,
                    'part_machine'  => $this->part_machine,
                    'department_id' => $this->department_id['value'] ?? $this->department_id,
                    'status'        => $this->status ?? 1,
                    'updated_by'    => auth()->user()->username,
                    'updated_on'    => now(),
                ]);

            DB::commit();

            $this->resetFields();
            $this->dispatch('closeModalUpdate');
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Machine Part updated successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update Machine Part: ' . $e->getMessage());
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Failed to update the Machine Part: ' . $e->getMessage()]);
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
        DB::beginTransaction();
        try {
            $statusInactive = 0;
            DB::table('ms_machine_part')
                ->where('id', $this->idDelete)
                ->update([
                    'status'     => $statusInactive,
                    'updated_by' => auth()->user()->username,
                    'updated_on' => now(),
                ]);

            DB::commit();
            $this->dispatch('closeModalDelete');
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Machine Part deleted successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete Machine Part: ' . $e->getMessage());
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Failed to delete the Machine Part: ' . $e->getMessage()]);
        }
    }

    public function export()
    {
        $response = $this->exportBagianMesin();
        if ($response['status'] == 'success') {
            return $response['spreadsheet'];
        } else if ($response['status'] == 'error') {
            $this->dispatch('notification', ['type' => 'warning', 'message' => $response['message']]);
            return;
        }
    }

    public function exportBagianMesin()
    {
        ini_set('max_execution_time', '300');
        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();
        $activeWorksheet->setShowGridlines(false);
        Carbon::setLocale('id');

        $activeWorksheet->setCellValue('A1', 'MASTER BAGIAN MESIN - ' . Carbon::now()->translatedFormat('M Y'));
        phpspreadsheet::styleFont($spreadsheet, 'A1', true, 11, 'Calibri');

        $rowHeaderStart = 2;
        $columnHeaderStart = 'A';
        $columnHeaderEnd = 'A';

        $header = ['No', 'Kode', 'Bagian Mesin', 'Departemen', 'Status', 'Updated By', 'Updated On'];

        foreach ($header as $value) {
            $activeWorksheet->setCellValue($columnHeaderEnd . $rowHeaderStart, $value);
            $columnHeaderEnd++;
        }

        $activeWorksheet->freezePane('A3');
        $activeWorksheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
        $activeWorksheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $activeWorksheet->getPageSetup()->setFitToPage(true);
        $activeWorksheet->getPageSetup()->setFitToWidth(1);
        $activeWorksheet->getPageSetup()->setFitToHeight(0);
        $activeWorksheet->getPageMargins()->setTop(0.75 / 2.54);
        $activeWorksheet->getPageMargins()->setBottom(0.75 / 2.54);
        $activeWorksheet->getPageMargins()->setLeft(0.75 / 2.54);
        $activeWorksheet->getPageMargins()->setRight(0.75 / 2.54);

        $columnHeaderEnd = chr(ord($columnHeaderEnd) - 1);
        phpspreadsheet::addFullBorder($spreadsheet, $columnHeaderStart . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);
        phpspreadsheet::styleFont($spreadsheet, $columnHeaderStart . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart, true, 9, 'Calibri');
        phpspreadsheet::textAlignCenter($spreadsheet, $columnHeaderStart . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);

        $data = DB::table('ms_machine_part as msp')
            ->select('msp.code', 'msp.part_machine', 'msd.name as departmentname', 'msp.status', 'msp.updated_by', 'msp.updated_on')
            ->leftJoin('msdepartment as msd', 'msd.id', '=', 'msp.department_id')
            ->orderBy('msp.code', 'ASC')
            ->get();

        if (count($data) == 0) {
            return ['status' => 'error', 'message' => 'Data tidak ditemukan'];
        }

        $rowItem = 3;
        foreach ($data as $key => $item) {
            $col = 'A';
            $activeWorksheet->setCellValue($col++ . $rowItem, $key + 1);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->code);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->part_machine);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->departmentname);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->status == 1 ? 'Active' : 'Inactive');
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->updated_by);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->updated_on);
            phpspreadsheet::styleFont($spreadsheet, 'A' . $rowItem . ':' . $columnHeaderEnd . $rowItem, false, 8, 'Calibri');
            phpspreadsheet::addFullBorder($spreadsheet, 'A' . $rowItem . ':' . $columnHeaderEnd . $rowItem);
            $rowItem++;
        }

        $rowFooter = $rowItem + 1;
        $activeWorksheet->setCellValue('A' . $rowFooter, 'Dicetak pada: ' . Carbon::now()->translatedFormat('d-M-Y H:i:s') . ', oleh: ' . auth()->user()->empname);
        phpspreadsheet::styleFont($spreadsheet, 'A' . $rowFooter, false, 9, 'Calibri');

        foreach (range('A', $columnHeaderEnd) as $col) {
            $activeWorksheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'Master-Bagian-Mesin.xlsx';
        $writer = new Xlsx($spreadsheet);
        $response = new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        });
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');
        return ['status' => 'success', 'spreadsheet' => $response];
    }

    public function render()
    {
        $data = DB::table('ms_machine_part as msp')
            ->select(
                'msp.id',
                'msp.code',
                'msp.part_machine',
                'msd.name as departmentname',
                'msp.status',
                'msp.updated_by',
                'msp.updated_on'
            )
            ->leftJoin('msdepartment as msd', 'msd.id', '=', 'msp.department_id')
            ->get();

        return view('livewire.master-tabel.machine.machine-part', [
            'data' => $data
        ])->extends('layouts.master');
    }

    public function rendered()
    {
        $this->dispatch('initDataTable');
    }
}
