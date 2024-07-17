<div class="row">
	<div class="col-lg-2"></div>
	<div class="col-lg-8">
    <form wire:submit.prevent="save">
		<div class="form-group">
			<div class="input-group">
                <label class="control-label col-12 col-lg-3 fw-bold text-muted">Tanggal Proses</label>
				<input class="form-control datepicker-input readonly" readonly="readonly" type="date" wire:model="process_date" placeholder="yyyy/mm/dd"/>
				@error('process_date')
					<span class="invalid-feedback">{{ $message }}</span>
				@enderror
			</div>
		</div>
		<div class="form-group mt-1">
			<div class="input-group">
                <label class="control-label col-12 col-lg-3 fw-bold text-muted">PO Number</label>
				<input type="text" class="form-control"  wire:model="po_no" />
				@error('po_no')
					<span class="invalid-feedback">{{ $message }}</span>
				@enderror
			</div>
		</div>
		<div class="form-group mt-1">
			<div class="input-group">
                <label class="control-label col-12 col-lg-3 fw-bold text-muted">Tanggal Order</label>
				<input class="form-control datepicker-input" type="date" wire:model="order_date" placeholder="yyyy/mm/dd"/>
				@error('order_date')
					<span class="invalid-feedback">{{ $message }}</span>
				@enderror
			</div>
		</div>
		<div class="form-group mt-1">
			<div class="input-group">
                <label class="control-label col-12 col-lg-3 fw-bold text-muted">Nomor Order</label>
				<input type="text" class="form-control" wire:model.debounce.300ms="product_id" />
				@error('product_id')
					<span class="invalid-feedback">{{ $message }}</span>
				@enderror
			</div>			
		</div>
		<div class="form-group mt-1">
			<div class="input-group">
                <label class="control-label col-12 col-lg-3 fw-bold text-muted">Nama Produk</label>
				<input type="text" class="form-control readonly"  readonly="readonly" wire:model="product_name" />
			</div>
		</div>
		<div class="form-group mt-1">
			<div class="input-group">
                <label class="control-label col-12 col-lg-3 fw-bold text-muted">Dimensi</label>
				<input type="text" class="form-control readonly"  readonly="readonly" wire:model="dimensi" />
			</div>
		</div>
		<div class="form-group mt-1">
			<div class="input-group">
                <label class="control-label col-12 col-lg-3 fw-bold text-muted">Jumlah Order</label>
				<input type="text" class="form-control" wire:model="order_qty" />
				@error('order_qty')
					<span class="invalid-feedback">{{ $message }}</span>
				@enderror
			</div>
		</div>
        <div class="form-group mt-1">
			<div class="input-group">
                <label class="control-label col-12 col-lg-3 fw-bold text-muted">Unit</label>
				<select class="form-control" wire:model="unit_id" placeholder="">
					<option value="0">Set</option>
					<option value="1">Lembar</option>
					<option value="2">Meter</option>
				</select>
			</div>
		</div>
        <div class="form-group mt-1">
			<div class="input-group">
                <label class="control-label col-12 col-lg-3 fw-bold text-muted">Tanggal Stuffing</label>
				<input class="form-control datepicker-input" type="date" wire:model="stufingdate" placeholder="yyyy/mm/dd"/>
				@error('stufingdate')
					<span class="invalid-feedback">{{ $message }}</span>
				@enderror
			</div>
		</div>
        <div class="form-group mt-1">
			<div class="input-group">
                <label class="control-label col-12 col-lg-3 fw-bold text-muted">ETD</label>
				<input class="form-control datepicker-input" type="date" wire:model="etddate" placeholder="yyyy/mm/dd"/>
				@error('etddate')
					<span class="invalid-feedback">{{ $message }}</span>
				@enderror
			</div>
		</div>
        <div class="form-group mt-1">
			<div class="input-group">
                <label class="control-label col-12 col-lg-3 fw-bold text-muted">ETA</label>
				<input class="form-control datepicker-input" type="date" wire:model="etadate" placeholder="yyyy/mm/dd"/>
				@error('etadate')
					<span class="invalid-feedback">{{ $message }}</span>
				@enderror
			</div>
		</div>
        <div class="form-group mt-1">
			<div class="input-group">
                <label class="control-label col-12 col-lg-3 fw-bold text-muted">Buyer</label>
				<select class="form-control" wire:model="buyer_id" placeholder="">
					@foreach ($buyer as $item)
						@if ( $item->id == $buyer_id )
							<option value="{{ $item->id }}">{{ $item->name }}</option>
						@else
							<option value="{{ $item->id }}">{{ $item->name }}</option>
						@endif                        
                    @endforeach
				</select>
			</div>
		</div>
		<hr />
		<div class="col-lg-12">
            <div class="toolbar">
                <button id="btnFilter" type="button" class="btn btn-warning w-lg" wire:click="cancel">
                    <i class="ri-close-line"> </i> Close
                </button>

				@if ($status_order == 0)
					<button id="btnFilter" type="button" class="btn btn-danger w-lg"  data-bs-toggle="modal" data-bs-target="#modal-default">
						<i class="ri-delete-bin-line"></i> Delete
					</button>

					<button id="btnCreate" type="submit" class="btn btn-success w-lg">
						<i class="ri-save-3-line"></i> Update
					</button>

					<button type="button" class="btn btn-success btn-print w-lg" wire:click="print">
						<i class="bx bx-printer"></i> Print
					</button>				
				@endif
				@if ($status_order == 1)
					<p class="text-secondary mb-0">Data sudah di LPK ! ..</p>
				@endif
                
				<script>
					document.addEventListener('livewire:load', function () {
						Livewire.on('redirectToPrint', function (data) {
							var printUrl = '{{ route('cetak-order') }}?processdate=' +  data.processdate + 
							'&po_no=' + data.po_no +
							'&order_date=' + data.order_date +
							'&code=' + data.code +
							'&name=' + data.name +
							'&dimensi=' + data.dimensi +
							'&order_qty=' + data.order_qty +
							'&stufingdate=' + data.stufingdate +
							'&etddate=' + data.etddate +
							'&etadate=' + data.etadate +
							'&namabuyer=' + data.namabuyer;
							window.open(printUrl, '_blank');
						});
					});
				</script>
            </div>
        </div>
		<div class="modal fade" id="modal-default" tabindex="-1" role="dialog" aria-labelledby="modal-default" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content">
					{{-- <div class="modal-header">
						<h2 class="h6 modal-title">Terms of Service</h2>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					</div> --}}
					<div class="modal-body">
						<h3>
							Are you sure want to delete ?
						</h3>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" wire:click="delete">Yes</button>
						<button type="button" class="btn btn-link text-gray-600 ms-auto" data-bs-dismiss="modal">No</button>
					</div>
				</div>
			</div>
		</div>
    </form>        
	</div>
	<div class="col-lg-2"></div>
</div>