<div class="row">
    <form wire:submit.prevent="save">
        <div class="row">
            <div class="col-lg-6">
                <div class="form-group">
                    <div class="input-group">
                        <label class="control-label col-12 col-lg-3 fw-bold text-muted">Tanggal LPK</label>
                        <input wire:model.defer="lpk_date" type="text" class="form-control @error('lpk_date') is-invalid @enderror" style="padding:0.44rem" data-provider="flatpickr" data-date-format="d/m/Y">
                        <span class="input-group-text py-0">
                            <i class="ri-calendar-event-fill fs-4"></i>
                        </span>
                        @error('lpk_date')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="form-group mt-1">
                    <div class="input-group">
                        <label class="control-label col-12 col-lg-3 fw-bold text-muted">Nomor LPK</label>
                        <input type="text" class="form-control @error('lpk_no') is-invalid @enderror" wire:model="lpk_no" />
                        @error('lpk_no')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="form-group mt-1">
                    <div class="input-group">
                        <label class="control-label col-12 col-lg-3 fw-bold text-muted">PO Number</label>
                        <input type="text" class="form-control @error('po_no') is-invalid @enderror" wire:model.live.debounce.300ms="po_no"  placeholder="PO NUMBER" />
                        @error('po_no')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="form-group mt-1">
                    <div class="input-group">
                        <label class="control-label col-12 col-lg-3 fw-bold text-muted">Nomor Order</label>
                        <input type="text" class="form-control readonly" readonly="readonly" wire:model="no_order" />
                    </div>
                </div>
                <div class="form-group mt-1">
                    <div class="input-group">
                        <label class="control-label col-12 col-lg-3 fw-bold text-muted">Nomor Mesin</label>
                        <input type="text" class="form-control @error('machineno') is-invalid @enderror" wire:model.live.debounce.300ms="machineno" />
                        @error('machineno')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="form-group mt-1">
                    <div class="input-group">
                        <label class="control-label col-12 col-lg-3 fw-bold text-muted">Jumlah LPK</label>
                        <input type="text" class="form-control @error('qty_lpk') is-invalid @enderror" wire:model="qty_lpk" />
                        <span class="input-group-text">
                            Lembar                            
                        </span>
                        @error('qty_lpk')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="form-group mt-1">
                    <div class="input-group">
                        <label class="control-label col-12 col-lg-3 fw-bold text-muted">Jumlah Gentan</label>
                        <input type="text" class="form-control @error('qty_gentan') is-invalid @enderror" wire:model="qty_gentan" />
                        <span class="input-group-text">
                            roll
                        </span>
                        @error('qty_gentan')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="form-group mt-1">
                    <div class="input-group">
                        <label class="control-label col-12 col-lg-3 fw-bold text-muted">Meter Gulung</label>
                        <input type="text" class="form-control @error('qty_gulung') is-invalid @enderror" wire:model="qty_gulung" />
                        <span class="input-group-text">
                            meter
                        </span>
                        @error('qty_gulung')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="form-group mt-1">
                    <div class="input-group">
                        <label class="control-label col-12 col-lg-3 fw-bold text-muted">Panjang LPK</label>
                        <input type="text" class="form-control readonly @error('panjang_lpk') is-invalid @enderror" readonly="readonly" wire:model="panjang_lpk" />
                        <span class="input-group-text">
                            meter
                        </span>
                        @error('panjang_lpk')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="form-group mt-1">
                    <label for="textarea" class="control-label col-12 col-lg-3 fw-bold text-muted">Catatan</label>
                    <textarea class="form-control" placeholder="Catatan" id="textarea" rows="2" wire:model="remark"></textarea>
                </div>
                       
            </div>

            <div class="col-lg-6">
                <div class="form-group">
                    <div class="input-group">
                        <label class="control-label col-12 col-lg-3 fw-bold text-muted">Tanggal Proses</label>                        
                        <input wire:model.defer="processdate" type="date" class="form-control datepicker-input @error('processdate') is-invalid @enderror" placeholder="yyyy/mm/dd" style="padding:0.44rem" data-provider="flatpickr" data-date-format="d/m/Y"/>
                        <span class="input-group-text py-0">
                            <i class="ri-calendar-event-fill fs-4"></i>
                        </span>
                        @error('processdate')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                
                <div class="form-group mt-1">
                    <div class="input-group">
                        <label class="control-label col-12 col-lg-3 fw-bold text-muted">Tanggal PO</label>
                        <input class="form-control datepicker-input readonly" readonly="readonly" type="date" wire:model.defer="order_date" placeholder="yyyy/mm/dd"/>
                    </div>
                </div>
                <div class="form-group mt-1">
                    <div class="input-group">
                        <label class="control-label col-12 col-lg-3 fw-bold text-muted">Buyer</label>
                        <input type="text" class="form-control readonly"  readonly="readonly" wire:model="buyer_name" />
                    </div>
                </div>
                <div class="form-group mt-1">
                    <div class="input-group">
                        <label class="control-label col-12 col-lg-3 fw-bold text-muted">Nama Produk</label>
                        <input type="text" class="form-control readonly" readonly="readonly" wire:model="product_name" />
                    </div>
                </div>
                <div class="form-group mt-1">
                    <div class="input-group">                        
                        <label class="control-label col-12 col-lg-3 fw-bold text-muted">Nama Mesin</label>
                        <input type="text" class="form-control readonly" readonly="readonly" wire:model="machinename" />
                    </div>
                </div>
                <div class="form-group mt-1">
                    <div class="input-group">
                        <label class="control-label col-12 col-lg-3 fw-bold text-muted">Panjang Total</label>
                        <input type="text" class="form-control readonly"  readonly="readonly" wire:model="total_assembly_line" />
                        <span class="input-group-text">
                            meter
                        </span>
                    </div>
                </div>
                <div class="form-group mt-1">
                    <div class="input-group">
                        <label class="control-label col-12 col-lg-3 fw-bold text-muted">Dimensi (TxLxP)</label>
                        <input type="text" class="form-control readonly" readonly="readonly" wire:model="dimensi" />
                    </div>
                </div>
                <div class="form-group mt-1">
                    <div class="input-group">
                        <label class="control-label col-12 col-lg-3 fw-bold text-muted">Default Gulung</label>
                        <input type="text" class="form-control readonly"  readonly="readonly" wire:model="defaultgulung" />
                        <span class="input-group-text" id="basic-addon2">
                            meter
                        </span>
                    </div>
                </div>
                <div class="form-group mt-1">
                    <div class="input-group">
                        <label class="control-label col-12 col-lg-3 fw-bold text-muted">Selisih Kurang</label>
                        <input type="text" class="form-control readonly"  readonly="readonly" wire:model="selisihkurang" />
                        <span class="input-group-text">
                            meter
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <hr/>
        <div class="col-lg-12" style="border-top:1px solid #efefef">
            <div class="toolbar">
                <button type="button" class="btn btn-warning" wire:click="cancel">
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

                <button type="button" class="btn btn-danger" wire:click="delete">
                    <i class="ri-delete-bin-line"></i> Delete
                </button>

                <button type="submit" class="btn btn-success w-lg">
                    <span wire:loading.remove wire:target="save">
                        <i class="ri-save-3-line"></i> Update
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

                <button type="button" class="btn btn-success btn-print" disabled="disabled">
                    <i class="bx bx-printer"></i> Print
                </button>
            </div>
        </div>
    </form>        
</div>