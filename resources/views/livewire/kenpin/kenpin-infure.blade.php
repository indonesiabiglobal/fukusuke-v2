<div class="row">
    <div class="col-12 col-lg-7 mb-1">
        <div class="row">
            <div class="col-12 col-lg-3">
                <label class="form-label text-muted fw-bold">Filter Tanggal</label>
            </div>
            <div class="col-12 col-lg-9 mb-1">
                <div class="form-group">
                    <div class="input-group">
                        <div class="col-3">
                            <select class="form-select" style="padding:0.44rem" wire:model.defer="transaksi">
                                <option value="1">Proses</option>
                                <option value="2">Order</option>
                            </select>
                        </div>
                        <div class="col-9">
                            <div class="form-group">
                                <div class="input-group">
                                    <input wire:model.defer="tglMasuk" type="text" class="form-control" style="padding:0.44rem" data-provider="flatpickr" data-date-format="d-m-Y">
                                    <span class="input-group-text py-0">
                                        <i class="ri-calendar-event-fill fs-4"></i>
                                    </span>

                                    <input wire:model.defer="tglKeluar" type="text" class="form-control" style="padding:0.44rem" data-provider="flatpickr" data-date-format="d-m-Y">
                                    <span class="input-group-text py-0">
                                        <i class="ri-calendar-event-fill fs-4"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-3">
                <label class="form-label text-muted fw-bold">Search</label>
            </div>
            <div class="col-12 col-lg-9">
                <div class="input-group">
                    <input wire:model.defer="searchTerm" class="form-control"style="padding:0.44rem" type="text" placeholder="search nomor PO, nama produk" />
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-5">
        <div class="row">
            <div class="col-12 col-lg-3">
                <label class="form-label text-muted fw-bold">Nomor Han</label>
            </div>
            <div class="col-12 col-lg-9">
                <div class="mb-1" wire:ignore>
                    <input wire:model.defer="no_han" class="form-control" type="text" placeholder="00-00-00-00A" />
                </div>
            </div>
            <div class="col-12 col-lg-3">
                <label class="form-label text-muted fw-bold">Status</label>
            </div>
            <div class="col-12 col-lg-9">
                <div class="mb-1" wire:ignore>
                    <select class="form-control" placeholder="- all -">
                        <option value="">- all -</option>
                        <option value="1">Proses</option>
                        <option value="2">Finish</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-12 mt-3">
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
                    onclick="window.location.href='/add-kenpin-infure'"
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
                    <th class="border-0 rounded-start">Action</th>
                    <th class="border-0">Tgl.Kenpin</th>
                    <th class="border-0">No Kenpin</th>
                    <th class="border-0">No LPK</th>
                    <th class="border-0">Tgl. LPK</th>
                    <th class="border-0">Nama Produk</th>
                    <th class="border-0">No Order</th>
                    <th class="border-0">Petugas</th>
                    <th class="border-0">Berat Loss (kg)</th>
                    <th class="border-0 rounded-end">Status</th>
                </tr>
            </thead>
            <tbody class="list form-check-all">
                @php
                    $no = 1;
                @endphp
                @forelse ($data as $item)
                    {{-- <tr>
                        <td>
                            <a href="/edit-order?orderId={{ $item->id }}" class="link-success fs-15 p-1 bg-primary rounded">
                                <i class="ri-edit-box-line text-white"></i>
                            </a>
                        </td>
                        <td>{{ $item->po_no }}</td>
                        <td>{{ $item->produk_name }}</td>
                        <td>{{ $item->product_code }}</td>
                        <td>{{ $item->buyer_name }}</td>
                        <td>{{ $item->order_qty }}</td>
                        <td>{{ $item->order_date }}</td>
                        <td>{{ $item->etddate }}</td>
                        <td>{{ $item->processdate }}</td>
                        <td>{{ $no++ }}</td>
                    </tr> --}}
                    <tr>
                        <td>
                            {{-- <a href="/edit-order?orderId={{ $item->id }}" class="btn btn-info">
                                <i class="fa fa-edit"></i> Edit
                            </a> --}}
                            <a href="/edit-order?orderId={{ $item->id }}" class="link-success fs-15 p-1 bg-primary rounded">
                                <i class="ri-edit-box-line text-white"></i>
                            </a>
                        </td>
                        <td>{{ $item->kenpin_date }}</td>
                        <td>{{ $item->kenpin_no }}</td>
                        <td>{{ $item->lpk_no }}</td>
                        <td>{{ $item->lpk_date }}</td>
                        <td>{{ $item->namaproduk }}</td>
                        <td>{{ $item->code }}</td>
                        <td>{{ $item->empname }}</td>
                        <td>{{ $item->berat_loss }}</td>
                        <td>{{ $item->status_kenpin }}</td>
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
</div>
{{-- <div class="card border-0 shadow mb-4 mt-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-centered table-nowrap mb-0 rounded">
                <thead class="thead-light">
                    <tr>
                        <th class="border-0 rounded-start">Action</th>
                        <th class="border-0">Tgl.Kenpin</th>
                        <th class="border-0">No Kenpin</th>
                        <th class="border-0">No LPK</th>
                        <th class="border-0">Tgl. LPK</th>
                        <th class="border-0">Nama Produk</th>
                        <th class="border-0">No Order</th>
                        <th class="border-0">Petugas</th>
                        <th class="border-0">Berat Loss (kg)</th>
                        <th class="border-0 rounded-end">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Item -->
                    @foreach ($data as $item)
                    <tr>
                        <td>
                            <a href="{{ route('edit-order', ['orderId' => $item->id]) }}" class="btn btn-info">
                                <i class="fa fa-edit"></i> Edit
                            </a>
                        </td>
                        <td>                                
                            {{ $item->kenpin_date }}
                        </td>
                        <td>
                            {{ $item->kenpin_no }}
                        </td>
                        <td>
                            {{ $item->lpk_no }}
                        </td>
                        <td>
                            {{ $item->lpk_date }}
                        </td>
                        <td>
                            {{ $item->namaproduk }}
                        </td>
                        <td>
                            {{ $item->code }}
                        </td>
                        <td>
                            {{ $item->namapetugas }}
                        </td>
                        <td>
                            {{ $item->berat_loss }}
                        </td>
                        <td>
                            {{ $item->status_kenpin }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div> --}}
