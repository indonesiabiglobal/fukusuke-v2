<?php

namespace App\Http\Livewire\MasterTabel;

use App\Helpers\phpspreadsheet;
use Carbon\Carbon;
use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\DB;
use Livewire\WithoutUrlPagination;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Session;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Traits\HandlesHeavyJob;

class KatanukiController extends Component
{
    use WithFileUploads;
    use WithPagination, WithoutUrlPagination;
    use HandlesHeavyJob;
    protected $paginationTheme = 'bootstrap';
    protected $listeners = ['delete', 'edit'];
    public $katanukis;
    public $searchTerm;
    public $code;
    public $name;
    public $filename;
    public $photo;
    public $idUpdate;
    public $idDelete;
    public $status;
    public $statusIsVisible = false;

    #[Session]
    public $sortingTable;

    #[Validate('image|max:10240')] // 10MB Max


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
        $this->removePhoto();
    }

    public function showModalCreate()
    {
        $this->resetFields();
        $this->dispatch('showModalCreate');
        // Mencegah render ulang
        $this->skipRender();
    }

    public function removePhoto()
    {
        $this->photo = null;
    }

    public function store()
    {
        $this->validate([
            'code' => 'required|max:10|unique:mskatanuki,code',
            'name' => 'required',
        ]);

        try {
            $this->validate([
                'photo' => 'required|image|max:10240',
            ]);
        } catch (\Exception $e) {
            $this->dispatch('notification', ['type' => 'error', 'message' => 'The photo field is required and must be an image file not exceeding 10MB.']);
            return;
        }

        DB::beginTransaction();
        try {
            $statusActive = 1;
            // menyimpan file image ke storage
            if (isset($this->photo)) {
                $filename = Str::random(20) . '.' . $this->photo->getClientOriginalExtension();

                // Menyimpan file dengan nama custom
                $this->filename = $this->photo->storeAs('katanuki', $filename, 'public');
            }

            // menyimpan data ke database
            DB::table('mskatanuki')->insert([
                'code' => $this->code,
                'name' => strtoupper($this->name),
                'filename' => $this->filename ?? null,
                'status' => $statusActive,
                'created_by' => auth()->user()->username,
                'created_on' => now(),
                'updated_by' => auth()->user()->username,
                'updated_on' => now(),
            ]);
            DB::commit();
            $this->resetFields();
            $this->dispatch('closeModalCreate');
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Katanuki created successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to save master Katanuki: ' . $e->getMessage());
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Failed to save the Katanuki: ' . $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $katanuki = DB::table('mskatanuki')
            ->where('id', $id)
            ->first(['code', 'name', 'filename', 'status']);
        $this->code = $katanuki->code;
        $this->name = $katanuki->name;
        $this->filename = $katanuki->filename;
        $this->idUpdate = $id;
        $this->status = $katanuki->status;
        $this->statusIsVisible = $katanuki->status == 0 ? true : false;
        // $this->skipRender();
        $this->dispatch('showModalUpdate');
    }

    public function update()
    {
        $this->validate([
            'code' => 'required|max:10|unique:mskatanuki,code,' . $this->idUpdate,
            'name' => 'required',
            'photo' => 'max:10240',
        ]);

        DB::beginTransaction();
        try {
            // cek apakah file image diubah atau tidak
            $this->filename = $this->photo ? 'katanuki/' . $this->photo->getClientOriginalName() : $this->filename;
            $mskatanuki = DB::table('mskatanuki')->where('id', $this->idUpdate)->first();
            if ($this->filename && $mskatanuki->filename != $this->filename) {
                // hapus file image lama
                $pathFile = $mskatanuki->filename;
                if ($pathFile) {
                    $pathFile = 'public/' . $pathFile;
                    if (file_exists(storage_path('app/' . $pathFile))) {
                        unlink(storage_path('app/' . $pathFile));
                    }
                }
            }

            $statusActive = 1;
            if ($this->photo) {
                // menyimpan file image ke storage
                $filename = Str::random(20) . '.' . $this->photo->getClientOriginalExtension();
                $this->filename = $this->photo->storeAs('katanuki', $filename, 'public');
            }

            // menyimpan data ke database
            DB::table('mskatanuki')
                ->where('id', $this->idUpdate)
                ->update([
                    'code' => $this->code,
                    'name' => strtoupper($this->name),
                    'filename' => $this->filename,
                    'status' => $this->status,
                    'updated_by' => auth()->user()->username,
                    'updated_on' => now(),
                ]);
            DB::commit();

            $this->resetFields();
            $this->dispatch('closeModalUpdate');
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Katanuki updated successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update master Katanuki: ' . $e->getMessage());
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Failed to update the Katanuki: ' . $e->getMessage()]);
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
            $katanuki = DB::table('mskatanuki')->where('id', $this->idDelete)->first();
            // $pathFile = $katanuki->filename;
            // if ($pathFile) {
            //     $pathFile = 'public/' . $pathFile;
            //     if (file_exists(storage_path('app/' . $pathFile))) {
            //         unlink(storage_path('app/' . $pathFile));
            //     }
            // }
            DB::table('mskatanuki')->where('id', $this->idDelete)->update([
                'status' => $statusInactive,
                'updated_by' => auth()->user()->username,
                'updated_on' => now(),
            ]);
            DB::commit();
            $this->dispatch('closeModalDelete');
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Katanuki deleted successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete master Katanuki: ' . $e->getMessage());
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Failed to delete the Katanuki: ' . $e->getMessage()]);
        }
    }

    public function export()
    {
        $response = $this->exportKatanuki();
        if ($response['status'] == 'success') {
            return $response['spreadsheet'];
        } else if ($response['status'] == 'error') {
            $this->dispatch('notification', ['type' => 'warning', 'message' => $response['message']]);
            return;
        }
    }

    public function exportKatanuki()
    {
        ini_set('max_execution_time', '300');
        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();
        $activeWorksheet->setShowGridlines(false);
        Carbon::setLocale('id');

        $activeWorksheet->setCellValue('A1', 'MASTER KATANUKI - ' . Carbon::now()->translatedFormat('M Y'));
        phpspreadsheet::styleFont($spreadsheet, 'A1', true, 11, 'Calibri');

        $rowHeaderStart = 2;
        $columnHeaderStart = 'A';
        $columnHeaderEnd = 'A';

        $header = ['No', 'Kode', 'Nama', 'Status', 'Updated By', 'Updated On'];

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

        $data = DB::table('mskatanuki')
            ->select('id', 'code', 'name', 'status', 'updated_by', 'updated_on')
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

        $filename = 'Master-Katanuki.xlsx';
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
        $data = DB::table('mskatanuki')
            ->get();

        return view('livewire.master-tabel.katanuki', [
            'data' => $data
        ])->extends('layouts.master');
    }

    public function rendered()
    {
        $this->dispatch('initDataTable');
    }
}
