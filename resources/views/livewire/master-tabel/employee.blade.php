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
                {{-- Button Add employee --}}
                <button type="button" class="btn btn-success w-lg p-1" wire:click="showModalCreate">
                    <i class="ri-add-line"> </i> Add
                </button>
                {{-- modal add employee --}}
                <div class="modal fade" id="modal-add" tabindex="-1" aria-labelledby="modal-addLabel" aria-modal="true"
                    wire:ignore.self>
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modal-addLabel">Add Master Employee</h5> <button
                                    type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form wire:submit.prevent="store">
                                    <div class="row g-3">
                                        {{-- employeeno --}}
                                        <div class="col-xxl-12">
                                            <div>
                                                <label for="employeeno" class="form-label">NIK</label>
                                                <input type="text"
                                                    class="form-control @error('employeeno') is-invalid @enderror"
                                                    id="employeeno" wire:model.defer="employeeno"
                                                    placeholder="Nomor Induk Karyawan" maxlength="8"
                                                    style="text-transform: uppercase;"
                                                    oninput="this.value = this.value.toUpperCase();" >
                                                @error('employeeno')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        {{-- nama Karyawan --}}
                                        <div class="col-xxl-12">
                                            <div>
                                                <label for="empname" class="form-label">Nama Karyawan</label>
                                                <input type="text"
                                                    class="form-control @error('empname') is-invalid @enderror"
                                                    id="empname" wire:model.defer="empname" placeholder="Nama">
                                                @error('empname')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        {{-- Departemen Karyawan --}}
                                        <div class="col-xxl-12">
                                            <div class="row" wire:ignore>
                                                <label for="Departemen Karyawan" class="form-label">Departemen
                                                    Karyawan</label>
                                                <select data-choices data-choices-sorting="true"
                                                    class="form-select @error('department_id') is-invalid @enderror"
                                                    wire:model="department_id" placeholder="">
                                                    <option value="" selected>
                                                        Silahkan Pilih
                                                    </option>
                                                    @foreach (\App\Models\MsDepartment::select('id', 'name')->get() as $department)
                                                        <option value="{{ $department->id }}">
                                                            {{ $department->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('department_id')
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
                {{-- end modal employee --}}

                {{-- modal edit employee --}}
                <div class="modal fade" id="modal-edit" tabindex="-1" aria-labelledby="modal-editLabel"
                    aria-modal="true" wire:ignore.self>
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modal-editLabel">Edit Master Employee</h5> <button
                                    type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form wire:submit.prevent="update">
                                    <div class="row g-3">
                                        {{-- employeeno --}}
                                        <div class="col-xxl-12">
                                            <div>
                                                <label for="employeeno" class="form-label">NIK</label>
                                                <input type="text"
                                                    class="form-control @error('employeeno') is-invalid @enderror"
                                                    id="employeeno" wire:model.defer="employeeno"
                                                    placeholder="Nomor Induk Karyawan" maxlength="8"
                                                    style="text-transform: uppercase;"
                                                    oninput="this.value = this.value.toUpperCase();">
                                                @error('employeeno')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        {{-- nama employee --}}
                                        <div class="col-xxl-12">
                                            <div>
                                                <label for="empname" class="form-label">Nama Karyawan</label>
                                                <input type="text"
                                                    class="form-control @error('empname') is-invalid @enderror"
                                                    id="empname" wire:model.defer="empname" placeholder="Nama">
                                                @error('empname')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        {{-- Departemen Karyawan --}}
                                        <div class="col-xxl-12">
                                            <div class="row">
                                                <label for="Departemen Karyawan" class="form-label">Departemen
                                                    Karyawan</label>
                                                <select
                                                    class="form-select select2 @error('department_id') is-invalid @enderror"
                                                    wire:model="department_id" placeholder="" id="department_id">
                                                    <option value="" selected>
                                                        Silahkan Pilih
                                                    </option>
                                                    @foreach (\App\Models\MsDepartment::select('id', 'name')->get() as $department)
                                                        <option value="{{ $department->id }}"
                                                            {{ $department->id == $department_id ? 'selected' : '' }}>
                                                            {{ $department->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('department_id')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        @if ($status == '0')
                                            <div class="col-xxl-12">
                                                <div wire:ignore>
                                                    <label for="empname" class="form-label">Status</label>
                                                    <select class="form-select" wire:model="status">
                                                        <option value="0" {{ $status == '0' ? 'selected' : '' }}>
                                                            Inactive</option>
                                                        <option value="1" {{ $status == '1' ? 'selected' : '' }}>
                                                            Active</option>
                                                    </select>
                                                </div>
                                            </div>
                                        @endif
                                        
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
                {{-- end modal employee --}}


                {{-- start modal delete employee --}}
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
                                        <p class="text-muted mx-4 mb-0">Are you sure you want to remove this employee
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
                {{-- end modal delete employee --}}
            </div>

            {{-- filter search --}}
            {{-- <div class="col-12 col-lg-6">
                <form wire:submit.prevent="search">
                    <div class="input-group">
                        <input wire:model.defer="searchTerm" class="form-control"style="padding:0.44rem"
                            type="text" placeholder="search kode,nama employee" />
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
                                    data-column="1" checked> Nama
                            </label>
                        </li>
                        <li>
                            <label style="cursor: pointer;">
                                <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox"
                                    data-column="2" checked> NIK
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
                                    data-column="4" checked> Status
                            </label>
                        </li>
                        <li>
                            <label style="cursor: pointer;">
                                <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox"
                                    data-column="5" checked> Updated By
                            </label>
                        </li>
                        <li>
                            <label style="cursor: pointer;">
                                <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox"
                                    data-column="6" checked> Updated
                            </label>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    {{-- Table employee --}}
    <div class="table-responsive table-card mt-3 mb-1">

        <table class="table align-middle table-nowrap" id="employeeTable" style="width:100%">
            <thead class="table-light">
                <tr>
                    <th>Action</th>
                    <th>Nama</th>
                    <th>NIK</th>
                    <th>Departemen</th>
                    <th>Status</th>
                    <th>Updated By</th>
                    <th>Updated</th>
                    {{-- <th>No.</th> --}}
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
                            <button {{ $item->status == 0 ? 'hidden' : '' }} type="button"
                                class="btn fs-15 p-1 bg-danger rounded modal-delete btn-delete"
                                data-delete-id="{{ $item->id }}" wire:click="delete({{ $item->id }})">
                                <i class="ri-delete-bin-line  text-white"></i>
                            </button>
                        </td>
                        <td>{{ $item->empname }}</td>
                        <td>{{ $item->employeeno }}</td>
                        <td>{{ $item->department_name }}</td>
                        <td>
                            {!! $item->status == 1
                                ? '<span class="badge text-success bg-success-subtle">Active</span>'
                                : '<span class="badge text-bg-danger">Inactive</span>' !!}
                        </td>
                        <td>{{ $item->updated_by }}</td>
                        <td>{{ \Carbon\Carbon::parse($item->updated_on)->format('d-M-Y H:i:s') }}</td>
                        {{-- <td>{{ $no++ }}</td> --}}
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
        // close modal create employee
        $wire.on('closeModalCreate', () => {
            $('#modal-add').modal('hide');
        });

        // show modal update employee
        $wire.on('showModalUpdate', () => {
            $('#modal-edit').modal('show');
        });
        // close modal update employee
        $wire.on('closeModalUpdate', () => {
            $('#modal-edit').modal('hide');
        });

        // show modal delete employee
        $wire.on('showModalDelete', () => {
            $('#modal-delete').modal('show');
        });

        // close modal delete employee
        $wire.on('closeModalDelete', () => {
            $('#modal-delete').modal('hide');
        });

        // datatable
        $wire.on('initDataTable', () => {
            initDataTable('employeeTable');
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
            }, 500);
        }
    </script>
@endscript

@push('scripts')
    <script>
        // datatable
        // const table = $('#tableEmployee').DataTable({
        //     "pageLength": 10,
        //     "searching": true,
        //     "responsive": true,
        //     "order": [
        //         [1, "asc"]
        //     ]
        // });

        // // Tambahkan event listener ke setiap checkbox
        // document.querySelectorAll('.toggle-column').forEach(function(checkbox) {
        //     checkbox.addEventListener('change', function() {
        //         let column = table.column($(this).attr('data-column'));
        //         column.visible(!column.visible());
        //     });
        // });
    </script>
@endpush
