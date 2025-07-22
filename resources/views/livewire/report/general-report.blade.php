<div class="row">
    <div class="col-lg-2"></div>
    <div class="col-lg-6">
        <div class="form-group">
            <label class="control-label col-md-4 col-xs-12">Tanggal Periode</label>
            <div class="col-12 mt-1">
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-addon col-12 col-lg-2 text-muted">Awal: </span>
                        <input wire:model.defer="tglMasuk" type="text" class="form-control" style="padding:0.44rem"
                            data-provider="flatpickr" data-date-format="Y-m-d">
                        <span class="input-group-text py-0">
                            <i class="ri-calendar-event-fill fs-4"></i>
                        </span>

                        <select wire:ignore wire:model.defer="jamMasuk" class="form-control"
                            placeholder="- pilih jam kerja -">
                            @foreach ($workingShiftHour as $item)
                                <option value="{{ $item->work_hour_from }}">{{ $item->work_hour_from }}</option>
                            @endforeach
                        </select>
                        <span class="input-group-text py-0">
                            <i class="ri-time-line fs-4"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group mt-1">
            <div class="col-12">
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-addon col-12 col-lg-2 text-muted">Akhir: </span>
                        <input wire:model.defer="tglKeluar" type="text" class="form-control" style="padding:0.44rem"
                            data-provider="flatpickr" data-date-format="Y-m-d">
                        <span class="input-group-text py-0">
                            <i class="ri-calendar-event-fill fs-4"></i>
                        </span>

                        <select wire:ignore wire:model.defer="jamKeluar" class="form-control"
                            placeholder="- pilih jam kerja -">
                            @foreach ($workingShiftHour as $item)
                                <option value="{{ $item->work_hour_till }}">{{ $item->work_hour_till }}</option>
                            @endforeach
                        </select>
                        {{-- <input wire:model.defer="jamKeluar" type="text" class="form-control" data-provider="timepickr" data-time-hrs="true" id="timepicker-24hrs"> --}}
                        <span class="input-group-text py-0">
                            <i class="ri-time-line fs-4"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group mt-1">
            <div class="input-group">
                <span class="input-group-addon col-12 col-lg-3">Nippo </span>
                <select class="form-control" wire:model.live="nipon" placeholder="- pilih -">
                    <option value="Infure" selected="selected">Infure</option>
                    <option value="Seitai">Seitai</option>
                </select>
                {{-- <select id="nippo" class="form-control" placeholder="- pilih jenis report -">
					<option value="1">Infure</option>
					<option value="2">Seitai</option>
				</select> --}}
            </div>
        </div>
        <div class="form-group mt-1">
            <div class="input-group">
                <span class="input-group-addon col-12 col-lg-3">Jenis Report </span>
                <select class="form-control" wire:model.defer="jenisreport" placeholder="- pilih jenis report -">
                    <option value="0">- pilih jenis report -</option>
                    <optgroup label="Daftar Produksi">
                        <option value="Daftar Produksi Per Mesin" selected="selected">1. Daftar Produksi Per Mesin
                        </option>
                        <option value="Daftar Produksi Per Tipe Per Mesin">2. Daftar Produksi Per Tipe Per Mesin
                        </option>
                        <option value="Daftar Produksi Per Jenis">3. Daftar Produksi Per Jenis</option>
                        <option value="Daftar Produksi Per Tipe">4. Daftar Produksi Per Tipe</option>
                        <option value="Daftar Produksi Per Produk">5. Daftar Produksi Per Produk</option>
                        <option value="Daftar Produksi Per Departemen Per Jenis">6. Daftar Produksi Per Departemen Per
                            Jenis</option>
                        <option value="Daftar Produksi Per Departemen & Tipe">7. Daftar Produksi Per Departemen & Tipe
                        </option>
                        <option value="Daftar Produksi Per Departemen & Petugas">8. Daftar Produksi Per Departemen &
                            Petugas</option>
                            <option value="Daftar Produksi Per Palet"
                            @if ($nipon == 'Infure')
                            disabled
                            @endif
                            >
                                9. Daftar Produksi Per Palet
                            </option>
                            <option value="Daftar Produksi Per Mesin Per Produk">10. Daftar Produksi Per Mesin Per Produk</option>
                    </optgroup>
                    <optgroup label="Daftar Loss">
                        <option value="Daftar Loss Per Departemen">11. Daftar Loss Per Departemen</option>
                        <option value="Daftar Loss Per Departemen & Jenis">12. Daftar Loss Per Departemen & Jenis
                        </option>
                        <option value="Daftar Loss Per Petugas">13. Daftar Loss Per Petugas</option>
                        <option value="Daftar Loss Per Mesin">14. Daftar Loss Per Mesin</option>
                    </optgroup>
                    <optgroup label="Kapasitas">
                        <option value="Kapasitas Produksi">15. Kapasitas Produksi</option>
                    </optgroup>
                    <optgroup label="Jam Mati Mesin">
                        <option value="Jam Mati Per Mesin">16. Jam Mati Per Mesin</option>
                        <option value="Jam Mati Per Jenis">17. Jam Mati Per Jenis</option>
                    </optgroup>
                </select>
            </div>
        </div>
        <hr />
        <div class="form-group">
            <button type="button" wire:loading.attr="disabled"  class="btn btn-success btn-print" wire:click="export" style="width:99%">
                <span wire:loading.remove wire:target="export">
                    <i class="ri-printer-line"></i> Generate Report
                </span>
                <div wire:loading wire:target="export">
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
    <div class="col-lg-4"></div>
</div>
