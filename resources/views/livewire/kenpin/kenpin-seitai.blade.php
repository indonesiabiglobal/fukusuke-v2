<div class="row">
    <div class="col-12 col-lg-6">
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
                <label class="form-label text-muted fw-bold">Nomor Kenpin</label>
            </div>
            <div class="col-12 col-lg-9">
                <div class="input-group">
                    <input wire:model.defer="searchTerm" class="form-control" style="padding:0.44rem" type="text" placeholder="_____-_____" />
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-6">
        <div class="row">
            <div class="col-12 col-lg-3">
                <label for="product" class="form-label text-muted fw-bold">Product</label>
            </div>
            <div class="col-12 col-lg-9 mb-1">
                <div wire:ignore>
                    <select class="form-control" style="padding:0.44rem" wire:model.defer="idProduct" data-choices data-choices-sorting-false data-choices-removeItem>
                        <option value="">- All -</option>
                        @foreach ($products as $item)
                            <option value="{{ $item->id }}" @if ($item->id == ($idProduct['value'] ?? null)) selected @endif>{{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-12 col-lg-3">
                <label class="form-label text-muted fw-bold">Nomor Palet</label>
            </div>
            <div class="col-12 col-lg-9 mb-1">
                <div class="input-group">
                    <input type="text" class="form-control" style="padding:0.44rem" placeholder="A0000-000000" wire:model.defer="nomor_palet">
                    <span class="input-group-text readonly" readonly="readonly">
                        NO. LOT
                    </span>
                    <input wire:model.defer="nomor_lot" type="text" class="form-control" placeholder="---">
                </div>
            </div>
            <div class="col-12 col-lg-3">
                <label for="status" class="form-label text-muted fw-bold">Status</label>
            </div>
            <div class="col-12 col-lg-9">
                <div wire:ignore>
                    <select class="form-control" style="padding:0.44rem" wire:model.defer="status" id="status" name="status" data-choices data-choices-sorting-false data-choices-removeItem>
                        <option value="">- all -</option>
                        <option value="1" @if (($status['value'] ?? null) == 1) selected @endif>Proses</option>
                        <option value="2" @if (($status['value'] ?? null) == 2) selected @endif>Finish</option>
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
                    onclick="window.location.href='/add-kenpin-seitai'"
                    >
                    <i class="ri-add-line"> </i> Add
                </button>
            </div>
        </div>
    </div>

    <div class="col text-end dropdown" x-data="{ 
        tgl_kenpin:true, no_kenpin:true, nama_produk:false, no_order:true, petugas:true, jumlah_loss:true, status:true, update_by:false, updated: false
        }">
        <button type="button" data-bs-toggle="dropdown" aria-expanded="false" class="btn btn-soft-primary btn-icon fs-14 mb-4">
            <i class="ri-grid-fill"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
            <li @click="tgl_kenpin = !tgl_kenpin; $refs.checkbox.checked = tgl_kenpin" style="cursor: pointer;">
                <input x-ref="checkbox" @change="tgl_kenpin = $refs.checkbox.checked" class="form-check-input fs-15 ms-2" type="checkbox" :checked="tgl_kenpin"> 
                Tgl. Kenpin
            </li>
            <li @click="no_kenpin = !no_kenpin; $refs.checkbox.checked = no_kenpin" style="cursor: pointer;">
                <input x-ref="checkbox" @change="no_kenpin = $refs.checkbox.checked" class="form-check-input fs-15 ms-2" type="checkbox" :checked="no_kenpin"> 
                No Kenpin
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
            <li @click="jumlah_loss = !jumlah_loss; $refs.checkbox.checked = jumlah_loss" style="cursor: pointer;">
                <input x-ref="checkbox" @change="jumlah_loss = $refs.checkbox.checked" class="form-check-input fs-15 ms-2" type="checkbox" :checked="jumlah_loss"> 
                Jumlah Loss
            </li>
            <li @click="status = !status; $refs.checkbox.checked = status" style="cursor: pointer;">
                <input x-ref="checkbox" @change="status = $refs.checkbox.checked" class="form-check-input fs-15 ms-2" type="checkbox" :checked="status"> 
                Jumlah Loss
            </li>
            <li @click="update_by = !update_by; $refs.checkbox.checked = update_by" style="cursor: pointer;">
                <input x-ref="checkbox" @change="update_by = $refs.checkbox.checked" class="form-check-input fs-15 ms-2" type="checkbox" :checked="update_by"> 
                Update By
            </li>
            <li @click="updated = !updated; $refs.checkbox.checked = updated" style="cursor: pointer;">
                <input x-ref="checkbox" @change="updated = $refs.checkbox.checked" class="form-check-input fs-15 ms-2" type="checkbox" :checked="updated"> 
                Updated
            </li>
        </ul>
    
        <div class="table-responsive table-card">
            <table class="table align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Action</th>
                        <th x-show="tgl_kenpin">Tgl. Kenpin</th>
                        <th x-show="no_kenpin">No Kenpin</th>
                        <th x-show="nama_produk">Nama Produk</th>
                        <th x-show="no_order">No. Order</th>
                        <th x-show="petugas">Petugas</th>
                        <th x-show="jumlah_loss">Jumlah Loss (lbr)</th>
                        <th x-show="status">Status</th>
                        <th x-show="update_by">Update By</th>
                        <th x-show="updated">Updated</th>
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
                            <td x-show="no_kenpin">{{ $item->kenpin_no }}</td>
                            <td x-show="nama_produk">{{ $item->namaproduk }}</td>
                            <td x-show="no_order">{{ $item->code }}</td>
                            <td x-show="petugas">{{ $item->namapetugas }}</td>
                            <td x-show="jumlah_loss">{{ $item->qty_loss }}</td>
                            <td x-show="status">{{ $item->status_kenpin }}</td>
                            <td x-show="update_by">{{ $item->updated_by }}</td>
                            <td x-show="updated">{{ $item->updated_on }}</td>
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
                    <th class="border-0">Tgl. Kenpin</th>
                    <th class="border-0">No Kenpin</th>
                    <th class="border-0">Nama Produk</th>
                    <th class="border-0">No. Order</th>
                    <th class="border-0">Petugas</th>
                    <th class="border-0">Jumlah Loss (lbr)</th>
                    <th class="border-0">Status</th>
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
                        <td>{{ $item->namaproduk }}</td>
                        <td>{{ $item->code }}</td>
                        <td>{{ $item->namapetugas }}</td>
                        <td>{{ $item->qty_loss }}</td>
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
