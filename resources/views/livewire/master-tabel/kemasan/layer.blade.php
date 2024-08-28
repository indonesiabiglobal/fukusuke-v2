<div class="row">
    <div class="col-lg-12 mt-2">
        <div class="row">
            <div class="col-12 col-lg-6">
                <button type="button" class="btn btn-success w-lg p-1" wire:click="showModalCreate">
                    <i class="ri-add-line"> </i> Add
                </button>
                {{-- modal add buyer --}}
                <div class="modal fade" id="modal-add" tabindex="-1" aria-labelledby="modal-addLabel" aria-modal="true"
                    wire:ignore.self>
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modal-addLabel">Add Master Layer</h5> <button type="button"
                                    class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form wire:submit.prevent="store">
                                    <div class="row g-3">
                                        <div class="col-xxl-12">
                                            <div>
                                                <label for="code" class="form-label">Kode Layer</label>
                                                <input type="number"
                                                    class="form-control @error('code') is-invalid @enderror"
                                                    id="code" wire:model.defer="code" placeholder="Kode">
                                                @error('code')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-xxl-12">
                                            <div>
                                                <label for="name" class="form-label">Nama Layer</label>
                                                <input type="text"
                                                    class="form-control @error('name') is-invalid @enderror"
                                                    id="name" wire:model.defer="name" placeholder="Nama">
                                                @error('name')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-xxl-12">
                                            <div wire:ignore>
                                                <label for="name" class="form-label">Klasifikasi</label>
                                                <select class="form-control @error('box_class') is-invalid @enderror"
                                                    wire:model.defer="box_class" data-choices data-choices-sorting-false
                                                    data-choices-removeItem>
                                                    <option value="">- All -</option>
                                                    <option value="1">Khusus</option>
                                                    <option value="2">Standar</option>
                                                </select>
                                                @error('box_class')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-xxl-12">
                                            <div class="form-group">
                                                <label for="panjang" class="form-label">Panjang</label>
                                                <div class="input-group">
                                                    <input type="text"
                                                        class="form-control @error('panjang') is-invalid @enderror"
                                                        id="panjang" wire:model.defer="panjang"
                                                        placeholder="Ukuran Panjang">
                                                    <span class="input-group-text py-0">
                                                        mm
                                                    </span>
                                                    @error('panjang')
                                                        <span class="invalid-feedback">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xxl-12">
                                            <div class="form-group">
                                                <label for="lebar" class="form-label">Lebar</label>
                                                <div class="input-group">
                                                    <input type="text"
                                                        class="form-control @error('lebar') is-invalid @enderror"
                                                        id="lebar" wire:model.defer="lebar"
                                                        placeholder="Ukuran lebar">
                                                    <span class="input-group-text py-0">
                                                        mm
                                                    </span>
                                                    @error('lebar')
                                                        <span class="invalid-feedback">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xxl-12">
                                            <div class="form-group">
                                                <label for="tinggi" class="form-label">Tinggi</label>
                                                <div class="input-group">
                                                    <input type="text"
                                                        class="form-control @error('tinggi') is-invalid @enderror"
                                                        id="tinggi" wire:model.defer="tinggi"
                                                        placeholder="Ukuran tinggi">
                                                    <span class="input-group-text py-0">
                                                        mm
                                                    </span>
                                                    @error('tinggi')
                                                        <span class="invalid-feedback">{{ $message }}</span>
                                                    @enderror
                                                </div>
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
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="modal-edit" tabindex="-1" aria-labelledby="modal-editLabel"
                    aria-modal="true" wire:ignore.self>
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modal-editLabel">Edit Master Layer</h5> <button
                                    type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form wire:submit.prevent="update">
                                    <div class="row g-3">
                                        <div class="col-xxl-12">
                                            <div>
                                                <label for="code" class="form-label">Kode Layer</label>
                                                <input type="number"
                                                    class="form-control @error('code') is-invalid @enderror"
                                                    id="code" wire:model.defer="code" placeholder="Kode">
                                                @error('code')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-xxl-12">
                                            <div>
                                                <label for="name" class="form-label">Nama Layer</label>
                                                <input type="text"
                                                    class="form-control @error('name') is-invalid @enderror"
                                                    id="name" wire:model.defer="name" placeholder="Nama">
                                                @error('name')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-xxl-12">
                                            <div>
                                                <label for="name" class="form-label">Klasifikasi</label>
                                                <select class="form-control @error('box_class') is-invalid @enderror"
                                                    wire:model.defer="box_class">
                                                    <option value="1"
                                                        {{ ($box_class ?? '') == 1 ? 'selected' : '' }}>Khusus</option>
                                                    <option value="2"
                                                        {{ ($box_class ?? '') == 2 ? 'selected' : '' }}>Standar</option>
                                                </select>
                                                @error('box_class')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-xxl-12">
                                            <div class="form-group">
                                                <label for="panjang" class="form-label">Panjang</label>
                                                <div class="input-group">
                                                    <input type="text"
                                                        class="form-control @error('panjang') is-invalid @enderror"
                                                        id="panjang" wire:model.defer="panjang"
                                                        placeholder="Ukuran Panjang">
                                                    <span class="input-group-text py-0">
                                                        mm
                                                    </span>
                                                    @error('panjang')
                                                        <span class="invalid-feedback">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xxl-12">
                                            <div class="form-group">
                                                <label for="lebar" class="form-label">Lebar</label>
                                                <div class="input-group">
                                                    <input type="text"
                                                        class="form-control @error('lebar') is-invalid @enderror"
                                                        id="lebar" wire:model.defer="lebar"
                                                        placeholder="Ukuran lebar">
                                                    <span class="input-group-text py-0">
                                                        mm
                                                    </span>
                                                    @error('lebar')
                                                        <span class="invalid-feedback">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xxl-12">
                                            <div class="form-group">
                                                <label for="tinggi" class="form-label">Tinggi</label>
                                                <div class="input-group">
                                                    <input type="text"
                                                        class="form-control @error('tinggi') is-invalid @enderror"
                                                        id="tinggi" wire:model.defer="tinggi"
                                                        placeholder="Ukuran tinggi">
                                                    <span class="input-group-text py-0">
                                                        mm
                                                    </span>
                                                    @error('tinggi')
                                                        <span class="invalid-feedback">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
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
            </div>
            {{-- filter search --}}
            {{-- <div class="col-12 col-lg-6">
                <div class="input-group">
                    <input wire:model.defer="searchTerm" class="form-control"style="padding:0.44rem" type="text"
                        placeholder="Pencairan" />
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
                                    data-column="1" checked> Klasifikasi
                            </label>
                        </li>
                        <li>
                            <label style="cursor: pointer;">
                                <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox"
                                    data-column="2" checked> Nama Layer
                            </label>
                        </li>
                        <li>
                            <label style="cursor: pointer;">
                                <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox"
                                    data-column="3" checked> Code
                            </label>
                        </li>
                        <li>
                            <label style="cursor: pointer;">
                                <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox"
                                    data-column="4" checked> Panjang
                            </label>
                        </li>
                        <li>
                            <label style="cursor: pointer;">
                                <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox"
                                    data-column="5" checked> Lebar
                            </label>
                        </li>
                        <li>
                            <label style="cursor: pointer;">
                                <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox"
                                    data-column="6" checked> Tinggi
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
        <table class="table align-middle table-nowrap" id="layerTable" style="width:100%">
            <thead class="table-light">
                <tr>
                    <th>Action</th>
                    <th>Klasifikasi</th>
                    <th>Nama Layer</th>
                    <th>Code</th>
                    <th>Panjang</th>
                    <th>Lebar</th>
                    <th>Tinggi</th>
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
                                data-delete-id="{{ $item->id }}" wire:click="delete({{ $item->id }})">
                                <i class="ri-delete-bin-line text-white"></i>
                            </button>
                        </td>
                        <td>{{ $item->box_class == 1 ? 'Khusus' : 'Standar' }}</td>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->code }}</td>
                        <td>{{ number_format($item->panjang, 2) }}</td>
                        <td>{{ number_format($item->lebar, 2) }}</td>
                        <td>{{ number_format($item->tinggi, 2) }}</td>
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
                                colors="primary:#121331,secondary:#08a88a" style="width:40px;height:40px"></lord-icon>
                            <h5 class="mt-2">Sorry! No Result Found</h5>
                            <p class="text-muted mb-0">We've searched more than 150+ Orders We did not find any orders
                                for you search.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        {{-- {{ $result->links() }} --}}
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
            initDataTable('layerTable');
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
