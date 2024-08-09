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
    
    <div class="col text-end dropdown" x-data="{ 
        po_no:true, na_pr:true, ko_pr:true, bu:true, qt:true, tgo:true, stf:false, etd:true, eta:false, tgp:true, num:true, up_by: false, up_dt: false
        }">
        <button type="button" data-bs-toggle="dropdown" aria-expanded="false" class="btn btn-soft-primary btn-icon fs-14 me-4 mb-4">
            <i class="ri-grid-fill"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
            <li @click="po_no = !po_no; $refs.checkbox.checked = po_no" style="cursor: pointer;">
                <input x-ref="checkbox" @change="po_no = $refs.checkbox.checked" class="form-check-input fs-15 ms-2" type="checkbox" :checked="po_no"> 
                PO Number
            </li>
            <li @click="na_pr = !na_pr; $refs.checkbox.checked = na_pr" style="cursor: pointer;">
                <input x-ref="checkbox" @change="na_pr = $refs.checkbox.checked" class="form-check-input fs-15 ms-2" type="checkbox" :checked="na_pr"> 
                Nama Produk
            </li>
            <li @click="ko_pr = !ko_pr; $refs.checkbox.checked = ko_pr" style="cursor: pointer;">
                <input x-ref="checkbox" @change="ko_pr = $refs.checkbox.checked" class="form-check-input fs-15 ms-2" type="checkbox" :checked="ko_pr"> 
                Kode Produk
            </li>
            <li @click="bu = !bu; $refs.checkbox.checked = bu" style="cursor: pointer;">
                <input x-ref="checkbox" @change="bu = $refs.checkbox.checked" class="form-check-input fs-15 ms-2" type="checkbox" :checked="bu"> 
                Buyer
            </li>
            <li @click="qt = !qt; $refs.checkbox.checked = qt" style="cursor: pointer;">
                <input x-ref="checkbox" @change="qt = $refs.checkbox.checked" class="form-check-input fs-15 ms-2" type="checkbox" :checked="qt"> 
                Quantity
            </li>
            <li @click="tgo = !tgo; $refs.checkbox.checked = tgo" style="cursor: pointer;">
                <input x-ref="checkbox" @change="tgo = $refs.checkbox.checked" class="form-check-input fs-15 ms-2" type="checkbox" :checked="tgo"> 
                Quantity
            </li>
            <li @click="stf = !stf; $refs.checkbox.checked = stf" style="cursor: pointer;">
                <input x-ref="checkbox" @change="stf = $refs.checkbox.checked" class="form-check-input fs-15 ms-2" type="checkbox" :checked="stf"> 
                Stuffing
            </li>
            <li @click="etd = !etd; $refs.checkbox.checked = etd" style="cursor: pointer;">
                <input x-ref="checkbox" @change="etd = $refs.checkbox.checked" class="form-check-input fs-15 ms-2" type="checkbox" :checked="etd"> 
                Etd
            </li>
            <li @click="eta = !eta; $refs.checkbox.checked = eta" style="cursor: pointer;">
                <input x-ref="checkbox" @change="eta = $refs.checkbox.checked" class="form-check-input fs-15 ms-2" type="checkbox" :checked="eta"> 
                Eta
            </li>
            <li @click="tgp = !tgp; $refs.checkbox.checked = tgp" style="cursor: pointer;">
                <input x-ref="checkbox" @change="tgp = $refs.checkbox.checked" class="form-check-input fs-15 ms-2" type="checkbox" :checked="tgp"> 
                Tgl Proses
            </li>
            <li @click="num = !num; $refs.checkbox.checked = num" style="cursor: pointer;">
                <input x-ref="checkbox" @change="num = $refs.checkbox.checked" class="form-check-input fs-15 ms-2" type="checkbox" :checked="num"> 
                No.
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
            <table class="table align-middle">
                <thead class="table-light">
                    <tr>
                        <th></th>
                        <th x-show="po_no">PO Number</th>
                        <th x-show="na_pr">Nama Produk</th>
                        <th x-show="ko_pr">Kode Produk</th>
                        <th x-show="bu">Buyer</th>
                        <th x-show="qt">Quantity</th>
                        <th x-show="tgo">Tgl. Order</th>
                        <th x-show="stf">Stuffing</th>
                        <th x-show="etd">Etd</th>
                        <th x-show="eta">Eta</th>
                        <th x-show="tgp">Tgl Proses</th>            
                        <th x-show="num">No.</th>                
                        <th x-show="up_by">Update By</th>               
                        <th x-show="up_dt">Update On</th>
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
                            <td x-show="po_no">{{ $item->po_no }}</td>
                            <td x-show="na_pr">{{ $item->produk_name }}</td>
                            <td x-show="ko_pr">{{ $item->product_code }}</td>
                            <td x-show="bu">{{ $item->buyer_name }}</td>
                            <td x-show="qt">{{ $item->order_qty }}</td>
                            <td x-show="tgo">{{ $item->order_date }}</td>
                            <td x-show="stf">{{ $item->stufingdate }}</td>
                            <td x-show="etd">{{ $item->etddate }}</td>
                            <td x-show="eta">{{ $item->etadate }}</td>
                            <td x-show="tgp">{{ $item->processdate }}</td>
                            <td x-show="num">{{ $no++ }}</td>    
                            <td x-show="up_by">{{ $updated_by }}</td>  
                            <td x-show="up_dt">{{ $updated_on }}</td>
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
            {{ $data->links(data: ['scrollTo' => false]) }}
        </div>
    </div>
    {{-- <livewire:tdorder/> --}}
</div>