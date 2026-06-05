<div wire:init="loadData">
    {{-- Skeleton: tampil saat data belum dimuat (isLoaded = false) --}}
    @if(!$isLoaded)
    <div class="card">
        <div class="card-body py-5 text-center">
            <div class="spinner-border text-primary me-2" role="status" style="width:2.5rem;height:2.5rem;"></div>
            <p class="text-muted mt-3 mb-0 fs-5">Memuat data nippo seitai...</p>
            <p class="text-muted small">Harap tunggu sebentar</p>
        </div>
    </div>
    @else
    {{-- Loading bar untuk Livewire request (filter, sort, paginate) --}}
    <div wire:loading.delay class="position-fixed" style="top:0;left:0;width:100%;height:3px;background:linear-gradient(90deg,#0ab39c,#405189,#0ab39c);background-size:200%;animation:nippo-bar-slide 1.5s linear infinite;z-index:99999;"></div>
    {{-- Overlay untuk navigasi full-page (edit, add) --}}
    <div id="seitai-nav-overlay" class="d-none position-fixed top-0 start-0 w-100 h-100 justify-content-center align-items-center" style="background:rgba(255,255,255,0.75);z-index:99998;">
        <div class="spinner-border text-primary" style="width:3rem;height:3rem;" role="status"><span class="visually-hidden">Loading...</span></div>
    </div>

    <div class="row filter-section">
        <div class="col-12 col-lg-7">
            <div class="row">
                <div class="col-12 col-lg-3">
                    <label class="form-label text-muted fw-bold">Filter Tanggal</label>
                </div>
                <div class="col-12 col-lg-9 mb-1">
                    <div class="form-group">
                        <div class="input-group">
                            <div class="col-3">
                                <select class="form-select" style="padding:0.44rem" wire:model.live="transaksi">
                                    <option value="1">Proses</option>
                                    <option value="2">Produksi</option>
                                </select>
                            </div>
                            <div class="col-9">
                                <div class="input-group">
                                    <input wire:model.live="tglMasuk" type="date" class="form-control"
                                        style="padding:0.44rem">
                                    <input wire:model.live="tglKeluar" type="date" class="form-control"
                                        style="padding:0.44rem">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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
                <div class="col-12 col-lg-3">
                    <label class="form-label text-muted fw-bold">Nomor Gentan</label>
                </div>
                <div class="col-12 col-lg-9 mb-1">
                    <input wire:model.defer="gentan_no" class="form-control" style="padding:0.44rem" type="text"
                        placeholder="Nomor Gentan" />
                </div>
                <div class="col-12 col-lg-3">
                    <label class="form-label text-muted fw-bold">Search</label>
                </div>
                <div class="col-12 col-lg-9">
                    <input wire:model.defer="searchTerm" class="form-control" style="padding:0.44rem" type="text"
                        placeholder="Search no produksi, no palet, no lot, produk, dll" />
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-5">
            <div class="row">
                <div class="col-12 col-lg-2">
                    <label class="form-label text-muted fw-bold">Product</label>
                </div>
                <div class="col-12 col-lg-10">
                    <div class="mb-1" wire:ignore>
                        <select class="form-control select2-product-seitai">
                            <option value="">- All -</option>
                            @foreach ($products as $item)
                                <option value="{{ $item->id }}" @if ($item->id == ($idProduct ?? null)) selected @endif>
                                    {{ $item->name }}, {{ $item->code }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-12 col-lg-2">
                    <label class="form-label text-muted fw-bold">Mesin</label>
                </div>
                <div class="col-12 col-lg-10">
                    <div class="mb-1" wire:ignore>
                        <select class="form-control select2-machine-seitai">
                            <option value="">- All -</option>
                            @foreach ($machine as $item)
                                <option value="{{ $item->id }}" @if ($item->id == ($machineId ?? null)) selected @endif>
                                    {{ $item->machineno }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-12 col-lg-2">
                    <label class="form-label text-muted fw-bold">Status</label>
                </div>
                <div class="col-12 col-lg-10">
                    <div class="mb-1" wire:ignore>
                        <select class="form-control select2-status-seitai">
                            <option value="">- All -</option>
                            <option value="0" @if (($status ?? null) == '0') selected @endif>Open</option>
                            <option value="1" @if (($status ?? null) == 1) selected @endif>Seitai</option>
                            <option value="2" @if (($status ?? null) == 2) selected @endif>Kenpin</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-10 mt-2">
            <div class="row">
                <div class="col-12 col-lg-6">
                    <button wire:click="search" type="button" class="btn btn-primary btn-load w-lg p-1"
                        wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="search">
                            <i class="ri-search-line"></i> Filter
                        </span>
                        <div wire:loading wire:target="search">
                            <span class="d-flex align-items-center">
                                <span class="spinner-border flex-shrink-0" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </span>
                                <span class="flex-grow-1 ms-1">Loading...</span>
                            </span>
                        </div>
                    </button>
                    <button type="button" class="btn btn-success w-lg p-1"
                        onclick="window.location.href='/add-seitai?lpk_no={{ $lpk_no }}'">
                        <i class="ri-add-line"></i> Add
                    </button>
                </div>
            </div>
        </div>
        <div class="col-lg-2 mt-2 text-end">
            <button class="btn btn-info w-lg p-1" wire:click="export" type="button" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="export">
                    <i class="ri-printer-line"></i> Print
                </span>
                <div wire:loading wire:target="export">
                    <span class="d-flex align-items-center">
                        <span class="spinner-border flex-shrink-0" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </span>
                        <span class="flex-grow-1 ms-1"></span>
                    </span>
                </div>
            </button>
        </div>
    </div>

    <div x-data="{
        cols: JSON.parse(localStorage.getItem('nippo-seitai-cols') || JSON.stringify({1:true,2:false,3:false,4:true,5:true,6:true,7:false,8:false,9:false,10:true,11:true,12:true,13:true,14:true,15:true,16:false,17:false,18:true,19:false,20:false})),
    }" x-init="$watch('cols', val => { try { localStorage.setItem('nippo-seitai-cols', JSON.stringify(val)); } catch(e) {} })"
    class="mt-2 mb-2">

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
                    <li><label style="cursor:pointer"><input class="form-check-input fs-15 ms-2" type="checkbox" x-model="cols[1]"> Nomor LPK</label></li>
                    <li><label style="cursor:pointer"><input class="form-check-input fs-15 ms-2" type="checkbox" x-model="cols[2]"> Tanggal LPK</label></li>
                    <li><label style="cursor:pointer"><input class="form-check-input fs-15 ms-2" type="checkbox" x-model="cols[3]"> Jumlah LPK</label></li>
                    <li><label style="cursor:pointer"><input class="form-check-input fs-15 ms-2" type="checkbox" x-model="cols[4]"> Jumlah Produksi</label></li>
                    <li><label style="cursor:pointer"><input class="form-check-input fs-15 ms-2" type="checkbox" x-model="cols[5]"> Loss Seitai</label></li>
                    <li><label style="cursor:pointer"><input class="form-check-input fs-15 ms-2" type="checkbox" x-model="cols[6]"> Loss Infure</label></li>
                    <li><label style="cursor:pointer"><input class="form-check-input fs-15 ms-2" type="checkbox" x-model="cols[7]"> Selisih</label></li>
                    <li><label style="cursor:pointer"><input class="form-check-input fs-15 ms-2" type="checkbox" x-model="cols[8]"> Nama Produk</label></li>
                    <li><label style="cursor:pointer"><input class="form-check-input fs-15 ms-2" type="checkbox" x-model="cols[9]"> Nomor Order</label></li>
                    <li><label style="cursor:pointer"><input class="form-check-input fs-15 ms-2" type="checkbox" x-model="cols[10]"> Mesin</label></li>
                    <li><label style="cursor:pointer"><input class="form-check-input fs-15 ms-2" type="checkbox" x-model="cols[11]"> Tanggal Produksi</label></li>
                    <li><label style="cursor:pointer"><input class="form-check-input fs-15 ms-2" type="checkbox" x-model="cols[12]"> Tanggal Proses</label></li>
                    <li><label style="cursor:pointer"><input class="form-check-input fs-15 ms-2" type="checkbox" x-model="cols[13]"> Jam</label></li>
                    <li><label style="cursor:pointer"><input class="form-check-input fs-15 ms-2" type="checkbox" x-model="cols[14]"> Shift</label></li>
                    <li><label style="cursor:pointer"><input class="form-check-input fs-15 ms-2" type="checkbox" x-model="cols[15]"> Seq</label></li>
                    <li><label style="cursor:pointer"><input class="form-check-input fs-15 ms-2" type="checkbox" x-model="cols[16]"> Nomor Palet</label></li>
                    <li><label style="cursor:pointer"><input class="form-check-input fs-15 ms-2" type="checkbox" x-model="cols[17]"> Nomor Lot</label></li>
                    <li><label style="cursor:pointer"><input class="form-check-input fs-15 ms-2" type="checkbox" x-model="cols[18]"> Status</label></li>
                    <li><label style="cursor:pointer"><input class="form-check-input fs-15 ms-2" type="checkbox" x-model="cols[19]"> Update By</label></li>
                    <li><label style="cursor:pointer"><input class="form-check-input fs-15 ms-2" type="checkbox" x-model="cols[20]"> Updated</label></li>
                </ul>
            </div>
        </div>

        <div wire:loading.class="opacity-50"
             wire:target="search,sortBy,gotoPage,nextPage,previousPage,perPage"
             style="overflow-x:auto; overflow-y:auto; max-height:65vh; transition: opacity 0.15s;">
            <table class="table align-middle table-nowrap table-hover" id="tableSeitai">
                <thead class="table-light">
                    <tr>
                        <th style="width:36px"></th>
                        <th :class="{'d-none': !cols[1]}" wire:click="sortBy('tdol.lpk_no')" style="cursor:pointer;white-space:nowrap">
                            Nomor LPK <i class="{{ $sortColumn === 'tdol.lpk_no' ? ($sortDirection === 'asc' ? 'ri-arrow-up-s-line text-primary' : 'ri-arrow-down-s-line text-primary') : 'ri-expand-up-down-line text-muted' }} fs-12"></i>
                        </th>
                        <th :class="{'d-none': !cols[2]}" wire:click="sortBy('tdol.lpk_date')" style="cursor:pointer;white-space:nowrap">
                            Tanggal LPK <i class="{{ $sortColumn === 'tdol.lpk_date' ? ($sortDirection === 'asc' ? 'ri-arrow-up-s-line text-primary' : 'ri-arrow-down-s-line text-primary') : 'ri-expand-up-down-line text-muted' }} fs-12"></i>
                        </th>
                        <th :class="{'d-none': !cols[3]}" wire:click="sortBy('tdol.qty_lpk')" style="cursor:pointer;white-space:nowrap">
                            Jumlah LPK <i class="{{ $sortColumn === 'tdol.qty_lpk' ? ($sortDirection === 'asc' ? 'ri-arrow-up-s-line text-primary' : 'ri-arrow-down-s-line text-primary') : 'ri-expand-up-down-line text-muted' }} fs-12"></i>
                        </th>
                        <th :class="{'d-none': !cols[4]}" wire:click="sortBy('tdpg.qty_produksi')" style="cursor:pointer;white-space:nowrap">
                            Jumlah Produksi <i class="{{ $sortColumn === 'tdpg.qty_produksi' ? ($sortDirection === 'asc' ? 'ri-arrow-up-s-line text-primary' : 'ri-arrow-down-s-line text-primary') : 'ri-expand-up-down-line text-muted' }} fs-12"></i>
                        </th>
                        <th :class="{'d-none': !cols[5]}" wire:click="sortBy('tdpg.seitai_berat_loss')" style="cursor:pointer;white-space:nowrap">
                            Loss Seitai <i class="{{ $sortColumn === 'tdpg.seitai_berat_loss' ? ($sortDirection === 'asc' ? 'ri-arrow-up-s-line text-primary' : 'ri-arrow-down-s-line text-primary') : 'ri-expand-up-down-line text-muted' }} fs-12"></i>
                        </th>
                        <th :class="{'d-none': !cols[6]}" wire:click="sortBy('tdpg.infure_berat_loss')" style="cursor:pointer;white-space:nowrap">
                            Loss Infure <i class="{{ $sortColumn === 'tdpg.infure_berat_loss' ? ($sortDirection === 'asc' ? 'ri-arrow-up-s-line text-primary' : 'ri-arrow-down-s-line text-primary') : 'ri-expand-up-down-line text-muted' }} fs-12"></i>
                        </th>
                        <th :class="{'d-none': !cols[7]}">Selisih</th>
                        <th :class="{'d-none': !cols[8]}" wire:click="sortBy('mp.name')" style="cursor:pointer;white-space:nowrap">
                            Nama Produk <i class="{{ $sortColumn === 'mp.name' ? ($sortDirection === 'asc' ? 'ri-arrow-up-s-line text-primary' : 'ri-arrow-down-s-line text-primary') : 'ri-expand-up-down-line text-muted' }} fs-12"></i>
                        </th>
                        <th :class="{'d-none': !cols[9]}" wire:click="sortBy('mp.code')" style="cursor:pointer;white-space:nowrap">
                            Nomor Order <i class="{{ $sortColumn === 'mp.code' ? ($sortDirection === 'asc' ? 'ri-arrow-up-s-line text-primary' : 'ri-arrow-down-s-line text-primary') : 'ri-expand-up-down-line text-muted' }} fs-12"></i>
                        </th>
                        <th :class="{'d-none': !cols[10]}" wire:click="sortBy('mc.machineno')" style="cursor:pointer;white-space:nowrap">
                            Mesin <i class="{{ $sortColumn === 'mc.machineno' ? ($sortDirection === 'asc' ? 'ri-arrow-up-s-line text-primary' : 'ri-arrow-down-s-line text-primary') : 'ri-expand-up-down-line text-muted' }} fs-12"></i>
                        </th>
                        <th :class="{'d-none': !cols[11]}" wire:click="sortBy('tdpg.production_date')" style="cursor:pointer;white-space:nowrap">
                            Tanggal Produksi <i class="{{ $sortColumn === 'tdpg.production_date' ? ($sortDirection === 'asc' ? 'ri-arrow-up-s-line text-primary' : 'ri-arrow-down-s-line text-primary') : 'ri-expand-up-down-line text-muted' }} fs-12"></i>
                        </th>
                        <th :class="{'d-none': !cols[12]}" wire:click="sortBy('tdpg.created_on')" style="cursor:pointer;white-space:nowrap">
                            Tanggal Proses <i class="{{ $sortColumn === 'tdpg.created_on' ? ($sortDirection === 'asc' ? 'ri-arrow-up-s-line text-primary' : 'ri-arrow-down-s-line text-primary') : 'ri-expand-up-down-line text-muted' }} fs-12"></i>
                        </th>
                        <th :class="{'d-none': !cols[13]}" wire:click="sortBy('tdpg.work_hour')" style="cursor:pointer;white-space:nowrap">
                            Jam <i class="{{ $sortColumn === 'tdpg.work_hour' ? ($sortDirection === 'asc' ? 'ri-arrow-up-s-line text-primary' : 'ri-arrow-down-s-line text-primary') : 'ri-expand-up-down-line text-muted' }} fs-12"></i>
                        </th>
                        <th :class="{'d-none': !cols[14]}" wire:click="sortBy('tdpg.work_shift')" style="cursor:pointer;white-space:nowrap">
                            Shift <i class="{{ $sortColumn === 'tdpg.work_shift' ? ($sortDirection === 'asc' ? 'ri-arrow-up-s-line text-primary' : 'ri-arrow-down-s-line text-primary') : 'ri-expand-up-down-line text-muted' }} fs-12"></i>
                        </th>
                        <th :class="{'d-none': !cols[15]}" wire:click="sortBy('tdpg.seq_no')" style="cursor:pointer;white-space:nowrap">
                            Seq <i class="{{ $sortColumn === 'tdpg.seq_no' ? ($sortDirection === 'asc' ? 'ri-arrow-up-s-line text-primary' : 'ri-arrow-down-s-line text-primary') : 'ri-expand-up-down-line text-muted' }} fs-12"></i>
                        </th>
                        <th :class="{'d-none': !cols[16]}" wire:click="sortBy('tdpg.nomor_palet')" style="cursor:pointer;white-space:nowrap">
                            Nomor Palet <i class="{{ $sortColumn === 'tdpg.nomor_palet' ? ($sortDirection === 'asc' ? 'ri-arrow-up-s-line text-primary' : 'ri-arrow-down-s-line text-primary') : 'ri-expand-up-down-line text-muted' }} fs-12"></i>
                        </th>
                        <th :class="{'d-none': !cols[17]}" wire:click="sortBy('tdpg.nomor_lot')" style="cursor:pointer;white-space:nowrap">
                            Nomor Lot <i class="{{ $sortColumn === 'tdpg.nomor_lot' ? ($sortDirection === 'asc' ? 'ri-arrow-up-s-line text-primary' : 'ri-arrow-down-s-line text-primary') : 'ri-expand-up-down-line text-muted' }} fs-12"></i>
                        </th>
                        <th :class="{'d-none': !cols[18]}">Status</th>
                        <th :class="{'d-none': !cols[19]}">Update By</th>
                        <th :class="{'d-none': !cols[20]}" wire:click="sortBy('tdpg.updated_on')" style="cursor:pointer;white-space:nowrap">
                            Updated <i class="{{ $sortColumn === 'tdpg.updated_on' ? ($sortDirection === 'asc' ? 'ri-arrow-up-s-line text-primary' : 'ri-arrow-down-s-line text-primary') : 'ri-expand-up-down-line text-muted' }} fs-12"></i>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data as $item)
                        <tr>
                            <td>
                                <a href="/edit-seitai?orderId={{ $item->id }}"
                                    class="link-success fs-15 p-1 bg-primary rounded">
                                    <i class="ri-edit-box-line text-white"></i>
                                </a>
                            </td>
                            <td :class="{'d-none': !cols[1]}">{{ $item->lpk_no }}</td>
                            <td :class="{'d-none': !cols[2]}">{{ \Carbon\Carbon::parse($item->lpk_date)->format('d M Y') }}</td>
                            <td :class="{'d-none': !cols[3]}">{{ number_format($item->qty_lpk, 0, ',', ',') }}</td>
                            <td :class="{'d-none': !cols[4]}">{{ number_format($item->qty_produksi, 0, ',', ',') }}</td>
                            <td :class="{'d-none': !cols[5]}">{{ number_format($item->seitai_berat_loss, 2, ',', '.') }}</td>
                            <td :class="{'d-none': !cols[6]}">{{ number_format($item->infure_berat_loss, 2, ',', '.') }}</td>
                            <td :class="{'d-none': !cols[7]}">{{ number_format($item->selisih) }}</td>
                            <td :class="{'d-none': !cols[8]}">{{ $item->product_name }}</td>
                            <td :class="{'d-none': !cols[9]}">{{ $item->code }}</td>
                            <td :class="{'d-none': !cols[10]}">{{ $item->machineno }}</td>
                            <td :class="{'d-none': !cols[11]}">{{ \Carbon\Carbon::parse($item->production_date)->format('d M Y') }}</td>
                            <td :class="{'d-none': !cols[12]}">{{ \Carbon\Carbon::parse($item->created_on)->format('d M Y') }}</td>
                            <td :class="{'d-none': !cols[13]}">{{ $item->work_hour }}</td>
                            <td :class="{'d-none': !cols[14]}">{{ $item->work_shift }}</td>
                            <td :class="{'d-none': !cols[15]}">{{ $item->seq_no }}</td>
                            <td :class="{'d-none': !cols[16]}">{{ $item->nomor_palet }}</td>
                            <td :class="{'d-none': !cols[17]}">{{ $item->nomor_lot }}</td>
                            <td :class="{'d-none': !cols[18]}">
                                @if ($item->status_warehouse == 1)
                                    <span class="badge bg-success">Kenpin</span>
                                @elseif ($item->status_production == 1)
                                    <span class="badge bg-info">Seitai</span>
                                @else
                                    <span class="badge bg-warning">Open</span>
                                @endif
                            </td>
                            <td :class="{'d-none': !cols[19]}">{{ $item->updated_by }}</td>
                            <td :class="{'d-none': !cols[20]}">{{ \Carbon\Carbon::parse($item->updated_on)->format('d M Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="21" class="text-center py-4">
                                <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop"
                                    colors="primary:#121331,secondary:#08a88a" style="width:40px;height:40px"></lord-icon>
                                <h5 class="mt-2">Record not Found..!</h5>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-between align-items-center flex-wrap mt-2 gap-2">
            <div class="text-muted small">
                @if ($data instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    Showing {{ $data->firstItem() ?? 0 }}–{{ $data->lastItem() ?? 0 }} of {{ $data->total() }} entries
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
        #tableSeitai.table>:not(caption)>*>* {
            font-size: 13px !important;
            padding: 4px 2px 4px 4px;
            color: var(--tb-table-color-state, var(--tb-table-color-type, var(--tb-table-color)));
            background-color: var(--tb-table-bg);
            border-bottom-width: var(--tb-border-width);
            box-shadow: inset 0 0 0 9999px var(--tb-table-bg-state, var(--tb-table-bg-type, var(--tb-table-accent-bg)));
        }
    </style>
</div>
@endif

@script
    <script>
        document.addEventListener('livewire:initialized', function () {
            function initProductSelect() {
                if ($('.select2-product-seitai').hasClass('select2-hidden-accessible')) {
                    $('.select2-product-seitai').select2('destroy');
                }
                $('.select2-product-seitai').select2({
                    theme: 'bootstrap-5',
                    allowClear: true,
                    placeholder: '- All -',
                }).on('change', function () {
                    @this.set('idProduct', $(this).val() || null);
                });
            }

            function initMachineSelect() {
                if ($('.select2-machine-seitai').hasClass('select2-hidden-accessible')) {
                    $('.select2-machine-seitai').select2('destroy');
                }
                $('.select2-machine-seitai').select2({
                    theme: 'bootstrap-5',
                    allowClear: true,
                    placeholder: '- All -',
                }).on('change', function () {
                    @this.set('machineId', $(this).val() || null);
                });
            }

            function initStatusSelect() {
                if ($('.select2-status-seitai').hasClass('select2-hidden-accessible')) {
                    $('.select2-status-seitai').select2('destroy');
                }
                $('.select2-status-seitai').select2({
                    theme: 'bootstrap-5',
                    allowClear: true,
                    placeholder: '- All -',
                }).on('change', function () {
                    @this.set('status', $(this).val() || null);
                });
            }

            initProductSelect();
            initMachineSelect();
            initStatusSelect();

            Livewire.hook('morph', ({ el, component }) => {
                setTimeout(() => {
                    if (!$('.select2-product-seitai').hasClass('select2-hidden-accessible')) {
                        initProductSelect();
                    }
                    if (!$('.select2-machine-seitai').hasClass('select2-hidden-accessible')) {
                        initMachineSelect();
                    }
                    if (!$('.select2-status-seitai').hasClass('select2-hidden-accessible')) {
                        initStatusSelect();
                    }
                }, 100);
            });

            window.addEventListener('beforeunload', function () {
                var overlay = document.getElementById('seitai-nav-overlay');
                if (overlay) {
                    overlay.classList.remove('d-none');
                    overlay.classList.add('d-flex');
                }
            });
        });
    </script>
@endscript

