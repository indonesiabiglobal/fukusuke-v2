<?php

namespace App\Http\Livewire\Warehouse\LabelMasukGudangReport;

use App\Models\MsMachine;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class LabelMasukGudangReportExport
{
    private $spreadsheet;
    private $worksheet;
    private $currentRow = 1;
    private $styleCache = [];

    public function generateReport($tglAwal, $tglAkhir)
    {
        // Inisialisasi spreadsheet dengan pengaturan optimal
        $this->initSpreadsheet();

        // Ambil data dengan query yang dioptimasi
        $data = $this->getOptimizedData($tglAwal, $tglAkhir);

        if (empty($data)) {
            throw new \Exception("Data dengan filter tersebut tidak ditemukan");
        }

        // Proses data sekali jalan untuk mengurangi loop
        $processedData = $this->preprocessData($data);

        // Tulis report dengan metode yang dioptimasi
        $this->writeReport($tglAwal, $tglAkhir, $processedData);

        // Simpan file
        return $this->saveReport();
    }

    private function initSpreadsheet()
    {
        $this->spreadsheet = new Spreadsheet();
        $this->worksheet = $this->spreadsheet->getActiveSheet();
        $this->worksheet->setShowGridlines(false);
        $activeWorksheet = $this->spreadsheet->getActiveSheet();

        // Optimalkan penggunaan memori
        $this->spreadsheet->getProperties()
            ->setTitle('Detail Produksi Infure')
            ->setCreator('System');

        // Nonaktifkan perhitungan otomatis
        $this->spreadsheet->getCalculationEngine()->disableCalculationCache();
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
    }

    private function getOptimizedData($tglAwal, $tglAkhir)
    {
        // Gunakan query builder untuk performa lebih baik
        $filterDate = "tkmg.printed_on BETWEEN '$tglAwal' AND '$tglAkhir'";

        return DB::select(
            "
            SELECT
                tdpg.id AS production_id,
                tdpg.production_date AS tgl_produksi,
                tdpg.created_on AS tgl_proses,
                tdpg.nomor_palet AS nomor_palet,
                tdpg.nomor_lot AS nomor_lot,
                tdpg.work_shift AS work_shift,
                tdpg.employee_id AS employee_id,
                tdpg.start_box AS start_box,
                tdpg.end_box AS end_box,
                me.empname as namapetugas,
                tdpg.product_id AS product_id,
                mp.code_alias as nocode,
                mp.name as namaproduk,
                mp.palet_jumlah_baris as tinggi,
                mp.palet_isi_baris as jmlbaris,
                tdpg.qty_produksi/cast(mp.case_box_count as  INTEGER) AS qty_produksi
            FROM  tr_kartu_masuk_gudang AS tkmg
                INNER JOIN tdProduct_Goods AS tdpg ON tkmg.nomor_palet = tdpg.nomor_palet
                LEFT JOIN tdOrderLpk AS tdol ON tdpg.lpk_id = tdol.id
                LEFT join msproduct as mp on mp.id=tdpg.product_id
                LEFT join msemployee as me on me.id=tdpg.employee_id
            WHERE
                $filterDate
                AND tkmg.revisi = (
                    SELECT MAX(revisi)
                    FROM tr_kartu_masuk_gudang AS sub_tkmg
                    WHERE sub_tkmg.nomor_palet = tkmg.nomor_palet
                )
            ORDER BY tdpg.created_on ASC",
        );
    }

    private function preprocessData($data)
    {
        $processed = [
            'palet' => [],
        ];
        $isiPalet = [];

        foreach ($data as $row) {
            if (!isset($isiPalet[$row->nomor_palet])) {
                $isiPalet[$row->nomor_palet] = 0;
            }
            $isiPalet[$row->nomor_palet] = $isiPalet[$row->nomor_palet] + $row->qty_produksi;
            // Struktur data untuk akses lebih cepat
            if (!isset($processed['palet'][$row->nomor_palet])) {
                $parts = explode('-', (string) $row->nomor_palet, 2);
                $processed['palet'][$row->nomor_palet] = [
                    'tgl_proses' => $row->tgl_proses,
                    'tgl_produksi' => $row->tgl_produksi,
                    'no_palet' => trim($parts[0] ?? ''),
                    'no_label' => isset($parts[1]) ? trim($parts[1]) : '',
                    'no_produk' => $row->nocode,
                    'nama_produk' => $row->namaproduk,
                ];
            } else {
                // Pastikan isi_palet diupdate jika sudah ada
                $processed['palet'][$row->nomor_palet]['isi_palet'] = $isiPalet[$row->nomor_palet];
            }

            if (!isset($processed['palet'][$row->nomor_palet]['nomor_lot'][$row->nomor_lot])) {
                $processed['palet'][$row->nomor_palet]['nomor_lot'][$row->nomor_lot] = [
                    'nomor_lot' => $row->nomor_lot,
                    'qty_produksi' => $row->qty_produksi,
                ];
            }
        }

        return $processed;
    }

    private function writeReport($tglAwal, $tglAkhir, $data)
    {
        // Tulis header report
        $this->writeHeaders($tglAwal, $tglAkhir);

        // Tulis data dengan buffering
        $this->writeData($data);

        // Terapkan style yang di-cache
        $this->applyStyles();
    }

    private function writeHeaders($tglAwal, $tglAkhir)
    {
        // Cache operasi style untuk diterapkan sekali di akhir
        $this->cacheStyle('B1:B2', ['font' => ['bold' => true, 'size' => 12, 'name' => 'Arial']]);

        $this->worksheet->setCellValue('B1', 'SUMMARY MASUK PRODUK');
        $this->worksheet->setCellValue('B2', 'Period: ' . Carbon::parse($tglAwal)->translatedFormat('d-M-Y H:i') .
            '  ~  ' . Carbon::parse($tglAkhir)->translatedFormat('d-M-Y H:i'));

        // Set column headers dengan format yang dioptimasi
        $this->setColumnHeaders();
    }

    private function setColumnHeaders()
    {
        $headers = [
            'NO TRANS',
            'TGL. PROSES',
            'TGL. PRODUKSI',
            'NO. PALET',
            'NO. LABEL',
            'Alamat Rak',
            'NO. PRODUK',
            'NAMA PRODUK',
            'ISI PALET',
            'NO LOT1',
            'NO LOT2',
            'NO LOT3',
            'NO LOT4',
            'NO LOT5',
            'NO LOT6',
            'NO LOT7',
            'NO LOT8',
            'NO LOT9',
            'NO LOT10',
            'QTY1',
            'QTY2',
            'QTY3',
            'QTY4',
            'QTY5',
            'QTY6',
            'QTY7',
            'QTY8',
            'QTY9',
            'QTY10',
            'INF1',
            'INF2',
            'INF3',
            'INF4',
            'INF5',
            'INF6',
            'INF7',
            'INF8',
            'INF9',
            'INF10',
            'CATATAN',
        ];

        foreach ($headers as $col => $header) {
            $column = Coordinate::stringFromColumnIndex($col + 2);
            $this->worksheet->setCellValue($column . '4', $header);
        }

        $this->worksheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

        // Cache header styles
        $this->cacheStyle('B4:AN4', [
            'font' => ['bold' => true, 'size' => 8, 'name' => 'Arial'],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
            'borders' => ['allBorders' => ['borderStyle' => 'thin']],
            'fill' => [
                'fillType' => 'solid',
                'startColor' => ['argb' => 'FFFFA500'],
            ],
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
        $columnItemStart = 'B';
        $columnItemEnd = 'AN';
        $this->worksheet->getStyle($columnItemStart . 4 . ':' . $columnItemEnd . 4)->getAlignment()->setWrapText(true);
    }

    private function saveReport()
    {
        $filename = 'Label-Masuk-Gudang-Report.csv';
        $writer = new Csv($this->spreadsheet);
        $writer->setDelimiter(';'); // Menggunakan titik koma sebagai delimiter untuk kompatibilitas Excel Indonesia
        $writer->save($filename);

        return [
            'status' => 'success',
            'filename' => $filename
        ];
    }

    private function writeData($data)
    {
        $currentRow = 5;

        foreach ($data['palet'] as $nomorPalet => $paletData) {
            $startRow = $currentRow;
            $rowData = [
                '',
                Carbon::parse($paletData['tgl_proses'])->translatedFormat('d-M-Y'),
                Carbon::parse($paletData['tgl_produksi'])->translatedFormat('d-M-Y'),
                $paletData['no_palet'],
                $paletData['no_label'],
                '',
                $paletData['no_produk'],
                $paletData['nama_produk'],
                $paletData['isi_palet'],
            ];

            // Tulis detail untuk setiap lot
            $countLot = count($paletData['nomor_lot']);
            foreach ($paletData['nomor_lot'] as $nomor_lot => $data) {
                $rowData = array_merge($rowData, [
                    $data['nomor_lot']
                ]);
            }
            // Tambahkan kolom kosong jika kurang dari 10 lot
            for ($i = $countLot; $i < 10; $i++) {
                $rowData = array_merge($rowData, ['']);
            }

            // Tulis detail untuk setiap qty
            foreach ($paletData['nomor_lot'] as $nomor_lot => $data) {
                $rowData = array_merge($rowData, [
                    $data['qty_produksi']
                ]);
            }
            // Tambahkan kolom kosong jika kurang dari 10 qty
            for ($i = $countLot; $i < 10; $i++) {
                $rowData = array_merge($rowData, ['']);
            }
            $this->writeRowData($currentRow, $rowData);
            $currentRow++;
            $this->applyBlockStyles($startRow, $currentRow);
        }

        $this->currentRow = $currentRow + 1; // Simpan posisi baris terakhir untuk grand total
    }

    private function applyBlockStyles($startRow, $endRow)
    {
        $this->cacheStyle("B{$startRow}:AN{$endRow}", [
            'font' => ['size' => 8, 'name' => 'Calibri'],
            'alignment' => ['horizontal' => 'left'],
            'borders' => ['allBorders' => ['borderStyle' => 'thin']],
        ]);
    }

    private function writeRowData($row, $data)
    {
        foreach ($data as $col => $value) {
            $column = Coordinate::stringFromColumnIndex($col + 2);
            $this->worksheet->setCellValue($column . $row, $value);
        }
    }
}
