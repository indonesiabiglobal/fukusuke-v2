<div>
    <div class="row">
        <div class="col-12">
            <div id="mobileDebugLog" style="display:none; background:#000; color:#0f0; padding:15px; margin:10px 0; font-family:monospace; font-size:12px; max-height:300px; overflow-y:auto; border-radius:5px; position:sticky; top:0; z-index:1000;">
                <div style="display:flex; justify-content:space-between; margin-bottom:10px;">
                    <strong style="color:#ff0;">🔍 DEBUG LOG</strong>
                    <button onclick="clearDebugLog()" style="background:#f00; color:#fff; border:none; padding:5px 10px; border-radius:3px;">Clear</button>
                </div>
                <div id="mobileLogContent"></div>
            </div>
        </div>
    </div>

    <div class="row">
        <form wire:submit.prevent="save">
            <div class="row mt-2">
                <div class="col-12">
                    <div class="row">
                        <div class="col-12 col-lg-6">
                            <div class="form-group">
                                <div class="input-group">
                                    <label class="control-label col-5 pe-2">Tanggal Produksi</label>
                                    <input required
                                        class="form-control @error('production_date') is-invalid @enderror"
                                        type="date"
                                        wire:model.live="production_date"
                                        max="{{ now()->format('Y-m-d') }}"
                                        value="{{ \Carbon\Carbon::parse($production_date)->format('Y-m-d') }}" />

                                    <span class="input-group-text py-0">
                                        <i class="ri-calendar-event-fill fs-4"></i>
                                    </span>
                                    @error('production_date')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6 d-none d-lg-block">
                            <div class="form-group">
                                <div class="input-group">
                                    <label class="control-label col-5 pe-2">Tanggal Proses</label>
                                    <input class="form-control bg-light @error('created_on') is-invalid @enderror"
                                        readonly="readonly" disabled type="text" style="padding:0.44rem"
                                        data-provider="flatpickr" data-date-format="d/m/Y" wire:model.defer="created_on"
                                        placeholder="yyyy/mm/dd" />
                                    <span class="input-group-text py-0">
                                        <i class="ri-calendar-event-fill fs-4"></i>
                                    </span>
                                    @error('created_on')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-4 mt-1">
                            <div class="form-group">
                                <div class="input-group">
                                    <label class="control-label col-5 pe-2 fw-bold text-muted"
                                        style="text-decoration: underline;">
                                        <a href="#" data-bs-toggle="modal" wire:click="showModalLPK"
                                            class="text-muted">
                                            Nomor LPK
                                        </a>
                                    </label>
                                    <div x-data="{ lpk_no: @entangle('lpk_no').live, status: true }"
                                        x-init="$nextTick(() => $refs.lpkInput.focus())"> <!-- ⭐ TAMBAHKAN x-init ini -->
                                        <input class="form-control @error('lpk_no') is-invalid @enderror"
                                            style="padding:0.44rem" type="text" placeholder="000000-000"
                                            x-model="lpk_no"
                                            x-ref="lpkInput"
                                            maxlength="10"
                                            x-on:keydown.tab="$event.preventDefault(); document.querySelector('[x-ref=machineInput]')?.focus();"
                                            x-init="$watch('lpk_no', value => {
                                                let cleanValue = value.replace(/-/g, '');

                                                if (cleanValue.length >= 6) {
                                                    lpk_no = cleanValue.substring(0, 6) + '-' + cleanValue.substring(6, 9);
                                                    status = false;
                                                } else {
                                                    lpk_no = cleanValue;
                                                    status = true;
                                                }

                                                if (lpk_no.length > 10) {
                                                    lpk_no = lpk_no.substring(0, 10);
                                                }

                                                if (lpk_no.length === 10) {
                                                    $wire.processLpkNo(lpk_no);
                                                }
                                            })" />
                                    </div>
                                    @error('lpk_no')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Polling HANYA untuk mobile/tablet -->
                        {{-- <div wire:poll.500ms="checkLpkProcessed" class="d-lg-none" style="display:none;"></div> --}}

                        <script>
                            document.addEventListener('livewire:initialized', () => {
                                // ⭐ GANTI dengan event listener yang lebih reliable
                                Livewire.on('lpk-processed', () => {
                                    // Detect device
                                    let isMobile = window.innerWidth < 992;

                                    setTimeout(() => {
                                        if (isMobile) {
                                            // Mobile: cari input dengan wire:model.defer
                                            let input = document.querySelector('input[wire\\:model\\.defer="machineno"]');
                                            if (input) {
                                                input.scrollIntoView({ block: 'center' });
                                                setTimeout(() => {
                                                    input.focus();
                                                    input.click();
                                                }, 300);
                                            }
                                        } else {
                                            // Desktop: gunakan x-ref
                                            let input = document.querySelector('[x-ref="machineInput"]');
                                            if (input) {
                                                input.focus();
                                            }
                                        }
                                    }, 200);
                                });
                            });
                        </script>
                        <div class="col-12 col-lg-4 mt-1 d-none d-lg-block">
                            <div class="form-group">
                                <div class="input-group">
                                    <label class="control-label pe-2">Tanggal LPK</label>
                                    <input
                                        class="form-control readonly datepicker-input bg-light @error('lpk_date') is-invalid @enderror"
                                        readonly="readonly" type="text" style="padding:0.44rem"
                                        wire:model.defer="lpk_date" placeholder="yyyy/mm/dd" />
                                    <span class="input-group-text py-0">
                                        <i class="ri-calendar-event-fill fs-4"></i>
                                    </span>
                                    @error('lpk_date')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-4 mt-1 d-none d-lg-block">
                            <div class="form-group">
                                <div class="input-group">
                                    <label class="control-label pe-2">Panjang LPK</label>
                                    <input type="text" placeholder="-"
                                        class="form-control readonly bg-light @error('panjang_lpk') is-invalid @enderror"
                                        readonly="readonly" wire:model="panjang_lpk" />
                                    <span class="input-group-text">
                                        m
                                    </span>
                                    @error('panjang_lpk')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-4 mt-1">
                            <div class="form-group">
                                <div class="input-group">
                                    <label class="control-label col-5 pe-2 fw-bold text-muted"
                                        style="text-decoration: underline;">
                                        <a href="#" data-bs-toggle="modal" wire:click="showModalNoOrder"
                                            class="text-muted">
                                            <span class="d-none d-lg-inline">Nomor Order</span>
                                            <span class="d-lg-none">Nama Produk</span>
                                        </a>
                                    </label>
                                    <input type="text" placeholder="-"
                                        class="form-control readonly bg-light @error('code') is-invalid @enderror"
                                        readonly="readonly" wire:model="code" />
                                    @error('code')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-lg-4 d-lg-none">
                            <div class="form-group">
                                <label class="form-label mb-1">
                                    <small class="text-muted">Total / Selisih</small>
                                </label>
                                <div class="input-group">
                                    <input type="text" placeholder="0"
                                        class="form-control readonly bg-light @error('total_assembly_line') is-invalid @enderror"
                                        readonly="readonly" value="{{ number_format($total_assembly_line) }}" />
                                    <span class="input-group-text">
                                        /
                                    </span>
                                    <input type="text" placeholder="0"
                                        class="form-control readonly bg-light @error('selisih') is-invalid @enderror"
                                        readonly="readonly" wire:model="selisih" />
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-lg-8 mt-1 d-none d-lg-block">
                            <div class="form-group">
                                <div class="input-group">
                                    <label class="control-label"></label>
                                    <input type="text" placeholder="-"
                                        class="form-control readonly bg-light @error('name') is-invalid @enderror"
                                        readonly="readonly" wire:model="name" />
                                    @error('name')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <!-- MOBILE VERSION -->
                        <div class="d-lg-none">
                            <!-- Nomor Mesin Mobile -->
                            <div class="col-12 mt-1">
                                <div class="form-group">
                                    <div class="input-group">
                                        <label class="control-label col-5">Nomor Mesin</label>
                                        <input type="text" placeholder=" ... "
                                            class="form-control @error('machineno') is-invalid @enderror"
                                            wire:model.defer="machineno"
                                            x-on:blur="$wire.validateMachine()"
                                            x-on:keydown.enter="$wire.validateMachine(); $event.preventDefault(); document.querySelector('[x-ref=employeeno]').focus();"
                                            x-on:keydown.tab="$event.preventDefault(); document.querySelector('[x-ref=employeeno]').focus();" />
                                        @error('machineno')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                        <input type="text" placeholder="-" class="form-control readonly bg-light"
                                            readonly="readonly" wire:model="machinename" />
                                    </div>
                                </div>
                            </div>

                            <!-- Petugas Mobile -->
                            <div class="col-12 mt-1">
                                <div class="form-group">
                                    <div class="input-group">
                                        <label class="control-label col-5">Petugas</label>
                                        <input type="text" placeholder=" ... "
                                            class="form-control @error('employeeno') is-invalid @enderror"
                                            wire:model.defer="employeeno"
                                            x-ref="employeeno"
                                            x-on:blur="$wire.validateEmployee()"
                                            x-on:keydown.enter="$wire.validateEmployee(); $event.preventDefault(); document.querySelector('[x-ref=nomor_barcode]').focus();"
                                            x-on:keydown.tab="$event.preventDefault(); document.querySelector('[x-ref=nomor_barcode]').focus();" />
                                        @error('employeeno')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                        <input type="text" placeholder="-"
                                            class="form-control readonly bg-light @error('empname') is-invalid @enderror"
                                            readonly="readonly" wire:model="empname" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- DESKTOP VERSION - Tetap pakai wire:model.change -->
                        <div class="d-none d-lg-block">
                            <div class="row">
                                <!-- Nomor Mesin Desktop -->
                                <div class="col-12 col-lg-4 mt-1">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <label class="control-label col-5 pe-2">Nomor Mesin</label>
                                            <input type="text" placeholder=" ... "
                                                class="form-control @error('machineno') is-invalid @enderror"
                                                wire:model.defer="machineno"
                                                x-ref="machineInput"
                                                x-on:blur="$wire.validateMachine()"
                                                x-on:keydown.enter="$wire.validateMachine(); $event.preventDefault(); $refs.employeeno.focus();"
                                                x-on:keydown.tab="$event.preventDefault(); $refs.employeeno.focus();" />
                                            @error('machineno')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-lg-8 mt-1">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <label class="control-label"></label>
                                            <input type="text" placeholder="-" class="form-control readonly bg-light"
                                                readonly="readonly" wire:model="machinename" />
                                        </div>
                                    </div>
                                </div>

                                <!-- Petugas Desktop -->
                                <div class="col-12 col-lg-4 mt-1">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <label class="control-label col-5 pe-2">Petugas</label>
                                            <input type="text" placeholder=" ... "
                                                class="form-control @error('employeeno') is-invalid @enderror"
                                                wire:model.defer="employeeno"
                                                x-ref="employeeno"
                                                x-on:blur="$wire.validateEmployee()"
                                                x-on:keydown.enter="$wire.validateEmployee(); $event.preventDefault(); $refs.nomor_barcode.focus();"
                                                x-on:keydown.tab="$event.preventDefault(); $refs.nomor_barcode.focus();" />
                                            @error('employeeno')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-lg-8 mt-1">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <label class="control-label"></label>
                                            <input type="text" placeholder="-"
                                                class="form-control readonly bg-light @error('empname') is-invalid @enderror"
                                                readonly="readonly" wire:model="empname" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- Nomor Barcode --}}
                        <div class="col-12 col-lg-4 mt-1">
                            <div class="form-group">
                                <div class="input-group">
                                    <label class="control-label col-5 pe-2">Nomor Barcode</label>
                                    <div x-data="{
                                        nomor_barcode: @entangle('nomor_barcode'),
                                        handleInput(value) {
                                            // Hapus semua karakter non-alphanumeric
                                            let cleanValue = value.replace(/[^a-zA-Z0-9]/g, '');

                                            // Update value dan convert ke uppercase
                                            this.nomor_barcode = cleanValue.toUpperCase();
                                        }
                                    }" class="flex-fill">
                                        <input type="text"
                                            class="form-control @error('nomor_barcode') is-invalid @enderror"
                                            placeholder="Scan Barcode"
                                            x-model="nomor_barcode"
                                            x-on:input="handleInput($event.target.value)"
                                            x-on:blur="if(nomor_barcode) $wire.validateBarcode()"
                                            x-on:keydown.enter="if(nomor_barcode) { $wire.validateBarcode(); $event.preventDefault(); $refs.panjang_produksi.focus(); }"
                                            x-on:keydown.tab="$event.preventDefault(); $refs.panjang_produksi.focus();"
                                            x-ref="nomor_barcode"
                                            required />
                                    </div>
                                    @error('nomor_barcode')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="w-100"></div>
                        {{-- Dimensi Infure --}}
                        <div class="col-12 col-lg-4 mt-1 d-none d-lg-block">
                            <div class="form-group">
                                <div class="input-group">
                                    <label class="control-label col-5">Dimensi Infure</label>
                                    <input type="text" placeholder="-" x-ref="dimensiinfure"
                                        class="form-control readonly bg-light @error('dimensiinfure') is-invalid @enderror"
                                        readonly="readonly" wire:model="dimensiinfure" />
                                    <span class="input-group-text">
                                        mm
                                    </span>
                                    @error('dimensiinfure')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-8 mt-1 d-none d-lg-block">
                            <div class="form-group">
                                <div class="input-group">
                                    <label class="control-label col-3">Meter Gulung</label>
                                    <input type="text" placeholder="-"
                                        class="form-control readonly bg-light @error('qty_gulung') is-invalid @enderror"
                                        readonly="readonly" wire:model="qty_gulung" />
                                    <span class="input-group-text">
                                        m
                                    </span>
                                    @error('qty_gulung')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror

                                    <input type="text" class="form-control readonly bg-light" readonly="readonly"
                                        placeholder=" .. X .." />
                                    <input type="text"
                                        class="form-control readonly bg-light @error('qty_gentan') is-invalid @enderror"
                                        readonly="readonly" wire:model="qty_gentan" />
                                    <span class="input-group-text">
                                        roll
                                    </span>
                                    @error('qty_gentan')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-4 mt-1">
                            <div class="form-group">
                                <div class="input-group">
                                    <label class="control-label col-5">Panjang Produksi</label>
                                    <input type="text" placeholder="-"
                                        class="form-control @error('panjang_produksi') is-invalid @enderror"
                                        wire:model.change="panjang_produksi"
                                        oninput="this.value = window.formatNumber(this.value)"
                                        x-on:keydown.tab="$event.preventDefault(); $refs.berat_produksi.focus();"
                                        x-ref="panjang_produksi" />
                                    <span class="input-group-text">
                                        m
                                    </span>
                                    @if ((int) str_replace(',', '', $panjang_produksi) > 25000)
                                        <span class="text-danger">Panjang Produksi melebihi 25.000 m</span>
                                    @endif
                                    @error('panjang_produksi')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-5 mt-1 d-none d-lg-block">
                            <div class="form-group">
                                <div class="input-group">
                                    <label class="control-label col-6">Total Panjang Produksi</label>
                                    <input type="text" placeholder="0"
                                        class="form-control readonly bg-light @error('total_assembly_line') is-invalid @enderror"
                                        readonly="readonly" value="{{ number_format($total_assembly_line) }}" />
                                    <span class="input-group-text">
                                        m
                                    </span>
                                    @error('total_assembly_line')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-3 mt-1 d-none d-lg-block">
                            <div class="form-group">
                                <div class="input-group">
                                    <label class="control-label col-3">Selisih</label>
                                    <input type="text" placeholder="0"
                                        class="form-control readonly bg-light @error('selisih') is-invalid @enderror"
                                        readonly="readonly" wire:model="selisih" />
                                    <span class="input-group-text">
                                        m
                                    </span>
                                    @error('selisih')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-4 mt-1">
                            <div class="form-group">
                                <div class="input-group">
                                    <label class="control-label col-5">Berat Gentan</label>
                                    <input type="text"
                                        class="form-control @error('berat_produksi') is-invalid @enderror"
                                        wire:model.change="berat_produksi"
                                        oninput="this.value = window.formatNumber(this.value)"
                                        x-on:keydown.tab="$event.preventDefault(); $refs.work_hour.focus();"
                                        x-ref="berat_produksi" />
                                    <span class="input-group-text">
                                        kg
                                    </span>
                                    @if ($berat_produksi > 900)
                                        <span class="text-danger">Berat Gentan melebihi 900 kg</span>
                                    @endif
                                    @error('berat_produksi')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-5 mt-1 d-none d-lg-block">
                            <div class="form-group">
                                <div class="input-group">
                                    <label class="control-label col-6">Berat Standard</label>
                                    <input type="text" placeholder="0"
                                        class="form-control readonly bg-light @error('berat_standard') is-invalid @enderror"
                                        readonly="readonly" wire:model="berat_standard" />
                                    <span class="input-group-text">
                                        kg
                                    </span>
                                    @error('berat_standard')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-3 mt-1 d-none d-lg-block">
                            <div class="form-group">
                                <div class="input-group">
                                    <label class="control-label col-3">
                                        @if ($rasio == 0)
                                            Rasio
                                        @elseif ($rasio < 50)
                                            <span class="text-danger">Rasio dibawah</span>
                                        @elseif ($rasio > 150)
                                            <span class="text-danger">Rasio melebihi</span>
                                        @endif
                                    </label>
                                    <input type="text" placeholder="0"
                                        class="form-control readonly bg-light @error('rasio') is-invalid @enderror"
                                        readonly="readonly" wire:model="rasio" />
                                    <span class="input-group-text">
                                        %
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-4 mt-1">
                            <div class="form-group">
                                <div class="input-group">
                                    <label class="control-label col-5 pe-2">Jam Produksi</label>

                                    <!-- Mobile: Separate hour and minute selects -->
                                    <div class="d-flex gap-1 flex-fill d-lg-none" x-data="{
                                        workHour: @entangle('work_hour').live,
                                        get hourValue() {
                                            return this.workHour ? this.workHour.split(':')[0] : '00';
                                        },
                                        get minuteValue() {
                                            return this.workHour ? this.workHour.split(':')[1] : '00';
                                        },
                                        updateTime(hour, minute) {
                                            this.workHour = hour + ':' + minute;
                                        }
                                    }">
                                        <select class="form-control @error('work_hour') is-invalid @enderror"
                                            :value="hourValue"
                                            @change="updateTime($event.target.value, minuteValue)">
                                            @for($i = 0; $i < 24; $i++)
                                                <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}">
                                                    {{ str_pad($i, 2, '0', STR_PAD_LEFT) }}
                                                </option>
                                            @endfor
                                        </select>
                                        <span class="align-self-center">:</span>
                                        <select class="form-control @error('work_hour') is-invalid @enderror"
                                            :value="minuteValue"
                                            @change="updateTime(hourValue, $event.target.value)">
                                            @for($i = 0; $i < 60; $i++)
                                                <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}">
                                                    {{ str_pad($i, 2, '0', STR_PAD_LEFT) }}
                                                </option>
                                            @endfor
                                        </select>
                                    </div>

                                    <!-- Desktop: Time input -->
                                    <input class="form-control d-none d-lg-block @error('work_hour') is-invalid @enderror"
                                        wire:model.change="work_hour" type="time" placeholder="HH:mm"
                                        x-ref="work_hour" title="Format waktu harus HH:mm">

                                    @error('work_hour')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-4 mt-1 d-none d-lg-block">
                            <div class="form-group">
                                <div class="input-group">
                                    <label class="control-label col-4">Shift Kerja</label>
                                    <input type="text"
                                        class="form-control readonly bg-light @error('work_shift') is-invalid @enderror"
                                        readonly="readonly" wire:model="work_shift" />
                                    @error('work_shift')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Nomor Han --}}
                        <div class="col-12 col-lg-4 mt-1">
                            <div class="form-group">
                                <div class="input-group">
                                    <label class="control-label col-4">Nomor Han</label>
                                    <div x-data="{ nomor_han: @entangle('nomor_han'), status: true }" x-init="$watch('nomor_han', value => {
                                        if (value.length === 2 && status) {
                                            nomor_han = value + '-';
                                        }
                                        if (value.length === 5 && status) {
                                            nomor_han = value + '-';
                                        }
                                        if (value.length === 8 && status) {
                                            nomor_han = value + '-';
                                        }
                                        if (value.length < 10) {
                                            status = true;
                                        }
                                        if (value.length === 3 || value.length === 6 || value.length === 9) {
                                            status = false;
                                        }
                                        if (value.length === 12) {
                                            // Capitalize the character at index 12
                                            nomor_han = value.substring(0, 11) + value.charAt(11).toUpperCase();
                                        }
                                        if (value.length > 12) {
                                            nomor_han = value.substring(0, 12);
                                        }
                                    })">
                                        <input class="form-control @error('nomor_han') is-invalid @enderror"
                                            style="padding:0.44rem" type="text" placeholder="00-00-00-00A"
                                            x-model="nomor_han" maxlength="12" x-ref="nomor_han" />
                                    </div>
                                    @error('nomor_han')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-4 mt-1 d-none d-lg-block">
                            <div class="form-group">
                                <div class="input-group">
                                    <label class="control-label col-5 pe-2">Nomor Gentan</label>
                                    <input type="text"
                                        class="form-control bg-light @error('gentan_no') is-invalid @enderror"
                                        readonly="readonly" wire:model="gentan_no" x-ref="gentan_no" />
                                    @error('gentan_no')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <hr />
            <div class="row">
                <div class="col-lg-3">
                    <button wire:click="addLossInfure" type="button" class="btn btn-success">
                        <i class="ri-add-line"></i> Add Loss Infure
                    </button>
                </div>
                <div class="col-lg-6 col-12">
                    @if ($selisih > 0)
                        <h4 class="text-danger text-center">Total panjang melebihi LPK ..!</h4>
                    @endif
                </div>
                <div class="col-lg-3">
                    <div class="toolbar float-end">
                        <button type="button" class="btn btn-warning" wire:click="cancel" wire:loading.attr="disabled">
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
                        <button type="button" wire:click="save" class="btn btn-success" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="save">
                                <i class="ri-save-3-line"></i> Save 1.4
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
                    </div>
                </div>
            </div>
            <div class="modal fade" id="modal-add" tabindex="-1" role="dialog" aria-labelledby="modal-add"
                aria-hidden="true" wire:ignore.self>
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h2 class="h6 modal-title">Add Loss Infure</h2>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-lg-12 mb-1">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <label class="col-12 col-lg-2 fw-bold text-muted">Kode Loss </label>
                                            <input id="inputKodeLoss"
                                                class="form-control @error('loss_infure_code') is-invalid @enderror"
                                                type="text" wire:model.change="loss_infure_code" placeholder="..."
                                                x-on:keydown.tab="$event.preventDefault(); $refs.berat_loss.focus();" />
                                            @error('loss_infure_code')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12 mb-1">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <label class="col-12 col-lg-2 fw-bold text-muted">Nama Loss </label>
                                            <input class="form-control readonly bg-light" readonly="readonly"
                                                type="text" wire:model.defer="name_infure" placeholder="..." />
                                            @error('name')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12 mb-1">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <label class="col-12 col-lg-2 fw-bold text-muted">Berat Loss </label>
                                            <input class="form-control @error('berat_loss') is-invalid @enderror"
                                                type="text" wire:model.defer="berat_loss" placeholder="0"
                                                x-ref="berat_loss" />
                                            @error('berat_loss')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12 mb-1">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <label class="col-12 col-lg-2 fw-bold text-muted">Frekuensi </label>
                                            <input class="form-control @error('frekuensi') is-invalid @enderror"
                                                type="number" min="0" wire:model.defer="frekuensi" placeholder="0" />
                                            @error('frekuensi')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-success ms-auto" wire:click="saveInfure"
                                wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="saveInfure">
                                    <i class="ri-save-3-line"></i> Save
                                </span>
                                <div wire:loading wire:target="saveInfure">
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
                            <button type="button" class="btn btn-link text-gray-600"
                                data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card border-0 shadow mb-4 mt-4">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap mb-0 rounded">
                            <thead class="table-light">
                                <tr>
                                    <th class="border-0 rounded-start">Action</th>
                                    <th class="border-0">Kode</th>
                                    <th class="border-0">Nama Loss</th>
                                    <th class="border-0">Berat (kg)</th>
                                    <th class="border-0 rounded-end">Frekuensi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $total = 0;
                                @endphp
                                @forelse ($details as $item)
                                    <tr>
                                        <td>
                                            <button type="button" class="btn btn-warning"
                                                wire:click="editLossInfure({{ $item['id'] }})"
                                                wire:loading.attr="disabled">
                                                <span wire:loading.remove
                                                    wire:target="editLossInfure({{ $item['id'] }})">
                                                    <i class="fa fa-edit"></i> Edit
                                                </span>
                                                <div wire:loading wire:target="editLossInfure({{ $item['id'] }})">
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
                                            <button type="button" class="btn btn-danger"
                                                data-bs-toggle="modal" data-bs-target="#modal-delete-loss-infure-{{ $item['id'] }}">
                                                <i class="fa fa-trash"></i> Delete
                                            </button>

                                            <div id="modal-delete-loss-infure-{{ $item['id'] }}" class="modal fade zoomIn" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                                                                id="close-remove-loss-infure-{{ $item['id'] }}"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="mt-2 text-center">
                                                                <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop"
                                                                    colors="primary:#f7b84b,secondary:#f06548"
                                                                    style="width:100px;height:100px"></lord-icon>
                                                                <div class="mt-4 pt-2 fs-15 mx-4 mx-sm-5">
                                                                    <h4>Are you sure ?</h4>
                                                                    <p class="text-muted mx-4 mb-0">Are you sure you want to remove this loss infure ?</p>
                                                                </div>
                                                            </div>
                                                            <div class="d-flex gap-2 justify-content-center mt-4 mb-2">
                                                                <button type="button" class="btn w-sm btn-light" data-bs-dismiss="modal">Close</button>
                                                                <button wire:click="deleteInfure({{ $item['id'] }})" type="button"
                                                                    class="btn w-sm btn-danger" wire:loading.attr="disabled">
                                                                    <span wire:loading.remove wire:target="deleteInfure({{ $item['id'] }})">
                                                                        <i class="ri-save-3-line"></i> Yes, Delete It!
                                                                    </span>
                                                                    <div wire:loading wire:target="deleteInfure({{ $item['id'] }})">
                                                                        <span class="d-flex align-items-center">
                                                                            <span class="spinner-border flex-shrink-0" role="status">
                                                                                <span class="visually-hidden">Loading...</span>
                                                                            </span>
                                                                            <span class="flex-grow-1 ms-1">Loading...</span>
                                                                        </span>
                                                                    </div>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $item['loss_infure_code'] }}</td>
                                        <td>{{ $item['name_infure'] ?? '' }}</td>
                                        <td>{{ $item['berat_loss'] }}</td>
                                        <td>{{ $item['frekuensi'] }}</td>
                                    </tr>
                                    @php
                                        $total += $item['berat_loss'];
                                    @endphp
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">No results found</td>
                                    </tr>
                                @endforelse
                                <tr>
                                    <td colspan="4" class="text-end">Berat Loss Total (kg):</td>
                                    <td colspan="1" class="text-center">{{ $total }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!--  modal master produk -->
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
            </div><!-- /.modal -->

            <!--  modal LPK -->
            <div class="modal fade" id="modal-lpk" tabindex="-1" role="dialog"
                aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="myExtraLargeModalLabel">LPK Info - Nomor: <span
                                    class="fw-bold">{{ $lpk_no }}</span></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="col-lg-12">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <div class="input-group">
                                                <label class="control-label col-12 col-lg-3 fw-bold text-muted">Tanggal
                                                    LPK</label>
                                                <input value="{{ $orderLPK->lpk_date ?? '' }}" disabled type="text"
                                                    class="form-control" style="padding:0.44rem"
                                                    data-provider="flatpickr" data-date-format="d/m/Y">
                                                <span class="input-group-text py-0">
                                                    <i class="ri-calendar-event-fill fs-4"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="form-group mt-1">
                                            <div class="input-group">
                                                <label class="control-label col-12 col-lg-3 fw-bold text-muted">Nomor
                                                    LPK</label>
                                                <input type="text" class="form-control"
                                                    value="{{ $orderLPK->lpk_no ?? '' }}" />
                                            </div>
                                        </div>
                                        <div class="form-group mt-1">
                                            <div class="input-group">
                                                <label class="control-label col-12 col-lg-3 fw-bold text-muted">PO
                                                    Number</label>
                                                <input type="text" class="form-control"
                                                    value="{{ $orderLPK->po_no ?? '' }}" />
                                            </div>
                                        </div>
                                        <div class="form-group mt-1">
                                            <div class="input-group">
                                                <label class="control-label col-12 col-lg-3 fw-bold text-muted">Nomor
                                                    Order</label>
                                                <input type="text" class="form-control readonly"
                                                    readonly="readonly" value="{{ $orderLPK->no_order ?? '' }}" />
                                            </div>
                                        </div>
                                        <div class="form-group mt-1">
                                            <div class="input-group">
                                                <label class="control-label col-12 col-lg-3 fw-bold text-muted">Nomor
                                                    Mesin</label>
                                                <input type="text" class="form-control"
                                                    value="{{ $orderLPK->machineno ?? '' }}" />
                                            </div>
                                        </div>
                                        <div class="form-group mt-1">
                                            <div class="input-group">
                                                <label class="control-label col-12 col-lg-3 fw-bold text-muted">Jumlah
                                                    LPK</label>
                                                <input type="text" class="form-control"
                                                    value="{{ $orderLPK->qty_lpk ?? '' }}" />
                                                <span class="input-group-text">
                                                    Lembar
                                                </span>
                                            </div>
                                        </div>
                                        <div class="form-group mt-1">
                                            <div class="input-group">
                                                <label class="control-label col-12 col-lg-3 fw-bold text-muted">Jumlah
                                                    Gentan</label>
                                                <input type="text" class="form-control"
                                                    value="{{ $orderLPK->qty_gentan ?? '' }}" />
                                                <span class="input-group-text">
                                                    roll
                                                </span>
                                            </div>
                                        </div>
                                        <div class="form-group mt-1">
                                            <div class="input-group">
                                                <label class="control-label col-12 col-lg-3 fw-bold text-muted">Meter
                                                    Gulung</label>
                                                <input type="text" class="form-control"
                                                    value="{{ $orderLPK->qty_gulung ?? '' }}" />
                                                <span class="input-group-text">
                                                    meter
                                                </span>
                                            </div>
                                        </div>
                                        <div class="form-group mt-1">
                                            <div class="input-group">
                                                <label class="control-label col-12 col-lg-3 fw-bold text-muted">Panjang
                                                    LPK</label>
                                                <input type="text" class="form-control readonly"
                                                    readonly="readonly" value="{{ $orderLPK->panjang_lpk ?? '' }}" />
                                                <span class="input-group-text">
                                                    meter
                                                </span>
                                            </div>
                                        </div>
                                        {{-- warna LPK --}}
                                        <div class="form-group mt-1">
                                            <div class="input-group">
                                                <label class="control-label col-12 col-lg-3 fw-bold text-muted">Warna
                                                    LPK</label>
                                                <input type="text" class="form-control readonly"
                                                    readonly="readonly" value="{{ $orderLPK->warnalpkid ?? '' }}" />
                                            </div>
                                        </div>

                                    </div>

                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <div class="input-group">
                                                <label class="control-label col-12 col-lg-3 fw-bold text-muted">Tanggal
                                                    Proses</label>
                                                <input value="{{ $orderLPK->processdate ?? '' }}" disabled
                                                    type="date" class="form-control datepicker-input"
                                                    placeholder="yyyy/mm/dd" style="padding:0.44rem"
                                                    data-provider="flatpickr" data-date-format="d/m/Y" />
                                                <span class="input-group-text py-0">
                                                    <i class="ri-calendar-event-fill fs-4"></i>
                                                </span>
                                            </div>
                                        </div>

                                        <div class="form-group mt-1">
                                            <div class="input-group">
                                                <label class="control-label col-12 col-lg-3 fw-bold text-muted">Tanggal
                                                    PO</label>
                                                <input class="form-control datepicker-input readonly"
                                                    readonly="readonly" type="date"
                                                    value="{{ $orderLPK->order_date ?? '' }}"
                                                    placeholder="yyyy/mm/dd" />
                                            </div>
                                        </div>
                                        <div class="form-group mt-1">
                                            <div class="input-group">
                                                <label
                                                    class="control-label col-12 col-lg-3 fw-bold text-muted">Buyer</label>
                                                <input type="text" class="form-control readonly"
                                                    readonly="readonly" value="{{ $orderLPK->buyer_name ?? '' }}" />
                                            </div>
                                        </div>
                                        <div class="form-group mt-1">
                                            <div class="input-group">
                                                <label class="control-label col-12 col-lg-3 fw-bold text-muted">Nama
                                                    Produk</label>
                                                <input type="text" class="form-control readonly"
                                                    readonly="readonly" value="{{ $orderLPK->product_name ?? '' }}" />
                                            </div>
                                        </div>
                                        <div class="form-group mt-1">
                                            <div class="input-group">
                                                <label class="control-label col-12 col-lg-3 fw-bold text-muted">Nama
                                                    Mesin</label>
                                                <input type="text" class="form-control readonly"
                                                    readonly="readonly" value="{{ $orderLPK->machinename ?? '' }}" />
                                            </div>
                                        </div>
                                        <div class="form-group mt-1">
                                            <div class="input-group">
                                                <label class="control-label col-12 col-lg-3 fw-bold text-muted">Panjang
                                                    Total</label>
                                                <input type="text" class="form-control readonly"
                                                    readonly="readonly"
                                                    value="{{ $orderLPK->total_assembly_line ?? '' }}" />
                                                <span class="input-group-text">
                                                    meter
                                                </span>
                                            </div>
                                        </div>
                                        <div class="form-group mt-1">
                                            <div class="input-group">
                                                <label class="control-label col-12 col-lg-3 fw-bold text-muted">Dimensi
                                                    (TxLxP)</label>
                                                <input type="text" class="form-control readonly"
                                                    readonly="readonly" value="{{ $orderLPK->dimensi ?? '' }}" />
                                            </div>
                                        </div>
                                        <div class="form-group mt-1">
                                            <div class="input-group">
                                                <label class="control-label col-12 col-lg-3 fw-bold text-muted">Default
                                                    Gulung</label>
                                                <input type="text" class="form-control readonly"
                                                    readonly="readonly"
                                                    value="{{ $orderLPK->defaultgulung ?? '' }}" />
                                                <span class="input-group-text" id="basic-addon2">
                                                    meter
                                                </span>
                                            </div>
                                        </div>
                                        <div class="form-group mt-1">
                                            <div class="input-group">
                                                <label class="control-label col-12 col-lg-3 fw-bold text-muted">Selisih
                                                    Kurang</label>
                                                <input type="text" class="form-control readonly"
                                                    readonly="readonly"
                                                    value="{{ $orderLPK->selisihKurang ?? '' }}" />
                                                <span class="input-group-text">
                                                    meter
                                                </span>
                                            </div>
                                        </div>
                                        <div class="form-group mt-1">
                                            <label for="textarea"
                                                class="control-label col-12 col-lg-3 fw-bold text-muted">Catatan</label>
                                            <textarea class="form-control" placeholder="Catatan" id="textarea" rows="2">{{ $orderLPK->remark ?? '' }}</textarea>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-2">
                                        <div class="fw-bold text-muted">
                                            Progress
                                        </div>
                                    </div>
                                    <div class="col-10">
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="form-group mt-1">
                                                    <div class="input-group">
                                                        <label
                                                            class="control-label col-12 col-lg-3 text-muted">INFURE:</label>
                                                        <input type="text" class="form-control readonly"
                                                            readonly="readonly"
                                                            value="{{ $orderLPK->progressInfure ?? '' }}" />
                                                        <span class="input-group-text">
                                                            meter
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group mt-1">
                                                    <div class="input-group">
                                                        <label
                                                            class="control-label col-12 col-lg-3 text-muted">{{ $orderLPK != null ? ($orderLPK->progressInfureSelisih < 0 ? 'Kurang' : 'Lebih') : '' }}:</label>
                                                        <input type="text"
                                                            class="form-control readonly {{ $orderLPK != null ? ($orderLPK->progressInfureSelisih < 0 ? 'text-danger' : 'text-info') : '' }}"
                                                            readonly="readonly"
                                                            value="{{ $orderLPK->progressInfureSelisih ?? 0 }}" />
                                                        <span class="input-group-text">
                                                            meter
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="form-group mt-1">
                                                    <div class="input-group">
                                                        <label
                                                            class="control-label col-12 col-lg-3 mt-1 text-muted">SEITAI:</label>
                                                        <input type="text" class="form-control readonly"
                                                            readonly="readonly"
                                                            value="{{ $orderLPK->progressSeitai ?? '' }}" />
                                                        <span class="input-group-text">
                                                            lbr
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group mt-1">
                                                    <div class="input-group">
                                                        <label
                                                            class="control-label col-12 col-lg-3 mt-1 text-muted">{{ $orderLPK != null ? ($orderLPK->progressSeitaiSelisih < 0 ? 'Kurang' : 'Lebih') : '' }}:</label>
                                                        <input type="text"
                                                            class="form-control readonly {{ $orderLPK != null ? ($orderLPK->progressSeitaiSelisih < 0 ? 'text-danger' : 'text-info') : '' }}"
                                                            readonly="readonly"
                                                            value="{{ $orderLPK->progressSeitaiSelisih ?? '' }}" />
                                                        <span class="input-group-text">
                                                            lbr
                                                        </span>
                                                    </div>
                                                </div>
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
            </div><!-- /.modal -->

            <!-- modal edit loss infure-->
            <div class="modal fade" id="modal-edit" tabindex="-1" role="dialog" aria-labelledby="modal-edit"
                aria-hidden="true" wire:ignore.self>
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h2 class="h6 modal-title">Edit Loss Infure</h2>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-lg-12 mb-1">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <label class="col-12 col-lg-2 fw-bold text-muted">Kode Loss </label>
                                            <input id="inputKodeLoss" class="form-control" type="text"
                                                wire:model.change="loss_infure_code" placeholder="..."
                                                x-on:keydown.tab="$event.preventDefault(); $refs.edit_berat_loss.focus();" />
                                            @error('loss_infure_code')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12 mb-1">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <label class="col-12 col-lg-2 fw-bold text-muted">Nama Loss </label>
                                            <input class="form-control readonly bg-light" readonly="readonly"
                                                type="text" wire:model.defer="name_infure" placeholder="..." />
                                            @error('name')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12 mb-1">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <label class="col-12 col-lg-2 fw-bold text-muted">Berat Loss </label>
                                            <input class="form-control" type="text" wire:model.defer="berat_loss"
                                                placeholder="0" x-ref="edit_berat_loss" />
                                            @error('berat_loss')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12 mb-1">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <label class="col-12 col-lg-2 fw-bold text-muted">Frekuensi </label>
                                            <input class="form-control" type="number" min="0" wire:model.defer="frekuensi"
                                                placeholder="0" />
                                            @error('frekuensi')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-success ms-auto" wire:click="updateLossInfure"
                                wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="updateLossInfure">
                                    <i class="ri-save-3-line"></i> Update
                                </span>
                                <div wire:loading wire:target="updateLossInfure">
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
                            <button type="button" class="btn btn-link text-gray-600"
                                data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var modalAdd = document.getElementById('modal-add');

        modalAdd.addEventListener('shown.bs.modal', function() {
            document.getElementById('inputKodeLoss').focus();
        });
    });

    document.addEventListener('livewire:initialized', () => {
        Livewire.on('openModal', (modalId) => {
            new bootstrap.Modal(document.getElementById(modalId)).show();
        });

        Livewire.on('closeModal', (modalId) => {
            bootstrap.Modal.getInstance(document.getElementById(modalId)).hide();
        });
    });
