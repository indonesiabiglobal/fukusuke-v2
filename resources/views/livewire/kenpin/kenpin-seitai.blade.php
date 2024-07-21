{{-- <title>Kenpin Seitai</title> --}}
<div class="container mt-2">
    <div class="row">
        <div class="col-lg-6">
            <div class="form-group">
                <label class="control-label col-12">
                    <span class="hidden-xs">Tanggal </span>Kenpin
                </label>
                <div class="input-group col-md-9 col-xs-8">
                    <div class="col-12">
                        <div class="form-group">
                            <div class="input-group">
                                <input class="form-control datepicker-input" type="date" wire:model.defer="tglMasuk" placeholder="yyyy/mm/dd"/>
    
                                <input class="form-control datepicker-input" type="date" wire:model.defer="tglKeluar" placeholder="yyyy/mm/dd"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-3 col-xs-4">Search </label>
                <div class="input-group col-md-9 col-xs-8">
                    <input wire:model.defer="searchTerm" class="form-control" type="text" placeholder="search nomor PO, nama produk" />
                </div>
            </div>
        </div>
    
        <div class="col-lg-6">
            {{-- <div class="form-group">
                <label class="control-label col-md-3 col-xs-4" resources="OrgBranch">Produk</label>
                <div class="input-group col-md-9 col-xs-8">
                    <select id='searchProd' name="searchProd" class="js-states form-control" placeholder="- all -"></select>
                </div>
            </div> --}}
            <div class="form-group">
                <label class="control-label col-md-3 col-xs-4">Nomor Palet</label>
                <div class="input-group col-md-9 col-xs-8">
                    <input type="text" class="form-control" placeholder="A0000-000000" wire:model.defer="nomor_palet">
                    <span class="input-group-text readonly" readonly="readonly">
                        NO. LOT
                    </span>
                    <input wire:model.defer="nomor_lot" type="text" class="form-control" placeholder="---">
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-3 col-xs-4">Status</label>
                <div class="input-group col-md-9 col-xs-8">
                    <select wire:model.defer="status" class="form-control" placeholder="- all -">
                        <option value="">- all -</option>
                        <option value="1">Proses</option>
                        <option value="2">Finish</option>
                    </select>
                </div>
            </div>
        </div>
    
        <div class="col-lg-12 mt-3" style="border-top:1px solid #efefef">
            <div class="toolbar">
                <button wire:click="search" type="button" class="btn btn-info" style="width:125px;">
                    <i class="fa fa-search"></i> Filter
                    <div wire:loading wire:target="search">
                        <span class="fa fa-spinner fa-spin"></span>
                    </div>
                </button>
                <button 
                    id="btnCreate" 
                    type="button" 
                    class="btn btn-success" 
                    style="width:125px;"
                    onclick="window.location.href='{{ route('add-kenpin-seitai') }}'">
                    <i class="fa fa-plus"></i> Add
                </button>
                {{-- <button id="btnCreate" type="button" class="btn btn-success" style="width:125px;">
                    <i class="fa fa-plus"></i> Add
                </button> --}}
            </div>
        </div>
    </div>
    <div class="card border-0 shadow mb-4 mt-3">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-centered table-nowrap mb-0 rounded">
                    <thead class="thead-light">
                        <tr>
                            <th class="border-0 rounded-start">Action</th>
                            <th class="border-0">Tgl. Kenpin</th>
                            <th class="border-0">No Kenpin</th>
                            <th class="border-0">Nama Produk</th>
                            <th class="border-0">No. Order</th>
                            <th class="border-0">Petugas</th>
                            <th class="border-0">Jumlah Loss (lbr)</th>
                            <th class="border-0">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Item -->
                        @foreach ($data as $item)
                        <tr>
                            <td>
                                <a href="{{ route('edit-order', ['orderId' => $item->id]) }}" class="btn btn-info">
                                    <i class="fa fa-edit"></i> Edit
                                </a>
                            </td>
                            <td>                                
                                {{ $item->kenpin_date }}
                            </td>
                            <td>
                                {{ $item->kenpin_no }}
                            </td>
                            <td>
                                {{ $item->namaproduk }}
                            </td>
                            <td>
                                {{ $item->code }}
                            </td>
                            <td>
                                {{ $item->namapetugas }}
                            </td>
                            <td>
                                {{ $item->qty_loss }}
                            </td>
                            <td>
                                {{ $item->status_kenpin }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
