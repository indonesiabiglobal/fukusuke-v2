<div class="row">
	<div class="col-lg-2"></div>
	<div class="col-lg-6">
		<div class="form-group">
			<div class="input-group col-md-9 col-xs-8">
				<label class="control-label col-4">Tanggal Produksi</label>
				<div class="col-12 col-lg-8">
					<select class="form-select mb-0" wire:model.defer="transaksi">
						<option value="1">Produksi</option>
						<option value="2">Order</option>
					</select>
				</div>
			</div>
			<div class="col-12 mt-1">
				<div class="form-group">
					<div class="input-group">
						<span class="input-group-addon col-12 col-lg-2">Awal: </span>
						<input class="form-control datepicker-input" type="date" wire:model.defer="tglMasuk" placeholder="yyyy/mm/dd hh:mm" />
					</div>
				</div>
			</div>
		</div>
		<div class="form-group mt-1">
			<div class="col-12">
				<div class="form-group">
					<div class="input-group">
						<span class="input-group-addon col-12 col-lg-2">Akhir: </span>
						<input class="form-control datepicker-input" type="date" wire:model.defer="tglKeluar" placeholder="yyyy/mm/dd hh:mm" />
					</div>
				</div>
			</div>
		</div>
		<div class="form-group mt-1">
			<div class="input-group">
				<span class="input-group-addon col-12 col-lg-3">Jenis Report </span>
				<select wire:model.defer="jenisReport" class="form-control" placeholder="- pilih jenis report -">
					<option value="1">Check List</option>
					<option value="2">Loss Infure</option>
				</select>
			</div>
		</div>
		<div class="form-group mt-1">
			<div class="input-group">
				<span class="input-group-addon col-12 col-lg-3">Nomor Proses </span>
				<input type="text" class="form-control" placeholder="1" wire:model.defer="noprosesawal">
				<span class="input-group-text readonly" readonly="readonly">
					~
				</span>
				<input type="text" class="form-control" placeholder="1000" wire:model.defer="noprosesakhir">
			</div>
		</div>
		<div class="form-group mt-1">
			<div class="input-group">
				<span class="input-group-addon col-12 col-lg-3">Nomor LPK </span>
				<input type="text" class="form-control" placeholder="000000-000" wire:model.defer="lpk_no">
			</div>
		</div>
		<div class="form-group mt-1">
			<div class="input-group">
				<span class="input-group-addon col-12 col-lg-3">Nomor Order </span>
				<input type="text" class="form-control" placeholder=".." wire:model.defer="code">
			</div>
		</div>
        <div class="form-group mt-1">
			<div class="input-group">
				<span class="input-group-addon col-12 col-lg-3">Departemen</span>
				<select class="form-control" wire:model.defer="departemenId">
					<option value="">- all -</option>
					@foreach ($department as $item)
						<option value="{{ $item->id }}">{{ $item->name }}</option>
					@endforeach
				</select>
			</div>
		</div>
		<div class="form-group mt-1">
			<div class="input-group">
				<span class="input-group-addon col-12 col-lg-3">Mesin</span>
				<select class="form-control" wire:model.defer="machineId">
					<option value="">- all -</option>
					<option value="1"></option>
					<option value="2"></option>
				</select>
			</div>
		</div>
		<div class="form-group mt-1">
			<div class="input-group">
				<span class="input-group-addon col-12 col-lg-3">Nomor Han</span>
				<input type="text" class="form-control" placeholder="00-00-00A" wire:model.defer="nomor_han" />
			</div>
		</div>
		<hr />
		<div class="form-group">
			<label class="control-label col-md-4 col-xs-12"></label>
			<div class="input-group">
				<button type="button" class="btn btn-success btn-print" wire:click="export" style="width:99%">
					<i class="fa fa-print"></i> Generate Report
					<div wire:loading wire:target="export">
						<span class="fa fa-spinner fa-spin"></span>
					</div>
				</button>
			</div>
		</div>
	</div>
	<div class="col-lg-4"></div>
</div>