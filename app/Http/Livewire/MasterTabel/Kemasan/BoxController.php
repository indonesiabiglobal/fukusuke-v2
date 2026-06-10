<?php

namespace App\Http\Livewire\MasterTabel\Kemasan;

use App\Models\MsPackagingBox;
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

class BoxController extends Component
{
    use WithPagination, WithoutUrlPagination;
    use HandlesHeavyJob;
    protected $paginationTheme = 'bootstrap';
    protected $listeners = ['delete', 'edit'];
    public $searchTerm;
    public $code;
    public $name;
    public $box_class;
    public $panjang;
    public $lebar;
    public $tinggi;
    public $idUpdate;
    public $idDelete;
    public $class;
    public $status;
    public $statusIsVisible = false;
    #[Session]
    public $sortingTable;

    protected $rules = [
        'code' => 'required',
        'name' => 'required',
        'box_class' => 'required',
        'panjang' => 'required',
        'lebar' => 'required',
        'tinggi' => 'required',
    ];
    
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
        $this->name = '';
        $this->box_class['value'] = 1;
        $this->panjang = '';
        $this->lebar = '';
        $this->tinggi = '';
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
            $data = MsPackagingBox::create([
                'code' => $this->code,
                'name' => $this->name,
                'box_class' => $this->box_class['value'],
                'panjang' => (int)str_replace(',', '', $this->panjang),
                'lebar' => (int)str_replace(',', '', $this->lebar),
                'tinggi' => (int)str_replace(',', '', $this->tinggi),
                'status' => $statusActive,
                'created_by' => Auth::user()->username,
                'created_on' => Carbon::now(),
                'updated_by' => Auth::user()->username,
                'updated_on' => Carbon::now(),
            ]);

            DB::commit();
            $this->dispatch('closeModalCreate');
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Master Box saved successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to save master Box: ' . $e->getMessage());
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Failed to save the Box: ' . $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $data = MsPackagingBox::where('id', $id)->first();
        $this->idUpdate = $id;
        $this->code = $data->code;
        $this->name = $data->name;
        $this->box_class = $data->box_class;
        $this->panjang = number_format($data->panjang);
        $this->lebar = number_format($data->lebar);
        $this->tinggi = number_format($data->tinggi);
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
            $data = MsPackagingBox::where('id', $this->idUpdate)->first();
            $data->code = $this->code;
            $data->name = $this->name;
            $data->box_class = is_array($this->box_class) ? $this->box_class['value'] : $this->box_class;
            $data->panjang = (int)str_replace(',', '', $this->panjang);
            $data->lebar = (int)str_replace(',', '', $this->lebar);
            $data->tinggi = (int)str_replace(',', '', $this->tinggi);
            $data->status = $this->status;
            $data->updated_by = Auth::user()->username;
            $data->updated_on = Carbon::now();
            $data->save();

            DB::commit();
            $this->dispatch('closeModalUpdate');
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Master Box updated successfully.']);
        } catch (\Exception $e) {
            $this->skipRender();
            DB::rollBack();
            Log::error('Failed to update master Box: ' . $e->getMessage());
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Failed to update the Box: ' . $e->getMessage()]);
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
            $data = MsPackagingBox::where('id', $this->idDelete)->first();
            $data->status = $statusInactive;
            $data->updated_by = Auth::user()->username;
            $data->updated_on = Carbon::now();
            $data->save();

            DB::commit();
            $this->dispatch('closeModalDelete');
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Master Box deleted successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete master Box: ' . $e->getMessage());
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Failed to delete the Box: ' . $e->getMessage()]);
        }
    }

    public function export()
    {
        $this->startHeavyJob();
        $response = $this->exportBox();
        if ($response['status'] == 'success') {
            return $response['spreadsheet'];
        } else if ($response['status'] == 'error') {
            $this->dispatch('notification', ['type' => 'warning', 'message' => $response['message']]);
            return;
        }
    }

    private function exportBox()
    {
        ini_set('max_execution_time', '300');
        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();
        $activeWorksheet->setShowGridlines(false);
        Carbon::setLocale('id');

        $activeWorksheet->setCellValue('A1', 'MASTER KEMASAN BOX - ' . Carbon::now()->translatedFormat('M Y'));
        phpspreadsheet::styleFont($spreadsheet, 'A1', true, 11, 'Calibri');

        $header = ['No', 'Kode', 'Nama', 'Kelas Box', 'Panjang', 'Lebar', 'Tinggi', 'Status', 'Updated By', 'Updated On'];
        $columnHeaderEnd = 'A';
        foreach ($header as $value) {
            $activeWorksheet->setCellValue($columnHeaderEnd . '2', $value);
            phpspreadsheet::styleFont($spreadsheet, $columnHeaderEnd . '2', true, 11, 'Calibri');
            phpspreadsheet::addFullBorder($spreadsheet, $columnHeaderEnd . '2');
            phpspreadsheet::textAlignCenter($spreadsheet, $columnHeaderEnd . '2');
            $columnHeaderEnd++;
        }
        $columnHeaderEnd = chr(ord($columnHeaderEnd) - 1);

        $data = MsPackagingBox::orderBy('code', 'ASC')->get();

        if (count($data) == 0) {
            return ['status' => 'error', 'message' => 'Data tidak ditemukan'];
        }

        $row = 3;
        $no = 1;
        foreach ($data as $item) {
            $activeWorksheet->setCellValue('A' . $row, $no++);
            $activeWorksheet->setCellValue('B' . $row, $item->code);
            $activeWorksheet->setCellValue('C' . $row, $item->name);
            $activeWorksheet->setCellValue('D' . $row, $item->box_class);
            $activeWorksheet->setCellValue('E' . $row, $item->panjang);
            $activeWorksheet->setCellValue('F' . $row, $item->lebar);
            $activeWorksheet->setCellValue('G' . $row, $item->tinggi);
            $activeWorksheet->setCellValue('H' . $row, $item->status == 1 ? 'Active' : 'Inactive');
            $activeWorksheet->setCellValue('I' . $row, $item->updated_by);
            $activeWorksheet->setCellValue('J' . $row, $item->updated_on);
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
        $response->headers->set('Content-Disposition', 'attachment; filename="Master-Kemasan-Box.xlsx"');
        return ['status' => 'success', 'spreadsheet' => $response];
    }

    public function render()
    {
        $result = MsPackagingBox::get();

        return view('livewire.master-tabel.kemasan.box', [
            'result' => $result
        ])->extends('layouts.master');
    }

    public function rendered()
    {
        $this->dispatch('initDataTable');
    }
}
