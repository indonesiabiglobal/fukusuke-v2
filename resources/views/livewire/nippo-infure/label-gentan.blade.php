<div class="row mt-3">
	<div class="col-lg-2"></div>
	<div class="col-lg-8">
		<div class="form-group">
			<div class="input-group">
				<label class="control-label col-12 col-lg-3 text-muted fw-bold">Nomor LPK</label>
				<input type="text" wire:model.live.debounce.300ms="lpk_no" class="form-control" placeholder="000000-000"/>
			</div>
		</div>
		<div class="form-group mt-1">
			<div class="input-group">
				<label class="control-label col-12 col-lg-3 text-muted fw-bold">Nomor Gentan</label>
				<input type="text"  wire:model.live="gentan_no" class="form-control" placeholder="..." />
			</div>
		</div>
		<div class="form-group mt-1">
			<div class="input-group">
				<label class="control-label col-12 col-lg-3 text-muted fw-bold">Nomor Order</label>
				<input type="text" wire:model="code" class="form-control readonly" readonly="readonly" />
			</div>
		</div>
		<div class="form-group mt-1">
			<div class="input-group">
				<label class="control-label col-12 col-lg-3 text-muted fw-bold">Nama Produk</label>
				<input type="text" wire:model="product_name" class="form-control readonly" readonly="readonly" />
			</div>
		</div>
		<div class="form-group mt-1">
			<div class="input-group">
				<label class="control-label col-12 col-lg-3 text-muted fw-bold">Panjang Produksi</label>
				<input type="text" wire:model="product_panjang" class="form-control readonly currency" readonly="readonly" />
				<span class="input-group-text">
					meter
				</span>
			</div>
		</div>
		<div class="form-group mt-1">
			<div class="input-group">
				<label class="control-label col-12 col-lg-3 text-muted fw-bold">Berat Gentan</label>
				<input type="text" wire:model="qty_gentan" class="form-control readonly currency" readonly="readonly" />
				<span class="input-group-text">
					kg
				</span>
			</div>
		</div>
		<div class="form-group mt-1">
			<div class="input-group">
				<label class="control-label col-12 col-lg-3 text-muted fw-bold">Berat Standard</label>
				<input type="text" wire:model="product_panjanggulung" class="form-control readonly currency" readonly="readonly" />
				<span class="input-group-text">
					kg
				</span>
			</div>
		</div>
		<div class="form-group mt-1">
			<div class="input-group">
				<label class="control-label col-12 col-lg-3 text-muted fw-bold">Tanggal LPK</label>
				<input class="form-control readonly datepicker-input" readonly="readonly" type="date" wire:model="lpk_date" />
			</div>
		</div>
		<div class="form-group mt-1">
			<div class="input-group">
				<label class="control-label col-12 col-lg-3 text-muted fw-bold">Jumlah LPK</label>
				<input type="text" name="qty_lpk" class="form-control readonly integer" readonly="readonly" wire:model="qty_lpk" />
			</div>
		</div>
		<hr />
		<div class="form-group">
			<div class="input-group">				
				<button type="button" class="btn btn-success btn-print" wire:click="print">
					<i class="ri-printer-line"></i> Print
				</button>
				<div style="float:right" class="text-danger">Paper: A4-Portrait or Thermal </div>
			</div>
		</div>
	</div>
	<div class="col-lg-2"></div>
</div>
@script
	<script>
		$wire.on('redirectToPrint', (lpk_no) => {
			var printUrl = '{{ route('report-gentan') }}?lpk_no=' + lpk_no
			window.open(printUrl, '_blank');
		});
	</script>
@endscript