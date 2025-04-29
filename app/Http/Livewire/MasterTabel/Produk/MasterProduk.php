<?php

namespace App\Http\Livewire\MasterTabel\Produk;

use App\Helpers\phpspreadsheet;
use App\Models\MsProduct;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class MasterProduk extends Component
{
    use WithPagination, WithoutUrlPagination;
    protected $paginationTheme = 'bootstrap';
    protected $listeners = ['delete', 'edit'];
    public $products;
    public $searchTerm;
    public $product_type_id;
    public $idUpdate;
    public $idDelete;
    public $paginate = 10;

    public function search()
    {
        $this->resetPage();
        // $this->render();
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
            MsProduct::where('id', $this->idDelete)->update([
                'status' => $statusInactive,
                'updated_by' => Auth::user()->username,
                'updated_on' => Carbon::now()
            ]);
            DB::commit();
            $this->dispatch('closeModalDelete');
            $this->dispatch('notification', ['type' => 'success', 'message' => 'Master Buyer deleted successfully.']);
            $this->search();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete master buyer: ' . $e->getMessage());
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Failed to delete the buyer: ' . $e->getMessage()]);
        }
    }


    public function export()
    {
        $response = $this->exportProduct();
        if ($response['status'] == 'success') {
            return response()->download($response['filename']);
        } else if ($response['status'] == 'error') {
            $this->dispatch('notification', ['type' => 'warning', 'message' => $response['message']]);
            return;
        }
    }

    public function exportProduct()
    {
        ini_set('max_execution_time', '300');
        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();
        $activeWorksheet->setShowGridlines(false);

        // Set locale agar tanggal indonesia
        Carbon::setLocale('id');

        // Judul
        $activeWorksheet->setCellValue('A1', 'MASTER PRODUK - ' . Carbon::now()->translatedFormat('M Y'));
        // Style Judul
        phpspreadsheet::styleFont($spreadsheet, 'A1', true, 11, 'Calibri');

        // header
        $rowHeaderStart = 2;
        $columnHeaderStart = 'A';
        $columnHeaderEnd = 'A';

        $header = [
            'No',
            'Nomor Order',
            'Kode Produk',
            'Kode Tipe Produk',
            'Nama Tipe Produk',
            'Jenis Produk',
            'Nama Produk',
            'Satuan',
            'Berat Satuan',
            'Tebal Produk',
            'Lebar Produk',
            'Panjang Produk',
            'Jumlah Warna Depan',
            'Jumlah Warna Belakang',
            'Dimensi Infure',
            'Panjang Gulung',
        ];

        foreach ($header as $key => $value) {
            $activeWorksheet->setCellValue($columnHeaderEnd . $rowHeaderStart, $value);
            $columnHeaderEnd++;
        }

        /**
         * Mengatur halaman
         */
        $activeWorksheet->freezePane('A3');
        // Mengatur ukuran kertas menjadi A4
        $activeWorksheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
        // Mengatur orientasi menjadi landscape
        $activeWorksheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $activeWorksheet->getStyle($columnHeaderStart . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart)->getAlignment()->setWrapText(true);
        // Mengatur agar semua kolom muat dalam satu halaman
        $activeWorksheet->getPageSetup()->setFitToWidth(1);
        $activeWorksheet->getPageSetup()->setFitToHeight(0); // Biarkan tinggi menyesuaikan otomatis

        // Jika ingin memastikan rasio tetap terjaga
        $activeWorksheet->getPageSetup()->setFitToPage(true);
        // Mengatur margin halaman menjadi 0.75 cm di semua sisi
        $activeWorksheet->getPageMargins()->setTop(0.75 / 2.54);
        $activeWorksheet->getPageMargins()->setBottom(0.75 / 2.54);
        $activeWorksheet->getPageMargins()->setLeft(0.75 / 2.54);
        $activeWorksheet->getPageMargins()->setRight(0.75 / 2.54);
        $activeWorksheet->getPageMargins()->setHeader(0.75 / 2.54);
        $activeWorksheet->getPageMargins()->setFooter(0.75 / 2.54);

        $columnHeaderEnd = chr(ord($columnHeaderEnd) - 1);

        // style header
        phpspreadsheet::addFullBorder($spreadsheet, $columnHeaderStart . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);
        phpspreadsheet::styleFont($spreadsheet, $columnHeaderStart . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart, true, 9, 'Calibri');
        phpspreadsheet::textAlignCenter($spreadsheet, $columnHeaderStart . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);

        $data = DB::table('msproduct as msp')
            ->leftJoin('msproduct_type as mspt', 'msp.product_type_id', '=', 'mspt.id')
            ->leftJoin('msproduct_group as mspg', 'mspt.product_group_id', '=', 'mspg.id')
            ->leftJoin('msunit as msu', 'msp.product_unit_id', '=', 'msu.id')
            ->select(
                'msp.id',
                'msp.code as product_code',
                'msp.code_alias',
                'msp.name as product_name',
                'msp.product_type_code',
                'mspt.name as product_type_name',
                'mspg.name as product_group_name',
                'msu.name as product_unit_name',
                'msp.ketebalan',
                'msp.diameterlipat',
                'msp.productlength',
                'msp.unit_weight',
                'msp.number_of_color',
                'msp.back_color_number',
                'msp.one_winding_m_number',
                'msp.status',
                'msp.updated_by',
                'msp.updated_on'
            )
            ->when(isset($this->product_type_id) && $this->product_type_id != "" && $this->product_type_id != "undefined" && $this->product_type_id['value'] != "", function ($query) {
                $query->where('msp.product_type_id', $this->product_type_id);
            })
            ->orderBy('msp.id', 'ASC')
            ->get();

        if (count($data) == 0) {
            $response = [
                'status' => 'error',
                'message' => "Data pada periode tanggal tersebut tidak ditemukan"
            ];

            return $response;
        }

        // After processing, we have a $dataMap (HashMap) ready for use.

        // Now, let's write the data into Excel
        $rowItemStart = 3;
        $columnItemStart = 'A';
        $rowItem = $rowItemStart;
        foreach ($data as $key => $dataItem) {
            $columnItemEnd = $columnItemStart;

            // No
            $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $key + 1);
            $columnItemEnd++;

            // Nomor Order
            $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $dataItem->product_code);
            $columnItemEnd++;

            // Kode Produk
            $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $dataItem->code_alias);
            $columnItemEnd++;

            // Kode Tipe Produk
            $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $dataItem->product_type_code);
            $columnItemEnd++;

            // Nama Tipe Produk
            $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $dataItem->product_type_name);
            $columnItemEnd++;

            // Jenis Produk
            $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $dataItem->product_group_name);
            $columnItemEnd++;

            // Nama Produk
            $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $dataItem->product_name);
            $columnItemEnd++;

            // Satuan
            $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $dataItem->product_unit_name);
            $columnItemEnd++;

            // Berat Satuan
            $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $dataItem->unit_weight);
            // phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItemEnd . $rowItem . ':' . $columnItemEnd . $rowItem);
            $columnItemEnd++;

            // Tebal Produk
            $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $dataItem->ketebalan);
            // phpspreadsheet::numberFormatCommaThousandsOrZero($spreadsheet, $columnItemEnd . $rowItem . ':' . $columnItemEnd . $rowItem);
            $columnItemEnd++;

            // Lebar Produk
            $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $dataItem->diameterlipat);
            phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItemEnd . $rowItem . ':' . $columnItemEnd . $rowItem);
            $columnItemEnd++;

            // Panjang Produk
            $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $dataItem->productlength);
            phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItemEnd . $rowItem . ':' . $columnItemEnd . $rowItem);
            $columnItemEnd++;

            // Jumlah Warna Depan
            $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $dataItem->number_of_color);
            phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItemEnd . $rowItem . ':' . $columnItemEnd . $rowItem);
            $columnItemEnd++;

            // Jumlah Warna Belakang
            $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $dataItem->back_color_number);
            phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItemEnd . $rowItem . ':' . $columnItemEnd . $rowItem);
            $columnItemEnd++;

            // Dimensi Infure
            $activeWorksheet->setCellValue($columnItemEnd . $rowItem, $dataItem->ketebalan . ' x ' . $dataItem->diameterlipat . ' mm');
            phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItemEnd . $rowItem . ':' . $columnItemEnd . $rowItem);
            $columnItemEnd++;

            // Panjang Gulung
            $activeWorksheet->setCellValue($columnItemEnd . $rowItem, number_format($dataItem->one_winding_m_number, 0, '.', '.') . ' m');
            phpspreadsheet::addBorderDottedHorizontal($spreadsheet, $columnItemStart . $rowItem . ':' . $columnItemEnd . $rowItem);
            $columnItemEnd++;

            phpspreadsheet::styleFont($spreadsheet, $columnItemStart . $rowItem . ':' . $columnItemEnd . $rowItem, false, 8, 'Calibri');
            $rowItem++;
        }


        // footer keterangan tanggal, jam, dan nama petugas
        $rowFooterStart = $rowItem + 2;
        $activeWorksheet->setCellValue('A' . $rowFooterStart, 'Dicetak pada: ' . Carbon::now()->translatedFormat('d-M-Y H:i:s') . ', oleh: ' . auth()->user()->empname);
        phpspreadsheet::styleFont($spreadsheet, 'A' . $rowFooterStart . ':A' . ($rowFooterStart + 1), false, 9, 'Calibri');


        // mengatur lebar kolom
        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(2.00);
        $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(6.00);
        $activeWorksheet->getStyle('B' . $rowItemStart . ':' . 'B' . $rowItemStart)->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(6.00);
        $activeWorksheet->getStyle('C' . $rowItemStart . ':' . 'C' . $rowItemStart)->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(6.00);
        $activeWorksheet->getStyle('D' . $rowItemStart . ':' . 'D' . $rowItemStart)->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
        phpspreadsheet::textAlignCenter($spreadsheet, 'F' . $rowItemStart . ':' . 'F' . $rowFooterStart);
        $spreadsheet->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
        phpspreadsheet::textAlignCenter($spreadsheet, 'H' . $rowItemStart . ':' . 'H' . $rowFooterStart);
        $spreadsheet->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('O')->setAutoSize(true);
        phpspreadsheet::textAlignRight($spreadsheet, 'O' . $rowItemStart . ':' . 'O' . $rowFooterStart);
        $spreadsheet->getActiveSheet()->getColumnDimension('P')->setAutoSize(true);
        phpspreadsheet::textAlignRight($spreadsheet, 'P' . $rowItemStart . ':' . 'P' . $rowFooterStart);

        $writer = new Xlsx($spreadsheet);
        $filename = 'Master-Produk.xlsx';
        $writer->save($filename);
        $response = [
            'status' => 'success',
            'filename' => $filename
        ];
        return $response;
    }

    public function render()
    {
        $data = DB::table('msproduct as msp')
            ->leftJoin('msproduct_type as mspt', 'msp.product_type_id', '=', 'mspt.id')
            ->leftJoin('mskatanuki as msk', 'msp.katanuki_id', '=', 'msk.id')
            ->select(
                'msp.id',
                'msp.code as product_code',
                'msp.name as product_name',
                'msp.product_type_code',
                'mspt.name as product_type_name',
                DB::raw('msp.ketebalan || \'x\' || msp.diameterlipat || \'x\' || msp.productlength as dimensi'),
                'msp.unit_weight',
                'msk.code as katanuki_code',
                'msp.number_of_color',
                'msp.back_color_number',
                'msp.status',
                'msp.updated_by',
                'msp.updated_on'
            )
            ->when(isset($this->product_type_id) && $this->product_type_id != "" && $this->product_type_id != "undefined" && $this->product_type_id['value'] != "", function ($query) {
                $query->where('msp.product_type_id', $this->product_type_id);
            })
            ->orderBy('msp.updated_on', 'DESC')
            ->get();

        return view('livewire.master-tabel.produk.master-produk', [
            'data' => $data
        ])->extends('layouts.master');
    }

    public function rendered()
    {
        $this->dispatch('initDataTable');
    }
}
