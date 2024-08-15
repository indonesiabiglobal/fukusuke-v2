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
        $this->filter = 'Order';
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
        if ($this->filter == 'Tanggal Order') {
            $fieldDate = 'tod.order_date';
            $filterDate = 'tod.order_date BETWEEN :tglAwal AND :tglAkhir';
            $headerDate = 'Order Date';
        } else if ($this->filter == 'Tanggal Proses') {
            $fieldDate = 'tod.processdate';
            $filterDate = 'tod.processdate BETWEEN :tglAwal AND :tglAkhir';
            $headerDate = 'Process Date';
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
            'Daftar Date',
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

        // $headerProgressFirst = [
        //     'INFURE PROGRESS (Meter)',
        //     'INFURE PROGRESS (Pcs)',
        // ];

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

        $activeWorksheet->getStyle($columnHeaderStart . $rowHeaderStart . ':' . $columnHeaderProgressMeter . $rowHeaderSecond)->getAlignment()->setWrapText(true);

        // // size auto
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

    public function render()
    {
        return view('livewire.order-lpk.order-report')->extends('layouts.master');
    }
}
