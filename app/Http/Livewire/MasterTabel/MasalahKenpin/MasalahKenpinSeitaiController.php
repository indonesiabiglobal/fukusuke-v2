<?php

namespace App\Http\Livewire\MasterTabel\MasalahKenpin;

use App\Helpers\departmentHelper;
use App\Models\MsMasalahKenpin;
use Livewire\Component;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;
use Livewire\Attributes\Session;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Helpers\phpspreadsheet;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Traits\HandlesHeavyJob;

class MasalahKenpinSeitaiController extends Component
{
    use WithPagination, WithoutUrlPagination;
    use HandlesHeavyJob;
    protected $paginationTheme = 'bootstrap';
    protected $listeners = [
        'delete',
        'edit',
        'refreshData',
        'filterChanged'
    ];
    public $searchTerm;
    public $code;
    public $name;
    public $listDepartments;
    public $department;
    public $idUpdate;
    public $idDelete;
    public $status;
    public $statusIsVisible = false;
    #[Session]
    public $sortingTable;
    public $statusFilter = 'active';
    public $data;

    // Loading states
    public $isLoading = false;

    protected $rules = [
        'code' => 'required',
        'name' => 'required',
        'department' => 'required',
    ];

    public function mount()
    {
        if (empty($this->sortingTable)) {
            $this->sortingTable = [[2, 'asc']];
        }
        $this->listDepartments = departmentHelper::masalahKenpinSeitaiDepartmentGroup();
    }

    public function filterByStatus($status)
    {
        $this->isLoading = true;

        // Validate status
        $allowedStatuses = ['all', 'active', 'inactive'];
        if (in_array($status, $allowedStatuses)) {
            $this->statusFilter = $status;
        }

        $this->isLoading = false;

        // Show toast notification
        $this->dispatch('notification', ['type' => 'success', 'message' => 'Filter applied: ' . ucfirst($status)]);
    }

    public function clearFilter()
    {
        $this->statusFilter = 'all';

        $this->dispatch('notification', ['type' => 'success', 'message' => 'Filter cleared']);
    }

