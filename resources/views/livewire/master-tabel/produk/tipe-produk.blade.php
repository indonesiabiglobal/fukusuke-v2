<div class="row">
    <div class="col-lg-12 mt-2">
        <div class="row">

            <div class="col-12 col-lg-6">
                <button class="btn btn-primary w-lg p-1" wire:click="download" type="button">
                    <span wire:loading.remove wire:target="download">
                        <i class="ri-download-cloud-2-line"> </i> Download
                    </span>
                    <div wire:loading wire:target="download">
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
                <button class="btn btn-info w-lg p-1" wire:click="print" type="button">
                    <span wire:loading.remove wire:target="print">
                        <i class="ri-printer-line"> </i> Print
                    </span>
                    <div wire:loading wire:target="print">
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

                {{-- Button Add buyer --}}
                <button type="button" class="btn btn-success w-lg p-1" data-bs-toggle="modal"
                    data-bs-target="#modal-addBuyer">
                    <i class="ri-add-line"> </i> Add
                </button>
                {{-- modal add buyer --}}
                <div class="modal fade" id="modal-addBuyer" tabindex="-1" aria-labelledby="modal-addBuyerLabel"
                    aria-modal="true" wire:ignore.self>
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modal-addBuyerLabel">Add Tipe Produk</h5> <button
                                    type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form wire:submit.prevent="store">
                                    <div class="row g-3">
                                        {{-- kode buyer --}}
                                        <div class="col-xxl-12">
                                            <div class="row">
                                                <div class="col-5">
                                                    <label for="code" class="form-label">Kode Tipe Produk</label>
                                                </div>
                                                <div class="col-7">
                                                    <input type="number"
                                                        class="form-control @error('code') is-invalid @enderror"
                                                        id="code" wire:model.defer="code" placeholder="Kode">
                                                    @error('code')
                                                        <span class="invalid-feedback">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        {{-- nama buyer --}}
                                        <div class="col-xxl-12">
                                            <div class="row">
                                                <div class="col-5">
                                                    <label for="name" class="form-label">Nama Tipe Produk</label>
                                                </div>
                                                <div class="col-7">
                                                    <input type="text"
                                                        class="form-control @error('name') is-invalid @enderror"
                                                        id="name" wire:model.defer="name" placeholder="Nama">
                                                    @error('name')
                                                        <span class="invalid-feedback">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        {{-- Jenis Produk --}}
                                        <div class="col-xxl-12">
                                            <div class="row">
                                                <div class="col-5">
                                                    <label for="Jenis Produk" class="form-label">Jenis Produk</label>
                                                </div>
                                                <div class="col-7">
                                                    <select class="form-control col-12 col-lg-3 @error('product_group_id') is-invalid @enderror" wire:model.defer="product_group_id" placeholder="" data-choices data-choices-sorting-false data-choices-removeItem>
                                                        <option value="">
                                                            Silahkan Pilih
                                                        </option>
                                                        @foreach ($productGroups as $item)
                                                        <option value="{{ $item->id }}">
                                                            {{ $item->name }}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                    @error('product_group_id')
                                                        <span class="invalid-feedback">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        {{-- Harga Satuan Infure --}}
                                        <div class="col-xxl-12">
                                            <div class="row">
                                                <div class="col-5">
                                                    <label for="harga_sat_infure" class="form-label">Harga Satuan Infure</label>
                                                </div>
                                                <div class="col-7">
                                                    <input type="number"
                                                        class="form-control @error('harga_sat_infure') is-invalid @enderror"
                                                        id="harga_sat_infure" wire:model.defer="harga_sat_infure" placeholder="Harga Satuan Infure">
                                                    @error('harga_sat_infure')
                                                        <span class="invalid-feedback">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        {{-- Harga Satuan Infure --}}
                                        <div class="col-xxl-12">
                                            <div class="row">
                                                <div class="col-5">
                                                    <label for="harga_sat_infure_loss" class="form-label">Harga Satuan Loss Infure</label>
                                                </div>
                                                <div class="col-7">
                                                    <input type="number"
                                                        class="form-control @error('harga_sat_infure_loss') is-invalid @enderror"
                                                        id="harga_sat_infure_loss" wire:model.defer="harga_sat_infure_loss" placeholder="Harga Satuan Loss Infure">
                                                    @error('harga_sat_infure_loss')
                                                        <span class="invalid-feedback">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        {{-- Harga Satuan inline --}}
                                        <div class="col-xxl-12">
                                            <div class="row">
                                                <div class="col-5">
                                                    <label for="harga_sat_inline" class="form-label">Harga Satuan Inline</label>
                                                </div>
                                                <div class="col-7">
                                                    <input type="number"
                                                        class="form-control @error('harga_sat_inline') is-invalid @enderror"
                                                        id="harga_sat_inline" wire:model.defer="harga_sat_inline" placeholder="Harga Satuan Inline">
                                                    @error('harga_sat_inline')
                                                        <span class="invalid-feedback">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        {{-- Harga Satuan cetak --}}
                                        <div class="col-xxl-12">
                                            <div class="row">
                                                <div class="col-5">
                                                    <label for="harga_sat_cetak" class="form-label">Harga Satuan cetak</label>
                                                </div>
                                                <div class="col-7">
                                                    <input type="text"
                                                        class="form-control @error('harga_sat_cetak') is-invalid @enderror"
                                                        id="harga_sat_cetak" wire:model.defer="harga_sat_cetak" placeholder="Harga Satuan cetak">
                                                    @error('harga_sat_cetak')
                                                        <span class="invalid-feedback">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        {{-- Harga Satuan seitai --}}
                                        <div class="col-xxl-12">
                                            <div class="row">
                                                <div class="col-5">
                                                    <label for="harga_sat_seitai" class="form-label">Harga Satuan seitai</label>
                                                </div>
                                                <div class="col-7">
                                                    <input type="text"
                                                        class="form-control @error('harga_sat_seitai') is-invalid @enderror"
                                                        id="harga_sat_seitai" wire:model.defer="harga_sat_seitai" placeholder="Harga Satuan seitai">
                                                    @error('harga_sat_seitai')
                                                        <span class="invalid-feedback">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        {{-- Harga Satuan seitai_loss --}}
                                        <div class="col-xxl-12">
                                            <div class="row">
                                                <div class="col-5">
                                                    <label for="harga_sat_seitai_loss" class="form-label">Harga Satuan Seitai Loss</label>
                                                </div>
                                                <div class="col-7">
                                                    <input type="text"
                                                        class="form-control @error('harga_sat_seitai_loss') is-invalid @enderror"
                                                        id="harga_sat_seitai_loss" wire:model.defer="harga_sat_seitai_loss" placeholder="Harga Satuan seitai_loss">
                                                    @error('harga_sat_seitai_loss')
                                                        <span class="invalid-feedback">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        {{-- Berat Jenis --}}
                                        <div class="col-xxl-12">
                                            <div class="row">
                                                <div class="col-5">
                                                    <label for="berat_jenis" class="form-label" >Berat Jenis</label>
                                                </div>
                                                <div class="col-7">
                                                    <input type="text"
                                                        class="form-control @error('berat_jenis') is-invalid @enderror"
                                                        id="berat_jenis" wire:model.defer="berat_jenis" placeholder="Berat Jenis">
                                                    @error('berat_jenis')
                                                        <span class="invalid-feedback">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        {{-- button --}}
                                        <div class="col-lg-12">
                                            <div class="hstack gap-2 justify-content-end">
                                                <button type="button" class="btn btn-light"
                                                    data-bs-dismiss="modal">Close</button>
                                                <button id="btnCreate" type="submit" class="btn btn-success w-lg">
                                                    <span wire:loading.remove wire:target="save">
                                                        <i class="ri-save-3-line"></i> Save
                                                    </span>
                                                    <div wire:loading wire:target="save">
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
                                                {{-- <button type="submit" class="btn btn-primary">Save</button> --}}
                                            </div>
                                        </div><!--end col-->
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- end modal buyer --}}
            </div>
            <div class="col-12 col-lg-6">
                <div class="input-group">
                    <input wire:model.defer="searchTerm" class="form-control"style="padding:0.44rem" type="text"
                        placeholder="search tipe produk" />
                    <button wire:click="search" type="button" class="btn btn-primary btn-load w-lg p-1">
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
                </div>
            </div>
        </div>
    </div>
    <div class="table-responsive table-card mt-3 mb-1">
        <table class="table align-middle table-nowrap" id="customerTable" style="width:100%">
            <thead class="table-light">
                <tr>
                    <th>Action</th>
                    <th class="sort">Kode</th>
                    <th class="sort">Nama Tipe Produk</th>
                    <th class="sort">Jenis Produk</th>
                    <th class="sort">Infure</th>
                    <th class="sort">Loss Infure</th>
                    <th class="sort">Inline</th>
                    <th class="sort">Cetak</th>
                    <th class="sort">Berat Jenis</th>
                    <th class="sort">Status</th>
                </tr>
            </thead>
            <tbody class="list form-check-all">
                @php
                    $no = 1;
                @endphp
                @forelse ($data as $item)
                    <tr>
                        <td>
                            <button type="button" class="btn fs-15 p-1 bg-primary rounded" data-bs-toggle="modal"
                                data-bs-target="#modal-edit" wire:click="edit({{ $item->id }})">
                                <i class="ri-edit-box-line text-white"></i>
                            </button>
                            <button type="button" class="btn fs-15 p-1 bg-danger rounded removeBuyerModal"
                                href="#removeBuyerModal" data-bs-toggle="modal" data-bs-target="#removeBuyerModal"
                                data-remove-id="{{ $item->id }}">
                                <i class="ri-delete-bin-line  text-white"></i>
                            </button>
                        </td>
                        <td>{{ $item->code }}</td>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->jenisproduk }}</td>
                        <td>{{ $item->harga_sat_infure }}</td>
                        <td>{{ $item->harga_sat_infure_loss }}</td>
                        <td>{{ $item->harga_sat_inline }}</td>
                        <td>{{ $item->harga_sat_cetak }}</td>
                        <td>{{ $item->berat_jenis }}</td>
                        <td>
                            {!! $item->status == 1
                                ? '<span class="badge text-success bg-success-subtle">Active</span>'
                                : '<span class="badge text-bg-danger">Non Active</span>' !!}
                        </td>
                        {{-- <td>{{ $no++ }}</td> --}}
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center">
                            <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop"
                                colors="primary:#121331,secondary:#08a88a" style="width:40px;height:40px"></lord-icon>
                            <h5 class="mt-2">Sorry! No Result Found</h5>
                            <p class="text-muted mb-0">We've searched more than 150+ Orders We did not find any orders
                                for you search.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        {{ $data->links() }}
    </div>
    {{-- <livewire:tdorder/> --}}
</div>

<script>
    function formatNumber(input) {
        // Remove non-digit characters and add commas for thousands
        let value = input.value.replace(/[^0-9]/g, '');
        if (value.length > 0) {
            value = parseInt(value, 10).toLocaleString('en-US');
        }
        input.value = value;
        // Update Livewire property
        @this.set('harga_sat_inline', value.replace(/,/g, ''));
    }

    function parseNumber(input) {
        // Remove non-digit characters and set value without commas
        let value = input.value.replace(/[^0-9]/g, '');
        input.value = value;
        // Update Livewire property
        @this.set('harga_sat_inline', value);
    }
</script>
