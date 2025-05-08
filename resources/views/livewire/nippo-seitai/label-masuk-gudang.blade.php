<form>
    <div class="row mt-3">
        <div class="col-12 col-lg-6">
            <div class="form-group">
                <div class="input-group">
                    <span class="input-group-text readonly">
                        Nomor Palet Sumber
                    </span>
                    <div x-data="{ nomor_palet: @entangle('nomor_palet').change, status: true }" x-init="$watch('nomor_palet', value => {
                        // Membuat karakter pertama kapital
                        nomor_palet = value.charAt(0).toUpperCase() + value.slice(1).replace(/[^0-9-]/g, '');
                        if (value.length === 5 && !value.includes('-') && status) {
                            nomor_palet = value + '-';
                        }
                        if (value.length < 5) {
                            status = true;
                        }
                        if (value.length === 6) {
                            status = false;
                        }
                        {{-- membatasi 12 karakter --}}
                        if (value.length == 11 && !value.includes('-') && status) {
                            nomor_palet = value.substring(0, 5) + '-' + value.substring(5, 11);
                        } else if (value.length > 12) {
                            nomor_palet = value.substring(0, 12);
                        }
                    })">
                        <input type="text" class="form-control" x-model="nomor_palet" wire:model="nomor_palet"
                            maxlength="12" x-on:keydown.tab="$event.preventDefault(); $refs.lotnoInput.focus();"
                            placeholder="A0000-000000" />
                    </div>
                    {{-- <input wire:model.defer="nomor_palet" class="form-control" type="text" placeholder="A0000-000000" /> --}}
                    <button wire:click="search" type="button" class="btn btn-light">
                        <i class="ri-search-line"></i>
                    </button>
                </div>
            </div>
            <div class="card border-0 shadow mb-4 mt-4">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap mb-0 rounded">
                            <thead class="table-light">
                                <tr>
                                    <th class="border-0 rounded-start">Nomor LOT</th>
                                    <th class="border-0">Mesin</th>
                                    <th class="border-0">Tg. Produksi</th>
                                    <th class="border-0">Jumlah Box</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($data as $item)
                                    <tr>
                                        <td>
                                            {{ $item->nomor_lot }}
                                        </td>
                                        <td>
                                            {{ $item->machinename }}
                                        </td>
                                        <td>
                                            {{ $item->production_date }}
                                        </td>
                                        <td>
                                            {{ $item->qty_produksi }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">No results found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-6">
            <div class="form-group">
                <div class="input-group">
                    <label class="control-label col-3">Nomor Produk</label>
                    <input type="text" class="form-control readonly" readonly="readonly" wire:model="code" />
                </div>
            </div>
            <div class="form-group mt-1">
                <div class="input-group">
                    <label class="control-label col-3">Nama Produk</label>
                    <input type="text" class="form-control readonly" readonly="readonly" wire:model="name" />
                </div>
            </div>
            <button type="button" class="btn btn-success btn-print mt-1" wire:click="export" style="width:99%" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="export">
                    <i class="fa fa-print"></i> Print
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
</form>
@script
    <script>
        $wire.on('redirectToPrint', (no_palet) => {
            var printUrl = '{{ route('report-masuk-gudang') }}?no_palet=' + no_palet
            window.open(printUrl, '_blank');
        });
    </script>
@endscript
