<?php

namespace App\Http\Livewire\Report\GeneralReport;

use App\Helpers\phpspreadsheet;
use App\Models\MsJamMatiMesin;
use App\Models\MsMachine;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class JamMatiReportService
{
    public static function jamMatiPerMesin($nipon, $jenisReport, $tglMasuk, $tglKeluar)
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

        // Judul
        $activeWorksheet->setCellValue('A1', 'DAFTAR JAM MATI PER MESIN ' . strtoupper($nipon));
        $activeWorksheet->setCellValue('A2', 'Periode : ' . $tglMasuk->format('d-M-Y H:i') . '  ~  ' . $tglKeluar->format('d-M-Y H:i'));
        // Style Judul
        phpspreadsheet::styleFont($spreadsheet, 'A1:A2', true, 11, 'Calibri');

        // header
        $rowHeaderStart = 3;
        $columnHeaderStart = 'A';
        $columnHeaderEnd = 'A';
        $header = [
            'Kode Jam Mati Mesin',
            'Nama Mati Mesin',
            'Jam Mati (Menit)',
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

        $query = MsMachine::query()
            ->select(
                'msmachine.id',
                'msmachine.department_id',
                'machineno',
                'machinename',
                'jmm.code as code_jam_mati',
                'jmm.name as name_jam_mati'
            )
            ->selectRaw("FLOOR(EXTRACT(EPOCH FROM COALESCE(SUM(jkjmm.off_hour), INTERVAL '0')) / 60) AS total_off_minutes")
            ->join('tdjamkerjamesin as jkm', 'jkm.machine_id', '=', 'msmachine.id')
            ->join('tdjamkerja_jammatimesin as jkjmm', 'jkjmm.jam_kerja_mesin_id', '=', 'jkm.id')
            ->join('ms_jam_mati_mesin as jmm', 'jmm.id', '=', 'jkjmm.jam_mati_mesin_id')
            ->join('msworkingshift as ws', 'ws.id', '=', 'jkm.work_shift')
            ->whereRaw("(jkm.working_date + ws.work_hour_from) BETWEEN ? AND ?", [$tglMasuk, $tglKeluar])
            ->where('msmachine.status', 1);

        // filter department
        if ($nipon === 'Infure') {
            $query->infureDepartment();
        } elseif ($nipon === 'Seitai') {
            $query->seitaiDepartment();
        }

        // apply groupBy dan ambil datanya
        $data = $query->groupBy(
            'msmachine.id',
            'msmachine.department_id',
            'machineno',
            'machinename',
            'code_jam_mati',
            'name_jam_mati'
        )->get();

        if (count($data) == 0) {
            $response = [
                'status' => 'error',
                'message' => "Data pada periode tanggal tersebut tidak ditemukan"
            ];

            return $response;
        }

        $dataFilter = $data->groupBy('machineno');

        // index
        $columnKodeJamMati = 'A';
        $columnNamaJamMati = 'B';
        $columnTotalJamMati = 'C';
        $startRowItem = 4;
        $rowItem = $startRowItem;
        $columnRowTotalByMachine = [];
        // daftar departemen
        foreach ($dataFilter as $machine) {
            $machineData = $machine->first();
            // Menulis data departemen
            $activeWorksheet->setCellValue($columnKodeJamMati . $rowItem, $machineData['machineno'] . ' : ' . $machineData['machinename']);
            phpspreadsheet::styleFont($spreadsheet, $columnKodeJamMati . $rowItem, true, 9, 'Calibri');
            $rowItem++;
            $startRowMachine = $rowItem;
            // daftar mesin
            foreach ($machine as $dataItem) {
                // kode jam mati
                $activeWorksheet->setCellValue($columnKodeJamMati . $rowItem, $dataItem->code_jam_mati);

                // nama jam mati
                $activeWorksheet->setCellValue($columnNamaJamMati . $rowItem, $dataItem->name_jam_mati);

                // jam mati
                $activeWorksheet->setCellValue($columnTotalJamMati . $rowItem, $dataItem->total_off_minutes);
                $rowItem++;
            }
            $rowItemStyling = $rowItem - 1;
            phpspreadsheet::addBorderDottedHorizontal($spreadsheet, $columnKodeJamMati . $startRowMachine . ':' . $columnTotalJamMati . $rowItem);
            phpspreadsheet::styleFont($spreadsheet, $columnKodeJamMati . $startRowMachine . ':' . $columnTotalJamMati . $rowItem, false, 9, 'Calibri');
            phpspreadsheet::textAlignCenter($spreadsheet, $columnKodeJamMati . $startRowMachine . ':' . $columnNamaJamMati . $rowItem);

            // total jam mati
            $spreadsheet->getActiveSheet()->mergeCells($columnKodeJamMati . $rowItem . ':' . $columnNamaJamMati . $rowItem);
            $spreadsheet->getActiveSheet()->setCellValue($columnKodeJamMati . $rowItem, 'TOTAL');
            $activeWorksheet->setCellValue($columnTotalJamMati . $rowItem, '=SUM(' . $columnTotalJamMati . $startRowMachine . ':' . $columnTotalJamMati . $rowItemStyling . ')');
            phpspreadsheet::addFullBorder($spreadsheet, $columnKodeJamMati . $rowItem . ':' . $columnTotalJamMati . $rowItem);
            phpspreadsheet::styleFont($spreadsheet, $columnKodeJamMati . $rowItem . ':' . $columnTotalJamMati . $rowItem, true, 11, 'Calibri');
            $columnRowTotalByMachine[] = $columnTotalJamMati . $rowItem;
            $rowItem++;
        }

        // Grand total
        $rowGrandTotal = $rowItem;
        $spreadsheet->getActiveSheet()->mergeCells($columnKodeJamMati . $rowGrandTotal . ':' . $columnNamaJamMati . $rowGrandTotal);
        $spreadsheet->getActiveSheet()->setCellValue($columnKodeJamMati . $rowGrandTotal, 'GRAND TOTAL');
        phpspreadsheet::styleFont($spreadsheet, $columnKodeJamMati . $rowGrandTotal . ':' . $columnTotalJamMati . $rowGrandTotal, true, 11, 'Calibri');
        phpspreadsheet::textAlignCenter($spreadsheet, $columnKodeJamMati . $rowGrandTotal . ':' . $columnNamaJamMati . $rowGrandTotal);

        $spreadsheet->getActiveSheet()->setCellValue($columnTotalJamMati . $rowGrandTotal, '=SUM(' . implode(',', $columnRowTotalByMachine) . ')');
        phpspreadsheet::addFullBorder($spreadsheet, $columnKodeJamMati . $rowGrandTotal . ':' . $columnTotalJamMati . $rowGrandTotal);

        $columnTotalJamMati++;
        while ($columnKodeJamMati !== $columnTotalJamMati) {
            $spreadsheet->getActiveSheet()->getColumnDimension($columnKodeJamMati)->setAutoSize(true);
            $columnKodeJamMati++;
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'asset/report/' . $nipon . '-' . $jenisReport . '.xlsx';
        $writer->save($filename);
        $response = [
            'status' => 'success',
            'filename' => $filename
        ];
        return $response;
    }

    public static function jamMatiPerJenis($nipon, $jenisReport, $tglMasuk, $tglKeluar)
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

        // Judul
        $activeWorksheet->setCellValue('A1', 'DAFTAR JAM MATI PER JENIS ' . strtoupper($nipon));
        $activeWorksheet->setCellValue('A2', 'Periode : ' . $tglMasuk->format('d-M-Y H:i') . '  ~  ' . $tglKeluar->format('d-M-Y H:i'));
        // Style Judul
        phpspreadsheet::styleFont($spreadsheet, 'A1:A2', true, 11, 'Calibri');

        // header
        $rowHeaderStart = 3;
        $columnHeaderStart = 'A';
        $columnHeaderEnd = 'A';
        $header = [
            'Kode Jam Mati Mesin',
            'Nama Mati Mesin',
            'Jam Mati (Menit)',
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

        $query = MsJamMatiMesin::query()
            ->select(
                'ms_jam_mati_mesin.code',
                'ms_jam_mati_mesin.name',
            )
            ->selectRaw("FLOOR(EXTRACT(EPOCH FROM COALESCE(SUM(jkjmm.off_hour), INTERVAL '0')) / 60) AS total_off_minutes")
            ->join('tdjamkerja_jammatimesin as jkjmm', 'jkjmm.jam_mati_mesin_id', '=', 'ms_jam_mati_mesin.id')
            ->join('tdjamkerjamesin as jkm', 'jkjmm.jam_kerja_mesin_id', '=', 'jkm.id')
            ->join('msworkingshift as ws', 'ws.id', '=', 'jkm.work_shift')
            ->whereRaw("(jkm.working_date + ws.work_hour_from) BETWEEN ? AND ?", [$tglMasuk, $tglKeluar])
            ->where('ms_jam_mati_mesin.status', 1);

        // filter department
        if ($nipon === 'Infure') {
            $query->infureDivision();
        } elseif ($nipon === 'Seitai') {
            $query->seitaiDivision();
        }

        // apply groupBy dan ambil datanya
        $data = $query->groupBy(
            'ms_jam_mati_mesin.code',
            'ms_jam_mati_mesin.name'
        )->get();

        if (count($data) == 0) {
            $response = [
                'status' => 'error',
                'message' => "Data pada periode tanggal tersebut tidak ditemukan"
            ];

            return $response;
        }

        // index
        $columnKodeJamMati = 'A';
        $columnNamaJamMati = 'B';
        $columnTotalJamMati = 'C';
        $startRowItem = 4;
        $rowItem = $startRowItem;
        $columnRowTotalByMachine = [];
        // daftar departemen
        foreach ($data as $dataItem) {
            $startRowMachine = $rowItem;
            // kode jam mati
            $activeWorksheet->setCellValue($columnKodeJamMati . $rowItem, $dataItem->code);

            // nama jam mati
            $activeWorksheet->setCellValue($columnNamaJamMati . $rowItem, $dataItem->name);

            // jam mati
            $activeWorksheet->setCellValue($columnTotalJamMati . $rowItem, $dataItem->total_off_minutes);
            phpspreadsheet::addBorderDottedHorizontal($spreadsheet, $columnKodeJamMati . $startRowMachine . ':' . $columnTotalJamMati . $rowItem);
            phpspreadsheet::styleFont($spreadsheet, $columnKodeJamMati . $startRowMachine . ':' . $columnTotalJamMati . $rowItem, false, 9, 'Calibri');
            phpspreadsheet::textAlignCenter($spreadsheet, $columnKodeJamMati . $startRowMachine . ':' . $columnNamaJamMati . $rowItem);
            $rowItem++;

        }

        // Grand total
        $rowGrandTotal = $rowItem;
        $spreadsheet->getActiveSheet()->mergeCells($columnKodeJamMati . $rowGrandTotal . ':' . $columnNamaJamMati . $rowGrandTotal);
        $spreadsheet->getActiveSheet()->setCellValue($columnKodeJamMati . $rowGrandTotal, 'GRAND TOTAL');
        phpspreadsheet::styleFont($spreadsheet, $columnKodeJamMati . $rowGrandTotal . ':' . $columnTotalJamMati . $rowGrandTotal, true, 11, 'Calibri');
        phpspreadsheet::textAlignCenter($spreadsheet, $columnKodeJamMati . $rowGrandTotal . ':' . $columnNamaJamMati . $rowGrandTotal);

        $spreadsheet->getActiveSheet()->setCellValue($columnTotalJamMati . $rowGrandTotal, '=SUM(' . $columnTotalJamMati . $startRowItem . ':' . $columnTotalJamMati . ($rowItem - 1) . ')');
        phpspreadsheet::addFullBorder($spreadsheet, $columnKodeJamMati . $rowGrandTotal . ':' . $columnTotalJamMati . $rowGrandTotal);

        $columnTotalJamMati++;
        while ($columnKodeJamMati !== $columnTotalJamMati) {
            $spreadsheet->getActiveSheet()->getColumnDimension($columnKodeJamMati)->setAutoSize(true);
            $columnKodeJamMati++;
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'asset/report/' . $nipon . '-' . $jenisReport . '.xlsx';
        $writer->save($filename);
        $response = [
            'status' => 'success',
            'filename' => $filename
        ];
        return $response;
    }
}
