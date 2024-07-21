<div class="row">	
    <form wire:submit.prevent="save">
        <div class="row">
            <div class="col-lg-5 ms-3">
                <div class="form-group">
                    <label class="control-label col-md-6 col-xs-12">Tanggal LPK</label>
                    <div class="input-group col-md-9 col-xs-12">
                        <input data-datepicker="" class="form-control datepicker-input readonly" readonly="readonly" id="lpk_date" type="text" placeholder="yyyy/mm/dd" wire:model="lpk_date">
                        <span class="input-group-text">
                            <svg class="icon icon-xs" fill="currentColor"
                            viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd"
                                d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                                clip-rule="evenodd"></path>
                            </svg>
                        </span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-6 col-xs-12">Nomor LPK</label>
                    <div class="input-group col-md-9 col-xs-12">
                        <input type="text" id="lpk_no" class="form-control"  wire:model="lpk_no" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-6 col-xs-12">PO Number</label>
                    <div class="input-group col-md-9 col-xs-12">
                        <input type="text" id="prev_lpk_no" class="form-control readonly" readonly="readonly" wire:model="prev_lpk_no" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-6 col-xs-12">Nomor Order</label>
                    <div class="input-group col-md-9 col-xs-12">
                        <input type="text" id="order_id" class="form-control readonly" readonly="readonly"  wire:model="order_id" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-6 col-xs-12">Nomor Mesin</label>
                    <div class="input-group col-md-9 col-xs-12">
                        <input type="text" id="prev_machine_no" class="form-control" wire:model="prev_machine_no" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-6 col-xs-12">Jumlah LPK</label>
                    <div class="input-group col-md-9 col-xs-12">
                        <input type="text" id="order_qty" class="form-control" wire:model="order_qty" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-6 col-xs-12">Jumlah Gentan</label>
                    <div class="input-group col-md-9 col-xs-12">
                        <input type="text" id="qty_gentan" class="form-control" wire:model="qty_gentan" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-6 col-xs-12">Meter Gulung</label>
                    <div class="input-group col-md-9 col-xs-12">
                        <input type="text" id="qty_gulung" class="form-control" wire:model="qty_gulung" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-6 col-xs-12">Panjang LPK</label>
                    <div class="input-group col-md-9 col-xs-12">
                        <input type="text" id="panjang_lpk" class="form-control readonly" readonly="readonly" wire:model="panjang_lpk" />
                    </div>
                </div>
                       
            </div>

            <div class="col-lg-5 ms-4">
                <div class="form-group">
                    <label class="control-label col-md-6 col-xs-12">Tanggal Proses</label>
                    <div class="input-group col-md-9 col-xs-12">
                        <input data-datepicker="" class="form-control datepicker-input" id="lpk_date" type="text" placeholder="yyyy/mm/dd" wire:model="lpk_date">
                        <span class="input-group-text">
                            <svg class="icon icon-xs" fill="currentColor"
                            viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd"
                                d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                                clip-rule="evenodd"></path>
                            </svg>
                        </span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-6 col-xs-12">Tanggal PO</label>
                    <div class="input-group col-md-9 col-xs-12">
                        <input data-datepicker="" class="form-control datepicker-input" id="lpk_date" type="text" placeholder="yyyy/mm/dd" wire:model="lpk_date">
                        <span class="input-group-text">
                            <svg class="icon icon-xs" fill="currentColor"
                            viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd"
                                d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                                clip-rule="evenodd"></path>
                            </svg>
                        </span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-9 col-xs-12">Buyer</label>
                    <div class="input-group col-md-9 col-xs-12">
                        <input type="text" id="buyer_id" class="form-control readonly"  readonly="readonly" wire:model="buyer_id" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-9 col-xs-12">Nama Produk</label>
                    <div class="input-group col-md-9 col-xs-12">
                        <input type="text" id="product_id" class="form-control readonly"  readonly="readonly" wire:model="product_id" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-9 col-xs-12">Nama Mesin</label>
                    <div class="input-group col-md-9 col-xs-12">
                        <input type="text" id="machine_id" class="form-control readonly" readonly="readonly" wire:model="machine_id" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-9 col-xs-12">Panjang Total</label>
                    <div class="input-group col-md-9 col-xs-12">
                        <input type="text" id="qty_lpk" class="form-control readonly"  readonly="readonly" wire:model="qty_lpk" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-9 col-xs-12">Dimensi (TxLxP)</label>
                    <div class="input-group col-md-9 col-xs-12">
                        <input type="text" id="dimensi" class="form-control readonly"  readonly="readonly" wire:model="dimensi" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-9 col-xs-12">Default Gulung</label>
                    <div class="input-group col-md-9 col-xs-12">
                        <input type="text" id="default_gulung" class="form-control readonly"  readonly="readonly" wire:model="default_gulung" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-9 col-xs-12">Selisih Kurang</label>
                    <div class="input-group col-md-9 col-xs-12">
                        <input type="text" id="selisih_kurang" class="form-control readonly"  readonly="readonly" wire:model="selisih_kurang" />
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
                    <i class="fa fa-plus"></i> Update
                </button>

                <button type="button" class="btn btn-success btn-print" disabled="disabled">
                    <i class="fa fa-print"></i> Print
                </button>
            </div>
        </div>
    </form>        

</div>
<input name="lpk_id" type="hidden" value="" />
<input id="searchLpkNo_selected" type="hidden" value="" />