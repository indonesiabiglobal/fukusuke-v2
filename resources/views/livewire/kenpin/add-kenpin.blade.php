<div class="row">
    <form wire:submit.prevent="save">
        <div class="row mt-2">
            <div class="col-12 col-lg-12">
                <div class="form-group">
                    <div class="input-group">
                        <label class="control-label col-12 col-lg-2">Tanggal Kenpin</label>
                        <input class="form-control" type="text" data-provider="flatpickr" data-date-format="d-m-Y" data-maxDate="{{ now()->format('d/m/Y') }}"
                            wire:model.change="kenpin_date" placeholder="yyyy/mm/dd" />
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
                            if (value.length >= 4 && !value.includes('-') && status) {
                                kenpin_no = value + '-';
                            }
                            if (value.length < 4) {
                                status = true;
                            }
                            if (value.length === 6) {
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
                                x-model="kenpin_no" wire:model="kenpin_no" maxlength="8" disabled/>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-4 mt-1">
                <div class="form-group">
                    <div class="input-group">
                        <label class="control-label col-12 col-lg-6">
                            <a href="#" data-bs-toggle="modal" wire:click="showModalLPK" class="text-underscore">
                                Nomor LPK <i class="ri-information-fill"></i>
                            </a></label>
                        <div class="col-12 col-lg-6" x-data="{ lpk_no: @entangle('lpk_no').change, status: true }" x-init="$watch('lpk_no', value => {
                            if (value.length === 6 && !value.includes('-') && status) {
                                lpk_no = value + '-';
                            }
                            if (value.length < 6) {
                                status = true;
                            }
                            if (value.length === 7) {
                                status = false;
                            }

                            if (value.length == 9 && !value.includes('-') && status) {
                                lpk_no = value.substring(0, 6) + '-' + value.substring(6, 9);
                            } else if (value.length > 10) {
                                lpk_no = value.substring(0, 10);
                            }
                        })">
                            <input class="form-control @error('lpk_no') is-invalid @enderror" style="padding:0.44rem"
                                type="text" placeholder="000000-000" x-model="lpk_no" maxlength="10"
                                wire:model.change="lpk_no"
                                x-on:keydown.tab="$event.preventDefault(); $refs.employeenoInput.focus();" />
                            @error('lpk_no')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-4 mt-1">
                <div class="form-group">
                    <div class="input-group">
                        <label class="control-label col-12 col-lg-5">Tanggal LPK</label>
                        <input class="form-control readonly datepicker-input bg-light" readonly="readonly"
                            type="text" wire:model.defer="lpk_date" />
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-4 mt-1">
                <div class="form-group">
                    <div class="input-group">
                        <label class="control-label col-12 col-lg-5">Panjang LPK</label>
                        <input type="text" placeholder="-" class="form-control readonly bg-light" readonly="readonly"
                            wire:model="panjang_lpk" />
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
                        <input type="text" placeholder="-" class="form-control readonly bg-light" readonly="readonly"
                            wire:model="code" />
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-8 mt-1">
                <div class="form-group">
                    <div class="input-group">
                        <label class="control-label"></label>
                        <input type="text" placeholder="-" class="form-control readonly bg-light" readonly="readonly"
                            wire:model="name" />
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-4 mt-1">
                <div class="form-group">
                    <div class="input-group">
                        <label class="control-label col-12 col-lg-6">Petugas</label>
                        <input type="text" placeholder="-" x-ref="employeenoInput"
                            class="form-control @error('employeeno') is-invalid @enderror" maxlength="8"
                            wire:model.change="employeeno"
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
                            class="form-control col-12 col-lg-8 readonly bg-light" readonly="readonly"
                            wire:model="empname" />
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
                        <label class="control-label col-12 col-lg-6">Kode NG</label>
                        <input type="text" placeholder="..."
                            class="form-control @error('kode_ng') is-invalid @enderror" x-ref="ngInput"
                            x-on:keydown.tab="$event.preventDefault(); $refs.penyebabSelect.focus();"
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
                        <input type="text" placeholder="-" class="form-control readonly bg-light"
                            readonly="readonly" wire:model="nama_ng" />
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-4 mt-1">
                <div class="form-group">
                    <div class="input-group">
                        <label class="control-label col-12 col-lg-6">Penyebab</label>
                        <select wire:model="penyebab" x-ref="penyebabSelect"
                            class="form-control @error('penyebab') is-invalid @enderror"
                            x-on:keydown.tab="$event.preventDefault(); $refs.keteranganPenyebabInput.focus();">
                            <option value="">- Pilih Penyebab -</option>
                            <option value="Man">Man</option>
                            <option value="Machine">Machine</option>
                            <option value="Method">Method</option>
                            <option value="Material">Material</option>
                            <option value="Measure">Measure</option>
                            <option value="Environment">Environment</option>
                        </select>
                        @error('penyebab')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-8 mt-1">
                <div class="form-group">
                    <div class="input-group">
                        <label class="control-label"></label>
                        <input type="text" placeholder="Keterangan penyebab" x-ref="keteranganPenyebabInput"
                            class="form-control @error('keterangan_penyebab') is-invalid @enderror"
                            wire:model.defer="keterangan_penyebab"
                            x-on:keydown.tab="$event.preventDefault(); $refs.penanggulanganInput.focus();" />
                        @error('keterangan_penyebab')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-12 mt-1">
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
            <div class="col-12 col-lg-12 mt-1">
                <div class="form-group">
                    <div class="input-group">
                        <label class="control-label col-12 col-lg-2">Bagian Mesin</label>
                        <div class="col-12 col-lg-10" wire:ignore>
                            <select wire:model="bagian_mesin_id" x-ref="bagianMesinSelect"
                                class="form-control @error('bagian_mesin_id') is-invalid @enderror" data-choices
                                data-choices-sorting-false data-choices-removeItem
                                x-on:keydown.tab="$event.preventDefault(); $refs.statusKenpinSelect.focus();">
                                <option value="">- Pilih Bagian -</option>
                                @if (isset($bagianMesinList))
                                    @foreach ($bagianMesinList as $bagianMesin)
                                        <option value="{{ $bagianMesin->id }}">{{ $bagianMesin->code . ' - ' . ($bagianMesin->name) }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            @error('bagian_mesin_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-12 mt-1">
                <div class="form-group">
                    <div class="input-group">
                        <label class="control-label col-12 col-lg-2">Status</label>
                        <select wire:model="status_kenpin" x-ref="statusKenpinSelect"
                            class="form-control @error('status_kenpin') is-invalid @enderror" placeholder="- all -">
                            <option value="1">Proses</option>
                            {{-- <option value="2">Finish</option> --}}
                        </select>
                    </div>
                    @error('status_kenpin')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>
        <hr />
        <div class="row">
            <div class="col-lg-8">
                <button wire:click="addGentan" type="button" class="btn btn-success" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="addGentan">
                        <i class="ri-add-line"></i> Add Gentan
                    </span>

                    <div wire:loading wire:target="addGentan">
                        <span class="d-flex align-items-center">
                            <span class="spinner-border flex-shrink-0" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </span>
                            <span class="flex-grow-1 ms-1">Loading...</span>
                        </span>
                    </div>
                </button>
            </div>
            <div class="col-lg-4" style="border-top:1px solid #efefef">
                <div class="toolbar">
                    <button type="button" class="btn btn-warning" wire:click="cancel" wire:loading.attr="disabled" wire:target="cancel">
                        <span wire:loading.remove wire:target="cancel">
                            <i class="ri-close-line"></i> Close
                        </span>
                        <span wire:loading wire:target="cancel">
                            <span class="d-flex align-items-center">
                                <span class="spinner-border spinner-border-sm flex-shrink-0" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </span>
                                <span class="flex-grow-1 ms-1">Closing...</span>
                            </span>
                        </span>
                    </button>
                    <button wire:click="save" type="button" class="btn btn-success w-lg"
                        wire:loading.attr="disabled">
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
                </div>
            </div>
        </div>

        <div class="modal fade" id="modal-add-gentan" tabindex="-1" role="dialog" aria-labelledby="modal-add-gentan"
            aria-hidden="true" wire:ignore.self>
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="h6 modal-title">Add Gentan Infure</h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-12 mb-1">
                                <div class="form-group">
                                    <label>Nomor Gentan </label>
                                    <div class="input-group col-md-9 col-xs-8">
                                        <input class="form-control" type="text" wire:model.change="gentan_no"
                                            maxlength="3"
                                            oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/(\..*)\./g, '$1');"
                                            placeholder="..."
                                            x-on:keydown.tab="$event.preventDefault(); $refs.beratLossAddInput.focus();" />
                                        @error('gentan_no')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12 mb-1">
                                <div class="form-group">
                                    <label>Nomor Mesin </label>
                                    <div class="input-group col-md-9 col-xs-8">
                                        <input class="form-control readonly bg-light" readonly="readonly"
                                            type="text" wire:model.defer="machineno" placeholder="..." />
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12 mb-1">
                                <div class="form-group">
                                    <label>Petugas </label>
                                    <div class="input-group col-md-9 col-xs-8">
                                        <input class="form-control readonly bg-light" readonly="readonly"
                                            type="text" wire:model.defer="namapetugas" placeholder="..." />
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12 mb-1">
                                <div class="form-group">
                                    <label>Berat Loss </label>
                                    <div class="input-group col-md-9 col-xs-8">
                                        <input class="form-control" type="text" wire:model.defer="berat_loss"
                                            placeholder="0" x-ref="beratLossAddInput"
                                            oninput="this.value = window.formatNumberDecimal(this.value)" />
                                        <span class="input-group-text">
                                            Kg
                                        </span>
                                        @error('berat_loss')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            {{-- <div class="col-lg-12 mb-1">
                                <div class="form-group">
                                    <label>Frekuensi </label>
                                    <div class="input-group col-md-9 col-xs-8">
                                        <input class="form-control" type="number" wire:model.defer="frekuensi"
                                            placeholder="0" />
                                        @error('frekuensi')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div> --}}
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="ms-auto">
                            <button type="button" class="btn btn-success mr-0" wire:target="saveGentan" wire:click="saveGentan"
                                @disabled(!$gentan_no && !$machineno) wire:loading.attr="disabled" >
                                <span wire:loading.remove wire:target="saveGentan">
                                    <i class="ri-save-3-line"></i> Save
                                </span>
                                <div wire:loading wire:target="saveGentan">
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
                            <button type="button" class="btn btn-link text-gray-600"
                                data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modal-edit-gentan" tabindex="-1" role="dialog" aria-labelledby="modal-edit-gentan"
            aria-hidden="true" wire:ignore.self>
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="h6 modal-title">Edit Gentan Infure</h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-12 mb-1">
                                <div class="form-group">
                                    <label>Nomor Gentan </label>
                                    <div class="input-group col-md-9 col-xs-8">
                                        <input class="form-control" type="text" wire:model.change="gentan_no"
                                            maxlength="3"
                                            oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/(\..*)\./g, '$1');"
                                            placeholder="..."
                                            x-on:keydown.tab="$event.preventDefault(); $refs.beratLossEditInput.focus();" />
                                        @error('gentan_no')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12 mb-1">
                                <div class="form-group">
                                    <label>Nomor Mesin </label>
                                    <div class="input-group col-md-9 col-xs-8">
                                        <input class="form-control readonly bg-light" readonly="readonly"
                                            type="text" wire:model.defer="machineno" placeholder="..." />
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12 mb-1">
                                <div class="form-group">
                                    <label>Petugas </label>
                                    <div class="input-group col-md-9 col-xs-8">
                                        <input class="form-control readonly bg-light" readonly="readonly"
                                            type="text" wire:model.defer="namapetugas" placeholder="..." />
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12 mb-1">
                                <div class="form-group">
                                    <label>Berat Loss </label>
                                    <div class="input-group col-md-9 col-xs-8">
                                        <input class="form-control" type="text" wire:model.defer="berat_loss"
                                            placeholder="0" x-ref="beratLossEditInput"
                                            oninput="this.value = window.formatNumberDecimal(this.value)" />
                                        <span class="input-group-text">
                                            Kg
                                        </span>
                                        @error('berat_loss')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            {{-- <div class="col-lg-12 mb-1">
                                <div class="form-group">
                                    <label>Frekuensi </label>
                                    <div class="input-group col-md-9 col-xs-8">
                                        <input class="form-control" type="text" wire:model.defer="frekuensi"
                                            placeholder="0" />
                                        @error('frekuensi')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div> --}}
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="ms-auto">
                            <button type="button" class="btn btn-success mr-0" wire:click="saveGentan" wire:loading.attr="disabled"
                                @disabled(!$gentan_no && !$machineno) wire:target="saveGentan">
                                <span wire:loading.remove wire:target="saveGentan">
                                    <i class="ri-save-3-line"></i> Save
                                </span>
                                <div wire:loading wire:target="saveGentan">
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
                            <button type="button" class="btn btn-link text-gray-600" data-bs-dismiss="modal">Close</button>
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
                                <th class="border-0">Gentan</th>
                                <th class="border-0">No Mesin</th>
                                <th class="border-0">Shift</th>
                                <th class="border-0">Petugas</th>
                                <th class="border-0">Nomor Han</th>
                                <th class="border-0">Tgl Produksi</th>
                                <th class="border-0 rounded-end">Berat Loss (kg)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $total = 0;
                            @endphp
                            @forelse ($details as $item)
                                <tr>
                                    <td>
                                        <button type="button" class="btn btn-primary p-1"
                                            wire:click="edit({{ $item['id'] }})"
                                            wire:loading.attr="disabled" wire:target="edit">
                                            <span wire:loading.remove wire:target="edit">
                                                <i class="ri-edit-2-fill"></i>
                                            </span>
                                            <span wire:loading wire:target="edit">
                                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                            </span>
                                        </button>

                                        <button type="button" class="btn btn-danger p-1"
                                            wire:click="deleteInfure({{ $item['id'] }})" wire:loading.attr="disabled" wire:target="deleteInfure">
                                            <span wire:loading.remove wire:target="deleteInfure">
                                                <i class="ri-delete-bin-4-fill"></i>
                                            </span>
                                            <span wire:loading wire:target="deleteInfure">
                                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                            </span>
                                        </button>
                                    </td>
                                    <td>
                                        {{ $item['gentan_no'] }}
                                    </td>
                                    <td>
                                        {{ $item['machineno'] }}
                                    </td>
                                    <td>
                                        {{ $item['work_shift'] }}
                                    </td>
                                    <td>
                                        {{ $item['namapetugas'] }}
                                    </td>
                                    <td>
                                        {{ $item['nomor_han'] }}
                                    </td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($item['tglproduksi'])->format('d M Y') }}
                                    </td>
                                    <td>
                                        {{ $item['berat_loss'] }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">No results found</td>
                                </tr>
                            @endforelse
                            <tr>
                                <td colspan="7" class="text-end">Berat Loss Total (kg):</td>
                                <td colspan="1" class="text-center">{{ $beratLossTotal }}</td>
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
                                            <input type="text" class="form-control col-12 col-lg-8"
                                                value="{{ $product->code ?? '' }}" placeholder="KODE" required />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-4">Nama Produk</label>
                                            <input type="text" class="form-control col-12 col-lg-8"
                                                value="{{ $product->name ?? '' }}" placeholder="nama" required />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-4">Kode Tipe</label>
                                            <input type="text" class="form-control col-12 col-lg-8"
                                                value="{{ $product->product_type_id ?? '' }}" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-4">Kode Produk (Alias)</label>
                                            <input type="text" class="form-control col-12 col-lg-8"
                                                value="{{ $product->code_alias ?? '' }}" placeholder="KODE" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-4">Code Barcode</label>
                                            <input type="text" class="form-control col-12 col-lg-8"
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
                                            <input required type="number" class="form-control"
                                                value="{{ $product->ketebalan ?? '' }}" placeholder="Tebal" />
                                            <span class="input-group-text">
                                                L
                                            </span>
                                            <input required type="number" class="form-control"
                                                value="{{ $product->diameterlipat ?? '' }}" placeholder="Lebar" />
                                            <span class="input-group-text">
                                                P
                                            </span>
                                            <input required type="number" class="form-control"
                                                value="{{ $product->productlength ?? '' }}" placeholder="Panjang" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-4">Berat Satuan</label>
                                            <input required type="number" class="form-control col-12 col-lg-8"
                                                value="{{ $product->unit_weight ?? '' }}" placeholder="0" />
                                            <span class="input-group-text">
                                                gram
                                            </span>
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-4">Satuan</label>
                                            <input required type="text" class="form-control"
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
                                            <input required type="text" class="form-control"
                                                value="{{ $product->inflation_thickness ?? '' }}"
                                                placeholder="Tebal" />
                                            @error('inflation_thickness')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                            <span class="input-group-text">
                                                x
                                            </span>
                                            <input required type="text" class="form-control"
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
                                            <input required type="text" class="form-control col-12 col-lg-8"
                                                value="{{ $product->one_winding_m_number ?? '' }}" placeholder="0" />
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
                                            <input required type="text" class="form-control col-12 col-lg-8"
                                                value="{{ $product->material_classification ?? '' }}"
                                                placeholder="0" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-4">Embos</label>
                                            <input required type="text" class="form-control col-12 col-lg-8"
                                                value="{{ $product->embossed_classification ?? '' }}"
                                                placeholder="0" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-4">Corona</label>
                                            <input required type="text" class="form-control col-12 col-lg-8"
                                                value="{{ $product->surface_classification ?? '' }}"
                                                placeholder="0" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-lg-6">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-4">MB -1 (Master Batch) </label>
                                            <input type="text" class="form-control"
                                                value="{{ $product->coloring_1 ?? '' }}" placeholder="warna mb 1" />
                                            @error('coloring_1')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-4">MB -2 </label>
                                            <input type="text" class="form-control"
                                                value="{{ $product->coloring_2 ?? '' }}" placeholder="warna mb 2" />
                                            @error('coloring_2')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-4">MB -3 </label>
                                            <input type="text" class="form-control"
                                                value="{{ $product->coloring_3 ?? '' }}" placeholder="warna mb 3" />
                                            @error('coloring_3')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-4">MB -4 </label>
                                            <input type="text" class="form-control"
                                                value="{{ $product->coloring_4 ?? '' }}" placeholder="warna mb 4" />
                                            @error('coloring_4')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-4">MB -5 </label>
                                            <input type="text" class="form-control"
                                                value="{{ $product->coloring_5 ?? '' }}" placeholder="warna mb 5" />
                                            @error('coloring_5')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-4">Catatan </label>
                                            <input type="text" class="form-control"
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
                                            <input type="text" class="form-control"
                                                value="{{ $product->gentan_classification ?? '' }}" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-4">Gazette</label>
                                            <input type="text" class="form-control"
                                                value="{{ $product->gazette_classification ?? '' }}" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-4">GZ Dimensi</label>
                                            <span class="input-group-text">
                                                A
                                            </span>
                                            <input required type="text" class="form-control col-12 col-lg-8"
                                                value="{{ $product->gazette_dimension_a ?? '' }}" placeholder="0" />

                                            <span class="input-group-text">
                                                B
                                            </span>
                                            <input required type="text" class="form-control col-12 col-lg-8"
                                                value="{{ $product->gazette_dimension_b ?? '' }}" placeholder="0" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-4">-</label>
                                            <span class="input-group-text">
                                                C
                                            </span>
                                            <input required type="text" class="form-control col-12 col-lg-8"
                                                value="{{ $product->gazette_dimension_c ?? '' }}" placeholder="0" />

                                            <span class="input-group-text">
                                                D
                                            </span>
                                            <input required type="text" class="form-control col-12 col-lg-8"
                                                value="{{ $product->gazette_dimension_d ?? '' }}" placeholder="0" />
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
                                            <input type="text" class="form-control"
                                                value="{{ $katanuki_id ?? '' }}" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-4">A.</label>
                                            <input required type="number" class="form-control col-12 col-lg-8"
                                                value="{{ $product->extracted_dimension_a ?? '' }}"
                                                placeholder="0" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-4">B.</label>
                                            <input required type="number" class="form-control col-12 col-lg-8"
                                                value="{{ $product->extracted_dimension_b ?? '' }}"
                                                placeholder="0" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-4">C.</label>
                                            <input required type="number" class="form-control col-12 col-lg-8"
                                                value="{{ $product->extracted_dimension_c ?? '' }}"
                                                placeholder="0" />
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
                                            <input required type="number" class="form-control"
                                                value="{{ $product->number_of_color ?? '' }}" placeholder="..." />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-1">1</label>
                                            <input type="text" class="form-control"
                                                value="{{ $product->color_spec_1 ?? '' }}" placeholder="..." />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-1">2</label>
                                            <input type="text" class="form-control"
                                                value="{{ $product->color_spec_2 ?? '' }}" placeholder="..." />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-1">3</label>
                                            <input type="text" class="form-control"
                                                value="{{ $product->color_spec_3 ?? '' }}" placeholder="..." />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-1">4</label>
                                            <input type="text" class="form-control"
                                                value="{{ $product->color_spec_4 ?? '' }}" placeholder="..." />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-1">5</label>
                                            <input type="text" class="form-control"
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
                                            <input required type="text" class="form-control"
                                                value="{{ $product->back_color_number ?? '' }}" placeholder="..." />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-1">1</label>
                                            <input type="text" class="form-control"
                                                value="{{ $product->back_color_1 ?? '' }}" placeholder="..." />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-1">2</label>
                                            <input type="text" class="form-control"
                                                value="{{ $product->back_color_2 ?? '' }}" placeholder="..." />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-1">3</label>
                                            <input type="text" class="form-control"
                                                value="{{ $product->back_color_3 ?? '' }}" placeholder="..." />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-1">4</label>
                                            <input type="text" class="form-control"
                                                value="{{ $product->back_color_4 ?? '' }}" placeholder="..." />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-1">5</label>
                                            <input type="text" class="form-control"
                                                value="{{ $product->back_color_5 ?? '' }}" placeholder="..." />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-lg-4">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-4">Jenis Cetak</label>
                                            <input type="text" class="form-control"
                                                value="{{ $product->print_type ?? '' }}" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-4">Sifat Tinta</label>
                                            <input type="text" class="form-control"
                                                value="{{ $product->ink_characteristic ?? '' }}" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-4">Endless</label>
                                            <input type="text" class="form-control"
                                                value="{{ $product->endless_printing ?? '' }}" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-4">Arah Gulung</label>
                                            <input type="text" class="form-control"
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
                                            <input type="text" class="form-control"
                                                value="{{ $product->seal_classification ?? '' }}" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-6">Jarak Seal dari Pola</label>
                                            <input required type="number" class="form-control"
                                                value="{{ $product->from_seal_design ?? '' }}" placeholder="..."
                                                min="0" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-6">Jarak Seal Bawah</label>
                                            <input required type="number" class="form-control"
                                                value="{{ $product->lower_sealing_length ?? '' }}" placeholder="..."
                                                min="0" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-6">Jumlah Baris Palet</label>
                                            <input required type="number" class="form-control"
                                                value="{{ $product->palet_jumlah_baris ?? '' }}" placeholder="..."
                                                min="0" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-6">Isi Baris Palet</label>
                                            <input required type="number" class="form-control"
                                                value="{{ $product->palet_isi_baris ?? '' }}" placeholder="..."
                                                min="0" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-6">Lakban Seitai</label>
                                            <input type="text" class="form-control"
                                                value="{{ $product->lakbanseitaiid ?? '' }}" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-6">Stempel Seitai</label>
                                            <input type="text" class="form-control"
                                                value="{{ $product->stampelseitaiid ?? '' }}" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-6">Hagata Seitai</label>
                                            <input type="text" class="form-control"
                                                value="{{ $product->hagataseitaiid ?? '' }}" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-6">Jenis Seal Seitai</label>
                                            <input type="text" class="form-control"
                                                value="{{ $product->jenissealseitaiid ?? '' }}" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-lg-5">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-3">Kode Gasio</label>
                                            <input type="text" class="form-control"
                                                value="{{ $product->pack_gaiso_id ?? '' }}" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-3">Kode Box</label>
                                            <input type="text" class="form-control"
                                                value="{{ $product->pack_box_id ?? '' }}" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-3">Kode Inner</label>
                                            <input type="text" class="form-control"
                                                value="{{ $product->pack_inner_id ?? '' }}" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-3">Kode Layer</label>
                                            <input type="text" class="form-control"
                                                value="{{ $product->pack_layer_id ?? '' }}" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-3">Catatan Produksi</label>
                                            <textarea class="form-control" rows="2" placeholder="Catatan Produksi">{{ $product->manufacturing_summary ?? '' }}</textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-lg-3">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-2">Isi</label>
                                            <input required type="number" class="form-control"
                                                value="{{ $product->case_gaiso_count ?? '' }}" placeholder="0" />
                                            <input required type="text" class="form-control"
                                                value="{{ $product->case_gaiso_count_unit ?? '' }}" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-2">Isi</label>
                                            <input type="number" class="form-control"
                                                value="{{ $product->case_box_count ?? '' }}" placeholder="0" />
                                            <input type="text" class="form-control"
                                                value="{{ $product->case_box_count_unit ?? '' }}" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-2">Isi</label>
                                            <input required type="text" class="form-control"
                                                value="{{ $product->case_inner_count ?? '' }}" />
                                            <input required type="text" class="form-control"
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

        <!--  modal LPK -->
        <div class="modal fade" id="modal-lpk" tabindex="-1" role="dialog"
            aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="myExtraLargeModalLabel">LPK Info - Nomor: <span
                                class="fw-bold">{{ $lpk_no }}</span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="col-lg-12">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-3 fw-bold text-muted">Tanggal
                                                LPK</label>
                                            <input value="{{ $orderLPK->lpk_date ?? '' }}" disabled type="text"
                                                class="form-control" style="padding:0.44rem"
                                                data-provider="flatpickr" data-date-format="d/m/Y">
                                            <span class="input-group-text py-0">
                                                <i class="ri-calendar-event-fill fs-4"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-3 fw-bold text-muted">Nomor
                                                LPK</label>
                                            <input type="text" class="form-control"
                                                value="{{ $orderLPK->lpk_no ?? '' }}" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-3 fw-bold text-muted">PO
                                                Number</label>
                                            <input type="text" class="form-control"
                                                value="{{ $orderLPK->po_no ?? '' }}" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-3 fw-bold text-muted">Nomor
                                                Order</label>
                                            <input type="text" class="form-control readonly" readonly="readonly"
                                                value="{{ $orderLPK->no_order ?? '' }}" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-3 fw-bold text-muted">Nomor
                                                Mesin</label>
                                            <input type="text" class="form-control"
                                                value="{{ $orderLPK->machineno ?? '' }}" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-3 fw-bold text-muted">Jumlah
                                                LPK</label>
                                            <input type="text" class="form-control"
                                                value="{{ $orderLPK->qty_lpk ?? '' }}" />
                                            <span class="input-group-text">
                                                Lembar
                                            </span>
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-3 fw-bold text-muted">Jumlah
                                                Gentan</label>
                                            <input type="text" class="form-control"
                                                value="{{ $orderLPK->qty_gentan ?? '' }}" />
                                            <span class="input-group-text">
                                                roll
                                            </span>
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-3 fw-bold text-muted">Meter
                                                Gulung</label>
                                            <input type="text" class="form-control"
                                                value="{{ $orderLPK->qty_gulung ?? '' }}" />
                                            <span class="input-group-text">
                                                meter
                                            </span>
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-3 fw-bold text-muted">Panjang
                                                LPK</label>
                                            <input type="text" class="form-control readonly"
                                                readonly="readonly" value="{{ $orderLPK->panjang_lpk ?? '' }}" />
                                            <span class="input-group-text">
                                                meter
                                            </span>
                                        </div>
                                    </div>
                                    {{-- warna LPK --}}
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-3 fw-bold text-muted">Warna
                                                LPK</label>
                                            <input type="text" class="form-control readonly"
                                                readonly="readonly" value="{{ $orderLPK->warnalpkid ?? '' }}" />
                                        </div>
                                    </div>

                                </div>

                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-3 fw-bold text-muted">Tanggal
                                                Proses</label>
                                            <input value="{{ $orderLPK->processdate ?? '' }}" disabled
                                                type="date" class="form-control datepicker-input"
                                                placeholder="yyyy/mm/dd" style="padding:0.44rem"
                                                data-provider="flatpickr" data-date-format="d/m/Y" />
                                            <span class="input-group-text py-0">
                                                <i class="ri-calendar-event-fill fs-4"></i>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-3 fw-bold text-muted">Tanggal
                                                PO</label>
                                            <input class="form-control datepicker-input readonly"
                                                readonly="readonly" type="date"
                                                value="{{ $orderLPK->order_date ?? '' }}"
                                                placeholder="yyyy/mm/dd" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label
                                                class="control-label col-12 col-lg-3 fw-bold text-muted">Buyer</label>
                                            <input type="text" class="form-control readonly"
                                                readonly="readonly" value="{{ $orderLPK->buyer_name ?? '' }}" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-3 fw-bold text-muted">Nama
                                                Produk</label>
                                            <input type="text" class="form-control readonly"
                                                readonly="readonly" value="{{ $orderLPK->product_name ?? '' }}" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-3 fw-bold text-muted">Nama
                                                Mesin</label>
                                            <input type="text" class="form-control readonly"
                                                readonly="readonly" value="{{ $orderLPK->machinename ?? '' }}" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-3 fw-bold text-muted">Panjang
                                                Total</label>
                                            <input type="text" class="form-control readonly"
                                                readonly="readonly"
                                                value="{{ $orderLPK->total_assembly_line ?? '' }}" />
                                            <span class="input-group-text">
                                                meter
                                            </span>
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-3 fw-bold text-muted">Dimensi
                                                (TxLxP)</label>
                                            <input type="text" class="form-control readonly"
                                                readonly="readonly" value="{{ $orderLPK->dimensi ?? '' }}" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-3 fw-bold text-muted">Default
                                                Gulung</label>
                                            <input type="text" class="form-control readonly"
                                                readonly="readonly"
                                                value="{{ $orderLPK->defaultgulung ?? '' }}" />
                                            <span class="input-group-text" id="basic-addon2">
                                                meter
                                            </span>
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-3 fw-bold text-muted">Selisih
                                                Kurang</label>
                                            <input type="text" class="form-control readonly"
                                                readonly="readonly"
                                                value="{{ $orderLPK->selisihKurang ?? '' }}" />
                                            <span class="input-group-text">
                                                meter
                                            </span>
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <label for="textarea"
                                            class="control-label col-12 col-lg-3 fw-bold text-muted">Catatan</label>
                                        <textarea class="form-control" placeholder="Catatan" id="textarea" rows="2">{{ $orderLPK->remark ?? '' }}</textarea>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-2">
                                    <div class="fw-bold text-muted">
                                        Progress
                                    </div>
                                </div>
                                <div class="col-10">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="form-group mt-1">
                                                <div class="input-group">
                                                    <label
                                                        class="control-label col-12 col-lg-3 text-muted">INFURE:</label>
                                                    <input type="text" class="form-control readonly"
                                                        readonly="readonly"
                                                        value="{{ $orderLPK->progressInfure ?? '' }}" />
                                                    <span class="input-group-text">
                                                        meter
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-group mt-1">
                                                <div class="input-group">
                                                    <label
                                                        class="control-label col-12 col-lg-3 text-muted">{{ $orderLPK != null ? ($orderLPK->progressInfureSelisih < 0 ? 'Kurang' : 'Lebih') : '' }}:</label>
                                                    <input type="text"
                                                        class="form-control readonly {{ $orderLPK != null ? ($orderLPK->progressInfureSelisih < 0 ? 'text-danger' : 'text-info') : '' }}"
                                                        readonly="readonly"
                                                        value="{{ $orderLPK->progressInfureSelisih ?? 0 }}" />
                                                    <span class="input-group-text">
                                                        meter
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="form-group mt-1">
                                                <div class="input-group">
                                                    <label
                                                        class="control-label col-12 col-lg-3 mt-1 text-muted">SEITAI:</label>
                                                    <input type="text" class="form-control readonly"
                                                        readonly="readonly"
                                                        value="{{ $orderLPK->progressSeitai ?? '' }}" />
                                                    <span class="input-group-text">
                                                        lbr
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-group mt-1">
                                                <div class="input-group">
                                                    <label
                                                        class="control-label col-12 col-lg-3 mt-1 text-muted">{{ $orderLPK != null ? ($orderLPK->progressSeitaiSelisih < 0 ? 'Kurang' : 'Lebih') : '' }}:</label>
                                                    <input type="text"
                                                        class="form-control readonly {{ $orderLPK != null ? ($orderLPK->progressSeitaiSelisih < 0 ? 'text-danger' : 'text-info') : '' }}"
                                                        readonly="readonly"
                                                        value="{{ $orderLPK->progressSeitaiSelisih ?? '' }}" />
                                                    <span class="input-group-text">
                                                        lbr
                                                    </span>
                                                </div>
                                            </div>
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
        $wire.on('showModalAddGentan', () => {
            $('#modal-add-gentan').modal('show');
        });

        $wire.on('closeModalAddGentan', () => {
            $('#modal-add-gentan').modal('hide');
        });
        $wire.on('showModalEditGentan', () => {
            $('#modal-edit-gentan').modal('show');
        });

        $wire.on('closeModalEditGentan', () => {
            $('#modal-edit-gentan').modal('hide');
        });
        $wire.on('showModalNoOrder', () => {
            $('#modal-noorder-produk').modal('show');
        });
        // close modal NoOrder
        $wire.on('closeModalNoOrder', () => {
            $('#modal-noorder-produk').modal('hide');
        });

        // show modal LPK
        $wire.on('showModalLPK', () => {
            $('#modal-lpk').modal('show');
        });
        // close modal LPK
        $wire.on('closeModalLPK', () => {
            $('#modal-lpk').modal('hide');
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
