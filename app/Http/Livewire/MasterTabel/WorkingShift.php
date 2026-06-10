<?php

namespace App\Http\Livewire\MasterTabel;

use App\Helpers\phpspreadsheet;
use App\Models\MsWorkingShift;
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

class WorkingShift extends Component
{
    use WithPagination, WithoutUrlPagination;
    use HandlesHeavyJob;
    protected $paginationTheme = 'bootstrap';
    protected $listeners = ['delete', 'edit'];
    public $workingShifts;
    public $searchTerm;
    public $work_shift;
    public $work_hour_from;
    public $work_hour_till;
    public $idUpdate;
    public $idDelete;
    public $status;
    public $statusIsVisible;

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
        $this->work_shift = '';
        $this->work_hour_from = '';
        $this->work_hour_till = '';
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
            'work_shift' => 'required|unique:msworkingshift,work_shift',
            'work_hour_from' => 'required',
            'work_hour_till' => 'required',
        ]);

        DB::beginTransaction();
        try {
            // check jika work_hour_from sama dengan work_hour_till
            if ($this->work_hour_from == $this->work_hour_till) {
                $this->dispatch('notification', ['type' => 'error', 'message' => 'Work Hour From cannot be equal to Work Hour Till.']);
                return;
            }

            $statusActive = 1;
            $msworkingshift = MsWorkingShift::create([
                'work_shift' => $this->work_shift,
                'work_hour_from' => $this->work_hour_from,
                'work_hour_till' => $this->work_hour_till,
                'status' => $statusActive,
                'created_by' => auth()->user()->username,
                'created_on' => Carbon::now(),
                'updated_by' => auth()->user()->username,
                'updated_on' => Carbon::now(),
                'trial464' => 'T'
            ]);
            DB::commit();
            $this->resetFields();
            $this->dispatch('closeModalCreate');
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Master Working Shift saved successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to save master Working Shift: ' . $e->getMessage());
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Failed to save the Working Shift: ' . $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $workingShift = MsWorkingShift::find($id);
        $this->idUpdate = $id;
        $this->work_shift = $workingShift->work_shift;
        $this->work_hour_from = $workingShift->work_hour_from;
        $this->work_hour_till = $workingShift->work_hour_till;
        $this->status = $workingShift->status;

        $this->statusIsVisible = $workingShift->status == 0 ? true : false;
        $this->skipRender();
        $this->dispatch('showModalUpdate');
    }

    public function update()
    {
        $this->validate([
            'work_shift' => 'required|unique:msworkingshift,work_shift,' . $this->idUpdate,
            'work_hour_from' => 'required',
            'work_hour_till' => 'required',
        ]);

        DB::beginTransaction();
        try {
            // check jika work_hour_from lebih besar dari work_hour_till
            if ($this->work_hour_from > $this->work_hour_till) {
                $this->dispatch('notification', ['type' => 'error', 'message' => 'Work Hour From cannot be greater than Work Hour Till.']);
                return;
            }

            // check jika work_hour_from sama dengan work_hour_till
            if ($this->work_hour_from == $this->work_hour_till) {
                $this->dispatch('notification', ['type' => 'error', 'message' => 'Work Hour From cannot be equal to Work Hour Till.']);
                return;
            }
            MsWorkingShift::where('id', $this->idUpdate)->update([
                'work_shift' => $this->work_shift,
                'work_hour_from' => $this->work_hour_from,
                'work_hour_till' => $this->work_hour_till,
                'status' => $this->status,
                'updated_by' => auth()->user()->username,
                'updated_on' => Carbon::now(),
            ]);
            DB::commit();
            $this->resetFields();
            $this->dispatch('closeModalUpdate');
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Master Working Shift updated successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            $this->skipRender();
            Log::error('Failed to update master Working Shift: ' . $e->getMessage());
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Failed to update the Working Shift: ' . $e->getMessage()]);
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
            MsWorkingShift::where('id', $this->idDelete)->update([
                'status' => $statusInactive,
                'updated_by' => auth()->user()->username,
                'updated_on' => Carbon::now(),
            ]);
            DB::commit();
            $this->dispatch('closeModalDelete');
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Master Working Shift deleted successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete master Working Shift: ' . $e->getMessage());
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Failed to delete the Working Shift: ' . $e->getMessage()]);
        }
    }

    public function export()
    {
        $response = $this->exportWorkingShift();
        if ($response['status'] == 'success') {
            return $response['spreadsheet'];
        } else if ($response['status'] == 'error') {
            $this->dispatch('notification', ['type' => 'warning', 'message' => $response['message']]);
            return;
        }
    }

    public function exportWorkingShift()
    {
        ini_set('max_execution_time', '300');
        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();
        $activeWorksheet->setShowGridlines(false);
        Carbon::setLocale('id');

        $activeWorksheet->setCellValue('A1', 'MASTER WORKING SHIFT - ' . Carbon::now()->translatedFormat('M Y'));
        phpspreadsheet::styleFont($spreadsheet, 'A1', true, 11, 'Calibri');

        $rowHeaderStart = 2;
        $columnHeaderStart = 'A';
        $columnHeaderEnd = 'A';

        $header = ['No', 'Shift', 'Jam Mulai', 'Jam Selesai', 'Status', 'Updated By', 'Updated On'];

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

        $data = MsWorkingShift::orderBy('work_shift', 'ASC')->get();

        if (count($data) == 0) {
            return ['status' => 'error', 'message' => 'Data tidak ditemukan'];
        }

        $rowItem = 3;
        foreach ($data as $key => $item) {
            $col = 'A';
            $activeWorksheet->setCellValue($col++ . $rowItem, $key + 1);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->work_shift);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->work_hour_from);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->work_hour_till);
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

        $filename = 'Master-Working-Shift.xlsx';
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
        $workingShifts = MsWorkingShift::where('work_shift', 'like', '%' . $this->searchTerm . '%')
            ->get();

        return view('livewire.master-tabel.working-shift', [
            'data' => $workingShifts
        ])->extends('layouts.master');
    }

    public function rendered()
    {
        $this->dispatch('initDataTable');
    }
}
