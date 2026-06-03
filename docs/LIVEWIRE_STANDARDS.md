# Standard Pattern: Livewire List Page

Dokumen ini mendefinisikan pola standar untuk semua halaman Livewire yang menampilkan daftar data dengan filter, dropdown, dan tabel. Referensi utama: `nippo-infure` dan `nippo-seitai`.

---

## Status Implementasi

| Halaman | Controller | Status |
|---|---|---|
| `/nippo-infure` | `NippoInfure/NippoInfureController.php` | ✅ Selesai |
| `/nippo-seitai` | `NippoSeitai/NippoSeitaiController.php` | ✅ Selesai |
| `/kenpin-seitai` | `Kenpin/KenpinSeitaiController.php` | ⏳ Belum |
| `/kenpin-infure` | `Kenpin/KenpinInfureController.php` | ⏳ Belum |
| `/jam-kerja/check-list` | `JamKerja/CheckListJamKerjaController.php` | ⏳ Belum |
| `/jam-kerja/infure` | `JamKerja/InfureJamKerjaController.php` | ⏳ Belum |
| `/jam-kerja/seitai` | `JamKerja/SeitaiJamKerjaController.php` | ⏳ Belum |

---

## Arsitektur Umum

```
Browser ──→ /livewire/update ──→ Controller::render()
                                      │
                                 Cache::remember()    ← produk, mesin (1 jam)
                                      │
                                 DB query + filter    ← nilai scalar dari #[Session]
                                      │
                                 ->orderBy()->paginate($perPage)
                                      │
                                 return view(...)     → Blade template
                                      │
                          Select2 + Alpine.js (JS)   ← reinit setelah morph
```

---

## BAGIAN 1 — CONTROLLER

### 1.1 Template Lengkap

