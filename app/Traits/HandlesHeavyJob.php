<?php

namespace App\Traits;

/**
 * Digunakan pada Livewire component atau controller yang menjalankan
 * proses berat (export Excel, generate laporan, batch processing, dll).
 *
 * Cara pakai:
 *   use App\Traits\HandlesHeavyJob;
 *
 *   class MyController extends Component {
 *       use HandlesHeavyJob;
 *
 *       public function export() {
 *           $this->startHeavyJob();
 *           // ... proses export
 *       }
 *   }
 */
trait HandlesHeavyJob
{
    /**
     * Hilangkan batasan waktu dan naikkan memory limit untuk proses berat.
     * Panggil di awal method export / generate report / batch.
     *
     * @param string|null $memoryLimit  Override memory, default dari env PHP_HEAVY_MEMORY_LIMIT
     */
    protected function startHeavyJob(?string $memoryLimit = null): void
    {
        set_time_limit(0);
        ini_set('max_execution_time', 0);
        // Naikkan memory hanya untuk request ini, default 1G cukup untuk export/report
        ini_set('memory_limit', $memoryLimit ?? env('PHP_HEAVY_MEMORY_LIMIT', '1G'));
    }
}
