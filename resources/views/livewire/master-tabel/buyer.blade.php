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
                {{-- Button Add buyer --}}
                <button type="button" class="btn btn-success w-lg p-1" wire:click="showModalCreate">
                    <i class="ri-add-line"> </i> Add
                </button>
                {{-- modal add buyer --}}
                <div class="modal fade" id="modal-add" tabindex="-1" aria-labelledby="modal-addLabel" aria-modal="true"
                    wire:ignore.self>
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modal-addLabel">Add Master Buyer</h5> <button type="button"
                                    class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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

                {{-- modal add buyer --}}
                <div class="modal fade" id="modal-edit" tabindex="-1" aria-labelledby="modal-editLabel"
                    aria-modal="true" wire:ignore.self>
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modal-editLabel">Edit Master Buyer</h5> <button
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
                                        <p class="text-muted mx-4 mb-0">Are you sure you want to remove this buyer ?
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
    <div class="col text-end dropdown" x-data="{ 
        po_no:true, na_pr:true, ko_pr:true, bu:true, qt:true, up_by: true, up_dt: false
        }">
        <button type="button" data-bs-toggle="dropdown" aria-expanded="false" class="btn btn-soft-primary btn-icon fs-14 mb-4">
            <i class="ri-grid-fill"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
            <li @click="po_no = !po_no; $refs.checkbox.checked = po_no" style="cursor: pointer;">
                <input x-ref="checkbox" @change="po_no = $refs.checkbox.checked" class="form-check-input fs-15 ms-2" type="checkbox" :checked="po_no"> 
                Kode Buyer
            </li>
            <li @click="na_pr = !na_pr; $refs.checkbox.checked = na_pr" style="cursor: pointer;">
                <input x-ref="checkbox" @change="na_pr = $refs.checkbox.checked" class="form-check-input fs-15 ms-2" type="checkbox" :checked="na_pr"> 
                Nama Buyer
            </li>
            <li @click="ko_pr = !ko_pr; $refs.checkbox.checked = ko_pr" style="cursor: pointer;">
                <input x-ref="checkbox" @change="ko_pr = $refs.checkbox.checked" class="form-check-input fs-15 ms-2" type="checkbox" :checked="ko_pr"> 
                Alamat
            </li>
            <li @click="bu = !bu; $refs.checkbox.checked = bu" style="cursor: pointer;">
                <input x-ref="checkbox" @change="bu = $refs.checkbox.checked" class="form-check-input fs-15 ms-2" type="checkbox" :checked="bu"> 
                Negara
            </li>
            <li @click="qt = !qt; $refs.checkbox.checked = qt" style="cursor: pointer;">
                <input x-ref="checkbox" @change="qt = $refs.checkbox.checked" class="form-check-input fs-15 ms-2" type="checkbox" :checked="qt"> 
                Status
            </li>
            <li @click="up_by = !up_by; $refs.checkbox.checked = up_by" style="cursor: pointer;">
                <input x-ref="checkbox" @change="up_by = $refs.checkbox.checked" class="form-check-input fs-15 ms-2" type="checkbox" :checked="up_by"> 
                Update By
            </li>
            <li @click="up_dt = !up_dt; $refs.checkbox.checked = up_dt" style="cursor: pointer;">
                <input x-ref="checkbox" @change="up_dt = $refs.checkbox.checked" class="form-check-input fs-15 ms-2" type="checkbox" :checked="up_dt"> 
                UpdateDt
            </li>
        </ul>
    
        <div class="table-responsive table-card">
            <table class="table table-bordered align-middle dt-responsive mdl-data-table" style="overflow-x: :scroll">
                <thead class="table-light">
                    <tr>
                        <th></th>
                        <th x-show="po_no" style="width: 70px;" wire:click='sortBy("id")'>Kode Buyer
                            <i class="ri-arrow-up-line"></i>
                        </th>
                        <th x-show="na_pr" style="width: 200px; text-align: left;">Nama Buyer</th>
                        <th x-show="ko_pr" style="width: 350px; text-align: left; ">Alamat</th>
                        <th x-show="bu" style="width: 80px; text-align: left;" >Negara</th>
                        <th x-show="qt" style="width: 60px;">Status</th>
                        <th x-show="up_by" style="width: 50px;">Updated By</th>
                        <th x-show="up_dt" style="width: 50px;">Updated</th>
                    </tr>
                </thead>
                <tbody class="list form-check-all">
                    @php
                        $no = 1;
                    @endphp
                    @forelse ($buyers as $item)
                        <tr>
                            <td class="d-flex">
                                <button type="button" class="btn fs-10 p-1 bg-primary rounded me-2" data-bs-toggle="modal"
                                    data-bs-target="#modal-edit" wire:click="edit({{ $item->id }})">
                                    <i class="ri-edit-box-line text-white"></i>
                                </button>
                                <button type="button" class="btn fs-10 p-1 bg-danger rounded" wire:click="delete({{ $item->id }})">
                                    <i class="ri-delete-bin-line text-white"></i>
                                </button>
                            </td>
                            <td x-show="po_no" style="width: 70px;">{{ $item->code }}</td>
                            <td x-show="na_pr" style="width: 200px; text-align: left;">{{ $item->name }}</td>
                            <td x-show="ko_pr" style="width: 350px; text-align: left;">{{ $item->address }}</td>
                            <td x-show="bu" style="width: 80px; text-align: left;">{{ $item->country }}</td>
                            <td x-show="qt" style="width: 60px;">{!! $item->status == 1
                                ? '<span class="badge text-success bg-success-subtle">Active</span>'
                                : '<span class="badge text-bg-danger">Non Active</span>' !!}</td>
                            <td x-show="up_by" style="width: 50px;">{{ $item->updated_by }}</td>  
                            <td x-show="up_dt" style="width: 50px;">{{ $item->updated_on }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center">
                                <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop" colors="primary:#121331,secondary:#08a88a" style="width:25px;height:25px"></lord-icon>
                                <h5 class="mt-2">Sorry! No Result Found</h5>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            {{-- {{ $buyers->links(data: ['scrollTo' => false]) }} --}}
            {{-- {{ $buyers->links() }} --}}
        </div>
    </div>
    
    {{-- <livewire:tdorder/> --}}
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
    </script>
@endscript
