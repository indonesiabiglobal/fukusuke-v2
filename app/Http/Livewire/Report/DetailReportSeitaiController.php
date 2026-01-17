<?php

namespace App\Http\Livewire\Report;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DetailReportSeitaiController
{
    private $spreadsheet;
    private $worksheet;
    private $currentRow = 1;
    private $styleCache = [];

    public function generateReport($tglAwal, $tglAkhir, $filters)
    {
        // Inisialisasi spreadsheet dengan pengaturan optimal
        $this->initSpreadsheet();

        // Ambil data dengan query yang dioptimasi
        $data = $this->getOptimizedData($tglAwal, $tglAkhir, $filters);

        if (empty($data)) {
            throw new \Exception("Data dengan filter tersebut tidak ditemukan");
        }

        // Proses data sekali jalan untuk mengurangi loop
        $processedData = $this->preprocessData($data);

        // Tulis report dengan metode yang dioptimasi
        $this->writeReport($tglAwal, $tglAkhir, $filters, $processedData);

        // Simpan file
        return $this->saveReport($filters['nippo']);
    }

    private function initSpreadsheet()
    {
        $this->spreadsheet = new Spreadsheet();
        $this->worksheet = $this->spreadsheet->getActiveSheet();
        $this->worksheet->setShowGridlines(false);
        $this->worksheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $activeWorksheet = $this->spreadsheet->getActiveSheet();

        // Mengatur ukuran kertas menjadi A4
        $activeWorksheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
        // Mengatur agar semua kolom muat dalam satu halaman
        $activeWorksheet->getPageSetup()->setFitToWidth(1);
        $activeWorksheet->getPageSetup()->setFitToHeight(0); // Biarkan tinggi menyesuaikan otomatis
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

        // Optimalkan penggunaan memori
        $this->spreadsheet->getProperties()
            ->setTitle('Detail Produksi Seitai')
            ->setCreator('System');

        // Nonaktifkan perhitungan otomatis
        $this->spreadsheet->getCalculationEngine()->disableCalculationCache();
    }

