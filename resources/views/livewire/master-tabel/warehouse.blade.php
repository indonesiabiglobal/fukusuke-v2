<div class="row">
    <div class="col-lg-12 mt-2">
        <div class="row">

            <div class="col-12 col-lg-6">
                {{-- Button Add warehouse --}}
                <button type="button" class="btn btn-success w-lg p-1" wire:click="showModalCreate">
                    <i class="ri-add-line"> </i> Add
                </button>
                {{-- modal add warehouse --}}
                <div class="modal fade" id="modal-add" tabindex="-1" aria-labelledby="modal-addLabel" aria-modal="true"
                    wire:ignore.self>
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modal-addLabel">Add Master Warehouse</h5> <button
                                    type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form wire:submit.prevent="store">
                                    <div class="row g-3">
                                        {{-- nama warehouse --}}
                                        <div class="col-xxl-12">
                                            <div>
                                                <label for="name" class="form-label">Nama Warehouse</label>
                                                <input type="text"
                                                    class="form-control @error('name') is-invalid @enderror"
                                                    id="name" wire:model.defer="name" placeholder="Nama">
                                                @error('name')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        {{-- Deskripsi warehouse --}}
                                        <div class="col-xxl-12">
                                            <div> <label for="description" class="form-label">Deskripsi</label>
                                                <input type="text"
                                                    class="form-control @error('description') is-invalid @enderror"
                                                    id="description" wire:model.defer="description"
                                                    placeholder="Deskripsi">
                                                @error('description')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        {{-- alamat warehouse --}}
                                        <div class="col-xxl-12">
                                            <div> <label for="address" class="form-label">Alamat</label>
                                                <textarea class="form-control @error('address') is-invalid @enderror" id="address" wire:model.defer="address"
                                                    placeholder="Alamat"></textarea>
                                                @error('address')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        {{-- kota warehouse --}}
                                        <div class="col-xxl-12">
                                            <div> <label for="city" class="form-label">Kota</label>
                                                <input type="text"
                                                    class="form-control @error('city') is-invalid @enderror"
                                                    id="city" wire:model.defer="city" placeholder="Kota">
                                                @error('city')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        {{-- Provinsi warehouse --}}
                                        <div class="col-xxl-12">
                                            <div> <label for="province" class="form-label">Provinsi</label>
                                                <input type="text"
                                                    class="form-control @error('province') is-invalid @enderror"
                                                    id="province" wire:model.defer="province" placeholder="Provinsi">
                                                @error('province')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        {{-- negara warehouse --}}
                                        <div class="col-xxl-12">
                                            <div> <label for="country" class="form-label">Negara</label>
                                                <input type="text"
                                                    class="form-control @error('country') is-invalid @enderror"
                                                    id="country" wire:model.defer="country" placeholder="Negara">
                                                @error('country')
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
                                                    <span wire:loading.remove wire:target="store" wire:loading.attr="disabled">
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
                {{-- end modal warehouse --}}

                {{-- modal add warehouse --}}
                <div class="modal fade" id="modal-edit" tabindex="-1" aria-labelledby="modal-editLabel"
                    aria-modal="true" wire:ignore.self>
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modal-editLabel">Edit Master Warehouse</h5> <button
                                    type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form wire:submit.prevent="update">
                                    <div class="row g-3">
                                        {{-- nama warehouse --}}
                                        <div class="col-xxl-12">
                                            <div>
                                                <label for="name" class="form-label">Nama Warehouse</label>
                                                <input type="text"
                                                    class="form-control @error('name') is-invalid @enderror"
                                                    id="name" wire:model.defer="name" placeholder="Nama">
                                                @error('name')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        {{-- Deskripsi warehouse --}}
                                        <div class="col-xxl-12">
                                            <div> <label for="description" class="form-label">Deskripsi</label>
                                                <input type="text"
                                                    class="form-control @error('description') is-invalid @enderror"
                                                    id="description" wire:model.defer="description"
                                                    placeholder="Deskripsi">
                                                @error('description')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        {{-- alamat warehouse --}}
                                        <div class="col-xxl-12">
                                            <div> <label for="address" class="form-label">Alamat</label>
                                                <textarea class="form-control @error('address') is-invalid @enderror" id="address" wire:model.defer="address"
                                                    placeholder="Alamat"></textarea>
                                                @error('address')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        {{-- kota warehouse --}}
                                        <div class="col-xxl-12">
                                            <div> <label for="city" class="form-label">Kota</label>
                                                <input type="text"
                                                    class="form-control @error('city') is-invalid @enderror"
                                                    id="city" wire:model.defer="city" placeholder="Kota">
                                                @error('city')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        {{-- Provinsi warehouse --}}
                                        <div class="col-xxl-12">
                                            <div> <label for="province" class="form-label">Provinsi</label>
                                                <input type="text"
                                                    class="form-control @error('province') is-invalid @enderror"
                                                    id="province" wire:model.defer="province"
                                                    placeholder="Provinsi">
                                                @error('province')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        {{-- negara warehouse --}}
                                        <div class="col-xxl-12">
                                            <div> <label for="country" class="form-label">Negara</label>
                                                <input type="text"
                                                    class="form-control @error('country') is-invalid @enderror"
                                                    id="country" wire:model.defer="country" placeholder="Negara">
                                                @error('country')
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
                                        {{-- button --}}
                                        <div class="col-lg-12">
                                            <div class="hstack gap-2 justify-content-end">
                                                <button type="button" class="btn btn-light"
                                                    data-bs-dismiss="modal">Close</button>
                                                <button id="btnCreate" type="submit" class="btn btn-success w-lg" wire:loading.attr="disabled">
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
                {{-- end modal warehouse --}}


                {{-- start modal delete warehouse --}}
                <div id="removeWarehouseModal" class="modal fade zoomIn" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                                    id="close-removeWarehouseModal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mt-2 text-center">
                                    <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop"
                                        colors="primary:#f7b84b,secondary:#f06548"
                                        style="width:100px;height:100px"></lord-icon>
                                    <div class="mt-4 pt-2 fs-15 mx-4 mx-sm-5">
                                        <h4>Are you sure ?</h4>
                                        <p class="text-muted mx-4 mb-0">Are you sure you want to remove this warehouse?
                                        </p>
                                    </div>
                                </div>
                                <div class="d-flex gap-2 justify-content-center mt-4 mb-2">
                                    <button type="button" class="btn w-sm btn-light"
                                        data-bs-dismiss="modal">Close</button>
                                    <button wire:click="destroy" id="btnCreate" type="button"
                                        class="btn w-sm btn-danger" id="remove-item" wire:loading.attr="disabled">
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
                {{-- end modal delete warehouse --}}
            </div>

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
                                    data-column="1" checked> Warehouse
                            </label>
                        </li>
                        <li>
                            <label style="cursor: pointer;">
                                <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox"
                                    data-column="2" checked> Alamat
                            </label>
                        </li>
                        <li>
                            <label style="cursor: pointer;">
                                <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox"
                                    data-column="3" checked> Kota
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
    <div class="table-responsive table-card mt-3 mb-1">
        <table class="table align-middle table-nowrap" id="warehouseTable" style="width:100%">
            <thead class="table-light">
                <tr>
                    <th>Action</th>
                    <th>Warehouse</th>
                    <th>Alamat</th>
                    <th>Kota</th>
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
                                data-edit-id="{{ $item->id }}" data-bs-toggle="modal"
                                data-bs-target="#modal-edit" wire:click="edit({{ $item->id }})">
                                <i class="ri-edit-box-line text-white"></i>
                            </button>
                            <button {{ $item->status == 0 ? 'hidden' : '' }} type="button"
                                class="btn fs-15 p-1 bg-danger rounded removeWarehouseModal  btn-delete"
                                data-delete-id="{{ $item->id }}" wire:click="delete({{ $item->id }})">
                                <i class="ri-delete-bin-line  text-white"></i>
                            </button>
                        </td>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->address }}</td>
                        <td>{{ $item->city }}</td>
                        <td>
                            {!! $item->status == 1
                                ? '<span class="badge text-success bg-success-subtle">Active</span>'
                                : '<span class="badge text-bg-danger">Non Active</span>' !!}
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
        // close modal create warehouse
        $wire.on('closeModalCreate', () => {
            $('#modal-add').modal('hide');
        });

        // close modal update warehouse
        $wire.on('closeModalUpdate', () => {
            $('#modal-edit').modal('hide');
        });

        // show modal delete warehouse
        $wire.on('showModalDelete', () => {
            $('#removeWarehouseModal').modal('show');
        });

        // close modal delete warehouse
        $wire.on('closeModalDelete', () => {
            $('#removeWarehouseModal').modal('hide');
        });

        // datatable
        $wire.on('initDataTable', () => {
            initDataTable('warehouseTable');
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
            }, 500);
        }
    </script>
@endscript
