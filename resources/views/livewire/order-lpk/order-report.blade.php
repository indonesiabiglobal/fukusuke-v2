<div class="container">
	<div class="row">
		<div class="col-lg-2"></div>
		<div class="col-lg-6">
			<div class="form-group">
				<div class="input-group">
					<label class="control-label col-12 fw-bold">Filter Tanggal</label>
				</div>
				<div class="col-12 mt-1">
					<div class="form-group">
						<div class="input-group">
							<span class="input-group-addon col-12 col-lg-3 fw-bold">Awal: </span>
							{{-- <input class="form-control datepicker-input" type="date" wire:model.defer="tglMasuk" placeholder="yyyy/mm/dd" /> --}}
							<input wire:model.defer="tglMasuk" type="text" class="form-control" style="padding:0.44rem" data-provider="flatpickr" data-date-format="d/m/Y">
						</div>
					</div>
				</div>
			</div>
			<div class="form-group mt-1">
				<div class="col-12">
					<div class="form-group">
						<div class="input-group">
							<span class="input-group-addon col-12 col-lg-3 fw-bold">Akhir: </span>
							{{-- <input class="form-control datepicker-input" type="date" wire:model.defer="tglKeluar" placeholder="yyyy/mm/dd" /> --}}
							<input wire:model.defer="tglKeluar" type="text" class="form-control" style="padding:0.44rem" data-provider="flatpickr" data-date-format="d/m/Y">
						</div>
					</div>
				</div>
			</div>
			<div class="form-group mt-1">
				<div class="col-12">
					<div class="form-group">
						<div class="input-group">
							<span class="input-group-addon col-3 fw-bold">Filter:&nbsp;</span>
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
							<span class="input-group-addon col-3 fw-bold">Buyer</span>
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
							<span class="input-group-addon col-3 fw-bold">Jenis Report</span>
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
				<div class="input-group">
					<button type="button" class="btn btn-success btn-print" wire:click="export" style="width:99%">
						<span wire:loading.remove wire:target="export">
							<i class="ri-printer-line"></i> Generate Report
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
		</div>
		<div class="col-lg-4"></div>
	</div>
</div>
