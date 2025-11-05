<?php

namespace App\Http\Livewire\Kenpin\Report;

use App\Exports\KenpinExport;
use App\Helpers\phpspreadsheet;
use App\Models\MsDepartment;
use App\Models\MsMachine;
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

class GeneralReportKenpinInfureController extends Component
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

    public function perMesinReportKenpinInfure($tglAwal, $tglAkhir, $filter = null)
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
        $activeWorksheet->setCellValue('A1', 'JUMLAH KENPIN PER MESIN INFURE');
        $activeWorksheet->setCellValue('A2', 'Tanggal: ' . $tglAwal->translatedFormat('d-M-Y H:i') . '  ~  ' . $tglAkhir->translatedFormat('d-M-Y H:i'));
        // Style Judul
        phpspreadsheet::styleFont($spreadsheet, 'A1:A2', true, 11, 'Calibri');

        // header
        $rowHeaderStart = 4;
        $columnHeaderStart = 'A';
        $columnHeaderEnd = 'A';

        // machine infure
        $machineInfure = MsMachine::infureDepartment()
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
                    msmk.name AS nama_masalah
                FROM
                    tdKenpin AS tdka
                    INNER JOIN tdOrderLpk AS tdol ON tdka.lpk_id = tdol.ID
                    INNER JOIN tdProduct_Assembly AS tdpa ON tdol.ID = tdpa.lpk_id
                    INNER JOIN msmachine AS msm ON msm.ID = tdpa.machine_id
                    INNER JOIN msmasalahkenpin AS msmk ON msmk.ID = tdka.masalah_kenpin_id
                WHERE
                    tdka.department_id = 2 AND
                    tdka.is_kasus = true
                    $filterKenpinId
                    $filterDate
                    $filterNoLPK
                    $filterProduct
                    $filterNomorKenpin
                    $filterStatus
                    $filterNomorHan
                GROUP BY tdka.id, msm.machineno, msmk.code, msmk.name
                ORDER BY msmk.code ASC, msm.machineno ASC",
        );

        if (count($data) == 0) {
            $response = [
                'status' => 'error',
                'message' => "Data pada periode tanggal tersebut tidak ditemukan"
            ];

            return $response;
        }

        // Get all machines from machine infure for consistent ordering
        $allMachines = $machineInfure->pluck('machineno')->toArray();

        // Get all masalah kenpin with department id = 3 for complete list
        $allMasalah = DB::select(
            "SELECT code, name
             FROM msmasalahkenpin
             WHERE department_group_id = 3
             ORDER BY code ASC"
        );

        // Build complete masalah list from department id = 3
        $masalahList = [];
        foreach ($allMasalah as $masalah) {
            $masalahList[$masalah->code] = $masalah->name;
        }

        // Prepare data structure for pivot table
        $kenpinData = [];

        // Collect kenpin data from actual results
        foreach ($data as $item) {
            $masalahKey = $item->code_masalah;
            // Store kenpin count data
            $kenpinData[$masalahKey][$item->machineno] = ($kenpinData[$masalahKey][$item->machineno] ?? 0) + 1;
        }
        $header = [
            'Kode Masalah',
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
        $activeWorksheet->freezePane('C5'); // Freeze after masalah columns
        $columnHeaderEnd = Coordinate::stringFromColumnIndex(Coordinate::columnIndexFromString($columnHeaderEnd) - 1);

        // style header
        phpspreadsheet::addFullBorder($spreadsheet, $columnHeaderStart . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);
        phpspreadsheet::styleFont($spreadsheet, $columnHeaderStart . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart, true, 9, 'Calibri');
        phpspreadsheet::textAlignCenter($spreadsheet, $columnHeaderStart . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);
        $activeWorksheet->getStyle($columnHeaderStart . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart)->getAlignment()->setWrapText(true);

        // Fill data rows
        $rowItem = $rowHeaderStart + 1;
        foreach ($masalahList as $codeMasalah => $namaMasalah) {
            $columnItem = $columnHeaderStart;

            // Kode Masalah
            $activeWorksheet->setCellValue($columnItem . $rowItem, $codeMasalah);
            $columnItem++;

            // Nama Masalah
            $activeWorksheet->setCellValue($columnItem . $rowItem, $namaMasalah);
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

        // Apply styles to data area
        $lastRow = $rowItem - 1;
        $dataRange = $columnHeaderStart . ($rowHeaderStart + 1) . ':' . $columnHeaderEnd . $lastRow;
        phpspreadsheet::addBorderDottedMiddleHorizontal($spreadsheet, $dataRange);
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
        $filename = 'Kenpin-Infure-Per-Mesin-' . $tglAwal->format('dmyHi') . '-' . $tglAkhir->format('dmyHi') . '.xlsx';
        $writer->save($filename);
        $response = [
            'status' => 'success',
            'filename' => $filename
        ];
        return $response;
    }

    public function beratLossReportKenpinInfure($tglAwal, $tglAkhir, $filter = null)
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
        $activeWorksheet->setCellValue('A1', 'BERAT LOSS KENPIN PER MESIN INFURE (KG)');
        $activeWorksheet->setCellValue('A2', 'Tanggal: ' . $tglAwal->translatedFormat('d-M-Y H:i') . '  ~  ' . $tglAkhir->translatedFormat('d-M-Y H:i'));
        // Style Judul
        phpspreadsheet::styleFont($spreadsheet, 'A1:A2', true, 11, 'Calibri');

        // header
        $rowHeaderStart = 4;
        $columnHeaderStart = 'A';
        $columnHeaderEnd = 'A';

        // machine infure
        $machineInfure = MsMachine::infureDepartment()
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
                SUM(tdkad.berat_loss) AS total_berat_loss
            FROM
                tdKenpin AS tdka
                INNER JOIN tdkenpin_assembly_detail AS tdkad ON tdka.ID = tdkad.kenpin_id
                INNER JOIN tdProduct_Assembly AS tdpa ON tdkad.product_assembly_id = tdpa.ID
                INNER JOIN msmachine AS msm ON msm.ID = tdpa.machine_id
                INNER JOIN msmasalahkenpin AS msmk ON msmk.ID = tdka.masalah_kenpin_id
            WHERE
                tdka.department_id = 2
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

        // Get all machines from machine infure for consistent ordering
        $allMachines = $machineInfure->pluck('machineno')->toArray();

        // Get all masalah kenpin with department id = 3 for complete list
        $allMasalah = DB::select(
            "SELECT code, name
             FROM msmasalahkenpin
             WHERE department_group_id = 3
             ORDER BY code ASC"
        );

        // Build complete masalah list from department id = 3
        $masalahList = [];
        foreach ($allMasalah as $masalah) {
            $masalahList[$masalah->code] = $masalah->name;
        }

        // Prepare data structure for pivot table
        $beratLossData = [];

        // Collect berat loss data from actual results
        foreach ($data as $item) {
            $masalahKey = $item->code_masalah;
            // Store berat loss data
            $beratLossData[$masalahKey][$item->machineno] = $item->total_berat_loss;
        }

        // Update header to include all machines (from the existing $machineInfure)
        $header = [
            'Kode Masalah',
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
        $activeWorksheet->freezePane('C5'); // Freeze after masalah columns
        $columnHeaderEnd = Coordinate::stringFromColumnIndex(Coordinate::columnIndexFromString($columnHeaderEnd) - 1);

        // style header
        phpspreadsheet::addFullBorder($spreadsheet, $columnHeaderStart . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);
        phpspreadsheet::styleFont($spreadsheet, $columnHeaderStart . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart, true, 9, 'Calibri');
        phpspreadsheet::textAlignCenter($spreadsheet, $columnHeaderStart . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);
        $activeWorksheet->getStyle($columnHeaderStart . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart)->getAlignment()->setWrapText(true);

        // Fill data rows
        $rowItem = $rowHeaderStart + 1;
        foreach ($masalahList as $codeMasalah => $namaMasalah) {
            $columnItem = $columnHeaderStart;

            // Kode Masalah
            $activeWorksheet->setCellValue($columnItem . $rowItem, $codeMasalah);
            $columnItem++;

            // Nama Masalah
            $activeWorksheet->setCellValue($columnItem . $rowItem, $namaMasalah);
            $columnItem++;

            // Fill berat loss for each machine
            foreach ($allMachines as $machine) {
                $beratLoss = isset($beratLossData[$codeMasalah][$machine]) ? $beratLossData[$codeMasalah][$machine] : 0;
                if ($beratLoss > 0) {
                    $activeWorksheet->setCellValue($columnItem . $rowItem, $beratLoss);
                    phpspreadsheet::numberFormatCommaThousandsOrZero($spreadsheet, $columnItem . $rowItem);
                }
                $columnItem++;
            }

            $rowItem++;
        }

        // Apply styles to data area
        $lastRow = $rowItem - 1;
        $dataRange = $columnHeaderStart . ($rowHeaderStart + 1) . ':' . $columnHeaderEnd . $lastRow;
        phpspreadsheet::addBorderDottedMiddleHorizontal($spreadsheet, $dataRange);
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
        $filename = 'Berat-Loss-Kenpin-Infure-Per-Mesin-' . $tglAwal->format('dmyHi') . '-' . $tglAkhir->format('dmyHi') . '.xlsx';
        $writer->save($filename);
        $response = [
            'status' => 'success',
            'filename' => $filename
        ];
        return $response;
    }

    public function qtyLossReportKenpinInfure($tglAwal, $tglAkhir, $filter = null)
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
        $activeWorksheet->setCellValue('A1', 'QTY LOSS KENPIN PER MESIN INFURE');
        $activeWorksheet->setCellValue('A2', 'Tanggal: ' . $tglAwal->translatedFormat('d-M-Y H:i') . '  ~  ' . $tglAkhir->translatedFormat('d-M-Y H:i'));
        // Style Judul
        phpspreadsheet::styleFont($spreadsheet, 'A1:A2', true, 11, 'Calibri');

        // header
        $rowHeaderStart = 4;
        $columnHeaderStart = 'A';
        $columnHeaderEnd = 'A';

        // machine infure
        $machineInfure = MsMachine::seitaiDepartment()
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
                INNER JOIN msmachine AS msm ON msm.ID = tdpg.machine_id
                INNER JOIN msmasalahkenpin AS msmk ON msmk.ID = tdka.masalah_kenpin_id
            WHERE
                tdka.department_id = 2
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

        // Get all machines from machine infure for consistent ordering
        $allMachines = $machineInfure->pluck('machineno')->toArray();

        // Get all masalah kenpin with department id = 3 for complete list
        $allMasalah = DB::select(
            "SELECT code, name
             FROM msmasalahkenpin
             WHERE department_group_id = 3
             ORDER BY code ASC"
        );

        // Build complete masalah list from department id = 3
        $masalahList = [];
        foreach ($allMasalah as $masalah) {
            $masalahList[$masalah->code] = $masalah->name;
        }

        // Prepare data structure for pivot table
        $qtyLossData = [];

        // Collect qty loss data from actual results
        foreach ($data as $item) {
            $masalahKey = $item->code_masalah;
            // Store qty loss data
            $qtyLossData[$masalahKey][$item->machineno] = $item->total_qty_loss;
        }

        // Update header to include all machines (from the existing $machineInfure)
        $header = [
            'Kode Masalah',
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
        $activeWorksheet->freezePane('C5'); // Freeze after masalah columns
        $columnHeaderEnd = Coordinate::stringFromColumnIndex(Coordinate::columnIndexFromString($columnHeaderEnd) - 1);

        // style header
        phpspreadsheet::addFullBorder($spreadsheet, $columnHeaderStart . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);
        phpspreadsheet::styleFont($spreadsheet, $columnHeaderStart . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart, true, 9, 'Calibri');
        phpspreadsheet::textAlignCenter($spreadsheet, $columnHeaderStart . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);
        $activeWorksheet->getStyle($columnHeaderStart . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart)->getAlignment()->setWrapText(true);

        // Fill data rows
        $rowItem = $rowHeaderStart + 1;
        foreach ($masalahList as $codeMasalah => $namaMasalah) {
            $columnItem = $columnHeaderStart;

            // Kode Masalah
            $activeWorksheet->setCellValue($columnItem . $rowItem, $codeMasalah);
            $columnItem++;

            // Nama Masalah
            $activeWorksheet->setCellValue($columnItem . $rowItem, $namaMasalah);
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

        // Apply styles to data area
        $lastRow = $rowItem - 1;
        $dataRange = $columnHeaderStart . ($rowHeaderStart + 1) . ':' . $columnHeaderEnd . $lastRow;
        phpspreadsheet::addBorderDottedMiddleHorizontal($spreadsheet, $dataRange);
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
        $filename = 'Qty-Loss-Kenpin-Infure-Per-Mesin-' . $tglAwal->format('dmyHi') . '-' . $tglAkhir->format('dmyHi') . '.xlsx';
        $writer->save($filename);
        $response = [
            'status' => 'success',
            'filename' => $filename
        ];
        return $response;
    }
}
