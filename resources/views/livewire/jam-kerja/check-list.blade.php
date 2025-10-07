<div>
    <div class="row">
        <div class="col-lg-2"></div>
        <div class="col-lg-6">
            <div class="form-group">
                <div class="input-group">
                    <label class="control-label col-3 text-muted fw-bold">Tanggal Produksi</label>
                    <div class="col-12 col-lg-9">
                        <select class="form-select mb-0" wire:model.defer="transaksi">
                            <option value="1" selected>Produksi</option>
                            <option value="2">Proses</option>
                        </select>
                    </div>
                </div>
                <div class="col-12 mt-1">
                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon col-12 col-lg-3 text-muted fw-bold">Awal: </span>
                            <input wire:model.defer="tglAwal" type="text" class="form-control"
                                style="padding:0.44rem" data-provider="flatpickr" data-date-format="Y-m-d">
                            <span class="input-group-text py-0">
                                <i class="ri-calendar-event-fill fs-4"></i>
                            </span>

                            <div class="mb-1" wire:ignore>
                                <select class="form-control" wire:model.defer="jamAwal" data-choices
                                    data-choices-sorting-false data-choices-removeItem data-choices-search-field-label>
                                    <option value="">- All -</option>
                                    @foreach ($workingShiftHour as $item)
                                        <option value="{{ $item->work_hour_from }}"
                                            @if ($jamAwal == $item->work_hour_from) selected @endif>
                                            {{ $item->work_hour_from }}
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
                            <input wire:model.defer="tglAkhir" type="text" class="form-control"
                                style="padding:0.44rem" data-provider="flatpickr" data-date-format="Y-m-d">
                            <span class="input-group-text py-0">
                                <i class="ri-calendar-event-fill fs-4"></i>
                            </span>

                            <div class="mb-1" wire:ignore>
                                <select class="form-control" wire:model.defer="jamAkhir" data-choices
                                    data-choices-sorting-false data-choices-removeItem data-choices-search-field-label>
                                    <option value="">- All -</option>
                                    @foreach ($workingShiftHour as $item)
                                        <option value="{{ $item->work_hour_till }}"
                                            @if ($jamAkhir == $item->work_hour_till) selected @endif>
                                            {{ $item->work_hour_till }}
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
                <div class="row">
                    <div class="col-12 col-lg-3">
                        <label for="product" class="form-label text-muted fw-bold">Division</label>
                    </div>
                    <div class="col-12 col-lg-9">
                        <div class="mb-1">
                            <select class="form-control" wire:model.change="divisionId">
                                @foreach ($divisions as $item)
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
                        <label for="product" class="form-label text-muted fw-bold">Department</label>
                    </div>
                    <div class="col-12 col-lg-9">
                        <div class="mb-1">
                            <select class="form-control" wire:model.defer="departmentId" wire:loading.attr="disabled"
                                wire:loading.class="bg-light" wire:target="divisionId">
                                <option value="">-- All --</option>
                                @foreach ($departments as $item)
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
                        <div class="mb-1">
                            <select class="form-control select2-machine py-2 px-3" wire:model="machineId" wire:loading.attr="disabled"
                                wire:loading.class="bg-light" wire:target="divisionId">
                                <option value="">- All -</option>
                                @foreach ($machine as $item)
                                    <option value="{{ $item->id }}">{{ $item->machineno }} - {{ $item->machinename }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <hr />
            <button type="button" class="btn btn-success btn-print" wire:click="export" style="width:99%"
                wire:loading.attr="disabled">
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
</div>
@script
    <script type="text/javascript">
        document.addEventListener('livewire:initialized', function() {
            function selectMachine() {
                // Destroy instance Select2 yang ada
                if ($('.select2-machine').hasClass("select2-hidden-accessible")) {
                    $('.select2-machine').select2('destroy');
                }

                $('.select2-machine').select2().on('change', function(e) {
                    var data = $(this).val();
                    @this.set('machineId', data);
                });
            }

            selectMachine();

            // Hook untuk Livewire v3
            Livewire.hook('morph', ({
                el,
                component
            }) => {
                setTimeout(() => {
                    selectMachine();
                }, 100);
            });
        });
    </script>
@endscript