    public function getTotalRecordsProperty()
    {
        return MsMasalahKenpin::whereIn('department_group_id', departmentHelper::masalahKenpinSeitaiDepartmentGroup()->pluck('id'))->count();
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
        $this->department = '';
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
        $this->validate();

        DB::beginTransaction();
        try {
            MsMasalahKenpin::create([
                'code' => $this->code,
                'name' => $this->name,
                'department_group_id' => $this->department ?? $this->department["value"],
                'status' => 1,
                'created_by' => Auth::user()->username,
                'updated_by' => Auth::user()->username,
            ]);

            DB::commit();
            $this->dispatch('closeModalCreate');
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Master Kenpin Seitai saved successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to save master Kenpin Seitai: ' . $e->getMessage());
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Failed to save the Kenpin Seitai: ' . $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $data = MsMasalahKenpin::where('id', $id)->first();
        $this->idUpdate = $id;
        $this->code = $data->code;
        $this->name = $data->name;
        $this->department = $data->department_group_id;
        $this->status = $data->status;
        $this->statusIsVisible = $data->status == 0 ? true : false;
        $this->skipRender();

        $this->dispatch('showModalUpdate');
    }

    public function update()
    {
        $this->validate();

        DB::beginTransaction();
        try {
            $data = MsMasalahKenpin::where('id', $this->idUpdate)->first();
            $data->code = $this->code;
            $data->name = $this->name;
            $data->department_group_id = $this->department ?? $this->department["value"];
            $data->status = $this->status;
            $data->updated_by = Auth::user()->username;
            $data->save();

            DB::commit();
            $this->dispatch('closeModalUpdate');
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Master Jam Mati Mesin Seitai updated successfully.']);
        } catch (\Exception $e) {
            $this->skipRender();
            DB::rollBack();
            Log::error('Failed to update master Kenpin Seitai: ' . $e->getMessage());
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Failed to update the Kenpin Seitai: ' . $e->getMessage()]);
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
            $data = MsMasalahKenpin::where('id', $this->idDelete)->first();
            $data->status = $statusInactive;
            $data->updated_by = Auth::user()->username;
            $data->save();

            DB::commit();
            $this->dispatch('closeModalDelete');
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Master Kenpin Seitai deleted successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete master Kenpin Seitai: ' . $e->getMessage());
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Failed to delete the Kenpin Seitai: ' . $e->getMessage()]);
        }
    }

    public function export()
    {
        $this->startHeavyJob();
        $response = $this->exportMasalahKenpinSeitai();
        if ($response['status'] == 'success') {
            return $response['spreadsheet'];
        } else if ($response['status'] == 'error') {
            $this->dispatch('notification', ['type' => 'warning', 'message' => $response['message']]);
            return;
        }
    }

    private function exportMasalahKenpinSeitai()
    {
        ini_set('max_execution_time', '300');
        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();
        $activeWorksheet->setShowGridlines(false);
        Carbon::setLocale('id');

        $activeWorksheet->setCellValue('A1', 'MASTER MASALAH KENPIN SEITAI - ' . Carbon::now()->translatedFormat('M Y'));
        phpspreadsheet::styleFont($spreadsheet, 'A1', true, 11, 'Calibri');

        $header = ['No', 'Kode', 'Nama', 'Status', 'Updated By', 'Updated On'];
        $columnHeaderEnd = 'A';
        foreach ($header as $value) {
            $activeWorksheet->setCellValue($columnHeaderEnd . '2', $value);
            phpspreadsheet::styleFont($spreadsheet, $columnHeaderEnd . '2', true, 11, 'Calibri');
            phpspreadsheet::addFullBorder($spreadsheet, $columnHeaderEnd . '2');
            phpspreadsheet::textAlignCenter($spreadsheet, $columnHeaderEnd . '2');
            $columnHeaderEnd++;
        }
        $columnHeaderEnd = chr(ord($columnHeaderEnd) - 1);

        $departments = departmentHelper::masalahKenpinSeitaiDepartmentGroup();
        $data = MsMasalahKenpin::whereIn('department_group_id', $departments->pluck('id'))
            ->orderBy('code', 'ASC')
            ->get();

        if (count($data) == 0) {
            return ['status' => 'error', 'message' => 'Data tidak ditemukan'];
        }

        $row = 3;
        $no = 1;
        foreach ($data as $item) {
            $activeWorksheet->setCellValue('A' . $row, $no++);
            $activeWorksheet->setCellValue('B' . $row, $item->code);
            $activeWorksheet->setCellValue('C' . $row, $item->name);
            $activeWorksheet->setCellValue('D' . $row, $item->status == 1 ? 'Active' : 'Inactive');
            $activeWorksheet->setCellValue('E' . $row, $item->updated_by);
            $activeWorksheet->setCellValue('F' . $row, $item->updated_at);
            foreach (range('A', $columnHeaderEnd) as $col) {
                phpspreadsheet::styleFont($spreadsheet, $col . $row, false, 11, 'Calibri');
                phpspreadsheet::addFullBorder($spreadsheet, $col . $row);
            }
            $row++;
        }

        $rowFooter = $row + 1;
        $activeWorksheet->setCellValue('A' . $rowFooter, 'Dicetak pada: ' . Carbon::now()->translatedFormat('d-M-Y H:i:s') . ', oleh: ' . auth()->user()->empname);
        phpspreadsheet::styleFont($spreadsheet, 'A' . $rowFooter, false, 11, 'Calibri');

        $activeWorksheet->mergeCells('A1:' . $columnHeaderEnd . '1');
        phpspreadsheet::textAlignCenter($spreadsheet, 'A1');

        $spreadsheet->getActiveSheet()->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $spreadsheet->getActiveSheet()->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
        $spreadsheet->getActiveSheet()->getPageSetup()->setFitToWidth(1);
        $spreadsheet->getActiveSheet()->getPageSetup()->setFitToHeight(0);
        $spreadsheet->getActiveSheet()->getPageMargins()->setTop(0.75 / 2.54);
        $spreadsheet->getActiveSheet()->getPageMargins()->setBottom(0.75 / 2.54);
        $spreadsheet->getActiveSheet()->getPageMargins()->setLeft(0.75 / 2.54);
        $spreadsheet->getActiveSheet()->getPageMargins()->setRight(0.75 / 2.54);
        $spreadsheet->getActiveSheet()->freezePane('A3');

        foreach (range('A', $columnHeaderEnd) as $col) {
            $spreadsheet->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $response = new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        });
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment; filename="Master-Masalah-Kenpin-Seitai.xlsx"');
        return ['status' => 'success', 'spreadsheet' => $response];
    }

    public function render()
    {
        $query = MsMasalahKenpin::with('departmentGroup');

        // Apply status filter
        if ($this->statusFilter !== 'all') {
            $status = $this->statusFilter === 'active' ? 1 : 0;
            $query->where('status', $status);
        }

        $query->whereIn('department_group_id', $this->listDepartments->pluck('id'));

        $result = $query->get();

        return view('livewire.master-tabel.masalah-kenpin.seitai', [
            'result' => $result,
            'totalRecords' => $this->totalRecords,
        ])->extends('layouts.master');
    }

    public function rendered()
    {
        $this->dispatch('initDataTable');
    }
}
