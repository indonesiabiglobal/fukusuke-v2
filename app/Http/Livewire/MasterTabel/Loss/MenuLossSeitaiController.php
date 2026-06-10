<?php

namespace App\Http\Livewire\MasterTabel\Loss;

use App\Models\MsLossCategory;
use App\Models\MsLossClass;
use App\Models\MsLossSeitai;
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

class MenuLossSeitaiController extends Component
{
    use WithPagination, WithoutUrlPagination;
    use HandlesHeavyJob;
    protected $paginationTheme = 'bootstrap';
    protected $listeners = ['delete','edit'];
    public $searchTerm;
    public $code;
    public $name;
    public $loss_class_id;
    public $loss_category_code;
    public $idUpdate;
    public $idDelete;
    public $class;
    public $status;
    public $categories;
    public $statusIsVisible = false;
    #[Session]
    public $sortingTable;

    protected $rules = [
        'code' => 'required',
        'name' => 'required',
    ];

    public function mount(){
        $this->class = MsLossClass::get();
        $this->categories = MsLossCategory::get();

        if (empty($this->sortingTable)) {
            $this->sortingTable = [[1, 'asc']];
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
            $statusActive = 1;
            $data = MsLossSeitai::create([
                'code' => $this->code,
                'name' => $this->name,
                'loss_class_id' => $this->loss_class_id['value'],
                'loss_category_code' => $this->loss_category_code['value'],
                'status' => $statusActive,
                'created_by' => Auth::user()->username,
                'created_on' => Carbon::now(),
                'updated_by' => Auth::user()->username,
                'updated_on' => Carbon::now(),
            ]);

            DB::commit();
            $this->dispatch('closeModalCreate');
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Master Buyer saved successfully.']);
            // return redirect()->route('buyer');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to save master buyer: ' . $e->getMessage());
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Failed to save the buyer: ' . $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $data = MsLossSeitai::where('id', $id)->first();
        $this->idUpdate = $data->id;
        $this->code = $data->code;
        $this->name = $data->name;
        $this->loss_class_id = $data->loss_class_id;
        $this->loss_category_code = $data->loss_category_code;
        $this->status = $data->status;
        $this->statusIsVisible = $data->status == 0 ? true : false;
        // $this->skipRender();

        $this->dispatch('showModalUpdate');
    }

    public function update()
    {
        $this->validate();

        DB::beginTransaction();
        try {
            $statusActive = 1;
            $data = MsLossSeitai::where('id', $this->idUpdate)->first();
            $data->code = $this->code;
            $data->name = $this->name;
            $data->loss_class_id = is_array($this->loss_class_id) ? $this->loss_class_id['value'] : $this->loss_class_id;
            $data->loss_category_code = is_array($this->loss_category_code) ? $this->loss_category_code['value'] : $this->loss_category_code;
            $data->status = $statusActive;
            $data->updated_by = Auth::user()->username;
            $data->updated_on = Carbon::now();
            $data->save();

            DB::commit();
            $this->dispatch('closeModalUpdate');
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Master Loss Infure updated successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update master Loss Infure: ' . $e->getMessage());
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Failed to update the Loss Infure: ' . $e->getMessage()]);
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
            $data = MsLossSeitai::where('id', $this->idDelete)->first();
            $data->status = $statusInactive;
            $data->updated_by = Auth::user()->username;
            $data->updated_on = Carbon::now();
            $data->save();

            DB::commit();
            $this->dispatch('closeModalDelete');
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Master Loss Infure deleted successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete master Loss Infure: ' . $e->getMessage());
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Failed to delete the Loss Infure: ' . $e->getMessage()]);
        }
    }

    public function export()
    {
        $this->startHeavyJob();
        $response = $this->exportLossSeitai();
        if ($response['status'] == 'success') {
            return $response['spreadsheet'];
        } else if ($response['status'] == 'error') {
            $this->dispatch('notification', ['type' => 'warning', 'message' => $response['message']]);
            return;
        }
    }

    private function exportLossSeitai()
    {
        ini_set('max_execution_time', '300');
        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();
        $activeWorksheet->setShowGridlines(false);
        Carbon::setLocale('id');

        $activeWorksheet->setCellValue('A1', 'MASTER LOSS SEITAI - ' . Carbon::now()->translatedFormat('M Y'));
        phpspreadsheet::styleFont($spreadsheet, 'A1', true, 11, 'Calibri');

        $header = ['No', 'Kode', 'Nama', 'Kategori', 'Kelas', 'Status', 'Updated By', 'Updated On'];
        $columnHeaderEnd = 'A';
        foreach ($header as $value) {
            $activeWorksheet->setCellValue($columnHeaderEnd . '2', $value);
            phpspreadsheet::styleFont($spreadsheet, $columnHeaderEnd . '2', true, 11, 'Calibri');
            phpspreadsheet::addFullBorder($spreadsheet, $columnHeaderEnd . '2');
            phpspreadsheet::textAlignCenter($spreadsheet, $columnHeaderEnd . '2');
            $columnHeaderEnd++;
        }
        $columnHeaderEnd = chr(ord($columnHeaderEnd) - 1);

        $data = MsLossSeitai::join('mslosscategory as mlc', 'mslossseitai.loss_category_code', '=', 'mlc.code')
            ->join('mslossclass as mlcl', 'mslossseitai.loss_class_id', '=', 'mlcl.id')
            ->select('mslossseitai.*', 'mlc.name as category_name', 'mlcl.name as class_name')
            ->orderBy('mslossseitai.code', 'ASC')
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
            $activeWorksheet->setCellValue('D' . $row, $item->category_name);
            $activeWorksheet->setCellValue('E' . $row, $item->class_name);
            $activeWorksheet->setCellValue('F' . $row, $item->status == 1 ? 'Active' : 'Inactive');
            $activeWorksheet->setCellValue('G' . $row, $item->updated_by);
            $activeWorksheet->setCellValue('H' . $row, $item->updated_on);
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
        $response->headers->set('Content-Disposition', 'attachment; filename="Master-Loss-Seitai.xlsx"');
        return ['status' => 'success', 'spreadsheet' => $response];
    }

    public function render()
    {
        $result = MsLossSeitai::join('mslosscategory as mlc', 'mslossseitai.loss_category_code', '=', 'mlc.code')
        ->join('mslossclass as mlcl', 'mslossseitai.loss_class_id', '=', 'mlcl.id')
        ->select('mslossseitai.*', 'mlc.name as category_name', 'mlcl.name as class_name')
        ->get();

        return view('livewire.master-tabel.loss.menu-loss-seitai',[
            'result' => $result
        ])->extends('layouts.master');
    }

    public function rendered()
    {
        $this->dispatch('initDataTable');
    }
}
