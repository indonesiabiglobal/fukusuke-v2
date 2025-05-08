<div class="row mt-3">
    <div class="col-lg-2"></div>
    <div class="col-lg-6">
        <div class="form-group">
            <label class="control-label col-md-4 col-xs-12">Tanggal Kenpin</label>
            <div class="col-12 mt-1">
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-addon col-12 col-lg-3">Awal: </span>
                        <input wire:model.defer="tglAwal" type="text" class="form-control" style="padding:0.44rem"
                            data-provider="flatpickr" data-date-format="Y-m-d">
                        <span class="input-group-text py-0">
                            <i class="ri-calendar-event-fill fs-4"></i>
                        </span>

                        <div class="mb-1" wire:ignore>
                            <select class="form-control" wire:model.defer="jamAwal" data-choices
                                data-choices-sorting-false data-choices-removeItem data-choices-search-field-label>
                                <option value="">- All -</option>
                                @foreach ($workingShiftHour as $item)
                                    <option value="{{ $item->work_hour_from }}"
                                        @if ($jamAwal == $item->work_hour_from) selected @endif>{{ $item->work_hour_from }}
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
            <div class="col-12">
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-addon col-12 col-lg-3">Akhir: </span>
                        <input wire:model.defer="tglAkhir" type="text" class="form-control" style="padding:0.44rem"
                            data-provider="flatpickr" data-date-format="Y-m-d">
                        <span class="input-group-text py-0">
                            <i class="ri-calendar-event-fill fs-4"></i>
                        </span>

                        <div class="mb-1" wire:ignore>
                            <select class="form-control" wire:model.defer="jamAkhir" data-choices
                                data-choices-sorting-false data-choices-removeItem data-choices-search-field-label>
                                <option value="">- All -</option>
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
                <span class="input-group-addon col-12 col-lg-3">Departemen </span>
                <div class="col-12 col-lg-9" wire:ignore>
                    <select class="form-select" wire:model.live="nippo" data-choices data-choices-search-false>
                        @foreach ($department as $item)
                            <option value="{{ $item->name }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="form-group mt-1">
            <div class="input-group">
                <span class="input-group-addon col-12 col-lg-3">Nomor LPK </span>
                <input type="text" class="form-control" placeholder="000000-000" wire:model.defer="lpk_no">
            </div>
        </div>
        <div class="form-group mt-1">
            <div class="input-group">
                <span class="input-group-addon col-12 col-lg-3">Nomor Order</span>
                <div class="col-12 col-lg-9" wire:ignore>
                    <select class="form-control" wire:model.defer="productId" data-choices data-choices-sorting-false
                        data-choices-removeItem data-choices-search-field-label>
                        <option value="">- All -</option>
                        @foreach ($product as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="form-group mt-1">
            <div class="input-group">
                <span class="input-group-addon col-12 col-lg-3">Nomor Kenpin </span>
                <input type="text" class="form-control" placeholder="_____-_____" wire:model.defer="nomorKenpin">
            </div>
        </div>
        {{-- Nomor Han --}}
        @if ($nippo == 'INFURE')
            <div class="form-group mt-1">
                <div class="input-group">
                    <span class="input-group-addon col-12 col-lg-3">Nomor Han</span>
                    <input type="text" class="form-control" placeholder="00-00-00A" wire:model.defer="nomorHan" />
                </div>
            </div>
        @elseif ($nippo == 'SEITAI')
            {{-- nomor Palet --}}
            <div class="form-group mt-1">
                <div class="input-group">
                    <span class="input-group-addon col-12 col-lg-3">Nomor Palet</span>
                    <input type="text" class="form-control" placeholder="A0000-000000"
                        wire:model.defer="nomorPalet" />
                </div>
            </div>
            {{-- nomor LOT --}}
            <div class="form-group mt-1">
                <div class="input-group">
                    <span class="input-group-addon col-12 col-lg-3">Nomor LOT</span>
                    <input type="text" class="form-control" placeholder="---" wire:model.defer="nomorLot" />
                </div>
            </div>
        @endif
        <div class="form-group mt-1">
            <div class="input-group">
                <span class="input-group-addon col-12 col-lg-3">Status</span>
                <select class="form-control" wire:model.defer="status">
                    <option value="">- all -</option>
                    <option value="proses">Proses</option>
                    <option value="finish">Finish</option>
                </select>
            </div>
        </div>
        <hr />
        <button type="button" class="btn btn-success btn-print" wire:click="export" style="width:99%">
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
