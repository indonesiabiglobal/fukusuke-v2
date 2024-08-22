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
                    <input wire:model.defer.live="lpk_no" class="form-control"style="padding:0.44rem" type="text" placeholder="000000-000" />
                </div>
            </div>
            <div class="col-12 col-lg-3">
                <label class="form-label text-muted fw-bold">Search</label>
            </div>
            <div class="col-12 col-lg-9">
                <div class="input-group">
                    <input wire:model.defer="searchTerm" class="form-control"style="padding:0.44rem" type="text" placeholder="search nomor produksi, no han, dll" />
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
                    <select class="form-control" wire:model.defer="idProduct" data-choices data-choices-sorting-false data-choices-removeItem>
                        <option value="">- All -</option>
                        @foreach ($products as $item)
                            <option data-custom-properties='{"code": "{{ $item->code }}"}' value="{{ $item->id }}" @if ($item->id == ($idProduct['value'] ?? null)) selected @endif>{{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-12 col-lg-2">
                <label class="form-label text-muted fw-bold">Mesin</label>
            </div>
            <div class="col-12 col-lg-10">
                <div class="mb-1" wire:ignore>
                    <select class="form-control" wire:model.defer="machineId" data-choices data-choices-sorting-false data-choices-removeItem>
                        <option value="">- All -</option>
                        @foreach ($machine as $item)
                            <option value="{{ $item->id }}" @if ($item->id == ($machineId['value'] ?? null)) selected @endif>{{ $item->machineno }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-12 col-lg-2">
                <label for="status" class="form-label text-muted fw-bold">Status</label>
            </div>
            <div class="col-12 col-lg-10">
                <div class="mb-1" wire:ignore>
                    <select class="form-control" wire:model.defer="status" id="status" name="status" data-choices data-choices-sorting-false data-choices-removeItem>
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

                <button
                    type="button"
                    class="btn btn-success w-lg p-1"
                    onclick="window.location.href='/add-nippo'"
                    >
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

    <div class="col text-end dropdown" x-data="{
        lpk_date:false, lpk_panjang:false, produksi_panjang:true, gentan_berat:true, gentan_nomor:true, standard_berat:true, rasio:false,
        selisih:false, produk_nama:false, order_nomor:true, mesin:true, produksi_tanggal:true, proses_tanggal:true, jam:true, shift:true,
        seq:true, loss:false, by_update:false, updated:false,
        }">
        <button type="button" data-bs-toggle="dropdown" aria-expanded="false" class="btn btn-soft-primary btn-icon fs-14 mb-4">
            <i class="ri-grid-fill"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
            <li @click="lpk_date = !lpk_date; $refs.checkbox.checked = lpk_date" style="cursor: pointer;">
                <input x-ref="checkbox" @change="lpk_date = $refs.checkbox.checked" class="form-check-input fs-15 ms-2" type="checkbox" :checked="lpk_date">
                Tanggal LPK
            </li>
            <li @click="lpk_panjang = !lpk_panjang; $refs.checkbox.checked = lpk_panjang" style="cursor: pointer;">
                <input x-ref="checkbox" @change="lpk_panjang = $refs.checkbox.checked" class="form-check-input fs-15 ms-2" type="checkbox" :checked="lpk_panjang">
                Panjang LPK
            </li>
            <li @click="produksi_panjang = !produksi_panjang; $refs.checkbox.checked = produksi_panjang" style="cursor: pointer;">
                <input x-ref="checkbox" @change="produksi_panjang = $refs.checkbox.checked" class="form-check-input fs-15 ms-2" type="checkbox" :checked="produksi_panjang">
                Panjang Produksi
            </li>
            <li @click="gentan_berat = !gentan_berat; $refs.checkbox.checked = gentan_berat" style="cursor: pointer;">
                <input x-ref="checkbox" @change="gentan_berat = $refs.checkbox.checked" class="form-check-input fs-15 ms-2" type="checkbox" :checked="gentan_berat">
                Berat Gentan
            </li>
            <li @click="gentan_nomor = !gentan_nomor; $refs.checkbox.checked = gentan_nomor" style="cursor: pointer;">
                <input x-ref="checkbox" @change="gentan_nomor = $refs.checkbox.checked" class="form-check-input fs-15 ms-2" type="checkbox" :checked="gentan_nomor">
                Nomor Gentan
            </li>
            <li @click="standard_berat = !standard_berat; $refs.checkbox.checked = standard_berat" style="cursor: pointer;">
                <input x-ref="checkbox" @change="standard_berat = $refs.checkbox.checked" class="form-check-input fs-15 ms-2" type="checkbox" :checked="standard_berat">
                Berat Standard
            </li>
            <li @click="rasio = !rasio; $refs.checkbox.checked = rasio" style="cursor: pointer;">
                <input x-ref="checkbox" @change="rasio = $refs.checkbox.checked" class="form-check-input fs-15 ms-2" type="checkbox" :checked="rasio">
                Rasio
            </li>
            <li @click="selisih = !selisih; $refs.checkbox.checked = selisih" style="cursor: pointer;">
                <input x-ref="checkbox" @change="selisih = $refs.checkbox.checked" class="form-check-input fs-15 ms-2" type="checkbox" :checked="selisih">
                Selisih
            </li>
            <li @click="produk_nama = !produk_nama; $refs.checkbox.checked = produk_nama" style="cursor: pointer;">
                <input x-ref="checkbox" @change="produk_nama = $refs.checkbox.checked" class="form-check-input fs-15 ms-2" type="checkbox" :checked="produk_nama">
                Nama Produk
            </li>
            <li @click="order_nomor = !order_nomor; $refs.checkbox.checked = order_nomor" style="cursor: pointer;">
                <input x-ref="checkbox" @change="order_nomor = $refs.checkbox.checked" class="form-check-input fs-15 ms-2" type="checkbox" :checked="order_nomor">
                Nomor Order
            </li>
            <li @click="mesin = !mesin; $refs.checkbox.checked = mesin" style="cursor: pointer;">
                <input x-ref="checkbox" @change="mesin = $refs.checkbox.checked" class="form-check-input fs-15 ms-2" type="checkbox" :checked="mesin">
                Mesin
            </li>
            <li @click="produksi_tanggal = !produksi_tanggal; $refs.checkbox.checked = produksi_tanggal" style="cursor: pointer;">
                <input x-ref="checkbox" @change="produksi_tanggal = $refs.checkbox.checked" class="form-check-input fs-15 ms-2" type="checkbox" :checked="produksi_tanggal">
                Tanggal Produksi
            </li>
            <li @click="proses_tanggal = !proses_tanggal; $refs.checkbox.checked = proses_tanggal" style="cursor: pointer;">
                <input x-ref="checkbox" @change="proses_tanggal = $refs.checkbox.checked" class="form-check-input fs-15 ms-2" type="checkbox" :checked="proses_tanggal">
                Tanggal Proses
            </li>
            <li @click="jam = !jam; $refs.checkbox.checked = jam" style="cursor: pointer;">
                <input x-ref="checkbox" @change="jam = $refs.checkbox.checked" class="form-check-input fs-15 ms-2" type="checkbox" :checked="jam">
                Jam
            </li>
            <li @click="shift = !shift; $refs.checkbox.checked = shift" style="cursor: pointer;">
                <input x-ref="checkbox" @change="shift = $refs.checkbox.checked" class="form-check-input fs-15 ms-2" type="checkbox" :checked="shift">
                Shift
            </li>
            <li @click="seq = !seq; $refs.checkbox.checked = seq" style="cursor: pointer;">
                <input x-ref="checkbox" @change="seq = $refs.checkbox.checked" class="form-check-input fs-15 ms-2" type="checkbox" :checked="seq">
                Seq
            </li>
            <li @click="loss = !loss; $refs.checkbox.checked = loss" style="cursor: pointer;">
                <input x-ref="checkbox" @change="loss = $refs.checkbox.checked" class="form-check-input fs-15 ms-2" type="checkbox" :checked="loss">
                Loss
            </li>
            <li @click="by_update = !by_update; $refs.checkbox.checked = by_update" style="cursor: pointer;">
                <input x-ref="checkbox" @change="by_update = $refs.checkbox.checked" class="form-check-input fs-15 ms-2" type="checkbox" :checked="by_update">
                Update By
            </li>
            <li @click="updated = !updated; $refs.checkbox.checked = updated" style="cursor: pointer;">
                <input x-ref="checkbox" @change="updated = $refs.checkbox.checked" class="form-check-input fs-15 ms-2" type="checkbox" :checked="updated">
                Updated
            </li>
        </ul>

        <div class="table-responsive table-card">
            <table class="table align-middle table-nowrap">
                <thead class="table-light">
                    <tr>
                        <th></th>
                        <th>Nomor LPK</th>
                        <th x-show="lpk_date">Tanggal LPK</th>
                        <th x-show="lpk_panjang">Panjang LPK</th>
                        <th x-show="produksi_panjang">Panjang Produksi</th>
                        <th x-show="gentan_berat">Berat Gentan</th>
                        <th x-show="gentan_nomor">Nomor Gentan</th>
                        <th x-show="standard_berat">Berat Standard</th>
                        <th x-show="rasio">Rasio %</th>
                        <th x-show="selisih">Selisih</th>
                        <th x-show="produk_nama">Nama Produk</th>
                        <th x-show="order_nomor">Nomor Order</th>
                        <th x-show="mesin">Mesin</th>
                        <th x-show="produksi_tanggal">Tanggal Produksi</th>
                        <th x-show="proses_tanggal">Tanggal Proses</th>
                        <th x-show="jam">Jam</th>
                        <th x-show="shift">Shift</th>
                        <th x-show="seq">Seq</th>
                        <th x-show="loss">Loss</th>
                        <th x-show="by_update">Update By</th>
                        <th x-show="updated">Updated</th>
                    </tr>
                </thead>
                <tbody class="list form-check-all">
                    @forelse ($data as $item)
                        <tr>
                            <td>
                                <a href="/edit-nippo?orderId={{ $item->id }}" class="link-success fs-15 p-1 bg-primary rounded">
                                    <i class="ri-edit-box-line text-white"></i>
                                </a>
                            </td>
                            <td> {{ $item->lpk_no }} </td>
                            <td x-show="lpk_date"> {{ $item->lpk_date }} </td>
                            <td x-show="lpk_panjang"> {{ $item->panjang_lpk }}</td>
                            <td x-show="produksi_panjang"> {{ $item->panjang_produksi }} </td>
                            <td x-show="gentan_berat"> {{ $item->berat_produksi }} </td>
                            <td x-show="gentan_nomor"> {{ $item->gentan_no }} </td>
                            <td x-show="standard_berat"> {{ $item->berat_standard }}</td>
                            <td x-show="rasio"> - </td>
                            <td x-show="selisih"> - </td>
                            <td x-show="produk_nama"> {{ $item->product_name }} </td>
                            <td x-show="order_nomor"> {{ $item->product_code }} </td>
                            <td x-show="mesin"> {{ $item->machineno }} </td>
                            <td x-show="produksi_tanggal"> {{ $item->production_date }} </td>
                            <td x-show="proses_tanggal"> {{ $item->created_on }} </td>
                            <td x-show="jam"> {{ $item->work_hour }} </td>
                            <td x-show="shift"> {{ $item->work_shift }} </td>
                            <td x-show="seq"> {{ $item->seq_no }} </td>
                            <td x-show="loss"> {{ $item->infure_berat_loss }} </td>
                            <td x-show="by_update"> {{ $item->updated_by }} </td>
                            <td x-show="updated"> {{ $item->updated_on }} </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="12" class="text-center">
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
                    <th class="border-0">Nomor LPK</th>
                    <th class="border-0">Panjang Produksi</th>
                    <th class="border-0">Berat Gentan</th>
                    <th class="border-0">Nomor Gentan</th>
                    <th class="border-0">Nomor Order</th>
                    <th class="border-0 rounded-end">Mesin</th>
                    <th class="border-0">Tanggal Produksi</th>
                    <th class="border-0">Tanggal Proses</th>
                    <th class="border-0">Jam</th>
                    <th class="border-0 rounded-end">Shift</th>
                    <th class="border-0 rounded-end">Seq</th>
                </tr>
            </thead>
            <tbody class="list form-check-all">
                @forelse ($data as $item)
                    <tr>
                        <td>
                            <a href="/edit-nippo?orderId={{ $item->id }}" class="link-success fs-15 p-1 bg-primary rounded">
                                <i class="ri-edit-box-line text-white"></i>
                            </a>
                        </td>
                        <td> {{ $item->lpk_no }} </td>
                        <td> {{ $item->panjang_produksi }} </td>
                        <td> {{ $item->qty_gentan }} </td>
                        <td> {{ $item->gentan_no }} </td>
                        <td> {{ $item->product_code }} </td>
                        <td> {{ $item->machineno }} </td>
                        <td> {{ $item->production_date }} </td>
                        <td> {{ $item->created_on }} </td>
                        <td> {{ $item->work_hour }} </td>
                        <td> {{ $item->work_shift }} </td>
                        <td> {{ $item->seq_no }} </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="12" class="text-center">
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
@script
    <script>
        $wire.on('redirectToPrint', (datas) => {
            var printUrl = '{{ route('report-nippo-infure') }}?tanggal=' + datas;
            window.open(printUrl, '_blank');
        });
    </script>
@endscript
