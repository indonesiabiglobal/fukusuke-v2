{{-- <title>Loss Seitai</title> --}}
<div class="container mt-4">
    <div class="row">
        @if (session()->has('message'))
            <div class="alert alert-success">
                {{ session('message') }}
            </div>
        @endif
        <div class="col-lg-6 mb-4">
            <div class="form-group">
                <label class="control-label col-md-3 col-xs-4">
                    <span class="hidden-xs" style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">Filter </span>Tanggal
                </label>
                <div class="input-group col-md-9 col-xs-8">
                    <div class="col-4 pe-1">
                        <select class="form-select mb-0" wire:model.defer="transaksi">
                            <option value="1">Proses</option>
                            <option value="2">Order</option>
                        </select>
                    </div>
                    <div class="col-8">
                        <div class="form-group">
                            <div class="input-group">
                                <input class="form-control datepicker-input" type="date" wire:model.defer="tglMasuk" placeholder="yyyy/mm/dd"/>
    
                                <input class="form-control datepicker-input" type="date" wire:model.defer="tglKeluar" placeholder="yyyy/mm/dd"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- <div class="form-group">
                <label class="control-label col-md-3 col-xs-4" resources="Search">Nomor LPK </label>
                <div class="input-group col-md-9 col-xs-8">
                    <input id='searchText' name='searchText' class="form-control" type="text" resources-placeholder="SearchTextOrCode" placeholder="search nomor PO, nama produk" />
                </div>
            </div> --}}
            <div class="form-group">
                <label class="control-label col-md-3 col-xs-4" resources="Search">Search</label>
                <div class="input-group col-md-9 col-xs-8">
                    <input id='searchText' name='searchText' class="form-control" type="text"  placeholder="search nomor PO, nama produk" />
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
            {{-- <input type="text" list="machineOptions" wire:model="machine_id" class="input-group col-md-9 col-xs-8" x-on:change.debounce="addressSelected($event)" >
            <datalist id="machineOptions">
                @foreach($machine as $result)
                    <option
                        wire:key="{{ $result->id }}"
                        data-value="{{ $result->id }}"
                        value="{{ $result->machinename }}"
                    ></option>
                @endforeach
            </datalist> --}}
            <div class="form-group">
                <label class="control-label col-md-3 col-xs-4">Mesin</label>
                <div class="input-group col-md-9 col-xs-8">
                    <select id='searchBuyer' name="searchBuyer" class="form-control" placeholder="- all -">
                        <option value="">- all -</option>
                        @foreach ($machine as $item)
                            <option value="{{ $item->id }}">{{ $item->machinename }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-3 col-xs-4">Status</label>
                <div class="input-group col-md-9 col-xs-8">
                    <select class="form-control" placeholder="- all -">
                        <option value="">- all -</option>
                        <option value="0">Belum LPK</option>
                        <option value="1">SUdah LPK</option>
                    </select>
                </div>
            </div>
        </div>
    
        <div class="col-lg-12" style="border-top:1px solid #efefef">
            <div class="toolbar">
                <button id="btnFilter" wire:click="search" type="button" class="btn btn-info" style="width:125px;">
                    <i class="fa fa-search"></i> Filter
                    <div wire:loading wire:target="search">
                        <span class="fa fa-spinner fa-spin"></span>
                    </div>
                </button>
            </div>
            <table class="table table-bordered" data-height="414" id="tableSrc"></table>
        </div>
    </div>
    <div class="card border-0 shadow mb-4 mt-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-centered table-nowrap mb-0 rounded">
                    <thead class="thead-light">
                        <tr>
                            <th class="border-0 rounded-start">Action</th>
                            <th class="border-0">Nomor LPK</th>
                            <th class="border-0">Jumlah LPK</th>
                            <th class="border-0">Jumlah Produksi</th>
                            <th class="border-0">Loss Seitai</th>
                            <th class="border-0">Nomor Order</th>
                            <th class="border-0 rounded-end">Mesin</th>
                            <th class="border-0">Tanggal Produksi</th>
                            <th class="border-0">Tanggal Proses</th>
                            <th class="border-0 rounded-end">Shift</th>
                            <th class="border-0 rounded-end">No Palet</th>
                            <th class="border-0 rounded-end">Seq</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Item -->
                        @foreach ($loss as $item)
                        <tr>
                            <td>
                                <a href="{{ route('edit-seitai', ['orderId' => $item->id]) }}" class="btn btn-info">
                                    <i class="fa fa-edit"></i> Edit
                                </a>
                            </td>
                            <td>                                
                                {{ $item->lpk_no }}
                            </td>
                            <td>
                                {{ $item->qty_lpk }}
                            </td>
                            <td>
                                {{ $item->qty_produksi }}
                            </td>
                            <td>
                                
                            </td>
                            <td>
                                {{ $item->order_id }}
                            </td>
                            <td>
                                {{ $item->machine_id }}
                            </td>
                            <td>
                                {{ $item->production_date }}
                            </td>
                            <td>
                                
                            </td>
                            <td>
                                {{ $item->work_hour }}
                            </td>
                            <td>
                                {{ $item->work_shift }}
                            </td>
                            <td>
                                {{ $item->seq_no }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
