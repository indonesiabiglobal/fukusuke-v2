<div>
    <div class="row filter-section">
        <div class="col-12 col-lg-7">
            <div class="row">
                <div class="col-12 col-lg-3">
                    <label class="form-label text-muted fw-bold">Filter Tanggal</label>
                </div>
                <div class="col-12 col-lg-9 mb-1">
                    <div class="form-group">
                        <div class="input-group">
                            <div class="col-3">
                                <select class="form-select" style="padding:0.44rem" wire:model.defer="transaksi">
                                    <option value="1">Produksi</option>
                                    <option value="2">Proses</option>
                                </select>
                            </div>
                            <div class="col-9">
                                <div class="form-group">
                                    <div class="input-group">
                                        <input wire:model.defer="tglMasuk" type="text" class="form-control"
                                            style="padding:0.44rem" data-provider="flatpickr" data-date-format="d-m-Y">
                                        <span class="input-group-text py-0">
                                            <i class="ri-calendar-event-fill fs-4"></i>
                                        </span>

                                        <input wire:model.defer="tglKeluar" type="text" class="form-control"
                                            style="padding:0.44rem" data-provider="flatpickr" data-date-format="d-m-Y">
                                        <span class="input-group-text py-0">
                                            <i class="ri-calendar-event-fill fs-4"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-3">
                    <label class="form-label text-muted fw-bold">Search</label>
                </div>
                <div class="col-12 col-lg-9">
                    <div class="input-group">
                        <input wire:model.defer="searchTerm" class="form-control"style="padding:0.44rem" type="text"
                            placeholder="search kode atau nama" />
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-5">
            <div class="row">
                <div class="col-12 col-lg-2">
                    <label class="form-label text-muted fw-bold">Mesin</label>
                </div>
                <div class="col-12 col-lg-10">
                    <div class="mb-1" wire:ignore>
                        <select class="form-control" wire:model.defer="machine_id" data-choices
                            data-choices-sorting-false data-choices-removeItem data-choices-search-field-label>
                            <option value="">- All -</option>
                            @foreach ($machine as $item)
                                <option value="{{ $item->id }}" @if ($item->id == ($machine_id['value'] ?? null)) selected @endif>
                                    {{ $item->machineno }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-12 col-lg-2">
                    <label class="form-label text-muted fw-bold">Shift</label>
                </div>
                <div class="col-12 col-lg-10">
                    <div class="mb-1" wire:ignore>
                        <select class="form-control" wire:model.defer="work_shift_filter" data-choices
                            data-choices-sorting-false data-choices-removeItem data-choices-search-field-label>
                            <option value="">- All -</option>
                            @foreach ($workShift as $item)
                                <option value="{{ $item->id }}" @if ($item->id == ($work_shift_filter['value'] ?? null)) selected @endif>
                                    Shift-{{ $item->work_shift }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-10 mt-2">
            <div class="row">
                <div class="col-12 col-lg-6">
                    <button wire:click="search" type="button" class="btn btn-primary btn-load w-lg p-1"
                        wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="search">
                            <i class="ri-search-line"></i> Filter
                        </span>
                        <div wire:loading wire:target="search">
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

                    <button type="button" class="btn btn-success w-lg p-1" wire:click="showModalCreate">
                        <i class="ri-add-line"> </i> Add
                    </button>

                    {{-- Modal ADD jam kerja --}}
                    <div class="modal fade" id="modal-add" data-bs-backdrop="static" data-bs-keyboard="false"
                        tabindex="-1" role="dialog" aria-labelledby="modal-add" aria-hidden="true" wire:ignore.self>
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h2 class="h6 modal-title">Add Jam Kerja Seitai</h2>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                                        wire:click="closeModal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-lg-12 mb-1">
                                            <label for="">Tanggal</label>
                                            <div class="form-group" style="margin-left:1px; white-space:nowrap">
                                                <div class="input-group">
                                                    <input class="form-control" style="padding:0.44rem"
                                                        data-provider="flatpickr" data-date-format="d-m-Y"
                                                        type="text" wire:model.defer="working_date"
                                                        placeholder="yyyy/mm/dd" />
                                                    <span class="input-group-text py-0">
                                                        <i class="ri-calendar-event-fill fs-4"></i>
                                                    </span>
                                                    @error('working_date')
                                                        <span class="invalid-feedback">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-12 mb-1">
                                            <div class="form-group">
                                                <label>Shift </label>
                                                <div class="input-group col-md-9 col-xs-8">
                                                    <input class="form-control" type="text" id="work_shift"
                                                        wire:model.defer="work_shift" placeholder="..."
                                                        maxlength="1" />
                                                    @error('work_shift')
                                                        <span class="invalid-feedback">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-12 mb-1">
                                            <div class="form-group">
                                                <label>Nomor Mesin </label>
                                                <div class="input-group">
                                                    <input class="form-control" type="text" max="6"
                                                        x-on:keydown.tab="$event.preventDefault(); $refs.employeenoInput.focus();"
                                                        wire:model.change="machineno" placeholder="..." />
                                                    <input class="form-control readonly bg-light" readonly="readonly"
                                                        type="text" wire:model="machinename" placeholder="..." />
                                                    @error('machineno')
                                                        <span class="invalid-feedback">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-12 mb-1">
                                            <div class="form-group">
                                                <label>Petugas </label>
                                                <div class="input-group col-md-9 col-xs-8">
                                                    <input class="form-control" wire:model.change="employeeno"
                                                        max="8"
                                                        x-on:keydown.tab="$event.preventDefault(); $refs.workHourInput.focus();"
                                                        x-ref="employeenoInput" type="text" placeholder="..." />
                                                    <input class="form-control readonly bg-light" readonly="readonly"
                                                        type="text" wire:model="empname" placeholder="..." />
                                                    @error('employeeno')
                                                        <span class="invalid-feedback">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-12 mb-1">
                                            <label for="">Jam Kerja</label>
                                            <div class="form-group" style="margin-left:1px; white-space:nowrap">
                                                <input class="form-control" wire:model.lazy="work_hour"
                                                    type="time"
                                                    x-on:keydown.tab="$event.preventDefault(); $refs.showModalJamMatiMesin.focus();"
                                                    x-ref="workHourInput" placeholder="hh:mm"
                                                    wire:lazy="validateWorkHour" max="08:00">
                                                @error('work_hour')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-lg-12 mb-1">
                                            <label for="">Lama Mesin Mati</label>
                                            <div class="form-group" style="margin-left:1px; white-space:nowrap">
                                                <input class="form-control bg-light readonly"
                                                    wire:model="totalOffHour" wire:model="validateWorkHour"
                                                    type="time" placeholder="hh:mm" readonly="readonly">
                                                @error('totalOffHour')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-lg-12 mb-1">
                                            <label for="">Jam Mati Mesin</label>
                                            <div class="border-0 shadow">
                                                <div class="card-body">
                                                    <div class="table-responsive">
                                                        <table class="table table-centered table-nowrap mb-0 rounded">
                                                            <thead class="table-light">
                                                                <tr>
                                                                    <th class="border-0 rounded-start">Action</th>
                                                                    <th class="border-0">Kode</th>
                                                                    <th class="border-0">Nama</th>
                                                                    <th class="border-0">Jam Mati Mesin</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @forelse ($dataJamMatiMesin as $index => $item)
                                                                    <tr
                                                                        wire:key="jam-mati-{{ $item['id'] ?? $index }}">
                                                                        <td>
                                                                            <button type="button"
                                                                                class="btn btn-danger"
                                                                                wire:click="deleteJamMatiMesin({{ $item['id'] }})"
                                                                                wire:loading.attr="disabled">
                                                                                <span wire:loading.remove
                                                                                    wire:target="deleteJamMatiMesin({{ $item['id'] }})">
                                                                                    <i class="fa fa-trash"></i> Delete
                                                                                </span>
                                                                                <div wire:loading
                                                                                    wire:target="deleteJamMatiMesin({{ $item['id'] }})">
                                                                                    <span
                                                                                        class="d-flex align-items-center">
                                                                                        <span
                                                                                            class="spinner-border flex-shrink-0"
                                                                                            role="status">
                                                                                            <span
                                                                                                class="visually-hidden">Loading...</span>
                                                                                        </span>
                                                                                        <span class="flex-grow-1 ms-1">
                                                                                            Loading...
                                                                                        </span>
                                                                                    </span>
                                                                                </div>
                                                                            </button>
                                                                        </td>
                                                                        <td>{{ $item['code'] }}</td>
                                                                        <td>{{ $item['name'] ?? '' }}</td>
                                                                        <td>{{ $item['off_hour'] ?? '00:00' }}</td>
                                                                    </tr>
                                                                @empty
                                                                    <tr>
                                                                        <td colspan="6" class="text-center">
                                                                            No results found</td>
                                                                    </tr>
                                                                @endforelse
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div
                                    class="modal-footer d-flex justify-content-between align-items-center flex-wrap gap-2">
                                    <!-- Left side button -->
                                    <button type="button" class="btn btn-outline-success"
                                        wire:click="showModalJamMatiMesin" x-ref="showModalJamMatiMesin">
                                        <i class="ri-add-line me-1"></i> Add Jam Mati
                                    </button>

                                    <!-- Right side actions -->
                                    <div class="d-flex gap-2 align-items-center">
                                        <button type="button" class="btn btn-success d-flex align-items-center"
                                            wire:click="save" wire:loading.attr="disabled" x-ref="save">
                                            <span wire:loading.remove wire:target="save">
                                                <i class="ri-save-3-line"></i> Save
                                            </span>
                                            <div wire:loading wire:target="save">
                                                <span class="d-flex align-items-center">
                                                    <span class="spinner-border spinner-border-sm me-1" role="status"
                                                        aria-hidden="true"></span>
                                                    Saving...
                                                </span>
                                            </div>
                                        </button>
                                        <!-- Close Button -->
                                        <button type="button" class="btn btn-outline-secondary"
                                            data-bs-dismiss="modal" wire:click="closeModal">
                                            Close
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal fade" id="modal-edit" data-bs-backdrop="static" data-bs-keyboard="false"
                        tabindex="-1" role="dialog" aria-labelledby="modal-edit" aria-hidden="true"
                        wire:ignore.self>
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h2 class="h6 modal-title">Edit Jam Kerja Seitai</h2>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close" wire:click="closeModal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-lg-12 mb-1">
                                            <label for="">Tanggal</label>
                                            <div class="form-group" style="margin-left:1px; white-space:nowrap">
                                                <div class="input-group">
                                                    <input class="form-control datepicker-input" type="date"
                                                        wire:model.defer="working_date" placeholder="yyyy/mm/dd" />
                                                    @error('working_date')
                                                        <span class="invalid-feedback">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-12 mb-1">
                                            <div class="form-group">
                                                <label>Shift </label>
                                                <div class="input-group col-md-9 col-xs-8">
                                                    <input class="form-control" type="text"
                                                        wire:model.defer="work_shift" placeholder="..."
                                                        maxlength="1" />
                                                    @error('work_shift')
                                                        <span class="invalid-feedback">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-12 mb-1">
                                            <div class="form-group">
                                                <label>Nomor Mesin </label>
                                                <div class="input-group">
                                                    <input class="form-control" type="text" max="6"
                                                        x-on:keydown.tab="$event.preventDefault(); $refs.employeenoEditInput.focus();"
                                                        wire:model.change="machineno" placeholder="..." />
                                                    <input class="form-control readonly" readonly="readonly"
                                                        type="text" wire:model="machinename" placeholder="..." />
                                                    @error('machineno')
                                                        <span class="invalid-feedback">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-12 mb-1">
                                            <div class="form-group">
                                                <label>Petugas </label>
                                                <div class="input-group col-md-9 col-xs-8">
                                                    <input class="form-control" wire:model.change="employeeno"
                                                        max="8"
                                                        x-on:keydown.tab="$event.preventDefault(); $refs.workHourEditInput.focus();"
                                                        x-ref="employeenoEditInput" type="text"
                                                        placeholder="..." />
                                                    <input class="form-control readonly" readonly="readonly"
                                                        type="text" wire:model="empname" placeholder="..." />
                                                    @error('employeeno')
                                                        <span class="invalid-feedback">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-12 mb-1">
                                            <label for="">Jam Kerja</label>
                                            <div class="form-group" style="margin-left:1px; white-space:nowrap">
                                                <input class="form-control" wire:model="work_hour" type="time"
                                                    x-on:keydown.tab="$event.preventDefault(); $refs.offHourEditInput.focus();"
                                                    x-ref="workHourEditInput" placeholder="hh:mm"
                                                    wire:change="validateWorkHour" max="08:00">
                                                @error('work_hour')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-lg-12 mb-1">
                                            <label for="">Lama Mesin Mati</label>
                                            <div class="form-group" style="margin-left:1px; white-space:nowrap">
                                                <input class="form-control readonly bg-light"
                                                    wire:model="totalOffHour" wire:change="validateWorkHour"
                                                    type="time" readonly="readonly" x-ref="offHourEditInput"
                                                    placeholder="hh:mm">
                                            </div>
                                        </div>
                                        <div class="col-lg-12 mb-1">
                                            <label for="">Jam Mati Mesin</label>
                                            <div class="border-0 shadow">
                                                <div class="card-body">
                                                    <div class="table-responsive">
                                                        <table class="table table-centered table-nowrap mb-0 rounded">
                                                            <thead class="table-light">
                                                                <tr>
                                                                    <th class="border-0 rounded-start">Action</th>
                                                                    <th class="border-0">Kode</th>
                                                                    <th class="border-0">Nama</th>
                                                                    <th class="border-0">Jam Mati Mesin</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @forelse ($dataJamMatiMesin as $index => $item)
                                                                    <tr
                                                                        wire:key="jam-mati-{{ $item['id'] ?? $index }}">
                                                                        <td>
                                                                            <button type="button"
                                                                                class="btn btn-danger"
                                                                                wire:click="deleteJamMatiMesin({{ $item['id'] }})"
                                                                                wire:loading.attr="disabled">
                                                                                <span wire:loading.remove
                                                                                    wire:target="deleteJamMatiMesin({{ $item['id'] }})">
                                                                                    <i class="fa fa-trash"></i> Delete
                                                                                </span>
                                                                                <div wire:loading
                                                                                    wire:target="deleteJamMatiMesin({{ $item['id'] }})">
                                                                                    <span
                                                                                        class="d-flex align-items-center">
                                                                                        <span
                                                                                            class="spinner-border flex-shrink-0"
                                                                                            role="status">
                                                                                            <span
                                                                                                class="visually-hidden">Loading...</span>
                                                                                        </span>
                                                                                        <span class="flex-grow-1 ms-1">
                                                                                            Loading...
                                                                                        </span>
                                                                                    </span>
                                                                                </div>
                                                                            </button>
                                                                        </td>
                                                                        <td>{{ $item['code'] }}</td>
                                                                        <td>{{ $item['name'] ?? '' }}</td>
                                                                        <td>{{ $item['off_hour'] ?? '00:00' }}</td>
                                                                    </tr>
                                                                @empty
                                                                    <tr>
                                                                        <td colspan="6" class="text-center">
                                                                            No results found</td>
                                                                    </tr>
                                                                @endforelse
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div
                                    class="modal-footer d-flex justify-content-between align-items-center flex-wrap gap-2">
                                    <!-- Left side button -->
                                    <button type="button" class="btn btn-outline-success"
                                        wire:click="showModalJamMatiMesin" x-ref="showModalJamMatiMesin">
                                        <i class="ri-add-line me-1"></i> Add Jam Mati
                                    </button>
                                    <div class="d-flex gap-2 align-items-center">
                                        <button type="button" class="btn btn-success d-flex align-items-center"
                                            wire:click="save" wire:loading.attr="disabled" x-ref="saveEdit">
                                            <span wire:loading.remove wire:target="save">
                                                <i class="ri-save-3-line"></i> Save
                                            </span>
                                            <div wire:loading wire:target="save">
                                                <span class="d-flex align-items-center">
                                                    <span class="spinner-border flex-shrink-0" role="status">
                                                        <span class="visually-hidden">Saving...</span>
                                                    </span>
                                                    <span class="flex-grow-1 ms-1">
                                                        Saving...
                                                    </span>
                                                </span>
                                            </div>
                                        </button>
                                        <!-- Close Button -->
                                        <button type="button" class="btn btn-outline-secondary"
                                            data-bs-dismiss="modal" wire:click="closeModal">
                                            Close
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Modal Jam Mati --}}
                    <div class="modal fade modal-backdrop-custom" id="modal-jam-mati" tabindex="-1" role="dialog"
                        aria-labelledby="modal-jam-mati" aria-hidden="true" wire:ignore.self>
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h2 class="h6 modal-title">Add Jam Mati Mesin</h2>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        {{-- Kode Jam Mati Mesin --}}
                                        <div class="col-lg-12 mb-1">
                                            <div class="form-group">
                                                <label>Kode Jam Mati Mesin</label>
                                                <div class="input-group col-md-9 col-xs-8">
                                                    <input class="form-control" wire:model.change="jamMatiMesinCode"
                                                        x-ref="jamMatiMesinCodeInput" type="text"
                                                        placeholder="..."  id="jamMatiMesinCode"
                                                        oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                                        maxlength="3" autofocus
                                                        x-on:keydown.tab="$event.preventDefault(); $refs.offHourInput.focus();" />
                                                    @error('jamMatiMesinCode')
                                                        <span class="invalid-feedback">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        {{-- Keterangan Jam Mati --}}
                                        <div class="col-lg-12 mb-1">
                                            <label for="">Keterangan Jam Mati</label>
                                            <div class="form-group" style="margin-left:1px; white-space:nowrap">
                                                <input class="form-control readonly bg-light" readonly="readonly"
                                                    type="text" wire:model="jamMatiMesinName" placeholder="..." />
                                            </div>
                                        </div>
                                        {{-- Lama Mesin Mati --}}
                                        <div class="col-lg-12 mb-1">
                                            <label for="">Lama Mesin Mati</label>
                                            <div class="form-group" style="margin-left:1px; white-space:nowrap">
                                                <input class="form-control" wire:model.lazy="off_hour" type="time"
                                                    x-ref="offHourInput" max="08:00"
                                                    x-on:keydown.tab="$event.preventDefault(); $refs.addJamMatiMesin.focus();"
                                                    placeholder="hh:mm">
                                                @error('off_hour')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-success" wire:click="addJamMatiMesin"
                                        wire:loading.attr="disabled" x-ref="addJamMatiMesin">
                                        <span wire:loading.remove wire:target="addJamMatiMesin">
                                            <i class="ri-save-3-line"></i> Save
                                        </span>
                                        <div wire:loading wire:target="addJamMatiMesin">
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
                    {{-- delete --}}
                    <div id="removModal" class="modal fade zoomIn" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close" id="close-removModal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mt-2 text-center">
                                        <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop"
                                            colors="primary:#f7b84b,secondary:#f06548"
                                            style="width:100px;height:100px"></lord-icon>
                                        <div class="mt-4 pt-2 fs-15 mx-4 mx-sm-5">
                                            <h4>Apakah Anda yakin ingin menghapus data ini?</h4>
                                            <p class="text-muted mx-4 mb-0">Data
                                                yang dihapus tidak dapat dikembalikan.
                                            </p>
                                        </div>
                                    </div>
                                    <div class="d-flex gap-2 justify-content-center mt-4 mb-2">
                                        <button type="button" class="btn w-sm btn-light"
                                            data-bs-dismiss="modal">Tutup</button>
                                        <button wire:click="destroy" id="btnCreate" type="button"
                                            class="btn w-sm btn-danger" id="remove-item"
                                            wire:loading.attr="disabled">
                                            <span wire:loading.remove wire:target="destroy">
                                                <i class="ri-save-3-line"></i> Ya, Hapus!
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
                    {{-- end modal delete --}}
                </div>
            </div>
        </div>
        <div class="col-lg-2 mt-2 text-end">
            <button class="btn btn-info w-lg p-1" wire:click="export" type="button" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="export">
                    <i class="ri-printer-line"> </i> Print
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

    <div class="table-responsive table-card  mt-2  mb-2">
        {{-- toggle column table --}}
        <div class="col text-end dropdown">
            <button type="button" data-bs-toggle="dropdown" aria-expanded="false"
                class="btn btn-soft-primary btn-icon fs-14 mt-2">
                <i class="ri-grid-fill"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="1"
                            checked> Tanggal
                    </label>
                </li>
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="2"
                            checked> Shift
                    </label>
                </li>
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="3"
                            checked> Nomor Mesin
                    </label>
                </li>
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="4"
                            checked> NIK
                    </label>
                </li>
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="5"
                            checked> Petugas
                    </label>
                </li>
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="6"
                            checked> Jam Kerja
                    </label>
                </li>
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="7"
                            checked> Jam Mati
                    </label>
                </li>
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="8"
                            checked> Jam Jalan
                    </label>
                </li>
                {{-- <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="9"
                            checked> Jam Mati Mesin
                    </label>
                </li> --}}
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="10"
                            checked> Update By
                    </label>
                </li>
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="11"
                            checked> Updated
                    </label>
                </li>
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="12"
                            checked> Created
                    </label>
                </li>
            </ul>
        </div>
        <table class="table align-middle" id="seitaiTable">
            <thead class="table-light">
                <tr>
                    <th>Action</th>
                    <th>Tanggal</th>
                    <th>Shift</th>
                    <th>Nomor Mesin</th>
                    <th>NIK</th>
                    <th>Petugas</th>
                    <th>Jam Kerja</th>
                    <th>Jam Mati</th>
                    <th>Jam Jalan</th>
                    {{-- <th>Jam Mati Mesin</th> --}}
                    <th>Update By</th>
                    <th>Updated</th>
                    <th>Created</th>
                </tr>
            </thead>
            <tbody class="list form-check-all">
                @forelse ($data as $item)
                    <tr>
                        <td>
                            <button type="button" class="btn fs-15 p-1 bg-primary rounded btn-edit"
                                data-edit-id="{{ $item->id }}" wire:click="edit({{ $item->id }})">
                                <i class="ri-edit-box-line text-white"></i>
                            </button>
                            <button type="button" class="btn fs-15 p-1 bg-danger rounded btn-delete"
                                data-delete-id="{{ $item->id }}" wire:click="delete({{ $item->id }})">
                                <i class="ri-delete-bin-line text-white"></i>
                            </button>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($item->working_date)->format('d M Y') }}</td>
                        <td>{{ $item->work_shift }}</td>
                        <td>{{ $item->machineno }}</td>
                        <td> {{ $item->employeeno }}</td>
                        <td> {{ $item->empname }}</td>
                        <td>{{ $item->work_hour }}</td>
                        <td>{{ $item->off_hour }}</td>
                        <td>{{ $item->on_hour }}</td>
                        <td>{{ $item->updated_by }}</td>
                        <td>{{ \Carbon\Carbon::parse($item->updated_on)->format('d M Y H:i:s') }}</td>
                        <td>{{ \Carbon\Carbon::parse($item->created_on)->format('d M Y H:i:s') }}</td>
                    </tr>
                @empty
                @endforelse
            </tbody>
        </table>
    </div>

    <style>
        .modal-overlay-top {
            z-index: 1060;
            /* default Bootstrap modal z-index is 1055 */
        }

        .modal-backdrop-custom {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background-color: rgba(0, 0, 0, 0.5);
            /* gelap transparan */
            z-index: 1058;
            /* di bawah modal-jam-mati, tapi di atas modal-jam-kerja */
        }

        #seitaiTable.table>:not(caption)>*>* {
            font-size: 13px !important;
            padding: 4px 2px 4px 4px;
            color: var(--tb-table-color-state, var(--tb-table-color-type, var(--tb-table-color)));
            background-color: var(--tb-table-bg);
            border-bottom-width: var(--tb-border-width);
            box-shadow: inset 0 0 0 9999px var(--tb-table-bg-state, var(--tb-table-bg-type, var(--tb-table-accent-bg)));
        }
    </style>
