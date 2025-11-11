<div>
    {{-- @include('layouts.customizer') --}}
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Security Management</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="/">Administration</a></li>
                        <li class="breadcrumb-item active">Security Management</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

<div class="row">
    <div class="col-12 col-lg-8">
        <div class="row">
            <div class="col-12 col-lg-1">
                <label for="product" class="form-label text-muted fw-bold">Roles</label>
            </div>
            <div class="col-12 col-lg-6">
                <div class="mb-1" wire:ignore>
                    <select class="form-control" wire:model.defer="idRole" data-choices data-choices-sorting-false data-choices-removeItem data-choices-search-field-label>
                        <option value="">- All -</option>
                        @foreach ($userrole as $item)
                            <option value="{{ $item->id }}">{{ $item->description }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-12 col-lg-1">
                <label for="status" class="form-label text-muted fw-bold">Status</label>
            </div>
            <div class="col-12 col-lg-4">
                <div class="mb-1" wire:ignore>
                    <select class="form-control" wire:model.defer="status" data-choices data-choices-sorting-false data-choices-removeItem data-choices-search-field-label>
                        <option value="">- All -</option>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-12 mt-2">
        <div class="row">
            <div class="col-12 col-lg-6">
                <button wire:click="search" type="button" class="btn btn-primary btn-load w-lg p-1" wire:loading.attr="disabled">
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
                    onclick="window.location.href='/add-user'">
                    <i class="ri-add-line"> </i> Add
                </button>
            </div>
        </div>
    </div>
    <div class="table-responsive table-card mt-3 mb-2">
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
                            checked> User Name
                    </label>
                </li>
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="2"
                            checked> Email
                    </label>
                </li>
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="3"
                            checked> Employee Name
                    </label>
                </li>
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="4"
                            checked> Job Title
                    </label>
                </li>
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="5"
                            checked> Status
                    </label>
                </li>
            </ul>
        </div>
        <table class="table align-middle table-nowrap" id="securityTable" style="width:100%">
            <thead class="table-light">
                <tr>
                    <th>Action</th>
                    <th>User Name</th>
                    <th>Email</th>
                    <th>Employee Name</th>
                    <th>Job Title</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody class="list form-check-all">
                @php
                    $no = 1;
                @endphp
                @forelse ($data as $item)
                    <tr>
                        <td>
                            <a href="/edit-user?orderId={{ $item->id }}" class="btn link-success fs-15 p-1 bg-primary rounded">
                                <i class="ri-edit-box-line text-white"></i>
                            </a>
                            <button type="button" class="btn fs-15 ms-1 p-1 bg-danger removeBuyerModal"
                                wire:click="setDeleteId({{ $item->id }})"
                                data-bs-toggle="modal" data-bs-target="#removeBuyerModal">
                                <i class="ri-delete-bin-line text-white"></i>
                            </button>
                        </td>
                        <td>{{ $item->username }}</td>
                        <td>{{ $item->email }}</td>
                        <td>{{ $item->empname }}</td>
                        <td>{{ $item->job }}</td>
                        <td>
                            @if($item->status == 'Active')
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-danger">Inactive</span>
                            @endif
                        </td>
                    </tr>
                @empty
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Remove Modal -->
<div id="removeBuyerModal" class="modal fade zoomIn" tabindex="-1" aria-hidden="true" wire:ignore.self>
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
                        <p class="text-muted mx-4 mb-0">Are you sure you want to remove this user?</p>
                    </div>
                </div>
                <div class="d-flex gap-2 justify-content-center mt-4 mb-2">
                    <button type="button" class="btn w-sm btn-light"
                        data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn w-sm btn-danger"
                        wire:click="delete"
                        data-bs-dismiss="modal">Yes, Delete It!</button>
                </div>
            </div>
        </div>
    </div>
</div>

@script
    <script>
        // datatable
        // inisialisasi DataTable
        $wire.on('initDataTable', () => {
            initDataTable();
        });

        // Close modal after delete
        $wire.on('closeModal', () => {
            var modal = bootstrap.Modal.getInstance(document.getElementById('removeBuyerModal'));
            if (modal) {
                modal.hide();
            }
        });

        // Show toast notification
        $wire.on('showToast', (data) => {
            const [event] = data;
            if (event.type === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: event.message,
                    showConfirmButton: false,
                    timer: 2000
                });
            } else if (event.type === 'error') {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: event.message,
                    showConfirmButton: true
                });
            }
        });

        // Fungsi untuk menginisialisasi ulang DataTable
        function initDataTable() {
            const savedOrder = $wire.get('sortingTable');

            let defaultOrder = [[1, "asc"]];
            if (savedOrder) {
                defaultOrder = savedOrder;
            }

            // Hapus DataTable jika sudah ada
            if ($.fn.dataTable.isDataTable('#securityTable')) {
                let table = $('#securityTable').DataTable();
                table.clear();
                table.destroy();
            }

            setTimeout(() => {
                // Inisialisasi ulang DataTable
                let table = $('#securityTable').DataTable({
                    "pageLength": 10,
                    "searching": true,
                    "responsive": true,
                    "order": defaultOrder,
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

@if (session()->has('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: '{{ session('success') }}',
                showConfirmButton: false,
                timer: 2000
            });
        });
    </script>
@endif

@if (session()->has('error'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: '{{ session('error') }}',
                showConfirmButton: true
            });
        });
    </script>
@endif
</div>
