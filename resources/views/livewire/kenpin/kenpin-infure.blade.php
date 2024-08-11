<div class="row">
    <div class="col-12 col-lg-7">
        <div class="row">
            <div class="col-12 col-lg-3">
                <label class="form-label text-muted fw-bold">Filter Tanggal</label>
            </div>
            <div class="col-12 col-lg-9 mb-1">
                <div class="form-group">
                    <div class="input-group">
                        <div class="col-12">
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
                <label class="form-label text-muted fw-bold">Nomor LPK</label>
            </div>
            <div class="col-12 col-lg-9 mb-1">
                <div class="input-group">
                    <input wire:model.defer="lpk_no" class="form-control" style="padding:0.44rem" type="text" placeholder="000000-000" />
                </div>
            </div>
            <div class="col-12 col-lg-3">
                <label class="form-label text-muted fw-bold">Search</label>
            </div>
            <div class="col-12 col-lg-9">
                <div class="input-group">
                    <input wire:model.defer="searchTerm" class="form-control" style="padding:0.44rem" type="text" placeholder="_____-_____" />
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-5">
        <div class="row">
            <div class="col-12 col-lg-2">
                <label for="product" class="form-label text-muted fw-bold">Product</label>
            </div>
            <div class="col-12 col-lg-10 mb-1">
                <div wire:ignore>
                    <select class="form-control" wire:model.defer="idProduct" data-choices data-choices-sorting-false data-choices-removeItem>
                        <option value="">- All -</option>
                        @foreach ($products as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-12 col-lg-2">
                <label class="form-label text-muted fw-bold">No Han</label>
            </div>
            <div class="col-12 col-lg-10 mb-1">
                <div wire:ignore>
                    <input wire:model.defer="no_han" class="form-control" style="padding:0.44rem" type="text" placeholder="00-00-00-00A" />
                </div>
            </div>
            <div class="col-12 col-lg-2">
                <label for="status" class="form-label text-muted fw-bold">Status</label>
            </div>
            <div class="col-12 col-lg-10">
                <div wire:ignore>
                    <select class="form-control" style="padding:0.44rem" wire:model.defer="status" id="status" name="status" data-choices data-choices-sorting-false data-choices-removeItem>
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

    <div class="col text-end dropdown" x-data="{ 
        tgl_kenpin:true, tgl_lpk:true, jml_lpk:false, panjang_lpk:false, nama_produk:true, no_order:true, petugas:true, status:true, update_by:false, ups: false
        }">
        <button type="button" data-bs-toggle="dropdown" aria-expanded="false" class="btn btn-soft-primary btn-icon fs-14 mb-4">
            <i class="ri-grid-fill"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
            <li @click="tgl_kenpin = !tgl_kenpin; $refs.checkbox.checked = tgl_kenpin" style="cursor: pointer;">
                <input x-ref="checkbox" @change="tgl_kenpin = $refs.checkbox.checked" class="form-check-input fs-15 ms-2" type="checkbox" :checked="tgl_kenpin"> 
                Tgl. Kenpin
            </li>
            <li @click="tgl_lpk = !tgl_lpk; $refs.checkbox.checked = tgl_lpk" style="cursor: pointer;">
                <input x-ref="checkbox" @change="tgl_lpk = $refs.checkbox.checked" class="form-check-input fs-15 ms-2" type="checkbox" :checked="tgl_lpk"> 
                Tgl. Lpk
            </li>
            <li @click="jml_lpk = !jml_lpk; $refs.checkbox.checked = jml_lpk" style="cursor: pointer;">
                <input x-ref="checkbox" @change="jml_lpk = $refs.checkbox.checked" class="form-check-input fs-15 ms-2" type="checkbox" :checked="jml_lpk"> 
                Jml LPK
            </li>
            <li @click="panjang_lpk = !panjang_lpk; $refs.checkbox.checked = panjang_lpk" style="cursor: pointer;">
                <input x-ref="checkbox" @change="panjang_lpk = $refs.checkbox.checked" class="form-check-input fs-15 ms-2" type="checkbox" :checked="panjang_lpk"> 
                Panjang LPK
            </li>
            <li @click="nama_produk = !nama_produk; $refs.checkbox.checked = nama_produk" style="cursor: pointer;">
                <input x-ref="checkbox" @change="nama_produk = $refs.checkbox.checked" class="form-check-input fs-15 ms-2" type="checkbox" :checked="nama_produk"> 
                Nama Produk
            </li>
            <li @click="no_order = !no_order; $refs.checkbox.checked = no_order" style="cursor: pointer;">
                <input x-ref="checkbox" @change="no_order = $refs.checkbox.checked" class="form-check-input fs-15 ms-2" type="checkbox" :checked="no_order"> 
                No Order
            </li>
            <li @click="petugas = !petugas; $refs.checkbox.checked = petugas" style="cursor: pointer;">
                <input x-ref="checkbox" @change="petugas = $refs.checkbox.checked" class="form-check-input fs-15 ms-2" type="checkbox" :checked="petugas"> 
                Petugas
            </li>
            <li @click="status = !status; $refs.checkbox.checked = status" style="cursor: pointer;">
                <input x-ref="checkbox" @change="status = $refs.checkbox.checked" class="form-check-input fs-15 ms-2" type="checkbox" :checked="status"> 
                Status
            </li>
            <li @click="update_by = !update_by; $refs.checkbox.checked = update_by" style="cursor: pointer;">
                <input x-ref="checkbox" @change="update_by = $refs.checkbox.checked" class="form-check-input fs-15 ms-2" type="checkbox" :checked="update_by"> 
                Update By
            </li>
            <li @click="ups = !ups; $refs.checkbox.checked = ups" style="cursor: pointer;">
                <input x-ref="checkbox" @change="ups = $refs.checkbox.checked" class="form-check-input fs-15 ms-2" type="checkbox" :checked="ups"> 
                Updated
            </li>
        </ul>
    
        <div class="table-responsive table-card">
            <table class="table align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Action</th>
                        <th x-show="tgl_kenpin">Tgl.Kenpin</th>
                        <th>No Kenpin</th>
                        <th>No LPK</th>
                        <th x-show="tgl_lpk">Tgl. LPK</th>
                        <th x-show="jml_lpk">Jml LPK</th>
                        <th x-show="panjang_lpk">Panjang LPK</th>
                        <th x-show="nama_produk">Nama Produk</th>
                        <th x-show="no_order">No Order</th>
                        <th x-show="petugas">Petugas</th>
                        <th>Berat Loss (kg)</th>
                        <th x-show="status">Status</th>
                        <th x-show="update_by">Update By</th>
                        <th x-show="ups">Updated</th>
                    </tr>
                </thead>
                <tbody class="list form-check-all">
                    @forelse ($data as $item)
                        <tr>
                            <td>
                                <a href="/edit-kenpin-infure?orderId={{ $item->id }}" class="link-success fs-15 p-1 bg-primary rounded">
                                    <i class="ri-edit-box-line text-white"></i>
                                </a>
                            </td>
                            <td x-show="tgl_kenpin">{{ $item->kenpin_date }}</td>
                            <td>{{ $item->kenpin_no }}</td>
                            <td>{{ $item->lpk_no }}</td>
                            <td x-show="tgl_lpk">{{ $item->lpk_date }}</td>
                            <td x-show="jml_lpk"> - </td>
                            <td x-show="panjang_lpk">{{ $item->panjang_lpk }}</td>
                            <td x-show="nama_produk">{{ $item->namaproduk }}</td>
                            <td x-show="no_order">{{ $item->code }}</td>
                            <td x-show="petugas">{{ $item->empname }}</td>
                            <td>{{ $item->berat_loss }}</td>
                            <td x-show="status">{{ $item->status_kenpin }}</td>
                            <td x-show="update_by">{{ $item->updated_by }}</td>
                            <td x-show="ups">{{ $item->updated_on }}</td>
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
            {{ $data->links(data: ['scrollTo' => false]) }}
        </div>
    </div>
    {{-- <div class="table-responsive table-card mt-3 mb-1">
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
                    <tr>
                        <td>
                            <a href="/edit-kenpin-infure?orderId={{ $item->id }}" class="link-success fs-15 p-1 bg-primary rounded">
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
    </div> --}}
</div>
