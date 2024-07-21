<div class="row">
	{{-- @if (session()->has('message'))
		<div class="alert alert-success">
			{{ session('message') }}
		</div>
	@endif
	@if (session()->has('error'))
		<div class="alert alert-danger">
			{{ session('error') }}
		</div>
	@endif --}}
    <form wire:submit.prevent="save">
        <div class="row">
            <div class="col-lg-5 ms-3">
                <div class="form-group">
                    <label class="control-label col-md-6 col-xs-12">Tanggal LPK</label>
                    <div class="input-group col-md-9 col-xs-12">
                        <input class="form-control datepicker-input" type="date" wire:model.defer="lpk_date" placeholder="yyyy/mm/dd"/>
                        @error('lpk_date')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-6 col-xs-12">Nomor LPK</label>
                    <div class="input-group col-md-9 col-xs-12">
                        <input type="text" class="form-control"  wire:model="lpk_no" />
                        @error('lpk_no')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-6 col-xs-12">PO Number</label>
                    <div class="input-group col-md-9 col-xs-12">
                        <input type="text" class="form-control" wire:model.debounce.300ms="po_no"  placeholder="PO NUMBER" />
                        @error('po_no')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-6 col-xs-12">Nomor Order</label>
                    <div class="input-group col-md-9 col-xs-12">
                        <input type="text" class="form-control readonly" readonly="readonly" wire:model="no_order" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-6 col-xs-12">Nomor Mesin</label>
                    <div class="input-group col-md-9 col-xs-12">
                        <input type="text" class="form-control" wire:model.debounce.300ms="machineno" />
                        @error('machineno')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-6 col-xs-12">Jumlah LPK</label>
                    <div class="input-group col-md-9 col-xs-12">
                        <input type="text" class="form-control" wire:model="qty_lpk" />
                        <span class="input-group-text">                            
                        </span>
                        @error('qty_lpk')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-6 col-xs-12">Jumlah Gentan</label>
                    <div class="input-group col-md-9 col-xs-12">
                        <input type="text" class="form-control" wire:model="qty_gentan" />
                        <span class="input-group-text">
                            roll
                        </span>
                        @error('qty_gentan')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-6 col-xs-12">Meter Gulung</label>
                    <div class="input-group col-md-9 col-xs-12">
                        <input type="text" class="form-control" wire:model="qty_gulung" />
                        <span class="input-group-text">
                            meter
                        </span>
                        @error('qty_gulung')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-6 col-xs-12">Panjang LPK</label>
                    <div class="input-group col-md-9 col-xs-12">
                        <input type="text" class="form-control readonly" readonly="readonly" wire:model="panjang_lpk" />
                        <span class="input-group-text">
                            meter
                        </span>
                        @error('panjang_lpk')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class=form-group">
                    <label for="textarea">Catatan</label>
                    <textarea class="form-control" placeholder="Catatan" id="textarea" rows="2" wire:model="remark"></textarea>
                </div>
                       
            </div>

            <div class="col-lg-5 ms-4">
                <div class="form-group">
                    <label class="control-label col-md-6 col-xs-12">Tanggal Proses</label>
                    <input class="form-control datepicker-input" type="date" wire:model.defer="processdate" placeholder="yyyy/mm/dd"/>
                    @error('processdate')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label class="control-label col-md-6 col-xs-12">Tanggal PO</label>
                    <input class="form-control datepicker-input readonly" readonly="readonly" type="date" wire:model.defer="order_date" placeholder="yyyy/mm/dd"/>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-9 col-xs-12">Buyer</label>
                    <div class="input-group col-md-9 col-xs-12">
                        <input type="text" class="form-control readonly"  readonly="readonly" wire:model="buyer_name" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-9 col-xs-12">Nama Produk</label>
                    <div class="input-group col-md-9 col-xs-12">
                        <input type="text" class="form-control readonly" readonly="readonly" wire:model="product_name" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-9 col-xs-12">Nama Mesin</label>
                    <div class="input-group col-md-9 col-xs-12">
                        <input type="text" class="form-control readonly" readonly="readonly" wire:model="machinename" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-9 col-xs-12">Panjang Total</label>
                    <div class="input-group col-md-9 col-xs-12">
                        <input type="text" class="form-control readonly"  readonly="readonly" wire:model="total_assembly_line" />
                        <span class="input-group-text">
                            meter
                        </span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-9 col-xs-12">Dimensi (TxLxP)</label>
                    <div class="input-group col-md-9 col-xs-12">
                        <input type="text" class="form-control readonly" readonly="readonly" wire:model="dimensi" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-9 col-xs-12">Default Gulung</label>
                    <div class="input-group col-md-9 col-xs-12">
                        <input type="text" class="form-control readonly"  readonly="readonly" wire:model="defaultgulung" />
                        <span class="input-group-text" id="basic-addon2">
                            meter
                        </span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-9 col-xs-12">Selisih Kurang</label>
                    <div class="input-group col-md-9 col-xs-12">
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
                <button id="btnFilter" type="button" class="btn btn-warning" wire:click="cancel">
                    <i class="fa fa-back"></i> Close
                </button>

                <button id="btnFilter" type="button" class="btn btn-danger" wire:click="delete">
                    <i class="fa fa-trash"></i> Delete
                </button>

                <button id="btnCreate" type="submit" class="btn btn-success">
                    <i class="fa fa-plus"></i> Save
                </button>

                <button type="button" class="btn btn-success btn-print" disabled="disabled">
                    <i class="fa fa-print"></i> Print
                </button>
            </div>
        </div>
    </form>        
</div>