</div>

@script
    <script>
        const setupAddModalFocus = () => {
            const modalEl = document.getElementById('modal-add');
            if (!modalEl) return;

            modalEl.addEventListener('shown.bs.modal', () => {
                setTimeout(() => document.getElementById('work_shift')?.focus(), 0);
            });
        };

        // Jalankan segera; kalau DOM belum siap, tunda sampai siap
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', setupAddModalFocus, {
                once: true
            });
        } else {
            setupAddModalFocus();
        }

        // Buka modal dari Livewire
        $wire.on('showModalCreate', () => {
            const modalEl = document.getElementById('modal-add');
            const modal = bootstrap.Modal.getOrCreateInstance(modalEl, {
                backdrop: 'static',
                keyboard: false,
            });
            modal.show();
        });
        // Close modal create
        $wire.on('closeModalCreate', () => {
            $('#modal-add').modal('hide');
        });
        // Show modal update
        $wire.on('showModalUpdate', () => {
            $('#modal-edit').modal('show');
        });
        // Close modal update
        $wire.on('closeModalUpdate', () => {
            $('#modal-edit').modal('hide');
        });

        // Show modal jam mati mesin
        const setupJamMatiModalFocus = () => {
            const modalEl = document.getElementById('modal-jam-mati');
            if (!modalEl) return;

            modalEl.addEventListener('shown.bs.modal', () => {
                setTimeout(() => document.getElementById('jamMatiMesinCode')?.focus(), 0);
            });
        };

        // Jalankan segera; kalau DOM belum siap, tunda sampai siap
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', setupJamMatiModalFocus, {
                once: true
            });
        } else {
            setupJamMatiModalFocus();
        }
        $wire.on('showModalJamMatiMesin', () => {
            const modalEl = document.getElementById('modal-jam-mati');
            const modal = bootstrap.Modal.getOrCreateInstance(modalEl, {
                backdrop: 'static',
                keyboard: false,
            });
            modal.show();
        });
        // Close modal update
        $wire.on('closeModalJamMatiMesin', () => {
            $('#modal-jam-mati').modal('hide');
        });

        // Show modal delete
        $wire.on('showModalDelete', () => {
            $('#removModal').modal('show');
        });

        // Close modal delete
        $wire.on('closeModalDelete', () => {
            $('#removModal').modal('hide');
        });

        // datatable
        // inisialisasi DataTable
        $wire.on('initDataTable', () => {
            initDataTable();
        });

        function calculateTableHeight() {
            const totalHeight = window.innerHeight;

            const filterSectionTop = document.querySelector('.filter-section')?.getBoundingClientRect().top || 0;
            const offsetTop = document.querySelector('#seitaiTable')?.getBoundingClientRect().top || 0;

            const paddingTop = document.querySelector('.navbar-header')?.getBoundingClientRect().top || 0;
            const availableHeight = totalHeight - offsetTop - filterSectionTop - paddingTop;

            return availableHeight;
        }

        // Fungsi untuk menginisialisasi ulang DataTable
        function initDataTable() {
            const savedOrder = $wire.get('sortingTable');

            let defaultOrder = [
                [1, "asc"]
            ];
            if (savedOrder) {
                defaultOrder = savedOrder;
            }
            // Hapus DataTable jika sudah ada
            if ($.fn.dataTable.isDataTable('#seitaiTable')) {
                let table = $('#seitaiTable').DataTable();
                table.clear(); // Bersihkan data tabel
                table.destroy(); // Hancurkan DataTable
            }

            setTimeout(() => {
                // Inisialisasi ulang DataTable
                let table = $('#seitaiTable').DataTable({
                    "pageLength": 10,
                    "searching": true,
                    "responsive": true,
                    "scrollX": true,
                    "order": defaultOrder,
                    "scrollY": calculateTableHeight() + 'px',
                    "scrollCollapse": true,
                    "scrollX": true,
                    "language": {
                        "emptyTable": `
                            <div class="text-center">
                                <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop"
                                    colors="primary:#121331,secondary:#08a88a" style="width:40px;height:40px"></lord-icon>
                                <h5 class="mt-2">Sorry! No Result Found</h5>
                            </div>
                        `
                    }
                });
                // tombol edit
                $('.btn-edit').on('click', function() {
                    let id = $(this).attr('data-edit-id');

                    // livewire click
                    $wire.dispatch('edit', {
                        id
                    });
                });
                // tombol delete
                $('.btn-delete').on('click', function() {
                    let id = $(this).attr('data-delete-id');

                    // livewire click
                    $wire.dispatch('delete', {
                        id
                    });
                });
                // Listen to sort event
                table.on('order.dt', function() {
                    let order = table.order();
                    if (order.length == 0 && defaultOrder.length > 0) {
                        order = defaultOrder;
                    }
                    $wire.call('updateSortingTable', order);
                });

                // default column visibility
                $('.toggle-column').each(function() {
                    let column = table.column($(this).attr('data-column'));
                    column.visible($(this).is(':checked'));
                });

                // Inisialisasi ulang event listener checkbox
                $('.toggle-column').off('change').on('change', function() {
                    let column = table.column($(this).attr('data-column'));
                    column.visible(!column.visible());
                });
            }, 500);
        }
    </script>
@endscript

<script>
    document.addEventListener('livewire:load', function() {
        // Listener untuk menampilkan modal
        window.livewire.on('showModal', () => {
            var modal = new bootstrap.Modal(document.getElementById('modal-add'));
            modal.show();
        });

        // Listener untuk menutup modal
        window.livewire.on('closeModal', () => {
            var modal = bootstrap.Modal.getInstance(document.getElementById('modal-add'));
            if (modal) {
                modal.hide();
            }
        });
    });
</script>
