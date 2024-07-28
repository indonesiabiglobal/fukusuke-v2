{{-- @include('layouts.customizer') --}}
<div class="row">
    <div class="col-12 col-lg-4">
        <div class="row">
            <div class="col-12 col-lg-3">
                <label class="form-label text-muted fw-bold">Search</label>
            </div>
            <div class="col-12 col-lg-9">
                <div class="input-group">
                    <input wire:model.defer="searchTerm" class="form-control"style="padding:0.44rem" type="text" placeholder="Enter name or code" />
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-8">
        <div class="row">
            <div class="col-12 col-lg-1">
                <label for="product" class="form-label text-muted fw-bold">Roles</label>
            </div>
            <div class="col-12 col-lg-6">
                <div class="mb-1" wire:ignore>
                    <select class="form-control" wire:model.defer="idRole" data-choices data-choices-sorting-false data-choices-removeItem>
                        <option value="">- All -</option>
                        @foreach ($userrole as $item)
                            <option value="{{ $item->id }}">{{ $item->rolename }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-12 col-lg-1">
                <label for="status" class="form-label text-muted fw-bold">Status</label>
            </div>
            <div class="col-12 col-lg-4">
                <div class="mb-1" wire:ignore>
                    <select class="form-control" wire:model.defer="status" data-choices data-choices-sorting-false data-choices-removeItem>
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
                
                <button
                    type="button" 
                    class="btn btn-success w-lg p-1"
                    onclick="window.location.href='/add-user'"
                    >
                    <i class="ri-add-line"> </i> Add
                </button>
            </div>            
        </div>
    </div>
    <div class="table-responsive table-card mt-3 mb-1">
        <table class="table align-middle table-nowrap" id="customerTable" style="width:100%">
            <thead class="table-light">
                <tr>
                    <th class="sort">Action</th>
                    <th class="sort">User Name</th>
                    <th class="sort">Email</th>
                    <th class="sort">Employee Name</th>
                    <th class="sort">Job Title</th>
                    <th class="sort">Status</th>
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
                                href="#removeBuyerModal" data-bs-toggle="modal" data-bs-target="#removeBuyerModal"
                                data-remove-id="{{ $item->id }}">
                                <i class="ri-delete-bin-line  text-white"></i>
                            </button>
                            {{-- <a href="/edit-order?orderId={{ $item->id }}" class="link-success ms-1 fs-15 p-1 bg-danger rounded">
                                <i class="ri-delete-bin-6-line text-white"></i>
                            </a> --}}
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
                            
                        </td>
                        <td>{{ $item->username }}</td>
                        <td>{{ $item->email }}</td>
                        <td>{{ $item->empname }}</td>
                        <td>{{ $item->job }}</td>
                        <td>{{ $item->status }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center">
                            <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop" colors="primary:#121331,secondary:#08a88a" style="width:40px;height:40px"></lord-icon>
                            <h5 class="mt-2">Sorry! No Result Found</h5>
                            <p class="text-muted mb-0">We've searched more than 150+ Orders We did not find any orders for you search.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        {{ $data->links() }}
    </div>
    
    {{-- <livewire:tdorder/> --}}
</div>