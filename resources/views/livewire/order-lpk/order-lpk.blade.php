{{-- @include('layouts.customizer') --}}
<div>
    <div class="row filter-section">
        <div class="col-12 col-lg-7">
            <div class="row">
                <div class="col-12 col-lg-3">
                    <label class="form-label text-muted fw-bold">Filter Tanggal</label>
                </div>
                <div class="col-12 col-lg-9 mb-1">
                    <div class="form-group" wire:ignore>
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
                                        <input wire:model.defer="tglMasuk" type="text" class="form-control"
                                            style="padding:0.44rem" data-provider="flatpickr" data-date-format="d M Y">
                                        <span class="input-group-text py-0">
                                            <i class="ri-calendar-event-fill fs-4"></i>
                                        </span>

                                        <input wire:model.defer="tglKeluar" type="text" class="form-control"
                                            style="padding:0.44rem" data-provider="flatpickr" data-date-format="d M Y">
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
                        <input wire:model.defer="searchTerm" class="form-control"style="padding:0.44rem" type="text"
                            placeholder="search nomor PO, nama produk" />
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
                        <select class="form-control" wire:model.defer="idProduct" id="product" name="product"
                            data-choices data-choices-sorting-false data-choices-removeItem
                            data-choices-search-field-label data-choices-exact-match>
                            <option value="">- All -</option>
                            @foreach ($products as $item)
                                <option data-custom-properties='{"code": "{{ $item->code }}"}'
                                    value="{{ $item->id }}" @if ($item->id == ($idProduct['value'] ?? null)) selected @endif>
                                    {{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-12 col-lg-2">
                    <label for="buyer" class="form-label text-muted fw-bold">Buyer</label>
                </div>
                <div class="col-12 col-lg-10">
                    <div class="mb-1" wire:ignore>
                        <select class="form-control" wire:model.defer="idBuyer" data-choices data-choices-sorting-false
                            data-choices-removeItem data-choices-search-field-label>
                            <option value="">- All -</option>
                            @foreach ($buyer as $item)
                                <option data-custom-properties='{"code": "{{ $item->code }}"}'
                                    value="{{ $item->id }}" @if ($item->id == ($idBuyer['value'] ?? null)) selected @endif>
                                    {{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-12 col-lg-2">
                    <label for="idStatus" class="form-label text-muted fw-bold">Status</label>
                </div>
                <div class="col-12 col-lg-10">
                    <div class="mb-1" wire:ignore>
                        <select class="form-control" wire:model.defer="status" data-choices data-choices-sorting-false
                            data-choices-removeItem data-choices-search-field-label>
                            <option value="">- All -</option>
                            <option value="0" @if (($status['value'] ?? '') == 0) selected @endif>Belum LPK</option>
                            <option value="1" @if (($status['value'] ?? '') == 1) selected @endif>Sudah LPK</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-12 mt-2">
            <div class="row">
                <div class="col-12 col-lg-6">
                    <button wire:click="search" type="button" class="btn btn-primary btn-load w-lg p-1" id="filterBtn"
                        wire:loading.attr="disabled">
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

                    <button type="button" class="btn btn-success w-lg p-1" onclick="window.location.href='/add-order'">
                        <i class="ri-add-line"> </i> Add
                    </button>
                </div>
                <div class="col-12 col-lg-6 d-none d-sm-block text-end">
                    <input type="file" id="fileInput" wire:model="file" style="display: none;">
                    <button class="btn btn-success w-lg p-1" type="button"
                        onclick="document.getElementById('fileInput').click()" wire:loading.attr="disabled">
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

                    <button class="btn btn-primary w-lg p-1" wire:click="download" type="button"
                        wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="download">
                            <i class="ri-download-cloud-2-line"> </i> Download Template
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
                    <button class="btn btn-info w-lg p-1" wire:click="print" type="button"
                        wire:loading.attr="disabled">
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

    </div>

    <div class="table-responsive table-card  mt-2  mb-2">
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
                            checked> PO Number
                    </label>
                </li>
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="2"
                            checked> Nama Produk
                    </label>
                </li>
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="3"
                            checked> Kode Produk
                    </label>
                </li>
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="4"
                            checked> Buyer
                    </label>
                </li>
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="5"
                            checked> Quantity
                    </label>
                </li>
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="6"
                            checked> Tgl. Order
                    </label>
                </li>
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="7"
                            unchecked> Stuffing
                    </label>
                </li>
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="8"
                            checked> Etd
                    </label>
                </li>
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="9"
                            unchecked> Eta
                    </label>
                </li>
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="10"
                            checked> Tgl Proses
                    </label>
                </li>
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="11"
                            unchecked> Update By
                    </label>
                </li>
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="12"
                            unchecked> Update On
                    </label>
                </li>
            </ul>
        </div>
        <table id="tableOrderLPK" class="table table-responsive table-bordered align-middle table-nowrap" style=" width:100%">
            <thead class="table-light">
                <tr>
                    <th></th>
                    <th>PO Number</th>
                    <th>Nama Produk</th>
                    <th>Kode Produk</th>
                    <th>Buyer</th>
                    <th>Quantity</th>
                    <th>Tgl. Order</th>
                    <th>Stuffing</th>
                    <th>Etd</th>
                    <th>Eta</th>
                    <th>Tgl Proses</th>
                    <th>Update By</th>
                    <th>Update On</th>
                </tr>
            </thead>
            <tbody class="list form-check-all">
                @forelse ($data as $item)
                    <tr>
                        <td class="text-center">
                            <a href="/edit-order?orderId={{ $item->id }}"
                                class="link-success fs-15 p-1 bg-primary rounded">
                                <i class="ri-edit-box-line text-white"></i>
                            </a>
                        </td>
                        <td>{{ $item->po_no }}</td>
                        <td class="text-start">{{ $item->produk_name }}</td>
                        <td>{{ $item->product_code }}</td>
                        <td>{{ $item->buyer_name }}</td>
                        <td>{{ number_format($item->order_qty) }}</td>
                        <td data-order="{{ $item->order_date }}">
                            {{ \Carbon\Carbon::parse($item->order_date)->format('d M Y') }}</td>
                        <td data-order="{{ $item->stufingdate }}">
                            {{ \Carbon\Carbon::parse($item->stufingdate)->format('d M Y') }}</td>
                        <td data-order="{{ $item->etddate }}">
                            {{ \Carbon\Carbon::parse($item->etddate)->format('d M Y') }}</td>
                        <td data-order="{{ $item->etadate }}">
                            {{ \Carbon\Carbon::parse($item->etadate)->format('d M Y') }}</td>
                        <td data-order="{{ $item->created_on }}">
                            {{ \Carbon\Carbon::parse($item->created_on)->format('d M Y') }}</td>
                        <td>{{ $item->updated_by }}</td>
                        <td data-order="{{ $item->updated_on }}">
                            {{ \Carbon\Carbon::parse($item->updated_on)->format('d M Y') }}</td>
                    </tr>
                @empty
                @endforelse
            </tbody>
        </table>
    </div>

    <style>
        #tableOrderLPK.table>:not(caption)>*>* {
            font-size: 13px !important;
            padding: 4px 2px 4px 4px;
            color: var(--tb-table-color-state, var(--tb-table-color-type, var(--tb-table-color)));
            background-color: var(--tb-table-bg);
            border-bottom-width: var(--tb-border-width);
            box-shadow: inset 0 0 0 9999px var(--tb-table-bg-state, var(--tb-table-bg-type, var(--tb-table-accent-bg)));
        }
    </style>
</div>

@script
    <script>
        // datatable
        // inisialisasi DataTable
        $wire.on('initDataTable', () => {
            initDataTable();
        });

        function calculateTableHeight() {
            const totalHeight = window.innerHeight;

            const offsetTop = document.querySelector('#tableOrderLPK')?.getBoundingClientRect().top || 0;
            const filterSectionTop = document.querySelector('.filter-section')?.getBoundingClientRect().top || 0;
            return totalHeight - offsetTop - filterSectionTop;
        }

        // Fungsi untuk menginisialisasi ulang DataTable
        function initDataTable() {
            const savedOrder = $wire.get('sortingTable');
            const savedEntriesPerPage = $wire.get('entriesPerPage');

            let defaultOrder = [
                [1, "asc"]
            ];
            if (savedOrder) {
                defaultOrder = savedOrder;
            }

            let entriesPerPage = 10;
            if (savedEntriesPerPage) {
                entriesPerPage = savedEntriesPerPage;
            }
            // Hapus DataTable jika sudah ada
            if ($.fn.dataTable.isDataTable('#tableOrderLPK')) {
                let table = $('#tableOrderLPK').DataTable();
                table.clear(); // Bersihkan data tabel
                table.destroy(); // Hancurkan DataTable
            }

            setTimeout(() => {
                // Inisialisasi ulang DataTable
                let table = $('#tableOrderLPK').DataTable({
                    "pageLength": entriesPerPage,
                    "searching": true,
                    "responsive": true,
                    "scrollX": true,
                    "order": defaultOrder,
                    "scrollY": calculateTableHeight() + 'px',
                    "scrollCollapse": true,
                    "language": {
                        "emptyTable": `
                            <div class="text-center">
                                <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop"
                                    colors="primary:#121331,secondary:#08a88a" style="width:40px;height:40px"></lord-icon>
                                <h5 class="mt-2">Record not found..!</h5>
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

                // Listen to page length change
                table.on('length.dt', function() {
                    let entriesPerPage = table.page.len();
                    $wire.call('updateEntriesPerPage', entriesPerPage);
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
