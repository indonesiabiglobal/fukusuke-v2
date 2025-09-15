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
                            <input type="text" class="form-control readonly bg-light" disabled wire:model="kenpin_no"
                                maxlength="8" x-model="kenpin_no" wire:model="kenpin_no" maxlength="8" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-12 mt-1">
                <div class="form-group">
                    <div class="input-group">
                        <label class="control-label col-12 col-lg-2">Departemen</label>
                        <select wire:model.change="department_id" class="form-control" placeholder="- all -">
                            <option value="7">Seitai</option>
                            <option value="2">Infure</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-4 mt-1">
                <div class="form-group">
                    <div class="input-group">
                        <label class="control-label col-12 col-lg-6">Produk</label>
                        <input type="text" placeholder="Kode Produk"
                            class="form-control readonly bg-light @error('kode_produk') is-invalid @enderror"
                            readonly="readonly" wire:model="kode_produk"
                            x-on:keydown.tab="$event.preventDefault(); $refs.employeenoInput.focus();" />
                        @error('kode_produk')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-8 mt-1">
                <div class="form-group">
                    <div class="input-group">
                        <label class="control-label"></label>
                        <input type="text"
                            @error('kode_produk') placeholder="{{ $message }}" id="nama_produk" @else placeholder="Nama Produk" @enderror
                            class="form-control readonly bg-light @error('nama_produk') is-invalid @enderror"
                            readonly="readonly" wire:model="nama_produk" />
                        <style>
                            #nama_produk::placeholder {
                                color: red;
                            }
                        </style>
                    </div>
                </div>
            </div>
            {{-- <div class="col-12 col-lg-4 mt-1">
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
            </div> --}}
            <div class="col-12 col-lg-4 mt-1">
                <div class="form-group">
                    <div class="input-group">
                        <label class="control-label col-12 col-lg-6">Petugas</label>
                        <input type="text" placeholder="-"
                            class="form-control @error('employeeno') is-invalid @enderror"
                            wire:model.change="employeeno" x-ref="employeenoInput" maxlength="8"
                            x-on:keydown.tab="$event.preventDefault(); $refs.ngInput.focus();" />
                        @error('employeeno')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
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
            <div class="col-12 col-lg-4 mt-1">
                <div class="form-group">
                    <div class="input-group">
                        <label class="control-label col-12 col-lg-6">NG</label>
                        <input type="text" placeholder="Kode NG"
                            class="form-control @error('kode_ng') is-invalid @enderror" x-ref="ngInput"
                            x-on:keydown.tab="$event.preventDefault(); $refs.statusKenpinSelect.focus();"
                            wire:model.change="kode_ng" maxlength="10" />
                        @error('kode_ng')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-8 mt-1">
                <div class="form-group">
                    <div class="input-group">
                        <label class="control-label"></label>
                        <input type="text"
                            @error('kode_ng') placeholder="{{ $message }}" id="nama_ng" @else placeholder="Nama NG" @enderror
                            class="form-control readonly bg-light @error('nama_ng') is-invalid @enderror"
                            readonly="readonly" wire:model="nama_ng" />
                        <style>
                            #nama_ng::placeholder {
                                color: red;
                            }
                        </style>
                    </div>
                </div>
            </div>
            {{-- status --}}
            <div class="col-12 col-lg-12 mt-1">
                <div class="form-group">
                    <div class="input-group">
                        <label class="control-label col-12 col-lg-2">Status</label>
                        <select wire:model.change="status" class="form-control" placeholder="- all -"
                            x-ref="statusKenpinSelect">
                            <option value="1">Proses</option>
                            <option value="2">Finish</option>
                        </select>
                    </div>
                </div>
            </div>
            {{-- bagian mesin infure --}}
            <div class="col-12 col-lg-12 mt-1" @if ($department_id == 7) style="display: none" @endif>
                <div class="form-group">
                    <div class="input-group">
                        <label class="control-label col-12 col-lg-2">Bagian Mesin</label>
                        <div class="col-12 col-lg-10" wire:ignore>
                            <select wire:model="bagian_mesin_id" x-ref="bagianMesinSelect"
                                class="form-control @error('bagian_mesin_id') is-invalid @enderror" data-choices
                                data-choices-sorting-false data-choices-removeItem
                                x-on:keydown.tab="$event.preventDefault(); $refs.statusKenpinSelect.focus();">
                                <option value="">- Pilih Bagian -</option>
                                @foreach ($bagianMesinListInfure as $bagianMesin)
                                    <option value="{{ $bagianMesin->id }}">
                                        {{ $bagianMesin->code . ' - ' . $bagianMesin->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('bagian_mesin_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
            {{-- bagian mesin seitai --}}
            <div class="col-12 col-lg-12 mt-1" @if ($department_id == 2) style="display: none" @endif>
                <div class="form-group">
                    <div class="input-group">
                        <label class="control-label col-12 col-lg-2">Bagian Mesin</label>
                        <div class="col-12 col-lg-10" wire:ignore>
                            <select wire:model="bagian_mesin_id" x-ref="bagianMesinSelect"
                                class="form-control @error('bagian_mesin_id') is-invalid @enderror" data-choices
                                data-choices-sorting-false data-choices-removeItem
                                x-on:keydown.tab="$event.preventDefault(); $refs.statusKenpinSelect.focus();">
                                <option value="">- Pilih Bagian -</option>
                                @foreach ($bagianMesinListSeitai as $bagianMesin)
                                    <option value="{{ $bagianMesin->id }}">
                                        {{ $bagianMesin->code . ' - ' . $bagianMesin->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('bagian_mesin_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
            {{-- penyebab --}}
            <div class="col-12 col-lg-4 mt-1" @if ($status == 1) style="display: none" @endif>
                <div class="form-group">
                    <div class="input-group">
                        <label class="control-label col-12 col-lg-6">Penyebab</label>
                        <select wire:model="penyebab" x-ref="penyebabSelect"
                            class="form-control @error('penyebab') is-invalid @enderror"
                            x-on:keydown.tab="$event.preventDefault(); $refs.keteranganPenyebabInput.focus();">
                            <option value="">- Pilih Penyebab -</option>
                            <option value="5 M Man Machines Money Method Materials">Pilihan 5 M Man Machines Money
                                Method Materials</option>
                            <option value="Man">Man</option>
                            <option value="Machine">Machine</option>
                            <option value="Method">Method</option>
                            <option value="Material">Material</option>
                            <option value="Milieu">Milieu</option>
                        </select>
                        @error('penyebab')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
            {{-- keterangan penyebab --}}
            <div class="col-12 col-lg-8 mt-1" @if ($status == 1) style="display: none" @endif>
                <div class="form-group">
                    <div class="input-group">
                        <label class="control-label"></label>
                        <input type="text" placeholder="Keterangan Penyebab" x-ref="keteranganPenyebabInput"
                            class="form-control @error('keterangan_penyebab') is-invalid @enderror"
                            wire:model.defer="keterangan_penyebab"
                            x-on:keydown.tab="$event.preventDefault(); $refs.penanggulanganInput.focus();" />
                        @error('keterangan_penyebab')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
            {{-- penanggulangan --}}
            <div class="col-12 col-lg-12 mt-1" @if ($status == 1) style="display: none" @endif>
                <div class="form-group">
                    <div class="input-group">
                        <label class="control-label col-12 col-lg-2">Penanggulangan</label>
                        <input type="text" placeholder="Keterangan penanggulangan" x-ref="penanggulanganInput"
                            class="form-control @error('penanggulangan') is-invalid @enderror"
                            wire:model.defer="penanggulangan" />
                        @error('penanggulangan')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
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
                            <input type="text" class="form-control" x-model="nomor_palet"
                                wire:model="nomor_palet" maxlength="12"
                                x-on:keydown.tab="$event.preventDefault(); $refs.lotnoInput.focus();"
                                placeholder="A0000-000000" />
                        </div>
                        <button wire:click="addPalet" type="button" class="btn btn-info z-0"
                            wire:loading.attr="disabled">
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
                                    Closing...
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

                    <button type="button" class="btn btn-success btn-print" wire:click="export"
                        wire:loading.attr="disabled">
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
        {{-- Modal edit palet --}}
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
                                            placeholder="..." wire:loading.attr="disabled" wire:loading.class="bg-light readonly"
                                            oninput="this.value = window.formatNumberDecimal(this.value)" />
                                        @error('qty_loss')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12 mb-1">
                                <div class="form-group">
                                    <label>Nomor Box </label>
                                    <div class="mb-2">
                                        @foreach($nomor_box as $index => $box)
                                            <div class="input-group mb-1">
                                                <input type="number" class="form-control" wire:model="nomor_box.{{ $index }}" placeholder="Masukkan nomor box"
                                                wire:loading.attr="disabled" wire:loading.class="bg-light readonly" />
                                                <button type="button" class="btn btn-outline-danger"
                                                    wire:click="removeBox({{ $index }})" wire:loading.attr="disabled" wire:loading.class="bg-light readonly">
                                                    <i class="ri-close-line"></i>
                                                </button>
                                            </div>
                                        @endforeach
                                        <button type="button" class="btn btn-primary btn-sm" wire:click="addBox" wire:loading.attr="disabled">
                                            <span wire:loading.remove wire:target="addBox">
                                                Tambah Box
                                            </span>
                                            <span wire:loading wire:target="addBox">
                                                <span class="d-flex align-items-center">
                                                    <span class="spinner-border spinner-border-sm flex-shrink-0" role="status">
                                                        <span class="visually-hidden">Loading...</span>
                                                    </span>
                                                    <span class="flex-grow-1 ms-1">Loading...</span>
                                                </span>
                                            </span>
                                        </button>
                                    </div>
                                    @error('nomor_box.*')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="ms-auto">
                            <button type="button" class="btn btn-success" wire:click="saveSeitai"
                                wire:loading.attr="disabled">
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
                            <button type="button" class="btn btn-link text-gray-600 ms-auto"
                                data-bs-dismiss="modal" wire:click="resetSeitai">Close</button>
                        </div>
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
                                <th class="border-0">Nomor Box</th>
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
                                        @if($item->nomor_box && is_array($item->nomor_box))
                                            {{ implode(', ', $item->nomor_box) }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        {{ number_format($item->qty_loss) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">No results found</td>
                                </tr>
                            @endforelse
                            <tr>
                                <td colspan="7" class="text-end">Berat Loss Total (kg):</td>
                                <td colspan="1" class="text-center">{{ number_format($beratLossTotal) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
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

        // Initialize Choices.js for bagian mesin select
        document.addEventListener('livewire:navigated', () => {
            const bagianMesinSelect = document.querySelector('[data-choices]');
            if (bagianMesinSelect && !bagianMesinSelect.choicesInstance) {
                const choices = new Choices(bagianMesinSelect, {
                    searchEnabled: true,
                    removeItemButton: true,
                    shouldSort: false
                });
                bagianMesinSelect.choicesInstance = choices;

                // Listen for changes and update Livewire
                bagianMesinSelect.addEventListener('change', function(e) {
                    @this.set('bagian_mesin_id', e.target.value);
                });
            }
        });
    </script>
@endscript
