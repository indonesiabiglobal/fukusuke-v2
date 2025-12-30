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
					<i class="ri-printer-line"></i> Print 1.4
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

{{-- Thermal Module - UPDATED UNTUK GLOBAL SCRIPT --}}
<script>
// ===== DEBUG LOGGER =====
window.debugLog = function(msg, type = 'info') {
    const logDiv = document.getElementById('logContent');
    if (!logDiv) return;

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

    console.log(msg);
};

window.toggleDebugLog = function() {
    const debugDiv = document.getElementById('debugLog');
    if (debugDiv) {
        debugDiv.style.display = debugDiv.style.display === 'none' ? 'block' : 'none';
    }
};

// ===== MAIN PRINT HANDLER =====
window.handleThermalPrint = async function() {
    const debugDiv = document.getElementById('debugLog');
    if (debugDiv) {
        debugDiv.style.display = 'block';
    }

    window.debugLog('=== MEMULAI PRINT ===', 'warn');

    if (!('bluetooth' in navigator)) {
        window.debugLog('❌ Bluetooth tidak tersedia!', 'error');
        alert('❌ Browser tidak support Bluetooth');
        return;
    }

    try {
        // Get Livewire component
        const component = window.Livewire.find(
            document.querySelector('[wire\\:id]').getAttribute('wire:id')
        );

        window.debugLog('Component found', 'success');

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

        window.debugLog('=== DATA YANG AKAN DICETAK ===', 'warn');
        window.debugLog('gentan_no: ' + (printData.gentan_no || 'KOSONG'), printData.gentan_no ? 'success' : 'error');
        window.debugLog('lpk_no: ' + (printData.lpk_no || 'KOSONG'), printData.lpk_no ? 'success' : 'error');
        window.debugLog('product_name: ' + (printData.product_name || 'KOSONG'), printData.product_name ? 'success' : 'error');
        window.debugLog('==============================', 'warn');

        // ✅ CEK PRINTER READY (PAKAI FUNCTION DARI GLOBAL SCRIPT)
        window.debugLog('🔍 Checking printer status...', 'info');
        const printerReady = await window.checkPrinterReady();

        if (!printerReady) {
            window.debugLog('⚠️ Printer not ready, requesting pairing...', 'warn');
            await window.connectThermalPrinter();
            await new Promise(r => setTimeout(r, 500));
        }

        // Print
        window.debugLog('🖨️ Mulai print...', 'info');
        await window.printToThermalPrinter(printData);
        window.debugLog('✅ Print selesai!', 'success');

    } catch (error) {
        window.debugLog('❌ ERROR: ' + error.message, 'error');
        console.error(error);

        if (confirm('❌ Print error\n\nGunakan Print Normal?')) {
            const component = window.Livewire.find(
                document.querySelector('[wire\\:id]').getAttribute('wire:id')
            );
            component.call('printNormal');
        }
    }
};

// ===== SCAN PRINTER UUID =====
window.scanPrinterUUID = async function() {
    const debugDiv = document.getElementById('debugLog');
    if (debugDiv) {
        debugDiv.style.display = 'block';
        document.getElementById('logContent').innerHTML = '';
    }

    window.debugLog('=== SCANNING PRINTER ===', 'warn');

    try {
        window.debugLog('🔍 Requesting device...', 'info');

        const device = await navigator.bluetooth.requestDevice({
            acceptAllDevices: true,
            optionalServices: []
        });

        window.debugLog('✅ Device: ' + device.name, 'success');
        window.debugLog('ID: ' + device.id, 'info');

        window.debugLog('🔌 Connecting...', 'info');
        const server = await device.gatt.connect();

        window.debugLog('🔍 Getting services...', 'info');
        const services = await server.getPrimaryServices();

        window.debugLog('Found ' + services.length + ' services', 'success');

        for (let i = 0; i < services.length; i++) {
            const service = services[i];
            window.debugLog('--- Service ' + (i+1) + ' ---', 'warn');
            window.debugLog('UUID: ' + service.uuid, 'success');

            try {
                const chars = await service.getCharacteristics();
                window.debugLog('Chars: ' + chars.length, 'info');

                for (let j = 0; j < chars.length; j++) {
                    const char = chars[j];
                    window.debugLog('  Char: ' + char.uuid, 'success');

                    const props = [];
                    if (char.properties.write) props.push('WRITE');
                    if (char.properties.writeWithoutResponse) props.push('WRITE_NO_RESP');
                    if (char.properties.read) props.push('READ');
                    if (char.properties.notify) props.push('NOTIFY');

                    window.debugLog('  Props: ' + props.join(', '), 'info');
                }
            } catch (err) {
                window.debugLog('  Error: ' + err.message, 'error');
            }
        }

        window.debugLog('=== SCAN DONE ===', 'warn');
        device.gatt.disconnect();

    } catch (error) {
        window.debugLog('❌ ERROR: ' + error.message, 'error');
    }
};
</script>
