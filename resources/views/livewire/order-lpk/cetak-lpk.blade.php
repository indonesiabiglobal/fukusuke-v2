<div class="row">
	<div class="col-lg-2"></div>
	<div class="col-lg-8">
		<div class="form-group">
			<label class="control-label col-md-3 col-xs-12">Departemen</label>
			<div class="input-group col-md-9 col-xs-12">
				<select id="printDept" class="js-states form-control" placeholder="- all -">
					<option value="all">- all -</option>
					<option value="INFURE">INFURE</option>
					<option value="SEITAI">SEITAI</option>
				</select>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-md-3 col-xs-12">Nomor LPK</label>
			<div class="input-group col-md-9 col-xs-12">
				<input type="text" class="form-control" wire:model.debounce.300ms="lpk_no"/>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-md-3 col-xs-12">Tanggal LPK</label>
			<div class="input-group col-md-9 col-xs-12">
				<input type="text"wire:model.debounce.300ms="lpk_date" class="form-control readonly" readonly="readonly" />
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-md-3 col-xs-12">Jumlah LPK</label>
			<div class="input-group col-md-9 col-xs-12">
				<input type="text" wire:model.debounce.300ms="qty_lpk" class="form-control readonly integer" readonly="readonly" />
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-md-3 col-xs-12">Nomor Order</label>
			<div class="input-group col-md-9 col-xs-12">
				<input type="text" wire:model.debounce.300ms="code" class="form-control readonly" readonly="readonly" />
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-md-3 col-xs-12">Nama Produk</label>
			<div class="input-group col-md-9 col-xs-12">
				<input type="text" wire:model.debounce.300ms="product_name" class="form-control readonly" readonly="readonly" />
			</div>
		</div>
		<div class="form-group reprint_no">
			<label class="control-label col-md-3 col-xs-12">Re-Print</label>
			<div class="input-group col-md-9 col-xs-12">
				<input type="text" wire:model.debounce.300ms="reprint_no" class="form-control readonly" readonly="readonly" />
			</div>
		</div>
		<hr />
		<div class="form-group">
			<label class="control-label col-md-3 col-xs-12"></label>
			<div class="input-group col-md-9 col-xs-12">
				<button type="button" class="btn btn-success btn-print" wire:click="print">
					<i class="fa fa-print"></i> Print</button>
			</div>
		</div>
	</div>
	<div class="col-lg-2">
	</div>
</div>
{{-- <script>
	document.addEventListener('livewire:load', function () {
		Livewire.on('redirectToPrint', function (lpk_id) {
			// var dt=data;
			var printUrl = '{{ route('report-lpk') }}?lpk_id=' +  lpk_id
			window.open(printUrl, '_blank');
		});
	});
</script> --}}
