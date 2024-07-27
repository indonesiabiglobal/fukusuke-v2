<div class="row">
    <div class="col-12 col-lg-7">
        <div class="row">
            <div class="col-12 col-lg-3">
                <label class="form-label text-muted fw-bold">Tanggal Proses</label>
            </div>
            <div class="col-12 col-lg-9 mb-1">
                <div class="form-group">
                    <div class="input-group">
                        <div class="col-12">
                            <div class="form-group">
                                <div class="input-group">
                                    <input wire:model.defer="tglMasuk" type="text" class="form-control" style="padding:0.44rem" data-provider="flatpickr" data-date-format="d-m-Y">
                                    <span class="input-group-text py-0">
                                        <i class="ri-calendar-event-fill fs-4"></i>
                                    </span>

                                    <input wire:model.defer="tglKeluar" type="text" class="form-control" style="padding:0.44rem" data-provider="flatpickr" data-date-format="d-m-Y">
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
                    <input wire:model.defer="searchTerm" class="form-control"style="padding:0.44rem" type="text" placeholder="search kode atau nama" />
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
                    <select class="form-control" wire:model.defer="machine_id" data-choices data-choices-sorting-false data-choices-removeItem>
                        <option value="">- All -</option>
                        @foreach ($machine as $item)
                            <option value="{{ $item->id }}">{{ $item->machineno }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-12 col-lg-2">
                <label class="form-label text-muted fw-bold">Shift</label>
            </div>
            <div class="col-12 col-lg-10">
                <div class="mb-1" wire:ignore>
                    <select class="form-control" wire:model.defer="work_shift_filter" data-choices data-choices-sorting-false data-choices-removeItem>
                        <option value="">- All -</option>
                        @foreach ($workShift as $item)
                            <option value="{{ $item->id }}">{{ $item->work_shift }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-12 mt-2">
        <div class="row">
            <div class="col-12 col-lg-6">
                <button wire:click="search" type="button" class="btn btn-primary btn-load w-lg p-1">
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

                <button
                    type="button"
                    class="btn btn-success w-lg p-1"
                     data-bs-toggle="modal" data-bs-target="#modal-add"
                    >
                    <i class="ri-add-line"> </i> Add
                </button>
                <div class="modal fade" id="modal-add" tabindex="-1" role="dialog" aria-labelledby="modal-add" aria-hidden="true" wire:ignore.self>
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h2 class="h6 modal-title">Add Jam Kerja Infure</h2>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-lg-12 mb-1">
                                            <label for="">Tanggal</label>
                                            <div class="form-group" style="margin-left:1px; white-space:nowrap">
                                                <div class="input-group">
                                                    <input class="form-control" style="padding:0.44rem" data-provider="flatpickr" data-date-format="d-m-Y" type="text" wire:model.defer="working_date" placeholder="yyyy/mm/dd"/>
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
                                                    <input class="form-control" type="text" wire:model.defer="work_shift" placeholder="..." />
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
                                                    <input class="form-control" type="text" wire:model.live="machineno" placeholder="..." />
                                                    <input class="form-control readonly" readonly="readonly" type="text" wire:model="machinename" placeholder="..." />
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
                                                    <input class="form-control" wire:model.live="employeeno" type="text" placeholder="..." />
                                                    <input class="form-control readonly" readonly="readonly" type="text" wire:model="empname" placeholder="..." />
                                                    @error('employeeno')
                                                        <span class="invalid-feedback">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-12 mb-1">
                                            <label for="">Jam Kerja</label>
                                            <div class="form-group" style="margin-left:1px; white-space:nowrap">
                                                <input class="form-control" wire:model="work_hour" type="time" placeholder="hh:mm">
                                                @error('work_hour')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-lg-12 mb-1">
                                            <label for="">Lama Mesin Mati</label>
                                            <div class="form-group" style="margin-left:1px; white-space:nowrap">
                                                <input class="form-control" wire:model="off_hour" type="time" placeholder="hh:mm">
                                                @error('off_hour')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    {{-- <button type="button" class="btn btn-secondary">Accept</button> --}}
                                    <button type="button" class="btn btn-link text-gray-600 ms-auto" data-bs-dismiss="modal">Close</button>
                                    {{-- <button type="submit" class="btn btn-success" wire:click="save">
                                        Save
                                    </button> --}}
                                    <button type="submit" class="btn btn-success" wire:click="save">
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
                </div>
                <div class="modal fade" id="modal-edit" tabindex="-1" role="dialog" aria-labelledby="modal-edit" aria-hidden="true" wire:ignore.self>
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h2 class="h6 modal-title">Edit Jam Kerja Infure</h2>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-lg-12 mb-1">
                                            <label for="">Tanggal</label>
                                            <div class="form-group" style="margin-left:1px; white-space:nowrap">
                                                <div class="input-group">
                                                    <input class="form-control datepicker-input" type="date" wire:model.defer="working_date" placeholder="yyyy/mm/dd"/>
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
                                                    <input class="form-control" type="text" wire:model.defer="work_shift" placeholder="..." />
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
                                                    <input class="form-control" type="text" wire:model.live="machineno" placeholder="..." />
                                                    <input class="form-control readonly" readonly="readonly" type="text" wire:model="machinename" placeholder="..." />
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
                                                    <input class="form-control" wire:model.live="employeeno" type="text" placeholder="..." />
                                                    <input class="form-control readonly" readonly="readonly" type="text" wire:model="empname" placeholder="..." />
                                                    @error('employeeno')
                                                        <span class="invalid-feedback">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-12 mb-1">
                                            <label for="">Jam Kerja</label>
                                            <div class="form-group" style="margin-left:1px; white-space:nowrap">
                                                <input class="form-control" wire:model="work_hour" type="time" placeholder="hh:mm">
                                                @error('work_hour')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-lg-12 mb-1">
                                            <label for="">Lama Mesin Mati</label>
                                            <div class="form-group" style="margin-left:1px; white-space:nowrap">
                                                <input class="form-control" wire:model="on_hour" type="time" placeholder="hh:mm">
                                                @error('on_hour')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    {{-- <button type="button" class="btn btn-secondary">Accept</button> --}}
                                    <button type="button" class="btn btn-link text-gray-600 ms-auto" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-success" wire:click="save">
                                        Save
                                    </button>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="table-responsive table-card mt-3 mb-1">
        <table class="table align-middle table-nowrap" id="customerTable" style="width:100%">
            <thead class="table-light">
                <tr>
                    <th class="border-0 rounded-start">Action</th>
                    <th class="border-0">Tanggal</th>
                    <th class="border-0">Shift</th>
                    <th class="border-0">Nomor Mesin</th>
                    <th class="border-0">NIK</th>
                    <th class="border-0">Petugas</th>
                    <th class="border-0">Jam Kerja</th>
                    <th class="border-0">Jam Mati</th>
                    <th class="border-0 rounded-end">Jam Jalan</th>
                </tr>
            </thead>
            <tbody class="list form-check-all">
                @forelse ($data as $item)
                    <tr>
                        <td>
                            <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#modal-edit" wire:click="edit({{$item->id}})">
                                <i class="ri-edit-box-line text-white"></i>
                            </button>
                        </td>
                        <td>{{ $item->working_date }}</td>
                        <td>{{ $item->work_shift }}</td>
                        <td>{{ $item->machine_id }}</td>
                        <td>

                        </td>
                        <td>

                        </td>
                        <td>{{ $item->work_hour }}</td>
                        <td>{{ $item->off_hour }}</td>
                        <td>{{ $item->on_hour }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="12" class="text-center">
                            <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop" colors="primary:#121331,secondary:#08a88a" style="width:40px;height:40px"></lord-icon>
                            <h5 class="mt-2">Sorry! No Result Found</h5>
                            <p class="text-muted mb-0">We've searched more than 150+ Orders We did not find any orders for you search.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        {{ $data->links() }}
    </div>
</div>

<script>
    document.addEventListener('livewire:load', function () {
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
