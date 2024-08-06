{{-- @include('layouts.customizer') --}}
<div class="row">
    <div class="col-12 col-lg-7">
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
            <div class="col-12 col-lg-2">
                <label for="product" class="form-label text-muted fw-bold">Product</label>
            </div>
            <div class="col-12 col-lg-10">
                <div class="mb-1" wire:ignore>
                    <select class="form-control"  wire:model.defer="idProduct" id="product" name="product" data-choices data-choices-sorting-false data-choices-removeItem>
                        <option value="">- All -</option>
                        @foreach ($products as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-12 col-lg-2">
                <label for="buyer" class="form-label text-muted fw-bold">Buyer</label>
            </div>
            <div class="col-12 col-lg-10">
                <div class="mb-1" wire:ignore>
                    <select class="form-control" wire:model.defer="idBuyer" data-choices data-choices-sorting-false data-choices-removeItem>
                        <option value="">- All -</option>
                        @foreach ($buyer as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-12 col-lg-2">
                <label for="status" class="form-label text-muted fw-bold">Status</label>
            </div>
            <div class="col-12 col-lg-10">
                <div class="mb-1" wire:ignore>
                    <select class="form-control" wire:model.defer="status" data-choices data-choices-sorting-false data-choices-removeItem>
                        <option value="">- All -</option>
                        <option value="0">Belum LPK</option>
                        <option value="1">Sudah LPK</option>
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
                    onclick="window.location.href='/add-order'"
                    >
                    <i class="ri-add-line"> </i> Add
                </button>
            </div>
            <div class="col-12 col-lg-6">
                    {{-- <form wire:submit.prevent="import">
                        <input type="file" wire:model="file">
                        <button type="submit">Import</button>
                    </form> --}}

                <input type="file" id="fileInput" wire:model="file" style="display: none;">
                <button class="btn btn-success w-lg p-1" type="button" onclick="document.getElementById('fileInput').click()">
                    <span wire:loading.remove wire:target="file">
                        <i class="ri-upload-2-fill"> </i> Upload Excel
                    </span>
                    <div wire:loading wire:target="file">
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
    
    <div class="col text-end dropdown" wire:ignore>
        {{-- <a href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="ri-more-fill fs-17"></i> 
        </a> --}}
        {{-- <button type="button" class="btn btn-soft-primary btn-icon fs-14"><i class="ri-grid-fill"></i></button> --}}
        <button type="button" data-bs-toggle="dropdown" aria-expanded="false" class="btn btn-soft-primary btn-icon fs-14">
            <i class="ri-grid-fill"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
            <li>
                <input class="form-check-input fs-15 ms-2" type="checkbox"
                wire:model.live="po_no" value="{{ $po_no }}"> PO Number
            </li>
            <li>
                <input class="form-check-input fs-15 ms-2" type="checkbox"
                wire:model.live="pr_na" value="{{ $pr_na }}"> Nama Produk
            </li>
            <li>
                <input class="form-check-input fs-15 ms-2" type="checkbox"
                wire:model.live="ko_pr" value="{{ $ko_pr }}"> Kode Produk
            </li>
            <li>
                <input class="form-check-input fs-15 ms-2" type="checkbox"
                wire:model.live="bu" value="{{ $bu }}"> Buyer
            </li>
            <li>
                <input class="form-check-input fs-15 ms-2" type="checkbox"
                wire:model.live="qt" value="{{ $qt }}"> Quantity
            </li>
            <li>
                <input class="form-check-input fs-15 ms-2" type="checkbox"
                wire:model.live="tgo" value="{{ $tgo }}"> Tgl. Order
            </li>
            <li>
                <input class="form-check-input fs-15 ms-2" type="checkbox"
                wire:model.live="stf" value="{{ $stf }}"> Stufing
            </li>
            <li>
                <input class="form-check-input fs-15 ms-2" type="checkbox"
                wire:model.live="et" value="{{ $et }}"> Etd
            </li>
            <li>
                <input class="form-check-input fs-15 ms-2" type="checkbox"
                wire:model.live="eta" value="{{ $eta }}"> Eta
            </li>
            <li>
                <input class="form-check-input fs-15 ms-2" type="checkbox"
                wire:model.live="tgp" value="{{ $tgp }}"> Tgl Proses
            </li>
            <li>
                <input class="form-check-input fs-15 ms-2" type="checkbox"
                wire:model.live="no" value="{{ $no }}"> No.
            </li>
            <li>
                <input class="form-check-input fs-15 ms-2" type="checkbox"
                wire:model.live="updated_by" value="{{ $updated_by }}"> UpdateBy
            </li>
            <li>
                <input class="form-check-input fs-15 ms-2" type="checkbox"
                wire:model.live="updated_on" value="{{ $updated_on }}"> UpdateDt
            </li>
        </ul>
    </div> 
    <div class="table-responsive table-card mt-3 mb-1">
        <table class="table align-middle table-nowrap" id="customerTable" style="width:100%">
            <thead class="table-light">
                <tr>
                    <th></th>
                    @if ($po_no)
                        <th>PO Number</th>
                    @endif
                    @if ($pr_na)
                        <th>Nama Produk</th>
                    @endif
                    @if ($ko_pr)
                        <th>Kode Produk</th>
                    @endif
                    @if ($bu)
                        <th>Buyer</th>
                    @endif
                    @if ($qt)
                        <th>Quantity</th>
                    @endif
                    @if ($tgo)
                        <th>Tgl. Order</th>
                    @endif
                    @if ($stf)
                        <th>Stuffing</th>
                    @endif
                    @if ($et)
                        <th>Etd</th>
                    @endif
                    @if ($eta)
                        <th>Eta</th>
                    @endif
                    @if ($tgp)
                        <th>Tgl Proses</th>
                    @endif
                    @if ($no)                    
                        <th>No.</th>
                    @endif
                    @if ($updated_by)                    
                        <th>Update By</th>
                    @endif
                    @if ($updated_on)                    
                        <th>Update On</th>
                    @endif
                </tr>
            </thead>
            <tbody class="list form-check-all">
                @php
                    $no = 1;
                @endphp
                @forelse ($data as $item)
                    <tr>
                        <td>
                            <a href="/edit-order?orderId={{ $item->id }}" class="link-success fs-15 p-1 bg-primary rounded">
                                <i class="ri-edit-box-line text-white"></i>
                            </a>
                        </td>
                        @if ($po_no)
                            <td>{{ $item->po_no }}</td>
                        @endif
                        @if ($pr_na)
                            <td>{{ $item->produk_name }}</td>
                        @endif
                        @if ($ko_pr)
                            <td>{{ $item->product_code }}</td>
                        @endif
                        @if ($bu)
                            <td>{{ $item->buyer_name }}</td>
                        @endif
                        @if ($qt)
                            <td>{{ $item->order_qty }}</td>
                        @endif
                        @if ($tgo)                        
                            <td>{{ $item->order_date }}</td>
                        @endif
                        @if ($stf)
                            <td>{{ $item->stufingdate }}</td>
                        @endif
                        @if ($et)                        
                            <td>{{ $item->etddate }}</td>
                        @endif
                        @if ($eta)
                            <td>{{ $item->etadate }}</td>
                        @endif
                        @if ($tgp)                            
                            <td>{{ $item->processdate }}</td>
                        @endif
                        @if ($no)                            
                            <td>{{ $no++ }}</td>
                        @endif
                        @if ($updated_by)                    
                            <td>{{ $updated_by }}</td>
                        @endif
                        @if ($updated_on)                    
                            <td>{{ $updated_on }}</td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center">
                            <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop" colors="primary:#121331,secondary:#08a88a" style="width:40px;height:40px"></lord-icon>
                            <h5 class="mt-2">Sorry! No Result Found</h5>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        {{ $data->links() }}
    </div>
    {{-- <livewire:tdorder/> --}}
</div>