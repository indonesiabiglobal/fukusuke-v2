<div class="row">
    <div class="col-lg-12 mt-2">
        <div class="row">

            <div class="col-12 col-lg-6">
                {{-- <button class="btn btn-primary w-lg p-1" wire:click="download" type="button">
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
                                        {{-- Nomor machine --}}
                                        <div class="col-xxl-12">
                                            <div>
                                                <label for="machineno" class="form-label">Nomor Machine</label>
                                                <input type="number"
                                                    class="form-control @error('machineno') is-invalid @enderror"
                                                    id="machineno" wire:model.defer="machineno"
                                                    placeholder="Kode/Nomor">
                                                @error('machineno')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        {{-- nama machine --}}
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
                                        {{-- Departemen Mesin --}}
                                        <div class="col-xxl-12">
                                            <div class="row">
                                                <label for="Departemen Mesin" class="form-label">Departemen
                                                    Mesin</label>
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
                                        {{-- Jenis Produk --}}
                                        <div class="col-xxl-12">
                                            <div class="row">
                                                <label for="Jenis Produk" class="form-label">Jenis Produk</label>
                                                <select data-choices data-choices-sorting="true" id="product_group_id"
                                                    class="form-control @error('product_group_id') is-invalid @enderror"
                                                    wire:model="product_group_id" placeholder="">
                                                    <option value="" selected>
                                                        Silahkan Pilih
                                                    </option>
                                                    @foreach (\App\Models\MsProductGroup::select('id', 'name')->get() as $productGroup)
                                                        <option value="{{ $productGroup->id }}">
                                                            {{ $productGroup->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('product_group_id')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        {{-- Kapasitas per Jam (kg) --}}
                                        <div class="col-xxl-12">
                                            <div> <label for="capacity_kg" class="form-label">Kapasitas per Jam
                                                    (kg)</label>
                                                <input type="number"
                                                    class="form-control @error('capacity_kg') is-invalid @enderror"
                                                    id="capacity_kg" wire:model.defer="capacity_kg"
                                                    placeholder="Kapasitas per Jam (kg)">
                                                @error('capacity_kg')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        {{-- Kapasitas per Jam (meter/lembar) --}}
                                        <div class="col-xxl-12">
                                            <div> <label for="capacity_lembar" class="form-label">Kapasitas per Jam
                                                    (meter/lembar)</label>
                                                <input type="number"
                                                    class="form-control @error('capacity_lembar') is-invalid @enderror"
                                                    id="capacity_lembar" wire:model.defer="capacity_lembar"
                                                    placeholder="Kapasitas per Jam (meter/lembar)">
                                                @error('capacity_lembar')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        {{-- Size --}}
                                        <div class="col-xxl-12">
                                            <div> <label for="capacity_size" class="form-label">Size</label>
                                                <input type="number"
                                                    class="form-control @error('capacity_size') is-invalid @enderror"
                                                    id="capacity_size" wire:model.defer="capacity_size"
                                                    placeholder="Size">
                                                @error('capacity_size')
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
                                <h5 class="modal-title" id="modal-editLabel">Edit Master Machine</h5> <button
                                    type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form wire:submit.prevent="update">
                                    <div class="row g-3">
                                        {{-- Nomor machine --}}
                                        <div class="col-xxl-12">
                                            <div>
                                                <label for="machineno" class="form-label">Nomor Machine</label>
                                                <input type="number"
                                                    class="form-control @error('machineno') is-invalid @enderror"
                                                    id="machineno" wire:model.defer="machineno"
                                                    placeholder="Kode/Nomor">
                                                @error('machineno')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        {{-- nama machine --}}
                                        <div class="col-xxl-12">
                                            <div>
                                                <label for="machinename" class="form-label">Nama Mesin</label>
                                                <input type="text"
                                                    class="form-control @error('machinename') is-invalid @enderror"
                                                    id="machinename" wire:model.defer="machinename"
                                                    placeholder="Nama">
                                                @error('machinename')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        {{-- Departemen Mesin --}}
                                        <div class="col-xxl-12">
                                            <div class="row">
                                                <label for="Departemen Mesin" class="form-label">Departemen
                                                    Mesin</label>
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
                                        {{-- Jenis Produk --}}
                                        <div class="col-xxl-12">
                                            <div class="row">
                                                <label for="Jenis Produk" class="form-label">Jenis Produk</label>
                                                <select data-choices data-choices-sorting="true" id="product_group_id"
                                                    class="form-control @error('product_group_id') is-invalid @enderror"
                                                    wire:model="product_group_id" placeholder="">
                                                    <option value="" selected>
                                                        Silahkan Pilih
                                                    </option>
                                                    @foreach (\App\Models\MsProductGroup::select('id', 'name')->get() as $productGroup)
                                                        <option value="{{ $productGroup->id }}">
                                                            {{ $productGroup->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('product_group_id')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        {{-- Kapasitas per Jam (kg) --}}
                                        <div class="col-xxl-12">
                                            <div> <label for="capacity_kg" class="form-label">Kapasitas per Jam
                                                    (kg)</label>
                                                <input type="number"
                                                    class="form-control @error('capacity_kg') is-invalid @enderror"
                                                    id="capacity_kg" wire:model.defer="capacity_kg"
                                                    placeholder="Kapasitas per Jam (kg)">
                                                @error('capacity_kg')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        {{-- Kapasitas per Jam (meter/lembar) --}}
                                        <div class="col-xxl-12">
                                            <div> <label for="capacity_lembar" class="form-label">Kapasitas per Jam
                                                    (meter/lembar)</label>
                                                <input type="number"
                                                    class="form-control @error('capacity_lembar') is-invalid @enderror"
                                                    id="capacity_lembar" wire:model.defer="capacity_lembar"
                                                    placeholder="Kapasitas per Jam (meter/lembar)">
                                                @error('capacity_lembar')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        {{-- Size --}}
                                        <div class="col-xxl-12">
                                            <div> <label for="capacity_size" class="form-label">Size</label>
                                                <input type="number"
                                                    class="form-control @error('capacity_size') is-invalid @enderror"
                                                    id="capacity_size" wire:model.defer="capacity_size"
                                                    placeholder="Size">
                                                @error('capacity_size')
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
            <div class="col-12 col-lg-6">
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
            </div>
        </div>
    </div>
    <div class="table-responsive table-card mt-3 mb-1">
        <table class="table align-middle table-nowrap" id="customerTable" style="width:100%">
            <thead class="table-light">
                <tr>
                    <th>Action</th>
                    <th class="sort">Nama Mesin</th>
                    <th class="sort">No Mesin</th>
                    <th class="sort">Departemen</th>
                    <th class="sort">Jenis Produk</th>
                    <th class="sort">Kapasitas (Kg)</th>
                    <th class="sort">Kapasitas (Qty)</th>
                    <th class="sort">Status</th>
                    <th class="sort">Updated By</th>
                    <th class="sort">Updated</th>
                    {{-- <th class="sort">No.</th> --}}
                </tr>
            </thead>
            <tbody class="list form-check-all">
                @forelse ($data as $item)
                    <tr>
                        <td>
                            <button type="button" class="btn fs-15 p-1 bg-primary rounded" wire:click="edit({{ $item->id }})">
                                <i class="ri-edit-box-line text-white"></i>
                            </button>
                            <button type="button" class="btn fs-15 p-1 bg-danger rounded modal-delete"
                                wire:click="delete({{ $item->id }})">
                                <i class="ri-delete-bin-line  text-white"></i>
                            </button>
                        </td>
                        <td>{{ $item->machinename }}</td>
                        <td>{{ $item->machineno }}</td>
                        <td>{{ $item->departmentname }}</td>
                        <td>{{ $item->productgroupname }}</td>
                        <td>{{ $item->capacity_kg }}</td>
                        <td>{{ $item->capacity_lembar }}</td>
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

        $(document).ready(function() {
            $('#product_group_id').select2();
            // $('#product_group_id').on('change', function(e) {
            //     var data = $('#product_group_id').select2("val");
            //     @this.set('selected', data);
            // });
        });
    </script>
@endscript
