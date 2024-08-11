<div class="row">
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
                    <input wire:model.defer="lpk_no" class="form-control" style="padding:0.44rem" type="text"
                        placeholder="000000-000" />
                </div>
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
                    <select class="form-control" wire:model.defer="idProduct" data-choices data-choices-sorting-false
                        data-choices-removeItem>
                        <option value="">- All -</option>
                        @foreach ($products as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-12 col-lg-2">
                <label for="buyer" class="form-label text-muted fw-bold">Buyer</label>
            </div>
            <div class="col-12 col-lg-10">
                <div class="mb-1" wire:ignore>
                    <select class="form-control" wire:model.defer="idBuyer" id="buyer" name="buyer" data-choices
                        data-choices-sorting-false data-choices-removeItem>
                        <option value="">- All -</option>
                        @foreach ($buyer as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
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
                        <option value="">- All -</option>
                        <option value="0">Un-Print</option>
                        <option value="1">Printed</option>
                        <option value="2">Re-Print</option>
                        <option value="3">Belum Produksi</option>
                        <option value="4">Sudah Produksi</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-12 mt-2">
        <div class="row">
            <div class="col-12 col-lg-5">
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

                <button type="button" class="btn btn-success w-lg p-1" onclick="window.location.href='/add-lpk'">
                    <i class="ri-add-line"> </i> Add
                </button>
            </div>
            <div class="col-12 col-lg-7 d-none d-sm-block">
                <input type="file" id="fileInput" wire:model="file" style="display: none;">
                <button class="btn btn-success w-lg p-1" type="button"
                    onclick="document.getElementById('fileInput').click()">
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

                <button class="btn btn-primary w-lg p-1" wire:click="download" type="button">
                    <span wire:loading.remove wire:target="download">
                        <i class="ri-download-cloud-2-line"> </i> Download
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
                <button class="btn btn-info w-lg p-1" wire:click="print" type="button">
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
                <button class="btn btn-info w-lg p-1" wire:click="printLPK" type="button">
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

    <div class="col text-end dropdown" x-data="{ 
        lpk_date:true, lpk_panjang:true, lpk_jumlah:true, gentan_jumlah:true, gulung_meter:true, selisih:false, infure_progress:true, seitai_progress:true, produk_nama:false, produk_kode:true, mesin:false, buyer:false, proses_tanggal: true, seq: false, by_update: false, updated: false
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
            <li @click="lpk_jumlah = !lpk_jumlah; $refs.checkbox.checked = lpk_jumlah" style="cursor: pointer;">
                <input x-ref="checkbox" @change="lpk_jumlah = $refs.checkbox.checked" class="form-check-input fs-15 ms-2" type="checkbox" :checked="lpk_jumlah"> 
                Jumlah LPK
            </li>
            <li @click="gentan_jumlah = !gentan_jumlah; $refs.checkbox.checked = gentan_jumlah" style="cursor: pointer;">
                <input x-ref="checkbox" @change="gentan_jumlah = $refs.checkbox.checked" class="form-check-input fs-15 ms-2" type="checkbox" :checked="gentan_jumlah"> 
                Jumlah Gentan
            </li>
            <li @click="gulung_meter = !gulung_meter; $refs.checkbox.checked = gulung_meter" style="cursor: pointer;">
                <input x-ref="checkbox" @change="gulung_meter = $refs.checkbox.checked" class="form-check-input fs-15 ms-2" type="checkbox" :checked="gulung_meter"> 
                Meter Gulung
            </li>
            <li @click="selisih = !selisih; $refs.checkbox.checked = selisih" style="cursor: pointer;">
                <input x-ref="checkbox" @change="selisih = $refs.checkbox.checked" class="form-check-input fs-15 ms-2" type="checkbox" :checked="selisih"> 
                Selisih
            </li>
            <li @click="infure_progress = !infure_progress; $refs.checkbox.checked = infure_progress" style="cursor: pointer;">
                <input x-ref="checkbox" @change="infure_progress = $refs.checkbox.checked" class="form-check-input fs-15 ms-2" type="checkbox" :checked="infure_progress"> 
                Progress Infure
            </li>
            <li @click="seitai_progress = !seitai_progress; $refs.checkbox.checked = seitai_progress" style="cursor: pointer;">
                <input x-ref="checkbox" @change="seitai_progress = $refs.checkbox.checked" class="form-check-input fs-15 ms-2" type="checkbox" :checked="seitai_progress"> 
                Progress Seitai
            </li>
            <li @click="produk_nama = !produk_nama; $refs.checkbox.checked = produk_nama" style="cursor: pointer;">
                <input x-ref="checkbox" @change="produk_nama = $refs.checkbox.checked" class="form-check-input fs-15 ms-2" type="checkbox" :checked="produk_nama"> 
                Nama Produk
            </li>
            <li @click="produk_kode = !produk_kode; $refs.checkbox.checked = produk_kode" style="cursor: pointer;">
                <input x-ref="checkbox" @change="produk_kode = $refs.checkbox.checked" class="form-check-input fs-15 ms-2" type="checkbox" :checked="produk_kode"> 
                Kode Produk
            </li>
            <li @click="mesin = !mesin; $refs.checkbox.checked = mesin" style="cursor: pointer;">
                <input x-ref="checkbox" @change="mesin = $refs.checkbox.checked" class="form-check-input fs-15 ms-2" type="checkbox" :checked="mesin"> 
                Mesin
            </li>
            <li @click="buyer = !buyer; $refs.checkbox.checked = buyer" style="cursor: pointer;">
                <input x-ref="checkbox" @change="buyer = $refs.checkbox.checked" class="form-check-input fs-15 ms-2" type="checkbox" :checked="buyer"> 
                Buyer
            </li>
            <li @click="proses_tanggal = !proses_tanggal; $refs.checkbox.checked = proses_tanggal" style="cursor: pointer;">
                <input x-ref="checkbox" @change="proses_tanggal = $refs.checkbox.checked" class="form-check-input fs-15 ms-2" type="checkbox" :checked="proses_tanggal"> 
                Tanggal Proses
            </li>
            <li @click="seq = !seq; $refs.checkbox.checked = seq" style="cursor: pointer;">
                <input x-ref="checkbox" @change="seq = $refs.checkbox.checked" class="form-check-input fs-15 ms-2" type="checkbox" :checked="seq"> 
                Seq
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
                        <th scope="col" style="width: 10px;">
                            <div class="form-check">
                                <input class="form-check-input fs-15" type="checkbox" id="checkAll" value="optionAll">
                            </div>
                        </th>
                        <th></th>
                        <th>No LPK</th>
                        <th x-show="lpk_date">Tgl LPK</th>
                        <th x-show="lpk_panjang">Panjang LPK</th>
                        <th x-show="lpk_jumlah">Jumlah LPK</th>
                        <th x-show="gentan_jumlah">Jumlah Gentan</th>
                        <th x-show="gulung_meter">Master Gulung</th>
                        <th x-show="selisih">Selisih</th>
                        <th x-show="infure_progress">Progres Infure</th>
                        <th x-show="seitai_progress">Progres Seitai</th>
                        <th>Nomor PO</th>
                        <th x-show="produk_nama">Nama Produk</th>
                        <th x-show="produk_kode">Kode Produk</th>
                        <th x-show="mesin">Mesin</th>
                        <th x-show="buyer">Buyer</th>
                        <th>Warna LPK</th>
                        <th x-show="proses_tanggal">Tanggal Proses</th>
                        <th x-show="seq">seq</th>
                        <th x-show="by_update">Update By</th>
                        <th x-show="updated">Updated</th>
                    </tr>
                </thead>
                <tbody class="list form-check-all">
                    @forelse ($data as $item)
                        <tr>
                            <th scope="row">
                                <div class="form-check">
                                    <input class="form-check-input fs-15 checkListLPK" type="checkbox"
                                        wire:model="checkListLPK" value="{{ $item->id }}">
                                </div>
                            </th>
                            <td>
                                <a href="/edit-lpk?orderId={{ $item->id }}"
                                    class="link-success fs-15 p-1 bg-primary rounded">
                                    <i class="ri-edit-box-line text-white"></i>
                                </a>
                            </td>
                            <td>{{ $item->lpk_no }}</td>
                            <td x-show="lpk_date">{{ $item->lpk_date }}</td>
                            <td x-show="lpk_panjang">{{ $item->panjang_lpk }}</td>
                            <td x-show="lpk_jumlah">{{ $item->qty_lpk }}</td>
                            <td x-show="gentan_jumlah">{{ $item->qty_gentan }}</td>
                            <td x-show="gulung_meter">{{ $item->qty_gulung }}</td>
                            <td x-show="selisih">-</td>
                            <td x-show="infure_progress">{{ $item->infure }}</td>
                            <td x-show="seitai_progress">{{ $item->total_assembly_qty }}</td>
                            <td>{{ $item->po_no }}</td>
                            <td x-show="produk_nama">{{ $item->product_name }}</td>
                            <td x-show="produk_kode">{{ $item->product_code }}</td>
                            <td x-show="mesin">{{ $item->machine_no }}</td>
                            <td x-show="buyer">{{ $item->buyer_name }}</td>
                            <td>{{ $item->warnalpk }}</td>
                            <td x-show="proses_tanggal">{{ $item->tglproses }}</td>
                            <td x-show="seq">{{ $item->seq_no }}</td>
                            <td x-show="by_update">{{ $item->updated_by }}</td>
                            <td x-show="updated">{{ $item->updatedt }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="12" class="text-center">
                                <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop"
                                    colors="primary:#121331,secondary:#08a88a" style="width:40px;height:40px"></lord-icon>
                                <h5 class="mt-2">Sorry! No Result Found</h5>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            {{ $data->links(data: ['scrollTo' => false]) }}
        </div>
    </div>

    {{-- <div class="col text-end dropdown" wire:ignore>
        <button type="button" data-bs-toggle="dropdown" aria-expanded="false" class="btn btn-soft-primary btn-icon fs-14 me-4 mb-4">
            <i class="ri-grid-fill"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
            <li>
                <input class="form-check-input fs-15 ms-2" type="checkbox"
                wire:model.live="po_no" value="{{ $po_no }}"> Tanggal LPK
            </li>
            <li>
                <input class="form-check-input fs-15 ms-2" type="checkbox"
                wire:model.live="pr_na" value="{{ $pr_na }}"> Panjang LPK
            </li>
            <li>
                <input class="form-check-input fs-15 ms-2" type="checkbox"
                wire:model.live="ko_pr" value="{{ $ko_pr }}"> Jumlah LPK
            </li>
            <li>
                <input class="form-check-input fs-15 ms-2" type="checkbox"
                wire:model.live="bu" value="{{ $bu }}"> Jumlah Gentan
            </li>
            <li>
                <input class="form-check-input fs-15 ms-2" type="checkbox"
                wire:model.live="qt" value="{{ $qt }}"> Meter Gulung
            </li>
            <li>
                <input class="form-check-input fs-15 ms-2" type="checkbox"
                wire:model.live="tgo" value="{{ $tgo }}"> Selisih
            </li>
            <li>
                <input class="form-check-input fs-15 ms-2" type="checkbox"
                wire:model.live="stf" value="{{ $stf }}"> Progress Infure
            </li>
            <li>
                <input class="form-check-input fs-15 ms-2" type="checkbox"
                wire:model.live="et" value="{{ $et }}"> Progress Seitai
            </li>
            <li>
                <input class="form-check-input fs-15 ms-2" type="checkbox"
                wire:model.live="eta" value="{{ $eta }}"> Nama Produk
            </li>
            <li>
                <input class="form-check-input fs-15 ms-2" type="checkbox"
                wire:model.live="tgp" value="{{ $tgp }}"> Kode Produk
            </li>
            <li>
                <input class="form-check-input fs-15 ms-2" type="checkbox"
                wire:model.live="no" value="{{ $no }}"> Mesin
            </li>
            <li>
                <input class="form-check-input fs-15 ms-2" type="checkbox"
                wire:model.live="updated_by" value="{{ $updated_by }}"> Buyer
            </li>
            <li>
                <input class="form-check-input fs-15 ms-2" type="checkbox"
                wire:model.live="updated_on" value="{{ $updated_on }}"> Tanggal Proses
            </li>
            <li>
                <input class="form-check-input fs-15 ms-2" type="checkbox"
                wire:model.live="no" value="{{ $no }}"> Seq.
            </li>
            <li>
                <input class="form-check-input fs-15 ms-2" type="checkbox"
                wire:model.live="updated_by" value="{{ $updated_by }}"> UpdateBy
            </li>
            <li>
                <input class="form-check-input fs-15 ms-2" type="checkbox"
                wire:model.live="updated_on" value="{{ $updated_on }}"> Updated
            </li>
        </ul>
    </div> --}}
    {{-- <div class="table-responsive table-card">
        <table class="table align-middle">
            <thead class="table-light">
                <tr>
                    <th scope="col" style="width: 10px;">
                        <div class="form-check">
                            <input class="form-check-input fs-15" type="checkbox" id="checkAll" value="optionAll">
                        </div>
                    </th>
                    <th class="border-0 rounded-start"></th>
                    <th class="border-0">No LPK</th>
                    <th class="border-0">Tgl LPK</th>
                    <th class="border-0">Panjang LPK</th>
                    <th class="border-0">Jumlah LPK</th>
                    <th class="border-0">Jumlah Gentan</th>
                    <th class="border-0">Master Gulung</th>
                    <th class="border-0">Progres Infure</th>
                    <th class="border-0">Progres Seitai</th>
                    <th class="border-0">Nomor PO</th>
                    <th class="border-0">Kode Produk</th>
                    <th class="border-0">Warna LPK</th>
                    <th class="border-0 rounded-end">Tanggal Proses</th>
                </tr>
            </thead>
            <tbody class="list form-check-all">
                @forelse ($data as $item)
                    <tr>
                        <th scope="row">
                            <div class="form-check">
                                <input class="form-check-input fs-15 checkListLPK" type="checkbox"
                                    wire:model="checkListLPK" value="{{ $item->id }}">
                            </div>
                        </th>
                        <td>
                            <a href="/edit-lpk?orderId={{ $item->id }}"
                                class="link-success fs-15 p-1 bg-primary rounded">
                                <i class="ri-edit-box-line text-white"></i>
                            </a>
                        </td>
                        <td>{{ $item->lpk_no }}</td>
                        <td>{{ $item->lpk_date }}</td>
                        <td>{{ $item->panjang_lpk }}</td>
                        <td>{{ $item->qty_lpk }}</td>
                        <td>{{ $item->qty_gentan }}</td>
                        <td>{{ $item->qty_gulung }}</td>
                        <td>{{ $item->infure }}</td>
                        <td>{{ $item->total_assembly_qty }}</td>
                        <td>{{ $item->po_no }}</td>
                        <td>{{ $item->product_code }}</td>
                        <td>{{ $item->warnalpk }}</td>
                        <td>{{ $item->tglproses }}</td>
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
        {{ $data->links() }}
    </div> --}}
</div>

@script
    <script>
        // memilih seluruh data pada table
        // livewire load
        $('#checkAll').click(function() {
            var isChecked = $(this).is(':checked');
            $('.checkListLPK').each(function() {
                $(this).prop('checked', isChecked);
                $(this).trigger('change');
            });
            @this.set('checkListLPK', $('.checkListLPK:checked').map(function() {
                return this.value;
            }).get());
        });

        // jika ada perubahan pada checkbox
        $('.checkListLPK').change(function() {
            $('#checkAll').prop('checked', $('.checkListLPK:checked').length == $('.checkListLPK').length);
        });

        $wire.on('redirectToPrint', (lpk_id) => {
            var printUrl = '{{ route('report-lpk') }}?lpk_id=' + lpk_id
            window.open(printUrl, '_blank');
        });
    </script>
@endscript
