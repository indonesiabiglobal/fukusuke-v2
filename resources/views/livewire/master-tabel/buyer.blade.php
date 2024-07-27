<div class="row">
    <div class="col-lg-12 mt-2">
        <div class="row">

            <div class="col-12 col-lg-6">
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
                {{-- Button Add buyer --}}
                <button type="button" class="btn btn-success w-lg p-1" data-bs-toggle="modal"
                    data-bs-target="#modal-addBuyer">
                    <i class="ri-add-line"> </i> Add
                </button>
                {{-- modal add buyer --}}
                <div class="modal fade" id="modal-addBuyer" tabindex="-1" aria-labelledby="modal-addBuyerLabel"
                    aria-modal="true" wire:ignore.self>
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modal-addBuyerLabel">Add Master Buyer</h5> <button
                                    type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form wire:submit.prevent="store">
                                    <div class="row g-3">
                                        {{-- kode buyer --}}
                                        <div class="col-xxl-12">
                                            <div>
                                                <label for="code" class="form-label">Kode Buyer</label>
                                                <input type="number"
                                                    class="form-control @error('code') is-invalid @enderror"
                                                    id="code" wire:model.defer="code" placeholder="Kode">
                                                @error('code')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        {{-- nama buyer --}}
                                        <div class="col-xxl-12">
                                            <div>
                                                <label for="name" class="form-label">Nama Buyer</label>
                                                <input type="text"
                                                    class="form-control @error('name') is-invalid @enderror"
                                                    id="name" wire:model.defer="name" placeholder="Nama">
                                                @error('name')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        {{-- alamat buyer --}}
                                        <div class="col-xxl-12">
                                            <div> <label for="address" class="form-label">Alamat</label>
                                                <textarea class="form-control @error('address') is-invalid @enderror" id="address" wire:model.defer="address"
                                                    placeholder="Alamat"></textarea>
                                                @error('address')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        {{-- kota buyer --}}
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
                                        {{-- negara buyer --}}
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

                {{-- modal add buyer --}}
                <div class="modal fade" id="modal-edit" tabindex="-1" aria-labelledby="modal-editLabel"
                    aria-modal="true" wire:ignore.self>
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modal-editLabel">Add Master Buyer</h5> <button
                                    type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form wire:submit.prevent="update">
                                    <div class="row g-3">
                                        {{-- kode buyer --}}
                                        <div class="col-xxl-12">
                                            <div>
                                                <label for="code" class="form-label">Kode Buyer</label>
                                                <input type="number"
                                                    class="form-control @error('code') is-invalid @enderror"
                                                    id="code" wire:model.defer="code" placeholder="Kode">
                                                @error('code')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        {{-- nama buyer --}}
                                        <div class="col-xxl-12">
                                            <div>
                                                <label for="name" class="form-label">Nama Buyer</label>
                                                <input type="text"
                                                    class="form-control @error('name') is-invalid @enderror"
                                                    id="name" wire:model.defer="name" placeholder="Nama">
                                                @error('name')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        {{-- alamat buyer --}}
                                        <div class="col-xxl-12">
                                            <div> <label for="address" class="form-label">Alamat</label>
                                                <textarea class="form-control @error('address') is-invalid @enderror" id="address" wire:model.defer="address"
                                                    placeholder="Alamat"></textarea>
                                                @error('address')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        {{-- kota buyer --}}
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
                                        {{-- negara buyer --}}
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


                {{-- start modal delete buyer --}}
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
                                    <button type="button" class="btn w-sm btn-danger" id="remove-item"
                                        wire:click="delete">Yes, Delete It!</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- end modal delete buyer --}}
            </div>
            <div class="col-12 col-lg-6">
                <div class="input-group">
                    <input wire:model.defer="searchTerm" class="form-control"style="padding:0.44rem" type="text"
                        placeholder="search nama buyer" />
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
            </div>
        </div>
    </div>
    <div class="table-responsive table-card mt-3 mb-1">
        <table class="table align-middle table-nowrap" id="customerTable" style="width:100%">
            <thead class="table-light">
                <tr>
                    <th>Action</th>
                    <th class="sort">Kode Buyer</th>
                    <th class="sort">Nama Buyer</th>
                    <th class="sort">Alamat</th>
                    <th class="sort">Negara</th>
                    <th class="sort">Status</th>
                    <th class="sort">Updated By</th>
                    <th class="sort">Updated</th>
                    {{-- <th class="sort">No.</th> --}}
                </tr>
            </thead>
            <tbody class="list form-check-all">
                @php
                    $no = 1;
                @endphp
                @forelse ($buyers as $item)
                    <tr>
                        <td>
                            <button type="button" class="btn fs-15 p-1 bg-primary rounded" data-bs-toggle="modal"
                                data-bs-target="#modal-edit" wire:click="edit({{ $item->id }})">
                                <i class="ri-edit-box-line text-white"></i>
                            </button>
                            <button type="button" class="btn fs-15 p-1 bg-danger rounded removeBuyerModal"
                                href="#removeBuyerModal" data-bs-toggle="modal" data-bs-target="#removeBuyerModal"
                                data-remove-id="{{ $item->id }}">
                                <i class="ri-delete-bin-line  text-white"></i>
                            </button>
                        </td>
                        <td>{{ $item->code }}</td>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->address }}</td>
                        <td>{{ $item->country }}</td>
                        <td>{{ $item->country }}</td>
                        <td>{{ $item->status == 1 ? 'Active' : 'Non Active' }}</td>
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
        {{-- {{ $buyers->links() }} --}}
    </div>
    {{-- <livewire:tdorder/> --}}
</div>

<script>
    document.addEventListener('livewire:load', function() {
        // close modal create buyer
        Livewire.on('closeModalCreate', () => {
            $('#modal-addBuyer').modal('hide');
        });

        // close modal update buyer
        Livewire.on('closeModalUpdate', () => {
            $('#modal-edit').modal('hide');
        });

        // Show modal and pass ID
        document.getElementsByClassName('removeBuyerModal').forEach(button => {
            button.addEventListener('click', function() {
                let removeId = this.getAttribute('data-remove-id');
                console.log(removeId);
                $('#removeBuyerModal').modal('show');
            });
        });

        // Confirm delete action
        document.getElementById('remove-item').addEventListener('click', function() {
            Livewire.emit('delete');
            $('#removeBuyerModal').modal('hide');
        });
    });
</script>
