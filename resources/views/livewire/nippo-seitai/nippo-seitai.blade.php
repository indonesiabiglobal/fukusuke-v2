<div class="row filter-section">
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
                                <option value="2">Produksi</option>
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
            <div class="col-12 col-lg-9 mb-1" x-data="{
                    lpk_no_local: @entangle('lpk_no'),
                    status: true,
                    formatValue(value) {
                        if (value.length === 6 && !value.includes('-') && this.status) {
                            value += '-';
                        }
                        if (value.length < 6) this.status = true;
                        if (value.length === 7) this.status = false;
                        if (value.length > 10) value = value.substring(0, 10);
                        return value;
                    }
                }" x-defer>
                    <input class="form-control" style="padding:0.44rem" type="text" placeholder="000000-000"
                        x-model="lpk_no_local" x-on:input="lpk_no_local = formatValue(lpk_no_local)" maxlength="10" />
                </div>
            <div class="col-12 col-lg-3">
                <label class="form-label text-muted fw-bold">Search</label>
            </div>
            <div class="col-12 col-lg-9">
                <div class="input-group">
                    <input wire:model.defer="searchTerm" class="form-control"style="padding:0.44rem" type="text"
                        placeholder="Search no produksi, no palet, no lot, dll" />
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
                            <option data-custom-properties='{"code": "{{ $item->code }}"}' value="{{ $item->id }}"
                                @if ($item->id == ($idProduct['value'] ?? null)) selected @endif>{{ $item->name }},
                                {{ $item->code }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-12 col-lg-2">
                <label for="buyer" class="form-label text-muted fw-bold">Mesin</label>
            </div>
            <div class="col-12 col-lg-10">
                <div class="mb-1" wire:ignore>
                    <select class="form-control" wire:model.defer="machineId" data-choices data-choices-sorting-true
                        data-choices-removeItem data-choices-sorter data-choices-search-field-label>
                        <option value="">- All -</option>
                        @foreach ($machine as $item)
                            <option data-custom-properties='{"code": "{{ $item->machineno }}"}'
                                value="{{ $item->id }}" @if ($item->id == ($machineId['value'] ?? null)) selected @endif>
                                {{ $item->machineno }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-12 col-lg-2">
                <label for="status" class="form-label text-muted fw-bold">Status</label>
            </div>
            <div class="col-12 col-lg-10">
                <div class="mb-1" wire:ignore>
                    <select class="form-control" wire:model.defer="status" data-choices data-choices-sorting-false
                        data-choices-removeItem data-choices-search-field-label>
                        <option value="">- All -</option>
                        <option value="0">Open</option>
                        <option value="1" @if (($status['value'] ?? null) == 1) selected @endif>Seitai</option>
                        <option value="2" @if (($status['value'] ?? null) == 2) selected @endif>Kenpin</option>
                    </select>
                </div>
            </div>
            <div class="col-12 col-lg-2">
                <label class="form-label text-muted fw-bold">Nomor Gentan</label>
            </div>
            <div class="col-12 col-lg-10">
                <div class="input-group">
                    <input wire:model.defer="gentan_no" class="form-control"style="padding:0.44rem" type="text"
                        placeholder="Nomor Gentan" />
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-12 mt-2">
        <div class="row">
            <div class="col-12 col-lg-10">
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

                <button type="button" class="btn btn-success w-lg p-1" onclick="window.location.href='/add-seitai?lpk_no={{ $lpk_no }}'">
                    <i class="ri-add-line"> </i> Add
                </button>
            </div>
            <div class="col-12 col-lg-2">
                <button class="btn btn-info w-lg p-1" wire:click="export" type="button"
                    wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="export">
                        <i class="ri-printer-line"> </i> Print
                    </span>
                    <div wire:loading wire:target="export">
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
                            checked> Nomor LPK
                    </label>
                </li>
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="2">
                        Tanggal LPK
                    </label>
                </li>
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="3"
                            checked> Jumlah LPK
                    </label>
                </li>
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="4"
                            checked> Jumlah Produksi
                    </label>
                </li>
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="5">
                        Selisih
                    </label>
                </li>
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="6"
                            checked> Loss Seitai
                    </label>
                </li>
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="7">
                        Loss
                    </label>
                </li>
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="8">
                        Nama Produk
                    </label>
                </li>
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="9"
                            checked> Nomor Order
                    </label>
                </li>
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="10"
                            checked> Mesin
                    </label>
                </li>
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="11"
                            checked> Tanggal Produksi
                    </label>
                </li>
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="12"
                            checked> Tanggal Proses
                    </label>
                </li>
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="13"
                            checked> Jam
                    </label>
                </li>
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="14"
                            checked> Shift
                    </label>
                </li>
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="15">
                        Nomor Palet
                    </label>
                </li>
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="16">
                        Nomor Lot
                    </label>
                </li>
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="17"
                            checked> Seq
                    </label>
                </li>
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="18">
                        Update By
                    </label>
                </li>
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="19">
                        Updated
                    </label>
                </li>
            </ul>
        </div>
        <table class="table align-middle table-nowrap" id="seitaiTable">
            <thead class="table-light">
                <tr>
                    <th></th>
                    <th>Nomor LPK</th>
                    <th>Tanggal LPK</th>
                    <th>Jumlah LPK</th>
                    <th>Jumlah Produksi</th>
                    <th>Selisih</th>
                    <th>Loss Seitai</th>
                    <th>Loss</th>
                    <th>Nama Produk</th>
                    <th>Nomor Order</th>
                    <th>Mesin</th>
                    <th>Tanggal Produksi</th>
                    <th>Tanggal Proses</th>
                    <th>Jam</th>
                    <th>Shift</th>
                    <th>Nomor Palet</th>
                    <th>Nomor Lot</th>
                    <th>Seq</th>
                    <th>Update By</th>
                    <th>Updated</th>
                </tr>
            </thead>
            <tbody class="list form-check-all">
                @forelse ($data as $item)
                    <tr>
                        <td>
                            <a href="/edit-seitai?orderId={{ $item->id }}"
                                class="link-success fs-15 p-1 bg-primary rounded">
                                <i class="ri-edit-box-line text-white"></i>
                            </a>
                        </td>
                        <td>{{ $item->lpk_no }}</td>
                        <td data-order="{{ $item->lpk_date }}">{{ \Carbon\Carbon::parse($item->lpk_date)->format('d M Y') }}</td>
                        <td>{{ number_format($item->qty_lpk, 0, ',', ',') }}</td>
                        <td>{{ number_format($item->qty_produksi, 0, ',', ',') }}</td>
                        <td>{{ number_format($item->selisih) }}</td>
                        <td>{{ number_format($item->seitai_berat_loss, 2, ',', ',') }}</td>
                        <td>{{ number_format($item->infure_berat_loss, 2, ',', ',') }}</td>
                        <td>{{ $item->product_name }}</td>
                        <td>{{ $item->code }}</td>
                        <td>{{ $item->machineno }}</td>
                        <td data-order="{{ $item->production_date }}">{{ \Carbon\Carbon::parse($item->production_date)->format('d M Y') }}</td>
                        <td data-order="{{ $item->created_on }}">{{ \Carbon\Carbon::parse($item->created_on)->format('d M Y') }}</td>
                        <td>{{ $item->work_hour }}</td>
                        <td>{{ $item->work_shift }}</td>
                        <td>{{ $item->nomor_palet }}</td>
                        <td>{{ $item->nomor_lot }}</td>
                        <td>{{ $item->seq_no }}</td>
                        <td>{{ $item->updated_by }}</td>
                        <td data-order="{{ $item->updated_on }}">{{ \Carbon\Carbon::parse($item->updated_on)->format('d M Y H:i') }}</td>
                    </tr>
                @empty
                @endforelse
            </tbody>
        </table>
    </div>
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

            const filterSectionTop = document.querySelector('.filter-section')?.getBoundingClientRect().top || 0;
            const offsetTop = document.querySelector('#seitaiTable')?.getBoundingClientRect().top || 0;

            const paddingTop = document.querySelector('.navbar-header')?.getBoundingClientRect().top || 0;
            const availableHeight = totalHeight - offsetTop - filterSectionTop - paddingTop + 100;

            return availableHeight;
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
            if ($.fn.dataTable.isDataTable('#seitaiTable')) {
                let table = $('#seitaiTable').DataTable();
                table.clear(); // Bersihkan data tabel
                table.destroy(); // Hancurkan DataTable
                // Hindari penggunaan $('#seitaiTable').empty(); di sini
            }

            setTimeout(() => {
                // Inisialisasi ulang DataTable
                let table = $('#seitaiTable').DataTable({
                    "pageLength": entriesPerPage,
                    "searching": true,
                    "responsive": true,
                    "order": defaultOrder,
                    "multiColumnSort": true,
                    "scrollX": true,
                    "scrollY": calculateTableHeight() + 'px',
                    "scrollCollapse": true,
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
