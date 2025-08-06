<?php

namespace App\Http\Livewire\Report\GeneralReport;

use App\Helpers\departmentHelper;
use App\Helpers\phpspreadsheet;
use App\Http\Livewire\MasterTabel\Department;
use App\Models\MsJamMatiMesin;
use App\Models\MsLossClass;
use App\Models\MsMachine;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class LossKasusReportService
{
    public static function daftarLossPerMesinJenis($nipon, $jenisReport, $tglMasuk, $tglKeluar)
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
        $activeWorksheet->setCellValue('A1', 'DAFTAR KASUS PER MESIN & JENIS ' . strtoupper($nipon));
        $activeWorksheet->setCellValue('A2', 'Periode : ' . $tglMasuk . ' s/d ' . $tglKeluar);
        // Style Judul
        phpspreadsheet::styleFont($spreadsheet, 'A1:A2', true, 11, 'Calibri');

        // header
        $rowHeaderStart = 3;
        $rowHeaderEnd = 5;
        $columnHeaderStart = 'A';
        $columnHeaderEnd = 'B';

        $spreadsheet->getActiveSheet()->mergeCells($columnHeaderStart . '3:' . $columnHeaderEnd . '5');
        $activeWorksheet->setCellValue('A3', 'Mesin');
        $activeWorksheet->setCellValue('C3', 'Loss');
        $activeWorksheet->setCellValue('C4', 'Kategori');
        $activeWorksheet->setCellValue('C5', 'Produksi');

        // kasus
        $departmentId = $nipon === 'Infure'
            ? departmentHelper::infureDivision()->id
            : departmentHelper::seitaiDivision()->id;

        $relation = $nipon === 'Infure' ? 'lossInfure' : 'lossSeitai';

        $kasus = MsLossClass::with([$relation => function ($query) {
            $query->select('id', 'name', 'code', 'loss_class_id')
                ->where('status', 1)
                ->orderBy('code');
        }])
            ->select('id', 'name', 'code')
            ->where('department_id', $departmentId)
            ->where('status', 1)
            ->orderBy('code')
            ->get();

        $startColLoss = 'D';
        $startColIndex = Coordinate::columnIndexFromString('D');
        $currentColIndex = $startColIndex;
        $lossClassColIndex = [];
        $lossCategoryColIndex = [];

        foreach ($kasus as $key => $value) {
            $columnHeaderLossStart = Coordinate::stringFromColumnIndex($currentColIndex);

            $activeWorksheet->setCellValue(
                $columnHeaderLossStart . $rowHeaderStart,
                $value->name
            );

            foreach ($value[$relation] as $index => $loss) {
                $lossCategoryColIndex[] = $currentColIndex;
                $colLetter = Coordinate::stringFromColumnIndex($currentColIndex);
                $activeWorksheet->setCellValue($colLetter . ($rowHeaderStart + 1), $loss->code);
                $activeWorksheet->setCellValue($colLetter . ($rowHeaderStart + 2), $loss->name);
                $currentColIndex++;
            }
            $lossClassColIndex[] = $currentColIndex - 1;

            $columnHeaderLossEnd = Coordinate::stringFromColumnIndex($currentColIndex - 1);

            $spreadsheet->getActiveSheet()->mergeCells(
                $columnHeaderLossStart . $rowHeaderStart . ':' . $columnHeaderLossEnd . $rowHeaderStart
            );
        }

        $columnHeaderLossEnd = Coordinate::stringFromColumnIndex($currentColIndex);
        $spreadsheet->getActiveSheet()->mergeCells($columnHeaderLossEnd . '3:' . $columnHeaderLossEnd . '5');
        $activeWorksheet->setCellValue($columnHeaderLossEnd . '3', 'Total');
        $headerTotalLossColIndex = Coordinate::stringFromColumnIndex($currentColIndex + 1);
        $spreadsheet->getActiveSheet()->mergeCells($headerTotalLossColIndex . '3:' . $headerTotalLossColIndex . '5');
        $activeWorksheet->setCellValue($headerTotalLossColIndex . '3', '% Loss');

        // style header
        phpspreadsheet::addFullBorder($spreadsheet, $columnHeaderStart . $rowHeaderStart . ':' . $headerTotalLossColIndex . '5');
        phpspreadsheet::addVerticalBorder($spreadsheet, $startColLoss . $rowHeaderStart + 2 . ':' . $columnHeaderLossEnd . $rowHeaderStart + 2, \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOTTED);
        phpspreadsheet::styleFont($spreadsheet, $columnHeaderStart . $rowHeaderStart . ':' . $headerTotalLossColIndex . $rowHeaderEnd, true, 9, 'Calibri');
        phpspreadsheet::textAlignCenter($spreadsheet, $columnHeaderStart . $rowHeaderStart . ':' . $headerTotalLossColIndex . $rowHeaderEnd);
        phpspreadsheet::textRotateUp($spreadsheet, $startColLoss . $rowHeaderEnd . ':' . $headerTotalLossColIndex . $rowHeaderEnd);

        foreach ($lossClassColIndex as $index => $item) {
            if ($index == 0) {
                phpspreadsheet::addOutlineBorder($spreadsheet, $startColLoss . $rowHeaderStart + 2 . ':' . Coordinate::stringFromColumnIndex($item) . $rowHeaderStart + 2);
                continue;
            }
            phpspreadsheet::addOutlineBorder($spreadsheet, Coordinate::stringFromColumnIndex($lossClassColIndex[$index - 1] + 1) . $rowHeaderStart + 2 . ':' . Coordinate::stringFromColumnIndex($item) . $rowHeaderStart + 2);
        }

        if ($nipon === 'Infure') {
            $produksiPerMesin = DB::table('tdproduct_assembly as tpa')
                ->select('tpa.machine_id', DB::raw('COALESCE(SUM(tpa.berat_produksi), 0) AS total_produksi'))
                ->whereBetween('tpa.production_date', [$tglMasuk, $tglKeluar])
                ->groupBy('tpa.machine_id');

            $data = DB::table('msmachine')
                ->select(
                    'msloss.id as loss_id',
                    'msloss.code',
                    'msmachine.id as machine_id',
                    'msdep.name as department_name',
                    'msmachine.machineno',
                    'msmachine.machinename',
                    DB::raw('COALESCE(produksi.total_produksi, 0) AS total_produksi'),
                    DB::raw('COALESCE(SUM(tpal.berat_loss), 0) AS total_loss')
                )
                ->crossJoin('mslossinfure as msloss')
                ->where('msloss.status', 1)
                ->leftJoin('tdproduct_assembly as tpa', function ($join) use ($tglMasuk, $tglKeluar) {
                    $join->on('tpa.machine_id', '=', 'msmachine.id')
                        ->whereBetween('tpa.production_date', [$tglMasuk, $tglKeluar]);
                })
                ->leftJoin('tdproduct_assembly_loss as tpal', function ($join) {
                    $join->on('tpal.product_assembly_id', '=', 'tpa.id')
                        ->on('tpal.loss_infure_id', '=', 'msloss.id');
                })
                ->leftJoinSub($produksiPerMesin, 'produksi', function ($join) {
                    $join->on('produksi.machine_id', '=', 'msmachine.id');
                })
                ->leftJoin('msdepartment as msdep', 'msdep.id', '=', 'msmachine.department_id')
                ->where('msmachine.status', 1)
                ->whereIn('msmachine.department_id', departmentHelper::infureDepartment())
                ->groupBy(
                    'msloss.id',
                    'msloss.code',
                    'msmachine.id',
                    'msdep.name',
                    'msmachine.machineno',
                    'msmachine.machinename',
                    'produksi.total_produksi'
                )
                ->orderBy('msmachine.machineno', 'asc')
                ->orderBy('msloss.code', 'asc')
                ->get();

            if (count($data) == 0) {
                $response = [
                    'status' => 'error',
                    'message' => "Data pada periode tanggal tersebut tidak ditemukan"
                ];

                return $response;
            }

            $dataFilter = $data
                ->groupBy('department_name')
                ->map(function ($itemsByDept) {
                    return $itemsByDept->groupBy('machineno');
                });
        }

        // index
        $codeMachineCol = 'A';
        $nameMachineCol = 'B';
        $produksiCol = 'C';
        $startLossColIndex = Coordinate::columnIndexFromString('D');
        $itemLossColIndex = Coordinate::columnIndexFromString('D');
        $startRowItem = 6;
        $rowItem = $startRowItem;
        $columnRowTotalProduksi = [];
        $rowTotalLoss = [];
        // daftar departemen
        foreach ($dataFilter as $key => $department) {
            // Menulis data departemen
            $activeWorksheet->setCellValue($codeMachineCol . $rowItem, $key);
            phpspreadsheet::styleFont($spreadsheet, $codeMachineCol . $rowItem, true, 9, 'Calibri');
            $startRowDepartment = $rowItem;
            $rowItem++;

            foreach ($department as $machine) {
                $machineData = $machine->first();
                // Menulis data mesin
                $activeWorksheet->setCellValue($codeMachineCol . $rowItem, $machineData->machineno);
                $activeWorksheet->setCellValue($nameMachineCol . $rowItem, $machineData->machinename);
                // produksi
                $activeWorksheet->setCellValue($produksiCol . $rowItem, $machineData->total_produksi);
                $startRowMachine = $rowItem;
                // daftar mesin

                $itemLossColIndex = $startLossColIndex;

                foreach ($kasus as $key => $value) {
                    foreach ($value[$relation] as $index => $loss) {
                        $colLetter = Coordinate::stringFromColumnIndex($itemLossColIndex);
                        $lossData = $machine->where('loss_id', $loss->id)->first()->total_loss ?? 0;
                        $activeWorksheet->setCellValue($colLetter . $rowItem, $lossData);
                        $itemLossColIndex++;
                    }
                }
                // total loss
                $activeWorksheet->setCellValue(Coordinate::stringFromColumnIndex($itemLossColIndex) . $rowItem, '=SUM(' . Coordinate::stringFromColumnIndex($startLossColIndex) . $rowItem . ':' . Coordinate::stringFromColumnIndex($itemLossColIndex - 1) . $rowItem . ')');

                $itemLossColIndex++;
                // % loss
                $activeWorksheet->setCellValue(Coordinate::stringFromColumnIndex($itemLossColIndex) . $rowItem, '=IF(' . $produksiCol . $rowItem . ' = 0, 0, ' . Coordinate::stringFromColumnIndex($itemLossColIndex - 1) . $rowItem . '/' . $produksiCol . $rowItem . ')');
                phpspreadsheet::numberPercentage($spreadsheet, Coordinate::stringFromColumnIndex($itemLossColIndex) . $rowItem);

                phpspreadsheet::styleFont($spreadsheet, $codeMachineCol . $startRowMachine . ':' . Coordinate::stringFromColumnIndex($itemLossColIndex) . $rowItem, false, 9, 'Calibri');
                $rowItem++;
            }
            phpspreadsheet::addInlineBorderDotted($spreadsheet, $codeMachineCol . $startRowDepartment + 1 . ':' . Coordinate::stringFromColumnIndex($itemLossColIndex) . $rowItem);
            phpspreadsheet::addVerticalBorder($spreadsheet, $codeMachineCol . $startRowDepartment + 1 . ':' . Coordinate::stringFromColumnIndex($startLossColIndex) . $rowItem);

            $rowTotalLossPerDepartment = $rowItem + 1;
            // total loss per loss class per departemen
            $rowTotalLoss[] = $rowItem;
            foreach ($lossCategoryColIndex as $index => $item) {
                $activeWorksheet->setCellValue(Coordinate::stringFromColumnIndex($item) . $rowItem, '=SUM(' . Coordinate::stringFromColumnIndex($item) . $startRowDepartment + 1 . ':' . Coordinate::stringFromColumnIndex($item) . $rowItem - 1 . ')');
            }
            foreach ($lossClassColIndex as $index => $item) {
                if ($index == 0) {
                    phpspreadsheet::addOutlineBorder($spreadsheet, Coordinate::stringFromColumnIndex($startLossColIndex) . $startRowDepartment + 1 . ':' . Coordinate::stringFromColumnIndex($item) . $rowItem);
                    $spreadsheet->getActiveSheet()->mergeCells(Coordinate::stringFromColumnIndex($startLossColIndex) . $rowTotalLossPerDepartment . ':' . Coordinate::stringFromColumnIndex($item) . $rowTotalLossPerDepartment);
                    $activeWorksheet->setCellValue(Coordinate::stringFromColumnIndex($startLossColIndex) . $rowTotalLossPerDepartment, '=SUM(' . Coordinate::stringFromColumnIndex($startLossColIndex) . $rowItem . ':' . Coordinate::stringFromColumnIndex($item) . $rowItem . ')');
                    continue;
                }
                phpspreadsheet::addOutlineBorder($spreadsheet, Coordinate::stringFromColumnIndex($lossClassColIndex[$index - 1] + 1) . $startRowDepartment + 1 . ':' . Coordinate::stringFromColumnIndex($item) . $rowItem);
                $spreadsheet->getActiveSheet()->mergeCells(Coordinate::stringFromColumnIndex($lossClassColIndex[$index - 1] + 1) . $rowTotalLossPerDepartment . ':' . Coordinate::stringFromColumnIndex($item) . $rowTotalLossPerDepartment);
                $activeWorksheet->setCellValue(Coordinate::stringFromColumnIndex($lossClassColIndex[$index - 1] + 1) . $rowTotalLossPerDepartment, '=SUM(' . Coordinate::stringFromColumnIndex($lossClassColIndex[$index - 1] + 1) . $rowItem . ':' . Coordinate::stringFromColumnIndex($item) . $rowItem . ')');
            }
            $rowItemStyling = $rowItem;

            $rowItem++;
            // total produksi per departemen
            $spreadsheet->getActiveSheet()->mergeCells($codeMachineCol . $rowItemStyling . ':' . $nameMachineCol . $rowItem);
            $spreadsheet->getActiveSheet()->setCellValue($codeMachineCol . $rowItemStyling, 'TOTAL');
            phpspreadsheet::textAlignCenter($spreadsheet, $codeMachineCol . $rowItemStyling);

            $activeWorksheet->setCellValue($produksiCol . $rowItemStyling, '=SUM(' . $produksiCol . $startRowDepartment . ':' . $produksiCol . $rowItemStyling - 1 . ')');
            $spreadsheet->getActiveSheet()->mergeCells($produksiCol . $rowItemStyling . ':' . $produksiCol . $rowItem);
            phpspreadsheet::textAlignRight($spreadsheet, $produksiCol . $rowItemStyling);
            $columnRowTotalProduksi[] = $produksiCol . $rowItemStyling;


            phpspreadsheet::addFullBorder($spreadsheet, $codeMachineCol . $rowItemStyling . ':' . Coordinate::stringFromColumnIndex($itemLossColIndex) . $rowItem);
            phpspreadsheet::styleFont($spreadsheet, $codeMachineCol . $rowItemStyling . ':' . Coordinate::stringFromColumnIndex($itemLossColIndex) . $rowItem, true, 8, 'Calibri');
            $rowItem++;
            $rowItem++;
        }

        // Grand total
        $itemLossColLetter = Coordinate::stringFromColumnIndex($itemLossColIndex);
        $rowGrandTotal = $rowItem;
        $spreadsheet->getActiveSheet()->mergeCells($codeMachineCol . $rowGrandTotal . ':' . $nameMachineCol . $rowGrandTotal);
        $spreadsheet->getActiveSheet()->setCellValue($codeMachineCol . $rowGrandTotal, 'GRAND TOTAL');
        phpspreadsheet::styleFont($spreadsheet, $codeMachineCol . $rowGrandTotal . ':' . $itemLossColLetter . $rowGrandTotal, true, 8, 'Calibri');
        phpspreadsheet::textAlignCenter($spreadsheet, $codeMachineCol . $rowGrandTotal . ':' . $itemLossColLetter . $rowGrandTotal);

        $spreadsheet->getActiveSheet()->setCellValue($produksiCol . $rowGrandTotal, '=SUM(' . implode(',', $columnRowTotalProduksi) . ')');

        foreach ($lossCategoryColIndex as $item) {
            $columnRowTotalLoss = [];
            foreach ($rowTotalLoss as $value) {
                $columnRowTotalLoss[] = Coordinate::stringFromColumnIndex($item) . $value;
            }
            $spreadsheet->getActiveSheet()->setCellValue(Coordinate::stringFromColumnIndex($item) . $rowGrandTotal, '=SUM(' . implode(',', $columnRowTotalLoss) . ')');
        }
        phpspreadsheet::addFullBorder($spreadsheet, $codeMachineCol . $rowGrandTotal . ':' . $itemLossColLetter . $rowGrandTotal);

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
        $activeWorksheet->setCellValue('A2', 'Periode : ' . $tglMasuk . ' s/d ' . $tglKeluar);
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