```php
<?php

namespace App\Http\Livewire\NamaModule;

use Livewire\Component;
use Carbon\Carbon;
use App\Models\MsProduct;
use App\Models\MsMachine;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;
use Livewire\Attributes\Session;
use App\Traits\HandlesHeavyJob;

class NamaController extends Component
{
    use HandlesHeavyJob;
    use WithPagination, WithoutUrlPagination;

    protected $paginationTheme = 'bootstrap';

    // ── Filter state (scalar, bukan array) ─────────────────────────────
    #[Session] public $tglMasuk;
    #[Session] public $tglKeluar;
    #[Session] public $transaksi;
    #[Session] public $machineId;
    #[Session] public $idProduct;
    #[Session] public $status;
    #[Session] public $searchTerm;
    #[Session] public $lpk_no;

    // ── Pagination & sorting ────────────────────────────────────────────
    #[Session] public $perPage = 10;
    #[Session] public $sortColumn = 'tabel.created_on';  // sesuaikan
    #[Session] public $sortDirection = 'desc';

    // ── JANGAN deklarasikan $products / $machine sebagai public property ─
    // public $products;  ← masuk Livewire snapshot, overhead besar

    public function mount()
    {
        $this->shouldForgetSession();

        // Normalisasi legacy choices.js {value: x} → scalar
        if (is_array($this->idProduct)) { $this->idProduct = $this->idProduct['value'] ?? null; }
        if (is_array($this->machineId)) { $this->machineId = $this->machineId['value'] ?? null; }
        if (is_array($this->status))    { $this->status    = $this->status['value']    ?? null; }

        if (empty($this->transaksi)) { $this->transaksi = 1; }
        if (empty($this->tglMasuk))  { $this->tglMasuk  = Carbon::now()->format('d M Y'); }
        if (empty($this->tglKeluar)) { $this->tglKeluar = Carbon::now()->format('d M Y'); }
    }

    protected function shouldForgetSession()
    {
        $prev = last(explode('/', url()->previous()));
        if (!(Str::contains($prev, 'edit-NAMA') || Str::contains($prev, 'add-NAMA') || Str::contains($prev, 'nippo-NAMA'))) {
            $this->reset('tglMasuk', 'tglKeluar', 'transaksi', 'machineId', 'idProduct',
                         'status', 'searchTerm', 'lpk_no', 'perPage', 'sortColumn', 'sortDirection');
        }
    }

    public function search()
    {
        $this->resetPage();
    }

    public function sortBy($column)
    {
        $allowed = [
            // Daftarkan kolom yang boleh di-sort
            'tabel.created_on', 'tabel.production_date', 'tabel.updated_on',
            'tdol.lpk_no', 'tdol.lpk_date',
            'mp.name', 'msm.machineno',
            // tambah sesuai kebutuhan halaman
        ];
        if (!in_array($column, $allowed)) return;

        if ($this->sortColumn === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortColumn    = $column;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function export()
    {
        $this->startHeavyJob();
        $filter = [
            'machineId'  => $this->machineId  ?? null,  // scalar ✅
            'idProduct'  => $this->idProduct  ?? null,  // scalar ✅
            'status'     => $this->status     ?? null,  // scalar ✅
            'searchTerm' => $this->searchTerm ?? null,
            'lpk_no'     => $this->lpk_no     ?? null,
            'transaksi'  => $this->transaksi  ?? 1,
            // ❌ JANGAN: $this->machineId['value'] ?? null
        ];
        // ... panggil controller export
    }

    public function render()
    {
        $tglAwal  = Carbon::parse($this->tglMasuk)->format('d M Y') . ' 00:00:00';
        $tglAkhir = Carbon::parse($this->tglKeluar)->format('d M Y') . ' 23:59:59';

        try {
            $data = DB::table('tabel_utama AS t')
                ->select([
                    't.id AS id',
                    // ... kolom lain
                    'mp.name AS product_name',
                    'msm.machineno',
                ])
                ->join('tdorderlpk AS tdol', 't.lpk_id', '=', 'tdol.id')
                ->leftJoin('msproduct AS mp', 'mp.id', '=', 'tdol.product_id')
                ->join('msmachine AS msm', 'msm.id', '=', 't.machine_id');

            // Filter tanggal
            if (!empty($this->tglMasuk)) {
                $data->where('t.production_date', '>=', $tglAwal);
            }
            if (!empty($this->tglKeluar)) {
                $data->where('t.production_date', '<=', $tglAkhir);
            }

            // Filter scalar — cukup !empty(), tidak perlu ['value']
            if (!empty($this->machineId) && $this->machineId != "undefined") {
                $data->where('msm.id', $this->machineId);
            }
            if (!empty($this->idProduct) && $this->idProduct != "undefined") {
                $data->where('t.product_id', $this->idProduct);
            }

            // Status: perlu !== null karena nilai 0 (Open) adalah valid
            if (isset($this->status) && $this->status !== "" && $this->status !== null) {
                if ($this->status == 0)     { $data->where('t.status', 0); }
                elseif ($this->status == 1) { $data->where('t.status', 1); }
                elseif ($this->status == 2) { $data->where('t.status', 2); }
            }

            if (!empty($this->searchTerm)) {
                $data->where(function ($q) {
                    $q->where('t.production_no', 'ilike', "%{$this->searchTerm}%")
                      ->orWhere('mp.name',        'ilike', "%{$this->searchTerm}%")
                      ->orWhere('mp.code',        'ilike', "%{$this->searchTerm}%");
                });
            }

            $data = $data->orderBy($this->sortColumn, $this->sortDirection)
                         ->paginate($this->perPage);

        } catch (\Exception $e) {
            $data = new \Illuminate\Pagination\LengthAwarePaginator([], 0, $this->perPage);
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
        }

        // Master data: cache 1 jam, select kolom minimal saja
        $products = Cache::remember('ms_products_NAMA', 3600, fn() =>
            MsProduct::select(['id', 'name', 'code'])->orderBy('code')->get()
        );
        $machine = Cache::remember('ms_machines_NAMA', 3600, fn() =>
            MsMachine::select(['id', 'machineno'])
                ->whereIn('department_id', [10, 12])  // sesuaikan per halaman
                ->orderBy('machineno')->get()
        );

        return view('livewire.NAMA.NAMA', [
            'data'     => $data,
            'products' => $products,
            'machine'  => $machine,
        ])->extends('layouts.master');
    }
}
```

