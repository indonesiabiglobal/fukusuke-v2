<div class="row">
	<div class="col-lg-2"></div>
	<div class="col-lg-8">
		<form wire:submit.prevent="save">
			<div class="form-group">
				<div class="input-group">
					<label class="control-label col-12 col-lg-3 fw-bold text-muted">Tanggal Proses</label>
					<input class="form-control datepicker-input @error('process_date') is-invalid @enderror" type="date" wire:model="process_date" placeholder="yyyy/mm/dd"/ disabled>
					@error('process_date')
						<span class="invalid-feedback">{{ $message }}</span>
					@enderror
				</div>
			</div>
			<div class="form-group mt-1">
				<div class="input-group">
					<label class="control-label col-12 col-lg-3 fw-bold text-muted">PO Number</label>
					<input type="text" class="form-control @error('po_no') is-invalid @enderror" wire:model="po_no" required/>
					@error('po_no')
						<div class="invalid-feedback">
							{{ $message }}
						</div>
					@enderror
				</div>
			</div>
			<div class="form-group mt-1">
				<div class="input-group">
					<label class="control-label col-12 col-lg-3 fw-bold text-muted">Tanggal Order</label>
					<input wire:model="order_date" type="text" class="form-control @error('order_date') is-invalid @enderror" data-provider="flatpickr" data-date-format="d/m/Y">
					@error('order_date')
						<span class="invalid-feedback">{{ $message }}</span>
					@enderror
				</div>
			</div>
			<div class="form-group mt-1">
				<div class="input-group">
					{{-- <button type="button" class="btn btn-info " data-bs-toggle="modal" data-bs-target=".bs-example-modal-xl">Nomor Order</button> --}}

					<label data-bs-toggle="modal" data-bs-target=".bs-example-modal-xl" class="control-label col-12 col-lg-3 fw-bold text-muted" style="text-decoration: underline;">Nomor Order</label>
					<input type="text" class="form-control @error('product_id') is-invalid @enderror" wire:model.live.debounce.300ms="product_id" />
					@error('product_id')
						<span class="invalid-feedback">{{ $message }}</span>
					@enderror
				</div>			
			</div>
			<div class="form-group mt-1">
				<div class="input-group">
					<label class="control-label col-12 col-lg-3 fw-bold text-muted">Nama Produk</label>
					<input type="text" class="form-control readonly"  readonly="readonly" wire:model="product_name" />
				</div>
			</div>
			<div class="form-group mt-1">
				<div class="input-group">
					<label class="control-label col-12 col-lg-3 fw-bold text-muted">Dimensi</label>
					<input type="text" class="form-control readonly"  readonly="readonly" wire:model="dimensi" />
				</div>
			</div>
			<div class="form-group mt-1">
				<div class="input-group">
					<label class="control-label col-12 col-lg-3 fw-bold text-muted">Jumlah Order</label>
					<input type="text" class="form-control @error('order_qty') is-invalid @enderror" wire:model="order_qty" />
					@error('order_qty')
						<span class="invalid-feedback">{{ $message }}</span>
					@enderror
				</div>
			</div>
			<div class="form-group mt-1">
				<div class="input-group">
					<label class="control-label col-12 col-lg-3 fw-bold text-muted">Unit</label>
					<select class="form-control @error('unit_id') is-invalid @enderror" wire:model="unit_id" placeholder="" required>
						<option value="0">Set</option>
						<option value="1">Lembar</option>
						<option value="2">Meter</option>
					</select>
					@error('unit_id')
						<span class="invalid-feedback">
							<strong>{{ $message }}</strong>
						</span>
					@enderror
				</div>
			</div>
			<div class="form-group mt-1">
				<div class="input-group">
					<label class="control-label col-12 col-lg-3 fw-bold text-muted">Tanggal Stuffing</label>
					<input class="form-control datepicker-input @error('stufingdate') is-invalid @enderror" type="text" wire:model="stufingdate" data-provider="flatpickr" data-date-format="d/m/Y" placeholder="yyyy/mm/dd"/>
					@error('stufingdate')
						<span class="invalid-feedback">{{ $message }}</span>
					@enderror
				</div>
			</div>
			<div class="form-group mt-1">
				<div class="input-group">
					<label class="control-label col-12 col-lg-3 fw-bold text-muted">ETD</label>
					<input class="form-control datepicker-input @error('etddate') is-invalid @enderror" type="text" wire:model="etddate" data-provider="flatpickr" data-date-format="d/m/Y" placeholder="yyyy/mm/dd"/>
					@error('etddate')
						<span class="invalid-feedback">{{ $message }}</span>
					@enderror
				</div>
			</div>
			<div class="form-group mt-1">
				<div class="input-group">
					<label class="control-label col-12 col-lg-3 fw-bold text-muted">ETA</label>
					<input class="form-control datepicker-input @error('etadate') is-invalid @enderror" type="text" wire:model="etadate" data-provider="flatpickr" data-date-format="d/m/Y" placeholder="yyyy/mm/dd"/>
					@error('etadate')
						<span class="invalid-feedback">{{ $message }}</span>
					@enderror
				</div>
			</div>
			
			<div class="row mt-1">
				<div class="col-12 col-lg-3">
					<label class="form-label text-muted fw-bold">Buyer</label>
				</div>
				<div class="col-12 col-lg-9">
					<div wire:ignore>
						<select class="form-control col-12 col-lg-3 @error('buyer_id') is-invalid @enderror" wire:model="buyer_id" placeholder="" data-choices data-choices-sorting-false data-choices-removeItem>						
							{{-- @foreach ($buyer as $item)
							<option value="{{ $item->id }}" {{ $item->id == $buyer_id['value'] ? 'selected' : '' }}>
								{{ $item->name }}
							</option>                      
							@endforeach --}}
						</select>
						@error('buyer_id')
							<span class="invalid-feedback">{{ $message }}</span>
						@enderror
					</div>
				</div>
			</div>
			<hr />
			<div class="col-lg-12">
				<div class="toolbar">
					<button type="button" class="btn btn-warning w-lg" wire:click="cancel">
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

					@if ($status_order == 0)
						<button type="button" class="btn btn-danger w-lg" href="#removeMemberModal" data-bs-toggle="modal" data-bs-target="#removeMemberModal" data-remove-id="12">
							<i class="ri-delete-bin-line"></i> Delete
						</button>

						<button id="btnCreate" type="submit" class="btn btn-success w-lg">
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

						<button type="button" class="btn btn-success btn-print w-lg" wire:click="print">
							<i class="bx bx-printer"></i> Print
						</button>				
					@endif
					@if ($status_order == 1)
						<p class="text-secondary mb-0">Data sudah di LPK ! ..</p>
					@endif
					
					{{-- <script>
						document.addEventListener('livewire:load', function () {
							Livewire.on('redirectToPrint', function (data) {
								var printUrl = '{{ route('cetak-order') }}?processdate=' +  data.processdate + 
								'&po_no=' + data.po_no +
								'&order_date=' + data.order_date +
								'&code=' + data.code +
								'&name=' + data.name +
								'&dimensi=' + data.dimensi +
								'&order_qty=' + data.order_qty +
								'&stufingdate=' + data.stufingdate +
								'&etddate=' + data.etddate +
								'&etadate=' + data.etadate +
								'&namabuyer=' + data.namabuyer;
								window.open(printUrl, '_blank');
							});
						});
					</script> --}}
				</div>
			</div>
			<!--  Extra Large modal example -->
			<div class="modal fade bs-example-modal-xl" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
				<div class="modal-dialog modal-xl">
					<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title" id="myExtraLargeModalLabel">Extra large
								modal</h5>
							<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
						</div>
						<div class="modal-body">
							<div class="col-lg-12">
								<div class="row">
									<div class="col-12 col-lg-6">
										<div class="form-group">
											<div class="input-group">
												<label class="control-label col-12 col-lg-4">Nomor Order</label>
												<input type="text" class="form-control col-12 col-lg-8" wire:model="code"
													placeholder="KODE" />
												@error('code')
													<span class="invalid-feedback">{{ $message }}</span>
												@enderror
											</div>
										</div>
										<div class="form-group mt-1">
											<div class="input-group">
												<label class="control-label col-12 col-lg-4">Nama Produk</label>
												<input type="text" class="form-control col-12 col-lg-8" wire:model="name"
													placeholder="nama" />
												@error('name')
													<span class="invalid-feedback">{{ $message }}</span>
												@enderror
											</div>
										</div>
										<div class="form-group mt-1">
											<div class="input-group">
												<label class="control-label col-12 col-lg-4">Kode Tipe</label>
												<input type="text" class="form-control col-12 col-lg-8" wire:model="product_type_code"
													placeholder="nama" />
												@error('product_type_code')
													<span class="invalid-feedback">{{ $message }}</span>
												@enderror
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
									</div>
									<div class="col-12 col-lg-6">
										<div class="form-group">
											<div class="input-group">
												<label class="control-label col-12 col-lg-4">Dimensi (T x L x P)</label>
												<span class="input-group-text">
													T
												</span>
												<input type="text" class="form-control" wire:model="ketebalan" placeholder="Tebal" />
												@error('ketebalan')
													<span class="invalid-feedback">{{ $message }}</span>
												@enderror
												<span class="input-group-text">
													L
												</span>
												<input type="text" class="form-control" wire:model="diameterlipat" placeholder="Lebar" />
												@error('diameterlipat')
													<span class="invalid-feedback">{{ $message }}</span>
												@enderror
												<span class="input-group-text">
													P
												</span>
												<input type="text" class="form-control" wire:model="productlength"
													placeholder="Panjang" />
												@error('productlength')
													<span class="invalid-feedback">{{ $message }}</span>
												@enderror
											</div>
										</div>
										<div class="form-group mt-1">
											<div class="input-group">
												<label class="control-label col-12 col-lg-4">Berat Satuan</label>
												<input type="text" class="form-control col-12 col-lg-8" wire:model="unit_weight"
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
												<div class="col-12 col-lg-8">
													<select data-choices data-choices-sorting="true"
														class="form-select @error('product_unit') is-invalid @enderror"
														wire:model="product_unit" placeholder="">
														<option value="" selected>
															Silahkan Pilih
														</option>
														<option value="1">
															Lembar
														</option>
														<option value="2">
															Meter
														</option>
														<option value="3">
															Set
														</option>
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
												<input type="text" class="form-control" wire:model="ketebalan" placeholder="Tebal" />
												<span class="input-group-text">
													x
												</span>
												<input type="text" class="form-control" wire:model="diameterlipat" placeholder="Lebar" />
												@error('diameterlipat')
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
												<input type="text" class="form-control col-12 col-lg-8"
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
												<div class="col-12 col-lg-8">
													<select data-choices data-choices-sorting="true"
														class="form-select col-12 col-lg-8 @error('material_classification') is-invalid @enderror"
														wire:model="material_classification" placeholder="">
														<option value="" selected>
															Silahkan Pilih
														</option>
														@foreach (\App\Models\MsMaterial::select('id', 'name')->get() as $item)
															<option value="{{ $item->id }}">
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
												<div class="col-12 col-lg-8">
													<select data-choices data-choices-sorting="true"
														class="form-select @error('embossed_classification') is-invalid @enderror"
														wire:model="embossed_classification" placeholder="">
														<option value="" selected>
															Silahkan Pilih
														</option>
														<option value="0">
															Tidak Ada
														</option>
														<option value="1">
															Ada
														</option>
													</select>
													@error('embossed_classification')
														<span class="invalid-feedback">{{ $message }}</span>
													@enderror
												</div>
											</div>
										</div>
										<div class="form-group mt-1">
											<div class="input-group">
												<label class="control-label col-12 col-lg-4">Corona</label>
												<input type="text" class="form-control col-12 col-lg-8" wire:model="product_unit"
													placeholder="Pilih" />
												@error('product_unit')
													<span class="invalid-feedback">{{ $message }}</span>
												@enderror
											</div>
										</div>
									</div>
									<div class="col-12 col-lg-6">
										<div class="form-group">
											<div class="input-group">
												<label class="control-label col-12 col-lg-5">MB -1 (Master Batch) </label>
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
												<div class="col-12 col-lg-8">
													<select data-choices data-choices-sorting="true"
														class="form-select @error('gentan_classification') is-invalid @enderror"
														wire:model="gentan_classification" placeholder="">
														<option value="" selected>
															Silahkan Pilih
														</option>
														{{-- @foreach ($masterGentanClassifications as $item)
															<option value="{{ $item->id }}">
																{{ $item->name }}
															</option>
														@endforeach --}}
													</select>
													@error('gentan_classification')
														<span class="invalid-feedback">{{ $message }}</span>
													@enderror
												</div>
											</div>
										</div>
										<div class="form-group mt-1">
											<div class="input-group">
												<label class="control-label col-12 col-lg-4">Gazette</label>
												<div class="col-12 col-lg-8">
													<select data-choices data-choices-sorting="true"
														class="form-select @error('gazette_classification') is-invalid @enderror"
														wire:model="gazette_classification" placeholder="">
														<option value="" selected>
															Silahkan Pilih
														</option>
														{{-- @foreach ($masterGazetteClassifications as $item)
															<option value="{{ $item->id }}">
																{{ $item->name }}
															</option>
														@endforeach --}}
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
												<span class="input-group-text">
													A
												</span>
												<input type="text" class="form-control col-12 col-lg-8"
													wire:model="gazette_dimension_a" placeholder="0" />
					
												<span class="input-group-text">
													B
												</span>
												<input type="text" class="form-control col-12 col-lg-8"
													wire:model="gazette_dimension_b" placeholder="0" />
											</div>
										</div>
										<div class="form-group mt-1">
											<div class="input-group">
												<label class="control-label col-12 col-lg-4">-</label>
												<span class="input-group-text">
													C
												</span>
												<input type="text" class="form-control col-12 col-lg-8"
													wire:model="gazette_dimension_c" placeholder="0" />
					
												<span class="input-group-text">
													D
												</span>
												<input type="text" class="form-control col-12 col-lg-8"
													wire:model="gazette_dimension_d" placeholder="0" />
											</div>
										</div>
									</div>
									<div class="col-12 col-lg-6 mt-3 d-flex justify-content-center">
										<img src="{{ asset('asset/image/Gazette-ent.png') }}" width="240" height="130"
											alt="img">
									</div>
									<div class="col-12">
										<p class="text-success">HAGATA</p>
									</div>
									<div class="col-12 col-lg-6">
										<div class="form-group">
											<div class="input-group">
												<label class="control-label col-12 col-lg-4">Kode Nukigata</label>
												<div class="col-12 col-lg-8" wire:ignore>
													<select data-choices data-choices-sorting="true"
														class="form-select @error('katanuki_id') is-invalid @enderror"
														wire:model.live="katanuki_id" placeholder="">
														<option value="" selected>
															Silahkan Pilih
														</option>
														{{-- @foreach ($masterKatanuki as $item)
															<option value="{{ $item->id }}">
																{{ $item->name }}
															</option>
														@endforeach --}}
													</select>
													@error('katanuki_id')
														<span class="invalid-feedback">{{ $message }}</span>
													@enderror
												</div>
											</div>
										</div>
										<div class="form-group mt-1">
											<div class="input-group">
												<label class="control-label col-12 col-lg-4">A.</label>
												<input type="number" class="form-control col-12 col-lg-8"
													wire:model="extracted_dimension_a" placeholder="0" />
											</div>
										</div>
										<div class="form-group mt-1">
											<div class="input-group">
												<label class="control-label col-12 col-lg-4">B.</label>
												<input type="number" class="form-control col-12 col-lg-8"
													wire:model="extracted_dimension_b" placeholder="0" />
											</div>
										</div>
										<div class="form-group mt-1">
											<div class="input-group">
												<label class="control-label col-12 col-lg-4">C.</label>
												<input type="number" class="form-control col-12 col-lg-8"
													wire:model="extracted_dimension_c" placeholder="0" />
											</div>
										</div>
									</div>
									<div class="col-12 col-lg-6 mt-3 d-flex justify-content-center">
										{{-- @if ($photoKatanuki)
											<img src="{{ asset('storage/' . $photoKatanuki) }}" width="240" height="130"
												alt="img">
										@endif --}}
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
												<input type="number" class="form-control" wire:model="number_of_color"
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
												<input type="text" class="form-control" wire:model="back_color_number"
													placeholder="Pilih" />
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
														{{-- @foreach ($masterPrintType as $item)
															<option value="{{ $item->id }}">
																{{ $item->name }}
															</option>
														@endforeach --}}
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
														{{-- @foreach ($masterInkCharacteristics as $item)
															<option value="{{ $item->id }}">
																{{ $item->name }}
															</option>
														@endforeach --}}
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
														{{-- @foreach ($masterEndlessPrinting as $item)
															<option value="{{ $item->id }}">
																{{ $item->name }}
															</option>
														@endforeach --}}
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
														{{-- @foreach ($masterArahGulung as $item)
															<option value="{{ $item->id }}">
																{{ $item->name }}
															</option>
														@endforeach --}}
													</select>
													@error('winding_direction_of_the_web')
														<span class="invalid-feedback">{{ $message }}</span>
													@enderror
												</div>
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
												<div class="col-12 col-lg-6" wire:ignore>
													<select data-choices data-choices-sorting="true"
														class="form-select @error('seal_classification') is-invalid @enderror"
														wire:model="seal_classification" placeholder="">
														<option value="" selected>
															Silahkan Pilih
														</option>
														{{-- @foreach ($masterKlasifikasiSeal as $item)
															<option value="{{ $item->id }}">
																{{ $item->name }}
															</option>
														@endforeach --}}
													</select>
													@error('seal_classification')
														<span class="invalid-feedback">{{ $message }}</span>
													@enderror
												</div>
											</div>
										</div>
										<div class="form-group mt-1">
											<div class="input-group">
												<label class="control-label col-12 col-lg-6">Jarak Seal dari Pola</label>
												<input type="number" class="form-control" wire:model="from_seal_design" placeholder="..." min="0" />
											</div>
										</div>
										<div class="form-group mt-1">
											<div class="input-group">
												<label class="control-label col-12 col-lg-6">Jarak Seal Bawah</label>
												<input type="number" class="form-control" wire:model="lower_sealing_length" placeholder="..." min="0" />
											</div>
										</div>
										<div class="form-group mt-1">
											<div class="input-group">
												<label class="control-label col-12 col-lg-6">Jumlah Baris Palet</label>
												<input type="number" class="form-control" wire:model="palet_jumlah_baris" placeholder="..." min="0" />
											</div>
										</div>
										<div class="form-group mt-1">
											<div class="input-group">
												<label class="control-label col-12 col-lg-6">Isi Baris Palet</label>
												<input type="number" class="form-control" wire:model="palet_isi_baris" placeholder="..." min="0" />
											</div>
										</div>
									</div>
									<div class="col-12 col-lg-5">
										<div class="form-group">
											<div class="input-group">
												<label class="control-label col-12 col-lg-3">Kode Gasio</label>
												<input type="number" class="form-control" wire:model="ketebalan" placeholder="..." min="0" />
											</div>
										</div>
										<div class="form-group mt-1">
											<div class="input-group">
												<label class="control-label col-12 col-lg-3">Kode Box</label>
												<input type="number" class="form-control" wire:model="ketebalan" placeholder="..." min="0" />
											</div>
										</div>
										<div class="form-group mt-1">
											<div class="input-group">
												<label class="control-label col-12 col-lg-3">Kode Inner</label>
												<input type="number" class="form-control" wire:model="ketebalan" placeholder="..." min="0" />
											</div>
										</div>
										<div class="form-group mt-1">
											<div class="input-group">
												<label class="control-label col-12 col-lg-3">Kode Layer</label>
												<input type="number" class="form-control" wire:model="ketebalan" placeholder="..." min="0" />
											</div>
										</div>
										<div class="form-group mt-1">
											<div class="input-group">
												<label class="control-label col-12 col-lg-3">Kode Gasio</label>
												<textarea name="" id="" cols="30" rows="5"></textarea>
											</div>
										</div>
									</div>
									<div class="col-12 col-lg-3">
										<div class="form-group">
											<div class="input-group">
												<label class="control-label col-12 col-lg-2">Isi</label>
												<input type="text" class="form-control" wire:model="ketebalan" placeholder="0" />
												<input type="text" class="form-control" wire:model="ketebalan" placeholder="Unit" />
											</div>
										</div>
										<div class="form-group mt-1">
											<div class="input-group">
												<label class="control-label col-12 col-lg-2">Isi</label>
												<input type="text" class="form-control" wire:model="ketebalan" placeholder="0" />
												<input type="text" class="form-control" wire:model="ketebalan" placeholder="Unit" />
											</div>
										</div>
										<div class="form-group mt-1">
											<div class="input-group">
												<label class="control-label col-12 col-lg-2">Isi</label>
												<input type="text" class="form-control" wire:model="ketebalan" placeholder="0" />
												<input type="text" class="form-control" wire:model="ketebalan" placeholder="Unit" />
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="modal-footer">
							<a href="javascript:void(0);" class="btn btn-light link-success fw-medium" data-bs-dismiss="modal"><i class="ri-close-line me-1 align-middle"></i> Close</a>
						</div>
					</div><!-- /.modal-content -->
				</div><!-- /.modal-dialog -->
			</div><!-- /.modal -->

			<!-- start delete modal -->
			<div id="removeMemberModal" class="modal fade zoomIn" tabindex="-1" aria-hidden="true">
				<div class="modal-dialog modal-dialog-centered">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="close-removeMemberModal"></button>
						</div>
						<div class="modal-body">
							<div class="mt-2 text-center">
								<lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop" colors="primary:#f7b84b,secondary:#f06548" style="width:100px;height:100px"></lord-icon>
								<div class="mt-4 pt-2 fs-15 mx-4 mx-sm-5">
									<h4>Are you sure ?</h4>
									<p class="text-muted mx-4 mb-0">Are you sure you want to remove this order ?</p>
								</div>
							</div>
							<div class="d-flex gap-2 justify-content-center mt-4 mb-2">
								<button type="button" class="btn w-sm btn-light" data-bs-dismiss="modal">Close</button>
								<button type="button" class="btn w-sm btn-danger" id="remove-item" wire:click="delete">Yes, Delete It!</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</form>        
	</div>
	<div class="col-lg-2"></div>
</div>
