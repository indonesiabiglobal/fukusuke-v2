<form>
	<div class="row mt-3">
		<div class="col-12 col-lg-6">
			<div class="form-group">
				<div class="input-group">
					<span class="input-group-text readonly">
						Nomor Palet Sumber
					</span>
					<input wire:model.defer="searchOld" class="form-control" type="text" placeholder="A0000-000000" />
					<button wire:click="search" type="button" class="btn btn-light">
						<span wire:loading.remove wire:target="search">
							<i class="ri-search-line"></i>
						</span>
						<div wire:loading wire:target="search">
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

			<div class="card border-0 shadow mb-4 mt-4">
				<div class="card-body">
					<div class="table-responsive">
						<table class="table table-centered table-nowrap mb-0 rounded">
							<thead class="thead-light">
								<tr>
									<th class="border-0 rounded-start">Action</th>
									<th class="border-0">Nomor LOT</th>
									<th class="border-0">Mesin</th>
									<th class="border-0">Tg. Produksi</th>
									<th class="border-0">Jumlah Box</th>
								</tr>
							</thead>
							<tbody>
								@forelse ($data as $item)
									<tr>
										<td>
											<button type="button" class="btn btn-success" wire:click="import({{$item->id}})">
												<i class="fa fa-edit"></i> Import
											</button>
										</td>
										<td>
											{{ $item->nomor_lot }}
										</td>
										<td>
											{{ $item->machineno }}
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
					<span class="input-group-text readonly">
						Nomor Palet Tujuan
					</span>
					<input wire:model.defer="searchNew" class="form-control" type="text" placeholder="A0000-000000" />
					<button wire:click="searchTujuan" type="button" class="btn btn-light">
						<span wire:loading.remove wire:target="searchTujuan">
							<i class="ri-search-line"></i>
						</span>
						<div wire:loading wire:target="searchTujuan">
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

			<div class="card border-0 shadow mb-4 mt-4">
				<div class="card-body">
					<div class="table-responsive">
						<table class="table table-centered table-nowrap mb-0 rounded">
							<thead class="thead-light">
								<tr>
									<th class="border-0">Nomor LOT</th>
									<th class="border-0">Mesin</th>
									<th class="border-0">Tg. Produksi</th>
									<th class="border-0">Jumlah Box</th>
								</tr>
							</thead>
							<tbody>
								@forelse ($result as $item)
									<tr>
										<td>
											{{ $item->nomor_lot }}
										</td>
										<td>
											{{ $item->machineno }}
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
		<div class="modal fade" id="modal-add" tabindex="-1" role="dialog" aria-labelledby="modal-add" aria-hidden="true" wire:ignore.self>
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="h6 modal-title">Mutasi Palet</h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
					<div class="modal-body">
						<div class="row">
							<div class="col-lg-12 mb-1">
								<div class="form-group">
									<label>Nomor Lot </label>
									<div class="input-group col-md-9 col-xs-8">
										<input class="form-control" type="text" wire:model="nomor_lot" placeholder="..." />
									</div>
								</div>
							</div>
							<div class="col-lg-12 mb-1">
								<div class="form-group">
									<label>Kode Loss </label>
									<div class="input-group col-md-9 col-xs-8">
										<input class="form-control readonly" readonly="readonly" type="text" wire:model.defer="qty_seitai" placeholder="..." />
									</div>
								</div>
							</div>
							<div class="col-lg-12 mb-1">
								<div class="form-group">
									<label>Berat Loss </label>
									<div class="input-group col-md-9 col-xs-8">
										<input class="form-control" type="text" wire:model.defer="qty_mutasi" placeholder="0" />
										@error('qty_mutasi')
											<span class="invalid-feedback">{{ $message }}</span>
										@enderror
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						{{-- <button type="button" class="btn btn-secondary">Accept</button> --}}
						<button type="button" class="btn btn-link text-gray-600 ms-auto" data-bs-dismiss="modal">Close</button>
						<button type="button" class="btn btn-success" wire:click="saveMutasi">
							<span wire:loading.remove wire:target="saveMutasi">
								Save
							</span>
							<div wire:loading wire:target="saveMutasi">
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
        </div>
		<div class="col-12 col-lg-6">
			<div class="row">
				<div class="col-12 col-lg-2">
					<label for="product" class="form-label text-muted fw-bold">Product</label>
				</div>
				<div class="col-12 col-lg-10">
					<div class="mb-1" wire:ignore>
						<select class="form-control"  wire:model.defer="idProduct" id="product" name="product" data-choices data-choices-sorting-false  data-choices-removeItem data-choices-search-field-label>
							<option value="">- All -</option>
							@foreach ($products as $item)
								<option value="{{ $item->id }}">{{ $item->name }}</option>
							@endforeach
						</select>
					</div>
				</div>
			</div>
		</div>
		<div class="col-12 col-lg-6">
			<div class="col-lg-12" style="border-top:1px solid #efefef">
                <div class="toolbar">
                    <button id="btnFilter" type="button" class="btn btn-warning" wire:click="cancel">
                        <i class="fa fa-back"></i> Close
                    </button>
					<button id="btnFilter" type="button" class="btn btn-danger" wire:click="delete">
						<span wire:loading.remove wire:target="delete">
							<i class="ri-repeat-line"></i> Undo
						</span>
						<div wire:loading wire:target="delete">
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
                    {{-- <button id="btnCreate" type="submit" class="btn btn-success">
                        <i class="fa fa-plus"></i> Proses Mutasi
                    </button> --}}
                </div>
            </div>
		</div>
	</div>
</form>
@script
<script>
    $wire.on('showModal', () => {
      $('#modal-add').modal('show');
    });

    $wire.on('closeModal', () => {
      $('#modal-add').modal('hide');
    });
 </script>
 @endscript
{{-- <script>
    document.addEventListener('livewire:load', function () {
        Livewire.on('showModal', () => {
            $('#modal-add').modal('show');
        });
        Livewire.on('closeModal', () => {
            $('#modal-add').modal('hide');
        });
    });
</script> --}}