---

## BAGIAN 2 — BLADE TEMPLATE

### 2.1 Template Lengkap

Ganti semua `NAMA` dengan nama halaman (e.g., `kenpin-seitai`, `kenpin-infure`).
Ganti semua `N` pada `cols[N]` sesuai jumlah kolom tabel.

```blade
<div>
    {{-- ① Loading bar gradient — muncul saat Livewire request --}}
    <div wire:loading.delay class="position-fixed"
         style="top:0;left:0;width:100%;height:3px;
                background:linear-gradient(90deg,#0ab39c,#405189,#0ab39c);
                background-size:200%;animation:nippo-bar-slide 1.5s linear infinite;z-index:99999;">
    </div>

    {{-- ② Navigation overlay — muncul saat berpindah halaman (edit/add) --}}
    <div id="NAMA-nav-overlay"
         class="d-none position-fixed top-0 start-0 w-100 h-100 justify-content-center align-items-center"
         style="background:rgba(255,255,255,0.75);z-index:99998;">
        <div class="spinner-border text-primary" style="width:3rem;height:3rem;" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    {{-- ③ Filter section --}}
    <div class="row filter-section">
        <div class="col-12 col-lg-7">
            <div class="row">
                {{-- Tanggal --}}
                <div class="col-12 col-lg-3">
                    <label class="form-label text-muted fw-bold">Filter Tanggal</label>
                </div>
                <div class="col-12 col-lg-9 mb-1">
                    <div class="input-group">
                        <div class="col-3">
                            <select class="form-select" style="padding:0.44rem" wire:model.defer="transaksi">
                                <option value="1">Produksi</option>
                                <option value="2">Proses</option>
                            </select>
                        </div>
                        <div class="col-9">
                            <div class="input-group">
                                <input wire:model.defer="tglMasuk" type="text" class="form-control"
                                    style="padding:0.44rem" data-provider="flatpickr" data-date-format="d M Y">
                                <span class="input-group-text py-0"><i class="ri-calendar-event-fill fs-4"></i></span>
                                <input wire:model.defer="tglKeluar" type="text" class="form-control"
                                    style="padding:0.44rem" data-provider="flatpickr" data-date-format="d M Y">
                                <span class="input-group-text py-0"><i class="ri-calendar-event-fill fs-4"></i></span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Nomor LPK dengan auto-format 000000-000 --}}
                <div class="col-12 col-lg-3">
                    <label class="form-label text-muted fw-bold">Nomor LPK</label>
                </div>
                <div class="col-12 col-lg-9 mb-1" x-data="{
                    lpk_no_local: @entangle('lpk_no'),
                    status: true,
                    formatValue(value) {
                        if (value.length === 6 && !value.includes('-') && this.status) { value += '-'; }
                        if (value.length < 6) this.status = true;
                        if (value.length === 7) this.status = false;
                        if (value.length > 10) value = value.substring(0, 10);
                        return value;
                    }
                }" x-defer>
                    <input class="form-control" style="padding:0.44rem" type="text" placeholder="000000-000"
                        x-model="lpk_no_local" x-on:input="lpk_no_local = formatValue(lpk_no_local)" maxlength="10" />
                </div>

                {{-- Search --}}
                <div class="col-12 col-lg-3">
                    <label class="form-label text-muted fw-bold">Search</label>
                </div>
                <div class="col-12 col-lg-9">
                    <input wire:model.defer="searchTerm" class="form-control" style="padding:0.44rem"
                        type="text" placeholder="search nomor produksi, produk, dll" />
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-5">
            <div class="row">
                {{-- Product — select2, wire:ignore --}}
                <div class="col-12 col-lg-2">
                    <label class="form-label text-muted fw-bold">Product</label>
                </div>
                <div class="col-12 col-lg-10">
                    <div class="mb-1" wire:ignore>
                        <select class="form-control select2-product-NAMA">
                            <option value="">- All -</option>
                            @foreach ($products as $item)
                                <option value="{{ $item->id }}"
                                    @if ($item->id == ($idProduct ?? null)) selected @endif>
                                    {{ $item->name }}, {{ $item->code }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Machine — select2, wire:ignore --}}
                <div class="col-12 col-lg-2">
                    <label class="form-label text-muted fw-bold">Mesin</label>
                </div>
                <div class="col-12 col-lg-10">
                    <div class="mb-1" wire:ignore>
                        <select class="form-control select2-machine-NAMA">
                            <option value="">- All -</option>
                            @foreach ($machine as $item)
                                <option value="{{ $item->id }}"
                                    @if ($item->id == ($machineId ?? null)) selected @endif>
                                    {{ $item->machineno }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Status — select2, wire:ignore --}}
                <div class="col-12 col-lg-2">
                    <label class="form-label text-muted fw-bold">Status</label>
                </div>
                <div class="col-12 col-lg-10">
                    <div class="mb-1" wire:ignore>
                        <select class="form-control select2-status-NAMA">
                            <option value="">- All -</option>
                            <option value="0">Open</option>
                            <option value="1" @if (($status ?? null) == 1) selected @endif>Seitai</option>
                            <option value="2" @if (($status ?? null) == 2) selected @endif>Kenpin</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tombol aksi --}}
        <div class="col-lg-10 mt-2">
            <button wire:click="search" type="button" class="btn btn-primary btn-load w-lg p-1"
                wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="search">
                    <i class="ri-search-line"></i> Filter
                </span>
                <div wire:loading wire:target="search">
                    <span class="d-flex align-items-center">
                        <span class="spinner-border flex-shrink-0" role="status"></span>
                        <span class="flex-grow-1 ms-1">Loading...</span>
                    </span>
                </div>
            </button>
            <button type="button" class="btn btn-success w-lg p-1"
                onclick="window.location.href='/add-NAMA?lpk_no={{ $lpk_no }}'">
                <i class="ri-add-line"></i> Add
            </button>
        </div>
        <div class="col-lg-2 mt-2 text-end">
            <button class="btn btn-info w-lg p-1" wire:click="export" type="button"
                wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="export">
                    <i class="ri-printer-line"></i> Print
                </span>
                <div wire:loading wire:target="export">
                    <span class="d-flex align-items-center">
                        <span class="spinner-border flex-shrink-0" role="status"></span>
                        <span class="flex-grow-1 ms-1"></span>
                    </span>
                </div>
            </button>
        </div>
    </div>

    {{-- ④ Tabel dengan Alpine.js column toggle --}}
    <div x-data="{
        cols: {1:true, 2:false, 3:true, 4:true, 5:true, 6:true, 7:false, 8:false}
        {{-- Sesuaikan: true = tampil default, false = tersembunyi default --}}
        {{-- Indeks mulai 1, cocokkan dengan urutan kolom di thead/tbody --}}
    }" class="mt-2 mb-2">

        {{-- Header baris: per-page selector + column toggle button --}}
        <div class="d-flex justify-content-between align-items-center mb-1 flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <label class="text-muted small mb-0">Show</label>
                <select wire:model.live="perPage" class="form-select form-select-sm" style="width:auto">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
                <label class="text-muted small mb-0">entries</label>
            </div>
            <div class="dropdown">
                <button type="button" data-bs-toggle="dropdown" aria-expanded="false"
                    class="btn btn-soft-primary btn-icon fs-14">
                    <i class="ri-grid-fill"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><label style="cursor:pointer"><input class="form-check-input fs-15 ms-2" type="checkbox" x-model="cols[1]"> Kolom 1</label></li>
                    <li><label style="cursor:pointer"><input class="form-check-input fs-15 ms-2" type="checkbox" x-model="cols[2]"> Kolom 2</label></li>
                    {{-- ... tambah sesuai jumlah kolom --}}
                </ul>
            </div>
        </div>

        {{-- Tabel: opacity saat loading --}}
        <div wire:loading.class="opacity-50"
             wire:target="search,sortBy,gotoPage,nextPage,previousPage,perPage"
             style="overflow-x:auto; overflow-y:auto; max-height:65vh; transition: opacity 0.15s;">
            <table class="table align-middle table-nowrap table-hover" id="tableNAMA">
                <thead class="table-light">
                    <tr>
                        <th style="width:36px"></th>

                        {{-- Kolom sortable: wire:click="sortBy('...')" --}}
                        <th :class="{'d-none': !cols[1]}"
                            wire:click="sortBy('tabel.kolom1')"
                            style="cursor:pointer;white-space:nowrap">
                            Nama Kolom 1
                            <i class="{{ $sortColumn === 'tabel.kolom1'
                                ? ($sortDirection === 'asc' ? 'ri-arrow-up-s-line text-primary' : 'ri-arrow-down-s-line text-primary')
                                : 'ri-expand-up-down-line text-muted' }} fs-12"></i>
                        </th>

                        {{-- Kolom tidak sortable (computed/formula) --}}
                        <th :class="{'d-none': !cols[2]}">Nama Kolom 2</th>

                        {{-- ... tambah kolom lain --}}
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data as $item)
                        <tr>
                            <td>
                                <a href="/edit-NAMA?orderId={{ $item->id }}"
                                    class="link-success fs-15 p-1 bg-primary rounded">
                                    <i class="ri-edit-box-line text-white"></i>
                                </a>
                            </td>
                            <td :class="{'d-none': !cols[1]}">{{ $item->kolom1 }}</td>
                            <td :class="{'d-none': !cols[2]}">{{ $item->kolom2 }}</td>
                            {{-- ... --}}
                        </tr>
                    @empty
                        <tr>
                            <td colspan="JUMLAH_KOLOM" class="text-center py-4">
                                <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop"
                                    colors="primary:#121331,secondary:#08a88a"
                                    style="width:40px;height:40px"></lord-icon>
                                <h5 class="mt-2">Record not Found..!</h5>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination info + links --}}
        <div class="d-flex justify-content-between align-items-center flex-wrap mt-2 gap-2">
            <div class="text-muted small">
                @if ($data instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    Showing {{ $data->firstItem() ?? 0 }}–{{ $data->lastItem() ?? 0 }}
                    of {{ $data->total() }} entries
                @endif
            </div>
            <div>
                @if ($data instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    {{ $data->links() }}
                @endif
            </div>
        </div>
    </div>

    <style>
        @keyframes nippo-bar-slide {
            0%   { background-position: 0% 0%; }
            100% { background-position: 200% 0%; }
        }
        #tableNAMA.table>:not(caption)>*>* {
            font-size: 13px !important;
            padding: 4px 2px 4px 4px;
            color: var(--tb-table-color-state, var(--tb-table-color-type, var(--tb-table-color)));
            background-color: var(--tb-table-bg);
            border-bottom-width: var(--tb-border-width);
            box-shadow: inset 0 0 0 9999px var(--tb-table-bg-state, var(--tb-table-bg-type, var(--tb-table-accent-bg)));
        }
    </style>
</div>

@script
<script>
    // ── Select2: satu fungsi per dropdown ──────────────────────────────
    function initProductSelect() {
        if ($('.select2-product-NAMA').hasClass('select2-hidden-accessible')) {
            $('.select2-product-NAMA').select2('destroy');
        }
        $('.select2-product-NAMA').select2({
            theme: 'bootstrap-5', allowClear: true, placeholder: '- All -',
        }).on('change', function () {
            @this.set('idProduct', $(this).val() || null);
        });
    }

    function initMachineSelect() {
        if ($('.select2-machine-NAMA').hasClass('select2-hidden-accessible')) {
            $('.select2-machine-NAMA').select2('destroy');
        }
        $('.select2-machine-NAMA').select2({
            theme: 'bootstrap-5', allowClear: true, placeholder: '- All -',
        }).on('change', function () {
            @this.set('machineId', $(this).val() || null);
        });
    }

    function initStatusSelect() {
        if ($('.select2-status-NAMA').hasClass('select2-hidden-accessible')) {
            $('.select2-status-NAMA').select2('destroy');
        }
        $('.select2-status-NAMA').select2({
            theme: 'bootstrap-5', allowClear: true, placeholder: '- All -',
        }).on('change', function () {
            @this.set('status', $(this).val() || null);
        });
    }

    document.addEventListener('livewire:initialized', function () {
        initProductSelect();
        initMachineSelect();
        initStatusSelect();

        // Reinit setelah Livewire update DOM (morph)
        Livewire.hook('morph', ({ el, component }) => {
            setTimeout(() => {
                initProductSelect();
                initMachineSelect();
                initStatusSelect();
            }, 100);
        });

        // Tampilkan overlay saat navigasi ke halaman lain
        window.addEventListener('beforeunload', function () {
            var overlay = document.getElementById('NAMA-nav-overlay');
            if (overlay) {
                overlay.classList.remove('d-none');
                overlay.classList.add('d-flex');
            }
        });
    });
</script>
@endscript
```

