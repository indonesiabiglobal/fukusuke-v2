<div class="row">
	<div class="col-lg-2"></div>
	<div class="col-lg-6">
		<div class="form-group">
			<label class="control-label col-md-4 col-xs-12">Tanggal Periode</label>
			<div class="col-12 mt-1">
				<div class="form-group">
					<div class="input-group">
						<span class="input-group-addon col-12 col-lg-2">Awal: </span>
						<input class="form-control datepicker-input" type="datetime-local" wire:model.defer="tglMasuk" placeholder="yyyy/mm/dd hh:mm" />
					</div>
				</div>
			</div>
		</div>
		<div class="form-group mt-1">
			<div class="col-12">
				<div class="form-group">
					<div class="input-group">
						<span class="input-group-addon col-12 col-lg-2">Akhir: </span>
						<input class="form-control datepicker-input" type="datetime-local" wire:model.defer="tglKeluar" placeholder="yyyy/mm/dd hh:mm" />
					</div>
				</div>
			</div>
		</div>
		<div class="form-group mt-1">
			<div class="input-group">
				<span class="input-group-addon col-12 col-lg-3">Nippo </span>
				<select id="department" class="form-control" placeholder="- pilih jenis report -">
					<option value="1">Infure</option>
					<option value="2">Seitai</option>
				</select>
			</div>
		</div>
        <div class="form-group mt-1">
			<div class="input-group">
				<span class="input-group-addon col-12 col-lg-3">Nomor LPK </span>
				<input type="text" class="form-control" placeholder="000000-000">
			</div>
		</div>
        <div class="form-group mt-1">
			<div class="input-group">
				<span class="input-group-addon col-12 col-lg-3">Nomor Order </span>
				<input type="text" class="form-control" placeholder="...">
			</div>
		</div>
        <div class="form-group mt-1">
			<div class="input-group">
				<span class="input-group-addon col-12 col-lg-3">Departemen </span>
				<select id="department" class="form-control" placeholder="- pilih jenis report -">
					{{-- <option value="1">Infure</option>
					<option value="2">Seitai</option> --}}
				</select>
			</div>
		</div>
        <div class="form-group mt-1">
			<div class="input-group">
				<span class="input-group-addon col-12 col-lg-3">Mesin </span>
				<select id="department" class="form-control" placeholder="- pilih jenis report -" onchange="changeDep(this.value)">
					{{-- <option value="1">Infure</option>
					<option value="2">Seitai</option> --}}
				</select>
			</div>
		</div>
        <div class="form-group mt-1">
			<div class="input-group">
				<span class="input-group-addon col-12 col-lg-3">Nomor Han </span>
				<input type="text" class="form-control" placeholder="...">
			</div>
		</div>
		<hr />
		<div class="form-group">
			<label class="control-label col-md-4 col-xs-12"></label>
			<div class="input-group col-md-8 col-xs-12">
				<button type="button" class="btn btn-success btn-print" wire:click="export" style="width:99%"><i class="fa fa-print"></i> Generate Report</button>
			</div>
		</div>
	</div>
</div>