    private function getOptimizedData($tglAwal, $tglAkhir, $filters)
    {
        // Gunakan query builder untuk performa lebih baik
        $filterDate = "tdpg.created_on BETWEEN '$tglAwal' AND '$tglAkhir'";
        $filterNoLPK = isset($filters['lpk_no']) ? " AND (tdol.lpk_no = '{$filters['lpk_no']}')" : '';
        $nomorOrder = isset($filters['nomorOrder']) ? " AND (msp.code = '{$filters['nomorOrder']}')" : '';
        $filters['departmentId'] = isset($filters['departmentId']) ? $filters['departmentId'] : '';
        $filterDepartment = $filters['departmentId'] ? " AND (msd.id = '{$filters['departmentId']}')" : '';
        $filters['machineId'] = isset($filters['machineId']) ? $filters['machineId'] : '';
        $filterMachine = $filters['machineId'] ? " AND (tdpg.machine_id = '{$filters['machineId']}')" : '';
        $filterNomorPalet = $filters['nomorPalet'] ? " AND (tdpg.nomor_palet = '{$filters['nomorPalet']}')" : '';
        $filterNomorLot = $filters['nomorLot'] ? " AND (tdpg.nomor_lot = '{$filters['nomorLot']}')" : '';

        return DB::select(
            "
                WITH goodasy AS (
                SELECT
                    tpga.id,
                    tpga.product_goods_id,
                    tdpa.gentan_no || '-' || tpga.gentan_line AS gentannomorline,
                    tdpa.gentan_no AS gentannomor,
                    tdpa.panjang_produksi,
                    tdpa.production_date AS tglproduksi,
                    tdpa.work_shift,
                    tdpa.work_hour,
                    msm.machineno AS nomesin,
                    tdpa.nomor_han,
                    mse.employeeno AS nik,
                    mse.empname AS namapetugas,
                    msd.NAME AS deptpetugas,
                    tdpg.infure_berat_loss AS infure_berat_loss
                FROM
                    tdproduct_goods_assembly AS tpga
                    INNER JOIN tdproduct_assembly AS tdpa ON tdpa.ID = tpga.product_assembly_id
                    INNER JOIN tdproduct_goods AS tdpg ON tdpg.ID = tpga.product_goods_id
                    INNER JOIN msmachine AS msm ON msm.ID = tdpa.machine_id
                    INNER JOIN msemployee AS mse ON mse.ID = tdpa.employee_id
                    INNER JOIN msDepartment AS msd ON msd.ID = msm.department_id
                ),
                lossgoods AS (
                SELECT
                    tpgl.id,
                    tpgl.product_goods_id,
                    msls.code,
                    msls.NAME AS namaloss,
                    tpgl.berat_loss
                FROM
                    tdproduct_goods_loss AS tpgl
                    INNER JOIN mslossseitai AS msls ON msls.ID = tpgl.loss_seitai_id
                ) SELECT
                tdpg.production_no AS production_no,
                tdpg.production_date AS production_date,
                msp.code AS produk_code,
                msp.NAME AS namaproduk,
                tdpg.employee_id AS employee_id,
                mse.employeeno AS nik,
                mse.empname AS namapetugas,
                msd.NAME AS deptpetugas,
                tdpg.work_shift AS work_shift,
                tdpg.work_hour AS work_hour,
                tdpg.machine_id AS machine_id,
                msm.machineno AS nomesin,
                msm.machinename AS namamesin,
                tdol.lpk_no,
                tdpg.nomor_palet AS nomor_palet,
                tdpg.nomor_lot AS nomor_lot,
                tdpg.qty_produksi AS qty_produksi,
                lossgoods.id as loss_id,
                lossgoods.code as loss_code_loss,
                lossgoods.namaloss AS loss_name_loss,
                lossgoods.berat_loss AS berat_loss_loss,
                goodasy.id AS gentan_id_asy,
                goodasy.gentannomorline AS gentan_no_line_asy,
                goodasy.gentannomor AS gentan_no_asy,
                goodasy.panjang_produksi AS panjang_produksi_asy,
                goodasy.tglproduksi AS tgl_produksi_asy,
                goodasy.work_shift AS work_shift_asy,
                goodasy.work_hour AS work_hour_asy,
                goodasy.nomesin AS no_mesin_asy,
                goodasy.nomor_han AS nomor_han_asy,
                goodasy.nik AS nik_asy,
                goodasy.namapetugas AS nama_petugas_asy,
                goodasy.deptpetugas AS dept_petugas_asy,
                goodasy.infure_berat_loss AS infure_berat_loss
            FROM
                tdProduct_Goods AS tdpg
                LEFT JOIN goodasy ON tdpg.ID = goodasy.product_goods_id
                LEFT JOIN lossgoods ON tdpg.ID = lossgoods.product_goods_id
                INNER JOIN tdOrderLpk AS tdol ON tdpg.lpk_id = tdol.
                ID INNER JOIN msmachine AS msm ON msm.ID = tdpg.machine_id
                INNER JOIN msemployee AS mse ON mse.ID = tdpg.employee_id
                INNER JOIN msDepartment AS msd ON msd.ID = msm.department_id
                INNER JOIN msProduct AS msp ON msp.ID = tdpg.product_id
            WHERE
                $filterDate
                $filterNoLPK
                $nomorOrder
                $filterDepartment
                $filterMachine
                $filterNomorPalet
                $filterNomorLot
            ",
        );
    }