---

## BAGIAN 3 — CHECKLIST CEPAT

### Controller

- [ ] `use WithPagination, WithoutUrlPagination;`
- [ ] `#[Session]` untuk semua filter + `$perPage`, `$sortColumn`, `$sortDirection`
- [ ] Tidak ada `public $products` / `public $machine` sebagai class property
- [ ] `mount()`: normalisasi `is_array()` untuk idProduct, machineId, status
- [ ] `search()`: hanya `$this->resetPage();`
- [ ] `sortBy()`: whitelist kolom, toggle asc/desc, resetPage
- [ ] `render()`: bungkus semua query dalam `try/catch`
- [ ] Filter pakai `!empty($this->field)` bukan `$this->field['value']`
- [ ] Status pakai `isset() && !== "" && !== null` (karena nilai 0 valid)
- [ ] `Cache::remember('key_unik_halaman', 3600, ...)` untuk produk & mesin
- [ ] `->orderBy($this->sortColumn, $this->sortDirection)->paginate($this->perPage)`
- [ ] Fallback: `new LengthAwarePaginator([], 0, $this->perPage)`
- [ ] `export()`: pakai `$this->field ?? null` (bukan `['value']`)
- [ ] `export()`: panggil `$this->startHeavyJob()` di baris pertama

### Blade

