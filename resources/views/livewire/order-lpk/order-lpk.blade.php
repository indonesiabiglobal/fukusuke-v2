{{-- @include('layouts.customizer') --}}
<div wire:init="loadData">
    @if(!$isLoaded)
    <div class="card">
        <div class="card-body py-5 text-center">
            <div class="spinner-border text-primary me-2" role="status" style="width:2.5rem;height:2.5rem;"></div>
            <p class="text-muted mt-3 mb-0 fs-5">Memuat data order LPK...</p>
        </div>
    </div>
    @else
    <div wire:loading.delay class="position-fixed" style="top:0;left:0;width:100%;height:3px;background:linear-gradient(90deg,#0ab39c,#405189,#0ab39c);background-size:200%;animation:ol-bar-slide 1.5s linear infinite;z-index:99999;"></div>

    <div class="row filter-section">
        <div class="col-12 col-lg-7">
            <div class="row">
                <div class="col-12 col-lg-3">
                    <label class="form-label text-muted fw-bold">Filter Tanggal</label>
                </div>
                <div class="col-12 col-lg-9 mb-1">
                    <div class="form-group">
                        <div class="input-group">
                            <div class="col-12 col-sm-3">
                                <select class="form-select mb-2 mb-sm-0" style="padding:0.44rem" wire:model.defer="transaksi">
                                    <option value="1">Proses</option>
                                    <option value="2">Order</option>
                                </select>
                            </div>
                            <div class="col-12 col-sm-9">
                                <div class="d-flex flex-column flex-sm-row gap-2">
                                    <input wire:model.defer="tglMasuk" type="date" class="form-control"
                                        style="padding:0.44rem">
                                    <input wire:model.defer="tglKeluar" type="date" class="form-control"
                                        style="padding:0.44rem">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-3">
                    <label class="form-label text-muted fw-bold">Search</label>
                </div>
                <div class="col-12 col-lg-9">
                    <div class="input-group">
                        <input wire:model.defer="searchTerm" class="form-control" style="padding:0.44rem" type="text"
                            placeholder="search nomor PO, nama produk" />
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
                        <select class="form-control select2-product">
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
                <div class="col-12 col-lg-2">
                    <label for="buyer" class="form-label text-muted fw-bold">Buyer</label>
                </div>
                <div class="col-12 col-lg-10">
                    <div class="mb-1" wire:ignore>
                        <select class="form-control select2-buyer-ol">
                            <option value="">- All -</option>
                            @foreach ($buyer as $item)
                                <option value="{{ $item->id }}"
                                    @if ($item->id == ($idBuyer ?? null)) selected @endif>
                                    {{ $item->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-12 col-lg-2">
                    <label for="idStatus" class="form-label text-muted fw-bold">Status</label>
                </div>
                <div class="col-12 col-lg-10">
                    <div class="mb-1" wire:ignore>
                        <select class="form-control select2-status-ol">
                            <option value="">- All -</option>
                            <option value="0" @if (($status ?? '') == 0) selected @endif>Belum LPK</option>
                            <option value="1" @if (($status ?? '') == 1) selected @endif>Sudah LPK</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-12 mt-2">
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
                        onclick="window.location.href='/add-order'">
                        <i class="ri-add-line"></i> Add
                    </button>
                </div>
                <div class="col-12 col-lg-6 d-none d-sm-block text-end">
                    <input type="file" id="fileInput" wire:model="file" style="display: none;">
                    <button class="btn btn-success w-lg p-1" type="button"
                        onclick="document.getElementById('fileInput').click()" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="file">
                            <i class="ri-upload-2-fill"></i> Upload Excel
                        </span>
                        <div wire:loading wire:target="file">
                            <span class="d-flex align-items-center">
<span class="spinner-border flex-shrink-0" role="status"><span class="visually-hidden">Loading...</span></span>
                                <span class="flex-grow-1 ms-1">Loading...</span>
                            </span>
                        </div>
                    </button>
                    <button class="btn btn-primary w-lg p-1" wire:click="download" type="button"
                        wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="download">
                            <i class="ri-download-cloud-2-line"></i> Download Template
                        </span>
                        <div wire:loading wire:target="download">
                            <span class="d-flex align-items-center">
                                <span class="spinner-border flex-shrink-0" role="status"></span>
                                <span class="flex-grow-1 ms-1">Loading...</span>
                            </span>
                        </div>
                    </button>
                    <button class="btn btn-info w-lg p-1" wire:click="print" type="button"
                        wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="print">
                            <i class="ri-printer-line"></i> Print
                        </span>
                        <div wire:loading wire:target="print">
                            <span class="d-flex align-items-center">
                                <span class="spinner-border flex-shrink-0" role="status"></span>
                                <span class="flex-grow-1 ms-1">Loading...</span>
                            </span>
                        </div>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div x-data="{
        cols: {1:true,2:true,3:true,4:true,5:true,6:true,7:false,8:true,9:false,10:true,11:false,12:false}
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
                    <li><label style="cursor:pointer"><input class="form-check-input fs-15 ms-2" type="checkbox" x-model="cols[1]"> PO Number</label></li>
                    <li><label style="cursor:pointer"><input class="form-check-input fs-15 ms-2" type="checkbox" x-model="cols[2]"> Nama Produk</label></li>
                    <li><label style="cursor:pointer"><input class="form-check-input fs-15 ms-2" type="checkbox" x-model="cols[3]"> Kode Produk</label></li>
                    <li><label style="cursor:pointer"><input class="form-check-input fs-15 ms-2" type="checkbox" x-model="cols[4]"> Buyer</label></li>
                    <li><label style="cursor:pointer"><input class="form-check-input fs-15 ms-2" type="checkbox" x-model="cols[5]"> Quantity</label></li>
                    <li><label style="cursor:pointer"><input class="form-check-input fs-15 ms-2" type="checkbox" x-model="cols[6]"> Tgl. Order</label></li>
                    <li><label style="cursor:pointer"><input class="form-check-input fs-15 ms-2" type="checkbox" x-model="cols[7]"> Stuffing</label></li>
                    <li><label style="cursor:pointer"><input class="form-check-input fs-15 ms-2" type="checkbox" x-model="cols[8]"> Etd</label></li>
                    <li><label style="cursor:pointer"><input class="form-check-input fs-15 ms-2" type="checkbox" x-model="cols[9]"> Eta</label></li>
                    <li><label style="cursor:pointer"><input class="form-check-input fs-15 ms-2" type="checkbox" x-model="cols[10]"> Tgl Proses</label></li>
                    <li><label style="cursor:pointer"><input class="form-check-input fs-15 ms-2" type="checkbox" x-model="cols[11]"> Update By</label></li>
                    <li><label style="cursor:pointer"><input class="form-check-input fs-15 ms-2" type="checkbox" x-model="cols[12]"> Update On</label></li>
                </ul>
            </div>
        </div>

        <div wire:loading.class="opacity-50"
             wire:target="search,sortBy,gotoPage,nextPage,previousPage,perPage"
             style="overflow-x:auto; overflow-y:auto; max-height:65vh; transition: opacity 0.15s;">
            <table class="table align-middle table-nowrap table-hover" id="tableOrderLPK">
                <thead class="table-light">
                    <tr>
                        <th style="width:36px"></th>
                        <th :class="{'d-none': !cols[1]}" wire:click="sortBy('tod.po_no')" style="cursor:pointer;white-space:nowrap">
                            PO Number <i class="{{ $sortColumn === 'tod.po_no' ? ($sortDirection === 'asc' ? 'ri-arrow-up-s-line text-primary' : 'ri-arrow-down-s-line text-primary') : 'ri-expand-up-down-line text-muted' }} fs-12"></i>
                        </th>
                        <th :class="{'d-none': !cols[2]}" wire:click="sortBy('mp.name')" style="cursor:pointer;white-space:nowrap">
                            Nama Produk <i class="{{ $sortColumn === 'mp.name' ? ($sortDirection === 'asc' ? 'ri-arrow-up-s-line text-primary' : 'ri-arrow-down-s-line text-primary') : 'ri-expand-up-down-line text-muted' }} fs-12"></i>
                        </th>
                        <th :class="{'d-none': !cols[3]}" wire:click="sortBy('tod.product_code')" style="cursor:pointer;white-space:nowrap">
                            Kode Produk <i class="{{ $sortColumn === 'tod.product_code' ? ($sortDirection === 'asc' ? 'ri-arrow-up-s-line text-primary' : 'ri-arrow-down-s-line text-primary') : 'ri-expand-up-down-line text-muted' }} fs-12"></i>
                        </th>
                        <th :class="{'d-none': !cols[4]}" wire:click="sortBy('mbu.name')" style="cursor:pointer;white-space:nowrap">
                            Buyer <i class="{{ $sortColumn === 'mbu.name' ? ($sortDirection === 'asc' ? 'ri-arrow-up-s-line text-primary' : 'ri-arrow-down-s-line text-primary') : 'ri-expand-up-down-line text-muted' }} fs-12"></i>
                        </th>
                        <th :class="{'d-none': !cols[5]}" wire:click="sortBy('tod.order_qty')" style="cursor:pointer;white-space:nowrap">
                            Quantity <i class="{{ $sortColumn === 'tod.order_qty' ? ($sortDirection === 'asc' ? 'ri-arrow-up-s-line text-primary' : 'ri-arrow-down-s-line text-primary') : 'ri-expand-up-down-line text-muted' }} fs-12"></i>
                        </th>
                        <th :class="{'d-none': !cols[6]}" wire:click="sortBy('tod.order_date')" style="cursor:pointer;white-space:nowrap">
                            Tgl. Order <i class="{{ $sortColumn === 'tod.order_date' ? ($sortDirection === 'asc' ? 'ri-arrow-up-s-line text-primary' : 'ri-arrow-down-s-line text-primary') : 'ri-expand-up-down-line text-muted' }} fs-12"></i>
                        </th>
                        <th :class="{'d-none': !cols[7]}" wire:click="sortBy('tod.stufingdate')" style="cursor:pointer;white-space:nowrap">
                            Stuffing <i class="{{ $sortColumn === 'tod.stufingdate' ? ($sortDirection === 'asc' ? 'ri-arrow-up-s-line text-primary' : 'ri-arrow-down-s-line text-primary') : 'ri-expand-up-down-line text-muted' }} fs-12"></i>
                        </th>
                        <th :class="{'d-none': !cols[8]}" wire:click="sortBy('tod.etddate')" style="cursor:pointer;white-space:nowrap">
                            ETD <i class="{{ $sortColumn === 'tod.etddate' ? ($sortDirection === 'asc' ? 'ri-arrow-up-s-line text-primary' : 'ri-arrow-down-s-line text-primary') : 'ri-expand-up-down-line text-muted' }} fs-12"></i>
                        </th>
                        <th :class="{'d-none': !cols[9]}" wire:click="sortBy('tod.etadate')" style="cursor:pointer;white-space:nowrap">
                            ETA <i class="{{ $sortColumn === 'tod.etadate' ? ($sortDirection === 'asc' ? 'ri-arrow-up-s-line text-primary' : 'ri-arrow-down-s-line text-primary') : 'ri-expand-up-down-line text-muted' }} fs-12"></i>
                        </th>
                        <th :class="{'d-none': !cols[10]}" wire:click="sortBy('tod.processdate')" style="cursor:pointer;white-space:nowrap">
                            Tgl Proses <i class="{{ $sortColumn === 'tod.processdate' ? ($sortDirection === 'asc' ? 'ri-arrow-up-s-line text-primary' : 'ri-arrow-down-s-line text-primary') : 'ri-expand-up-down-line text-muted' }} fs-12"></i>
                        </th>
                        <th :class="{'d-none': !cols[11]}">Update By</th>
                        <th :class="{'d-none': !cols[12]}" wire:click="sortBy('tod.updated_on')" style="cursor:pointer;white-space:nowrap">
                            Update On <i class="{{ $sortColumn === 'tod.updated_on' ? ($sortDirection === 'asc' ? 'ri-arrow-up-s-line text-primary' : 'ri-arrow-down-s-line text-primary') : 'ri-expand-up-down-line text-muted' }} fs-12"></i>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data as $item)
                        <tr>
                            <td class="text-center">
                                <a href="/edit-order?orderId={{ $item->id }}"
                                    class="link-success fs-15 p-1 bg-primary rounded">
                                    <i class="ri-edit-box-line text-white"></i>
                                </a>
                            </td>
                            <td :class="{'d-none': !cols[1]}">{{ $item->po_no }}</td>
                            <td :class="{'d-none': !cols[2]}" class="text-start">{{ $item->produk_name }}</td>
                            <td :class="{'d-none': !cols[3]}">{{ $item->product_code }}</td>
                            <td :class="{'d-none': !cols[4]}">{{ $item->buyer_name }}</td>
                            <td :class="{'d-none': !cols[5]}">{{ number_format($item->order_qty) }}</td>
                            <td :class="{'d-none': !cols[6]}">{{ \Carbon\Carbon::parse($item->order_date)->format('d M Y') }}</td>
                            <td :class="{'d-none': !cols[7]}">{{ \Carbon\Carbon::parse($item->stufingdate)->format('d M Y') }}</td>
                            <td :class="{'d-none': !cols[8]}">{{ \Carbon\Carbon::parse($item->etddate)->format('d M Y') }}</td>
                            <td :class="{'d-none': !cols[9]}">{{ \Carbon\Carbon::parse($item->etadate)->format('d M Y') }}</td>
                            <td :class="{'d-none': !cols[10]}">{{ \Carbon\Carbon::parse($item->processdate)->format('d M Y') }}</td>
                            <td :class="{'d-none': !cols[11]}">{{ $item->updated_by }}</td>
                            <td :class="{'d-none': !cols[12]}">{{ \Carbon\Carbon::parse($item->updated_on)->format('d M Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="13" class="text-center py-4">
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
        @keyframes ol-bar-slide {
            0%   { background-position: 0% 0%; }
            100% { background-position: 200% 0%; }
        }
        #tableOrderLPK.table>:not(caption)>*>* {
            font-size: 13px !important;
            padding: 4px 2px 4px 4px;
        }
    </style>
</div>
@endif

@script
    <script>
        document.addEventListener('livewire:initialized', function() {
            function initProductSelect() {
                if ($('.select2-product').hasClass('select2-hidden-accessible')) {
                    $('.select2-product').select2('destroy');
                }
                $('.select2-product').select2({
                    theme: 'bootstrap-5',
                    allowClear: true,
                    placeholder: '-- All --',
                }).on('change', function() {
                    @this.set('idProduct', $(this).val() || null);
                });
            }

            function initBuyerSelect() {
                if ($('.select2-buyer-ol').hasClass('select2-hidden-accessible')) {
                    $('.select2-buyer-ol').select2('destroy');
                }
                $('.select2-buyer-ol').select2({
                    theme: 'bootstrap-5',
                    allowClear: true,
                    placeholder: '- All -',
                }).on('change', function() {
                    @this.set('idBuyer', $(this).val() || null);
                });
            }

            function initStatusSelect() {
                if ($('.select2-status-ol').hasClass('select2-hidden-accessible')) {
                    $('.select2-status-ol').select2('destroy');
                }
                $('.select2-status-ol').select2({
                    theme: 'bootstrap-5',
                    allowClear: true,
                    placeholder: '- All -',
                }).on('change', function() {
                    @this.set('status', $(this).val() || null);
                });
            }

            initProductSelect();
            initBuyerSelect();
            initStatusSelect();

            Livewire.hook('morph', ({ el, component }) => {
                setTimeout(() => {
                    initProductSelect();
                    initBuyerSelect();
                    initStatusSelect();
                }, 100);
            });
        });
    </script>
@endscript
