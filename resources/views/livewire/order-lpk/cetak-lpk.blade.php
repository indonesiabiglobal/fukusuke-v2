<div class="row">
    <div class="col-lg-2"></div>
    <div class="col-lg-8">
        <div class="form-group">
            <div class="input-group">
                <label class="control-label col-12 col-lg-2 fw-bold"
                    style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; font-size: 12px;">Departemen</label>
                <select class="form-control form-control-sm" placeholder="- all -">
                    <option value="all">- all -</option>
                    <option value="INFURE">INFURE</option>
                    <option value="SEITAI">SEITAI</option>
                </select>
            </div>
        </div>
        <div class="form-group mt-1">
            <div class=" input-group" x-data="{ lpk_no: @entangle('lpk_no').live, status: true }" x-init="$watch('lpk_no', value => {
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
                <label class="control-label col-12 col-lg-2 fw-bold"
                    style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; font-size: 12px;">Nomor LPK</label>
                <input class="form-control form-control-sm" style="padding:0.44rem" type="text"
                    placeholder="000000-000" x-model="lpk_no" maxlength="10" />
            </div>
        </div>
        <div class="form-group mt-1">
            <div class="input-group">
                <label class="control-label col-12 col-lg-2 fw-bold"
                    style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; font-size: 12px;">Tanggal
                    LPK</label>
                <input type="text" wire:model="lpk_date" class="form-control form-control-sm readonly bg-light"
                    readonly="readonly" />
            </div>
        </div>
        <div class="form-group mt-1">
            <div class="input-group">
                <label class="control-label col-12 col-lg-2 fw-bold"
                    style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; font-size: 12px;">Jumlah
                    LPK</label>
                <input type="text" wire:model="qty_lpk" class="form-control form-control-sm readonly bg-light"
                    readonly="readonly" />
            </div>
        </div>
        <div class="form-group mt-1">
            <div class="input-group">
                <label class="control-label col-12 col-lg-2 fw-bold"
                    style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; font-size: 12px;">Nomor
                    Order</label>
                <input type="text" wire:model="code" class="form-control form-control-sm readonly bg-light"
                    readonly="readonly" />
            </div>
        </div>
        <div class="form-group mt-1">
            <div class="input-group">
                <label class="control-label col-12 col-lg-2 fw-bold"
                    style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; font-size: 12px;">Nama
                    Produk</label>
                <input type="text" wire:model="product_name" class="form-control form-control-sm readonly bg-light"
                    readonly="readonly" />
            </div>
        </div>
        <div class="form-group mt-1">
            <div class="input-group">
                <label class="control-label col-12 col-lg-2 fw-bold"
                    style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; font-size: 12px;">Re-Print</label>
                <input type="text" wire:model="reprint_no" class="form-control form-control-sm readonly bg-light"
                    readonly="readonly" />
            </div>
        </div>
        <hr />
        <div class="form-group mt-1">
            <div class="input-group">
                <label class="control-label col-12 col-lg-2 fw-bold"></label>
                <button type="button" class="btn btn-success btn-print" wire:click="print"
                    {{ !$lpk_id ? 'disabled' : '' }}>
                    <span wire:loading.remove wire:target="print">
                        <i class="ri-printer-line"></i> Print
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
    <div class="col-lg-2">
    </div>
</div>
@script
    <script>
        // document.addEventListener('livewire:load', function () {
        // 	Livewire.on('redirectToPrint', function (lpk_id) {
        // 		// var dt=data;
        // 		var printUrl = '{{ route('report-lpk') }}?lpk_id=' +  lpk_id
        // 		window.open(printUrl, '_blank');
        // 	});
        // });

        $wire.on('redirectToPrint', (lpk_id) => {
            var printUrl = '{{ route('report-lpk') }}?lpk_id=' + lpk_id
            window.open(printUrl, '_blank');
        });
    </script>
@endscript
