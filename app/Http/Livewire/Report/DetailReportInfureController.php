<?php

namespace App\Http\Livewire\Report;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DetailReportInfureController
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
            return [
                'status' => 'error',
                'message' => "Data pada periode tanggal atau pembeli tersebut tidak ditemukan"
            ];
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

        // Optimalkan penggunaan memori
        $this->spreadsheet->getProperties()
            ->setTitle('Detail Produksi Infure')
            ->setCreator('System');

        // Nonaktifkan perhitungan otomatis
        $this->spreadsheet->getCalculationEngine()->disableCalculationCache();
    }

    private function getOptimizedData($tglAwal, $tglAkhir, $filters)
    {
        // Gunakan query builder untuk performa lebih baik
        $filterDate = "tdpa.production_date BETWEEN '$tglAwal' AND '$tglAkhir'";
        $filterNoLPK = isset($filters['lpk_no']) ? " AND (tdol.lpk_no = '{$filters['lpk_no']}')" : '';
        $nomorOrder = isset($filters['nomorOrder']) ? " AND (msp.code = '{$filters['nomorOrder']}')" : '';
        $filters['departmentId'] = isset($filters['departmentId']) ? (is_array($filters['departmentId']) ? $filters['departmentId']['value'] : $filters['departmentId']) : '';
        $filterDepartment = $filters['departmentId'] ? " AND (msd.id = '{$filters['departmentId']}')" : '';
        $filters['machineId'] = isset($filters['machineId']) ? (is_array($filters['machineId']) ? $filters['machineId']['value'] : $filters['machineId']) : '';
        $filterMachine = $filters['machineId'] ? " AND (tdpa.machine_id = '{$filters['machineId']}')" : '';
        $filterNomorHan = isset($filters['nomorHan']) ? " AND (tdpa.nomor_han = '{$filters['nomorHan']}')" : '';

        return DB::select(
            "
                SELECT
                    tdpa.production_date AS tglproduksi,
                    tdpa.work_shift AS shift,
                    tdpa.work_hour AS jam,
                    tdpa.employee_id AS employee_id,
                    mse.employeeno AS nik,
                    mse.empname AS namapetugas,
                    msd.NAME AS deptpetugas,
                    tdpa.machine_id AS machine_id,
                    msm.machineno AS nomesin,
                    msm.machinename AS namamesin,
                    tdpa.product_id AS product_id,
                    msp.code AS produkcode,
                    msp.NAME AS namaproduk,
                    tdol.lpk_no AS lpk_no,
                    tdpa.nomor_han AS nomor_han,
                    tdpa.gentan_no AS gentan_no,
                    tdpa.panjang_produksi AS panjang_produksi,
                    tdpa.panjang_printing_inline AS panjang_printing_inline,
                    tdpa.berat_produksi AS berat_produksi,
                    msli.code AS losscode,
                    msli.NAME AS lossname,
                    tdpal.berat_loss
                FROM
                    tdProduct_Assembly AS tdpa
                    INNER JOIN tdOrderLpk AS tdol ON tdpa.lpk_id = tdol.id
                    INNER JOIN msEmployee AS mse ON mse.ID = tdpa.employee_id
                    INNER JOIN msMachine AS msm ON msm.ID = tdpa.machine_id
                    INNER JOIN msProduct AS msp ON msp.ID = tdpa.product_id
                    INNER JOIN msDepartment AS msd ON msd.ID = mse.department_id
                    LEFT JOIN tdProduct_Assembly_Loss AS tdpal ON tdpal.product_assembly_id = tdpa.
                    ID LEFT JOIN msLossInfure AS msli ON msli.ID = tdpal.loss_infure_id
                WHERE
                    $filterDate
                    $filterNoLPK
                    $nomorOrder
                    $filterDepartment
                    $filterMachine
                    $filterNomorHan
                ORDER BY tdpa.production_date ASC",
        );;
    }

    private function preprocessData($data)
    {
        $processed = [
            'products' => [],
            'workHours' => [],
            'totals' => [
                'panjang' => 0,
                'berat' => 0,
                'loss' => 0
            ]
        ];

        foreach ($data as $row) {
            // Struktur data untuk akses lebih cepat
            if (!isset($processed['products'][$row->product_id])) {
                $processed['products'][$row->product_id] = [
                    'name' => $row->produkcode . ' - ' . $row->namaproduk,
                    'dates' => []
                ];
            }

            $dateKey = $row->tglproduksi;
            if (!isset($processed['workHours'][$row->product_id][$dateKey][$row->jam])) {
                $processed['workHours'][$row->product_id][$dateKey][$row->jam] = [
                    'base' => $this->formatBaseData($row),
                    'loss' => []
                ];
            }

            if ($row->losscode) {
                $processed['workHours'][$row->product_id][$dateKey][$row->jam]['loss'][] = [
                    'name' => $row->lossname,
                    'weight' => $row->berat_loss
                ];
            }

            // Update totals
            $processed['totals']['panjang'] += $row->panjang_produksi;
            $processed['totals']['berat'] += $row->berat_produksi;
            $processed['totals']['loss'] += $row->berat_loss ?? 0;
        }

        return $processed;
    }

    private function formatBaseData($row)
    {
        return [
            'tglproduksi' => $row->tglproduksi,
            'shift' => $row->shift,
            'jam' => $row->jam,
            'nik' => $row->nik,
            'nama_petugas' => $row->namapetugas,
            'dept_petugas' => $row->deptpetugas,
            'nama_mesin' => $row->nomesin . ' - ' . $row->namamesin,
            'lpk_no' => $row->lpk_no,
            'gentan_no' => $row->gentan_no,
            'nomor_han' => $row->nomor_han,
            'panjang_produksi' => $row->panjang_produksi,
            'berat_produksi' => $row->berat_produksi
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

        $this->worksheet->setCellValue('A1', 'DETAIL PRODUKSI INFURE');
        $this->worksheet->setCellValue('A2', 'Periode: ' . Carbon::parse($tglAwal)->translatedFormat('d-M-Y H:i') .
            ' s/d ' . Carbon::parse($tglAkhir)->translatedFormat('d-M-Y H:i') .
            ' - Mesin: ' . ($filters['machine_id'] == '' ? 'Semua Mesin' : $filters['machine_id']));

        // Set column headers dengan format yang dioptimasi
        $this->setColumnHeaders();
    }

    private function setColumnHeaders()
    {
        $headers = [
            'Tanggal Produksi',
            'Shift',
            'Jam',
            'NIK',
            'Nama Petugas',
            'Dept. Petugas',
            'Mesin',
            'No LPK',
            'Nomor Gentan',
            'Nomor Han',
            'Panjang Produksi (meter)',
            'Berat Produksi (Kg)',
            'Loss',
            'Berat Loss (Kg)'
        ];

        foreach ($headers as $col => $header) {
            $column = chr(65 + $col); // A -> B -> C etc
            $this->worksheet->setCellValue($column . '3', $header);
        }

        $this->worksheet->freezePane('A4');
        $this->worksheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);


        // Cache header styles
        $this->cacheStyle('A3:N3', [
            'font' => ['bold' => true, 'size' => 9, 'name' => 'Calibri'],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
            'borders' => ['allBorders' => ['borderStyle' => 'thin']]
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
        $columnItemStart = 'A';
        $columnItemEnd = 'N';
        $this->worksheet->getStyle($columnItemStart . 3 . ':' . $columnItemEnd . 3)->getAlignment()->setWrapText(true);
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

    private function writeData($data)
    {
        $currentRow = 4; // Mulai dari baris ke-4 setelah header

        foreach ($data['products'] as $productId => $productInfo) {
            // Tulis nama produk
            $this->worksheet->setCellValue('A' . $currentRow, $productInfo['name']);
            $this->cacheStyle('A' . $currentRow, [
                'font' => ['bold' => true, 'size' => 9, 'name' => 'Calibri']
            ]);
            $currentRow++;
            $startRow = $currentRow;

            // Tulis detail untuk setiap produk
            foreach ($data['workHours'][$productId] as $date => $hours) {
                foreach ($hours as $hour => $details) {
                    $rowItemStart = $currentRow;
                    $baseData = $details['base'];

                    // Tulis data dasar dalam satu baris
                    $this->writeRowData($currentRow, [
                        Carbon::parse($baseData['tglproduksi'])->translatedFormat('d-M-Y'),
                        $baseData['shift'],
                        $baseData['jam'],
                        $baseData['nik'],
                        $baseData['nama_petugas'],
                        $baseData['dept_petugas'],
                        $baseData['nama_mesin'],
                        $baseData['lpk_no'],
                        $baseData['gentan_no'],
                        $baseData['nomor_han'],
                        $baseData['panjang_produksi'],
                        $baseData['berat_produksi']
                    ]);

                    // Tulis data loss jika ada
                    if (!empty($details['loss'])) {
                        foreach ($details['loss'] as $loss) {
                            $this->worksheet->setCellValue('M' . $currentRow, $loss['name']);
                            $this->worksheet->setCellValue('N' . $currentRow, $loss['weight']);
                            $currentRow++;
                        }
                        if (count($details['loss']) > 1) {
                            $this->applyLossDataBlockStyles($rowItemStart, $currentRow);
                        }
                    }
                    $currentRow++;
                    $this->applyDataBlockStyles($rowItemStart);
                }
            }
            $this->applyProductBlockStyles($startRow, $currentRow);
        }

        $this->currentRow = $currentRow; // Simpan posisi baris terakhir untuk grand total
    }

    private function applyDataBlockStyles($startRow)
    {
        // Apply font and alignment for the entire block
        $this->cacheStyle("A{$startRow}:N{$startRow}", [
            'borders' => ['allBorders' => ['borderStyle' => 'thin']]
        ]);
    }

    private function applyLossDataBlockStyles($startRow, $endRow)
    {
        $startRow++;
        $this->cacheStyle("M{$startRow}:N{$endRow}", [
            'borders' => ['allBorders' => ['borderStyle' => 'thin']]
        ]);
    }

    private function applyProductBlockStyles($startRow, $endRow)
    {
        $this->cacheStyle("A{$startRow}:N{$endRow}", [
            'font' => ['size' => 8, 'name' => 'Calibri'],
        ]);
        $this->cacheStyle("A{$startRow}:F{$endRow}", [
            'alignment' => ['horizontal' => 'center'],
        ]);
    }

    private function writeRowData($row, $data)
    {
        foreach ($data as $col => $value) {
            $column = chr(65 + $col); // Konversi 0 -> A, 1 -> B, dst
            $this->worksheet->setCellValue($column . $row, $value);
        }
    }

    private function writeGrandTotal($totals)
    {
        $row = $this->currentRow; // Menggunakan posisi baris terakhir yang disimpan

        // Merge cells untuk label GRAND TOTAL (A sampai J)
        $this->worksheet->mergeCells("A{$row}:J{$row}");
        $this->worksheet->setCellValue("A{$row}", 'GRAND TOTAL');

        // Tulis nilai total
        $this->worksheet->setCellValue("K{$row}", $totals['panjang']); // Total Panjang Produksi
        $this->worksheet->setCellValue("L{$row}", $totals['berat']);   // Total Berat Produksi
        $this->worksheet->setCellValue("N{$row}", $totals['loss']);    // Total Loss

        // Cache styles untuk grand total
        $this->cacheStyle("A{$row}:N{$row}", [
            'font' => [
                'bold' => true,
                'size' => 8,
                'name' => 'Calibri'
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => 'thin'
                ]
            ]
        ]);

        // Format angka untuk kolom total
        $this->cacheStyle("K5:L{$row}", [
            'numberFormat' => [
                'formatCode' => '#,##0'
            ]
        ]);

        $this->cacheStyle("H5:J{$row}", [
            'alignment' => ['horizontal' => 'center'],
        ]);
    }
}
