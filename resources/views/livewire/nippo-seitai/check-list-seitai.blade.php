<div class="row">
    <div class="col-lg-2"></div>
    <div class="col-lg-6">
        <div class="form-group">
            <div class="input-group">
                <label class="control-label col-4 text-muted fw-bold">Tanggal Produksi</label>
                <div class="col-12 col-lg-8">
                    <select class="form-select mb-0" wire:model.defer="transaksi">
                        <option value="1">Produksi</option>
                        <option value="2">Order</option>
                    </select>
                </div>
            </div>
            <div class="col-12 mt-1">
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-addon col-12 col-lg-2 text-muted fw-bold">Awal: </span>
                        <input wire:model.defer="tglMasuk" type="text" class="form-control" style="padding:0.44rem"
                            data-provider="flatpickr" data-date-format="d/m/Y">
                        <span class="input-group-text py-0">
                            <i class="ri-calendar-event-fill fs-4"></i>
                        </span>

                        <input wire:model.defer="jamMasuk" type="text" class="form-control" data-provider="timepickr"
                            data-time-hrs="true" id="timepicker-24hrs">
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
                        <span class="input-group-addon col-12 col-lg-2 text-muted fw-bold">Akhir: </span>
                        <input wire:model.defer="tglKeluar" type="text" class="form-control" style="padding:0.44rem"
                            data-provider="flatpickr" data-date-format="d/m/Y">
                        <span class="input-group-text py-0">
                            <i class="ri-calendar-event-fill fs-4"></i>
                        </span>

                        <input wire:model.defer="jamKeluar" type="text" class="form-control"
                            data-provider="timepickr" data-time-hrs="true" id="timepicker-24hrs">
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
                <select id="department" class="form-control" placeholder="- pilih jenis report -">
                    <option value="1">Check List</option>
                    <option value="2">Loss Seitai</option>
                </select>
            </div>
        </div>
        <div class="form-group mt-1">
            <div class="input-group">
                <span class="input-group-addon col-12 col-lg-3 text-muted fw-bold">Nomor Proses </span>
                <input type="text" class="form-control" placeholder="1" wire:model.defer="noprosesawal">
                <span class="input-group-text readonly" readonly="readonly">
                    ~
                </span>
                <input type="text" class="form-control" placeholder="1000" wire:model.defer="noprosesakhir">
            </div>
        </div>
        <div class="form-group mt-1">
            <div class="input-group">
                <span class="input-group-addon col-12 col-lg-3 text-muted fw-bold">Nomor LPK </span>
                <input type="text" class="form-control" placeholder="000000-000" wire:model.defer="lpk_no">
            </div>
        </div>
        <div class="form-group mt-1">
            <div class="input-group">
                <span class="input-group-addon col-12 col-lg-3 text-muted fw-bold">Nomor Order </span>
                <input type="text" class="form-control" placeholder=".." wire:model.defer="code">
            </div>
        </div>
        <div class="form-group mt-1">
            <div class="row">
                <div class="col-12 col-lg-3">
                    <label for="product" class="form-label text-muted fw-bold">Departemen</label>
                </div>
                <div class="col-12 col-lg-9">
                    <div class="mb-1" wire:ignore>
                        <select class="form-control" wire:model.defer="departemenId" data-choices
                            data-choices-sorting-false data-choices-removeItem>
                            <option value="">- All -</option>
                            @foreach ($department as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group mt-1">
            <div class="row">
                <div class="col-12 col-lg-3">
                    <label for="product" class="form-label text-muted fw-bold">Mesin</label>
                </div>
                <div class="col-12 col-lg-9">
                    <div class="mb-1" wire:ignore>
                        <select class="form-control" wire:model.defer="machineId" data-choices
                            data-choices-sorting-false data-choices-removeItem>
                            <option value="">- All -</option>
                            @foreach ($machine as $item)
                                <option value="{{ $item->id }}">{{ $item->machinename }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group mt-1">
            <div class="input-group">
                <span class="input-group-addon col-12 col-lg-3 text-muted fw-bold">Nomor Palet</span>
                <input type="text" class="form-control" placeholder="A0000-000000"
                    wire:model.defer="nomor_palet" />
            </div>
        </div>
        <div class="form-group mt-1">
            <div class="input-group">
                <span class="input-group-addon col-12 col-lg-3 text-muted fw-bold">Nomor LOT</span>
                <input type="text" class="form-control" placeholder="---" wire:model.defer="nomor_lot" />
            </div>
        </div>
        <hr />
        <div class="form-group">
            <div class="input-group">
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
        </div>
    </div>
    <div class="col-lg-4"></div>
</div>
