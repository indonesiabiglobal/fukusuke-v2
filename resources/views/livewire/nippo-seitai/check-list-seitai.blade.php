<div class="row">
    <div class="col-lg-2"></div>
    <div class="col-lg-6">
        <div class="form-group">
            <div class="input-group">
                <label class="control-label col-3 text-muted fw-bold">Tanggal Produksi</label>
                <div class="col-12 col-lg-9">
                    <select class="form-select mb-0" wire:model.defer="transaksi">
                        <option value="produksi">Produksi</option>
                        <option value="proses">Proses</option>
                    </select>
                </div>
            </div>
            <div class="col-12 mt-1">
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-addon col-12 col-lg-3 text-muted fw-bold">Awal: </span>
                        <input wire:model.defer="tglAwal" type="text" class="form-control" style="padding:0.44rem"
                            data-provider="flatpickr" data-date-format="Y-m-d">
                        <span class="input-group-text py-0">
                            <i class="ri-calendar-event-fill fs-4"></i>
                        </span>

                        <div class="mb-1" wire:ignore>
                            <select class="form-control" wire:model.defer="jamAwal" data-choices
                                data-choices-search-false data-choices-removeItem data-choices-search-field-label>
                                {{-- <option value="">- All -</option> --}}
                                @foreach ($workingShiftHour as $item)
                                    <option value="{{ $item->work_hour_from }}"
                                        @if ($jamAwal == $item->work_hour_from) selected @endif>{{ $item->work_hour_from }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        {{-- <input wire:model.defer="jamAwal" type="text" class="form-control" data-provider="timepickr" data-time-hrs="true" id="timepicker-24hrs"> --}}
                        <span class="input-group-text py-0">
                            <i class="ri-time-line fs-4"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group mt-1">
            <div class="col-12">
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-addon col-12 col-lg-3 text-muted fw-bold">Akhir: </span>
                        <input wire:model.defer="tglAkhir" type="text" class="form-control" style="padding:0.44rem"
                            data-provider="flatpickr" data-date-format="Y-m-d">
                        <span class="input-group-text py-0">
                            <i class="ri-calendar-event-fill fs-4"></i>
                        </span>

                        <div class="mb-1" wire:ignore>
                            <select class="form-control" wire:model.defer="jamAkhir" data-choices
                                data-choices-search-false data-choices-removeItem data-choices-search-field-label>
                                {{-- <option value="">- All -</option> --}}
                                @foreach ($workingShiftHour as $item)
                                    <option value="{{ $item->work_hour_till }}"
                                        @if ($jamAkhir == $item->work_hour_till) selected @endif>{{ $item->work_hour_till }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <span class="input-group-text py-0">
                            <i class="ri-time-line fs-4"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group mt-1">
            <div class="input-group">
                <span class="input-group-addon col-12 col-lg-3 text-muted fw-bold">Jenis Report </span>
                <select wire:model.defer="jenisReport" class="form-control" placeholder="- pilih jenis report -">
                    <option value="CheckList">Check List</option>
                    <option value="LossSeitai">Loss Seitai</option>
                </select>
            </div>
        </div>
        <div class="form-group mt-1">
            <div class="input-group">
                <span class="input-group-addon col-12 col-lg-3 text-muted fw-bold">Nomor Proses </span>
                <input type="text" class="form-control" placeholder="1" wire:model.defer="noprosesawal"
                    value="1">
                <span class="input-group-text readonly" readonly="readonly">
                    ~
                </span>
                <input type="text" class="form-control" placeholder="1000" wire:model.defer="noprosesakhir"
                    value="1000">
            </div>
        </div>
        <div class="form-group mt-1">
            <div class="input-group">
                <span class="input-group-addon col-12 col-lg-3 text-muted fw-bold">Nomor LPK </span>
                <div class="col-12 col-lg-9" x-data="{ lpk_no: @entangle('lpk_no').live, status: true }" x-init="$watch('lpk_no', value => {
                        if (value.length === 6 && !value.includes('-') && status) {
                            lpk_no = value + '-';
                        }
                        if (value.length < 6) {
                            status = true;
                        }
                        if (value.length === 7) {
                            status = false;
                        }
                        if (value.length > 10) {
                            lpk_no = value.substring(0, 10);
                        }
                    })">
                    <input
                        class="form-control"
                        style="padding:0.44rem"
                        type="text"
                        placeholder="000000-000"
                        x-model="lpk_no"
                        maxlength="10"
                        x-on:keydown.tab="$event.preventDefault(); $refs.machineInput.focus();"
                    />
                </div>
                {{-- <input type="text" class="form-control" placeholder="000000-000" wire:model.defer="lpk_no"> --}}
            </div>
        </div>
        <div class="form-group mt-1">
            <div class="input-group">
                <span class="input-group-addon col-12 col-lg-3 text-muted fw-bold">Nomor Order </span>
                <input type="text" class="form-control" placeholder=".." wire:model.defer="noorder">
            </div>
        </div>
        <div class="form-group mt-1">
            <div class="input-group">
                <span class="input-group-addon col-12 col-lg-3 text-muted fw-bold">Departemen </span>
                <div class="col-12 col-lg-9" wire:ignore>
                    <select class="form-control" wire:model.defer="departmentId" data-choices data-choices-search-false
                        data-choices-removeItem data-choices-search-field-label>
                        <option value="">- All -</option>
                        @foreach ($department as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        {{-- Mesin --}}
        <div class="form-group mt-1">
            <div class="input-group">
                <span class="input-group-addon col-12 col-lg-3 text-muted fw-bold">Mesin </span>
                <div class="col-12 col-lg-9" wire:ignore>
                    <select class="form-control" wire:model.defer="machineId" data-choices data-choices-sorting-false
                        data-choices-removeItem data-choices-search-field-label>
                        <option value="">- All -</option>
                        @foreach ($machine as $item)
                            <option value="{{ $item->id }}">{{ $item->machineno }} - {{ $item->machinename }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        {{-- Produk --}}
        <div class="form-group mt-1">
            <div class="input-group">
                <span class="input-group-addon col-12 col-lg-3 text-muted fw-bold">Produk </span>
                <div class="col-12 col-lg-9" wire:ignore>
                    <select class="form-control" wire:model.defer="productId" data-choices data-choices-sorting-false
                        data-choices-removeItem data-choices-search-field-label>
                        <option value="">- All -</option>
                        @foreach ($products as $item)
                            <option data-custom-properties='{"code": "{{ $item->code }}"}' value="{{ $item->id }}">{{ $item->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="form-group mt-1">
            <div class="input-group">
                <span class="input-group-addon col-12 col-lg-3 text-muted fw-bold">Nomor Palet</span>
                <div x-data="{ nomorPalet: @entangle('nomorPalet').change, status: true }" x-init="$watch('nomorPalet', value => {
                    // Membuat karakter pertama kapital
                    nomorPalet = value.charAt(0).toUpperCase() + value.slice(1).replace(/[^0-9-]/g, '');
                    if (value.length === 5 && !value.includes('-') && status) {
                        nomorPalet = value + '-';
                    }
                    if (value.length < 5) {
                        status = true;
                    }
                    if (value.length === 6) {
                        status = false;
                    }
                    {{-- membatasi 12 karakter --}}
                    if (value.length == 11 && !value.includes('-') && status) {
                        nomorPalet = value.substring(0, 5) + '-' + value.substring(5, 11);
                    } else if (value.length > 12) {
                        nomorPalet = value.substring(0, 12);
                    }
                })">
                    <input type="text" class="form-control" x-model="nomorPalet" wire:model="nomorPalet"
                        maxlength="12" x-on:keydown.tab="$event.preventDefault(); $refs.lotnoInput.focus();"
                        placeholder="A0000-000000" />
                </div>
                {{-- <input type="text" class="form-control" placeholder="A0000-000000" wire:model.defer="nomorPalet" /> --}}
            </div>
        </div>
        <div class="form-group mt-1">
            <div class="input-group">
                <span class="input-group-addon col-12 col-lg-3 text-muted fw-bold">Nomor LOT</span>
                <input type="text" class="form-control" placeholder="---" wire:model.defer="nomorLot" />
            </div>
        </div>
        <hr />
        <button type="button" class="btn btn-success btn-print" wire:click="export" wire:loading.attr="disabled" style="width:99%">
            <span wire:loading.remove wire:target="export">
                <i class="ri-printer-line"></i> Generate Report
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
    <div class="col-lg-4"></div>
</div>
@script
    <script>
        $wire.on('printNippo', (datas) => {
            var printUrl = '{{ route('report-checklist-seitai') }}?tanggal=' + datas;
            window.open(printUrl, '_blank');
        });

        $wire.on('printSeitai', (datas) => {
            var printUrl = '{{ route('report-loss-seitai') }}?tanggal=' + datas;
            window.open(printUrl, '_blank');
        });
    </script>
@endscript
