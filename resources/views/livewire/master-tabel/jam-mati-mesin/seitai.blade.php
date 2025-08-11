<div class="row">
    <div class="col-lg-12 mt-2">
        {{-- Header Section --}}
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                {{-- Title Section --}}
                <div class="row align-items-center mb-4">
                    <div class="col-12 col-md-6">
                        <h1 class="card-title mb-0 fs-20 fw-bold text-dark">
                            <i class="ri-settings-3-line me-2 text-primary"></i>
                            Master Jam Mati Mesin Seitai
                        </h1>
                        <p class="text-muted mb-0 mt-1">Manage machine downtime records</p>
                    </div>
                    <div class="col-12 col-md-6 text-md-end mt-3 mt-md-0">
                        <span class="badge bg-light text-dark fs-12">
                            Total Records: {{ $result->count() }}
                        </span>
                    </div>
                </div>

                {{-- Action Buttons Section --}}
                <div class="row align-items-center justify-content-between">
                    {{-- Add Button --}}
                    <div class="col-12 col-sm-6 col-lg-4 mb-2 mb-lg-0">
                        <button type="button" class="btn btn-success btn-label waves-effect waves-light"
                            wire:click="showModalCreate">
                            <i class="ri-add-circle-line label-icon align-middle fs-16 me-2"></i>
                            Tambah Data
                        </button>
                    </div>

                    {{-- Filter & Column Toggle --}}
                    <div class="col-12 col-md-4 col-xl-4">
                        <div class="d-flex flex-wrap gap-2 justify-content-md-end">
                            {{-- Filter Dropdown --}}
                            <div class="dropdown">
                                <button class="btn btn-outline-secondary dropdown-toggle position-relative"
                                    type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="ri-filter-3-line me-1"></i>
                                    Filter
                                    @if ($statusFilter !== 'all')
                                        <span
                                            class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary">
                                            <span class="visually-hidden">Active filter</span>
                                        </span>
                                    @endif
                                </button>
                                <ul class="dropdown-menu">
                                    <li class="dropdown-header d-flex justify-content-between align-items-center">
                                        <span>Filter by Status</span>
                                        @if ($statusFilter !== 'all')
                                            <button type="button" class="btn btn-link btn-sm text-decoration-none p-0"
                                                wire:click="clearFilter" title="Clear Filter">
                                                <i class="ri-close-line"></i>
                                            </button>
                                        @endif
                                    </li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>

                                    <li>
                                        <a class="dropdown-item {{ $statusFilter === 'active' ? 'active' : '' }}"
                                            href="#" wire:click.prevent="filterByStatus('active')">
                                            <i class="ri-check-line me-2 text-success"></i>
                                            Active
                                            @if ($statusFilter === 'active')
                                                <i class="ri-check-double-line ms-auto text-primary"></i>
                                            @endif
                                        </a>
                                    </li>

                                    <li>
                                        <a class="dropdown-item {{ $statusFilter === 'inactive' ? 'active' : '' }}"
                                            href="#" wire:click.prevent="filterByStatus('inactive')">
                                            <i class="ri-close-line me-2 text-danger"></i>
                                            Inactive
                                            @if ($statusFilter === 'inactive')
                                                <i class="ri-check-double-line ms-auto text-primary"></i>
                                            @endif
                                        </a>
                                    </li>

                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>

                                    <li>
                                        <a class="dropdown-item {{ $statusFilter === 'all' ? 'active' : '' }}"
                                            href="#" wire:click.prevent="filterByStatus('all')">
                                            <i class="ri-refresh-line me-2"></i>
                                            All Records
                                            @if ($statusFilter === 'all')
                                                <i class="ri-check-double-line ms-auto text-primary"></i>
                                            @endif
                                        </a>
                                    </li>

                                    {{-- Show filtered count --}}
                                    @if ($statusFilter !== 'all')
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li class="dropdown-header">
                                            <small class="text-muted">
                                                Showing {{ $result->count() }} of {{ $totalRecords ?? 0 }} records
                                            </small>
                                        </li>
                                    @endif
                                </ul>
                            </div>

                            {{-- Loading indicator when filtering --}}
                            {{-- <div wire:loading wire:target="filterByStatus,clearFilter" class="d-inline-block ms-2">
                                <div class="spinner-border spinner-border-sm text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div> --}}

                            {{-- Column Toggle --}}
                            <div class="dropdown">
                                <button type="button" data-bs-toggle="dropdown" aria-expanded="false"
                                    class="btn btn-outline-secondary dropdown-toggle">
                                    <i class="ri-layout-column-line me-1"></i> Columns
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end p-2" style="min-width: 200px;">
                                    <li class="dropdown-header">
                                        <i class="ri-eye-line me-1"></i> Toggle Columns
                                    </li>
                                    <li>
                                        <hr class="dropdown-divider my-2">
                                    </li>
                                    <li class="px-2 py-1">
                                        <div class="form-check">
                                            <input class="form-check-input toggle-column" type="checkbox"
                                                data-column="1" checked id="col1">
                                            <label class="form-check-label" for="col1">
                                                Nama Mesin Mati
                                            </label>
                                        </div>
                                    </li>
                                    <li class="px-2 py-1">
                                        <div class="form-check">
                                            <input class="form-check-input toggle-column" type="checkbox"
                                                data-column="2" checked id="col2">
                                            <label class="form-check-label" for="col2">
                                                Code
                                            </label>
                                        </div>
                                    </li>
                                    <li class="px-2 py-1">
                                        <div class="form-check">
                                            <input class="form-check-input toggle-column" type="checkbox"
                                                data-column="3" checked id="col3">
                                            <label class="form-check-label" for="col3">
                                                Klasifikasi
                                            </label>
                                        </div>
                                    </li>
                                    <li class="px-2 py-1">
                                        <div class="form-check">
                                            <input class="form-check-input toggle-column" type="checkbox"
                                                data-column="4" checked id="col4">
                                            <label class="form-check-label" for="col4">
                                                Status
                                            </label>
                                        </div>
                                    </li>
                                    <li class="px-2 py-1">
                                        <div class="form-check">
                                            <input class="form-check-input toggle-column" type="checkbox"
                                                data-column="5" checked id="col5">
                                            <label class="form-check-label" for="col5">
                                                Updated By
                                            </label>
                                        </div>
                                    </li>
                                    <li class="px-2 py-1">
                                        <div class="form-check">
                                            <input class="form-check-input toggle-column" type="checkbox"
                                                data-column="6" checked id="col6">
                                            <label class="form-check-label" for="col6">
                                                Updated
                                            </label>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-12 mt-3">
        <div class="table-responsive card">
            <div class="card-body">
                <table class="table align-middle table-nowrap" id="boxTable" style="width:100%">
                    <thead class="table-light">
                        <tr>
                            <th>Action</th>
                            <th>Nama Mesin Mati</th>
                            <th>Code</th>
                            <th>Klasifikasi</th>
                            <th>Status</th>
                            <th>Update_By</th>
                            <th>Updated</th>
                        </tr>
                    </thead>
                    <tbody class="list form-check-all">
                        @forelse ($result as $item)
                            <tr>
                                <td>
                                    <button type="button" class="btn fs-15 p-1 bg-primary rounded btn-edit"
                                        data-edit-id="{{ $item->id }}" wire:click="edit({{ $item->id }})">
                                        <i class="ri-edit-box-line text-white"></i>
                                    </button>
                                    <button {{ $item->status == 0 ? 'hidden' : '' }} type="button"
                                        class="btn fs-15 p-1 bg-danger rounded removeBuyerModal btn-delete"
                                        data-delete-id="{{ $item->id }}"
                                        wire:click="delete({{ $item->id }})">
                                        <i class="ri-delete-bin-line text-white"></i>
                                    </button>
                                </td>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->code }}</td>
                                <td>{{ $item->classification }}</td>
                                <td>
                                    {!! $item->status == 1
                                        ? '<span class="badge text-success bg-success-subtle">Active</span>'
                                        : '<span class="badge text-bg-danger">Non Active</span>' !!}
                                </td>
                                <td>{{ $item->updated_by }}</td>
                                <td>{{ \Carbon\Carbon::parse($item->updated_on)->format('d-M-Y H:i:s') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center">
                                    <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop"
                                        colors="primary:#121331,secondary:#08a88a"
                                        style="width:40px;height:40px"></lord-icon>
                                    <h5 class="mt-2">Sorry! No Result Found</h5>
                                    <p class="text-muted mb-0">We've searched more than 150+ Orders We did not find any
                                        orders
                                        for you search.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>


    <div class="col-12 col-lg-6">
        {{-- modal add buyer --}}
        <div class="modal fade" id="modal-add" tabindex="-1" aria-labelledby="modal-addLabel" aria-modal="true"
            wire:ignore.self>
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modal-addLabel">Add Master Jam Mati Mesin Seitai</h5> <button
                            type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form wire:submit.prevent="store">
                            <div class="row g-3">
                                <div class="col-xxl-12">
                                    <div>
                                        <label for="code" class="form-label">Kode Jam Mati Mesin</label>
                                        <input type="number" class="form-control @error('code') is-invalid @enderror"
                                            oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);"
                                            id="code" wire:model.defer="code" placeholder="Kode">
                                        @error('code')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                {{-- Nama Jam Mati Mesin --}}
                                <div class="col-xxl-12">
                                    <div>
                                        <label for="name" class="form-label">Nama Jam Mati Mesin</label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                                            id="name" wire:model.defer="name" placeholder="Nama">
                                        @error('name')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                {{-- Klasifikasi --}}
                                <div class="col-xxl-12">
                                    <div>
                                        <label for="classification" class="form-label">Klasifikasi</label>
                                        <input type="text"
                                            class="form-control @error('classification') is-invalid @enderror"
                                            id="classification" wire:model.defer="classification"
                                            placeholder="Klasifikasi">
                                        @error('classification')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                {{-- button --}}
                                <div class="col-lg-12">
                                    <div class="hstack gap-2 justify-content-end">
                                        <button type="button" class="btn btn-light"
                                            data-bs-dismiss="modal">Close</button>
                                        <button id="btnCreate" type="submit" class="btn btn-success w-lg"
                                            wire:loading.attr="disabled">
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
        {{-- end modal buyer --}}

        {{-- modal edit buyer --}}
        <div class="modal fade" id="modal-edit" tabindex="-1" aria-labelledby="modal-editLabel" aria-modal="true"
            wire:ignore.self>
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modal-editLabel">Edit Master Jam Mati Mesin Seitai</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form wire:submit.prevent="update">
                            <div class="row g-3">
                                <div class="col-xxl-12">
                                    <div>
                                        <label for="code" class="form-label">Kode Jam Mati Mesin</label>
                                        <input type="number" class="form-control @error('code') is-invalid @enderror"
                                            oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);"
                                            id="code" wire:model.defer="code" placeholder="Kode">
                                        @error('code')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-xxl-12">
                                    <div>
                                        <label for="name" class="form-label">Nama Jam Mati Mesin</label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                                            id="name" wire:model.defer="name" placeholder="Nama">
                                        @error('name')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                {{-- Klasifikasi --}}
                                <div class="col-xxl-12">
                                    <div>
                                        <label for="classification" class="form-label">Klasifikasi</label>
                                        <input type="text"
                                            class="form-control @error('classification') is-invalid @enderror"
                                            id="classification" wire:model.defer="classification"
                                            placeholder="Klasifikasi">
                                        @error('classification')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                {{-- status --}}
                                <div x-data="{ isVisible: $wire.entangle('statusIsVisible') }">
                                    <div class="col-xxl-12" x-show="isVisible">
                                        <div>
                                            <label for="status" class="form-label">Status</label>
                                            <select class="form-select" wire:model="status">
                                                <option value="0" {{ $status == '0' ? 'selected' : '' }}>
                                                    Inactive</option>
                                                <option value="1" {{ $status == '1' ? 'selected' : '' }}>
                                                    Active</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="hstack gap-2 justify-content-end">
                                        <button type="button" class="btn btn-light"
                                            data-bs-dismiss="modal">Close</button>
                                        <button id="btnCreate" type="submit" class="btn btn-success w-lg"
                                            wire:loading.attr="disabled">
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
        {{-- end modal buyer --}}
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
                                <h4>Are you sure ?</h4>
                                <p class="text-muted mx-4 mb-0">Are you sure you want to remove this order ?
                                </p>
                            </div>
                        </div>
                        <div class="d-flex gap-2 justify-content-center mt-4 mb-2">
                            <button type="button" class="btn w-sm btn-light" data-bs-dismiss="modal">Close</button>
                            <button wire:click="destroy" id="btnCreate" type="button" class="btn w-sm btn-danger"
                                id="remove-item" wire:loading.attr="disabled">
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
    </div>
