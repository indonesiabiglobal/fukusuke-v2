<div class="row">
    <div class="col-lg-12 mt-2">
        <div class="row">

            <div class="col-12 col-lg-6">
                {{-- <button class="btn btn-primary w-lg p-1" wire:click="download" type="button">
                    <span wire:loading.remove wire:target="download">
                        <i class="ri-download-cloud-2-line"> </i> Download Template
                    </span>
                    <div wire:loading wire:target="download">
                        <span class="d-flex align-items-center">
                            <span class="spinner-border flex-shrink-0" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </span>
                            <span class="flex-grow-1 ms-1">
                                Loading...
                            </span>
                        </span>
                    </div>
                </button> --}}
                {{-- <button class="btn btn-info w-lg p-1" wire:click="print" type="button">
                    <span wire:loading.remove wire:target="print">
                        <i class="ri-printer-line"> </i> Print
                    </span>
                    <div wire:loading wire:target="print">
                        <span class="d-flex align-items-center">
                            <span class="spinner-border flex-shrink-0" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </span>
                            <span class="flex-grow-1 ms-1">
                                Loading...
                            </span>
                        </span>
                    </div>
                </button> --}}
                {{-- Button Add machine --}}
                <button type="button" class="btn btn-success w-lg p-1" wire:click="showModalCreate">
                    <i class="ri-add-line"> </i> Add
                </button>
                {{-- modal add machine --}}
                <div class="modal fade" id="modal-add" tabindex="-1" aria-labelledby="modal-addLabel" aria-modal="true"
                    wire:ignore.self>
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modal-addLabel">Add Master Machine</h5> <button
                                    type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form wire:submit.prevent="store">
                                    <div class="row g-3">
                                        <div class="col-xxl-12">
                                            <div>
                                                <label for="machinename" class="form-label">Nama Mesin</label>
                                                <input type="text"
                                                    class="form-control @error('machinename') is-invalid @enderror"
                                                    id="machinename" wire:model.defer="machinename" placeholder="Nama">
                                                @error('machinename')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-xxl-12" wire:ignore>
                                            <div class="row">
                                                <label class="form-label">Tanggal</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" data-provider="flatpickr" data-date-format="d M, Y" data-range-date="true" wire:model="jadwal_mesin">
                                                    <span class="input-group-text py-0">
                                                        <i class="ri-calendar-event-fill fs-4"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xxl-12">
                                            <div>
                                                <label for="jam" class="form-label">Jam</label>
                                                <div class="input-group">
                                                    <input class="form-control" wire:model="jam" type="time" placeholder="hh:mm">
                                                    <span class="input-group-text py-0">
                                                        <i class="ri-calendar-event-fill fs-4"></i>
                                                    </span>
                                                    @error('jam')
                                                        <span class="invalid-feedback">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xxl-12">
                                            <div>
                                                <label for="percent" class="form-label">Percent</label>
                                                <input type="number"
                                                    class="form-control @error('percent') is-invalid @enderror"
                                                    id="percent" wire:model.defer="percent"
                                                    placeholder="">
                                                @error('percent')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        {{-- button --}}
                                        <div class="col-lg-12">
                                            <div class="hstack gap-2 justify-content-end">
                                                <button type="button" class="btn btn-light"
                                                    data-bs-dismiss="modal">Close</button>
                                                <button id="btnCreate" type="submit" class="btn btn-success w-lg">
                                                    <span wire:loading.remove wire:target="store">
                                                        <i class="ri-save-3-line"></i> Save
                                                    </span>
                                                    <div wire:loading wire:target="store">
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
                                                {{-- <button type="submit" class="btn btn-primary">Save</button> --}}
                                            </div>
                                        </div><!--end col-->
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- end modal machine --}}

                {{-- modal edit machine --}}
                <div class="modal fade" id="modal-edit" tabindex="-1" aria-labelledby="modal-editLabel"
                    aria-modal="true" wire:ignore.self>
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modal-editLabel">Edit Jadwal Machine</h5> <button
                                    type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form wire:submit.prevent="update">
                                    <div class="row g-3">
                                        <div class="col-xxl-12">
                                            <div>
                                                <label for="machinename" class="form-label">Nama Mesin</label>
                                                <input type="text" readonly="readonly"
                                                    class="form-control @error('machinename') is-invalid @enderror readonly bg-light"
                                                    id="machinename" wire:model.defer="machinename"
                                                    placeholder="Nama">
                                                @error('machinename')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-xxl-12" wire:ignore>
                                            <div class="row">
                                                <label class="form-label">Tanggal</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" data-provider="flatpickr" data-date-format="d M, Y" data-range-date="true" wire:model="jadwal_mesin">
                                                    <span class="input-group-text py-0">
                                                        <i class="ri-calendar-event-fill fs-4"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xxl-12">
                                            <div>
                                                <label for="jam" class="form-label">Jam</label>
                                                <div class="input-group">
                                                    <input class="form-control" wire:model="jam" type="time" placeholder="hh:mm">
                                                    <span class="input-group-text py-0">
                                                        <i class="ri-calendar-event-fill fs-4"></i>
                                                    </span>
                                                    @error('jam')
                                                        <span class="invalid-feedback">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xxl-12">
                                            <div>
                                                <label for="percent" class="form-label">Percent</label>
                                                <input type="number"
                                                    class="form-control @error('percent') is-invalid @enderror"
                                                    id="percent" wire:model.defer="percent"
                                                    placeholder="">
                                                @error('percent')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        {{-- button --}}
                                        <div class="col-lg-12">
                                            <div class="hstack gap-2 justify-content-end">
                                                <button type="button" class="btn btn-light"
                                                    data-bs-dismiss="modal">Close</button>
                                                <button id="btnCreate" type="submit" class="btn btn-success w-lg">
                                                    <span wire:loading.remove wire:target="update">
                                                        <i class="ri-save-3-line"></i> Update
                                                    </span>
                                                    <div wire:loading wire:target="update">
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
                                                {{-- <button type="submit" class="btn btn-primary">Save</button> --}}
                                            </div>
                                        </div><!--end col-->
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- end modal machine --}}

                {{-- start modal delete machine --}}
                <div id="modal-delete" class="modal fade zoomIn" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                                    id="close-modal-delete"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mt-2 text-center">
                                    <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop"
                                        colors="primary:#f7b84b,secondary:#f06548"
                                        style="width:100px;height:100px"></lord-icon>
                                    <div class="mt-4 pt-2 fs-15 mx-4 mx-sm-5">
                                        <h4>Are you sure ?</h4>
                                        <p class="text-muted mx-4 mb-0">Are you sure you want to remove this machine
                                            ?
                                        </p>
                                    </div>
                                </div>
                                <div class="d-flex gap-2 justify-content-center mt-4 mb-2">
                                    <button type="button" class="btn w-sm btn-light"
                                        data-bs-dismiss="modal">Close</button>
                                    <button wire:click="destroy" id="btnCreate" type="button"
                                        class="btn w-sm btn-danger" id="remove-item">
                                        <span wire:loading.remove wire:target="destroy">
                                            <i class="ri-save-3-line"></i> Yes, Delete It!
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
                {{-- end modal delete machine --}}
            </div>

            {{-- filter --}}
            {{-- <div class="col-12 col-lg-6">
                <form wire:submit.prevent="search">
                    <div class="input-group">
                        <input wire:model.defer="searchTerm" class="form-control"style="padding:0.44rem"
                            type="text" placeholder="search kode,nama machine" />
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
                    </div>
                </form>
            </div> --}}
            {{-- toggle column table --}}
            <div class="col-12 col-lg-6">
                <div class="col text-end dropdown">
                    <button type="button" data-bs-toggle="dropdown" aria-expanded="false"
                        class="btn btn-soft-primary btn-icon fs-14 mt-2">
                        <i class="ri-grid-fill"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <label style="cursor: pointer;">
                                <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox"
                                    data-column="1" checked> Nama Mesin
                            </label>
                        </li>
                        <li>
                            <label style="cursor: pointer;">
                                <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox"
                                    data-column="2" checked> No Mesin
                            </label>
                        </li>
                        <li>
                            <label style="cursor: pointer;">
                                <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox"
                                    data-column="3" checked> Departemen
                            </label>
                        </li>
                        <li>
                            <label style="cursor: pointer;">
                                <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox"
                                    data-column="4" checked> Jenis Produk
                            </label>
                        </li>
                        <li>
                            <label style="cursor: pointer;">
                                <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox"
                                    data-column="5" checked> Kapasitas (Kg)
                            </label>
                        </li>
                        <li>
                            <label style="cursor: pointer;">
                                <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox"
                                    data-column="6" checked> Kapasitas (Qty)
                            </label>
                        </li>
                        <li>
                            <label style="cursor: pointer;">
                                <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox"
                                    data-column="7" checked> Status
                            </label>
                        </li>
                        <li>
                            <label style="cursor: pointer;">
                                <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox"
                                    data-column="8" checked> Updated By
                            </label>
                        </li>
                        <li>
                            <label style="cursor: pointer;">
                                <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox"
                                    data-column="9" checked> Updated
                            </label>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="table-responsive table-card mt-3 mb-1">
        <table class="table align-middle table-nowrap" id="machineTable" style="width:100%">
            <thead class="table-light">
                <tr>
                    <th>Action</th>
                    <th>Nama Mesin</th>
                    <th>No Mesin</th>
                    <th>Kapasitas (Kg)</th>
                    <th>Kapasitas (Qty)</th>
                    <th>Jadwal</th>
                    <th>Jam</th>
                    <th>Percent</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody class="list form-check-all">
                @forelse ($data as $item)
                    <tr>
                        <td>
                            <button type="button" class="btn fs-15 p-1 bg-primary rounded btn-edit"
                                data-edit-id="{{ $item->id }}" wire:click="edit({{ $item->id }})">
                                <i class="ri-time-line text-white"></i>
                            </button>
                            <button {{ $item->status == 0 ? 'hidden' : '' }} type="button"
                                class="btn fs-15 p-1 bg-danger rounded modal-delete btn-delete"
                                data-delete-id="{{ $item->id }}" wire:click="delete({{ $item->id }})">
                                <i class="ri-delete-bin-line  text-white"></i>
                            </button>
                        </td>
                        <td>{{ $item->machinename }}</td>
                        <td>{{ $item->machineno }}</td>
                        <td>{{ number_format($item->capacity_kg, 2) }}</td>
                        <td>{{ number_format($item->capacity_lembar, 2) }}</td>
                        <td>{{ $item->jadwal }}</td>
                        <td>{{ $item->jam }}</td>
                        <td>{{ $item->percent }}</td>
                        <td>
                            {!! $item->status == 1
                                ? '<span class="badge text-success bg-success-subtle">Active</span>'
                                : '<span class="badge text-bg-danger">Non Active</span>' !!}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center">
                            <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop"
                                colors="primary:#121331,secondary:#08a88a" style="width:40px;height:40px"></lord-icon>
                            <h5 class="mt-2">Sorry! No Result Found</h5>
                            <p class="text-muted mb-0">We've searched more than 150+ Orders We did not find any orders
                                for you search.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        {{-- {{ $data->links() }} --}}
    </div>
    {{-- <livewire:tdorder/> --}}
