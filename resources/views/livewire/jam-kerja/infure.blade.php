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
                    <select class="form-control" wire:model.defer="machine_id" data-choices data-choices-sorting-false
                        data-choices-removeItem>
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
                        data-choices-sorting-false data-choices-removeItem>
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

                <button type="button" class="btn btn-success w-lg p-1" wire:click="showModalCreate">
                    <i class="ri-add-line"> </i> Add
                </button>
                <div class="modal fade" id="modal-add" tabindex="-1" role="dialog" aria-labelledby="modal-add"
                    aria-hidden="true" wire:ignore.self>
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h2 class="h6 modal-title">Add Jam Kerja Infure</h2>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-lg-12 mb-1">
                                        <label for="">Tanggal</label>
                                        <div class="form-group" style="margin-left:1px; white-space:nowrap">
                                            <div class="input-group">
                                                <input class="form-control" style="padding:0.44rem"
                                                    data-provider="flatpickr" data-date-format="d-m-Y" type="text"
                                                    wire:model.defer="working_date" placeholder="yyyy/mm/dd" />
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
                                                <input class="form-control" type="text"
                                                    wire:model.defer="work_shift" placeholder="..." />
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
                                                    x-on:keydown.tab="$event.preventDefault(); $refs.workHourInput.focus();" x-ref="employeenoInput" type="text"
                                                    placeholder="..." />
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
                                            <input class="form-control" wire:model="work_hour" type="time"
                                                x-on:keydown.tab="$event.preventDefault(); $refs.offHourInput.focus();" x-ref="workHourInput"
                                                placeholder="hh:mm" wire:change="validateWorkHour" max="08:00">
                                            @error('work_hour')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-12 mb-1">
                                        <label for="">Lama Mesin Mati</label>
                                        <div class="form-group" style="margin-left:1px; white-space:nowrap">
                                            <input class="form-control" wire:model="off_hour" wire:change="validateWorkHour" type="time"  x-ref="offHourInput"
                                                placeholder="hh:mm">
                                            @error('off_hour')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                {{-- <button type="button" class="btn btn-secondary">Accept</button> --}}
                                <button type="button" class="btn btn-link text-gray-600 ms-auto"
                                    data-bs-dismiss="modal">Close</button>
                                {{-- <button type="submit" class="btn btn-success" wire:click="save">
                                        Save
                                    </button> --}}
                                <button type="button" class="btn btn-success" wire:click="save">
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
                <div class="modal fade" id="modal-edit" tabindex="-1" role="dialog" aria-labelledby="modal-edit"
                    aria-hidden="true" wire:ignore.self>
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h2 class="h6 modal-title">Edit Jam Kerja Infure</h2>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
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
                                                    wire:model.defer="work_shift" placeholder="..." />
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
                                                    x-on:keydown.tab="$event.preventDefault(); $refs.workHourEditInput.focus();" x-ref="employeenoEditInput" type="text"
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
                                            x-on:keydown.tab="$event.preventDefault(); $refs.offHourEditInput.focus();" x-ref="workHourEditInput"
                                                placeholder="hh:mm" wire:change="validateWorkHour" max="08:00">
                                            @error('work_hour')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-12 mb-1">
                                        <label for="">Lama Mesin Mati</label>
                                        <div class="form-group" style="margin-left:1px; white-space:nowrap">
                                            <input class="form-control" wire:model="off_hour" wire:change="validateWorkHour" type="time"  x-ref="offHourEditInput"
                                                placeholder="hh:mm">
                                            @error('off_hour')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                {{-- <button type="button" class="btn btn-secondary">Accept</button> --}}
                                <button type="button" class="btn btn-link text-gray-600 ms-auto"
                                    data-bs-dismiss="modal">Close</button>
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

                {{-- delete --}}
                <div id="removeBuyerModal" class="modal fade zoomIn" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                                    id="close-removeBuyerModal"></button>
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
                                        class="btn w-sm btn-danger" id="remove-item">
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
                {{-- end modal delete buyer --}}
            </div>
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
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="9"
                            checked> Update By
                    </label>
                </li>
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="10"
                            checked> Updated
                    </label>
                </li>
            </ul>
        </div>
        <table class="table align-middle" id="infureTable">
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
                    <th>Update By</th>
                    <th>Updated</th>
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
                            <button type="button"
                                class="btn fs-15 p-1 bg-danger rounded btn-delete" data-delete-id="{{ $item->id }}"
                                wire:click="delete({{ $item->id }})">
                                <i class="ri-delete-bin-line text-white"></i>
                            </button>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($item->working_date)->format('d M Y') }}</td>
                        <td>{{ $item->work_shift }}</td>
                        <td>{{ $item->machine_id }}</td>
                        <td> {{ $item->employeeno }}</td>
                        <td> {{ $item->empname }}</td>
                        <td>{{ $item->work_hour }}</td>
                        <td>{{ $item->off_hour }}</td>
                        <td>{{ $item->on_hour }}</td>
                        <td>{{ $item->updated_by }}</td>
                        <td>{{ \Carbon\Carbon::parse($item->updated_on)->format('d M Y H:i:s') }}</td>
                    </tr>
                @empty
                    {{-- <tr>
                        <td colspan="12" class="text-center">
                            <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop"
                                colors="primary:#121331,secondary:#08a88a" style="width:40px;height:40px"></lord-icon>
                            <h5 class="mt-2">Sorry! No Result Found</h5>
                            <p class="text-muted mb-0">We've searched more than 150+ Orders We did not find any orders
                                for you search.</p>
                        </td>
                    </tr> --}}
                @endforelse
            </tbody>
        </table>
        {{-- {{ $data->links(data: ['scrollTo' => false]) }} --}}
    </div>
</div>

@script
    <script>
        // Show modal create buyer
        $wire.on('showModalCreate', () => {
            $('#modal-add').modal('show');
        });

        // Close modal create buyer
        $wire.on('closeModalCreate', () => {
            $('#modal-add').modal('hide');
        });
        // Show modal update buyer
        $wire.on('showModalUpdate', () => {
            $('#modal-edit').modal('show');
        });

        // Close modal update buyer
        $wire.on('closeModalUpdate', () => {
            $('#modal-edit').modal('hide');
        });

        // Show modal delete buyer
        $wire.on('showModalDelete', () => {
            $('#removeBuyerModal').modal('show');
        });

        // Close modal delete buyer
        $wire.on('closeModalDelete', () => {
            $('#removeBuyerModal').modal('hide');
        });

        // datatable
        // inisialisasi DataTable
        $wire.on('initDataTable', () => {
            initDataTable();
        });

        // Fungsi untuk menginisialisasi ulang DataTable
        function initDataTable() {
            // Hapus DataTable jika sudah ada
            if ($.fn.dataTable.isDataTable('#infureTable')) {
                let table = $('#infureTable').DataTable();
                table.clear(); // Bersihkan data tabel
                table.destroy(); // Hancurkan DataTable
                // Hindari penggunaan $('#infureTable').empty(); di sini
            }

            setTimeout(() => {
                // Inisialisasi ulang DataTable
                let table = $('#infureTable').DataTable({
                    "pageLength": 10,
                    "searching": true,
                    "responsive": true,
                    "order": [
                        [1, "asc"]
                    ],
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