    private function preprocessData($data)
    {
        $processed = [
            'products' => [],
            'productionDates' => [],
            'losses' => [],
            'gentan' => [],
            'totals' => [
                'qty_produksi' => 0,
                'berat_loss' => 0,
                'panjang_produksi' => 0,
                'infure_berat_loss' => 0
            ]
        ];

        foreach ($data as $row) {
            $productCode = $row->produk_code;
            $date = $row->production_date;

            // Organize products
            if (!isset($processed['products'][$productCode])) {
                $processed['products'][$productCode] = $row->produk_code . ' - ' . $row->namaproduk;
            }

            // Organize production dates
            if (!isset($processed['productionDates'][$productCode][$date])) {
                $processed['productionDates'][$productCode][$date] = $this->formatBaseData($row);
                $processed['totals']['qty_produksi'] += $row->qty_produksi ?? 0;
            }

            // Organize losses
            if (!isset($processed['losses'][$productCode][$date][$row->loss_id]) && $row->loss_id) {
                $processed['losses'][$productCode][$date][$row->loss_id] = [
                    'name' => $row->loss_name_loss,
                    'weight' => $row->berat_loss_loss
                ];
                $processed['totals']['berat_loss'] += $row->berat_loss_loss ?? 0;
            }

            // Organize gentan data
            if (!isset($processed['gentan'][$productCode][$date][$row->gentan_id_asy]) && $row->gentan_id_asy) {
                $processed['gentan'][$productCode][$date][$row->gentan_id_asy] = $this->formatGentanData($row);
                $processed['totals']['panjang_produksi'] += $row->panjang_produksi_asy ?? 0;
                $processed['totals']['infure_berat_loss'] += $row->infure_berat_loss ?? 0;
            }
        }

        return $processed;
    }

    private function formatBaseData($row)
    {
        return [
            'production_date' => $row->production_date,
            'namapetugas' => $row->namapetugas,
            'deptpetugas' => $row->deptpetugas,
            'nomesin' => $row->nomesin . ' - ' . $row->namamesin,
            'lpk_no' => $row->lpk_no,
            'work_shift' => $row->work_shift,
            'work_hour' => $row->work_hour,
            'nomor_palet' => $row->nomor_palet,
            'nomor_lot' => $row->nomor_lot,
            'qty_produksi' => $row->qty_produksi
        ];
    }

    private function formatGentanData($row)
    {
        return [
            'gentan_no_line' => $row->gentan_no_line_asy,
            'panjang_produksi' => $row->panjang_produksi_asy,
            'tgl_produksi' => $row->tgl_produksi_asy,
            'work_shift' => $row->work_shift_asy,
            'work_hour' => $row->work_hour_asy,
            'no_mesin' => $row->no_mesin_asy,
            'nomor_han' => $row->nomor_han_asy,
            'nama_petugas' => $row->nama_petugas_asy,
            'dept_petugas' => $row->dept_petugas_asy,
            'infure_berat_loss' => $row->infure_berat_loss
        ];
    }

    private function writeReport($tglAwal, $tglAkhir, $filters, $data)
    {
        // Tulis header report
        $this->writeHeaders($tglAwal, $tglAkhir, $filters);

        // Tulis data dengan buffering
        $this->writeData($data);

        // Tulis grand total
        $this->writeGrandTotal($data['totals']);

        // Terapkan style yang di-cache
        $this->applyStyles();
    }

    private function writeHeaders($tglAwal, $tglAkhir, $filters)
    {
        // Cache operasi style untuk diterapkan sekali di akhir
        $this->cacheStyle('A1:A2', ['font' => ['bold' => true, 'size' => 11, 'name' => 'Calibri']]);

        $this->worksheet->setCellValue('A1', 'DETAIL PRODUKSI SEITAI');
        $this->worksheet->setCellValue('A2', 'Periode: ' . Carbon::parse($tglAwal)->translatedFormat('d-M-Y H:i') .
            '  ~  ' . Carbon::parse($tglAkhir)->translatedFormat('d-M-Y H:i') .
            ' - Mesin: ' . ($filters['machineId'] == '' ? 'Semua Mesin' : $filters['machineId']));

        // Set column headers dengan format yang dioptimasi
        $this->setColumnHeaders();
    }

