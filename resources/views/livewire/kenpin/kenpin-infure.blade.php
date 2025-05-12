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
                                    <input wire:model.defer="tglMasuk" type="text" class="form-control"
                                        style="padding:0.44rem" data-provider="flatpickr" data-date-format="d-m-Y">
                                    <span class="input-group-text py-0">
                                        <i class="ri-calendar-event-fill fs-4"></i>
                                    </span>

                                    <input wire:model.defer="tglKeluar" type="text" class="form-control"
                                        style="padding:0.44rem" data-provider="flatpickr" data-date-format="d-m-Y">
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
                    {{-- <input wire:model.defer="lpk_no" class="form-control" style="padding:0.44rem" type="text"
                        placeholder="000000-000" /> --}}
                        <div class="col-12 col-lg-12 mb-1" x-data="{ lpk_no: @entangle('lpk_no'), status: true }" x-init="$watch('lpk_no', value => {
                            if (value.length === 6 && !value.includes('-') && status) {
                                lpk_no = value + '-';
                            }
                            if (value.length < 6) {
                                status = true;
                            }
                            if (value.length === 7) {
                                status = false;
                            }
                            if (value.length > 10) {
                                lpk_no = value.substring(0, 10);
                            }
                        })">
                        <input
                            class="form-control"
                            style="padding:0.44rem"
                            type="text"
                            placeholder="000000-000"
                            x-model="lpk_no"
                            maxlength="10"
                        />
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-3">
                <label class="form-label text-muted fw-bold">Nomor Kenpin</label>
            </div>
            <div class="col-12 col-lg-9">
                <div class="input-group">
                    {{-- <input wire:model.defer="searchTerm" class="form-control" style="padding:0.44rem" type="text"
                        placeholder="_____-_____" /> --}}
                        <div class="col-12 col-lg-12 mb-1" x-data="{ searchTerm: @entangle('searchTerm'), status: true }" x-init="$watch('searchTerm', value => {
                            if (value.length === 5 && !value.includes('-') && status) {
                                searchTerm = value + '-';
                            }
                            if (value.length < 5) {
                                status = true;
                            }
                            if (value.length === 6) {
                                status = false;
                            }
                            if (value.length > 8) {
                                searchTerm = value.substring(0, 8);
                            }
                        })">
                        <input
                            class="form-control"
                            style="padding:0.44rem"
                            type="text"
                            placeholder="_____-_____"
                            x-model="searchTerm"
                            maxlength="8"
                        />
                    </div>
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
                    <select class="form-control" wire:model.defer="idProduct" data-choices data-choices-sorting-false
                        data-choices-removeItem data-choices-search-field-label data-choices-exact-match>
                        <option value="">- All -</option>
                        @foreach ($products as $item)
                            <option data-custom-properties='{"code": "{{ $item->code }}"}' value="{{ $item->id }}"
                                @if ($item->id == ($idProduct['value'] ?? null)) selected @endif>{{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-12 col-lg-2">
                <label class="form-label text-muted fw-bold">No Han</label>
            </div>
            {{-- <div class="col-12 col-lg-10 mb-1">
                <div wire:ignore>
                    <input wire:model.defer="no_han" class="form-control" style="padding:0.44rem" type="text"
                        placeholder="00-00-00-00A" />
                </div>
            </div> --}}
            <div class="col-12 col-lg-10 mb-1" x-data="{ no_han: @entangle('no_han'), status: true }" x-init="$watch('no_han', value => {
                    if (value.length === 2 && status) {
                        no_han = value + '-';
                    }
                    if (value.length === 5 && status) {
                        no_han = value + '-';
                    }
                    if (value.length === 8 && status) {
                        no_han = value + '-';
                    }
                    if (value.length < 10) {
                        status = true;
                    }
                    if (value.length === 3 || value.length === 6 || value.length === 9) {
                        status = false;
                    }
                    if (value.length > 12) {
                        no_han = value.substring(0, 12);
                    }
                })">
                <input
                    class="form-control"
                    style="padding:0.44rem"
                    type="text"
                    placeholder="00-00-00-00A"
                    x-model="no_han"
                    maxlength="12"
                    x-on:keydown.tab="$event.preventDefault(); $refs.nomor_barcode.focus();"
                />
            </div>
            <div class="col-12 col-lg-2">
                <label for="status" class="form-label text-muted fw-bold">Status</label>
            </div>
            <div class="col-12 col-lg-10">
                <div wire:ignore>
                    <select class="form-control" style="padding:0.44rem" wire:model.defer="status" id="status"
                        name="status" data-choices data-choices-sorting-false  data-choices-removeItem data-choices-search-field-label>
                        <option value="">- all -</option>
                        <option value="1" @if (($status['value'] ?? null) == 1) selected @endif>Proses</option>
                        <option value="2" @if (($status['value'] ?? null) == 2) selected @endif>Finish</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-12 mt-3">
        <div class="row">
            <div class="col-12 col-lg-6">
                <button wire:click="search" type="button" class="btn btn-primary btn-load w-lg p-1" wire:loading.attr="disabled">
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

                <button type="button" class="btn btn-success w-lg p-1"
                    onclick="window.location.href='/add-kenpin-infure'">
                    <i class="ri-add-line"> </i> Add
                </button>
            </div>
        </div>
    </div>

    <div class="table-responsive table-card mt-2 mb-2">
        {{-- toggle column table --}}
        <div class="col text-end dropdown">
            <button type="button" data-bs-toggle="dropdown" aria-expanded="false"
                class="btn btn-soft-primary btn-icon fs-14 mt-2">
                <i class="ri-grid-fill"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="1"
                            checked> Tgl.Kenpin
                    </label>
                </li>
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="2"
                            checked> No Kenpin
                    </label>
                </li>
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="3"
                            checked> No LPK
                    </label>
                </li>
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="4"
                            checked> Tgl. LPK
                    </label>
                </li>
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="5"> Jml
                        LPK
                    </label>
                </li>
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="6">
                        Panjang LPK
                    </label>
                </li>
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="7"
                            checked> Nama Produk
                    </label>
                </li>
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="8"
                            checked> No Order
                    </label>
                </li>
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="9"
                            checked> Petugas
                    </label>
                </li>
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="10"
                            checked> Berat Loss (kg)
                    </label>
                </li>
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="11"
                            checked> Status
                    </label>
                </li>
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="12">
                        Update By
                    </label>
                </li>
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="13">
                        Update On
                    </label>
                </li>
            </ul>
        </div>
        <table class="table align-middle" id="kenpinInfureTable">
            <thead class="table-light">
                <tr>
                    <th>Action</th>
                    <th>Tgl.Kenpin</th>
                    <th>No Kenpin</th>
                    <th>No LPK</th>
                    <th>Tgl. LPK</th>
                    <th>Jml LPK</th>
                    <th>Panjang LPK</th>
                    <th>Nama Produk</th>
                    <th>No Order</th>
                    <th>Petugas</th>
                    <th>Berat Loss (kg)</th>
                    <th>Status</th>
                    <th>Update By</th>
                    <th>Updated</th>
                </tr>
            </thead>
            <tbody class="list form-check-all">
                @forelse ($data as $item)
                    <tr>
                        <td>
                            <a href="/edit-kenpin-infure?orderId={{ $item->id }}"
                                class="link-success fs-15 p-1 bg-primary rounded">
                                <i class="ri-edit-box-line text-white"></i>
                            </a>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($item->kenpin_date)->format('d M Y') }}</td>
                        <td>{{ $item->kenpin_no }}</td>
                        <td>{{ $item->lpk_no }}</td>
                        <td>{{ \Carbon\Carbon::parse($item->lpk_date)->format('d M Y') }}</td>
                        <td>{{ number_format($item->qty_lpk) }}</td>
                        <td>{{ number_format($item->panjang_lpk) }}</td>
                        <td>{{ $item->namaproduk }}</td>
                        <td>{{ $item->code }}</td>
                        <td>{{ $item->empname }}</td>
                        <td>{{ number_format($item->berat_loss) }}</td>
                        <td>{{ $item->status_kenpin }}</td>
                        <td>{{ $item->updated_by }}</td>
                        <td>{{ \Carbon\Carbon::parse($item->updated_on)->format('d M Y') }}</td>
                    </tr>
                @empty
                    {{-- <tr>
                        <td colspan="10" class="text-center">
                            <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop"
                                colors="primary:#121331,secondary:#08a88a" style="width:40px;height:40px"></lord-icon>
                            <h5 class="mt-2">Sorry! No Result Found</h5>
                            <p class="text-muted mb-0">We've searched more than 150+ Orders We did not find any orders
                                for you search.</p>
                        </td>
                    </tr> --}}
                @endforelse
            </tbody>
        </table>
        {{-- {{ $data->links(data: ['scrollTo' => false]) }} --}}
    </div>
