<?php

namespace App\Http\Livewire\Report\GeneralReport;

use App\Helpers\phpspreadsheet;
use App\Models\MsJamMatiMesin;
use App\Models\MsMachine;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
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
        )
            ->orderBy('total_off_minutes', 'DESC')
            ->get();

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
        $activeWorksheet->freezePane('E5'); // Freeze after masalah columns

        // header
        $rowHeaderStart = 3;
        $rowHeaderEnd = 4;
        $columnHeaderStart = 'A';
        $columnHeaderEnd = 'A';
        $header = [
            'Kode Jam Mati Mesin',
            'Nama Mati Mesin',
        ];

        foreach ($header as $key => $value) {
            $activeWorksheet->setCellValue($columnHeaderEnd . $rowHeaderStart, $value);
            $columnHeaderEnd++;
        }

        // merge column A3:A4
        $spreadsheet->getActiveSheet()->mergeCells($columnHeaderStart . $rowHeaderStart . ':' . $columnHeaderStart . $rowHeaderEnd);
        $spreadsheet->getActiveSheet()->mergeCells(chr(ord($columnHeaderStart) + 1) . $rowHeaderStart . ':' . chr(ord($columnHeaderStart) + 1) . $rowHeaderEnd);

        // tambahkan header total jam mati
        $activeWorksheet->setCellValue($columnHeaderEnd . $rowHeaderStart, 'Total Jam Mati (Menit)');
        $spreadsheet->getActiveSheet()->mergeCells($columnHeaderEnd . $rowHeaderStart . ':' . chr(ord($columnHeaderEnd) + 1) . $rowHeaderStart);
        $activeWorksheet->setCellValue($columnHeaderEnd . $rowHeaderEnd, 'Jam');
        $columnHeaderEnd++;
        $activeWorksheet->setCellValue($columnHeaderEnd . $rowHeaderEnd, 'Menit');
        $columnHeaderEnd++;

        $query = MsJamMatiMesin::query()
            ->select(
                'ms_jam_mati_mesin.code as kode_mati_mesin',
                'ms_jam_mati_mesin.name as nama_mati_mesin',
                'msmachine.machineno'
            )
            ->selectRaw("
            FLOOR(EXTRACT(EPOCH FROM COALESCE(SUM(jkjmm.off_hour), INTERVAL '0')) / 3600) AS total_off_hours,
            FLOOR(
                (EXTRACT(EPOCH FROM COALESCE(SUM(jkjmm.off_hour), INTERVAL '0')) / 60)
                - FLOOR(EXTRACT(EPOCH FROM COALESCE(SUM(jkjmm.off_hour), INTERVAL '0')) / 3600) * 60
            ) AS total_off_minutes,
            FLOOR(EXTRACT(EPOCH FROM COALESCE(SUM(jkjmm.off_hour), INTERVAL '0')) / 60) AS total_off_overall_minutes,
            FLOOR(EXTRACT(EPOCH FROM COALESCE(SUM(jkm.on_hour), INTERVAL '0')) / 3600) AS total_on_hours,
            FLOOR(
                (EXTRACT(EPOCH FROM COALESCE(SUM(jkm.on_hour), INTERVAL '0')) / 60)
                - FLOOR(EXTRACT(EPOCH FROM COALESCE(SUM(jkm.on_hour), INTERVAL '0')) / 3600) * 60
            ) AS total_on_minutes,
            FLOOR(EXTRACT(EPOCH FROM COALESCE(SUM(jkm.on_hour), INTERVAL '0')) / 60) AS total_on_overall_minutes
            ")
            ->join('tdjamkerja_jammatimesin as jkjmm', 'jkjmm.jam_mati_mesin_id', '=', 'ms_jam_mati_mesin.id')
            ->join('tdjamkerjamesin as jkm', 'jkjmm.jam_kerja_mesin_id', '=', 'jkm.id')
            ->join('msmachine', 'msmachine.id', '=', 'jkm.machine_id')
            ->join('msworkingshift as ws', 'ws.id', '=', 'jkm.work_shift')
            ->whereRaw("(jkm.working_date + ws.work_hour_from) BETWEEN ? AND ?", [$tglMasuk, $tglKeluar])
            ->where('ms_jam_mati_mesin.status', 1);

        // filter department
        if ($nipon === 'Infure') {
            $query->infureDivision();
            // machine
            $machines = MsMachine::infureDepartment()
                ->active()
                ->orderBy('machineno', 'ASC')
                ->get()
                ->pluck('machineno');
        } elseif ($nipon === 'Seitai') {
            $query->seitaiDivision();
            // machine
            $machines = MsMachine::seitaiDepartment()
                ->active()
                ->orderBy('machineno', 'ASC')
                ->get()
                ->pluck('machineno');
        }

        // apply groupBy dan ambil datanya
        $data = $query->groupBy(
            'ms_jam_mati_mesin.code',
            'ms_jam_mati_mesin.name',
            'msmachine.machineno'
        )->get();

        if (count($data) == 0) {
            $response = [
                'status' => 'error',
                'message' => "Data pada periode tanggal tersebut tidak ditemukan"
            ];

            return $response;
        }

        // Write headers to Excel
        $columnHeaderEnd = Coordinate::columnIndexFromString($columnHeaderEnd);
        foreach ($machines as $machine) {
            $columnHeader = Coordinate::stringFromColumnIndex($columnHeaderEnd);
            $activeWorksheet->setCellValue($columnHeader . $rowHeaderStart, $machine);
            $nextColumn = Coordinate::stringFromColumnIndex($columnHeaderEnd + 1);
            $spreadsheet->getActiveSheet()->mergeCells($columnHeader . $rowHeaderStart . ':' . $nextColumn . $rowHeaderStart);
            $activeWorksheet->setCellValue($columnHeader . $rowHeaderEnd, 'Jam');
            $columnHeaderEnd++;
            $columnHeader = Coordinate::stringFromColumnIndex($columnHeaderEnd);
            $activeWorksheet->setCellValue($columnHeader . $rowHeaderEnd, 'Menit');
            $columnHeaderEnd++;
        }

        // style header
        $columnHeader = Coordinate::stringFromColumnIndex($columnHeaderEnd);
        phpspreadsheet::addFullBorder($spreadsheet, $columnHeaderStart . $rowHeaderStart . ':' . $columnHeader . $rowHeaderEnd);
        phpspreadsheet::styleFont($spreadsheet, $columnHeaderStart . $rowHeaderStart . ':' . $columnHeader . $rowHeaderEnd, true, 9, 'Calibri');
        phpspreadsheet::textAlignCenter($spreadsheet, $columnHeaderStart . $rowHeaderStart . ':' . $columnHeader . $rowHeaderEnd);

        $jamMatiData = [];
        foreach ($data as $item) {
            $codeJamMati = $item->kode_mati_mesin;
            // Initialize container for this kode if not exists (ensure aggregate keys exist)
            if (!isset($jamMatiData[$codeJamMati])) {
                $jamMatiData[$codeJamMati] = [
                    'code' => $item->kode_mati_mesin,
                    'name' => $item->nama_mati_mesin,
                    'off_overall_minutes' => 0,
                    'on_overall_minutes' => 0,
                ];
            }
            // Store per-machine breakdown and ensure numeric types
            $jamMatiData[$codeJamMati][$item->machineno] = [
                'hours' => intval($item->total_off_hours),
                'minutes' => intval($item->total_off_minutes),
                'off_overall_minutes' => intval($item->total_off_overall_minutes),
                'on_overall_minutes' => intval($item->total_on_overall_minutes),
                'on_hours' => intval($item->total_on_hours),
                'on_minutes' => intval($item->total_on_minutes),
            ];
            // Accumulate overall minutes for the kode
            $jamMatiData[$codeJamMati]['off_overall_minutes'] += intval($item->total_off_overall_minutes);
            $jamMatiData[$codeJamMati]['on_overall_minutes'] += intval($item->total_on_overall_minutes);
        }

        // index
        $columnKodeJamMati = 'A';
        $columnNamaJamMati = 'B';
        $columnTotalJamMati = 'C';
        $columnTotalMenitMati = 'D';
        $startRowItem = 5;
        $rowItem = $startRowItem;
        // daftar departemen
        foreach ($jamMatiData as $dataItem) {
            // kode jam mati
            $activeWorksheet->setCellValue($columnKodeJamMati . $rowItem, $dataItem['code']);

            // nama jam mati
            $activeWorksheet->setCellValue($columnNamaJamMati . $rowItem, $dataItem['name']);

            // total jam mati - jam
            $activeWorksheet->setCellValue($columnTotalJamMati . $rowItem, $dataItem['off_overall_minutes'] >= 60 ? floor($dataItem['off_overall_minutes'] / 60) : 0);

            // total jam mati - menit
            $activeWorksheet->setCellValue($columnTotalMenitMati . $rowItem, $dataItem['off_overall_minutes'] < 60 ? $dataItem['off_overall_minutes'] : ($dataItem['off_overall_minutes'] % 60));

            phpspreadsheet::addXHorizontalVerticalBorder($spreadsheet, $columnTotalJamMati . $rowItem . ':' . $columnTotalMenitMati . $rowItem, \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_HAIR);

            // total jam mati per mesin
            $columnTotalByMachine = [];
            $columnHeaderEnd = Coordinate::columnIndexFromString('E');
            foreach ($machines as $machine) {
                $columnHeaderStartMachine = Coordinate::stringFromColumnIndex($columnHeaderEnd);
                if (isset($jamMatiData[$dataItem['code']][$machine])) {
                    // jam
                    $activeWorksheet->setCellValue($columnHeaderStartMachine . $rowItem, $jamMatiData[$dataItem['code']][$machine]['hours']);
                    $columnHeaderEnd++;
                    $columnHeaderEndMachine = Coordinate::stringFromColumnIndex($columnHeaderEnd);
                    // menit
                    $activeWorksheet->setCellValue($columnHeaderEndMachine . $rowItem, $jamMatiData[$dataItem['code']][$machine]['minutes']);
                    $columnTotalByMachine[] = $columnHeaderEndMachine . $rowItem;
                    $columnHeaderEnd++;
                } else {
                    // jam
                    $activeWorksheet->setCellValue($columnHeaderStartMachine . $rowItem, 0);
                    $columnHeaderEnd++;
                    $columnHeaderEndMachine = Coordinate::stringFromColumnIndex($columnHeaderEnd);
                    // menit
                    $activeWorksheet->setCellValue($columnHeaderEndMachine . $rowItem, 0);
                    $columnTotalByMachine[] = $columnHeaderEndMachine . $rowItem;
                    $columnHeaderEnd++;
                }

                phpspreadsheet::addXHorizontalVerticalBorder($spreadsheet, $columnHeaderStartMachine . $rowItem . ':' . Coordinate::stringFromColumnIndex($columnHeaderEnd) . $rowItem, \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_HAIR);
            }
            $rowItem++;
        }
        $columnHeader = Coordinate::stringFromColumnIndex($columnHeaderEnd - 1);
        phpspreadsheet::addBorderDottedMiddleHorizontal($spreadsheet, $columnKodeJamMati . $startRowItem . ':' . $columnNamaJamMati . $rowItem);
        phpspreadsheet::styleFont($spreadsheet, $columnKodeJamMati . $startRowItem . ':' . $columnHeader . $rowItem, false, 9, 'Calibri');
        phpspreadsheet::textAlignCenter($spreadsheet, $columnKodeJamMati . $startRowItem . ':' . $columnKodeJamMati . $rowItem);

        // Grand total
        $rowGrandTotal = $rowItem;
        $spreadsheet->getActiveSheet()->mergeCells($columnKodeJamMati . $rowGrandTotal . ':' . $columnNamaJamMati . $rowGrandTotal);
        $spreadsheet->getActiveSheet()->setCellValue($columnKodeJamMati . $rowGrandTotal, 'GRAND TOTAL');
        // grand total - jam (handle minutes overflow by converting total minutes into hours)
        $activeWorksheet->setCellValue($columnTotalJamMati . $rowGrandTotal, '=INT((SUM(' . $columnTotalJamMati . $startRowItem . ':' . $columnTotalJamMati . ($rowItem - 1) . ')*60 + SUM(' . $columnTotalMenitMati . $startRowItem . ':' . $columnTotalMenitMati . ($rowItem - 1) . '))/60)');
        // grand total - menit (remaining minutes after converting to hours)
        $activeWorksheet->setCellValue($columnTotalMenitMati . $rowGrandTotal, '=MOD(SUM(' . $columnTotalJamMati . $startRowItem . ':' . $columnTotalJamMati . ($rowItem - 1) . ')*60 + SUM(' . $columnTotalMenitMati . $startRowItem . ':' . $columnTotalMenitMati . ($rowItem - 1) . '),60)');

        $colIndex = Coordinate::columnIndexFromString('E');
        foreach ($machines as $machine) {
            // Hours column for this machine
            $colHours = Coordinate::stringFromColumnIndex($colIndex);
            $colIndex++;
            // Minutes column for this machine
            $colMinutes = Coordinate::stringFromColumnIndex($colIndex);
            $colIndex++;

            // Ranges for summation
            $hoursRange = $colHours . $startRowItem . ':' . $colHours . ($rowItem - 1);
            $minutesRange = $colMinutes . $startRowItem . ':' . $colMinutes . ($rowItem - 1);

            // Total minutes expression: SUM(hours)*60 + SUM(minutes)
            $totalMinutesExpr = 'SUM(' . $hoursRange . ')*60 + SUM(' . $minutesRange . ')';

            // Grand total per machine: convert total minutes to hours and remaining minutes
            $activeWorksheet->setCellValue($colHours . $rowGrandTotal, '=INT((' . $totalMinutesExpr . ')/60)');
            $activeWorksheet->setCellValue($colMinutes . $rowGrandTotal, '=MOD((' . $totalMinutesExpr . '),60)');
        }

        // Jam kerja mesin
        $rowGrandTotal++;
        $spreadsheet->getActiveSheet()->mergeCells($columnKodeJamMati . $rowGrandTotal . ':' . $columnNamaJamMati . $rowGrandTotal);
        $spreadsheet->getActiveSheet()->setCellValue($columnKodeJamMati . $rowGrandTotal, 'Jam Kerja Mesin');

        // jam kerja mesin dari on hour data - jam
        // jam kerja mesin dari on hour data on_overall_minutes pada kolom columnTotalJamMati (jam), dan columnTotalMenitMati (menit)
        $totalOnOverallMinutes = 0;
        foreach ($jamMatiData as $code => $codeData) {
            if (isset($codeData['on_overall_minutes'])) {
                $totalOnOverallMinutes += intval($codeData['on_overall_minutes']);
            } else {
                // fallback: jika tidak tersedia agregat, jumlahkan dari tiap mesin pada kode tersebut
                foreach ($machines as $m) {
                    if (isset($codeData[$m])) {
                        $onH = isset($codeData[$m]['on_hours']) ? intval($codeData[$m]['on_hours']) : 0;
                        $onM = isset($codeData[$m]['on_minutes']) ? intval($codeData[$m]['on_minutes']) : 0;
                        $totalOnOverallMinutes += ($onH * 60) + $onM;
                    }
                }
            }
        }

        $hoursTotal = intdiv($totalOnOverallMinutes, 60);
        $minutesTotal = $totalOnOverallMinutes % 60;

        $activeWorksheet->setCellValue($columnTotalJamMati . $rowGrandTotal, $hoursTotal);
        $activeWorksheet->setCellValue($columnTotalMenitMati . $rowGrandTotal, $minutesTotal);
        // Hitung jam kerja mesin berdasarkan on_overall_minutes per mesin dan tulis ke baris Jam Kerja Mesin
        $colIndex = Coordinate::columnIndexFromString('E');
        foreach ($machines as $machine) {
            $totalOnMinutes = 0;
            foreach ($jamMatiData as $code => $codeData) {
                if (isset($codeData[$machine])) {
                    $onHours = isset($codeData[$machine]['on_hours']) ? intval($codeData[$machine]['on_hours']) : 0;
                    $onMinutes = isset($codeData[$machine]['on_minutes']) ? intval($codeData[$machine]['on_minutes']) : 0;
                    $totalOnMinutes += ($onHours * 60) + $onMinutes;
                }
            }

            $hours = intdiv($totalOnMinutes, 60);
            $minutes = $totalOnMinutes % 60;

            $colHours = Coordinate::stringFromColumnIndex($colIndex);
            $colIndex++;
            $colMinutes = Coordinate::stringFromColumnIndex($colIndex);
            $colIndex++;

            $activeWorksheet->setCellValue($colHours . $rowGrandTotal, $hours);
            $activeWorksheet->setCellValue($colMinutes . $rowGrandTotal, $minutes);
        }

        // Kadou jikan
        $rowGrandTotal++;
        $spreadsheet->getActiveSheet()->mergeCells($columnKodeJamMati . $rowGrandTotal . ':' . $columnNamaJamMati . $rowGrandTotal);
        $spreadsheet->getActiveSheet()->setCellValue($columnKodeJamMati . $rowGrandTotal, 'Kadou Jikan (%)');
        // kadou jikan = jam kerja mesin
        $spreadsheet->getActiveSheet()->mergeCells($columnTotalJamMati . $rowGrandTotal . ':' . $columnTotalMenitMati . $rowGrandTotal);
        // hitungan kadou jikan = on hour dibagi dengan on hour + off hour (grand total )
        $activeWorksheet->setCellValue($columnTotalJamMati . $rowGrandTotal, '=IF((' . $columnTotalJamMati . ($rowGrandTotal - 1) . '*60 + ' . $columnTotalMenitMati . ($rowGrandTotal - 1) . ') = 0, 0, ROUND((' . $columnTotalJamMati . ($rowGrandTotal - 1) . '*60 + ' . $columnTotalMenitMati . ($rowGrandTotal - 1) . ')/(' . $columnTotalJamMati . ($rowGrandTotal - 1) . '*60 + ' . $columnTotalMenitMati . ($rowGrandTotal - 1) . '+' . $columnTotalJamMati . ($rowGrandTotal - 2) . '*60 + ' . $columnTotalMenitMati . ($rowGrandTotal - 2) . ')*100,2))');

        // lajutkan untuk tiap mesin
        $colIndex = Coordinate::columnIndexFromString('E');
        foreach ($machines as $machine) {
            $colHours = Coordinate::stringFromColumnIndex($colIndex);
            $colIndex++;
            $colMinutes = Coordinate::stringFromColumnIndex($colIndex);
            $colIndex++;
            // jam kerja mesin per mesin
            $spreadsheet->getActiveSheet()->mergeCells($colHours . $rowGrandTotal . ':' . $colMinutes . $rowGrandTotal);
            $activeWorksheet->setCellValue($colHours . $rowGrandTotal, '=IF((' . $colHours . ($rowGrandTotal - 1) . '*60 + ' . $colMinutes . ($rowGrandTotal - 1) . ') = 0, 0, ROUND((' . $colHours . ($rowGrandTotal - 1) . '*60 + ' . $colMinutes . ($rowGrandTotal - 1) . ')/(' . $colHours . ($rowGrandTotal - 1) . '*60 + ' . $colMinutes . ($rowGrandTotal - 1) . '+' . $colHours . ($rowGrandTotal - 2) . '*60 + ' . $colMinutes . ($rowGrandTotal - 2) . ')*100,2))');
        }
        $columnEnd = Coordinate::stringFromColumnIndex($colIndex - 1);


        phpspreadsheet::styleFont($spreadsheet, $columnKodeJamMati . $rowItem . ':' . $columnEnd . $rowGrandTotal, true, 11, 'Calibri');
        phpspreadsheet::textAlignCenter($spreadsheet, $columnKodeJamMati . $rowItem . ':' . $columnNamaJamMati . $rowGrandTotal);
        phpspreadsheet::addFullBorder($spreadsheet, $columnKodeJamMati . $rowItem . ':' . $columnEnd . $rowGrandTotal);

        // wrap text untuk kolom kode dan nama jam mati
        $activeWorksheet->getStyle($columnKodeJamMati . $rowHeaderStart . ':' . $columnNamaJamMati . $rowGrandTotal)
            ->getAlignment()
            ->setWrapText(true);

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
