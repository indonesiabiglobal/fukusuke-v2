<div wire:init="loadData">
    @if(!$isLoaded)
    <div class="card">
        <div class="card-body py-5 text-center">
            <div class="spinner-border text-primary me-2" role="status" style="width:2.5rem;height:2.5rem;"></div>
            <p class="text-muted mt-3 mb-0 fs-5">Memuat data kenpin seitai...</p>
        </div>
    </div>
    @else
    <div wire:loading.delay class="position-fixed" style="top:0;left:0;width:100%;height:3px;background:linear-gradient(90deg,#0ab39c,#405189,#0ab39c);background-size:200%;animation:ks-bar-slide 1.5s linear infinite;z-index:99999;"></div>

    <div class="row">
    <div class="col-12 col-lg-6">
        <div class="row">
            <div class="col-12 col-lg-3">
                <label class="form-label text-muted fw-bold">Filter Tanggal</label>
            </div>
            <div class="col-12 col-lg-9 mb-1">
                <div class="form-group">
                    <div class="input-group">
                        <div class="col-12">
                            <div class="form-group">
                                <div class="input-group">
                                    <input wire:model.defer="tglMasuk" type="date" class="form-control"
                                        style="padding:0.44rem">
                                    <input wire:model.defer="tglKeluar" type="date" class="form-control"
                                        style="padding:0.44rem">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-3">
                <label class="form-label text-muted fw-bold">Nomor LPK</label>
            </div>
            <div class="col-12 col-lg-9 mb-1">
                <div class="input-group">
                    <div class="col-12 col-lg-12 mb-1" x-data="{ lpk_no: @entangle('lpk_no'), status: true }" x-init="$watch('lpk_no', value => {
                        if (value.length === 6 && !value.includes('-') && status) { lpk_no = value + '-'; }
                        if (value.length < 6) { status = true; }
                        if (value.length === 7) { status = false; }
                        if (value.length > 10) { lpk_no = value.substring(0, 10); }
                    })">
                        <input class="form-control" style="padding:0.44rem" type="text"
                            placeholder="000000-000" x-model="lpk_no" maxlength="10" />
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-3">
                <label class="form-label text-muted fw-bold">Nomor Kenpin</label>
            </div>
            <div class="col-12 col-lg-9">
                <div class="input-group">
                    <div class="col-12 col-lg-12 mb-1" x-data="{ searchTerm: @entangle('searchTerm'), status: true }" x-init="$watch('searchTerm', value => {
                        if (value.length === 7 && !value.includes('-') && status) { searchTerm = value + '-'; }
                        if (value.length < 7) { status = true; }
                        if (value.length === 11) { status = false; }
                        if (value.length > 11) { searchTerm = value.substring(0, 11); }
                    })">
                        <input class="form-control" style="padding:0.44rem" type="text"
                            placeholder="_____-_____" x-model="searchTerm" maxlength="8" />
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-6">
        <div class="row">
            <div class="col-12 col-lg-3">
                <label for="product" class="form-label text-muted fw-bold">Product</label>
            </div>
            <div class="col-12 col-lg-9 mb-1">
                <div wire:ignore>
                    <select class="form-control select2-product-ks" style="padding:0.44rem">
                        <option value="">- All -</option>
                        @foreach ($products as $item)
                            <option value="{{ $item->id }}"
                                @if ($item->id == ($idProduct ?? null)) selected @endif>
                                {{ $item->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-12 col-lg-3">
                <label class="form-label text-muted fw-bold">Nomor Palet</label>
            </div>
            <div class="col-12 col-lg-9 mb-1">
                <div class="input-group">
                    <div class="col-6 col-lg-6 mb-1" x-data="{ nomor_palet: @entangle('nomor_palet'), status: true }" x-init="$watch('nomor_palet', value => {
                        if (value.length === 5 && !value.includes('-') && status) { nomor_palet = value + '-'; }
                        if (value.length < 5) { status = true; }
                        if (value.length === 6) { status = false; }
                        if (value.length > 12) { nomor_palet = value.substring(0, 12); }
                    })">
                        <input class="form-control" type="text" placeholder="A0000-000000"
                            x-model="nomor_palet" maxlength="12" />
                    </div>
                    <span class="input-group-text readonly" readonly="readonly">NO. LOT</span>
                    <input wire:model.defer="nomor_lot" type="text" class="form-control" placeholder="---">
                </div>
            </div>
            <div class="col-12 col-lg-3">
                <label for="status" class="form-label text-muted fw-bold">Status</label>
            </div>
            <div class="col-12 col-lg-9">
                <div wire:ignore>
                    <select class="form-control select2-status-ks" style="padding:0.44rem">
                        <option value="">- all -</option>
                        <option value="1" @if (($status ?? null) == 1) selected @endif>Proses</option>
                        <option value="2" @if (($status ?? null) == 2) selected @endif>Finish</option>
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
                    onclick="window.location.href='/add-kenpin-seitai'">
                    <i class="ri-add-line"></i> Add
                </button>
            </div>
        </div>
    </div>

    <div x-data="{
        cols: JSON.parse(localStorage.getItem('kenpin-seitai-cols') || JSON.stringify({1:true,2:true,3:true,4:true,5:true,6:true,7:true,8:true,9:false,10:false})),
    }" x-init="$watch('cols', val => { try { localStorage.setItem('kenpin-seitai-cols', JSON.stringify(val)); } catch(e) {} })"
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
                    <li><label style="cursor:pointer"><input class="form-check-input fs-15 ms-2" type="checkbox" x-model="cols[1]"> Tgl. Kenpin</label></li>
                    <li><label style="cursor:pointer"><input class="form-check-input fs-15 ms-2" type="checkbox" x-model="cols[2]"> No Kenpin</label></li>
                    <li><label style="cursor:pointer"><input class="form-check-input fs-15 ms-2" type="checkbox" x-model="cols[3]"> No Palet</label></li>
                    <li><label style="cursor:pointer"><input class="form-check-input fs-15 ms-2" type="checkbox" x-model="cols[4]"> Nama Produk</label></li>
                    <li><label style="cursor:pointer"><input class="form-check-input fs-15 ms-2" type="checkbox" x-model="cols[5]"> No. Order</label></li>
                    <li><label style="cursor:pointer"><input class="form-check-input fs-15 ms-2" type="checkbox" x-model="cols[6]"> Petugas</label></li>
                    <li><label style="cursor:pointer"><input class="form-check-input fs-15 ms-2" type="checkbox" x-model="cols[7]"> Jumlah Loss (Box)</label></li>
                    <li><label style="cursor:pointer"><input class="form-check-input fs-15 ms-2" type="checkbox" x-model="cols[8]"> Status</label></li>
                    <li><label style="cursor:pointer"><input class="form-check-input fs-15 ms-2" type="checkbox" x-model="cols[9]"> Update By</label></li>
                    <li><label style="cursor:pointer"><input class="form-check-input fs-15 ms-2" type="checkbox" x-model="cols[10]"> Updated</label></li>
                </ul>
            </div>
        </div>

        <div wire:loading.class="opacity-50"
             wire:target="search,sortBy,gotoPage,nextPage,previousPage,perPage"
             style="overflow-x:auto; overflow-y:auto; max-height:65vh; transition: opacity 0.15s;">
            <table class="table align-middle table-nowrap table-hover" id="kenpinSeitaiTable">
                <thead class="table-light">
                    <tr>
                        <th style="width:36px"></th>
                        <th :class="{'d-none': !cols[1]}" wire:click="sortBy('tdkg.kenpin_date')" style="cursor:pointer;white-space:nowrap">
                            Tgl. Kenpin <i class="{{ $sortColumn === 'tdkg.kenpin_date' ? ($sortDirection === 'asc' ? 'ri-arrow-up-s-line text-primary' : 'ri-arrow-down-s-line text-primary') : 'ri-expand-up-down-line text-muted' }} fs-12"></i>
                        </th>
                        <th :class="{'d-none': !cols[2]}" wire:click="sortBy('tdkg.kenpin_no')" style="cursor:pointer;white-space:nowrap">
                            No Kenpin <i class="{{ $sortColumn === 'tdkg.kenpin_no' ? ($sortDirection === 'asc' ? 'ri-arrow-up-s-line text-primary' : 'ri-arrow-down-s-line text-primary') : 'ri-expand-up-down-line text-muted' }} fs-12"></i>
                        </th>
                        <th :class="{'d-none': !cols[3]}" wire:click="sortBy('pg.nomor_palet')" style="cursor:pointer;white-space:nowrap">
                            No Palet <i class="{{ $sortColumn === 'pg.nomor_palet' ? ($sortDirection === 'asc' ? 'ri-arrow-up-s-line text-primary' : 'ri-arrow-down-s-line text-primary') : 'ri-expand-up-down-line text-muted' }} fs-12"></i>
                        </th>
                        <th :class="{'d-none': !cols[4]}" wire:click="sortBy('msp.name')" style="cursor:pointer;white-space:nowrap">
                            Nama Produk <i class="{{ $sortColumn === 'msp.name' ? ($sortDirection === 'asc' ? 'ri-arrow-up-s-line text-primary' : 'ri-arrow-down-s-line text-primary') : 'ri-expand-up-down-line text-muted' }} fs-12"></i>
                        </th>
                        <th :class="{'d-none': !cols[5]}" wire:click="sortBy('msp.code')" style="cursor:pointer;white-space:nowrap">
                            No. Order <i class="{{ $sortColumn === 'msp.code' ? ($sortDirection === 'asc' ? 'ri-arrow-up-s-line text-primary' : 'ri-arrow-down-s-line text-primary') : 'ri-expand-up-down-line text-muted' }} fs-12"></i>
                        </th>
                        <th :class="{'d-none': !cols[6]}" wire:click="sortBy('mse.empname')" style="cursor:pointer;white-space:nowrap">
                            Petugas <i class="{{ $sortColumn === 'mse.empname' ? ($sortDirection === 'asc' ? 'ri-arrow-up-s-line text-primary' : 'ri-arrow-down-s-line text-primary') : 'ri-expand-up-down-line text-muted' }} fs-12"></i>
                        </th>
                        <th :class="{'d-none': !cols[7]}" wire:click="sortBy('tdkg.qty_loss')" style="cursor:pointer;white-space:nowrap">
                            Jumlah Loss (Box) <i class="{{ $sortColumn === 'tdkg.qty_loss' ? ($sortDirection === 'asc' ? 'ri-arrow-up-s-line text-primary' : 'ri-arrow-down-s-line text-primary') : 'ri-expand-up-down-line text-muted' }} fs-12"></i>
                        </th>
                        <th :class="{'d-none': !cols[8]}">Status</th>
                        <th :class="{'d-none': !cols[9]}">Update By</th>
                        <th :class="{'d-none': !cols[10]}" wire:click="sortBy('tdkg.updated_on')" style="cursor:pointer;white-space:nowrap">
                            Updated <i class="{{ $sortColumn === 'tdkg.updated_on' ? ($sortDirection === 'asc' ? 'ri-arrow-up-s-line text-primary' : 'ri-arrow-down-s-line text-primary') : 'ri-expand-up-down-line text-muted' }} fs-12"></i>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data as $item)
                        <tr>
                            <td>
                                <a href="/edit-kenpin-seitai?orderId={{ $item->id }}"
                                    class="link-success fs-15 p-1 bg-primary rounded">
                                    <i class="ri-edit-box-line text-white"></i>
                                </a>
                            </td>
                            <td :class="{'d-none': !cols[1]}">{{ \Carbon\Carbon::parse($item->kenpin_date)->format('d M Y') }}</td>
                            <td :class="{'d-none': !cols[2]}">{{ $item->kenpin_no }}</td>
                            <td :class="{'d-none': !cols[3]}">{{ $item->nomor_palet }}</td>
                            <td :class="{'d-none': !cols[4]}">{{ $item->namaproduk }}</td>
                            <td :class="{'d-none': !cols[5]}">{{ $item->code }}</td>
                            <td :class="{'d-none': !cols[6]}">{{ $item->namapetugas }}</td>
                            <td :class="{'d-none': !cols[7]}">{{ number_format($item->qty_loss) }}</td>
                            <td :class="{'d-none': !cols[8]}">{{ $item->status_kenpin == 2 ? 'Finish' : 'Proses' }}</td>
                            <td :class="{'d-none': !cols[9]}">{{ $item->updated_by }}</td>
                            <td :class="{'d-none': !cols[10]}">{{ \Carbon\Carbon::parse($item->updated_on)->format('d M Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center py-4">
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
        @keyframes ks-bar-slide {
            0%   { background-position: 0% 0%; }
            100% { background-position: 200% 0%; }
        }
        #kenpinSeitaiTable.table>:not(caption)>*>* {
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
                if ($('.select2-product-ks').hasClass('select2-hidden-accessible')) {
                    $('.select2-product-ks').select2('destroy');
                }
                $('.select2-product-ks').select2({
                    theme: 'bootstrap-5',
                    allowClear: true,
                    placeholder: '- All -',
                }).on('change', function() {
                    @this.set('idProduct', $(this).val() || null);
                });
            }

            function initStatusSelect() {
                if ($('.select2-status-ks').hasClass('select2-hidden-accessible')) {
                    $('.select2-status-ks').select2('destroy');
                }
                $('.select2-status-ks').select2({
                    theme: 'bootstrap-5',
                    allowClear: true,
                    placeholder: '- all -',
                }).on('change', function() {
                    @this.set('status', $(this).val() || null);
                });
            }

            initProductSelect();
            initStatusSelect();

            Livewire.hook('morph', ({ el, component }) => {
                setTimeout(() => {
                    initProductSelect();
                    initStatusSelect();
                }, 100);
            });
        });
    </script>
@endscript
