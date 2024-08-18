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
                            <option data-custom-properties='{"code": "{{ $item->code }}"}' value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-12 mt-2">
        <div class="toolbar">
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
    <div class="card border-0 shadow mb-4 mt-2">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-centered table-nowrap mb-0 rounded">
                    <thead class="table-light">
                        <tr>
                            <th class="border-0 rounded-start">Action</th>
                            <th class="border-0">No Palet</th>
                            <th class="border-0">No Order</th>
                            <th class="border-0">Nama Produk</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($penarikan as $item)
                            <tr>
                                <td>
                                    <input type="checkbox" wire:model="selectedItems" value="{{ $item->product_id }}">
                                </td>
                                <td>{{ $item->nomor_palet }}</td>
                                <td>{{ $item->code }}</td>
                                <td>{{ $item->name }}</td>
                            </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center">No results found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-12 text-end">
        <button type="button" class="btn btn-success">
            <i class="ri-settings-4-fill"></i> Proses Penarikan
        </button>
    </div>
</div>

