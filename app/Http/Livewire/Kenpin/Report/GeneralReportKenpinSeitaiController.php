<?php

namespace App\Http\Livewire\Kenpin\Report;

use App\Exports\KenpinExport;
use App\Helpers\phpspreadsheet;
use App\Models\MsDepartment;
use App\Models\MsMachine;
use App\Models\MsMasalahKenpin;
use App\Models\MsProduct;
use App\Models\MsWorkingShift;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class GeneralReportKenpinSeitaiController extends Component
{
    public $tglAwal;
    public $tglAkhir;
    public $jamAwal;
    public $jamAkhir;
    public $workingShiftHour;
    public $product;
    public $productId;
    public $department;
    public $nippo;
    public $buyer;
    public $buyer_id;
    public $lpk_no;
    public $nomorKenpin;
    public $nomorHan;
    public $nomorPalet;
    public $nomorLot;
    public $status;

    public function perMesinReportKenpinSeitai($tglAwal, $tglAkhir, $filter = null)
    {
        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();
        $activeWorksheet->setShowGridlines(false);
        $activeWorksheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        // Mengatur ukuran kertas menjadi A4
        $activeWorksheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
        // Mengatur agar semua kolom muat dalam satu halaman
        $activeWorksheet->getPageSetup()->setFitToWidth(1);
        $activeWorksheet->getPageSetup()->setFitToHeight(0);
        // Set header berulang untuk print
        $activeWorksheet->getPageSetup()->setRowsToRepeatAtTop([1, 3]);

        // Jika ingin memastikan rasio tetap terjaga
        $activeWorksheet->getPageSetup()->setFitToPage(true);

        // Mengatur margin halaman menjadi 0.75 cm di semua sisi
        $activeWorksheet->getPageMargins()->setTop(1.1 / 2.54);
        $activeWorksheet->getPageMargins()->setBottom(1.0 / 2.54);
        $activeWorksheet->getPageMargins()->setLeft(0.75 / 2.54);
        $activeWorksheet->getPageMargins()->setRight(0.75 / 2.54);
        $activeWorksheet->getPageMargins()->setHeader(0.4 / 2.54);
        $activeWorksheet->getPageMargins()->setFooter(0.5 / 2.54);
        // Mengatur tinggi sel agar otomatis menyesuaikan dengan konten
        $activeWorksheet->getDefaultRowDimension()->setRowHeight(-1);

        // Header yang hanya muncul saat print
        $activeWorksheet->getHeaderFooter()->setOddHeader('&L&"Calibri,Bold"&14Fukusuke - Production Control');
        // Footer
        $currentDate = date('d M Y - H:i');
        $footerLeft = '&L&"Calibri"&10Printed: ' . $currentDate . ', by: ' . auth()->user()->username;
        $footerRight = '&R&"Calibri"&10Page: &P of: &N';
        $activeWorksheet->getHeaderFooter()->setOddFooter($footerLeft . $footerRight);

        // Set locale agar tanggal indonesia
        Carbon::setLocale('id');

        // Judul
        $activeWorksheet->setCellValue('A1', 'JUMLAH KENPIN PER MESIN SEITAI');
        $activeWorksheet->setCellValue('A2', 'Tanggal: ' . $tglAwal->translatedFormat('d-M-Y H:i') . '  ~  ' . $tglAkhir->translatedFormat('d-M-Y H:i'));
        // Style Judul
        phpspreadsheet::styleFont($spreadsheet, 'A1:A2', true, 11, 'Calibri');

        // header
        $rowHeaderStart = 4;
        $rowDataStart = 5;
        $columnHeaderStart = 'A';
        $columnDataStart = 'D';
        $columnHeaderEnd = 'A';

        // machine seitai
        $machineSeitai = MsMachine::seitaiDepartment()
            ->active()
            ->orderBy('machineno', 'ASC')
            ->get();

        // Filter Query
        $filterKenpinId = $filter && isset($filter['kenpin_id']) ? " AND (tdka.ID = '" . $filter['kenpin_id'] . "')" : '';
        $filterDate = "AND tdka.kenpin_date BETWEEN '" . $tglAwal . "' AND '" . $tglAkhir . "'";
        $filterNoLPK = $filter && isset($filter['lpk_no']) ? " AND (tdol.lpk_no = '" . $filter['lpk_no'] . "')" : '';
        $filterProduct = isset($filter['productId']) ? " AND (tdpa.product_id = '" . $filter['productId'] . "')" : '';
        $filterNomorKenpin = isset($filter['nomorKenpin']) ? " AND (tdka.kenpin_no = '" . $filter['nomorKenpin'] . "')" : '';
        $filterStatus = isset($filter['status']) ? " AND (tdka.status_kenpin = '" . $filter['status'] . "')" : '';
        $filterNomorPalet = isset($filter['nomorPalet']) ? " AND (tdpa.nomor_palet = '" . $this->nomorPalet . "')" : '';
        $filterNomorLot = isset($filter['nomorLot']) ? " AND (tdpa.nomor_lot = '" . $this->nomorLot . "')" : '';

        $data = DB::select(
            "
                SELECT
                    msm.machineno,
                    msmk.code AS code_masalah,
                    msmk.name AS nama_masalah,
                    COUNT(DISTINCT tdka.id) AS jumlah_kenpin
                FROM
                    tdKenpin AS tdka
                    INNER JOIN tdkenpin_goods_detail AS tdkgd ON tdka.ID = tdkgd.kenpin_id
                    INNER JOIN tdproduct_goods AS tdpa ON tdkgd.product_goods_id = tdpa.ID
                    INNER JOIN tdorderlpk AS tdol ON tdol.ID = tdpa.lpk_id
                    INNER JOIN msmachine AS msm ON msm.ID = tdpa.machine_id
                    INNER JOIN msmasalahkenpin AS msmk ON msmk.ID = tdka.masalah_kenpin_id
                WHERE
                    tdka.department_id = 7
                    $filterKenpinId
                    $filterDate
                    $filterNoLPK
                    $filterProduct
                    $filterNomorKenpin
                    $filterStatus
                    $filterNomorPalet
                    $filterNomorLot
                GROUP BY msm.machineno, msmk.code, msmk.name
                ORDER BY msmk.code ASC, msm.machineno ASC",
        );

        if (count($data) == 0) {
            $response = [
                'status' => 'error',
                'message' => "Data pada periode tanggal tersebut tidak ditemukan"
            ];

            return $response;
        }

        // Get all machines from machine seitai for consistent ordering
        $allMachines = $machineSeitai->pluck('machineno')->toArray();

        // Get all masalah kenpin with department id = 3 for complete list
        $allMasalah = MsMasalahKenpin::with('departmentGroup')
            ->whereHas('departmentGroup', function ($query) {
                $query->where('department_id', 7);
            })
            ->orderBy('code', 'ASC')
            ->get();

        // Build complete masalah list from department id = 3
        $masalahList = [];
        foreach ($allMasalah as $masalah) {
            $masalahList[$masalah->code] = [
                'name' => $masalah->name,
                'department_group' => $masalah->departmentGroup->name
            ];
        }

        // Prepare data structure for pivot table
        $kenpinData = [];

        // Collect kenpin data from actual results
        foreach ($data as $item) {
            $masalahKey = $item->code_masalah;
            // Store kenpin count data
            $kenpinData[$masalahKey][$item->machineno] = $item->jumlah_kenpin;
        }
        $header = [
            'Kode Masalah',
            'DEPT',
            'Masalah',
        ];

        foreach ($allMachines as $machine) {
            // Format machine number (e.g., "INF-01" becomes "00|01")
            $header[] = $machine;
        }

        // Write headers to Excel
        foreach ($header as $key => $value) {
            $activeWorksheet->setCellValue($columnHeaderEnd . $rowHeaderStart, $value);
            $columnHeaderEnd++;
        }
        $activeWorksheet->freezePane('D5'); // Freeze after masalah columns
        $columnHeaderEnd = Coordinate::stringFromColumnIndex(Coordinate::columnIndexFromString($columnHeaderEnd) - 1);

        // style header
        phpspreadsheet::addFullBorder($spreadsheet, $columnHeaderStart . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);
        phpspreadsheet::styleFont($spreadsheet, $columnHeaderStart . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart, true, 9, 'Calibri');
        phpspreadsheet::textAlignCenter($spreadsheet, $columnHeaderStart . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);
        $activeWorksheet->getStyle($columnHeaderStart . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart)->getAlignment()->setWrapText(true);

        // Fill data rows
        $rowItem = $rowHeaderStart + 1;
        foreach ($masalahList as $codeMasalah => $masalah) {
            $columnItem = $columnHeaderStart;

            // Kode Masalah
            $activeWorksheet->setCellValue($columnItem . $rowItem, $codeMasalah);
            $columnItem++;

            // Department
            $activeWorksheet->setCellValue($columnItem . $rowItem, $masalah['department_group']);
            $columnItem++;

            // Nama Masalah
            $activeWorksheet->setCellValue($columnItem . $rowItem, $masalah['name']);
            $columnItem++;

            // Fill kenpin counts for each machine
            foreach ($allMachines as $machine) {
                $count = isset($kenpinData[$codeMasalah][$machine]) ? $kenpinData[$codeMasalah][$machine] : 0;
                if ($count > 0) {
                    $activeWorksheet->setCellValue($columnItem . $rowItem, $count);
                    phpspreadsheet::numberFormatThousands($spreadsheet, $columnItem . $rowItem);
                }
                $columnItem++;
            }

            $rowItem++;
        }

        // Total row
        $rowTotalItem = 4;
        $columnTotalItemEnd = $columnHeaderEnd;
        $columnHeaderEnd++;
        $activeWorksheet->setCellValue($columnHeaderEnd . $rowTotalItem, 'Total');

        // Apply SUM formula for each machine column
        $rowTotalItem++;
        foreach ($allMasalah as $masalah) {
            $activeWorksheet->setCellValue($columnHeaderEnd . $rowTotalItem, '=SUM(' . 'D' . $rowTotalItem . ':' . $columnTotalItemEnd . $rowTotalItem . ')');
            phpspreadsheet::numberFormatThousands($spreadsheet, $columnHeaderEnd . $rowTotalItem);
            $rowTotalItem++;
        }

        // Apply styles to data area
        $lastRow = $rowItem - 1;
        $dataRange = $columnHeaderStart . $rowHeaderStart . ':' . $columnHeaderEnd . $lastRow;
        phpspreadsheet::addFullBorder($spreadsheet, $dataRange);
        phpspreadsheet::styleFont($spreadsheet, $dataRange, false, 8, 'Calibri');
        phpspreadsheet::textAlignCenter($spreadsheet, $dataRange);

        // Auto-size columns
        // Set column A to ~85 pixels and auto-size the rest
        $pixels = 85;
        // Approximate conversion from pixels to Excel column width (Calibri 11 max digit width ≈ 7px)
        $maxDigitWidth = 7;
        $excelWidth = ($pixels - 5) / $maxDigitWidth;
        if ($excelWidth < 1) {
            $excelWidth = 1;
        }

        // Handle columns beyond 'Z' (e.g. 'BS', 'AAA', etc.) by using numeric indexes
        $lastColIndex = Coordinate::columnIndexFromString($columnHeaderEnd);
        for ($colIndex = 1; $colIndex <= $lastColIndex; $colIndex++) {
            $colLetter = Coordinate::stringFromColumnIndex($colIndex);
            if ($colIndex === 1) { // column A
            $activeWorksheet->getColumnDimension($colLetter)->setAutoSize(false);
            $activeWorksheet->getColumnDimension($colLetter)->setWidth($excelWidth);
            } else {
            $activeWorksheet->getColumnDimension($colLetter)->setAutoSize(true);
            }
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'Kenpin-Seitai-Per-Mesin-' . $tglAwal->format('dmyHi') . '-' . $tglAkhir->format('dmyHi') . '.xlsx';
        $writer->save($filename);
        $response = [
            'status' => 'success',
            'filename' => $filename
        ];
        return $response;
    }

    public function perBoxReportKenpinSeitai($tglAwal, $tglAkhir, $filter = null)
    {
        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();
        $activeWorksheet->setShowGridlines(false);
        $activeWorksheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        // Mengatur ukuran kertas menjadi A4
        $activeWorksheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
        // Mengatur agar semua kolom muat dalam satu halaman
        $activeWorksheet->getPageSetup()->setFitToWidth(1);
        $activeWorksheet->getPageSetup()->setFitToHeight(0);
        // Set header berulang untuk print
        $activeWorksheet->getPageSetup()->setRowsToRepeatAtTop([1, 3]);

        // Jika ingin memastikan rasio tetap terjaga
        $activeWorksheet->getPageSetup()->setFitToPage(true);

        // Mengatur margin halaman menjadi 0.75 cm di semua sisi
        $activeWorksheet->getPageMargins()->setTop(1.1 / 2.54);
        $activeWorksheet->getPageMargins()->setBottom(1.0 / 2.54);
        $activeWorksheet->getPageMargins()->setLeft(0.75 / 2.54);
        $activeWorksheet->getPageMargins()->setRight(0.75 / 2.54);
        $activeWorksheet->getPageMargins()->setHeader(0.4 / 2.54);
        $activeWorksheet->getPageMargins()->setFooter(0.5 / 2.54);
        // Mengatur tinggi sel agar otomatis menyesuaikan dengan konten
        $activeWorksheet->getDefaultRowDimension()->setRowHeight(-1);

        // Header yang hanya muncul saat print
        $activeWorksheet->getHeaderFooter()->setOddHeader('&L&"Calibri,Bold"&14Fukusuke - Production Control');
        // Footer
        $currentDate = date('d M Y - H:i');
        $footerLeft = '&L&"Calibri"&10Printed: ' . $currentDate . ', by: ' . auth()->user()->username;
        $footerRight = '&R&"Calibri"&10Page: &P of: &N';
        $activeWorksheet->getHeaderFooter()->setOddFooter($footerLeft . $footerRight);

        // Set locale agar tanggal indonesia
        Carbon::setLocale('id');

        // Judul
        $activeWorksheet->setCellValue('A1', 'JUMLAH KENPIN PER BOX SEITAI');
        $activeWorksheet->setCellValue('A2', 'Tanggal: ' . $tglAwal->translatedFormat('d-M-Y H:i') . '  ~  ' . $tglAkhir->translatedFormat('d-M-Y H:i'));
        // Style Judul
        phpspreadsheet::styleFont($spreadsheet, 'A1:A2', true, 11, 'Calibri');

        // header
        $rowHeaderStart = 4;
        $columnHeaderStart = 'A';
        $columnHeaderEnd = 'A';

        // Filter Query
        $filterKenpinId = $filter && isset($filter['kenpin_id']) ? " AND (tdka.ID = '" . $filter['kenpin_id'] . "')" : '';
        $filterDate = "AND tdka.kenpin_date BETWEEN '" . $tglAwal . "' AND '" . $tglAkhir . "'";
        $filterNoLPK = $filter && isset($filter['lpk_no']) ? " AND (tdol.lpk_no = '" . $filter['lpk_no'] . "')" : '';
        $filterProduct = isset($filter['productId']) ? " AND (tdpa.product_id = '" . $filter['productId'] . "')" : '';
        $filterNomorKenpin = isset($filter['nomorKenpin']) ? " AND (tdka.kenpin_no = '" . $filter['nomorKenpin'] . "')" : '';
        $filterStatus = isset($filter['status']) ? " AND (tdka.status_kenpin = '" . $filter['status'] . "')" : '';
        $filterNomorPalet = isset($filter['nomorPalet']) ? " AND (tdpa.nomor_palet = '" . $this->nomorPalet . "')" : '';
        $filterNomorLot = isset($filter['nomorLot']) ? " AND (tdpa.nomor_lot = '" . $this->nomorLot . "')" : '';

        $data = DB::select(
            "
                SELECT
                    tdkgdb.box_number,
                    msmk.code AS code_masalah,
                    msmk.name AS nama_masalah,
                    COUNT(DISTINCT tdka.id) AS jumlah_kenpin
                FROM
                    tdKenpin AS tdka
                    INNER JOIN tdkenpin_goods_detail AS tdkgd ON tdka.ID = tdkgd.kenpin_id
                    INNER JOIN tdkenpin_goods_detail_box AS tdkgdb ON tdkgd.ID = tdkgdb.kenpin_goods_detail_id
                    INNER JOIN tdproduct_goods AS tdpa ON tdkgd.product_goods_id = tdpa.ID
                    INNER JOIN tdorderlpk AS tdol ON tdol.ID = tdpa.lpk_id
                    INNER JOIN msmasalahkenpin AS msmk ON msmk.ID = tdka.masalah_kenpin_id
                WHERE
                    tdka.department_id = 7
                    $filterKenpinId
                    $filterDate
                    $filterNoLPK
                    $filterProduct
                    $filterNomorKenpin
                    $filterStatus
                    $filterNomorPalet
                    $filterNomorLot
                GROUP BY tdkgdb.box_number, msmk.code, msmk.name
                ORDER BY msmk.code ASC, tdkgdb.box_number ASC",
        );

        if (count($data) == 0) {
            $response = [
                'status' => 'error',
                'message' => "Data pada periode tanggal tersebut tidak ditemukan"
            ];

            return $response;
        }

        // Get all unique box numbers for consistent ordering
        $allBoxNumbers = collect($data)->pluck('box_number')->unique()->sort()->values()->toArray();

        // Get all masalah kenpin with department id = 7 for complete list
        $allMasalah = MsMasalahKenpin::with('departmentGroup')
            ->whereHas('departmentGroup', function ($query) {
                $query->where('department_id', 7);
            })
            ->orderBy('code', 'ASC')
            ->get();

        // Build complete masalah list from department id = 7
        $masalahList = [];
        foreach ($allMasalah as $masalah) {
            $masalahList[$masalah->code] = [
                'name' => $masalah->name,
                'department_group' => $masalah->departmentGroup->name
            ];
        }

        // Prepare data structure for pivot table
        $kenpinData = [];

        // Collect kenpin data from actual results
        foreach ($data as $item) {
            $masalahKey = $item->code_masalah;
            // Store kenpin count data and qty
            $kenpinData[$masalahKey][$item->box_number] = $item->jumlah_kenpin;
        }

        $header = [
            'Kode Masalah',
            'DEPT',
            'Masalah',
        ];

        foreach ($allBoxNumbers as $boxNumber) {
            $header[] = 'Box ' . $boxNumber;
        }

        // Write headers to Excel
        foreach ($header as $key => $value) {
            $activeWorksheet->setCellValue($columnHeaderEnd . $rowHeaderStart, $value);
            $columnHeaderEnd++;
        }
        $activeWorksheet->freezePane('D5'); // Freeze after masalah columns
        $columnHeaderEnd = Coordinate::stringFromColumnIndex(Coordinate::columnIndexFromString($columnHeaderEnd) - 1);

        // style header
        phpspreadsheet::addFullBorder($spreadsheet, $columnHeaderStart . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);
        phpspreadsheet::styleFont($spreadsheet, $columnHeaderStart . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart, true, 9, 'Calibri');
        phpspreadsheet::textAlignCenter($spreadsheet, $columnHeaderStart . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);
        $activeWorksheet->getStyle($columnHeaderStart . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart)->getAlignment()->setWrapText(true);

        // Fill data rows
        $rowItem = $rowHeaderStart + 1;
        foreach ($masalahList as $codeMasalah => $masalah) {
            $columnItem = $columnHeaderStart;

            // Kode Masalah
            $activeWorksheet->setCellValue($columnItem . $rowItem, $codeMasalah);
            $columnItem++;

            // Department
            $activeWorksheet->setCellValue($columnItem . $rowItem, $masalah['department_group']);
            $columnItem++;

            // Nama Masalah
            $activeWorksheet->setCellValue($columnItem . $rowItem, $masalah['name']);
            $columnItem++;

            // Fill kenpin counts for each box
            foreach ($allBoxNumbers as $boxNumber) {
                $count = isset($kenpinData[$codeMasalah][$boxNumber]) ? $kenpinData[$codeMasalah][$boxNumber] : 0;
                if ($count > 0) {
                    $activeWorksheet->setCellValue($columnItem . $rowItem, $count);
                    phpspreadsheet::numberFormatThousands($spreadsheet, $columnItem . $rowItem);
                }
                $columnItem++;
            }

            $rowItem++;
        }

        // Total row
        $rowTotalItem = 4;
        $columnTotalItemEnd = $columnHeaderEnd;
        $columnHeaderEnd++;
        $activeWorksheet->setCellValue($columnHeaderEnd . $rowTotalItem, 'Total');

        // Apply SUM formula for each box column
        $rowTotalItem++;
        foreach ($allMasalah as $masalah) {
            $activeWorksheet->setCellValue($columnHeaderEnd . $rowTotalItem, '=SUM(' . 'D' . $rowTotalItem . ':' . $columnTotalItemEnd . $rowTotalItem . ')');
            phpspreadsheet::numberFormatThousands($spreadsheet, $columnHeaderEnd . $rowTotalItem);
            $rowTotalItem++;
        }

        // Apply styles to data area
        $lastRow = $rowItem - 1;
        $dataRange = $columnHeaderStart . $rowHeaderStart . ':' . $columnHeaderEnd . $lastRow;
        phpspreadsheet::addFullBorder($spreadsheet, $dataRange);
        phpspreadsheet::styleFont($spreadsheet, $dataRange, false, 8, 'Calibri');
        phpspreadsheet::textAlignCenter($spreadsheet, $dataRange);

        // Auto-size columns
        // Set column A to ~85 pixels and auto-size the rest
        $pixels = 85;
        // Approximate conversion from pixels to Excel column width (Calibri 11 max digit width ≈ 7px)
        $maxDigitWidth = 7;
        $excelWidth = ($pixels - 5) / $maxDigitWidth;
        if ($excelWidth < 1) {
            $excelWidth = 1;
        }

        // Handle columns beyond 'Z' (e.g. 'BS', 'AAA', etc.) by using numeric indexes
        $lastColIndex = Coordinate::columnIndexFromString($columnHeaderEnd);
        for ($colIndex = 1; $colIndex <= $lastColIndex; $colIndex++) {
            $colLetter = Coordinate::stringFromColumnIndex($colIndex);
            if ($colIndex === 1) { // column A
                $activeWorksheet->getColumnDimension($colLetter)->setAutoSize(false);
                $activeWorksheet->getColumnDimension($colLetter)->setWidth($excelWidth);
            } else {
                $activeWorksheet->getColumnDimension($colLetter)->setAutoSize(true);
            }
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'Kenpin-Seitai-Per-Box-' . $tglAwal->format('dmyHi') . '-' . $tglAkhir->format('dmyHi') . '.xlsx';
        $writer->save($filename);
        $response = [
            'status' => 'success',
            'filename' => $filename
        ];
        return $response;
    }

    public function perPaletReportKenpinSeitai($tglAwal, $tglAkhir, $filter = null)
    {
        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();
        $activeWorksheet->setShowGridlines(false);
        $activeWorksheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        // Mengatur ukuran kertas menjadi A4
        $activeWorksheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
        // Mengatur agar semua kolom muat dalam satu halaman
        $activeWorksheet->getPageSetup()->setFitToWidth(1);
        $activeWorksheet->getPageSetup()->setFitToHeight(0);
        // Set header berulang untuk print
        $activeWorksheet->getPageSetup()->setRowsToRepeatAtTop([1, 3]);

        // Jika ingin memastikan rasio tetap terjaga
        $activeWorksheet->getPageSetup()->setFitToPage(true);

        // Mengatur margin halaman menjadi 0.75 cm di semua sisi
        $activeWorksheet->getPageMargins()->setTop(1.1 / 2.54);
        $activeWorksheet->getPageMargins()->setBottom(1.0 / 2.54);
        $activeWorksheet->getPageMargins()->setLeft(0.75 / 2.54);
        $activeWorksheet->getPageMargins()->setRight(0.75 / 2.54);
        $activeWorksheet->getPageMargins()->setHeader(0.4 / 2.54);
        $activeWorksheet->getPageMargins()->setFooter(0.5 / 2.54);
        // Mengatur tinggi sel agar otomatis menyesuaikan dengan konten
        $activeWorksheet->getDefaultRowDimension()->setRowHeight(-1);

        // Header yang hanya muncul saat print
        $activeWorksheet->getHeaderFooter()->setOddHeader('&L&"Calibri,Bold"&14Fukusuke - Production Control');
        // Footer
        $currentDate = date('d M Y - H:i');
        $footerLeft = '&L&"Calibri"&10Printed: ' . $currentDate . ', by: ' . auth()->user()->username;
        $footerRight = '&R&"Calibri"&10Page: &P of: &N';
        $activeWorksheet->getHeaderFooter()->setOddFooter($footerLeft . $footerRight);

        // Set locale agar tanggal indonesia
        Carbon::setLocale('id');

        // Judul
        $activeWorksheet->setCellValue('A1', 'JUMLAH KENPIN PER PALET SEITAI');
        $activeWorksheet->setCellValue('A2', 'Tanggal: ' . $tglAwal->translatedFormat('d-M-Y H:i') . '  ~  ' . $tglAkhir->translatedFormat('d-M-Y H:i'));
        // Style Judul
        phpspreadsheet::styleFont($spreadsheet, 'A1:A2', true, 11, 'Calibri');

        // header
        $rowHeaderStart = 4;
        $columnHeaderStart = 'A';
        $columnHeaderEnd = 'A';

        // Filter Query
        $filterKenpinId = $filter && isset($filter['kenpin_id']) ? " AND (tdka.ID = '" . $filter['kenpin_id'] . "')" : '';
        $filterDate = "AND tdka.kenpin_date BETWEEN '" . $tglAwal . "' AND '" . $tglAkhir . "'";
        $filterNoLPK = $filter && isset($filter['lpk_no']) ? " AND (tdol.lpk_no = '" . $filter['lpk_no'] . "')" : '';
        $filterProduct = isset($filter['productId']) ? " AND (tdpa.product_id = '" . $filter['productId'] . "')" : '';
        $filterNomorKenpin = isset($filter['nomorKenpin']) ? " AND (tdka.kenpin_no = '" . $filter['nomorKenpin'] . "')" : '';
        $filterStatus = isset($filter['status']) ? " AND (tdka.status_kenpin = '" . $filter['status'] . "')" : '';
        $filterNomorPalet = isset($filter['nomorPalet']) ? " AND (tdka.nomor_palet = '" . $filter['nomorPalet'] . "')" : '';
        $filterNomorLot = isset($filter['nomorLot']) ? " AND (tdpa.nomor_lot = '" . $this->nomorLot . "')" : '';

        $data = DB::select(
            "
                SELECT
                    tdka.nomor_palet,
                    msmk.code AS code_masalah,
                    msmk.name AS nama_masalah,
                    COUNT(DISTINCT tdka.id) AS jumlah_kenpin,
                    SUM(tdka.qty_loss) AS total_qty_loss
                FROM
                    tdKenpin AS tdka
                    INNER JOIN tdkenpin_goods_detail AS tdkgd ON tdka.ID = tdkgd.kenpin_id
                    INNER JOIN tdproduct_goods AS tdpa ON tdkgd.product_goods_id = tdpa.ID
                    INNER JOIN tdorderlpk AS tdol ON tdol.ID = tdpa.lpk_id
                    INNER JOIN msmasalahkenpin AS msmk ON msmk.ID = tdka.masalah_kenpin_id
                WHERE
                    tdka.department_id = 7
                    AND tdka.nomor_palet IS NOT NULL
                    AND tdka.nomor_palet != ''
                    $filterKenpinId
                    $filterDate
                    $filterNoLPK
                    $filterProduct
                    $filterNomorKenpin
                    $filterStatus
                    $filterNomorPalet
                    $filterNomorLot
                GROUP BY tdka.nomor_palet, msmk.code, msmk.name
                ORDER BY msmk.code ASC, tdka.nomor_palet ASC",
        );

        if (count($data) == 0) {
            $response = [
                'status' => 'error',
                'message' => "Data pada periode tanggal tersebut tidak ditemukan"
            ];

            return $response;
        }

        // Get all unique palet numbers for consistent ordering
        $allPaletNumbers = collect($data)->pluck('nomor_palet')->unique()->sort()->values()->toArray();

        // Get all masalah kenpin with department id = 7 for complete list
        $allMasalah = MsMasalahKenpin::with('departmentGroup')
            ->whereHas('departmentGroup', function ($query) {
                $query->where('department_id', 7);
            })
            ->orderBy('code', 'ASC')
            ->get();

        // Build complete masalah list from department id = 7
        $masalahList = [];
        foreach ($allMasalah as $masalah) {
            $masalahList[$masalah->code] = [
                'name' => $masalah->name,
                'department_group' => $masalah->departmentGroup->name
            ];
        }

        // Prepare data structure for pivot table
        $kenpinData = [];

        // Collect kenpin data from actual results
        foreach ($data as $item) {
            $masalahKey = $item->code_masalah;
            // Store kenpin count data and qty loss
            $kenpinData[$masalahKey][$item->nomor_palet] = [
                'jumlah_kenpin' => $item->jumlah_kenpin,
                'total_qty_loss' => $item->total_qty_loss
            ];
        }

        $header = [
            'Kode Masalah',
            'DEPT',
            'Masalah',
        ];

        foreach ($allPaletNumbers as $paletNumber) {
            $header[] = $paletNumber;
        }

        // Write headers to Excel
        foreach ($header as $key => $value) {
            $activeWorksheet->setCellValue($columnHeaderEnd . $rowHeaderStart, $value);
            $columnHeaderEnd++;
        }
        $activeWorksheet->freezePane('D5'); // Freeze after masalah columns
        $columnHeaderEnd = Coordinate::stringFromColumnIndex(Coordinate::columnIndexFromString($columnHeaderEnd) - 1);

        // style header
        phpspreadsheet::addFullBorder($spreadsheet, $columnHeaderStart . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);
        phpspreadsheet::styleFont($spreadsheet, $columnHeaderStart . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart, true, 9, 'Calibri');
        phpspreadsheet::textAlignCenter($spreadsheet, $columnHeaderStart . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);
        $activeWorksheet->getStyle($columnHeaderStart . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart)->getAlignment()->setWrapText(true);

        // Fill data rows
        $rowItem = $rowHeaderStart + 1;
        foreach ($masalahList as $codeMasalah => $masalah) {
            $columnItem = $columnHeaderStart;

            // Kode Masalah
            $activeWorksheet->setCellValue($columnItem . $rowItem, $codeMasalah);
            $columnItem++;

            // Department
            $activeWorksheet->setCellValue($columnItem . $rowItem, $masalah['department_group']);
            $columnItem++;

            // Nama Masalah
            $activeWorksheet->setCellValue($columnItem . $rowItem, $masalah['name']);
            $columnItem++;

            // Fill kenpin counts for each palet
            foreach ($allPaletNumbers as $paletNumber) {
                $count = isset($kenpinData[$codeMasalah][$paletNumber]) ? $kenpinData[$codeMasalah][$paletNumber]['jumlah_kenpin'] : 0;
                if ($count > 0) {
                    $activeWorksheet->setCellValue($columnItem . $rowItem, $count);
                    phpspreadsheet::numberFormatThousands($spreadsheet, $columnItem . $rowItem);
                }
                $columnItem++;
            }

            $rowItem++;
        }

        // Total row
        $rowTotalItem = 4;
        $columnTotalItemEnd = $columnHeaderEnd;
        $columnHeaderEnd++;
        $activeWorksheet->setCellValue($columnHeaderEnd . $rowTotalItem, 'Total');

        // Apply SUM formula for each box column
        $rowTotalItem++;
        foreach ($allMasalah as $masalah) {
            $activeWorksheet->setCellValue($columnHeaderEnd . $rowTotalItem, '=SUM(' . 'D' . $rowTotalItem . ':' . $columnTotalItemEnd . $rowTotalItem . ')');
            phpspreadsheet::numberFormatThousands($spreadsheet, $columnHeaderEnd . $rowTotalItem);
            $rowTotalItem++;
        }

        // Apply styles to data area
        $lastRow = $rowItem - 1;
        $dataRange = $columnHeaderStart . $rowHeaderStart . ':' . $columnHeaderEnd . $lastRow;
        phpspreadsheet::addFullBorder($spreadsheet, $dataRange);
        phpspreadsheet::styleFont($spreadsheet, $dataRange, false, 8, 'Calibri');
        phpspreadsheet::textAlignCenter($spreadsheet, $dataRange);

        // Auto-size columns
        // Set column A to ~85 pixels and auto-size the rest
        $pixels = 85;
        // Approximate conversion from pixels to Excel column width (Calibri 11 max digit width ≈ 7px)
        $maxDigitWidth = 7;
        $excelWidth = ($pixels - 5) / $maxDigitWidth;
        if ($excelWidth < 1) {
            $excelWidth = 1;
        }

        // Handle columns beyond 'Z' (e.g. 'BS', 'AAA', etc.) by using numeric indexes
        $lastColIndex = Coordinate::columnIndexFromString($columnHeaderEnd);
        for ($colIndex = 1; $colIndex <= $lastColIndex; $colIndex++) {
            $colLetter = Coordinate::stringFromColumnIndex($colIndex);
            if ($colIndex === 1) { // column A
                $activeWorksheet->getColumnDimension($colLetter)->setAutoSize(false);
                $activeWorksheet->getColumnDimension($colLetter)->setWidth($excelWidth);
            } else {
                $activeWorksheet->getColumnDimension($colLetter)->setAutoSize(true);
            }
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'Kenpin-Seitai-Per-Palet-' . $tglAwal->format('dmyHi') . '-' . $tglAkhir->format('dmyHi') . '.xlsx';
        $writer->save($filename);
        $response = [
            'status' => 'success',
            'filename' => $filename
        ];
        return $response;
    }

    public function qtyLossReportKenpinSeitai($tglAwal, $tglAkhir, $filter = null)
    {
        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();
        $activeWorksheet->setShowGridlines(false);
        $activeWorksheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        // Mengatur ukuran kertas menjadi A4
        $activeWorksheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
        // Mengatur agar semua kolom muat dalam satu halaman
        $activeWorksheet->getPageSetup()->setFitToWidth(1);
        $activeWorksheet->getPageSetup()->setFitToHeight(0);
        // Set header berulang untuk print
        $activeWorksheet->getPageSetup()->setRowsToRepeatAtTop([1, 3]);

        // Jika ingin memastikan rasio tetap terjaga
        $activeWorksheet->getPageSetup()->setFitToPage(true);

        // Mengatur margin halaman menjadi 0.75 cm di semua sisi
        $activeWorksheet->getPageMargins()->setTop(1.1 / 2.54);
        $activeWorksheet->getPageMargins()->setBottom(1.0 / 2.54);
        $activeWorksheet->getPageMargins()->setLeft(0.75 / 2.54);
        $activeWorksheet->getPageMargins()->setRight(0.75 / 2.54);
        $activeWorksheet->getPageMargins()->setHeader(0.4 / 2.54);
        $activeWorksheet->getPageMargins()->setFooter(0.5 / 2.54);
        // Mengatur tinggi sel agar otomatis menyesuaikan dengan konten
        $activeWorksheet->getDefaultRowDimension()->setRowHeight(-1);

        // Header yang hanya muncul saat print
        $activeWorksheet->getHeaderFooter()->setOddHeader('&L&"Calibri,Bold"&14Fukusuke - Production Control');
        // Footer
        $currentDate = date('d M Y - H:i');
        $footerLeft = '&L&"Calibri"&10Printed: ' . $currentDate . ', by: ' . auth()->user()->username;
        $footerRight = '&R&"Calibri"&10Page: &P of: &N';
        $activeWorksheet->getHeaderFooter()->setOddFooter($footerLeft . $footerRight);

        // Set locale agar tanggal indonesia
        Carbon::setLocale('id');

        // Judul
        $activeWorksheet->setCellValue('A1', 'QTY LOSS KENPIN PER MESIN SEITAI');
        $activeWorksheet->setCellValue('A2', 'Tanggal: ' . $tglAwal->translatedFormat('d-M-Y H:i') . '  ~  ' . $tglAkhir->translatedFormat('d-M-Y H:i'));
        // Style Judul
        phpspreadsheet::styleFont($spreadsheet, 'A1:A2', true, 11, 'Calibri');

        // header
        $rowHeaderStart = 4;
        $columnHeaderStart = 'A';
        $columnHeaderEnd = 'A';

        // machine seitai
        $machineSeitai = MsMachine::seitaiDepartment()
            ->active()
            ->orderBy('machineno', 'ASC')
            ->get();

        // Filter Query
        $filterKenpinId = $filter && isset($filter['kenpin_id']) ? " AND (tdka.ID = '" . $filter['kenpin_id'] . "')" : '';
        $filterDate = "AND tdka.kenpin_date BETWEEN '" . $tglAwal . "' AND '" . $tglAkhir . "'";
        $filterNoLPK = $filter && isset($filter['lpk_no']) ? " AND (tdol.lpk_no = '" . $filter['lpk_no'] . "')" : '';
        $filterProduct = isset($filter['productId']) ? " AND (tdpa.product_id = '" . $filter['productId'] . "')" : '';
        $filterNomorKenpin = isset($filter['nomorKenpin']) ? " AND (tdka.kenpin_no = '" . $filter['nomorKenpin'] . "')" : '';
        $filterStatus = isset($filter['status']) ? " AND (tdka.status_kenpin = '" . $filter['status'] . "')" : '';
        $filterNomorHan = isset($filter['nomorHan']) ? " AND (tdpa.nomor_han = '" . $this->nomorHan . "')" : '';

        $data = DB::select(
            "
            SELECT
                msm.machineno,
                msmk.code AS code_masalah,
                msmk.name AS nama_masalah,
                SUM(tdka.qty_loss) AS total_qty_loss
            FROM
                tdKenpin AS tdka
                INNER JOIN tdkenpin_goods_detail AS tdkgd ON tdka.ID = tdkgd.kenpin_id
                INNER JOIN tdProduct_goods AS tdpg ON tdkgd.product_goods_id = tdpg.ID
                INNER JOIN tdorderlpk AS tdol ON tdol.ID = tdpg.lpk_id
                INNER JOIN msmachine AS msm ON msm.ID = tdpg.machine_id
                INNER JOIN msmasalahkenpin AS msmk ON msmk.ID = tdka.masalah_kenpin_id
            WHERE
                tdka.department_id = 7
                $filterKenpinId
                $filterDate
                $filterNoLPK
                $filterProduct
                $filterNomorKenpin
                $filterStatus
                $filterNomorHan
            GROUP BY msm.machineno, msmk.code, msmk.name
            ORDER BY msmk.code ASC, msm.machineno ASC",
        );

        if (count($data) == 0) {
            $response = [
                'status' => 'error',
                'message' => "Data pada periode tanggal tersebut tidak ditemukan"
            ];

            return $response;
        }

        // Get all machines from machine seitai for consistent ordering
        $allMachines = $machineSeitai->pluck('machineno')->toArray();

        // Get all masalah kenpin with department id = 3 for complete list
        $allMasalah = MsMasalahKenpin::with('departmentGroup')
            ->whereHas('departmentGroup', function ($query) {
                $query->where('department_id', 7);
            })
            ->orderBy('code', 'ASC')
            ->get();

        // Build complete masalah list from department id = 3
        $masalahList = [];
        foreach ($allMasalah as $masalah) {
            $masalahList[$masalah->code] = [
                'name' => $masalah->name,
                'department_group' => $masalah->departmentGroup->name
            ];
        }

        // Prepare data structure for pivot table
        $qtyLossData = [];

        // Collect qty loss data from actual results
        foreach ($data as $item) {
            $masalahKey = $item->code_masalah;
            // Store qty loss data
            $qtyLossData[$masalahKey][$item->machineno] = $item->total_qty_loss;
        }

        // Update header to include all machines (from the existing $machineSeitai)
        $header = [
            'Kode Masalah',
            'DEPT',
            'Masalah',
        ];

        foreach ($allMachines as $machine) {
            $header[] = $machine;
        }

        // Write headers to Excel
        foreach ($header as $key => $value) {
            $activeWorksheet->setCellValue($columnHeaderEnd . $rowHeaderStart, $value);
            $columnHeaderEnd++;
        }
        $activeWorksheet->freezePane('D5'); // Freeze after masalah columns
        $columnHeaderEnd = Coordinate::stringFromColumnIndex(Coordinate::columnIndexFromString($columnHeaderEnd) - 1);

        // style header
        phpspreadsheet::addFullBorder($spreadsheet, $columnHeaderStart . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);
        phpspreadsheet::styleFont($spreadsheet, $columnHeaderStart . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart, true, 9, 'Calibri');
        phpspreadsheet::textAlignCenter($spreadsheet, $columnHeaderStart . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);
        $activeWorksheet->getStyle($columnHeaderStart . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart)->getAlignment()->setWrapText(true);

        // Fill data rows
        $rowItem = $rowHeaderStart + 1;
        foreach ($masalahList as $codeMasalah => $masalah) {
            $columnItem = $columnHeaderStart;

            // Kode Masalah
            $activeWorksheet->setCellValue($columnItem . $rowItem, $codeMasalah);
            $columnItem++;

            // Department
            $activeWorksheet->setCellValue($columnItem . $rowItem, $masalah['department_group']);
            $columnItem++;

            // Nama Masalah
            $activeWorksheet->setCellValue($columnItem . $rowItem, $masalah['name']);
            $columnItem++;

            // Fill qty loss for each machine
            foreach ($allMachines as $machine) {
                $qtyLoss = isset($qtyLossData[$codeMasalah][$machine]) ? $qtyLossData[$codeMasalah][$machine] : 0;
                if ($qtyLoss > 0) {
                    $activeWorksheet->setCellValue($columnItem . $rowItem, $qtyLoss);
                    phpspreadsheet::numberFormatThousands($spreadsheet, $columnItem . $rowItem);
                }
                $columnItem++;
            }

            $rowItem++;
        }

        // Total row
        $rowTotalItem = 4;
        $columnTotalItemEnd = $columnHeaderEnd;
        $columnHeaderEnd++;
        $activeWorksheet->setCellValue($columnHeaderEnd . $rowTotalItem, 'Total');

        // Apply SUM formula for each box column
        $rowTotalItem++;
        foreach ($allMasalah as $masalah) {
            $activeWorksheet->setCellValue($columnHeaderEnd . $rowTotalItem, '=SUM(' . 'D' . $rowTotalItem . ':' . $columnTotalItemEnd . $rowTotalItem . ')');
            phpspreadsheet::numberFormatThousands($spreadsheet, $columnHeaderEnd . $rowTotalItem);
            $rowTotalItem++;
        }

        // Apply styles to data area
        $lastRow = $rowItem - 1;
        $dataRange = $columnHeaderStart . $rowHeaderStart . ':' . $columnHeaderEnd . $lastRow;
        phpspreadsheet::addFullBorder($spreadsheet, $dataRange);
        phpspreadsheet::styleFont($spreadsheet, $dataRange, false, 8, 'Calibri');
        phpspreadsheet::textAlignCenter($spreadsheet, $dataRange);

        // Auto-size columns
        // Set column A to ~85 pixels and auto-size the rest
        $pixels = 85;
        // Approximate conversion from pixels to Excel column width (Calibri 11 max digit width ≈ 7px)
        $maxDigitWidth = 7;
        $excelWidth = ($pixels - 5) / $maxDigitWidth;
        if ($excelWidth < 1) {
            $excelWidth = 1;
        }

        for ($col = 'A'; $col <= $columnHeaderEnd; $col++) {
            if ($col === 'A') {
                $activeWorksheet->getColumnDimension($col)->setAutoSize(false);
                $activeWorksheet->getColumnDimension($col)->setWidth($excelWidth);
            } else {
                $activeWorksheet->getColumnDimension($col)->setAutoSize(true);
            }
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'Qty-Loss-Kenpin-Seitai-Per-Mesin-' . $tglAwal->format('dmyHi') . '-' . $tglAkhir->format('dmyHi') . '.xlsx';
        $writer->save($filename);
        $response = [
            'status' => 'success',
            'filename' => $filename
        ];
        return $response;
    }
}
