<div class="row">
    <form wire:submit.prevent="save">
        <div class="row mt-2">
			<div class="col-12 col-lg-12">
				<div class="form-group">                            
					<div class="input-group">
						<label class="control-label col-12 col-lg-2">Tanggal Kenpin</label>
						<input class="form-control datepicker-input" type="date" wire:model.defer="kenpin_date" placeholder="yyyy/mm/dd"/>
					</div>
				</div>
			</div>
			<div class="col-12 col-lg-12 mt-1">
				<div class="form-group">
					<div class="input-group">
						<label class="control-label col-12 col-lg-2">Nomor Kenpin</label>
						<input type="text" class="form-control" wire:model="kenpin_no" />
					</div>
				</div>
			</div>			
			<div class="col-12 col-lg-4 mt-1">
				<div class="form-group">
					<div class="input-group">
						<label class="control-label col-12 col-lg-6">Nomor Order</label>
						<input type="text" placeholder="-" class="form-control col-4" wire:model.debounce.300ms="code" />
					</div>
				</div>
			</div>
			<div class="col-12 col-lg-8 mt-1">
				<div class="form-group">                            
					<div class="input-group">
						<label class="control-label"></label>
						<input type="text" placeholder="-" class="form-control col-8 readonly" readonly="readonly" wire:model="name" />
					</div>
				</div>
			</div>
			<div class="col-12 col-lg-4 mt-1">
				<div class="form-group">
					<div class="input-group">
						<label class="control-label col-12 col-lg-6">Petugas</label>
						<input type="text" placeholder="-" class="form-control" wire:model="employeeno" />
						@error('employeeno')
							<span class="invalid-feedback">{{ $message }}</span>
						@enderror
					</div>
				</div>
			</div>
			<div class="col-12 col-lg-8 mt-1">
				<div class="form-group">
					<div class="input-group">
						<label class="control-label"></label>
						<input type="text" placeholder="-" class="form-control readonly" readonly="readonly" wire:model="empname" />
					</div>
				</div>
			</div>
			<div class="col-12 col-lg-12 mt-1">
				<div class="form-group">
					<div class="input-group">
						<label class="control-label col-12 col-lg-2">NG</label>
						<input type="text" class="form-control" wire:model="remark" />
					</div>
				</div>
			</div>
			<div class="col-12 col-lg-12 mt-1">
				<div class="form-group">
					<div class="input-group">
						<label class="control-label col-12 col-lg-2">Status</label>
						<select wire:model="status" class="form-control" placeholder="- all -">
							<option value="">- all -</option>
							<option value="1">Proses</option>
							<option value="2">Finish</option>
						</select>
					</div>
				</div>
			</div>
        </div>
        <hr/>
        <div class="row">
            <div class="col-12 col-lg-5">
                <div class="form-group">				
					<div class="input-group">
						<span class="input-group-text readonly">
							Nomor Palet
						</span>
						<input wire:model.defer="nomor_palet" class="form-control" type="text" placeholder="A0000-000000" />
						<button wire:click="search" type="button" class="btn btn-info">
							<i class="fa fa-search"></i>
						</button>
					</div>
				</div>
            </div>
			<div class="col-lg-3"></div>
            <div class="col-lg-4" style="border-top:1px solid #efefef">
                <div class="toolbar">
                    <button type="button" class="btn btn-warning" wire:click="cancel">
                        <i class="fa fa-back"></i> Close
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fa fa-plus"></i> Save
                    </button>
                    <button type="button" class="btn btn-success btn-print" disabled="disabled">
                        <i class="fa fa-print"></i> Print
                    </button>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modal-edit" tabindex="-1" role="dialog" aria-labelledby="modal-edit" aria-hidden="true" wire:ignore.self>
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="h6 modal-title">Edit Palet Setai	</h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-lg-12 mb-1">
                                    <div class="form-group">
                                        <label>Nomor Palet </label>
                                        <div class="input-group col-md-9 col-xs-8">
                                            <input class="form-control readonly" readonly="readonly" type="text" wire:model.defer="no_palet" placeholder="..." />
                                            @error('no_palet')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
								<div class="col-lg-12 mb-1">
                                    <div class="form-group">
                                        <label>Nomor Lot </label>
                                        <div class="input-group col-md-9 col-xs-8">
                                            <input class="form-control readonly" readonly="readonly" type="text" wire:model.defer="no_lot" placeholder="..." />
                                            @error('no_lot')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
								<div class="col-lg-12 mb-1">
                                    <div class="form-group">
                                        <label>No LPK </label>
                                        <div class="input-group col-md-9 col-xs-8">
                                            <input class="form-control readonly" readonly="readonly" type="text" wire:model.defer="no_lpk" placeholder="..." />
                                            @error('no_lpk')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
								<div class="col-lg-12 mb-1">
                                    <div class="form-group">
                                        <label>Quantity </label>
                                        <div class="input-group col-md-9 col-xs-8">
                                            <input class="form-control readonly" readonly="readonly" type="text" wire:model.defer="quantity" placeholder="..." />
                                            @error('quantity')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
								<div class="col-lg-12 mb-1">
                                    <div class="form-group">
                                        <label>Loss (Lembar) </label>
                                        <div class="input-group col-md-9 col-xs-8">
                                            <input class="form-control" type="text" wire:model.defer="qty_loss" placeholder="..." />
                                            @error('qty_loss')
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
                            <button type="submit" class="btn btn-success" wire:click="saveSeitai">
                                Save
                            </button>
                        </div>
                </div>
            </div>
        </div>
        <div class="card border-0 shadow mb-4 mt-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-centered table-nowrap mb-0 rounded">
                        <thead class="thead-light">
                            <tr>
                                <th class="border-0 rounded-start">Action</th>
                                <th class="border-0">Nomor Palet</th>
                                <th class="border-0">Nomor LOT</th>
								<th class="border-0">No LPK</th>
                                <th class="border-0">Tgl Produksi</th>
								<th class="border-0">Quantity</th>
                                <th class="border-0 rounded-end">Loss (Lembar)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($details as $item)
							<tr>
                                <td>
									<button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#modal-edit" wire:click="edit({{$item->id}})">
										<i class="fa fa-edit"></i> Edit
									</button>

									<button type="button" class="btn btn-danger" wire:click="deleteSeitai({{$item->id}})">
										<i class="fa fa-trash"></i> Delete
									</button>
								</td>
                                <td>                                
                                    {{ $item->nomor_palet }}
                                </td>
                                <td>
                                    {{ $item->nomor_lot }}
                                </td>
                                <td>
                                    {{ $item->lpk_no }}
                                </td>
                                <td>
                                    {{ $item->production_date }}
                                </td>
                                <td>
                                    {{ $item->qty_produksi }}
                                </td>
								<td>
                                    {{ $item->qty_loss }}
                                </td>
                            </tr>
							@empty
							<tr>
                                <td colspan="7" class="text-center">No results found</td>
                            </tr>
							@endforelse
                            <tr>
                                <td colspan="6" class="text-end">Berat Loss Total (kg):</td>
                                <td colspan="1" class="text-center">0</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </form>
</div>
<script>
    document.addEventListener('livewire:load', function () {
        Livewire.on('showModal', () => {
            $('#modal-edit').modal('show');
        });
        Livewire.on('closeModal', () => {
            $('#modal-edit').modal('hide');
        });
    });
</script>