</div>

@script
    <script>
        $wire.on('showModalCreate', () => {
            $('#modal-add').modal('show');
        });
        // close modal create buyer
        $wire.on('closeModalCreate', () => {
            $('#modal-add').modal('hide');
        });

        // Show modal update buyer
        $wire.on('showModalUpdate', () => {
            $('#modal-edit').modal('show');
        });

        // close modal update buyer
        $wire.on('closeModalUpdate', () => {
            $('#modal-edit').modal('hide');
        });

        // show modal delete buyer
        $wire.on('showModalDelete', () => {
            $('#removeBuyerModal').modal('show');
        });

        // close modal delete buyer
        $wire.on('closeModalDelete', () => {
            $('#removeBuyerModal').modal('hide');
        });

        // datatable
        $wire.on('initDataTable', () => {
            initDataTable('boxTable');
        });

        // Fungsi untuk menginisialisasi ulang DataTable
        function initDataTable(id) {

            const savedOrder = $wire.get('sortingTable');

            let defaultOrder = [
                [1, "asc"]
            ];
            if (savedOrder) {
                defaultOrder = savedOrder;
            }
            // Hapus DataTable jika sudah ada
            if ($.fn.dataTable.isDataTable('#' + id)) {
                let table = $('#' + id).DataTable();
                table.clear();
                table.destroy();
            }

            setTimeout(() => {
                // Inisialisasi ulang DataTable
                let table = $('#' + id).DataTable({
                    "pageLength": 10,
                    "searching": true,
                    "responsive": true,
                    "scrollX": true,
                    "order": defaultOrder,
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

                // Listen to sort event
                table.on('order.dt', function() {
                    let order = table.order();
                    if (order.length == 0 && defaultOrder.length > 0) {
                        order = defaultOrder;
                    }
                    $wire.call('updateSortingTable', order);
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
