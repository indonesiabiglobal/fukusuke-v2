<div class="row">
    <form wire:submit.prevent="save">
        <div class="row mt-2">
            <div class="col-4 col-lg-12">
                <div class="row">
                    <div class="col-12 col-lg-6">
                        <div class="form-group">                            
                            <div class="input-group">
                                <label class="control-label pe-2">Tanggal Produksi</label>
                                <input class="form-control datepicker-input" type="date" wire:model.defer="production_date" placeholder="yyyy/mm/dd"/>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-6">
                        <div class="form-group">                            
                            <div class="input-group">
                                <label class="control-label pe-2">Tanggal Proses</label>
                                <input class="form-control datepicker-input" type="date" wire:model.defer="created_on" placeholder="yyyy/mm/dd"/>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-4 mt-1">
                        <div class="form-group">
                            <div class="input-group">
                                <label class="control-label col-5 pe-2">Nomor LPK</label>
                                <input type="text" class="form-control"  wire:model="lpk_no" />
                                @error('lpk_no')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-4 mt-1">
                        <div class="form-group">
                            <div class="input-group">
                                <label class="control-label pe-2">Tanggal LPK</label>
                                <input class="form-control readonly datepicker-input" readonly="readonly" type="date" wire:model.defer="lpk_date" placeholder="yyyy/mm/dd"/>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-4 mt-1">
                        <div class="form-group">                            
                            <div class="input-group">
                                <label class="control-label pe-2">Jumlah LPK</label>
                                <input type="text" placeholder="-" class="form-control readonly" readonly="readonly" wire:model="qty_lpk" />
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-4 mt-1">
                        <div class="form-group">
                            <div class="input-group">
                                <label class="control-label col-5 pe-2">Nomor Order</label>
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
                                <label class="control-label col-5 pe-2">Nomor Mesin</label>
                                <input type="text" placeholder=" ... " class="form-control" wire:model.debounce.300ms="machineno" />
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-8 mt-1">
                        <div class="form-group">                            
                            <div class="input-group">
                                <label class="control-label"></label>
                                <input type="text" placeholder="-" class="form-control readonly" readonly="readonly" wire:model="machinename" />
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-4 mt-1">
                        <div class="form-group">
                            <div class="input-group">
                                <label class="control-label col-5 pe-2">Petugas</label>
                                <input type="text" placeholder=" ... " class="form-control" wire:model="employeeno" />
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
                    <div class="col-12 col-lg-4 mt-1">
                        <div class="form-group">
                            <div class="input-group">
                                <label class="control-label col-5">Jumlah Produksi</label>
                                <input type="text" placeholder="-" class="form-control" wire:model="qty_produksi" />
                                <span class="input-group-text">
                                    mm
                                </span>
                            </div>                            
                        </div>
                    </div>
                    <div class="col-12 col-lg-5 mt-1">
                        <div class="form-group">                            
                            <div class="input-group">
                                <label class="control-label col-5">Total Produksi</label>
                                <input type="text" placeholder="-" class="form-control readonly" readonly="readonly" wire:model="total_produksi" />
                                <span class="input-group-text">
                                    lbr
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-3 mt-1">
                        <div class="form-group">                            
                            <div class="input-group">
                                <label class="control-label col-3">Selisih</label>
                                <input type="text" class="form-control readonly" readonly="readonly" wire:model="selisih" />
                                <span class="input-group-text">
                                    lbr
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-4 mt-1">
                        <div class="form-group">
                            <div class="input-group">
                                <label class="control-label col-5">Nomor Palet</label>
                                <input type="text" placeholder="A0000-000000" class="form-control" wire:model="nomor_palet" />
                                @error('nomor_palet')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>                            
                        </div>
                    </div>
                    <div class="col-12 col-lg-8 mt-1">
                        <div class="form-group">
                            <div class="input-group">
                                <label class="control-label col-3">Nomor LOT</label>
                                <input type="text" placeholder="----------" class="form-control" wire:model="nomor_lot" />

                                <input type="text" class="form-control readonly" readonly="readonly" wire:model="selisih" />
                                @error('nomor_lot')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-12 col-lg-4 mt-1">
                        <div class="form-group">
                            <div class="input-group">
                                <label class="control-label col-5">Loss Infure</label>
                                <input type="text" class="form-control"  wire:model="infure_berat_loss" />
                                <span class="input-group-text">
                                    kg
                                </span>
                            </div>                            
                        </div>
                    </div>
                    <div class="col-12 col-lg-8 mt-1">
                        <div class="form-group">
                            <div class="input-group">
                                <label class="control-label col-3">Petugas Infure</label>
                                <input type="text" placeholder="..." class="form-control" wire:model="employeenoinfure" />

                                <input type="text" placeholder="-" class="form-control readonly" readonly="readonly" wire:model="empnameinfure" />
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-4 mt-1">
                        <div class="form-group">
                            <div class="input-group">
                                <label class="control-label col-5 pe-2">Jam Produksi</label>
                                <input class="form-control" wire:model="work_hour" type="time" placeholder="hh:mm">
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-8 mt-1">
                        <div class="form-group">                            
                            <div class="input-group">
                                <label class="control-label col-2">Shift Kerja</label>
                                <input type="text" class="form-control readonly" readonly="readonly" wire:model="work_shift" />
                            </div>
                        </div>
                    </div>                    
                </div>
            </div>
        </div>
        <hr/>
        <div class="row">
            <div class="col-lg-8">
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="periode1SP-tab" data-bs-toggle="tab" data-bs-target="#periode1SP" type="button" role="tab" aria-controls="periode1SP" aria-selected="true">Gentan(s)</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="periode2SP-tab" data-bs-toggle="tab" data-bs-target="#periode2SP" type="button" role="tab" aria-controls="periode2SP" aria-selected="false">Loss(s)</button>
                    </li>
                </ul>
            </div>
            <div class="col-lg-4" style="border-top:1px solid #efefef">
                <div class="toolbar">
                    <button id="btnFilter" type="button" class="btn btn-warning" wire:click="cancel">
                        <i class="fa fa-back"></i> Close
                    </button>
                    <button id="btnCreate" type="submit" class="btn btn-success">
                        <i class="fa fa-plus"></i> Save
                    </button>
                    <button type="button" class="btn btn-success btn-print" disabled="disabled">
                        <i class="fa fa-print"></i> Print
                    </button>
                </div>
            </div>
        </div>
        
        
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="periode1SP" role="tabpanel" aria-labelledby="periode1SP-tab">
                <div class="row justify-content-start">
                    <div class="row mt-3">
                        <div class="col-lg-8">
                            <button wire:click="addGentan" type="button" class="btn btn-warning">
                                <i class="fa fa-plus"></i> Add Gentan
                            </button>
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
                                            <th class="border-0">Line</th>
                                            <th class="border-0">No Mesin</th>
                                            <th class="border-0">Shift</th>
                                            <th class="border-0">Petugas</th>
                                            <th class="border-0">Tg. Produksi</th>
                                            <th class="border-0 rounded-end">Berat Produksi (kg)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $total=0
                                        @endphp
                                        @forelse ($detailsGentan as $item)
                                            <tr>
                                                <td>
                                                    <button type="button" class="btn btn-danger" wire:click="deleteGentan({{$item->id}})">
                                                        <i class="fa fa-trash"></i> Delete
                                                    </button>
                                                </td>
                                                <td>                                
                                                    {{ $item->gentan_no }}
                                                </td>
                                                <td>
                                                    {{ $item->gentan_line }}
                                                </td>
                                                <td>
                                                    {{ $item->machineno }}
                                                </td>
                                                <td>
                                                    {{ $item->work_shift }}
                                                </td>
                                                <td>
                                                    {{ $item->empname }}
                                                </td>
                                                <td>
                                                    {{ $item->production_date }}
                                                </td>
                                                <td>
                                                    {{ $item->berat_produksi }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center">No results found</td>
                                            </tr>
                                        @endforelse
                                        <tr>
                                            <td colspan="7" class="text-end">Berat Total (kg):</td>
                                            <td colspan="1" class="text-center">0</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="periode2SP" role="tabpanel" aria-labelledby="periode2SP-tab">
                <div class="row justify-content-start">
                    <div class="row mt-3">
                        <div class="col-lg-8">
                            <button wire:click="addLoss" type="button" class="btn btn-success">
                                <i class="fa fa-plus"></i> Add Loss Seitai
                            </button>
                        </div>
                    </div>
                    
                    <div class="card border-0 shadow mb-4 mt-4">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-centered table-nowrap mb-0 rounded">
                                    <thead class="thead-light">
                                        <tr>
                                            <th class="border-0 rounded-start">Action</th>
                                            <th class="border-0">Kode</th>
                                            <th class="border-0">Nama Loss</th>
                                            <th class="border-0 rounded-end">Berat (kg)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $total=0
                                        @endphp
                                        @forelse ($detailsLoss as $item)
                                            <tr>
                                                <td>
                                                    <button type="button" class="btn btn-danger" wire:click="deleteLoss({{$item->id}})">
                                                        <i class="fa fa-trash"></i> Delete
                                                    </button>
                                                </td>
                                                <td>                                
                                                    {{ $item->code }}
                                                </td>
                                                <td>
                                                    {{ $item->name }}
                                                </td>
                                                <td>
                                                    {{ $item->berat_loss }}
                                                </td>
                                            </tr>
                                        @empty
                                        <tr>
                                            <td colspan="4" class="text-center">No results found</td>
                                        </tr>
                                        @endforelse
                                        
                                        <tr>
                                            <td colspan="3" class="text-end">Berat Loss Total (kg):</td>
                                            <td colspan="1" class="text-center">0</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modal-gentan" tabindex="-1" role="dialog" aria-labelledby="modal-gentan" aria-hidden="true" wire:ignore.self>
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
                                            @error('gentan_no')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12 mb-1">
                                    <div class="form-group">
                                        <label>Nomor Mesin </label>
                                        <div class="input-group col-md-9 col-xs-8">
                                            <input class="form-control readonly" readonly="readonly" type="text" wire:model.defer="machine_no" placeholder="..." />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12 mb-1">
                                    <div class="form-group">
                                        <label>Petugas </label>
                                        <div class="input-group col-md-9 col-xs-8">
                                            <input class="form-control readonly" readonly="readonly" type="text" wire:model.defer="petugas" placeholder="..." />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12 mb-1">
                                    <div class="form-group">
                                        <label>Berat Produksi</label>
                                        <div class="input-group col-md-9 col-xs-8">
                                            <input class="form-control readonly" readonly="readonly" type="text" wire:model.defer="berat_produksi" placeholder="..." />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12 mb-1">
                                    <div class="form-group">
                                        <label>Line Gentan </label>
                                        <div class="input-group col-md-9 col-xs-8">
                                            <input class="form-control" type="text" wire:model.defer="gentan_line" placeholder="0" />
                                            @error('gentan_line')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-link text-gray-600 ms-auto" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-success" wire:click="saveGentan">
                                Save
                            </button>
                        </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modal-loss" tabindex="-1" role="dialog" aria-labelledby="modal-loss" aria-hidden="true" wire:ignore.self>
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="h6 modal-title">Add Loss Seitai</h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-lg-12 mb-1">
                                    <div class="form-group">
                                        <label>Kode Loss </label>
                                        <div class="input-group col-md-9 col-xs-8">
                                            <input class="form-control" type="text" wire:model="loss_seitai_id" placeholder="..." />
                                            @error('loss_seitai_id')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12 mb-1">
                                    <div class="form-group">
                                        <label>Nama Loss </label>
                                        <div class="input-group col-md-9 col-xs-8">
                                            <input class="form-control readonly" readonly="readonly" type="text" wire:model.defer="namaloss" placeholder="..." />
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
                            <button type="button" class="btn btn-link text-gray-600 ms-auto" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-success" wire:click="saveLoss">
                                Save
                            </button>
                        </div>
                </div>
            </div>
        </div>
    </form>        

</div>
<script>
    document.addEventListener('livewire:load', function () {
        Livewire.on('showModalGentan', () => {
            $('#modal-gentan').modal('show');
        });
        Livewire.on('closeModalGentan', () => {
            $('#modal-gentan').modal('hide');
        });

        Livewire.on('showModalLoss', () => {
            $('#modal-loss').modal('show');
        });
        Livewire.on('closeModalLoss', () => {
            $('#modal-loss').modal('hide');
        });
    });
</script>