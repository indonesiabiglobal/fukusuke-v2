<div class="container">
	<div class="row">
		<div class="col-lg-2"></div>
		<div class="col-lg-6">
            {{-- Filter tanggal awal --}}
            <div class="form-group">
                <label class="control-label col-md-4 col-xs-12 fw-bold">Tanggal Periode</label>
                <div class="col-12 mt-1">
                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon col-3 fw-bold">Awal: </span>
                            <input wire:model.defer="tglMasuk" type="text" class="form-control" style="padding:0.44rem"
                                data-provider="flatpickr" data-date-format="Y-m-d">
                            <span class="input-group-text py-0">
                                <i class="ri-calendar-event-fill fs-4"></i>
                            </span>

                            <select wire:ignore wire:model.defer="jamMasuk" class="form-control"
                                placeholder="- pilih jam kerja -">
                                @foreach ($workingShiftHour as $item)
                                    <option value="{{ $item->work_hour_from }}">{{ $item->work_hour_from }}</option>
                                @endforeach
                            </select>
                            <span class="input-group-text py-0">
                                <i class="ri-time-line fs-4"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            {{-- Filter tanggal akhir --}}
            <div class="form-group mt-1">
                <div class="col-12">
                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon col-3 fw-bold">Akhir: </span>
                            <input wire:model.defer="tglKeluar" type="text" class="form-control" style="padding:0.44rem"
                                data-provider="flatpickr" data-date-format="Y-m-d">
                            <span class="input-group-text py-0">
                                <i class="ri-calendar-event-fill fs-4"></i>
                            </span>

                            <select wire:ignore wire:model.defer="jamKeluar" class="form-control"
                                placeholder="- pilih jam kerja -">
                                @foreach ($workingShiftHour as $item)
                                    <option value="{{ $item->work_hour_till }}">{{ $item->work_hour_till }}</option>
                                @endforeach
                            </select>
                            {{-- <input wire:model.defer="jamKeluar" type="text" class="form-control" data-provider="timepickr" data-time-hrs="true" id="timepicker-24hrs"> --}}
                            <span class="input-group-text py-0">
                                <i class="ri-time-line fs-4"></i>
                            </span>
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
