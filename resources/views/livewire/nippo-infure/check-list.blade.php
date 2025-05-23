<div class="row">
	<div class="col-lg-2"></div>
	<div class="col-lg-6">
		<div class="form-group">
			<div class="input-group">
				<label class="control-label col-3 text-muted fw-bold">Tanggal Produksi</label>
				<div class="col-12 col-lg-9">
					<select class="form-select mb-0" wire:model.defer="transaksi">
						<option value="1" selected>Produksi</option>
						<option value="2">Proses</option>
					</select>
				</div>
			</div>
			<div class="col-12 mt-1">
				<div class="form-group">
					<div class="input-group">
						<span class="input-group-addon col-12 col-lg-3 text-muted fw-bold">Awal: </span>
						<input wire:model.defer="tglAwal" type="text" class="form-control" style="padding:0.44rem" data-provider="flatpickr" data-date-format="Y-m-d">
						<span class="input-group-text py-0">
							<i class="ri-calendar-event-fill fs-4"></i>
						</span>

						<div class="mb-1" wire:ignore>
							<select class="form-control" wire:model.defer="jamAwal" data-choices data-choices-sorting-false  data-choices-removeItem data-choices-search-field-label>
								<option value="">- All -</option>
								@foreach ($workingShiftHour as $item)
									<option value="{{ $item->work_hour_from }}" @if ($jamAwal == $item->work_hour_from) selected @endif>{{ $item->work_hour_from }}</option>
								@endforeach
							</select>
						</div>
						{{-- <input wire:model.defer="jamAwal" type="text" class="form-control" data-provider="timepickr" data-time-hrs="true" id="timepicker-24hrs"> --}}
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
						<span class="input-group-addon col-12 col-lg-3 text-muted fw-bold">Akhir: </span>
						<input wire:model.defer="tglAkhir" type="text" class="form-control" style="padding:0.44rem" data-provider="flatpickr" data-date-format="Y-m-d">
						<span class="input-group-text py-0">
							<i class="ri-calendar-event-fill fs-4"></i>
						</span>

						<div class="mb-1" wire:ignore>
							<select class="form-control" wire:model.defer="jamAkhir" data-choices data-choices-sorting-false  data-choices-removeItem data-choices-search-field-label>
								<option value="">- All -</option>
								@foreach ($workingShiftHour as $item)
									<option value="{{ $item->work_hour_till }}" @if ($jamAkhir == $item->work_hour_till) selected @endif>{{ $item->work_hour_till }}</option>
								@endforeach
							</select>
						</div>
						{{-- <input wire:model.defer="jamAkhir" type="text" class="form-control" data-provider="timepickr" data-time-hrs="true" id="timepicker-24hrs"> --}}
						<span class="input-group-text py-0">
							<i class="ri-time-line fs-4"></i>
						</span>
					</div>
				</div>
			</div>
		</div>
		<div class="form-group mt-1">
			<div class="input-group">
				<span class="input-group-addon col-12 col-lg-3 text-muted fw-bold">Jenis Report </span>
				<select wire:model="jenisReport" class="form-control" placeholder="- pilih jenis report -">
					<option value="Checklist" selected>Check List</option>
					<option value="Loss">Loss Infure</option>
				</select>
			</div>
		</div>
		<div class="form-group mt-1">
			<div class="input-group">
				<span class="input-group-addon col-12 col-lg-3 text-muted fw-bold">Nomor Proses </span>
				<input type="text" class="form-control" placeholder="1" wire:model.defer="noprosesawal">
				<span class="input-group-text readonly" readonly="readonly">
					~
				</span>
				<input type="text" class="form-control" placeholder="1000" wire:model.defer="noprosesakhir">
			</div>
		</div>
		<div class="form-group mt-1">
			<div class="input-group">
				<span class="input-group-addon col-12 col-lg-3 text-muted fw-bold">Nomor LPK </span>
                <div class="col-12 col-lg-9" x-data="{ lpk_no: @entangle('lpk_no').live, status: true }" x-init="$watch('lpk_no', value => {
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
                    <input
                        class="form-control"
                        style="padding:0.44rem"
                        type="text"
                        placeholder="000000-000"
                        x-model="lpk_no"
                        maxlength="10"
                        x-on:keydown.tab="$event.preventDefault(); $refs.machineInput.focus();"
                    />
                </div>
				{{-- <input type="text" class="form-control" placeholder="000000-000" wire:model.live.debounce.300ms="lpk_no"> --}}
			</div>
		</div>
		<div class="form-group mt-1">
			<div class="input-group">
				<span class="input-group-addon col-12 col-lg-3 text-muted fw-bold">Nomor Order </span>
				<input type="text" class="form-control" placeholder=".." wire:model.defer="nomorOrder">
			</div>
		</div>
        <div class="form-group mt-1">
			<div class="row">
				<div class="col-12 col-lg-3">
					<label for="product" class="form-label text-muted fw-bold">Departemen</label>
				</div>
				<div class="col-12 col-lg-9">
					<div class="mb-1" wire:ignore>
						<select class="form-control" wire:model.defer="departmentId" data-choices data-choices-sorting-false  data-choices-removeItem data-choices-search-field-label>
							<option value="">- All -</option>
							@foreach ($department as $item)
							<option value="{{ $item->id }}">{{ $item->name }}</option>
						@endforeach
						</select>
					</div>
				</div>
			</div>
		</div>
		<div class="form-group mt-1">
			<div class="row">
				<div class="col-12 col-lg-3">
					<label for="product" class="form-label text-muted fw-bold">Mesin</label>
				</div>
				<div class="col-12 col-lg-9">
					<div class="mb-1" wire:ignore>
						<select class="form-control" wire:model.defer="machineId" data-choices data-choices-sorting-false  data-choices-removeItem data-choices-search-field-label>
							<option value="">- All -</option>
							@foreach ($machine as $item)
							<option value="{{ $item->id }}">{{ $item->machineno }} - {{ $item->machinename }}</option>
						@endforeach
						</select>
					</div>
				</div>
			</div>
		</div>
        {{-- Produk --}}
        <div class="form-group mt-1">
            <div class="input-group">
                <span class="input-group-addon col-12 col-lg-3 text-muted fw-bold">Produk </span>
                <div class="col-12 col-lg-9" wire:ignore>
                    <select class="form-control" wire:model.defer="productId" data-choices data-choices-sorting-false
                        data-choices-removeItem data-choices-search-field-label>
                        <option value="">- All -</option>
                        @foreach ($products as $item)
                            <option data-custom-properties='{"code": "{{ $item->code }}"}' value="{{ $item->id }}">{{ $item->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
		<div class="form-group mt-1">
			<div class="input-group">
				<span class="input-group-addon col-12 col-lg-3 text-muted fw-bold">Nomor Han</span>
                <div class="col-12 col-lg-9" x-data="{ nomorHan: @entangle('nomorHan'), status: true }" x-init="$watch('nomorHan', value => {
                        if (value.length === 2 && status) {
                            nomorHan = value + '-';
                        }
                        if (value.length === 5 && status) {
                            nomorHan = value + '-';
                        }
                        if (value.length === 8 && status) {
                            nomorHan = value + '-';
                        }
                        if (value.length < 10) {
                            status = true;
                        }
                        if (value.length === 3 || value.length === 6 || value.length === 9) {
                            status = false;
                        }
                        if (value.length > 12) {
                            nomorHan = value.substring(0, 12);
                        }
                    })">
                    <input
                        class="form-control"
                        style="padding:0.44rem"
                        type="text"
                        placeholder="00-00-00-00A"
                        x-model="nomorHan"
                        maxlength="12"
                        x-on:keydown.tab="$event.preventDefault(); $refs.nomor_barcode.focus();"
                    />
                </div>
				{{-- <input type="text" class="form-control" placeholder="00-00-00A" wire:model.defer="nomorHan" /> --}}
			</div>
		</div>
		<hr />
		<button type="button" class="btn btn-success btn-print" wire:click="export" style="width:99%" wire:loading.attr="disabled">
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
	<div class="col-lg-4"></div>
</div>
