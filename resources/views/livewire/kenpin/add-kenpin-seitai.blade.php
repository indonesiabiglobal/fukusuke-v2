<div class="row">
    <form wire:submit.prevent="save">
        <div class="row mt-2">
            <div class="col-12 col-lg-12">
                <div class="form-group">
                    <div class="input-group">
                        <label class="control-label col-12 col-lg-2">Tanggal Kenpin</label>
                        <input wire:model.defer="kenpin_date" type="text" class="form-control" style="padding:0.44rem"
                            data-provider="flatpickr" data-date-format="d-m-Y">
                        <span class="input-group-text py-0">
                            <i class="ri-calendar-event-fill fs-4"></i>
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-12 mt-1">
                <div class="form-group">
                    <div class="input-group">
                        <label class="control-label col-12 col-lg-2">Nomor Kenpin</label>
                        <div class="col-12 col-lg-10" x-data="{ kenpin_no: @entangle('kenpin_no').live, status: true }" x-init="$watch('kenpin_no', value => {
                            // Membuat karakter pertama kapital
                            if (value.length === 4 && !value.includes('-') && status) {
                                kenpin_no = value + '-';
                            }
                            if (value.length < 4) {
                                status = true;
                            }
                            if (value.length === 5) {
                                status = false;
                            }
                            {{-- membatasi 12 karakter --}}
                            if (value.length == 11 && !value.includes('-') && status) {
                                kenpin_no = value.substring(0, 5) + '-' + value.substring(5, 11);
                            } else if (value.length > 12) {
                                kenpin_no = value.substring(0, 12);
                            }
                        })">
                            <input type="text" class="form-control" wire:model="kenpin_no" maxlength="8"
                                x-model="kenpin_no" wire:model="kenpin_no" maxlength="8" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-4 mt-1">
                <div class="form-group">
                    <div class="input-group">
                        <label class="control-label col-12 col-lg-6">
                            <a href="#" data-bs-toggle="modal" wire:click="showModalNoOrder"
                                class="text-underscore">
                                Nomor Order <i class="ri-information-fill"></i>
                            </a>
                        </label>
                        <input type="text" placeholder="-" maxlength="10"
                            class="form-control col-4 @error('code') is-invalid @enderror" wire:model.change="code"
                            x-on:keydown.tab="$event.preventDefault(); $refs.employeenoInput.focus();" />
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-8 mt-1">
                <div class="form-group">
                    <div class="input-group">
                        <label class="control-label"></label>
                        <input type="text" class="form-control col-8 readonly bg-light"
                            @error('code') placeholder="{{ $message }}" id="nameId" @else placeholder="-" @enderror
                            readonly="readonly" wire:model="name" />
                        <style>
                            #nameId::placeholder {
                                color: red;
                            }
                        </style>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-4 mt-1">
                <div class="form-group">
                    <div class="input-group">
                        <label class="control-label col-12 col-lg-6">Petugas</label>
                        <input type="text" placeholder="-"
                            class="form-control @error('employeeno') is-invalid @enderror" wire:model.change="employeeno"
                            x-ref="employeenoInput" maxlength="8"
                            x-on:keydown.tab="$event.preventDefault(); $refs.remarkInput.focus();" />
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-8 mt-1">
                <div class="form-group">
                    <div class="input-group">
                        <label class="control-label"></label>
                        <input type="text"
                            @error('employeeno') placeholder="{{ $message }}" id="empnameId" @else placeholder="-" @enderror
                            class="form-control readonly bg-light" readonly="readonly" wire:model="empname" />
                        <style>
                            #empnameId::placeholder {
                                color: red;
                            }
                        </style>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-12 mt-1">
                <div class="form-group">
                    <div class="input-group">
                        <label class="control-label col-12 col-lg-2">NG</label>
                        <input type="text" class="form-control" wire:model="remark" x-ref="remarkInput" />
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-12 mt-1">
                <div class="form-group">
                    <div class="input-group">
                        <label class="control-label col-12 col-lg-2">Status</label>
                        <select wire:model="status" class="form-control" placeholder="- all -">
                            <option value="1">Proses</option>
                            <option value="2">Finish</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <hr />
        <div class="row">
            <div class="col-12 col-lg-5">
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-text readonly">
                            Nomor Palet
                        </span>
                        <div x-data="{ nomor_palet: @entangle('nomor_palet').change, status: true }" x-init="$watch('nomor_palet', value => {
                            // Membuat karakter pertama kapital
                            nomor_palet = value.charAt(0).toUpperCase() + value.slice(1).replace(/[^0-9-]/g, '');
                            if (value.length === 5 && !value.includes('-') && status) {
                                nomor_palet = value + '-';
                            }
                            if (value.length < 5) {
                                status = true;
                            }
                            if (value.length === 6) {
                                status = false;
                            }
                            {{-- membatasi 12 karakter --}}
                            if (value.length == 11 && !value.includes('-') && status) {
                                nomor_palet = value.substring(0, 5) + '-' + value.substring(5, 11);
                            } else if (value.length > 12) {
                                nomor_palet = value.substring(0, 12);
                            }
                        })">
                            <input type="text" class="form-control" x-model="nomor_palet" wire:model="nomor_palet"
                                maxlength="12" x-on:keydown.tab="$event.preventDefault(); $refs.lotnoInput.focus();"
                                placeholder="A0000-000000" />
                        </div>
                        <button wire:click="addPalet" type="button" class="btn btn-info" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="addPalet">
                                <i class="ri-search-line"></i>
                            </span>
                            <div wire:loading wire:target="addPalet">
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
            <div class="col-lg-3"></div>
            <div class="col-lg-4">
                <div class="toolbar">
                    <button type="button" class="btn btn-warning" wire:click="cancel" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="cancel">
                            <i class="ri-close-line"> </i> Close
                        </span>
                        <div wire:loading wire:target="cancel">
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
                    <button type="submit" class="btn btn-success" wire:loading.attr="disabled">
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

                    <button type="button" class="btn btn-success btn-print" wire:click="export" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="export">
                            <i class="bx bx-printer"></i> Print
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
        </div>
        <div class="modal fade" id="modal-edit" tabindex="-1" role="dialog" aria-labelledby="modal-edit"
            aria-hidden="true" wire:ignore.self>
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="h6 modal-title">Edit Palet Setai </h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-12 mb-1">
                                <div class="form-group">
                                    <label>Nomor Palet </label>
                                    <div class="input-group col-md-9 col-xs-8">
                                        <input class="form-control readonly bg-light" readonly="readonly"
                                            type="text" wire:model.defer="no_palet" placeholder="..." />
                                        @error('no_palet')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12 mb-1">
                                <div class="form-group">
                                    <label>Nomor Lot </label>
                                    <div class="input-group col-md-9 col-xs-8">
                                        <input class="form-control readonly bg-light" readonly="readonly"
                                            type="text" wire:model.defer="no_lot" placeholder="..." />
                                        @error('no_lot')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12 mb-1">
                                <div class="form-group">
                                    <label>No LPK </label>
                                    <div class="input-group col-md-9 col-xs-8">
                                        <input class="form-control readonly bg-light" readonly="readonly"
                                            type="text" wire:model.defer="no_lpk" placeholder="..." />
                                        @error('no_lpk')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12 mb-1">
                                <div class="form-group">
                                    <label>Quantity </label>
                                    <div class="input-group col-md-9 col-xs-8">
                                        <input class="form-control readonly bg-light" readonly="readonly"
                                            type="text" wire:model.defer="quantity" placeholder="..." />
                                        @error('quantity')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12 mb-1">
                                <div class="form-group">
                                    <label>Loss (Lembar) </label>
                                    <div class="input-group col-md-9 col-xs-8">
                                        <input class="form-control" type="text" wire:model.defer="qty_loss"
                                            placeholder="..."
                                            oninput="this.value = window.formatNumberDecimal(this.value)" />
                                        @error('qty_loss')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        {{-- <button type="button" class="btn btn-secondary">Accept</button> --}}
                        <button type="button" class="btn btn-link text-gray-600 ms-auto"
                            data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-success" wire:click="saveSeitai" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="saveSeitai">
                                <i class="ri-save-3-line"></i> Save
                            </span>
                            <div wire:loading wire:target="saveSeitai">
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
        <div class="card border-0 shadow mb-4 mt-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-centered table-nowrap mb-0 rounded">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0 rounded-start">Action</th>
                                <th class="border-0">Nomor Palet</th>
                                <th class="border-0">Nomor LOT</th>
                                <th class="border-0">No LPK</th>
                                <th class="border-0">Tgl Produksi</th>
                                <th class="border-0">Quantity</th>
                                <th class="border-0 rounded-end">Loss (Lembar)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($details as $item)
                                <tr>
                                    <td>
                                        <button type="button" class="btn btn-primary p-1" data-bs-toggle="modal"
                                            data-bs-target="#modal-edit" wire:click="edit({{ $item->id }})">
                                            <i class="ri-edit-2-fill"></i>
                                        </button>

                                        <button type="button" class="btn btn-danger p-1"
                                            wire:click="deleteSeitai({{ $item->id }})">
                                            <i class="ri-delete-bin-4-fill"></i>
                                        </button>
                                    </td>
                                    <td>
                                        {{ $item->nomor_palet }}
                                    </td>
                                    <td>
                                        {{ $item->nomor_lot }}
                                    </td>
                                    <td>
                                        {{ $item->lpk_no }}
                                    </td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($item->production_date)->format('d M Y') }}
                                    </td>
                                    <td>
                                        {{ number_format($item->qty_produksi) }}
                                    </td>
                                    <td>
                                        {{ number_format($item->qty_loss) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">No results found</td>
                                </tr>
                            @endforelse
                            <tr>
                                <td colspan="6" class="text-end">Berat Loss Total (kg):</td>
                                <td colspan="1" class="text-center">{{ number_format($beratLossTotal) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!--  modal master produk -->
        <div class="modal fade" id="modal-noorder-produk" tabindex="-1" role="dialog"
            aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="myExtraLargeModalLabel">Produk Info - Nomor: <span
                                class="fw-bold">{{ $code }}</span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="col-lg-12">
                            <div class="row">
                                <div class="col-12 col-lg-6">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-4">Nomor Order</label>
                                            <input type="text" class="form-control col-12 col-lg-8" name="product.code"
                                                value="{{ $product->code ?? '' }}" placeholder="KODE" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-4">Nama Produk</label>
                                            <input type="text" class="form-control col-12 col-lg-8" name="product.name"
                                                value="{{ $product->name ?? '' }}" placeholder="nama" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-4">Kode Tipe</label>
                                            <input type="text" class="form-control col-12 col-lg-8" name="product.product_type_id"
                                                value="{{ $product->product_type_id ?? '' }}" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-4">Kode Produk (Alias)</label>
                                            <input type="text" class="form-control col-12 col-lg-8" name="product.code_alias"
                                                value="{{ $product->code_alias ?? '' }}" placeholder="KODE" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-4">Code Barcode</label>
                                            <input type="text" class="form-control col-12 col-lg-8" name="product.codebarcode"
                                                value="{{ $product->codebarcode ?? '' }}" placeholder="KODE" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-lg-6">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-4">Dimensi (T x L x P)</label>
                                            <span class="input-group-text">
                                                T
                                            </span>
                                            <input type="number" class="form-control" name="product.ketebalan"
                                                value="{{ $product->ketebalan ?? '' }}" placeholder="Tebal" />
                                            <span class="input-group-text">
                                                L
                                            </span>
                                            <input type="number" class="form-control" name="product.lebar"
                                                value="{{ $product->diameterlipat ?? '' }}" placeholder="Lebar" />
                                            <span class="input-group-text">
                                                P
                                            </span>
                                            <input type="number" class="form-control" name="product.panjang"
                                                value="{{ $product->productlength ?? '' }}" placeholder="Panjang" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-4">Berat Satuan</label>
                                            <input type="number" class="form-control col-12 col-lg-8" name="product.unit_weight"
                                                value="{{ $product->unit_weight ?? '' }}"  />
                                            <span class="input-group-text">
                                                gram
                                            </span>
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-4">Satuan</label>
                                            <input type="text" class="form-control" name="product.product_unit"
                                                value="{{ $product->product_unit ?? '' }}" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 ">
                                    <p class="text-success fw-bold">INFURE</p>
                                </div>
                                <div class="col-12 col-lg-6">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-4">Dimensi</label>
                                            <input type="text" class="form-control" name="product.inflation_thickness"
                                                value="{{ $product->inflation_thickness ?? '' }}"
                                                placeholder="Tebal" />
                                            @error('inflation_thickness')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                            <span class="input-group-text">
                                                x
                                            </span>
                                            <input type="text" class="form-control" name="product.inflation_width"
                                                value="{{ $product->inflation_fold_diameter ?? '' }}"
                                                placeholder="Lebar" />
                                            @error('inflation_fold_diameter')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                            <span class="input-group-text">
                                                mm
                                            </span>
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-4">Panjang Gulung</label>
                                            <input type="text" class="form-control col-12 col-lg-8" name="product.inflation_length"
                                                value="{{ $product->one_winding_m_number ?? '' }}"  />
                                            <span class="input-group-text">
                                                m
                                            </span>
                                            @error('one_winding_m_number')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-4">Material</label>
                                            <input type="text" class="form-control col-12 col-lg-8" name="product.material_classification"
                                                value="{{ $product->material_classification ?? '' }}"
                                                 />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-4">Embos</label>
                                            <input type="text" class="form-control col-12 col-lg-8" name="product.embossed_classification"
                                                value="{{ $product->embossed_classification ?? '' }}"
                                                 />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-4">Corona</label>
                                            <input type="text" class="form-control col-12 col-lg-8" name="product.corona_classification"
                                                value="{{ $product->surface_classification ?? '' }}"
                                                 />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-lg-6">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-4">MB -1 (Master Batch) </label>
                                            <input type="text" class="form-control" name="product.coloring_1"
                                                value="{{ $product->coloring_1 ?? '' }}" placeholder="warna mb 1" />
                                            @error('coloring_1')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-4">MB -2 </label>
                                            <input type="text" class="form-control" name="product.coloring_2"
                                                value="{{ $product->coloring_2 ?? '' }}" placeholder="warna mb 2" />
                                            @error('coloring_2')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-4">MB -3 </label>
                                            <input type="text" class="form-control" name="product.coloring_3"
                                                value="{{ $product->coloring_3 ?? '' }}" placeholder="warna mb 3" />
                                            @error('coloring_3')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-4">MB -4 </label>
                                            <input type="text" class="form-control" name="product.coloring_4"
                                                value="{{ $product->coloring_4 ?? '' }}" placeholder="warna mb 4" />
                                            @error('coloring_4')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-4">MB -5 </label>
                                            <input type="text" class="form-control" name="product.coloring_5"
                                                value="{{ $product->coloring_5 ?? '' }}" placeholder="warna mb 5" />
                                            @error('coloring_5')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-4">Catatan </label>
                                            <input type="text" class="form-control" name="product.inflation_notes"
                                                value="{{ $product->inflation_notes ?? '' }}"
                                                placeholder="Catatan" />
                                            @error('inflation_notes')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-lg-6 mt-3">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-4">Gentan</label>
                                            <input type="text" class="form-control" name="product.gentan_classification"
                                                value="{{ $product->gentan_classification ?? '' }}" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-4">Gazette</label>
                                            <input type="text" class="form-control" name="product.gazette_classification"
                                                value="{{ $product->gazette_classification ?? '' }}" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-4">GZ Dimensi</label>
                                            <span class="input-group-text">
                                                A
                                            </span>
                                            <input type="text" class="form-control col-12 col-lg-8" name="product.gazette_dimension_a"
                                                value="{{ $product->gazette_dimension_a ?? '' }}"  />

                                            <span class="input-group-text">
                                                B
                                            </span>
                                            <input type="text" class="form-control col-12 col-lg-8" name="product.gazette_dimension_b"
                                                value="{{ $product->gazette_dimension_b ?? '' }}"  />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-4">-</label>
                                            <span class="input-group-text">
                                                C
                                            </span>
                                            <input type="text" class="form-control col-12 col-lg-8" name="product.gazette_dimension_c"
                                                value="{{ $product->gazette_dimension_c ?? '' }}"  />

                                            <span class="input-group-text">
                                                D
                                            </span>
                                            <input type="text" class="form-control col-12 col-lg-8" name="product.gazette_dimension_d"
                                                value="{{ $product->gazette_dimension_d ?? '' }}"  />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-lg-6 mt-3 d-flex justify-content-center">
                                    <img src="{{ asset('asset/image/Gazette-ent.png') }}" width="240"
                                        height="130" alt="img">
                                </div>
                                <div class="col-12">
                                    <p class="text-success">HAGATA</p>
                                </div>
                                <div class="col-12 col-lg-6">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-4">Kode Nukigata</label>
                                            <input type="text" class="form-control" name="product.katanuki_id"
                                                value="{{ $katanuki_id ?? '' }}" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-4">A.</label>
                                            <input type="number" class="form-control col-12 col-lg-8" name="product.extracted_dimension_a"
                                                value="{{ $product->extracted_dimension_a ?? '' }}"
                                                 />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-4">B.</label>
                                            <input type="number" class="form-control col-12 col-lg-8" name="product.extracted_dimension_b"
                                                value="{{ $product->extracted_dimension_b ?? '' }}"
                                                 />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-4">C.</label>
                                            <input type="number" class="form-control col-12 col-lg-8" name="product.extracted_dimension_c"
                                                value="{{ $product->extracted_dimension_c ?? '' }}"
                                                 />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-lg-6 mt-3 d-flex justify-content-center">
                                    @if ($photoKatanuki)
                                        <img src="{{ asset('storage/' . $photoKatanuki) }}" width="240"
                                            height="130" alt="img">
                                    @endif
                                </div>
                                <div class="col-12">
                                    <p class="text-success">PRINTING</p>
                                </div>
                                <div class="col-12 col-lg-4">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-1">*</label>
                                            <span class="input-group-text">
                                                Warna Depan:
                                            </span>
                                            <input type="number" class="form-control" name="product.number_of_color"
                                                value="{{ $product->number_of_color ?? '' }}" placeholder="..." />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-1">1</label>
                                            <input type="text" class="form-control" name="product.color_spec_1"
                                                value="{{ $product->color_spec_1 ?? '' }}" placeholder="..." />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-1">2</label>
                                            <input type="text" class="form-control" name="product.color_spec_2"
                                                value="{{ $product->color_spec_2 ?? '' }}" placeholder="..." />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-1">3</label>
                                            <input type="text" class="form-control" name="product.color_spec_3"
                                                value="{{ $product->color_spec_3 ?? '' }}" placeholder="..." />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-1">4</label>
                                            <input type="text" class="form-control" name="product.color_spec_4"
                                                value="{{ $product->color_spec_4 ?? '' }}" placeholder="..." />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-1">5</label>
                                            <input type="text" class="form-control" name="product.color_spec_5"
                                                value="{{ $product->color_spec_5 ?? '' }}" placeholder="..." />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-lg-4">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-1">*</label>
                                            <span class="input-group-text">
                                                Warna Belakang:
                                            </span>
                                            <input type="text" class="form-control" name="product.back_color_number"
                                                value="{{ $product->back_color_number ?? '' }}" placeholder="..." />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-1">1</label>
                                            <input type="text" class="form-control" name="product.back_color_1"
                                                value="{{ $product->back_color_1 ?? '' }}" placeholder="..." />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-1">2</label>
                                            <input type="text" class="form-control" name="product.back_color_2"
                                                value="{{ $product->back_color_2 ?? '' }}" placeholder="..." />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-1">3</label>
                                            <input type="text" class="form-control" name="product.back_color_3"
                                                value="{{ $product->back_color_3 ?? '' }}" placeholder="..." />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-1">4</label>
                                            <input type="text" class="form-control" name="product.back_color_4"
                                                value="{{ $product->back_color_4 ?? '' }}" placeholder="..." />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-1">5</label>
                                            <input type="text" class="form-control" name="product.back_color_5"
                                                value="{{ $product->back_color_5 ?? '' }}" placeholder="..." />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-lg-4">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-4">Jenis Cetak</label>
                                            <input type="text" class="form-control" name="product.print_type"
                                                value="{{ $product->print_type ?? '' }}" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-4">Sifat Tinta</label>
                                            <input type="text" class="form-control" name="product.ink_characteristic"
                                                value="{{ $product->ink_characteristic ?? '' }}" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-4">Endless</label>
                                            <input type="text" class="form-control" name="product.endless_printing"
                                                value="{{ $product->endless_printing ?? '' }}" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-4">Arah Gulung</label>
                                            <input type="text" class="form-control" name="product.winding_direction_of_the_web"
                                                value="{{ $product->winding_direction_of_the_web ?? '' }}" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <p class="text-success">SEITAI</p>
                                </div>
                                <div class="col-12 col-lg-4">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-6">Klarifikasi Seal</label>
                                            <input type="text" class="form-control" name="product.seal_classification"
                                                value="{{ $product->seal_classification ?? '' }}" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-6">Jarak Seal dari Pola</label>
                                            <input type="number" class="form-control" name="product.from_seal_design"
                                                value="{{ $product->from_seal_design ?? '' }}" placeholder="..."
                                                min="0" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-6">Jarak Seal Bawah</label>
                                            <input type="number" class="form-control" name="product.lower_sealing_length"
                                                value="{{ $product->lower_sealing_length ?? '' }}" placeholder="..."
                                                min="0" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-6">Jumlah Baris Palet</label>
                                            <input type="number" class="form-control" name="product.palet_jumlah_baris"
                                                value="{{ $product->palet_jumlah_baris ?? '' }}" placeholder="..."
                                                min="0" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-6">Isi Baris Palet</label>
                                            <input type="number" class="form-control" name="product.palet_isi_baris"
                                                value="{{ $product->palet_isi_baris ?? '' }}" placeholder="..."
                                                min="0" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-6">Lakban Seitai</label>
                                            <input type="text" class="form-control" name="product.lakbanseitaiid"
                                                value="{{ $product->lakbanseitaiid ?? '' }}" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-6">Stempel Seitai</label>
                                            <input type="text" class="form-control" name="product.stampelseitaiid"
                                                value="{{ $product->stampelseitaiid ?? '' }}" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-6">Hagata Seitai</label>
                                            <input type="text" class="form-control" name="product.hagataseitaiid"
                                                value="{{ $product->hagataseitaiid ?? '' }}" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-6">Jenis Seal Seitai</label>
                                            <input type="text" class="form-control" name="product.jenissealseitaiid"
                                                value="{{ $product->jenissealseitaiid ?? '' }}" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-lg-5">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-3">Kode Gasio</label>
                                            <input type="text" class="form-control" name="product.pack_gaiso_id"
                                                value="{{ $product->pack_gaiso_id ?? '' }}" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-3">Kode Box</label>
                                            <input type="text" class="form-control" name="product.pack_box_id"
                                                value="{{ $product->pack_box_id ?? '' }}" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-3">Kode Inner</label>
                                            <input type="text" class="form-control" name="product.pack_inner_id"
                                                value="{{ $product->pack_inner_id ?? '' }}" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-3">Kode Layer</label>
                                            <input type="text" class="form-control" name="product.pack_layer_id"
                                                value="{{ $product->pack_layer_id ?? '' }}" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-3">Catatan Produksi</label>
                                            <textarea class="form-control" rows="2" name="product.manufacturing_summary"
                                             placeholder="Catatan Produksi">{{ $product->manufacturing_summary ?? '' }}</textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-lg-3">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-2">Isi</label>
                                            <input type="number" class="form-control" name="product.case_gaiso_count"
                                                value="{{ $product->case_gaiso_count ?? '' }}"  />
                                            <input type="text" class="form-control" name="product.case_gaiso_count_unit"
                                                value="{{ $product->case_gaiso_count_unit ?? '' }}" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-2">Isi</label>
                                            <input type="number" class="form-control"  name="product.case_box_count"
                                                value="{{ $product->case_box_count ?? '' }}"  />
                                            <input type="text" class="form-control" name="product.case_box_count_unit"
                                                value="{{ $product->case_box_count_unit ?? '' }}" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-2">Isi</label>
                                            <input type="text" class="form-control" name="product.case_inner_count"
                                                value="{{ $product->case_inner_count ?? '' }}" />
                                            <input type="text" class="form-control" name="product.case_inner_count_unit"
                                                value="{{ $product->case_inner_count_unit ?? '' }}" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <a href="javascript:void(0);" class="btn btn-light link-success fw-medium"
                            data-bs-dismiss="modal"><i class="ri-close-line me-1 align-middle"></i> Close</a>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
    </form>
</div>
@script
    <script>
        $wire.on('showModal', () => {
            $('#modal-edit').modal('show');
        });

        $wire.on('closeModal', () => {
            $('#modal-edit').modal('hide');
        });

        $wire.on('showModalNoOrder', () => {
            $('#modal-noorder-produk').modal('show');
        });
        // close modal NoOrder
        $wire.on('closeModalNoOrder', () => {
            $('#modal-noorder-produk').modal('hide');
        });
    </script>
@endscript
{{-- <script>
    document.addEventListener('livewire:load', function () {
        Livewire.on('showModal', () => {
            $('#modal-edit').modal('show');
        });
        Livewire.on('closeModal', () => {
            $('#modal-edit').modal('hide');
        });
    });
</script> --}}
