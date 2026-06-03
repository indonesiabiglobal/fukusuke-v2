<div>
    {{-- Loading bar untuk Livewire request (filter, sort, paginate) --}}
    <div wire:loading.delay class="position-fixed" style="top:0;left:0;width:100%;height:3px;background:linear-gradient(90deg,#0ab39c,#405189,#0ab39c);background-size:200%;animation:nippo-bar-slide 1.5s linear infinite;z-index:99999;"></div>
    {{-- Overlay untuk navigasi full-page (edit, add) --}}
    <div id="nippo-nav-overlay" class="d-none position-fixed top-0 start-0 w-100 h-100 justify-content-center align-items-center" style="background:rgba(255,255,255,0.75);z-index:99998;">
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
                                <select class="form-select" style="padding:0.44rem" wire:model.defer="transaksi">
                                    <option value="1">Produksi</option>
                                    <option value="2">Proses</option>
                                </select>
                            </div>
                            <div class="col-9">
                                <div class="form-group">
                                    <div class="input-group">
                                        <input wire:model.defer="tglMasuk" type="text" class="form-control"
                                            style="padding:0.44rem" data-provider="flatpickr" data-date-format="d M Y">
                                        <span class="input-group-text py-0">
                                            <i class="ri-calendar-event-fill fs-4"></i>
                                        </span>

                                        <input wire:model.defer="tglKeluar" type="text" class="form-control"
                                            style="padding:0.44rem" data-provider="flatpickr" data-date-format="d M Y">
                                        <span class="input-group-text py-0">
                                            <i class="ri-calendar-event-fill fs-4"></i>
                                        </span>
                                    </div>
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
                        if (value.length === 6 && !value.includes('-') && this.status) {
                            value += '-';
                        }
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
                    <label class="form-label text-muted fw-bold">Search</label>
                </div>
                <div class="col-12 col-lg-9">
                    <div class="input-group">
                        <input wire:model.defer="searchTerm" class="form-control"style="padding:0.44rem" type="text"
                            placeholder="search nomor produksi, no han, dll" />
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-5">
            <div class="row">
                <div class="col-12 col-lg-2">
                    <label for="product" class="form-label text-muted fw-bold">Product</label>
                </div>
                <div class="col-12 col-lg-10">
                    <div class="mb-1" wire:ignore>
                        <select class="form-control select2-product-infure">
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
                        <select class="form-control select2-machine-infure">
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
                    <label for="status" class="form-label text-muted fw-bold">Status</label>
                </div>
                <div class="col-12 col-lg-10">
                    <div class="mb-1" wire:ignore>
                        <select class="form-control select2-status-infure">
                            <option value="">- all -</option>
                            <option value="0">Open</option>
                            <option value="1" @if (($status['value'] ?? null) == 1) selected @endif>Seitai</option>
                            <option value="2" @if (($status['value'] ?? null) == 2) selected @endif>Kenpin</option>
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
                                <span class="flex-grow-1 ms-1">
                                    Loading...
                                </span>
                            </span>
                        </div>
                    </button>

                    <button type="button" class="btn btn-success w-lg p-1"
                        onclick="window.location.href='/add-nippo?lpk_no={{ $lpk_no }}'">
                        <i class="ri-add-line"> </i> Add
                    </button>
                </div>
            </div>
        </div>
        <div class="col-lg-2 mt-2 text-end">
            <button class="btn btn-info w-lg p-1" wire:click="export" type="button" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="export">
                    <i class="ri-printer-line"> </i> Print
                </span>
                <div wire:loading wire:target="export">
                    <span class="d-flex align-items-center">
                        <span class="spinner-border flex-shrink-0" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </span>
                        <span class="flex-grow-1 ms-1">
                            Loading...
                        </span>
                    </span>
                </div>
            </button>
        </div>
    </div>

    <div x-data="{
        cols: {1:true,2:false,3:false,4:true,5:true,6:true,7:false,8:false,9:false,10:false,11:true,12:true,13:true,14:true,15:true,16:true,17:true,18:false,19:false,20:false}
    }" class="mt-2 mb-2">

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
                    <li><label style="cursor:pointer"><input class="form-check-input fs-15 ms-2" type="checkbox" x-model="cols[3]"> Panjang LPK</label></li>
                    <li><label style="cursor:pointer"><input class="form-check-input fs-15 ms-2" type="checkbox" x-model="cols[4]"> Panjang Produksi</label></li>
                    <li><label style="cursor:pointer"><input class="form-check-input fs-15 ms-2" type="checkbox" x-model="cols[5]"> Berat Gentan</label></li>
                    <li><label style="cursor:pointer"><input class="form-check-input fs-15 ms-2" type="checkbox" x-model="cols[6]"> Nomor Gentan</label></li>
                    <li><label style="cursor:pointer"><input class="form-check-input fs-15 ms-2" type="checkbox" x-model="cols[7]"> Berat Standard</label></li>
                    <li><label style="cursor:pointer"><input class="form-check-input fs-15 ms-2" type="checkbox" x-model="cols[8]"> Rasio %</label></li>
                    <li><label style="cursor:pointer"><input class="form-check-input fs-15 ms-2" type="checkbox" x-model="cols[9]"> Selisih</label></li>
                    <li><label style="cursor:pointer"><input class="form-check-input fs-15 ms-2" type="checkbox" x-model="cols[10]"> Nama Produk</label></li>
                    <li><label style="cursor:pointer"><input class="form-check-input fs-15 ms-2" type="checkbox" x-model="cols[11]"> Nomor Order</label></li>
                    <li><label style="cursor:pointer"><input class="form-check-input fs-15 ms-2" type="checkbox" x-model="cols[12]"> Mesin</label></li>
                    <li><label style="cursor:pointer"><input class="form-check-input fs-15 ms-2" type="checkbox" x-model="cols[13]"> Tanggal Produksi</label></li>
                    <li><label style="cursor:pointer"><input class="form-check-input fs-15 ms-2" type="checkbox" x-model="cols[14]"> Tanggal Proses</label></li>
                    <li><label style="cursor:pointer"><input class="form-check-input fs-15 ms-2" type="checkbox" x-model="cols[15]"> Jam</label></li>
                    <li><label style="cursor:pointer"><input class="form-check-input fs-15 ms-2" type="checkbox" x-model="cols[16]"> Shift</label></li>
                    <li><label style="cursor:pointer"><input class="form-check-input fs-15 ms-2" type="checkbox" x-model="cols[17]"> Seq</label></li>
                    <li><label style="cursor:pointer"><input class="form-check-input fs-15 ms-2" type="checkbox" x-model="cols[18]"> Loss</label></li>
                    <li><label style="cursor:pointer"><input class="form-check-input fs-15 ms-2" type="checkbox" x-model="cols[19]"> Update By</label></li>
                    <li><label style="cursor:pointer"><input class="form-check-input fs-15 ms-2" type="checkbox" x-model="cols[20]"> Updated</label></li>
                </ul>
            </div>
        </div>

        <div wire:loading.class="opacity-50" wire:target="search,sortBy,gotoPage,nextPage,previousPage,perPage"
            style="overflow-x:auto; overflow-y:auto; max-height:65vh; transition: opacity 0.15s;">
            <table class="table align-middle table-nowrap table-hover" id="tableInfure">
                <thead class="table-light">
                    <tr>
                        <th style="width:36px"></th>
                        <th :class="{'d-none': !cols[1]}" wire:click="sortBy('tdol.lpk_no')" style="cursor:pointer;white-space:nowrap">
                            Nomor LPK <i class="{{ $sortColumn === 'tdol.lpk_no' ? ($sortDirection === 'asc' ? 'ri-arrow-up-s-line text-primary' : 'ri-arrow-down-s-line text-primary') : 'ri-expand-up-down-line text-muted' }} fs-12"></i>
                        </th>
                        <th :class="{'d-none': !cols[2]}" wire:click="sortBy('tdol.lpk_date')" style="cursor:pointer;white-space:nowrap">
                            Tanggal LPK <i class="{{ $sortColumn === 'tdol.lpk_date' ? ($sortDirection === 'asc' ? 'ri-arrow-up-s-line text-primary' : 'ri-arrow-down-s-line text-primary') : 'ri-expand-up-down-line text-muted' }} fs-12"></i>
                        </th>
                        <th :class="{'d-none': !cols[3]}" wire:click="sortBy('tdol.panjang_lpk')" style="cursor:pointer;white-space:nowrap">
                            Panjang LPK <i class="{{ $sortColumn === 'tdol.panjang_lpk' ? ($sortDirection === 'asc' ? 'ri-arrow-up-s-line text-primary' : 'ri-arrow-down-s-line text-primary') : 'ri-expand-up-down-line text-muted' }} fs-12"></i>
                        </th>
                        <th :class="{'d-none': !cols[4]}" wire:click="sortBy('tda.panjang_produksi')" style="cursor:pointer;white-space:nowrap">
                            Panjang Produksi <i class="{{ $sortColumn === 'tda.panjang_produksi' ? ($sortDirection === 'asc' ? 'ri-arrow-up-s-line text-primary' : 'ri-arrow-down-s-line text-primary') : 'ri-expand-up-down-line text-muted' }} fs-12"></i>
                        </th>
                        <th :class="{'d-none': !cols[5]}" wire:click="sortBy('tda.berat_produksi')" style="cursor:pointer;white-space:nowrap">
                            Berat Gentan <i class="{{ $sortColumn === 'tda.berat_produksi' ? ($sortDirection === 'asc' ? 'ri-arrow-up-s-line text-primary' : 'ri-arrow-down-s-line text-primary') : 'ri-expand-up-down-line text-muted' }} fs-12"></i>
                        </th>
                        <th :class="{'d-none': !cols[6]}" wire:click="sortBy('tda.gentan_no')" style="cursor:pointer;white-space:nowrap">
                            Nomor Gentan <i class="{{ $sortColumn === 'tda.gentan_no' ? ($sortDirection === 'asc' ? 'ri-arrow-up-s-line text-primary' : 'ri-arrow-down-s-line text-primary') : 'ri-expand-up-down-line text-muted' }} fs-12"></i>
                        </th>
                        <th :class="{'d-none': !cols[7]}" wire:click="sortBy('tda.berat_standard')" style="cursor:pointer;white-space:nowrap">
                            Berat Standard <i class="{{ $sortColumn === 'tda.berat_standard' ? ($sortDirection === 'asc' ? 'ri-arrow-up-s-line text-primary' : 'ri-arrow-down-s-line text-primary') : 'ri-expand-up-down-line text-muted' }} fs-12"></i>
                        </th>
                        <th :class="{'d-none': !cols[8]}">Rasio %</th>
                        <th :class="{'d-none': !cols[9]}">Selisih</th>
                        <th :class="{'d-none': !cols[10]}" wire:click="sortBy('mp.name')" style="cursor:pointer;white-space:nowrap">
                            Nama Produk <i class="{{ $sortColumn === 'mp.name' ? ($sortDirection === 'asc' ? 'ri-arrow-up-s-line text-primary' : 'ri-arrow-down-s-line text-primary') : 'ri-expand-up-down-line text-muted' }} fs-12"></i>
                        </th>
                        <th :class="{'d-none': !cols[11]}" wire:click="sortBy('tdo.product_code')" style="cursor:pointer;white-space:nowrap">
                            Nomor Order <i class="{{ $sortColumn === 'tdo.product_code' ? ($sortDirection === 'asc' ? 'ri-arrow-up-s-line text-primary' : 'ri-arrow-down-s-line text-primary') : 'ri-expand-up-down-line text-muted' }} fs-12"></i>
                        </th>
                        <th :class="{'d-none': !cols[12]}" wire:click="sortBy('msm.machineno')" style="cursor:pointer;white-space:nowrap">
                            Mesin <i class="{{ $sortColumn === 'msm.machineno' ? ($sortDirection === 'asc' ? 'ri-arrow-up-s-line text-primary' : 'ri-arrow-down-s-line text-primary') : 'ri-expand-up-down-line text-muted' }} fs-12"></i>
                        </th>
                        <th :class="{'d-none': !cols[13]}" wire:click="sortBy('tda.production_date')" style="cursor:pointer;white-space:nowrap">
                            Tanggal Produksi <i class="{{ $sortColumn === 'tda.production_date' ? ($sortDirection === 'asc' ? 'ri-arrow-up-s-line text-primary' : 'ri-arrow-down-s-line text-primary') : 'ri-expand-up-down-line text-muted' }} fs-12"></i>
                        </th>
                        <th :class="{'d-none': !cols[14]}" wire:click="sortBy('tda.created_on')" style="cursor:pointer;white-space:nowrap">
                            Tanggal Proses <i class="{{ $sortColumn === 'tda.created_on' ? ($sortDirection === 'asc' ? 'ri-arrow-up-s-line text-primary' : 'ri-arrow-down-s-line text-primary') : 'ri-expand-up-down-line text-muted' }} fs-12"></i>
                        </th>
                        <th :class="{'d-none': !cols[15]}" wire:click="sortBy('tda.work_hour')" style="cursor:pointer;white-space:nowrap">
                            Jam <i class="{{ $sortColumn === 'tda.work_hour' ? ($sortDirection === 'asc' ? 'ri-arrow-up-s-line text-primary' : 'ri-arrow-down-s-line text-primary') : 'ri-expand-up-down-line text-muted' }} fs-12"></i>
                        </th>
                        <th :class="{'d-none': !cols[16]}" wire:click="sortBy('tda.work_shift')" style="cursor:pointer;white-space:nowrap">
                            Shift <i class="{{ $sortColumn === 'tda.work_shift' ? ($sortDirection === 'asc' ? 'ri-arrow-up-s-line text-primary' : 'ri-arrow-down-s-line text-primary') : 'ri-expand-up-down-line text-muted' }} fs-12"></i>
                        </th>
                        <th :class="{'d-none': !cols[17]}" wire:click="sortBy('tda.seq_no')" style="cursor:pointer;white-space:nowrap">
                            Seq <i class="{{ $sortColumn === 'tda.seq_no' ? ($sortDirection === 'asc' ? 'ri-arrow-up-s-line text-primary' : 'ri-arrow-down-s-line text-primary') : 'ri-expand-up-down-line text-muted' }} fs-12"></i>
                        </th>
                        <th :class="{'d-none': !cols[18]}">Loss</th>
                        <th :class="{'d-none': !cols[19]}">Update By</th>
                        <th :class="{'d-none': !cols[20]}" wire:click="sortBy('tda.updated_on')" style="cursor:pointer;white-space:nowrap">
                            Updated <i class="{{ $sortColumn === 'tda.updated_on' ? ($sortDirection === 'asc' ? 'ri-arrow-up-s-line text-primary' : 'ri-arrow-down-s-line text-primary') : 'ri-expand-up-down-line text-muted' }} fs-12"></i>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data as $item)
                        <tr>
                            <td>
                                <a href="/edit-nippo?orderId={{ $item->id }}"
                                    class="link-success fs-15 p-1 bg-primary rounded">
                                    <i class="ri-edit-box-line text-white"></i>
                                </a>
                            </td>
                            <td :class="{'d-none': !cols[1]}"> {{ $item->lpk_no }} </td>
                            <td :class="{'d-none': !cols[2]}">{{ \Carbon\Carbon::parse($item->lpk_date)->format('d M Y') }}</td>
                            <td :class="{'d-none': !cols[3]}"> {{ number_format($item->panjang_lpk, 0, ',', ',') }} </td>
                            <td :class="{'d-none': !cols[4]}"> {{ number_format($item->panjang_produksi, 0, ',', ',') }} </td>
                            <td :class="{'d-none': !cols[5]}"> {{ number_format($item->berat_produksi, 2, ',', '.') }} </td>
                            <td :class="{'d-none': !cols[6]}"> {{ $item->gentan_no }} </td>
                            <td :class="{'d-none': !cols[7]}"> {{ number_format($item->berat_standard, 0, ',', ',') }} </td>
                            <td :class="{'d-none': !cols[8]}">{{ number_format($item->rasio, 2, ',', ',') }}</td>
                            <td :class="{'d-none': !cols[9]}">{{ number_format($item->selisih, 0, ',', ',') }}</td>
                            <td :class="{'d-none': !cols[10]}"> {{ $item->product_name }} </td>
                            <td :class="{'d-none': !cols[11]}"> {{ $item->product_code }} </td>
                            <td :class="{'d-none': !cols[12]}"> {{ $item->machineno }} </td>
                            <td :class="{'d-none': !cols[13]}">{{ \Carbon\Carbon::parse($item->production_date)->format('d M Y') }}</td>
                            <td :class="{'d-none': !cols[14]}">{{ \Carbon\Carbon::parse($item->created_on)->format('d M Y') }}</td>
                            <td :class="{'d-none': !cols[15]}"> {{ $item->work_hour }} </td>
                            <td :class="{'d-none': !cols[16]}"> {{ $item->work_shift }} </td>
                            <td :class="{'d-none': !cols[17]}"> {{ $item->seq_no }} </td>
                            <td :class="{'d-none': !cols[18]}"> {{ $item->infure_berat_loss }} </td>
                            <td :class="{'d-none': !cols[19]}"> {{ $item->updated_by }} </td>
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
        #tableInfure.table>:not(caption)>*>* {
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
        $wire.on('redirectToPrint', (datas) => {
            var printUrl = '{{ route('report-nippo-infure') }}?tanggal=' + datas;
            window.open(printUrl, '_blank');
        });

        document.addEventListener('livewire:initialized', function () {
            function initProductSelect() {
                if ($('.select2-product-infure').hasClass('select2-hidden-accessible')) {
                    $('.select2-product-infure').select2('destroy');
                }
                $('.select2-product-infure').select2({
                    theme: 'bootstrap-5',
                    allowClear: true,
                    placeholder: '- All -',
                }).on('change', function () {
                    @this.set('idProduct', $(this).val() || null);
                });
            }

            function initMachineSelect() {
                if ($('.select2-machine-infure').hasClass('select2-hidden-accessible')) {
                    $('.select2-machine-infure').select2('destroy');
                }
                $('.select2-machine-infure').select2({
                    theme: 'bootstrap-5',
                    allowClear: true,
                    placeholder: '- All -',
                }).on('change', function () {
                    @this.set('machineId', $(this).val() || null);
                });
            }

            function initStatusSelect() {
                if ($('.select2-status-infure').hasClass('select2-hidden-accessible')) {
                    $('.select2-status-infure').select2('destroy');
                }
                $('.select2-status-infure').select2({
                    theme: 'bootstrap-5',
                    allowClear: true,
                    placeholder: '- all -',
                }).on('change', function () {
                    @this.set('status', $(this).val() || null);
                });
            }

            initProductSelect();
            initMachineSelect();
            initStatusSelect();

            Livewire.hook('morph', ({ el, component }) => {
                setTimeout(() => {
                    initProductSelect();
                    initMachineSelect();
                    initStatusSelect();
                }, 100);
            });

            // Tampilkan overlay saat navigasi full-page (edit, add, dll)
            window.addEventListener('beforeunload', function () {
                var overlay = document.getElementById('nippo-nav-overlay');
                if (overlay) {
                    overlay.classList.remove('d-none');
                    overlay.classList.add('d-flex');
                }
            });
        });
    </script>
@endscript
