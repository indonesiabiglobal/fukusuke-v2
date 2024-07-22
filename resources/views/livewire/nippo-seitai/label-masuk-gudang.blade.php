<form>
	<div class="row mt-3">
		<div class="col-12 col-lg-6">
			<div class="form-group">				
				<div class="input-group">
					<span class="input-group-text readonly">
						Nomor Palet Sumber
					</span>
					<input wire:model.defer="nomor_palet" class="form-control" type="text" placeholder="A0000-000000" />
					<button wire:click="search" type="button" class="btn btn-light">
						<i class="ri-search-line"></i>
					</button>
				</div>
			</div>
			<div class="card border-0 shadow mb-4 mt-4">
				<div class="card-body">
					<div class="table-responsive">
						<table class="table table-centered table-nowrap mb-0 rounded">
							<thead class="table-light">
								<tr>
									<th class="border-0 rounded-start">Nomor LOT</th>
									<th class="border-0">Mesin</th>
									<th class="border-0">Tg. Produksi</th>
									<th class="border-0">Jumlah Box</th>
								</tr>
							</thead>
							<tbody>
								@forelse ($data as $item)
									<tr>
										<td>
											{{ $item->nomor_lot }}
										</td>
										<td>
											{{ $item->machinename }}
										</td>
										<td>
											{{ $item->production_date }}
										</td>
										<td>
											{{ $item->qty_produksi }}
										</td>
									</tr>
								@empty
									<tr>
										<td colspan="8" class="text-center">No results found</td>
									</tr>
								@endforelse
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
		<div class="col-12 col-lg-6">
			<div class="form-group">
				<div class="input-group">
					<label class="control-label col-3">Nomor Produk</label>
					<input type="text" class="form-control readonly" readonly="readonly" wire:model="code" />
				</div>
			</div>
			<div class="form-group mt-1">
				<div class="input-group">
					<label class="control-label col-3">Nama Produk</label>
					<input type="text" class="form-control readonly" readonly="readonly" wire:model="name" />
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-md-4 col-xs-12"></label>
				<div class="input-group">
					<button type="button" class="btn btn-success btn-print" wire:click="print" style="width:99%">
						<i class="fa fa-print"></i> Print
					</button>
				</div>
			</div>
		</div>
	</div>
</form>
{{-- <script>
	document.addEventListener('livewire:load', function () {
		Livewire.on('redirectToPrint', function (data) {
			var printUrl = '{{ route('report-masuk-gudang') }}?test=' +  'data.test';
			window.open(printUrl, '_blank');
		});
	});
</script> --}}