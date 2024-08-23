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
                                <option value="1">Produksi</option>
                                <option value="2">Proses</option>
                            </select>
                        </div>
                        <div class="col-9">
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
                    <input wire:model.defer.live="lpk_no" class="form-control"style="padding:0.44rem" type="text"
                        placeholder="000000-000" />
                </div>
            </div>
            <div class="col-12 col-lg-3">
                <label class="form-label text-muted fw-bold">Search</label>
            </div>
            <div class="col-12 col-lg-9">
                <div class="input-group">
                    <input wire:model.defer="searchTerm" class="form-control"style="padding:0.44rem" type="text"
                        placeholder="search nomor produksi, no han, dll" />
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
                    <select class="form-control" wire:model.defer="idProduct" data-choices data-choices-sorting-false
                        data-choices-removeItem>
                        <option value="">- All -</option>
                        @foreach ($products as $item)
                            <option data-custom-properties='{"code": "{{ $item->code }}"}' value="{{ $item->id }}"
                                @if ($item->id == ($idProduct['value'] ?? null)) selected @endif>{{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-12 col-lg-2">
                <label class="form-label text-muted fw-bold">Mesin</label>
            </div>
            <div class="col-12 col-lg-10">
                <div class="mb-1" wire:ignore>
                    <select class="form-control" wire:model.defer="machineId" data-choices data-choices-sorting-false
                        data-choices-removeItem>
                        <option value="">- All -</option>
                        @foreach ($machine as $item)
                            <option value="{{ $item->id }}" @if ($item->id == ($machineId['value'] ?? null)) selected @endif>
                                {{ $item->machineno }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-12 col-lg-2">
                <label for="status" class="form-label text-muted fw-bold">Status</label>
            </div>
            <div class="col-12 col-lg-10">
                <div class="mb-1" wire:ignore>
                    <select class="form-control" wire:model.defer="status" id="status" name="status" data-choices
                        data-choices-sorting-false data-choices-removeItem>
                        <option value="">- all -</option>
                        <option value="0" @if (($status['value'] ?? null) == 0) selected @endif>Open</option>
                        <option value="1" @if (($status['value'] ?? null) == 1) selected @endif>Seitai</option>
                        <option value="2" @if (($status['value'] ?? null) == 2) selected @endif>Kenpin</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-10 mt-2">
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

                <button type="button" class="btn btn-success w-lg p-1" onclick="window.location.href='/add-nippo'">
                    <i class="ri-add-line"> </i> Add
                </button>
            </div>
        </div>
    </div>
    <div class="col-lg-2 mt-2">
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

    <div class="table-responsive table-card mt-3 mb-1">
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
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="2"
                            checked> Tanggal LPK
                    </label>
                </li>
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="3"
                            checked> Panjang LPK
                    </label>
                </li>
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="4"
                            checked> Panjang Produksi
                    </label>
                </li>
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="5"
                            checked> Berat Gentan
                    </label>
                </li>
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="6"
                            checked> Nomor Gentan
                    </label>
                </li>
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="6"
                            checked> Berat Standard
                    </label>
                </li>
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="6"
                            checked> Rasio %
                    </label>
                </li>
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="6"
                            checked> Selisih
                    </label>
                </li>
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="6"
                            checked> Nama Produk
                    </label>
                </li>
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="6"
                            checked> Nomor Order
                    </label>
                </li>
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="6"
                            checked> Mesin
                    </label>
                </li>
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="6"
                            checked> Tanggal Produksi
                    </label>
                </li>
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="6"
                            checked> Tanggal Proses
                    </label>
                </li>
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="6"
                            checked> Jam
                    </label>
                </li>
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="6"
                            checked> Shift
                    </label>
                </li>
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="6"
                            checked> Seq
                    </label>
                </li>
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="6"
                            checked> Loss
                    </label>
                </li>
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="6"
                            checked> Update By
                    </label>
                </li>
                <li>
                    <label style="cursor: pointer;">
                        <input class="form-check-input fs-15 ms-2 toggle-column" type="checkbox" data-column="6"
                            checked> Updated
                    </label>
                </li>
            </ul>
        </div>
        <table class="table align-middle table-nowrap" id="tableInfure">
            <thead class="table-light">
                <tr>
                    <th></th>
                    <th>Nomor LPK</th>
                    <th>Tanggal LPK</th>
                    <th>Panjang LPK</th>
                    <th>Panjang Produksi</th>
                    <th>Berat Gentan</th>
                    <th>Nomor Gentan</th>
                    <th>Berat Standard</th>
                    <th>Rasio %</th>
                    <th>Selisih</th>
                    <th>Nama Produk</th>
                    <th>Nomor Order</th>
                    <th>Mesin</th>
                    <th>Tanggal Produksi</th>
                    <th>Tanggal Proses</th>
                    <th>Jam</th>
                    <th>Shift</th>
                    <th>Seq</th>
                    <th>Loss</th>
                    <th>Update By</th>
                    <th>Updated</th>
                </tr>
            </thead>
            <tbody class="list form-check-all">
                @forelse ($data as $item)
                    <tr>
                        <td>
                            <a href="/edit-nippo?orderId={{ $item->id }}"
                                class="link-success fs-15 p-1 bg-primary rounded">
                                <i class="ri-edit-box-line text-white"></i>
                            </a>
                        </td>
                        <td> {{ $item->lpk_no }} </td>
                        <td> {{ $item->lpk_date }} </td>
                        <td> {{ $item->panjang_lpk }}</td>
                        <td> {{ $item->panjang_produksi }} </td>
                        <td> {{ $item->berat_produksi }} </td>
                        <td> {{ $item->gentan_no }} </td>
                        <td> {{ $item->berat_standard }}</td>
                        <td> - </td>
                        <td> - </td>
                        <td> {{ $item->product_name }} </td>
                        <td> {{ $item->product_code }} </td>
                        <td> {{ $item->machineno }} </td>
                        <td> {{ $item->production_date }} </td>
                        <td> {{ $item->created_on }} </td>
                        <td> {{ $item->work_hour }} </td>
                        <td> {{ $item->work_shift }} </td>
                        <td> {{ $item->seq_no }} </td>
                        <td> {{ $item->infure_berat_loss }} </td>
                        <td> {{ $item->updated_by }} </td>
                        <td> {{ $item->updated_on }} </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="12" class="text-center">
                            <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop"
                                colors="primary:#121331,secondary:#08a88a" style="width:40px;height:40px"></lord-icon>
                            <h5 class="mt-2">Sorry! No Result Found</h5>
                            <p class="text-muted mb-0">We've searched more than 150+ Orders We did not find any orders
                                for you search.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        {{-- {{ $data->links(data: ['scrollTo' => false]) }} --}}
    </div>

</div>
@script
    <script>
        $wire.on('redirectToPrint', (datas) => {
            var printUrl = '{{ route('report-nippo-infure') }}?tanggal=' + datas;
            window.open(printUrl, '_blank');
        });
    </script>
@endscript

@push('scripts')
    <script>
        // datatable
        const table = $('#tableInfure').DataTable({
            "pageLength": 10,
            "searching": true,
            "responsive": true,
            "order": [
                [1, "asc"]
            ]
        });

        // Tambahkan event listener ke setiap checkbox
        document.querySelectorAll('.toggle-column').forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                let column = table.column($(this).attr('data-column'));
                column.visible(!column.visible());
            });
        });
    </script>
@endpush
