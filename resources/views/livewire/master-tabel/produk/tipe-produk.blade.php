<div class="row">
    <div class="col-lg-12 mt-2">
        <div class="row">

            <div class="col-12 col-lg-6">
                {{-- <button class="btn btn-primary w-lg p-1" wire:click="download" type="button">
                    <span wire:loading.remove wire:target="download">
                        <i class="ri-download-cloud-2-line"> </i> Download Template
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
                </button> --}}
                {{-- <button class="btn btn-info w-lg p-1" wire:click="print" type="button">
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
                </button> --}}

                {{-- Button Add type product --}}
                <button type="button" class="btn btn-success w-lg p-1" wire:click="showModalCreate">
                    <i class="ri-add-line"> </i> Add
                </button>
                {{-- modal add type product --}}
                <div class="modal fade" id="modal-add" tabindex="-1" aria-labelledby="modal-addLabel" aria-modal="true"
                    wire:ignore.self>
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modal-addLabel">Add Tipe Produk</h5> <button type="button"
                                    class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form wire:submit.prevent="store">
                                    <div class="row g-3">
                                        {{-- kode type product --}}
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
                                        {{-- nama type product --}}
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
                                                    <select
                                                        class="form-control col-12 col-lg-3 @error('product_group_id') is-invalid @enderror"
                                                        wire:model="product_group_id" placeholder="">
                                                        <option value="" selected>
                                                            Silahkan Pilih
                                                        </option>
                                                        @foreach ($productGroups as $productGroup)
                                                            <option value="{{ $productGroup->id }}">
                                                                {{ $productGroup->name }}
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
                                                    <label for="harga_sat_infure" class="form-label">Harga Satuan
                                                        Infure</label>
                                                </div>
                                                <div class="col-7">
                                                    <input type="text" step="0.001"
                                                        class="form-control @error('harga_sat_infure') is-invalid @enderror"
                                                        id="harga_sat_infure" wire:model.defer="harga_sat_infure"
                                                        placeholder="Harga Satuan Infure"
                                                        oninput="this.value = window.formatNumberDecimal(this.value)">
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
                                                    <label for="harga_sat_infure_loss" class="form-label">Harga Satuan
                                                        Loss Infure</label>
                                                </div>
                                                <div class="col-7">
                                                    <input type="text" step="0.001"
                                                        class="form-control @error('harga_sat_infure_loss') is-invalid @enderror"
                                                        id="harga_sat_infure_loss"
                                                        wire:model.defer="harga_sat_infure_loss"
                                                        placeholder="Harga Satuan Loss Infure"
                                                        oninput="this.value = window.formatNumberDecimal(this.value)">
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
                                                    <label for="harga_sat_inline" class="form-label">Harga Satuan
                                                        Inline</label>
                                                </div>
                                                <div class="col-7">
                                                    <input type="text" step="0.001"
                                                        class="form-control @error('harga_sat_inline') is-invalid @enderror"
                                                        id="harga_sat_inline" wire:model.defer="harga_sat_inline"
                                                        placeholder="Harga Satuan Inline"
                                                        oninput="this.value = window.formatNumberDecimal(this.value)">
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
                                                    <label for="harga_sat_cetak" class="form-label">Harga Satuan
                                                        cetak</label>
                                                </div>
                                                <div class="col-7">
                                                    <input type="text" step="0.001"
                                                        class="form-control @error('harga_sat_cetak') is-invalid @enderror"
                                                        id="harga_sat_cetak" wire:model.defer="harga_sat_cetak"
                                                        placeholder="Harga Satuan cetak"
                                                        oninput="this.value = window.formatNumberDecimal(this.value)">
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
                                                    <label for="harga_sat_seitai" class="form-label">Harga Satuan
                                                        seitai</label>
                                                </div>
                                                <div class="col-7">
                                                    <input type="text" step="0.001"
                                                        class="form-control @error('harga_sat_seitai') is-invalid @enderror"
                                                        id="harga_sat_seitai" wire:model.defer="harga_sat_seitai"
                                                        placeholder="Harga Satuan seitai"
                                                        oninput="this.value = window.formatNumberDecimal(this.value)">
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
                                                    <label for="harga_sat_seitai_loss" class="form-label">Harga Satuan
                                                        Seitai Loss</label>
                                                </div>
                                                <div class="col-7">
                                                    <input type="text" step="0.001"
                                                        class="form-control @error('harga_sat_seitai_loss') is-invalid @enderror"
                                                        id="harga_sat_seitai_loss"
                                                        wire:model.defer="harga_sat_seitai_loss"
                                                        placeholder="Harga Satuan seitai_loss"
                                                        oninput="this.value = window.formatNumberDecimal(this.value)">
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
                                                    <label for="berat_jenis" class="form-label">Berat Jenis</label>
                                                </div>
                                                <div class="col-7">
                                                    <input type="text" step="0.001"
                                                        class="form-control @error('berat_jenis') is-invalid @enderror"
                                                        id="berat_jenis" wire:model.defer="berat_jenis"
                                                        placeholder="Berat Jenis"
                                                        oninput="this.value = window.formatNumberDecimal(this.value)">
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
                                                    <span wire:loading.remove wire:target="store">
                                                        <i class="ri-save-3-line"></i> Save
                                                    </span>
                                                    <div wire:loading wire:target="store">
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
                {{-- end modal type product --}}
                {{-- modal Edit type product --}}
                <div class="modal fade" id="modal-edit" tabindex="-1" aria-labelledby="modal-editLabel"
                    aria-modal="true" wire:ignore.self>
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modal-EditLabel">Edit Tipe Produk</h5> <button
                                    type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form wire:submit.prevent="update">
                                    <div class="row g-3">
                                        {{-- kode type product --}}
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
                                        {{-- nama type product --}}
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
                                                    <select
                                                        class="form-control col-12 col-lg-3 @error('product_group_id') is-invalid @enderror"
                                                        wire:model="product_group_id" placeholder="">
                                                        @foreach ($productGroups as $productGroup)
                                                            <option value="{{ $productGroup->id }}"
                                                                @if ($productGroup->id == $product_group_id) selected @endif>
                                                                {{ $productGroup->name }}
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
                                                    <label for="harga_sat_infure" class="form-label">Harga Satuan
                                                        Infure</label>
                                                </div>
                                                <div class="col-7">
                                                    <input type="text" step="0.001"
                                                        class="form-control @error('harga_sat_infure') is-invalid @enderror"
                                                        id="harga_sat_infure" wire:model.defer="harga_sat_infure"
                                                        placeholder="Harga Satuan Infure"
                                                        oninput="this.value = window.formatNumberDecimal(this.value)">
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
                                                    <label for="harga_sat_infure_loss" class="form-label">Harga Satuan
                                                        Loss Infure</label>
                                                </div>
                                                <div class="col-7">
                                                    <input type="text" step="0.001"
                                                        class="form-control @error('harga_sat_infure_loss') is-invalid @enderror"
                                                        id="harga_sat_infure_loss"
                                                        wire:model.defer="harga_sat_infure_loss"
                                                        placeholder="Harga Satuan Loss Infure"
                                                        oninput="this.value = window.formatNumberDecimal(this.value)">
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
                                                    <label for="harga_sat_inline" class="form-label">Harga Satuan
                                                        Inline</label>
                                                </div>
                                                <div class="col-7">
                                                    <input type="text" step="0.001"
                                                        class="form-control @error('harga_sat_inline') is-invalid @enderror"
                                                        id="harga_sat_inline" wire:model.defer="harga_sat_inline"
                                                        placeholder="Harga Satuan Inline"
                                                        oninput="this.value = window.formatNumberDecimal(this.value)">
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
                                                    <label for="harga_sat_cetak" class="form-label">Harga Satuan
                                                        cetak</label>
                                                </div>
                                                <div class="col-7">
                                                    <input type="text" step="0.001"
                                                        class="form-control @error('harga_sat_cetak') is-invalid @enderror"
                                                        id="harga_sat_cetak" wire:model.defer="harga_sat_cetak"
                                                        placeholder="Harga Satuan cetak"
                                                        oninput="this.value = window.formatNumberDecimal(this.value)">
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
                                                    <label for="harga_sat_seitai" class="form-label">Harga Satuan
                                                        seitai</label>
                                                </div>
                                                <div class="col-7">
                                                    <input type="text" step="0.001"
                                                        class="form-control @error('harga_sat_seitai') is-invalid @enderror"
                                                        id="harga_sat_seitai" wire:model.defer="harga_sat_seitai"
                                                        placeholder="Harga Satuan seitai"
                                                        oninput="this.value = window.formatNumberDecimal(this.value)">
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
                                                    <label for="harga_sat_seitai_loss" class="form-label">Harga Satuan
                                                        Seitai Loss</label>
                                                </div>
                                                <div class="col-7">
                                                    <input type="text" step="0.001"
                                                        class="form-control @error('harga_sat_seitai_loss') is-invalid @enderror"
                                                        id="harga_sat_seitai_loss"
                                                        wire:model.defer="harga_sat_seitai_loss"
                                                        placeholder="Harga Satuan seitai_loss"
                                                        oninput="this.value = window.formatNumberDecimal(this.value)">
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
                                                    <label for="berat_jenis" class="form-label">Berat Jenis</label>
                                                </div>
                                                <div class="col-7">
                                                    <input type="text" step="0.001"
                                                        class="form-control @error('berat_jenis') is-invalid @enderror"
                                                        id="berat_jenis" wire:model.defer="berat_jenis"
                                                        placeholder="Berat Jenis"
                                                        oninput="this.value = window.formatNumberDecimal(this.value)">
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
                                                    <span wire:loading.remove wire:target="update">
                                                        <i class="ri-save-3-line"></i> Update
                                                    </span>
                                                    <div wire:loading wire:target="update">
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
                {{-- end modal type product --}}

                {{-- start modal delete buyer --}}
                <div id="modal-delete" class="modal fade zoomIn" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                                    id="close-modal-delete"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mt-2 text-center">
                                    <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop"
                                        colors="primary:#f7b84b,secondary:#f06548"
                                        style="width:100px;height:100px"></lord-icon>
                                    <div class="mt-4 pt-2 fs-15 mx-4 mx-sm-5">
                                        <h4>Are you sure ?</h4>
                                        <p class="text-muted mx-4 mb-0">Are you sure you want to remove this type
                                            product ?
                                        </p>
                                    </div>
                                </div>
                                <div class="d-flex gap-2 justify-content-center mt-4 mb-2">
                                    <button type="button" class="btn w-sm btn-light"
                                        data-bs-dismiss="modal">Close</button>
                                    <button wire:click="destroy" id="btnCreate" type="button"
                                        class="btn w-sm btn-danger" id="remove-item">
                                        <span wire:loading.remove wire:target="destroy">
                                            <i class="ri-save-3-line"></i> Yes, Delete It!
                                        </span>
                                        <div wire:loading wire:target="destroy">
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
                </div>
                {{-- end modal delete buyer --}}
            </div>
            {{-- filter search --}}
            {{-- <div class="col-12 col-lg-6">
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
            </div> --}}

            {{-- toggle column table --}}
            <div class="col-12 col-lg-6">
                <div class="col text-end dropdown">
                    <button type="button" data-bs-toggle="dropdown" aria-expanded="false"
                        class="btn btn-soft-primary btn-icon fs-14 mt-2">
                        <i class="ri-grid-fill"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <label style="cursor: pointer;">
                                <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox"
                                    data-column="1" checked> Kode
                            </label>
                        </li>
                        <li>
                            <label style="cursor: pointer;">
                                <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox"
                                    data-column="2" checked> Nama Tipe Produk
                            </label>
                        </li>
                        <li>
                            <label style="cursor: pointer;">
                                <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox"
                                    data-column="3" checked> Jenis Produk
                            </label>
                        </li>
                        <li>
                            <label style="cursor: pointer;">
                                <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox"
                                    data-column="4" checked> Infure
                            </label>
                        </li>
                        <li>
                            <label style="cursor: pointer;">
                                <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox"
                                    data-column="5" checked> Loss Infure
                            </label>
                        </li>
                        <li>
                            <label style="cursor: pointer;">
                                <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox"
                                    data-column="6" checked> Inline
                            </label>
                        </li>
                        <li>
                            <label style="cursor: pointer;">
                                <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox"
                                    data-column="7" checked> Cetak
                            </label>
                        </li>
                        <li>
                            <label style="cursor: pointer;">
                                <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox"
                                    data-column="8" checked> Berat Jenis
                            </label>
                        </li>
                        <li>
                            <label style="cursor: pointer;">
                                <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox"
                                    data-column="9" checked> Status
                            </label>
                        </li>
                        <li>
                            <label style="cursor: pointer;">
                                <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox"
                                    data-column="10" checked> Updated By
                            </label>
                        </li>
                        <li>
                            <label style="cursor: pointer;">
                                <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox"
                                    data-column="11" checked> Updated
                            </label>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    {{-- paginate --}}
    {{-- <div class="d-flex justify-content-between mt-3">
        <div class="d-flex align-items-center">
            <span class="me-2">Show</span>
            <select wire:model.live="paginate" class="form-select form-select-sm me-2" style="width: auto;">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
                <option value="all">All</option>
            </select>
            <span>Entries</span>
        </div>
    </div> --}}
    <div class="table-responsive table-card mt-3 mb-1">
        <table class="table align-middle table-nowrap" id="tipeProdukTable" style="width:100%">
            <thead class="table-light">
                <tr>
                    <th>Action</th>
                    <th>Kode</th>
                    <th>Nama Tipe Produk</th>
                    <th>Jenis Produk</th>
                    <th>Infure</th>
                    <th>Loss Infure</th>
                    <th>Inline</th>
                    <th>Cetak</th>
                    <th>Berat Jenis</th>
                    <th>Status</th>
                    <th>Updated By</th>
                    <th>Updated</th>
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
                            <button type="button" class="btn fs-15 p-1 bg-danger rounded"
                                wire:click="delete({{ $item->id }})">
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
                        <td>{{ $item->updated_by }}</td>
                        <td>{{ $item->updated_on }}</td>
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
        {{-- {{ $data->links() }} --}}
    </div>
    {{-- <livewire:tdorder/> --}}
