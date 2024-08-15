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
            session()->flash('error', 'Tanggal akhir tidak boleh kurang dari tanggal awal');
            return;
        }

        $tglAwal = Carbon::parse($this->tglAwal . ' ' . $this->jamAwal);
        $tglAkhir = Carbon::parse($this->tglAkhir . ' ' . $this->jamAkhir);

        switch ($this->jenisReport) {
            case 'Daftar Order':
                $file = $this->daftarOrder($tglAwal, $tglAkhir);
                return response()->download($file);
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
        $header = [
            'No',
            'Order Data',
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

        $data = collect(DB::select("
            SELECT
                tod.id,
                tod.order_date,
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
            "));
        // $tglMasuk
        // $tglKeluar
        // $buyer_id

        $activeWorksheet->getStyle($columnHeaderStart . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart)->getAlignment()->setWrapText(true);

        $writer = new Xlsx($spreadsheet);
        $filename = 'Order-List.xlsx';
        $writer->save($filename);
        return $filename;
    }

    public function render()
    {
        return view('livewire.order-lpk.order-report')->extends('layouts.master');
    }
}