</script>
@script
    <script>
        window.formatNumber = function(value) {
            // Hapus koma jika ada untuk pemrosesan angka (kecuali koma desimal)
            value = value.replace(/,/g, '');

            // Pertahankan hanya angka dan satu titik desimal
            value = value.replace(/[^0-9.]/g, '');

            // Jika ada lebih dari satu titik, hapus titik tambahan
            let parts = value.split('.');
            if (parts.length > 2) {
                value = parts[0] + '.' + parts[1]; // Gabungkan bagian sebelum titik dan satu bagian desimal
            }

            // Hapus nol di depan angka
            if (parts[0] !== '') {
                parts[0] = parts[0].replace(/^0+/, '');
            }

            // Jika value adalah angka yang valid, format dengan pemisah ribuan
            if (!isNaN(parts[0])) {
                parts[0] = Number(parts[0]).toLocaleString('en-US');
            }

            // Gabungkan kembali angka dengan desimal (jika ada)
            return parts.length > 1 ? parts[0] + '.' + parts[1] : parts[0];
        };
        $wire.on('showModal', () => {
            $('#modal-add').modal('show');
        });
        $wire.on('closeModal', () => {
            $('#modal-add').modal('hide');
        });
        $wire.on('showModalNoOrder', () => {
            $('#modal-noorder-produk').modal('show');
        });
        // close modal NoOrder
        $wire.on('closeModalNoOrder', () => {
            $('#modal-noorder-produk').modal('hide');
        });

        // show modal LPK
        $wire.on('showModalLPK', () => {
            $('#modal-lpk').modal('show');
        });
        // close modal LPK
        $wire.on('closeModalLPK', () => {
            $('#modal-lpk').modal('hide');
        });

        $wire.on('closeModalDeleteLossInfure', (id) => {
            $('#modal-delete-loss-infure-' + id).modal('hide');
        });

        $wire.on('redirectToPrint', (produk_asemblyid) => {
            var printUrl = '{{ route('report-gentan') }}?produk_asemblyid=' + produk_asemblyid;

            // Buka window baru untuk print
            var printWindow = window.open(printUrl, '_blank', 'width=800,height=600');

            // Focus ke window print (penting untuk trigger print dialog)
            if (printWindow) {
                printWindow.focus();
            }
        });
    </script>
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('barcode-validated', () => {
                // Auto focus ke panjang produksi setelah barcode valid
                setTimeout(() => {
                    document.querySelector('[x-ref="panjang_produksi"]')?.focus();
                }, 100);
            });
        });
    </script>
