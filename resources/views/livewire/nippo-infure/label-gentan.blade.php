{{-- FORM ASLI - TETAP SAMA --}}
<div class="row mt-3">
	<div class="col-lg-2"></div>
	<div class="col-lg-8">
		<div class="form-group">
			<div class="input-group">
				<label class="control-label col-12 col-lg-3 text-muted fw-bold">Nomor LPK</label>
				<div class="col-12 col-lg-9 mb-1" x-data="{ lpk_no: @entangle('lpk_no').live, status: true }" x-init="$watch('lpk_no', value => {
                    if (value.length === 6 && !value.includes('-') && status) {
                        lpk_no = value + '-';
                    }
                    if (value.length < 6) {
                        status = true;
                    }
                    if (value.length === 7) {
                        status = false;
                    }
                    if (value.length > 10) {
                        lpk_no = value.substring(0, 10);
                    }
                })">
					<input
						class="form-control"
						style="padding:0.44rem"
						type="text"
						placeholder="000000-000"
						x-model="lpk_no"
						maxlength="10"
					/>
				</div>
			</div>
		</div>
		<div class="form-group mt-1">
			<div class="input-group">
				<label class="control-label col-12 col-lg-3 text-muted fw-bold">Nomor Gentan</label>
				<input type="text" wire:model.change="gentan_no" class="form-control" placeholder="..." />
			</div>
		</div>
		<div class="form-group mt-1">
			<div class="input-group">
				<label class="control-label col-12 col-lg-3 text-muted fw-bold">Nomor Order</label>
				<input type="text" wire:model="code" class="form-control readonly bg-light" readonly="readonly" />
			</div>
		</div>
		<div class="form-group mt-1">
			<div class="input-group">
				<label class="control-label col-12 col-lg-3 text-muted fw-bold">Nama Produk</label>
				<input type="text" wire:model="product_name" class="form-control readonly bg-light" readonly="readonly" />
			</div>
		</div>
		<div class="form-group mt-1">
			<div class="input-group">
				<label class="control-label col-12 col-lg-3 text-muted fw-bold">Panjang Produksi</label>
				<input type="text" wire:model="product_panjang" class="form-control readonly currency bg-light" readonly="readonly" />
				<span class="input-group-text">meter</span>
			</div>
		</div>
		<div class="form-group mt-1">
			<div class="input-group">
				<label class="control-label col-12 col-lg-3 text-muted fw-bold">Berat Gentan</label>
				<input type="text" wire:model="berat_produksi" class="form-control readonly currency bg-light" readonly="readonly" />
				<span class="input-group-text">kg</span>
			</div>
		</div>
		<div class="form-group mt-1">
			<div class="input-group">
				<label class="control-label col-12 col-lg-3 text-muted fw-bold">Berat Standard</label>
				<input type="text" wire:model="berat_standard" class="form-control readonly currency bg-light" readonly="readonly" />
				<span class="input-group-text">kg</span>
			</div>
		</div>
		<div class="form-group mt-1">
			<div class="input-group">
				<label class="control-label col-12 col-lg-3 text-muted fw-bold">Tanggal LPK</label>
				<input class="form-control readonly datepicker-input bg-light" readonly="readonly" type="text" style="padding:0.44rem" wire:model.defer="lpk_date" placeholder="yyyy/mm/dd"/>
			</div>
		</div>
		<div class="form-group mt-1">
			<div class="input-group">
				<label class="control-label col-12 col-lg-3 text-muted fw-bold">Jumlah LPK</label>
				<input type="text" name="qty_lpk" class="form-control readonly integer bg-light" readonly="readonly" wire:model="qty_lpk" />
			</div>
		</div>
		<hr />

        <div id="debugLog" style="display:none; background:#000; color:#0f0; padding:10px; margin-bottom:10px; font-family:monospace; font-size:11px; max-height:200px; overflow-y:auto; border-radius:5px;">
			<strong style="color:#ff0;">DEBUG LOG:</strong><br>
			<div id="logContent"></div>
		</div>

		<div class="form-group">
			<div class="input-group flex-wrap">
				{{-- Button Debug --}}
				<button type="button"
					class="btn btn-warning btn-sm me-2 mb-2"
					onclick="toggleDebugLog()">
					🔍 Toggle Debug
				</button>

				{{-- Button Thermal --}}
				<button type="button"
					class="btn btn-success btn-print me-2 mb-2"
					onclick="handleThermalPrint()"
					{{ !$statusPrint ? 'disabled' : '' }}>
					<i class="ri-printer-line"></i> Print 1.2
				</button>

				{{-- Button Normal --}}
				<button type="button"
					class="btn btn-outline-secondary btn-print mb-2"
					wire:click="printNormal"
					{{ !$statusPrint ? 'disabled' : '' }}>
					<i class="ri-printer-line"></i> Print Normal
				</button>

                <button type="button"
                    class="btn btn-info btn-sm me-2 mb-2"
                    onclick="scanPrinterUUID()">
                    🔬 Scan UUID Epson
                </button>

				<div class="w-100"></div>
				<small class="text-info">
					💡 Support: Printer Panda & Epson TM-P20II<br>
					🔍 Klik "Toggle Debug" untuk lihat error
				</small>
			</div>
		</div>
	</div>
	<div class="col-lg-2"></div>
