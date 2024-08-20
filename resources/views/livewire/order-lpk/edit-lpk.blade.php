<div class="row">
    <form wire:submit.prevent="save">
        <div class="row">
            <div class="col-lg-6">
                <div class="form-group">
                    <div class="input-group">
                        <label class="control-label col-12 col-lg-3 fw-bold text-muted">Tanggal LPK</label>
                        <input wire:model.defer="lpk_date" type="text"
                            class="form-control @error('lpk_date') is-invalid @enderror" style="padding:0.44rem"
                            data-provider="flatpickr" data-date-format="d/m/Y">
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
                        <input type="text" class="form-control @error('lpk_no') is-invalid @enderror"
                            wire:model="lpk_no" />
                        @error('lpk_no')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="form-group mt-1">
                    <div class="input-group">
                        <label class="control-label col-12 col-lg-3 fw-bold text-muted">PO Number</label>
                        <input type="text" class="form-control @error('po_no') is-invalid @enderror"
                            wire:model.live.debounce.300ms="po_no" placeholder="PO NUMBER" />
                        @error('po_no')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="form-group mt-1">
                    <div class="input-group">
                        <label class="control-label col-12 col-lg-3 fw-bold text-muted"
                            style="text-decoration: underline;">
                            <a href="#" data-bs-toggle="modal" wire:click="showModalNoOrder" class="text-muted">
                                Nomor Order
                            </a>
                        </label>
                        <input type="text" class="form-control readonly bg-light" readonly="readonly"
                            wire:model="no_order" />
                    </div>
                </div>
                <div class="form-group mt-1">
                    <div class="input-group">
                        <label class="control-label col-12 col-lg-3 fw-bold text-muted">Nomor Mesin</label>
                        <input type="text" class="form-control @error('machineno') is-invalid @enderror"
                            wire:model.live.debounce.300ms="machineno" />
                        @error('machineno')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="form-group mt-1">
                    <div class="input-group">
                        <label class="control-label col-12 col-lg-3 fw-bold text-muted">Jumlah LPK</label>
                        <input type="number" class="form-control @error('qty_lpk') is-invalid @enderror"
                            wire:model.live="qty_lpk" />
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
                        <input type="number" class="form-control @error('qty_gentan') is-invalid @enderror"
                            wire:model.live="qty_gentan" />
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
                        <input type="number" class="form-control @error('qty_gulung') is-invalid @enderror"
                            wire:model.live="qty_gulung" />
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
                        <input type="number"
                            class="form-control readonly bg-light @error('panjang_lpk') is-invalid @enderror"
                            readonly="readonly" wire:model="panjang_lpk" />
                        <span class="input-group-text">
                            meter
                        </span>
                        @error('panjang_lpk')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                {{-- catatan --}}
                <div class="form-group mt-1">
                    <label for="textarea" class="control-label col-12 col-lg-3 fw-bold text-muted">Catatan</label>
                    <textarea class="form-control" placeholder="Catatan" id="textarea" rows="2" wire:model="remark"></textarea>
                </div>
                {{-- warna LPK --}}
                {{-- <div class="form-group mt-1">
                    <div class="input-group">
                        <label class="control-label col-12 col-lg-3 fw-bold text-muted">Warna LPK</label>
                        <div class="col-12 col-lg-9" wire:ignore>
                            <select required data-choices data-choices-sorting="true"
                                class="form-select @error('warnalpkid') is-invalid @enderror"
                                wire:model="warnalpkid">
                                <option value="" selected>
                                    Silahkan Pilih
                                </option>
                                @foreach ($masterWarnaLPK as $item)
                                    <option value="{{ $item->id }}" @if ($warnalpkid['value'] == $item->id) selected @endif>
                                        {{ $item->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('warnalpkid')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div> --}}

            </div>

            <div class="col-lg-6">
                <div class="form-group">
                    <div class="input-group">
                        <label class="control-label col-12 col-lg-3 fw-bold text-muted">Tanggal Proses</label>
                        {{-- <input wire:model.defer="processdate" type="date" class="form-control datepicker-input @error('processdate') is-invalid @enderror" placeholder="yyyy/mm/dd" style="padding:0.44rem" data-provider="flatpickr" data-date-format="d/m/Y"/> --}}
                        <input wire:model.defer="processdate" type="text"
                            class="form-control @error('processdate') is-invalid @enderror" style="padding:0.44rem"
                            data-provider="flatpickr" data-date-format="d/m/Y">
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
                        <input class="form-control datepicker-input readonly bg-light" readonly="readonly"
                            type="date" wire:model.defer="order_date" placeholder="yyyy/mm/dd" />
                    </div>
                </div>
                <div class="form-group mt-1">
                    <div class="input-group">
                        <label class="control-label col-12 col-lg-3 fw-bold text-muted">Buyer</label>
                        <input type="text" class="form-control readonly bg-light" readonly="readonly"
                            wire:model="buyer_name" />
                    </div>
                </div>
                <div class="form-group mt-1">
                    <div class="input-group">
                        <label class="control-label col-12 col-lg-3 fw-bold text-muted">Nama Produk</label>
                        <input type="text" class="form-control readonly bg-light" readonly="readonly"
                            wire:model="product_name" />
                    </div>
                </div>
                <div class="form-group mt-1">
                    <div class="input-group">
                        <label class="control-label col-12 col-lg-3 fw-bold text-muted">Nama Mesin</label>
                        <input type="text" class="form-control readonly bg-light" readonly="readonly"
                            wire:model="machinename" />
                    </div>
                </div>
                <div class="form-group mt-1">
                    <div class="input-group">
                        <label class="control-label col-12 col-lg-3 fw-bold text-muted">Panjang Total</label>
                        <input type="text" class="form-control readonly bg-light" readonly="readonly"
                            wire:model="total_assembly_line" />
                        <span class="input-group-text">
                            meter
                        </span>
                    </div>
                </div>
                <div class="form-group mt-1">
                    <div class="input-group">
                        <label class="control-label col-12 col-lg-3 fw-bold text-muted">Dimensi (TxLxP)</label>
                        <input type="text" class="form-control readonly bg-light" readonly="readonly"
                            wire:model="dimensi" />
                    </div>
                </div>
                <div class="form-group mt-1">
                    <div class="input-group">
                        <label class="control-label col-12 col-lg-3 fw-bold text-muted">Default Gulung</label>
                        <input type="text" class="form-control readonly bg-light" readonly="readonly"
                            wire:model="defaultgulung" />
                        <span class="input-group-text">
                            meter
                        </span>
                    </div>
                </div>
                <div class="form-group mt-1">
                    <div class="input-group">
                        <label class="control-label col-12 col-lg-3 fw-bold text-muted">Selisih Kurang</label>
                        <input type="text" class="form-control readonly bg-light" readonly="readonly"
                            wire:model="selisihkurang" />
                        <span class="input-group-text">
                            meter
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <hr />
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

                @if ($status_lpk == 0)
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
                @endif
                @if ($status_lpk == 1)
                    <button type="button" class="btn btn-success btn-print" wire:click="print">
                        <i class="bx bx-printer"></i> Print
                    </button>
                    <p class="text-secondary mb-0">Data LPK Sudah Di Produksi ! ..</p>
                @endif

            </div>
        </div>

        <div class="modal fade" id="modal-noorder-produk" tabindex="-1" role="dialog"
            aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="myExtraLargeModalLabel">Produk Info - Nomor: <span
                                class="fw-bold">{{ $code }}</span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="col-lg-12">
                            <div class="row">
                                <div class="col-12 col-lg-6">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-4">Nomor Order</label>
                                            <input type="text" class="form-control col-12 col-lg-8"
                                                value="{{ $product->code ?? '' }}" placeholder="KODE" required />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-4">Nama Produk</label>
                                            <input type="text" class="form-control col-12 col-lg-8"
                                                value="{{ $product->name ?? '' }}" placeholder="nama" required />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-4">Kode Tipe</label>
                                            <input type="text" class="form-control col-12 col-lg-8"
                                                value="{{ $product->product_type_id ?? '' }}" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-4">Kode Produk (Alias)</label>
                                            <input type="text" class="form-control col-12 col-lg-8"
                                                value="{{ $product->code_alias ?? '' }}" placeholder="KODE" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-4">Code Barcode</label>
                                            <input type="text" class="form-control col-12 col-lg-8"
                                                value="{{ $product->codebarcode ?? '' }}" placeholder="KODE" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-lg-6">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-4">Dimensi (T x L x P)</label>
                                            <span class="input-group-text">
                                                T
                                            </span>
                                            <input required type="number" class="form-control"
                                                value="{{ $product->ketebalan ?? '' }}" placeholder="Tebal" />
                                            <span class="input-group-text">
                                                L
                                            </span>
                                            <input required type="number" class="form-control"
                                                value="{{ $product->diameterlipat ?? '' }}" placeholder="Lebar" />
                                            <span class="input-group-text">
                                                P
                                            </span>
                                            <input required type="number" class="form-control"
                                                value="{{ $product->productlength ?? '' }}" placeholder="Panjang" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-4">Berat Satuan</label>
                                            <input required type="number" class="form-control col-12 col-lg-8"
                                                value="{{ $product->unit_weight ?? '' }}" placeholder="0" />
                                            <span class="input-group-text">
                                                gram
                                            </span>
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-4">Satuan</label>
                                            <input required type="text" class="form-control"
                                                value="{{ $product->product_unit ?? '' }}" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 ">
                                    <p class="text-success fw-bold">INFURE</p>
                                </div>
                                <div class="col-12 col-lg-6">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-4">Dimensi</label>
                                            <input required type="text" class="form-control"
                                                value="{{ $product->inflation_thickness ?? '' }}"
                                                placeholder="Tebal" />
                                            @error('inflation_thickness')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                            <span class="input-group-text">
                                                x
                                            </span>
                                            <input required type="text" class="form-control"
                                                value="{{ $product->inflation_fold_diameter ?? '' }}"
                                                placeholder="Lebar" />
                                            @error('inflation_fold_diameter')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                            <span class="input-group-text">
                                                mm
                                            </span>
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-4">Panjang Gulung</label>
                                            <input required type="text" class="form-control col-12 col-lg-8"
                                                value="{{ $product->one_winding_m_number ?? '' }}" placeholder="0" />
                                            <span class="input-group-text">
                                                m
                                            </span>
                                            @error('one_winding_m_number')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-4">Material</label>
                                            <input required type="text" class="form-control col-12 col-lg-8"
                                                value="{{ $product->material_classification ?? '' }}"
                                                placeholder="0" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-4">Embos</label>
                                            <input required type="text" class="form-control col-12 col-lg-8"
                                                value="{{ $product->embossed_classification ?? '' }}"
                                                placeholder="0" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-4">Corona</label>
                                            <input required type="text" class="form-control col-12 col-lg-8"
                                                value="{{ $product->surface_classification ?? '' }}"
                                                placeholder="0" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-lg-6">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-4">MB -1 (Master Batch) </label>
                                            <input type="text" class="form-control"
                                                value="{{ $product->coloring_1 ?? '' }}" placeholder="warna mb 1" />
                                            @error('coloring_1')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-4">MB -2 </label>
                                            <input type="text" class="form-control"
                                                value="{{ $product->coloring_2 ?? '' }}" placeholder="warna mb 2" />
                                            @error('coloring_2')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-4">MB -3 </label>
                                            <input type="text" class="form-control"
                                                value="{{ $product->coloring_3 ?? '' }}" placeholder="warna mb 3" />
                                            @error('coloring_3')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-4">MB -4 </label>
                                            <input type="text" class="form-control"
                                                value="{{ $product->coloring_4 ?? '' }}" placeholder="warna mb 4" />
                                            @error('coloring_4')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-4">MB -5 </label>
                                            <input type="text" class="form-control"
                                                value="{{ $product->coloring_5 ?? '' }}" placeholder="warna mb 5" />
                                            @error('coloring_5')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-4">Catatan </label>
                                            <input type="text" class="form-control"
                                                value="{{ $product->inflation_notes ?? '' }}"
                                                placeholder="Catatan" />
                                            @error('inflation_notes')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-lg-6 mt-3">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-4">Gentan</label>
                                            <input type="text" class="form-control"
                                                value="{{ $product->gentan_classification ?? '' }}" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-4">Gazette</label>
                                            <input type="text" class="form-control"
                                                value="{{ $product->gazette_classification ?? '' }}" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-4">GZ Dimensi</label>
                                            <span class="input-group-text">
                                                A
                                            </span>
                                            <input required type="text" class="form-control col-12 col-lg-8"
                                                value="{{ $product->gazette_dimension_a ?? '' }}" placeholder="0" />

                                            <span class="input-group-text">
                                                B
                                            </span>
                                            <input required type="text" class="form-control col-12 col-lg-8"
                                                value="{{ $product->gazette_dimension_b ?? '' }}" placeholder="0" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-4">-</label>
                                            <span class="input-group-text">
                                                C
                                            </span>
                                            <input required type="text" class="form-control col-12 col-lg-8"
                                                value="{{ $product->gazette_dimension_c ?? '' }}" placeholder="0" />

                                            <span class="input-group-text">
                                                D
                                            </span>
                                            <input required type="text" class="form-control col-12 col-lg-8"
                                                value="{{ $product->gazette_dimension_d ?? '' }}" placeholder="0" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-lg-6 mt-3 d-flex justify-content-center">
                                    <img src="{{ asset('asset/image/Gazette-ent.png') }}" width="240"
                                        height="130" alt="img">
                                </div>
                                <div class="col-12">
                                    <p class="text-success">HAGATA</p>
                                </div>
                                <div class="col-12 col-lg-6">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-4">Kode Nukigata</label>
                                            <input type="text" class="form-control"
                                                value="{{ $katanuki_id ?? '' }}" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-4">A.</label>
                                            <input required type="number" class="form-control col-12 col-lg-8"
                                                value="{{ $product->extracted_dimension_a ?? '' }}"
                                                placeholder="0" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-4">B.</label>
                                            <input required type="number" class="form-control col-12 col-lg-8"
                                                value="{{ $product->extracted_dimension_b ?? '' }}"
                                                placeholder="0" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-4">C.</label>
                                            <input required type="number" class="form-control col-12 col-lg-8"
                                                value="{{ $product->extracted_dimension_c ?? '' }}"
                                                placeholder="0" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-lg-6 mt-3 d-flex justify-content-center">
                                    @if ($photoKatanuki)
                                        <img src="{{ asset('storage/' . $photoKatanuki) }}" width="240"
                                            height="130" alt="img">
                                    @endif
                                </div>
                                <div class="col-12">
                                    <p class="text-success">PRINTING</p>
                                </div>
                                <div class="col-12 col-lg-4">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-1">*</label>
                                            <span class="input-group-text">
                                                Warna Depan:
                                            </span>
                                            <input required type="number" class="form-control"
                                                value="{{ $product->number_of_color ?? '' }}" placeholder="..." />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-1">1</label>
                                            <input type="text" class="form-control"
                                                value="{{ $product->color_spec_1 ?? '' }}" placeholder="..." />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-1">2</label>
                                            <input type="text" class="form-control"
                                                value="{{ $product->color_spec_2 ?? '' }}" placeholder="..." />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-1">3</label>
                                            <input type="text" class="form-control"
                                                value="{{ $product->color_spec_3 ?? '' }}" placeholder="..." />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-1">4</label>
                                            <input type="text" class="form-control"
                                                value="{{ $product->color_spec_4 ?? '' }}" placeholder="..." />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-1">5</label>
                                            <input type="text" class="form-control"
                                                value="{{ $product->color_spec_5 ?? '' }}" placeholder="..." />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-lg-4">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-1">*</label>
                                            <span class="input-group-text">
                                                Warna Belakang:
                                            </span>
                                            <input required type="text" class="form-control"
                                                value="{{ $product->back_color_number ?? '' }}" placeholder="..." />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-1">1</label>
                                            <input type="text" class="form-control"
                                                value="{{ $product->back_color_1 ?? '' }}" placeholder="..." />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-1">2</label>
                                            <input type="text" class="form-control"
                                                value="{{ $product->back_color_2 ?? '' }}" placeholder="..." />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-1">3</label>
                                            <input type="text" class="form-control"
                                                value="{{ $product->back_color_3 ?? '' }}" placeholder="..." />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-1">4</label>
                                            <input type="text" class="form-control"
                                                value="{{ $product->back_color_4 ?? '' }}" placeholder="..." />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-1">5</label>
                                            <input type="text" class="form-control"
                                                value="{{ $product->back_color_5 ?? '' }}" placeholder="..." />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-lg-4">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-4">Jenis Cetak</label>
                                            <input type="text" class="form-control"
                                                value="{{ $product->print_type ?? '' }}" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-4">Sifat Tinta</label>
                                            <input type="text" class="form-control"
                                                value="{{ $product->ink_characteristic ?? '' }}" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-4">Endless</label>
                                            <input type="text" class="form-control"
                                                value="{{ $product->endless_printing ?? '' }}" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-4">Arah Gulung</label>
                                            <input type="text" class="form-control"
                                                value="{{ $product->winding_direction_of_the_web ?? '' }}" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <p class="text-success">SEITAI</p>
                                </div>
                                <div class="col-12 col-lg-4">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-6">Klarifikasi Seal</label>
                                            <input type="text" class="form-control"
                                                value="{{ $product->seal_classification ?? '' }}" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-6">Jarak Seal dari Pola</label>
                                            <input required type="number" class="form-control"
                                                value="{{ $product->from_seal_design ?? '' }}" placeholder="..."
                                                min="0" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-6">Jarak Seal Bawah</label>
                                            <input required type="number" class="form-control"
                                                value="{{ $product->lower_sealing_length ?? '' }}" placeholder="..."
                                                min="0" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-6">Jumlah Baris Palet</label>
                                            <input required type="number" class="form-control"
                                                value="{{ $product->palet_jumlah_baris ?? '' }}" placeholder="..."
                                                min="0" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-6">Isi Baris Palet</label>
                                            <input required type="number" class="form-control"
                                                value="{{ $product->palet_isi_baris ?? '' }}" placeholder="..."
                                                min="0" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-6">Lakban Seitai</label>
                                            <input type="text" class="form-control"
                                                value="{{ $product->lakbanseitaiid ?? '' }}" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-6">Stempel Seitai</label>
                                            <input type="text" class="form-control"
                                                value="{{ $product->stampelseitaiid ?? '' }}" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-6">Hagata Seitai</label>
                                            <input type="text" class="form-control"
                                                value="{{ $product->hagataseitaiid ?? '' }}" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-6">Jenis Seal Seitai</label>
                                            <input type="text" class="form-control"
                                                value="{{ $product->jenissealseitaiid ?? '' }}" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-lg-5">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-3">Kode Gasio</label>
                                            <input type="text" class="form-control"
                                                value="{{ $product->pack_gaiso_id ?? '' }}" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-3">Kode Box</label>
                                            <input type="text" class="form-control"
                                                value="{{ $product->pack_box_id ?? '' }}" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-3">Kode Inner</label>
                                            <input type="text" class="form-control"
                                                value="{{ $product->pack_inner_id ?? '' }}" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-3">Kode Layer</label>
                                            <input type="text" class="form-control"
                                                value="{{ $product->pack_layer_id ?? '' }}" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-3">Catatan Produksi</label>
                                            <textarea class="form-control" rows="2" placeholder="Catatan Produksi">{{ $product->manufacturing_summary ?? '' }}</textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-lg-3">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-2">Isi</label>
                                            <input required type="number" class="form-control"
                                                value="{{ $product->case_gaiso_count ?? '' }}" placeholder="0" />
                                            <input required type="text" class="form-control"
                                                value="{{ $product->case_gaiso_count_unit ?? '' }}" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-2">Isi</label>
                                            <input type="number" class="form-control"
                                                value="{{ $product->case_box_count ?? '' }}" placeholder="0" />
                                            <input type="text" class="form-control"
                                                value="{{ $product->case_box_count_unit ?? '' }}" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-1">
                                        <div class="input-group">
                                            <label class="control-label col-12 col-lg-2">Isi</label>
                                            <input required type="text" class="form-control"
                                                value="{{ $product->case_inner_count ?? '' }}" />
                                            <input required type="text" class="form-control"
                                                value="{{ $product->case_inner_count_unit ?? '' }}" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <a href="javascript:void(0);" class="btn btn-light link-success fw-medium"
                            data-bs-dismiss="modal"><i class="ri-close-line me-1 align-middle"></i> Close</a>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div>
    </form>
</div>
@script
    <script>
        $wire.on('redirectToPrint', (lpk_id) => {
            var printUrl = '{{ route('report-lpk') }}?lpk_id=' + lpk_id
            window.open(printUrl, '_blank');
        });

        $wire.on('showModalNoOrder', () => {
            $('#modal-noorder-produk').modal('show');
        });

        $wire.on('closeModalNoOrder', () => {
            $('#modal-noorder-produk').modal('hide');
        });
    </script>
@endscript