    private function setColumnHeaders()
    {
        $headers = [
            'Tanggal Produksi',
            'Nama Petugas',
            'Dept. Petugas',
            'Nomor Mesin',
            'No LPK',
            'Shift',
            'Jam',
            'Nomor Palet',
            'Nomor LOT',
            'Quantity (Lembar)',
            'Loss',
            'Berat Loss (Kg)',
            'Nomor Gentan',
            'Panjang (meter)',
            'Tanggal Produksi Infure',
            'Shift',
            'Jam',
            'Nomor Mesin Infure',
            'Nomor Han Infure',
            'Petugas Infure',
            'Dept. Infure',
            'Loss Infure di Seitai',
        ];

        foreach ($headers as $col => $header) {
            $column = chr(65 + $col); // A -> B -> C etc
            $this->worksheet->setCellValue($column . '3', $header);
        }

        $this->worksheet->freezePane('A4');


        // Cache header styles
        $this->cacheStyle('A3:V3', [
            'font' => ['bold' => true, 'size' => 9, 'name' => 'Calibri'],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
            'borders' => ['allBorders' => ['borderStyle' => 'thin']]
        ]);
    }

    private function writeData($data)
    {
        $currentRow = 4; // Start after headers

        foreach ($data['products'] as $productCode => $productName) {
            // Write product name
            $this->worksheet->setCellValue('A' . $currentRow, $productName);
            $this->cacheStyle('A' . $currentRow, [
                'font' => ['bold' => true, 'size' => 9, 'name' => 'Calibri']
            ]);
            $currentRow++;
            $startRow = $currentRow;

            foreach ($data['productionDates'][$productCode] as $date => $baseData) {
                $rowItemStart = $currentRow;
                $maxRow = $currentRow;
                $productionDate = Carbon::parse($date)->translatedFormat('d-M-Y');

                // Write base data
                $this->writeBaseData($currentRow, $baseData, $productionDate);

                // Write loss data if exists
                $rowLoss = $currentRow;
                if (isset($data['losses'][$productCode][$date])) {
                    foreach ($data['losses'][$productCode][$date] as $lossCode => $lossData) {
                        $this->writeLossData($rowLoss, $lossData);
                        $rowLoss++;
                    }
                    $maxRow = max($maxRow, $rowLoss);
                }

                // Write gentan data if exists
                $rowGentan = $currentRow;
                if (isset($data['gentan'][$productCode][$date])) {
                    foreach ($data['gentan'][$productCode][$date] as $gentanNo => $gentanData) {
                        $this->writeGentanData($rowGentan, $gentanData);
                        $rowGentan++;
                    }
                    $maxRow = max($maxRow, $rowGentan);
                }
                // Apply styles for the entire data block
                $this->applyDataBlockStyles($rowItemStart, $maxRow - 1);

                $currentRow = $maxRow + 1;
            }
            $this->applyProductBlockStyles($startRow, $maxRow);
        }

        $this->currentRow = $currentRow; // Save for grand total
    }

    private function writeBaseData($row, $data, $productionDate)
    {
        $columns = [
            'A' => $productionDate,
            'B' => $data['namapetugas'],
            'C' => $data['deptpetugas'],
            'D' => $data['nomesin'],
            'E' => $data['lpk_no'],
            'F' => $data['work_shift'],
            'G' => $data['work_hour'],
            'H' => $data['nomor_palet'],
            'I' => $data['nomor_lot'],
            'J' => $data['qty_produksi']
        ];

        foreach ($columns as $column => $value) {
            $this->worksheet->setCellValue($column . $row, $value);
        }
    }

    private function writeLossData($row, $data)
    {
        $this->worksheet->setCellValue('K' . $row, $data['name']);
        $this->worksheet->setCellValue('L' . $row, $data['weight']);
    }

