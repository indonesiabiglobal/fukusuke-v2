<div wire:init="loadData">
    @if (!$isLoaded)
        <div>
            <div class="placeholder-glow mb-3" style="height:80px">
                <span class="placeholder col-12 rounded" style="height:80px"></span>
            </div>
            <div style="overflow:hidden;border-radius:4px">
                @for ($i = 0; $i < 8; $i++)
                    <div style="height:36px;margin-bottom:2px;border-radius:3px;background:linear-gradient(90deg,#e9ecef 25%,#f8f9fa 50%,#e9ecef 75%);background-size:200% 100%;animation:ijk-bar-slide 1.2s {{ $i * 0.1 }}s infinite linear"></div>
                @endfor
            </div>
            <style>@keyframes ijk-bar-slide{0%{background-position:200% 0}100%{background-position:-200% 0}}</style>
        </div>
    @else
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
                                        <input wire:model.defer="tglMasuk" type="date" class="form-control"
                                            style="padding:0.44rem" value="{{ $tglMasuk }}">
                                        <input wire:model.defer="tglKeluar" type="date" class="form-control"
                                            style="padding:0.44rem" value="{{ $tglKeluar }}">
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
                        <select class="form-control select2-machine-ijk">
                            <option value="">- All -</option>
                            @foreach ($machine as $item)
                                <option value="{{ $item->id }}" @if ($item->id == ($machine_id ?? null)) selected @endif>
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
                        <select class="form-control select2-shift-ijk">
                            <option value="">- All -</option>
                            @foreach ($workShift as $item)
                                <option value="{{ $item->id }}" @if ($item->id == ($work_shift_filter ?? null)) selected @endif>
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
                        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h2 class="h6 modal-title">Add Jam Kerja Infure</h2>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                                        wire:click="closeModal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-lg-12 mb-1">
                                            <label for="">Tanggal</label>
                                            <div class="form-group" style="margin-left:1px; white-space:nowrap">
                                                <div class="input-group">
                                                    <input class="form-control" style="padding:0.44rem" autocomplete="off"
                                                        type="date" wire:model.defer="working_date"
                                                        max="{{ now()->format('Y-m-d') }}" />
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
                                                        maxlength="1" max="3" />
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
                                                                    <th class="border-0">Dari</th>
                                                                    <th class="border-0">Sampai</th>
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
                                                                        <td>{{ $item['from'] ?? '00:00' }}</td>
                                                                        <td>{{ $item['to'] ?? '00:00' }}</td>
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
                    {{-- Modal Edit jam kerja --}}
                    <div class="modal fade" id="modal-edit" data-bs-backdrop="static" data-bs-keyboard="false"
                        tabindex="-1" role="dialog" aria-labelledby="modal-edit" aria-hidden="true"
                        wire:ignore.self>
                        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h2 class="h6 modal-title">Edit Jam Kerja Infure</h2>
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
                                                    <input class="form-control" type="text" id="work_shift"
                                                        wire:model.defer="work_shift" placeholder="..."
                                                        maxlength="1" max="3" />
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
                                                                    <th class="border-0">Dari</th>
                                                                    <th class="border-0">Sampai</th>
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
                                                                        <td>{{ $item['from'] ?? '00:00' }}</td>
                                                                        <td>{{ $item['to'] ?? '00:00' }}</td>
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
                                                        placeholder="..." id="jamMatiMesinCode"
                                                        oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                                        maxlength="3"
                                                        x-on:keydown.tab="$event.preventDefault(); $refs.jamMatiFromInput.focus();" />
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
                                        {{-- Dari / Sampai dan Lama Mesin Mati --}}
                                        <div class="col-lg-6 mb-1">
                                            <label for="">Dari (HH:MM)</label>
                                            <div class="form-group" style="margin-left:1px; white-space:nowrap">
                                                <input class="form-control" wire:model.change="jamMatiFrom"
                                                    type="time" placeholder="hh:mm" x-ref="jamMatiFromInput">
                                                @error('jamMatiFrom')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-lg-6 mb-1">
                                            <label for="">Sampai (HH:MM)</label>
                                            <div class="form-group" style="margin-left:1px; white-space:nowrap">
                                                <input class="form-control" wire:model.change="jamMatiTo"
                                                    type="time" placeholder="hh:mm" x-ref="jamMatiToInput">
                                                @error('jamMatiTo')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-lg-12 mb-1">
                                            <label for="">Lama Mesin Mati</label>
                                            <div class="form-group" style="margin-left:1px; white-space:nowrap">
                                                <input class="form-control bg-light" wire:model.lazy="off_hour"
                                                    type="time" x-ref="offHourInput" max="08:00" disabled
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

    <div x-data="{
        cols: JSON.parse(localStorage.getItem('jam-kerja-infure-cols') || JSON.stringify({1:true,2:true,3:true,4:true,5:true,6:true,7:true,8:true,9:true,10:true,11:true})),
    }" x-init="$watch('cols', val => { try { localStorage.setItem('jam-kerja-infure-cols', JSON.stringify(val)); } catch(e) {} })"
    class="mt-2 mb-2">
        <div class="d-flex justify-content-between align-items-center mb-1 flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <label class="text-muted small mb-0">Show</label>
                <select wire:model.live="perPage" class="form-select form-select-sm" style="width:auto">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
                <label class="text-muted small mb-0">entries</label>
            </div>
            <div class="dropdown">
                <button type="button" data-bs-toggle="dropdown" class="btn btn-soft-primary btn-icon fs-14">
                    <i class="ri-grid-fill"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" style="min-width:160px">
                    <li><label class="dropdown-item" style="cursor:pointer"><input type="checkbox" x-model="cols[1]" class="form-check-input me-1"> Tanggal</label></li>
                    <li><label class="dropdown-item" style="cursor:pointer"><input type="checkbox" x-model="cols[2]" class="form-check-input me-1"> Shift</label></li>
                    <li><label class="dropdown-item" style="cursor:pointer"><input type="checkbox" x-model="cols[3]" class="form-check-input me-1"> Nomor Mesin</label></li>
                    <li><label class="dropdown-item" style="cursor:pointer"><input type="checkbox" x-model="cols[4]" class="form-check-input me-1"> NIK</label></li>
                    <li><label class="dropdown-item" style="cursor:pointer"><input type="checkbox" x-model="cols[5]" class="form-check-input me-1"> Petugas</label></li>
                    <li><label class="dropdown-item" style="cursor:pointer"><input type="checkbox" x-model="cols[6]" class="form-check-input me-1"> Jam Kerja</label></li>
                    <li><label class="dropdown-item" style="cursor:pointer"><input type="checkbox" x-model="cols[7]" class="form-check-input me-1"> Jam Mati</label></li>
                    <li><label class="dropdown-item" style="cursor:pointer"><input type="checkbox" x-model="cols[8]" class="form-check-input me-1"> Jam Jalan</label></li>
                    <li><label class="dropdown-item" style="cursor:pointer"><input type="checkbox" x-model="cols[9]" class="form-check-input me-1"> Update By</label></li>
                    <li><label class="dropdown-item" style="cursor:pointer"><input type="checkbox" x-model="cols[10]" class="form-check-input me-1"> Updated</label></li>
                    <li><label class="dropdown-item" style="cursor:pointer"><input type="checkbox" x-model="cols[11]" class="form-check-input me-1"> Created</label></li>
                </ul>
            </div>
        </div>

        <div wire:loading.class="opacity-50"
             wire:target="search,sortBy,gotoPage,nextPage,previousPage,perPage"
             style="overflow-x:auto; overflow-y:auto; max-height:65vh; transition:opacity 0.15s;">
            <table class="table align-middle table-nowrap table-hover" id="infureTable">
                <thead class="table-light">
                    <tr>
                        <th style="width:80px">Action</th>
                        <th :class="{'d-none': !cols[1]}" wire:click="sortBy('tdjkm.working_date')" style="cursor:pointer;white-space:nowrap">
                            Tanggal <i class="{{ $sortColumn === 'tdjkm.working_date' ? ($sortDirection === 'asc' ? 'ri-arrow-up-s-line text-primary' : 'ri-arrow-down-s-line text-primary') : 'ri-expand-up-down-line text-muted' }} fs-12"></i>
                        </th>
                        <th :class="{'d-none': !cols[2]}" wire:click="sortBy('tdjkm.work_shift')" style="cursor:pointer;white-space:nowrap">
                            Shift <i class="{{ $sortColumn === 'tdjkm.work_shift' ? ($sortDirection === 'asc' ? 'ri-arrow-up-s-line text-primary' : 'ri-arrow-down-s-line text-primary') : 'ri-expand-up-down-line text-muted' }} fs-12"></i>
                        </th>
                        <th :class="{'d-none': !cols[3]}" wire:click="sortBy('msm.machineno')" style="cursor:pointer;white-space:nowrap">
                            Nomor Mesin <i class="{{ $sortColumn === 'msm.machineno' ? ($sortDirection === 'asc' ? 'ri-arrow-up-s-line text-primary' : 'ri-arrow-down-s-line text-primary') : 'ri-expand-up-down-line text-muted' }} fs-12"></i>
                        </th>
                        <th :class="{'d-none': !cols[4]}" wire:click="sortBy('mse.employeeno')" style="cursor:pointer;white-space:nowrap">
                            NIK <i class="{{ $sortColumn === 'mse.employeeno' ? ($sortDirection === 'asc' ? 'ri-arrow-up-s-line text-primary' : 'ri-arrow-down-s-line text-primary') : 'ri-expand-up-down-line text-muted' }} fs-12"></i>
                        </th>
                        <th :class="{'d-none': !cols[5]}" wire:click="sortBy('mse.empname')" style="cursor:pointer;white-space:nowrap">
                            Petugas <i class="{{ $sortColumn === 'mse.empname' ? ($sortDirection === 'asc' ? 'ri-arrow-up-s-line text-primary' : 'ri-arrow-down-s-line text-primary') : 'ri-expand-up-down-line text-muted' }} fs-12"></i>
                        </th>
                        <th :class="{'d-none': !cols[6]}" wire:click="sortBy('tdjkm.work_hour')" style="cursor:pointer;white-space:nowrap">
                            Jam Kerja <i class="{{ $sortColumn === 'tdjkm.work_hour' ? ($sortDirection === 'asc' ? 'ri-arrow-up-s-line text-primary' : 'ri-arrow-down-s-line text-primary') : 'ri-expand-up-down-line text-muted' }} fs-12"></i>
                        </th>
                        <th :class="{'d-none': !cols[7]}" wire:click="sortBy('tdjkm.off_hour')" style="cursor:pointer;white-space:nowrap">
                            Jam Mati <i class="{{ $sortColumn === 'tdjkm.off_hour' ? ($sortDirection === 'asc' ? 'ri-arrow-up-s-line text-primary' : 'ri-arrow-down-s-line text-primary') : 'ri-expand-up-down-line text-muted' }} fs-12"></i>
                        </th>
                        <th :class="{'d-none': !cols[8]}" wire:click="sortBy('tdjkm.on_hour')" style="cursor:pointer;white-space:nowrap">
                            Jam Jalan <i class="{{ $sortColumn === 'tdjkm.on_hour' ? ($sortDirection === 'asc' ? 'ri-arrow-up-s-line text-primary' : 'ri-arrow-down-s-line text-primary') : 'ri-expand-up-down-line text-muted' }} fs-12"></i>
                        </th>
                        <th :class="{'d-none': !cols[9]}" style="white-space:nowrap">Update By</th>
                        <th :class="{'d-none': !cols[10]}" wire:click="sortBy('tdjkm.updated_on')" style="cursor:pointer;white-space:nowrap">
                            Updated <i class="{{ $sortColumn === 'tdjkm.updated_on' ? ($sortDirection === 'asc' ? 'ri-arrow-up-s-line text-primary' : 'ri-arrow-down-s-line text-primary') : 'ri-expand-up-down-line text-muted' }} fs-12"></i>
                        </th>
                        <th :class="{'d-none': !cols[11]}" wire:click="sortBy('tdjkm.created_on')" style="cursor:pointer;white-space:nowrap">
                            Created <i class="{{ $sortColumn === 'tdjkm.created_on' ? ($sortDirection === 'asc' ? 'ri-arrow-up-s-line text-primary' : 'ri-arrow-down-s-line text-primary') : 'ri-expand-up-down-line text-muted' }} fs-12"></i>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data as $item)
                        <tr>
                            <td>
                                <button type="button" class="btn fs-15 p-1 bg-primary rounded"
                                    wire:click="edit({{ $item->id }})">
                                    <i class="ri-edit-box-line text-white"></i>
                                </button>
                                <button type="button" class="btn fs-15 p-1 bg-danger rounded"
                                    wire:click="delete({{ $item->id }})">
                                    <i class="ri-delete-bin-line text-white"></i>
                                </button>
                            </td>
                            <td :class="{'d-none': !cols[1]}">{{ \Carbon\Carbon::parse($item->working_date)->format('d M Y') }}</td>
                            <td :class="{'d-none': !cols[2]}">{{ $item->work_shift }}</td>
                            <td :class="{'d-none': !cols[3]}">{{ $item->machineno }}</td>
                            <td :class="{'d-none': !cols[4]}">{{ $item->employeeno }}</td>
                            <td :class="{'d-none': !cols[5]}">{{ $item->empname }}</td>
                            <td :class="{'d-none': !cols[6]}">{{ $item->work_hour }}</td>
                            <td :class="{'d-none': !cols[7]}">{{ $item->off_hour }}</td>
                            <td :class="{'d-none': !cols[8]}">{{ $item->on_hour }}</td>
                            <td :class="{'d-none': !cols[9]}">{{ $item->updated_by }}</td>
                            <td :class="{'d-none': !cols[10]}">{{ \Carbon\Carbon::parse($item->updated_on)->format('d M Y H:i:s') }}</td>
                            <td :class="{'d-none': !cols[11]}">{{ \Carbon\Carbon::parse($item->created_on)->format('d M Y H:i:s') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="12" class="text-center py-4">
                                <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop"
                                    colors="primary:#121331,secondary:#08a88a" style="width:40px;height:40px"></lord-icon>
                                <h5 class="mt-2">Record not Found..!</h5>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-between align-items-center flex-wrap mt-2 gap-2">
            <div class="text-muted small">
                @if ($data instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    Showing {{ $data->firstItem() ?? 0 }}–{{ $data->lastItem() ?? 0 }} of {{ $data->total() }} entries
                @endif
            </div>
            <div>
                @if ($data instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    {{ $data->links() }}
                @endif
            </div>
        </div>
    </div>

    <style>
        .modal-overlay-top { z-index: 1060; }
        .modal-backdrop-custom {
            position: fixed; top: 0; left: 0; width: 100vw; height: 100vh;
            background-color: rgba(0,0,0,0.5); z-index: 1058;
        }
        #infureTable.table > :not(caption) > * > * {
            font-size: 13px !important;
            padding: 4px 2px 4px 4px;
        }
    </style>
    @endif
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
        // Close modal jam mati mesin
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

        document.addEventListener('livewire:initialized', function() {
            function initMachineSelect() {
                if ($('.select2-machine-ijk').hasClass('select2-hidden-accessible')) {
                    $('.select2-machine-ijk').select2('destroy');
                }
                $('.select2-machine-ijk').select2({
                    theme: 'bootstrap-5', allowClear: true, placeholder: '- All -',
                }).on('change', function() {
                    @this.set('machine_id', $(this).val() || null);
                });
            }

            function initShiftSelect() {
                if ($('.select2-shift-ijk').hasClass('select2-hidden-accessible')) {
                    $('.select2-shift-ijk').select2('destroy');
                }
                $('.select2-shift-ijk').select2({
                    theme: 'bootstrap-5', allowClear: true, placeholder: '- All -',
                }).on('change', function() {
                    @this.set('work_shift_filter', $(this).val() || null);
                });
            }

            initMachineSelect();
            initShiftSelect();

            Livewire.hook('morph', ({ el, component }) => {
                setTimeout(() => {
                    initMachineSelect();
                    initShiftSelect();
                }, 100);
            });
        });
    </script>
@endscript
