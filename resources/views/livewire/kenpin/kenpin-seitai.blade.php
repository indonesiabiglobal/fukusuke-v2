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
                    <div class="col-12 col-lg-12 mb-1" x-data="{ searchTerm: @entangle('searchTerm'), status: true }" x-init="$watch('searchTerm', value => {
                            if (value.length === 7 && !value.includes('-') && status) {
                                searchTerm = value + '-';
                            }
                            if (value.length < 7) {
                                status = true;
                            }
                            if (value.length === 11) {
                                status = false;
                            }
                            if (value.length > 11) {
                                searchTerm = value.substring(0, 11);
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
    <div class="col-12 col-lg-6">
        <div class="row">
            <div class="col-12 col-lg-3">
                <label for="product" class="form-label text-muted fw-bold">Product</label>
            </div>
            <div class="col-12 col-lg-9 mb-1">
                <div wire:ignore>
                    <select class="form-control" style="padding:0.44rem" wire:model.defer="idProduct" data-choices
                        data-choices-sorting-false data-choices-removeItem data-choices-search-field-label data-choices-exact-match>
                        <option value="">- All -</option>
                        @foreach ($products as $item)
                            <option data-custom-properties='{"code": "{{ $item->code }}"}' value="{{ $item->id }}"
                                @if ($item->id == ($idProduct['value'] ?? null)) selected @endif>{{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-12 col-lg-3">
                <label class="form-label text-muted fw-bold">Nomor Palet</label>
            </div>
            <div class="col-12 col-lg-9 mb-1">
                <div class="input-group">
                    {{-- <input type="text" class="form-control" style="padding:0.44rem" placeholder="A0000-000000"
                        wire:model.defer="nomor_palet"> --}}
                        <div class="col-6 col-lg-6 mb-1" x-data="{ nomor_palet: @entangle('nomor_palet'), status: true }" x-init="$watch('nomor_palet', value => {
                            if (value.length === 5 && !value.includes('-') && status) {
                                nomor_palet = value + '-';
                            }
                            if (value.length < 5) {
                                status = true;
                            }
                            if (value.length === 6) {
                                status = false;
                            }
                            if (value.length > 12) {
                                nomor_palet = value.substring(0, 12);
                            }
                        })">
                        <input
                            class="form-control"
                            type="text"
                            placeholder="A0000-000000"
                            x-model="nomor_palet"
                            maxlength="12"
                        />
                    </div>
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
    <div class="col-lg-12 mt-2">
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
                    onclick="window.location.href='/add-kenpin-seitai'">
                    <i class="ri-add-line"> </i> Add
                </button>
            </div>
        </div>
    </div>

    <div class="table-responsive table-card">
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
                            checked> Tgl. Kenpin
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
                            checked> Nama Produk
                    </label>
                </li>
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="4"
                            checked> No. Order
                    </label>
                </li>
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="5"
                            checked> Petugas
                    </label>
                </li>
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="6"
                            checked> Jumlah Loss (lbr)
                    </label>
                </li>
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="7"
                            checked> Status
                    </label>
                </li>
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="8"
                            checked> Department
                    </label>
                </li>
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="9">
                        Update By
                    </label>
                </li>
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="10">
                        Updated
                    </label>
                </li>
            </ul>
        </div>
        <table class="table align-middle"  id="kenpinSeitaiTable">
            <thead class="table-light">
                <tr>
                    <th>Action</th>
                    <th>Tgl. Kenpin</th>
                    <th>No Kenpin</th>
                    <th>Nama Produk</th>
                    <th>No. Order</th>
                    <th>Petugas</th>
                    <th>Jumlah Loss (lbr)</th>
                    <th>Status</th>
                    <th>Department</th>
                    <th>Update By</th>
                    <th>Updated</th>
                </tr>
            </thead>
            <tbody class="list form-check-all">
                @forelse ($data as $item)
                    <tr>
                        <td>
                            <a href="/edit-kenpin-seitai?orderId={{ $item->id }}"
                                class="link-success fs-15 p-1 bg-primary rounded">
                                <i class="ri-edit-box-line text-white"></i>
                            </a>
                        </td>
                        <td data-order="{{ $item->kenpin_date }}">{{ \Carbon\Carbon::parse($item->kenpin_date)->format('d M Y') }}</td>
                        <td>{{ $item->kenpin_no }}</td>
                        <td>{{ $item->namaproduk }}</td>
                        <td>{{ $item->code }}</td>
                        <td>{{ $item->namapetugas }}</td>
                        <td>{{ number_format($item->qty_loss) }}</td>
                        <td>{{ $item->status_kenpin == 2 ? 'Finish' : 'Proses'  }}</td>
                        <td>{{ $item->nama_department }}</td>
                        <td>{{ $item->updated_by }}</td>
                        <td data-order="{{ $item->updated_on }}">{{ \Carbon\Carbon::parse($item->updated_on)->format('d M Y') }}</td>
                    </tr>
                @empty
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
            if ($.fn.dataTable.isDataTable('#kenpinSeitaiTable')) {
                let table = $('#kenpinSeitaiTable').DataTable();
                table.clear(); // Bersihkan data tabel
                table.destroy(); // Hancurkan DataTable
                // Hindari penggunaan $('#kenpinSeitaiTable').empty(); di sini
            }

            setTimeout(() => {
                // Inisialisasi ulang DataTable
                let table = $('#kenpinSeitaiTable').DataTable({
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