@endscript

{{-- THERMAL PRINT MODULE - UPDATE DENGAN DEBUG --}}
<script>
// ===== MOBILE DEBUG LOGGER =====
window.mobileDebug = function(msg, type = 'info') {
    const logDiv = document.getElementById('mobileLogContent');
    const debugPanel = document.getElementById('mobileDebugLog');

    if (!logDiv || !debugPanel) return;

    debugPanel.style.display = 'block';

    const colors = {
        'info': '#0ff',
        'success': '#0f0',
        'error': '#f00',
        'warn': '#ff0'
    };

    const timestamp = new Date().toLocaleTimeString();
    const color = colors[type] || '#fff';

    logDiv.innerHTML += `<div style="color:${color}; margin:5px 0; padding:5px; border-left:3px solid ${color};">[${timestamp}] ${msg}</div>`;
    logDiv.scrollTop = logDiv.scrollHeight;

    console.log(msg);
};

window.clearDebugLog = function() {
    const logDiv = document.getElementById('mobileLogContent');
    if (logDiv) logDiv.innerHTML = '';
};

// ===== THERMAL PRINTER MODULE =====
(function() {
    if (typeof window === 'undefined' || typeof navigator === 'undefined') {
        window.mobileDebug('❌ Window/Navigator not available', 'error');
        return;
    }

    if (window.thermalPrinterLoaded) {
        window.mobileDebug('✅ Thermal module already loaded', 'success');
        return;
    }

    try {
        window.thermalPrinterLoaded = true;

        const hasBluetoothAPI = 'bluetooth' in navigator;

        if (!hasBluetoothAPI) {
            window.mobileDebug('⚠️ Bluetooth API not available', 'warn');
            return;
        }

        window.mobileDebug('✅ Bluetooth API available', 'success');

        // UUID EPSON TM-P20II
        window.THERMAL_UUID_CONFIGS = [{
            name: 'Epson TM-P20II',
            serviceUUID: '49535343-fe7d-4ae5-8fa9-9fafd205e455',
            characteristicUUID: '49535343-1e4d-4bd9-ba61-23c647249616',
        }];

        window.connectedDevice = null;
        window.printerCharacteristic = null;
        window.printerReady = false; // ✅ FLAG BARU

        // Generate ESC/POS
        window.generateEscPosCommands = function(data) {
            const ESC = '\x1B';
            const GS = '\x1D';
            let cmd = '';

            cmd += ESC + '@';
            cmd += ESC + 'R' + String.fromCharCode(0);

            // GENTAN NO (DOUBLE SIZE) - CENTER
            cmd += ESC + 'a' + String.fromCharCode(1);
            cmd += GS + '!' + String.fromCharCode(0x11);
            cmd += String(data.gentan_no || '0') + '\n';
            cmd += GS + '!' + String.fromCharCode(0);
            cmd += '\n';

            // QR CODE (LPK NO) - CENTER
            const qrData = String(data.lpk_no || '000000-000');
            cmd += GS + '(k' + String.fromCharCode(4, 0, 49, 65, 50, 0);
            cmd += GS + '(k' + String.fromCharCode(3, 0, 49, 67, 6);
            cmd += GS + '(k' + String.fromCharCode(3, 0, 49, 69, 49);

            const qrLen = qrData.length + 3;
            const pL = qrLen % 256;
            const pH = Math.floor(qrLen / 256);
            cmd += GS + '(k' + String.fromCharCode(pL, pH, 49, 80, 48) + qrData;
            cmd += GS + '(k' + String.fromCharCode(3, 0, 49, 81, 48);

            cmd += '\n\n';
            cmd += ESC + 'a' + String.fromCharCode(0);

            // LPK NO (DOUBLE SIZE)
            cmd += '================================\n';
            cmd += ESC + 'a' + String.fromCharCode(1);
            cmd += GS + '!' + String.fromCharCode(0x11);
            cmd += String(data.lpk_no || '-') + '\n';
            cmd += GS + '!' + String.fromCharCode(0);
            cmd += ESC + 'a' + String.fromCharCode(0);
            cmd += '================================\n';

            // PRODUCT NAME (BOLD)
            cmd += ESC + 'E' + String.fromCharCode(1);
            cmd += String(data.product_name || '-') + '\n';
            cmd += ESC + 'E' + String.fromCharCode(0);
            cmd += '--------------------------------\n';

            // DETAIL (NORMAL SIZE)
            cmd += 'No. Order   : ' + String(data.code || '-') + '\n';
            cmd += 'Kode        : ' + String(data.code_alias || '-') + '\n';
            cmd += 'Tgl Prod    : ' + String(data.production_date || '-') + '\n';
            cmd += 'Jam         : ' + String(data.work_hour || '-') + '\n';
            cmd += 'Shift       : ' + String(data.work_shift || '-') + '\n';
            cmd += 'Mesin       : ' + String(data.machineno || '-') + '\n';
            cmd += '--------------------------------\n';

            // BERAT & PANJANG (BOLD)
            cmd += ESC + 'E' + String.fromCharCode(1);
            cmd += 'Berat       : ' + String(data.berat_produksi || '0') + '\n';
            cmd += 'Panjang     : ' + String(data.panjang_produksi || '0') + '\n';
            cmd += ESC + 'E' + String.fromCharCode(0);

            cmd += 'Lebih       : ' + String(data.selisih || '0') + '\n';
            cmd += 'No Han      : ' + String(data.nomor_han || '-') + '\n';
            cmd += '--------------------------------\n';

            // NIK & NAMA
            cmd += 'NIK         : ' + String(data.nik || '-') + '\n';
            cmd += 'Nama        : ' + String(data.empname || '-') + '\n';
            cmd += '================================\n';
            cmd += '\n\n\n';

            // Cut
            cmd += GS + 'V' + String.fromCharCode(0);

            return cmd;
        };

        // ===== AUTO INIT PRINTER SAAT PAGE LOAD =====
        window.initPrinterOnLoad = async function() {
            try {
                window.mobileDebug('🔄 Auto-checking printer...', 'info');

                let devices = [];
                try {
                    devices = await navigator.bluetooth.getDevices();
                } catch (e) {
                    window.mobileDebug('⚠️ getDevices() failed: ' + e.message, 'warn');
                    return false;
                }

                window.mobileDebug('Found ' + devices.length + ' paired device(s)', 'info');

                // Cari TM-P20II
                const epsonPrinter = devices.find(d =>
                    d.name && d.name.includes('TM-P20II')
                );

                if (!epsonPrinter) {
                    window.mobileDebug('⚠️ TM-P20II not paired yet', 'warn');
                    return false;
                }

                window.mobileDebug('✅ Found: ' + epsonPrinter.name, 'success');

                // Try connect
                try {
                    let server;
                    if (epsonPrinter.gatt && epsonPrinter.gatt.connected) {
                        window.mobileDebug('Already connected!', 'success');
                        server = epsonPrinter.gatt;
                    } else {
                        window.mobileDebug('Connecting...', 'info');
                        server = await epsonPrinter.gatt.connect();
                    }

                    const service = await server.getPrimaryService('49535343-fe7d-4ae5-8fa9-9fafd205e455');
                    const characteristic = await service.getCharacteristic('49535343-1e4d-4bd9-ba61-23c647249616');

                    window.connectedDevice = epsonPrinter;
                    window.printerCharacteristic = characteristic;
                    window.printerReady = true; // ✅ TANDAI READY

                    localStorage.setItem('thermal_printer_name', epsonPrinter.name);

                    window.mobileDebug('✅ Printer READY!', 'success');
                    return true;

                } catch (err) {
                    window.mobileDebug('❌ Connection failed: ' + err.message, 'error');
                    return false;
                }

            } catch (error) {
                window.mobileDebug('❌ Init error: ' + error.message, 'error');
                return false;
            }
        };

        // ===== MANUAL CONNECT (UNTUK FIRST TIME) =====
        window.connectThermalPrinter = async function() {
            window.mobileDebug('🔍 Requesting printer...', 'info');

            const device = await navigator.bluetooth.requestDevice({
                acceptAllDevices: true,
                optionalServices: ['49535343-fe7d-4ae5-8fa9-9fafd205e455']
            });

            window.mobileDebug('Connecting to: ' + device.name, 'info');

            const server = await device.gatt.connect();
            const service = await server.getPrimaryService('49535343-fe7d-4ae5-8fa9-9fafd205e455');
            const characteristic = await service.getCharacteristic('49535343-1e4d-4bd9-ba61-23c647249616');

            window.connectedDevice = device;
            window.printerCharacteristic = characteristic;
            window.printerReady = true; // ✅ TANDAI READY

            localStorage.setItem('thermal_printer_name', device.name);

            window.mobileDebug('✅ Printer saved: ' + device.name, 'success');
            return true;
        };

        // Print
        window.printToThermalPrinter = async function(data) {
            window.mobileDebug('📝 Generating commands...', 'info');
            const commands = window.generateEscPosCommands(data);
            const encoder = new TextEncoder();
            const bytes = encoder.encode(commands);

            window.mobileDebug('Bytes: ' + bytes.length, 'info');

            // ✅ CEK PRINTER READY
            if (!window.printerReady || !window.printerCharacteristic) {
                window.mobileDebug('❌ Printer not ready!', 'error');
                throw new Error('Printer not connected');
            }

            const chunkSize = 128;
            const totalChunks = Math.ceil(bytes.length / chunkSize);

            window.mobileDebug('Sending ' + totalChunks + ' chunks...', 'info');

            for (let i = 0; i < bytes.length; i += chunkSize) {
                const chunk = bytes.slice(i, i + chunkSize);
                await window.printerCharacteristic.writeValue(chunk);
                await new Promise(r => setTimeout(r, 200));
            }

            window.mobileDebug('✅ Print complete!', 'success');
            await new Promise(r => setTimeout(r, 1000));

            if (typeof Toastify !== 'undefined') {
                Toastify({
                    text: "✅ Label berhasil dicetak!",
                    duration: 3000,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "#10b981",
                }).showToast();
            }
        };

        window.mobileDebug('✅ Thermal module loaded', 'success');

        // ✅ AUTO INIT SAAT PAGE LOAD (TANPA USER CLICK)
        setTimeout(() => {
            window.initPrinterOnLoad();
        }, 1000);

    } catch (error) {
        window.mobileDebug('❌ Init error: ' + error.message, 'error');
    }
})();

