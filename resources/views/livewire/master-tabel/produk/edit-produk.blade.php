<div class="row mt-2">
    {{-- <div class="col-lg-2"></div> --}}
    <div class="col-lg-12">
        <form wire:submit.prevent="update">
            <div class="row">
                <div class="col-12 col-lg-6">
                    <div class="form-group">
                        <div class="input-group">
                            <label class="control-label col-12 col-lg-4">Nomor Order</label>
                            <input type="text" class="form-control col-12 col-lg-8" wire:model="code"
                                placeholder="KODE" required style="text-transform: uppercase;"
                                oninput="this.value = this.value.toUpperCase()" />
                            @error('code')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group mt-1">
                        <div class="input-group">
                            <label class="control-label col-12 col-lg-4">Nama Produk</label>
                            <input type="text" class="form-control col-12 col-lg-8" wire:model="name"
                                placeholder="nama" required />
                            @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group mt-1">
                        <div class="input-group">
                            <label class="control-label col-12 col-lg-4">Kode Tipe</label>
                            <div class="col-12 col-lg-8" wire:ignore>
                                <select required data-choices data-choices-search-field
                                    class="form-select col-12 col-lg-8 @error('product_type_id') is-invalid @enderror"
                                    wire:model="product_type_id" placeholder="">
                                    <option value="" selected>
                                        Silahkan Pilih
                                    </option>
                                    @foreach ($masterProductType as $item)
                                        <option value="{{ $item->id }}"
                                            data-custom-properties='{"code": "{{ $item->code }}"}'
                                            {{ $product_type_id['value'] != null ? ($item->id == $product_type_id['value'] ? 'selected' : '') : '' }}>
                                            {{ $item->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('product_type_id')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="form-group mt-1">
                        <div class="input-group">
                            <label class="control-label col-12 col-lg-4">Kode Produk (Alias)</label>
                            <input type="text" class="form-control col-12 col-lg-8" wire:model="code_alias"
                                placeholder="KODE" />
                            @error('code_alias')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group mt-1">
                        <div class="input-group">
                            <label class="control-label col-12 col-lg-4">Code Barcode</label>
                            <input type="text" class="form-control col-12 col-lg-8" wire:model="codebarcode"
                                placeholder="KODE" />
                            @error('codebarcode')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    {{-- warna LPK --}}
                    <div class="form-group mt-1">
                        <div class="input-group">
                            <label class="control-label col-12 col-lg-4">Warna LPK</label>
                            <div class="col-12 col-lg-8" wire:ignore>
                                <select required data-choices data-choices-sorting-false
                                    class="form-select @error('warnalpkid') is-invalid @enderror"
                                    wire:model.live="warnalpkid">
                                    <option value="" selected>
                                        Silahkan Pilih
                                    </option>
                                    @foreach ($masterWarnaLPK as $item)
                                        <option value="{{ $item->id }}"
                                            @if ($warnalpkid['value'] == $item->id) selected @endif>
                                            {{ $item->name }}
                                        </option>
                                    @endforeach
                                    <option value="lainnya">
                                        Lainnya
                                    </option>
                                </select>
                            </div>
                            @if (($warnalpkid['value'] ?? '') == 'lainnya')
                                <input required type="text" class="form-control mt-2" wire:model="custom_warna_lpk"
                                    placeholder="Masukkan warna LPK" />
                            @endif
                            @error('warnalpkid')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            @error('custom_warna_lpk')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
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
                            <input required type="number" class="form-control" wire:model="ketebalan"
                                placeholder="Tebal" />
                            @error('ketebalan')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <span class="input-group-text">
                                L
                            </span>
                            <input required type="number" class="form-control" wire:model="diameterlipat"
                                placeholder="Lebar" />
                            @error('diameterlipat')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <span class="input-group-text">
                                P
                            </span>
                            <input required type="number" class="form-control" wire:model="productlength"
                                placeholder="Panjang" />
                            @error('productlength')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group mt-1">
                        <div class="input-group">
                            <label class="control-label col-12 col-lg-4">Berat Satuan</label>
                            <input required type="number" class="form-control col-12 col-lg-8" wire:model="unit_weight"
                                placeholder="0" />
                            <span class="input-group-text">
                                gram
                            </span>
                            @error('unit_weight')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group mt-1">
                        <div class="input-group" wire:ignore>
                            <label class="control-label col-12 col-lg-4">Satuan</label>
                            <div class="col-12 col-lg-8" wire:ignore>
                                <select required data-choices data-choices-sorting="true"
                                    class="form-select @error('product_unit') is-invalid @enderror"
                                    wire:model="product_unit" placeholder="">
                                    <option value="" selected>
                                        Silahkan Pilih
                                    </option>
                                    @foreach ($masterUnit as $item)
                                        <option value="{{ $item->id }}"
                                            {{ $product_unit['value'] != null ? ($item->id == $product_unit['value'] ? 'selected' : '') : '' }}>
                                            {{ $item->name }}
                                        </option>
                                    @endforeach`
                                </select>
                                @error('product_unit')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
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
                            <input required type="text" class="form-control" wire:model="inflation_thickness"
                                placeholder="Tebal" />
                            @error('inflation_thickness')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <span class="input-group-text">
                                x
                            </span>
                            <input required type="text" class="form-control" wire:model="inflation_fold_diameter"
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
                                wire:model="one_winding_m_number" placeholder="0" />
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
                            <div class="col-12 col-lg-8" wire:ignore>
                                <select data-choices data-choices-sorting="true"
                                    class="form-select col-12 col-lg-8 @error('material_classification') is-invalid @enderror"
                                    wire:model="material_classification" placeholder="">
                                    <option value="" selected>
                                        Silahkan Pilih
                                    </option>
                                    @foreach ($masterMaterial as $item)
                                        <option value="{{ $item->id }}"
                                            {{ $material_classification['value'] != null ? ($item->id == $material_classification['value'] ? 'selected' : '') : '' }}>
                                            {{ $item->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('material_classification')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="form-group mt-1">
                        <div class="input-group">
                            <label class="control-label col-12 col-lg-4">Embos</label>
                            <div class="col-12 col-lg-8" wire:ignore>
                                <select data-choices data-choices-sorting="true"
                                    class="form-select col-12 col-lg-8 @error('embossed_classification') is-invalid @enderror"
                                    wire:model="embossed_classification" placeholder="">
                                    <option value="" selected>
                                        Silahkan Pilih
                                    </option>
                                    @foreach ($masterEmbossed as $item)
                                        <option value="{{ $item->id }}"
                                            {{ $embossed_classification['value'] != null ? ($item->id == $embossed_classification['value'] ? 'selected' : '') : '' }}>
                                            {{ $item->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('embossed_classification')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    {{-- Corona --}}
                    <div class="form-group mt-1">
                        <div class="input-group">
                            <label class="control-label col-12 col-lg-4">Corona</label>
                            <div class="col-12 col-lg-8" wire:ignore>
                                <select data-choices data-choices-sorting="true"
                                    class="form-select col-12 col-lg-8 @error('surface_classification') is-invalid @enderror"
                                    wire:model="surface_classification" placeholder="">
                                    <option value="" selected>
                                        Silahkan Pilih
                                    </option>
                                    @foreach ($masterSurface as $item)
                                        <option value="{{ $item->id }}"
                                            {{ $surface_classification['value'] != null ? ($item->id == $surface_classification['value'] ? 'selected' : '') : '' }}>
                                            {{ $item->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('surface_classification')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    {{-- Lakban Infure --}}
                    <div class="form-group mt-1">
                        <div class="input-group">
                            <label class="control-label col-12 col-lg-4">Lakban Infure</label>
                            <div class="col-12 col-lg-8" wire:ignore>
                                <select data-choices data-choices-sorting-false
                                    class="form-select @error('lakbaninfureid') is-invalid @enderror"
                                    wire:model.live="lakbaninfureid" placeholder="">
                                    <option value="" selected>
                                        Silahkan Pilih
                                    </option>
                                    @foreach ($masterLakbanInfure as $item)
                                        <option value="{{ $item->id }}"
                                            {{ $lakbaninfureid['value'] != null ? ($item->id == $lakbaninfureid['value'] ? 'selected' : '') : '' }}>
                                            {{ $item->name }}
                                        </option>
                                    @endforeach
                                    <option value="lainnya">
                                        Lainnya
                                    </option>
                                </select>
                            </div>
                            @if (($lakbaninfureid['value'] ?? '') === 'lainnya')
                                <input required type="text" class="form-control mt-2"
                                    wire:model="custom_lakban_infure" placeholder="Masukkan lakban infure lainnya" />
                            @endif
                            @error('lakbaninfureid')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            @error('custom_lakban_infure')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-6">
                    <div class="form-group">
                        <div class="input-group">
                            <label class="control-label col-12 col-lg-4">MB -1 (Master Batch) </label>
                            <input type="text" class="form-control" wire:model="coloring_1"
                                placeholder="warna mb 1" />
                            @error('coloring_1')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group mt-1">
                        <div class="input-group">
                            <label class="control-label col-12 col-lg-4">MB -2 </label>
                            <input type="text" class="form-control" wire:model="coloring_2"
                                placeholder="warna mb 2" />
                            @error('coloring_2')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group mt-1">
                        <div class="input-group">
                            <label class="control-label col-12 col-lg-4">MB -3 </label>
                            <input type="text" class="form-control" wire:model="coloring_3"
                                placeholder="warna mb 3" />
                            @error('coloring_3')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group mt-1">
                        <div class="input-group">
                            <label class="control-label col-12 col-lg-4">MB -4 </label>
                            <input type="text" class="form-control" wire:model="coloring_4"
                                placeholder="warna mb 4" />
                            @error('coloring_4')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group mt-1">
                        <div class="input-group">
                            <label class="control-label col-12 col-lg-4">MB -5 </label>
                            <input type="text" class="form-control" wire:model="coloring_5"
                                placeholder="warna mb 5" />
                            @error('coloring_5')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group mt-1">
                        <div class="input-group">
                            <label class="control-label col-12 col-lg-4">Catatan </label>
                            <input type="text" class="form-control" wire:model="inflation_notes"
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
                            <div class="col-12 col-lg-8" wire:ignore wire:ignore>
                                <select data-choices data-choices-sorting="true"
                                    class="form-select @error('gentan_classification') is-invalid @enderror"
                                    wire:model="gentan_classification" placeholder="">
                                    <option value="" selected>
                                        Silahkan Pilih
                                    </option>
                                    @foreach ($masterGentanClassifications as $item)
                                        <option value="{{ $item->id }}"
                                            {{ $gentan_classification['value'] != null ? ($item->id == $gentan_classification['value'] ? 'selected' : '') : '' }}>
                                            {{ $item->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('gentan_classification')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group mt-1">
                        <div class="input-group">
                            <label class="control-label col-12 col-lg-4">Gazette</label>
                            <div class="col-12 col-lg-8" wire:ignore>
                                <select data-choices data-choices-sorting="true"
                                    class="form-select @error('gazette_classification') is-invalid @enderror"
                                    wire:model="gazette_classification" placeholder="">
                                    <option value="" selected>
                                        Silahkan Pilih
                                    </option>
                                    @foreach ($masterGazetteClassifications as $item)
                                        <option value="{{ $item->id }}"
                                            {{ $gazette_classification['value'] != null ? ($item->id == $gazette_classification['value'] ? 'selected' : '') : '' }}>
                                            {{ $item->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('gazette_classification')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                        </div>
                    </div>
                    <div class="form-group mt-1">
                        <div class="input-group">
                            <label class="control-label col-12 col-lg-4">GZ Dimensi</label>
                            {{-- A --}}
                            <span class="input-group-text">
                                A
                            </span>
                            <input required type="text" class="form-control col-12 col-lg-8"
                                wire:model="gazette_dimension_a" placeholder="0" />

                            {{-- C --}}
                            <span class="input-group-text">
                                C
                            </span>
                            <input required type="text" class="form-control col-12 col-lg-8"
                                wire:model="gazette_dimension_c" placeholder="0" />
                        </div>
                    </div>
                    <div class="form-group mt-1">
                        <div class="input-group">
                            <label class="control-label col-12 col-lg-4">-</label>
                            {{-- B --}}
                            <span class="input-group-text">
                                B
                            </span>
                            <input required type="text" class="form-control col-12 col-lg-8"
                                wire:model="gazette_dimension_b" placeholder="0" />

                            {{-- D --}}
                            <span class="input-group-text">
                                D
                            </span>
                            <input required type="text" class="form-control col-12 col-lg-8"
                                wire:model="gazette_dimension_d" placeholder="0" />
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-6 mt-3 d-flex justify-content-center">
                    <img src="{{ asset('asset/image/Gazette-ent.png') }}" width="240" height="130"
                        alt="img">
                </div>

                {{-- PRINTING --}}
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
                            <input required type="number" class="form-control" wire:model="number_of_color"
                                placeholder="..." />
                        </div>
                    </div>
                    <div class="form-group mt-1">
                        <div class="input-group">
                            <label class="control-label col-12 col-lg-1">1</label>
                            <input type="text" class="form-control" wire:model="color_spec_1"
                                placeholder="..." />
                        </div>
                    </div>
                    <div class="form-group mt-1">
                        <div class="input-group">
                            <label class="control-label col-12 col-lg-1">2</label>
                            <input type="text" class="form-control" wire:model="color_spec_2"
                                placeholder="..." />
                        </div>
                    </div>
                    <div class="form-group mt-1">
                        <div class="input-group">
                            <label class="control-label col-12 col-lg-1">3</label>
                            <input type="text" class="form-control" wire:model="color_spec_3"
                                placeholder="..." />
                        </div>
                    </div>
                    <div class="form-group mt-1">
                        <div class="input-group">
                            <label class="control-label col-12 col-lg-1">4</label>
                            <input type="text" class="form-control" wire:model="color_spec_4"
                                placeholder="..." />
                        </div>
                    </div>
                    <div class="form-group mt-1">
                        <div class="input-group">
                            <label class="control-label col-12 col-lg-1">5</label>
                            <input type="text" class="form-control" wire:model="color_spec_5"
                                placeholder="..." />
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
                            <input required type="text" class="form-control" wire:model="back_color_number"
                                placeholder="..." />
                        </div>
                    </div>
                    <div class="form-group mt-1">
                        <div class="input-group">
                            <label class="control-label col-12 col-lg-1">1</label>
                            <input type="text" class="form-control" wire:model="back_color_1"
                                placeholder="..." />
                        </div>
                    </div>
                    <div class="form-group mt-1">
                        <div class="input-group">
                            <label class="control-label col-12 col-lg-1">2</label>
                            <input type="text" class="form-control" wire:model="back_color_2"
                                placeholder="..." />
                        </div>
                    </div>
                    <div class="form-group mt-1">
                        <div class="input-group">
                            <label class="control-label col-12 col-lg-1">3</label>
                            <input type="text" class="form-control" wire:model="back_color_3"
                                placeholder="..." />
                        </div>
                    </div>
                    <div class="form-group mt-1">
                        <div class="input-group">
                            <label class="control-label col-12 col-lg-1">4</label>
                            <input type="text" class="form-control" wire:model="back_color_4"
                                placeholder="..." />
                        </div>
                    </div>
                    <div class="form-group mt-1">
                        <div class="input-group">
                            <label class="control-label col-12 col-lg-1">5</label>
                            <input type="text" class="form-control" wire:model="back_color_5"
                                placeholder="..." />
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-4">
                    <div class="form-group">
                        <div class="input-group">
                            <label class="control-label col-12 col-lg-4">Jenis Cetak</label>
                            <div class="col-12 col-lg-8" wire:ignore>
                                <select data-choices data-choices-sorting="true"
                                    class="form-select @error('print_type') is-invalid @enderror"
                                    wire:model="print_type" placeholder="">
                                    <option value="" selected>
                                        Silahkan Pilih
                                    </option>
                                    @foreach ($masterPrintType as $item)
                                        <option value="{{ $item->id }}"
                                            {{ $print_type['value'] != null ? ($item->id == $print_type['value'] ? 'selected' : '') : '' }}>
                                            {{ $item->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('print_type')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="form-group mt-1">
                        <div class="input-group">
                            <label class="control-label col-12 col-lg-4">Sifat Tinta</label>
                            <div class="col-12 col-lg-8" wire:ignore>
                                <select data-choices data-choices-sorting="true"
                                    class="form-select @error('ink_characteristic') is-invalid @enderror"
                                    wire:model="ink_characteristic" placeholder="">
                                    <option value="" selected>
                                        Silahkan Pilih
                                    </option>
                                    @foreach ($masterInkCharacteristics as $item)
                                        <option value="{{ $item->id }}"
                                            {{ $ink_characteristic['value'] != null ? ($item->id == $ink_characteristic['value'] ? 'selected' : '') : '' }}>
                                            {{ $item->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('ink_characteristic')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="form-group mt-1">
                        <div class="input-group">
                            <label class="control-label col-12 col-lg-4">Endless</label>
                            <div class="col-12 col-lg-8" wire:ignore>
                                <select data-choices data-choices-sorting="true"
                                    class="form-select @error('endless_printing') is-invalid @enderror"
                                    wire:model="endless_printing" placeholder="">
                                    <option value="" selected>
                                        Silahkan Pilih
                                    </option>
                                    @foreach ($masterEndlessPrinting as $item)
                                        <option value="{{ $item->id }}"
                                            {{ $endless_printing['value'] != null ? ($item->id == $endless_printing['value'] ? 'selected' : '') : '' }}>
                                            {{ $item->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('endless_printing')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="form-group mt-1">
                        <div class="input-group">
                            <label class="control-label col-12 col-lg-4">Arah Gulung</label>
                            <div class="col-12 col-lg-8" wire:ignore>
                                <select data-choices data-choices-sorting="true"
                                    class="form-select @error('winding_direction_of_the_web') is-invalid @enderror"
                                    wire:model="winding_direction_of_the_web" placeholder="">
                                    <option value="" selected>
                                        Silahkan Pilih
                                    </option>
                                    @foreach ($masterArahGulung as $item)
                                        <option value="{{ $item->id }}"
                                            {{ $winding_direction_of_the_web['value'] != null ? ($item->id == $winding_direction_of_the_web['value'] ? 'selected' : '') : '' }}>
                                            {{ $item->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('winding_direction_of_the_web')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Kode Plate --}}
                    <div class="form-group mt-1">
                        <div class="input-group">
                            <label class="control-label col-12 col-lg-4">Kode Plate</label>
                            <input required type="text" class="form-control col-12 col-lg-8"
                                wire:model="kode_plate" placeholder="..." />
                        </div>
                    </div>
                </div>
                {{-- END PRINTING --}}

                {{-- SEITAI --}}
                <div class="col-12">
                    <p class="text-success">SEITAI</p>
                </div>
                <div class="col-12 col-lg-4">
                    {{-- klasifikasi seal --}}
                    <div class="form-group">
                        <div class="input-group">
                            <label class="control-label col-12 col-lg-6">Klarifikasi Seal</label>
                            <div class="col-12 col-lg-6" wire:ignore>
                                <select data-choices data-choices-sorting="true"
                                    class="form-select @error('seal_classification_id') is-invalid @enderror"
                                    wire:model.live="seal_classification_id">
                                    <option value="">
                                        Silahkan Pilih
                                    </option>
                                    @foreach ($masterKlasifikasiSeal as $item)
                                        <option value="{{ $item->id }}"
                                            {{ $seal_classification_id['value'] != null ? ($item->id == $seal_classification_id['value'] ? 'selected' : '') : '' }}>
                                            {{ $item->name }}
                                        </option>
                                    @endforeach
                                    <option value="lainnya">
                                        Lainnya
                                    </option>
                                </select>
                            </div>
                            @if (($seal_classification_id['value'] ?? '') === 'lainnya')
                                <input required type="text" class="form-control mt-2"
                                    wire:model="custom_seal_classification_id"
                                    placeholder="Masukkan klasifikasi seal lainnya" />
                            @endif

                            @error('seal_classification_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            @error('custom_seal_classification_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group mt-1">
                        <div class="input-group">
                            <label class="control-label col-12 col-lg-6">Jarak Seal dari Pola</label>
                            <input type="number" class="form-control" wire:model="from_seal_design"
                                placeholder="..." min="0" />
                        </div>
                    </div>
                    <div class="form-group mt-1">
                        <div class="input-group">
                            <label class="control-label col-12 col-lg-6">Jarak Seal Bawah</label>
                            <input type="number" class="form-control" wire:model="lower_sealing_length"
                                placeholder="..." min="0" />
                        </div>
                    </div>
                    <div class="form-group mt-1">
                        <div class="input-group">
                            <label class="control-label col-12 col-lg-6">Jumlah Baris Palet</label>
                            <input required type="number" class="form-control" wire:model="palet_jumlah_baris"
                                placeholder="..." min="0" />
                        </div>
                    </div>
                    <div class="form-group mt-1">
                        <div class="input-group">
                            <label class="control-label col-12 col-lg-6">Isi Baris Palet</label>
                            <input required type="number" class="form-control" wire:model="palet_isi_baris"
                                placeholder="..." min="0" />
                        </div>
                    </div>
                    <div class="form-group mt-1">
                        <div class="input-group">
                            <label class="control-label col-12 col-lg-6">Lakban Seitai</label>
                            <div class="col-12 col-lg-6" wire:ignore>
                                <select data-choices data-choices-sorting-false
                                    class="form-select @error('lakbanseitaiid') is-invalid @enderror"
                                    wire:model.live="lakbanseitaiid" placeholder="">
                                    <option value="" selected>
                                        Silahkan Pilih
                                    </option>
                                    @foreach ($masterLakbanSeitai as $item)
                                        <option value="{{ $item->id }}"
                                            {{ $lakbanseitaiid['value'] != null ? ($item->id == $lakbanseitaiid['value'] ? 'selected' : '') : '' }}>
                                            {{ $item->name }}
                                        </option>
                                    @endforeach
                                    <option value="lainnya">
                                        Lainnya
                                    </option>
                                </select>
                            </div>
                            @if (($lakbanseitaiid['value'] ?? '') === 'lainnya')
                                <input required type="text" class="form-control mt-2"
                                    wire:model="custom_lakban_seitai" placeholder="Masukkan lakban seitai lainnya" />
                            @endif
                            @error('lakbanseitaiid')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            @error('custom_lakban_seitai')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-3">
                    {{-- Kode Gaiso --}}
                    <div class="form-group" wire:ignore>
                        <div class="input-group">
                            <label class="control-label col-12 col-lg-3">Kode Gaiso</label>
                            <div class="col-12 col-lg-9" wire:ignore>
                                <select data-choices data-choices-sorting="true" data-choices-search-field
                                    class="form-select @error('pack_gaiso_id') is-invalid @enderror"
                                    wire:model="pack_gaiso_id" placeholder="">
                                    <option value="" selected>
                                        Silahkan Pilih
                                    </option>
                                    @foreach ($masterPackagingGaiso as $item)
                                        <option value="{{ $item->code }}" data-custom-properties='{"code": "{{ $item->name }}"}'
                                            {{ $pack_gaiso_id['value'] != null ? ($item->id == $pack_gaiso_id['value'] ? 'selected' : '') : '' }}>
                                            {{ $item->code }}, {{ $item->box_class == 1 ? 'Khusus' : 'Standar' }},
                                            {{ $item->name }}
                                        </option>
                                    @endforeach`
                                </select>
                                @error('pack_gaiso_id')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="form-group mt-1">
                        <div class="input-group">
                            <label class="control-label col-12 col-lg-3">Kode Box</label>
                            <div class="col-12 col-lg-9" wire:ignore>
                                <select data-choices data-choices-sorting="true" data-choices-search-field
                                    class="form-select @error('pack_box_id') is-invalid @enderror"
                                    wire:model="pack_box_id" placeholder="">
                                    <option value="" selected>
                                        Silahkan Pilih
                                    </option>
                                    @foreach ($masterPackagingBox as $item)
                                        <option value="{{ $item->code }}" data-custom-properties='{"code": "{{ $item->name }}"}'
                                            {{ $pack_box_id['value'] != null ? ($item->id == $pack_box_id['value'] ? 'selected' : '') : '' }}>
                                            {{ $item->code }}, {{ $item->box_class == 1 ? 'Khusus' : 'Standar' }},
                                            {{ $item->name }}
                                        </option>
                                    @endforeach`
                                </select>
                                @error('pack_box_id')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="form-group mt-1">
                        <div class="input-group">
                            <label class="control-label col-12 col-lg-3">Kode Inner</label>
                            <div class="col-12 col-lg-9" wire:ignore>
                                <select data-choices data-choices-sorting="true" data-choices-search-field
                                    class="form-select @error('pack_inner_id') is-invalid @enderror"
                                    wire:model="pack_inner_id" placeholder="">
                                    <option value="" selected>
                                        Silahkan Pilih
                                    </option>
                                    @foreach ($masterPackagingInner as $item)
                                        <option value="{{ $item->code }}" data-custom-properties='{"code": "{{ $item->name }}"}'
                                            {{ $pack_inner_id['value'] != null ? ($item->id == $pack_inner_id['value'] ? 'selected' : '') : '' }}>
                                            {{ $item->code }}, {{ $item->box_class == 1 ? 'Khusus' : 'Standar' }},
                                            {{ $item->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('pack_inner_id')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="form-group mt-1">
                        <div class="input-group">
                            <label class="control-label col-12 col-lg-3">Kode Layer</label>
                            <div class="col-12 col-lg-9" wire:ignore>
                                <select data-choices data-choices-sorting="true" data-choices-search-field
                                    class="form-select @error('pack_layer_id') is-invalid @enderror"
                                    wire:model="pack_layer_id" placeholder="">
                                    <option value="" selected>
                                        Silahkan Pilih
                                    </option>
                                    @foreach ($masterPackagingLayer as $item)
                                        <option value="{{ $item->code }}" data-custom-properties='{"code": "{{ $item->name }}"}'
                                            {{ $pack_layer_id['value'] != null ? ($item->id == $pack_layer_id['value'] ? 'selected' : '') : '' }}>
                                            {{ $item->code }}, {{ $item->box_class == 1 ? 'Khusus' : 'Standar' }},
                                            {{ $item->name }}
                                        </option>
                                    @endforeach`
                                </select>
                                @error('pack_layer_id')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-5">
                    {{-- Isi gaiso --}}
                    <div class="form-group">
                        <div class="input-group" wire:ignore>
                            <label class="control-label col-12 col-lg-1 me-2">Isi</label>
                            <div class="col-12 col-lg-2 me-2">
                                <input required type="number" class="form-control" wire:model="case_gaiso_count"
                                    placeholder="0" />
                            </div>
                            <div class="col-12 col-lg-2 me-2">
                                <select data-choices data-choices-sorting="true"
                                    class="form-control @error('case_gaiso_count_unit') is-invalid @enderror"
                                    wire:model="case_gaiso_count_unit" placeholder="">
                                    <option value="">
                                        Unit
                                    </option>
                                    @foreach ($masterUnit as $item)
                                        <option value="{{ $item->id }}"
                                            {{ $case_gaiso_count_unit['value'] != null ? ($item->id == $case_gaiso_count_unit['value'] ? 'selected' : '') : '' }}>
                                            {{ $item->name }}
                                        </option>
                                    @endforeach`
                                </select>
                                @error('case_gaiso_count_unit')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-12 col-lg-5 me-2">
                                <div class="form-group mt-1">
                                    <div class="input-group">
                                        <label class="control-label col-12 col-lg-6">Stempel</label>
                                        <div class="col-12 col-lg-6">
                                            <input required type="text"
                                                class="form-control @error('case_gaiso_stampel') is-invalid @enderror"
                                                wire:model="case_gaiso_stampel" placeholder="..." />
                                            @error('case_gaiso_stampel')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Isi box --}}
                    <div class="form-group mt-1">
                        <div class="input-group" wire:ignore>
                            <label class="control-label col-12 col-lg-1 me-2">Isi</label>
                            <div class="col-12 col-lg-2 me-2">
                                <input required type="number" class="form-control" wire:model="case_box_count"
                                    placeholder="0" />
                            </div>
                            <div class="col-12 col-lg-2 me-2">
                                <select required data-choices data-choices-sorting="true"
                                    class="form-control @error('case_box_count_unit') is-invalid @enderror"
                                    wire:model="case_box_count_unit" placeholder="">
                                    <option value="" selected>
                                        Unit
                                    </option>
                                    @foreach ($masterUnit as $item)
                                        <option value="{{ $item->id }}"
                                            {{ $case_box_count_unit['value'] != null ? ($item->id == $case_box_count_unit['value'] ? 'selected' : '') : '' }}>
                                            {{ $item->name }}
                                        </option>
                                    @endforeach`
                                </select>
                                @error('case_box_count_unit')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-12 col-lg-5 me-2">
                                <div class="form-group mt-1">
                                    <div class="input-group">
                                        <label class="control-label col-12 col-lg-6">Stempel</label>
                                        <div class="col-12 col-lg-6">
                                            <input required type="text"
                                                class="form-control @error('case_box_stampel') is-invalid @enderror"
                                                wire:model="case_box_stampel" placeholder="..." />
                                            @error('case_box_stampel')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group mt-1">
                        <div class="input-group" wire:ignore>
                            <label class="control-label col-12 col-lg-1 me-2">Isi</label>
                            <div class="col-12 col-lg-2 me-2">
                                <input required type="number" class="form-control" wire:model="case_inner_count"
                                    placeholder="0" />
                            </div>
                            <div class="col-12 col-lg-2 me-2">
                                <select data-choices data-choices-sorting="true"
                                    class="form-control @error('case_inner_count_unit') is-invalid @enderror"
                                    wire:model="case_inner_count_unit" placeholder="">
                                    <option value="" selected>
                                        Unit
                                    </option>
                                    @foreach ($masterUnit as $item)
                                        <option value="{{ $item->id }}"
                                            {{ $case_inner_count_unit['value'] != null ? ($item->id == $case_inner_count_unit['value'] ? 'selected' : '') : '' }}>
                                            {{ $item->name }}
                                        </option>
                                    @endforeach`
                                </select>
                                @error('case_inner_count_unit')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-12 col-lg-5 me-2">
                                <div class="form-group mt-1">
                                    <div class="input-group">
                                        <label class="control-label col-12 col-lg-6">Stempel</label>
                                        <div class="col-12 col-lg-6">
                                            <input required type="text"
                                                class="form-control @error('case_inner_stampel') is-invalid @enderror"
                                                wire:model="case_inner_stampel" placeholder="..." />
                                            @error('case_inner_stampel')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- END SEITAI --}}

                {{-- HAGATA --}}
                <div class="col-12">
                    <p class="text-success">HAGATA</p>
                </div>
                <div class="col-12 col-lg-6">
                    <div class="form-group">
                        <div class="input-group">
                            <label class="control-label col-12 col-lg-4">Tipe Hagata</label>
                            <div class="col-12 col-lg-8" wire:ignore>
                                <select data-choices data-choices-sorting="true"
                                    class="form-select @error('katanuki_id') is-invalid @enderror"
                                    wire:model.live="katanuki_id" placeholder="">
                                    <option value="" selected>
                                        Silahkan Pilih
                                    </option>
                                    @foreach ($masterKatanuki as $item)
                                        <option value="{{ $item->id }}"
                                            {{ $katanuki_id['value'] != null ? ($item->id == $katanuki_id['value'] ? 'selected' : '') : '' }}>
                                            {{ $item->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('katanuki_id')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="form-group mt-1">
                        <div class="input-group">
                            <label class="control-label col-12 col-lg-4">Kode Hagata</label>
                            <input required type="text" class="form-control col-12 col-lg-8"
                                wire:model="kodehagata" placeholder="..." />
                        </div>
                    </div>
                    <div class="form-group mt-1">
                        <div class="input-group">
                            <label class="control-label col-12 col-lg-4">A.</label>
                            <input required type="number" class="form-control col-12 col-lg-8"
                                wire:model="extracted_dimension_a" placeholder="0" />
                        </div>
                    </div>
                    <div class="form-group mt-1">
                        <div class="input-group">
                            <label class="control-label col-12 col-lg-4">B.</label>
                            <input required type="number" class="form-control col-12 col-lg-8"
                                wire:model="extracted_dimension_b" placeholder="0" />
                        </div>
                    </div>
                    <div class="form-group mt-1">
                        <div class="input-group">
                            <label class="control-label col-12 col-lg-4">C.</label>
                            <input required type="number" class="form-control col-12 col-lg-8"
                                wire:model="extracted_dimension_c" placeholder="0" />
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-6 mt-3 d-flex justify-content-center">
                    @if ($photoKatanuki)
                        <img src="{{ asset('storage/' . $photoKatanuki) }}" width="240" height="130"
                            alt="img">
                    @endif
                </div>
                {{-- Catatan Produksi --}}
                <div class="col-12">
                    <div class="form-group mt-1">
                    <div class="input-group">
                        <label class="control-label col-12 col-lg-2">Catatan Produksi</label>
                        <textarea class="form-control" rows="2" placeholder="Catatan Produksi" wire:model="manufacturing_summary"></textarea>
                        @error('manufacturing_summary')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                </div>
                {{-- END HAGATA --}}
            </div>
            <hr />
            <div class="col-lg-12" style="border-top:1px solid #efefef">
                <div class="toolbar">
                    <button id="btnFilter" type="button" class="btn btn-warning w-lg" wire:click="cancel" wire:loading.attr="disabled">
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
                    <button type="button" wire:click="update" class="btn btn-success w-lg" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="update">
                            <i class="ri-save-3-line"></i> Update
                        </span>
                        <div wire:loading wire:target="update">
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
                </div>
            </div>
        </form>
    </div>
</div>
