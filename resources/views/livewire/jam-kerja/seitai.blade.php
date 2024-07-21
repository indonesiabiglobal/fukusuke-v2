{{-- <title>Order Entry</title> --}}
<div class="container">
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="form-group">
                <label class="control-label col-md-3 col-xs-4" resources="DatePeriod"><span class="hidden-xs" style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">Filter </span>Tanggal</label>
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
                <label class="control-label col-md-3 col-xs-4" resources="Search">Search</label>
                <div class="input-group col-md-9 col-xs-8">
                    <input id='searchText' name='searchText' class="form-control" type="text"  placeholder="search nomor PO, nama produk" />
                </div>
            </div>
        </div>
    
        <div class="col-lg-6">
            <div class="form-group">
                <label class="control-label col-md-3 col-xs-4">Mesin</label>
                <div class="input-group col-md-9 col-xs-8">
                    <select class="form-control" placeholder="- all -">
                        <option value="">- all -</option>
                        @foreach ($machine as $item)
                            <option value="{{ $item->id }}">{{ $item->machinename }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-3 col-xs-4">Shift</label>
                <div class="input-group col-md-9 col-xs-8">
                    <select class="form-control" placeholder="- all -"></select>
                </div>
            </div>
        </div>
    
        <div class="col-lg-12" style="border-top:1px solid #efefef">
            <div class="toolbar">
                <button id="btnFilter" wire:click="search" type="button" class="btn btn-info" style="width:125px;">
                    <i class="fa fa-search"></i> Filter
                </button>
                
                <button id="btnCreate" type="button" data-bs-toggle="modal" data-bs-target="#modal-add" class="btn btn-success" style="width:125px;" asp-app-role="write">
                    <i class="fa fa-plus"></i> Add
                </button>
            </div>
            <table class="table table-bordered" data-height="414" id="tableSrc"></table>
        </div>
        <div wire:ignore.self class="modal fade" id="modal-add" tabindex="-1" role="dialog" aria-labelledby="modal-add" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="h6 modal-title">Add Jam Kerja Seitai</h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-12 mb-1">
                                <label for="">Tanggal</label>
                                <div class="form-group" style="margin-left:1px; white-space:nowrap">
                                    <div class="input-group">
                                        <input class="form-control datepicker-input" type="date" wire:model.defer="working_date" placeholder="yyyy/mm/dd"/>
                                        @error('working_date')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12 mb-1">
                                <div class="form-group">
                                    <label>Shift </label>
                                    <div class="input-group col-md-9 col-xs-8">
                                        <input class="form-control" type="text" wire:model.defer="work_shift" placeholder="..." />
                                        @error('work_shift')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12 mb-1">
                                <div class="form-group">
                                    <label>Nomor Mesin </label>
                                    <div class="input-group">
                                        <input class="form-control" type="text" wire:model="machineno" placeholder="..." />
                                        <input class="form-control readonly" readonly="readonly" type="text" wire:model="machinename" placeholder="..." />
                                        @error('machineno')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12 mb-1">
                                <div class="form-group">
                                    <label>Petugas </label>
                                    <div class="input-group col-md-9 col-xs-8">
                                        <input class="form-control" wire:model="employeeno" type="text" placeholder="..." />
                                        <input class="form-control readonly" readonly="readonly" type="text" wire:model="empname" placeholder="..." />
                                        @error('employeeno')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12 mb-1">
                                <label for="">Jam Kerja</label>
                                <div class="form-group" style="margin-left:1px; white-space:nowrap">
                                    <input class="form-control" wire:model="work_hour" type="time" placeholder="hh:mm">
                                    @error('work_hour')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-12 mb-1">
                                <label for="">Lama Mesin Mati</label>
                                <div class="form-group" style="margin-left:1px; white-space:nowrap">
                                    <input class="form-control" wire:model="on_hour" type="time" placeholder="hh:mm">
                                    @error('on_hour')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        {{-- <button type="button" class="btn btn-secondary">Accept</button> --}}
                        <button type="button" class="btn btn-link text-gray-600 ms-auto" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-success">
                            Save
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div wire:ignore.self class="modal fade" id="modal-edit" tabindex="-1" role="dialog" aria-labelledby="modal-edit" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="h6 modal-title">Edit Jam Kerja Seitai</h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-12 mb-1">
                                <label for="">Tanggal</label>
                                <div class="form-group" style="margin-left:1px; white-space:nowrap">
                                    <div class="input-group">
                                        <input class="form-control datepicker-input" type="date" wire:model.defer="working_date" placeholder="yyyy/mm/dd"/>
                                        @error('working_date')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12 mb-1">
                                <div class="form-group">
                                    <label>Shift </label>
                                    <div class="input-group col-md-9 col-xs-8">
                                        <input class="form-control" type="text" wire:model.defer="work_shift" placeholder="..." />
                                        @error('work_shift')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12 mb-1">
                                <div class="form-group">
                                    <label>Nomor Mesin </label>
                                    <div class="input-group">
                                        <input class="form-control" type="text" wire:model="machineno" placeholder="..." />
                                        <input class="form-control readonly" readonly="readonly" type="text" wire:model="machinename" placeholder="..." />
                                        @error('machineno')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12 mb-1">
                                <div class="form-group">
                                    <label>Petugas </label>
                                    <div class="input-group col-md-9 col-xs-8">
                                        <input class="form-control" wire:model="employeeno" type="text" placeholder="..." />
                                        <input class="form-control readonly" readonly="readonly" type="text" wire:model="empname" placeholder="..." />
                                        @error('employeeno')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12 mb-1">
                                <label for="">Jam Kerja</label>
                                <div class="form-group" style="margin-left:1px; white-space:nowrap">
                                    <input class="form-control" wire:model="work_hour" type="time" placeholder="hh:mm">
                                    @error('work_hour')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-12 mb-1">
                                <label for="">Lama Mesin Mati</label>
                                <div class="form-group" style="margin-left:1px; white-space:nowrap">
                                    <input class="form-control" wire:model="on_hour" type="time" placeholder="hh:mm">
                                    @error('on_hour')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        {{-- <button type="button" class="btn btn-secondary">Accept</button> --}}
                        <button type="button" class="btn btn-link text-gray-600 ms-auto" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-success" wire:click="save">
                            Save
                        </button>
                    </div>
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
                            <th class="border-0">Tanggal</th>
                            <th class="border-0">Shift</th>
                            <th class="border-0">Nomor Mesin</th>
                            <th class="border-0">NIK</th>
                            <th class="border-0">Petugas</th>
                            <th class="border-0 rounded-end">Jam Kerja</th>
                            <th class="border-0">Jam Mati</th>
                            <th class="border-0">Jam Jalan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Item -->
                        @if (count($jamkerja) > 0)
                            @foreach ($jamkerja as $item)
                                <tr>
                                    <td>
                                        <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#modal-edit" wire:click="edit({{$item->orderid}})">
                                            <i class="fa fa-edit"></i> Edit
                                        </button>
                                    </td> 
                                    <td>                                
                                        {{ $item->working_date }}
                                    </td>
                                    <td>
                                        {{ $item->work_shift }}
                                    </td>
                                    <td>
                                        {{ $item->machine_id }}
                                    </td>
                                    <td>
                                        
                                    </td>
                                    <td>
                                        
                                    </td>
                                    <td>
                                        {{ $item->work_hour }}
                                    </td>
                                    <td>
                                        {{ $item->off_hour }}
                                    </td>
                                    <td>
                                        {{ $item->on_hour }}
                                    </td>
                                    </td>
                                </tr>
                            @endforeach 
                        @else
                            <tr style="text-align: center">
                                <td>Results Not Found</td>
                            </tr>
                        @endif
                        
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