</div>

@script
    <script>
        $wire.on('showModalCreate', () => {
            $('#modal-add').modal('show');
        });
        // close modal create buyer
        $wire.on('closeModalCreate', () => {
            $('#modal-add').modal('hide');
        });

        // close modal update buyer
        $wire.on('closeModalUpdate', () => {
            $('#modal-edit').modal('hide');
        });

        // show modal delete buyer
        $wire.on('showModalDelete', () => {
            $('#modal-delete').modal('show');
        });

        // close modal delete buyer
        $wire.on('closeModalDelete', () => {
            $('#modal-delete').modal('hide');
        });

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

        // Inisialisasi saat Livewire di-initialized
        document.addEventListener('livewire:initialized', function() {
            initDataTable();
        });

        // Fungsi untuk menginisialisasi ulang DataTable
        function initDataTable() {
            // Hapus DataTable jika sudah ada
            let table = $.fn.dataTable.isDataTable('#tipeProdukTable') ?
                $('#tipeProdukTable').DataTable() :
                null;

            if (table) {
                table.destroy();
            }

            // Inisialisasi ulang DataTable
            table = $('#tipeProdukTable').DataTable({
                "pageLength": 10,
                "searching": true,
                "responsive": true,
                "order": [
                    [1, "asc"]
                ]
            });

            // Inisialisasi ulang event listener checkbox
            $('.toggle-column').off('change').on('change', function() {
                let column = table.column($(this).attr('data-column'));
                column.visible(!column.visible());
            });
        }
    </script>
@endscript
