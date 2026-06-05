<div wire:init="loadData">
    @if (!$isLoaded)
        <div>
            <div class="placeholder-glow mb-3" style="height:80px">
                <span class="placeholder col-12 rounded" style="height:80px"></span>
            </div>
            <div style="overflow:hidden;border-radius:4px">
                @for ($i = 0; $i < 8; $i++)
                    <div style="height:36px;margin-bottom:2px;border-radius:3px;background:linear-gradient(90deg,#e9ecef 25%,#f8f9fa 50%,#e9ecef 75%);background-size:200% 100%;animation:le-bar-slide 1.2s {{ $i * 0.1 }}s infinite linear"></div>
                @endfor
            </div>
            <style>
                @keyframes le-bar-slide {
                    0%   { background-position: 200% 0; }
                    100% { background-position: -200% 0; }
                }
            </style>
        </div>
    @else
        <div class="row filter-section">
            <div class="col-12 col-lg-7">
                <div class="row">
                    <div class="col-12 col-lg-3">
                        <label class="form-label text-muted fw-bold">Filter Tanggal</label>
                    </div>
                    <div class="col-12 col-lg-9 mb-1">
                        <div class="input-group flex-column flex-sm-row">
                            <div class="col-12 col-sm-3 mb-2 mb-sm-0">
                                <select class="form-select" style="padding:0.44rem" wire:model.defer="transaksi">
                                    <option value="1">Proses</option>
                                    <option value="2">LPK</option>
                                </select>
                            </div>
                            <div class="col-12 col-sm-9">
                                <div class="d-flex flex-column flex-sm-row gap-2">
                                    <input wire:model.defer="tglMasuk" type="date" class="form-control" style="padding:0.44rem" value="{{ $tglMasuk }}">
                                    <input wire:model.defer="tglKeluar" type="date" class="form-control" style="padding:0.44rem" value="{{ $tglKeluar }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-3">
                        <label class="form-label text-muted fw-bold">Nomor LPK</label>
                    </div>
                    <div class="col-12 col-lg-9 mb-1">
                        <div class="input-group" x-data="{ lpk_no: '{{ $lpk_no ?? '' }}', status: true }" x-init="$watch('lpk_no', value => {
                            if (value.length === 6 && !value.includes('-') && status) {
                                lpk_no = value + '-';
                            }
                            if (value.length < 6) { status = true; }
                            if (value.length === 7) { status = false; }
                            if (value.length > 10) { lpk_no = value.substring(0, 11); }
                        })">
                            <input x-model="lpk_no" @input="$wire.set('lpk_no', lpk_no)"
                                class="form-control" style="padding:0.44rem" type="text"
                                placeholder="000000-000" maxlength="10" />
                        </div>
                    </div>
                    <div class="col-12 col-lg-3">
                        <label class="form-label text-muted fw-bold">Search</label>
                    </div>
                    <div class="col-12 col-lg-9">
                        <input wire:model.defer="searchTerm" class="form-control" style="padding:0.44rem" type="text"
                            placeholder="search nomor PO atau nama produk" />
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-5">
                <div class="row">
                    <div class="col-12 col-lg-2">
                        <label class="form-label text-muted fw-bold">Product</label>
                    </div>
                    <div class="col-12 col-lg-10 mb-1">
                        <div wire:ignore>
                            <select class="form-control select2-product-le">
                                <option value="">- All -</option>
                                @foreach ($products as $item)
                                    <option value="{{ $item->id }}" @if ($item->id == ($idProduct ?? null)) selected @endif>
                                        {{ $item->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-12 col-lg-2">
                        <label class="form-label text-muted fw-bold">LPK Color</label>
                    </div>
                    <div class="col-12 col-lg-10 mb-1">
                        <div wire:ignore>
                            <select class="form-control select2-lpkcolor-le">
                                <option value="">- All -</option>
                                @foreach ($lpkColors as $item)
                                    <option value="{{ $item->id }}" @if ($item->id == ($idLPKColor ?? null)) selected @endif>
                                        {{ $item->name }} ({{ $item->code }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-12 col-lg-2">
                        <label class="form-label text-muted fw-bold">Buyer</label>
                    </div>
                    <div class="col-12 col-lg-10 mb-1">
                        <div wire:ignore>
                            <select class="form-control select2-buyer-le">
                                <option value="">- All -</option>
                                @foreach ($buyer as $item)
                                    <option value="{{ $item->id }}" @if ($item->id == ($idBuyer ?? null)) selected @endif>
                                        {{ $item->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-12 col-lg-2">
                        <label class="form-label text-muted fw-bold">Status</label>
                    </div>
                    <div class="col-12 col-lg-10 mb-1">
                        <div wire:ignore>
                            <select class="form-control select2-status-le">
                                <option value="">- All -</option>
                                <option value="0" @if (($status ?? '') == '0') selected @endif>Un-Print</option>
                                <option value="1" @if (($status ?? '') == '1') selected @endif>Printed</option>
                                <option value="2" @if (($status ?? '') == '2') selected @endif>Re-Print</option>
                                <option value="3" @if (($status ?? '') == '3') selected @endif>Belum Produksi</option>
                                <option value="4" @if (($status ?? '') == '4') selected @endif>Sudah Produksi</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12 mt-2">
                <div class="row">
                    <div class="col-12 col-lg-5">
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
                        <button type="button" class="btn btn-success w-lg p-1" onclick="window.location.href='/add-lpk'">
                            <i class="ri-add-line"></i> Add
                        </button>
                    </div>
                    <div class="col-12 col-lg-7 d-none d-sm-block">
                        <input type="file" id="fileInput" wire:model="file" style="display: none;">
                        <button class="btn btn-success w-lg p-1" type="button"
                            onclick="document.getElementById('fileInput').click()" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="file">
                                <i class="ri-upload-2-fill"></i> Upload Excel
                            </span>
                            <div wire:loading wire:target="file">
                                <span class="d-flex align-items-center">
                                    <span class="spinner-border flex-shrink-0" role="status"></span>
                                    <span class="flex-grow-1 ms-1">Loading...</span>
                                </span>
                            </div>
                        </button>
                        <button class="btn btn-primary w-lg p-1" wire:click="download" type="button" wire:loading.attr="disabled">
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
                        <button class="btn btn-info w-lg p-1" wire:click="print" type="button" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="print">
                                <i class="ri-printer-line"></i> Export
                            </span>
                            <div wire:loading wire:target="print">
                                <span class="d-flex align-items-center">
                                    <span class="spinner-border flex-shrink-0" role="status"></span>
                                    <span class="flex-grow-1 ms-1">Loading...</span>
                                </span>
                            </div>
                        </button>
                        <button class="btn btn-info w-lg p-1" wire:click="printLPK" type="button" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="printLPK">
                                <i class="ri-printer-line"></i> Cetak LPK
                            </span>
                            <div wire:loading wire:target="printLPK">
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
            cols: JSON.parse(localStorage.getItem('lpk-entry-cols') || JSON.stringify({1:true,2:false,3:true,4:true,5:true,6:true,7:true,8:false,9:true,10:true,11:true,12:false,13:true,14:false,15:false,16:true,17:false,18:false,19:false})),
        }" x-init="$watch('cols', val => { try { localStorage.setItem('lpk-entry-cols', JSON.stringify(val)); } catch(e) {} })"
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
                    <ul class="dropdown-menu dropdown-menu-end" style="min-width:160px">
                        <li><label class="dropdown-item" style="cursor:pointer"><input type="checkbox" x-model="cols[1]" class="form-check-input me-1"> No LPK</label></li>
                        <li><label class="dropdown-item" style="cursor:pointer"><input type="checkbox" x-model="cols[2]" class="form-check-input me-1"> Warna LPK</label></li>
                        <li><label class="dropdown-item" style="cursor:pointer"><input type="checkbox" x-model="cols[3]" class="form-check-input me-1"> Tgl LPK</label></li>
                        <li><label class="dropdown-item" style="cursor:pointer"><input type="checkbox" x-model="cols[4]" class="form-check-input me-1"> Panjang LPK</label></li>
                        <li><label class="dropdown-item" style="cursor:pointer"><input type="checkbox" x-model="cols[5]" class="form-check-input me-1"> Jumlah LPK</label></li>
                        <li><label class="dropdown-item" style="cursor:pointer"><input type="checkbox" x-model="cols[6]" class="form-check-input me-1"> Jumlah Gentan</label></li>
                        <li><label class="dropdown-item" style="cursor:pointer"><input type="checkbox" x-model="cols[7]" class="form-check-input me-1"> Master Gulung</label></li>
                        <li><label class="dropdown-item" style="cursor:pointer"><input type="checkbox" x-model="cols[8]" class="form-check-input me-1"> Selisih</label></li>
                        <li><label class="dropdown-item" style="cursor:pointer"><input type="checkbox" x-model="cols[9]" class="form-check-input me-1"> Progres Infure</label></li>
                        <li><label class="dropdown-item" style="cursor:pointer"><input type="checkbox" x-model="cols[10]" class="form-check-input me-1"> Progres Seitai</label></li>
                        <li><label class="dropdown-item" style="cursor:pointer"><input type="checkbox" x-model="cols[11]" class="form-check-input me-1"> Nomor PO</label></li>
                        <li><label class="dropdown-item" style="cursor:pointer"><input type="checkbox" x-model="cols[12]" class="form-check-input me-1"> Nama Produk</label></li>
                        <li><label class="dropdown-item" style="cursor:pointer"><input type="checkbox" x-model="cols[13]" class="form-check-input me-1"> Kode Produk</label></li>
                        <li><label class="dropdown-item" style="cursor:pointer"><input type="checkbox" x-model="cols[14]" class="form-check-input me-1"> Mesin</label></li>
                        <li><label class="dropdown-item" style="cursor:pointer"><input type="checkbox" x-model="cols[15]" class="form-check-input me-1"> Buyer</label></li>
                        <li><label class="dropdown-item" style="cursor:pointer"><input type="checkbox" x-model="cols[16]" class="form-check-input me-1"> Tanggal Proses</label></li>
                        <li><label class="dropdown-item" style="cursor:pointer"><input type="checkbox" x-model="cols[17]" class="form-check-input me-1"> Seq</label></li>
                        <li><label class="dropdown-item" style="cursor:pointer"><input type="checkbox" x-model="cols[18]" class="form-check-input me-1"> Update By</label></li>
                        <li><label class="dropdown-item" style="cursor:pointer"><input type="checkbox" x-model="cols[19]" class="form-check-input me-1"> Updated</label></li>
                    </ul>
                </div>
            </div>

            <div wire:loading.class="opacity-50"
                 wire:target="search,sortBy,gotoPage,nextPage,previousPage,perPage"
                 style="overflow-x:auto; overflow-y:auto; max-height:65vh; transition: opacity 0.15s;">
                <table class="table align-middle table-nowrap table-hover" id="LPKEntryTable">
                    <thead class="table-light">
                        <tr>
                            <th style="width:36px">
                                <div class="form-check">
                                    <input class="form-check-input checkbox-big fs-15" type="checkbox" id="checkAll" value="optionAll">
                                </div>
                            </th>
                            <th style="width:36px"></th>
                            <th :class="{'d-none': !cols[1]}" wire:click="sortBy('tolp.lpk_no')" style="cursor:pointer;white-space:nowrap">
                                No LPK <i class="{{ $sortColumn === 'tolp.lpk_no' ? ($sortDirection === 'asc' ? 'ri-arrow-up-s-line text-primary' : 'ri-arrow-down-s-line text-primary') : 'ri-expand-up-down-line text-muted' }} fs-12"></i>
                            </th>
                            <th :class="{'d-none': !cols[2]}" style="white-space:nowrap">Warna LPK</th>
                            <th :class="{'d-none': !cols[3]}" wire:click="sortBy('tolp.lpk_date')" style="cursor:pointer;white-space:nowrap">
                                Tgl LPK <i class="{{ $sortColumn === 'tolp.lpk_date' ? ($sortDirection === 'asc' ? 'ri-arrow-up-s-line text-primary' : 'ri-arrow-down-s-line text-primary') : 'ri-expand-up-down-line text-muted' }} fs-12"></i>
                            </th>
                            <th :class="{'d-none': !cols[4]}" wire:click="sortBy('tolp.panjang_lpk')" style="cursor:pointer;white-space:nowrap">
                                Panjang LPK <i class="{{ $sortColumn === 'tolp.panjang_lpk' ? ($sortDirection === 'asc' ? 'ri-arrow-up-s-line text-primary' : 'ri-arrow-down-s-line text-primary') : 'ri-expand-up-down-line text-muted' }} fs-12"></i>
                            </th>
                            <th :class="{'d-none': !cols[5]}" wire:click="sortBy('tolp.qty_lpk')" style="cursor:pointer;white-space:nowrap">
                                Jumlah LPK <i class="{{ $sortColumn === 'tolp.qty_lpk' ? ($sortDirection === 'asc' ? 'ri-arrow-up-s-line text-primary' : 'ri-arrow-down-s-line text-primary') : 'ri-expand-up-down-line text-muted' }} fs-12"></i>
                            </th>
                            <th :class="{'d-none': !cols[6]}" wire:click="sortBy('tolp.qty_gentan')" style="cursor:pointer;white-space:nowrap">
                                Jumlah Gentan <i class="{{ $sortColumn === 'tolp.qty_gentan' ? ($sortDirection === 'asc' ? 'ri-arrow-up-s-line text-primary' : 'ri-arrow-down-s-line text-primary') : 'ri-expand-up-down-line text-muted' }} fs-12"></i>
                            </th>
                            <th :class="{'d-none': !cols[7]}" wire:click="sortBy('tolp.qty_gulung')" style="cursor:pointer;white-space:nowrap">
                                Master Gulung <i class="{{ $sortColumn === 'tolp.qty_gulung' ? ($sortDirection === 'asc' ? 'ri-arrow-up-s-line text-primary' : 'ri-arrow-down-s-line text-primary') : 'ri-expand-up-down-line text-muted' }} fs-12"></i>
                            </th>
                            <th :class="{'d-none': !cols[8]}" style="white-space:nowrap">Selisih</th>
                            <th :class="{'d-none': !cols[9]}" wire:click="sortBy('tolp.total_assembly_line')" style="cursor:pointer;white-space:nowrap">
                                Progres Infure <i class="{{ $sortColumn === 'tolp.total_assembly_line' ? ($sortDirection === 'asc' ? 'ri-arrow-up-s-line text-primary' : 'ri-arrow-down-s-line text-primary') : 'ri-expand-up-down-line text-muted' }} fs-12"></i>
                            </th>
                            <th :class="{'d-none': !cols[10]}" wire:click="sortBy('tolp.total_assembly_qty')" style="cursor:pointer;white-space:nowrap">
                                Progres Seitai <i class="{{ $sortColumn === 'tolp.total_assembly_qty' ? ($sortDirection === 'asc' ? 'ri-arrow-up-s-line text-primary' : 'ri-arrow-down-s-line text-primary') : 'ri-expand-up-down-line text-muted' }} fs-12"></i>
                            </th>
                            <th :class="{'d-none': !cols[11]}" wire:click="sortBy('tod.po_no')" style="cursor:pointer;white-space:nowrap">
                                Nomor PO <i class="{{ $sortColumn === 'tod.po_no' ? ($sortDirection === 'asc' ? 'ri-arrow-up-s-line text-primary' : 'ri-arrow-down-s-line text-primary') : 'ri-expand-up-down-line text-muted' }} fs-12"></i>
                            </th>
                            <th :class="{'d-none': !cols[12]}" wire:click="sortBy('mp.name')" style="cursor:pointer;white-space:nowrap">
                                Nama Produk <i class="{{ $sortColumn === 'mp.name' ? ($sortDirection === 'asc' ? 'ri-arrow-up-s-line text-primary' : 'ri-arrow-down-s-line text-primary') : 'ri-expand-up-down-line text-muted' }} fs-12"></i>
                            </th>
                            <th :class="{'d-none': !cols[13]}" wire:click="sortBy('mp.code')" style="cursor:pointer;white-space:nowrap">
                                Kode Produk <i class="{{ $sortColumn === 'mp.code' ? ($sortDirection === 'asc' ? 'ri-arrow-up-s-line text-primary' : 'ri-arrow-down-s-line text-primary') : 'ri-expand-up-down-line text-muted' }} fs-12"></i>
                            </th>
                            <th :class="{'d-none': !cols[14]}" wire:click="sortBy('mm.machineno')" style="cursor:pointer;white-space:nowrap">
                                Mesin <i class="{{ $sortColumn === 'mm.machineno' ? ($sortDirection === 'asc' ? 'ri-arrow-up-s-line text-primary' : 'ri-arrow-down-s-line text-primary') : 'ri-expand-up-down-line text-muted' }} fs-12"></i>
                            </th>
                            <th :class="{'d-none': !cols[15]}" wire:click="sortBy('mbu.name')" style="cursor:pointer;white-space:nowrap">
                                Buyer <i class="{{ $sortColumn === 'mbu.name' ? ($sortDirection === 'asc' ? 'ri-arrow-up-s-line text-primary' : 'ri-arrow-down-s-line text-primary') : 'ri-expand-up-down-line text-muted' }} fs-12"></i>
                            </th>
                            <th :class="{'d-none': !cols[16]}" wire:click="sortBy('tolp.created_on')" style="cursor:pointer;white-space:nowrap">
                                Tanggal Proses <i class="{{ $sortColumn === 'tolp.created_on' ? ($sortDirection === 'asc' ? 'ri-arrow-up-s-line text-primary' : 'ri-arrow-down-s-line text-primary') : 'ri-expand-up-down-line text-muted' }} fs-12"></i>
                            </th>
                            <th :class="{'d-none': !cols[17]}" wire:click="sortBy('tolp.seq_no')" style="cursor:pointer;white-space:nowrap">
                                Seq <i class="{{ $sortColumn === 'tolp.seq_no' ? ($sortDirection === 'asc' ? 'ri-arrow-up-s-line text-primary' : 'ri-arrow-down-s-line text-primary') : 'ri-expand-up-down-line text-muted' }} fs-12"></i>
                            </th>
                            <th :class="{'d-none': !cols[18]}" style="white-space:nowrap">Update By</th>
                            <th :class="{'d-none': !cols[19]}" wire:click="sortBy('tolp.updated_on')" style="cursor:pointer;white-space:nowrap">
                                Updated <i class="{{ $sortColumn === 'tolp.updated_on' ? ($sortDirection === 'asc' ? 'ri-arrow-up-s-line text-primary' : 'ri-arrow-down-s-line text-primary') : 'ri-expand-up-down-line text-muted' }} fs-12"></i>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($data as $item)
                            <tr>
                                <td>
                                    <div class="form-check text-center">
                                        <input class="form-check-input fs-15 checkbox-big checkListLPK" type="checkbox"
                                            wire:model="checkListLPK" value="{{ $item->id }}">
                                    </div>
                                </td>
                                <td>
                                    <a href="/edit-lpk?orderId={{ $item->id }}"
                                        class="link-success fs-15 p-1 bg-primary rounded">
                                        <i class="ri-edit-box-line text-white"></i>
                                    </a>
                                </td>
                                <td :class="{'d-none': !cols[1]}">{{ $item->lpk_no }}</td>
                                <td :class="{'d-none': !cols[2]}">{{ $item->warna_lpk }}</td>
                                <td :class="{'d-none': !cols[3]}">{{ \Carbon\Carbon::parse($item->lpk_date)->format('d M Y') }}</td>
                                <td :class="{'d-none': !cols[4]}">{{ number_format($item->panjang_lpk) }}</td>
                                <td :class="{'d-none': !cols[5]}">{{ number_format($item->qty_lpk) }}</td>
                                <td :class="{'d-none': !cols[6]}">{{ $item->qty_gentan }}</td>
                                <td :class="{'d-none': !cols[7]}">{{ number_format($item->qty_gulung) }}</td>
                                <td :class="{'d-none': !cols[8]}">{{ number_format($item->selisih) }}</td>
                                <td :class="{'d-none': !cols[9]}">{{ number_format($item->infure) }}</td>
                                <td :class="{'d-none': !cols[10]}">{{ number_format($item->total_assembly_qty) }}</td>
                                <td :class="{'d-none': !cols[11]}">{{ $item->po_no }}</td>
                                <td :class="{'d-none': !cols[12]}">{{ $item->product_name }}</td>
                                <td :class="{'d-none': !cols[13]}">{{ $item->product_code }}</td>
                                <td :class="{'d-none': !cols[14]}">{{ $item->machine_no }}</td>
                                <td :class="{'d-none': !cols[15]}">{{ $item->buyer_name }}</td>
                                <td :class="{'d-none': !cols[16]}">{{ \Carbon\Carbon::parse($item->created_on)->format('d M Y') }}</td>
                                <td :class="{'d-none': !cols[17]}">{{ $item->seq_no }}</td>
                                <td :class="{'d-none': !cols[18]}">{{ $item->updated_by }}</td>
                                <td :class="{'d-none': !cols[19]}">{{ \Carbon\Carbon::parse($item->updatedt)->format('d M Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="21" class="text-center py-4">
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
            .checkbox-big {
                width: 20px;
                height: 20px;
                border: 2px solid #333;
                cursor: pointer;
            }
            .checkbox-big:checked {
                background-color: #0d6efd;
                border-color: #0d6efd;
            }
            #LPKEntryTable.table > :not(caption) > * > * {
                font-size: 13px !important;
                padding: 4px 2px 4px 4px;
            }
        </style>
    @endif
</div>

@script
<script>
    $wire.on('redirectToPrint', (lpk_ids) => {
        var printUrl = '{{ route('report-lpk') }}?lpk_ids=' + lpk_ids;
        window.open(printUrl, '_blank');
    });

    document.addEventListener('livewire:initialized', function() {
        function initProductSelect() {
            if ($('.select2-product-le').hasClass('select2-hidden-accessible')) {
                $('.select2-product-le').select2('destroy');
            }
            $('.select2-product-le').select2({
                theme: 'bootstrap-5', allowClear: true, placeholder: '- All -',
            }).on('change', function() {
                @this.set('idProduct', $(this).val() || null);
            });
        }

        function initLpkColorSelect() {
            if ($('.select2-lpkcolor-le').hasClass('select2-hidden-accessible')) {
                $('.select2-lpkcolor-le').select2('destroy');
            }
            $('.select2-lpkcolor-le').select2({
                theme: 'bootstrap-5', allowClear: true, placeholder: '- All -',
            }).on('change', function() {
                @this.set('idLPKColor', $(this).val() || null);
            });
        }

        function initBuyerSelect() {
            if ($('.select2-buyer-le').hasClass('select2-hidden-accessible')) {
                $('.select2-buyer-le').select2('destroy');
            }
            $('.select2-buyer-le').select2({
                theme: 'bootstrap-5', allowClear: true, placeholder: '- All -',
            }).on('change', function() {
                @this.set('idBuyer', $(this).val() || null);
            });
        }

        function initStatusSelect() {
            if ($('.select2-status-le').hasClass('select2-hidden-accessible')) {
                $('.select2-status-le').select2('destroy');
            }
            $('.select2-status-le').select2({
                theme: 'bootstrap-5', allowClear: true, placeholder: '- All -',
            }).on('change', function() {
                var val = $(this).val();
                @this.set('status', val !== '' ? val : null);
            });
        }

        function initCheckAll() {
            $('#checkAll').off('click').on('click', function() {
                var isChecked = $(this).is(':checked');
                $('.checkListLPK').each(function() {
                    $(this).prop('checked', isChecked).trigger('change');
                });
                @this.set('checkListLPK', $('.checkListLPK:checked').map(function() {
                    return this.value;
                }).get(), false);
            });

            $(document).off('change', '.checkListLPK').on('change', '.checkListLPK', function() {
                $('#checkAll').prop('checked', $('.checkListLPK:checked').length === $('.checkListLPK').length);
                @this.set('checkListLPK', $('.checkListLPK:checked').map(function() {
                    return this.value;
                }).get(), false);
            });
        }

        initProductSelect();
        initLpkColorSelect();
        initBuyerSelect();
        initStatusSelect();
        initCheckAll();

        Livewire.hook('morph', ({ el, component }) => {
            setTimeout(() => {
                initProductSelect();
                initLpkColorSelect();
                initBuyerSelect();
                initStatusSelect();
                initCheckAll();
            }, 100);
        });
    });
</script>
@endscript