- [ ] Loading bar gradient dengan `wire:loading.delay` di baris pertama `<div>`
- [ ] Navigation overlay `id="NAMA-nav-overlay"` class `d-none`
- [ ] Semua dropdown: hapus `data-choices`, `wire:model.defer` → ganti class `select2-xxx-NAMA`
- [ ] Wrapper dropdown: `wire:ignore`
- [ ] Kondisi selected: `@if ($item->id == ($field ?? null)) selected @endif`
- [ ] Status selected: `@if (($status ?? null) == N) selected @endif`
- [ ] `x-data="{ cols: {1:true, 2:false, ...} }"` — toggle kolom via Alpine.js
- [ ] Per-page: `<select wire:model.live="perPage">`
- [ ] Column toggle: `<input type="checkbox" x-model="cols[N]">`
- [ ] Tabel header sortable: `wire:click="sortBy('...')"` + ikon kondisional
- [ ] Tabel wrapper: `wire:loading.class="opacity-50"` + `wire:target="search,sortBy,gotoPage,nextPage,previousPage,perPage"`
- [ ] Row `@forelse` dengan `@empty` berisi lord-icon
- [ ] Setiap `<td>`: `:class="{'d-none': !cols[N]}"`
- [ ] Pagination: `{{ $data->links() }}` + counter "Showing X–Y of Z"
- [ ] CSS: `@keyframes nippo-bar-slide` + style tabel compact (font 13px, padding 4px)
- [ ] `@script`: `initProductSelect()`, `initMachineSelect()`, `initStatusSelect()`
- [ ] Event `livewire:initialized` → init semua select2
- [ ] `Livewire.hook('morph')` dengan `setTimeout(..., 100)` → reinit semua select2
- [ ] Event `beforeunload` → show overlay

