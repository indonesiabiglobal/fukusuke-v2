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
                                    <option value="2">LPK</option>
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
                    <label class="form-label text-muted fw-bold">Nomor LPK</label>
                </div>
                <div class="col-12 col-lg-9 mb-1">
                    <div class="input-group">
                        <input wire:model="lpk_no" class="form-control" style="padding:0.44rem" type="text"
                            placeholder="000000-000" x-data="{ lpk_no: '', status: true }" x-init="$watch('lpk_no', value => {
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
                                    lpk_no = value.substring(0, 11);
                                }
                            })" x-model="lpk_no"
                            maxlength="10" />
                    </div>
                    {{-- <div class="input-group">
                    <input wire:model.defer="lpk_no" class="form-control" style="padding:0.44rem" type="text"
                        placeholder="000000-000" />
                </div> --}}
                </div>
                <div class="col-12 col-lg-3">
                    <label class="form-label text-muted fw-bold">Search</label>
                </div>
                <div class="col-12 col-lg-9">
                    <div class="input-group">
                        <input wire:model.defer="searchTerm" class="form-control"style="padding:0.44rem" type="text"
                            placeholder="search nomor PO atau nama produk" />
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
                        <select class="form-control" wire:model.defer="idProduct" data-choices data-choices-sorting-true
                            data-choices-removeItem data-choices-sorter data-choices-search-field-label>
                            <option value="">- All -</option>
                            @foreach ($products as $item)
                                <option data-custom-properties='{"code": "{{ $item->code }}"}'
                                    value="{{ $item->id }}" @if ($item->id == ($idProduct['value'] ?? null)) selected @endif>
                                    {{ $item->name }}
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-12 col-lg-2">
                    <label for="lpkColor" class="form-label text-muted fw-bold">LPK Color</label>
                </div>
                <div class="col-12 col-lg-10">
                    <div class="mb-1" wire:ignore>
                        <select class="form-control" wire:model.defer="idLPKColor" data-choices
                            data-choices-sorting-true data-choices-removeItem data-choices-sorter
                            data-choices-search-field-label>
                            <option value="">- All -</option>
                            @foreach ($lpkColors as $item)
                                <option data-custom-properties='{"code": "{{ $item->code }}"}'
                                    value="{{ $item->id }}" @if ($item->id == ($idLPKColor['value'] ?? null)) selected @endif>
                                    {{ $item->name }}
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-12 col-lg-2">
                    <label for="buyer" class="form-label text-muted fw-bold">Buyer</label>
                </div>
                <div class="col-12 col-lg-10">
                    <div class="mb-1" wire:ignore>
                        <select class="form-control" wire:model.defer="idBuyer" id="buyer" name="buyer"
                            data-choices data-choices-sorting-true data-choices-removeItem data-choices-sorter
                            data-choices-search-field-label>
                            <option value="">- All -</option>
                            @foreach ($buyer as $item)
                                <option data-custom-properties='{"code": "{{ $item->code }}"}'
                                    value="{{ $item->id }}" @if ($item->id == ($idBuyer['value'] ?? null)) selected @endif>
                                    {{ $item->name }}
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-12 col-lg-2">
                    <label for="status" class="form-label text-muted fw-bold">Status</label>
                </div>
                <div class="col-12 col-lg-10">
                    <div class="mb-1" wire:ignore>
                        <select class="form-control" wire:model.defer="status" id="status" name="status"
                            data-choices data-choices-sorting-false data-choices-removeItem
                            data-choices-search-field-label>
                            <option value="">- All -</option>
                            <option value="0" @if (($status['value'] ?? '') == 0) selected @endif>Un-Print</option>
                            <option value="1" @if (($status['value'] ?? '') == 1) selected @endif>Printed</option>
                            <option value="2" @if (($status['value'] ?? '') == 2) selected @endif>Re-Print</option>
                            <option value="3" @if (($status['value'] ?? '') == 3) selected @endif>Belum Produksi
                            </option>
                            <option value="4" @if (($status['value'] ?? '') == 4) selected @endif>Sudah Produksi
                            </option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-12 mt-2">
            <div class="row">
                <div class="col-12 col-lg-5">
                    <button wire:click="search" type="button" class="btn btn-primary btn-load w-lg p-1"
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

                    <button type="button" class="btn btn-success w-lg p-1"
                        onclick="window.location.href='/add-lpk'">
                        <i class="ri-add-line"> </i> Add
                    </button>
                </div>
                <div class="col-12 col-lg-7 d-none d-sm-block">
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
                            <i class="ri-printer-line"> </i> Export
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
                    {{-- cetak lpk --}}
                    <button class="btn btn-info w-lg p-1" wire:click="printLPK" type="button"
                        wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="printLPK">
                            <i class="ri-printer-line"> </i> Cetak LPK
                        </span>
                        <div wire:loading wire:target="printLPK">
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
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="2"
                            checked> No LPK
                    </label>
                </li>
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="3">
                        Warna LPK
                    </label>
                </li>
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="4"
                            checked> Tgl LPK
                    </label>
                </li>
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="5"
                            checked> Panjang LPK
                    </label>
                </li>
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="6"
                            checked> Jumlah LPK
                    </label>
                </li>
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="7"
                            checked> Jumlah Gentan
                    </label>
                </li>
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="8"
                            checked> Meter Gulung
                    </label>
                </li>
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="9">
                        Selisih
                    </label>
                </li>
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="10"
                            checked> Progres Infure
                    </label>
                </li>
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="11"
                            checked> Progres Seitai
                    </label>
                </li>
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="12"
                            checked> Nomor PO
                    </label>
                </li>
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="13">
                        Nama Produk
                    </label>
                </li>
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="14"
                            checked> Kode Produk
                    </label>
                </li>
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="15">
                        Mesin
                    </label>
                </li>
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="16">
                        Buyer
                    </label>
                </li>
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="17"
                            checked> Tanggal Proses
                    </label>
                </li>
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="18">
                        seq
                    </label>
                </li>
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="19">
                        Update By
                    </label>
                </li>
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="20">
                        Updated
                    </label>
                </li>
            </ul>
        </div>
        <table class="table align-middle" id="LPKEntryTable">
            <thead class="table-light">
                <tr>
                    <th scope="col" style="width: 10px;">
                        <div class="form-check">
                            <input class="form-check-input checkbox-big  fs-15" type="checkbox" id="checkAll"
                                value="optionAll">
                        </div>
                    </th>
                    <th></th>
                    <th>No LPK</th>
                    <th>Warna LPK</th>
                    <th>Tgl LPK</th>
                    <th>Panjang LPK</th>
                    <th>Jumlah LPK</th>
                    <th>Jumlah Gentan</th>
                    <th>Master Gulung</th>
                    <th>Selisih</th>
                    <th>Progres Infure</th>
                    <th>Progres Seitai</th>
                    <th>Nomor PO</th>
                    <th>Nama Produk</th>
                    <th>Kode Produk</th>
                    <th>Mesin</th>
                    <th>Buyer</th>
                    <th>Tanggal Proses</th>
                    <th>seq</th>
                    <th>Update By</th>
                    <th>Updated</th>
                </tr>
            </thead>
            <tbody class="list form-check-all">
                @forelse ($data as $item)
                    <tr>
                        <td scope="row">
                            <div class="form-check text-center">
                                <input class="form-check-input fs-15 checkbox-big checkListLPK" type="checkbox"
                                    wire:model="checkListLPK" value="{{ $item->id }}">
                            </div>
                        </td>
                        <td>
                            <a href="/edit-lpk?orderId={{ $item->id }}"
                                class="link-success fs-15 p-1 bg-primary rounded">
                                <i class="ri-edit-box-line text-white"></i>
                            </a>
                        </td>
                        <td>{{ $item->lpk_no }}</td>
                        <td>{{ $item->warna_lpk }}</td>
                        <td>{{ \Carbon\Carbon::parse($item->lpk_date)->format('d M Y') }}</td>
                        <td>{{ number_format($item->panjang_lpk) }}</td>
                        <td>{{ number_format($item->qty_lpk) }}</td>
                        <td>{{ $item->qty_gentan }}</td>
                        <td>{{ number_format($item->qty_gulung) }}</td>
                        <td>{{ number_format($item->selisih) }}</td>
                        <td>{{ number_format($item->infure) }}</td>
                        <td>{{ number_format($item->total_assembly_qty) }}</td>
                        <td>{{ $item->po_no }}</td>
                        <td>{{ $item->product_name }}</td>
                        <td>{{ $item->product_code }}</td>
                        <td>{{ $item->machine_no }}</td>
                        <td>{{ $item->buyer_name }}</td>
                        {{-- <td>{{ $item->warnalpk }}</td> --}}
                        <td data-order="{{ $item->created_on }}">
                            {{ \Carbon\Carbon::parse($item->created_on)->format('d M Y') }}</td>
                        <td>{{ $item->seq_no }}</td>
                        <td>{{ $item->updated_by }}</td>
                        <td data-order="{{ $item->updatedt }}">
                            {{ \Carbon\Carbon::parse($item->updatedt)->format('d M Y') }}</td>
                    </tr>
                @empty
                @endforelse
            </tbody>
        </table>
    </div>

    <style>
        .checkbox-big {
            width: 20px;
            height: 20px;
            border: 2px solid #333;
            cursor: pointer;
        }

        .checkbox-big:checked {
            background-color: #0d6efd;
            /* biru bootstrap */
            border-color: #0d6efd;
        }

        #LPKEntryTable.table>:not(caption)>*>* {
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
        // memilih seluruh data pada table

        $wire.on('redirectToPrint', (lpk_ids) => {
            var printUrl = '{{ route('report-lpk') }}?lpk_ids=' + lpk_ids
            window.open(printUrl, '_blank');
        });


        // datatable
        // inisialisasi DataTable
        $wire.on('initDataTable', () => {
            initDataTable();
        });

        function calculateTableHeight() {
            const totalHeight = window.innerHeight;

            const offsetTop = document.querySelector('#LPKEntryTable')?.getBoundingClientRect().top || 0;
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
            if ($.fn.dataTable.isDataTable('#LPKEntryTable')) {
                let table = $('#LPKEntryTable').DataTable();
                table.clear(); // Bersihkan data tabel
                table.destroy(); // Hancurkan DataTable
            }

            setTimeout(() => {
                // Inisialisasi ulang DataTable
                let table = $('#LPKEntryTable').DataTable({
                    "pageLength": entriesPerPage,
                    "searching": true,
                    "responsive": true,
                    "scrollX": true,
                    "scrollY": calculateTableHeight() + 'px',
                    "order": defaultOrder,
                    "orderCellsTop": true,
                    "scrollCollapse": true,
                    "columnDefs": [{
                        "orderable": false,
                        "targets": [0, 1]
                    }],
                    "language": {
                        "emptyTable": `
                            <div class="text-center">
                                <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop"
                                    colors="primary:#121331,secondary:#08a88a" style="width:40px;height:40px"></lord-icon>
                                <h5 class="mt-2">Sorry! No Result Found</h5>
                            </div>
                        `
                    },
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

                // Meng-handle check all
                $('#checkAll').click(function() {
                    var isChecked = $(this).is(':checked');
                    $('.checkListLPK').each(function() {
                        $(this).prop('checked', isChecked);
                        $(this).trigger('change');
                    });
                    // Update Livewire saat "check all" berubah
                    @this.set('checkListLPK', $('.checkListLPK:checked').map(function() {
                        return this.value;
                    }).get(), false);
                });

                // Jika ada perubahan pada checkbox individual
                $('.checkListLPK').change(function() {
                    // Perbarui status "check all"
                    $('#checkAll').prop('checked', $('.checkListLPK:checked').length == $('.checkListLPK')
                        .length);
                    // Update Livewire saat checkbox individual berubah
                    @this.set('checkListLPK', $('.checkListLPK:checked').map(function() {
                        return this.value;
                    }).get(), false);
                });
            }, 500);
        }
    </script>
@endscript
