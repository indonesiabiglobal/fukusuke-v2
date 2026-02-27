<?php

namespace App\Http\Livewire\Report;

use Carbon\Carbon;
use Livewire\Component;
use App\Models\MsMachine;
use App\Models\MsWorkingShift;
use App\Helpers\phpspreadsheet;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Exports\GeneralReportExport;
use App\Http\Livewire\Report\GeneralReport\JamMatiReportService;
use App\Http\Livewire\Report\GeneralReport\KapasitasProduksiReportService;
use App\Http\Livewire\Report\GeneralReport\LossKasusReportService;
use App\Http\Livewire\Report\GeneralReport\LossPerDepartemenPerJenisReportService;
use App\Http\Livewire\Report\GeneralReport\ProduksiPerDepartemenPerJenisReportService;
use App\Http\Livewire\Report\GeneralReport\ProduksiPerDepartemenPerTypeReportService;
use App\Http\Livewire\Report\GeneralReport\ProduksiPerJenisReportService;
use App\Http\Livewire\Report\GeneralReport\ProduksiPerTypeReportService;
use App\Http\Livewire\Report\GeneralReport\LossPerMesinReportService;
use App\Http\Livewire\Report\GeneralReport\ProduksiPerDepartemenPerPetugasReportService;
use App\Http\Livewire\Report\GeneralReport\ProduksiPerMesinPerProdukReportService;
use App\Http\Livewire\Report\GeneralReport\ProduksiPerMesinReportService;
use App\Http\Livewire\Report\GeneralReport\ProduksiPerProdukReportService;
use App\Http\Livewire\Report\GeneralReport\ProduksiPerTypePerMesinReportService;
use App\Http\Livewire\Report\GeneralReport\LossPerDepartemenReportService;
use App\Http\Livewire\Report\GeneralReport\LossPerPetugasReportService;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class GeneralReportController extends Component
{
    public $jenisreport;
    public $tglMasuk;
    public $tglKeluar;
    public $jamMasuk;
    public $jamKeluar;
    public $workingShiftHour;
    public $nipon = 'Infure';


    public function mount()
    {
        $this->tglMasuk = Carbon::now()->format('Y-m-d');
        $this->tglKeluar = Carbon::now()->format('Y-m-d');
        $this->workingShiftHour = MsWorkingShift::select('work_hour_from', 'work_hour_till')->active()->orderBy('work_hour_from', 'ASC')->get();
        $this->jamMasuk = $this->workingShiftHour[0]->work_hour_from;
        $this->jamKeluar = $this->workingShiftHour[count($this->workingShiftHour) - 1]->work_hour_till;
    }

    public function export()
    {
        // mengecek apakah jenis report sudah dipilih atau belum
        if ($this->jenisreport == null) {
            $this->dispatch('notification', ['type' => 'warning', 'message' => 'Pilih Jenis Report.']);
            return;
        }

        if ($this->tglMasuk > $this->tglKeluar) {
            session()->flash('error', 'Tanggal akhir tidak boleh kurang dari tanggal awal');
            return;
        }

        $tglMasuk = Carbon::parse($this->tglMasuk . ' ' . $this->jamMasuk);
        $tglKeluar = Carbon::parse($this->tglKeluar . ' ' . $this->jamKeluar);

        switch ($this->jenisreport) {
            case 'Daftar Produksi Per Mesin':
                if ($this->nipon == 'Infure') {
                    $response = $this->daftarProduksiPerMesinInfure($tglMasuk, $tglKeluar);
                    if ($response['status'] == 'success') {
                        return response()->download($response['filename'])->deleteFileAfterSend(true);
                    } else if ($response['status'] == 'error') {
                        $this->dispatch('notification', ['type' => 'warning', 'message' => $response['message']]);
                        return;
                    }
                } else {
                    $response = $this->daftarProduksiPerMesinSeitai($tglMasuk, $tglKeluar);
                    if ($response['status'] == 'success') {
                        return response()->download($response['filename'])->deleteFileAfterSend(true);
                    } else if ($response['status'] == 'error') {
                        $this->dispatch('notification', ['type' => 'warning', 'message' => $response['message']]);
                        return;
                    }
                }
                break;
            case 'Daftar Produksi Per Tipe Per Mesin':
                if ($this->nipon == 'Infure') {
                    $response = $this->daftarProduksiPerTipePerMesinInfure($tglMasuk, $tglKeluar);
                    if ($response['status'] == 'success') {
                        return response()->download($response['filename'])->deleteFileAfterSend(true);
                    } else if ($response['status'] == 'error') {
                        $this->dispatch('notification', ['type' => 'warning', 'message' => $response['message']]);
                        return;
                    }
                } else {
                    $response = $this->daftarProduksiPerTipePerMesinSeitai($tglMasuk, $tglKeluar);
                    if ($response['status'] == 'success') {
                        return response()->download($response['filename'])->deleteFileAfterSend(true);
                    } else if ($response['status'] == 'error') {
                        $this->dispatch('notification', ['type' => 'warning', 'message' => $response['message']]);
                        return;
                    }
                }
                break;
            case 'Daftar Produksi Per Jenis':
                if ($this->nipon == 'Infure') {
                    $response = $this->daftarProduksiPerJenisInfure($tglMasuk, $tglKeluar);
                    if ($response['status'] == 'success') {
                        return response()->download($response['filename'])->deleteFileAfterSend(true);
                    } else if ($response['status'] == 'error') {
                        $this->dispatch('notification', ['type' => 'warning', 'message' => $response['message']]);
                        return;
                    }
                } else {
                    $response = $this->daftarProduksiPerJenisSeitai($tglMasuk, $tglKeluar);
                    if ($response['status'] == 'success') {
                        return response()->download($response['filename'])->deleteFileAfterSend(true);
                    } else if ($response['status'] == 'error') {
                        $this->dispatch('notification', ['type' => 'warning', 'message' => $response['message']]);
                        return;
                    }
                }
                break;
            case 'Daftar Produksi Per Tipe':
                if ($this->nipon == 'Infure') {
                    $response = $this->daftarProduksiPerTipeInfure($tglMasuk, $tglKeluar);
                    if ($response['status'] == 'success') {
                        return response()->download($response['filename'])->deleteFileAfterSend(true);
                    } else if ($response['status'] == 'error') {
                        $this->dispatch('notification', ['type' => 'warning', 'message' => $response['message']]);
                        return;
                    }
                } else {
                    $response = $this->daftarProduksiPerTipeSeitai($tglMasuk, $tglKeluar);
                    if ($response['status'] == 'success') {
                        return response()->download($response['filename'])->deleteFileAfterSend(true);
                    } else if ($response['status'] == 'error') {
                        $this->dispatch('notification', ['type' => 'warning', 'message' => $response['message']]);
                        return;
                    }
                }
                break;
            case 'Daftar Produksi Per Produk':
                if ($this->nipon == 'Infure') {
                    $response = $this->daftarProduksiPerProdukInfure($tglMasuk, $tglKeluar);
                    if ($response['status'] == 'success') {
                        return response()->download($response['filename'])->deleteFileAfterSend(true);
                    } else if ($response['status'] == 'error') {
                        $this->dispatch('notification', ['type' => 'warning', 'message' => $response['message']]);
                        return;
                    }
                } else {
                    $response = $this->daftarProduksiPerProdukSeitai($tglMasuk, $tglKeluar);
                    if ($response['status'] == 'success') {
                        return response()->download($response['filename'])->deleteFileAfterSend(true);
                    } else if ($response['status'] == 'error') {
                        $this->dispatch('notification', ['type' => 'warning', 'message' => $response['message']]);
                        return;
                    }
                }
                break;
            case 'Daftar Produksi Per Departemen Per Jenis':
                if ($this->nipon == 'Infure') {
                    $response = $this->daftarProduksiPerDepartemenPerJenisInfure($tglMasuk, $tglKeluar);
                    if ($response['status'] == 'success') {
                        return response()->download($response['filename'])->deleteFileAfterSend(true);
                    } else if ($response['status'] == 'error') {
                        $this->dispatch('notification', ['type' => 'warning', 'message' => $response['message']]);
                        return;
                    }
                } else {
                    $response = $this->daftarProduksiPerDepartemenPerJenisSeitai($tglMasuk, $tglKeluar);
                    if ($response['status'] == 'success') {
                        return response()->download($response['filename'])->deleteFileAfterSend(true);
                    } else if ($response['status'] == 'error') {
                        $this->dispatch('notification', ['type' => 'warning', 'message' => $response['message']]);
                        return;
                    }
                }
                break;
            case 'Daftar Produksi Per Departemen & Tipe':
                if ($this->nipon == 'Infure') {
                    $response = $this->daftarProduksiPerDepartemenPerTypeInfure($tglMasuk, $tglKeluar);
                    if ($response['status'] == 'success') {
                        return response()->download($response['filename'])->deleteFileAfterSend(true);
                    } else if ($response['status'] == 'error') {
                        $this->dispatch('notification', ['type' => 'warning', 'message' => $response['message']]);
                        return;
                    }
                } else {
                    $response = $this->daftarProduksiPerDepartemenPerTypeSeitai($tglMasuk, $tglKeluar);
                    if ($response['status'] == 'success') {
                        return response()->download($response['filename'])->deleteFileAfterSend(true);
                    } else if ($response['status'] == 'error') {
                        $this->dispatch('notification', ['type' => 'warning', 'message' => $response['message']]);
                        return;
                    }
                }
                break;
            case 'Daftar Produksi Per Departemen & Petugas':
                if ($this->nipon == 'Infure') {
                    $response = $this->daftarProduksiPerDepartemenPerPetugasInfure($tglMasuk, $tglKeluar);
                    if ($response['status'] == 'success') {
                        return response()->download($response['filename'])->deleteFileAfterSend(true);
                    } else if ($response['status'] == 'error') {
                        $this->dispatch('notification', ['type' => 'warning', 'message' => $response['message']]);
                        return;
                    }
                } else {
                    $response = $this->daftarProduksiPerDepartemenPerPetugasSeitai($tglMasuk, $tglKeluar);
                    if ($response['status'] == 'success') {
                        return response()->download($response['filename'])->deleteFileAfterSend(true);
                    } else if ($response['status'] == 'error') {
                        $this->dispatch('notification', ['type' => 'warning', 'message' => $response['message']]);
                        return;
                    }
                }
                break;
            case 'Daftar Loss Per Departemen':
                if ($this->nipon == 'Infure') {
                    $response = $this->daftarLossPerDepartemenInfure($tglMasuk, $tglKeluar);
                    if ($response['status'] == 'success') {
                        return response()->download($response['filename'])->deleteFileAfterSend(true);
                    } else if ($response['status'] == 'error') {
                        $this->dispatch('notification', ['type' => 'warning', 'message' => $response['message']]);
                        return;
                    }
                } else {
                    $response = $this->daftarLossPerDepartemenSeitai($tglMasuk, $tglKeluar);
                    if ($response['status'] == 'success') {
                        return response()->download($response['filename'])->deleteFileAfterSend(true);
                    } else if ($response['status'] == 'error') {
                        $this->dispatch('notification', ['type' => 'warning', 'message' => $response['message']]);
                        return;
                    }
                }
                break;
            case 'Daftar Loss Per Departemen & Jenis':
                if ($this->nipon == 'Infure') {
                    $response = $this->daftarLossPerDepartemenPerJenisInfure($tglMasuk, $tglKeluar);
                    if ($response['status'] == 'success') {
                        return response()->download($response['filename'])->deleteFileAfterSend(true);
                    } else if ($response['status'] == 'error') {
                        $this->dispatch('notification', ['type' => 'warning', 'message' => $response['message']]);
                        return;
                    }
                } else {
                    $response = $this->daftarLossPerDepartemenPerJenisSeitai($tglMasuk, $tglKeluar);
                    if ($response['status'] == 'success') {
                        return response()->download($response['filename'])->deleteFileAfterSend(true);
                    } else if ($response['status'] == 'error') {
                        $this->dispatch('notification', ['type' => 'warning', 'message' => $response['message']]);
                        return;
                    }
                }
                break;
            case 'Daftar Loss Per Petugas':
                if ($this->nipon == 'Infure') {
                    $response = $this->daftarLossPerPetugasInfure($tglMasuk, $tglKeluar);
                    if ($response['status'] == 'success') {
                        return response()->download($response['filename'])->deleteFileAfterSend(true);
                    } else if ($response['status'] == 'error') {
                        $this->dispatch('notification', ['type' => 'warning', 'message' => $response['message']]);
                        return;
                    }
                } else {
                    $response = $this->daftarLossPerPetugasSeitai($tglMasuk, $tglKeluar);
                    if ($response['status'] == 'success') {
                        return response()->download($response['filename'])->deleteFileAfterSend(true);
                    } else if ($response['status'] == 'error') {
                        $this->dispatch('notification', ['type' => 'warning', 'message' => $response['message']]);
                        return;
                    }
                }
                break;
            case 'Daftar Loss Per Mesin':
                if ($this->nipon == 'Infure') {
                    $response = $this->daftarLossPerMesinInfure($tglMasuk, $tglKeluar);
                    if ($response['status'] == 'success') {
                        return response()->download($response['filename'])->deleteFileAfterSend(true);
                    } else if ($response['status'] == 'error') {
                        $this->dispatch('notification', ['type' => 'warning', 'message' => $response['message']]);
                        return;
                    }
                } else {
                    $response = $this->daftarLossPerMesinSeitai($tglMasuk, $tglKeluar);
                    if ($response['status'] == 'success') {
                        return response()->download($response['filename'])->deleteFileAfterSend(true);
                    } else if ($response['status'] == 'error') {
                        $this->dispatch('notification', ['type' => 'warning', 'message' => $response['message']]);
                        return;
                    }
                }
                break;

            case 'Daftar Loss Per Mesin dan Jenis':
                $response = $this->daftarLossPerMesinJenis($tglMasuk, $tglKeluar);
                if ($response['status'] == 'success') {
                    return response()->download($response['filename'])->deleteFileAfterSend(true);
                } else if ($response['status'] == 'error') {
                    $this->dispatch('notification', ['type' => 'warning', 'message' => $response['message']]);
                    return;
                }
                break;

            case 'Daftar Kasus Loss Per Mesin dan Jenis':
                $response = $this->daftarKasusLossPerMesinJenis($tglMasuk, $tglKeluar);
                if ($response['status'] == 'success') {
                    return response()->download($response['filename'])->deleteFileAfterSend(true);
                } else if ($response['status'] == 'error') {
                    $this->dispatch('notification', ['type' => 'warning', 'message' => $response['message']]);
                    return;
                }
                break;

            case 'Kapasitas Produksi':
                if ($this->nipon == 'Infure') {
                    $response = $this->kapasitasProduksiInfure($tglMasuk, $tglKeluar);
                    if ($response['status'] == 'success') {
                        return response()->download($response['filename'])->deleteFileAfterSend(true);
                    } else if ($response['status'] == 'error') {
                        $this->dispatch('notification', ['type' => 'warning', 'message' => $response['message']]);
                        return;
                    }
                } else {
                    $response = $this->kapasitasProduksiSeitai($tglMasuk, $tglKeluar);
                    if ($response['status'] == 'success') {
                        return response()->download($response['filename'])->deleteFileAfterSend(true);
                    } else if ($response['status'] == 'error') {
                        $this->dispatch('notification', ['type' => 'warning', 'message' => $response['message']]);
                        return;
                    }
                }
                break;
            case 'Daftar Produksi Per Palet':
                if ($this->nipon == 'Seitai') {
                    $response = $this->daftarProduksiPerPaletSeitai($tglMasuk, $tglKeluar);
                    if ($response['status'] == 'success') {
                        return response()->download($response['filename'])->deleteFileAfterSend(true);
                    } else if ($response['status'] == 'error') {
                        $this->dispatch('notification', ['type' => 'warning', 'message' => $response['message']]);
                        return;
                    }
                } else {
                    $this->dispatch('notification', ['type' => 'warning', 'message' => 'Pilih Nipon Seitai.']);
                }
                break;
            case 'Daftar Produksi Per Mesin Per Produk':
                if ($this->nipon == 'Infure') {
                    $response = $this->daftarProduksiPerMesinPerProdukInfure($tglMasuk, $tglKeluar);
                    if ($response['status'] == 'success') {
                        return response()->download($response['filename'])->deleteFileAfterSend(true);
                    } else if ($response['status'] == 'error') {
                        $this->dispatch('notification', ['type' => 'warning', 'message' => $response['message']]);
                        return;
                    }
                } else {
                    $response = $this->daftarProduksiPerMesinPerProdukSeitai($tglMasuk, $tglKeluar);
                    if ($response['status'] == 'success') {
                        return response()->download($response['filename'])->deleteFileAfterSend(true);
                    } else if ($response['status'] == 'error') {
                        $this->dispatch('notification', ['type' => 'warning', 'message' => $response['message']]);
                        return;
                    }
                }
                break;

            case 'Jam Mati Per Mesin':
                $response = $this->jamMatiPerMesin($tglMasuk, $tglKeluar);
                if ($response['status'] == 'success') {
                    return response()->download($response['filename'])->deleteFileAfterSend(true);
                } else if ($response['status'] == 'error') {
                    $this->dispatch('notification', ['type' => 'warning', 'message' => $response['message']]);
                    return;
                }
                break;

            case 'Jam Mati Per Jenis':
                $response = $this->jamMatiPerJenis($tglMasuk, $tglKeluar);
                if ($response['status'] == 'success') {
                    return response()->download($response['filename'])->deleteFileAfterSend(true);
                } else if ($response['status'] == 'error') {
                    $this->dispatch('notification', ['type' => 'warning', 'message' => $response['message']]);
                    return;
                }
                break;
        }
    }

    public function daftarProduksiPerMesinInfure($tglMasuk, $tglKeluar)
    {
        return ProduksiPerMesinReportService::daftarProduksiPerMesinInfure($this->nipon, $this->jenisreport, $tglMasuk, $tglKeluar);
    }

    public function daftarProduksiPerMesinSeitai($tglMasuk, $tglKeluar)
    {
        return ProduksiPerMesinReportService::daftarProduksiPerMesinSeitai($this->nipon, $this->jenisreport, $tglMasuk, $tglKeluar);
    }

    public function daftarProduksiPerTipePerMesinInfure($tglMasuk, $tglKeluar)
    {
        return ProduksiPerTypePerMesinReportService::daftarProduksiPerTipePerMesinInfure($this->nipon, $this->jenisreport, $tglMasuk, $tglKeluar);
    }

    public function daftarProduksiPerTipePerMesinSeitai($tglMasuk, $tglKeluar)
    {
        return ProduksiPerTypePerMesinReportService::daftarProduksiPerTipePerMesinSeitai($this->nipon, $this->jenisreport, $tglMasuk, $tglKeluar);
    }

    public function daftarProduksiPerJenisInfure($tglMasuk, $tglKeluar)
    {
        return ProduksiPerJenisReportService::daftarProduksiPerJenisInfure($this->nipon, $this->jenisreport, $tglMasuk, $tglKeluar);
    }

    public function daftarProduksiPerJenisSeitai($tglMasuk, $tglKeluar)
    {
        return ProduksiPerJenisReportService::daftarProduksiPerJenisSeitai($this->nipon, $this->jenisreport, $tglMasuk, $tglKeluar);
    }

    public function daftarProduksiPerTipeInfure($tglMasuk, $tglKeluar)
    {
        return ProduksiPerTypeReportService::daftarProduksiPerTipeInfure($this->nipon, $this->jenisreport, $tglMasuk, $tglKeluar);
    }

    public function daftarProduksiPerTipeSeitai($tglMasuk, $tglKeluar)
    {
        return ProduksiPerTypeReportService::daftarProduksiPerTipeSeitai($this->nipon, $this->jenisreport, $tglMasuk, $tglKeluar);
    }

    public function daftarProduksiPerProdukInfure($tglMasuk, $tglKeluar)
    {
        return ProduksiPerProdukReportService::daftarProduksiPerProdukInfure($this->nipon, $this->jenisreport, $tglMasuk, $tglKeluar);
    }

    public function daftarProduksiPerProdukSeitai($tglMasuk, $tglKeluar)
    {
        return ProduksiPerProdukReportService::daftarProduksiPerProdukSeitai($this->nipon, $this->jenisreport, $tglMasuk, $tglKeluar);
    }

    public function daftarProduksiPerDepartemenPerJenisInfure($tglMasuk, $tglKeluar)
    {
        return ProduksiPerDepartemenPerJenisReportService::daftarProduksiPerDepartemenPerJenisInfure($this->nipon, $this->jenisreport, $tglMasuk, $tglKeluar);
    }

    public function daftarProduksiPerDepartemenPerJenisSeitai($tglMasuk, $tglKeluar)
    {
        return ProduksiPerDepartemenPerJenisReportService::daftarProduksiPerDepartemenPerJenisSeitai($this->nipon, $this->jenisreport, $tglMasuk, $tglKeluar);
    }

    public function daftarProduksiPerDepartemenPerTypeInfure($tglMasuk, $tglKeluar)
    {
        return ProduksiPerDepartemenPerTypeReportService::daftarProduksiPerDepartemenPerTypeInfure($this->nipon, $this->jenisreport, $tglMasuk, $tglKeluar);
    }

    public function daftarProduksiPerDepartemenPerTypeSeitai($tglMasuk, $tglKeluar)
    {
        return ProduksiPerDepartemenPerTypeReportService::daftarProduksiPerDepartemenPerTypeSeitai($this->nipon, $this->jenisreport, $tglMasuk, $tglKeluar);
    }

    public function daftarProduksiPerDepartemenPerPetugasInfure($tglMasuk, $tglKeluar)
    {
        return ProduksiPerDepartemenPerPetugasReportService::daftarProduksiPerDepartemenPerPetugasInfure($this->nipon, $this->jenisreport, $tglMasuk, $tglKeluar);
    }

    public function daftarProduksiPerDepartemenPerPetugasSeitai($tglMasuk, $tglKeluar)
    {
        return ProduksiPerDepartemenPerPetugasReportService::daftarProduksiPerDepartemenPerPetugasSeitai($this->nipon, $this->jenisreport, $tglMasuk, $tglKeluar);
    }

    public function daftarLossPerDepartemenInfure($tglMasuk, $tglKeluar)
    {
        return LossPerDepartemenReportService::daftarLossPerDepartemenInfure($this->nipon, $this->jenisreport, $tglMasuk, $tglKeluar);
    }

    public function daftarLossPerDepartemenSeitai($tglMasuk, $tglKeluar)
    {
        return LossPerDepartemenReportService::daftarLossPerDepartemenSeitai($this->nipon, $this->jenisreport, $tglMasuk, $tglKeluar);
    }

    public function daftarLossPerDepartemenPerJenisInfure($tglMasuk, $tglKeluar)
    {
        return LossPerDepartemenPerJenisReportService::daftarLossPerDepartemenPerJenisInfure($this->nipon, $this->jenisreport, $tglMasuk, $tglKeluar);
    }

    public function daftarLossPerDepartemenPerJenisSeitai($tglMasuk, $tglKeluar)
    {
        return LossPerDepartemenPerJenisReportService::daftarLossPerDepartemenPerJenisSeitai($this->nipon, $this->jenisreport, $tglMasuk, $tglKeluar);
    }

    public function daftarLossPerPetugasInfure($tglMasuk, $tglKeluar)
    {
        return LossPerPetugasReportService::daftarLossPerPetugasInfure($this->nipon, $this->jenisreport, $tglMasuk, $tglKeluar);
    }

    public function daftarLossPerPetugasSeitai($tglMasuk, $tglKeluar)
    {
        return LossPerPetugasReportService::daftarLossPerPetugasSeitai($this->nipon, $this->jenisreport, $tglMasuk, $tglKeluar);
    }

    public function daftarLossPerMesinInfure($tglMasuk, $tglKeluar)
    {
        return LossPerMesinReportService::daftarLossPerMesinInfure($this->nipon, $this->jenisreport, $tglMasuk, $tglKeluar);
    }

    public function daftarLossPerMesinSeitai($tglMasuk, $tglKeluar)
    {
        return LossPerMesinReportService::daftarLossPerMesinSeitai($this->nipon, $this->jenisreport, $tglMasuk, $tglKeluar);
    }

    public function kapasitasProduksiInfure($tglMasuk, $tglKeluar)
    {
        return KapasitasProduksiReportService::kapasitasProduksiInfure($this->nipon, $this->jenisreport, $tglMasuk, $tglKeluar);
    }

    public function kapasitasProduksiSeitai($tglMasuk, $tglKeluar)
    {
        return KapasitasProduksiReportService::kapasitasProduksiSeitai($this->nipon, $this->jenisreport, $tglMasuk, $tglKeluar);
    }

    public function daftarProduksiPerPaletSeitai($tglMasuk, $tglKeluar)
    {
        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();
        $activeWorksheet->setShowGridlines(false);
        $activeWorksheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        // Mengatur ukuran kertas menjadi A4
        $activeWorksheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
        // Mengatur agar semua kolom muat dalam satu halaman
        $activeWorksheet->getPageSetup()->setFitToWidth(1);
        $activeWorksheet->getPageSetup()->setFitToHeight(0); // Biarkan tinggi menyesuaikan otomatis
        // Set header berulang untuk print
        $activeWorksheet->getPageSetup()->setRowsToRepeatAtTop([1, 4]);

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
        $spreadsheet->getActiveSheet()->freezePane('A5');

        // Judul
        $activeWorksheet->setCellValue('B1', 'DAFTAR PRODUKSI PER PALET SEITAI');
        $activeWorksheet->setCellValue('B2', 'Periode : ' . $tglMasuk->format('d-M-Y H:i') . '  ~  ' . $tglKeluar->format('d-M-Y H:i'));
        // Style Judul
        phpspreadsheet::styleFont($spreadsheet, 'B1:B2', true, 11, 'Calibri');
        // hide column A
        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setVisible(false);

        /**
         * Header
         */
        $rowHeaderStart = 3;
        $rowHeaderEnd = 4;
        $columnHeaderStart = 'B';
        $columnHeaderEnd = 'B';
        // Nomor LPK
        $spreadsheet->getActiveSheet()->mergeCells($columnHeaderEnd . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderEnd);
        $activeWorksheet->setCellValue($columnHeaderEnd . $rowHeaderStart, 'Nomor LPK');
        $columnHeaderEnd++;
        // Nomor Palet
        $spreadsheet->getActiveSheet()->mergeCells($columnHeaderEnd . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderEnd);
        $activeWorksheet->setCellValue($columnHeaderEnd . $rowHeaderStart, 'Nomor Palet');
        $columnHeaderEnd++;
        // Tanggal Produksi
        $spreadsheet->getActiveSheet()->mergeCells($columnHeaderEnd . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderEnd);
        $activeWorksheet->setCellValue($columnHeaderEnd . $rowHeaderStart, 'Tanggal Produksi');
        $columnHeaderEnd++;
        // Kode shift
        $spreadsheet->getActiveSheet()->mergeCells($columnHeaderEnd . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderEnd);
        $activeWorksheet->setCellValue($columnHeaderEnd . $rowHeaderStart, 'Kode Shift');
        $columnHeaderEnd++;
        // nomor lot
        $spreadsheet->getActiveSheet()->mergeCells($columnHeaderEnd . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderEnd);
        $activeWorksheet->setCellValue($columnHeaderEnd . $rowHeaderStart, 'Nomor Lot');
        $columnHeaderEnd++;
        // production quantity
        $columnHeaderProductionQuantity = $columnHeaderEnd;
        $columnHeaderProductionQuantity++;
        $spreadsheet->getActiveSheet()->mergeCells($columnHeaderEnd . $rowHeaderStart . ':' . $columnHeaderProductionQuantity . $rowHeaderStart);
        $activeWorksheet->setCellValue($columnHeaderEnd . $rowHeaderStart, 'Production Quantity');
        // production quantity lembar
        $activeWorksheet->setCellValue($columnHeaderEnd . 4, 'Lembar');
        $columnHeaderEnd++;
        // production quantity box
        $activeWorksheet->setCellValue($columnHeaderProductionQuantity . 4, 'Box');
        $columnHeaderEnd++;
        // on kenpin process
        $columnHeaderKenpinProcess = $columnHeaderEnd;
        $columnHeaderKenpinProcess++;
        $spreadsheet->getActiveSheet()->mergeCells($columnHeaderEnd . $rowHeaderStart . ':' . $columnHeaderKenpinProcess . $rowHeaderStart);
        $activeWorksheet->setCellValue($columnHeaderEnd . $rowHeaderStart, 'On Kenpin Process');
        // on kenpin process lembar
        $activeWorksheet->setCellValue($columnHeaderEnd . $rowHeaderEnd, 'Lembar');
        $columnHeaderEnd++;
        // on kenpin process box
        $activeWorksheet->setCellValue($columnHeaderKenpinProcess . $rowHeaderEnd, 'Box');
        $columnHeaderEnd++;
        // kenpin loss
        $columnHeaderKenpinLoss = $columnHeaderEnd;
        $columnHeaderKenpinLoss++;
        $spreadsheet->getActiveSheet()->mergeCells($columnHeaderEnd . $rowHeaderStart . ':' . $columnHeaderKenpinLoss . $rowHeaderStart);
        $activeWorksheet->setCellValue($columnHeaderEnd . $rowHeaderStart, 'Kenpin Loss');
        // kenpin loss lembar
        $activeWorksheet->setCellValue($columnHeaderEnd . $rowHeaderEnd, 'Lembar');
        $columnHeaderEnd++;
        // kenpin loss box
        $activeWorksheet->setCellValue($columnHeaderKenpinLoss . $rowHeaderEnd, 'Box');
        $columnHeaderEnd++;
        // actual quantity
        $columnHeaderActualQuantity = $columnHeaderEnd;
        $columnHeaderActualQuantity++;
        $spreadsheet->getActiveSheet()->mergeCells($columnHeaderEnd . $rowHeaderStart . ':' . $columnHeaderActualQuantity . $rowHeaderStart);
        $activeWorksheet->setCellValue($columnHeaderEnd . $rowHeaderStart, 'Actual Quantity');
        // actual quantity lembar
        $activeWorksheet->setCellValue($columnHeaderEnd . $rowHeaderEnd, 'Lembar');
        $columnHeaderEnd++;
        // actual quantity box
        $activeWorksheet->setCellValue($columnHeaderActualQuantity . $rowHeaderEnd, 'Box');

        // style header
        phpspreadsheet::addFullBorder($spreadsheet, $columnHeaderStart . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderEnd);
        phpspreadsheet::styleFont($spreadsheet, $columnHeaderStart . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderEnd, true, 9, 'Calibri');
        phpspreadsheet::textAlignCenter($spreadsheet, $columnHeaderStart . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderEnd);

        $data = DB::select("
            SELECT
                prd.code AS product_code,
                prd.code || ' ' || prd.code_alias || ' ' || prd.name AS product_name,
                lpk.lpk_no,
                good.nomor_palet,
                good.production_date,
                good.work_shift,
                good.nomor_lot,
                (good.qty_produksi * prd.unit_weight * 0.001) AS berat_produksi,
                good.qty_produksi,
                (good.qty_produksi / prd.case_box_count) AS qty_produksi_box,
                good.kenpin_qty_loss_proses,
                (good.kenpin_qty_loss_proses / prd.case_box_count) AS kenpin_qty_box_proses,
                good.kenpin_qty_loss,
                (good.kenpin_qty_loss / prd.case_box_count) AS kenpin_qty_box,
                (good.kenpin_qty_loss_proses * prd.unit_weight * 0.001) AS kenpin_qty_berat_proses,
                (good.kenpin_qty_loss * prd.unit_weight * 0.001) AS kenpin_qty_berat
            FROM tdProduct_Goods AS good
            INNER JOIN tdOrderLpk AS lpk ON good.lpk_id = lpk.id
            INNER JOIN msProduct AS prd ON good.product_id = prd.id
            WHERE good.production_date BETWEEN '$tglMasuk' AND '$tglKeluar';
        ");

        if (count($data) == 0) {
            $response = [
                'status' => 'error',
                'message' => "Data pada periode tanggal tersebut tidak ditemukan"
            ];

            return $response;
        }

        $dataFilter = [];

        foreach ($data as $item) {
            // List Produk
            $listProduct[$item->product_code] = $item->product_name;

            // List LPK
            $listLpk[$item->product_code][$item->lpk_no] = $item->lpk_no;

            // List Palet
            $listPalet[$item->product_code][$item->lpk_no][$item->nomor_palet] = $item->nomor_palet;

            // Data Filter
            $dataFilter[$item->product_code][$item->lpk_no][$item->nomor_palet][$item->production_date] = (object) [
                'production_date' => $item->production_date,
                'work_shift' => $item->work_shift,
                'nomor_lot' => $item->nomor_lot,
                'qty_produksi' => $item->qty_produksi,
                'qty_produksi_box' => $item->qty_produksi_box,
                'kenpin_qty_loss_proses' => $item->kenpin_qty_loss_proses,
                'kenpin_qty_box_proses' => $item->kenpin_qty_box_proses,
                'kenpin_qty_loss' => $item->kenpin_qty_loss,
                'kenpin_qty_box' => $item->kenpin_qty_box,
            ];
        }

        // index
        $startColumnItem = 'B';
        $endColumnItem = $columnHeaderEnd;
        $startColumnItemData = 'D';
        $columnNoLPK = 'B';
        $columnNoPalet = 'C';
        $startRowItem = 5;
        $rowItem = $startRowItem;

        $grandTotal = [
            'qty_produksi' => 0,
            'qty_produksi_box' => 0,
            'kenpin_qty_loss_proses' => 0,
            'kenpin_qty_box_proses' => 0,
            'kenpin_qty_loss' => 0,
            'kenpin_qty_box' => 0,
            'qty_produksi' => 0,
            'qty_produksi_box' => 0
        ];

        // create excel
        foreach ($listProduct as $productCode => $productName) {
            // Menulis data produk
            $activeWorksheet->setCellValue($startColumnItem . $rowItem, $productName);
            $spreadsheet->getActiveSheet()->mergeCells($startColumnItem . $rowItem . ':' . $endColumnItem . $rowItem);
            phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowItem, true, 9, 'Calibri');
            $rowItem++;
            $startRowItemSum = $rowItem;
            foreach ($listLpk[$productCode] as $noLPK) {
                // Menulis data no lpk
                $activeWorksheet->setCellValue($columnNoLPK . $rowItem, $noLPK);
                // daftar palet
                foreach ($listPalet[$productCode][$noLPK] as $palet) {
                    if ($dataFilter[$productCode][$noLPK][$palet] == null) {
                        continue;
                    }
                    // Menulis data palet
                    $spreadsheet->getActiveSheet()->setCellValue($columnNoPalet . $rowItem, $palet);

                    // memasukkan data
                    $dataItem = $dataFilter[$productCode][$noLPK][$palet];
                    $startRowItemData = $rowItem;
                    foreach ($dataItem as $item) {
                        $columnItem = $startColumnItemData;
                        // tanggal produksi
                        $activeWorksheet->setCellValue($columnItem . $rowItem, $item->production_date);
                        phpspreadsheet::textAlignCenter($spreadsheet, $columnItem . $rowItem);
                        $columnItem++;
                        // kode shift
                        $activeWorksheet->setCellValue($columnItem . $rowItem, $item->work_shift ?? '');
                        phpspreadsheet::textAlignCenter($spreadsheet, $columnItem . $rowItem);
                        $columnItem++;
                        // nomor lot
                        $activeWorksheet->setCellValue($columnItem . $rowItem, $item->nomor_lot ?? '');
                        phpspreadsheet::textAlignCenter($spreadsheet, $columnItem . $rowItem);
                        $columnItem++;
                        /**
                         * Prodction quantity
                         */
                        // lembar
                        $activeWorksheet->setCellValue($columnItem . $rowItem, $item->qty_produksi ?? '');
                        phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
                        $columnItem++;
                        $dataQtyProduksi[] = $item->qty_produksi;
                        // box
                        $activeWorksheet->setCellValue($columnItem . $rowItem, $item->qty_produksi_box ?? '');
                        phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
                        $columnItem++;
                        /**
                         * On Kenpin Process
                         */
                        // lembar
                        $activeWorksheet->setCellValue($columnItem . $rowItem, $item->kenpin_qty_loss_proses ?? '');
                        phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
                        $columnItem++;
                        // box
                        $activeWorksheet->setCellValue($columnItem . $rowItem, $item->kenpin_qty_box_proses ?? '');
                        phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
                        $columnItem++;
                        /**
                         * Kenpin Loss
                         */
                        // lembar
                        $activeWorksheet->setCellValue($columnItem . $rowItem, $item->kenpin_qty_loss ?? '');
                        phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
                        $columnItem++;
                        // box
                        $activeWorksheet->setCellValue($columnItem . $rowItem, $item->kenpin_qty_box ?? '');
                        phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
                        $columnItem++;
                        /**
                         * Actual quantity
                         */
                        // lembar
                        $activeWorksheet->setCellValue($columnItem . $rowItem, $item->qty_produksi ?? '');
                        phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
                        $columnItem++;
                        // box
                        $activeWorksheet->setCellValue($columnItem . $rowItem, $item->qty_produksi_box ?? '');
                        phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
                        phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);
                        $columnItem++;
                        $rowItem++;

                        // perhitungan grand total
                        $grandTotal['qty_produksi'] += $item->qty_produksi;
                        $grandTotal['qty_produksi_box'] += $item->qty_produksi_box;
                        $grandTotal['kenpin_qty_loss_proses'] += $item->kenpin_qty_loss_proses;
                        $grandTotal['kenpin_qty_box_proses'] += $item->kenpin_qty_box_proses;
                        $grandTotal['kenpin_qty_loss'] += $item->kenpin_qty_loss;
                        $grandTotal['kenpin_qty_box'] += $item->kenpin_qty_box;
                    }

                    phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $startRowItemData . ':' . $columnItem . $rowItem, false, 8, 'Calibri');
                }
                // $rowItem++;
            }
            // perhitungan jumlah berdasarkan produk
            $spreadsheet->getActiveSheet()->mergeCells($startColumnItem . $rowItem . ':' . 'F' . $rowItem);
            $activeWorksheet->setCellValue($startColumnItem . $rowItem, 'Total');
            $columnItem = $startColumnItemData;
            $columnItem++;
            $columnItem++;
            $columnItem++;
            /**
             * Prodction quantity
             */
            // lembar
            $activeWorksheet->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . $startRowItemSum . ':' . $columnItem . ($rowItem - 1) . ')');
            phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // box
            $activeWorksheet->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . $startRowItemSum . ':' . $columnItem . ($rowItem - 1) . ')');
            phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            /**
             * On Kenpin Process
             */
            // lembar
            $activeWorksheet->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . $startRowItemSum . ':' . $columnItem . ($rowItem - 1) . ')');
            phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // box
            $activeWorksheet->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . $startRowItemSum . ':' . $columnItem . ($rowItem - 1) . ')');
            phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            /**
             * Kenpin Loss
             */
            // lembar
            $activeWorksheet->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . $startRowItemSum . ':' . $columnItem . ($rowItem - 1) . ')');
            phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // box
            $activeWorksheet->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . $startRowItemSum . ':' . $columnItem . ($rowItem - 1) . ')');
            phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            /**
             * Actual Quantity
             */
            // lembar
            $activeWorksheet->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . $startRowItemSum . ':' . $columnItem . ($rowItem - 1) . ')');
            phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // box
            $activeWorksheet->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . $startRowItemSum . ':' . $columnItem . ($rowItem - 1) . ')');
            phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
            phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);
            phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem, true, 8, 'Calibri');
            $columnItem++;

            $rowItem++;
            $rowItem++;
        }

        // Grand total
        $rowGrandTotal = $rowItem;
        $spreadsheet->getActiveSheet()->mergeCells($startColumnItem . $rowGrandTotal . ':' . 'E' . $rowGrandTotal);
        $spreadsheet->getActiveSheet()->setCellValue($startColumnItem . $rowGrandTotal, 'GRAND TOTAL');
        phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowGrandTotal . ':' . $columnHeaderEnd . $rowGrandTotal, true, 8, 'Calibri');

        $columnItem = $startColumnItemData;
        $columnItem++;
        $columnItem++;
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['qty_produksi']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['qty_produksi_box']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['kenpin_qty_loss_proses']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['kenpin_qty_box_proses']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['kenpin_qty_loss']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['kenpin_qty_box']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['qty_produksi']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['qty_produksi_box']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowGrandTotal . ':' . $columnItem . $rowGrandTotal);
        $columnItem++;

        $activeWorksheet->getStyle($columnHeaderStart . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart)->getAlignment()->setWrapText(true);

        // size auto
        $columnHeaderEnd = $columnHeaderStart;
        $endColumnItem++;
        $columnHeaderEnd++;
        while ($columnHeaderEnd !== $endColumnItem) {
            $spreadsheet->getActiveSheet()->getColumnDimension($columnHeaderEnd)->setAutoSize(true);
            $columnHeaderEnd++;
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'asset/report/' . $this->nipon . '-' . $this->jenisreport . '.xlsx';
        $writer->save($filename);
        $response = [
            'status' => 'success',
            'filename' => $filename
        ];
        return $response;
    }

    public function daftarProduksiPerMesinPerProdukInfure($tglMasuk, $tglKeluar)
    {
        return ProduksiPerMesinPerProdukReportService::daftarProduksiPerMesinPerProdukInfure($this->nipon, $this->jenisreport, $tglMasuk, $tglKeluar);
    }

    public function daftarProduksiPerMesinPerProdukSeitai($tglMasuk, $tglKeluar)
    {
        return ProduksiPerMesinPerProdukReportService::daftarProduksiPerMesinPerProdukSeitai($this->nipon, $this->jenisreport, $tglMasuk, $tglKeluar);
    }

    public function jamMatiPerMesin($tglMasuk, $tglKeluar)
    {
        return JamMatiReportService::jamMatiPerMesin($this->nipon, $this->jenisreport, $tglMasuk, $tglKeluar);
    }

    public function jamMatiPerJenis($tglMasuk, $tglKeluar)
    {
        return JamMatiReportService::jamMatiPerJenis($this->nipon, $this->jenisreport, $tglMasuk, $tglKeluar);
    }

    public function daftarLossPerMesinJenis($tglMasuk, $tglKeluar)
    {
        return LossKasusReportService::daftarLossPerMesinJenis($this->nipon, $this->jenisreport, $tglMasuk, $tglKeluar);
    }

    public function daftarKasusLossPerMesinJenis($tglMasuk, $tglKeluar)
    {
        return LossKasusReportService::daftarKasusLossPerMesinJenis($this->nipon, $this->jenisreport, $tglMasuk, $tglKeluar);
    }

    public function render()
    {
        return view('livewire.report.general-report')->extends('layouts.master');
    }
}
