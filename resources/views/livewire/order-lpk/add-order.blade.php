<div class="row">
	<div class="col-lg-2"></div>
	<div class="col-lg-8">
    <form wire:submit.prevent="save">
		<div class="form-group">
			<div class="input-group">
                <label class="control-label col-12 col-lg-3 fw-bold text-muted">Tanggal Proses</label>
				<input class="form-control datepicker-input @error('process_date') is-invalid @enderror" type="date" wire:model="process_date" placeholder="yyyy/mm/dd"/ disabled>
				@error('process_date')
					<span class="invalid-feedback">{{ $message }}</span>
				@enderror
			</div>
		</div>
		<div class="form-group mt-1">
			<div class="input-group">
                <label class="control-label col-12 col-lg-3 fw-bold text-muted">PO Number</label>
				<input type="text" class="form-control @error('po_no') is-invalid @enderror" wire:model="po_no" required/>
				@error('po_no')
				<div class="invalid-feedback">
					{{ $message }}
				</div>
				@enderror
			</div>
		</div>
		<div class="form-group mt-1">
			<div class="input-group">
                <label class="control-label col-12 col-lg-3 fw-bold text-muted">Tanggal Order</label>
				<input wire:model="order_date" type="text" class="form-control @error('order_date') is-invalid @enderror" data-provider="flatpickr" data-date-format="d/m/Y">
				@error('order_date')
					<span class="invalid-feedback">{{ $message }}</span>
				@enderror
			</div>
		</div>
		<div class="form-group mt-1">
			<div class="input-group">
                <label class="control-label col-12 col-lg-3 fw-bold text-muted">Nomor Order</label>
				<input type="text" class="form-control @error('product_id') is-invalid @enderror" wire:model.live.debounce.300ms="product_id" />
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
				<input type="text" class="form-control @error('order_qty') is-invalid @enderror" wire:model="order_qty" />
				@error('order_qty')
					<span class="invalid-feedback">{{ $message }}</span>
				@enderror
			</div>
		</div>
        <div class="form-group mt-1">
			<div class="input-group">
                <label class="control-label col-12 col-lg-3 fw-bold text-muted">Unit</label>
				<select class="form-control @error('unit_id') is-invalid @enderror" wire:model="unit_id" placeholder="" required>
					<option value="0">Set</option>
					<option value="1">Lembar</option>
					<option value="2">Meter</option>
				</select>
				@error('unit_id') 
					<span class="invalid-tooltip">{{ $message }}</span> 
				@enderror
			</div>
		</div>
        <div class="form-group mt-1">
			<div class="input-group">
                <label class="control-label col-12 col-lg-3 fw-bold text-muted">Tanggal Stuffing</label>
				<input class="form-control datepicker-input @error('stufingdate') is-invalid @enderror" type="text" wire:model="stufingdate" data-provider="flatpickr" data-date-format="d/m/Y" placeholder="yyyy/mm/dd"/>
				@error('stufingdate')
					<span class="invalid-feedback">{{ $message }}</span>
				@enderror
			</div>
		</div>
        <div class="form-group mt-1">
			<div class="input-group">
                <label class="control-label col-12 col-lg-3 fw-bold text-muted">ETD</label>
				<input class="form-control datepicker-input @error('etddate') is-invalid @enderror" type="text" wire:model="etddate" data-provider="flatpickr" data-date-format="d/m/Y" placeholder="yyyy/mm/dd"/>
				@error('etddate')
					<span class="invalid-feedback">{{ $message }}</span>
				@enderror
			</div>
		</div>
        <div class="form-group mt-1">
			<div class="input-group">
                <label class="control-label col-12 col-lg-3 fw-bold text-muted">ETA</label>
				<input class="form-control datepicker-input @error('etadate') is-invalid @enderror" type="text" wire:model="etadate" data-provider="flatpickr" data-date-format="d/m/Y" placeholder="yyyy/mm/dd"/>
				@error('etadate')
					<span class="invalid-feedback">{{ $message }}</span>
				@enderror
			</div>
		</div>
        <div class="form-group mt-1">
			<div class="input-group" wire:ignore>
                <label class="control-label col-12 col-lg-3 fw-bold text-muted">Buyer</label>
				<select class="form-control" wire:model="buyer_id" placeholder="" data-choices data-choices-sorting-false>
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
					<span wire:loading.remove wire:target="cancel">
						<i class="ri-close-line"> </i> Close
					</span>
					<div wire:loading wire:target="cancel">
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
                <button id="btnCreate" type="submit" class="btn btn-success w-lg">
                    <span wire:loading.remove wire:target="save">
                        <i class="ri-save-3-line"></i> Save
                    </span>
                    <div wire:loading wire:target="save">
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
                <button type="button" class="btn btn-success btn-print w-lg" wire:click="print">
                    <i class="bx bx-printer"></i> Print
                </button>
            </div>
        </div>		
    </form>        
	</div>
	<div class="col-lg-2"></div>
</div>
