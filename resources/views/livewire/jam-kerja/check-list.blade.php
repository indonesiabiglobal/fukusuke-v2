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
                    <div class="mb-1" wire:ignore>
                        <select class="form-control machine-select" id="machineSelect" wire:model.defer="machineId" data-choices
                            data-choices-sorting-false data-choices-removeItem data-choices-search-field-label>
                            <option value="">- All -</option>
                            @foreach ($machine as $item)
                                <option value="{{ $item->id }}">{{ $item->machineno }} - {{ $item->machinename }}
                                </option>
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

@script
    <script>
        // Function to initialize Choices.js for machine select
        function initMachineSelect() {
            const machineSelect = document.getElementById('machineSelect');
            
            if (machineSelect) {
                // Destroy existing Choices instance if it exists
                if (machineSelect.choicesInstance) {
                    machineSelect.choicesInstance.destroy();
                }
                
                // Initialize new Choices instance
                const choices = new Choices(machineSelect, {
                    searchEnabled: true,
                    removeItemButton: true,
                    shouldSort: false,
                    searchFields: ['label']
                });
                
                // Store the instance for later destruction
                machineSelect.choicesInstance = choices;
                
                // Listen for changes and update Livewire property
                machineSelect.addEventListener('change', function(e) {
                    @this.set('machineId', e.target.value);
                });
            }
        }
        
        // Initialize on component load
        document.addEventListener('livewire:init', () => {
            initMachineSelect();
        });
        
        // Reinitialize after Livewire updates the DOM
        Livewire.hook('morph', ({ component, cleanup }) => {
            cleanup(() => {
                // Reinitialize after the morph is complete
                setTimeout(() => {
                    initMachineSelect();
                }, 100);
            });
        });
    </script>
@endscript