---

## BAGIAN 4 — RULES & PENJELASAN

### ❌ Pola Lama (choices.js) — Jangan Dipakai

```php
// Controller
if ($this->machineId['value'] != "") { ... }        // error jika nilai sudah scalar
'machineId' => $this->machineId['value'] ?? null,   // sama
```
```html
<!-- Blade -->
<select wire:model.defer="machineId" data-choices>  <!-- choices.js format array -->
@if ($machineId['value'] == 1) selected @endif      <!-- error jika scalar -->
```

### ✅ Pola Baru (select2) — Standar Sekarang

```php
// Controller
if (!empty($this->machineId)) { ... }               // bekerja dengan scalar
'machineId' => $this->machineId ?? null,            // bekerja dengan scalar
```
```html
<!-- Blade -->
<div wire:ignore>
    <select class="form-control select2-machine-NAMA"> <!-- select2, tanpa wire:model -->
@if ($item->id == ($machineId ?? null)) selected @endif <!-- scalar comparison -->
```

### Kenapa `wire:ignore` pada Wrapper Select2?

Select2 memanipulasi DOM langsung. Tanpa `wire:ignore`, Livewire menimpa DOM saat update dan select2 rusak. Nilai dikirim via `@this.set('field', value)` di JavaScript, bukan via `wire:model`.

