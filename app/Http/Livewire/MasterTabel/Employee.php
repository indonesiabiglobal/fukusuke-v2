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

class Employee extends Component
{
    use WithPagination, WithoutUrlPagination;
    use HandlesHeavyJob;
    protected $paginationTheme = 'bootstrap';
    protected $listeners = ['delete', 'edit'];
    public $data;
    public $searchTerm;
    public $employeeno;
    public $empname;
    public $department_id;
    public $department_name;
    public $idUpdate;
    public $idDelete;
    public $status;
    public $statusIsVisible = false;
    public $paginate = 10;
    #[Session]
    public $sortingTable;

    public function mount()
    {
        $this->data =  DB::table('msemployee as mse')
            ->select(
                'mse.id',
                'mse.employeeno',
                'mse.empname',
                'mse.department_id',
                'mse.status',
                'mse.updated_by',
                'mse.updated_on',
                'msd.name as department_name'
            )
            ->leftJoin('msdepartment as msd', 'mse.department_id', '=', 'msd.id')
            ->get();

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
        $this->employeeno = '';
        $this->empname = '';
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
            'employeeno' => 'required|unique:msemployee,employeeno',
            'empname' => 'required',
            'department_id' => 'required',
        ]);

        DB::beginTransaction();
        try {
            $statusActive = 1;
            DB::table('msemployee')->insert([
                'employeeno' => $this->employeeno,
                'empname' => $this->empname,
                'department_id' => is_array($this->department_id) ? $this->department_id['value'] : $this->department_id,
                'status' => $statusActive,
                'created_by' => auth()->user()->username,
                'created_on' => now(),
                'updated_by' => auth()->user()->username,
                'updated_on' => now(),
            ]);
            DB::commit();
            $this->resetFields();
            $this->dispatch('closeModalCreate');
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Employee created successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to save master Employee: ' . $e->getMessage());
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Failed to save the Employee: ' . $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $employee = DB::table('msemployee as mse')
            ->join('msdepartment as msd', 'mse.department_id', '=', 'msd.id')
            ->where('mse.id', $id)
            ->first(['mse.employeeno', 'mse.empname', 'mse.department_id', 'mse.status', 'msd.name as department_name']);
        $this->employeeno = $employee->employeeno;
        $this->empname = $employee->empname;
        $this->department_id = $employee->department_id;
        $this->department_name = $employee->department_name;
        $this->idUpdate = $id;
        $this->status = $employee->status;
        $this->statusIsVisible = $employee->status == 0 ? true : false;
        $this->skipRender();
        $this->dispatch('showModalUpdate');
    }

    public function update()
    {
        $this->validate([
            'employeeno' => 'required|unique:msemployee,employeeno,' . $this->idUpdate,
            'empname' => 'required',
            'department_id' => 'required',
        ]);

        DB::beginTransaction();
        try {
            DB::table('msemployee')
                ->where('id', $this->idUpdate)
                ->update([
                    'employeeno' => $this->employeeno,
                    'empname' => $this->empname,
                    'department_id' => $this->department_id,
                    'status' => $this->status,
                    'updated_by' => auth()->user()->username,
                    'updated_on' => now(),
                ]);
            DB::commit();
            $this->resetFields();
            $this->dispatch('closeModalUpdate');
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Employee updated successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            $this->skipRender();
            Log::error('Failed to update master Employee: ' . $e->getMessage());
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Failed to update the Employee: ' . $e->getMessage()]);
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
            DB::table('msemployee')->where('id', $this->idDelete)->update([
                'status' => $statusInactive,
                'updated_by' => auth()->user()->username,
                'updated_on' => now(),
            ]);
            DB::commit();
            $this->dispatch('closeModalDelete');
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Employee deleted successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete master Employee: ' . $e->getMessage());
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Failed to delete the Employee: ' . $e->getMessage()]);
        }
    }

    public function export()
    {
        $response = $this->exportKaryawan();
        if ($response['status'] == 'success') {
            return $response['spreadsheet'];
        } else if ($response['status'] == 'error') {
            $this->dispatch('notification', ['type' => 'warning', 'message' => $response['message']]);
            return;
        }
    }

    public function exportKaryawan()
    {
        ini_set('max_execution_time', '300');
        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();
        $activeWorksheet->setShowGridlines(false);
        Carbon::setLocale('id');

        $activeWorksheet->setCellValue('A1', 'MASTER KARYAWAN - ' . Carbon::now()->translatedFormat('M Y'));
        phpspreadsheet::styleFont($spreadsheet, 'A1', true, 11, 'Calibri');

        $rowHeaderStart = 2;
        $columnHeaderStart = 'A';
        $columnHeaderEnd = 'A';

        $header = ['No', 'No. Karyawan', 'Nama Karyawan', 'Departemen', 'Status', 'Updated By', 'Updated On'];

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

        $data = DB::table('msemployee as mse')
            ->select('mse.employeeno', 'mse.empname', 'msd.name as department_name', 'mse.status', 'mse.updated_by', 'mse.updated_on')
            ->leftJoin('msdepartment as msd', 'mse.department_id', '=', 'msd.id')
            ->orderBy('mse.empname', 'ASC')
            ->get();

        if (count($data) == 0) {
            return ['status' => 'error', 'message' => 'Data tidak ditemukan'];
        }

        $rowItem = 3;
        foreach ($data as $key => $item) {
            $col = 'A';
            $activeWorksheet->setCellValue($col++ . $rowItem, $key + 1);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->employeeno);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->empname);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->department_name);
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

        $filename = 'Master-Karyawan.xlsx';
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
        $this->data =  DB::table('msemployee as mse')
            ->select(
                'mse.id',
                'mse.employeeno',
                'mse.empname',
                'mse.department_id',
                'mse.status',
                'mse.updated_by',
                'mse.updated_on',
                'msd.name as department_name'
            )
            ->leftJoin('msdepartment as msd', 'mse.department_id', '=', 'msd.id')
            ->get();

        return view('livewire.master-tabel.employee')->extends('layouts.master');
    }

    public function rendered()
    {
        $this->dispatch('initDataTable');
    }
}