// ===== LISTEN EVENT DARI LIVEWIRE =====
document.addEventListener('livewire:initialized', () => {
    window.mobileDebug('🎧 Livewire initialized', 'success');

    Livewire.on('gentan-saved', async (event) => {
        window.mobileDebug('=== GENTAN SAVED ===', 'warn');

        const produk_asemblyid = event.produk_asemblyid || event[0]?.produk_asemblyid;
        window.mobileDebug('Product ID: ' + produk_asemblyid, 'info');

        if (!produk_asemblyid) {
            window.mobileDebug('❌ No product ID!', 'error');
            return;
        }

        try {
            // ✅ CEK PRINTER READY DULU
            if (!window.printerReady) {
                window.mobileDebug('⚠️ Printer not ready, requesting...', 'warn');

                // Minta user pilih printer (FIRST TIME ONLY)
                await window.connectThermalPrinter();

                // Tunggu sebentar biar connect stabil
                await new Promise(r => setTimeout(r, 500));
            }

            // Fetch data
            window.mobileDebug('📡 Fetching print data...', 'info');
            const response = await fetch(`/get-print-data/${produk_asemblyid}`);

            if (!response.ok) {
                const errorText = await response.text();
                window.mobileDebug('❌ Fetch failed: ' + errorText.substring(0, 100), 'error');
                throw new Error('Fetch error: ' + response.status);
            }

            const printData = await response.json();
            window.mobileDebug('✅ Data received', 'success');

            if (printData.error) {
                throw new Error(printData.error);
            }

            // Print
            window.mobileDebug('🖨️ Printing...', 'info');
            await window.printToThermalPrinter(printData);

            window.mobileDebug('✅ ALL DONE!', 'success');

            // Redirect
            setTimeout(() => {
                window.location.href = '{{ route("nippo-infure") }}';
            }, 2000);

        } catch (error) {
            window.mobileDebug('❌ ERROR: ' + error.message, 'error');

            if (confirm('❌ Print gagal: ' + error.message + '\n\nGunakan Print Normal?')) {
                const printUrl = '{{ route("report-gentan") }}?produk_asemblyid=' + produk_asemblyid;
                window.open(printUrl, '_blank');

                setTimeout(() => {
                    window.location.href = '{{ route("nippo-infure") }}';
                }, 1000);
            } else {
                // Tetap redirect meski gagal print
                setTimeout(() => {
                    window.location.href = '{{ route("nippo-infure") }}';
                }, 1000);
            }
        }
    });
});
</script>
