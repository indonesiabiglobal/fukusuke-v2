<div class="row">
    <form wire:submit.prevent="save">
        <div class="row mt-2">
            <div class="col-4 col-lg-12">
                <div class="row">
                    <div class="col-12 col-lg-4">
                        <div class="form-group">
                            <div class="input-group">
                                <label class="control-label col-5">Tanggal Produksi</label>
                                {{-- <input class="form-control datepicker-input" type="datetime-local" wire:model.defer="production_date" placeholder="yyyy/mm/dd"/> --}}
                                <input class="form-control" type="text" style="padding:0.44rem" data-provider="flatpickr" data-date-format="d/m/Y" data-default-date="{{ $production_date }}" data-maxDate="{{ now()->format('d/m/Y') }}" wire:model.change="production_date" placeholder="dd/mm/yyyy"/>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-4">
                        <div class="form-group">
                            <div class="input-group">
                                <label class="control-label col-5">Nomor Produksi</label>
                                <input type="text" class="form-control readonly bg-light" readonly="readonly" wire:model="production_no" />
                                @error('lpk_no')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-4">
                        <div class="form-group">
                            <div class="input-group">
                                <label class="control-label col-5">Tanggal Proses</label>
                                <input type="text" class="form-control readonly bg-light" readonly="readonly" wire:model="created_on"/>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-4 mt-1">
                        <div class="form-group">
                            <div class="input-group">
                                {{-- <label class="control-label col-5 pe-2">Nomor LPK</label> --}}
                                <label class="control-label col-5 pe-2 fw-bold text-muted" style="text-decoration: underline;">
                                    <a href="#" data-bs-toggle="modal" wire:click="showModalLPK" class="text-muted">
                                        Nomor LPK
                                    </a>
                                </label>
                                <input type="text" class="form-control readonly bg-light" readonly="readonly"  wire:model="lpk_no" />
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-4 mt-1">
                        <div class="form-group">
                            <div class="input-group">
                                <label class="control-label pe-2">Tanggal LPK</label>
                                <input class="form-control readonly datepicker-input bg-light" readonly="readonly" type="date" wire:model.defer="lpk_date" placeholder="yyyy/mm/dd"/>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-4 mt-1">
                        <div class="form-group">
                            <div class="input-group">
                                <label class="control-label pe-2">Jumlah LPK</label>
                                <input type="text" placeholder="-" class="form-control readonly bg-light" readonly="readonly" value="{{ number_format($qty_lpk) }}" />
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-4 mt-1">
                        <div class="form-group">
                            <div class="input-group">
                                <label class="control-label col-5 pe-2 fw-bold text-muted" style="text-decoration: underline;">
                                    <a href="#" data-bs-toggle="modal" wire:click="showModalNoOrder" class="text-muted">
                                        Nomor Order
                                    </a>
                                </label>
                                <input type="text" placeholder="-" class="form-control readonly bg-light" readonly="readonly" wire:model="code" />
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-8 mt-1">
                        <div class="form-group">
                            <div class="input-group">
                                <label class="control-label"></label>
                                <input type="text" placeholder="-" class="form-control readonly bg-light" readonly="readonly" wire:model="name" />
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-4 mt-1">
                        <div class="form-group">
                            <div class="input-group">
                                <label class="control-label col-5 pe-2">Nomor Mesin</label>
                                <input type="text" placeholder=" ... " class="form-control" wire:model.debounce.300ms="machineno"
                                x-on:keydown.tab="$event.preventDefault(); $refs.employeeno.focus();"
                                x-ref="machineInput" />
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-8 mt-1">
                        <div class="form-group">
                            <div class="input-group">
                                <label class="control-label"></label>
                                <input type="text" placeholder="-" class="form-control readonly bg-light" readonly="readonly" wire:model="machinename" />
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-4 mt-1">
                        <div class="form-group">
                            <div class="input-group">
                                <label class="control-label col-5 pe-2">Petugas</label>
                                <input type="text" placeholder=" ... " class="form-control" wire:model.change="employeeno"
                                x-on:keydown.tab="$event.preventDefault(); $refs.qty_produksi.focus();"
                                x-ref="employeeno" />
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-8 mt-1">
                        <div class="form-group">
                            <div class="input-group">
                                <label class="control-label"></label>
                                <input type="text" placeholder="-" class="form-control readonly bg-light" readonly="readonly" wire:model="empname" />
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-4 mt-1">
                        <div class="form-group">
                            <div class="input-group">
                                <label class="control-label col-5">Jumlah Produksi</label>
                                <input type="text" placeholder="-" class="form-control" wire:model.change="qty_produksi"
                                x-on:keydown.tab="$event.preventDefault(); document.querySelector('#nomor_palet').focus();"
                                x-ref="qty_produksi"
                                oninput="this.value = window.formatNumber(this.value)"/>
                                <span class="input-group-text">
                                    mm
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-5 mt-1">
                        <div class="form-group">
                            <div class="input-group">
                                <label class="control-label col-5">Total Produksi</label>
                                <input type="text" placeholder="-" class="form-control readonly bg-light" readonly="readonly" wire:model="total_assembly_qty" />
                                <span class="input-group-text">
                                    lbr
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-3 mt-1">
                        <div class="form-group">
                            <div class="input-group">
                                <label class="control-label col-3">Selisih</label>
                                <input type="text" class="form-control readonly bg-light" readonly="readonly" wire:model="selisih" />
                                <span class="input-group-text">
                                    lbr
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-4 mt-1">
                        <div class="form-group">
                            <div class="input-group">
                                <label class="control-label col-5">Nomor Palet</label>
                                <div x-data="{ nomor_palet: @entangle('nomor_palet'), status: true }" x-init="$watch('nomor_palet', value => {
                                    nomor_palet = value.charAt(0).toUpperCase() + value.slice(1).replace(/[^0-9-]/g, '');
                                    if (value.length === 5 && !value.includes('-') && status) {
                                        nomor_palet = value + '-';
                                    }
                                    if (value.length < 6) {
                                        status = true;
                                    }
                                    if (value.length === 7) {
                                        status = false;
                                    }
                                    if (value.length > 12) {
                                        nomor_palet = value.substring(0, 12);
                                    }
                                })">
                                    <input class="form-control @error('nomor_palet') is-invalid @enderror" style="padding:0.44rem" type="text"
                                        placeholder="A0000-000000" x-model="nomor_palet" maxlength="12" id="nomor_palet"
                                        x-on:keydown.tab="$event.preventDefault(); $refs.nomor_lot.focus();" />
                                </div>
                                {{-- <input type="text" placeholder="A0000-000000" class="form-control" wire:model="nomor_palet"
                                x-on:keydown.tab="$event.preventDefault(); $refs.nomor_lot.focus();"
                                x-ref="nomor_palet" /> --}}
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-8 mt-1">
                        <div class="form-group">
                            <div class="input-group">
                                <label class="control-label col-3">Nomor LOT</label>
                                <input type="text" placeholder="----------" class="form-control" wire:model="nomor_lot"
                                x-on:keydown.tab="$event.preventDefault(); $refs.infure_berat_loss.focus();"
                                x-ref="nomor_lot"/>

                                <input type="text" class="form-control readonly bg-light" readonly="readonly" />
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-lg-4 mt-1">
                        <div class="form-group">
                            <div class="input-group">
                                <label class="control-label col-5">Loss Infure</label>
                                <input type="text" class="form-control"  wire:model="infure_berat_loss"
                                x-ref="infure_berat_loss" />
                                <span class="input-group-text">
                                    kg
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-8 mt-1">
                        <div class="form-group">
                            <div class="input-group">
                                <label class="control-label col-3">Petugas Infure</label>
                                <input type="text" placeholder="..." class="form-control" wire:model.change="employeenoinfure"
                                x-on:keydown.tab="$event.preventDefault(); $refs.work_hour.focus();" />

                                <input type="text" placeholder="-" class="form-control readonly bg-light" readonly="readonly" wire:model="empnameinfure" />
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-4 mt-1">
                        <div class="form-group">
                            <div class="input-group">
                                <label class="control-label col-5 pe-2">Jam Produksi</label>
                                {{-- <input class="form-control" type="time" placeholder="hh:mm" wire:model="work_hour"> --}}
                                <input class="form-control @error('work_hour') is-invalid @enderror" wire:model.change="work_hour" type="time" placeholder="HH:mm"
                                x-ref="work_hour" >
                                <span class="input-group-text py-0">
                                    <i class="ri-time-line fs-5"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-8 mt-1">
                        <div class="form-group">
                            <div class="input-group">
                                <label class="control-label col-2">Shift Kerja</label>
                                <input type="text" class="form-control readonly bg-light" readonly="readonly" wire:model="work_shift" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <hr/>

        <div class="row">
            <div class="col-lg-6 col-12">
                @if ($selisih < 0)
                <h4 class="text-danger text-center">Total jumlah produksi sudah melebihi jumlah LPK ..!</h4>
                @endif
            </div>
        </div>

        <div class="row">
            <div class="col-lg-7">
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ $activeTab == 'Gentan' ? 'active' : '' }}" id="periode1SP-tab" data-bs-toggle="tab" data-bs-target="#periode1SP" type="button" role="tab" aria-controls="periode1SP" aria-selected="true">Gentan(s)</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ $activeTab == 'Loss' ? 'active' : '' }}" id="periode2SP-tab" data-bs-toggle="tab" data-bs-target="#periode2SP" type="button" role="tab" aria-controls="periode2SP" aria-selected="false">Loss(<span class="text-danger">{{ $jumlahBeratLoss }} kg</span>)</button>
                    </li>
                </ul>
            </div>
            <div class="col-lg-5">
                <div class="toolbar">
                    {{-- <button id="btnFilter" type="button" class="btn btn-warning" wire:click="cancel">
                        <i class="fa fa-back"></i> Close
                    </button>
                    <button id="btnCreate" type="submit" class="btn btn-success">
                        <i class="fa fa-plus"></i> Save
                    </button>
                    <button type="button" class="btn btn-success btn-print" disabled="disabled">
                        <i class="fa fa-print"></i> Print
                    </button> --}}
                    <button type="button" class="btn btn-warning" wire:click="cancel">
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
                    <button id="btnFilter" type="button" class="btn btn-danger" wire:click="delete">
                        <span wire:loading.remove wire:target="delete">
                            <i class="ri-delete-bin-line"> </i> delete
                        </span>
                        <div wire:loading wire:target="delete">
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
                    <button type="button" wire:click="save" class="btn btn-success">
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
                    <button type="button" class="btn btn-success" wire:click="print">
                        <i class="bx bx-printer"></i> Print
                    </button>
                </div>
            </div>
        </div>


        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade {{ $activeTab == 'Gentan' ? 'show active' : '' }}" id="periode1SP" role="tabpanel" aria-labelledby="periode1SP-tab">
                <div class="row justify-content-start">
                    <div class="row mt-3">
                        <div class="col-lg-8">
                            <button wire:click="addGentan" type="button" class="btn btn-warning">
                                <i class="fa fa-plus"></i> Add Gentan
                            </button>
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
                                            <th class="border-0">Line</th>
                                            <th class="border-0">No Mesin</th>
                                            <th class="border-0">Shift</th>
                                            <th class="border-0">Petugas</th>
                                            <th class="border-0">Tg. Produksi</th>
                                            <th class="border-0">Frekuensi</th>
                                            <th class="border-0 rounded-end">Berat Produksi (kg)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $total=0
                                        @endphp
                                        @forelse ($detailsGentan as $item)
                                            <tr>
                                                <td>
                                                    <button type="button" class="btn btn-danger" wire:click="deleteGentan({{$item->id}})">
                                                        <i class="fa fa-trash"></i> Delete
                                                    </button>
                                                </td>
                                                <td>
                                                    {{ $item->gentan_no }}
                                                </td>
                                                <td>
                                                    {{ $item->gentan_line }}
                                                </td>
                                                <td>
                                                    {{ $item->machineno }}
                                                </td>
                                                <td>
                                                    {{ $item->work_shift }}
                                                </td>
                                                <td>
                                                    {{ $item->empname }}
                                                </td>
                                                <td>
                                                    {{ $item->production_date }}
                                                </td>
                                                <td>
                                                    {{ $item->frekuensi }}
                                                </td>
                                                <td>
                                                    {{ number_format($item->berat_produksi, 2) }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center">No results found</td>
                                            </tr>
                                        @endforelse
                                        <tr>
                                            <td colspan="7" class="text-end">Berat Total (kg):</td>
                                            <td colspan="1" class="text-center">{{ $jumlahBeratProduksi }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade {{ $activeTab == 'Loss' ? 'show active' : '' }}" id="periode2SP" role="tabpanel" aria-labelledby="periode2SP-tab">
                <div class="row justify-content-start">
                    <div class="row mt-3">
                        <div class="col-lg-8">
                            <button wire:click="addLoss" type="button" class="btn btn-success">
                                <i class="fa fa-plus"></i> Add Loss Seitai
                            </button>
                        </div>
                    </div>

                    <div class="card border-0 shadow mb-4 mt-4">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-centered table-nowrap mb-0 rounded">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="border-0 rounded-start">Action</th>
                                            <th class="border-0">Kode</th>
                                            <th class="border-0">Nama Loss</th>
                                            <th class="border-0">Frekuensi</th>
                                            <th class="border-0 rounded-end">Berat (kg)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $total=0
                                        @endphp
                                        @forelse ($detailsLoss as $item)
                                            <tr>
                                                <td>
                                                    <button type="button" class="btn btn-danger" wire:click="deleteLoss({{$item->id}})">
                                                        <i class="fa fa-trash"></i> Delete
                                                    </button>
                                                </td>
                                                <td>
                                                    {{ $item->code }}
                                                </td>
                                                <td>
                                                    {{ $item->name }}
                                                </td>
                                                <td>
                                                    {{ $item->frekuensi }}
                                                </td>
                                                <td>
                                                    {{ $item->berat_loss }}
                                                </td>
                                            </tr>
                                        @empty
                                        <tr>
                                            <td colspan="4" class="text-center">No results found</td>
                                        </tr>
                                        @endforelse

                                        <tr>
                                            <td colspan="3" class="text-end">Berat Loss Total (kg):</td>
                                            <td colspan="1" class="text-center">{{ number_format($jumlahBeratLoss, 2) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modal-gentan" tabindex="-1" role="dialog" aria-labelledby="modal-gentan" aria-hidden="true" wire:ignore.self>
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="h6 modal-title">Add Gentan Infure</h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-lg-12 mb-1">
                                    <div class="form-group">
                                        <label>Nomor Gentan </label>
                                        <div class="input-group col-md-9 col-xs-8">
                                            <input id="inputKodeGentan" class="form-control" type="text" wire:model.live.debounce.300ms="gentan_no"
                                            x-on:keydown.tab="$event.preventDefault(); $refs.gentan_line.focus();" placeholder="..." />
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
                                            <input class="form-control readonly bg-light" readonly="readonly" type="text" wire:model.defer="machine_no" placeholder="..." />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12 mb-1">
                                    <div class="form-group">
                                        <label>Petugas </label>
                                        <div class="input-group col-md-9 col-xs-8">
                                            <input class="form-control readonly bg-light" readonly="readonly" type="text" wire:model.defer="petugas" placeholder="..." />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12 mb-1">
                                    <div class="form-group">
                                        <label>Berat Produksi</label>
                                        <div class="input-group col-md-9 col-xs-8">
                                            <input class="form-control readonly bg-light" readonly="readonly" type="text" wire:model.defer="berat_produksi" placeholder="..." />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12 mb-1">
                                <div class="form-group">
                                    <label>Line Gentan </label>
                                    <div class="input-group col-md-9 col-xs-8">
                                        <select class="form-select" wire:model.defer="gentan_line" x-ref="gentan_line">
                                            <option value="">Pilih Line</option>
                                            <option value="A">A</option>
                                            <option value="B">B</option>
                                        </select>
                                        @error('gentan_line')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                                {{-- <div class="col-lg-12 mb-1">
                                    <div class="form-group">
                                        <label>Berat</label>
                                        <div class="input-group col-md-9 col-xs-8">
                                            <input class="form-control" type="text" wire:model.defer="berat" placeholder="..." />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12 mb-1">
                                    <div class="form-group">
                                        <label>Frekuensi</label>
                                        <div class="input-group col-md-9 col-xs-8">
                                            <input class="form-control" type="text" wire:model.defer="frekuensi" placeholder="..." />
                                        </div>
                                    </div>
                                </div> --}}
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-link text-gray-600 ms-auto" data-bs-dismiss="modal">Close</button>
                            <button type="button" wire:click="saveGentan" class="btn btn-success">
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
                        </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modal-loss" tabindex="-1" role="dialog" aria-labelledby="modal-loss" aria-hidden="true" wire:ignore.self>
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="h6 modal-title">Add Loss Seitai</h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-lg-12 mb-1">
                                    <div class="form-group">
                                        <label>Kode Loss </label>
                                        <div class="input-group col-md-9 col-xs-8">
                                            <input id="inputKodeLoss" class="form-control" type="text" wire:model.live.debounce.300ms="loss_seitai_id" placeholder="..." x-on:keydown.tab="$event.preventDefault(); $refs.berat_loss.focus();" />
                                            @error('loss_seitai_id')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12 mb-1">
                                    <div class="form-group">
                                        <label>Nama Loss </label>
                                        <div class="input-group col-md-9 col-xs-8">
                                            <input class="form-control readonly bg-light" readonly="readonly" type="text" wire:model.defer="namaloss" placeholder="..." />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12 mb-1">
                                    <div class="form-group">
                                        <label>Berat Loss </label>
                                        <div class="input-group col-md-9 col-xs-8">
                                            <input class="form-control" type="text" wire:model.defer="berat_loss" placeholder="0"  x-ref="berat_loss"/>
                                            @error('berat_loss')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                {{-- <div class="col-lg-12 mb-1">
                                    <div class="form-group">
                                        <label>Berat </label>
                                        <div class="input-group col-md-9 col-xs-8">
                                            <input class="form-control" type="text" wire:model.defer="berat_fr" placeholder="0" />
                                            @error('berat_fr')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div> --}}
                                <div class="col-lg-12 mb-1">
                                    <div class="form-group">
                                        <label>Frekuensi </label>
                                        <div class="input-group col-md-9 col-xs-8">
                                            <input class="form-control" type="text" wire:model.defer="frekuensi_fr" placeholder="0" />
                                            @error('frekuensi_fr')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-link text-gray-600 ms-auto" data-bs-dismiss="modal">Close</button>
                            <button type="button" wire:click="saveLoss" class="btn btn-success">
                                <span wire:loading.remove wire:target="saveLoss">
                                    <i class="ri-save-3-line"></i> Save
                                </span>
                                <div wire:loading wire:target="saveLoss">
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

        <!--  modal master produk -->
        <div class="modal fade" id="modal-noorder-produk" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="myExtraLargeModalLabel">Produk Info - Nomor: <span class="fw-bold">{{ $code }}</span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="col-lg-12">
                            <div class="row">
                                <div class="col-12 col-lg-6">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-4">Nomor Order</label>
                                            <input type="text" class="form-control col-12 col-lg-8" value="{{ $product->code ?? '' }}"
                                                placeholder="KODE" required />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-4">Nama Produk</label>
                                            <input type="text" class="form-control col-12 col-lg-8" value="{{ $product->name ?? '' }}"
                                                placeholder="nama" required />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-4">Kode Tipe</label>
                                            <input type="text" class="form-control col-12 col-lg-8" value="{{ $product->product_type_id ?? '' }}"/>
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-4">Kode Produk (Alias)</label>
                                            <input type="text" class="form-control col-12 col-lg-8" value="{{ $product->code_alias ?? '' }}"
                                                placeholder="KODE" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-4">Code Barcode</label>
                                            <input type="text" class="form-control col-12 col-lg-8" value="{{ $product->codebarcode ?? '' }}"
                                                placeholder="KODE" />
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
                                            <input required type="number" class="form-control" value="{{ $product->ketebalan ?? '' }}"
                                                placeholder="Tebal" />
                                            <span class="input-group-text">
                                                L
                                            </span>
                                            <input required type="number" class="form-control" value="{{ $product->diameterlipat ?? '' }}"
                                                placeholder="Lebar" />
                                            <span class="input-group-text">
                                                P
                                            </span>
                                            <input required type="number" class="form-control" value="{{ $product->productlength ?? '' }}"
                                                placeholder="Panjang" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-4">Berat Satuan</label>
                                            <input required type="number" class="form-control col-12 col-lg-8" value="{{ $product->unit_weight ?? '' }}"
                                                placeholder="0" />
                                            <span class="input-group-text">
                                                gram
                                            </span>
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-4">Satuan</label>
                                            <input required type="text" class="form-control" value="{{ $product->product_unit ?? '' }}" />
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
                                            <input required type="text" class="form-control" value="{{ $product->inflation_thickness ?? '' }}"
                                                placeholder="Tebal" />
                                            @error('inflation_thickness')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                            <span class="input-group-text">
                                                x
                                            </span>
                                            <input required type="text" class="form-control" value="{{ $product->inflation_fold_diameter ?? '' }}"
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
                                            <input required type="text" class="form-control col-12 col-lg-8" value="{{ $product->material_classification ?? '' }}" placeholder="0" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-4">Embos</label>
                                            <input required type="text" class="form-control col-12 col-lg-8" value="{{ $product->embossed_classification ?? '' }}" placeholder="0" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-4">Corona</label>
                                            <input required type="text" class="form-control col-12 col-lg-8" value="{{ $product->surface_classification ?? '' }}" placeholder="0" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-lg-6">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-4">MB -1 (Master Batch) </label>
                                            <input type="text" class="form-control" value="{{ $product->coloring_1 ?? '' }}"
                                                placeholder="warna mb 1" />
                                            @error('coloring_1')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-4">MB -2 </label>
                                            <input type="text" class="form-control" value="{{ $product->coloring_2 ?? '' }}"
                                                placeholder="warna mb 2" />
                                            @error('coloring_2')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-4">MB -3 </label>
                                            <input type="text" class="form-control" value="{{ $product->coloring_3 ?? '' }}"
                                                placeholder="warna mb 3" />
                                            @error('coloring_3')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-4">MB -4 </label>
                                            <input type="text" class="form-control" value="{{ $product->coloring_4 ?? '' }}"
                                                placeholder="warna mb 4" />
                                            @error('coloring_4')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-4">MB -5 </label>
                                            <input type="text" class="form-control" value="{{ $product->coloring_5 ?? '' }}"
                                                placeholder="warna mb 5" />
                                            @error('coloring_5')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-4">Catatan </label>
                                            <input type="text" class="form-control" value="{{ $product->inflation_notes ?? '' }}"
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
                                            <input type="text" class="form-control" value="{{ $product->gentan_classification ?? '' }}" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-4">Gazette</label>
                                            <input type="text" class="form-control" value="{{ $product->gazette_classification ?? '' }}" />
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
                                    <img src="{{ asset('asset/image/Gazette-ent.png') }}" width="240" height="130"
                                        alt="img">
                                </div>
                                <div class="col-12">
                                    <p class="text-success">HAGATA</p>
                                </div>
                                <div class="col-12 col-lg-6">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-4">Kode Nukigata</label>
                                            <input type="text" class="form-control" value="{{ $katanuki_id ?? '' }}" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-4">A.</label>
                                            <input required type="number" class="form-control col-12 col-lg-8"
                                                value="{{ $product->extracted_dimension_a ?? '' }}" placeholder="0" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-4">B.</label>
                                            <input required type="number" class="form-control col-12 col-lg-8"
                                                value="{{ $product->extracted_dimension_b ?? '' }}" placeholder="0" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-4">C.</label>
                                            <input required type="number" class="form-control col-12 col-lg-8"
                                                value="{{ $product->extracted_dimension_c ?? '' }}" placeholder="0" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-lg-6 mt-3 d-flex justify-content-center">
                                    @if ($photoKatanuki)
                                        <img src="{{ asset('storage/' . $photoKatanuki) }}" width="240" height="130"
                                            alt="img">
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
                                            <input required type="number" class="form-control" value="{{ $product->number_of_color ?? '' }}"
                                                placeholder="..." />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-1">1</label>
                                            <input type="text" class="form-control" value="{{ $product->color_spec_1 ?? '' }}"
                                                placeholder="..." />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-1">2</label>
                                            <input type="text" class="form-control" value="{{ $product->color_spec_2 ?? '' }}"
                                                placeholder="..." />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-1">3</label>
                                            <input type="text" class="form-control" value="{{ $product->color_spec_3 ?? '' }}"
                                                placeholder="..." />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-1">4</label>
                                            <input type="text" class="form-control" value="{{ $product->color_spec_4 ?? '' }}"
                                                placeholder="..." />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-1">5</label>
                                            <input type="text" class="form-control" value="{{ $product->color_spec_5 ?? '' }}"
                                                placeholder="..." />
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
                                            <input required type="text" class="form-control" value="{{ $product->back_color_number ?? '' }}"
                                                placeholder="..." />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-1">1</label>
                                            <input type="text" class="form-control" value="{{ $product->back_color_1 ?? '' }}"
                                                placeholder="..." />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-1">2</label>
                                            <input type="text" class="form-control" value="{{ $product->back_color_2 ?? '' }}"
                                                placeholder="..." />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-1">3</label>
                                            <input type="text" class="form-control" value="{{ $product->back_color_3 ?? '' }}"
                                                placeholder="..." />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-1">4</label>
                                            <input type="text" class="form-control" value="{{ $product->back_color_4 ?? '' }}"
                                                placeholder="..." />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-1">5</label>
                                            <input type="text" class="form-control" value="{{ $product->back_color_5 ?? '' }}"
                                                placeholder="..." />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-lg-4">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-4">Jenis Cetak</label>
                                            <input type="text" class="form-control" value="{{ $product->print_type ?? '' }}" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-4">Sifat Tinta</label>
                                            <input type="text" class="form-control" value="{{ $product->ink_characteristic ?? '' }}" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-4">Endless</label>
                                            <input type="text" class="form-control" value="{{ $product->endless_printing ?? '' }}" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-4">Arah Gulung</label>
                                            <input type="text" class="form-control" value="{{ $product->winding_direction_of_the_web ?? '' }}" />
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
                                            <input type="text" class="form-control" value="{{ $product->seal_classification ?? '' }}" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-6">Jarak Seal dari Pola</label>
                                            <input required type="number" class="form-control" value="{{ $product->from_seal_design ?? '' }}"
                                                placeholder="..." min="0" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-6">Jarak Seal Bawah</label>
                                            <input required type="number" class="form-control" value="{{ $product->lower_sealing_length ?? '' }}"
                                                placeholder="..." min="0" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-6">Jumlah Baris Palet</label>
                                            <input required type="number" class="form-control" value="{{ $product->palet_jumlah_baris ?? '' }}"
                                                placeholder="..." min="0" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-6">Isi Baris Palet</label>
                                            <input required type="number" class="form-control" value="{{ $product->palet_isi_baris ?? '' }}"
                                                placeholder="..." min="0" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-6">Lakban Seitai</label>
                                            <input type="text" class="form-control" value="{{ $product->lakbanseitaiid ?? '' }}" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-6">Stempel Seitai</label>
                                            <input type="text" class="form-control" value="{{ $product->stampelseitaiid ?? '' }}" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-6">Hagata Seitai</label>
                                            <input type="text" class="form-control" value="{{ $product->hagataseitaiid ?? '' }}" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-6">Jenis Seal Seitai</label>
                                            <input type="text" class="form-control" value="{{ $product->jenissealseitaiid ?? '' }}" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-lg-5">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-3">Kode Gasio</label>
                                            <input type="text" class="form-control" value="{{ $product->pack_gaiso_id ?? '' }}" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-3">Kode Box</label>
                                            <input type="text" class="form-control" value="{{ $product->pack_box_id ?? '' }}" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-3">Kode Inner</label>
                                            <input type="text" class="form-control" value="{{ $product->pack_inner_id ?? '' }}" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-3">Kode Layer</label>
                                            <input type="text" class="form-control" value="{{ $product->pack_layer_id ?? '' }}" />
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
                                            <input required type="number" class="form-control" value="{{ $product->case_gaiso_count ?? '' }}"
                                                placeholder="0" />
                                            <input required type="text" class="form-control" value="{{ $product->case_gaiso_count_unit ?? '' }}"/>
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-2">Isi</label>
                                            <input type="number" class="form-control" value="{{ $product->case_box_count ?? '' }}"
                                                placeholder="0" />
                                            <input type="text" class="form-control" value="{{ $product->case_box_count_unit ?? '' }}" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-2">Isi</label>
                                            <input required type="text" class="form-control" value="{{ $product->case_inner_count ?? '' }}" />
                                            <input required type="text" class="form-control" value="{{ $product->case_inner_count_unit ?? '' }}"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <a href="javascript:void(0);" class="btn btn-light link-success fw-medium" data-bs-dismiss="modal"><i class="ri-close-line me-1 align-middle"></i> Close</a>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->

        <!--  modal LPK -->
        <div class="modal fade" id="modal-lpk" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="myExtraLargeModalLabel">LPK Info - Nomor: <span class="fw-bold">{{ $lpk_no }}</span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="col-lg-12">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-3 fw-bold text-muted">Tanggal LPK</label>
                                            <input value="{{ $orderLPK->lpk_date ?? '' }}" disabled type="text" class="form-control" style="padding:0.44rem" data-provider="flatpickr" data-date-format="d/m/Y">
                                            <span class="input-group-text py-0">
                                                <i class="ri-calendar-event-fill fs-4"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-3 fw-bold text-muted">Nomor LPK</label>
                                            <input type="text" class="form-control" value="{{ $orderLPK->lpk_no ?? '' }}" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-3 fw-bold text-muted">PO Number</label>
                                            <input type="text" class="form-control" value="{{ $orderLPK->po_no ?? '' }}" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-3 fw-bold text-muted">Nomor Order</label>
                                            <input type="text" class="form-control readonly bg-light" readonly="readonly" value="{{ $orderLPK->no_order ?? '' }}" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-3 fw-bold text-muted">Nomor Mesin</label>
                                            <input type="text" class="form-control" value="{{ $orderLPK->machineno ?? '' }}" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-3 fw-bold text-muted">Jumlah LPK</label>
                                            <input type="text" class="form-control" value="{{ $orderLPK->qty_lpk ?? '' }}" />
                                            <span class="input-group-text">
                                                Lembar
                                            </span>
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-3 fw-bold text-muted">Jumlah Gentan</label>
                                            <input type="text" class="form-control" value="{{ $orderLPK->qty_gentan ?? '' }}" />
                                            <span class="input-group-text">
                                                roll
                                            </span>
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-3 fw-bold text-muted">Meter Gulung</label>
                                            <input type="text" class="form-control" value="{{ $orderLPK->qty_gulung ?? '' }}" />
                                            <span class="input-group-text">
                                                meter
                                            </span>
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-3 fw-bold text-muted">Panjang LPK</label>
                                            <input type="text" class="form-control readonly bg-light" readonly="readonly" value="{{ $orderLPK->panjang_lpk ?? '' }}" />
                                            <span class="input-group-text">
                                                meter
                                            </span>
                                        </div>
                                    </div>
                                    {{-- warna LPK --}}
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-3 fw-bold text-muted">Warna LPK</label>
                                            <input type="text" class="form-control readonly bg-light" readonly="readonly" value="{{ $orderLPK->warnalpkid ?? '' }}" />
                                        </div>
                                    </div>

                                </div>

                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-3 fw-bold text-muted">Tanggal Proses</label>
                                            <input value="{{ $orderLPK->processdate ?? '' }}" disabled type="date" class="form-control datepicker-input" placeholder="yyyy/mm/dd" style="padding:0.44rem" data-provider="flatpickr" data-date-format="d/m/Y"/>
                                            <span class="input-group-text py-0">
                                                <i class="ri-calendar-event-fill fs-4"></i>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-3 fw-bold text-muted">Tanggal PO</label>
                                            <input class="form-control datepicker-input readonly bg-light" readonly="readonly" type="date" value="{{ $orderLPK->order_date ?? '' }}" placeholder="yyyy/mm/dd"/>
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-3 fw-bold text-muted">Buyer</label>
                                            <input type="text" class="form-control readonly bg-light"  readonly="readonly" value="{{ $orderLPK->buyer_name ?? '' }}" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-3 fw-bold text-muted">Nama Produk</label>
                                            <input type="text" class="form-control readonly bg-light" readonly="readonly" value="{{ $orderLPK->product_name ?? '' }}" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-3 fw-bold text-muted">Nama Mesin</label>
                                            <input type="text" class="form-control readonly bg-light" readonly="readonly" value="{{ $orderLPK->machinename ?? '' }}" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-3 fw-bold text-muted">Panjang Total</label>
                                            <input type="text" class="form-control readonly bg-light"  readonly="readonly" value="{{ $orderLPK->total_assembly_line ?? '' }}" />
                                            <span class="input-group-text">
                                                meter
                                            </span>
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-3 fw-bold text-muted">Dimensi (TxLxP)</label>
                                            <input type="text" class="form-control readonly bg-light" readonly="readonly" value="{{ $orderLPK->dimensi ?? '' }}" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-3 fw-bold text-muted">Default Gulung</label>
                                            <input type="text" class="form-control readonly bg-light"  readonly="readonly" value="{{ $orderLPK->defaultgulung ?? '' }}" />
                                            <span class="input-group-text" id="basic-addon2">
                                                meter
                                            </span>
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-3 fw-bold text-muted">Selisih Kurang</label>
                                            <input type="text" class="form-control readonly bg-light"  readonly="readonly" value="{{ $orderLPK->selisihKurang ?? '' }}" />
                                            <span class="input-group-text">
                                                meter
                                            </span>
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <label for="textarea" class="control-label col-12 col-lg-3 fw-bold text-muted">Catatan</label>
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
                                                    <label class="control-label col-12 col-lg-3 text-muted">INFURE:</label>
                                                    <input type="text" class="form-control readonly bg-light" readonly="readonly" value="{{ $orderLPK->progressInfure ?? '' }}" />
                                                    <span class="input-group-text">
                                                        meter
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-group mt-1">
                                                <div class="input-group">
                                                    <label class="control-label col-12 col-lg-3 text-muted">{{ $orderLPK != null ? ($orderLPK->progressInfureSelisih < 0 ? 'Kurang' : 'Lebih') : '' }}:</label>
                                                    <input type="text" class="form-control readonly bg-light {{ $orderLPK != null ? ($orderLPK->progressInfureSelisih < 0 ? 'text-danger' : 'text-info') : '' }}" readonly="readonly" value="{{ $orderLPK->progressInfureSelisih ?? 0 }}" />
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
                                                    <label class="control-label col-12 col-lg-3 mt-1 text-muted">SEITAI:</label>
                                                    <input type="text" class="form-control readonly bg-light" readonly="readonly" value="{{ $orderLPK->progressSeitai ?? '' }}" />
                                                    <span class="input-group-text">
                                                        lbr
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-group mt-1">
                                                <div class="input-group">
                                                    <label class="control-label col-12 col-lg-3 mt-1 text-muted">{{ $orderLPK != null ? ($orderLPK->progressSeitaiSelisih < 0 ? 'Kurang' : 'Lebih') : '' }}:</label>
                                                    <input type="text" class="form-control readonly bg-light {{ $orderLPK != null ? ($orderLPK->progressSeitaiSelisih < 0 ? 'text-danger' : 'text-info') : '' }}" readonly="readonly" value="{{ $orderLPK->progressSeitaiSelisih ?? '' }}" />
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
                        <a href="javascript:void(0);" class="btn btn-light link-success fw-medium" data-bs-dismiss="modal"><i class="ri-close-line me-1 align-middle"></i> Close</a>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->

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
                                <p class="text-muted mx-4 mb-0">Are you sure you want to remove this machine
                                    ?
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
    </form>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var modalAdd = document.getElementById('modal-gentan');

        modalAdd.addEventListener('shown.bs.modal', function () {
            document.getElementById('inputKodeGentan').focus();
        });
    });

    document.addEventListener('DOMContentLoaded', function () {
        var modalAdd = document.getElementById('modal-loss');

        modalAdd.addEventListener('shown.bs.modal', function () {
            document.getElementById('inputKodeLoss').focus();
        });
    });
</script>
@script
    <script>
        $wire.on('showModalGentan', () => {
            $('#modal-gentan').modal('show');
        });

        $wire.on('closeModalGentan', () => {
            $('#modal-gentan').modal('hide');
        });

        $wire.on('showModalLoss', () => {
            $('#modal-loss').modal('show');
        });

        $wire.on('closeModalLoss', () => {
            $('#modal-loss').modal('hide');
        });

        // show modal NoOrder Produk
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

        $wire.on('showModalDelete', () => {
            $('#modal-delete').modal('show');
        });

        // close modal delete machine
        $wire.on('closeModalDelete', () => {
            $('#modal-delete').modal('hide');
        });
    </script>
@endscript
{{-- <script>
    document.addEventListener('livewire:load', function () {
        Livewire.on('showModalGentan', () => {
            $('#modal-gentan').modal('show');
        });
        Livewire.on('closeModalGentan', () => {
            $('#modal-gentan').modal('hide');
        });

        Livewire.on('showModalLoss', () => {
            $('#modal-loss').modal('show');
        });
        Livewire.on('closeModalLoss', () => {
            $('#modal-loss').modal('hide');
        });
    });
</script> --}}
