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

class Warehouse extends Component
{
    use WithPagination, WithoutUrlPagination;
    use HandlesHeavyJob;
    protected $paginationTheme = 'bootstrap';
    protected $listeners = ['delete', 'edit'];
    public $warehouses;
    public $searchTerm;
    public $name;
    public $city;
    public $country;
    public $description;
    public $province;
    public $address;
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
        $this->name = '';
        $this->city = '';
        $this->country = '';
        $this->description = '';
        $this->province = '';
        $this->address = '';
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
            'name' => 'required|unique:mswarehouse,name',
            'city' => 'required',
            'country' => 'required',
            'description' => 'required',
            'province' => 'required',
            'address' => 'required',
        ]);

        DB::beginTransaction();
        try {
            $statusActive = 1;
            DB::table('mswarehouse')->insert([
                'name' => $this->name,
                'city' => $this->city,
                'country' => $this->country,
                'description' => $this->description,
                'province' => $this->province,
                'address' => $this->address,
                'status' => $statusActive,
                'created_by' => auth()->user()->username,
                'created_on' => now(),
                'updated_by' => auth()->user()->username,
                'updated_on' => now(),
            ]);
            DB::commit();
            $this->resetFields();
            $this->dispatch('closeModalCreate');
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Warehouse created successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Failed to create warehouse.']);
        }
    }

    public function edit($id)
    {
        $warehouse = DB::table('mswarehouse')->where('id', $id)->first();
        $this->idUpdate = $id;
        $this->name = $warehouse->name;
        $this->city = $warehouse->city;
        $this->country = $warehouse->country;
        $this->description = $warehouse->description;
        $this->province = $warehouse->province;
        $this->address = $warehouse->address;
        $this->status = $warehouse->status;

        $this->statusIsVisible = $warehouse->status == 0 ? true : false;
        $this->skipRender();
        $this->dispatch('showModalUpdate');
    }

    public function update()
    {
        $this->validate([
            'name' => 'required|unique:mswarehouse,name,' . $this->idUpdate,
            'city' => 'required',
            'country' => 'required',
            'description' => 'required',
            'province' => 'required',
            'address' => 'required',
        ]);

        DB::beginTransaction();
        try {
            DB::table('mswarehouse')->where('id', $this->idUpdate)->update([
                'name' => $this->name,
                'city' => $this->city,
                'country' => $this->country,
                'description' => $this->description,
                'province' => $this->province,
                'address' => $this->address,
                'status' => $this->status,
                'updated_by' => auth()->user()->username,
                'updated_on' => now(),
            ]);
            DB::commit();
            $this->resetFields();
            $this->dispatch('closeModalUpdate');
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Warehouse updated successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Failed to update warehouse.']);
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
            DB::table('mswarehouse')->where('id', $this->idDelete)->update([
                'status' => $statusInactive,
                'updated_by' => auth()->user()->username,
                'updated_on' => now(),
            ]);
            DB::commit();
            $this->dispatch('closeModalDelete');
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Warehouse deleted successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Failed to delete warehouse.']);
        }
    }

    public function export()
    {
        $response = $this->exportWarehouse();
        if ($response['status'] == 'success') {
            return $response['spreadsheet'];
        } else if ($response['status'] == 'error') {
            $this->dispatch('notification', ['type' => 'warning', 'message' => $response['message']]);
            return;
        }
    }

    public function exportWarehouse()
    {
        ini_set('max_execution_time', '300');
        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();
        $activeWorksheet->setShowGridlines(false);
        Carbon::setLocale('id');

        $activeWorksheet->setCellValue('A1', 'MASTER WAREHOUSE - ' . Carbon::now()->translatedFormat('M Y'));
        phpspreadsheet::styleFont($spreadsheet, 'A1', true, 11, 'Calibri');

        $rowHeaderStart = 2;
        $columnHeaderStart = 'A';
        $columnHeaderEnd = 'A';

        $header = ['No', 'Nama', 'Kota', 'Alamat', 'Status', 'Updated By', 'Updated On'];

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

        $data = DB::table('mswarehouse')
            ->select('id', 'name', 'city', 'address', 'status', 'updated_by', 'updated_on')
            ->orderBy('name', 'ASC')
            ->get();

        if (count($data) == 0) {
            return ['status' => 'error', 'message' => 'Data tidak ditemukan'];
        }

        $rowItem = 3;
        foreach ($data as $key => $item) {
            $col = 'A';
            $activeWorksheet->setCellValue($col++ . $rowItem, $key + 1);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->name);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->city);
            $activeWorksheet->setCellValue($col++ . $rowItem, $item->address);
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

        $filename = 'Master-Warehouse.xlsx';
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
        $this->warehouses = DB::table('mswarehouse')
            ->select('id', 'name', 'city', 'address', 'status', 'updated_by', 'updated_on')
            ->get();

        return view('livewire.master-tabel.warehouse', [
            'data' => $this->warehouses,
        ])->extends('layouts.master');
    }

    public function rendered()
    {
        $this->dispatch('initDataTable');
    }
}
