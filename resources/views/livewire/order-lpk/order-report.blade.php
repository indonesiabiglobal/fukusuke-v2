<div class="container">
	<div class="row">
		<div class="col-lg-2"></div>
		<div class="col-lg-6">
			<div class="form-group">
				<div class="input-group col-md-9 col-xs-8">
					<label class="control-label col-4">Filter Tanggal</label>
				</div>
				<div class="col-12 mt-1">
					<div class="form-group">
						<div class="input-group">
							<span class="input-group-addon col-12 col-lg-2">Awal: </span>
							<input class="form-control datepicker-input" type="date" wire:model.defer="tglMasuk" placeholder="yyyy/mm/dd" />
						</div>
					</div>
				</div>
			</div>
			<div class="form-group mt-1">
				<div class="col-12">
					<div class="form-group">
						<div class="input-group">
							<span class="input-group-addon col-12 col-lg-2">Akhir: </span>
							<input class="form-control datepicker-input" type="date" wire:model.defer="tglKeluar" placeholder="yyyy/mm/dd" />
						</div>
					</div>
				</div>
			</div>
			<div class="form-group mt-1">
				<div class="col-12">
					<div class="form-group">
						<div class="input-group">
							<span class="input-group-addon col-2">Filter:&nbsp;</span>
							<select class="form-control" wire:model.defer="filter">
								<option value="1">Tanggal Order</option>
								<option value="2">Tanggal Proses</option>
							</select>
						</div>
					</div>
				</div>
			</div>
			<div class="form-group mt-1">
				<div class="col-12">
					<div class="form-group">
						<div class="input-group">
							<span class="input-group-addon col-2">Buyer</span>
							<select class="form-control" wire:model.defer="buyer_id">
								<option value=""> -- ALL --</option>
								@foreach ($buyer as $item)
									<option value="{{ $item->id }}">{{ $item->name }}</option>
								@endforeach
							</select>
						</div>
					</div>
				</div>
			</div>
			<div class="form-group mt-1">
				<div class="col-12">
					<div class="form-group">
						<div class="input-group">
							<span class="input-group-addon col-2">Jenis Report</span>
							<select class="form-control">
								<option value=""> -- ALL --</option>
								<option value="1">Daftar Order</option>
								<option value="2">Daftar Order Per Buyer Per Tipe</option>
								<option value="3">CheckList Order</option>
								<option value="4">CheckList LPK</option>
								<option value="5">Progress Order</option>
							</select>
						</div>
					</div>
				</div>
			</div>
			<hr />
			<div class="form-group">
				<label class="control-label col-md-4 col-xs-12"></label>
				<div class="input-group col-md-8 col-xs-12">
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
</div>
