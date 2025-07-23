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
use App\Http\Livewire\Report\GeneralReport\JamMatiPerMesinService;
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
        $this->workingShiftHour = MsWorkingShift::select('work_hour_from', 'work_hour_till')->where('status', 1)->orderBy('work_hour_from', 'ASC')->get();
        $this->jamMasuk = $this->workingShiftHour[0]->work_hour_from;
        $this->jamKeluar = $this->workingShiftHour[count($this->workingShiftHour) - 1]->work_hour_till;
    }

    public function exportOld()
    {
        // mengecek apakah jenis report sudah dipilih atau belum
        if ($this->jenisreport == null) {
            $this->dispatch('notification', ['type' => 'warning', 'message' => 'Pilih Jenis Report.']);
            return;
        }
        switch ($this->jenisreport) {
            case 'Daftar Produksi Per Mesin':
                if ($this->nipon == 'Infure') {
                    return Excel::download(new GeneralReportExport(
                        $this->tglMasuk,
                        $this->tglKeluar,
                        $this->nipon,
                        $this->jenisreport,
                    ), 'Infure_Daftar Produksi Per Mesin.xlsx');
                } else {
                    return Excel::download(new GeneralReportExport(
                        $this->tglMasuk,
                        $this->tglKeluar,
                        $this->nipon,
                        $this->jenisreport,
                    ), 'Seitai_Daftar Produksi Per Mesin.xlsx');
                }

                break;

            case 'Daftar Produksi Per Tipe Per Mesin':
                if ($this->nipon == 'Infure') {
                    return Excel::download(new GeneralReportExport(
                        $this->tglMasuk,
                        $this->tglKeluar,
                        $this->nipon,
                        $this->jenisreport,
                    ), 'Infure_Daftar Produksi Per Mesin dan Type.xlsx');
                } else {
                    return Excel::download(new GeneralReportExport(
                        $this->tglMasuk,
                        $this->tglKeluar,
                        $this->nipon,
                        $this->jenisreport,
                    ), 'Seitai_Daftar Produksi Per Mesin dan Type.xlsx');
                }

                break;

            case 'Daftar Produksi Per Jenis':
                if ($this->nipon == 'Infure') {
                    return Excel::download(new GeneralReportExport(
                        $this->tglMasuk,
                        $this->tglKeluar,
                        $this->nipon,
                        $this->jenisreport,
                    ), 'Infure_Daftar Produksi Per Jenis.xlsx');
                } else {
                    return Excel::download(new GeneralReportExport(
                        $this->tglMasuk,
                        $this->tglKeluar,
                        $this->nipon,
                        $this->jenisreport,
                    ), 'Seitai_Daftar Produksi Per Jenis.xlsx');
                }

                break;
            case 'Daftar Produksi Per Tipe':
                if ($this->nipon == 'Infure') {
                    return Excel::download(new GeneralReportExport(
                        $this->tglMasuk,
                        $this->tglKeluar,
                        $this->nipon,
                        $this->jenisreport,
                    ), 'Infure_Daftar Produksi Per Tipe.xlsx');
                } else {
                    return Excel::download(new GeneralReportExport(
                        $this->tglMasuk,
                        $this->tglKeluar,
                        $this->nipon,
                        $this->jenisreport,
                    ), 'Seitai_Daftar Produksi Per Tipe.xlsx');
                }

                break;
            case 'Daftar Produksi Per Produk':
                if ($this->nipon == 'Infure') {
                    return Excel::download(new GeneralReportExport(
                        $this->tglMasuk,
                        $this->tglKeluar,
                        $this->nipon,
                        $this->jenisreport,
                    ), 'Infure_Daftar Produksi Per Produk.xlsx');
                } else {
                    return Excel::download(new GeneralReportExport(
                        $this->tglMasuk,
                        $this->tglKeluar,
                        $this->nipon,
                        $this->jenisreport,
                    ), 'Seitai_Daftar Produksi Per Produk.xlsx');
                }

                break;
            case 'Daftar Produksi Per Departemen Per Jenis':
                if ($this->nipon == 'Infure') {
                    return Excel::download(new GeneralReportExport(
                        $this->tglMasuk,
                        $this->tglKeluar,
                        $this->nipon,
                        $this->jenisreport,
                    ), 'Infure_Daftar Produksi Per Departemen Per Jenis.xlsx');
                } else {
                    return Excel::download(new GeneralReportExport(
                        $this->tglMasuk,
                        $this->tglKeluar,
                        $this->nipon,
                        $this->jenisreport,
                    ), 'Seitai_Daftar Produksi Per Departemen Per Jenis.xlsx');
                }

                break;
            case 'Daftar Produksi Per Departemen & Tipe':
                if ($this->nipon == 'Infure') {
                    return Excel::download(new GeneralReportExport(
                        $this->tglMasuk,
                        $this->tglKeluar,
                        $this->nipon,
                        $this->jenisreport,
                    ), 'Infure_Daftar Produksi Per Departemen & Tipe.xlsx');
                } else {
                    return Excel::download(new GeneralReportExport(
                        $this->tglMasuk,
                        $this->tglKeluar,
                        $this->nipon,
                        $this->jenisreport,
                    ), 'Seitai_Daftar Produksi Per Departemen & Tipe.xlsx');
                }

                break;
            case 'Daftar Produksi Per Departemen & Petugas':
                if ($this->nipon == 'Infure') {
                    return Excel::download(new GeneralReportExport(
                        $this->tglMasuk,
                        $this->tglKeluar,
                        $this->nipon,
                        $this->jenisreport,
                    ), 'Infure_Daftar Produksi Per Departemen & Petugas.xlsx');
                } else {
                    return Excel::download(new GeneralReportExport(
                        $this->tglMasuk,
                        $this->tglKeluar,
                        $this->nipon,
                        $this->jenisreport,
                    ), 'Seitai_Daftar Produksi Per Departemen & Petugas.xlsx');
                }

                break;
            case 'Daftar Produksi Per Palet':
                // dd($this->nipon);
                if ($this->nipon == 2) {
                    return Excel::download(new GeneralReportExport(
                        $this->tglMasuk,
                        $this->tglKeluar,
                        $this->nipon,
                        $this->jenisreport,
                    ), 'Infure_Daftar Produksi Per Palet.xlsx');
                }

                break;
            case 'Daftar Loss Per Departemen':
                if ($this->nipon == 'Infure') {
                    return Excel::download(new GeneralReportExport(
                        $this->tglMasuk,
                        $this->tglKeluar,
                        $this->nipon,
                        $this->jenisreport,
                    ), 'Infure_Daftar Loss Per Departemen.xlsx');
                } else {
                    return Excel::download(new GeneralReportExport(
                        $this->tglMasuk,
                        $this->tglKeluar,
                        $this->nipon,
                        $this->jenisreport,
                    ), 'Seitai_Daftar Loss Per Departemen.xlsx');
                }

                break;
            case 'Daftar Loss Per Departemen & Jenis':
                if ($this->nipon == 'Infure') {
                    return Excel::download(new GeneralReportExport(
                        $this->tglMasuk,
                        $this->tglKeluar,
                        $this->nipon,
                        $this->jenisreport,
                    ), 'Infure_Daftar Loss Per Departemen & Jenis.xlsx');
                } else {
                    return Excel::download(new GeneralReportExport(
                        $this->tglMasuk,
                        $this->tglKeluar,
                        $this->nipon,
                        $this->jenisreport,
                    ), 'Seitai_Daftar Loss Per Departemen & Jenis.xlsx');
                }

                break;
            case 'Daftar Loss Per Petugas':
                if ($this->nipon == 'Infure') {
                    return Excel::download(new GeneralReportExport(
                        $this->tglMasuk,
                        $this->tglKeluar,
                        $this->nipon,
                        $this->jenisreport,
                    ), 'Infure_Daftar Loss Per Petugas.xlsx');
                } else {
                    return Excel::download(new GeneralReportExport(
                        $this->tglMasuk,
                        $this->tglKeluar,
                        $this->nipon,
                        $this->jenisreport,
                    ), 'Seitai_Daftar Loss Per Petugas.xlsx');
                }

                break;
            case 'Daftar Loss Per Mesin':
                if ($this->nipon == 'Infure') {
                    return Excel::download(new GeneralReportExport(
                        $this->tglMasuk,
                        $this->tglKeluar,
                        $this->nipon,
                        $this->jenisreport,
                    ), 'Infure_Daftar Loss Per Mesin.xlsx');
                } else {
                    return Excel::download(new GeneralReportExport(
                        $this->tglMasuk,
                        $this->tglKeluar,
                        $this->nipon,
                        $this->jenisreport,
                    ), 'Seitai_Daftar Loss Per Mesin.xlsx');
                }

                break;
            case 'Kapasitas Produksi':
                if ($this->nipon == 'Infure') {
                    return Excel::download(new GeneralReportExport(
                        $this->tglMasuk,
                        $this->tglKeluar,
                        $this->nipon,
                        $this->jenisreport,
                    ), 'Infure_Kapasitas Produksi.xlsx');
                } else {
                    return Excel::download(new GeneralReportExport(
                        $this->tglMasuk,
                        $this->tglKeluar,
                        $this->nipon,
                        $this->jenisreport,
                    ), 'Seitai_Kapasitas Produksi.xlsx');
                }
                break;
            default:
                // dd('ini percobaan');
                session()->flash('notification', ['type' => 'warning', 'message' => 'Pilih Jenis Report.']);
        }
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
        // hide column A
        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setVisible(false);

        // Judul
        $activeWorksheet->setCellValue('B1', 'DAFTAR PRODUKSI PER MESIN INFURE');
        $activeWorksheet->setCellValue('B2', 'Periode : ' . $tglMasuk . ' s/d ' . $tglKeluar);
        // Style Judul
        phpspreadsheet::styleFont($spreadsheet, 'B1:B2', true, 11, 'Calibri');

        // Header
        $columnMesin = 'B';
        $columnMesinEnd = 'C';
        $spreadsheet->getActiveSheet()->mergeCells($columnMesin . '3:' . $columnMesinEnd . '3');
        $activeWorksheet->setCellValue('B3', 'Mesin');

        // header
        $rowHeaderStart = 3;
        $columnHeaderStart = 'D';
        $columnHeaderEnd = 'D';
        $header = [
            'Berat Standar (Kg)',
            'Berat Produksi (Kg)',
            'Weight Rate',
            'Infure Cost',
            'Loss (Kg)',
            'Loss (%)',
            'Panjang Infure (meter)',
            'Inline Printing (meter)',
            'Inline Printing Cost',
            'Process Cost',
            'Jam Jalan (h:m)',
            'Jam Off (h:m)',
            'Jalan Mesin (%)',
        ];

        foreach ($header as $key => $value) {
            $activeWorksheet->setCellValue($columnHeaderEnd . $rowHeaderStart, $value);
            $columnHeaderEnd++;
        }
        $columnHeaderEnd = chr(ord($columnHeaderEnd) - 1);

        // style header
        phpspreadsheet::addFullBorder($spreadsheet, $columnMesin . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);
        phpspreadsheet::styleFont($spreadsheet, $columnMesin . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart, true, 9, 'Calibri');
        phpspreadsheet::textAlignCenter($spreadsheet, $columnMesin . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);

        $data = DB::select("
            WITH jam AS (
                SELECT
                    jam_.machine_id,
                    SUM(EXTRACT(EPOCH FROM work_hour) / 60) AS work_hour,
                    SUM(EXTRACT(EPOCH FROM off_hour) / 60) AS off_hour,
                    SUM(EXTRACT(EPOCH FROM on_hour) / 60) AS on_hour
                FROM
                    tdJamKerjaMesin AS jam_
                INNER JOIN
                    msworkingshift AS ws ON jam_.work_shift = ws.id
                WHERE
                    (working_date || ' ' || work_hour_from)::TIMESTAMP
                    BETWEEN '$tglMasuk' AND '$tglKeluar'
                GROUP BY
                    jam_.machine_id
            )
            SELECT
                MAX(mac.machineNo) AS machine_no,
                MAX(mac.machineName) AS machine_name,
                MAX(dep.ID) AS department_id,
                MAX(dep.NAME) AS department_name,
                SUM(asy.berat_standard) AS berat_standard,
                SUM(asy.berat_produksi) AS berat_produksi,
                SUM(asy.infure_cost) AS infure_cost,
                SUM(asy.infure_berat_loss) AS infure_berat_loss,
                SUM(asy.panjang_produksi) AS panjang_produksi,
                SUM(asy.panjang_printing_inline) AS panjang_printing_inline,
                SUM(asy.infure_cost_printing) AS infure_cost_printing,
                COALESCE(MAX(jam.work_hour), 0) AS work_hour_mm,
                COALESCE(MAX(jam.off_hour), 0) AS work_hour_off_mm,
                COALESCE(MAX(jam.on_hour), 0) AS work_hour_on_mm
            FROM
                tdProduct_Assembly AS asy
            LEFT JOIN
                jam ON asy.machine_id = jam.machine_id
            LEFT JOIN
                msMachine AS mac ON asy.machine_id = mac.ID
            LEFT JOIN
                msDepartment AS dep ON mac.department_id = dep.ID
            WHERE
                asy.production_date BETWEEN '$tglMasuk' AND '$tglKeluar'
            GROUP BY
                asy.machine_id;
        ");

        if (count($data) == 0) {
            $response = [
                'status' => 'error',
                'message' => "Data pada periode tanggal tersebut tidak ditemukan"
            ];

            return $response;
        }

        $listDepartment = array_reduce($data, function ($carry, $item) {
            $carry[$item->department_id] = [
                'department_id' => $item->department_id,
                'department_name' => $item->department_name
            ];
            return $carry;
        }, []);

        // list mesin berdasarkan tanggal pertama dan departemen
        $listMachine = MsMachine::where('status', 1)
            ->whereIn('department_id', array_keys($listDepartment))
            ->orderBy('machineno')
            ->get()
            ->groupBy('department_id')
            ->map(function ($item) {
                return $item->pluck('machinename', 'machineno');
            });

        $dataFilter = array_reduce($data, function ($carry, $item) {
            $carry[$item->department_id][$item->machine_no] = $item;
            return $carry;
        }, []);

        // index
        $startColumnItem = 'B';
        $endColumnItem = $columnHeaderEnd;
        $startColumnItemData = 'D';
        $columnMachineNo = 'B';
        $columnMachineName = 'C';
        $startRowItem = 5;
        $rowItem = $startRowItem;
        // daftar departemen
        foreach ($listDepartment as $department) {
            // Menulis data departemen
            $activeWorksheet->setCellValue($startColumnItem . $rowItem, $department['department_name']);
            $spreadsheet->getActiveSheet()->mergeCells($startColumnItem . $rowItem . ':' . $endColumnItem . $rowItem);
            phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowItem, true, 9, 'Calibri');
            $rowItem++;
            $countMachine = 0;
            // daftar mesin
            foreach ($listMachine[$department['department_id']] as $machineNo => $machineName) {
                // memasukkan data
                $dataItem = $dataFilter[$department['department_id']][$machineNo] ?? (object)[
                    'berat_standard' => 0,
                    'berat_produksi' => 0,
                    'infure_cost' => 0,
                    'infure_berat_loss' => 0,
                    'panjang_produksi' => 0,
                    'panjang_printing_inline' => 0,
                    'infure_cost_printing' => 0,
                    'work_hour_mm' => 0,
                    'work_hour_off_mm' => 0,
                    'work_hour_on_mm' => 0,
                ];
                if ($dataItem->berat_standard == 0 && $dataItem->berat_produksi == 0) {
                    continue;
                }
                $countMachine++;
                $columnItem = $startColumnItemData;

                // Menulis data mesin
                $spreadsheet->getActiveSheet()->setCellValue($columnMachineNo . $rowItem, $machineNo);
                $spreadsheet->getActiveSheet()->setCellValue($columnMachineName . $rowItem, $machineName);
                // phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);

                // berat standard
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_standard);
                phpSpreadsheet::numberFormatCommaThousandsOrZero($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // berat produksi
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_produksi);
                phpSpreadsheet::numberFormatCommaThousandsOrZero($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // weight rate
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_standard > 0 ? $dataItem->berat_produksi / $dataItem->berat_standard : 0);
                phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem, 3);
                $columnItem++;
                // infure cost
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->infure_cost);
                phpSpreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // infure berat loss
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->infure_berat_loss);
                phpSpreadsheet::numberFormatCommaThousandsOrZero($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // loss %
                $lossPercentage = $dataItem->berat_produksi > 0 ? $dataItem->infure_berat_loss / ($dataItem->berat_produksi + $dataItem->infure_berat_loss) : 0;
                $activeWorksheet->setCellValue($columnItem . $rowItem, $lossPercentage);
                phpspreadsheet::numberPercentageOrZero($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // panjang produksi
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->panjang_produksi);
                phpSpreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // panjang printing inline
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->panjang_printing_inline);
                phpSpreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // infure cost printing
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->infure_cost_printing);
                phpSpreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // process cost
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->infure_cost + $dataItem->infure_cost_printing);
                phpSpreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
                $columnItem++;

                // jam kerja
                $workHours = $dataItem->work_hour_on_mm; // Ambil nilai menit dari data
                $hours = floor($workHours / 60); // Hitung jumlah jam
                $minutes = $workHours % 60; // Hitung sisa menit
                if ($hours == 0 && $minutes == 0) {
                    $activeWorksheet->setCellValue($columnItem . $rowItem, '');
                } else {
                    $formatedWorkHours = sprintf('%d:%02d', $hours, $minutes);
                    $activeWorksheet->setCellValue($columnItem . $rowItem, $formatedWorkHours);
                }
                $columnItem++;

                // jam mati
                $offHours = $dataItem->work_hour_off_mm; // Ambil nilai menit dari data
                $hours = floor($offHours / 60); // Hitung jumlah jam
                $minutes = $offHours % 60; // Hitung sisa menit
                $formatedOffHours = sprintf('%d:%02d', $hours, $minutes);
                $activeWorksheet->setCellValue($columnItem . $rowItem, $formatedOffHours);
                $columnItem++;

                // jalan mesin
                $activeWorksheet->setCellValue($columnItem . $rowItem, $workHours > 0 ? $workHours / ($offHours + $workHours) : 0);
                phpspreadsheet::numberPercentageOrZero($spreadsheet, $columnItem . $rowItem);
                phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);
                $columnItem++;

                phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem, false, 8, 'Calibri');
                $rowItem++;
            }
            // perhitungan jumlah berdasarkan departemen
            $spreadsheet->getActiveSheet()->mergeCells($columnMachineNo . $rowItem . ':' . $columnMachineName . $rowItem);
            // $activeWorksheet->setCellValue($columnMachineNo . $rowItem, 'Total ' . $department['department_name']);
            $columnItem = $startColumnItemData;
            $columnItemEnd = chr(ord($columnItem) + count($header) - 1);
            // berat standard
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - $countMachine) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatCommaThousandsOrZero($spreadsheet, $columnItem . $rowItem, 1);
            $columnItem++;
            // berat produksi
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - $countMachine) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatCommaThousandsOrZero($spreadsheet, $columnItem . $rowItem, 1);
            $columnItem++;
            // weight rate
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - $countMachine) . ':' . $columnItem . ($rowItem - 1) . ')/COUNTIF(' . $columnItem . ($rowItem - $countMachine) . ':' . $columnItem . ($rowItem - 1) . ', "<>0")');
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // infure cost
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - $countMachine) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // infure berat loss
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - $countMachine) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatCommaThousandsOrZero($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // loss %
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - $countMachine) . ':' . $columnItem . ($rowItem - 1) . ')/COUNTIF(' . $columnItem . ($rowItem - $countMachine) . ':' . $columnItem . ($rowItem - 1) . ', "<>0")');
            phpspreadsheet::numberPercentageOrZero($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // panjang produksi
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - $countMachine) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // panjang printing inline
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - $countMachine) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // infure cost printing
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - $countMachine) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // process cost
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - $countMachine) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // jam kerja
            $jamKerjaMesin = array_reduce(array_keys($listMachine[$department['department_id']]->toArray()), function ($carry, $item) use ($dataFilter, $department) {
                $dataItem = $dataFilter[$department['department_id']][$item] ?? (object)[
                    'berat_standard' => 0,
                    'berat_produksi' => 0,
                    'infure_cost' => 0,
                    'infure_berat_loss' => 0,
                    'panjang_produksi' => 0,
                    'panjang_printing_inline' => 0,
                    'infure_cost_printing' => 0,
                    'work_hour_mm' => 0,
                    'work_hour_off_mm' => 0,
                    'work_hour_on_mm' => 0,
                ];
                $carry['workHours'] += $dataItem->work_hour_on_mm;
                $carry['offHours'] += $dataItem->work_hour_off_mm;
                return $carry;
            }, ['workHours' => 0, 'offHours' => 0]);
            $hours = floor($jamKerjaMesin['workHours'] / 60); // Hitung jumlah jam
            $minutes = $jamKerjaMesin['workHours'] % 60; // Hitung sisa menit
            $formatedWorkHours = sprintf('%d:%02d', $hours, $minutes);
            $activeWorksheet->setCellValue($columnItem . $rowItem, $formatedWorkHours);
            $columnItem++;

            // jam mati
            $hours = floor($jamKerjaMesin['offHours'] / 60); // Hitung jumlah jam
            $minutes = $jamKerjaMesin['offHours'] % 60; // Hitung sisa menit
            $formatedOffHours = sprintf('%d:%02d', $hours, $minutes);
            $activeWorksheet->setCellValue($columnItem . $rowItem, $formatedOffHours);
            $columnItem++;

            $avgJamKerjaMesin = $jamKerjaMesin['workHours'] > 0 ? $jamKerjaMesin['workHours'] / ($jamKerjaMesin['offHours'] + $jamKerjaMesin['workHours']) : 0;
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, $avgJamKerjaMesin);
            phpspreadsheet::numberPercentageOrZero($spreadsheet, $columnItem . $rowItem);
            phpspreadsheet::addFullBorder($spreadsheet, $columnMachineNo . $rowItem . ':' . $columnItem . $rowItem);
            phpspreadsheet::styleFont($spreadsheet, $columnMachineNo . $rowItem . ':' . $columnItem . $rowItem, true, 8, 'Calibri');
            $columnItem++;


            $rowItem++;
            $rowItem++;
        }

        // Grand total
        $rowGrandTotal = $rowItem;
        $spreadsheet->getActiveSheet()->mergeCells($columnMachineNo . $rowGrandTotal . ':' . $columnMachineName . $rowGrandTotal);
        $spreadsheet->getActiveSheet()->setCellValue($columnMachineNo . $rowGrandTotal, 'GRAND TOTAL');
        phpspreadsheet::styleFont($spreadsheet, $columnMachineNo . $rowGrandTotal . ':' . $columnHeaderEnd . $rowGrandTotal, true, 8, 'Calibri');
        // $this->addFullBorder($spreadsheet, $columnMachineNo . $rowGrandTotal . ':' . $columnValueAvg . $rowGrandTotal);

        // grand total
        $grandTotal = [
            'berat_standard' => 0,
            'berat_produksi' => 0,
            'infure_cost' => 0,
            'infure_berat_loss' => 0,
            'panjang_produksi' => 0,
            'panjang_printing_inline' => 0,
            'infure_cost_printing' => 0,
            'work_hour_mm' => 0,
            'work_hour_on_mm' => 0,
            'work_hour_off_mm' => 0,
        ];

        foreach ($dataFilter as $departmentId => $department) {
            foreach ($department as $machineNo => $machine) {
                $grandTotal['berat_standard'] += $machine->berat_standard;
                $grandTotal['berat_produksi'] += $machine->berat_produksi;
                $grandTotal['infure_cost'] += $machine->infure_cost;
                $grandTotal['infure_berat_loss'] += $machine->infure_berat_loss;
                $grandTotal['panjang_produksi'] += $machine->panjang_produksi;
                $grandTotal['panjang_printing_inline'] += $machine->panjang_printing_inline;
                $grandTotal['infure_cost_printing'] += $machine->infure_cost_printing;
                $grandTotal['work_hour_mm'] += $machine->work_hour_mm;
                $grandTotal['work_hour_on_mm'] += $machine->work_hour_on_mm;
                $grandTotal['work_hour_off_mm'] += $machine->work_hour_off_mm;
            }
        }

        $columnItem = $startColumnItemData;
        $columnItemEnd = chr(ord($columnItem) + count($header) - 1);
        // berat standard
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_standard']);
        phpSpreadsheet::numberFormatCommaThousandsOrZero($spreadsheet, $columnItem . $rowGrandTotal, 1);
        $columnItem++;
        // berat produksi
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_produksi']);
        phpSpreadsheet::numberFormatCommaThousandsOrZero($spreadsheet, $columnItem . $rowGrandTotal, 1);
        $columnItem++;
        // weight rate
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_standard'] > 0 ? $grandTotal['berat_produksi'] / $grandTotal['berat_standard'] : 0);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // infure cost
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['infure_cost']);
        phpSpreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // infure berat loss
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['infure_berat_loss']);
        phpSpreadsheet::numberFormatCommaThousandsOrZero($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // loss %
        $lossPercentageGrandTotal = $grandTotal['berat_produksi'] > 0 ? $grandTotal['infure_berat_loss'] / ($grandTotal['berat_produksi'] + $grandTotal['infure_berat_loss']) : 0;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $lossPercentageGrandTotal);
        phpspreadsheet::numberPercentageOrZero($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // panjang produksi
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['panjang_produksi']);
        phpSpreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // panjang printing inline
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['panjang_printing_inline']);
        phpSpreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // infure cost printing
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['infure_cost_printing']);
        phpSpreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // process cost
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['infure_cost'] + $grandTotal['infure_cost_printing']);
        phpSpreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;

        // jam kerja
        $hours = floor($grandTotal['work_hour_on_mm'] / 60); // Hitung jumlah jam
        $minutes = $grandTotal['work_hour_on_mm'] % 60; // Hitung sisa menit
        $formatedWorkHours = sprintf('%d:%02d', $hours, $minutes);
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $formatedWorkHours);
        $columnItem++;

        // jam mati
        $hours = floor($grandTotal['work_hour_off_mm'] / 60); // Hitung jumlah jam
        $minutes = $grandTotal['work_hour_off_mm'] % 60; // Hitung sisa menit
        $formatedOffHours = sprintf('%d:%02d', $hours, $minutes);
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $formatedOffHours);
        $columnItem++;

        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['work_hour_on_mm'] > 0 ? $grandTotal['work_hour_on_mm'] / ($grandTotal['work_hour_off_mm'] + $grandTotal['work_hour_on_mm']) : 0);
        phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowGrandTotal);
        phpspreadsheet::addFullBorder($spreadsheet, $columnMachineNo . $rowGrandTotal . ':' . $columnItem . $rowGrandTotal);

        $activeWorksheet->getStyle($columnMesin . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart)->getAlignment()->setWrapText(true);

        $writer = new Xlsx($spreadsheet);
        $filename = 'asset/report/' . $this->nipon . '-' . $this->jenisreport . '.xlsx';
        $writer->save($filename);
        $response = [
            'status' => 'success',
            'filename' => $filename
        ];
        return $response;
    }

    public function daftarProduksiPerMesinSeitai($tglMasuk, $tglKeluar)
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
        $activeWorksheet->setCellValue('B1', 'DAFTAR PRODUKSI PER MESIN SEITAI');
        $activeWorksheet->setCellValue('B2', 'Periode : ' . $tglMasuk . ' s/d ' . $tglKeluar);
        // Style Judul
        phpspreadsheet::styleFont($spreadsheet, 'B1:B2', true, 11, 'Calibri');
        // hide column A
        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setVisible(false);

        // Header
        $columnMesin = 'B';
        $columnMesinEnd = 'C';
        $spreadsheet->getActiveSheet()->mergeCells($columnMesin . '3:' . $columnMesinEnd . '3');
        $activeWorksheet->setCellValue('B3', 'Mesin');

        // header
        $rowHeaderStart = 3;
        $columnHeaderStart = 'D';
        $columnHeaderEnd = 'D';
        $header = [
            'Jumlah Produksi (Lembar)',
            'Berat Standar (Kg)',
            'Loss (Kg)',
            'Loss (%)',
            'Seitai Cost',
            'Ponsu Loss (Kg)',
            'Infure Loss (Kg)',
            'Produksi per jam (Lembar)',
            'Jalan Mesin (%)',
            'Jam Jalan (h:m)',
            'Jam Off (h:m)',
        ];

        foreach ($header as $key => $value) {
            $activeWorksheet->setCellValue($columnHeaderEnd . $rowHeaderStart, $value);
            $columnHeaderEnd++;
        }
        $columnHeaderEnd = chr(ord($columnHeaderEnd) - 1);

        // style header
        phpspreadsheet::addFullBorder($spreadsheet, $columnMesin . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);
        phpspreadsheet::styleFont($spreadsheet, $columnMesin . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart, true, 9, 'Calibri');
        phpspreadsheet::textAlignCenter($spreadsheet, $columnMesin . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);

        $data = DB::select("
            SELECT
                MAX(mac.machineNo) AS machine_no,
                MAX(mac.machineName) AS machine_name,
                MAX(dep.name) AS department_name,
                MAX(dep.id) AS department_id,
                SUM(good.qty_produksi) AS qty_produksi,
                SUM(good.qty_produksi * prd.unit_weight * 0.001) AS berat_produksi,
                SUM(good.seitai_berat_loss) - COALESCE(SUM(ponsu.berat_loss), 0) AS seitai_berat_loss,
                SUM(good.qty_produksi * prT.harga_sat_seitai) AS seitai_cost,
                COALESCE(SUM(ponsu.berat_loss), 0) AS seitai_berat_loss_ponsu,
                SUM(good.infure_berat_loss) AS infure_berat_loss,
                COALESCE(MAX(jam.work_hour), 0) AS work_hour_mm,
                COALESCE(MAX(jam.off_hour), 0) AS work_hour_off_mm,
                COALESCE(MAX(jam.on_hour), 0) AS work_hour_on_mm
            FROM tdProduct_Goods AS good
                LEFT JOIN (
                    SELECT
                        los_.product_goods_id,
                        SUM(los_.berat_loss) AS berat_loss
                    FROM tdProduct_Goods_Loss AS los_
                    WHERE los_.loss_seitai_id = 1
                    GROUP BY los_.product_goods_id
                ) ponsu ON good.id = ponsu.product_goods_id
                LEFT JOIN (
                    SELECT
                        jam_.machine_id,
                        SUM(EXTRACT(EPOCH FROM work_hour) / 60) AS work_hour,
                        SUM(EXTRACT(EPOCH FROM off_hour) / 60) AS off_hour,
                        SUM(EXTRACT(EPOCH FROM on_hour) / 60) AS on_hour
                    FROM tdJamKerjaMesin AS jam_
                    INNER JOIN msworkingshift AS ws ON jam_.work_shift = ws.id
                    WHERE
                        ( working_date :: TEXT || ' ' || work_hour_from :: TEXT ) :: TIMESTAMP BETWEEN '$tglMasuk' AND '$tglKeluar'
                    GROUP BY jam_.machine_id
                ) jam ON good.machine_id = jam.machine_id
                INNER JOIN msMachine AS mac ON good.machine_id = mac.id
                INNER JOIN msDepartment AS dep ON mac.department_id = dep.id
                INNER JOIN msProduct AS prd ON good.product_id = prd.id
                INNER JOIN msProduct_type AS prT ON prd.product_type_id = prT.id
            WHERE good.production_date BETWEEN  '$tglMasuk' AND '$tglKeluar'
            GROUP BY good.machine_id
        ");

        if (count($data) == 0) {
            $response = [
                'status' => 'error',
                'message' => "Data pada periode tanggal tersebut tidak ditemukan"
            ];

            return $response;
        }

        $listDepartment = array_reduce($data, function ($carry, $item) {
            $carry[$item->department_id] = [
                'department_id' => $item->department_id,
                'department_name' => $item->department_name
            ];
            return $carry;
        }, []);

        // list mesin berdasarkan tanggal pertama dan departemen
        $listMachine = MsMachine::where('status', 1)
            ->whereIn('department_id', array_keys($listDepartment))
            ->orderBy('machineno')
            ->get()
            ->groupBy('department_id')
            ->map(function ($item) {
                return $item->pluck('machinename', 'machineno');
            });

        $dataFilter = array_reduce($data, function ($carry, $item) {
            $carry[$item->department_id][$item->machine_no] = $item;
            return $carry;
        }, []);

        // index
        $startColumnItem = 'B';
        $endColumnItem = $columnHeaderEnd;
        $startColumnItemData = 'D';
        $columnMachineNo = 'B';
        $columnMachineName = 'C';
        $startRowItem = 5;
        $rowItem = $startRowItem;
        // daftar departemen
        foreach ($listDepartment as $department) {
            // Menulis data departemen
            $activeWorksheet->setCellValue($startColumnItem . $rowItem, $department['department_name']);
            $spreadsheet->getActiveSheet()->mergeCells($startColumnItem . $rowItem . ':' . $endColumnItem . $rowItem);
            phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowItem, true, 9, 'Calibri');
            $rowItem++;
            $countMachine = 0;
            // daftar mesin
            foreach ($listMachine[$department['department_id']] as $machineNo => $machineName) {
                // memasukkan data
                $dataItem = $dataFilter[$department['department_id']][$machineNo] ?? (object)[
                    'qty_produksi' => 0,
                    'berat_produksi' => 0,
                    'seitai_berat_loss' => 0,
                    'seitai_cost' => 0,
                    'seitai_berat_loss_ponsu' => 0,
                    'infure_berat_loss' => 0,
                    'work_hour_mm' => 0,
                    'work_hour_off_mm' => 0,
                    'work_hour_on_mm' => 0,
                ];
                if ($dataItem->qty_produksi == 0 && $dataItem->berat_produksi == 0) {
                    continue;
                }
                $countMachine++;
                $columnItem = $startColumnItemData;

                // Menulis data mesin
                $spreadsheet->getActiveSheet()->setCellValue($columnMachineNo . $rowItem, $machineNo);
                $spreadsheet->getActiveSheet()->setCellValue($columnMachineName . $rowItem, $machineName);
                // phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);

                // jumlah produksi
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->qty_produksi);
                phpspreadsheet::numberFormatCommaThousandsOrZero($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // berat_produksi
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_produksi);
                phpspreadsheet::numberFormatCommaThousandsOrZero($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // seitai_berat_loss
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->seitai_berat_loss);
                phpspreadsheet::numberFormatCommaThousandsOrZero($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // seitai loss %
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_produksi > 0 ? $dataItem->seitai_berat_loss / ($dataItem->berat_produksi + $dataItem->seitai_berat_loss) : 0);
                phpspreadsheet::numberPercentageOrZero($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // seitai_cost
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->seitai_cost);
                phpspreadsheet::numberFormatCommaThousandsOrZero($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // seitai_berat_loss_ponsu
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->seitai_berat_loss_ponsu);
                phpspreadsheet::numberFormatCommaThousandsOrZero($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // infure_berat_loss
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->infure_berat_loss);
                phpspreadsheet::numberFormatCommaThousandsOrZero($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // produksi per jam
                $workHours = $dataItem->work_hour_on_mm; // Ambil nilai menit dari data
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->work_hour_on_mm > 0 ? $dataItem->qty_produksi / ($workHours / 60) : 0);
                phpspreadsheet::numberFormatCommaThousandsOrZero($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_standard > 0 ? $dataItem->berat_produksi / $dataItem->berat_standard : 0);
                // phpspreadsheet::numberPercentageOrZero($spreadsheet, $columnItem . $rowItem);
                // $columnItem++;
                // $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->infure_cost_printing);
                // $columnItem++;
                // $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->infure_cost + $dataItem->infure_cost_printing);
                // $columnItem++;

                // jam kerja
                $workHours = $dataItem->work_hour_on_mm; // Ambil nilai menit dari data
                $hours = floor($workHours / 60); // Hitung jumlah jam
                $minutes = $workHours % 60; // Hitung sisa menit
                $formatedWorkHours = sprintf('%d:%02d', $hours, $minutes);

                // jam mati
                $offHours = $dataItem->work_hour_off_mm; // Ambil nilai menit dari data
                $hours = floor($offHours / 60); // Hitung jumlah jam
                $minutes = $offHours % 60; // Hitung sisa menit
                $formatedOffHours = sprintf('%d:%02d', $hours, $minutes);

                // jalan mesin %
                $activeWorksheet->setCellValue($columnItem . $rowItem, $workHours > 0 ? $workHours / ($offHours + $workHours) : 0);
                phpspreadsheet::numberPercentageOrZero($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // jam kerja
                $activeWorksheet->setCellValue($columnItem . $rowItem, $formatedWorkHours);
                $columnItem++;
                // jam mati
                $activeWorksheet->setCellValue($columnItem . $rowItem, $formatedOffHours);
                phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);
                $columnItem++;

                phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem, false, 8, 'Calibri');
                $rowItem++;
            }
            // perhitungan jumlah berdasarkan departemen
            $spreadsheet->getActiveSheet()->mergeCells($columnMachineNo . $rowItem . ':' . $columnMachineName . $rowItem);
            // $activeWorksheet->setCellValue($columnMachineNo . $rowItem, 'Total ' . $department['department_name']);
            $columnItem = $startColumnItemData;
            $columnItemEnd = chr(ord($columnItem) + count($header) - 1);
            // jumlah produksi
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - $countMachine) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpspreadsheet::numberFormatThousands($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // berat_produksi
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - $countMachine) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpspreadsheet::numberFormatCommaThousandsOrZero($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // seitai_berat_loss
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - $countMachine) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpspreadsheet::numberFormatCommaThousandsOrZero($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // seitai loss %
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - $countMachine) . ':' . $columnItem . ($rowItem - 1) . ')/COUNTIF(' . $columnItem . ($rowItem - $countMachine) . ':' . $columnItem . ($rowItem - 1) . ', "<>0")');
            phpspreadsheet::numberPercentageOrZero($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // seitai_cost
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - $countMachine) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpspreadsheet::numberFormatCommaThousandsOrZero($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // seitai_berat_loss_ponsu
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - $countMachine) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpspreadsheet::numberFormatCommaThousandsOrZero($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // infure_berat_loss
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - $countMachine) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpspreadsheet::numberFormatCommaThousandsOrZero($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // produksi per jam
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - $countMachine) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpspreadsheet::numberFormatCommaThousandsOrZero($spreadsheet, $columnItem . $rowItem);
            $columnItem++;

            $jamKerjaMesin = array_reduce(array_keys($listMachine[$department['department_id']]->toArray()), function ($carry, $item) use ($dataFilter, $department) {
                $dataItem = $dataFilter[$department['department_id']][$item] ?? (object)[
                    'qty_produksi' => 0,
                    'berat_produksi' => 0,
                    'seitai_berat_loss' => 0,
                    'seitai_cost' => 0,
                    'seitai_berat_loss_ponsu' => 0,
                    'infure_berat_loss' => 0,
                    'work_hour_mm' => 0,
                    'work_hour_off_mm' => 0,
                    'work_hour_on_mm' => 0,
                ];
                $carry['workHours'] += $dataItem->work_hour_mm;
                $carry['offHours'] += $dataItem->work_hour_off_mm;
                return $carry;
            }, ['workHours' => 0, 'offHours' => 0]);

            // total jalan mesin % berdasarkan departemen
            $totalJalanMesin = $jamKerjaMesin['workHours'] + $jamKerjaMesin['offHours'];
            $hours = floor($totalJalanMesin / 60); // Hitung jumlah jam
            $minutes = $totalJalanMesin % 60; // Hitung sisa menit

            $avgJamKerjaMesin = $jamKerjaMesin['workHours'] > 0 ? $jamKerjaMesin['workHours'] / $totalJalanMesin : 0;
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, $avgJamKerjaMesin);
            phpspreadsheet::numberPercentageOrZero($spreadsheet, $columnItem . $rowItem);
            $columnItem++;

            // jam kerja
            $hours = floor($jamKerjaMesin['workHours'] / 60); // Hitung jumlah jam
            $minutes = $jamKerjaMesin['workHours'] % 60; // Hitung sisa menit
            $formatedWorkHours = sprintf('%d:%02d', $hours, $minutes);
            $activeWorksheet->setCellValue($columnItem . $rowItem, $formatedWorkHours);
            $columnItem++;

            // jam mati
            $hours = floor($jamKerjaMesin['offHours'] / 60); // Hitung jumlah jam
            $minutes = $jamKerjaMesin['offHours'] % 60; // Hitung sisa menit
            $formatedOffHours = sprintf('%d:%02d', $hours, $minutes);
            $activeWorksheet->setCellValue($columnItem . $rowItem, $formatedOffHours);

            phpspreadsheet::styleFont($spreadsheet, $columnMachineNo . $rowItem . ':' . $columnItem . $rowItem, true, 8, 'Calibri');
            phpspreadsheet::addFullBorder($spreadsheet, $columnMachineNo . $rowItem . ':' . $columnItem . $rowItem);
            $columnItem++;

            $rowItem++;
            $rowItem++;
        }

        // Grand total
        $rowGrandTotal = $rowItem;
        $spreadsheet->getActiveSheet()->mergeCells($columnMachineNo . $rowGrandTotal . ':' . $columnMachineName . $rowGrandTotal);
        $spreadsheet->getActiveSheet()->setCellValue($columnMachineNo . $rowGrandTotal, 'GRAND TOTAL');
        phpspreadsheet::styleFont($spreadsheet, $columnMachineNo . $rowGrandTotal . ':' . $columnHeaderEnd . $rowGrandTotal, true, 8, 'Calibri');
        // $this->addFullBorder($spreadsheet, $columnMachineNo . $rowGrandTotal . ':' . $columnValueAvg . $rowGrandTotal);

        // grand total
        $grandTotal = [
            'qty_produksi' => 0,
            'berat_produksi' => 0,
            'seitai_berat_loss' => 0,
            'seitai_cost' => 0,
            'seitai_berat_loss_ponsu' => 0,
            'infure_berat_loss' => 0,
            'work_hour_mm' => 0,
            'work_hour_on_mm' => 0,
            'work_hour_off_mm' => 0,
        ];

        foreach ($dataFilter as $departmentId => $department) {
            foreach ($department as $machineNo => $machine) {
                $grandTotal['qty_produksi'] += $machine->qty_produksi;
                $grandTotal['berat_produksi'] += $machine->berat_produksi;
                $grandTotal['seitai_berat_loss'] += $machine->seitai_berat_loss;
                $grandTotal['seitai_cost'] += $machine->seitai_cost;
                $grandTotal['seitai_berat_loss_ponsu'] += $machine->seitai_berat_loss_ponsu;
                $grandTotal['infure_berat_loss'] += $machine->infure_berat_loss;
                $grandTotal['work_hour_mm'] += $machine->work_hour_mm;
                $grandTotal['work_hour_on_mm'] += $machine->work_hour_on_mm;
                $grandTotal['work_hour_off_mm'] += $machine->work_hour_off_mm;
            }
        }

        $columnItem = $startColumnItemData;
        $columnItemEnd = chr(ord($columnItem) + count($header) - 1);
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['qty_produksi']);
        phpspreadsheet::numberFormatThousands($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_produksi']);
        phpspreadsheet::numberFormatCommaThousandsOrZero($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['seitai_berat_loss']);
        phpspreadsheet::numberFormatCommaThousandsOrZero($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // seitai loss %
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_produksi'] > 0 ? $grandTotal['seitai_berat_loss'] / ($grandTotal['berat_produksi'] + $grandTotal['seitai_berat_loss']) : 0);
        phpspreadsheet::numberPercentageOrZero($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // seitai_cost
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['seitai_cost']);
        phpspreadsheet::numberFormatCommaThousandsOrZero($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // seitai_berat_loss_ponsu
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['seitai_berat_loss_ponsu']);
        phpspreadsheet::numberFormatCommaThousandsOrZero($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // infure_berat_loss
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['infure_berat_loss']);
        phpspreadsheet::numberFormatCommaThousandsOrZero($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // produksi per jam
        $hours = floor($grandTotal['work_hour_on_mm'] / 60); // Hitung jumlah jam
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $hours > 0 ? $grandTotal['qty_produksi'] / $hours : 0);
        phpspreadsheet::numberFormatCommaThousandsOrZero($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;

        // jalan mesin %
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['work_hour_on_mm'] > 0 ? $grandTotal['work_hour_on_mm'] / ($grandTotal['work_hour_off_mm'] + $grandTotal['work_hour_on_mm']) : 0);
        phpspreadsheet::numberPercentageOrZero($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;

        // jam kerja
        $minutes = $grandTotal['work_hour_on_mm'] % 60; // Hitung sisa menit
        $formatedWorkHours = sprintf('%d:%02d', $hours, $minutes);
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $formatedWorkHours);
        $columnItem++;

        // jam mati
        $hours = floor($grandTotal['work_hour_off_mm'] / 60); // Hitung jumlah jam
        $minutes = $grandTotal['work_hour_off_mm'] % 60; // Hitung sisa menit
        $formatedOffHours = sprintf('%d:%02d', $hours, $minutes);
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $formatedOffHours);

        phpspreadsheet::addFullBorder($spreadsheet, $columnMachineNo . $rowGrandTotal . ':' . $columnItem . $rowGrandTotal);

        $activeWorksheet->getStyle($columnMesin . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart)->getAlignment()->setWrapText(true);

        // size auto
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);

        $writer = new Xlsx($spreadsheet);
        $filename = 'asset/report/' . $this->nipon . '-' . $this->jenisreport . '.xlsx';
        $writer->save($filename);
        $response = [
            'status' => 'success',
            'filename' => $filename
        ];
        return $response;
    }

    public function daftarProduksiPerTipePerMesinInfure($tglMasuk, $tglKeluar)
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
        $activeWorksheet->setCellValue('B1', 'DAFTAR PRODUKSI PER TIPE PER MESIN INFURE');
        $activeWorksheet->setCellValue('B2', 'Periode : ' . $tglMasuk . ' s/d ' . $tglKeluar);
        // Style Judul
        phpspreadsheet::styleFont($spreadsheet, 'B1:B2', true, 11, 'Calibri');
        // hide column A
        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setVisible(false);

        // Header
        $columnTipeProduk = 'A';
        $columnTipeProdukEnd = 'B';
        $spreadsheet->getActiveSheet()->mergeCells($columnTipeProduk . '3:' . $columnTipeProdukEnd . '3');
        $activeWorksheet->setCellValue('A3', 'Tipe Produk');

        $columnMesin = 'C';
        $columnMesinEnd = 'D';
        $spreadsheet->getActiveSheet()->mergeCells($columnMesin . '3:' . $columnMesinEnd . '3');
        $activeWorksheet->setCellValue('C3', 'Mesin');

        // header
        $rowHeaderStart = 3;
        $columnHeaderStart = 'E';
        $columnHeaderEnd = 'E';
        $header = [
            'Berat Standar (Kg)',
            'Berat Produksi (Kg)',
            'Weight Rate',
            'Infure Cost',
            'Loss (Kg)',
            'Loss (%)',
            'Panjang Infure (meter)',
            'Panjang Inline Printing (meter)',
            'Inline Printing Cost',
            'Process Cost',
        ];

        foreach ($header as $key => $value) {
            $activeWorksheet->setCellValue($columnHeaderEnd . $rowHeaderStart, $value);
            $columnHeaderEnd++;
        }
        $columnHeaderEnd = chr(ord($columnHeaderEnd) - 1);

        // style header
        phpspreadsheet::addFullBorder($spreadsheet, $columnTipeProduk . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);
        phpspreadsheet::styleFont($spreadsheet, $columnMesin . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart, true, 9, 'Calibri');
        phpspreadsheet::textAlignCenter($spreadsheet, $columnMesin . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);

        $data = DB::select("
            select max(dep.name) AS department_name,
                max(dep.id) AS department_id,
                max(prTip.id) AS product_type_id,
                max(prTip.name) AS product_type_name,
                max(mac.machineNo) AS machine_no,
                max(mac.machineName) AS machine_name,
                SUM(asy.berat_standard) AS berat_standard,
                SUM(asy.berat_produksi) AS berat_produksi,
                SUM(asy.infure_cost) AS infure_cost,
                SUM(asy.infure_berat_loss) AS infure_berat_loss,
                SUM(asy.panjang_produksi) AS panjang_produksi,
                SUM(asy.panjang_printing_inline) AS panjang_printing_inline,
                SUM(asy.infure_cost_printing) AS infure_cost_printing
            FROM tdProduct_Assembly AS asy
            left JOIN msMachine AS mac ON asy.machine_id = mac.id
            left JOIN msDepartment AS dep ON mac.department_id = dep.id
            left JOIN msProduct AS prd ON asy.product_id = prd.id
            left JOIN msProduct_type AS prTip ON prd.product_type_id = prTip.id
            WHERE asy.production_date BETWEEN '$tglMasuk' AND '$tglKeluar'
            GROUP BY dep.id, asy.machine_id, prTip.id
        ");

        if (count($data) == 0) {
            $response = [
                'status' => 'error',
                'message' => "Data pada periode tanggal tersebut tidak ditemukan"
            ];

            return $response;
        }

        $listDepartment = array_reduce($data, function ($carry, $item) {
            $carry[$item->department_id] = [
                'department_id' => $item->department_id,
                'department_name' => $item->department_name
            ];
            return $carry;
        }, []);

        $listProductType = array_reduce($data, function ($carry, $item) {
            $carry[$item->department_id][$item->product_type_id] = [
                'product_type_id' => $item->product_type_id,
                'product_type_name' => $item->product_type_name
            ];
            return $carry;
        }, []);

        // list mesin berdasarkan tanggal pertama dan departemen
        $listMachine = array_reduce($data, function ($carry, $item) {
            $carry[$item->department_id][$item->product_type_id][$item->machine_no] = $item->machine_name;
            return $carry;
        }, []);

        $dataFilter = array_reduce($data, function ($carry, $item) {
            $carry[$item->department_id][$item->product_type_id][$item->machine_no] = $item;
            return $carry;
        }, []);

        // index
        $startColumnItem = 'A';
        $endColumnItem = $columnHeaderEnd;
        $startColumnItemData = 'E';
        $columnMachineNo = 'C';
        $columnMachineName = 'D';
        $startRowItem = 5;
        $rowItem = $startRowItem;
        // daftar departemen
        foreach ($listDepartment as $department) {
            // Menulis data departemen
            $activeWorksheet->setCellValue($startColumnItem . $rowItem, $department['department_name']);
            $spreadsheet->getActiveSheet()->mergeCells($startColumnItem . $rowItem . ':' . $endColumnItem . $rowItem);
            phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowItem, true, 9, 'Calibri');
            $rowItem++;

            // daftar tipe produk
            foreach ($listProductType[$department['department_id']] as $productType) {
                $spreadsheet->getActiveSheet()->mergeCells($startColumnItem . $rowItem . ':' . $endColumnItem . $rowItem);
                $activeWorksheet->setCellValue($startColumnItem . $rowItem, $productType['product_type_name']);
                phpspreadsheet::styleFont($spreadsheet, $columnMachineNo . $rowItem, false, 8, 'Calibri');
                $rowItem++;
                // daftar mesin
                foreach ($listMachine[$department['department_id']][$productType['product_type_id']] as $machineNo => $machineName) {
                    if ($dataFilter[$department['department_id']][$productType['product_type_id']][$machineNo] == null) {
                        continue;
                    }
                    // memasukkan data
                    $dataItem = $dataFilter[$department['department_id']][$productType['product_type_id']][$machineNo];
                    $columnItem = $startColumnItemData;

                    // Menulis data mesin
                    $spreadsheet->getActiveSheet()->setCellValue($columnMachineNo . $rowItem, $machineNo);
                    $spreadsheet->getActiveSheet()->setCellValue($columnMachineName . $rowItem, $machineName);
                    // phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);

                    // berat standar
                    $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_standard);
                    phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                    $columnItem++;
                    // berat produksi
                    $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_produksi);
                    phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                    $columnItem++;
                    // weight rate
                    $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_standard > 0 ? $dataItem->berat_produksi / $dataItem->berat_standard : 0);
                    phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowItem);
                    $columnItem++;
                    // infure cost
                    $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->infure_cost);
                    phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                    $columnItem++;
                    // loss
                    $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->infure_berat_loss);
                    phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                    $columnItem++;
                    // loss %
                    $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_produksi > 0 ? $dataItem->infure_berat_loss / $dataItem->berat_produksi : 0);
                    phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowItem);
                    $columnItem++;
                    // panjang infure
                    $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->panjang_produksi);
                    phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                    $columnItem++;
                    // panjang inline printing
                    $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->panjang_printing_inline);
                    phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                    $columnItem++;
                    // inline printing cost
                    $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->infure_cost_printing);
                    phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                    $columnItem++;
                    // process cost
                    $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->infure_cost + $dataItem->infure_cost_printing);
                    phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                    phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);
                    $columnItem++;

                    phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem, false, 8, 'Calibri');
                    $rowItem++;
                }
                // perhitungan jumlah berdasarkan type produk
                $spreadsheet->getActiveSheet()->mergeCells($columnMachineNo . $rowItem . ':' . $columnMachineName . $rowItem);
                // $activeWorksheet->setCellValue($columnMachineNo . $rowItem, 'Total ' . $department['department_name']);
                $columnItem = $startColumnItemData;
                $columnItemEnd = chr(ord($columnItem) + count($header) - 1);
                $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listMachine[$department['department_id']][$productType['product_type_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
                phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listMachine[$department['department_id']][$productType['product_type_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
                phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listMachine[$department['department_id']][$productType['product_type_id']])) . ':' . $columnItem . ($rowItem - 1) . ')/COUNTIF(' . $columnItem . ($rowItem - count($listMachine[$department['department_id']][$productType['product_type_id']])) . ':' . $columnItem . ($rowItem - 1) . ', "<>0")');
                phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listMachine[$department['department_id']][$productType['product_type_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
                phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listMachine[$department['department_id']][$productType['product_type_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
                phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listMachine[$department['department_id']][$productType['product_type_id']])) . ':' . $columnItem . ($rowItem - 1) . ')/COUNTIF(' . $columnItem . ($rowItem - count($listMachine[$department['department_id']][$productType['product_type_id']])) . ':' . $columnItem . ($rowItem - 1) . ', "<>0")');
                phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listMachine[$department['department_id']][$productType['product_type_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
                phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listMachine[$department['department_id']][$productType['product_type_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
                phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listMachine[$department['department_id']][$productType['product_type_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
                phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listMachine[$department['department_id']][$productType['product_type_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
                phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                phpspreadsheet::addFullBorder($spreadsheet, $columnMachineNo . $rowItem . ':' . $columnItem . $rowItem);
                $columnItem++;
                phpspreadsheet::styleFont($spreadsheet, $columnMachineNo . $rowItem . ':' . $columnHeaderEnd . $rowItem, true, 8, 'Calibri');

                $rowItem++;
                $rowItem++;
            }
            // total berdasarkan departemen
            $columnTotalDepartment = $startColumnItem;
            $columnTotalDepartmentEnd = 'D';
            $spreadsheet->getActiveSheet()->mergeCells($startColumnItem . $rowItem . ':' . $columnTotalDepartmentEnd . $rowItem);
            $activeWorksheet->setCellValue($columnTotalDepartment . $rowItem, 'Total');
            phpspreadsheet::styleFont($spreadsheet, $columnTotalDepartment . $rowItem . ':' . $columnHeaderEnd . $rowItem, true, 8, 'Calibri');

            $columnItem = $startColumnItemData;
            $columnItemEnd = chr(ord($columnItem) + count($header) - 1);
            $totalByDepartment = array_reduce(
                array_keys($listProductType[$department['department_id']]),
                function ($carry, $productType) use ($dataFilter, $department) {
                    $dataItems = $dataFilter[$department['department_id']][$productType] ?? [];

                    foreach ($dataItems as $item) {
                        $carry['berat_standard'] += $item->berat_standard;
                        $carry['berat_produksi'] += $item->berat_produksi;
                        $carry['infure_cost'] += $item->infure_cost;
                        $carry['infure_berat_loss'] += $item->infure_berat_loss;
                        $carry['panjang_produksi'] += $item->panjang_produksi;
                        $carry['panjang_printing_inline'] += $item->panjang_printing_inline;
                        $carry['infure_cost_printing'] += $item->infure_cost_printing;
                    }

                    return $carry;
                },
                [
                    'berat_standard' => 0,
                    'berat_produksi' => 0,
                    'infure_cost' => 0,
                    'infure_berat_loss' => 0,
                    'panjang_produksi' => 0,
                    'panjang_printing_inline' => 0,
                    'infure_cost_printing' => 0
                ]
            );

            // berat standar
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, $totalByDepartment['berat_standard']);
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // berat produksi
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, $totalByDepartment['berat_produksi']);
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // weight rate
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, $totalByDepartment['berat_produksi'] > 0 ? $totalByDepartment['berat_produksi'] / $totalByDepartment['berat_standard'] : 0);
            phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // infure cost
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, $totalByDepartment['infure_cost']);
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // loss
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, $totalByDepartment['infure_berat_loss']);
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // loss %
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, $totalByDepartment['berat_produksi'] > 0 ? $totalByDepartment['infure_berat_loss'] / $totalByDepartment['berat_produksi'] : 0);
            phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // panjang infure
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, $totalByDepartment['panjang_produksi']);
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // panjang inline printing
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, $totalByDepartment['panjang_printing_inline']);
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // inline printing cost
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, $totalByDepartment['infure_cost_printing']);
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // process cost
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, $totalByDepartment['infure_cost'] + $totalByDepartment['infure_cost_printing']);
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            phpspreadsheet::styleFont($spreadsheet, $columnMachineNo . $rowItem . ':' . $columnItem . $rowItem, true, 8, 'Calibri');
            phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);
            $columnItem++;

            $rowItem++;
            $rowItem++;
        }

        // Grand total
        $rowGrandTotal = $rowItem;
        $spreadsheet->getActiveSheet()->mergeCells($startColumnItem . $rowGrandTotal . ':' . $columnMachineName . $rowGrandTotal);
        $spreadsheet->getActiveSheet()->setCellValue($startColumnItem . $rowGrandTotal, 'GRAND TOTAL');
        phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowGrandTotal . ':' . $columnHeaderEnd . $rowGrandTotal, true, 8, 'Calibri');
        // $this->addFullBorder($spreadsheet, $columnMachineNo . $rowGrandTotal . ':' . $columnValueAvg . $rowGrandTotal);

        $grandTotal = array_reduce(array_keys($listDepartment), function ($carry, $department) use ($dataFilter, $listProductType) {
            $productType = $listProductType[$department];
            foreach ($productType as $type) {
                $dataItem = $dataFilter[$department][$type['product_type_id']] ?? [];
                $carry['berat_standard'] += array_sum(array_column($dataItem, 'berat_standard'));
                $carry['berat_produksi'] += array_sum(array_column($dataItem, 'berat_produksi'));
                $carry['infure_cost'] += array_sum(array_column($dataItem, 'infure_cost'));
                $carry['infure_berat_loss'] += array_sum(array_column($dataItem, 'infure_berat_loss'));
                $carry['panjang_produksi'] += array_sum(array_column($dataItem, 'panjang_produksi'));
                $carry['panjang_printing_inline'] += array_sum(array_column($dataItem, 'panjang_printing_inline'));
                $carry['infure_cost_printing'] += array_sum(array_column($dataItem, 'infure_cost_printing'));
            }
            return $carry;
        }, [
            'berat_standard' => 0,
            'berat_produksi' => 0,
            'infure_cost' => 0,
            'infure_berat_loss' => 0,
            'panjang_produksi' => 0,
            'panjang_printing_inline' => 0,
            'infure_cost_printing' => 0
        ]);

        $columnItem = $startColumnItemData;
        $columnItemEnd = chr(ord($columnItem) + count($header) - 1);
        // berat standar
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_standard']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // berat produksi
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_produksi']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // weight rate
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_standard'] > 0 ? $grandTotal['berat_produksi'] / $grandTotal['berat_standard'] : 0);
        phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // infure cost
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['infure_cost']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // loss
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['infure_berat_loss']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // loss %
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_produksi'] > 0 ? $grandTotal['infure_berat_loss'] / $grandTotal['berat_produksi'] : 0);
        phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // panjang infure
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['panjang_produksi']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // panjang inline printing
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['panjang_printing_inline']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // inline printing cost
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['infure_cost_printing']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // process cost
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['infure_cost'] + $grandTotal['infure_cost_printing']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowGrandTotal . ':' . $columnItem . $rowGrandTotal);
        $columnItem++;

        $activeWorksheet->getStyle($columnMesin . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart)->getAlignment()->setWrapText(true);

        $writer = new Xlsx($spreadsheet);
        $filename = 'asset/report/' . $this->nipon . '-' . $this->jenisreport . '.xlsx';
        $writer->save($filename);
        $response = [
            'status' => 'success',
            'filename' => $filename
        ];
        return $response;
    }

    public function daftarProduksiPerTipePerMesinSeitai($tglMasuk, $tglKeluar)
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
        $activeWorksheet->setCellValue('A1', 'DAFTAR PRODUKSI PER TIPE PER MESIN SEITAI');
        $activeWorksheet->setCellValue('A2', 'Periode : ' . $tglMasuk . ' s/d ' . $tglKeluar);
        // Style Judul
        phpspreadsheet::styleFont($spreadsheet, 'B1:B2', true, 11, 'Calibri');

        // Header
        $columnTipeProduk = 'A';
        $columnTipeProdukEnd = 'B';
        $spreadsheet->getActiveSheet()->mergeCells($columnTipeProduk . '3:' . $columnTipeProdukEnd . '3');
        $activeWorksheet->setCellValue('A3', 'Tipe Produk');

        $columnMesin = 'C';
        $columnMesinEnd = 'D';
        $spreadsheet->getActiveSheet()->mergeCells($columnMesin . '3:' . $columnMesinEnd . '3');
        $activeWorksheet->setCellValue('C3', 'Mesin');

        // header
        $rowHeaderStart = 3;
        $columnHeaderStart = 'E';
        $columnHeaderEnd = 'E';
        $header = [
            'Jumlah Produksi (Lembar)',
            'Berat Produksi (Kg)',
            'Loss (Kg)',
            'Loss (%)',
            'Seitai Cost',
            'Ponsu Loss (Kg)',
            'Infure Loss (Kg)',
        ];

        foreach ($header as $key => $value) {
            $activeWorksheet->setCellValue($columnHeaderEnd . $rowHeaderStart, $value);
            $columnHeaderEnd++;
        }
        $columnHeaderEnd = chr(ord($columnHeaderEnd) - 1);

        // style header
        phpspreadsheet::addFullBorder($spreadsheet, $columnTipeProduk . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);
        phpspreadsheet::styleFont($spreadsheet, $columnMesin . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart, true, 9, 'Calibri');
        phpspreadsheet::textAlignCenter($spreadsheet, $columnMesin . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);

        $data = DB::select("
            SELECT
                MAX(dep.id) AS department_id,
                MAX(dep.name) AS department_name,
                MAX(prT.id) AS product_type_id,
                MAX(prT.name) AS product_type_name,
                MAX(mac.machineNo) AS machine_no,
                MAX(mac.machineName) AS machine_name,
                SUM(good.qty_produksi) AS qty_produksi,
                SUM(good.qty_produksi * prd.unit_weight * 0.001) AS berat_produksi,
                SUM(good.qty_produksi * prT.harga_sat_seitai) AS seitai_cost,
                SUM(good.seitai_berat_loss) - COALESCE(SUM(ponsu.berat_loss), 0) AS seitai_berat_loss,
                COALESCE(SUM(ponsu.berat_loss), 0) AS seitai_berat_loss_ponsu,
                SUM(good.infure_berat_loss) AS infure_berat_loss
            FROM tdProduct_Goods AS good
            LEFT JOIN (
                SELECT
                    los_.product_goods_id,
                    SUM(los_.berat_loss) AS berat_loss
                FROM tdProduct_Goods_Loss AS los_
                WHERE los_.loss_seitai_id = 1
                GROUP BY los_.product_goods_id
            ) ponsu ON good.id = ponsu.product_goods_id
            INNER JOIN msMachine AS mac ON good.machine_id = mac.id
            INNER JOIN msDepartment AS dep ON mac.department_id = dep.id
            INNER JOIN msProduct AS prd ON good.product_id = prd.id
            INNER JOIN msProduct_type AS prT ON prd.product_type_id = prT.id
            WHERE good.production_date BETWEEN '$tglMasuk' AND '$tglKeluar'
            GROUP BY dep.id, prT.name, good.machine_id
        ");

        if (count($data) == 0) {
            $response = [
                'status' => 'error',
                'message' => "Data pada periode tanggal tersebut tidak ditemukan"
            ];

            return $response;
        }

        $listDepartment = array_reduce($data, function ($carry, $item) {
            $carry[$item->department_id] = [
                'department_id' => $item->department_id,
                'department_name' => $item->department_name
            ];
            return $carry;
        }, []);

        $listProductType = array_reduce($data, function ($carry, $item) {
            $carry[$item->department_id][$item->product_type_id] = [
                'product_type_id' => $item->product_type_id,
                'product_type_name' => $item->product_type_name
            ];
            return $carry;
        }, []);

        // list mesin berdasarkan tanggal pertama dan departemen
        $listMachine = array_reduce($data, function ($carry, $item) {
            $carry[$item->department_id][$item->product_type_id][$item->machine_no] = $item->machine_name;
            return $carry;
        }, []);

        $dataFilter = array_reduce($data, function ($carry, $item) {
            $carry[$item->department_id][$item->product_type_id][$item->machine_no] = $item;
            return $carry;
        }, []);

        // index
        $startColumnItem = 'A';
        $endColumnItem = $columnHeaderEnd;
        $startColumnItemData = 'E';
        $columnMachineNo = 'C';
        $columnMachineName = 'D';
        $startRowItem = 5;
        $rowItem = $startRowItem;
        // daftar departemen
        foreach ($listDepartment as $department) {
            // Menulis data departemen
            $activeWorksheet->setCellValue($startColumnItem . $rowItem, $department['department_name']);
            $spreadsheet->getActiveSheet()->mergeCells($startColumnItem . $rowItem . ':' . $endColumnItem . $rowItem);
            phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowItem, true, 9, 'Calibri');
            $rowItem++;

            // daftar tipe produk
            foreach ($listProductType[$department['department_id']] as $productType) {
                $spreadsheet->getActiveSheet()->mergeCells($startColumnItem . $rowItem . ':' . $endColumnItem . $rowItem);
                $activeWorksheet->setCellValue($startColumnItem . $rowItem, $productType['product_type_name']);
                phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowItem, false, 8, 'Calibri');
                $rowItem++;
                // daftar mesin
                foreach ($listMachine[$department['department_id']][$productType['product_type_id']] as $machineNo => $machineName) {
                    if ($dataFilter[$department['department_id']][$productType['product_type_id']][$machineNo] == null) {
                        continue;
                    }
                    $columnItem = $startColumnItemData;

                    // Menulis data mesin
                    $spreadsheet->getActiveSheet()->setCellValue($columnMachineNo . $rowItem, $machineNo);
                    $spreadsheet->getActiveSheet()->setCellValue($columnMachineName . $rowItem, $machineName);
                    // phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);

                    // memasukkan data
                    $dataItem = $dataFilter[$department['department_id']][$productType['product_type_id']][$machineNo];
                    // $dataItem = $dataFilter[$department['department_id']][$productType['product_type_id']][$machineNo] ?? (object)[
                    //     'qty_produksi' => 0,
                    //     'berat_produksi' => 0,
                    //     'seitai_cost' => 0,
                    //     'seitai_berat_loss' => 0,
                    //     'seitai_berat_loss_ponsu' => 0,
                    //     'infure_berat_loss' => 0
                    // ];
                    // jumlah produksi
                    $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->qty_produksi);
                    phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                    $columnItem++;
                    // berat produksi
                    $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_produksi);
                    phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                    $columnItem++;
                    // loss
                    $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->seitai_berat_loss);
                    phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                    $columnItem++;
                    // loss %
                    $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_produksi > 0 ? $dataItem->seitai_berat_loss / $dataItem->berat_produksi : 0);
                    phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowItem);
                    $columnItem++;
                    // Seitai cost
                    $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->seitai_cost);
                    phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                    $columnItem++;
                    // Ponsu Loss
                    $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->seitai_berat_loss_ponsu);
                    phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                    $columnItem++;
                    // Infure Loss
                    $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->infure_berat_loss);
                    phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                    phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);
                    $columnItem++;

                    phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem, false, 8, 'Calibri');
                    $rowItem++;
                }
                // perhitungan jumlah berdasarkan type produk
                $spreadsheet->getActiveSheet()->mergeCells($columnMachineNo . $rowItem . ':' . $columnMachineName . $rowItem);
                // $activeWorksheet->setCellValue($columnMachineNo . $rowItem, 'Total ' . $department['department_name']);
                $columnItem = $startColumnItemData;
                $columnItemEnd = chr(ord($columnItem) + count($header) - 1);
                // jumlah produksi
                $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listMachine[$department['department_id']][$productType['product_type_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
                phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // berat produksi
                $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listMachine[$department['department_id']][$productType['product_type_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
                phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // Loss
                $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listMachine[$department['department_id']][$productType['product_type_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
                phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // loss %
                $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listMachine[$department['department_id']][$productType['product_type_id']])) . ':' . $columnItem . ($rowItem - 1) . ')/COUNTIF(' . $columnItem . ($rowItem - count($listMachine[$department['department_id']][$productType['product_type_id']])) . ':' . $columnItem . ($rowItem - 1) . ', "<>0")');
                phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // Seitai cost
                $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listMachine[$department['department_id']][$productType['product_type_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
                phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // Ponsu Loss
                $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listMachine[$department['department_id']][$productType['product_type_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
                phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // Infure Loss
                $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listMachine[$department['department_id']][$productType['product_type_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
                phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                phpspreadsheet::addFullBorder($spreadsheet, $columnMachineNo . $rowItem . ':' . $columnItem . $rowItem);
                $columnItem++;
                phpspreadsheet::styleFont($spreadsheet, $columnMachineNo . $rowItem . ':' . $columnHeaderEnd . $rowItem, true, 8, 'Calibri');

                $rowItem++;
            }
            // total berdasarkan departemen
            $columnTotalDepartment = $startColumnItem;
            $columnTotalDepartmentEnd = 'D';
            $spreadsheet->getActiveSheet()->mergeCells($startColumnItem . $rowItem . ':' . $columnTotalDepartmentEnd . $rowItem);
            $activeWorksheet->setCellValue($columnTotalDepartment . $rowItem, 'Total');
            phpspreadsheet::styleFont($spreadsheet, $columnTotalDepartment . $rowItem . ':' . $columnHeaderEnd . $rowItem, true, 8, 'Calibri');

            $columnItem = $startColumnItemData;
            $columnItemEnd = chr(ord($columnItem) + count($header) - 1);
            $totalByDepartment = array_reduce(
                array_keys($listProductType[$department['department_id']]),
                function ($carry, $productType) use ($dataFilter, $department) {
                    $dataItems = $dataFilter[$department['department_id']][$productType] ?? [];

                    // dd($dataItems);
                    foreach ($dataItems as $item) {
                        $carry['qty_produksi'] += $item->qty_produksi;
                        $carry['berat_produksi'] += $item->berat_produksi;
                        $carry['seitai_cost'] += $item->seitai_cost;
                        $carry['seitai_berat_loss'] += $item->seitai_berat_loss;
                        $carry['seitai_berat_loss_ponsu'] += $item->seitai_berat_loss_ponsu;
                        $carry['infure_berat_loss'] += $item->infure_berat_loss;
                    }

                    return $carry;
                },
                [
                    'qty_produksi' => 0,
                    'berat_produksi' => 0,
                    'seitai_cost' => 0,
                    'seitai_berat_loss' => 0,
                    'seitai_berat_loss_ponsu' => 0,
                    'infure_berat_loss' => 0
                ]
            );
            // dd($totalByDepartment);

            // jumlah produksi
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, $totalByDepartment['qty_produksi']);
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // berat produksi
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, $totalByDepartment['berat_produksi']);
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // seitai cost
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, $totalByDepartment['seitai_cost']);
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // loss
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, $totalByDepartment['seitai_berat_loss']);
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // loss %
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, $totalByDepartment['berat_produksi'] > 0 ? $totalByDepartment['seitai_berat_loss'] / $totalByDepartment['berat_produksi'] : 0);
            phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            //  berat loss ponsu
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, $totalByDepartment['seitai_berat_loss_ponsu']);
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // berat loss infure
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, $totalByDepartment['infure_berat_loss']);
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            phpspreadsheet::styleFont($spreadsheet, $columnMachineNo . $rowItem . ':' . $columnItem . $rowItem, true, 8, 'Calibri');
            phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);
            $columnItem++;

            $rowItem++;
            $rowItem++;
        }

        // Grand total
        $rowGrandTotal = $rowItem;
        $spreadsheet->getActiveSheet()->mergeCells($startColumnItem . $rowGrandTotal . ':' . $columnMachineName . $rowGrandTotal);
        $spreadsheet->getActiveSheet()->setCellValue($startColumnItem . $rowGrandTotal, 'GRAND TOTAL');
        phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowGrandTotal . ':' . $columnHeaderEnd . $rowGrandTotal, true, 8, 'Calibri');
        // $this->addFullBorder($spreadsheet, $columnMachineNo . $rowGrandTotal . ':' . $columnValueAvg . $rowGrandTotal);

        $grandTotal = array_reduce(array_keys($listDepartment), function ($carry, $department) use ($dataFilter, $listProductType) {
            $productType = $listProductType[$department];
            foreach ($productType as $type) {
                $dataItem = $dataFilter[$department][$type['product_type_id']] ?? [];
                $carry['qty_produksi'] += array_sum(array_column($dataItem, 'qty_produksi'));
                $carry['berat_produksi'] += array_sum(array_column($dataItem, 'berat_produksi'));
                $carry['seitai_cost'] += array_sum(array_column($dataItem, 'seitai_cost'));
                $carry['seitai_berat_loss'] += array_sum(array_column($dataItem, 'seitai_berat_loss'));
                $carry['seitai_berat_loss_ponsu'] += array_sum(array_column($dataItem, 'seitai_berat_loss_ponsu'));
                $carry['infure_berat_loss'] += array_sum(array_column($dataItem, 'infure_berat_loss'));
            }
            return $carry;
        }, [
            'qty_produksi' => 0,
            'berat_produksi' => 0,
            'seitai_cost' => 0,
            'seitai_berat_loss' => 0,
            'seitai_berat_loss_ponsu' => 0,
            'infure_berat_loss' => 0
        ]);

        $columnItem = $startColumnItemData;
        $columnItemEnd = chr(ord($columnItem) + count($header) - 1);
        // jumlah produksi
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['qty_produksi']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // berat produksi
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_produksi']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // seitai cost
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['seitai_cost']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // loss
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['seitai_berat_loss']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // loss %
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_produksi'] > 0 ? $grandTotal['seitai_berat_loss'] / $grandTotal['berat_produksi'] : 0);
        phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // berat loss ponsu
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['seitai_berat_loss_ponsu']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // berat loss infure
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['infure_berat_loss']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowGrandTotal . ':' . $columnItem . $rowGrandTotal);
        $columnItem++;

        $activeWorksheet->getStyle($columnMesin . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart)->getAlignment()->setWrapText(true);

        $writer = new Xlsx($spreadsheet);
        $filename = 'asset/report/' . $this->nipon . '-' . $this->jenisreport . '.xlsx';
        $writer->save($filename);
        $response = [
            'status' => 'success',
            'filename' => $filename
        ];
        return $response;
    }

    public function daftarProduksiPerJenisInfure($tglMasuk, $tglKeluar)
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
        $activeWorksheet->setCellValue('B1', 'DAFTAR PRODUKSI PER JENIS INFURE');
        $activeWorksheet->setCellValue('B2', 'Periode : ' . $tglMasuk . ' s/d ' . $tglKeluar);
        // Style Judul
        phpspreadsheet::styleFont($spreadsheet, 'B1:B2', true, 11, 'Calibri');
        // hide column A
        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setVisible(false);

        // Header
        $columnJenisProduk = 'B';
        $columnJenisProdukEnd = 'C';
        $spreadsheet->getActiveSheet()->mergeCells($columnJenisProduk . '3:' . $columnJenisProdukEnd . '3');
        $activeWorksheet->setCellValue('B3', 'JENIS PRODUK');

        // header
        $rowHeaderStart = 3;
        $columnHeaderStart = 'D';
        $columnHeaderEnd = 'D';
        $header = [
            'Berat Standar (Kg)',
            'Berat Produksi (Kg)',
            'Weight Rate',
            'Infure Cost',
            'Loss (Kg)',
            'Loss (%)',
            'Panjang Infure (meter)',
            'Panjang Inline Printing (meter)',
            'Inline Printing Cost',
            'Process Cost',
        ];

        foreach ($header as $key => $value) {
            $activeWorksheet->setCellValue($columnHeaderEnd . $rowHeaderStart, $value);
            $columnHeaderEnd++;
        }
        $columnHeaderEnd = chr(ord($columnHeaderEnd) - 1);

        // style header
        phpspreadsheet::addFullBorder($spreadsheet, $columnJenisProduk . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);
        phpspreadsheet::styleFont($spreadsheet, $columnJenisProduk . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart, true, 9, 'Calibri');
        phpspreadsheet::textAlignCenter($spreadsheet, $columnJenisProduk . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);

        $data = DB::select("
            SELECT max(prGrp.code) AS product_group_code,
                max(prGrp.name) AS product_group_name,
                SUM(asy.berat_standard) AS berat_standard,
                SUM(asy.berat_produksi) AS berat_produksi,
                SUM(asy.infure_cost) AS infure_cost,
                SUM(asy.infure_berat_loss) AS infure_berat_loss,
                SUM(asy.panjang_produksi) AS panjang_produksi,
                SUM(asy.panjang_printing_inline) AS panjang_printing_inline,
                SUM(asy.infure_cost_printing) AS infure_cost_printing
            FROM tdProduct_Assembly AS asy
            INNER JOIN msProduct AS prd ON asy.product_id = prd.id
            INNER JOIN msProduct_type AS prTip ON prd.product_type_id = prTip.id
            INNER JOIN msProduct_group AS prGrp ON prTip.product_group_id = prGrp.id
            WHERE asy.production_date BETWEEN '$tglMasuk' AND '$tglKeluar'
            GROUP BY prGrp.id
        ");

        if (count($data) == 0) {
            $response = [
                'status' => 'error',
                'message' => "Data pada periode tanggal tersebut tidak ditemukan"
            ];

            return $response;
        }

        // list jenis produk
        $listProductType = array_reduce($data, function ($carry, $item) {
            $carry[$item->product_group_code] = [
                'product_group_code' => $item->product_group_code,
                'product_group_name' => $item->product_group_name
            ];
            return $carry;
        }, []);

        $dataFilter = array_reduce($data, function ($carry, $item) {
            $carry[$item->product_group_code] = $item;
            return $carry;
        }, []);

        // index
        $startColumnItem = 'B';
        $endColumnItem = $columnHeaderEnd;
        $startColumnItemData = 'D';
        $columnProductGroupCode = 'B';
        $columnProductGroupName = 'C';
        $startRowItem = 4;
        $rowItem = $startRowItem;
        // daftar departemen
        foreach ($listProductType as $productGroupCode => $productGroup) {
            if ($dataFilter[$productGroupCode] == null) {
                continue;
            }
            // daftar mesin
            $columnItem = $startColumnItemData;

            // Menulis data mesin
            $spreadsheet->getActiveSheet()->setCellValue($columnProductGroupCode . $rowItem, $productGroup['product_group_code']);
            $spreadsheet->getActiveSheet()->setCellValue($columnProductGroupName . $rowItem, $productGroup['product_group_name']);
            // phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);

            // memasukkan data
            $dataItem = $dataFilter[$productGroupCode];
            // $dataItem = $dataFilter[$productGroupCode] ?? (object)[
            //     'berat_standard' => 0,
            //     'berat_produksi' => 0,
            //     'infure_cost' => 0,
            //     'infure_berat_loss' => 0,
            //     'panjang_produksi' => 0,
            //     'panjang_printing_inline' => 0,
            //     'infure_cost_printing' => 0
            // ];
            $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_standard);
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_produksi);
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_standard > 0 ? $dataItem->berat_produksi / $dataItem->berat_standard : 0);
            phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->infure_cost);
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->infure_berat_loss);
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_produksi > 0 ? $dataItem->infure_berat_loss / $dataItem->berat_produksi : 0);
            phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->panjang_produksi);
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->panjang_printing_inline);
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->infure_cost_printing);
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->infure_cost + $dataItem->infure_cost_printing);
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);
            $columnItem++;

            phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem, false, 8, 'Calibri');
            $rowItem++;
        }

        // Grand total
        $rowGrandTotal = $rowItem;
        $spreadsheet->getActiveSheet()->mergeCells($columnProductGroupCode . $rowGrandTotal . ':' . $columnProductGroupName . $rowGrandTotal);
        $spreadsheet->getActiveSheet()->setCellValue($columnProductGroupCode . $rowGrandTotal, 'GRAND TOTAL');
        phpspreadsheet::styleFont($spreadsheet, $columnProductGroupCode . $rowGrandTotal . ':' . $columnHeaderEnd . $rowGrandTotal, true, 8, 'Calibri');
        // $this->addFullBorder($spreadsheet, $columnProductGroupCode . $rowGrandTotal . ':' . $columnValueAvg . $rowGrandTotal);

        $grandTotal = array_reduce(array_keys($dataFilter), function ($carry, $item) use ($dataFilter) {
            $dataItem = $dataFilter[$item] ?? (object)[
                'berat_standard' => 0,
                'berat_produksi' => 0,
                'infure_cost' => 0,
                'infure_berat_loss' => 0,
                'panjang_produksi' => 0,
                'panjang_printing_inline' => 0,
                'infure_cost_printing' => 0,
            ];
            $carry['berat_standard'] += $dataItem->berat_standard;
            $carry['berat_produksi'] += $dataItem->berat_produksi;
            $carry['infure_cost'] += $dataItem->infure_cost;
            $carry['infure_berat_loss'] += $dataItem->infure_berat_loss;
            $carry['panjang_produksi'] += $dataItem->panjang_produksi;
            $carry['panjang_printing_inline'] += $dataItem->panjang_printing_inline;
            $carry['infure_cost_printing'] += $dataItem->infure_cost_printing;
            return $carry;
        }, [
            'berat_standard' => 0,
            'berat_produksi' => 0,
            'infure_cost' => 0,
            'infure_berat_loss' => 0,
            'panjang_produksi' => 0,
            'panjang_printing_inline' => 0,
            'infure_cost_printing' => 0,
        ]);

        $columnItem = $startColumnItemData;
        $columnItemEnd = chr(ord($columnItem) + count($header) - 1);
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_standard']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_produksi']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_standard'] > 0 ? $grandTotal['berat_produksi'] / $grandTotal['berat_standard'] : 0);
        phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['infure_cost']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['infure_berat_loss']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_produksi'] > 0 ? $grandTotal['infure_berat_loss'] / $grandTotal['berat_produksi'] : 0);
        phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['panjang_produksi']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['panjang_printing_inline']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['infure_cost_printing']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['infure_cost'] + $grandTotal['infure_cost_printing']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
        phpspreadsheet::addFullBorder($spreadsheet, $columnProductGroupCode . $rowGrandTotal . ':' . $columnItem . $rowGrandTotal);
        $columnItem++;

        $activeWorksheet->getStyle($columnJenisProduk . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart)->getAlignment()->setWrapText(true);


        // size auto
        while ($startColumnItemData !== $endColumnItem) {
            $spreadsheet->getActiveSheet()->getColumnDimension($startColumnItemData)->setAutoSize(true);
            $startColumnItemData++;
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

    public function daftarProduksiPerJenisSeitai($tglMasuk, $tglKeluar)
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
        $activeWorksheet->setCellValue('B1', 'DAFTAR PRODUKSI PER JENIS SEITAI');
        $activeWorksheet->setCellValue('B2', 'Periode : ' . $tglMasuk . ' s/d ' . $tglKeluar);
        // Style Judul
        phpspreadsheet::styleFont($spreadsheet, 'B1:B2', true, 11, 'Calibri');
        // hide column A
        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setVisible(false);

        // Header
        $columnJenisProduk = 'B';
        $columnJenisProdukEnd = 'C';
        $spreadsheet->getActiveSheet()->mergeCells($columnJenisProduk . '3:' . $columnJenisProdukEnd . '3');
        $activeWorksheet->setCellValue('B3', 'JENIS PRODUK');

        // header
        $rowHeaderStart = 3;
        $columnHeaderStart = 'D';
        $columnHeaderEnd = 'D';
        $header = [
            'Jumlah Produksi (Lembar)',
            'Berat Produksi (Kg)',
            'Loss (Kg)',
            'Loss (%)',
            'Seitai Cost',
            'Loss Ponsu (Kg)',
            'Infure Loss (Kg)',
        ];

        foreach ($header as $key => $value) {
            $activeWorksheet->setCellValue($columnHeaderEnd . $rowHeaderStart, $value);
            $columnHeaderEnd++;
        }
        $columnHeaderEnd = chr(ord($columnHeaderEnd) - 1);

        // style header
        phpspreadsheet::addFullBorder($spreadsheet, $columnJenisProduk . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);
        phpspreadsheet::styleFont($spreadsheet, $columnJenisProduk . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart, true, 9, 'Calibri');
        phpspreadsheet::textAlignCenter($spreadsheet, $columnJenisProduk . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);

        $data = DB::select("
            SELECT
                MAX(prGrp.code) AS product_group_code,
                MAX(prGrp.name) AS product_group_name,
                SUM(good.qty_produksi) AS qty_produksi,
                SUM(good.qty_produksi * prd.unit_weight * 0.001) AS berat_produksi,
                SUM(good.qty_produksi * prT.harga_sat_seitai) AS seitai_cost,
                SUM(good.seitai_berat_loss) - COALESCE(SUM(ponsu.berat_loss), 0) AS seitai_berat_loss,
                COALESCE(SUM(ponsu.berat_loss), 0) AS seitai_berat_loss_ponsu,
                SUM(good.infure_berat_loss) AS infure_berat_loss
            FROM tdProduct_Goods AS good
            LEFT JOIN (
                SELECT
                    los_.product_goods_id,
                    SUM(los_.berat_loss) AS berat_loss
                FROM tdProduct_Goods_Loss AS los_
                WHERE los_.loss_seitai_id = 1 -- ponsu
                GROUP BY los_.product_goods_id
            ) ponsu ON good.id = ponsu.product_goods_id
            INNER JOIN msProduct AS prd ON good.product_id = prd.id
            INNER JOIN msProduct_type AS prT ON prd.product_type_id = prT.id
            INNER JOIN msProduct_group AS prGrp ON prT.product_group_id = prGrp.id
            WHERE good.production_date BETWEEN '$tglMasuk' AND '$tglKeluar'
            GROUP BY prGrp.name
        ");

        if (count($data) == 0) {
            $response = [
                'status' => 'error',
                'message' => "Data pada periode tanggal tersebut tidak ditemukan"
            ];

            return $response;
        }

        // list jenis produk
        $listProductGroup = array_reduce($data, function ($carry, $item) {
            $carry[$item->product_group_code] = [
                'product_group_code' => $item->product_group_code,
                'product_group_name' => $item->product_group_name
            ];
            return $carry;
        }, []);

        $dataFilter = array_reduce($data, function ($carry, $item) {
            $carry[$item->product_group_code] = $item;
            return $carry;
        }, []);

        // index
        $startColumnItem = 'B';
        $endColumnItem = $columnHeaderEnd;
        $startColumnItemData = 'D';
        $columnProductGroupCode = 'B';
        $columnProductGroupName = 'C';
        $startRowItem = 4;
        $rowItem = $startRowItem;
        // daftar departemen
        foreach ($listProductGroup as $productGroupCode => $productGroup) {
            if ($dataFilter[$productGroupCode] == null) {
                continue;
            }
            // daftar mesin
            $columnItem = $startColumnItemData;

            // Menulis data mesin
            $spreadsheet->getActiveSheet()->setCellValue($columnProductGroupCode . $rowItem, $productGroup['product_group_code']);
            $spreadsheet->getActiveSheet()->setCellValue($columnProductGroupName . $rowItem, $productGroup['product_group_name']);
            // phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);

            // memasukkan data
            $dataItem = $dataFilter[$productGroupCode];
            // $dataItem = $dataFilter[$productGroupCode] ?? (object)[
            //     'qty_produksi' => 0,
            //     'berat_produksi' => 0,
            //     'seitai_cost' => 0,
            //     'seitai_berat_loss' => 0,
            //     'seitai_berat_loss_ponsu' => 0,
            //     'infure_berat_loss' => 0
            // ];
            // jumlah produksi
            $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->qty_produksi);
            phpspreadsheet::numberFormatThousands($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // berat_produksi
            $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_produksi);
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // seitai_berat_loss
            $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->seitai_berat_loss);
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // seitai loss %
            $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_produksi > 0 ? $dataItem->seitai_berat_loss / $dataItem->berat_produksi : 0);
            phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // seitai_cost
            $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->seitai_cost);
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // seitai_berat_loss_ponsu
            $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->seitai_berat_loss_ponsu);
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // infure_berat_loss
            $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->infure_berat_loss);
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);
            $columnItem++;

            phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem, false, 8, 'Calibri');
            $rowItem++;
        }

        // Grand total
        $rowGrandTotal = $rowItem;
        $spreadsheet->getActiveSheet()->mergeCells($columnProductGroupCode . $rowGrandTotal . ':' . $columnProductGroupName . $rowGrandTotal);
        $spreadsheet->getActiveSheet()->setCellValue($columnProductGroupCode . $rowGrandTotal, 'GRAND TOTAL');
        phpspreadsheet::styleFont($spreadsheet, $columnProductGroupCode . $rowGrandTotal . ':' . $columnHeaderEnd . $rowGrandTotal, true, 8, 'Calibri');
        // $this->addFullBorder($spreadsheet, $columnProductGroupCode . $rowGrandTotal . ':' . $columnValueAvg . $rowGrandTotal);

        $grandTotal = array_reduce(array_keys($dataFilter), function ($carry, $item) use ($dataFilter) {
            $dataItem = $dataFilter[$item] ?? (object)[
                'qty_produksi' => 0,
                'berat_produksi' => 0,
                'seitai_cost' => 0,
                'seitai_berat_loss' => 0,
                'seitai_berat_loss_ponsu' => 0,
                'infure_berat_loss' => 0
            ];
            $carry['qty_produksi'] += $dataItem->qty_produksi;
            $carry['berat_produksi'] += $dataItem->berat_produksi;
            $carry['seitai_cost'] += $dataItem->seitai_cost;
            $carry['seitai_berat_loss'] += $dataItem->seitai_berat_loss;
            $carry['seitai_berat_loss_ponsu'] += $dataItem->seitai_berat_loss_ponsu;
            $carry['infure_berat_loss'] += $dataItem->infure_berat_loss;
            return $carry;
        }, [
            'qty_produksi' => 0,
            'berat_produksi' => 0,
            'seitai_cost' => 0,
            'seitai_berat_loss' => 0,
            'seitai_berat_loss_ponsu' => 0,
            'infure_berat_loss' => 0
        ]);


        $columnItem = $startColumnItemData;
        $columnItemEnd = chr(ord($columnItem) + count($header) - 1);
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['qty_produksi']);
        phpspreadsheet::numberFormatThousands($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_produksi']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['seitai_berat_loss']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // seitai loss %
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_produksi'] > 0 ? $grandTotal['seitai_berat_loss'] / $grandTotal['berat_produksi'] : 0);
        phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['seitai_cost']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['seitai_berat_loss_ponsu']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['infure_berat_loss']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        phpspreadsheet::addFullBorder($spreadsheet, $columnProductGroupCode . $rowGrandTotal . ':' . $columnItem . $rowGrandTotal);
        $columnItem++;

        $activeWorksheet->getStyle($columnJenisProduk . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart)->getAlignment()->setWrapText(true);

        // size auto
        while ($startColumnItemData !== $endColumnItem) {
            $spreadsheet->getActiveSheet()->getColumnDimension($startColumnItemData)->setAutoSize(true);
            $startColumnItemData++;
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

    public function daftarProduksiPerTipeInfure($tglMasuk, $tglKeluar)
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
        $activeWorksheet->setCellValue('B1', 'DAFTAR PRODUKSI PER TIPE INFURE');
        $activeWorksheet->setCellValue('B2', 'Periode : ' . $tglMasuk . ' s/d ' . $tglKeluar);
        // Style Judul
        phpspreadsheet::styleFont($spreadsheet, 'B1:B2', true, 11, 'Calibri');
        // hide column A
        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setVisible(false);

        // Header
        $columnJenisProduk = 'B';
        $columnJenisProdukEnd = 'C';
        $spreadsheet->getActiveSheet()->mergeCells($columnJenisProduk . '3:' . $columnJenisProdukEnd . '3');
        $activeWorksheet->setCellValue('B3', 'Type Produk');

        // header
        $rowHeaderStart = 3;
        $columnHeaderStart = 'D';
        $columnHeaderEnd = 'D';
        $header = [
            'Berat Standar (Kg)',
            'Berat Produksi (Kg)',
            'Weight Rate',
            'Infure Cost',
            'Loss (Kg)',
            'Loss (%)',
            'Panjang Infure (meter)',
            'Panjang Inline Printing (meter)',
            'Inline Printing Cost',
            'Process Cost',
        ];

        foreach ($header as $key => $value) {
            $activeWorksheet->setCellValue($columnHeaderEnd . $rowHeaderStart, $value);
            $columnHeaderEnd++;
        }
        $columnHeaderEnd = chr(ord($columnHeaderEnd) - 1);

        // style header
        phpspreadsheet::addFullBorder($spreadsheet, $columnJenisProduk . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);
        phpspreadsheet::styleFont($spreadsheet, $columnJenisProduk . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart, true, 9, 'Calibri');
        phpspreadsheet::textAlignCenter($spreadsheet, $columnJenisProduk . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);

        $data = DB::select("
            SELECT
                max(prTip.code) AS product_type_code,
                max(prTip.name) AS product_type_name,
                SUM(asy.berat_standard) AS berat_standard,
                SUM(asy.berat_produksi) AS berat_produksi,
                SUM(asy.infure_cost) AS infure_cost,
                SUM(asy.infure_berat_loss) AS infure_berat_loss,
                SUM(asy.panjang_produksi) AS panjang_produksi,
                SUM(asy.panjang_printing_inline) AS panjang_printing_inline,
                SUM(asy.infure_cost_printing) AS infure_cost_printing
            FROM tdProduct_Assembly AS asy
            INNER JOIN msProduct AS prd ON asy.product_id = prd.id
            INNER JOIN msProduct_type AS prTip ON prd.product_type_id = prTip.id
            WHERE asy.production_date BETWEEN '$tglMasuk' AND '$tglKeluar'
            GROUP BY prTip.id
        ");

        if (count($data) == 0) {
            $response = [
                'status' => 'error',
                'message' => "Data pada periode tanggal tersebut tidak ditemukan"
            ];

            return $response;
        }

        // list jenis produk
        $listProductGroup = array_reduce($data, function ($carry, $item) {
            $carry[$item->product_type_code] = [
                'product_type_code' => $item->product_type_code,
                'product_type_name' => $item->product_type_name
            ];
            return $carry;
        }, []);

        $dataFilter = array_reduce($data, function ($carry, $item) {
            $carry[$item->product_type_code] = $item;
            return $carry;
        }, []);

        // index
        $startColumnItem = 'B';
        $endColumnItem = $columnHeaderEnd;
        $startColumnItemData = 'D';
        $columnProductGroupCode = 'B';
        $columnProductGroupName = 'C';
        $startRowItem = 4;
        $rowItem = $startRowItem;
        // daftar departemen
        foreach ($listProductGroup as $productGroupCode => $productGroup) {
            if ($dataFilter[$productGroupCode] == null) {
                continue;
            }
            // daftar mesin
            $columnItem = $startColumnItemData;

            // Menulis data mesin
            $spreadsheet->getActiveSheet()->setCellValue($columnProductGroupCode . $rowItem, $productGroup['product_type_code']);
            $spreadsheet->getActiveSheet()->setCellValue($columnProductGroupName . $rowItem, $productGroup['product_type_name']);
            // phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);

            // memasukkan data
            $dataItem = $dataFilter[$productGroupCode];
            // $dataItem = $dataFilter[$productGroupCode] ?? (object)[
            //     'berat_standard' => 0,
            //     'berat_produksi' => 0,
            //     'infure_cost' => 0,
            //     'infure_berat_loss' => 0,
            //     'panjang_produksi' => 0,
            //     'panjang_printing_inline' => 0,
            //     'infure_cost_printing' => 0
            // ];
            $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_standard);
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_produksi);
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_standard > 0 ? $dataItem->berat_produksi / $dataItem->berat_standard : 0);
            phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->infure_cost);
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->infure_berat_loss);
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_produksi > 0 ? $dataItem->infure_berat_loss / $dataItem->berat_produksi : 0);
            phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->panjang_produksi);
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->panjang_printing_inline);
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->infure_cost_printing);
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->infure_cost + $dataItem->infure_cost_printing);
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);
            $columnItem++;

            phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem, false, 8, 'Calibri');
            $rowItem++;
        }

        // Grand total
        $rowGrandTotal = $rowItem;
        $spreadsheet->getActiveSheet()->mergeCells($columnProductGroupCode . $rowGrandTotal . ':' . $columnProductGroupName . $rowGrandTotal);
        $spreadsheet->getActiveSheet()->setCellValue($columnProductGroupCode . $rowGrandTotal, 'GRAND TOTAL');
        phpspreadsheet::styleFont($spreadsheet, $columnProductGroupCode . $rowGrandTotal . ':' . $columnHeaderEnd . $rowGrandTotal, true, 8, 'Calibri');
        // $this->addFullBorder($spreadsheet, $columnProductGroupCode . $rowGrandTotal . ':' . $columnValueAvg . $rowGrandTotal);

        $grandTotal = array_reduce(array_keys($dataFilter), function ($carry, $item) use ($dataFilter) {
            $dataItem = $dataFilter[$item] ?? (object)[
                'berat_standard' => 0,
                'berat_produksi' => 0,
                'infure_cost' => 0,
                'infure_berat_loss' => 0,
                'panjang_produksi' => 0,
                'panjang_printing_inline' => 0,
                'infure_cost_printing' => 0,
            ];
            $carry['berat_standard'] += $dataItem->berat_standard;
            $carry['berat_produksi'] += $dataItem->berat_produksi;
            $carry['infure_cost'] += $dataItem->infure_cost;
            $carry['infure_berat_loss'] += $dataItem->infure_berat_loss;
            $carry['panjang_produksi'] += $dataItem->panjang_produksi;
            $carry['panjang_printing_inline'] += $dataItem->panjang_printing_inline;
            $carry['infure_cost_printing'] += $dataItem->infure_cost_printing;
            return $carry;
        }, [
            'berat_standard' => 0,
            'berat_produksi' => 0,
            'infure_cost' => 0,
            'infure_berat_loss' => 0,
            'panjang_produksi' => 0,
            'panjang_printing_inline' => 0,
            'infure_cost_printing' => 0,
        ]);

        $columnItem = $startColumnItemData;
        $columnItemEnd = chr(ord($columnItem) + count($header) - 1);
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_standard']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_produksi']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_standard'] > 0 ? $grandTotal['berat_produksi'] / $grandTotal['berat_standard'] : 0);
        phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['infure_cost']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['infure_berat_loss']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_produksi'] > 0 ? $grandTotal['infure_berat_loss'] / $grandTotal['berat_produksi'] : 0);
        phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['panjang_produksi']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['panjang_printing_inline']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['infure_cost_printing']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['infure_cost'] + $grandTotal['infure_cost_printing']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
        phpspreadsheet::addFullBorder($spreadsheet, $columnProductGroupCode . $rowGrandTotal . ':' . $columnItem . $rowGrandTotal);
        $columnItem++;

        $activeWorksheet->getStyle($columnJenisProduk . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart)->getAlignment()->setWrapText(true);


        // size auto
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $endColumnItem++;
        while ($startColumnItemData !== $endColumnItem) {
            $spreadsheet->getActiveSheet()->getColumnDimension($startColumnItemData)->setAutoSize(true);
            $startColumnItemData++;
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

    public function daftarProduksiPerTipeSeitai($tglMasuk, $tglKeluar)
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
        $activeWorksheet->setCellValue('B1', 'DAFTAR PRODUKSI PER TIPE SEITAI');
        $activeWorksheet->setCellValue('B2', 'Periode : ' . $tglMasuk . ' s/d ' . $tglKeluar);
        // Style Judul
        phpspreadsheet::styleFont($spreadsheet, 'B1:B2', true, 11, 'Calibri');
        // hide column A
        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setVisible(false);

        // Header
        $columnJenisProduk = 'B';
        $columnJenisProdukEnd = 'C';
        $spreadsheet->getActiveSheet()->mergeCells($columnJenisProduk . '3:' . $columnJenisProdukEnd . '3');
        $activeWorksheet->setCellValue('B3', 'Type Produk');

        // header
        $rowHeaderStart = 3;
        $columnHeaderStart = 'D';
        $columnHeaderEnd = 'D';
        $header = [
            'Jumlah Produksi (Lembar)',
            'Berat Produksi (Kg)',
            'Loss (Kg)',
            'Loss (%)',
            'Seitai Cost',
            'Loss Ponsu (Kg)',
            'Infure Loss (Kg)',
        ];

        foreach ($header as $key => $value) {
            $activeWorksheet->setCellValue($columnHeaderEnd . $rowHeaderStart, $value);
            $columnHeaderEnd++;
        }
        $columnHeaderEnd = chr(ord($columnHeaderEnd) - 1);

        // style header
        phpspreadsheet::addFullBorder($spreadsheet, $columnJenisProduk . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);
        phpspreadsheet::styleFont($spreadsheet, $columnJenisProduk . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart, true, 9, 'Calibri');
        phpspreadsheet::textAlignCenter($spreadsheet, $columnJenisProduk . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);

        $data = DB::select("
            SELECT
                MAX(prT.code) AS product_type_code,
                MAX(prT.name) AS product_type_name,
                SUM(good.qty_produksi) AS qty_produksi,
                SUM(good.qty_produksi * prd.unit_weight * 0.001) AS berat_produksi,
                SUM(good.qty_produksi * prT.harga_sat_seitai) AS seitai_cost,
                SUM(good.seitai_berat_loss) - COALESCE(SUM(ponsu.berat_loss), 0) AS seitai_berat_loss,
                COALESCE(SUM(ponsu.berat_loss), 0) AS seitai_berat_loss_ponsu,
                SUM(good.infure_berat_loss) AS infure_berat_loss
            FROM tdProduct_Goods AS good
            LEFT JOIN (
                SELECT
                    los_.product_goods_id,
                    SUM(los_.berat_loss) AS berat_loss
                FROM tdProduct_Goods_Loss AS los_
                WHERE los_.loss_seitai_id = 1 -- ponsu
                GROUP BY los_.product_goods_id
            ) ponsu ON good.id = ponsu.product_goods_id
            INNER JOIN msProduct AS prd ON good.product_id = prd.id
            INNER JOIN msProduct_type AS prT ON prd.product_type_id = prT.id
            WHERE good.production_date BETWEEN '$tglMasuk' AND '$tglKeluar'
            GROUP BY prT.id
        ");

        if (count($data) == 0) {
            $response = [
                'status' => 'error',
                'message' => "Data pada periode tanggal tersebut tidak ditemukan"
            ];

            return $response;
        }

        // list jenis produk
        $listProductGroup = array_reduce($data, function ($carry, $item) {
            $carry[$item->product_type_code] = [
                'product_type_code' => $item->product_type_code,
                'product_type_name' => $item->product_type_name
            ];
            return $carry;
        }, []);

        $dataFilter = array_reduce($data, function ($carry, $item) {
            $carry[$item->product_type_code] = $item;
            return $carry;
        }, []);

        // index
        $startColumnItem = 'B';
        $endColumnItem = $columnHeaderEnd;
        $startColumnItemData = 'D';
        $columnProductGroupCode = 'B';
        $columnProductGroupName = 'C';
        $startRowItem = 4;
        $rowItem = $startRowItem;
        // daftar departemen
        foreach ($listProductGroup as $productGroupCode => $productGroup) {
            if ($dataFilter[$productGroupCode] == null) {
                continue;
            }
            // daftar mesin
            $columnItem = $startColumnItemData;

            // Menulis data mesin
            $spreadsheet->getActiveSheet()->setCellValue($columnProductGroupCode . $rowItem, $productGroup['product_type_code']);
            $spreadsheet->getActiveSheet()->setCellValue($columnProductGroupName . $rowItem, $productGroup['product_type_name']);
            // phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);

            // memasukkan data
            $dataItem = $dataFilter[$productGroupCode];
            // $dataItem = $dataFilter[$productGroupCode] ?? (object)[
            //     'qty_produksi' => 0,
            //     'berat_produksi' => 0,
            //     'seitai_cost' => 0,
            //     'seitai_berat_loss' => 0,
            //     'seitai_berat_loss_ponsu' => 0,
            //     'infure_berat_loss' => 0
            // ];
            // jumlah produksi
            $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->qty_produksi);
            phpspreadsheet::numberFormatThousands($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // berat_produksi
            $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_produksi);
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // seitai_berat_loss
            $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->seitai_berat_loss);
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // seitai loss %
            $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_produksi > 0 ? $dataItem->seitai_berat_loss / $dataItem->berat_produksi : 0);
            phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // seitai_cost
            $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->seitai_cost);
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // seitai_berat_loss_ponsu
            $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->seitai_berat_loss_ponsu);
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // infure_berat_loss
            $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->infure_berat_loss);
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);
            $columnItem++;

            phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem, false, 8, 'Calibri');
            $rowItem++;
        }

        // Grand total
        $rowGrandTotal = $rowItem;
        $spreadsheet->getActiveSheet()->mergeCells($columnProductGroupCode . $rowGrandTotal . ':' . $columnProductGroupName . $rowGrandTotal);
        $spreadsheet->getActiveSheet()->setCellValue($columnProductGroupCode . $rowGrandTotal, 'GRAND TOTAL');
        phpspreadsheet::styleFont($spreadsheet, $columnProductGroupCode . $rowGrandTotal . ':' . $columnHeaderEnd . $rowGrandTotal, true, 8, 'Calibri');
        // $this->addFullBorder($spreadsheet, $columnProductGroupCode . $rowGrandTotal . ':' . $columnValueAvg . $rowGrandTotal);

        $grandTotal = array_reduce(array_keys($dataFilter), function ($carry, $item) use ($dataFilter) {
            $dataItem = $dataFilter[$item] ?? (object)[
                'qty_produksi' => 0,
                'berat_produksi' => 0,
                'seitai_cost' => 0,
                'seitai_berat_loss' => 0,
                'seitai_berat_loss_ponsu' => 0,
                'infure_berat_loss' => 0
            ];
            $carry['qty_produksi'] += $dataItem->qty_produksi;
            $carry['berat_produksi'] += $dataItem->berat_produksi;
            $carry['seitai_cost'] += $dataItem->seitai_cost;
            $carry['seitai_berat_loss'] += $dataItem->seitai_berat_loss;
            $carry['seitai_berat_loss_ponsu'] += $dataItem->seitai_berat_loss_ponsu;
            $carry['infure_berat_loss'] += $dataItem->infure_berat_loss;
            return $carry;
        }, [
            'qty_produksi' => 0,
            'berat_produksi' => 0,
            'seitai_cost' => 0,
            'seitai_berat_loss' => 0,
            'seitai_berat_loss_ponsu' => 0,
            'infure_berat_loss' => 0
        ]);


        $columnItem = $startColumnItemData;
        $columnItemEnd = chr(ord($columnItem) + count($header) - 1);
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['qty_produksi']);
        phpspreadsheet::numberFormatThousands($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_produksi']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['seitai_berat_loss']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // seitai loss %
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_produksi'] > 0 ? $grandTotal['seitai_berat_loss'] / $grandTotal['berat_produksi'] : 0);
        phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['seitai_cost']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['seitai_berat_loss_ponsu']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['infure_berat_loss']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        phpspreadsheet::addFullBorder($spreadsheet, $columnProductGroupCode . $rowGrandTotal . ':' . $columnItem . $rowGrandTotal);
        $columnItem++;

        $activeWorksheet->getStyle($columnJenisProduk . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart)->getAlignment()->setWrapText(true);

        // size auto
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $endColumnItem++;
        while ($startColumnItemData !== $endColumnItem) {
            $spreadsheet->getActiveSheet()->getColumnDimension($startColumnItemData)->setAutoSize(true);
            $startColumnItemData++;
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

    public function daftarProduksiPerProdukInfure($tglMasuk, $tglKeluar)
    {
        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();
        $activeWorksheet->setShowGridlines(false);
        $spreadsheet->getActiveSheet()->freezePane('A4');
        $activeWorksheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        // Mengatur ukuran kertas menjadi A4
        $activeWorksheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
        // Mengatur agar semua kolom muat dalam satu halaman
        $activeWorksheet->getPageSetup()->setFitToWidth(1);
        $activeWorksheet->getPageSetup()->setFitToHeight(0); // Biarkan tinggi menyesuaikan otomatis
        // Set header berulang untuk print
        $activeWorksheet->getPageSetup()->setRowsToRepeatAtTop([1, 3]);

        // Jika ingin memastikan rasio tetap terjaga
        $activeWorksheet->getPageSetup()->setFitToPage(true);

        // Mengatur margin halaman
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

        // hide column A
        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setVisible(false);

        // Judul
        $activeWorksheet->setCellValue('B1', 'DAFTAR PRODUKSI PER PRODUK INFURE');
        $activeWorksheet->setCellValue('B2', 'Periode : ' . $tglMasuk . ' s/d ' . $tglKeluar);
        // Style Judul
        phpspreadsheet::styleFont($spreadsheet, 'B1:B2', true, 11, 'Calibri');
        // hide column A
        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setVisible(false);

        // Header
        $columnJenisProduk = 'B';
        $columnJenisProdukEnd = 'C';
        $spreadsheet->getActiveSheet()->mergeCells($columnJenisProduk . '3:' . $columnJenisProdukEnd . '3');
        $activeWorksheet->setCellValue('B3', 'Nama Produk');

        // header
        $rowHeaderStart = 3;
        $columnHeaderStart = 'D';
        $columnHeaderEnd = 'D';
        $header = [
            'Berat Standar (Kg)',
            'Berat Produksi (Kg)',
            'Weight Rate',
            'Infure Cost',
            'Loss (Kg)',
            'Loss (%)',
            'Panjang Infure (meter)',
            'Panjang Inline Printing (meter)',
            'Inline Printing Cost',
            'Process Cost',
        ];

        foreach ($header as $key => $value) {
            $activeWorksheet->setCellValue($columnHeaderEnd . $rowHeaderStart, $value);
            $columnHeaderEnd++;
        }
        $columnHeaderEnd = chr(ord($columnHeaderEnd) - 1);

        // style header
        phpspreadsheet::addFullBorder($spreadsheet, $columnJenisProduk . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);
        phpspreadsheet::styleFont($spreadsheet, $columnJenisProduk . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart, true, 9, 'Calibri');
        phpspreadsheet::textAlignCenter($spreadsheet, $columnJenisProduk . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);

        $data = DB::select("
            SELECT
                max(prd.code) AS product_code,
                max(prd.name) AS product_name,
                SUM(asy.berat_standard) AS berat_standard,
                SUM(asy.berat_produksi) AS berat_produksi,
                SUM(asy.infure_cost) AS infure_cost,
                SUM(asy.infure_berat_loss) AS infure_berat_loss,
                SUM(asy.panjang_produksi) AS panjang_produksi,
                SUM(asy.panjang_printing_inline) AS panjang_printing_inline,
                SUM(asy.infure_cost_printing) AS infure_cost_printing
            FROM tdProduct_Assembly AS asy
            INNER JOIN msProduct AS prd ON asy.product_id = prd.id
            WHERE asy.production_date BETWEEN '$tglMasuk' AND '$tglKeluar'
            GROUP BY prd.id
            ORDER BY prd.name
        ");

        if (count($data) == 0) {
            $response = [
                'status' => 'error',
                'message' => "Data pada periode tanggal tersebut tidak ditemukan"
            ];

            return $response;
        }

        // list jenis produk
        $listProduct = array_reduce($data, function ($carry, $item) {
            $carry[$item->product_code] = [
                'product_code' => $item->product_code,
                'product_name' => $item->product_name
            ];
            return $carry;
        }, []);

        $dataFilter = array_reduce($data, function ($carry, $item) {
            $carry[$item->product_code] = $item;
            return $carry;
        }, []);

        // index
        $startColumnItem = 'B';
        $endColumnItem = $columnHeaderEnd;
        $startColumnItemData = 'D';
        $columnProductCode = 'B';
        $columnProductName = 'C';
        $startRowItem = 4;
        $rowItem = $startRowItem;
        // daftar departemen
        foreach ($listProduct as $productCode => $product) {
            if ($dataFilter[$productCode] == null) {
                continue;
            }
            // daftar mesin
            $columnItem = $startColumnItemData;

            // Menulis data mesin
            $spreadsheet->getActiveSheet()->setCellValue($columnProductCode . $rowItem, $product['product_code']);
            $spreadsheet->getActiveSheet()->setCellValue($columnProductName . $rowItem, $product['product_name']);

            // memasukkan data
            $dataItem = $dataFilter[$productCode];
            $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_standard);
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_produksi);
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_standard > 0 ? $dataItem->berat_produksi / $dataItem->berat_standard : 0);
            phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->infure_cost);
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->infure_berat_loss);
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_produksi > 0 ? $dataItem->infure_berat_loss / $dataItem->berat_produksi : 0);
            phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->panjang_produksi);
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->panjang_printing_inline);
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->infure_cost_printing);
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->infure_cost + $dataItem->infure_cost_printing);
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);
            $columnItem++;

            phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem, false, 8, 'Calibri');
            $rowItem++;
        }

        // Grand total
        $rowGrandTotal = $rowItem;
        $spreadsheet->getActiveSheet()->mergeCells($columnProductCode . $rowGrandTotal . ':' . $columnProductName . $rowGrandTotal);
        $spreadsheet->getActiveSheet()->setCellValue($columnProductCode . $rowGrandTotal, 'GRAND TOTAL');
        phpspreadsheet::styleFont($spreadsheet, $columnProductCode . $rowGrandTotal . ':' . $columnHeaderEnd . $rowGrandTotal, true, 8, 'Calibri');
        // $this->addFullBorder($spreadsheet, $columnProductCode . $rowGrandTotal . ':' . $columnValueAvg . $rowGrandTotal);

        $grandTotal = array_reduce(array_keys($dataFilter), function ($carry, $item) use ($dataFilter) {
            $dataItem = $dataFilter[$item] ?? (object)[
                'berat_standard' => 0,
                'berat_produksi' => 0,
                'infure_cost' => 0,
                'infure_berat_loss' => 0,
                'panjang_produksi' => 0,
                'panjang_printing_inline' => 0,
                'infure_cost_printing' => 0,
            ];
            $carry['berat_standard'] += $dataItem->berat_standard;
            $carry['berat_produksi'] += $dataItem->berat_produksi;
            $carry['infure_cost'] += $dataItem->infure_cost;
            $carry['infure_berat_loss'] += $dataItem->infure_berat_loss;
            $carry['panjang_produksi'] += $dataItem->panjang_produksi;
            $carry['panjang_printing_inline'] += $dataItem->panjang_printing_inline;
            $carry['infure_cost_printing'] += $dataItem->infure_cost_printing;
            return $carry;
        }, [
            'berat_standard' => 0,
            'berat_produksi' => 0,
            'infure_cost' => 0,
            'infure_berat_loss' => 0,
            'panjang_produksi' => 0,
            'panjang_printing_inline' => 0,
            'infure_cost_printing' => 0,
        ]);

        $columnItem = $startColumnItemData;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_standard']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_produksi']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_standard'] > 0 ? $grandTotal['berat_produksi'] / $grandTotal['berat_standard'] : 0);
        phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['infure_cost']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['infure_berat_loss']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_produksi'] > 0 ? $grandTotal['infure_berat_loss'] / $grandTotal['berat_produksi'] : 0);
        phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['panjang_produksi']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['panjang_printing_inline']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['infure_cost_printing']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['infure_cost'] + $grandTotal['infure_cost_printing']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
        phpspreadsheet::addFullBorder($spreadsheet, $columnProductCode . $rowGrandTotal . ':' . $columnItem . $rowGrandTotal);
        $columnItem++;

        $activeWorksheet->getStyle($columnJenisProduk . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart)->getAlignment()->setWrapText(true);

        $columnWidthMap = [
            'D' => 15,  // Berat Standar
            'E' => 16,  // Berat Produksi
            'F' => 12,  // Weight Rate
            'G' => 14,  // Infure Cost
            'H' => 10,  // Loss Kg
            'I' => 9,   // Loss %
            'J' => 18,  // Panjang Infure
            'K' => 22,  // Inline Printing
            'L' => 16,  // Inline Cost
            'M' => 14,  // Process Cost
        ];

        // Set kolom C
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);

        // Set kolom data
        foreach (range($startColumnItemData, $columnHeaderEnd) as $columnID) {
            $width = isset($columnWidthMap[$columnID]) ? $columnWidthMap[$columnID] : 12;
            $spreadsheet->getActiveSheet()->getColumnDimension($columnID)->setWidth($width);
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

    public function daftarProduksiPerProdukSeitai($tglMasuk, $tglKeluar)
    {
        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();
        $activeWorksheet->setShowGridlines(false);
        $spreadsheet->getActiveSheet()->freezePane('A4');
        $activeWorksheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
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

        // Judul
        $activeWorksheet->setCellValue('B1', 'DAFTAR PRODUKSI PER PRODUK SEITAI');
        $activeWorksheet->setCellValue('B2', 'Periode : ' . $tglMasuk . ' s/d ' . $tglKeluar);
        // Style Judul
        phpspreadsheet::styleFont($spreadsheet, 'B1:B2', true, 11, 'Calibri');
        // hide column A
        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setVisible(false);

        // Header
        $columnJenisProduk = 'B';
        $columnJenisProdukEnd = 'C';
        $spreadsheet->getActiveSheet()->mergeCells($columnJenisProduk . '3:' . $columnJenisProdukEnd . '3');
        $activeWorksheet->setCellValue('B3', 'Nama Produk');

        // header
        $rowHeaderStart = 3;
        $columnHeaderStart = 'D';
        $columnHeaderEnd = 'D';
        $header = [
            'Jumlah Produksi (Lembar)',
            'Berat Produksi (Kg)',
            'Loss (Kg)',
            'Loss (%)',
            'Seitai Cost',
            'Loss Ponsu (Kg)',
            'Infure Loss (Kg)',
        ];

        foreach ($header as $key => $value) {
            $activeWorksheet->setCellValue($columnHeaderEnd . $rowHeaderStart, $value);
            $columnHeaderEnd++;
        }
        $columnHeaderEnd = chr(ord($columnHeaderEnd) - 1);

        // style header
        phpspreadsheet::addFullBorder($spreadsheet, $columnJenisProduk . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);
        phpspreadsheet::styleFont($spreadsheet, $columnJenisProduk . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart, true, 9, 'Calibri');
        phpspreadsheet::textAlignCenter($spreadsheet, $columnJenisProduk . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);

        $data = DB::select("
            SELECT
                MAX(prd.code) AS product_code,
                MAX(prd.name) AS product_name,
                SUM(good.qty_produksi) AS qty_produksi,
                SUM(good.qty_produksi * prd.unit_weight * 0.001) AS berat_produksi,
                SUM(good.qty_produksi * prT.harga_sat_seitai) AS seitai_cost,
                SUM(good.seitai_berat_loss) - COALESCE(SUM(ponsu.berat_loss), 0) AS seitai_berat_loss,
                COALESCE(SUM(ponsu.berat_loss), 0) AS seitai_berat_loss_ponsu,
                SUM(good.infure_berat_loss) AS infure_berat_loss
            FROM tdProduct_Goods AS good
            LEFT JOIN (
                SELECT
                    los_.product_goods_id,
                    SUM(los_.berat_loss) AS berat_loss
                FROM tdProduct_Goods_Loss AS los_
                WHERE los_.loss_seitai_id = 1 -- ponsu
                GROUP BY los_.product_goods_id
            ) ponsu ON good.id = ponsu.product_goods_id
            INNER JOIN msProduct AS prd ON good.product_id = prd.id
            INNER JOIN msProduct_type AS prT ON prd.product_type_id = prT.id
            WHERE good.production_date BETWEEN '$tglMasuk' AND '$tglKeluar'
            GROUP BY prd.id
            ORDER BY prd.name
        ");

        if (count($data) == 0) {
            $response = [
                'status' => 'error',
                'message' => "Data pada periode tanggal tersebut tidak ditemukan"
            ];

            return $response;
        }

        // list jenis produk
        $listProduct = array_reduce($data, function ($carry, $item) {
            $carry[$item->product_code] = [
                'product_code' => $item->product_code,
                'product_name' => $item->product_name
            ];
            return $carry;
        }, []);

        $dataFilter = array_reduce($data, function ($carry, $item) {
            $carry[$item->product_code] = $item;
            return $carry;
        }, []);

        // index
        $startColumnItem = 'B';
        $endColumnItem = $columnHeaderEnd;
        $startColumnItemData = 'D';
        $columnProductCode = 'B';
        $columnProductName = 'C';
        $startRowItem = 4;
        $rowItem = $startRowItem;
        // daftar departemen
        foreach ($listProduct as $productCode => $product) {
            if ($dataFilter[$productCode] == null) {
                continue;
            }
            // daftar mesin
            $columnItem = $startColumnItemData;

            // Menulis data mesin
            $spreadsheet->getActiveSheet()->setCellValue($columnProductCode . $rowItem, $product['product_code']);
            $spreadsheet->getActiveSheet()->setCellValue($columnProductName . $rowItem, $product['product_name']);
            // phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);

            // memasukkan data
            $dataItem = $dataFilter[$productCode];
            // jumlah produksi
            $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->qty_produksi);
            phpspreadsheet::numberFormatThousands($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // berat_produksi
            $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_produksi);
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // seitai_berat_loss
            $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->seitai_berat_loss);
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // seitai loss %
            $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_produksi > 0 ? $dataItem->seitai_berat_loss / $dataItem->berat_produksi : 0);
            phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // seitai_cost
            $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->seitai_cost);
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // seitai_berat_loss_ponsu
            $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->seitai_berat_loss_ponsu);
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // infure_berat_loss
            $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->infure_berat_loss);
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);
            $columnItem++;

            phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem, false, 8, 'Calibri');
            $rowItem++;
        }

        // Grand total
        $rowGrandTotal = $rowItem;
        $spreadsheet->getActiveSheet()->mergeCells($columnProductCode . $rowGrandTotal . ':' . $columnProductName . $rowGrandTotal);
        $spreadsheet->getActiveSheet()->setCellValue($columnProductCode . $rowGrandTotal, 'GRAND TOTAL');
        phpspreadsheet::styleFont($spreadsheet, $columnProductCode . $rowGrandTotal . ':' . $columnHeaderEnd . $rowGrandTotal, true, 8, 'Calibri');
        // $this->addFullBorder($spreadsheet, $columnProductCode . $rowGrandTotal . ':' . $columnValueAvg . $rowGrandTotal);

        $grandTotal = array_reduce(array_keys($dataFilter), function ($carry, $item) use ($dataFilter) {
            $dataItem = $dataFilter[$item] ?? (object)[
                'qty_produksi' => 0,
                'berat_produksi' => 0,
                'seitai_cost' => 0,
                'seitai_berat_loss' => 0,
                'seitai_berat_loss_ponsu' => 0,
                'infure_berat_loss' => 0
            ];
            $carry['qty_produksi'] += $dataItem->qty_produksi;
            $carry['berat_produksi'] += $dataItem->berat_produksi;
            $carry['seitai_cost'] += $dataItem->seitai_cost;
            $carry['seitai_berat_loss'] += $dataItem->seitai_berat_loss;
            $carry['seitai_berat_loss_ponsu'] += $dataItem->seitai_berat_loss_ponsu;
            $carry['infure_berat_loss'] += $dataItem->infure_berat_loss;
            return $carry;
        }, [
            'qty_produksi' => 0,
            'berat_produksi' => 0,
            'seitai_cost' => 0,
            'seitai_berat_loss' => 0,
            'seitai_berat_loss_ponsu' => 0,
            'infure_berat_loss' => 0
        ]);


        $columnItem = $startColumnItemData;
        $columnItemEnd = chr(ord($columnItem) + count($header) - 1);
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['qty_produksi']);
        phpspreadsheet::numberFormatThousands($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_produksi']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['seitai_berat_loss']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // seitai loss %
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_produksi'] > 0 ? $grandTotal['seitai_berat_loss'] / $grandTotal['berat_produksi'] : 0);
        phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['seitai_cost']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['seitai_berat_loss_ponsu']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['infure_berat_loss']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        phpspreadsheet::addFullBorder($spreadsheet, $columnProductCode . $rowGrandTotal . ':' . $columnItem . $rowGrandTotal);
        $columnItem++;

        $activeWorksheet->getStyle($columnJenisProduk . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart)->getAlignment()->setWrapText(true);

        // size auto
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $endColumnItem++;
        while ($startColumnItemData !== $endColumnItem) {
            $spreadsheet->getActiveSheet()->getColumnDimension($startColumnItemData)->setAutoSize(true);
            $startColumnItemData++;
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

    public function daftarProduksiPerDepartemenPerJenisInfure($tglMasuk, $tglKeluar)
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
        $spreadsheet->getActiveSheet()->freezePane('A4');

        // Judul
        $activeWorksheet->setCellValue('B1', 'DAFTAR PRODUKSI PER DEPARTEMEN PER JENIS INFURE');
        $activeWorksheet->setCellValue('B2', 'Periode : ' . $tglMasuk . ' s/d ' . $tglKeluar);
        // Style Judul
        phpspreadsheet::styleFont($spreadsheet, 'B1:B2', true, 11, 'Calibri');
        // hide column A
        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setVisible(false);

        // Header
        $columnMesin = 'B';
        $columnMesinEnd = 'C';
        $spreadsheet->getActiveSheet()->mergeCells($columnMesin . '3:' . $columnMesinEnd . '3');
        $activeWorksheet->setCellValue('B3', 'Jenis Produk');

        // header
        $rowHeaderStart = 3;
        $columnHeaderStart = 'D';
        $columnHeaderEnd = 'D';
        $header = [
            'Berat Standar (Kg)',
            'Berat Produksi (Kg)',
            'Weight Rate',
            'Infure Cost',
            'Loss (Kg)',
            'Loss (%)',
            'Panjang Infure (meter)',
            'Inline Printing (meter)',
            'Inline Printing Cost',
            'Process Cost',
        ];

        foreach ($header as $key => $value) {
            $activeWorksheet->setCellValue($columnHeaderEnd . $rowHeaderStart, $value);
            $columnHeaderEnd++;
        }
        $columnHeaderEnd = chr(ord($columnHeaderEnd) - 1);

        // style header
        phpspreadsheet::addFullBorder($spreadsheet, $columnMesin . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);
        phpspreadsheet::styleFont($spreadsheet, $columnMesin . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart, true, 9, 'Calibri');
        phpspreadsheet::textAlignCenter($spreadsheet, $columnMesin . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);

        $data = DB::select("
            SELECT
                max(dep.name) AS department_name,
                max(dep.id) AS department_id,
                max(prGrp.code) AS product_group_code,
                max(prGrp.name) AS product_group_name,
                SUM(asy.berat_standard) AS berat_standard,
                SUM(asy.berat_produksi) AS berat_produksi,
                SUM(asy.infure_cost) AS infure_cost,
                SUM(asy.infure_berat_loss) AS infure_berat_loss,
                SUM(asy.panjang_produksi) AS panjang_produksi,
                SUM(asy.panjang_printing_inline) AS panjang_printing_inline,
                SUM(asy.infure_cost_printing) AS infure_cost_printing
            FROM tdProduct_Assembly AS asy
            INNER JOIN msMachine AS mac ON asy.machine_id = mac.id
            INNER JOIN msDepartment AS dep ON mac.department_id = dep.id
            INNER JOIN msProduct AS prd ON asy.product_id = prd.id
            INNER JOIN msProduct_type AS prTip ON prd.product_type_id = prTip.id
            INNER JOIN msProduct_group AS prGrp ON prTip.product_group_id = prGrp.id
            WHERE asy.production_date BETWEEN '$tglMasuk' AND '$tglKeluar'
            GROUP BY dep.id, prGrp.id
        ");

        if (count($data) == 0) {
            $response = [
                'status' => 'error',
                'message' => "Data pada periode tanggal tersebut tidak ditemukan"
            ];

            return $response;
        }
        $listDepartment = array_reduce($data, function ($carry, $item) {
            $carry[$item->department_id] = [
                'department_id' => $item->department_id,
                'department_name' => $item->department_name
            ];
            return $carry;
        }, []);

        // list jenis produk
        $listProductGroup = array_reduce($data, function ($carry, $item) {
            $carry[$item->department_id][$item->product_group_code] = $item->product_group_name;
            return $carry;
        }, []);

        $dataFilter = array_reduce($data, function ($carry, $item) {
            $carry[$item->department_id][$item->product_group_code] = $item;
            return $carry;
        }, []);

        // index
        $startColumnItem = 'B';
        $endColumnItem = $columnHeaderEnd;
        $startColumnItemData = 'D';
        $columnTypeCode = 'B';
        $columnTypeName = 'C';
        $startRowItem = 4;
        $rowItem = $startRowItem;
        // daftar departemen
        foreach ($listDepartment as $department) {
            // Menulis data departemen
            $activeWorksheet->setCellValue($startColumnItem . $rowItem, $department['department_name']);
            $spreadsheet->getActiveSheet()->mergeCells($startColumnItem . $rowItem . ':' . $endColumnItem . $rowItem);
            phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowItem, true, 9, 'Calibri');
            $rowItem++;
            // daftar mesin
            foreach ($listProductGroup[$department['department_id']] as $typeCode => $typeName) {
                if ($dataFilter[$department['department_id']][$typeCode] == null) {
                    continue;
                }
                $columnItem = $startColumnItemData;

                // Menulis data mesin
                $spreadsheet->getActiveSheet()->setCellValue($columnTypeCode . $rowItem, $typeCode);
                $spreadsheet->getActiveSheet()->setCellValue($columnTypeName . $rowItem, $typeName);
                // phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);

                // memasukkan data
                $dataItem = $dataFilter[$department['department_id']][$typeCode];

                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_standard);
                phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_produksi);
                phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_standard > 0 ? $dataItem->berat_produksi / $dataItem->berat_standard : 0);
                phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->infure_cost);
                phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->infure_berat_loss);
                phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_produksi > 0 ? $dataItem->infure_berat_loss / $dataItem->berat_produksi : 0);
                phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->panjang_produksi);
                phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->panjang_printing_inline);
                phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->infure_cost_printing);
                phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->infure_cost + $dataItem->infure_cost_printing);
                phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);
                $columnItem++;

                phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem, false, 8, 'Calibri');
                $rowItem++;
            }
            // perhitungan jumlah berdasarkan departemen
            $spreadsheet->getActiveSheet()->mergeCells($columnTypeCode . $rowItem . ':' . $columnTypeName . $rowItem);
            $activeWorksheet->setCellValue($columnTypeCode . $rowItem, 'Sub Total');
            $columnItem = $startColumnItemData;
            $columnItemEnd = chr(ord($columnItem) + count($header) - 1);
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listProductGroup[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listProductGroup[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listProductGroup[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')/COUNTIF(' . $columnItem . ($rowItem - count($listProductGroup[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ', "<>0")');
            phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listProductGroup[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listProductGroup[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listProductGroup[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')/COUNTIF(' . $columnItem . ($rowItem - count($listProductGroup[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ', "<>0")');
            phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listProductGroup[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listProductGroup[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listProductGroup[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listProductGroup[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            phpspreadsheet::addFullBorder($spreadsheet, $columnTypeCode . $rowItem . ':' . $columnItem . $rowItem);
            phpspreadsheet::styleFont($spreadsheet, $columnTypeCode . $rowItem . ':' . $columnItem . $rowItem, true, 8, 'Calibri');
            $columnItem++;

            $rowItem++;
            $rowItem++;
        }

        // Grand total
        $rowGrandTotal = $rowItem;
        $spreadsheet->getActiveSheet()->mergeCells($columnTypeCode . $rowGrandTotal . ':' . $columnTypeName . $rowGrandTotal);
        $spreadsheet->getActiveSheet()->setCellValue($columnTypeCode . $rowGrandTotal, 'GRAND TOTAL');
        phpspreadsheet::styleFont($spreadsheet, $columnTypeCode . $rowGrandTotal . ':' . $columnHeaderEnd . $rowGrandTotal, true, 8, 'Calibri');
        // $this->addFullBorder($spreadsheet, $columnTypeCode . $rowGrandTotal . ':' . $columnValueAvg . $rowGrandTotal);

        // grand total
        $grandTotal = [
            'berat_standard' => 0,
            'berat_produksi' => 0,
            'infure_cost' => 0,
            'infure_berat_loss' => 0,
            'panjang_produksi' => 0,
            'panjang_printing_inline' => 0,
            'infure_cost_printing' => 0
        ];

        foreach ($dataFilter as $departmentId => $department) {
            foreach ($department as $productGroupCode => $productGroup) {
                $grandTotal['berat_standard'] += $productGroup->berat_standard;
                $grandTotal['berat_produksi'] += $productGroup->berat_produksi;
                $grandTotal['infure_cost'] += $productGroup->infure_cost;
                $grandTotal['infure_berat_loss'] += $productGroup->infure_berat_loss;
                $grandTotal['panjang_produksi'] += $productGroup->panjang_produksi;
                $grandTotal['panjang_printing_inline'] += $productGroup->panjang_printing_inline;
                $grandTotal['infure_cost_printing'] += $productGroup->infure_cost_printing;
            }
        }

        $columnItem = $startColumnItemData;
        $columnItemEnd = chr(ord($columnItem) + count($header) - 1);
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_standard']);
        phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_produksi']);
        phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_standard'] > 0 ? $grandTotal['berat_produksi'] / $grandTotal['berat_standard'] : 0);
        phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['infure_cost']);
        phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['infure_berat_loss']);
        phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_produksi'] > 0 ? $grandTotal['infure_berat_loss'] / $grandTotal['berat_produksi'] : 0);
        phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['panjang_produksi']);
        phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['panjang_printing_inline']);
        phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['infure_cost_printing']);
        phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['infure_cost'] + $grandTotal['infure_cost_printing']);
        phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowGrandTotal);
        phpspreadsheet::addFullBorder($spreadsheet, $columnTypeCode . $rowGrandTotal . ':' . $columnItem . $rowGrandTotal);
        $columnItem++;

        $activeWorksheet->getStyle($columnMesin . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart)->getAlignment()->setWrapText(true);

        // size auto
        $endColumnItem++;
        while ($startColumnItemData !== $endColumnItem) {
            $spreadsheet->getActiveSheet()->getColumnDimension($startColumnItemData)->setAutoSize(true);
            $startColumnItemData++;
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

    public function daftarProduksiPerDepartemenPerJenisSeitai($tglMasuk, $tglKeluar)
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
        $activeWorksheet->setCellValue('B1', 'DAFTAR PRODUKSI PER DEPARTEMEN PER JENIS SEITAI');
        $activeWorksheet->setCellValue('B2', 'Periode : ' . $tglMasuk . ' s/d ' . $tglKeluar);
        // Style Judul
        phpspreadsheet::styleFont($spreadsheet, 'B1:B2', true, 11, 'Calibri');
        // hide column A
        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setVisible(false);

        // Header
        $columnMesin = 'B';
        $columnMesinEnd = 'C';
        $spreadsheet->getActiveSheet()->mergeCells($columnMesin . '3:' . $columnMesinEnd . '3');
        $activeWorksheet->setCellValue('B3', 'Jenis Produk');

        // header
        $rowHeaderStart = 3;
        $columnHeaderStart = 'D';
        $columnHeaderEnd = 'D';
        $header = [
            'Jumlah Produksi (Lembar)',
            'Berat Standar (Kg)',
            'Loss (Kg)',
            'Loss (%)',
            'Seitai Cost',
            'Ponsu Loss (Kg)',
            'Infure Loss (Kg)',
        ];

        foreach ($header as $key => $value) {
            $activeWorksheet->setCellValue($columnHeaderEnd . $rowHeaderStart, $value);
            $columnHeaderEnd++;
        }
        $columnHeaderEnd = chr(ord($columnHeaderEnd) - 1);

        // style header
        phpspreadsheet::addFullBorder($spreadsheet, $columnMesin . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);
        phpspreadsheet::styleFont($spreadsheet, $columnMesin . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart, true, 9, 'Calibri');
        phpspreadsheet::textAlignCenter($spreadsheet, $columnMesin . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);

        $data = DB::select("
            SELECT
                MAX(dep.name) AS department_name,
                MAX(dep.id) AS department_id,
                MAX(prGrp.code) AS product_group_code,
                MAX(prGrp.name) AS product_group_name,
                SUM(good.qty_produksi) AS qty_produksi,
                SUM(good.qty_produksi * prd.unit_weight * 0.001) AS berat_produksi,
                SUM(good.qty_produksi * prT.harga_sat_seitai) AS seitai_cost,
                SUM(good.seitai_berat_loss) - COALESCE(SUM(ponsu.berat_loss), 0) AS seitai_berat_loss,
                COALESCE(SUM(ponsu.berat_loss), 0) AS seitai_berat_loss_ponsu,
                SUM(good.infure_berat_loss) AS infure_berat_loss
            FROM tdProduct_Goods AS good
            LEFT JOIN (
                SELECT
                    los_.product_goods_id,
                    SUM(los_.berat_loss) AS berat_loss
                FROM tdProduct_Goods_Loss AS los_
                WHERE los_.loss_seitai_id = 1 -- ponsu
                GROUP BY los_.product_goods_id
            ) ponsu ON good.id = ponsu.product_goods_id
            INNER JOIN msMachine AS mac ON good.machine_id = mac.id
            INNER JOIN msDepartment AS dep ON mac.department_id = dep.id
            INNER JOIN msProduct AS prd ON good.product_id = prd.id
            INNER JOIN msProduct_type AS prT ON prd.product_type_id = prT.id
            INNER JOIN msProduct_group AS prGrp ON prT.product_group_id = prGrp.id
            WHERE good.production_date BETWEEN '$tglMasuk' AND '$tglKeluar'
            GROUP BY dep.id, prGrp.id
        ");

        if (count($data) == 0) {
            $response = [
                'status' => 'error',
                'message' => "Data pada periode tanggal tersebut tidak ditemukan"
            ];

            return $response;
        }

        $listDepartment = array_reduce($data, function ($carry, $item) {
            $carry[$item->department_id] = [
                'department_id' => $item->department_id,
                'department_name' => $item->department_name
            ];
            return $carry;
        }, []);

        // list jenis produk
        $listProductGroup = array_reduce($data, function ($carry, $item) {
            $carry[$item->department_id][$item->product_group_code] = $item->product_group_name;
            return $carry;
        }, []);

        $dataFilter = array_reduce($data, function ($carry, $item) {
            $carry[$item->department_id][$item->product_group_code] = $item;
            return $carry;
        }, []);

        // index
        $startColumnItem = 'B';
        $endColumnItem = $columnHeaderEnd;
        $startColumnItemData = 'D';
        $columnTypeCode = 'B';
        $columnTypeName = 'C';
        $startRowItem = 5;
        $rowItem = $startRowItem;
        // daftar departemen
        foreach ($listDepartment as $department) {
            // Menulis data departemen
            $activeWorksheet->setCellValue($startColumnItem . $rowItem, $department['department_name']);
            $spreadsheet->getActiveSheet()->mergeCells($startColumnItem . $rowItem . ':' . $endColumnItem . $rowItem);
            phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowItem, true, 9, 'Calibri');
            $rowItem++;
            // daftar mesin
            foreach ($listProductGroup[$department['department_id']] as $TypeCode => $TypeName) {
                if ($dataFilter[$department['department_id']][$TypeCode] == null) {
                    continue;
                }
                $columnItem = $startColumnItemData;

                // Menulis data mesin
                $spreadsheet->getActiveSheet()->setCellValue($columnTypeCode . $rowItem, $TypeCode);
                $spreadsheet->getActiveSheet()->setCellValue($columnTypeName . $rowItem, $TypeName);
                // phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);

                // memasukkan data
                $dataItem = $dataFilter[$department['department_id']][$TypeCode];
                // jumlah produksi
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->qty_produksi);
                phpspreadsheet::numberFormatThousands($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // berat_produksi
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_produksi);
                phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // seitai_berat_loss
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->seitai_berat_loss);
                phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // seitai loss %
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_produksi > 0 ? $dataItem->seitai_berat_loss / $dataItem->berat_produksi : 0);
                phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // seitai_cost
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->seitai_cost);
                phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // seitai_berat_loss_ponsu
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->seitai_berat_loss_ponsu);
                phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // infure_berat_loss
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->infure_berat_loss);
                phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);
                $columnItem++;

                phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem, false, 8, 'Calibri');
                $rowItem++;
            }
            // perhitungan jumlah berdasarkan departemen
            $spreadsheet->getActiveSheet()->mergeCells($columnTypeCode . $rowItem . ':' . $columnTypeName . $rowItem);
            // $activeWorksheet->setCellValue($columnTypeCode . $rowItem, 'Total ' . $department['department_name']);
            $columnItem = $startColumnItemData;
            $columnItemEnd = chr(ord($columnItem) + count($header) - 1);
            // jumlah produksi
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listProductGroup[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpspreadsheet::numberFormatThousands($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // berat_produksi
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listProductGroup[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // seitai_berat_loss
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listProductGroup[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // seitai loss %
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listProductGroup[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')/COUNTIF(' . $columnItem . ($rowItem - count($listProductGroup[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ', "<>0")');
            phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // seitai_cost
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listProductGroup[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // seitai_berat_loss_ponsu
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listProductGroup[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // infure_berat_loss
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listProductGroup[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            phpspreadsheet::addFullBorder($spreadsheet, $columnTypeCode . $rowItem . ':' . $columnItem . $rowItem);
            phpspreadsheet::styleFont($spreadsheet, $columnTypeCode . $rowItem . ':' . $columnItem . $rowItem, true, 8, 'Calibri');
            $columnItem++;


            $rowItem++;
            $rowItem++;
        }

        // Grand total
        $rowGrandTotal = $rowItem;
        $spreadsheet->getActiveSheet()->mergeCells($columnTypeCode . $rowGrandTotal . ':' . $columnTypeName . $rowGrandTotal);
        $spreadsheet->getActiveSheet()->setCellValue($columnTypeCode . $rowGrandTotal, 'GRAND TOTAL');
        phpspreadsheet::styleFont($spreadsheet, $columnTypeCode . $rowGrandTotal . ':' . $columnHeaderEnd . $rowGrandTotal, true, 8, 'Calibri');
        // $this->addFullBorder($spreadsheet, $columnTypeCode . $rowGrandTotal . ':' . $columnValueAvg . $rowGrandTotal);

        // grand total
        $grandTotal = [
            'qty_produksi' => 0,
            'berat_produksi' => 0,
            'seitai_berat_loss' => 0,
            'seitai_cost' => 0,
            'seitai_berat_loss_ponsu' => 0,
            'infure_berat_loss' => 0,
        ];

        foreach ($dataFilter as $departmentId => $department) {
            foreach ($department as $productGroupCode => $productGroup) {
                $grandTotal['qty_produksi'] += $productGroup->qty_produksi;
                $grandTotal['berat_produksi'] += $productGroup->berat_produksi;
                $grandTotal['seitai_berat_loss'] += $productGroup->seitai_berat_loss;
                $grandTotal['seitai_cost'] += $productGroup->seitai_cost;
                $grandTotal['seitai_berat_loss_ponsu'] += $productGroup->seitai_berat_loss_ponsu;
                $grandTotal['infure_berat_loss'] += $productGroup->infure_berat_loss;
            }
        }

        $columnItem = $startColumnItemData;
        $columnItemEnd = chr(ord($columnItem) + count($header) - 1);
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['qty_produksi']);
        phpspreadsheet::numberFormatThousands($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_produksi']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['seitai_berat_loss']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // seitai loss %
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_produksi'] > 0 ? $grandTotal['seitai_berat_loss'] / $grandTotal['berat_produksi'] : 0);
        phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['seitai_cost']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['seitai_berat_loss_ponsu']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['infure_berat_loss']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        phpspreadsheet::addFullBorder($spreadsheet, $columnTypeCode . $rowGrandTotal . ':' . $columnItem . $rowGrandTotal);
        $columnItem++;

        $activeWorksheet->getStyle($columnMesin . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart)->getAlignment()->setWrapText(true);

        // size auto
        $endColumnItem++;
        while ($startColumnItemData !== $endColumnItem) {
            $spreadsheet->getActiveSheet()->getColumnDimension($startColumnItemData)->setAutoSize(true);
            $startColumnItemData++;
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

    public function daftarProduksiPerDepartemenPerTypeInfure($tglMasuk, $tglKeluar)
    {
        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();
        $activeWorksheet->setShowGridlines(false);
        $activeWorksheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $spreadsheet->getActiveSheet()->freezePane('A4');
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

        // Judul
        $activeWorksheet->setCellValue('B1', 'DAFTAR PRODUKSI PER DEPARTEMEN PER TIPE INFURE');
        $activeWorksheet->setCellValue('B2', 'Periode : ' . $tglMasuk . ' s/d ' . $tglKeluar);
        // Style Judul
        phpspreadsheet::styleFont($spreadsheet, 'B1:B2', true, 11, 'Calibri');
        // hide column A
        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setVisible(false);

        // Header
        $columnMesin = 'B';
        $columnMesinEnd = 'C';
        $spreadsheet->getActiveSheet()->mergeCells($columnMesin . '3:' . $columnMesinEnd . '3');
        $activeWorksheet->setCellValue('B3', 'Tipe Produk');

        // header
        $rowHeaderStart = 3;
        $columnHeaderStart = 'D';
        $columnHeaderEnd = 'D';
        $header = [
            'Berat Standar (Kg)',
            'Berat Produksi (Kg)',
            'Weight Rate',
            'Infure Cost',
            'Loss (Kg)',
            'Loss (%)',
            'Panjang Infure (meter)',
            'Inline Printing (meter)',
            'Inline Printing Cost',
            'Process Cost',
        ];

        foreach ($header as $key => $value) {
            $activeWorksheet->setCellValue($columnHeaderEnd . $rowHeaderStart, $value);
            $columnHeaderEnd++;
        }
        $columnHeaderEnd = chr(ord($columnHeaderEnd) - 1);

        // style header
        phpspreadsheet::addFullBorder($spreadsheet, $columnMesin . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);
        phpspreadsheet::styleFont($spreadsheet, $columnMesin . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart, true, 9, 'Calibri');
        phpspreadsheet::textAlignCenter($spreadsheet, $columnMesin . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);

        $data = DB::select("
            SELECT
                max(dep.name) AS department_name,
                max(dep.id) AS department_id,
                max(prTip.code) AS product_type_code,
                max(prTip.name) AS product_type_name,
                SUM(asy.berat_standard) AS berat_standard,
                SUM(asy.berat_produksi) AS berat_produksi,
                SUM(asy.infure_cost) AS infure_cost,
                SUM(asy.infure_berat_loss) AS infure_berat_loss,
                SUM(asy.panjang_produksi) AS panjang_produksi,
                SUM(asy.panjang_printing_inline) AS panjang_printing_inline,
                SUM(asy.infure_cost_printing) AS infure_cost_printing
            FROM tdProduct_Assembly AS asy
            INNER JOIN msMachine AS mac ON asy.machine_id = mac.id
            INNER JOIN msDepartment AS dep ON mac.department_id = dep.id
            INNER JOIN msProduct AS prd ON asy.product_id = prd.id
            INNER JOIN msProduct_type AS prTip ON prd.product_type_id = prTip.id
            WHERE asy.production_date BETWEEN '$tglMasuk' AND '$tglKeluar'
            GROUP BY dep.id, prTip.id
        ");

        if (count($data) == 0) {
            $response = [
                'status' => 'error',
                'message' => "Data pada periode tanggal tersebut tidak ditemukan"
            ];

            return $response;
        }

        $listDepartment = array_reduce($data, function ($carry, $item) {
            $carry[$item->department_id] = [
                'department_id' => $item->department_id,
                'department_name' => $item->department_name
            ];
            return $carry;
        }, []);

        // list jenis produk
        $listProductType = array_reduce($data, function ($carry, $item) {
            $carry[$item->department_id][$item->product_type_code] = $item->product_type_name;
            return $carry;
        }, []);

        $dataFilter = array_reduce($data, function ($carry, $item) {
            $carry[$item->department_id][$item->product_type_code] = $item;
            return $carry;
        }, []);

        // index
        $startColumnItem = 'B';
        $endColumnItem = $columnHeaderEnd;
        $startColumnItemData = 'D';
        $columnTypeCode = 'B';
        $columnTypeName = 'C';
        $startRowItem = 4;
        $rowItem = $startRowItem;
        // daftar departemen
        foreach ($listDepartment as $department) {
            // Menulis data departemen
            $activeWorksheet->setCellValue($startColumnItem . $rowItem, $department['department_name']);
            $spreadsheet->getActiveSheet()->mergeCells($startColumnItem . $rowItem . ':' . $endColumnItem . $rowItem);
            phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowItem, true, 9, 'Calibri');
            $rowItem++;
            // daftar mesin
            foreach ($listProductType[$department['department_id']] as $typeCode => $typeName) {
                if ($dataFilter[$department['department_id']][$typeCode] == null) {
                    continue;
                }
                $columnItem = $startColumnItemData;

                // Menulis data mesin
                $spreadsheet->getActiveSheet()->setCellValue($columnTypeCode . $rowItem, $typeCode);
                $spreadsheet->getActiveSheet()->setCellValue($columnTypeName . $rowItem, $typeName);
                // phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);

                // memasukkan data
                $dataItem = $dataFilter[$department['department_id']][$typeCode];

                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_standard);
                phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_produksi);
                phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_standard > 0 ? $dataItem->berat_produksi / $dataItem->berat_standard : 0);
                phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->infure_cost);
                phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->infure_berat_loss);
                phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_produksi > 0 ? $dataItem->infure_berat_loss / $dataItem->berat_produksi : 0);
                phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->panjang_produksi);
                phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->panjang_printing_inline);
                phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->infure_cost_printing);
                phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->infure_cost + $dataItem->infure_cost_printing);
                phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);
                $columnItem++;

                phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem, false, 8, 'Calibri');
                $rowItem++;
            }
            // perhitungan jumlah berdasarkan departemen
            $spreadsheet->getActiveSheet()->mergeCells($columnTypeCode . $rowItem . ':' . $columnTypeName . $rowItem);
            $activeWorksheet->setCellValue($columnTypeCode . $rowItem, 'Sub Total');
            $columnItem = $startColumnItemData;
            $columnItemEnd = chr(ord($columnItem) + count($header) - 1);
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listProductType[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listProductType[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listProductType[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')/COUNTIF(' . $columnItem . ($rowItem - count($listProductType[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ', "<>0")');
            phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listProductType[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listProductType[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listProductType[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')/COUNTIF(' . $columnItem . ($rowItem - count($listProductType[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ', "<>0")');
            phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listProductType[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listProductType[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listProductType[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listProductType[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            phpspreadsheet::addFullBorder($spreadsheet, $columnTypeCode . $rowItem . ':' . $columnItem . $rowItem);
            phpspreadsheet::styleFont($spreadsheet, $columnTypeCode . $rowItem . ':' . $columnItem . $rowItem, true, 8, 'Calibri');
            $columnItem++;

            $rowItem++;
            $rowItem++;
        }

        // Grand total
        $rowGrandTotal = $rowItem;
        $spreadsheet->getActiveSheet()->mergeCells($columnTypeCode . $rowGrandTotal . ':' . $columnTypeName . $rowGrandTotal);
        $spreadsheet->getActiveSheet()->setCellValue($columnTypeCode . $rowGrandTotal, 'GRAND TOTAL');
        phpspreadsheet::styleFont($spreadsheet, $columnTypeCode . $rowGrandTotal . ':' . $columnHeaderEnd . $rowGrandTotal, true, 8, 'Calibri');
        // $this->addFullBorder($spreadsheet, $columnTypeCode . $rowGrandTotal . ':' . $columnValueAvg . $rowGrandTotal);

        // grand total
        $grandTotal = [
            'berat_standard' => 0,
            'berat_produksi' => 0,
            'infure_cost' => 0,
            'infure_berat_loss' => 0,
            'panjang_produksi' => 0,
            'panjang_printing_inline' => 0,
            'infure_cost_printing' => 0,
        ];

        foreach ($dataFilter as $departmentId => $department) {
            foreach ($department as $productTypeCode => $productType) {
                $grandTotal['berat_standard'] += $productType->berat_standard;
                $grandTotal['berat_produksi'] += $productType->berat_produksi;
                $grandTotal['infure_cost'] += $productType->infure_cost;
                $grandTotal['infure_berat_loss'] += $productType->infure_berat_loss;
                $grandTotal['panjang_produksi'] += $productType->panjang_produksi;
                $grandTotal['panjang_printing_inline'] += $productType->panjang_printing_inline;
                $grandTotal['infure_cost_printing'] += $productType->infure_cost_printing;
            }
        }

        $columnItem = $startColumnItemData;
        $columnItemEnd = chr(ord($columnItem) + count($header) - 1);
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_standard']);
        phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_produksi']);
        phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_standard'] > 0 ? $grandTotal['berat_produksi'] / $grandTotal['berat_standard'] : 0);
        phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['infure_cost']);
        phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['infure_berat_loss']);
        phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_produksi'] > 0 ? $grandTotal['infure_berat_loss'] / $grandTotal['berat_produksi'] : 0);
        phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['panjang_produksi']);
        phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['panjang_printing_inline']);
        phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['infure_cost_printing']);
        phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['infure_cost'] + $grandTotal['infure_cost_printing']);
        phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        phpspreadsheet::addFullBorder($spreadsheet, $columnTypeCode . $rowGrandTotal . ':' . $columnItem . $rowGrandTotal);
        $columnItem++;

        $activeWorksheet->getStyle($columnMesin . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart)->getAlignment()->setWrapText(true);

        // size auto
        $endColumnItem++;
        while ($startColumnItemData !== $endColumnItem) {
            $spreadsheet->getActiveSheet()->getColumnDimension($startColumnItemData)->setAutoSize(true);
            $startColumnItemData++;
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

    public function daftarProduksiPerDepartemenPerTypeSeitai($tglMasuk, $tglKeluar)
    {
        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();
        $activeWorksheet->setShowGridlines(false);
        $activeWorksheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $spreadsheet->getActiveSheet()->freezePane('A4');
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

        // Judul
        $activeWorksheet->setCellValue('B1', 'DAFTAR PRODUKSI PER DEPARTEMEN PER TIPE SEITAI');
        $activeWorksheet->setCellValue('B2', 'Periode : ' . $tglMasuk . ' s/d ' . $tglKeluar);
        // Style Judul
        phpspreadsheet::styleFont($spreadsheet, 'B1:B2', true, 11, 'Calibri');
        // hide column A
        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setVisible(false);

        // Header
        $columnMesin = 'B';
        $columnMesinEnd = 'C';
        $spreadsheet->getActiveSheet()->mergeCells($columnMesin . '3:' . $columnMesinEnd . '3');
        $activeWorksheet->setCellValue('B3', 'Tipe Produk');

        // header
        $rowHeaderStart = 3;
        $columnHeaderStart = 'D';
        $columnHeaderEnd = 'D';
        $header = [
            'Jumlah Produksi (Lembar)',
            'Berat Standar (Kg)',
            'Loss (Kg)',
            'Loss (%)',
            'Seitai Cost',
            'Ponsu Loss (Kg)',
            'Infure Loss (Kg)',
        ];

        foreach ($header as $key => $value) {
            $activeWorksheet->setCellValue($columnHeaderEnd . $rowHeaderStart, $value);
            $columnHeaderEnd++;
        }
        $columnHeaderEnd = chr(ord($columnHeaderEnd) - 1);

        // style header
        phpspreadsheet::addFullBorder($spreadsheet, $columnMesin . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);
        phpspreadsheet::styleFont($spreadsheet, $columnMesin . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart, true, 9, 'Calibri');
        phpspreadsheet::textAlignCenter($spreadsheet, $columnMesin . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);

        $data = DB::select("
            SELECT
                MAX(dep.name) AS department_name,
                MAX(dep.id) AS department_id,
                MAX(prT.code) AS product_type_code,
                MAX(prT.name) AS product_type_name,
                SUM(good.qty_produksi) AS qty_produksi,
                SUM(good.qty_produksi * prd.unit_weight * 0.001) AS berat_produksi,
                SUM(good.qty_produksi * prT.harga_sat_seitai) AS seitai_cost,
                SUM(good.seitai_berat_loss) - COALESCE(SUM(ponsu.berat_loss), 0) AS seitai_berat_loss,
                COALESCE(SUM(ponsu.berat_loss), 0) AS seitai_berat_loss_ponsu,
                SUM(good.infure_berat_loss) AS infure_berat_loss
            FROM tdProduct_Goods AS good
            LEFT JOIN (
                SELECT
                    los_.product_goods_id,
                    SUM(los_.berat_loss) AS berat_loss
                FROM tdProduct_Goods_Loss AS los_
                WHERE los_.loss_seitai_id = 1 -- ponsu
                GROUP BY los_.product_goods_id
            ) ponsu ON good.id = ponsu.product_goods_id
            INNER JOIN msMachine AS mac ON good.machine_id = mac.id
            INNER JOIN msDepartment AS dep ON mac.department_id = dep.id
            INNER JOIN msProduct AS prd ON good.product_id = prd.id
            INNER JOIN msProduct_type AS prT ON prd.product_type_id = prT.id
            WHERE good.production_date BETWEEN '$tglMasuk' AND '$tglKeluar'
            GROUP BY dep.id, prT.id
        ");

        if (count($data) == 0) {
            $response = [
                'status' => 'error',
                'message' => "Data pada periode tanggal tersebut tidak ditemukan"
            ];

            return $response;
        }

        $listDepartment = array_reduce($data, function ($carry, $item) {
            $carry[$item->department_id] = [
                'department_id' => $item->department_id,
                'department_name' => $item->department_name
            ];
            return $carry;
        }, []);

        // list jenis produk
        $listProductType = array_reduce($data, function ($carry, $item) {
            $carry[$item->department_id][$item->product_type_code] = $item->product_type_name;
            return $carry;
        }, []);

        $dataFilter = array_reduce($data, function ($carry, $item) {
            $carry[$item->department_id][$item->product_type_code] = $item;
            return $carry;
        }, []);

        // index
        $startColumnItem = 'B';
        $endColumnItem = $columnHeaderEnd;
        $startColumnItemData = 'D';
        $columnTypeCode = 'B';
        $columnTypeName = 'C';
        $startRowItem = 4;
        $rowItem = $startRowItem;
        // daftar departemen
        foreach ($listDepartment as $department) {
            // Menulis data departemen
            $activeWorksheet->setCellValue($startColumnItem . $rowItem, $department['department_name']);
            $spreadsheet->getActiveSheet()->mergeCells($startColumnItem . $rowItem . ':' . $endColumnItem . $rowItem);
            phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowItem, true, 9, 'Calibri');
            $rowItem++;
            // daftar mesin
            foreach ($listProductType[$department['department_id']] as $TypeCode => $TypeName) {
                if ($dataFilter[$department['department_id']][$TypeCode] == null) {
                    continue;
                }
                $columnItem = $startColumnItemData;

                // Menulis data mesin
                $spreadsheet->getActiveSheet()->setCellValue($columnTypeCode . $rowItem, $TypeCode);
                $spreadsheet->getActiveSheet()->setCellValue($columnTypeName . $rowItem, $TypeName);
                // phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);

                // memasukkan data
                $dataItem = $dataFilter[$department['department_id']][$TypeCode];

                // jumlah produksi
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->qty_produksi);
                phpspreadsheet::numberFormatThousands($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // berat_produksi
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_produksi);
                phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // seitai_berat_loss
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->seitai_berat_loss);
                phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // seitai loss %
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_produksi > 0 ? $dataItem->seitai_berat_loss / $dataItem->berat_produksi : 0);
                phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // seitai_cost
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->seitai_cost);
                phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // seitai_berat_loss_ponsu
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->seitai_berat_loss_ponsu);
                phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // infure_berat_loss
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->infure_berat_loss);
                phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);
                $columnItem++;

                phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem, false, 8, 'Calibri');
                $rowItem++;
            }
            // perhitungan jumlah berdasarkan departemen
            $spreadsheet->getActiveSheet()->mergeCells($columnTypeCode . $rowItem . ':' . $columnTypeName . $rowItem);
            $activeWorksheet->setCellValue($columnTypeCode . $rowItem, 'Sub Total');
            $columnItem = $startColumnItemData;
            $columnItemEnd = chr(ord($columnItem) + count($header) - 1);
            // jumlah produksi
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listProductType[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpspreadsheet::numberFormatThousands($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // berat_produksi
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listProductType[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // seitai_berat_loss
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listProductType[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // seitai loss %
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listProductType[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')/COUNTIF(' . $columnItem . ($rowItem - count($listProductType[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ', "<>0")');
            phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // seitai_cost
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listProductType[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // seitai_berat_loss_ponsu
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listProductType[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // infure_berat_loss
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listProductType[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            phpspreadsheet::addFullBorder($spreadsheet, $columnTypeCode . $rowItem . ':' . $columnItem . $rowItem);
            phpspreadsheet::styleFont($spreadsheet, $columnTypeCode . $rowItem . ':' . $columnItem . $rowItem, true, 8, 'Calibri');
            $columnItem++;


            $rowItem++;
            $rowItem++;
        }

        // Grand total
        $rowGrandTotal = $rowItem;
        $spreadsheet->getActiveSheet()->mergeCells($columnTypeCode . $rowGrandTotal . ':' . $columnTypeName . $rowGrandTotal);
        $spreadsheet->getActiveSheet()->setCellValue($columnTypeCode . $rowGrandTotal, 'GRAND TOTAL');
        phpspreadsheet::styleFont($spreadsheet, $columnTypeCode . $rowGrandTotal . ':' . $columnHeaderEnd . $rowGrandTotal, true, 8, 'Calibri');
        // $this->addFullBorder($spreadsheet, $columnTypeCode . $rowGrandTotal . ':' . $columnValueAvg . $rowGrandTotal);

        // grand total
        $grandTotal = [
            'qty_produksi' => 0,
            'berat_produksi' => 0,
            'seitai_berat_loss' => 0,
            'seitai_cost' => 0,
            'seitai_berat_loss_ponsu' => 0,
            'infure_berat_loss' => 0,
        ];

        foreach ($dataFilter as $departmentId => $department) {
            foreach ($department as $productTypeCode => $productType) {
                $grandTotal['qty_produksi'] += $productType->qty_produksi;
                $grandTotal['berat_produksi'] += $productType->berat_produksi;
                $grandTotal['seitai_berat_loss'] += $productType->seitai_berat_loss;
                $grandTotal['seitai_cost'] += $productType->seitai_cost;
                $grandTotal['seitai_berat_loss_ponsu'] += $productType->seitai_berat_loss_ponsu;
                $grandTotal['infure_berat_loss'] += $productType->infure_berat_loss;
            }
        }

        $columnItem = $startColumnItemData;
        $columnItemEnd = chr(ord($columnItem) + count($header) - 1);
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['qty_produksi']);
        phpspreadsheet::numberFormatThousands($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_produksi']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['seitai_berat_loss']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // seitai loss %
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_produksi'] > 0 ? $grandTotal['seitai_berat_loss'] / $grandTotal['berat_produksi'] : 0);
        phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['seitai_cost']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['seitai_berat_loss_ponsu']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['infure_berat_loss']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        phpspreadsheet::addFullBorder($spreadsheet, $columnTypeCode . $rowGrandTotal . ':' . $columnItem . $rowGrandTotal);
        $columnItem++;

        $activeWorksheet->getStyle($columnMesin . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart)->getAlignment()->setWrapText(true);

        // size auto
        $endColumnItem++;
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        while ($startColumnItemData !== $endColumnItem) {
            $spreadsheet->getActiveSheet()->getColumnDimension($startColumnItemData)->setAutoSize(true);
            $startColumnItemData++;
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

    public function daftarProduksiPerDepartemenPerPetugasInfure($tglMasuk, $tglKeluar)
    {
        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();
        $activeWorksheet->setShowGridlines(false);
        $activeWorksheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $spreadsheet->getActiveSheet()->freezePane('A4');
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

        // Judul
        $activeWorksheet->setCellValue('B1', 'DAFTAR PRODUKSI PER DEPARTEMEN PER PETUGAS INFURE');
        $activeWorksheet->setCellValue('B2', 'Periode : ' . $tglMasuk . ' s/d ' . $tglKeluar);
        // Style Judul
        phpspreadsheet::styleFont($spreadsheet, 'B1:B2', true, 11, 'Calibri');
        // hide column A
        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setVisible(false);

        // Header
        $columnMesin = 'B';
        $columnMesinEnd = 'C';
        $spreadsheet->getActiveSheet()->mergeCells($columnMesin . '3:' . $columnMesinEnd . '3');
        $activeWorksheet->setCellValue('B3', 'Petugas');

        // header
        $rowHeaderStart = 3;
        $columnHeaderStart = 'D';
        $columnHeaderEnd = 'D';
        $header = [
            'Berat Standar (Kg)',
            'Berat Produksi (Kg)',
            'Weight Rate',
            'Infure Cost',
            'Loss (Kg)',
            'Loss (%)',
            'Panjang Infure (meter)',
            'Inline Printing (meter)',
            'Inline Printing Cost',
            'Process Cost',
        ];

        foreach ($header as $key => $value) {
            $activeWorksheet->setCellValue($columnHeaderEnd . $rowHeaderStart, $value);
            $columnHeaderEnd++;
        }
        $columnHeaderEnd = chr(ord($columnHeaderEnd) - 1);

        // style header
        phpspreadsheet::addFullBorder($spreadsheet, $columnMesin . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);
        phpspreadsheet::styleFont($spreadsheet, $columnMesin . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart, true, 9, 'Calibri');
        phpspreadsheet::textAlignCenter($spreadsheet, $columnMesin . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);

        $data = DB::select("
            SELECT
                max(dep.name) AS department_name,
                max(dep.id) AS department_id,
                max(man.employeeNo) AS employeeNo,
                max(man.empName) AS empName,
                SUM(asy.berat_standard) AS berat_standard,
                SUM(asy.berat_produksi) AS berat_produksi,
                SUM(asy.infure_cost) AS infure_cost,
                SUM(asy.infure_berat_loss) AS infure_berat_loss,
                SUM(asy.panjang_produksi) AS panjang_produksi,
                SUM(asy.panjang_printing_inline) AS panjang_printing_inline,
                SUM(asy.infure_cost_printing) AS infure_cost_printing
            FROM tdProduct_Assembly AS asy
            INNER JOIN msEmployee AS man ON asy.employee_id = man.id
            INNER JOIN msDepartment AS dep ON man.department_id = dep.id
            WHERE asy.production_date BETWEEN '$tglMasuk' AND '$tglKeluar'
            GROUP BY dep.id, asy.employee_id
        ");

        if (count($data) == 0) {
            $response = [
                'status' => 'error',
                'message' => "Data pada periode tanggal tersebut tidak ditemukan"
            ];

            return $response;
        }

        $listDepartment = array_reduce($data, function ($carry, $item) {
            $carry[$item->department_id] = [
                'department_id' => $item->department_id,
                'department_name' => $item->department_name
            ];
            return $carry;
        }, []);

        // list petugas
        $listEmployee = array_reduce($data, function ($carry, $item) {
            $carry[$item->department_id][$item->employeeno] = $item->empname;
            return $carry;
        }, []);

        $dataFilter = array_reduce($data, function ($carry, $item) {
            $carry[$item->department_id][$item->employeeno] = $item;
            return $carry;
        }, []);

        // index
        $startColumnItem = 'B';
        $endColumnItem = $columnHeaderEnd;
        $startColumnItemData = 'D';
        $columnemployeeno = 'B';
        $columnEmployeeName = 'C';
        $startRowItem = 4;
        $rowItem = $startRowItem;
        // daftar departemen
        foreach ($listDepartment as $department) {
            // Menulis data departemen
            $activeWorksheet->setCellValue($startColumnItem . $rowItem, $department['department_name']);
            $spreadsheet->getActiveSheet()->mergeCells($startColumnItem . $rowItem . ':' . $endColumnItem . $rowItem);
            phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowItem, true, 9, 'Calibri');
            $rowItem++;
            // daftar mesin
            foreach ($listEmployee[$department['department_id']] as $employeeno => $EmployeeName) {
                if ($dataFilter[$department['department_id']][$employeeno] == null) {
                    continue;
                }
                $columnItem = $startColumnItemData;

                // Menulis data mesin
                $spreadsheet->getActiveSheet()->setCellValue($columnemployeeno . $rowItem, $employeeno);
                $spreadsheet->getActiveSheet()->setCellValue($columnEmployeeName . $rowItem, $EmployeeName);
                // phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);

                // memasukkan data
                $dataItem = $dataFilter[$department['department_id']][$employeeno];

                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_standard);
                phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_produksi);
                phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_standard > 0 ? $dataItem->berat_produksi / $dataItem->berat_standard : 0);
                phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->infure_cost);
                phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->infure_berat_loss);
                phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // loss %
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_produksi > 0 ? $dataItem->infure_berat_loss / ($dataItem->infure_berat_loss + $dataItem->berat_produksi) : 0);
                phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->panjang_produksi);
                phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->panjang_printing_inline);
                phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->infure_cost_printing);
                phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->infure_cost + $dataItem->infure_cost_printing);
                phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);
                $columnItem++;

                phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem, false, 8, 'Calibri');
                $rowItem++;
            }
            // perhitungan jumlah berdasarkan departemen
            $spreadsheet->getActiveSheet()->mergeCells($columnemployeeno . $rowItem . ':' . $columnEmployeeName . $rowItem);
            $activeWorksheet->setCellValue($columnemployeeno . $rowItem, 'Sub Total');
            $columnItem = $startColumnItemData;
            $columnItemEnd = chr(ord($columnItem) + count($header) - 1);
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listEmployee[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listEmployee[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listEmployee[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')/COUNTIF(' . $columnItem . ($rowItem - count($listEmployee[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ', "<>0")');
            phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listEmployee[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listEmployee[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listEmployee[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')/COUNTIF(' . $columnItem . ($rowItem - count($listEmployee[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ', "<>0")');
            phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listEmployee[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listEmployee[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listEmployee[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listEmployee[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            phpspreadsheet::addFullBorder($spreadsheet, $columnemployeeno . $rowItem . ':' . $columnItem . $rowItem);
            phpspreadsheet::styleFont($spreadsheet, $columnemployeeno . $rowItem . ':' . $columnItem . $rowItem, true, 8, 'Calibri');
            $columnItem++;

            $rowItem++;
            $rowItem++;
        }

        // Grand total
        $rowGrandTotal = $rowItem;
        $spreadsheet->getActiveSheet()->mergeCells($columnemployeeno . $rowGrandTotal . ':' . $columnEmployeeName . $rowGrandTotal);
        $spreadsheet->getActiveSheet()->setCellValue($columnemployeeno . $rowGrandTotal, 'GRAND TOTAL');
        phpspreadsheet::styleFont($spreadsheet, $columnemployeeno . $rowGrandTotal . ':' . $columnHeaderEnd . $rowGrandTotal, true, 8, 'Calibri');
        // $this->addFullBorder($spreadsheet, $columnemployeeno . $rowGrandTotal . ':' . $columnValueAvg . $rowGrandTotal);

        // grand total
        $grandTotal = [
            'berat_standard' => 0,
            'berat_produksi' => 0,
            'infure_cost' => 0,
            'infure_berat_loss' => 0,
            'panjang_produksi' => 0,
            'panjang_printing_inline' => 0,
            'infure_cost_printing' => 0,
        ];

        foreach ($dataFilter as $departmentId => $department) {
            foreach ($department as $employeeno => $employee) {
                $grandTotal['berat_standard'] += $employee->berat_standard;
                $grandTotal['berat_produksi'] += $employee->berat_produksi;
                $grandTotal['infure_cost'] += $employee->infure_cost;
                $grandTotal['infure_berat_loss'] += $employee->infure_berat_loss;
                $grandTotal['panjang_produksi'] += $employee->panjang_produksi;
                $grandTotal['panjang_printing_inline'] += $employee->panjang_printing_inline;
                $grandTotal['infure_cost_printing'] += $employee->infure_cost_printing;
            }
        }

        $columnItem = $startColumnItemData;
        $columnItemEnd = chr(ord($columnItem) + count($header) - 1);
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_standard']);
        phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_produksi']);
        phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_standard'] > 0 ? $grandTotal['berat_produksi'] / $grandTotal['berat_standard'] : 0);
        phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['infure_cost']);
        phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['infure_berat_loss']);
        phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_produksi'] > 0 ? $grandTotal['infure_berat_loss'] / $grandTotal['berat_produksi'] : 0);
        phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['panjang_produksi']);
        phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['panjang_printing_inline']);
        phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['infure_cost_printing']);
        phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['infure_cost'] + $grandTotal['infure_cost_printing']);
        phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        phpspreadsheet::addFullBorder($spreadsheet, $columnemployeeno . $rowGrandTotal . ':' . $columnItem . $rowGrandTotal);
        $columnItem++;

        $activeWorksheet->getStyle($columnMesin . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart)->getAlignment()->setWrapText(true);

        // size auto
        $endColumnItem++;
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        while ($startColumnItemData !== $endColumnItem) {
            $spreadsheet->getActiveSheet()->getColumnDimension($startColumnItemData)->setAutoSize(true);
            $startColumnItemData++;
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

    public function daftarProduksiPerDepartemenPerPetugasSeitai($tglMasuk, $tglKeluar)
    {
        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();
        $activeWorksheet->setShowGridlines(false);
        $activeWorksheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $spreadsheet->getActiveSheet()->freezePane('A4');
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

        // Judul
        $activeWorksheet->setCellValue('B1', 'DAFTAR PRODUKSI PER DEPARTEMEN PER PETUGAS SEITAI');
        $activeWorksheet->setCellValue('B2', 'Periode : ' . $tglMasuk . ' s/d ' . $tglKeluar);
        // Style Judul
        phpspreadsheet::styleFont($spreadsheet, 'B1:B2', true, 11, 'Calibri');
        // hide column A
        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setVisible(false);

        // Header
        $columnMesin = 'B';
        $columnMesinEnd = 'C';
        $spreadsheet->getActiveSheet()->mergeCells($columnMesin . '3:' . $columnMesinEnd . '3');
        $activeWorksheet->setCellValue('B3', 'Petugas');

        // header
        $rowHeaderStart = 3;
        $columnHeaderStart = 'D';
        $columnHeaderEnd = 'D';
        $header = [
            'Jumlah Produksi (Lembar)',
            'Berat Standar (Kg)',
            'Loss (Kg)',
            'Loss (%)',
            'Seitai Cost',
            'Ponsu Loss (Kg)',
            'Infure Loss (Kg)',
        ];

        foreach ($header as $key => $value) {
            $activeWorksheet->setCellValue($columnHeaderEnd . $rowHeaderStart, $value);
            $columnHeaderEnd++;
        }
        $columnHeaderEnd = chr(ord($columnHeaderEnd) - 1);

        // style header
        phpspreadsheet::addFullBorder($spreadsheet, $columnMesin . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);
        phpspreadsheet::styleFont($spreadsheet, $columnMesin . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart, true, 9, 'Calibri');
        phpspreadsheet::textAlignCenter($spreadsheet, $columnMesin . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);

        $data = DB::select("
            SELECT
                MAX(dep.name) AS department_name,
                MAX(dep.id) AS department_id,
                MAX(man.employeeNo) AS employeeNo,
                MAX(man.empName) AS empName,
                SUM(good.qty_produksi) AS qty_produksi,
                SUM(good.qty_produksi * prd.unit_weight * 0.001) AS berat_produksi,
                SUM(good.qty_produksi * prT.harga_sat_seitai) AS seitai_cost,
                SUM(good.seitai_berat_loss) - COALESCE(SUM(ponsu.berat_loss), 0) AS seitai_berat_loss,
                COALESCE(SUM(ponsu.berat_loss), 0) AS seitai_berat_loss_ponsu,
                SUM(good.infure_berat_loss) AS infure_berat_loss
            FROM tdProduct_Goods AS good
            LEFT JOIN (
                SELECT
                    los_.product_goods_id,
                    SUM(los_.berat_loss) AS berat_loss
                FROM tdProduct_Goods_Loss AS los_
                WHERE los_.loss_seitai_id = 1 -- ponsu
                GROUP BY los_.product_goods_id
            ) ponsu ON good.id = ponsu.product_goods_id
            INNER JOIN msEmployee AS man ON good.employee_id = man.id
            INNER JOIN msDepartment AS dep ON man.department_id = dep.id
            INNER JOIN msProduct AS prd ON good.product_id = prd.id
            INNER JOIN msProduct_type AS prT ON prd.product_type_id = prT.id
            WHERE good.production_date BETWEEN '$tglMasuk' AND '$tglKeluar'
            GROUP BY dep.id, good.employee_id
        ");

        if (count($data) == 0) {
            $response = [
                'status' => 'error',
                'message' => "Data pada periode tanggal tersebut tidak ditemukan"
            ];

            return $response;
        }

        $listDepartment = array_reduce($data, function ($carry, $item) {
            $carry[$item->department_id] = [
                'department_id' => $item->department_id,
                'department_name' => $item->department_name
            ];
            return $carry;
        }, []);

        // list petugas
        $listEmployee = array_reduce($data, function ($carry, $item) {
            $carry[$item->department_id][$item->employeeno] = $item->empname;
            return $carry;
        }, []);

        $dataFilter = array_reduce($data, function ($carry, $item) {
            $carry[$item->department_id][$item->employeeno] = $item;
            return $carry;
        }, []);

        // index
        $startColumnItem = 'B';
        $endColumnItem = $columnHeaderEnd;
        $startColumnItemData = 'D';
        $columnEmployeeNo = 'B';
        $columnEmployeeName = 'C';
        $startRowItem = 4;
        $rowItem = $startRowItem;
        // daftar departemen
        foreach ($listDepartment as $department) {
            // Menulis data departemen
            $activeWorksheet->setCellValue($startColumnItem . $rowItem, $department['department_name']);
            $spreadsheet->getActiveSheet()->mergeCells($startColumnItem . $rowItem . ':' . $endColumnItem . $rowItem);
            phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowItem, true, 9, 'Calibri');
            $rowItem++;
            // daftar mesin
            foreach ($listEmployee[$department['department_id']] as $employeeNo => $employeeName) {
                if ($dataFilter[$department['department_id']][$employeeNo] == null) {
                    continue;
                }
                $columnItem = $startColumnItemData;

                // Menulis data mesin
                $spreadsheet->getActiveSheet()->setCellValue($columnEmployeeNo . $rowItem, $employeeNo);
                $spreadsheet->getActiveSheet()->setCellValue($columnEmployeeName . $rowItem, $employeeName);
                // phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);

                // memasukkan data
                $dataItem = $dataFilter[$department['department_id']][$employeeNo];

                // jumlah produksi
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->qty_produksi);
                phpspreadsheet::numberFormatThousands($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // berat_produksi
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_produksi);
                phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // seitai_berat_loss
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->seitai_berat_loss);
                phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // seitai loss %
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_produksi > 0 ? $dataItem->seitai_berat_loss / ($dataItem->seitai_berat_loss + $dataItem->berat_produksi) : 0);
                phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // seitai_cost
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->seitai_cost);
                phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // seitai_berat_loss_ponsu
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->seitai_berat_loss_ponsu);
                phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // infure_berat_loss
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->infure_berat_loss);
                phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);
                $columnItem++;

                phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem, false, 8, 'Calibri');
                $rowItem++;
            }
            // perhitungan jumlah berdasarkan departemen
            $spreadsheet->getActiveSheet()->mergeCells($columnEmployeeNo . $rowItem . ':' . $columnEmployeeName . $rowItem);
            $activeWorksheet->setCellValue($columnEmployeeNo . $rowItem, 'Sub Total');
            $columnItem = $startColumnItemData;
            $columnItemEnd = chr(ord($columnItem) + count($header) - 1);
            // jumlah produksi
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listEmployee[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpspreadsheet::numberFormatThousands($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // berat_produksi
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listEmployee[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // seitai_berat_loss
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listEmployee[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // seitai loss %
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listEmployee[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')/COUNTIF(' . $columnItem . ($rowItem - count($listEmployee[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ', "<>0")');
            phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // seitai_cost
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listEmployee[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // seitai_berat_loss_ponsu
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listEmployee[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // infure_berat_loss
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listEmployee[$department['department_id']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            phpspreadsheet::addFullBorder($spreadsheet, $columnEmployeeNo . $rowItem . ':' . $columnItem . $rowItem);
            phpspreadsheet::styleFont($spreadsheet, $columnEmployeeNo . $rowItem . ':' . $columnItem . $rowItem, true, 8, 'Calibri');
            $columnItem++;


            $rowItem++;
            $rowItem++;
        }

        // Grand total
        $rowGrandTotal = $rowItem;
        $spreadsheet->getActiveSheet()->mergeCells($columnEmployeeNo . $rowGrandTotal . ':' . $columnEmployeeName . $rowGrandTotal);
        $spreadsheet->getActiveSheet()->setCellValue($columnEmployeeNo . $rowGrandTotal, 'GRAND TOTAL');
        phpspreadsheet::styleFont($spreadsheet, $columnEmployeeNo . $rowGrandTotal . ':' . $columnHeaderEnd . $rowGrandTotal, true, 8, 'Calibri');
        // $this->addFullBorder($spreadsheet, $columnEmployeeNo . $rowGrandTotal . ':' . $columnValueAvg . $rowGrandTotal);

        // grand total
        $grandTotal = [
            'qty_produksi' => 0,
            'berat_produksi' => 0,
            'seitai_berat_loss' => 0,
            'seitai_cost' => 0,
            'seitai_berat_loss_ponsu' => 0,
            'infure_berat_loss' => 0,
        ];

        foreach ($dataFilter as $departmentId => $department) {
            foreach ($department as $employeeno => $employee) {
                $grandTotal['qty_produksi'] += $employee->qty_produksi;
                $grandTotal['berat_produksi'] += $employee->berat_produksi;
                $grandTotal['seitai_cost'] += $employee->seitai_cost;
                $grandTotal['seitai_berat_loss'] += $employee->seitai_berat_loss;
                $grandTotal['seitai_berat_loss_ponsu'] += $employee->seitai_berat_loss_ponsu;
                $grandTotal['infure_berat_loss'] += $employee->infure_berat_loss;
            }
        }

        $columnItem = $startColumnItemData;
        $columnItemEnd = chr(ord($columnItem) + count($header) - 1);
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['qty_produksi']);
        phpspreadsheet::numberFormatThousands($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_produksi']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['seitai_berat_loss']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // seitai loss %
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_produksi'] > 0 ? $grandTotal['seitai_berat_loss'] / $grandTotal['berat_produksi'] : 0);
        phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['seitai_cost']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['seitai_berat_loss_ponsu']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['infure_berat_loss']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        phpspreadsheet::addFullBorder($spreadsheet, $columnEmployeeNo . $rowGrandTotal . ':' . $columnItem . $rowGrandTotal);
        $columnItem++;

        $activeWorksheet->getStyle($columnMesin . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart)->getAlignment()->setWrapText(true);

        // size auto
        $endColumnItem++;
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        while ($startColumnItemData !== $endColumnItem) {
            $spreadsheet->getActiveSheet()->getColumnDimension($startColumnItemData)->setAutoSize(true);
            $startColumnItemData++;
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

    public function daftarLossPerDepartemenInfure($tglMasuk, $tglKeluar)
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
        $activeWorksheet->setCellValue('B1', 'DAFTAR LOSS PER DEPARTEMEN INFURE');
        $activeWorksheet->setCellValue('B2', 'Periode : ' . $tglMasuk . ' s/d ' . $tglKeluar);
        // Style Judul
        phpspreadsheet::styleFont($spreadsheet, 'B1:B2', true, 11, 'Calibri');
        // hide column A
        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setVisible(false);

        // Header
        $columnMesin = 'B';
        $columnMesinEnd = 'C';
        $spreadsheet->getActiveSheet()->mergeCells($columnMesin . '3:' . $columnMesinEnd . '3');
        $activeWorksheet->setCellValue('B3', 'Klasifikasi');

        // header
        $rowHeaderStart = 3;
        $columnHeaderStart = 'D';
        $columnHeaderEnd = 'D';
        $header = [
            'Kode Loss',
            'Nama Loss',
            'Loss Produksi (Kg)',
            'Loss Kebutuhan (Kg)',
            'Frekuensi'
        ];

        foreach ($header as $key => $value) {
            $activeWorksheet->setCellValue($columnHeaderEnd . $rowHeaderStart, $value);
            $columnHeaderEnd++;
        }
        $columnHeaderEnd = chr(ord($columnHeaderEnd) - 1);

        // style header
        phpspreadsheet::addFullBorder($spreadsheet, $columnMesin . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);
        phpspreadsheet::styleFont($spreadsheet, $columnMesin . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart, true, 9, 'Calibri');
        phpspreadsheet::textAlignCenter($spreadsheet, $columnMesin . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);

        $data = DB::select("
            SELECT
                max(dep.id) AS department_id,
                max(dep.name) AS department_name,
                max(mslosCls.name) AS loss_class_name,
                max(mslos.code) AS loss_code,
                max(mslos.name) AS loss_name,
                SUM(CASE WHEN mslos.loss_category_code <> '1' THEN det.berat_loss ELSE 0 END) AS berat_loss_produksi,
                SUM(CASE WHEN mslos.loss_category_code = '1' THEN det.berat_loss ELSE 0 END) AS berat_loss_kebutuhan,
                SUM(det.frekuensi) AS frekuensi
            FROM tdProduct_Assembly AS asy
            INNER JOIN tdProduct_Assembly_Loss AS det ON asy.id = det.product_assembly_id
            INNER JOIN msLossInfure AS mslos ON det.loss_infure_id = mslos.id
            INNER JOIN msLossClass AS mslosCls ON mslos.loss_class_id = mslosCls.id
            INNER JOIN msMachine AS mac ON asy.machine_id = mac.id
            INNER JOIN msDepartment AS dep ON mac.department_id = dep.id
            WHERE asy.production_date BETWEEN '$tglMasuk' AND '$tglKeluar'
            GROUP BY dep.id, det.loss_infure_id
        ");

        if (count($data) == 0) {
            $response = [
                'status' => 'error',
                'message' => "Data pada periode tanggal tersebut tidak ditemukan"
            ];

            return $response;
        }

        $listDepartment = array_reduce($data, function ($carry, $item) {
            $carry[$item->department_id] = [
                'department_id' => $item->department_id,
                'department_name' => $item->department_name
            ];
            return $carry;
        }, []);

        // list klasifikasi
        $listLossClass = array_reduce($data, function ($carry, $item) {
            $carry[$item->department_id][$item->loss_class_name] = $item->loss_class_name;
            return $carry;
        }, []);

        $dataFilter = array_reduce($data, function ($carry, $item) {
            // Periksa apakah department_id sudah ada
            if (!isset($carry[$item->department_id])) {
                $carry[$item->department_id] = [];
            }

            // Periksa apakah loss_class_name sudah ada di department_id tersebut
            if (!isset($carry[$item->department_id][$item->loss_class_name])) {
                $carry[$item->department_id][$item->loss_class_name] = [
                    'loss_class_name' => $item->loss_class_name,
                    'losses' => []  // Buat array untuk menampung beberapa loss_name
                ];
            }

            // Tambahkan loss_name ke dalam array 'losses'
            $carry[$item->department_id][$item->loss_class_name]['losses'][] = [
                'loss_code' => $item->loss_code,
                'loss_name' => $item->loss_name,
                'berat_loss_produksi' => $item->berat_loss_produksi,
                'berat_loss_kebutuhan' => $item->berat_loss_kebutuhan,
                'frekuensi' => $item->frekuensi
            ];

            return $carry;
        }, []);


        // index
        $startColumnItem = 'B';
        $endColumnItem = $columnHeaderEnd;
        $startColumnItemData = 'D';
        $columnLossClass = 'B';
        $columnLossClassName = 'C';
        $startRowItem = 5;
        $rowItem = $startRowItem;
        // daftar departemen
        foreach ($listDepartment as $department) {
            // Menulis data departemen
            $activeWorksheet->setCellValue($startColumnItem . $rowItem, $department['department_name']);
            $spreadsheet->getActiveSheet()->mergeCells($startColumnItem . $rowItem . ':' . $endColumnItem . $rowItem);
            phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowItem, true, 9, 'Calibri');
            $rowItem++;
            $startRowItemSum = $rowItem;
            // daftar mesin
            foreach ($listLossClass[$department['department_id']] as $lossClass) {
                if ($dataFilter[$department['department_id']][$lossClass] == null) {
                    continue;
                }
                // Menulis data mesin
                $spreadsheet->getActiveSheet()->mergeCells($columnLossClass . $rowItem . ':' . $columnLossClassName . $rowItem);
                $spreadsheet->getActiveSheet()->setCellValue($columnLossClass . $rowItem, $lossClass);
                // phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);

                // memasukkan data
                $dataItem = $dataFilter[$department['department_id']][$lossClass];
                // $dataItem = $dataFilter[$department['department_id']][$lossClass] ?? [
                //     'loss_class_name' => $lossClass,
                //     'losses' => [
                //         [
                //             'loss_code' => '',
                //             'loss_name' => '',
                //             'berat_loss_produksi' => 0,
                //             'berat_loss_kebutuhan' => 0,
                //             'frekuensi' => 0
                //         ]
                //     ]
                // ];

                foreach ($dataItem['losses'] as $item) {
                    $spreadsheet->getActiveSheet()->mergeCells($columnLossClass . $rowItem . ':' . $columnLossClassName . $rowItem);
                    $columnItem = $startColumnItemData;
                    // kode loss
                    $activeWorksheet->setCellValue($columnItem . $rowItem, $item['loss_code'] ?? '');
                    phpspreadsheet::textAlignCenter($spreadsheet, $columnItem . $rowItem);
                    $columnItem++;
                    // nama loss
                    $activeWorksheet->setCellValue($columnItem . $rowItem, $item['loss_name'] ?? '');
                    $columnItem++;
                    // loss produksi
                    $activeWorksheet->setCellValue($columnItem . $rowItem, $item['berat_loss_produksi']);
                    if ($item['berat_loss_produksi'] == 0) {
                        $activeWorksheet->getStyle($columnItem . $rowItem)->getNumberFormat()->setFormatCode('0;-0;"-"');
                    } else {
                        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                    }
                    $columnItem++;
                    // loss kebutuhan
                    $activeWorksheet->setCellValue($columnItem . $rowItem, $item['berat_loss_kebutuhan']);
                    if ($item['berat_loss_kebutuhan'] == 0) {
                        $activeWorksheet->getStyle($columnItem . $rowItem)->getNumberFormat()->setFormatCode('0;-0;"-"');
                    } else {
                        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                    }
                    $columnItem++;
                    // frekuensi
                    $activeWorksheet->setCellValue($columnItem . $rowItem, $item['frekuensi']);
                    phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
                    // Terapkan custom format untuk mengganti tampilan 0 dengan -
                    phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);
                    $columnItem++;

                    phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem, false, 8, 'Calibri');
                    $rowItem++;
                }
            }
            // perhitungan jumlah berdasarkan departemen
            $spreadsheet->getActiveSheet()->mergeCells($columnLossClass . $rowItem . ':' . 'E' . $rowItem);
            $activeWorksheet->setCellValue($columnLossClass . $rowItem, 'Total');
            $columnItem = $startColumnItemData;
            $columnItem++;
            // loss produksi
            $columnItem++;
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . $startRowItemSum . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // loss kebutuhan
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . $startRowItemSum . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // frekuensi
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . $startRowItemSum . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatThousands($spreadsheet, $columnItem . $rowItem);
            phpspreadsheet::addFullBorder($spreadsheet, $columnLossClass . $rowItem . ':' . $columnItem . $rowItem);
            phpspreadsheet::styleFont($spreadsheet, $columnLossClass . $rowItem . ':' . $columnItem . $rowItem, true, 8, 'Calibri');
            $columnItem++;

            $rowItem++;
            $rowItem++;
        }

        // Grand total
        $rowGrandTotal = $rowItem;
        $spreadsheet->getActiveSheet()->mergeCells($columnLossClass . $rowGrandTotal . ':' . 'E' . $rowGrandTotal);
        $spreadsheet->getActiveSheet()->setCellValue($columnLossClass . $rowGrandTotal, 'GRAND TOTAL');
        phpspreadsheet::styleFont($spreadsheet, $columnLossClass . $rowGrandTotal . ':' . $columnHeaderEnd . $rowGrandTotal, true, 8, 'Calibri');
        // $this->addFullBorder($spreadsheet, $columnLossClass . $rowGrandTotal . ':' . $columnValueAvg . $rowGrandTotal);

        $grandTotal = [
            'berat_loss_produksi' => 0,
            'berat_loss_kebutuhan' => 0,
            'frekuensi' => 0
        ];

        foreach ($dataFilter as $departmentId => $lossClasses) {
            foreach ($listLossClass[$departmentId] as $lossClass => $lossClassName) {
                if (isset($lossClasses[$lossClass])) {
                    $dataItem = $lossClasses[$lossClass];
                    foreach ($dataItem['losses'] as $item) {
                        $grandTotal['berat_loss_produksi'] += $item['berat_loss_produksi'];
                        $grandTotal['berat_loss_kebutuhan'] += $item['berat_loss_kebutuhan'];
                        $grandTotal['frekuensi'] += $item['frekuensi'];
                    }
                } else {
                    // Tambahkan default value jika $lossClass tidak ditemukan
                    $grandTotal['berat_loss_produksi'] += 0;
                    $grandTotal['berat_loss_kebutuhan'] += 0;
                    $grandTotal['frekuensi'] += 0;
                }
            }
        }

        $columnItem = $startColumnItemData;
        $columnItem++;
        $columnItem++;
        // berat loss produksi
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_loss_produksi']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // berat loss kebutuhan
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_loss_kebutuhan']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // frekuensi
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['frekuensi']);
        phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowGrandTotal);
        phpspreadsheet::addFullBorder($spreadsheet, $columnLossClass . $rowGrandTotal . ':' . $columnItem . $rowGrandTotal);
        phpspreadsheet::addFullBorder($spreadsheet, $columnLossClass . $rowGrandTotal . ':' . $columnItem . $rowGrandTotal);
        $columnItem++;

        $activeWorksheet->getStyle($columnMesin . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart)->getAlignment()->setWrapText(true);

        // size auto
        $endColumnItem++;
        while ($startColumnItemData !== $endColumnItem) {
            $spreadsheet->getActiveSheet()->getColumnDimension($startColumnItemData)->setAutoSize(true);
            $startColumnItemData++;
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

    public function daftarLossPerDepartemenSeitai($tglMasuk, $tglKeluar)
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
        $activeWorksheet->setCellValue('B1', 'DAFTAR LOSS PER DEPARTEMEN SEITAI');
        $activeWorksheet->setCellValue('B2', 'Periode : ' . $tglMasuk . ' s/d ' . $tglKeluar);
        // Style Judul
        phpspreadsheet::styleFont($spreadsheet, 'B1:B2', true, 11, 'Calibri');
        // hide column A
        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setVisible(false);

        // Header
        $columnMesin = 'B';
        $columnMesinEnd = 'C';
        $spreadsheet->getActiveSheet()->mergeCells($columnMesin . '3:' . $columnMesinEnd . '3');
        $activeWorksheet->setCellValue('B3', 'Klasifikasi');

        // header
        $rowHeaderStart = 3;
        $columnHeaderStart = 'D';
        $columnHeaderEnd = 'D';
        $header = [
            'Kode Loss',
            'Nama Loss',
            'Loss Produksi (Kg)',
            'Loss Kebutuhan (Kg)',
            'Frekuensi'
        ];

        foreach ($header as $key => $value) {
            $activeWorksheet->setCellValue($columnHeaderEnd . $rowHeaderStart, $value);
            $columnHeaderEnd++;
        }
        $columnHeaderEnd = chr(ord($columnHeaderEnd) - 1);

        // style header
        phpspreadsheet::addFullBorder($spreadsheet, $columnMesin . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);
        phpspreadsheet::styleFont($spreadsheet, $columnMesin . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart, true, 9, 'Calibri');
        phpspreadsheet::textAlignCenter($spreadsheet, $columnMesin . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);

        $data = DB::select("
            SELECT
                max(dep.name) AS department_name,
                max(dep.id) AS department_id,
                max(mslosCls.name) AS loss_class_name,
                max(mslos.code) AS loss_code,
                max(mslos.name) AS loss_name,
                SUM(CASE WHEN mslos.loss_category_code <> '1' THEN det.berat_loss ELSE 0 END) AS berat_loss_produksi,
                SUM(CASE WHEN mslos.loss_category_code = '1' THEN det.berat_loss ELSE 0 END) AS berat_loss_kebutuhan,
                SUM(det.frekuensi) AS frekuensi
            FROM tdProduct_Goods AS good
            INNER JOIN tdProduct_Goods_Loss AS det ON good.id = det.product_goods_id
            INNER JOIN msLossSeitai AS mslos ON det.loss_seitai_id = mslos.id
            INNER JOIN msLossClass AS mslosCls ON mslos.loss_class_id = mslosCls.id
            INNER JOIN msMachine AS mac ON good.machine_id = mac.id
            INNER JOIN msDepartment AS dep ON mac.department_id = dep.id
            WHERE good.production_date BETWEEN '$tglMasuk' AND '$tglKeluar'
            GROUP BY dep.id, det.loss_seitai_id
            ORDER BY loss_code ASC
        ");

        if (count($data) == 0) {
            $response = [
                'status' => 'error',
                'message' => "Data pada periode tanggal tersebut tidak ditemukan"
            ];

            return $response;
        }

        $listDepartment = array_reduce($data, function ($carry, $item) {
            $carry[$item->department_id] = [
                'department_id' => $item->department_id,
                'department_name' => $item->department_name
            ];
            return $carry;
        }, []);

        // list klasifikasi
        $listLossClass = array_reduce($data, function ($carry, $item) {
            $carry[$item->department_id][$item->loss_class_name] = $item->loss_class_name;
            return $carry;
        }, []);

        $dataFilter = array_reduce($data, function ($carry, $item) {
            // Periksa apakah department_id sudah ada
            if (!isset($carry[$item->department_id])) {
                $carry[$item->department_id] = [];
            }

            // Periksa apakah loss_class_name sudah ada di department_id tersebut
            if (!isset($carry[$item->department_id][$item->loss_class_name])) {
                $carry[$item->department_id][$item->loss_class_name] = [
                    'loss_class_name' => $item->loss_class_name,
                    'losses' => []  // Buat array untuk menampung beberapa loss_name
                ];
            }

            // Tambahkan loss_name ke dalam array 'losses'
            $carry[$item->department_id][$item->loss_class_name]['losses'][] = [
                'loss_code' => $item->loss_code,
                'loss_name' => $item->loss_name,
                'berat_loss_produksi' => $item->berat_loss_produksi,
                'berat_loss_kebutuhan' => $item->berat_loss_kebutuhan,
                'frekuensi' => $item->frekuensi
            ];

            return $carry;
        }, []);


        // index
        $startColumnItem = 'B';
        $endColumnItem = $columnHeaderEnd;
        $startColumnItemData = 'D';
        $columnLossClass = 'B';
        $columnLossClassName = 'C';
        $startRowItem = 5;
        $rowItem = $startRowItem;
        // daftar departemen
        foreach ($listDepartment as $department) {
            // Menulis data departemen
            $activeWorksheet->setCellValue($startColumnItem . $rowItem, $department['department_name']);
            $spreadsheet->getActiveSheet()->mergeCells($startColumnItem . $rowItem . ':' . $endColumnItem . $rowItem);
            phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowItem, true, 9, 'Calibri');
            $rowItem++;
            $startRowItemSum = $rowItem;
            // daftar mesin
            foreach ($listLossClass[$department['department_id']] as $lossClass) {
                if ($dataFilter[$department['department_id']][$lossClass] == null) {
                    continue;
                }
                // Menulis data mesin
                $spreadsheet->getActiveSheet()->mergeCells($columnLossClass . $rowItem . ':' . $columnLossClassName . $rowItem);
                $spreadsheet->getActiveSheet()->setCellValue($columnLossClass . $rowItem, $lossClass);
                // phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);

                // memasukkan data
                $dataItem = $dataFilter[$department['department_id']][$lossClass];
                // $dataItem = $dataFilter[$department['department_id']][$lossClass] ?? [
                //     'loss_class_name' => $lossClass,
                //     'losses' => [
                //         [
                //             'loss_code' => '',
                //             'loss_name' => '',
                //             'berat_loss_produksi' => 0,
                //             'berat_loss_kebutuhan' => 0,
                //             'frekuensi' => 0
                //         ]
                //     ]
                // ];

                foreach ($dataItem['losses'] as $item) {
                    $spreadsheet->getActiveSheet()->mergeCells($columnLossClass . $rowItem . ':' . $columnLossClassName . $rowItem);
                    $columnItem = $startColumnItemData;
                    // kode loss
                    $activeWorksheet->setCellValue($columnItem . $rowItem, $item['loss_code'] ?? '');
                    phpspreadsheet::textAlignCenter($spreadsheet, $columnItem . $rowItem);
                    $columnItem++;
                    // nama loss
                    $activeWorksheet->setCellValue($columnItem . $rowItem, $item['loss_name'] ?? '');
                    $columnItem++;
                    // loss produksi
                    $activeWorksheet->setCellValue($columnItem . $rowItem, $item['berat_loss_produksi']);
                    if ($item['berat_loss_produksi'] == 0) {
                        $activeWorksheet->getStyle($columnItem . $rowItem)->getNumberFormat()->setFormatCode('0;-0;"-"');
                    } else {
                        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                    }
                    $columnItem++;
                    // loss kebutuhan
                    $activeWorksheet->setCellValue($columnItem . $rowItem, $item['berat_loss_kebutuhan']);
                    if ($item['berat_loss_kebutuhan'] == 0) {
                        $activeWorksheet->getStyle($columnItem . $rowItem)->getNumberFormat()->setFormatCode('0;-0;"-"');
                    } else {
                        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                    }
                    $columnItem++;
                    // frekuensi
                    $activeWorksheet->setCellValue($columnItem . $rowItem, $item['frekuensi']);
                    phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
                    // Terapkan custom format untuk mengganti tampilan 0 dengan -
                    phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);
                    $columnItem++;

                    phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem, false, 8, 'Calibri');
                    $rowItem++;
                }
            }
            // perhitungan jumlah berdasarkan departemen
            $spreadsheet->getActiveSheet()->mergeCells($columnLossClass . $rowItem . ':' . 'E' . $rowItem);
            $activeWorksheet->setCellValue($columnLossClass . $rowItem, 'Total');
            $columnItem = $startColumnItemData;
            $columnItem++;
            // loss produksi
            $columnItem++;
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . $startRowItemSum . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // loss kebutuhan
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . $startRowItemSum . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // frekuensi
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . $startRowItemSum . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatThousands($spreadsheet, $columnItem . $rowItem);
            phpspreadsheet::addFullBorder($spreadsheet, $columnLossClass . $rowItem . ':' . $columnItem . $rowItem);
            phpspreadsheet::styleFont($spreadsheet, $columnLossClass . $rowItem . ':' . $columnItem . $rowItem, true, 8, 'Calibri');
            $columnItem++;

            $rowItem++;
            $rowItem++;
        }

        // Grand total
        $rowGrandTotal = $rowItem;
        $spreadsheet->getActiveSheet()->mergeCells($columnLossClass . $rowGrandTotal . ':' . 'E' . $rowGrandTotal);
        $spreadsheet->getActiveSheet()->setCellValue($columnLossClass . $rowGrandTotal, 'GRAND TOTAL');
        phpspreadsheet::styleFont($spreadsheet, $columnLossClass . $rowGrandTotal . ':' . $columnHeaderEnd . $rowGrandTotal, true, 8, 'Calibri');
        // $this->addFullBorder($spreadsheet, $columnLossClass . $rowGrandTotal . ':' . $columnValueAvg . $rowGrandTotal);

        $grandTotal = [
            'berat_loss_produksi' => 0,
            'berat_loss_kebutuhan' => 0,
            'frekuensi' => 0
        ];

        foreach ($dataFilter as $departmentId => $lossClasses) {
            foreach ($listLossClass[$departmentId] as $lossClass => $lossClassName) {
                if (isset($lossClasses[$lossClass])) {
                    $dataItem = $lossClasses[$lossClass];
                    foreach ($dataItem['losses'] as $item) {
                        $grandTotal['berat_loss_produksi'] += $item['berat_loss_produksi'];
                        $grandTotal['berat_loss_kebutuhan'] += $item['berat_loss_kebutuhan'];
                        $grandTotal['frekuensi'] += $item['frekuensi'];
                    }
                } else {
                    // Tambahkan default value jika $lossClass tidak ditemukan
                    $grandTotal['berat_loss_produksi'] += 0;
                    $grandTotal['berat_loss_kebutuhan'] += 0;
                    $grandTotal['frekuensi'] += 0;
                }
            }
        }

        $columnItem = $startColumnItemData;
        $columnItem++;
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_loss_produksi']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_loss_kebutuhan']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // frekuensi
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['frekuensi']);
        phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowGrandTotal);
        phpspreadsheet::addFullBorder($spreadsheet, $columnLossClass . $rowGrandTotal . ':' . $columnItem . $rowGrandTotal);
        phpspreadsheet::addFullBorder($spreadsheet, $columnLossClass . $rowGrandTotal . ':' . $columnItem . $rowGrandTotal);
        $columnItem++;

        $activeWorksheet->getStyle($columnMesin . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart)->getAlignment()->setWrapText(true);

        // size auto
        $endColumnItem++;
        while ($startColumnItemData !== $endColumnItem) {
            $spreadsheet->getActiveSheet()->getColumnDimension($startColumnItemData)->setAutoSize(true);
            $startColumnItemData++;
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

    public function daftarLossPerDepartemenPerJenisInfure($tglMasuk, $tglKeluar)
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
        $activeWorksheet->setCellValue('B1', 'DAFTAR LOSS PER DEPARTEMEN PER JENIS INFURE');
        $activeWorksheet->setCellValue('B2', 'Periode : ' . $tglMasuk . ' s/d ' . $tglKeluar);
        // Style Judul
        phpspreadsheet::styleFont($spreadsheet, 'B1:B2', true, 11, 'Calibri');
        // hide column A
        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setVisible(false);

        // Header
        $columnFirstHeader = 'B';
        $columnFirstHeaderEnd = 'C';
        $spreadsheet->getActiveSheet()->mergeCells($columnFirstHeader . '3:' . $columnFirstHeaderEnd . '3');
        $activeWorksheet->setCellValue('B3', 'Jenis Produk');

        // header
        $rowHeaderStart = 3;
        $columnHeaderStart = 'D';
        $columnHeaderEnd = 'D';
        $header = [
            'Klasifikasi',
            'Kode Loss',
            'Nama Loss',
            'Loss Produksi (Kg)',
            'Loss Kebutuhan (Kg)',
            'Frekuensi'
        ];

        foreach ($header as $key => $value) {
            $activeWorksheet->setCellValue($columnHeaderEnd . $rowHeaderStart, $value);
            $columnHeaderEnd++;
        }
        $columnHeaderEnd = chr(ord($columnHeaderEnd) - 1);

        // style header
        phpspreadsheet::addFullBorder($spreadsheet, $columnFirstHeader . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);
        phpspreadsheet::styleFont($spreadsheet, $columnFirstHeader . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart, true, 9, 'Calibri');
        phpspreadsheet::textAlignCenter($spreadsheet, $columnFirstHeader . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);

        $data = DB::select("
            SELECT
                max(dep.name) AS department_name,
                max(dep.id) AS department_id,
                max(prGrp.code || ' : ' || prGrp.name) AS product_group_name,
                max(mslosCls.name) AS loss_class_name,
                max(mslos.code) AS loss_code,
                max(mslos.name) AS loss_name,
                SUM(CASE WHEN mslos.loss_category_code <> '1' THEN det.berat_loss ELSE 0 END) AS berat_loss_produksi,
                SUM(CASE WHEN mslos.loss_category_code = '1' THEN det.berat_loss ELSE 0 END) AS berat_loss_kebutuhan,
                SUM(det.frekuensi) AS frekuensi
            FROM tdProduct_Assembly AS asy
            INNER JOIN tdProduct_Assembly_Loss AS det ON asy.id = det.product_assembly_id
            INNER JOIN msLossInfure AS mslos ON det.loss_infure_id = mslos.id
            INNER JOIN msLossClass AS mslosCls ON mslos.loss_class_id = mslosCls.id
            INNER JOIN msMachine AS mac ON asy.machine_id = mac.id
            INNER JOIN msDepartment AS dep ON mac.department_id = dep.id
            INNER JOIN msProduct AS prd ON asy.product_id = prd.id
            INNER JOIN msProduct_type AS prTip ON prd.product_type_id = prTip.id
            INNER JOIN msProduct_group AS prGrp ON prTip.product_group_id = prGrp.id
            WHERE asy.production_date BETWEEN '$tglMasuk' AND '$tglKeluar'
            GROUP BY dep.id, prGrp.id, det.loss_infure_id
        ");

        if (count($data) == 0) {
            $response = [
                'status' => 'error',
                'message' => "Data pada periode tanggal tersebut tidak ditemukan"
            ];

            return $response;
        }

        $listDepartment = array_reduce($data, function ($carry, $item) {
            $carry[$item->department_id] = [
                'department_id' => $item->department_id,
                'department_name' => $item->department_name
            ];
            return $carry;
        }, []);

        // list jenis produk
        $listProductGroup = array_reduce($data, function ($carry, $item) {
            $carry[$item->department_id][$item->product_group_name] = $item->product_group_name;
            return $carry;
        }, []);

        // list klasifikasi
        $listLossClass = array_reduce($data, function ($carry, $item) {
            $carry[$item->department_id][$item->product_group_name][$item->loss_class_name] = $item->loss_class_name;
            return $carry;
        }, []);

        $dataFilter = array_reduce($data, function ($carry, $item) {
            // Periksa apakah department_id sudah ada
            if (!isset($carry[$item->department_id])) {
                $carry[$item->department_id] = [];
            }

            // Periksa apakah product_group_name sudah ada di department_id tersebut
            if (!isset($carry[$item->department_id][$item->product_group_name])) {
                $carry[$item->department_id][$item->product_group_name] = [];
            }

            // Periksa apakah loss_class_name sudah ada di product_group_name tersebut
            if (!isset($carry[$item->department_id][$item->product_group_name][$item->loss_class_name])) {
                $carry[$item->department_id][$item->product_group_name][$item->loss_class_name] = [
                    'loss_class_name' => $item->loss_class_name,
                    'losses' => []  // Buat array untuk menampung beberapa loss_name
                ];
            }

            // Tambahkan loss_name ke dalam array 'losses'
            $carry[$item->department_id][$item->product_group_name][$item->loss_class_name]['losses'][] = [
                'loss_code' => $item->loss_code,
                'loss_name' => $item->loss_name,
                'berat_loss_produksi' => $item->berat_loss_produksi,
                'berat_loss_kebutuhan' => $item->berat_loss_kebutuhan,
                'frekuensi' => $item->frekuensi
            ];

            return $carry;
        }, []);

        // index
        $startColumnItem = 'B';
        $endColumnItem = $columnHeaderEnd;
        $startColumnItemData = 'E';
        $columnProductGroup = 'B';
        $columnProductGroupEnd = 'C';
        $columnLossClass = 'D';
        $startRowItem = 5;
        $rowItem = $startRowItem;
        // daftar departemen
        foreach ($listDepartment as $department) {
            // Menulis data departemen
            $activeWorksheet->setCellValue($startColumnItem . $rowItem, $department['department_name']);
            $spreadsheet->getActiveSheet()->mergeCells($startColumnItem . $rowItem . ':' . $endColumnItem . $rowItem);
            phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowItem, true, 9, 'Calibri');
            $rowItem++;
            $startRowItemSum = $rowItem;
            foreach ($listProductGroup[$department['department_id']] as $productGroup) {
                // Menulis data tipe produk
                $activeWorksheet->setCellValue($columnProductGroup . $rowItem, $productGroup);
                $spreadsheet->getActiveSheet()->mergeCells($columnProductGroup . $rowItem . ':' . $columnProductGroupEnd . $rowItem);
                // phpspreadsheet::styleFont($spreadsheet, $columnProductGroup . $rowItem, true, 9, 'Calibri');
                // $rowItem++;
                // daftar loss class
                foreach ($listLossClass[$department['department_id']][$productGroup] as $lossClass) {
                    if ($dataFilter[$department['department_id']][$productGroup][$lossClass] == null) {
                        continue;
                    }
                    // Menulis data loss class
                    $spreadsheet->getActiveSheet()->setCellValue($columnLossClass . $rowItem, $lossClass);
                    // phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);

                    // memasukkan data
                    $dataItem = $dataFilter[$department['department_id']][$productGroup][$lossClass];

                    foreach ($dataItem['losses'] as $item) {
                        $spreadsheet->getActiveSheet()->mergeCells($columnProductGroup . $rowItem . ':' . $columnProductGroupEnd . $rowItem);
                        $columnItem = $startColumnItemData;
                        // kode loss
                        $activeWorksheet->setCellValue($columnItem . $rowItem, $item['loss_code'] ?? '');
                        phpspreadsheet::textAlignCenter($spreadsheet, $columnItem . $rowItem);
                        $columnItem++;
                        // nama loss
                        $activeWorksheet->setCellValue($columnItem . $rowItem, $item['loss_name'] ?? '');
                        $columnItem++;
                        // loss produksi
                        $activeWorksheet->setCellValue($columnItem . $rowItem, $item['berat_loss_produksi']);
                        if ($item['berat_loss_produksi'] == 0) {
                            $activeWorksheet->getStyle($columnItem . $rowItem)->getNumberFormat()->setFormatCode('0;-0;"-"');
                        } else {
                            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                        }
                        $columnItem++;
                        // loss kebutuhan
                        $activeWorksheet->setCellValue($columnItem . $rowItem, $item['berat_loss_kebutuhan']);
                        if ($item['berat_loss_kebutuhan'] == 0) {
                            $activeWorksheet->getStyle($columnItem . $rowItem)->getNumberFormat()->setFormatCode('0;-0;"-"');
                        } else {
                            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                        }
                        $columnItem++;
                        // frekuensi
                        $activeWorksheet->setCellValue($columnItem . $rowItem, $item['frekuensi']);
                        phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
                        // Terapkan custom format untuk mengganti tampilan 0 dengan -
                        phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);
                        $columnItem++;

                        phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem, false, 8, 'Calibri');
                        $rowItem++;
                    }
                }
            }
            // perhitungan jumlah berdasarkan departemen
            $spreadsheet->getActiveSheet()->mergeCells($startColumnItem . $rowItem . ':' . 'F' . $rowItem);
            $activeWorksheet->setCellValue($startColumnItem . $rowItem, 'Total');
            $columnItem = $startColumnItemData;
            $columnItem++;
            // loss produksi
            $columnItem++;
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . $startRowItemSum . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // loss kebutuhan
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . $startRowItemSum . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // frekuensi
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . $startRowItemSum . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
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
        // $this->addFullBorder($spreadsheet, $startColumnItem . $rowGrandTotal . ':' . $columnValueAvg . $rowGrandTotal);

        $grandTotal = [
            'berat_loss_produksi' => 0,
            'berat_loss_kebutuhan' => 0,
            'frekuensi' => 0
        ];

        foreach ($dataFilter as $departmentId => $lossClasses) {
            foreach ($listProductGroup[$departmentId] as $productGroup) {
                foreach ($listLossClass[$departmentId][$productGroup] as $lossClass => $lossClassName) {
                    if (isset($lossClasses[$productGroup])) {
                        $dataItem = $lossClasses[$productGroup][$lossClass];
                        foreach ($dataItem['losses'] as $item) {
                            $grandTotal['berat_loss_produksi'] += $item['berat_loss_produksi'];
                            $grandTotal['berat_loss_kebutuhan'] += $item['berat_loss_kebutuhan'];
                            $grandTotal['frekuensi'] += $item['frekuensi'];
                        }
                    } else {
                        // Tambahkan default value jika $lossClass tidak ditemukan
                        $grandTotal['berat_loss_produksi'] += 0;
                        $grandTotal['berat_loss_kebutuhan'] += 0;
                        $grandTotal['frekuensi'] += 0;
                    }
                }
            }
        }

        $columnItem = $startColumnItemData;
        $columnItem++;
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_loss_produksi']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // loss kebutuhan
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_loss_kebutuhan']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // frekuensi
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['frekuensi']);
        phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowGrandTotal);
        phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowGrandTotal . ':' . $columnItem . $rowGrandTotal);
        $columnItem++;

        $activeWorksheet->getStyle($columnFirstHeader . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart)->getAlignment()->setWrapText(true);

        // size auto
        $endColumnItem++;
        while ($startColumnItemData !== $endColumnItem) {
            $spreadsheet->getActiveSheet()->getColumnDimension($startColumnItemData)->setAutoSize(true);
            $startColumnItemData++;
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

    public function daftarLossPerDepartemenPerJenisSeitai($tglMasuk, $tglKeluar)
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
        $activeWorksheet->setCellValue('B1', 'DAFTAR LOSS PER DEPARTEMEN PER JENIS SEITAI');
        $activeWorksheet->setCellValue('B2', 'Periode : ' . $tglMasuk . ' s/d ' . $tglKeluar);
        // Style Judul
        phpspreadsheet::styleFont($spreadsheet, 'B1:B2', true, 11, 'Calibri');
        // hide column A
        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setVisible(false);

        // Header
        $columnFirstHeader = 'B';
        $columnFirstHeaderEnd = 'C';
        $spreadsheet->getActiveSheet()->mergeCells($columnFirstHeader . '3:' . $columnFirstHeaderEnd . '3');
        $activeWorksheet->setCellValue('B3', 'Jenis Produk');

        // header
        $rowHeaderStart = 3;
        $columnHeaderStart = 'D';
        $columnHeaderEnd = 'D';
        $header = [
            'Klasifikasi',
            'Kode Loss',
            'Nama Loss',
            'Loss Produksi (Kg)',
            'Loss Kebutuhan (Kg)',
            'Frekuensi'
        ];

        foreach ($header as $key => $value) {
            $activeWorksheet->setCellValue($columnHeaderEnd . $rowHeaderStart, $value);
            $columnHeaderEnd++;
        }
        $columnHeaderEnd = chr(ord($columnHeaderEnd) - 1);

        // style header
        phpspreadsheet::addFullBorder($spreadsheet, $columnFirstHeader . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);
        phpspreadsheet::styleFont($spreadsheet, $columnFirstHeader . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart, true, 9, 'Calibri');
        phpspreadsheet::textAlignCenter($spreadsheet, $columnFirstHeader . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);

        $data = DB::select("
            SELECT
                max(dep.name) AS department_name,
                max(dep.id) AS department_id,
                max(prGrp.code || ' : ' || prGrp.name) AS product_group_name,
                max(mslosCls.name) AS loss_class_name,
                max(mslos.code) AS loss_code,
                max(mslos.name) AS loss_name,
                SUM(CASE WHEN mslos.loss_category_code <> '1' THEN det.berat_loss ELSE 0 END) AS berat_loss_produksi,
                SUM(CASE WHEN mslos.loss_category_code = '1' THEN det.berat_loss ELSE 0 END) AS berat_loss_kebutuhan,
                SUM(det.frekuensi) AS frekuensi
            FROM tdProduct_Goods AS good
            INNER JOIN tdProduct_Goods_Loss AS det ON good.id = det.product_goods_id
            INNER JOIN msLossSeitai AS mslos ON det.loss_seitai_id = mslos.id
            INNER JOIN msLossClass AS mslosCls ON mslos.loss_class_id = mslosCls.id
            INNER JOIN msMachine AS mac ON good.machine_id = mac.id
            INNER JOIN msDepartment AS dep ON mac.department_id = dep.id
            INNER JOIN msProduct AS prd ON good.product_id = prd.id
            INNER JOIN msProduct_type AS prTip ON prd.product_type_id = prTip.id
            INNER JOIN msProduct_group AS prGrp ON prTip.product_group_id = prGrp.id
            WHERE good.production_date BETWEEN '$tglMasuk' AND '$tglKeluar'
            GROUP BY dep.id, prGrp.id, det.loss_seitai_id
        ");

        if (count($data) == 0) {
            $response = [
                'status' => 'error',
                'message' => "Data pada periode tanggal tersebut tidak ditemukan"
            ];

            return $response;
        }

        $listDepartment = array_reduce($data, function ($carry, $item) {
            $carry[$item->department_id] = [
                'department_id' => $item->department_id,
                'department_name' => $item->department_name
            ];
            return $carry;
        }, []);

        // list jenis produk
        $listProductGroup = array_reduce($data, function ($carry, $item) {
            $carry[$item->department_id][$item->product_group_name] = $item->product_group_name;
            return $carry;
        }, []);

        // list klasifikasi
        $listLossClass = array_reduce($data, function ($carry, $item) {
            $carry[$item->department_id][$item->product_group_name][$item->loss_class_name] = $item->loss_class_name;
            return $carry;
        }, []);

        $dataFilter = array_reduce($data, function ($carry, $item) {
            // Periksa apakah department_id sudah ada
            if (!isset($carry[$item->department_id])) {
                $carry[$item->department_id] = [];
            }

            // Periksa apakah product_group_name sudah ada di department_id tersebut
            if (!isset($carry[$item->department_id][$item->product_group_name])) {
                $carry[$item->department_id][$item->product_group_name] = [];
            }

            // Periksa apakah loss_class_name sudah ada di product_group_name tersebut
            if (!isset($carry[$item->department_id][$item->product_group_name][$item->loss_class_name])) {
                $carry[$item->department_id][$item->product_group_name][$item->loss_class_name] = [
                    'loss_class_name' => $item->loss_class_name,
                    'losses' => []  // Buat array untuk menampung beberapa loss_name
                ];
            }

            // Tambahkan loss_name ke dalam array 'losses'
            $carry[$item->department_id][$item->product_group_name][$item->loss_class_name]['losses'][] = [
                'loss_code' => $item->loss_code,
                'loss_name' => $item->loss_name,
                'berat_loss_produksi' => $item->berat_loss_produksi,
                'berat_loss_kebutuhan' => $item->berat_loss_kebutuhan,
                'frekuensi' => $item->frekuensi
            ];

            return $carry;
        }, []);

        // index
        $startColumnItem = 'B';
        $endColumnItem = $columnHeaderEnd;
        $startColumnItemData = 'E';
        $columnProductGroup = 'B';
        $columnProductGroupEnd = 'C';
        $columnLossClass = 'D';
        $startRowItem = 4;
        $rowItem = $startRowItem;
        // daftar departemen
        foreach ($listDepartment as $department) {
            // Menulis data departemen
            $activeWorksheet->setCellValue($startColumnItem . $rowItem, $department['department_name']);
            $spreadsheet->getActiveSheet()->mergeCells($startColumnItem . $rowItem . ':' . $endColumnItem . $rowItem);
            phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowItem, true, 9, 'Calibri');
            $rowItem++;
            $startRowItemSum = $rowItem;
            foreach ($listProductGroup[$department['department_id']] as $productGroup) {
                // Menulis data tipe produk
                $activeWorksheet->setCellValue($columnProductGroup . $rowItem, $productGroup);
                $spreadsheet->getActiveSheet()->mergeCells($columnProductGroup . $rowItem . ':' . $columnProductGroupEnd . $rowItem);
                // phpspreadsheet::styleFont($spreadsheet, $columnProductGroup . $rowItem, true, 9, 'Calibri');
                // $rowItem++;
                // daftar loss class
                foreach ($listLossClass[$department['department_id']][$productGroup] as $lossClass) {
                    if ($dataFilter[$department['department_id']][$productGroup][$lossClass] == null) {
                        continue;
                    }
                    // Menulis data loss class
                    $spreadsheet->getActiveSheet()->setCellValue($columnLossClass . $rowItem, $lossClass);
                    // phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);

                    // memasukkan data
                    $dataItem = $dataFilter[$department['department_id']][$productGroup][$lossClass];

                    foreach ($dataItem['losses'] as $item) {
                        $spreadsheet->getActiveSheet()->mergeCells($columnProductGroup . $rowItem . ':' . $columnProductGroupEnd . $rowItem);
                        $columnItem = $startColumnItemData;
                        // kode loss
                        $activeWorksheet->setCellValue($columnItem . $rowItem, $item['loss_code'] ?? '');
                        phpspreadsheet::textAlignCenter($spreadsheet, $columnItem . $rowItem);
                        $columnItem++;
                        // nama loss
                        $activeWorksheet->setCellValue($columnItem . $rowItem, $item['loss_name'] ?? '');
                        $columnItem++;
                        // loss produksi
                        $activeWorksheet->setCellValue($columnItem . $rowItem, $item['berat_loss_produksi']);
                        if ($item['berat_loss_produksi'] == 0) {
                            $activeWorksheet->getStyle($columnItem . $rowItem)->getNumberFormat()->setFormatCode('0;-0;"-"');
                        } else {
                            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                        }
                        $columnItem++;
                        // loss kebutuhan
                        $activeWorksheet->setCellValue($columnItem . $rowItem, $item['berat_loss_kebutuhan']);
                        if ($item['berat_loss_kebutuhan'] == 0) {
                            $activeWorksheet->getStyle($columnItem . $rowItem)->getNumberFormat()->setFormatCode('0;-0;"-"');
                        } else {
                            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                        }
                        $columnItem++;
                        // frekuensi
                        $activeWorksheet->setCellValue($columnItem . $rowItem, $item['frekuensi']);
                        phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
                        // Terapkan custom format untuk mengganti tampilan 0 dengan -
                        phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);
                        $columnItem++;

                        phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem, false, 8, 'Calibri');
                        $rowItem++;
                    }
                }
            }
            // perhitungan jumlah berdasarkan departemen
            $spreadsheet->getActiveSheet()->mergeCells($startColumnItem . $rowItem . ':' . 'F' . $rowItem);
            $activeWorksheet->setCellValue($startColumnItem . $rowItem, 'Total');
            $columnItem = $startColumnItemData;
            $columnItem++;
            // loss produksi
            $columnItem++;
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . $startRowItemSum . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // loss kebutuhan
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . $startRowItemSum . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // frekuensi
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . $startRowItemSum . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
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
        // $this->addFullBorder($spreadsheet, $startColumnItem . $rowGrandTotal . ':' . $columnValueAvg . $rowGrandTotal);

        $grandTotal = [
            'berat_loss_produksi' => 0,
            'berat_loss_kebutuhan' => 0,
            'frekuensi' => 0
        ];

        foreach ($dataFilter as $departmentId => $lossClasses) {
            foreach ($listProductGroup[$departmentId] as $productGroup) {
                foreach ($listLossClass[$departmentId][$productGroup] as $lossClass => $lossClassName) {
                    if (isset($lossClasses[$productGroup])) {
                        $dataItem = $lossClasses[$productGroup][$lossClass];
                        foreach ($dataItem['losses'] as $item) {
                            $grandTotal['berat_loss_produksi'] += $item['berat_loss_produksi'];
                            $grandTotal['berat_loss_kebutuhan'] += $item['berat_loss_kebutuhan'];
                            $grandTotal['frekuensi'] += $item['frekuensi'];
                        }
                    } else {
                        // Tambahkan default value jika $lossClass tidak ditemukan
                        $grandTotal['berat_loss_produksi'] += 0;
                        $grandTotal['berat_loss_kebutuhan'] += 0;
                        $grandTotal['frekuensi'] += 0;
                    }
                }
            }
        }

        $columnItem = $startColumnItemData;
        $columnItem++;
        $columnItem++;
        // loss produksi
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_loss_produksi']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // loss kebutuhan
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_loss_kebutuhan']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // frekuensi
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['frekuensi']);
        phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowGrandTotal);
        phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowGrandTotal . ':' . $columnItem . $rowGrandTotal);
        $columnItem++;

        $activeWorksheet->getStyle($columnFirstHeader . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart)->getAlignment()->setWrapText(true);

        // size auto
        $endColumnItem++;
        while ($startColumnItemData !== $endColumnItem) {
            $spreadsheet->getActiveSheet()->getColumnDimension($startColumnItemData)->setAutoSize(true);
            $startColumnItemData++;
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

    public function daftarLossPerPetugasInfure($tglMasuk, $tglKeluar)
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
        $activeWorksheet->setCellValue('B1', 'DAFTAR LOSS PER PETUGAS INFURE');
        $activeWorksheet->setCellValue('B2', 'Periode : ' . $tglMasuk . ' s/d ' . $tglKeluar);
        // Style Judul
        phpspreadsheet::styleFont($spreadsheet, 'B1:B2', true, 11, 'Calibri');
        // hide column A
        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setVisible(false);

        // Header
        $columnMesin = 'B';
        $columnMesinEnd = 'C';
        $spreadsheet->getActiveSheet()->mergeCells($columnMesin . '3:' . $columnMesinEnd . '3');
        $activeWorksheet->setCellValue('B3', 'Petugas (NIK, Nama)');

        // header
        $rowHeaderStart = 3;
        $columnHeaderStart = 'D';
        $columnHeaderEnd = 'D';
        $header = [
            'Produksi (Kg)',
            'Total Loss (Kg)',
            'Presentase Loss(%)',
            'Katagae (Kg)',
            'Kualitas (Kg)',
            'Lain-lain (Kg)',
            'Mesin (Kg)',
            'Orang (Kg)',
            'Printing (Kg)',
            'Tachiage (Kg)',
            'Loss Infure di Seitai (Kg)',
            'Frekuensi'
        ];

        foreach ($header as $key => $value) {
            $activeWorksheet->setCellValue($columnHeaderEnd . $rowHeaderStart, $value);
            $columnHeaderEnd++;
        }
        $columnHeaderEnd = chr(ord($columnHeaderEnd) - 1);

        // style header
        phpspreadsheet::addFullBorder($spreadsheet, $columnMesin . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);
        phpspreadsheet::styleFont($spreadsheet, $columnMesin . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart, true, 9, 'Calibri');
        phpspreadsheet::textAlignCenter($spreadsheet, $columnMesin . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);

        $data = DB::select("
            WITH loss_summary AS (
                SELECT
                    los_.product_assembly_id,
                    SUM ( CASE WHEN mslosCls.code = '01' THEN los_.berat_loss ELSE 0 END ) AS berat_loss_katagae,
                    SUM ( CASE WHEN mslosCls.code = '03' THEN los_.berat_loss ELSE 0 END ) AS berat_loss_kualitas,
                    SUM ( CASE WHEN mslosCls.code = '09' THEN los_.berat_loss ELSE 0 END ) AS berat_loss_lainlain,
                    SUM ( CASE WHEN mslosCls.code = '07' THEN los_.berat_loss ELSE 0 END ) AS berat_loss_mesin,
                    SUM ( CASE WHEN mslosCls.code = '08' THEN los_.berat_loss ELSE 0 END ) AS berat_loss_orang,
                    SUM ( CASE WHEN mslosCls.code = '05' THEN los_.berat_loss ELSE 0 END ) AS berat_loss_printing,
                    SUM ( CASE WHEN mslosCls.code = '02' THEN los_.berat_loss ELSE 0 END ) AS berat_loss_tachiage,
                    SUM ( los_.frekuensi ) AS frekuensi
                FROM
                    tdProduct_Assembly_Loss AS los_
                    INNER JOIN msLossInfure AS mslos ON los_.loss_infure_id = mslos.
                    ID INNER JOIN msLossClass AS mslosCls ON mslos.loss_class_id = mslosCls.ID
                GROUP BY
                    los_.product_assembly_id
                ),
                loss_sitai_summary AS (
                SELECT
                    good.employee_id_infure,
                    SUM ( good.infure_berat_loss ) AS infure_berat_loss
                FROM
                    tdProduct_Goods AS good
                WHERE
                    good.production_date BETWEEN '$tglMasuk' AND '$tglKeluar'
                GROUP BY
                    good.employee_id_infure
                ) SELECT
                dep.NAME AS department_name,
                dep.id AS department_id,
                mac.employeeNo,
                mac.empName,
                SUM ( asy.berat_produksi ) AS berat_produksi,
                SUM ( asy.infure_berat_loss ) AS infure_berat_loss,
                COALESCE ( SUM ( loss_summary.berat_loss_katagae ), 0 ) AS berat_loss_katagae,
                COALESCE ( SUM ( loss_summary.berat_loss_kualitas ), 0 ) AS berat_loss_kualitas,
                COALESCE ( SUM ( loss_summary.berat_loss_lainlain ), 0 ) AS berat_loss_lainlain,
                COALESCE ( SUM ( loss_summary.berat_loss_mesin ), 0 ) AS berat_loss_mesin,
                COALESCE ( SUM ( loss_summary.berat_loss_orang ), 0 ) AS berat_loss_orang,
                COALESCE ( SUM ( loss_summary.berat_loss_printing ), 0 ) AS berat_loss_printing,
                COALESCE ( SUM ( loss_summary.berat_loss_tachiage ), 0 ) AS berat_loss_tachiage,
                COALESCE ( SUM ( loss_sitai_summary.infure_berat_loss ), 0 ) AS seitai_infure_berat_loss,
                COALESCE ( SUM ( loss_summary.frekuensi ), 0 ) AS frekuensi
            FROM
                tdProduct_Assembly AS asy
                LEFT JOIN loss_summary ON asy.ID = loss_summary.product_assembly_id
                LEFT JOIN loss_sitai_summary ON asy.employee_id = loss_sitai_summary.employee_id_infure
                INNER JOIN msEmployee AS mac ON asy.employee_id = mac.
                ID INNER JOIN msDepartment AS dep ON mac.department_id = dep.ID
            WHERE
                asy.production_date BETWEEN '$tglMasuk' AND '$tglKeluar'
            GROUP BY
                dep.NAME,
                dep.id,
                mac.employeeNo,
                mac.empName
            ORDER BY
                dep.NAME,
                mac.employeeNo;
        ");

        if (count($data) == 0) {
            $response = [
                'status' => 'error',
                'message' => "Data pada periode tanggal tersebut tidak ditemukan"
            ];

            return $response;
        }

        $listDepartment = array_reduce($data, function ($carry, $item) {
            $carry[$item->department_id] = [
                'department_id' => $item->department_id,
                'department_name' => $item->department_name
            ];
            return $carry;
        }, []);

        // list petugas
        $listEmployee = array_reduce($data, function ($carry, $item) {
            $carry[$item->department_id][$item->employeeno] = $item->empname;
            return $carry;
        }, []);

        $dataFilter = array_reduce($data, function ($carry, $item) {
            // Periksa apakah department_id sudah ada
            if (!isset($carry[$item->department_id])) {
                $carry[$item->department_id] = [];
            }

            // Periksa apakah employeeno sudah ada di department_id tersebut
            if (!isset($carry[$item->department_id][$item->employeeno])) {
                $carry[$item->department_id][$item->employeeno] = [
                    'employeeno' => $item->employeeno,
                    'empname' => $item->empname,
                    'berat_produksi' => $item->berat_produksi,
                    'infure_berat_loss' => $item->infure_berat_loss,
                    'berat_loss_katagae' => $item->berat_loss_katagae,
                    'berat_loss_tachiage' => $item->berat_loss_tachiage,
                    'berat_loss_kualitas' => $item->berat_loss_kualitas,
                    'berat_loss_printing' => $item->berat_loss_printing,
                    'berat_loss_mesin' => $item->berat_loss_mesin,
                    'berat_loss_orang' => $item->berat_loss_orang,
                    'berat_loss_lainlain' => $item->berat_loss_lainlain,
                    'seitai_infure_berat_loss' => $item->seitai_infure_berat_loss,
                    'frekuensi' => $item->frekuensi
                ];
            }

            return $carry;
        }, []);


        // index
        $startColumnItem = 'B';
        $endColumnItem = $columnHeaderEnd;
        $startColumnItemData = 'D';
        $columnEmployee = 'B';
        $columnEmployeeName = 'C';
        $startRowItem = 4;
        $rowItem = $startRowItem;
        // daftar departemen
        foreach ($listDepartment as $department) {
            // Menulis data departemen
            $activeWorksheet->setCellValue($startColumnItem . $rowItem, $department['department_name']);
            $spreadsheet->getActiveSheet()->mergeCells($startColumnItem . $rowItem . ':' . $endColumnItem . $rowItem);
            phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowItem, true, 9, 'Calibri');
            $rowItem++;
            $startRowItemSum = $rowItem;
            // daftar petugas
            foreach ($listEmployee[$department['department_id']] as $employeeNo => $employeeName) {
                if ($dataFilter[$department['department_id']][$employeeNo] == null) {
                    continue;
                }
                // Menulis data petugas
                // $spreadsheet->getActiveSheet()->mergeCells($columnEmployee . $rowItem . ':' . $columnEmployeeName . $rowItem);
                $spreadsheet->getActiveSheet()->setCellValue($columnEmployee . $rowItem, $employeeNo);
                phpspreadsheet::textAlignCenter($spreadsheet, $columnEmployee . $rowItem);
                $spreadsheet->getActiveSheet()->setCellValue($columnEmployeeName . $rowItem, $employeeName);
                // phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);

                // memasukkan data
                $dataItem = $dataFilter[$department['department_id']][$employeeNo];

                // $spreadsheet->getActiveSheet()->mergeCells($columnEmployee . $rowItem . ':' . $columnEmployeeName . $rowItem);
                $columnItem = $startColumnItemData;
                // produksi
                $columnProduksi = $columnItem;
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem['berat_produksi']);
                phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // total loss
                $columnTotalLoss = $columnItem;
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem['infure_berat_loss']);
                phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // presentase loss
                $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=IF(' . $columnProduksi . $rowItem . '=0, 0, ' . $columnTotalLoss . $rowItem . '/' . $columnProduksi . $rowItem . ')');
                $spreadsheet->getActiveSheet()->getStyle($columnItem . $rowItem)->getNumberFormat()->setFormatCode('0.00%');
                phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // katagae
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem['berat_loss_katagae']);
                if ($dataItem['berat_loss_katagae'] == 0) {
                    $activeWorksheet->getStyle($columnItem . $rowItem)->getNumberFormat()->setFormatCode('0;-0;"-"');
                } else {
                    phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                }
                $columnItem++;
                // kualitas
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem['berat_loss_kualitas']);
                if ($dataItem['berat_loss_kualitas'] == 0) {
                    $activeWorksheet->getStyle($columnItem . $rowItem)->getNumberFormat()->setFormatCode('0;-0;"-"');
                } else {
                    phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                }
                $columnItem++;
                // lain-lain
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem['berat_loss_lainlain']);
                if ($dataItem['berat_loss_lainlain'] == 0) {
                    $activeWorksheet->getStyle($columnItem . $rowItem)->getNumberFormat()->setFormatCode('0;-0;"-"');
                } else {
                    phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                }
                $columnItem++;
                // mesin
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem['berat_loss_mesin']);
                if ($dataItem['berat_loss_mesin'] == 0) {
                    $activeWorksheet->getStyle($columnItem . $rowItem)->getNumberFormat()->setFormatCode('0;-0;"-"');
                } else {
                    phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                }
                $columnItem++;
                // orang
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem['berat_loss_orang']);
                if ($dataItem['berat_loss_orang'] == 0) {
                    $activeWorksheet->getStyle($columnItem . $rowItem)->getNumberFormat()->setFormatCode('0;-0;"-"');
                } else {
                    phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                }
                $columnItem++;
                // printing
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem['berat_loss_printing']);
                if ($dataItem['berat_loss_printing'] == 0) {
                    $activeWorksheet->getStyle($columnItem . $rowItem)->getNumberFormat()->setFormatCode('0;-0;"-"');
                } else {
                    phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                }
                $columnItem++;
                // tachiage
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem['berat_loss_tachiage']);
                if ($dataItem['berat_loss_tachiage'] == 0) {
                    $activeWorksheet->getStyle($columnItem . $rowItem)->getNumberFormat()->setFormatCode('0;-0;"-"');
                } else {
                    phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                }
                $columnItem++;
                // loss infure di seitai
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem['seitai_infure_berat_loss']);
                if ($dataItem['seitai_infure_berat_loss'] == 0) {
                    $activeWorksheet->getStyle($columnItem . $rowItem)->getNumberFormat()->setFormatCode('0;-0;"-"');
                } else {
                    phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                }
                $columnItem++;
                // frekuensi
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem['frekuensi']);
                phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
                // Terapkan custom format untuk mengganti tampilan 0 dengan -
                phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);
                $columnItem++;

                phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem, false, 8, 'Calibri');
                $rowItem++;
            }
            // perhitungan jumlah berdasarkan departemen
            $spreadsheet->getActiveSheet()->mergeCells($columnEmployee . $rowItem . ':' . 'C' . $rowItem);
            $activeWorksheet->setCellValue($columnEmployee . $rowItem, 'Total');
            $columnItem = $startColumnItemData;
            // produksi
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . $startRowItemSum . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // total loss
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . $startRowItemSum . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // presentase loss
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=IF(' . $columnProduksi . $rowItem . '=0, 0, ' . $columnTotalLoss . $rowItem . '/' . $columnProduksi . $rowItem . ')');
            $spreadsheet->getActiveSheet()->getStyle($columnItem . $rowItem)->getNumberFormat()->setFormatCode('0.00%');
            phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // katagae
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . $startRowItemSum . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // kualitas
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . $startRowItemSum . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // lain-lain
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . $startRowItemSum . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // mesin
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . $startRowItemSum . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // orang
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . $startRowItemSum . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // printing
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . $startRowItemSum . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // tachiage
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . $startRowItemSum . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // loss infure di seitai
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . $startRowItemSum . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // frekuensi
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . $startRowItemSum . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
            phpspreadsheet::addFullBorder($spreadsheet, $columnEmployee . $rowItem . ':' . $columnItem . $rowItem);
            phpspreadsheet::styleFont($spreadsheet, $columnEmployee . $rowItem . ':' . $columnItem . $rowItem, true, 8, 'Calibri');
            $columnItem++;

            $rowItem++;
            $rowItem++;
        }

        // Grand total
        $rowGrandTotal = $rowItem;
        $spreadsheet->getActiveSheet()->mergeCells($columnEmployee . $rowGrandTotal . ':' . 'C' . $rowGrandTotal);
        $spreadsheet->getActiveSheet()->setCellValue($columnEmployee . $rowGrandTotal, 'GRAND TOTAL');
        phpspreadsheet::styleFont($spreadsheet, $columnEmployee . $rowGrandTotal . ':' . $columnHeaderEnd . $rowGrandTotal, true, 8, 'Calibri');
        // $this->addFullBorder($spreadsheet, $columnEmployee . $rowGrandTotal . ':' . $columnValueAvg . $rowGrandTotal);

        $grandTotal = [
            'berat_produksi' => 0,
            'infure_berat_loss' => 0,
            'berat_loss_katagae' => 0,
            'berat_loss_tachiage' => 0,
            'berat_loss_kualitas' => 0,
            'berat_loss_printing' => 0,
            'berat_loss_mesin' => 0,
            'berat_loss_orang' => 0,
            'berat_loss_lainlain' => 0,
            'seitai_infure_berat_loss' => 0,
            'frekuensi' => 0
        ];

        foreach ($dataFilter as $departmentId => $Employeees) {
            foreach ($listEmployee[$departmentId] as $EmployeeNo => $EmployeeName) {
                if (isset($Employeees[$EmployeeNo])) {
                    $dataItem = $Employeees[$EmployeeNo];
                    $grandTotal['berat_produksi'] += $dataItem['berat_produksi'];
                    $grandTotal['infure_berat_loss'] += $dataItem['infure_berat_loss'];
                    $grandTotal['berat_loss_katagae'] += $dataItem['berat_loss_katagae'];
                    $grandTotal['berat_loss_tachiage'] += $dataItem['berat_loss_tachiage'];
                    $grandTotal['berat_loss_kualitas'] += $dataItem['berat_loss_kualitas'];
                    $grandTotal['berat_loss_printing'] += $dataItem['berat_loss_printing'];
                    $grandTotal['berat_loss_mesin'] += $dataItem['berat_loss_mesin'];
                    $grandTotal['berat_loss_orang'] += $dataItem['berat_loss_orang'];
                    $grandTotal['berat_loss_lainlain'] += $dataItem['berat_loss_lainlain'];
                    $grandTotal['seitai_infure_berat_loss'] += $dataItem['seitai_infure_berat_loss'];
                    $grandTotal['frekuensi'] += $dataItem['frekuensi'];
                } else {
                    // Tambahkan default value jika $Employee tidak ditemukan
                    $grandTotal['berat_produksi'] += 0;
                    $grandTotal['infure_berat_loss'] += 0;
                    $grandTotal['berat_loss_katagae'] += 0;
                    $grandTotal['berat_loss_tachiage'] += 0;
                    $grandTotal['berat_loss_kualitas'] += 0;
                    $grandTotal['berat_loss_printing'] += 0;
                    $grandTotal['berat_loss_mesin'] += 0;
                    $grandTotal['berat_loss_orang'] += 0;
                    $grandTotal['berat_loss_lainlain'] += 0;
                    $grandTotal['seitai_infure_berat_loss'] += 0;
                    $grandTotal['frekuensi'] += 0;
                }
            }
        }

        $columnItem = $startColumnItemData;
        // produksi
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_produksi']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // total loss
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['infure_berat_loss']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // presentase loss
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, '=IF(' . $columnProduksi . $rowGrandTotal . '=0, 0, ' . $columnTotalLoss . $rowGrandTotal . '/' . $columnProduksi . $rowGrandTotal . ')');
        $spreadsheet->getActiveSheet()->getStyle($columnItem . $rowGrandTotal)->getNumberFormat()->setFormatCode('0.00%');
        phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // katagae
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_loss_katagae']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // kualitas
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_loss_kualitas']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // lain-lain
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_loss_lainlain']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // mesin
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_loss_mesin']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // orang
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_loss_orang']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // printing
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_loss_printing']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // tachiage
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_loss_tachiage']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // loss infure di seitai
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['seitai_infure_berat_loss']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // frekuensi
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['frekuensi']);
        phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowGrandTotal);
        phpspreadsheet::addFullBorder($spreadsheet, $columnEmployee . $rowGrandTotal . ':' . $columnItem . $rowGrandTotal);
        $columnItem++;

        $activeWorksheet->getStyle($columnMesin . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart)->getAlignment()->setWrapText(true);

        // size auto
        $endColumnItem++;
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        while ($startColumnItemData !== $endColumnItem) {
            $spreadsheet->getActiveSheet()->getColumnDimension($startColumnItemData)->setAutoSize(true);
            $startColumnItemData++;
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

    public function daftarLossPerPetugasSeitai($tglMasuk, $tglKeluar)
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
        $activeWorksheet->setCellValue('B1', 'DAFTAR LOSS PER PETUGAS SEITAI');
        $activeWorksheet->setCellValue('B2', 'Periode : ' . $tglMasuk . ' s/d ' . $tglKeluar);
        // Style Judul
        phpspreadsheet::styleFont($spreadsheet, 'B1:B2', true, 11, 'Calibri');
        // hide column A
        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setVisible(false);

        // Header
        $columnMesin = 'B';
        $columnMesinEnd = 'C';
        $spreadsheet->getActiveSheet()->mergeCells($columnMesin . '3:' . $columnMesinEnd . '3');
        $activeWorksheet->setCellValue('B3', 'Petugas (NIK, Nama)');

        // header
        $rowHeaderStart = 3;
        $columnHeaderStart = 'D';
        $columnHeaderEnd = 'D';
        $header = [
            'Produksi (Kg)',
            'Total Loss (Kg)',
            'Presentase Loss (%)',
            'Katanuki (Kg)',
            'Kualitas (Kg)',
            'Mesin (Kg)',
            'Lain-lain (Kg)',
            'Frekuensi'
        ];

        foreach ($header as $key => $value) {
            $activeWorksheet->setCellValue($columnHeaderEnd . $rowHeaderStart, $value);
            $columnHeaderEnd++;
        }
        $columnHeaderEnd = chr(ord($columnHeaderEnd) - 1);

        // style header
        phpspreadsheet::addFullBorder($spreadsheet, $columnMesin . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);
        phpspreadsheet::styleFont($spreadsheet, $columnMesin . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart, true, 9, 'Calibri');
        phpspreadsheet::textAlignCenter($spreadsheet, $columnMesin . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);

        $data = DB::select("
            WITH LossAggregates AS (
                SELECT
                    los_.product_goods_id,
                    SUM(CASE WHEN mslosCls.code = '24' THEN los_.berat_loss ELSE 0 END) AS berat_loss_katanuki,
                    SUM(CASE WHEN mslosCls.code = '03' THEN los_.berat_loss ELSE 0 END) AS berat_loss_kualitas,
                    SUM(CASE WHEN mslosCls.code = '07' THEN los_.berat_loss ELSE 0 END) AS berat_loss_mesin,
                    SUM(CASE WHEN mslosCls.code = '09' THEN los_.berat_loss ELSE 0 END) AS berat_loss_lainlain,
                    SUM(los_.frekuensi) AS frekuensi
                FROM tdProduct_Goods_Loss AS los_
                INNER JOIN msLossSeitai AS mslos ON los_.loss_seitai_id = mslos.id
                INNER JOIN msLossClass AS mslosCls ON mslos.loss_class_id = mslosCls.id
                WHERE mslos.id <> 1
                GROUP BY los_.product_goods_id
            )
            SELECT
                dep.name AS department_name,
                dep.id AS department_id,
                mac.employeeNo AS employeeNo,
                mac.empName AS empName,
                SUM(good.qty_produksi * prd.unit_weight * 0.001) AS berat_produksi,
                SUM(good.seitai_berat_loss) AS seitai_berat_loss,
                COALESCE(SUM(loss.berat_loss_katanuki), 0) AS berat_loss_katanuki,
                COALESCE(SUM(loss.berat_loss_kualitas), 0) AS berat_loss_kualitas,
                COALESCE(SUM(loss.berat_loss_mesin), 0) AS berat_loss_mesin,
                COALESCE(SUM(loss.berat_loss_lainlain), 0) AS berat_loss_lainlain,
                COALESCE(SUM(loss.frekuensi), 0) AS frekuensi
            FROM tdProduct_Goods AS good
            INNER JOIN msEmployee AS mac ON good.employee_id = mac.id
            INNER JOIN msDepartment AS dep ON mac.department_id = dep.id
            INNER JOIN msProduct AS prd ON good.product_id = prd.id
            LEFT JOIN LossAggregates AS loss ON good.id = loss.product_goods_id
            WHERE good.production_date BETWEEN '$tglMasuk' AND '$tglKeluar'
            GROUP BY dep.name,dep.id, mac.employeeNo, mac.empName;
        ");

        if (count($data) == 0) {
            $response = [
                'status' => 'error',
                'message' => "Data pada periode tanggal tersebut tidak ditemukan"
            ];

            return $response;
        }

        $listDepartment = array_reduce($data, function ($carry, $item) {
            $carry[$item->department_id] = [
                'department_id' => $item->department_id,
                'department_name' => $item->department_name
            ];
            return $carry;
        }, []);

        // list petugas
        $listEmployee = array_reduce($data, function ($carry, $item) {
            $carry[$item->department_id][$item->employeeno] = $item->empname;
            return $carry;
        }, []);

        $dataFilter = array_reduce($data, function ($carry, $item) {
            // Periksa apakah department_id sudah ada
            if (!isset($carry[$item->department_id])) {
                $carry[$item->department_id] = [];
            }

            // Periksa apakah employeeno sudah ada di department_id tersebut
            if (!isset($carry[$item->department_id][$item->employeeno])) {
                $carry[$item->department_id][$item->employeeno] = [
                    'employeeno' => $item->employeeno,
                    'empname' => $item->empname,
                    'berat_produksi' => $item->berat_produksi,
                    'seitai_berat_loss' => $item->seitai_berat_loss,
                    'berat_loss_katanuki' => $item->berat_loss_katanuki,
                    'berat_loss_kualitas' => $item->berat_loss_kualitas,
                    'berat_loss_mesin' => $item->berat_loss_mesin,
                    'berat_loss_lainlain' => $item->berat_loss_lainlain,
                    'frekuensi' => $item->frekuensi
                ];
            }

            return $carry;
        }, []);


        // index
        $startColumnItem = 'B';
        $endColumnItem = $columnHeaderEnd;
        $startColumnItemData = 'D';
        $columnEmployee = 'B';
        $columnEmployeeName = 'C';
        $startRowItem = 4;
        $rowItem = $startRowItem;
        // daftar departemen
        foreach ($listDepartment as $department) {
            // Menulis data departemen
            $activeWorksheet->setCellValue($startColumnItem . $rowItem, $department['department_name']);
            $spreadsheet->getActiveSheet()->mergeCells($startColumnItem . $rowItem . ':' . $endColumnItem . $rowItem);
            phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowItem, true, 9, 'Calibri');
            $rowItem++;
            $startRowItemSum = $rowItem;
            // daftar petugas
            foreach ($listEmployee[$department['department_id']] as $employeeNo => $employeeName) {
                if ($dataFilter[$department['department_id']][$employeeNo] == null) {
                    continue;
                }
                // Menulis data petugas
                // $spreadsheet->getActiveSheet()->mergeCells($columnEmployee . $rowItem . ':' . $columnEmployeeName . $rowItem);
                $spreadsheet->getActiveSheet()->setCellValue($columnEmployee . $rowItem, $employeeNo);
                phpspreadsheet::textAlignCenter($spreadsheet, $columnEmployee . $rowItem);
                $spreadsheet->getActiveSheet()->setCellValue($columnEmployeeName . $rowItem, $employeeName);
                // phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);

                // memasukkan data
                $dataItem = $dataFilter[$department['department_id']][$employeeNo];

                // $spreadsheet->getActiveSheet()->mergeCells($columnEmployee . $rowItem . ':' . $columnEmployeeName . $rowItem);
                $columnItem = $startColumnItemData;
                // produksi
                $columnProduksi = $columnItem;
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem['berat_produksi']);
                phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // total loss
                $columnTotalLoss = $columnItem;
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem['seitai_berat_loss']);
                phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // presentase loss
                $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=IF(' . $columnProduksi . $rowItem . '=0, 0, ' . $columnTotalLoss . $rowItem . '/' . $columnProduksi . $rowItem . ')');
                $spreadsheet->getActiveSheet()->getStyle($columnItem . $rowItem)->getNumberFormat()->setFormatCode('0.00%');
                phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // katanuki
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem['berat_loss_katanuki']);
                if ($dataItem['berat_loss_katanuki'] == 0) {
                    $activeWorksheet->getStyle($columnItem . $rowItem)->getNumberFormat()->setFormatCode('0;-0;"-"');
                } else {
                    phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                }
                $columnItem++;
                // kualitas
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem['berat_loss_kualitas']);
                if ($dataItem['berat_loss_kualitas'] == 0) {
                    $activeWorksheet->getStyle($columnItem . $rowItem)->getNumberFormat()->setFormatCode('0;-0;"-"');
                } else {
                    phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                }
                $columnItem++;
                // mesin
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem['berat_loss_mesin']);
                if ($dataItem['berat_loss_mesin'] == 0) {
                    $activeWorksheet->getStyle($columnItem . $rowItem)->getNumberFormat()->setFormatCode('0;-0;"-"');
                } else {
                    phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                }
                $columnItem++;
                // lain-lain
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem['berat_loss_lainlain']);
                if ($dataItem['berat_loss_lainlain'] == 0) {
                    $activeWorksheet->getStyle($columnItem . $rowItem)->getNumberFormat()->setFormatCode('0;-0;"-"');
                } else {
                    phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                }
                $columnItem++;
                // frekuensi
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem['frekuensi']);
                phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
                // Terapkan custom format untuk mengganti tampilan 0 dengan -
                phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);
                $columnItem++;

                phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem, false, 8, 'Calibri');
                $rowItem++;
            }
            // perhitungan jumlah berdasarkan departemen
            $spreadsheet->getActiveSheet()->mergeCells($columnEmployee . $rowItem . ':' . 'C' . $rowItem);
            $activeWorksheet->setCellValue($columnEmployee . $rowItem, 'Total');
            $columnItem = $startColumnItemData;
            // produksi
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . $startRowItemSum . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // total loss
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . $startRowItemSum . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // presentase loss
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=IF(' . $columnProduksi . $rowItem . '=0, 0, ' . $columnTotalLoss . $rowItem . '/' . $columnProduksi . $rowItem . ')');
            $spreadsheet->getActiveSheet()->getStyle($columnItem . $rowItem)->getNumberFormat()->setFormatCode('0.00%');
            phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // katanuki
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . $startRowItemSum . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // kualitas
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . $startRowItemSum . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // mesin
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . $startRowItemSum . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // lain-lain
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . $startRowItemSum . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // frekuensi
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . $startRowItemSum . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
            phpspreadsheet::addFullBorder($spreadsheet, $columnEmployee . $rowItem . ':' . $columnItem . $rowItem);
            phpspreadsheet::styleFont($spreadsheet, $columnEmployee . $rowItem . ':' . $columnItem . $rowItem, true, 8, 'Calibri');
            $columnItem++;

            $rowItem++;
            $rowItem++;
        }

        // Grand total
        $rowGrandTotal = $rowItem;
        $spreadsheet->getActiveSheet()->mergeCells($columnEmployee . $rowGrandTotal . ':' . 'C' . $rowGrandTotal);
        $spreadsheet->getActiveSheet()->setCellValue($columnEmployee . $rowGrandTotal, 'GRAND TOTAL');
        phpspreadsheet::styleFont($spreadsheet, $columnEmployee . $rowGrandTotal . ':' . $columnHeaderEnd . $rowGrandTotal, true, 8, 'Calibri');
        // $this->addFullBorder($spreadsheet, $columnEmployee . $rowGrandTotal . ':' . $columnValueAvg . $rowGrandTotal);

        $grandTotal = [
            'berat_produksi' => 0,
            'seitai_berat_loss' => 0,
            'berat_loss_katanuki' => 0,
            'berat_loss_kualitas' => 0,
            'berat_loss_mesin' => 0,
            'berat_loss_lainlain' => 0,
            'frekuensi' => 0
        ];

        foreach ($dataFilter as $departmentId => $Employeees) {
            foreach ($listEmployee[$departmentId] as $EmployeeNo => $EmployeeName) {
                if (isset($Employeees[$EmployeeNo])) {
                    $dataItem = $Employeees[$EmployeeNo];
                    $grandTotal['berat_produksi'] += $dataItem['berat_produksi'];
                    $grandTotal['seitai_berat_loss'] += $dataItem['seitai_berat_loss'];
                    $grandTotal['berat_loss_katanuki'] += $dataItem['berat_loss_katanuki'];
                    $grandTotal['berat_loss_kualitas'] += $dataItem['berat_loss_kualitas'];
                    $grandTotal['berat_loss_mesin'] += $dataItem['berat_loss_mesin'];
                    $grandTotal['berat_loss_lainlain'] += $dataItem['berat_loss_lainlain'];
                    $grandTotal['frekuensi'] += $dataItem['frekuensi'];
                } else {
                    // Tambahkan default value jika $Employee tidak ditemukan
                    $grandTotal['berat_produksi'] += 0;
                    $grandTotal['seitai_berat_loss'] += 0;
                    $grandTotal['berat_loss_katanuki'] += 0;
                    $grandTotal['berat_loss_kualitas'] += 0;
                    $grandTotal['berat_loss_mesin'] += 0;
                    $grandTotal['berat_loss_lainlain'] += 0;
                    $grandTotal['frekuensi'] += 0;
                }
            }
        }

        $columnItem = $startColumnItemData;
        // produksi
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_produksi']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // total loss
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['seitai_berat_loss']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // presentase loss
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, '=IF(' . $columnProduksi . $rowGrandTotal . '=0, 0, ' . $columnTotalLoss . $rowGrandTotal . '/' . $columnProduksi . $rowGrandTotal . ')');
        $spreadsheet->getActiveSheet()->getStyle($columnItem . $rowGrandTotal)->getNumberFormat()->setFormatCode('0.00%');
        phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // katanuki
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_loss_katanuki']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // kualitas
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_loss_kualitas']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // mesin
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_loss_mesin']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // lain-lain
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_loss_lainlain']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // frekuensi
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['frekuensi']);
        phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowGrandTotal);

        phpspreadsheet::addFullBorder($spreadsheet, $columnEmployee . $rowGrandTotal . ':' . $columnItem . $rowGrandTotal);
        $columnItem++;

        $activeWorksheet->getStyle($columnMesin . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart)->getAlignment()->setWrapText(true);

        // size auto
        $endColumnItem++;
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        while ($startColumnItemData !== $endColumnItem) {
            $spreadsheet->getActiveSheet()->getColumnDimension($startColumnItemData)->setAutoSize(true);
            $startColumnItemData++;
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

    public function daftarLossPerMesinInfure($tglMasuk, $tglKeluar)
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
        $activeWorksheet->setCellValue('B1', 'DAFTAR LOSS PER MESIN INFURE');
        $activeWorksheet->setCellValue('B2', 'Periode : ' . $tglMasuk . ' s/d ' . $tglKeluar);
        // Style Judul
        phpspreadsheet::styleFont($spreadsheet, 'B1:B2', true, 11, 'Calibri');
        // hide column A
        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setVisible(false);

        // Header
        $columnMesin = 'B';
        $columnMesinEnd = 'C';
        $spreadsheet->getActiveSheet()->mergeCells($columnMesin . '3:' . $columnMesinEnd . '3');
        $activeWorksheet->setCellValue('B3', 'Klasifikasi');

        // header
        $rowHeaderStart = 3;
        $columnHeaderStart = 'D';
        $columnHeaderEnd = 'D';
        $header = [
            'Kode Loss',
            'Nama Loss',
            'Loss Produksi (Kg)',
            'Loss Kebutuhan (Kg)',
            'Frekuensi'
        ];

        foreach ($header as $key => $value) {
            $activeWorksheet->setCellValue($columnHeaderEnd . $rowHeaderStart, $value);
            $columnHeaderEnd++;
        }
        $columnHeaderEnd = chr(ord($columnHeaderEnd) - 1);

        // style header
        phpspreadsheet::addFullBorder($spreadsheet, $columnMesin . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);
        phpspreadsheet::styleFont($spreadsheet, $columnMesin . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart, true, 9, 'Calibri');
        phpspreadsheet::textAlignCenter($spreadsheet, $columnMesin . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);

        $data = DB::select("
            SELECT
                max(mac.machineNo) AS machine_no,
                max(mac.machineNo || ' : ' || mac.machineName) AS machine_name,
                max(mslosCls.name) AS loss_class_name,
                max(mslos.code) AS loss_code,
                max(mslos.name) AS loss_name,
                SUM(CASE WHEN mslos.loss_category_code <> '1' THEN det.berat_loss ELSE 0 END) AS berat_loss_produksi,
                SUM(CASE WHEN mslos.loss_category_code = '1' THEN det.berat_loss ELSE 0 END) AS berat_loss_kebutuhan,
                SUM(det.frekuensi) AS frekuensi
            FROM tdProduct_Assembly AS asy
            INNER JOIN tdProduct_Assembly_Loss AS det ON asy.id = det.product_assembly_id
            INNER JOIN msLossInfure AS mslos ON det.loss_infure_id = mslos.id
            INNER JOIN msLossClass AS mslosCls ON mslos.loss_class_id = mslosCls.id
            INNER JOIN msMachine AS mac ON asy.machine_id = mac.id
            WHERE asy.production_date BETWEEN '$tglMasuk' AND '$tglKeluar'
            GROUP BY mac.id, det.loss_infure_id
        ");

        if (count($data) == 0) {
            $response = [
                'status' => 'error',
                'message' => "Data pada periode tanggal tersebut tidak ditemukan"
            ];

            return $response;
        }

        // list mesin
        $listMachine = array_reduce($data, function ($carry, $item) {
            $carry[$item->machine_no] = [
                'machine_no' => $item->machine_no,
                'machine_name' => $item->machine_name
            ];
            return $carry;
        }, []);

        // list klasifikasi
        $listLossClass = array_reduce($data, function ($carry, $item) {
            $carry[$item->machine_no][$item->loss_class_name] = $item->loss_class_name;
            return $carry;
        }, []);

        $dataFilter = array_reduce($data, function ($carry, $item) {
            // Periksa apakah machine_no sudah ada
            if (!isset($carry[$item->machine_no])) {
                $carry[$item->machine_no] = [];
            }

            // Periksa apakah loss_class_name sudah ada di machine_no tersebut
            if (!isset($carry[$item->machine_no][$item->loss_class_name])) {
                $carry[$item->machine_no][$item->loss_class_name] = [
                    'loss_class_name' => $item->loss_class_name,
                    'losses' => []  // Buat array untuk menampung beberapa loss_name
                ];
            }

            // Tambahkan loss_name ke dalam array 'losses'
            $carry[$item->machine_no][$item->loss_class_name]['losses'][] = [
                'loss_code' => $item->loss_code,
                'loss_name' => $item->loss_name,
                'berat_loss_produksi' => $item->berat_loss_produksi,
                'berat_loss_kebutuhan' => $item->berat_loss_kebutuhan,
                'frekuensi' => $item->frekuensi
            ];

            return $carry;
        }, []);


        // index
        $startColumnItem = 'B';
        $endColumnItem = $columnHeaderEnd;
        $startColumnItemData = 'D';
        $columnLossClass = 'B';
        $columnLossClassName = 'C';
        $startRowItem = 4;
        $rowItem = $startRowItem;
        // daftar departemen
        foreach ($listMachine as $machine) {
            // Menulis data departemen
            $activeWorksheet->setCellValue($startColumnItem . $rowItem, $machine['machine_name']);
            $spreadsheet->getActiveSheet()->mergeCells($startColumnItem . $rowItem . ':' . $endColumnItem . $rowItem);
            phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowItem, true, 9, 'Calibri');
            $rowItem++;
            $startRowItemSum = $rowItem;
            // daftar mesin
            foreach ($listLossClass[$machine['machine_no']] as $lossClass) {
                if ($dataFilter[$machine['machine_no']][$lossClass] == null) {
                    continue;
                }
                // Menulis data mesin
                $spreadsheet->getActiveSheet()->mergeCells($columnLossClass . $rowItem . ':' . $columnLossClassName . $rowItem);
                $spreadsheet->getActiveSheet()->setCellValue($columnLossClass . $rowItem, $lossClass);
                // phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);

                // memasukkan data
                $dataItem = $dataFilter[$machine['machine_no']][$lossClass];

                foreach ($dataItem['losses'] as $item) {
                    $spreadsheet->getActiveSheet()->mergeCells($columnLossClass . $rowItem . ':' . $columnLossClassName . $rowItem);
                    $columnItem = $startColumnItemData;
                    // kode loss
                    $activeWorksheet->setCellValue($columnItem . $rowItem, $item['loss_code'] ?? '');
                    phpspreadsheet::textAlignCenter($spreadsheet, $columnItem . $rowItem);
                    $columnItem++;
                    // nama loss
                    $activeWorksheet->setCellValue($columnItem . $rowItem, $item['loss_name'] ?? '');
                    $columnItem++;
                    // loss produksi
                    $activeWorksheet->setCellValue($columnItem . $rowItem, $item['berat_loss_produksi']);
                    if ($item['berat_loss_produksi'] == 0) {
                        $activeWorksheet->getStyle($columnItem . $rowItem)->getNumberFormat()->setFormatCode('0;-0;"-"');
                    } else {
                        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                    }
                    $columnItem++;
                    // loss kebutuhan
                    $activeWorksheet->setCellValue($columnItem . $rowItem, $item['berat_loss_kebutuhan']);
                    if ($item['berat_loss_kebutuhan'] == 0) {
                        $activeWorksheet->getStyle($columnItem . $rowItem)->getNumberFormat()->setFormatCode('0;-0;"-"');
                    } else {
                        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                    }
                    $columnItem++;
                    // frekuensi
                    $activeWorksheet->setCellValue($columnItem . $rowItem, $item['frekuensi']);
                    phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
                    // Terapkan custom format untuk mengganti tampilan 0 dengan -
                    phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);
                    $columnItem++;

                    phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem, false, 8, 'Calibri');
                    $rowItem++;
                }
            }
            // perhitungan jumlah berdasarkan departemen
            $spreadsheet->getActiveSheet()->mergeCells($columnLossClass . $rowItem . ':' . 'E' . $rowItem);
            $activeWorksheet->setCellValue($columnLossClass . $rowItem, 'Total');
            $columnItem = $startColumnItemData;
            $columnItem++;
            // loss produksi
            $columnItem++;
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . $startRowItemSum . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // loss kebutuhan
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . $startRowItemSum . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // frekuensi
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . $startRowItemSum . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
            phpspreadsheet::addFullBorder($spreadsheet, $columnLossClass . $rowItem . ':' . $columnItem . $rowItem);
            phpspreadsheet::styleFont($spreadsheet, $columnLossClass . $rowItem . ':' . $columnItem . $rowItem, true, 8, 'Calibri');
            $columnItem++;

            $rowItem++;
            $rowItem++;
        }

        // Grand total
        $rowGrandTotal = $rowItem;
        $spreadsheet->getActiveSheet()->mergeCells($columnLossClass . $rowGrandTotal . ':' . 'E' . $rowGrandTotal);
        $spreadsheet->getActiveSheet()->setCellValue($columnLossClass . $rowGrandTotal, 'GRAND TOTAL');
        phpspreadsheet::styleFont($spreadsheet, $columnLossClass . $rowGrandTotal . ':' . $columnHeaderEnd . $rowGrandTotal, true, 8, 'Calibri');
        // $this->addFullBorder($spreadsheet, $columnLossClass . $rowGrandTotal . ':' . $columnValueAvg . $rowGrandTotal);

        $grandTotal = [
            'berat_loss_produksi' => 0,
            'berat_loss_kebutuhan' => 0,
            'frekuensi' => 0
        ];

        foreach ($dataFilter as $machineNo => $lossClasses) {
            foreach ($listLossClass[$machineNo] as $lossClass => $lossClassName) {
                if (isset($lossClasses[$lossClass])) {
                    $dataItem = $lossClasses[$lossClass];
                    foreach ($dataItem['losses'] as $item) {
                        $grandTotal['berat_loss_produksi'] += $item['berat_loss_produksi'];
                        $grandTotal['berat_loss_kebutuhan'] += $item['berat_loss_kebutuhan'];
                        $grandTotal['frekuensi'] += $item['frekuensi'];
                    }
                } else {
                    // Tambahkan default value jika $lossClass tidak ditemukan
                    $grandTotal['berat_loss_produksi'] += 0;
                    $grandTotal['berat_loss_kebutuhan'] += 0;
                    $grandTotal['frekuensi'] += 0;
                }
            }
        }

        $columnItem = $startColumnItemData;
        $columnItem++;
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_loss_produksi']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_loss_kebutuhan']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // frekuensi
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['frekuensi']);
        phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowGrandTotal);
        phpspreadsheet::addFullBorder($spreadsheet, $columnLossClass . $rowGrandTotal . ':' . $columnItem . $rowGrandTotal);
        phpspreadsheet::addFullBorder($spreadsheet, $columnLossClass . $rowGrandTotal . ':' . $columnItem . $rowGrandTotal);
        $columnItem++;

        $activeWorksheet->getStyle($columnMesin . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart)->getAlignment()->setWrapText(true);

        // size auto
        $endColumnItem++;
        while ($startColumnItemData !== $endColumnItem) {
            $spreadsheet->getActiveSheet()->getColumnDimension($startColumnItemData)->setAutoSize(true);
            $startColumnItemData++;
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

    public function daftarLossPerMesinSeitai($tglMasuk, $tglKeluar)
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
        $activeWorksheet->setCellValue('B1', 'DAFTAR LOSS PER MESIN SEITAI');
        $activeWorksheet->setCellValue('B2', 'Periode : ' . $tglMasuk . ' s/d ' . $tglKeluar);
        // Style Judul
        phpspreadsheet::styleFont($spreadsheet, 'B1:B2', true, 11, 'Calibri');
        // hide column A
        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setVisible(false);

        // Header
        $columnMesin = 'B';
        $columnMesinEnd = 'C';
        $spreadsheet->getActiveSheet()->mergeCells($columnMesin . '3:' . $columnMesinEnd . '3');
        $activeWorksheet->setCellValue('B3', 'Klasifikasi');

        // header
        $rowHeaderStart = 3;
        $columnHeaderStart = 'D';
        $columnHeaderEnd = 'D';
        $header = [
            'Kode Loss',
            'Nama Loss',
            'Loss Produksi (Kg)',
            'Loss Kebutuhan (Kg)',
            'Frekuensi'
        ];

        foreach ($header as $key => $value) {
            $activeWorksheet->setCellValue($columnHeaderEnd . $rowHeaderStart, $value);
            $columnHeaderEnd++;
        }
        $columnHeaderEnd = chr(ord($columnHeaderEnd) - 1);

        // style header
        phpspreadsheet::addFullBorder($spreadsheet, $columnMesin . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);
        phpspreadsheet::styleFont($spreadsheet, $columnMesin . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart, true, 9, 'Calibri');
        phpspreadsheet::textAlignCenter($spreadsheet, $columnMesin . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);

        $data = DB::select("
            SELECT
                max(mac.machineNo) AS machine_no,
                max(mac.machineNo || ' : ' || mac.machineName) AS machine_name,
                max(mslosCls.name) AS loss_class_name,
                max(mslos.code) AS loss_code,
                max(mslos.name) AS loss_name,
                SUM(CASE WHEN mslos.loss_category_code <> '1' THEN det.berat_loss ELSE 0 END) AS berat_loss_produksi,
                SUM(CASE WHEN mslos.loss_category_code = '1' THEN det.berat_loss ELSE 0 END) AS berat_loss_kebutuhan,
                SUM(det.frekuensi) AS frekuensi
            FROM tdProduct_Goods AS good
            INNER JOIN tdProduct_Goods_Loss AS det ON good.id = det.product_goods_id
            INNER JOIN msLossSeitai AS mslos ON det.loss_seitai_id = mslos.id
            INNER JOIN msLossClass AS mslosCls ON mslos.loss_class_id = mslosCls.id
            INNER JOIN msMachine AS mac ON good.machine_id = mac.id
            WHERE good.production_date BETWEEN '$tglMasuk' AND '$tglKeluar'
            GROUP BY mac.id, det.loss_seitai_id
        ");

        if (count($data) == 0) {
            $response = [
                'status' => 'error',
                'message' => "Data pada periode tanggal tersebut tidak ditemukan"
            ];

            return $response;
        }

        // list mesin
        $listMachine = array_reduce($data, function ($carry, $item) {
            $carry[$item->machine_no] = [
                'machine_no' => $item->machine_no,
                'machine_name' => $item->machine_name
            ];
            return $carry;
        }, []);

        // list klasifikasi
        $listLossClass = array_reduce($data, function ($carry, $item) {
            $carry[$item->machine_no][$item->loss_class_name] = $item->loss_class_name;
            return $carry;
        }, []);

        $dataFilter = array_reduce($data, function ($carry, $item) {
            // Periksa apakah machine_no sudah ada
            if (!isset($carry[$item->machine_no])) {
                $carry[$item->machine_no] = [];
            }

            // Periksa apakah loss_class_name sudah ada di machine_no tersebut
            if (!isset($carry[$item->machine_no][$item->loss_class_name])) {
                $carry[$item->machine_no][$item->loss_class_name] = [
                    'loss_class_name' => $item->loss_class_name,
                    'losses' => []  // Buat array untuk menampung beberapa loss_name
                ];
            }

            // Tambahkan loss_name ke dalam array 'losses'
            $carry[$item->machine_no][$item->loss_class_name]['losses'][] = [
                'loss_code' => $item->loss_code,
                'loss_name' => $item->loss_name,
                'berat_loss_produksi' => $item->berat_loss_produksi,
                'berat_loss_kebutuhan' => $item->berat_loss_kebutuhan,
                'frekuensi' => $item->frekuensi
            ];

            return $carry;
        }, []);


        // index
        $startColumnItem = 'B';
        $endColumnItem = $columnHeaderEnd;
        $startColumnItemData = 'D';
        $columnLossClass = 'B';
        $columnLossClassName = 'C';
        $startRowItem = 4;
        $rowItem = $startRowItem;
        // daftar departemen
        foreach ($listMachine as $machine) {
            // Menulis data departemen
            $activeWorksheet->setCellValue($startColumnItem . $rowItem, $machine['machine_name']);
            $spreadsheet->getActiveSheet()->mergeCells($startColumnItem . $rowItem . ':' . $endColumnItem . $rowItem);
            phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowItem, true, 9, 'Calibri');
            $rowItem++;
            $startRowItemSum = $rowItem;
            // daftar mesin
            foreach ($listLossClass[$machine['machine_no']] as $lossClass) {
                if ($dataFilter[$machine['machine_no']][$lossClass] == null) {
                    continue;
                }
                // Menulis data mesin
                $spreadsheet->getActiveSheet()->mergeCells($columnLossClass . $rowItem . ':' . $columnLossClassName . $rowItem);
                $spreadsheet->getActiveSheet()->setCellValue($columnLossClass . $rowItem, $lossClass);
                // phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);

                // memasukkan data
                $dataItem = $dataFilter[$machine['machine_no']][$lossClass];

                foreach ($dataItem['losses'] as $item) {
                    $spreadsheet->getActiveSheet()->mergeCells($columnLossClass . $rowItem . ':' . $columnLossClassName . $rowItem);
                    $columnItem = $startColumnItemData;
                    // kode loss
                    $activeWorksheet->setCellValue($columnItem . $rowItem, $item['loss_code'] ?? '');
                    phpspreadsheet::textAlignCenter($spreadsheet, $columnItem . $rowItem);
                    $columnItem++;
                    // nama loss
                    $activeWorksheet->setCellValue($columnItem . $rowItem, $item['loss_name'] ?? '');
                    $columnItem++;
                    // loss produksi
                    $activeWorksheet->setCellValue($columnItem . $rowItem, $item['berat_loss_produksi']);
                    if ($item['berat_loss_produksi'] == 0) {
                        $activeWorksheet->getStyle($columnItem . $rowItem)->getNumberFormat()->setFormatCode('0;-0;"-"');
                    } else {
                        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                    }
                    $columnItem++;
                    // loss kebutuhan
                    $activeWorksheet->setCellValue($columnItem . $rowItem, $item['berat_loss_kebutuhan']);
                    if ($item['berat_loss_kebutuhan'] == 0) {
                        $activeWorksheet->getStyle($columnItem . $rowItem)->getNumberFormat()->setFormatCode('0;-0;"-"');
                    } else {
                        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                    }
                    $columnItem++;
                    // frekuensi
                    $activeWorksheet->setCellValue($columnItem . $rowItem, $item['frekuensi']);
                    phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
                    // Terapkan custom format untuk mengganti tampilan 0 dengan -
                    phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);
                    $columnItem++;

                    phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem, false, 8, 'Calibri');
                    $rowItem++;
                }
            }
            // perhitungan jumlah berdasarkan departemen
            $spreadsheet->getActiveSheet()->mergeCells($columnLossClass . $rowItem . ':' . 'E' . $rowItem);
            $activeWorksheet->setCellValue($columnLossClass . $rowItem, 'Total');
            $columnItem = $startColumnItemData;
            $columnItem++;
            // loss produksi
            $columnItem++;
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . $startRowItemSum . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // loss kebutuhan
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . $startRowItemSum . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // frekuensi
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . $startRowItemSum . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
            phpspreadsheet::addFullBorder($spreadsheet, $columnLossClass . $rowItem . ':' . $columnItem . $rowItem);
            phpspreadsheet::styleFont($spreadsheet, $columnLossClass . $rowItem . ':' . $columnItem . $rowItem, true, 8, 'Calibri');
            $columnItem++;

            $rowItem++;
            $rowItem++;
        }

        // Grand total
        $rowGrandTotal = $rowItem;
        $spreadsheet->getActiveSheet()->mergeCells($columnLossClass . $rowGrandTotal . ':' . 'E' . $rowGrandTotal);
        $spreadsheet->getActiveSheet()->setCellValue($columnLossClass . $rowGrandTotal, 'GRAND TOTAL');
        phpspreadsheet::styleFont($spreadsheet, $columnLossClass . $rowGrandTotal . ':' . $columnHeaderEnd . $rowGrandTotal, true, 8, 'Calibri');
        // $this->addFullBorder($spreadsheet, $columnLossClass . $rowGrandTotal . ':' . $columnValueAvg . $rowGrandTotal);

        $grandTotal = [
            'berat_loss_produksi' => 0,
            'berat_loss_kebutuhan' => 0,
            'frekuensi' => 0
        ];

        foreach ($dataFilter as $machineNo => $lossClasses) {
            foreach ($listLossClass[$machineNo] as $lossClass => $lossClassName) {
                if (isset($lossClasses[$lossClass])) {
                    $dataItem = $lossClasses[$lossClass];
                    foreach ($dataItem['losses'] as $item) {
                        $grandTotal['berat_loss_produksi'] += $item['berat_loss_produksi'];
                        $grandTotal['berat_loss_kebutuhan'] += $item['berat_loss_kebutuhan'];
                        $grandTotal['frekuensi'] += $item['frekuensi'];
                    }
                } else {
                    // Tambahkan default value jika $lossClass tidak ditemukan
                    $grandTotal['berat_loss_produksi'] += 0;
                    $grandTotal['berat_loss_kebutuhan'] += 0;
                    $grandTotal['frekuensi'] += 0;
                }
            }
        }

        $columnItem = $startColumnItemData;
        $columnItem++;
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_loss_produksi']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_loss_kebutuhan']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // frekuensi
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['frekuensi']);
        phpspreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowGrandTotal);
        phpspreadsheet::addFullBorder($spreadsheet, $columnLossClass . $rowGrandTotal . ':' . $columnItem . $rowGrandTotal);
        phpspreadsheet::addFullBorder($spreadsheet, $columnLossClass . $rowGrandTotal . ':' . $columnItem . $rowGrandTotal);
        $columnItem++;

        $activeWorksheet->getStyle($columnMesin . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart)->getAlignment()->setWrapText(true);

        // size auto
        $endColumnItem++;
        while ($startColumnItemData !== $endColumnItem) {
            $spreadsheet->getActiveSheet()->getColumnDimension($startColumnItemData)->setAutoSize(true);
            $startColumnItemData++;
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

    public function kapasitasProduksiInfure($tglMasuk, $tglKeluar)
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
        $activeWorksheet->setCellValue('B1', 'KAPASITAS PRODUKSI INFURE');
        $activeWorksheet->setCellValue('B2', 'Periode : ' . $tglMasuk . ' s/d ' . $tglKeluar);
        // Style Judul
        phpspreadsheet::styleFont($spreadsheet, 'B1:B2', true, 11, 'Calibri');
        // hide column A
        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setVisible(false);

        // Header
        $columnMesin = 'B';
        $columnMesinEnd = 'C';
        $spreadsheet->getActiveSheet()->mergeCells($columnMesin . '3:' . $columnMesinEnd . '3');
        $activeWorksheet->setCellValue('B3', 'Mesin');

        // header
        $rowHeaderStart = 3;
        $columnHeaderStart = 'D';
        $columnHeaderEnd = 'D';
        $header = [
            'Hari Kerja (Hari)',
            'Kapasitas (Kg)',
            'Produksi (Kg)',
            'Rasio Produksi (%)',
            'Kapasitas (Meter)',
            'Produksi (Meter)',
            'Rasio Produksi (%)',
        ];

        foreach ($header as $key => $value) {
            $activeWorksheet->setCellValue($columnHeaderEnd . $rowHeaderStart, $value);
            $columnHeaderEnd++;
        }
        $columnHeaderEnd = chr(ord($columnHeaderEnd) - 1);

        // style header
        phpspreadsheet::addFullBorder($spreadsheet, $columnMesin . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);
        phpspreadsheet::styleFont($spreadsheet, $columnMesin . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart, true, 9, 'Calibri');
        phpspreadsheet::textAlignCenter($spreadsheet, $columnMesin . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);

        $data = DB::select("
            SELECT
                max(prGrp.code) AS product_group_code,
                max(prGrp.name) AS product_group_name,
                MAX(mac.machineNo) AS machine_no,
                MAX(mac.machineName) AS machine_name,
                SUM(asy.panjang_produksi) AS panjang_produksi,
                SUM(asy.berat_produksi) AS berat_produksi,
                MAX(mac.capacity_kg) AS capacity_kg,
                MAX(mac.capacity_lembar) AS capacity_lembar--,
                --@day AS seq_no
            FROM tdProduct_Assembly AS asy
            INNER JOIN msProduct AS prd ON asy.product_id = prd.id
            INNER JOIN msProduct_type AS prTip ON prd.product_type_id = prTip.id
            INNER JOIN msProduct_group AS prGrp ON prTip.product_group_id = prGrp.id
            INNER JOIN msMachine AS mac ON asy.machine_id = mac.id
            WHERE asy.production_date BETWEEN '$tglMasuk' AND '$tglKeluar'
            GROUP BY prGrp.id, asy.machine_id
        ");

        if (count($data) == 0) {
            $response = [
                'status' => 'error',
                'message' => "Data pada periode tanggal tersebut tidak ditemukan"
            ];

            return $response;
        }

        // list group produk
        $listProductGroup = array_reduce($data, function ($carry, $item) {
            $carry[$item->product_group_code] = [
                'product_group_code' => $item->product_group_code,
                'product_group_name' => $item->product_group_name
            ];
            return $carry;
        }, []);

        // list mesin berdasarkan tanggal pertama dan departemen
        $listMachine = array_reduce($data, function ($carry, $item) {
            if (!isset($carry[$item->product_group_code])) {
                $carry[$item->product_group_code] = [];
            }
            $carry[$item->product_group_code][$item->machine_no] = $item->machine_name;
            return $carry;
        }, []);

        $dataFilter = array_reduce($data, function ($carry, $item) {
            $carry[$item->product_group_code][$item->machine_no] = $item;
            return $carry;
        }, []);

        // index
        $startColumnItem = 'B';
        $endColumnItem = $columnHeaderEnd;
        $startColumnItemData = 'D';
        $columnMachineNo = 'B';
        $columnMachineName = 'C';
        $startRowItem = 4;
        $rowItem = $startRowItem;
        // daftar departemen
        foreach ($listProductGroup as $productGroup) {
            // Menulis data departemen
            $activeWorksheet->setCellValue($startColumnItem . $rowItem, $productGroup['product_group_name']);
            $spreadsheet->getActiveSheet()->mergeCells($startColumnItem . $rowItem . ':' . $endColumnItem . $rowItem);
            phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowItem, true, 9, 'Calibri');
            $rowItem++;
            // daftar mesin
            foreach ($listMachine[$productGroup['product_group_code']] as $machineNo => $machineName) {
                if ($dataFilter[$productGroup['product_group_code']][$machineNo] == null) {
                    continue;
                }
                $columnItem = $startColumnItemData;

                // Menulis data mesin
                $spreadsheet->getActiveSheet()->setCellValue($columnMachineNo . $rowItem, $machineNo);
                $spreadsheet->getActiveSheet()->setCellValue($columnMachineName . $rowItem, $machineName);
                // phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);

                // memasukkan data
                $dataItem = $dataFilter[$productGroup['product_group_code']][$machineNo];

                // hari kerja
                $hariKerja = $tglMasuk->diffInDays($tglKeluar) + 1;
                $activeWorksheet->setCellValue($columnItem . $rowItem, $hariKerja);
                $columnItem++;
                // kapasitas (kg)
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->capacity_kg);
                phpSpreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // produksi (kg)
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_produksi);
                phpSpreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // rasio produksi (%)
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->capacity_kg > 0 ? $dataItem->berat_produksi / $dataItem->capacity_kg : 0);
                if ($dataItem->capacity_kg == 0) {
                    $activeWorksheet->getStyle($columnItem . $rowItem)->getNumberFormat()->setFormatCode('0;-0;"-"');
                } else {
                    phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowItem);
                }
                $columnItem++;
                // kapasitas (meter)
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->capacity_lembar);
                phpSpreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // produksi (meter)
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->panjang_produksi);
                phpSpreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // rasio produksi (%)
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->capacity_lembar > 0 ? $dataItem->panjang_produksi / $dataItem->capacity_lembar : 0);
                if ($dataItem->capacity_lembar == 0) {
                    $activeWorksheet->getStyle($columnItem . $rowItem)->getNumberFormat()->setFormatCode('0;-0;"-"');
                } else {
                    phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowItem);
                }
                phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);
                $columnItem++;

                phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem, false, 8, 'Calibri');
                $rowItem++;
            }
            // perhitungan jumlah berdasarkan departemen
            $spreadsheet->getActiveSheet()->mergeCells($columnMachineNo . $rowItem . ':' . $columnMachineName . $rowItem);
            // $activeWorksheet->setCellValue($columnMachineNo . $rowItem, 'Total ' . $department['department_name']);
            $columnItem = $startColumnItemData;
            $columnItemEnd = chr(ord($columnItem) + count($header) - 1);
            // hari kerja
            $columnItem++;
            // kapasitas (kg)
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listMachine[$productGroup['product_group_code']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // produksi (kg)
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listMachine[$productGroup['product_group_code']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // rasio produksi (%)
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listMachine[$productGroup['product_group_code']])) . ':' . $columnItem . ($rowItem - 1) . ')/COUNTIF(' . $columnItem . ($rowItem - count($listMachine[$productGroup['product_group_code']])) . ':' . $columnItem . ($rowItem - 1) . ', "<>0")');
            phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // kapasitas (meter)
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listMachine[$productGroup['product_group_code']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // produksi (meter)
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listMachine[$productGroup['product_group_code']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            phpSpreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // rasio produksi (%)
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listMachine[$productGroup['product_group_code']])) . ':' . $columnItem . ($rowItem - 1) . ')/COUNTIF(' . $columnItem . ($rowItem - count($listMachine[$productGroup['product_group_code']])) . ':' . $columnItem . ($rowItem - 1) . ', "<>0")');
            phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowItem);
            phpspreadsheet::addFullBorder($spreadsheet, $columnMachineNo . $rowItem . ':' . $columnItem . $rowItem);
            phpspreadsheet::styleFont($spreadsheet, $columnMachineNo . $rowItem . ':' . $columnItem . $rowItem, true, 8, 'Calibri');
            $columnItem++;


            $rowItem++;
            $rowItem++;
        }

        // Grand total
        $rowGrandTotal = $rowItem;
        $spreadsheet->getActiveSheet()->mergeCells($columnMachineNo . $rowGrandTotal . ':' . $columnMachineName . $rowGrandTotal);
        $spreadsheet->getActiveSheet()->setCellValue($columnMachineNo . $rowGrandTotal, 'GRAND TOTAL');
        phpspreadsheet::styleFont($spreadsheet, $columnMachineNo . $rowGrandTotal . ':' . $columnHeaderEnd . $rowGrandTotal, true, 8, 'Calibri');
        // $this->addFullBorder($spreadsheet, $columnMachineNo . $rowGrandTotal . ':' . $columnValueAvg . $rowGrandTotal);

        // grand total
        $grandTotal = [
            'panjang_produksi' => 0,
            'berat_produksi' => 0,
            'capacity_kg' => 0,
            'capacity_lembar' => 0
        ];

        foreach ($dataFilter as $productGroupCode => $productGroup) {
            foreach ($productGroup as $machineNo => $machine) {
                $grandTotal['panjang_produksi'] += $machine->panjang_produksi;
                $grandTotal['berat_produksi'] += $machine->berat_produksi;
                $grandTotal['capacity_kg'] += $machine->capacity_kg;
                $grandTotal['capacity_lembar'] += $machine->capacity_lembar;
            }
        }

        $columnItem = $startColumnItemData;
        $columnItemEnd = chr(ord($columnItem) + count($header) - 1);
        // hari kerja
        $columnItem++;
        // kapasitas (kg)
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['capacity_kg']);
        phpSpreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
        $columnItem++;
        // produksi (kg)
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_produksi']);
        phpSpreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
        $columnItem++;
        // rasio produksi (%)
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['capacity_kg'] > 0 ? $grandTotal['berat_produksi'] / $grandTotal['capacity_kg'] : 0);
        if ($grandTotal['capacity_kg'] == 0) {
            $activeWorksheet->getStyle($columnItem . $rowGrandTotal)->getNumberFormat()->setFormatCode('0;-0;"-"');
        } else {
            phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowGrandTotal);
        }
        $columnItem++;
        // kapasitas (meter)
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['capacity_lembar']);
        phpSpreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
        $columnItem++;
        // produksi (meter)
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['panjang_produksi']);
        phpSpreadsheet::numberFormatThousandsOrZero($spreadsheet, $columnItem . $rowItem);
        $columnItem++;
        // rasio produksi (%)
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['capacity_lembar'] > 0 ? $grandTotal['panjang_produksi'] / $grandTotal['capacity_lembar'] : 0);
        if ($grandTotal['capacity_lembar'] == 0) {
            $activeWorksheet->getStyle($columnItem . $rowGrandTotal)->getNumberFormat()->setFormatCode('0;-0;"-"');
        } else {
            phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowGrandTotal);
        }
        phpspreadsheet::addFullBorder($spreadsheet, $columnMachineNo . $rowGrandTotal . ':' . $columnItem . $rowGrandTotal);
        $columnItem++;

        $activeWorksheet->getStyle($columnMesin . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart)->getAlignment()->setWrapText(true);

        // auto size
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);

        $writer = new Xlsx($spreadsheet);
        $filename = 'asset/report/' . $this->nipon . '-' . $this->jenisreport . '.xlsx';
        $writer->save($filename);
        $response = [
            'status' => 'success',
            'filename' => $filename
        ];
        return $response;
    }

    public function kapasitasProduksiSeitai($tglMasuk, $tglKeluar)
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
        $activeWorksheet->setCellValue('B1', 'KAPASITAS PRODUKSI SEITAI');
        $activeWorksheet->setCellValue('B2', 'Periode : ' . $tglMasuk . ' s/d ' . $tglKeluar);
        // Style Judul
        phpspreadsheet::styleFont($spreadsheet, 'B1:B2', true, 11, 'Calibri');
        // hide column A
        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setVisible(false);

        // Header
        $columnMesin = 'B';
        $columnMesinEnd = 'C';
        $spreadsheet->getActiveSheet()->mergeCells($columnMesin . '3:' . $columnMesinEnd . '3');
        $activeWorksheet->setCellValue('B3', 'Mesin');

        // header
        $rowHeaderStart = 3;
        $columnHeaderStart = 'D';
        $columnHeaderEnd = 'D';
        $header = [
            'Hari Kerja (Hari)',
            'Kapasitas (Kg)',
            'Produksi (Kg)',
            'Rasio Produksi (%)',
            'Kapasitas (Lembar)',
            'Produksi (Lembar)',
            'Rasio Produksi (%)',
        ];

        foreach ($header as $key => $value) {
            $activeWorksheet->setCellValue($columnHeaderEnd . $rowHeaderStart, $value);
            $columnHeaderEnd++;
        }
        $columnHeaderEnd = chr(ord($columnHeaderEnd) - 1);

        // style header
        phpspreadsheet::addFullBorder($spreadsheet, $columnMesin . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);
        phpspreadsheet::styleFont($spreadsheet, $columnMesin . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart, true, 9, 'Calibri');
        phpspreadsheet::textAlignCenter($spreadsheet, $columnMesin . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);

        $data = DB::select("
            SELECT
                MAX(prGrp.code) AS product_group_code,
                MAX(prGrp.name) AS product_group_name,
                MAX(mac.machineNo) AS machine_no,
                MAX(mac.machineName) AS machine_name,
                SUM(good.qty_produksi) AS qty_produksi,
                SUM(good.qty_produksi * prd.unit_weight * 0.001) AS berat_produksi,
                MAX(mac.capacity_kg) AS capacity_kg,
                MAX(mac.capacity_lembar) AS capacity_lembar--,
                --@day AS seq_no
            FROM tdProduct_Goods AS good
            INNER JOIN msProduct AS prd ON good.product_id = prd.id
            INNER JOIN msProduct_type AS prT ON prd.product_type_id = prT.id
            INNER JOIN msProduct_group AS prGrp ON prT.product_group_id = prGrp.id
            INNER JOIN msMachine AS mac ON good.machine_id = mac.id
            WHERE good.production_date BETWEEN '$tglMasuk' AND '$tglKeluar'
            GROUP BY prGrp.name, good.machine_id;
        ");

        if (count($data) == 0) {
            $response = [
                'status' => 'error',
                'message' => "Data pada periode tanggal tersebut tidak ditemukan"
            ];

            return $response;
        }

        // list group produk
        $listProductGroup = array_reduce($data, function ($carry, $item) {
            $carry[$item->product_group_code] = [
                'product_group_code' => $item->product_group_code,
                'product_group_name' => $item->product_group_name
            ];
            return $carry;
        }, []);

        // list mesin berdasarkan tanggal pertama dan departemen
        $listMachine = array_reduce($data, function ($carry, $item) {
            if (!isset($carry[$item->product_group_code])) {
                $carry[$item->product_group_code] = [];
            }
            $carry[$item->product_group_code][$item->machine_no] = $item->machine_name;
            return $carry;
        }, []);

        $dataFilter = array_reduce($data, function ($carry, $item) {
            $carry[$item->product_group_code][$item->machine_no] = $item;
            return $carry;
        }, []);

        // index
        $startColumnItem = 'B';
        $endColumnItem = $columnHeaderEnd;
        $startColumnItemData = 'D';
        $columnMachineNo = 'B';
        $columnMachineName = 'C';
        $startRowItem = 4;
        $rowItem = $startRowItem;
        // daftar departemen
        foreach ($listProductGroup as $productGroup) {
            // Menulis data departemen
            $activeWorksheet->setCellValue($startColumnItem . $rowItem, $productGroup['product_group_name']);
            $spreadsheet->getActiveSheet()->mergeCells($startColumnItem . $rowItem . ':' . $endColumnItem . $rowItem);
            phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowItem, true, 9, 'Calibri');
            $rowItem++;
            // daftar mesin
            foreach ($listMachine[$productGroup['product_group_code']] as $machineNo => $machineName) {
                if ($dataFilter[$productGroup['product_group_code']][$machineNo] == null) {
                    continue;
                }
                $columnItem = $startColumnItemData;

                // Menulis data mesin
                $spreadsheet->getActiveSheet()->setCellValue($columnMachineNo . $rowItem, $machineNo);
                $spreadsheet->getActiveSheet()->setCellValue($columnMachineName . $rowItem, $machineName);
                // phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);

                // memasukkan data
                $dataItem = $dataFilter[$productGroup['product_group_code']][$machineNo];

                // hari kerja
                $hariKerja = $tglMasuk->diffInDays($tglKeluar) + 1;
                $activeWorksheet->setCellValue($columnItem . $rowItem, $hariKerja);
                $columnItem++;
                // kapasitas (kg)
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->capacity_kg);
                $activeWorksheet->getStyle($columnItem . $rowItem)->getNumberFormat()->setFormatCode('#,##0;-#,##0;"-"');
                $columnItem++;
                // produksi (kg)
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_produksi);
                $activeWorksheet->getStyle($columnItem . $rowItem)->getNumberFormat()->setFormatCode('#,##0;-#,##0;"-"');
                $columnItem++;
                // rasio produksi (%)
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->capacity_kg > 0 ? $dataItem->berat_produksi / $dataItem->capacity_kg : 0);
                if ($dataItem->capacity_kg == 0) {
                    $activeWorksheet->getStyle($columnItem . $rowItem)->getNumberFormat()->setFormatCode('0;-0;"-"');
                } else {
                    phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowItem);
                }
                $columnItem++;
                // kapasitas (lembar)
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->capacity_lembar);
                $activeWorksheet->getStyle($columnItem . $rowItem)->getNumberFormat()->setFormatCode('#,##0;-#,##0;"-"');
                $columnItem++;
                // produksi (lembar)
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->qty_produksi);
                $activeWorksheet->getStyle($columnItem . $rowItem)->getNumberFormat()->setFormatCode('#,##0;-#,##0;"-"');
                $columnItem++;
                // rasio produksi (%)
                $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->capacity_lembar > 0 ? $dataItem->qty_produksi / $dataItem->capacity_lembar : 0);
                if ($dataItem->capacity_lembar == 0) {
                    $activeWorksheet->getStyle($columnItem . $rowItem)->getNumberFormat()->setFormatCode('0;-0;"-"');
                } else {
                    phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowItem);
                }
                phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);
                $columnItem++;

                phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem, false, 8, 'Calibri');
                $rowItem++;
            }
            // perhitungan jumlah berdasarkan departemen
            $spreadsheet->getActiveSheet()->mergeCells($columnMachineNo . $rowItem . ':' . $columnMachineName . $rowItem);
            // $activeWorksheet->setCellValue($columnMachineNo . $rowItem, 'Total ' . $department['department_name']);
            $columnItem = $startColumnItemData;
            $columnItemEnd = chr(ord($columnItem) + count($header) - 1);
            // hari kerja
            $columnItem++;
            // kapasitas (kg)
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listMachine[$productGroup['product_group_code']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            $activeWorksheet->getStyle($columnItem . $rowItem)->getNumberFormat()->setFormatCode('#,##0;-#,##0;"-"');
            $columnItem++;
            // produksi (kg)
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listMachine[$productGroup['product_group_code']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            $activeWorksheet->getStyle($columnItem . $rowItem)->getNumberFormat()->setFormatCode('#,##0;-#,##0;"-"');
            $columnItem++;
            // rasio produksi (%)
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listMachine[$productGroup['product_group_code']])) . ':' . $columnItem . ($rowItem - 1) . ')/COUNTIF(' . $columnItem . ($rowItem - count($listMachine[$productGroup['product_group_code']])) . ':' . $columnItem . ($rowItem - 1) . ', "<>0")');
            phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowItem);
            $columnItem++;
            // kapasitas (lembar)
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listMachine[$productGroup['product_group_code']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            $activeWorksheet->getStyle($columnItem . $rowItem)->getNumberFormat()->setFormatCode('#,##0;-#,##0;"-"');
            $columnItem++;
            // produksi (lembar)
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listMachine[$productGroup['product_group_code']])) . ':' . $columnItem . ($rowItem - 1) . ')');
            $activeWorksheet->getStyle($columnItem . $rowItem)->getNumberFormat()->setFormatCode('#,##0;-#,##0;"-"');
            $columnItem++;
            // rasio produksi (%)
            $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listMachine[$productGroup['product_group_code']])) . ':' . $columnItem . ($rowItem - 1) . ')/COUNTIF(' . $columnItem . ($rowItem - count($listMachine[$productGroup['product_group_code']])) . ':' . $columnItem . ($rowItem - 1) . ', "<>0")');
            phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowItem);
            phpspreadsheet::addFullBorder($spreadsheet, $columnMachineNo . $rowItem . ':' . $columnItem . $rowItem);
            phpspreadsheet::styleFont($spreadsheet, $columnMachineNo . $rowItem . ':' . $columnItem . $rowItem, true, 8, 'Calibri');
            $columnItem++;


            $rowItem++;
            $rowItem++;
        }

        // Grand total
        $rowGrandTotal = $rowItem;
        $spreadsheet->getActiveSheet()->mergeCells($columnMachineNo . $rowGrandTotal . ':' . $columnMachineName . $rowGrandTotal);
        $spreadsheet->getActiveSheet()->setCellValue($columnMachineNo . $rowGrandTotal, 'GRAND TOTAL');
        phpspreadsheet::styleFont($spreadsheet, $columnMachineNo . $rowGrandTotal . ':' . $columnHeaderEnd . $rowGrandTotal, true, 8, 'Calibri');
        // $this->addFullBorder($spreadsheet, $columnMachineNo . $rowGrandTotal . ':' . $columnValueAvg . $rowGrandTotal);

        // grand total
        $grandTotal = [
            'qty_produksi' => 0,
            'berat_produksi' => 0,
            'capacity_kg' => 0,
            'capacity_lembar' => 0
        ];

        foreach ($dataFilter as $productGroupCode => $productGroup) {
            foreach ($productGroup as $machineNo => $machine) {
                $grandTotal['qty_produksi'] += $machine->qty_produksi;
                $grandTotal['berat_produksi'] += $machine->berat_produksi;
                $grandTotal['capacity_kg'] += $machine->capacity_kg;
                $grandTotal['capacity_lembar'] += $machine->capacity_lembar;
            }
        }

        $columnItem = $startColumnItemData;
        $columnItemEnd = chr(ord($columnItem) + count($header) - 1);
        // hari kerja
        $columnItem++;
        // kapasitas (kg)
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['capacity_kg']);
        if ($grandTotal['capacity_kg'] == 0) {
            $activeWorksheet->getStyle($columnItem . $rowGrandTotal)->getNumberFormat()->setFormatCode('0;-0;"-"');
        } else {
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        }
        $columnItem++;
        // produksi (kg)
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_produksi']);
        if ($grandTotal['berat_produksi'] == 0) {
            $activeWorksheet->getStyle($columnItem . $rowGrandTotal)->getNumberFormat()->setFormatCode('0;-0;"-"');
        } else {
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        }
        $columnItem++;
        // rasio produksi (%)
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['capacity_kg'] > 0 ? $grandTotal['berat_produksi'] / $grandTotal['capacity_kg'] : 0);
        if ($grandTotal['capacity_kg'] == 0) {
            $activeWorksheet->getStyle($columnItem . $rowGrandTotal)->getNumberFormat()->setFormatCode('0;-0;"-"');
        } else {
            phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowGrandTotal);
        }
        $columnItem++;
        // kapasitas (lembar)
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['capacity_lembar']);
        if ($grandTotal['capacity_lembar'] == 0) {
            $activeWorksheet->getStyle($columnItem . $rowGrandTotal)->getNumberFormat()->setFormatCode('0;-0;"-"');
        } else {
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        }
        $columnItem++;
        // produksi (lembar)
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['qty_produksi']);
        if ($grandTotal['qty_produksi'] == 0) {
            $activeWorksheet->getStyle($columnItem . $rowGrandTotal)->getNumberFormat()->setFormatCode('0;-0;"-"');
        } else {
            phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        }
        $columnItem++;
        // rasio produksi (%)
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['capacity_lembar'] > 0 ? $grandTotal['qty_produksi'] / $grandTotal['capacity_lembar'] : 0);
        if ($grandTotal['capacity_lembar'] == 0) {
            $activeWorksheet->getStyle($columnItem . $rowGrandTotal)->getNumberFormat()->setFormatCode('0;-0;"-"');
        } else {
            phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowGrandTotal);
        }
        phpspreadsheet::addFullBorder($spreadsheet, $columnMachineNo . $rowGrandTotal . ':' . $columnItem . $rowGrandTotal);
        $columnItem++;

        $activeWorksheet->getStyle($columnMesin . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart)->getAlignment()->setWrapText(true);

        // auto size
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);

        $writer = new Xlsx($spreadsheet);
        $filename = 'asset/report/' . $this->nipon . '-' . $this->jenisreport . '.xlsx';
        $writer->save($filename);
        $response = [
            'status' => 'success',
            'filename' => $filename
        ];
        return $response;
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
        $activeWorksheet->setCellValue('B2', 'Periode : ' . $tglMasuk . ' s/d ' . $tglKeluar);
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
        $activeWorksheet->setCellValue('B1', 'DAFTAR PRODUKSI PER MESIN PER PRODUK INFURE');
        $activeWorksheet->setCellValue('B2', 'Periode : ' . $tglMasuk . ' s/d ' . $tglKeluar);
        // Style Judul
        phpspreadsheet::styleFont($spreadsheet, 'B1:B2', true, 11, 'Calibri');
        // hide column A
        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setVisible(false);

        // Header
        $columnTipeProduk = 'A';
        $columnTipeProdukEnd = 'B';
        $spreadsheet->getActiveSheet()->mergeCells($columnTipeProduk . '3:' . $columnTipeProdukEnd . '3');
        $activeWorksheet->setCellValue('A3', 'Mesin');

        $columnMesin = 'C';
        $columnMesinEnd = 'D';
        $spreadsheet->getActiveSheet()->mergeCells($columnMesin . '3:' . $columnMesinEnd . '3');
        $activeWorksheet->setCellValue('C3', 'Produk');

        // header
        $rowHeaderStart = 3;
        $columnHeaderStart = 'E';
        $columnHeaderEnd = 'E';
        $header = [
            'Berat Standar (Kg)',
            'Berat Produksi (Kg)',
            'Weight Rate',
            'Infure Cost',
            'Loss (Kg)',
            'Loss (%)',
            'Panjang Infure (meter)',
            'Panjang Inline Printing (meter)',
            'Inline Printing Cost',
            'Process Cost',
        ];

        foreach ($header as $key => $value) {
            $activeWorksheet->setCellValue($columnHeaderEnd . $rowHeaderStart, $value);
            $columnHeaderEnd++;
        }
        // freeze pane
        $spreadsheet->getActiveSheet()->freezePane('A4');
        $columnHeaderEnd = chr(ord($columnHeaderEnd) - 1);

        // style header
        phpspreadsheet::addFullBorder($spreadsheet, $columnTipeProduk . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);
        phpspreadsheet::styleFont($spreadsheet, $columnTipeProduk . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart, true, 9, 'Calibri');
        phpspreadsheet::textAlignCenter($spreadsheet, $columnTipeProduk . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);

        $data = DB::select("
            SELECT MAX
                ( dep.NAME ) AS department_name,
                MAX ( dep.id ) AS department_id,
                MAX ( prd.NAME ) AS product_name,
                MAX ( prd.code ) AS noorder,
                MAX ( prd.id ) AS product_id,
                MAX ( mac.machineNo ) AS machine_no,
                MAX ( mac.machineName ) AS machine_name,
                SUM ( asy.berat_standard ) AS berat_standard,
                SUM ( asy.berat_produksi ) AS berat_produksi,
                SUM ( asy.infure_cost ) AS infure_cost,
                SUM ( asy.infure_berat_loss ) AS infure_berat_loss,
                SUM ( asy.panjang_produksi ) AS panjang_produksi,
                SUM ( asy.panjang_printing_inline ) AS panjang_printing_inline,
                SUM ( asy.infure_cost_printing ) AS infure_cost_printing
            FROM
                tdProduct_Assembly AS asy
                INNER JOIN msMachine AS mac ON asy.machine_id = mac.
                ID INNER JOIN msDepartment AS dep ON mac.department_id = dep.
                ID INNER JOIN msProduct AS prd ON asy.product_id = prd.ID
            WHERE
                asy.production_date BETWEEN '$tglMasuk'
                AND '$tglKeluar'
            GROUP BY
                dep.id,
                asy.machine_id,
                asy.product_id
        ");

        if (count($data) == 0) {
            $response = [
                'status' => 'error',
                'message' => "Data pada periode tanggal tersebut tidak ditemukan"
            ];

            return $response;
        }

        $listDepartment = array_reduce($data, function ($carry, $item) {
            $carry[$item->department_id] = [
                'department_id' => $item->department_id,
                'department_name' => $item->department_name
            ];
            return $carry;
        }, []);

        // list mesin berdasarkan tanggal pertama dan departemen
        $listMachine = array_reduce($data, function ($carry, $item) {
            $carry[$item->department_id][$item->machine_no] = $item->machine_name;
            return $carry;
        }, []);

        $listProduct = array_reduce($data, function ($carry, $item) {
            $carry[$item->department_id][$item->machine_no][$item->product_id] = [
                'productName' => $item->product_name,
                'productId' => $item->product_id,
                'noorder' => $item->noorder
            ];
            return $carry;
        }, []);

        $dataFilter = array_reduce($data, function ($carry, $item) {
            $carry[$item->department_id][$item->machine_no][$item->product_id] = $item;
            return $carry;
        }, []);

        // index
        $startColumnItem = 'A';
        $endColumnItem = $columnHeaderEnd;
        $startColumnItemData = 'E';
        $columnMachineNo = 'C';
        $columnMachineName = 'D';
        $startRowItem = 5;
        $rowItem = $startRowItem;
        // daftar departemen
        foreach ($listDepartment as $department) {
            // Menulis data departemen
            $activeWorksheet->setCellValue($startColumnItem . $rowItem, $department['department_name']);
            $spreadsheet->getActiveSheet()->mergeCells($startColumnItem . $rowItem . ':' . $endColumnItem . $rowItem);
            phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowItem, true, 9, 'Calibri');
            $rowItem++;

            // daftar mesin
            foreach ($listMachine[$department['department_id']] as $machineNo => $machineName) {
                $spreadsheet->getActiveSheet()->mergeCells($startColumnItem . $rowItem . ':' . $endColumnItem . $rowItem);
                $activeWorksheet->setCellValue($startColumnItem . $rowItem, $machineNo . ' - ' . $machineName);
                phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowItem, true, 8, 'Calibri');
                $rowItem++;
                // daftar mesin
                foreach ($listProduct[$department['department_id']][$machineNo] as $productId => $product) {
                    $columnItem = $startColumnItemData;

                    // Menulis data mesin
                    $spreadsheet->getActiveSheet()->setCellValue($columnMachineNo . $rowItem, $product['noorder']);
                    $spreadsheet->getActiveSheet()->setCellValue($columnMachineName . $rowItem, $product['productName']);
                    // phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);

                    // memasukkan data
                    $dataItem = $dataFilter[$department['department_id']][$machineNo][$productId] ?? (object)[
                        'berat_standard' => 0,
                        'berat_produksi' => 0,
                        'infure_cost' => 0,
                        'infure_berat_loss' => 0,
                        'panjang_produksi' => 0,
                        'panjang_printing_inline' => 0,
                        'infure_cost_printing' => 0
                    ];
                    // berat standar
                    $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_standard);
                    phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                    $columnItem++;
                    // berat produksi
                    $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_produksi);
                    phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                    $columnItem++;
                    // weight rate
                    $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_standard > 0 ? $dataItem->berat_produksi / $dataItem->berat_standard : 0);
                    phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowItem);
                    $columnItem++;
                    // infure cost
                    $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->infure_cost);
                    phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                    $columnItem++;
                    // loss
                    $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->infure_berat_loss);
                    phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                    $columnItem++;
                    // loss %
                    $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_produksi > 0 ? $dataItem->infure_berat_loss / ($dataItem->berat_produksi + $dataItem->infure_berat_loss) : 0);
                    phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowItem);
                    $columnItem++;
                    // panjang infure
                    $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->panjang_produksi);
                    phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                    $columnItem++;
                    // panjang inline printing
                    $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->panjang_printing_inline);
                    phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                    $columnItem++;
                    // inline printing cost
                    $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->infure_cost_printing);
                    phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                    $columnItem++;
                    // process cost
                    $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->infure_cost + $dataItem->infure_cost_printing);
                    phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                    phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);
                    $columnItem++;

                    phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem, false, 8, 'Calibri');
                    $rowItem++;
                }
                // perhitungan jumlah berdasarkan type produk
                $spreadsheet->getActiveSheet()->mergeCells($startColumnItem . $rowItem . ':' . $columnMachineName . $rowItem);
                $activeWorksheet->setCellValue($startColumnItem . $rowItem, 'Total');
                $columnItem = $startColumnItemData;
                $columnItemEnd = chr(ord($columnItem) + count($header) - 1);
                // berat standar
                $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listProduct[$department['department_id']][$machineNo])) . ':' . $columnItem . ($rowItem - 1) . ')');
                phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // berat produksi
                $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listProduct[$department['department_id']][$machineNo])) . ':' . $columnItem . ($rowItem - 1) . ')');
                phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // weight rate
                $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listProduct[$department['department_id']][$machineNo])) . ':' . $columnItem . ($rowItem - 1) . ')/COUNTIF(' . $columnItem . ($rowItem - count($listProduct[$department['department_id']][$machineNo])) . ':' . $columnItem . ($rowItem - 1) . ', "<>0")');
                phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // infure cost
                $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listProduct[$department['department_id']][$machineNo])) . ':' . $columnItem . ($rowItem - 1) . ')');
                phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // loss
                $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listProduct[$department['department_id']][$machineNo])) . ':' . $columnItem . ($rowItem - 1) . ')');
                phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // loss %
                $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listProduct[$department['department_id']][$machineNo])) . ':' . $columnItem . ($rowItem - 1) . ')/COUNTIF(' . $columnItem . ($rowItem - count($listProduct[$department['department_id']][$machineNo])) . ':' . $columnItem . ($rowItem - 1) . ', "<>0")');
                phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listProduct[$department['department_id']][$machineNo])) . ':' . $columnItem . ($rowItem - 1) . ')');
                phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listProduct[$department['department_id']][$machineNo])) . ':' . $columnItem . ($rowItem - 1) . ')');
                phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listProduct[$department['department_id']][$machineNo])) . ':' . $columnItem . ($rowItem - 1) . ')');
                phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listProduct[$department['department_id']][$machineNo])) . ':' . $columnItem . ($rowItem - 1) . ')');
                phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);
                $columnItem++;
                phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowItem . ':' . $columnHeaderEnd . $rowItem, true, 8, 'Calibri');

                $rowItem++;
                $rowItem++;
            }

            $rowItem++;
        }

        // Grand total
        $rowGrandTotal = $rowItem;
        $spreadsheet->getActiveSheet()->mergeCells($startColumnItem . $rowGrandTotal . ':' . $columnMachineName . $rowGrandTotal);
        $spreadsheet->getActiveSheet()->setCellValue($startColumnItem . $rowGrandTotal, 'GRAND TOTAL');
        phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowGrandTotal . ':' . $columnHeaderEnd . $rowGrandTotal, true, 8, 'Calibri');
        // $this->addFullBorder($spreadsheet, $columnMachineNo . $rowGrandTotal . ':' . $columnValueAvg . $rowGrandTotal);

        // berat standar
        $grandTotal = [
            'berat_standard' => 0,
            'berat_produksi' => 0,
            'infure_cost' => 0,
            'infure_berat_loss' => 0,
            'panjang_produksi' => 0,
            'panjang_printing_inline' => 0,
            'infure_cost_printing' => 0
        ];

        foreach ($dataFilter as $departmentId => $department) {
            foreach ($department as $machineNo => $machine) {
                foreach ($machine as $productId => $product) {
                    $grandTotal['berat_standard'] += $product->berat_standard;
                    $grandTotal['berat_produksi'] += $product->berat_produksi;
                    $grandTotal['infure_cost'] += $product->infure_cost;
                    $grandTotal['infure_berat_loss'] += $product->infure_berat_loss;
                    $grandTotal['panjang_produksi'] += $product->panjang_produksi;
                    $grandTotal['panjang_printing_inline'] += $product->panjang_printing_inline;
                    $grandTotal['infure_cost_printing'] += $product->infure_cost_printing;
                }
            }
        }

        $columnItem = $startColumnItemData;
        $columnItemEnd = chr(ord($columnItem) + count($header) - 1);
        // berat standar
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_standard']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // berat produksi
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_produksi']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // weight rate
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_standard'] > 0 ? $grandTotal['berat_produksi'] / $grandTotal['berat_standard'] : 0);
        phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // infure cost
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['infure_cost']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // loss
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['infure_berat_loss']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // loss %
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_produksi'] > 0 ? $grandTotal['infure_berat_loss'] / ($grandTotal['berat_produksi'] + $grandTotal['infure_berat_loss']) : 0);
        phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // panjang infure
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['panjang_produksi']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // panjang inline printing
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['panjang_printing_inline']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // inline printing cost
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['infure_cost_printing']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // process cost
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['infure_cost'] + $grandTotal['infure_cost_printing']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowGrandTotal . ':' . $columnItem . $rowGrandTotal);
        $columnItem++;

        // $activeWorksheet->getStyle($columnMesin . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart)->getAlignment()->setWrapText(true);

        // size auto
        $columnSizeStart = $startColumnItem;
        $columnSizeStart++;
        $columnSizeStart++;
        while ($columnSizeStart !== $columnItemEnd) {
            $spreadsheet->getActiveSheet()->getColumnDimension($columnSizeStart)->setAutoSize(true);
            $columnSizeStart++;
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

    public function daftarProduksiPerMesinPerProdukSeitai($tglMasuk, $tglKeluar)
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
        $activeWorksheet->setCellValue('A1', 'DAFTAR PRODUKSI PER MESIN PER PRODUK SEITAI');
        $activeWorksheet->setCellValue('A2', 'Periode : ' . $tglMasuk . ' s/d ' . $tglKeluar);
        // Style Judul
        phpspreadsheet::styleFont($spreadsheet, 'A1:A2', true, 11, 'Calibri');

        // Header
        $columnTipeProduk = 'A';
        $columnTipeProdukEnd = 'B';
        $spreadsheet->getActiveSheet()->mergeCells($columnTipeProduk . '3:' . $columnTipeProdukEnd . '3');
        $activeWorksheet->setCellValue('A3', 'Mesin');

        $columnMesin = 'C';
        $columnMesinEnd = 'D';
        $spreadsheet->getActiveSheet()->mergeCells($columnMesin . '3:' . $columnMesinEnd . '3');
        $activeWorksheet->setCellValue('C3', 'Produk');

        // header
        $rowHeaderStart = 3;
        $columnHeaderStart = 'E';
        $columnHeaderEnd = 'E';
        $header = [
            'Jumlah Produksi (Lembar)',
            'Berat Produksi (Kg)',
            'Loss (Kg)',
            'Loss (%)',
            'Seitai Cost',
            'Ponsu Loss (Kg)',
            'Infure Loss (Kg)',
        ];

        foreach ($header as $key => $value) {
            $activeWorksheet->setCellValue($columnHeaderEnd . $rowHeaderStart, $value);
            $columnHeaderEnd++;
        }
        // freeze pane
        $spreadsheet->getActiveSheet()->freezePane('A4');
        $columnHeaderEnd = chr(ord($columnHeaderEnd) - 1);

        // style header
        phpspreadsheet::addFullBorder($spreadsheet, $columnTipeProduk . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);
        phpspreadsheet::styleFont($spreadsheet, $columnTipeProduk . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart, true, 9, 'Calibri');
        phpspreadsheet::textAlignCenter($spreadsheet, $columnTipeProduk . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart);

        $divisionCodeSeitai = '20';
        $data = DB::select("
            SELECT
                MAX(dep.name) AS department_name,
                MAX(dep.id) AS department_id,
                MAX(prd.id) AS product_id,
                MAX(prd.code) AS noorder,
                MAX(prd.name) AS product_name,
                MAX(mac.machineNo) AS machine_no,
                MAX(mac.machineName) AS machine_name,
                SUM(good.qty_produksi) AS qty_produksi,
                SUM(good.qty_produksi * prd.unit_weight * 0.001) AS berat_produksi,
                SUM(good.qty_produksi * prT.harga_sat_seitai) AS seitai_cost,
                SUM(good.seitai_berat_loss) - COALESCE(SUM(ponsu.berat_loss), 0) AS seitai_berat_loss,
                COALESCE(SUM(ponsu.berat_loss), 0) AS seitai_berat_loss_ponsu,
                SUM(good.infure_berat_loss) AS infure_berat_loss
            FROM tdProduct_Goods AS good
            LEFT JOIN (
                SELECT
                    los_.product_goods_id,
                    SUM(los_.berat_loss) AS berat_loss
                FROM tdProduct_Goods_Loss AS los_
                WHERE los_.loss_seitai_id = 1
                GROUP BY los_.product_goods_id
            ) ponsu ON good.id = ponsu.product_goods_id
            INNER JOIN msMachine AS mac ON good.machine_id = mac.id
            INNER JOIN msDepartment AS dep ON mac.department_id = dep.id
            INNER JOIN msProduct AS prd ON good.product_id = prd.id
            INNER JOIN msProduct_type AS prT ON prd.product_type_id = prT.id
            WHERE good.production_date BETWEEN '$tglMasuk' AND '$tglKeluar'
            AND (dep.division_code = '$divisionCodeSeitai')
            GROUP BY dep.id, good.machine_id, prd.name;
        ");

        if (count($data) == 0) {
            $response = [
                'status' => 'error',
                'message' => "Data pada periode tanggal tersebut tidak ditemukan"
            ];

            return $response;
        }

        $listDepartment = array_reduce($data, function ($carry, $item) {
            $carry[$item->department_id] = [
                'department_id' => $item->department_id,
                'department_name' => $item->department_name
            ];
            return $carry;
        }, []);

        // list mesin berdasarkan tanggal pertama dan departemen
        $listMachine = array_reduce($data, function ($carry, $item) {
            $carry[$item->department_id][$item->machine_no] = $item->machine_name;
            return $carry;
        }, []);

        $listProduct = array_reduce($data, function ($carry, $item) {
            $carry[$item->department_id][$item->machine_no][$item->product_id] = [
                'productName' => $item->product_name,
                'productId' => $item->product_id,
                'noorder' => $item->noorder
            ];
            return $carry;
        }, []);

        $dataFilter = array_reduce($data, function ($carry, $item) {
            $carry[$item->department_id][$item->machine_no][$item->product_id] = $item;
            return $carry;
        }, []);

        // index
        $startColumnItem = 'A';
        $endColumnItem = $columnHeaderEnd;
        $startColumnItemData = 'E';
        $columnMachineNo = 'C';
        $columnMachineName = 'D';
        $startRowItem = 5;
        $rowItem = $startRowItem;
        // daftar departemen
        foreach ($listDepartment as $department) {
            // Menulis data departemen
            $activeWorksheet->setCellValue($startColumnItem . $rowItem, $department['department_name']);
            $spreadsheet->getActiveSheet()->mergeCells($startColumnItem . $rowItem . ':' . $endColumnItem . $rowItem);
            phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowItem, true, 9, 'Calibri');
            $rowItem++;

            // daftar mesin
            foreach ($listMachine[$department['department_id']] as $machineNo => $machineName) {
                $spreadsheet->getActiveSheet()->mergeCells($startColumnItem . $rowItem . ':' . $endColumnItem . $rowItem);
                $activeWorksheet->setCellValue($startColumnItem . $rowItem, $machineNo . ' - ' . $machineName);
                phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowItem, true, 8, 'Calibri');
                $rowItem++;
                // daftar mesin
                foreach ($listProduct[$department['department_id']][$machineNo] as $productId => $product) {
                    $columnItem = $startColumnItemData;

                    // Menulis data mesin
                    $spreadsheet->getActiveSheet()->setCellValue($columnMachineNo . $rowItem, $product['noorder']);
                    $spreadsheet->getActiveSheet()->setCellValue($columnMachineName . $rowItem, $product['productName']);
                    // phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);

                    // memasukkan data
                    $dataItem = $dataFilter[$department['department_id']][$machineNo][$productId] ?? (object)[
                        'qty_produksi' => 0,
                        'berat_produksi' => 0,
                        'seitai_cost' => 0,
                        'seitai_berat_loss' => 0,
                        'seitai_berat_loss_ponsu' => 0,
                        'infure_berat_loss' => 0
                    ];
                    // jumlah produksi
                    $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->qty_produksi);
                    phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                    $columnItem++;
                    // berat produksi
                    $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_produksi);
                    phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                    $columnItem++;
                    // loss
                    $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->seitai_berat_loss);
                    phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                    $columnItem++;
                    // loss %
                    $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->berat_produksi > 0 ? $dataItem->seitai_berat_loss / ($dataItem->berat_produksi + $dataItem->seitai_berat_loss) : 0);
                    phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowItem);
                    $columnItem++;
                    // Seitai cost
                    $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->seitai_cost);
                    phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                    $columnItem++;
                    // Ponsu Loss
                    $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->seitai_berat_loss_ponsu);
                    phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                    $columnItem++;
                    // Infure Loss
                    $activeWorksheet->setCellValue($columnItem . $rowItem, $dataItem->infure_berat_loss);
                    phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                    phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);
                    $columnItem++;

                    phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem, false, 8, 'Calibri');
                    $rowItem++;
                }
                // perhitungan jumlah total
                $spreadsheet->getActiveSheet()->mergeCells($startColumnItem . $rowItem . ':' . $columnMachineName . $rowItem);
                $activeWorksheet->setCellValue($startColumnItem . $rowItem, 'Total');
                $columnItem = $startColumnItemData;
                $columnItemEnd = chr(ord($columnItem) + count($header) - 1);
                // jumlah produksi
                $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listProduct[$department['department_id']][$machineNo])) . ':' . $columnItem . ($rowItem - 1) . ')');
                phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // berat produksi
                $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listProduct[$department['department_id']][$machineNo])) . ':' . $columnItem . ($rowItem - 1) . ')');
                phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // loss
                $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listProduct[$department['department_id']][$machineNo])) . ':' . $columnItem . ($rowItem - 1) . ')');
                phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // loss %
                $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listProduct[$department['department_id']][$machineNo])) . ':' . $columnItem . ($rowItem - 1) . ')/COUNTIF(' . $columnItem . ($rowItem - count($listProduct[$department['department_id']][$machineNo])) . ':' . $columnItem . ($rowItem - 1) . ', "<>0")');
                phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // Seitai cost
                $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listProduct[$department['department_id']][$machineNo])) . ':' . $columnItem . ($rowItem - 1) . ')');
                phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // Ponsu Loss
                $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listProduct[$department['department_id']][$machineNo])) . ':' . $columnItem . ($rowItem - 1) . ')');
                phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                $columnItem++;
                // Infure Loss
                $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowItem, '=SUM(' . $columnItem . ($rowItem - count($listProduct[$department['department_id']][$machineNo])) . ':' . $columnItem . ($rowItem - 1) . ')');
                phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowItem);
                phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowItem . ':' . $columnItem . $rowItem);
                $columnItem++;
                phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowItem . ':' . $columnHeaderEnd . $rowItem, true, 8, 'Calibri');

                $rowItem++;
            }

            $rowItem++;
        }

        // Grand total
        $rowGrandTotal = $rowItem;
        $spreadsheet->getActiveSheet()->mergeCells($startColumnItem . $rowGrandTotal . ':' . $columnMachineName . $rowGrandTotal);
        $spreadsheet->getActiveSheet()->setCellValue($startColumnItem . $rowGrandTotal, 'GRAND TOTAL');
        phpspreadsheet::styleFont($spreadsheet, $startColumnItem . $rowGrandTotal . ':' . $columnHeaderEnd . $rowGrandTotal, true, 8, 'Calibri');
        // $this->addFullBorder($spreadsheet, $columnMachineNo . $rowGrandTotal . ':' . $columnValueAvg . $rowGrandTotal);

        // berat standar
        $grandTotal = [
            'qty_produksi' => 0,
            'berat_produksi' => 0,
            'seitai_cost' => 0,
            'seitai_berat_loss' => 0,
            'seitai_berat_loss_ponsu' => 0,
            'infure_berat_loss' => 0
        ];

        foreach ($dataFilter as $departmentId => $department) {
            foreach ($department as $machineNo => $machine) {
                foreach ($machine as $productId => $product) {
                    $grandTotal['qty_produksi'] += $product->qty_produksi;
                    $grandTotal['berat_produksi'] += $product->berat_produksi;
                    $grandTotal['seitai_cost'] += $product->seitai_cost;
                    $grandTotal['seitai_berat_loss'] += $product->seitai_berat_loss;
                    $grandTotal['seitai_berat_loss_ponsu'] += $product->seitai_berat_loss_ponsu;
                    $grandTotal['infure_berat_loss'] += $product->infure_berat_loss;
                }
            }
        }

        $columnItem = $startColumnItemData;
        $columnItemEnd = chr(ord($columnItem) + count($header) - 1);
        // jumlah produksi
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['qty_produksi']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // berat produksi
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_produksi']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // loss
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['seitai_berat_loss']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // loss %
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['berat_produksi'] > 0 ? $grandTotal['seitai_berat_loss'] / ($grandTotal['berat_produksi'] + $grandTotal['seitai_berat_loss']) : 0);
        phpspreadsheet::numberPercentage($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // Seitai cost
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['seitai_cost']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // Ponsu Loss
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['seitai_berat_loss_ponsu']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        $columnItem++;
        // Infure Loss
        $spreadsheet->getActiveSheet()->setCellValue($columnItem . $rowGrandTotal, $grandTotal['infure_berat_loss']);
        phpspreadsheet::numberFormatCommaSeparated($spreadsheet, $columnItem . $rowGrandTotal);
        phpspreadsheet::addFullBorder($spreadsheet, $startColumnItem . $rowGrandTotal . ':' . $columnItem . $rowGrandTotal);
        $columnItem++;

        // $activeWorksheet->getStyle($columnMesin . $rowHeaderStart . ':' . $columnHeaderEnd . $rowHeaderStart)->getAlignment()->setWrapText(true);

        // size auto
        $columnSizeStart = $startColumnItem;
        $columnSizeStart++;
        $columnSizeStart++;
        while ($columnSizeStart !== $columnItemEnd) {
            $spreadsheet->getActiveSheet()->getColumnDimension($columnSizeStart)->setAutoSize(true);
            $columnSizeStart++;
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

    public function jamMatiPerMesin($tglMasuk, $tglKeluar)
    {
        return JamMatiPerMesinService::jamMatiPerMesin($this->nipon, $this->jenisreport, $tglMasuk, $tglKeluar);
    }

    public function jamMatiPerJenis($tglMasuk, $tglKeluar)
    {
        return JamMatiPerMesinService::jamMatiPerJenis($this->nipon, $this->jenisreport, $tglMasuk, $tglKeluar);
    }

    public function render()
    {
        return view('livewire.report.general-report')->extends('layouts.master');
    }
}
