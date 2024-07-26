<div class="row">
	<div class="col-lg-2"></div>
	<div class="col-lg-8">
		<div class="form-group">
			<div class="input-group">
				<label class="control-label col-12 col-lg-2 fw-bold">Departemen</label>
				<select class="form-control" placeholder="- all -">
					<option value="all">- all -</option>
					<option value="INFURE">INFURE</option>
					<option value="SEITAI">SEITAI</option>
				</select>
			</div>
		</div>
		<div class="form-group mt-1">
			<div class="input-group">
				<label class="control-label col-12 col-lg-2 fw-bold">Nomor LPK</label>
				<input type="text" class="form-control" wire:model.live.debounce.300ms="lpk_no"/>
			</div>
		</div>
		<div class="form-group mt-1">
			<div class="input-group">
				<label class="control-label col-12 col-lg-2 fw-bold">Tanggal LPK</label>
				<input type="text" wire:model="lpk_date" class="form-control readonly" readonly="readonly" />
			</div>
		</div>
		<div class="form-group mt-1">
			<div class="input-group">
				<label class="control-label col-12 col-lg-2 fw-bold">Jumlah LPK</label>
				<input type="text" wire:model="qty_lpk" class="form-control readonly integer" readonly="readonly" />
			</div>
		</div>
		<div class="form-group mt-1">
			<div class="input-group">
				<label class="control-label col-12 col-lg-2 fw-bold">Nomor Order</label>
				<input type="text" wire:model="code" class="form-control readonly" readonly="readonly" />
			</div>
		</div>
		<div class="form-group mt-1">
			<div class="input-group">
				<label class="control-label col-12 col-lg-2 fw-bold">Nama Produk</label>
				<input type="text" wire:model="product_name" class="form-control readonly" readonly="readonly" />
			</div>
		</div>
		<div class="form-group mt-1">
			<div class="input-group">
				<label class="control-label col-12 col-lg-2 fw-bold">Re-Print</label>
				<input type="text" wire:model="reprint_no" class="form-control readonly" readonly="readonly" />
			</div>
		</div>
		<hr />
		<div class="form-group mt-1">
			<div class="input-group">
				<label class="control-label col-12 col-lg-2 fw-bold"></label>
				<button type="button" class="btn btn-success btn-print" wire:click="print">
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
<script>
	document.addEventListener('livewire:load', function () {
		Livewire.on('redirectToPrint', function (lpk_id) {
			// var dt=data;
			var printUrl = '{{ route('report-lpk') }}?lpk_id=' +  lpk_id
			window.open(printUrl, '_blank');
		});
	});
</script>
