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
				<select class="form-control" wire:model.defer="nipon" placeholder="- pilih -">
					<option value="1" selected="selected">Infure</option>
					<option value="2">Seitai</option>
				</select>
				{{-- <select id="nippo" class="form-control" placeholder="- pilih jenis report -">
					<option value="1">Infure</option>
					<option value="2">Seitai</option>
				</select> --}}
			</div>
		</div>
		<div class="form-group mt-1">
			<div class="input-group">
				<span class="input-group-addon col-12 col-lg-3">Jenis Report </span>
				<select class="form-control" wire:model.defer="jenisreport" placeholder="- pilih jenis report -">
					<option value="0">- pilih jenis report -</option>
					<option value="1" selected="selected">Daftar Produksi Per Mesin</option>
					<option value="2">Daftar Produksi Per Tipe Per Mesin</option>
					<option value="3">Daftar Produksi Per Jenis</option>
					<option value="4">Daftar Produksi Per Tipe</option>
					<option value="5">Daftar Produksi Per Produk</option>
					<option value="6">Daftar Produksi Per Departemen Per Jenis</option>
					<option value="7">Daftar Produksi Per Departemen & Tipe</option>
					<option value="8">Daftar Produksi Per Departemen & Petugas</option>
					{{-- <option value="9" style="display:none">Daftar Produksi Per Palet</option> --}}
					<option value="9">Daftar Produksi Per Palet</option>
					<option value="10">Daftar Loss Per Departemen</option>
					<option value="11">Daftar Loss Per Departemen & Jenis</option>
					<option value="12">Daftar Loss Per Petugas</option>
					<option value="13">Daftar Loss Per Mesin</option>
					<option value="14">Kapasitas Produksi</option>
				</select>
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
	<div class="col-lg-4"></div>
</div>