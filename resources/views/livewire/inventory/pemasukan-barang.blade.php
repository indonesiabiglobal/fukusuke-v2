{{-- @include('layouts.customizer') --}}
<div class="row">
    <div class="col-12 col-lg-6">
        <div class="row">
            <div class="col-12 col-lg-3">
                <label class="form-label text-muted fw-bold">Filter Tanggal</label>
            </div>
            <div class="col-12 col-lg-9 mb-1">
                <div class="form-group" wire:ignore>
                    <div class="input-group">
                        <div class="col-12">
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
                        placeholder="No pabean, Kode Barang, Nama Barang" />
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-6">
        <div class="row">
            <div class="col-12 col-lg-2">
                <label class="form-label text-muted fw-bold">Jenis Pabean</label>
            </div>
            <div class="col-12 col-lg-10">
                <div class="mb-1" wire:ignore>
                    <select class="form-control" wire:model.defer="jenis_pabean" data-choices
                        data-choices-sorting-false data-choices-removeItem data-choices-search-field-label>
                        <option value=""></option>
                        <option value="BC 2.0">BC 2.0</option>
                        <option value="BC 2.3">BC 2.3</option>
                        <option value="BC 2.5">BC 2.5</option>
                        <option value="BC 2.6.1">BC 2.6.1</option>
                        <option value="BC 2.6.2">BC 2.6.2</option>
                        <option value="BC 2.7">BC 2.7</option>
                        <option value="BC 3.0">BC 3.0</option>
                        <option value="BC 4.0">BC 4.0</option>
                        <option value="BC 4.1">BC 4.1</option>
                    </select>
                </div>
            </div>

            <div class="col-12 col-lg-2">
                <label for="buyer" class="form-label text-muted fw-bold">Group Barang</label>
            </div>
            <div class="col-12 col-lg-10">
                <div class="mb-1" wire:ignore>
                    <select class="form-control" wire:model.defer="idBuyer" data-choices data-choices-sorting-false
                        data-choices-removeItem data-choices-search-field-label>
                        <option value=""></option>
                        <option value="">MATERIAL</option>
                        <option value="">WIP</option>
                        <option value="">SCRAP</option>
                        <option value="">MESIN</option>
                        <option value="">PRODUCT</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-12 mt-2">
        <div class="row">
            <div class="col-12 col-lg-6">
                <button wire:click="search" type="button" class="btn btn-primary btn-load w-lg p-1" id="filterBtn" wire:loading.attr="disabled">
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
            <div class="col-12 col-lg-6 d-none d-sm-block text-end">
                <button class="btn btn-info w-lg p-1" wire:click="print" type="button" wire:loading.attr="disabled">
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
                            checked> Jenis Pabean
                    </label>
                </li>
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="2"
                            checked> No Pabean
                    </label>
                </li>
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="3"
                            checked> Tgl Pabean
                    </label>
                </li>
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="4"
                            checked> No Bukti
                    </label>
                </li>
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="5"
                            checked> Tgl Bukti
                    </label>
                </li>
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="6"
                            checked> Pengirim Barang
                    </label>
                </li>
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="7"
                            checked> Kode Barang
                    </label>
                </li>
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="8"
                        checked> Nama Barang
                    </label>
                </li>
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="9"
                            checked> Jumlah
                    </label>
                </li>
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="10"
                        checked> Sat
                    </label>
                </li>
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="11"
                            checked> Valas
                    </label>
                </li>
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="12"
                        checked> Nilai
                    </label>
                </li>
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="13"
                        checked> Ket
                    </label>
                </li>
            </ul>
        </div>
        <table id="tablePemasukanBarang" class="table table-responsive table-bordered align-middle" style=" width:100%">
            <thead class="table-light">
                <tr>
                    <th>JENIS PABEAN</th>
                    <th>NO PABEAN</th>
                    <th>TGL. PABEAN</th>
                    <th>NO. BUKTI</th>
                    <th>TGL BUKTI</th>
                    <th>PENGIRIM BARANG</th>
                    <th>KODE BARANG</th>
                    <th>NAMA BARANG</th>
                    <th>JUMLAH</th>
                    <th>SAT</th>
                    <th>VALAS</th>
                    <th>NILAI</th>
                    <th>KET</th>
                </tr>
            </thead>
            <tbody class="list form-check-all">
                @forelse ($data as $item)
                    <tr>
                        <td>{{ $item->jenis_pabean }}</td>
                        <td>{{ $item->no_pabean }}</td>
                        <td>{{ $item->tgl_pabean }}</td>
                        <td>{{ $item->vend_dlv_no }}</td>
                        <td>{{ $item->trans_date }}</td>
                        <td>{{ $item->vendor_name }}</td>
                        <td>{{ $item->item_code }}</td>
                        <td>{{ $item->item_name }}</td>
                        <td>{{ $item->rcv_qty }}</td>
                        <td>{{ $item->pch_unit }}</td>
                        <td>{{ $item->curr_code }}</td>
                        <td>{{ $item->net_price }}</td>
                        <td>{{ $item->ket }}</td>
                        {{-- <td>{{ number_format($item->order_qty) }}</td> --}}
                        {{-- <td>{{ \Carbon\Carbon::parse($item->order_date)->format('d M Y') }}</td> --}}
                    </tr>
                @empty
                    {{-- <tr>
                        <td colspan="13" class="text-center">
                            <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop"
                                colors="primary:#121331,secondary:#08a88a" style="width:40px;height:40px"></lord-icon>
                            <h5 class="mt-2">Record not found..!</h5>
                        </td>
                    </tr> --}}
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
            if ($.fn.dataTable.isDataTable('#tablePemasukanBarang')) {
                let table = $('#tablePemasukanBarang').DataTable();
                table.clear(); // Bersihkan data tabel
                table.destroy(); // Hancurkan DataTable
                // Hindari penggunaan $('#tablePemasukanBarang').empty(); di sini
            }

            setTimeout(() => {
                // Inisialisasi ulang DataTable
                let table = $('#tablePemasukanBarang').DataTable({
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
