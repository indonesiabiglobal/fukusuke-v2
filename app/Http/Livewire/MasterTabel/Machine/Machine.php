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

class Machine extends Component
{
    use WithPagination, WithoutUrlPagination;
    use HandlesHeavyJob;
    protected $paginationTheme = 'bootstrap';
    protected $listeners = ['delete', 'edit'];
    public $machines;
    public $searchTerm;
    public $machineno;
    public $machinename;
    public $department_id;
    public $product_group_id;
    public $capacity_kg;
    public $capacity_lembar;
    public $capacity_size;
    public $idUpdate;
    public $idDelete;
    public $status;
    public $statusIsVisible = false;
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
        $this->machineno = '';
        $this->machinename = '';
        $this->department_id = '';
        $this->product_group_id = '';
        $this->capacity_kg = '';
        $this->capacity_lembar = '';
        $this->capacity_size = '';
    }

    public function showModalCreate()
    {
        $this->resetFields();
        $this->dispatch('showModalCreate');
        // Mencegah render ulang
        $this->skipRender();
    }

    public function store()
    {
        $this->validate([
            'machineno' => 'required|unique:msmachine,machineno',
            'machinename' => 'required',
            'department_id' => 'required',
        ]);

        DB::beginTransaction();
        try {
            $statusActive = 1;
            DB::table('msmachine')->insert([
                'machineno' => $this->machineno,
                'machinename' => $this->machinename,
                'department_id' => $this->department_id['value'] ?? $this->department_id,
                'product_group_id' => $this->product_group_id['value'] ?? $this->product_group_id,
                'capacity_kg' => $this->capacity_kg,
                'capacity_lembar' => $this->capacity_lembar,
                'capacity_size' => $this->capacity_size,
                'status' => $statusActive,
                'created_by' => auth()->user()->username,
                'created_on' => now(),
                'updated_by' => auth()->user()->username,
                'updated_on' => now(),
            ]);
            DB::commit();
            $this->resetFields();
            $this->dispatch('closeModalCreate');
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Machine created successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to save master Machine: ' . $e->getMessage());
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Failed to save the Machine: ' . $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $machine = DB::table('msmachine')->where('id', $id)->first();
        $this->idUpdate = $machine->id;
        $this->machineno = $machine->machineno;
        $this->machinename = $machine->machinename;
        $this->department_id = $machine->department_id;
        $this->product_group_id = $machine->product_group_id;
        $this->capacity_kg = $machine->capacity_kg;
        $this->capacity_lembar = $machine->capacity_lembar;
        $this->capacity_size = $machine->capacity_size;
        $this->status = $machine->status;
        $this->statusIsVisible = $machine->status == 0 ? true : false;
        $this->skipRender();
        $this->dispatch('showModalUpdate');
    }

    public function jadwal($id)
    {
        $machine = DB::table('msmachine')->where('id', $id)->first();
        $this->idUpdate = $machine->id;
        $this->machineno = $machine->machineno;
        $this->machinename = $machine->machinename;
        $this->department_id = $machine->department_id;
        $this->product_group_id = $machine->product_group_id;
        $this->capacity_kg = $machine->capacity_kg;
        $this->capacity_lembar = $machine->capacity_lembar;
        $this->capacity_size = $machine->capacity_size;
        $this->status = $machine->status;
        $this->statusIsVisible = $machine->status == 0 ? true : false;
        $this->skipRender();
        $this->dispatch('showModalUpdate');
    }

    public function update()
    {
        $this->validate([
            'machineno' => 'required|unique:msmachine,machineno,' . $this->idUpdate,
            'machinename' => 'required',
            'department_id' => 'required',
        ]);

        DB::beginTransaction();
        try {
            DB::table('msmachine')->where('id', $this->idUpdate)->update([
                'machineno' => $this->machineno,
                'machinename' => $this->machinename,
                'department_id' => $this->department_id,
                'product_group_id' => $this->product_group_id,
                'capacity_kg' => $this->capacity_kg,
                'capacity_lembar' => $this->capacity_lembar,
                'capacity_size' => $this->capacity_size,
                'status' => $this->status,
                'updated_by' => auth()->user()->username,
                'updated_on' => now(),
            ]);
            DB::commit();
            $this->resetFields();
            $this->dispatch('closeModalUpdate');
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Machine updated successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update master Machine: ' . $e->getMessage());
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Failed to update the Machine: ' . $e->getMessage()]);
        }
    }

    public function delete($id)
    {
        $this->idDelete = $id;
        $this->dispatch('showModalDelete');
        // Mencegah render ulang
        $this->skipRender();
    }

    public function destroy()
    {
        DB::beginTransaction();
        try {
            $statusInactive = 0;
            DB::table('msmachine')->where('id', $this->idDelete)->update([
                'status' => $statusInactive,
                'updated_by' => auth()->user()->username,
                'updated_on' => now(),
            ]);
            DB::commit();
            $this->dispatch('closeModalDelete');
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Machine deleted successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete master Machine: ' . $e->getMessage());
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Failed to delete the Machine: ' . $e->getMessage()]);
        }
    }

    public function export()
    {
        $response = $this->exportMesin();
        if ($response['status'] == 'success') {
            return $response['spreadsheet'];
        } else if ($response['status'] == 'error') {
            $this->dispatch('notification', ['type' => 'warning', 'message' => $response['message']]);
            return;
        }
    }

    public function exportMesin()
    {
        ini_set('max_execution_time', '300');
        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();
        $activeWorksheet->setShowGridlines(false);
        Carbon::setLocale('id');

        $activeWorksheet->setCellValue('A1', 'MASTER MESIN - ' . Carbon::now()->translatedFormat('M Y'));
        phpspreadsheet::styleFont($spreadsheet, 'A1', true, 11, 'Calibri');

        $rowHeaderStart = 2;
        $columnHeaderStart = 'A';
        $columnHeaderEnd = 'A';

        $header = ['No', 'No. Mesin', 'Nama Mesin', 'Departemen', 'Jenis Produk', 'Kapasitas KG', 'Kapasitas Lembar', 'Status', 'Updated By', 'Updated On'];

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

        $data = DB::table('msmachine as msm')
            ->select('msm.machineno', 'msm.machinename', 'msd.name as departmentname', 'mpg.name as productgroupname', 'msm.capacity_kg', 'msm.capacity_lembar', 'msm.status', 'msm.updated_by', 'msm.updated_on')
            ->leftJoin('msdepartment as msd', 'msd.id', '=', 'msm.department_id')
            ->leftJoin('msproduct_group as mpg', 'mpg.id', '=', 'msm.product_group_id')
            ->orderBy('msm.machineno', 'ASC')
            ->get();

        if (count($data) == 0) {
            return ['status' => 'error', 'message' => 'Data tidak ditemukan'];
        }

        $rowItem = 3;
        foreach ($data as $key => $item) {
            $col = 'A';
            $activeWorksheet->setCellValue($col++ . $rowItem, $key + 1);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->machineno);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->machinename);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->departmentname);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->productgroupname);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->capacity_kg);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->capacity_lembar);
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

        $filename = 'Master-Mesin.xlsx';
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
        $data = DB::table('msmachine as msm')
            ->select(
                'msm.id',
                'msm.machinename',
                'msm.machineno',
                'msd.name as departmentname',
                'mpg.name as productgroupname',
                'msm.capacity_kg',
                'msm.capacity_lembar',
                'msm.status',
                'msm.updated_by',
                'msm.updated_on'
            )
            ->leftJoin('msdepartment as msd', 'msd.id', '=', 'msm.department_id')
            ->leftJoin('msproduct_group as mpg', 'mpg.id', '=', 'msm.product_group_id')
            ->get();

        return view('livewire.master-tabel.machine.machine', [
            'data' => $data
        ])->extends('layouts.master');
    }

    public function rendered()
    {
        $this->dispatch('initDataTable');
    }
}
