<div class="row">
    <div class="col-lg-12 mt-2">
        <div class="row">
            <div class="col-12 col-lg-6">
                {{-- start modal delete product --}}
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
                                        <p class="text-muted mx-4 mb-0">Are you sure you want to remove this product
                                            ?
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
                {{-- end modal delete product --}}
            </div>
            <div class="row justify-content-between">

                {{-- <form wire:submit.prevent="search" class="row justify-content-between"> --}}
                {{-- filter search --}}
                {{-- <div class="col-12 col-lg-7">
                    <div class="row">
                        <div class="col-12 col-lg-3">
                            <label class="form-label text-muted fw-bold">Search</label>
                        </div>
                        <div class="col-12 col-lg-9">
                            <div class="input-group">
                                <input wire:model.defer="searchTerm" class="form-control"style="padding:0.44rem"
                                    type="text" placeholder="search nama produk, nomor order" />
                            </div>
                        </div>
                    </div>
                </div> --}}
                {{-- filter tipe produk --}}
                <div class="col-12 col-lg-6">
                    <div class="row">
                        <div class="col-12 col-lg-2">
                            <label for="productType" class="form-label text-muted fw-bold">Tipe Produk</label>
                        </div>
                        <div class="col-12 col-lg-10">
                            <div class="mb-1" wire:ignore>
                                <select class="form-control" wire:model.defer="product_type_id" id="productType"
                                    name="productType" data-choices data-choices-sorting-false  data-choices-removeItem data-choices-search-field-label>
                                    <option value="">- All -</option>
                                    @foreach (\App\Models\MsProductType::select('id', 'name', 'code')->get() as $item)
                                        <option value="{{ $item->id }}"
                                            data-custom-properties='{"code": "{{ $item->code }}"}'>{{ $item->name }}
                                            , {{ $item->code }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-6 text-end">
                    <button wire:click="search" type="submit" class="btn btn-primary btn-load w-lg p-1" wire:loading.attr="disabled">
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
                    {{-- Button Add product --}}
                    <button type="button" class="btn btn-success w-lg p-1"
                        onclick="window.location.href='{{ route('add-master-product') }}'">
                        <i class="ri-add-line"> </i> Add
                    </button>
                    <button class="btn btn-warning w-lg p-1" wire:click="export" type="button" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="export">
                            <i class="ri-download-cloud-2-line"> </i> Export
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
            {{-- </form> --}}
        </div>
    </div>
    <div class="table-responsive table-card mt-3 mb-1">
        {{-- toggle column table --}}
        <div class="col-12">
            <div class="col text-end dropdown">
                <button type="button" data-bs-toggle="dropdown" aria-expanded="false"
                    class="btn btn-soft-primary btn-icon fs-14 mt-2">
                    <i class="ri-grid-fill"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <label style="cursor: pointer;">
                            <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="1"
                                checked> Nama Produk
                        </label>
                    </li>
                    <li>
                        <label style="cursor: pointer;">
                            <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="2"
                                checked> Nomor Order
                        </label>
                    </li>
                    <li>
                        <label style="cursor: pointer;">
                            <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="3"
                                checked> Kode Tipe
                        </label>
                    </li>
                    <li>
                        <label style="cursor: pointer;">
                            <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="4"
                                checked> Jenis Tipe
                        </label>
                    </li>
                    <li>
                        <label style="cursor: pointer;">
                            <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="5"
                                checked> Dimensi (T*L*P)
                        </label>
                    </li>
                    <li>
                        <label style="cursor: pointer;">
                            <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="6"
                                checked> Berat Satuan
                        </label>
                    </li>
                    <li>
                        <label style="cursor: pointer;">
                            <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="7"
                                checked> Katanuki
                        </label>
                    </li>
                    <li>
                        <label style="cursor: pointer;">
                            <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="8"
                                checked> Warna Font
                        </label>
                    </li>
                    <li>
                        <label style="cursor: pointer;">
                            <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="9"
                                checked> Warna Back
                        </label>
                    </li>
                    <li>
                        <label style="cursor: pointer;">
                            <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="10"
                                checked> Status
                        </label>
                    </li>
                    <li>
                        <label style="cursor: pointer;">
                            <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="11"
                                checked> Updated By
                        </label>
                    </li>
                    <li>
                        <label style="cursor: pointer;">
                            <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="12"
                                checked> Updated
                        </label>
                    </li>
                </ul>
            </div>
        </div>
        <table class="table align-middle" id="productTable">
            <thead class="table-light">
                <tr>
                    <th style="width: 100px">Action</th>
                    <th>Nama Produk</th>
                    <th>Nomor Order</th>
                    <th>Kode Tipe</th>
                    <th>Jenis Tipe</th>
                    <th>Dimensi (T*L*P)</th>
                    <th>Berat Satuan</th>
                    <th>Katanuki</th>
                    <th>Warna Font</th>
                    <th>Warna Back</th>
                    <th>Status</th>
                    <th>Updated By</th>
                    <th>Updated</th>
                </tr>
            </thead>
            <tbody class="list form-check-all">
                @forelse ($data as $item)
                    <tr>
                        <td>
                            <a href="/edit-master-produk?productId={{ $item->id }}">
                                <button type="button" class="btn fs-15 p-1 bg-primary rounded">
                                    <i class="ri-edit-box-line text-white"></i>
                                </button>
                            </a>
                            <button type="button" class="btn fs-15 p-1 bg-danger rounded modal-delete  btn-delete"
                                data-delete-id="{{ $item->id }}" wire:click="delete({{ $item->id }})">
                                <i class="ri-delete-bin-line  text-white"></i>
                            </button>
                        </td>
                        <td>{{ $item->product_name }}</td>
                        <td>{{ $item->product_code }}</td>
                        <td>{{ $item->product_type_code }}</td>
                        <td>{{ $item->product_type_name }}</td>
                        <td>{{ $item->dimensi }}</td>
                        <td>{{ $item->unit_weight }}</td>
                        <td>{{ $item->katanuki_code }}</td>
                        <td>{{ $item->number_of_color }}</td>
                        <td>{{ $item->back_color_number }}</td>
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
        // show modal delete product
        $wire.on('showModalDelete', () => {
            $('#modal-delete').modal('show');
        });
        // close modal delete product
        $wire.on('closeModalDelete', () => {
            $('#modal-delete').modal('hide');
        });


        // datatable
        $wire.on('initDataTable', () => {
            initDataTable('productTable');
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
                    // "order": [
                    //     [2, "asc"]
                    // ],
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
