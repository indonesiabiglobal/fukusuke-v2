<?php

namespace App\Http\Livewire;

use App\Exports\OrderReportExport;
use Livewire\Component;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Exports\ProductsExport;
use App\Helpers\phpspreadsheet;
use App\Models\MsBuyer;
use App\Models\MsWorkingShift;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class OrderReportController extends Component
{
    public $tglAwal;
    public $tglAkhir;
    public $jamAwal;
    public $jamAkhir;
    public $buyer;
    public $workingShiftHour;
    public $buyer_id;
    public $filter;
    public $jenisReport;

    protected $rules = [
        'tglAwal' => 'required',
        'tglAkhir' => 'required',
        'jamAwal' => 'required',
        'jamAkhir' => 'required',
        'filter' => 'required',
        'jenisReport' => 'required',
    ];

    public function mount()
    {
        $this->tglAwal = Carbon::now()->format('Y-m-d');
        $this->tglAkhir = Carbon::now()->format('Y-m-d');
        $this->buyer = MsBuyer::get();
        $this->workingShiftHour = MsWorkingShift::select('work_hour_from', 'work_hour_till')->where('status', 1)->orderBy('work_hour_from', 'ASC')->get();
        $this->jamAwal = $this->workingShiftHour[0]->work_hour_from;
        $this->jamAkhir = $this->workingShiftHour[count($this->workingShiftHour) - 1]->work_hour_till;
        $this->filter = 'Tanggal Order';
        $this->jenisReport = 'Daftar Order';
    }

    public function print()
    {
        return Excel::download(new OrderReportExport(
            $this->tglAwal,
            $this->tglAkhir,
            $this->buyer_id,
            $this->filter,
        ), 'order_report.xlsx');
    }

    public function export()
    {
        $rules = [
            'tglAwal' => 'required',
            'tglAkhir' => 'required',
            'jamAwal' => 'required',
            'jamAkhir' => 'required',
            'filter' => 'required',
            'jenisReport' => 'required',
        ];

        $messages = [
            'tglAwal.required' => 'Tanggal Awal tidak boleh kosong',
            'tglAkhir.required' => 'Tanggal Akhir tidak boleh kosong',
            'jamAwal.required' => 'Jam Awal tidak boleh kosong',
            'jamAkhir.required' => 'Jam Akhir tidak boleh kosong',
            'filter.required' => 'Filter tidak boleh kosong',
            'jenisReport.required' => 'Jenis Report tidak boleh kosong',
        ];

        $validate = Validator::make([
            'tglAwal' => $this->tglAwal,
            'tglAkhir' => $this->tglAkhir,
            'jamAwal' => $this->jamAwal,
            'jamAkhir' => $this->jamAkhir,
            'filter' => $this->filter,
            'jenisReport' => $this->jenisReport,
        ], $rules, $messages);

        if ($validate->fails()) {
            $this->dispatch('notification', ['type' => 'warning', 'message' => $validate->errors()->first()]);
            return;
        }

        if ($this->tglAwal > $this->tglAkhir) {
            $this->dispatch('notification', ['type' => 'warning', 'message' => 'Tanggal akhir tidak boleh kurang dari tanggal awal']);
            return;
        }

        $tglAwal = Carbon::parse($this->tglAwal . ' ' . $this->jamAwal);
        $tglAkhir = Carbon::parse($this->tglAkhir . ' ' . $this->jamAkhir);

        switch ($this->jenisReport) {
            case 'Daftar Order':
                $file = $this->daftarOrder($tglAwal, $tglAkhir);
                return response()->download($file);
                break;
            case 'Daftar Order Per Buyer Per Tipe':
                $response = $this->daftarPerBuyerPerType($tglAwal, $tglAkhir);
                if ($response['status'] == 'success') {
                    return response()->download($response['filename']);
                } else if ($response['status'] == 'error') {
                    $this->dispatch('notification', ['type' => 'warning', 'message' => $response['message']]);
                    return;
                }
                break;
            case 'CheckList Order':
                $response = $this->checkListOrder($tglAwal, $tglAkhir);
                if ($response['status'] == 'success') {
                    return response()->download($response['filename']);
                } else if ($response['status'] == 'error') {
                    $this->dispatch('notification', ['type' => 'warning', 'message' => $response['message']]);
                    return;
                }
                break;
        }
    }

    public function daftarOrder($tglAwal, $tglAkhir)
    {
        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();
        $activeWorksheet->setShowGridlines(false);

        // Set locale agar tanggal indonesia
        Carbon::setLocale('id');

        // Judul
        $activeWorksheet->setCellValue('A1', 'ORDER LIST');
        $activeWorksheet->setCellValue('A2', 'Periode Order: ' . $tglAwal->translatedFormat('d-M-Y') . ' s/d ' . $tglAkhir->translatedFormat('d-M-Y') . ' - Buyer: ' . ($this->buyer_id != null ? MsBuyer::find($this->buyer_id)->name : 'all'));
        // Style Judul
        phpspreadsheet::styleFont($spreadsheet, 'A1:A2', true, 11, 'Calibri');

        // header
        $rowHeaderStart = 3;
        $columnHeaderStart = 'A';
        $columnHeaderEnd = 'A';

        // filter tanggal
        $filterDate = '';
        if ($this->filter == 'Tanggal Order') {
            $fieldDate = 'tod.order_date';
            $filterDate = 'tod.order_date BETWEEN :tglAwal AND :tglAkhir';
            $headerDate = 'Order Date';
        } else if ($this->filter == 'Tanggal Proses') {
            $fieldDate = 'tod.processdate';
            $filterDate = 'tod.processdate BETWEEN :tglAwal AND :tglAkhir';
            $headerDate = 'Process Date';
        }

        $header = [
            'No',
            $headerDate,
            'PO Number',
            'Order No',
            'Product Name',
            'Type Code',
            'Order Quantity',
            'Unit',
            'Stufing Date',
            'ETD Date',
            'ETA Date',
        ];

        foreach ($header as $key => $value) {
            $activeWorksheet->setCellValue($columnHeaderEnd . $rowHeaderStart, $value);
            $columnHeaderEnd++;
        }
        $columnHeaderEnd = chr(ord($columnHeaderEnd) - 1);

        // style header
        phpspreadsheet::addFullBorder($spreadsheet, $columnHeaderStart . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);
        phpspreadsheet::styleFont($spreadsheet, $columnHeaderStart . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart, true, 9, 'Calibri');
        phpspreadsheet::textAlignCenter($spreadsheet, $columnHeaderStart . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);

        // filter buyer
        $filterBuyer = '';
        if ($this->buyer_id != null) {
            $filterBuyer = 'AND tod.buyer_id = ' . $this->buyer_id;
        }

        $data = collect(DB::select(
            "
                SELECT
                    tod.id,
                    $fieldDate AS field_date,
                    tod.po_no,
                    mp.code,
                    mp.name AS produk_name,
                    tod.product_code,
                    tod.order_qty,
                    tod.order_unit,
                    tod.stufingdate,
                    tod.etddate,
                    tod.etadate,
                    mbu.NAME AS buyer_name
                FROM
                    tdorder AS tod
                INNER JOIN msproduct AS mp ON mp.id = tod.product_id
                INNER JOIN msbuyer AS mbu ON mbu.id = tod.buyer_id
                WHERE
                    $filterDate
                    $filterBuyer
                ",
            [
                'tglAwal' => $tglAwal->format('Y-m-d'),
                'tglAkhir' => $tglAkhir->format('Y-m-d'),
            ]
        ));

        if (count($data) == 0) {
            $response = [
                'status' => 'error',
                'message' => "Data pada periode tanggal atau pembeli tersebut tidak ditemukan"
            ];

            return $response;
        }

        $rowItemStart = $rowHeaderStart + 1;
        $rowItemEnd = $rowItemStart;
        $columnItemStart = 'A';
        $columnItemEnd = $columnItemStart;
        $iteration = 1;
        foreach ($data as $item) {
            $columnItemEnd = $columnItemStart;
            // mo
            $activeWorksheet->setCellValue($columnItemEnd . $rowItemEnd, $iteration);
            phpspreadsheet::textAlignCenter($spreadsheet, $columnItemEnd . $rowItemEnd);
            $iteration++;
            $columnItemEnd++;
            // field date
            $activeWorksheet->setCellValue($columnItemEnd . $rowItemEnd, Carbon::parse($item->field_date)->translatedFormat('d-M-Y'));
            phpspreadsheet::textAlignCenter($spreadsheet, $columnItemEnd . $rowItemEnd);
            $columnItemEnd++;
            // po no
            $activeWorksheet->setCellValue($columnItemEnd . $rowItemEnd, $item->po_no);
            $columnItemEnd++;
            // order no
            $activeWorksheet->setCellValue($columnItemEnd . $rowItemEnd, $item->id);
            $columnItemEnd++;
            // product name
            $activeWorksheet->setCellValue($columnItemEnd . $rowItemEnd, $item->produk_name);
            $columnItemEnd++;
            // type code
            $activeWorksheet->setCellValue($columnItemEnd . $rowItemEnd, $item->product_code);
            phpspreadsheet::textAlignCenter($spreadsheet, $columnItemEnd . $rowItemEnd);
            $columnItemEnd++;
            // order qty
            $activeWorksheet->setCellValue($columnItemEnd . $rowItemEnd, $item->order_qty);
            phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItemEnd . $rowItemEnd);
            $columnItemEnd++;
            // order unit
            $activeWorksheet->setCellValue($columnItemEnd . $rowItemEnd, $item->order_unit);
            phpspreadsheet::textAlignCenter($spreadsheet, $columnItemEnd . $rowItemEnd);
            $columnItemEnd++;
            // stufing date
            $activeWorksheet->setCellValue($columnItemEnd . $rowItemEnd, Carbon::parse($item->stufingdate)->translatedFormat('d-M-Y'));
            phpspreadsheet::textAlignCenter($spreadsheet, $columnItemEnd . $rowItemEnd);
            $columnItemEnd++;
            // etd date
            $activeWorksheet->setCellValue($columnItemEnd . $rowItemEnd, Carbon::parse($item->etddate)->translatedFormat('d-M-Y'));
            phpspreadsheet::textAlignCenter($spreadsheet, $columnItemEnd . $rowItemEnd);
            $columnItemEnd++;
            // eta date
            $activeWorksheet->setCellValue($columnItemEnd . $rowItemEnd, Carbon::parse($item->etadate)->translatedFormat('d-M-Y'));
            phpspreadsheet::textAlignCenter($spreadsheet, $columnItemEnd . $rowItemEnd);
            phpspreadsheet::addFullBorder($spreadsheet, $columnItemStart . $rowItemEnd . ':' . $columnItemEnd . $rowItemEnd);
            $columnItemEnd++;

            $rowItemEnd++;
        }

        $activeWorksheet->getStyle($columnHeaderStart . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart)->getAlignment()->setWrapText(true);

        // size auto
        $columnSizeStart = $columnItemStart;
        $columnSizeStart++;
        while ($columnSizeStart !== $columnItemEnd) {
            $spreadsheet->getActiveSheet()->getColumnDimension($columnSizeStart)->setAutoSize(true);
            $columnSizeStart++;
        }

        $writer = new Xlsx($spreadsheet);
        $filename = $this->jenisReport . '.xlsx';
        $writer->save($filename);
        $response = [
            'status' => 'success',
            'filename' => $filename
        ];
        return $response;
    }

    public function daftarPerBuyerPerType($tglAwal, $tglAkhir)
    {
        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();
        $activeWorksheet->setShowGridlines(false);

        // Set locale agar tanggal indonesia
        Carbon::setLocale('id');

        // Judul
        $activeWorksheet->setCellValue('A1', 'ORDER LIST PER TYPE');
        $activeWorksheet->setCellValue('A2', 'Periode Order: ' . $tglAwal->translatedFormat('d-M-Y') . ' s/d ' . $tglAkhir->translatedFormat('d-M-Y') . ' - Buyer: ' . ($this->buyer_id != null ? MsBuyer::find($this->buyer_id)->name : 'all'));
        // Style Judul
        phpspreadsheet::styleFont($spreadsheet, 'A1:A2', true, 11, 'Calibri');

        // header
        $rowHeaderStart = 3;
        $columnHeaderStart = 'A';
        $columnHeaderEnd = 'A';

        // filter tanggal
        $filterDate = '';
        if ($this->filter == 'Tanggal Order') {
            $fieldDate = 'tod.order_date';
            $filterDate = 'tod.order_date BETWEEN :tglAwal AND :tglAkhir';
            $headerDate = 'Order Date';
        } else if ($this->filter == 'Tanggal Proses') {
            $fieldDate = 'tod.processdate';
            $filterDate = 'tod.processdate BETWEEN :tglAwal AND :tglAkhir';
            $headerDate = 'Process Date';
        }

        $header = [
            'No',
            'Buyer',
            'Product Classification',
            'Product Type',
            'Order Quality (pcs)',
            'Order Weight (Kg)',
        ];

        foreach ($header as $key => $value) {
            $activeWorksheet->setCellValue($columnHeaderEnd . $rowHeaderStart, $value);
            $columnHeaderEnd++;
        }
        $columnHeaderEnd = chr(ord($columnHeaderEnd) - 1);

        // style header
        phpspreadsheet::addFullBorder($spreadsheet, $columnHeaderStart . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);
        phpspreadsheet::styleFont($spreadsheet, $columnHeaderStart . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart, true, 9, 'Calibri');
        phpspreadsheet::textAlignCenter($spreadsheet, $columnHeaderStart . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);

        // filter buyer
        $filterBuyer = '';
        if ($this->buyer_id != null) {
            $filterBuyer = 'AND tod.buyer_id = ' . $this->buyer_id;
        }

        // query masih belum bener
        $data = collect(DB::select(
            "
                SELECT
                    tod.id,
                    $fieldDate AS field_date,
                    tod.po_no,
                    mp.code,
                    mp.name AS produk_name,
                    tod.product_code,
                    tod.order_qty,
                    tod.order_unit,
                    tod.stufingdate,
                    tod.etddate,
                    tod.etadate,
                    mbu.NAME AS buyer_name
                FROM
                    tdorder AS tod
                INNER JOIN msproduct AS mp ON mp.id = tod.product_id
                INNER JOIN msbuyer AS mbu ON mbu.id = tod.buyer_id
                WHERE
                    $filterDate
                    $filterBuyer
                ",
            [
                'tglAwal' => $tglAwal->format('Y-m-d'),
                'tglAkhir' => $tglAkhir->format('Y-m-d'),
            ]
        ));

        if (count($data) == 0) {
            $response = [
                'status' => 'error',
                'message' => "Data pada periode tanggal atau pembeli tersebut tidak ditemukan"
            ];

            return $response;
        }

        // $rowItemStart = $rowHeaderStart + 1;
        // $rowItemEnd = $rowItemStart;
        // $columnItemStart = 'A';
        // $columnItemEnd = $columnItemStart;
        // $iteration = 1;
        // foreach ($data as $item) {
        //     $columnItemEnd = $columnItemStart;
        //     // mo
        //     $activeWorksheet->setCellValue($columnItemEnd . $rowItemEnd, $iteration);
        //     phpspreadsheet::textAlignCenter($spreadsheet, $columnItemEnd . $rowItemEnd);
        //     $iteration++;
        //     $columnItemEnd++;
        //     // field date
        //     $activeWorksheet->setCellValue($columnItemEnd . $rowItemEnd, Carbon::parse($item->field_date)->translatedFormat('d-M-Y'));
        //     phpspreadsheet::textAlignCenter($spreadsheet, $columnItemEnd . $rowItemEnd);
        //     $columnItemEnd++;
        //     // po no
        //     $activeWorksheet->setCellValue($columnItemEnd . $rowItemEnd, $item->po_no);
        //     $columnItemEnd++;
        //     // order no
        //     $activeWorksheet->setCellValue($columnItemEnd . $rowItemEnd, $item->id);
        //     $columnItemEnd++;
        //     // product name
        //     $activeWorksheet->setCellValue($columnItemEnd . $rowItemEnd, $item->produk_name);
        //     $columnItemEnd++;
        //     // type code
        //     $activeWorksheet->setCellValue($columnItemEnd . $rowItemEnd, $item->product_code);
        //     phpspreadsheet::textAlignCenter($spreadsheet, $columnItemEnd . $rowItemEnd);
        //     $columnItemEnd++;
        //     // order qty
        //     $activeWorksheet->setCellValue($columnItemEnd . $rowItemEnd, $item->order_qty);
        //     phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItemEnd . $rowItemEnd);
        //     $columnItemEnd++;
        //     // order unit
        //     $activeWorksheet->setCellValue($columnItemEnd . $rowItemEnd, $item->order_unit);
        //     phpspreadsheet::textAlignCenter($spreadsheet, $columnItemEnd . $rowItemEnd);
        //     $columnItemEnd++;
        //     // stufing date
        //     $activeWorksheet->setCellValue($columnItemEnd . $rowItemEnd, Carbon::parse($item->stufingdate)->translatedFormat('d-M-Y'));
        //     phpspreadsheet::textAlignCenter($spreadsheet, $columnItemEnd . $rowItemEnd);
        //     $columnItemEnd++;
        //     // etd date
        //     $activeWorksheet->setCellValue($columnItemEnd . $rowItemEnd, Carbon::parse($item->etddate)->translatedFormat('d-M-Y'));
        //     phpspreadsheet::textAlignCenter($spreadsheet, $columnItemEnd . $rowItemEnd);
        //     $columnItemEnd++;
        //     // eta date
        //     $activeWorksheet->setCellValue($columnItemEnd . $rowItemEnd, Carbon::parse($item->etadate)->translatedFormat('d-M-Y'));
        //     phpspreadsheet::textAlignCenter($spreadsheet, $columnItemEnd . $rowItemEnd);
        //     phpspreadsheet::addFullBorder($spreadsheet, $columnItemStart . $rowItemEnd . ':' . $columnItemEnd . $rowItemEnd);
        //     $columnItemEnd++;

        //     $rowItemEnd++;
        // }

        $activeWorksheet->getStyle($columnHeaderStart . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart)->getAlignment()->setWrapText(true);

        // size auto
        // $columnSizeStart = $columnItemStart;
        // $columnSizeStart++;
        // while ($columnSizeStart !== $columnItemEnd) {
        //     $spreadsheet->getActiveSheet()->getColumnDimension($columnSizeStart)->setAutoSize(true);
        //     $columnSizeStart++;
        // }

        $writer = new Xlsx($spreadsheet);
        $filename = $this->jenisReport . '.xlsx';
        $writer->save($filename);
        $response = [
            'status' => 'success',
            'filename' => $filename
        ];
        return $response;
    }

    public function checklistOrder($tglAwal, $tglAkhir)
    {
        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();
        $activeWorksheet->setShowGridlines(false);

        // Set locale agar tanggal indonesia
        Carbon::setLocale('id');

        // Judul
        $activeWorksheet->setCellValue('A1', 'ORDER LIST');
        $activeWorksheet->setCellValue('A2', 'Periode Order: ' . $tglAwal->translatedFormat('d-M-Y') . ' s/d ' . $tglAkhir->translatedFormat('d-M-Y') . ' - Buyer: ' . ($this->buyer_id != null ? MsBuyer::find($this->buyer_id)->name : 'all'));
        // Style Judul
        phpspreadsheet::styleFont($spreadsheet, 'A1:A2', true, 11, 'Calibri');

        // header
        $rowHeaderStart = 3;
        $columnHeaderStart = 'A';
        $columnHeaderEnd = 'A';

        // filter tanggal
        $filterDate = '';
        if ($this->filter == 'Tanggal Order') {
            $fieldDate = 'tod.order_date';
            $filterDate = 'tod.order_date BETWEEN :tglAwal AND :tglAkhir';
            $headerDate = 'Order Date';
        } else if ($this->filter == 'Tanggal Proses') {
            $fieldDate = 'tod.processdate';
            $filterDate = 'tod.processdate BETWEEN :tglAwal AND :tglAkhir';
            $headerDate = 'Process Date';
        }

        $header = [
            'No',
            'Tanggal Proses',
            'Nomor Proses',
            'Tanggal Order',
            'PO Number',
            'Order No',
            'Nama Produk',
            'Kode Tipe',
            'Jumlah Order',
            'Unit',
            'Tanggal Stuffing',
            'Tanggal ETD',
            'Tanggal ETA',
            'Buyer',
        ];

        foreach ($header as $key => $value) {
            $activeWorksheet->setCellValue($columnHeaderEnd . $rowHeaderStart, $value);
            $columnHeaderEnd++;
        }
        $columnHeaderEnd = chr(ord($columnHeaderEnd) - 1);

        // style header
        phpspreadsheet::addFullBorder($spreadsheet, $columnHeaderStart . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);
        phpspreadsheet::styleFont($spreadsheet, $columnHeaderStart . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart, true, 9, 'Calibri');
        phpspreadsheet::textAlignCenter($spreadsheet, $columnHeaderStart . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);

        // filter buyer
        $filterBuyer = '';
        if ($this->buyer_id != null) {
            $filterBuyer = 'AND tod.buyer_id = ' . $this->buyer_id;
        }

        // query belum benar
        $data = collect(DB::select(
            "
                SELECT
                    tod.id,
                    $fieldDate AS field_date,
                    tod.po_no,
                    mp.code,
                    mp.name AS produk_name,
                    tod.product_code,
                    tod.order_qty,
                    tod.order_unit,
                    tod.stufingdate,
                    tod.etddate,
                    tod.etadate,
                    mbu.NAME AS buyer_name
                FROM
                    tdorder AS tod
                INNER JOIN msproduct AS mp ON mp.id = tod.product_id
                INNER JOIN msbuyer AS mbu ON mbu.id = tod.buyer_id
                WHERE
                    $filterDate
                    $filterBuyer
                ",
            [
                'tglAwal' => $tglAwal->format('Y-m-d'),
                'tglAkhir' => $tglAkhir->format('Y-m-d'),
            ]
        ));

        if (count($data) == 0) {
            $response = [
                'status' => 'error',
                'message' => "Data pada periode tanggal atau pembeli tersebut tidak ditemukan"
            ];

            return $response;
        }

        // $rowItemStart = $rowHeaderStart + 1;
        // $rowItemEnd = $rowItemStart;
        // $columnItemStart = 'A';
        // $columnItemEnd = $columnItemStart;
        // $iteration = 1;
        // foreach ($data as $item) {
        //     $columnItemEnd = $columnItemStart;
        //     // mo
        //     $activeWorksheet->setCellValue($columnItemEnd . $rowItemEnd, $iteration);
        //     phpspreadsheet::textAlignCenter($spreadsheet, $columnItemEnd . $rowItemEnd);
        //     $iteration++;
        //     $columnItemEnd++;
        //     // field date
        //     $activeWorksheet->setCellValue($columnItemEnd . $rowItemEnd, Carbon::parse($item->field_date)->translatedFormat('d-M-Y'));
        //     phpspreadsheet::textAlignCenter($spreadsheet, $columnItemEnd . $rowItemEnd);
        //     $columnItemEnd++;
        //     // po no
        //     $activeWorksheet->setCellValue($columnItemEnd . $rowItemEnd, $item->po_no);
        //     $columnItemEnd++;
        //     // order no
        //     $activeWorksheet->setCellValue($columnItemEnd . $rowItemEnd, $item->id);
        //     $columnItemEnd++;
        //     // product name
        //     $activeWorksheet->setCellValue($columnItemEnd . $rowItemEnd, $item->produk_name);
        //     $columnItemEnd++;
        //     // type code
        //     $activeWorksheet->setCellValue($columnItemEnd . $rowItemEnd, $item->product_code);
        //     phpspreadsheet::textAlignCenter($spreadsheet, $columnItemEnd . $rowItemEnd);
        //     $columnItemEnd++;
        //     // order qty
        //     $activeWorksheet->setCellValue($columnItemEnd . $rowItemEnd, $item->order_qty);
        //     phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItemEnd . $rowItemEnd);
        //     $columnItemEnd++;
        //     // order unit
        //     $activeWorksheet->setCellValue($columnItemEnd . $rowItemEnd, $item->order_unit);
        //     phpspreadsheet::textAlignCenter($spreadsheet, $columnItemEnd . $rowItemEnd);
        //     $columnItemEnd++;
        //     // stufing date
        //     $activeWorksheet->setCellValue($columnItemEnd . $rowItemEnd, Carbon::parse($item->stufingdate)->translatedFormat('d-M-Y'));
        //     phpspreadsheet::textAlignCenter($spreadsheet, $columnItemEnd . $rowItemEnd);
        //     $columnItemEnd++;
        //     // etd date
        //     $activeWorksheet->setCellValue($columnItemEnd . $rowItemEnd, Carbon::parse($item->etddate)->translatedFormat('d-M-Y'));
        //     phpspreadsheet::textAlignCenter($spreadsheet, $columnItemEnd . $rowItemEnd);
        //     $columnItemEnd++;
        //     // eta date
        //     $activeWorksheet->setCellValue($columnItemEnd . $rowItemEnd, Carbon::parse($item->etadate)->translatedFormat('d-M-Y'));
        //     phpspreadsheet::textAlignCenter($spreadsheet, $columnItemEnd . $rowItemEnd);
        //     phpspreadsheet::addFullBorder($spreadsheet, $columnItemStart . $rowItemEnd . ':' . $columnItemEnd . $rowItemEnd);
        //     $columnItemEnd++;

        //     $rowItemEnd++;
        // }

        $activeWorksheet->getStyle($columnHeaderStart . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart)->getAlignment()->setWrapText(true);

        // // size auto
        // $columnSizeStart = $columnItemStart;
        // $columnSizeStart++;
        // while ($columnSizeStart !== $columnItemEnd) {
        //     $spreadsheet->getActiveSheet()->getColumnDimension($columnSizeStart)->setAutoSize(true);
        //     $columnSizeStart++;
        // }

        $writer = new Xlsx($spreadsheet);
        $filename = $this->jenisReport. '.xlsx';
        $writer->save($filename);
        $response = [
            'status' => 'success',
            'filename' => $filename
        ];
        return $response;
    }

    public function render()
    {
        return view('livewire.order-lpk.order-report')->extends('layouts.master');
    }
}