</div>

@script
    <script>
        $wire.on('showModalCreate', () => {
            $('#modal-add').modal('show');
        });
        // close modal create machine
        $wire.on('closeModalCreate', () => {
            $('#modal-add').modal('hide');
        });

        // show modal update machine
        $wire.on('showModalUpdate', () => {
            $('#modal-edit').modal('show');
        });
        // close modal update machine
        $wire.on('closeModalUpdate', () => {
            $('#modal-edit').modal('hide');
        });

        // show modal delete machine
        $wire.on('showModalDelete', () => {
            $('#modal-delete').modal('show');
        });

        // close modal delete machine
        $wire.on('closeModalDelete', () => {
            $('#modal-delete').modal('hide');
        });

        // datatable
        $wire.on('initDataTable', () => {
            initDataTable('machineTable');
        });

        // Fungsi untuk menginisialisasi ulang DataTable
        function initDataTable(id) {
            // Hapus DataTable jika sudah ada
            if ($.fn.dataTable.isDataTable('#' + id)) {
                let table = $('#' + id).DataTable();
                table.clear(); // Bersihkan data tabel
                table.destroy(); // Hancurkan DataTable
                // Hindari penggunaan $('#' + id).empty(); di sini
            }

            setTimeout(() => {
                // Inisialisasi ulang DataTable
                let table = $('#' + id).DataTable({
                    "pageLength": 10,
                    "searching": true,
                    "responsive": true,
                    "scrollX": true,
                    "order": [
                        [2, "asc"]
                    ],
                    "language": {
                        "emptyTable": `
                            <div class="text-center">
                                <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop"
                                    colors="primary:#121331,secondary:#08a88a" style="width:40px;height:40px"></lord-icon>
                                <h5 class="mt-2">Sorry! No Result Found</h5>
                            </div>
                        `
                    },
                });
                // tombol delete
                $('.btn-delete').on('click', function() {
                    let id = $(this).attr('data-delete-id');

                    // livewire click
                    $wire.dispatch('delete', {
                        id
                    });
                });
                // tombol edit
                $('.btn-edit').on('click', function() {
                    let id = $(this).attr('data-edit-id');

                    // livewire click
                    $wire.dispatch('edit', {
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

                setTimeout(() => {
                    table.on('search.dt', function() {
                        var value = $('.dt-search input').val();
                        // debounce
                        debounce(function() {
                            // url search
                            window.history.pushState(null, null, `?search=${value}`);
                        }, 300)();
                    });

                    let querySearch = new URLSearchParams(window.location.search).get('search') || '';

                    // set search term
                    table.search(querySearch).draw();
                }, 10);
            }, 500);
        }
    </script>
@endscript
