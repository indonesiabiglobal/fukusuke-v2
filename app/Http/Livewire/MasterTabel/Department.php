<?php

namespace App\Http\Livewire\MasterTabel;

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

class Department extends Component
{
    use WithPagination, WithoutUrlPagination;
    use HandlesHeavyJob;
    protected $paginationTheme = 'bootstrap';
    protected $listeners = ['delete', 'edit'];
    public $departments;
    public $searchTerm;
    public $code;
    public $name;
    public $divisionCode;
    public $idUpdate;
    public $idDelete;
    public $status;
    public $statusIsVisible = false;
    #[Session]
    public $sortingTable;

    public function mount()
    {
        $this->departments = DB::table('msdepartment')
            ->get(['id', 'code', 'name', 'division_code', 'status', 'updated_by', 'updated_on']);
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
        $this->name = '';
        $this->divisionCode = '';
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
            'code' => 'required|unique:msdepartment,code',
            'name' => 'required',
            'divisionCode' => 'required',
        ]);

        DB::beginTransaction();
        try {
            $statusActive = 1;
            DB::table('msdepartment')->insert([
                'code' => $this->code,
                'name' => $this->name,
                'division_code' => $this->divisionCode,
                'status' => $statusActive,
                'created_by' => auth()->user()->username,
                'created_on' => now(),
                'updated_by' => auth()->user()->username,
                'updated_on' => now(),
            ]);
            DB::commit();
            $this->resetFields();
            $this->search();
            $this->dispatch('closeModalCreate');
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Master Department saved successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Failed to save the Department: ' . $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $department = DB::table('msdepartment')->where('id', $id)->first();
        $this->idUpdate = $department->id;
        $this->code = $department->code;
        $this->name = $department->name;
        $this->divisionCode = $department->division_code;
        $this->status = $department->status;
        $this->status = $department->status;
        $this->statusIsVisible = $department->status == 0 ? true : false;
        $this->skipRender();
        $this->dispatch('showModalUpdate');
    }

    public function update()
    {
        $this->validate([
            'code' => 'required|unique:msdepartment,code,' . $this->idUpdate,
            'name' => 'required',
            'divisionCode' => 'required',
        ]);

        DB::beginTransaction();
        try {
            DB::table('msdepartment')->where('id', $this->idUpdate)->update([
                'code' => $this->code,
                'name' => $this->name,
                'division_code' => $this->divisionCode,
                'status' => $this->status,
                'updated_by' => auth()->user()->username,
                'updated_on' => now(),
            ]);
            DB::commit();
            $this->resetFields();
            $this->search();
            $this->dispatch('closeModalUpdate');
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Master Department updated successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Failed to update the Department: ' . $e->getMessage()]);
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

            DB::table('msdepartment')->where('id', $this->idDelete)->update([
                'status' => $statusInactive,
                'updated_by' => auth()->user()->username,
                'updated_on' => now(),
            ]);
            DB::commit();
            $this->search();
            $this->dispatch('closeModalDelete');
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Master Department deleted successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Failed to delete the Department: ' . $e->getMessage()]);
        }
    }

    public function export()
    {
        $response = $this->exportDepartemen();
        if ($response['status'] == 'success') {
            return $response['spreadsheet'];
        } else if ($response['status'] == 'error') {
            $this->dispatch('notification', ['type' => 'warning', 'message' => $response['message']]);
            return;
        }
    }

    public function exportDepartemen()
    {
        ini_set('max_execution_time', '300');
        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();
        $activeWorksheet->setShowGridlines(false);
        Carbon::setLocale('id');

        $activeWorksheet->setCellValue('A1', 'MASTER DEPARTEMEN - ' . Carbon::now()->translatedFormat('M Y'));
        phpspreadsheet::styleFont($spreadsheet, 'A1', true, 11, 'Calibri');

        $rowHeaderStart = 2;
        $columnHeaderStart = 'A';
        $columnHeaderEnd = 'A';

        $header = ['No', 'Kode', 'Nama', 'Kode Divisi', 'Status', 'Updated By', 'Updated On'];

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

        $data = DB::table('msdepartment')
            ->select('id', 'code', 'name', 'division_code', 'status', 'updated_by', 'updated_on')
            ->orderBy('code', 'ASC')
            ->get();

        if (count($data) == 0) {
            return ['status' => 'error', 'message' => 'Data tidak ditemukan'];
        }

        $rowItem = 3;
        foreach ($data as $key => $item) {
            $col = 'A';
            $activeWorksheet->setCellValue($col++ . $rowItem, $key + 1);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->code);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->name);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->division_code);
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

        $filename = 'Master-Departemen.xlsx';
        $writer = new Xlsx($spreadsheet);
        $response = new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        });
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');
        return ['status' => 'success', 'spreadsheet' => $response];
    }

    public function search()
    {
        $this->resetPage();
        $this->render();
    }

    public function render()
    {

        $data = DB::table('msdepartment')
            ->select('id', 'code', 'name', 'division_code', 'status', 'updated_by', 'updated_on')
            ->when(isset($this->searchTerm) && $this->searchTerm != "" && $this->searchTerm != "undefined", function ($query) {
                $query
                    ->where(function ($query) {
                        $query->where('code', 'ilike', '%' . $this->searchTerm . '%')
                            ->orWhere('name', 'ilike', '%' . $this->searchTerm . '%')
                            ->orWhere('division_code', 'ilike', '%' . $this->searchTerm . '%');
                    });
            })
            // ->paginate(10);
            ->get();

        return view('livewire.master-tabel.department', [
            'data' =>  $data
        ])->extends('layouts.master');
    }

    public function rendered()
    {
        $this->dispatch('initDataTable');
    }
}
