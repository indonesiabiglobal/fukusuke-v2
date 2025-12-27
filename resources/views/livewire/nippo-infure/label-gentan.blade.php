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
					<i class="ri-printer-line"></i> Print Thermal
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

{{-- Thermal Module - DENGAN BARCODE --}}
{{-- Thermal Module - AUTO RECONNECT tanpa pilih device lagi --}}
<script>
// ===== DEBUG LOGGER - TAMPIL DI LAYAR =====
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
    if (debugDiv.style.display === 'none') {
        debugDiv.style.display = 'block';
        window.debugLog('Debug mode ON', 'success');
    } else {
        debugDiv.style.display = 'none';
    }
};

(function() {
    if (typeof window === 'undefined' || typeof navigator === 'undefined') {
        window.debugLog('❌ Window/Navigator tidak tersedia', 'error');
        return;
    }

    if (window.thermalPrinterLoaded) {
        window.debugLog('✅ Module sudah loaded', 'info');
        return;
    }

    try {
        window.thermalPrinterLoaded = true;

        const hasBluetoothAPI = 'bluetooth' in navigator;
        window.debugLog('Browser: ' + navigator.userAgent.split(' ').pop(), 'info');
        window.debugLog('Bluetooth API: ' + (hasBluetoothAPI ? 'YES ✅' : 'NO ❌'), hasBluetoothAPI ? 'success' : 'error');

        if (!hasBluetoothAPI) {
            window.handleThermalPrint = function() {
                alert('❌ Bluetooth API tidak tersedia\n\nPakai Chrome/Edge dan aktifkan di chrome://flags');
            };
            return;
        }

        // UUID CONFIGS
        window.THERMAL_UUID_CONFIGS = [
            {
                name: 'Epson TM-P20II (Primary)',
                serviceUUID: '49535343-fe7d-4ae5-8fa9-9fafd205e455',
                characteristicUUID: '49535343-1e4d-4bd9-ba61-23c647249616',
            },
            {
                name: 'Epson TM-P20II (SPP)',
                serviceUUID: '00001101-0000-1000-8000-00805f9b34fb',
                characteristicUUID: '00002a19-0000-1000-8000-00805f9b34fb',
            },
            {
                name: 'Epson Serial',
                serviceUUID: '0000ffe0-0000-1000-8000-00805f9b34fb',
                characteristicUUID: '0000ffe1-0000-1000-8000-00805f9b34fb',
            },
            {
                name: 'Panda Thermal',
                serviceUUID: '000018f0-0000-1000-8000-00805f9b34fb',
                characteristicUUID: '00002af1-0000-1000-8000-00805f9b34fb',
            },
            {
                name: 'Generic Serial',
                serviceUUID: '0000fff0-0000-1000-8000-00805f9b34fb',
                characteristicUUID: '0000fff1-0000-1000-8000-00805f9b34fb',
            },
        ];

        window.connectedDevice = null;
        window.printerCharacteristic = null;
        window.currentConfigIndex = 0;
        window.savedDeviceId = null;

        window.debugLog('Total configs: ' + window.THERMAL_UUID_CONFIGS.length, 'info');

        // Generate ESC/POS commands
        window.generateEscPosCommands = function(data) {
            const ESC = '\x1B';
            const GS = '\x1D';
            let cmd = '';

            cmd += ESC + '@';
            cmd += ESC + 'a' + String.fromCharCode(0);
            cmd += GS + '!' + String.fromCharCode(0x33);
            cmd += (data.gentan_no || '-') + '\n';
            cmd += GS + '!' + String.fromCharCode(0);
            cmd += '\n';

            // QR CODE
            cmd += ESC + 'a' + String.fromCharCode(1);
            const qrData = data.lpk_no || '251030-070';
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
            cmd += '================================\n';
            cmd += ESC + 'a' + String.fromCharCode(1);
            cmd += GS + '!' + String.fromCharCode(0x11);
            cmd += (data.lpk_no || '-') + '\n';
            cmd += GS + '!' + String.fromCharCode(0);
            cmd += ESC + 'a' + String.fromCharCode(0);
            cmd += '================================\n';

            cmd += ESC + 'a' + String.fromCharCode(1);
            cmd += (data.product_name || '-') + '\n';
            cmd += ESC + 'a' + String.fromCharCode(0);
            cmd += '--------------------------------\n';

            cmd += 'No. Order   : ' + (data.code || '-') + '\n';
            cmd += 'Kode        : ' + (data.code_alias || '-') + '\n';
            cmd += '--------------------------------\n';
            cmd += 'Tgl Prod    : ' + (data.production_date || '-') + '\n';
            cmd += 'Jam         : ' + (data.work_hour || '-') + '\n';
            cmd += 'Shift       : ' + (data.work_shift || '-') + '\n';
            cmd += 'Mesin       : ' + (data.machineno || '-') + '\n';
            cmd += '--------------------------------\n';
            cmd += 'Berat       : ' + (data.berat_produksi || '0') + '\n';
            cmd += 'Panjang     : ' + (data.panjang_produksi || '0') + '\n';
            cmd += 'Lebih       : ' + (data.selisih || '0') + '\n';
            cmd += 'No Han      : ' + (data.nomor_han || '-') + '\n';
            cmd += '--------------------------------\n';
            cmd += 'NIK         : ' + (data.nik || '-') + '\n';
            cmd += 'Nama        : ' + (data.empname || '-') + '\n';
            cmd += '================================\n\n\n';

            cmd += GS + 'V' + String.fromCharCode(66, 0);

            return cmd;
        };

        // Reconnect
        window.reconnectSavedDevice = async function() {
            if (!window.savedDeviceId) {
                window.debugLog('📍 No saved device', 'warn');
                return false;
            }

            try {
                window.debugLog('🔄 Reconnecting...', 'info');
                const devices = await navigator.bluetooth.getDevices();
                window.debugLog('Found ' + devices.length + ' paired devices', 'info');

                const savedDevice = devices.find(d => d.id === window.savedDeviceId);

                if (!savedDevice) {
                    window.debugLog('❌ Saved device not found', 'error');
                    window.savedDeviceId = null;
                    localStorage.removeItem('thermal_printer_id');
                    return false;
                }

                window.debugLog('Found: ' + savedDevice.name, 'success');

                if (savedDevice.gatt.connected) {
                    window.debugLog('✅ Already connected!', 'success');
                    window.connectedDevice = savedDevice;
                    const config = window.THERMAL_UUID_CONFIGS[window.currentConfigIndex];
                    const service = await savedDevice.gatt.getPrimaryService(config.serviceUUID);
                    const characteristic = await service.getCharacteristic(config.characteristicUUID);
                    window.printerCharacteristic = characteristic;
                    return true;
                }

                const server = await savedDevice.gatt.connect();
                window.debugLog('✅ Reconnected!', 'success');

                const config = window.THERMAL_UUID_CONFIGS[window.currentConfigIndex];
                const service = await server.getPrimaryService(config.serviceUUID);
                const characteristic = await service.getCharacteristic(config.characteristicUUID);

                window.connectedDevice = savedDevice;
                window.printerCharacteristic = characteristic;
                return true;

            } catch (error) {
                window.debugLog('❌ Reconnect error: ' + error.message, 'error');
                window.savedDeviceId = null;
                localStorage.removeItem('thermal_printer_id');
                return false;
            }
        };

        // Connect
        window.connectThermalPrinter = async function(manualConfig = null) {
    const config = manualConfig || window.THERMAL_UUID_CONFIGS[window.currentConfigIndex];
    window.debugLog('🔍 Trying: ' + config.name, 'info');
    window.debugLog('Service: ' + config.serviceUUID, 'info');

    try {
        let device;

        // LANGSUNG acceptAllDevices - lebih aman
        window.debugLog('Scanning all devices...', 'info');
        device = await navigator.bluetooth.requestDevice({
            acceptAllDevices: true,
            optionalServices: [
                config.serviceUUID,
                '49535343-fe7d-4ae5-8fa9-9fafd205e455',
                '00001101-0000-1000-8000-00805f9b34fb',
                '0000ffe0-0000-1000-8000-00805f9b34fb',
                '000018f0-0000-1000-8000-00805f9b34fb',
                '0000fff0-0000-1000-8000-00805f9b34fb',
            ]
        });

        if (!device) {
            throw new Error('No device selected');
        }

        window.debugLog('✅ Selected: ' + device.name, 'success');

        const server = await device.gatt.connect();
        window.debugLog('✅ GATT connected', 'success');

        // COBA SEMUA UUID - TIDAK PAKAI LOOP
        let service = null;
        let characteristic = null;

        // Try semua UUID satu per satu
        const allUUIDs = window.THERMAL_UUID_CONFIGS.map(c => ({
            service: c.serviceUUID,
            char: c.characteristicUUID
        }));

        for (let uuid of allUUIDs) {
            try {
                window.debugLog('Trying service: ' + uuid.service, 'info');
                service = await server.getPrimaryService(uuid.service);
                window.debugLog('✅ Service found!', 'success');

                characteristic = await service.getCharacteristic(uuid.char);
                window.debugLog('✅ Characteristic found!', 'success');

                // Berhasil!
                break;
            } catch (e) {
                window.debugLog('UUID failed: ' + e.message, 'warn');
                continue;
            }
        }

        if (!service || !characteristic) {
            throw new Error('No compatible service/characteristic found');
        }

        window.connectedDevice = device;
        window.printerCharacteristic = characteristic;
        window.savedDeviceId = device.id;
        localStorage.setItem('thermal_printer_id', device.id);

        window.debugLog('🎉 Printer ready: ' + device.name, 'success');
        return true;

    } catch (error) {
        window.debugLog('❌ Connection failed: ' + error.name, 'error');
        window.debugLog('Error: ' + error.message, 'error');
        throw error;
    }
};


        // Print
        window.printToThermalPrinter = async function(data) {
            window.debugLog('🖨️ Starting print...', 'info');

            const commands = window.generateEscPosCommands(data);
            const encoder = new TextEncoder();
            const bytes = encoder.encode(commands);

            window.debugLog('Data size: ' + bytes.length + ' bytes', 'info');

            if (!window.printerCharacteristic) {
                window.debugLog('No characteristic, trying reconnect...', 'warn');
                const reconnected = await window.reconnectSavedDevice();

                if (!reconnected) {
                    window.debugLog('Reconnect failed, asking for new device...', 'warn');
                    window.currentConfigIndex = 0;
                    await window.connectThermalPrinter();
                }
            }

            const chunkSize = 512;
            const totalChunks = Math.ceil(bytes.length / chunkSize);
            window.debugLog('Sending ' + totalChunks + ' chunks...', 'info');

            for (let i = 0; i < bytes.length; i += chunkSize) {
                const chunk = bytes.slice(i, i + chunkSize);
                const chunkNum = Math.floor(i / chunkSize) + 1;
                await window.printerCharacteristic.writeValue(chunk);
                window.debugLog('Chunk ' + chunkNum + '/' + totalChunks + ' sent', 'info');
                await new Promise(r => setTimeout(r, 100));
            }

            window.debugLog('✅ Print complete!', 'success');

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

        // Main handler
        window.handleThermalPrint = async function() {
    window.debugLog('=== PRINT REQUEST ===', 'info');

    if (!('bluetooth' in navigator)) {
        window.debugLog('❌ Bluetooth API tidak ada!', 'error');
        alert('❌ Browser tidak support Bluetooth\n\nPakai Chrome/Edge');
        return;
    }

    try {
        if (!window.savedDeviceId) {
            window.savedDeviceId = localStorage.getItem('thermal_printer_id');
            window.debugLog('Loaded saved ID: ' + (window.savedDeviceId || 'none'), 'info');
        }

        const component = window.Livewire.find(
            document.querySelector('[wire\\:id]').getAttribute('wire:id')
        );

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

        window.debugLog('Data OK: ' + printData.lpk_no, 'success');

        // PRINT LANGSUNG - TANPA RECONNECT LOOP
        if (!window.printerCharacteristic) {
            window.debugLog('No printer, connecting...', 'warn');
            await window.connectThermalPrinter();
        }

        await window.printToThermalPrinter(printData);

    } catch (error) {
        window.debugLog('❌ FATAL ERROR: ' + error.name, 'error');
        window.debugLog('Message: ' + error.message, 'error');

        let errorMsg = '❌ Error: ' + error.name + '\n\n';

        if (error.name === 'NetworkError') {
            errorMsg += '🔧 Printer tidak kompatibel dengan UUID\n\n';
            errorMsg += '💡 Coba:\n';
            errorMsg += '1. Restart printer\n';
            errorMsg += '2. Unpair & pair ulang Bluetooth\n';
            errorMsg += '3. Atau gunakan Print Normal\n\n';
        } else {
            errorMsg += error.message + '\n\n';
        }

        errorMsg += 'Gunakan Print Normal?';

        if (confirm(errorMsg)) {
            component.call('printNormal');
        }
    }
};

window.scanPrinterUUID = async function() {
    window.debugLog('=== SCANNING PRINTER ===', 'info');

    try {
        const device = await navigator.bluetooth.requestDevice({
            acceptAllDevices: true,
            optionalServices: [
                '49535343-fe7d-4ae5-8fa9-9fafd205e455',
                '00001101-0000-1000-8000-00805f9b34fb',
                '0000ffe0-0000-1000-8000-00805f9b34fb',
                '000018f0-0000-1000-8000-00805f9b34fb',
                '0000fff0-0000-1000-8000-00805f9b34fb',
            ]
        });

        window.debugLog('Device: ' + device.name, 'success');
        window.debugLog('ID: ' + device.id, 'info');

        const server = await device.gatt.connect();
        window.debugLog('✅ Connected!', 'success');

        const services = await server.getPrimaryServices();
        window.debugLog('Found ' + services.length + ' services', 'success');

        for (let service of services) {
            window.debugLog('===================', 'info');
            window.debugLog('Service: ' + service.uuid, 'warn');

            try {
                const chars = await service.getCharacteristics();
                for (let char of chars) {
                    window.debugLog('  Char: ' + char.uuid, 'success');
                    window.debugLog('  Props: ' + JSON.stringify(char.properties), 'info');
                }
            } catch (e) {
                window.debugLog('  Error: ' + e.message, 'error');
            }
        }

        alert('✅ Scan selesai!\n\nCek Debug Log untuk UUID yang benar');

    } catch (error) {
        window.debugLog('❌ Scan error: ' + error.message, 'error');
        alert('Error: ' + error.message);
    }
};

        window.savedDeviceId = localStorage.getItem('thermal_printer_id');

        window.debugLog('✅ Module loaded!', 'success');

    } catch (error) {
        window.debugLog('❌ Init error: ' + error.message, 'error');
        window.handleThermalPrint = function() {
            alert('Thermal print error. Gunakan Print Normal.');
        };
    }
})();
</script>

