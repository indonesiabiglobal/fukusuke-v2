{{-- <title>Order Entry</title> --}}
<div class="container mt-2">
    <div class="row">
        <div class="col-lg-6 mb-3">
            <div class="form-group">
                <label class="control-label col-md-3 col-xs-4">Nomor Palet </label>
                <div class="input-group col-md-9 col-xs-8">
                    <input wire:model.defer="nomor_palet" class="form-control" type="text" placeholder="A0000-000000" />
                </div>
            </div>
        </div>    
        <div class="col-lg-6">
            <div class="form-group">
                <label class="control-label col-md-3 col-xs-4">Produk</label>
                <div class="input-group col-md-9 col-xs-8">
                    <select wire:model.defer="product_id" class="form-control" placeholder="- all -">
                        <option value="">- all -</option>
                        @foreach ($product as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>            
        </div>
    
        <div class="col-lg-12" style="border-top:1px solid #efefef">
            <div class="toolbar">
                <button wire:click="search" type="button" class="btn btn-info" style="width:125px;">
                    <i class="fa fa-search"></i> Filter
                    <div wire:loading wire:target="search">
                        <span class="fa fa-spinner fa-spin"></span>
                    </div>
                </button>
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
                            <th class="border-0">No Palet</th>
                            <th class="border-0">No Order</th>
                            <th class="border-0">Nama Produk</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Item -->
                        @forelse ($data as $item)
                        <tr>
                            <td>
                                <input type="checkbox" wire:model="selectedItems" value="{{ $item->product_id }}">
                            </td>
                            <td>                                
                                {{ $item->nomor_palet }}
                            </td>
                            <td>
                                {{ $item->code }}
                            </td>
                            <td>
                                {{ $item->name }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center">No results found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-12 text-end">
        <button type="button" class="btn btn-success">
            <i class="fa fa-plus"></i> Proses Retur
        </button>
    </div>
</div>