### Kenapa Cache Master Data di `render()`?

`render()` dipanggil di setiap Livewire request. Tanpa cache, query produk/mesin jalan setiap filter, sort, paginate. Dengan `Cache::remember(..., 3600)`, query hanya jalan sekali per jam.

### Kenapa `$perPage`, `$sortColumn`, `$sortDirection` Perlu `#[Session]`?

Agar state pagination dan sorting tetap tersimpan saat user berpindah dari halaman edit kembali ke halaman list. Tanpa `#[Session]`, saat balik dari edit, halaman kembali ke page 1 dan sort default.

### Kenapa `WithoutUrlPagination`?

Mencegah Livewire menambah parameter `?page=N` ke URL, sehingga URL tetap bersih dan tidak perlu di-handle oleh router.

---

## BAGIAN 5 — CARA MENERAPKAN KE HALAMAN BARU

1. Copy template controller dan blade dari Bagian 1 & 2
2. Ganti semua `NAMA` dengan nama halaman (e.g., `kenpin-seitai`)
3. Sesuaikan kolom `select` di query dengan kolom yang dibutuhkan halaman
4. Sesuaikan `sortBy()` whitelist dengan kolom yang boleh di-sort
5. Sesuaikan `cols: {1:true, 2:false, ...}` sesuai kolom tabel (indeks mulai 1)
6. Sesuaikan filter `department_id` atau kondisi mesin di Cache::remember
7. Jalankan checklist Bagian 3
8. Test: filter, sort, paginate, navigasi edit → kembali (session tetap)
9. Update tabel status di Bagian Status Implementasi

---

*Terakhir diperbarui: 2026-06-03*