    private function writeGentanData($row, $data)
    {
        $columns = [
            'M' => $data['gentan_no_line'],
            'N' => $data['panjang_produksi'],
            'O' => Carbon::parse($data['tgl_produksi'])->translatedFormat('d-M-Y'),
            'P' => $data['work_shift'],
            'Q' => $data['work_hour'],
            'R' => $data['no_mesin'],
            'S' => $data['nomor_han'],
            'T' => $data['nama_petugas'],
            'U' => $data['dept_petugas'],
            'V' => $data['infure_berat_loss']
        ];

        foreach ($columns as $column => $value) {
            $this->worksheet->setCellValue($column . $row, $value);
        }
    }

    private function applyDataBlockStyles($startRow, $endRow)
    {
        // Apply font and alignment for the entire block
        $this->cacheStyle("A{$startRow}:J{$startRow}", [
            'borders' => ['allBorders' => ['borderStyle' => 'thin']]
        ]);

        $this->cacheStyle("K{$startRow}:V{$endRow}", [
            'borders' => [
                'horizontal' => ['borderStyle' => 'hair'],
                'vertical' => ['borderStyle' => 'thin'],
                'outline' => ['borderStyle' => 'thin']
            ]
        ]);
    }

    private function applyProductBlockStyles($startRow, $endRow)
    {
        // Apply font and alignment for the entire block
        $this->cacheStyle("A{$startRow}:V{$endRow}", [
            'font' => ['size' => 8, 'name' => 'Calibri'],
        ]);

        // Center align specific columns
        $this->cacheStyle("A{$startRow}:C{$startRow}", [
            'alignment' => ['horizontal' => 'center']
        ]);
        $this->cacheStyle("E{$startRow}:I{$startRow}", [
            'alignment' => ['horizontal' => 'center']
        ]);
        $this->cacheStyle("O{$startRow}:S{$endRow}", [
            'alignment' => ['horizontal' => 'center']
        ]);

        $this->cacheStyle("J{$startRow}", [
            'numberFormat' => ['formatCode' => '#,##0']
        ]);

        // Cache number formats
        $this->cacheStyle("N{$startRow}:N{$endRow}", [
            'numberFormat' => ['formatCode' => '#,##0']
        ]);
    }

    private function writeGrandTotal($totals)
    {
        $row = $this->currentRow;

        // Merge cells for GRAND TOTAL label
        $this->worksheet->mergeCells("A{$row}:I{$row}");
        $this->worksheet->setCellValue("A{$row}", 'GRAND TOTAL');

        // Write totals
        $this->worksheet->setCellValue("J{$row}", $totals['qty_produksi']);
        $this->worksheet->setCellValue("L{$row}", $totals['berat_loss']);
        $this->worksheet->setCellValue("N{$row}", $totals['panjang_produksi']);
        $this->worksheet->setCellValue("V{$row}", $totals['infure_berat_loss']);

        // Cache styles for grand total row
        $this->cacheStyle("A{$row}:V{$row}", [
            'font' => ['bold' => true, 'size' => 9, 'name' => 'Calibri'],
            'borders' => ['allBorders' => ['borderStyle' => 'thin']]
        ]);

        // Cache number formats for total values
        $this->cacheStyle("J{$row}:V{$row}", [
            'numberFormat' => ['formatCode' => '#,##0']
        ]);

        // L dengan koma 1
        $this->cacheStyle("L{$row}", [
            'numberFormat' => ['formatCode' => '#,##0.0']
        ]);
    }

    private function cacheStyle($range, $style)
    {
        $this->styleCache[$range] = $style;
    }

    private function applyStyles()
    {
        foreach ($this->styleCache as $range => $style) {
            $this->worksheet->getStyle($range)->applyFromArray($style);
        }

        // Set auto width untuk semua kolom yang digunakan
        $this->worksheet->getStyle('A3:V3')->getAlignment()->setWrapText(true);
    }

    private function saveReport($nippo)
    {
        $filename = 'Detail-Produksi-' . $nippo . '.xlsx';
        $writer = new Xlsx($this->spreadsheet);
        $writer->save($filename);

        return [
            'status' => 'success',
            'filename' => $filename
        ];
    }
}