</div>
{{-- Script Normal Print - TETAP --}}
@script
<script>
	$wire.on('redirectToPrint', (produk_asemblyid) => {
		var printUrl = '{{ route('report-gentan') }}?produk_asemblyid=' + produk_asemblyid
		window.open(printUrl, '_blank');
	});
</script>
@endscript

{{-- Thermal Module - DENGAN DEBUG LENGKAP --}}
<script>
// ===== DEBUG LOGGER =====
window.debugLog = function(msg, type = 'info') {
    const logDiv = document.getElementById('logContent');
    if (!logDiv) {
        alert('Debug log div not found!');
        return;
    }

    const colors = {
        'info': '#0ff',
        'success': '#0f0',
        'error': '#f00',
        'warn': '#ff0'
    };

    const timestamp = new Date().toLocaleTimeString();
    const color = colors[type] || '#fff';

    logDiv.innerHTML += `<span style="color:${color}">[${timestamp}] ${msg}</span><br>`;
    logDiv.scrollTop = logDiv.scrollHeight;
};

window.toggleDebugLog = function() {
    const debugDiv = document.getElementById('debugLog');
    if (debugDiv) {
        debugDiv.style.display = debugDiv.style.display === 'none' ? 'block' : 'none';
    }
};

// ===== MAIN PRINT HANDLER - DENGAN DEBUG MAKSIMAL =====
window.handleThermalPrint = async function() {
    const debugDiv = document.getElementById('debugLog');
    if (debugDiv) {
        debugDiv.style.display = 'block';
        debugDiv.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    window.debugLog('=== MEMULAI PRINT ===', 'warn');

    // CEK BLUETOOTH API
    if (!('bluetooth' in navigator)) {
        window.debugLog('❌ Bluetooth tidak tersedia!', 'error');
        alert('❌ Browser tidak support Bluetooth');
        return;
    }
    window.debugLog('✅ Bluetooth API tersedia', 'success');

    try {
        // CEK GLOBAL SCRIPT
        if (typeof window.checkPrinterReady === 'undefined') {
            window.debugLog('❌ Global script belum load!', 'error');
            alert('❌ Error: Global script belum load. Refresh halaman.');
            return;
        }
        window.debugLog('✅ Global script loaded', 'success');

        // Get Livewire component
        const wireElement = document.querySelector('[wire\\:id]');
        if (!wireElement) {
            window.debugLog('❌ Livewire element tidak ditemukan!', 'error');
            throw new Error('Livewire element not found');
        }

        const component = window.Livewire.find(wireElement.getAttribute('wire:id'));
        if (!component) {
            window.debugLog('❌ Livewire component tidak ditemukan!', 'error');
            throw new Error('Livewire component not found');
        }
        window.debugLog('✅ Component found', 'success');

        // Collect print data
        const printData = {
            gentan_no: component.get('gentan_no'),
            lpk_no: component.get('lpk_no'),
            product_name: component.get('product_name'),
            code: component.get('code'),
            code_alias: component.get('code_alias'),
            production_date: component.get('production_date'),
            work_hour: component.get('work_hour'),
            work_shift: component.get('work_shift'),
            machineno: component.get('machineno'),
            berat_produksi: component.get('berat_produksi'),
            panjang_produksi: component.get('product_panjang'),
            selisih: component.get('selisih'),
            nomor_han: component.get('nomor_han'),
            nik: component.get('nik'),
            empname: component.get('empname'),
        };

        window.debugLog('=== DATA PRINT ===', 'warn');
        window.debugLog('gentan_no: ' + (printData.gentan_no || 'KOSONG'), printData.gentan_no ? 'success' : 'error');
        window.debugLog('lpk_no: ' + (printData.lpk_no || 'KOSONG'), printData.lpk_no ? 'success' : 'error');
        window.debugLog('product_name: ' + (printData.product_name || 'KOSONG'), printData.product_name ? 'success' : 'error');

        // CEK LOCALSTORAGE
        window.debugLog('=== LOCALSTORAGE ===', 'warn');
        const savedId = localStorage.getItem('thermal_printer_id');
        const savedName = localStorage.getItem('thermal_printer_name');
        window.debugLog('printer_id: ' + (savedId || 'KOSONG'), savedId ? 'info' : 'error');
        window.debugLog('printer_name: ' + (savedName || 'KOSONG'), savedName ? 'info' : 'error');

        // CEK PRINTER READY
        window.debugLog('=== CEK PRINTER ===', 'warn');
        window.debugLog('🔍 Memanggil checkPrinterReady()...', 'info');

        const printerReady = await window.checkPrinterReady();

        window.debugLog('Hasil: ' + (printerReady ? 'READY' : 'NOT READY'), printerReady ? 'success' : 'error');

        if (!printerReady) {
            window.debugLog('⚠️ Printer belum ready, minta pairing...', 'warn');
            await window.connectThermalPrinter();
            window.debugLog('✅ Pairing selesai', 'success');

            // Cek localStorage lagi setelah pairing
            const newSavedId = localStorage.getItem('thermal_printer_id');
            const newSavedName = localStorage.getItem('thermal_printer_name');
            window.debugLog('Setelah pairing - ID: ' + (newSavedId || 'GAGAL SAVE'), newSavedId ? 'success' : 'error');
            window.debugLog('Setelah pairing - Name: ' + (newSavedName || 'GAGAL SAVE'), newSavedName ? 'success' : 'error');

            await new Promise(r => setTimeout(r, 500));
        }

        // CEK CHARACTERISTIC
        window.debugLog('=== CEK CHARACTERISTIC ===', 'warn');
        if (window.thermalPrinter && window.thermalPrinter.characteristic) {
            window.debugLog('✅ Characteristic OK', 'success');
        } else {
            window.debugLog('❌ Characteristic NULL atau undefined', 'error');
            throw new Error('Printer characteristic not available');
        }

        // PRINT
        window.debugLog('=== MULAI PRINT ===', 'warn');
        window.debugLog('🖨️ Mengirim data ke printer...', 'info');

        await window.printToThermalPrinter(printData);

        window.debugLog('✅ PRINT SELESAI!', 'success');
        window.debugLog('===========================', 'warn');

        // Success message
        alert('✅ Print berhasil!');

    } catch (error) {
        window.debugLog('❌ ERROR: ' + error.message, 'error');
        if (error.stack) {
            window.debugLog('Stack: ' + error.stack.substring(0, 200), 'error');
        }

        if (confirm('❌ Print gagal: ' + error.message + '\n\nGunakan Print Normal?')) {
            const component = window.Livewire.find(
                document.querySelector('[wire\\:id]').getAttribute('wire:id')
            );
            component.call('printNormal');
        }
    }
};
</script>
