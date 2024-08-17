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
    public $jenisReport = 'Daftar Order';

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
        $this->filter = 'Order';
        // $this->jenisReport = 'Daftar Order';
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
        if ($this->tglAwal > $this->tglAkhir) {
            $this->dispatch('notification', ['type' => 'warning', 'message' => 'Tanggal akhir tidak boleh kurang dari tanggal awal']);
            return;
        }

        $tglAwal = Carbon::parse($this->tglAwal . ' ' . $this->jamAwal);
        $tglAkhir = Carbon::parse($this->tglAkhir . ' ' . $this->jamAkhir);

        switch ($this->jenisReport) {
            case 'Daftar Order':
                $response = $this->daftarOrder($tglAwal, $tglAkhir);
                if ($response['status'] == 'success') {
                    return response()->download($response['filename']);
                } else if ($response['status'] == 'error') {
                    $this->dispatch('notification', ['type' => 'warning', 'message' => $response['message']]);
                    return;
                }
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
            case 'CheckList LPK':
                $response = $this->checklistLPK($tglAwal, $tglAkhir);
                if ($response['status'] == 'success') {
                    return response()->download($response['filename']);
                } else if ($response['status'] == 'error') {
                    $this->dispatch('notification', ['type' => 'warning', 'message' => $response['message']]);
                    return;
                }
                break;
            case 'Progress Order':
                $response = $this->orderProgress($tglAwal, $tglAkhir);
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
        $activeWorksheet->setCellValue('A2', 'Periode ' . $this->filter . ': ' . $tglAwal->translatedFormat('d-M-Y H:i') . ' s/d ' . $tglAkhir->translatedFormat('d-M-Y H:i') . ' - Buyer: ' . ($this->buyer_id != null ? MsBuyer::find($this->buyer_id)->name : 'all'));
        // Style Judul
        phpspreadsheet::styleFont($spreadsheet, 'A1:A2', true, 11, 'Calibri');

        // header
        $rowHeaderStart = 3;
        $columnHeaderStart = 'A';
        $columnHeaderEnd = 'A';

        // filter tanggal
        $filterDate = '';
        if ($this->filter == 'Order') {
            $fieldDate = 'tod.order_date';
            $filterDate = 'tod.order_date BETWEEN :tglAwal AND :tglAkhir';
            $headerDate = 'Order Date';
        } else if ($this->filter == 'Proses') {
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
        $activeWorksheet->setCellValue('A2', 'Periode ' . $this->filter . ': ' . $tglAwal->translatedFormat('d-M-Y  H:i') . ' s/d ' . $tglAkhir->translatedFormat('d-M-Y H:i') . ' - Buyer: ' . ($this->buyer_id != null ? MsBuyer::find($this->buyer_id)->name : 'all'));
        // Style Judul
        phpspreadsheet::styleFont($spreadsheet, 'A1:A2', true, 11, 'Calibri');

        // header
        $rowHeaderStart = 3;
        $columnHeaderStart = 'A';
        $columnHeaderEnd = 'A';

        // filter tanggal
        $filterDate = '';
        if ($this->filter == 'Order') {
            $fieldDate = 'a.order_date';
            $filterDate = 'a.order_date BETWEEN :tglAwal AND :tglAkhir';
            $headerDate = 'Order Date';
        } else if ($this->filter == 'Proses') {
            $fieldDate = 'a.processdate';
            $filterDate = 'a.processdate BETWEEN :tglAwal AND :tglAkhir';
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
            $filterBuyer = 'AND a.buyer_id = ' . $this->buyer_id;
        }

        // query masih belum bener
        $data = DB::select(
            "
                SELECT
                    MAX(byr.name) AS buyer_name,
                    MAX(prdType.name) AS product_type_name,
                    MAX(msProduct_group.name) AS product_group_name,
                    SUM(a.order_qty) AS order_qty,
                    SUM(a.order_qty * prd.unit_weight * 0.001) AS order_berat
                FROM tdOrder AS a
                INNER JOIN msBuyer AS byr ON a.buyer_id = byr.id
                INNER JOIN msProduct AS prd ON a.product_id = prd.id
                INNER JOIN msProduct_type AS prdType ON prd.product_type_id = prdType.id
                INNER JOIN msProduct_group ON prdType.product_group_id = msProduct_group.id
                WHERE
                $filterDate
                $filterBuyer
                GROUP BY a.buyer_id, prdType.product_group_id, prdType.id;
                ",
            [
                'tglAwal' => $tglAwal->format('Y-m-d'),
                'tglAkhir' => $tglAkhir->format('Y-m-d'),
            ]
        );

        if (count($data) == 0) {
            $response = [
                'status' => 'error',
                'message' => "Data pada periode tanggal atau pembeli tersebut tidak ditemukan"
            ];

            return $response;
        }

        $listBuyer = array_reduce($data, function ($carry, $item) {
            $carry[$item->buyer_name] = $item->buyer_name;
            return $carry;
        }, []);

        $listProductGroup = array_reduce($data, function ($carry, $item) {
            $carry[$item->buyer_name][$item->product_group_name] = $item->product_group_name;
            return $carry;
        }, []);

        $dataFilter = array_reduce($data, function ($carry, $item) {
            $carry[$item->buyer_name][$item->product_group_name][$item->product_type_name] = $item;

            return $carry;
        }, []);

        $rowItemStart = 4;
        $rowItem = $rowItemStart;
        $columnItemStart = 'A';
        $columnItemBuyer = 'B';
        $columnItemProductGroup = 'C';
        $columnItem = 'D';
        $iteration = 1;
        foreach ($listBuyer as $buyer) {
            // no
            $activeWorksheet->setCellValue($columnItemStart . $rowItem, $iteration);
            phpspreadsheet::textAlignCenter($spreadsheet, $columnItemBuyer . $rowItem);
            $iteration++;
            // buyer
            $activeWorksheet->setCellValue($columnItemBuyer . $rowItem, $buyer);
            // phpspreadsheet::textAlignCenter($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            foreach ($listProductGroup[$buyer] as $productGroup) {
                // product classification
                $activeWorksheet->setCellValue($columnItemProductGroup . $rowItem, $productGroup);
                $columnItem++;
                foreach ($dataFilter[$buyer][$productGroup] as $productType) {
                    $columnItem = 'D';
                    // product type
                    $activeWorksheet->setCellValue($columnItem . $rowItem, $productType->product_type_name);
                    $columnItem++;
                    // order qty
                    $activeWorksheet->setCellValue($columnItem . $rowItem, $productType->order_qty);
                    phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
                    $columnItem++;
                    // order weight
                    $activeWorksheet->setCellValue($columnItem . $rowItem, $productType->order_berat);
                    phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
                    phpspreadsheet::addFullBorder($spreadsheet, $columnItemStart . $rowItem . ':' . $columnItem . $rowItem);
                    $columnItem++;

                    $rowItem++;
                }
            }
            // total
            $rowGrandTotal = $rowItem;
            $spreadsheet->getActiveSheet()->mergeCells('B' . $rowGrandTotal . ':D' . $rowGrandTotal);
            $activeWorksheet->setCellValue('B' . $rowGrandTotal, 'Total');
            $columnItem = 'E';
            $activeWorksheet->setCellValue($columnItem . $rowGrandTotal, '=SUM(E' . $rowItemStart . ':E' . ($rowItem - 1) . ')');
            phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowGrandTotal);
            $columnItem++;
            $activeWorksheet->setCellValue($columnItem . $rowGrandTotal, '=SUM(F' . $rowItemStart . ':F' . ($rowItem - 1) . ')');
            phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowGrandTotal);
            phpspreadsheet::addFullBorder($spreadsheet, 'B' . $rowGrandTotal . ':' . $columnItem . $rowGrandTotal);
            $columnItem++;

            $rowItem++;
            $rowItem++;
        }

        // total
        $rowGrandTotal = $rowItem;
        $spreadsheet->getActiveSheet()->mergeCells('A' . $rowGrandTotal . ':D' . $rowGrandTotal);
        $activeWorksheet->setCellValue('A' . $rowGrandTotal, 'Grand Total');
        $columnItem = 'E';
        $grandTotalOrderQuantity = array_reduce($data, function ($carry, $item) {
            $carry += $item->order_qty;
            return $carry;
        }, 0);
        $activeWorksheet->setCellValue($columnItem . $rowGrandTotal, $grandTotalOrderQuantity);
        phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        $grandTotalOrderWeight = array_reduce($data, function ($carry, $item) {
            $carry += $item->order_qty * $item->order_berat;
            return $carry;
        }, 0);
        $activeWorksheet->setCellValue($columnItem . $rowGrandTotal, $grandTotalOrderWeight);
        phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;

        $activeWorksheet->getStyle($columnHeaderStart . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart)->getAlignment()->setWrapText(true);

        // size auto
        $columnSizeStart = $columnItemStart;
        $columnSizeStart++;
        while ($columnSizeStart !== $columnHeaderEnd) {
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

    public function checklistOrder($tglAwal, $tglAkhir)
    {
        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();
        $activeWorksheet->setShowGridlines(false);

        // Set locale agar tanggal indonesia
        Carbon::setLocale('id');

        // Judul
        $activeWorksheet->setCellValue('A1', 'CHECKLIST');
        $activeWorksheet->setCellValue('A2', 'Periode Order: ' . $tglAwal->translatedFormat('d-M-Y H:i') . ' s/d ' . $tglAkhir->translatedFormat('d-M-Y H:i') . ' - Buyer: ' . ($this->buyer_id != null ? MsBuyer::find($this->buyer_id)->name : 'all'));
        // Style Judul
        phpspreadsheet::styleFont($spreadsheet, 'A1:A2', true, 11, 'Calibri');

        // header
        $rowHeaderStart = 3;
        $columnHeaderStart = 'A';
        $columnHeaderEnd = 'A';

        // filter tanggal
        $filterDate = '';
        if ($this->filter == 'Order') {
            $fieldDate = 'tdo.order_date';
            $filterDate = '(tdo.order_date BETWEEN :tglAwal AND :tglAkhir)';
            $headerDate = 'Order Date';
        } else if ($this->filter == 'Proses') {
            $fieldDate = 'tdo.processdate';
            $filterDate = '(tdo.processdate BETWEEN :tglAwal AND :tglAkhir)';
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
            $filterBuyer = 'AND '.'('.' tdo.buyer_id = ' . $this->buyer_id . ')';
        }

        // query belum benar
        $data = collect(DB::select(
            "
                SELECT
                tdo.id AS id,
                tdo.po_no AS po_no,
                tdo.product_id AS product_id,
                tdo.product_code AS product_code,
                mp.product_type_code AS product_type_code,
                mp.name AS produk_name,
                tdo.order_qty AS order_qty,
                tdo.order_unit AS order_unit,
                tdo.order_date AS order_date,
                tdo.stufingDate AS stufing_date,
                tdo.etdDate AS etdDate,
                tdo.etaDate AS etaDate,
                tdo.processDate AS process_date,
                tdo.processSeq AS process_seq,
                tdo.buyer_id AS buyer_id, msb.name as namabuyer,
                tdo.total_assembly AS total_assembly,
                tdo.total_finishing AS total_finishing,
                tdo.status_order AS status_order,
                tdo.created_by AS created_by,
                tdo.created_on AS created_on,
                tdo.updated_by AS updated_by,
                tdo.updated_on AS updated_on
                FROM tdOrder AS tdo
                    inner join msbuyer as msb on msb.id=tdo.buyer_id
                    LEFT JOIN msproduct AS mp ON mp.id = tdo.product_id
                WHERE
                    $filterDate
                    $filterBuyer
                ORDER BY $fieldDate ASC, process_seq ASC;
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
        $columnItem = $columnItemStart;
        $iteration = 1;
        foreach ($data as $item) {
            $columnItem = $columnItemStart;
            // no
            $activeWorksheet->setCellValue($columnItem . $rowItemEnd, $iteration);
            phpspreadsheet::textAlignCenter($spreadsheet, $columnItem . $rowItemEnd);
            $iteration++;
            $columnItem++;
            // Tanggal proses
            $activeWorksheet->setCellValue($columnItem . $rowItemEnd, Carbon::parse($item->process_date)->translatedFormat('d-M-Y'));
            phpspreadsheet::textAlignCenter($spreadsheet, $columnItem . $rowItemEnd);
            $columnItem++;
            // no process
            $activeWorksheet->setCellValue($columnItem . $rowItemEnd, $item->process_seq);
            phpspreadsheet::textAlignCenter($spreadsheet, $columnItem . $rowItemEnd);
            $columnItem++;
            // Tanggal Order
            $activeWorksheet->setCellValue($columnItem . $rowItemEnd, Carbon::parse($item->order_date)->translatedFormat('d-M-Y'));
            phpspreadsheet::textAlignCenter($spreadsheet, $columnItem . $rowItemEnd);
            $columnItem++;
            // po no
            $activeWorksheet->setCellValue($columnItem . $rowItemEnd, $item->po_no);
            $columnItem++;
            // noorder
            $activeWorksheet->setCellValue($columnItem . $rowItemEnd, $item->product_code);
            $columnItem++;
            // product name
            $activeWorksheet->setCellValue($columnItem . $rowItemEnd, $item->produk_name);
            $columnItem++;
            // product type code
            $activeWorksheet->setCellValue($columnItem . $rowItemEnd, $item->product_type_code);
            phpspreadsheet::textAlignCenter($spreadsheet, $columnItem . $rowItemEnd);
            $columnItem++;
            // order qty
            $activeWorksheet->setCellValue($columnItem . $rowItemEnd, $item->order_qty);
            phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItemEnd);
            $columnItem++;
            // order unit
            $activeWorksheet->setCellValue($columnItem . $rowItemEnd, $item->order_unit);
            phpspreadsheet::textAlignCenter($spreadsheet, $columnItem . $rowItemEnd);
            $columnItem++;
            // stufing date
            $activeWorksheet->setCellValue($columnItem . $rowItemEnd, Carbon::parse($item->stufing_date)->translatedFormat('d-M-Y'));
            phpspreadsheet::textAlignCenter($spreadsheet, $columnItem . $rowItemEnd);
            $columnItem++;
            // etd date
            $activeWorksheet->setCellValue($columnItem . $rowItemEnd, Carbon::parse($item->etddate)->translatedFormat('d-M-Y'));
            phpspreadsheet::textAlignCenter($spreadsheet, $columnItem . $rowItemEnd);
            $columnItem++;
            // eta date
            $activeWorksheet->setCellValue($columnItem . $rowItemEnd, Carbon::parse($item->etadate)->translatedFormat('d-M-Y'));
            phpspreadsheet::textAlignCenter($spreadsheet, $columnItem . $rowItemEnd);
            $columnItem++;
            // buyer
            $activeWorksheet->setCellValue($columnItem . $rowItemEnd, $item->namabuyer);
            phpspreadsheet::addFullBorder($spreadsheet, $columnItemStart . $rowItemEnd . ':' . $columnItem . $rowItemEnd);
            $columnItem++;
            // status order
            if ($item->status_order == 0) {
                $activeWorksheet->setCellValue($columnItem . $rowItemEnd, 'Belum di LPK');
            }
            phpspreadsheet::textAlignCenter($spreadsheet, $columnItem . $rowItemEnd);

            phpspreadsheet::styleFont($spreadsheet, $columnItemStart . $rowItemEnd . ':' . $columnItem . $rowItemEnd, false, 8, 'Calibri');
            $rowItemEnd++;
        }

        // grand total
        $rowGrandTotal = $rowItemEnd;
        $spreadsheet->getActiveSheet()->mergeCells('A' . $rowGrandTotal . ':I' . $rowGrandTotal);
        $activeWorksheet->setCellValue('A' . $rowGrandTotal, 'Grand Total');
        $columnItem = 'J';
        // total order qty
        $activeWorksheet->setCellValue($columnItem . $rowGrandTotal, '=SUM(J' . $rowItemStart . ':J' . ($rowItemEnd - 1) . ')');
        phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;

        $activeWorksheet->getStyle($columnHeaderStart . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart)->getAlignment()->setWrapText(true);

        // size auto
        $columnSizeStart = $columnItemStart;
        $columnItem = 'P';
        $columnSizeStart++;
        while ($columnSizeStart !== $columnItem) {
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

    public function checklistLPK($tglAwal, $tglAkhir)
    {
        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();
        $activeWorksheet->setShowGridlines(false);

        // Set locale agar tanggal indonesia
        Carbon::setLocale('id');

        // Judul
        $activeWorksheet->setCellValue('A1', 'CHECKLIST LPK');
        $activeWorksheet->setCellValue('A2', 'Periode LPK: ' . $tglAwal->translatedFormat('d-M-Y H:i') . ' s/d ' . $tglAkhir->translatedFormat('d-M-Y H:i'));
        // Style Judul
        phpspreadsheet::styleFont($spreadsheet, 'A1:A2', true, 11, 'Calibri');

        // header
        $rowHeaderStart = 3;
        $rowHeaderEnd = 3;
        $columnHeaderStart = 'A';
        $columnHeaderEnd = 'A';

        // filter tanggal
        $filterDate = '';
        if ($this->filter == 'Order') {
            $fieldDate = 'tdo.order_date';
            $filterDate = 'tdo.order_date BETWEEN :tglAwal AND :tglAkhir';
            $headerDate = 'Order Date';
        } else if ($this->filter == 'Proses') {
            $fieldDate = 'tdo.processdate';
            $filterDate = 'tdo.processdate BETWEEN :tglAwal AND :tglAkhir';
            $headerDate = 'Process Date';
        } else if ($this->filter == 'LPK') {
            $fieldDate = 'tdol.lpk_date';
            $filterDate = 'tdol.lpk_date BETWEEN :tglAwal AND :tglAkhir';
            $headerDate = 'LPK Date';
        }

        $headerFirst = [
            [
                'field' => 'No',
                'merge' => true
            ],
            [
                'field' => 'Tanggal Proses',
                'merge' => false
            ],
            [
                'field' => 'Tanggal LPK',
                'merge' => false
            ],
            [
                'field' => 'PO Number',
                'merge' => false
            ],
            [
                'field' => 'Nama Produk',
                'merge' => false
            ],
            [
                'field' => 'Jumlah LPK',
                'merge' => true
            ],
            [
                'field' => 'Unit',
                'merge' => true
            ],
            [
                'field' => 'Jumlah Gentan',
                'merge' => true
            ],
            [
                'field' => 'Total Meter',
                'merge' => true
            ],
            [
                'field' => 'Panjang Gulung',
                'merge' => true
            ],
            [
                'field' => 'Catatan',
                'merge' => true
            ],
        ];

        $headerSecond = [
            'No.Proses',
            'Nomor LPK',
            'Nomor Order',
            'Nomor Mesin'
        ];
        $columnHeaderSecond = 'B';
        $rowHeaderSecond = 4;

        // first header
        foreach ($headerFirst as $key => $value) {
            $activeWorksheet->setCellValue($columnHeaderEnd . $rowHeaderStart, $value['field']);
            if ($value['merge']) {
                $spreadsheet->getActiveSheet()->mergeCells($columnHeaderEnd . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderSecond);
            }
            $columnHeaderEnd++;
        }
        $columnHeaderEnd = chr(ord($columnHeaderEnd) - 1);

        // second header
        foreach ($headerSecond as $key => $value) {
            $activeWorksheet->setCellValue($columnHeaderSecond . $rowHeaderSecond, $value);
            $columnHeaderSecond++;
        }

        // style header
        phpspreadsheet::addFullBorder($spreadsheet, $columnHeaderStart . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderSecond);
        phpspreadsheet::styleFont($spreadsheet, $columnHeaderStart . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderSecond, true, 9, 'Calibri');
        phpspreadsheet::textAlignCenter($spreadsheet, $columnHeaderStart . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderSecond);

        // filter buyer
        $filterBuyer = '';
        if ($this->buyer_id != null) {
            $filterBuyer = 'AND tdol.buyer_id = ' . $this->buyer_id;
        }

        // query belum benar
        $data = DB::select(
            "
                SELECT
                    tdol.id AS id,
                    tdol.lpk_no AS lpk_no,
                    tdol.lpk_date AS lpk_date,
                    tdol.order_id AS order_id,
                    tdol.machine_id AS machine_id,
                    tdol.product_id AS product_id,
                    tdol.qty_gentan AS qty_gentan,
                    tdol.qty_gulung AS qty_gulung,
                    tdol.qty_lpk AS qty_lpk,
                    tdol.panjang_lpk AS panjang_lpk,
                    tdol.product_panjang AS product_panjang,
                    tdol.product_panjangGulung AS product_panjangGulung,
                    tdol.seq_no AS seq_no,
                    tdol.reprint_no AS reprint_no,
                    tdol.total_assembly_line AS total_assembly_line,
                    tdol.total_assembly_qty AS total_assembly_qty,
                    tdol.remark AS remark,
                    tdol.status_lpk AS status_lpk,
                    tdol.prev_lpk_no AS prev_lpk_no,
                    tdol.prev_machine_no AS prev_machine_no,
                    tdol.prev_product_code AS prev_product_code,
                    tdol.created_by AS created_by,
                    tdol.created_on AS created_on,
                    tdol.updated_by AS updated_by,
                    tdol.updated_on AS updated_on,
                    tdo.po_no AS po_no,
                    tdo.order_date AS order_date,
                    tdo.processDate AS process_date,
                    tdo.processSeq AS process_seq,
                    tdo.order_qty AS order_qty,
                    tdo.buyer_id AS buyer_id,
                    tdo.product_code,
                    mp.name AS produk_name,
                    msb.name AS namabuyer,
                    tdo.order_unit AS order_unit,
                    mu.name AS unit_name,
                    tdo.stufingDate AS stufingDate,
                    tdo.total_assembly AS total_assembly,
                    tdo.total_finishing AS total_finishing
                FROM tdOrderLpk AS tdol
                INNER JOIN tdOrder AS tdo ON tdol.order_id = tdo.id
                INNER JOIN msBuyer AS msb ON msb.id = tdo.buyer_id
                INNER JOIN msProduct AS mp ON mp.id = tdo.product_id
                INNER JOIN msunit AS mu ON mu.code = tdo.order_unit
                WHERE
                    $filterDate
                    $filterBuyer;
                ",
            [
                'tglAwal' => $tglAwal->format('Y-m-d'),
                'tglAkhir' => $tglAkhir->format('Y-m-d'),
            ]
        );

        if (count($data) == 0) {
            $response = [
                'status' => 'error',
                'message' => "Data pada periode tanggal atau pembeli tersebut tidak ditemukan"
            ];

            return $response;
        }

        $rowItemStart = 5;
        $rowItemStartSecond = 6;
        $rowItemEnd = $rowItemStart;
        $columnItemStart = 'A';
        $columnItemEnd = $columnItemStart;
        $iteration = 1;
        foreach ($data as $item) {
            $columnItemEnd = $columnItemStart;
            // no
            $activeWorksheet->setCellValue($columnItemEnd . $rowItemEnd, $iteration);
            phpspreadsheet::textAlignCenter($spreadsheet, $columnItemEnd . $rowItemEnd);
            $iteration++;
            $columnItemEnd++;
            // tanggal proses
            $activeWorksheet->setCellValue($columnItemEnd . $rowItemEnd, Carbon::parse($item->process_date)->translatedFormat('d-M-Y'));
            phpspreadsheet::textAlignCenter($spreadsheet, $columnItemEnd . $rowItemEnd);
            // no proses
            $activeWorksheet->setCellValue($columnItemEnd . $rowItemStartSecond, $item->process_seq);
            phpspreadsheet::textAlignCenter($spreadsheet, $columnItemEnd . $rowItemStartSecond);
            $columnItemEnd++;
            // tanggal lpk
            $activeWorksheet->setCellValue($columnItemEnd . $rowItemEnd, Carbon::parse($item->lpk_date)->translatedFormat('d-M-Y'));
            phpspreadsheet::textAlignCenter($spreadsheet, $columnItemEnd . $rowItemEnd);
            // nomor LPK
            $activeWorksheet->setCellValue($columnItemEnd . $rowItemStartSecond, $item->lpk_no);
            phpspreadsheet::textAlignCenter($spreadsheet, $columnItemEnd . $rowItemStartSecond);
            $columnItemEnd++;
            // po no
            $activeWorksheet->setCellValue($columnItemEnd . $rowItemEnd, $item->po_no);
            // nomor order
            $activeWorksheet->setCellValue($columnItemEnd . $rowItemStartSecond, $item->product_code);
            $columnItemEnd++;
            // nama produk
            $activeWorksheet->setCellValue($columnItemEnd . $rowItemEnd, $item->produk_name);
            // nomor mesin
            $activeWorksheet->setCellValue($columnItemEnd . $rowItemStartSecond, $item->machine_id);
            $columnItemEnd++;
            // jumlah lpk
            $activeWorksheet->setCellValue($columnItemEnd . $rowItemEnd, $item->qty_lpk);
            phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItemEnd . $rowItemEnd);
            $columnItemEnd++;
            // unit
            $activeWorksheet->setCellValue($columnItemEnd . $rowItemStartSecond, $item->order_unit);
            phpspreadsheet::textAlignCenter($spreadsheet, $columnItemEnd . $rowItemStartSecond);
            $columnItemEnd++;
            // jumlah gentan
            $activeWorksheet->setCellValue($columnItemEnd . $rowItemStartSecond, $item->qty_gentan);
            phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItemEnd . $rowItemStartSecond);
            $columnItemEnd++;
            // total meter
            $activeWorksheet->setCellValue($columnItemEnd . $rowItemStartSecond, $item->total_assembly_line);
            phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItemEnd . $rowItemStartSecond);
            $columnItemEnd++;
            // panjang gulung
            $activeWorksheet->setCellValue($columnItemEnd . $rowItemStartSecond, $item->panjang_lpk);
            phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItemEnd . $rowItemStartSecond);
            $columnItemEnd++;
            // catatan
            $activeWorksheet->setCellValue($columnItemEnd . $rowItemStartSecond, $item->remark);
            phpspreadsheet::addFullBorder($spreadsheet, $columnItemStart . $rowItemEnd . ':' . $columnItemEnd . $rowItemStartSecond);
            $columnItemEnd++;

            $rowItemEnd = $rowItemStartSecond + 1;
            $rowItemStartSecond = $rowItemEnd + 1;
        }

        // grand total
        $rowGrandTotal = $rowItemEnd;
        $spreadsheet->getActiveSheet()->mergeCells('A' . $rowGrandTotal . ':E' . $rowGrandTotal);
        $activeWorksheet->setCellValue('A' . $rowGrandTotal, 'Grand Total');
        $columnItem = 'F';
        // jumlah LPK
        $jumlahLPK = array_reduce($data, function ($carry, $item) {
            $carry += $item->qty_lpk;
            return $carry;
        }, 0);
        $activeWorksheet->setCellValue($columnItem . $rowGrandTotal, $jumlahLPK);
        phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        $columnItem++;
        // jumlah gentan
        $jumlahGentan = array_reduce($data, function ($carry, $item) {
            $carry += $item->qty_gentan;
            return $carry;
        }, 0);
        $activeWorksheet->setCellValue($columnItem . $rowGrandTotal, $jumlahGentan);
        phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // total meter
        $totalMeter = array_reduce($data, function ($carry, $item) {
            $carry += $item->total_assembly_line;
            return $carry;
        }, 0);
        $activeWorksheet->setCellValue($columnItem . $rowGrandTotal, $totalMeter);
        phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // panjang gulung
        $panjangGulung = array_reduce($data, function ($carry, $item) {
            $carry += $item->panjang_lpk;
            return $carry;
        }, 0);
        $activeWorksheet->setCellValue($columnItem . $rowGrandTotal, $panjangGulung);
        phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        phpspreadsheet::addFullBorder($spreadsheet, 'A' . $rowGrandTotal . ':' . $columnItem . $rowGrandTotal);

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

    public function orderProgress($tglAwal, $tglAkhir)
    {
        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();
        $activeWorksheet->setShowGridlines(false);

        // Set locale agar tanggal indonesia
        Carbon::setLocale('id');

        // Judul
        $activeWorksheet->setCellValue('A1', 'ORDER PROGRESS');
        $activeWorksheet->setCellValue('A2', 'Periode Proses: ' . $tglAwal->translatedFormat('d-M-Y H:i') . ' s/d ' . $tglAkhir->translatedFormat('d-M-Y H:i'));
        // Style Judul
        phpspreadsheet::styleFont($spreadsheet, 'A1:A2', true, 11, 'Calibri');

        // header
        $rowHeaderStart = 3;
        $rowHeaderEnd = 3;
        $columnHeaderStart = 'A';
        $columnHeaderEnd = 'A';

        // filter tanggal
        $filterDate = '';
        if ($this->filter == 'Order') {
            $fieldDate = 'ord.order_date as filter_date';
            $filterDate = 'ord.order_date BETWEEN :tglAwal AND :tglAkhir';
            $headerDate = 'Order Date';
        } else if ($this->filter == 'Proses') {
            $fieldDate = 'ord.processdate as filter_date';
            $filterDate = 'ord.processdate BETWEEN :tglAwal AND :tglAkhir';
            $headerDate = 'Process Date';
        } else if ($this->filter == 'LPK') {
            $fieldDate = 'lpk.lpk_date as filter_date';
            $filterDate = 'lpk.lpk_date BETWEEN :tglAwal AND :tglAkhir';
            $headerDate = 'LPK Date';
        }

        $header = [
            'No',
            $headerDate,
            'PO Number',
            'Stufing Date',
            'ETD',
            'No Order',
            'Product Name',
            'Order Quantity (pcs)',
            'LPK Number',
            'LPK Quantity (pcs)',
            'LPK Quantity (meter)',
        ];

        $headerProgressSecond = [
            'Production Total',
            'Kenpin on Process',
            'kenpin Loss',
            'Product Ready',
        ];

        $columnHeaderProgressMeter = 'L';
        $columnHeaderProgressPcs = 'P';
        $rowHeaderSecond = 4;

        // first header
        foreach ($header as $key => $value) {
            $activeWorksheet->setCellValue($columnHeaderEnd . $rowHeaderStart, $value);
            $spreadsheet->getActiveSheet()->mergeCells($columnHeaderEnd . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderSecond);
            $columnHeaderEnd++;
        }
        // $columnHeaderEnd = chr(ord($columnHeaderEnd) - 1);

        // second header
        $activeWorksheet->setCellValue($columnHeaderProgressMeter . $rowHeaderStart, 'INFURE PROGRESS (Meter)');
        $spreadsheet->getActiveSheet()->mergeCells($columnHeaderProgressMeter . $rowHeaderStart . ':O' . $rowHeaderStart);
        $activeWorksheet->setCellValue($columnHeaderProgressPcs . $rowHeaderStart, 'INFURE PROGRESS (Meter)');
        $spreadsheet->getActiveSheet()->mergeCells($columnHeaderProgressPcs . $rowHeaderStart . ':S' . $rowHeaderStart);

        for ($i = 1; $i <= 2; $i++) {
            foreach ($headerProgressSecond as $key => $value) {
                $activeWorksheet->setCellValue($columnHeaderProgressMeter . $rowHeaderSecond, $value);
                $columnHeaderProgressMeter++;
            }
        }
        $columnHeaderProgressMeter = chr(ord($columnHeaderProgressMeter) - 1);

        // style header
        phpspreadsheet::addFullBorder($spreadsheet, $columnHeaderStart . $rowHeaderStart . ':' . $columnHeaderProgressMeter . $rowHeaderSecond);
        phpspreadsheet::styleFont($spreadsheet, $columnHeaderStart . $rowHeaderStart . ':' . $columnHeaderProgressMeter . $rowHeaderSecond, true, 9, 'Calibri');
        phpspreadsheet::textAlignCenter($spreadsheet, $columnHeaderStart . $rowHeaderStart . ':' . $columnHeaderProgressMeter . $rowHeaderSecond);

        // filter buyer
        $filterBuyer = '';
        if ($this->buyer_id != null) {
            $filterBuyer = 'AND ord.buyer_id = ' . $this->buyer_id;
        }

        // query belum benar
        $data = DB::select(
            "
                SELECT
                    byr.id AS buyer_id,
                    byr.name AS buyer_name,
                    $fieldDate,
                    ord.order_date,
                    ord.po_no,
                    ord.stufingdate,
                    ord.etddate,
                    ord.etadate,
                    prd.code AS product_code,
                    prd.name AS product_name,
                    ord.order_qty,
                    ord.order_qty * prd.unit_weight * 0.001 AS order_berat,
                    lpk.lpk_no,
                    lpk.qty_lpk,
                    lpk.lpk_date,
                    lpk.panjang_lpk,
                    lpk.total_assembly_line,
                    lpk.total_assembly_qty,
                    COALESCE(asy.panjang_produksi, 0) AS panjang_produksi,
                    COALESCE(asy.kenpin_meter_loss, 0) AS kenpin_meter_loss,
                    COALESCE(asy.kenpin_meter_loss_proses, 0) AS kenpin_meter_loss_proses,
                    COALESCE(gds.qty_produksi, 0) AS qty_produksi,
                    COALESCE(gds.kenpin_qty_loss, 0) AS kenpin_qty_loss,
                    COALESCE(gds.kenpin_qty_loss_proses, 0) AS kenpin_qty_loss_proses
                FROM tdOrder AS ord
                INNER JOIN tdOrderLPK AS lpk ON ord.id = lpk.order_id
                CROSS JOIN LATERAL
                (
                    SELECT
                        SUM(panjang_produksi) AS panjang_produksi,
                        SUM(kenpin_meter_loss) AS kenpin_meter_loss,
                        SUM(kenpin_meter_loss_proses) AS kenpin_meter_loss_proses
                    FROM tdProduct_Assembly AS asyx
                    WHERE lpk.id = asyx.lpk_id
                ) asy
                LEFT JOIN LATERAL
                (
                    SELECT
                        SUM(qty_produksi) AS qty_produksi,
                        SUM(kenpin_qty_loss) AS kenpin_qty_loss,
                        SUM(kenpin_qty_loss_proses) AS kenpin_qty_loss_proses
                    FROM tdProduct_Goods AS gdsx
                    WHERE lpk.id = gdsx.lpk_id
                ) gds ON TRUE
                INNER JOIN msBuyer AS byr ON ord.buyer_id = byr.id
                INNER JOIN msProduct AS prd ON ord.product_id = prd.id
                INNER JOIN msProduct_type AS prdType ON prd.product_type_id = prdType.id
                INNER JOIN msProduct_group ON prdType.product_group_id = msProduct_group.id
                WHERE
                    $filterDate
                    $filterBuyer
                ",
            [
                'tglAwal' => $tglAwal->format('Y-m-d'),
                'tglAkhir' => $tglAkhir->format('Y-m-d'),
            ]
        );

        if (count($data) == 0) {
            $response = [
                'status' => 'error',
                'message' => "Data pada periode tanggal atau pembeli tersebut tidak ditemukan"
            ];

            return $response;
        }

        $listBuyer = array_reduce($data, function ($carry, $item) {
            $carry[$item->buyer_id] = $item->buyer_name;
            return $carry;
        }, []);

        $listPoNo = array_reduce($data, function ($carry, $item) {
            $carry[$item->buyer_id][$item->po_no] = [
                'filter_date' => $item->filter_date,
                'po_no' => $item->po_no,
                'stufingdate' => $item->stufingdate,
                'etddate' => $item->etddate,
                'product_code' => $item->product_code,
                'product_name' => $item->product_name,
                'order_qty' => $item->order_qty,
                'order_berat' => $item->order_berat,
            ];
            return $carry;
        }, []);

        $dataFilter = array_reduce($data, function ($carry, $item) {
            $carry[$item->buyer_id][$item->po_no][$item->lpk_no] = $item;
            return $carry;
        }, []);

        $rowItemStart = 5;
        $rowItemEnd = $rowItemStart;
        $columnItemStart = 'A';
        $columnLPKStart = 'I';
        $columnLProgresstart = 'I';
        $columnMergeLPKStart = 'B';
        $columnMergeLPKEnd = 'H';
        #R
        $columnItemEnd = $columnItemStart;
        foreach ($listBuyer as $buyerId => $buyerName) {
            $iteration = 1;
            $columnItemEnd = $columnItemStart;
            $spreadsheet->getActiveSheet()->mergeCells('A' . $rowItemEnd . ':S' . $rowItemEnd);
            $activeWorksheet->setCellValue($columnItemEnd . $rowItemEnd, $buyerName);
            phpspreadsheet::styleFont($spreadsheet, $columnItemEnd . $rowItemEnd, true, 9, 'Calibri');
            $rowItemEnd++;
            foreach ($listPoNo[$buyerId] as $poNo => $itemPO) {
                $columnItemEnd = $columnItemStart;
                // no
                $activeWorksheet->setCellValue($columnItemEnd . $rowItemEnd, $iteration);
                phpspreadsheet::textAlignCenter($spreadsheet, $columnItemEnd . $rowItemEnd);
                $iteration++;
                $columnItemEnd++;
                // filter date
                $activeWorksheet->setCellValue($columnItemEnd . $rowItemEnd, Carbon::parse($itemPO['filter_date'])->translatedFormat('d-M-Y'));
                phpspreadsheet::textAlignCenter($spreadsheet, $columnItemEnd . $rowItemEnd);
                $columnItemEnd++;
                // po no
                $activeWorksheet->setCellValue($columnItemEnd . $rowItemEnd, $itemPO['po_no']);
                $columnItemEnd++;
                // stufing date
                $activeWorksheet->setCellValue($columnItemEnd . $rowItemEnd, Carbon::parse($itemPO['stufingdate'])->translatedFormat('d-M-Y'));
                phpspreadsheet::textAlignCenter($spreadsheet, $columnItemEnd . $rowItemEnd);
                $columnItemEnd++;
                // etd date
                $activeWorksheet->setCellValue($columnItemEnd . $rowItemEnd, Carbon::parse($itemPO['etddate'])->translatedFormat('d-M-Y'));
                phpspreadsheet::textAlignCenter($spreadsheet, $columnItemEnd . $rowItemEnd);
                $columnItemEnd++;
                // order no
                $activeWorksheet->setCellValue($columnItemEnd . $rowItemEnd, $itemPO['product_code']);
                $columnItemEnd++;
                // product name
                $activeWorksheet->setCellValue($columnItemEnd . $rowItemEnd, $itemPO['product_name']);
                $columnItemEnd++;
                // order qty
                $activeWorksheet->setCellValue($columnItemEnd . $rowItemEnd, $itemPO['order_qty']);
                phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItemEnd . $rowItemEnd);
                $columnItemEnd++;

                $skipIteration = true;

                // LPK
                $dataItem = $dataFilter[$buyerId][$poNo];
                foreach ($dataItem as $item) {
                    $columnItemEnd = $columnLPKStart;
                    // $rowItemEnd++;
                    // no
                    if (!$skipIteration) {
                        $activeWorksheet->setCellValue($columnItemStart . $rowItemEnd, $iteration);
                        phpspreadsheet::textAlignCenter($spreadsheet, $columnItemStart . $rowItemEnd);
                        $iteration++;

                        // merge cell
                        $spreadsheet->getActiveSheet()->mergeCells($columnMergeLPKStart . $rowItemEnd . ':' . $columnMergeLPKEnd . $rowItemEnd);
                    }
                    $skipIteration = false;
                    // lpk no
                    $activeWorksheet->setCellValue($columnItemEnd . $rowItemEnd, $item->lpk_no);
                    $columnItemEnd++;
                    // lpk qty
                    $activeWorksheet->setCellValue($columnItemEnd . $rowItemEnd, $item->qty_lpk);
                    phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItemEnd . $rowItemEnd);
                    $columnItemEnd++;
                    // lpk meter
                    $activeWorksheet->setCellValue($columnItemEnd . $rowItemEnd, $item->total_assembly_line);
                    phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItemEnd . $rowItemEnd);
                    $columnItemEnd++;
                    /**
                     * INFURE PROGRESS (Meter)
                     */
                    // production total
                    $activeWorksheet->setCellValue($columnItemEnd . $rowItemEnd, $item->panjang_produksi);
                    phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItemEnd . $rowItemEnd);
                    $columnItemEnd++;
                    // kenpin on process
                    $activeWorksheet->setCellValue($columnItemEnd . $rowItemEnd, $item->kenpin_meter_loss_proses);
                    phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItemEnd . $rowItemEnd);
                    $columnItemEnd++;
                    // kenpin loss
                    $activeWorksheet->setCellValue($columnItemEnd . $rowItemEnd, $item->kenpin_meter_loss);
                    phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItemEnd . $rowItemEnd);
                    $columnItemEnd++;
                    // product ready
                    $activeWorksheet->setCellValue($columnItemEnd . $rowItemEnd, $item->panjang_produksi);
                    phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItemEnd . $rowItemEnd);
                    $columnItemEnd++;

                    /**
                     * SEITAI PROGRESS (PCS)
                     */
                    // production total
                    $activeWorksheet->setCellValue($columnItemEnd . $rowItemEnd, $item->qty_produksi);
                    phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItemEnd . $rowItemEnd);
                    $columnItemEnd++;
                    // kenpin on process
                    $activeWorksheet->setCellValue($columnItemEnd . $rowItemEnd, $item->kenpin_qty_loss_proses);
                    phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItemEnd . $rowItemEnd);
                    $columnItemEnd++;
                    // kenpin loss
                    $activeWorksheet->setCellValue($columnItemEnd . $rowItemEnd, $item->kenpin_qty_loss);
                    phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItemEnd . $rowItemEnd);
                    $columnItemEnd++;
                    // product ready
                    $activeWorksheet->setCellValue($columnItemEnd . $rowItemEnd, $item->qty_produksi);
                    phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItemEnd . $rowItemEnd);
                    phpspreadsheet::addFullBorder($spreadsheet, $columnItemStart . $rowItemEnd . ':' . $columnItemEnd . $rowItemEnd);
                    $columnItemEnd++;

                    phpspreadsheet::styleFont($spreadsheet, $columnItemStart . $rowItemEnd . ':' . $columnItemEnd . $rowItemEnd, false, 8, 'Calibri');
                    $rowItemEnd++;
                }
            }

            // Total
            $spreadsheet->getActiveSheet()->mergeCells('A' . $rowItemEnd . ':G' . $rowItemEnd);
            $activeWorksheet->setCellValue('A' . $rowItemEnd, 'Total');
            $columnItem = 'H';
            // total order qty
            $activeWorksheet->setCellValue($columnItem . $rowItemEnd, '=SUM('.$columnItem . $rowItemStart . ':'.$columnItem . ($rowItemEnd - 1) . ')');
            phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItemEnd);
            $columnItem++;
            $columnItem++;

            // total LPK qty
            $activeWorksheet->setCellValue($columnItem . $rowItemEnd, '=SUM('.$columnItem . $rowItemStart . ':'.$columnItem . ($rowItemEnd - 1) . ')');
            phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItemEnd);
            $columnItem++;
            // total LPK meter
            $activeWorksheet->setCellValue($columnItem . $rowItemEnd, '=SUM('.$columnItem . $rowItemStart . ':'.$columnItem . ($rowItemEnd - 1) . ')');
            phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItemEnd);
            $columnItem++;

            /**
             * INFURE PROGRESS (Meter)
             */
            // production total
            $activeWorksheet->setCellValue($columnItem . $rowItemEnd, '=SUM('.$columnItem . $rowItemStart . ':'. $columnItem . ($rowItemEnd - 1) . ')');
            phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItemEnd);
            $columnItem++;
            // kenpin on process
            $activeWorksheet->setCellValue($columnItem . $rowItemEnd, '=SUM('.$columnItem . $rowItemStart . ':'. $columnItem . ($rowItemEnd - 1) . ')');
            phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItemEnd);
            $columnItem++;
            // kenpin loss
            $activeWorksheet->setCellValue($columnItem . $rowItemEnd, '=SUM('.$columnItem . $rowItemStart . ':'. $columnItem . ($rowItemEnd - 1) . ')');
            phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItemEnd);
            $columnItem++;
            // product ready
            $activeWorksheet->setCellValue($columnItem . $rowItemEnd, '=SUM('.$columnItem . $rowItemStart . ':'. $columnItem . ($rowItemEnd - 1) . ')');
            phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItemEnd);
            $columnItem++;

            /**
             * SEITAI PROGRESS (PCS)
             */
            // production total
            $activeWorksheet->setCellValue($columnItem . $rowItemEnd, '=SUM('.$columnItem . $rowItemStart . ':'. $columnItem . ($rowItemEnd - 1) . ')');
            phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItemEnd);
            $columnItem++;
            // kenpin on process
            $activeWorksheet->setCellValue($columnItem . $rowItemEnd, '=SUM('.$columnItem . $rowItemStart . ':'. $columnItem . ($rowItemEnd - 1) . ')');
            phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItemEnd);
            $columnItem++;
            // kenpin loss
            $activeWorksheet->setCellValue($columnItem . $rowItemEnd, '=SUM('.$columnItem . $rowItemStart . ':'. $columnItem . ($rowItemEnd - 1) . ')');
            phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItemEnd);
            $columnItem++;
            // product ready
            $activeWorksheet->setCellValue($columnItem . $rowItemEnd, '=SUM('.$columnItem . $rowItemStart . ':'. $columnItem . ($rowItemEnd - 1) . ')');
            phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItemEnd);
            phpspreadsheet::addFullBorder($spreadsheet, 'A' . $rowItemEnd . ':' . $columnItem . $rowItemEnd);
            phpspreadsheet::styleFont($spreadsheet, 'A' . $rowItemEnd . ':' . $columnItem . $rowItemEnd, true, 9, 'Calibri');
            $columnItem++;

            $rowItemEnd++;
            $rowItemEnd++;
        }

        // Grand Total
        $spreadsheet->getActiveSheet()->mergeCells('A' . $rowItemEnd . ':G' . $rowItemEnd);
        $activeWorksheet->setCellValue('A' . $rowItemEnd, 'Grand Total');

        $columnItem = 'H';
        // total order qty
        $totalOrderQty = array_reduce($data, function ($carry, $item) {
            $carry += $item->order_qty;
            return $carry;
        }, 0);
        $activeWorksheet->setCellValue($columnItem . $rowItemEnd, $totalOrderQty);
        phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItemEnd);
        $columnItem++;
        $columnItem++;

        // total LPK qty
        $totalLPKQty = array_reduce($data, function ($carry, $item) {
            $carry += $item->qty_lpk;
            return $carry;
        }, 0);
        $activeWorksheet->setCellValue($columnItem . $rowItemEnd, $totalLPKQty);
        phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItemEnd);
        $columnItem++;
        // total LPK meter
        $totalLPKMeter = array_reduce($data, function ($carry, $item) {
            $carry += $item->total_assembly_line;
            return $carry;
        }, 0);
        $activeWorksheet->setCellValue($columnItem . $rowItemEnd, $totalLPKMeter);
        phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItemEnd);
        $columnItem++;

        /**
         * INFURE PROGRESS (Meter)
         */
        // production total
        $totalProductionTotal = array_reduce($data, function ($carry, $item) {
            $carry += $item->panjang_produksi;
            return $carry;
        }, 0);
        $activeWorksheet->setCellValue($columnItem . $rowItemEnd, $totalProductionTotal);
        phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItemEnd);
        $columnItem++;
        // kenpin on process
        $totalKenpinOnProcess = array_reduce($data, function ($carry, $item) {
            $carry += $item->kenpin_meter_loss_proses;
            return $carry;
        }, 0);
        $activeWorksheet->setCellValue($columnItem . $rowItemEnd, $totalKenpinOnProcess);
        phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItemEnd);
        $columnItem++;
        // kenpin loss
        $totalKenpinLoss = array_reduce($data, function ($carry, $item) {
            $carry += $item->kenpin_meter_loss;
            return $carry;
        }, 0);
        $activeWorksheet->setCellValue($columnItem . $rowItemEnd, $totalKenpinLoss);
        phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItemEnd);
        $columnItem++;
        // product ready
        $totalProductReady = array_reduce($data, function ($carry, $item) {
            $carry += $item->panjang_produksi;
            return $carry;
        }, 0);
        $activeWorksheet->setCellValue($columnItem . $rowItemEnd, $totalProductReady);
        phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItemEnd);
        $columnItem++;

        /**
         * SEITAI PROGRESS (PCS)
         */
        // production total
        $totalProductionTotal = array_reduce($data, function ($carry, $item) {
            $carry += $item->qty_produksi;
            return $carry;
        }, 0);
        $activeWorksheet->setCellValue($columnItem . $rowItemEnd, $totalProductionTotal);
        phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItemEnd);
        $columnItem++;
        // kenpin on process
        $totalKenpinOnProcess = array_reduce($data, function ($carry, $item) {
            $carry += $item->kenpin_qty_loss_proses;
            return $carry;
        }, 0);
        $activeWorksheet->setCellValue($columnItem . $rowItemEnd, $totalKenpinOnProcess);
        phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItemEnd);
        $columnItem++;
        // kenpin loss
        $totalKenpinLoss = array_reduce($data, function ($carry, $item) {
            $carry += $item->kenpin_qty_loss;
            return $carry;
        }, 0);
        $activeWorksheet->setCellValue($columnItem . $rowItemEnd, $totalKenpinLoss);
        phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItemEnd);
        $columnItem++;
        // product ready
        $totalProductReady = array_reduce($data, function ($carry, $item) {
            $carry += $item->qty_produksi;
            return $carry;
        }, 0);
        $activeWorksheet->setCellValue($columnItem . $rowItemEnd, $totalProductReady);
        phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItemEnd);
        phpspreadsheet::addFullBorder($spreadsheet, 'A' . $rowItemEnd . ':' . $columnItem . $rowItemEnd);
        phpspreadsheet::styleFont($spreadsheet, 'A' . $rowItemEnd . ':' . $columnItem . $rowItemEnd, true, 9, 'Calibri');
        $columnItem++;

        $activeWorksheet->getStyle($columnHeaderStart . $rowHeaderStart . ':' . $columnHeaderProgressMeter . $rowHeaderSecond)->getAlignment()->setWrapText(true);

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

    public function render()
    {
        return view('livewire.order-lpk.order-report')->extends('layouts.master');
    }
}
