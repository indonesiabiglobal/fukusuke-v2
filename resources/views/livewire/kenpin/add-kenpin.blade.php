<div class="row">
	{{-- @if (session()->has('message'))
		<div class="alert alert-success">
			{{ session('message') }}
		</div>
	@endif
	@if ($errors->any())
		<div class="alert alert-danger">
			<ul>
				@foreach ($errors->all() as $error)
					<li>{{ $error }}</li>
				@endforeach
			</ul>
		</div>
	@endif --}}
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
						<label class="control-label col-12 col-lg-6">Nomor LPK</label>
						<input type="text" class="form-control" placeholder="000000-00"  wire:model="lpk_no" />
						@error('lpk_no')
							<span class="invalid-feedback">{{ $message }}</span>
						@enderror
					</div>
				</div>
			</div>
			<div class="col-12 col-lg-4 mt-1">
				<div class="form-group">
					<div class="input-group">
						<label class="control-label col-12 col-lg-5">Tanggal LPK</label>
						<input class="form-control readonly datepicker-input" readonly="readonly" type="date" wire:model.defer="lpk_date" placeholder="yyyy/mm/dd"/>
					</div>
				</div>
			</div>
			<div class="col-12 col-lg-4 mt-1">
				<div class="form-group">                            
					<div class="input-group">
						<label class="control-label col-12 col-lg-5">Panjang LPK</label>
						<input type="text" placeholder="-" class="form-control readonly" readonly="readonly" wire:model="panjang_lpk" />
					</div>
				</div>
			</div>
			<div class="col-12 col-lg-4 mt-1">
				<div class="form-group">
					<div class="input-group">
						<label class="control-label col-12 col-lg-6">Nomor Order</label>
						<input type="text" placeholder="-" class="form-control readonly" readonly="readonly" wire:model="code" />
					</div>
				</div>
			</div>
			<div class="col-12 col-lg-8 mt-1">
				<div class="form-group">                            
					<div class="input-group">
						<label class="control-label"></label>
						<input type="text" placeholder="-" class="form-control readonly" readonly="readonly" wire:model="name" />
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
						<select wire:model="status_kenpin" class="form-control" placeholder="- all -">
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
            <div class="col-lg-8">
				<button wire:click="addGentan" type="button" class="btn btn-success">
                    <i class="fa fa-plus"></i> Add Gentan
                </button>
            </div>
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
        <div class="modal fade" id="modal-add" tabindex="-1" role="dialog" aria-labelledby="modal-add" aria-hidden="true" wire:ignore.self>
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="h6 modal-title">Add Gentan Infure</h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-lg-12 mb-1">
                                    <div class="form-group">
                                        <label>Nomor Gentan </label>
                                        <div class="input-group col-md-9 col-xs-8">
                                            <input class="form-control" type="text" wire:model="gentan_no" placeholder="..." />
                                            @error('loss_infure_id')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
								<div class="col-lg-12 mb-1">
                                    <div class="form-group">
                                        <label>Nomor Mesin </label>
                                        <div class="input-group col-md-9 col-xs-8">
                                            <input class="form-control readonly" readonly="readonly" type="text" wire:model.defer="machineno" placeholder="..." />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12 mb-1">
                                    <div class="form-group">
                                        <label>Petugas </label>
                                        <div class="input-group col-md-9 col-xs-8">
                                            <input class="form-control readonly" readonly="readonly" type="text" wire:model.defer="namapetugas" placeholder="..." />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12 mb-1">
                                    <div class="form-group">
                                        <label>Berat Loss </label>
                                        <div class="input-group col-md-9 col-xs-8">
                                            <input class="form-control" type="text" wire:model.defer="berat_loss" placeholder="0" />
                                            @error('berat_loss')
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
                            <button type="button" class="btn btn-success" wire:click="saveGentan">
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
                                <th class="border-0">Gentan</th>
                                <th class="border-0">No Mesin</th>
								<th class="border-0">Shift</th>
                                <th class="border-0">Petugas</th>
								<th class="border-0">Nomor Han</th>
                                <th class="border-0">Tgl Produksi</th>
                                <th class="border-0 rounded-end">Berat Loss (kg)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $total=0
                            @endphp
                            @forelse ($details as $item)
                                <tr>
                                    <td>
                                        <button type="button" class="btn btn-danger" wire:click="deleteInfure({{$item->id}})">
                                            <i class="fa fa-trash"></i> Delete
                                        </button>
                                    </td>
                                    <td>                                
                                        {{ $item->gentan_no }}
                                    </td>
                                    <td>
                                        {{ $item->nomesin }}
                                    </td>
                                    <td>
                                        {{ $item->work_shift }}
                                    </td>
									<td>                                
                                        {{ $item->namapetugas }}
                                    </td>
                                    <td>
                                        {{ $item->nomor_han }}
                                    </td>
                                    <td>
                                        {{ $item->tglproduksi }}
                                    </td>
									<td>
										{{ $item->berat_loss }}
									</td>
                                </tr>
                                @php
                                    $total += $item->berat_loss;
                                @endphp
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">No results found</td>
                                </tr>
                            @endforelse
                            <tr>
                                <td colspan="7" class="text-end">Berat Loss Total (kg):</td>
                                <td colspan="1" class="text-center">{{ $total }}</td>
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
            $('#modal-add').modal('show');
        });
        Livewire.on('closeModal', () => {
            $('#modal-add').modal('hide');
        });
    });
</script>