</div>

@script
    <script>
        // datatable
        // inisialisasi DataTable
        $wire.on('initDataTable', () => {
            initDataTable();
        });

        // Fungsi untuk menginisialisasi ulang DataTable
        function initDataTable() {
            const savedOrder = $wire.get('sortingTable');

            let defaultOrder = [
                [1, "asc"]
            ];
            if (savedOrder) {
                defaultOrder = savedOrder;
            }
            // Hapus DataTable jika sudah ada
            if ($.fn.dataTable.isDataTable('#kenpinInfureTable')) {
                let table = $('#kenpinInfureTable').DataTable();
                table.clear(); // Bersihkan data tabel
                table.destroy(); // Hancurkan DataTable
                // Hindari penggunaan $('#kenpinInfureTable').empty(); di sini
            }

            setTimeout(() => {
                // Inisialisasi ulang DataTable
                let table = $('#kenpinInfureTable').DataTable({
                    "pageLength": 10,
                    "searching": true,
                    "responsive": true,
                    "order": defaultOrder,
                    "scrollX": true,
                    "language": {
                        "emptyTable": `
                            <div class="text-center">
                                <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop"
                                    colors="primary:#121331,secondary:#08a88a" style="width:40px;height:40px"></lord-icon>
                                <h5 class="mt-2">Sorry! No Result Found</h5>
                            </div>
                        `
                    }
                });
                // Listen to sort event
                table.on('order.dt', function() {
                    let order = table.order();
                    if (order.length == 0 && defaultOrder.length > 0) {
                        order = defaultOrder;
                    }
                    $wire.call('updateSortingTable', order);
                });

                // default column visibility
                $('.toggle-column').each(function() {
                    let column = table.column($(this).attr('data-column'));
                    column.visible($(this).is(':checked'));
                });

                // Inisialisasi ulang event listener checkbox
                $('.toggle-column').off('change').on('change', function() {
                    let column = table.column($(this).attr('data-column'));
                    column.visible(!column.visible());
                });
            }, 500);
        }
    </script>
@endscript
