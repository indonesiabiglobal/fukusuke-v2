<div class="row">
    <div class="col-lg-12 mt-2">
        <div class="row">
            <div class="col-12 col-lg-7">
                <div class="row">
                    <div class="col-12 col-lg-3">
                        <label class="form-label text-muted fw-bold">Search</label>
                    </div>
                    <div class="col-12 col-lg-9">
                        <div class="input-group">
                            <input wire:model.defer="searchTerm" class="form-control"style="padding:0.44rem" type="text" placeholder="search nama produk, nomor order" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-5">
                <div class="row">
                    <div class="col-12 col-lg-2">
                        <label for="productType" class="form-label text-muted fw-bold">Tipe Produk</label>
                    </div>
                    <div class="col-12 col-lg-10">
                        <div class="mb-1" wire:ignore>
                            <select class="form-control"  wire:model.defer="product_type_id" id="productType" name="productType" data-choices data-choices-sorting-false data-choices-removeItem>
                                <option value="">- All -</option>
                                @foreach (\App\Models\MsProductType::select('id', 'name')->get() as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
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
                {{-- Button Add product --}}
                <button type="button" class="btn btn-success w-lg p-1" onclick="window.location.href='{{ route('add-master-product') }}'">
                    <i class="ri-add-line"> </i> Add
                </button>

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
                {{-- end modal delete product --}}
            </div>
            <div class="col-12 col-lg-6 text-end">
                <button class="btn btn-primary w-lg p-1" wire:click="download" type="button">
                    <span wire:loading.remove wire:target="download">
                        <i class="ri-download-cloud-2-line"> </i> Download
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
                </button>
                <button class="btn btn-info w-lg p-1" wire:click="print" type="button">
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
                </button>
            </div>
        </div>
    </div>
    <div class="table-responsive table-card mt-3 mb-1">
        <table class="table align-middle table-nowrap" id="customerTable" style="width:100%">
            <thead class="table-light">
                <tr>
                    <th>Action</th>
                    <th class="sort">Nama Produk</th>
                    <th class="sort">Nomor Order</th>
                    <th class="sort">Kode Tipe</th>
                    <th class="sort">Jenis Tipe</th>
                    <th class="sort">Dimensi (T*L*P)</th>
                    <th class="sort">Berat Satuan</th>
                    <th class="sort">Katanuki</th>
                    <th class="sort">Warna Font</th>
                    <th class="sort">Warna Back</th>
                    <th class="sort">Status</th>
                    <th class="sort">Updated By</th>
                    <th class="sort">Updated</th>
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
                            <button type="button" class="btn fs-15 p-1 bg-danger rounded modal-delete"
                                wire:click="delete({{ $item->id }})">
                                <i class="ri-delete-bin-line  text-white"></i>
                            </button>
                        </td>
                        <td>{{ $item->product_name }}</td>
                        <td>{{ $item->product_code }}</td>
                        <td>{{ $item->product_type_id }}</td>
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
                        <td>{{ $item->updated_on }}</td>
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
        {{ $data->links() }}
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

        $(document).ready(function() {
            $('#product_group_id').select2();
            // $('#product_group_id').on('change', function(e) {
            //     var data = $('#product_group_id').select2("val");
            //     @this.set('selected', data);
            // });
        });
    </script>
@endscript
