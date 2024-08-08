<div class="row">
    <form wire:submit.prevent="save">
        <div class="row mt-2">
            <div class="col-4 col-lg-12">
                <div class="row">
                    <div class="col-12 col-lg-6">
                        <div class="form-group">
                            <div class="input-group">
                                <label class="control-label pe-2">Tanggal Produksi</label>
                                <input class="form-control" type="text" style="padding:0.44rem" data-provider="flatpickr" data-date-format="d-m-Y" wire:model.defer="production_date" placeholder="yyyy/mm/dd"/>
                                <span class="input-group-text py-0">
                                    <i class="ri-calendar-event-fill fs-4"></i>
                                </span>
                                @error('production_date')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-6">
                        <div class="form-group">
                            <div class="input-group">
                                <label class="control-label pe-2">Tanggal Proses</label>
                                <input class="form-control" type="text" style="padding:0.44rem" data-provider="flatpickr" data-date-format="d-m-Y" wire:model.defer="created_on" placeholder="yyyy/mm/dd"/>
                                <span class="input-group-text py-0">
                                    <i class="ri-calendar-event-fill fs-4"></i>
                                </span>
                                @error('created_on')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-4 mt-1">
                        <div class="form-group">
                            <div class="input-group">
                                <label class="control-label col-5 pe-2">Nomor LPK</label>
                                <input type="text" class="form-control @error('lpk_no') is-invalid @enderror" wire:model.live.debounce.300ms="lpk_no" />
                                @error('lpk_no')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-4 mt-1">
                        <div class="form-group">
                            <div class="input-group">
                                <label class="control-label pe-2">Tanggal LPK</label>
                                <input class="form-control readonly datepicker-input" readonly="readonly" type="date" wire:model.defer="lpk_date" placeholder="yyyy/mm/dd"/>
                                <span class="input-group-text py-0">
                                    <i class="ri-calendar-event-fill fs-4"></i>
                                </span>
                                @error('lpk_date')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-4 mt-1">
                        <div class="form-group">
                            <div class="input-group">
                                <label class="control-label pe-2">Panjang LPK</label>
                                <input type="text" placeholder="-" class="form-control readonly" readonly="readonly" wire:model="panjang_lpk" />
                                <span class="input-group-text">
                                    m
                                </span>
                                @error('panjang_lpk')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-4 mt-1">
                        <div class="form-group">
                            <div class="input-group">
                                <label class="control-label col-5 pe-2">Nomor Order</label>
                                <input type="text" placeholder="-" class="form-control readonly" readonly="readonly" wire:model="code" />
                                @error('code')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-8 mt-1">
                        <div class="form-group">
                            <div class="input-group">
                                <label class="control-label"></label>
                                <input type="text" placeholder="-" class="form-control readonly" readonly="readonly" wire:model="name" />
                                @error('name')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-4 mt-1">
                        <div class="form-group">
                            <div class="input-group">
                                <label class="control-label col-5 pe-2">Nomor Mesin</label>
                                <input type="text" placeholder=" ... " class="form-control @error('machineno') is-invalid @enderror" wire:model.live.debounce.300ms="machineno" />
                                @error('machineno')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-8 mt-1">
                        <div class="form-group">
                            <div class="input-group">
                                <label class="control-label"></label>
                                <input type="text" placeholder="-" class="form-control readonly" readonly="readonly" wire:model="machinename" />
                                @error('machinename')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-4 mt-1">
                        <div class="form-group">
                            <div class="input-group">
                                <label class="control-label col-5 pe-2">Petugas</label>
                                <input type="text" placeholder=" ... " class="form-control @error('employeeno') is-invalid @enderror" wire:model.live="employeeno" />
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
                                <input type="text" placeholder="-" class="form-control readonly" readonly="readonly" wire:model="empname" />
                                @error('empname')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-4 mt-1">
                        <div class="form-group">
                            <div class="input-group">
                                <label class="control-label col-5">Dimensi Infure</label>
                                <input type="text" placeholder="-" class="form-control readonly" readonly="readonly" wire:model="dimensiinfure" />
                                <span class="input-group-text">
                                    mm
                                </span>
                                @error('dimensiinfure')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-8 mt-1">
                        <div class="form-group">
                            <div class="input-group">
                                <label class="control-label col-3">Meter Gulung</label>
                                <input type="text" placeholder="-" class="form-control readonly" readonly="readonly" wire:model="qty_gulung" />
                                <span class="input-group-text">
                                    m
                                </span>
                                @error('qty_gulung')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror

                                <input type="text" class="form-control readonly" readonly="readonly" placeholder=" .. X .." />
                                <input type="text" class="form-control readonly" readonly="readonly" wire:model="qty_gulung" />
                                <span class="input-group-text">
                                    roll
                                </span>
                                @error('qty_gulung')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-4 mt-1">
                        <div class="form-group">
                            <div class="input-group">
                                <label class="control-label col-5">Panjang Produksi</label>
                                <input type="text" placeholder="-" class="form-control @error('panjang_produksi') is-invalid @enderror" wire:model="panjang_produksi" />
                                <span class="input-group-text">
                                    m
                                </span>
                                @error('panjang_produksi')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-5 mt-1">
                        <div class="form-group">
                            <div class="input-group">
                                <label class="control-label col-6">Total Panjang Produksi</label>
                                <input type="text" placeholder="0" class="form-control readonly" readonly="readonly" wire:model="total_assembly_qty" />
                                <span class="input-group-text">
                                    m
                                </span>
                                @error('total_assembly_qty')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-3 mt-1">
                        <div class="form-group">
                            <div class="input-group">
                                <label class="control-label col-3">Selisih</label>
                                <input type="text" placeholder="0" class="form-control readonly" readonly="readonly" wire:model="selisih" />
                                <span class="input-group-text">
                                    m
                                </span>
                                @error('selisih')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-4 mt-1">
                        <div class="form-group">
                            <div class="input-group">
                                <label class="control-label col-5">Berat Gentan</label>
                                <input type="number" class="form-control @error('qty_gentan') is-invalid @enderror" wire:model="qty_gentan" />
                                <span class="input-group-text">
                                    kg
                                </span>
                                @error('qty_gentan')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-5 mt-1">
                        <div class="form-group">
                            <div class="input-group">
                                <label class="control-label col-6">Berat Standard</label>
                                <input type="text" placeholder="0" class="form-control readonly" readonly="readonly" wire:model="berat_standard" />
                                <span class="input-group-text">
                                    kg
                                </span>
                                @error('berat_standard')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-3 mt-1">
                        <div class="form-group">
                            <div class="input-group">
                                <label class="control-label col-3">Rasio</label>
                                <input type="text" placeholder="0" class="form-control readonly" readonly="readonly"  wire:model="rasio" />
                                <span class="input-group-text">
                                    %
                                </span>
                                {{-- @error('rasio')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror --}}
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-4 mt-1">
                        <div class="form-group">
                            <div class="input-group">
                                <label class="control-label col-5 pe-2">Jam Produksi</label>
                                <input class="form-control" wire:model="work_hour" type="time" placeholder="hh:mm">
                                {{-- <input class="form-control" type="time" placeholder="hh:mm" wire:model="work_hour"> --}}
                                @error('work_hour')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-4 mt-1">
                        <div class="form-group">
                            <div class="input-group">
                                <label class="control-label col-4">Shift Kerja</label>
                                <input type="text" class="form-control readonly" readonly="readonly" wire:model="work_shift" />
                                @error('work_shift')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-4 mt-1">
                        <div class="form-group">
                            <div class="input-group">
                                <label class="control-label col-4">Nomor Han</label>
                                <input type="text" class="form-control" placeholder="00-00-00-00A" wire:model="nomor_han" />
                                @error('nomor_han')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-4 mt-1">
                        <div class="form-group">
                            <div class="input-group">
                                <label class="control-label col-5 pe-2">Nomor Barcode</label>
                                <input type="text" class="form-control" wire:model.debounce.300ms="nomor_barcode" />
                                @error('nomor_barcode')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-4 mt-1">
                        <div class="form-group">
                            <div class="input-group">
                                <label class="control-label col-5 pe-2">Nomor Gentan</label>
                                <input type="text" class="form-control"  wire:model="gentan_no" />
                                @error('gentan_no')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <hr/>
        <div class="row">
            <div class="col-lg-8">
                <button wire:click="addLossInfure" type="button" class="btn btn-success">
                    <i class="ri-add-line"></i> Add Loss Infure
                </button>
                {{-- <button data-bs-toggle="modal" data-bs-target="#modal-add" type="button" class="btn btn-success">
                    <i class="ri-add-line"></i> Add Loss Infure
                </button> --}}
            </div>
            <div class="col-lg-4" style="border-top:1px solid #efefef">
                <div class="toolbar">
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
                    <button type="submit" class="btn btn-success">
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
                    <button type="button" class="btn btn-success btn-print" disabled="disabled">
                        <i class="bx bx-printer"></i> Print
                    </button>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modal-add" tabindex="-1" role="dialog" aria-labelledby="modal-add" aria-hidden="true" wire:ignore.self>
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="h6 modal-title">Add Loss Infure</h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-lg-12 mb-1">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <label class="col-12 col-lg-2 fw-bold text-muted">Kode Loss </label>
                                            <input class="form-control" type="text" wire:model.live="loss_infure_id" placeholder="..." />
                                            @error('loss_infure_id')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12 mb-1">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <label class="col-12 col-lg-2 fw-bold text-muted">Nama Loss </label>
                                            <input class="form-control readonly" readonly="readonly" type="text" wire:model.defer="name_infure" placeholder="..." />
                                            @error('name')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12 mb-1">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <label class="col-12 col-lg-2 fw-bold text-muted">Berat Loss </label>
                                            <input class="form-control" type="text" wire:model.defer="berat_loss" placeholder="0" />
                                            @error('berat_loss')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            {{-- <button type="button" class="btn btn-secondary">Accept</button> --}}
                            <button type="button" class="btn btn-link text-gray-600 ms-auto" data-bs-dismiss="modal">Close</button>
                            {{-- <button type="button" class="btn btn-success" wire:click="saveInfure">
                                Save
                            </button> --}}
                            <button type="button" class="btn btn-success" wire:click="saveInfure">
                                <span wire:loading.remove wire:target="saveInfure">
                                    <i class="ri-save-3-line"></i> Save
                                </span>
                                <div wire:loading wire:target="saveInfure">
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
                                <th class="border-0">Kode</th>
                                <th class="border-0">Nama Loss</th>
                                <th class="border-0 rounded-end">Berat (kg)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $total=0
                            @endphp
                            @forelse ($details as $item)
                                <tr>
                                    <td>
                                        <button type="button" class="btn btn-danger" wire:click="deleteInfure({{$item->id}})">
                                            <i class="fa fa-trash"></i> Delete
                                        </button>
                                    </td>
                                    <td>
                                        {{ $item->loss_infure_id }}
                                    </td>
                                    <td>
                                        {{ $item->name_infure }}
                                    </td>
                                    <td>
                                        {{ $item->berat_loss }}
                                    </td>
                                </tr>
                                @php
                                    $total += $item->berat_loss;
                                @endphp
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">No results found</td>
                                </tr>
                            @endforelse
                            <tr>
                                <td colspan="3" class="text-end">Berat Loss Total (kg):</td>
                                <td colspan="1" class="text-center">{{ $total }}</td>
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

      $('#modal-add').modal('show');

    });
    $wire.on('closeModal', () => {

      $('#modal-add').modal('hide');

    });
 </script>
@endscript
