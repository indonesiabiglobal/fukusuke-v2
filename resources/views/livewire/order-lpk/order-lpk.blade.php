<div class="row">
    <div class="col-12 col-lg-7">
        <div class="row">
            <div class="col-12 col-lg-3">
                <label class="form-label text-muted">Filter Tanggal</label>
            </div>
            <div class="col-12 col-lg-9 mb-3">
                <div class="form-group">
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
            </div>

            <div class="col-12 col-lg-3">
                <label class="form-label text-muted">Search</label>
            </div>
            <div class="col-12 col-lg-9">
                <div class="input-group col-md-9 col-xs-8">
                    <input id='search' name='search' wire:model.defer="searchTerm" class="form-control" type="text" placeholder="search nomor PO, nama produk" />
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-5">
        <div class="row">
            <div class="col-12 col-lg-2">
                <label for="buyer" class="form-label text-muted">Buyer</label>
            </div>
            <div class="col-12 col-lg-10">
                <div class="mb-3" wire:ignore>
                    <select class="form-control" wire:model.defer="idBuyer" id="buyer" name="buyer" data-choices data-choices-sorting-false>
                        <option value="">- Pilih Buyer -</option>
                        @foreach ($buyer as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-12 col-lg-2">
                <label for="product" class="form-label text-muted">Product</label>
            </div>
            <div class="col-12 col-lg-10">
                <div class="mb-3" wire:ignore>
                    <select class="form-control"  wire:model.defer="idProduct" id="product" name="product" data-choices data-choices-sorting-false>
                        <option value="">- All -</option>
                        @foreach ($products as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-12 mt-2">
        <div class="row">
            <div class="col-12 col-lg-6">
                <button id="btnFilter" wire:click="search" type="button" class="btn btn-primary"style="width:125px;">
                    <i class="fa fa-search"></i> Filter
                    <div wire:loading style="display:inline;">

                    </div>
                </button>
                
                

                <button 
                    id="btnCreate" 
                    type="button" 
                    class="btn btn-success" 
                    style="width:125px;"
                    {{-- onclick="window.location.href='{{ route('add-order') }}'"> --}}
                    <i class="fa fa-plus"></i> Add
                </button>
            </div>
            <div class="col-12 col-lg-6">
                <div class="d-flex align-items-center mb-3">
                    {{-- <form wire:submit.prevent="import">
                        <input type="file" wire:model="file">
                        <button type="submit">Import</button>
                    </form> --}}

                    <input type="file" id="fileInput" wire:model="file" style="display: none;">
                    <button class="btn mx-1 me-2 btn-success" type="button" onclick="document.getElementById('fileInput').click()" wire:loading.attr="disabled"><i
                        class="fas fa-arrow-up mx-1"></i>Upload Excel
                        <div wire:loading wire:target="file">
                            <span class="spinner-border flex-shrink-0" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </span>
                            <span class="flex-grow-1 ms-2">
                                Loading...
                            </span>
                        </div>
                    </button>

                    <button class="btn mx-1 me-2 btn-primary" wire:click="download" type="button">
                        <i class="fas fa-arrow-down mx-1"></i>Download</button>
                    <button class="btn mx-1 me-2 btn-info" type="button">
                        <i class="fas fa-print mx-1"></i>Print</button>
                </div>
            </div>
        </div>
    </div>
    {{-- <div class="col-lg-12">
        <table id="example" class="table table-bordered dt-responsive nowrap table-striped align-middle" style="width:100%">
            <thead>
                <tr>
                    <th class="border-0 rounded-start">Action</th>
                    <th class="border-0">PO Number</th>
                    <th class="border-0">Nama Produk</th>
                    <th class="border-0">Kode Produk</th>
                    <th class="border-0">Buyer</th>
                    <th class="border-0">Quantity</th>
                    <th class="border-0">Tgl. Order</th>
                    <th class="border-0">Etd</th>
                    <th class="border-0">Tgl Proses</th>
                    <th class="border-0 rounded-end">No.</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $no = 1;
                @endphp
                @foreach ($orders as $item)
                    <tr>
                        <td>
                            <a href="{{ route('edit-order', ['orderId' => $item->id]) }}" class="btn btn-info">
                                <i class="fa fa-edit"></i> Edit
                            </a>
                        </td>
                        <td>{{ $item->po_no }}</td>
                        <td>{{ $item->produk_name }}</td>
                        <td>{{ $item->product_code }}</td>
                        <td>{{ $item->buyer_name }}</td>
                        <td>{{ $item->order_qty }}</td>
                        <td>{{ $item->order_date }}</td>
                        <td>{{ $item->etddate }}</td>
                        <td>{{ $item->processdate }}</td>
                        <td>{{ $no++ }}</td>
                    </tr>
                @endforeach
                </tr>
            </tbody>
        </table>
    </div> --}}
    <div class="col-xl-12">
        <div class="card">
            {{-- <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Recent Orders</h4>
                <div class="flex-shrink-0">
                    <div class="dropdown card-header-dropdown">
                        <a class="text-reset dropdown-btn" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="fw-semibold text-uppercase fs-12">Sort by:
                            </span><span class="text-muted">Today<i class="mdi mdi-chevron-down ms-1"></i></span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a class="dropdown-item" href="#">Today</a>
                            <a class="dropdown-item" href="#">Yesterday</a>
                            <a class="dropdown-item" href="#">Last 7 Days</a>
                            <a class="dropdown-item" href="#">Last 30 Days</a>
                            <a class="dropdown-item" href="#">This Month</a>
                            <a class="dropdown-item" href="#">Last Month</a>
                        </div>
                    </div>
                </div>
            </div> --}}
            <!-- end card header -->

            <div class="card-body">
                <div class="table-responsive table-card">
                    <table class="table table-centered align-middle table-nowrap mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0 rounded-start">Action</th>
                                <th class="border-0">PO Number</th>
                                <th class="border-0">Nama Produk</th>
                                <th class="border-0">Kode Produk</th>
                                <th class="border-0">Buyer</th>
                                <th class="border-0">Quantity</th>
                                <th class="border-0">Tgl. Order</th>
                                <th class="border-0">Etd</th>
                                <th class="border-0">Tgl Proses</th>
                                <th class="border-0 rounded-end">No.</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $no = 1;
                            @endphp
                            @foreach ($orders as $item)
                                <tr>
                                    <td>
                                        {{-- <a href="{{ route('edit-order', ['orderId' => $item->id]) }}" class="btn btn-info">
                                            <i class="fa fa-edit"></i> Edit
                                        </a> --}}
                                    </td>
                                    <td>{{ $item->po_no }}</td>
                                    <td>{{ $item->produk_name }}</td>
                                    <td>{{ $item->product_code }}</td>
                                    <td>{{ $item->buyer_name }}</td>
                                    <td>{{ $item->order_qty }}</td>
                                    <td>{{ $item->order_date }}</td>
                                    <td>{{ $item->etddate }}</td>
                                    <td>{{ $item->processdate }}</td>
                                    <td>{{ $no++ }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="row align-items-center mt-4 pt-2 gy-2 text-center text-sm-start">
                    <div class="col-sm">
                        <div class="text-muted">
                            Showing <span class="fw-semibold">6</span> of <span class="fw-semibold">25</span> Results
                        </div>
                    </div>
                    <div class="col-sm-auto">
                        <ul class="pagination pagination-separated pagination-sm mb-0 justify-content-center justify-content-sm-start">
                            <li class="page-item disabled">
                                <a href="#" class="page-link">←</a>
                            </li>
                            <li class="page-item">
                                <a href="#" class="page-link">1</a>
                            </li>
                            <li class="page-item active">
                                <a href="#" class="page-link">2</a>
                            </li>
                            <li class="page-item">
                                <a href="#" class="page-link">3</a>
                            </li>
                            <li class="page-item">
                                <a href="#" class="page-link">→</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@section('script')

<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

  <!--datatable js-->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>

    <script src="{{ URL::asset('build/js/pages/datatables.init.js') }}"></script>

    {{-- <script src="{{ URL::asset('build/js/app.js') }}"></script> --}}

@endsection