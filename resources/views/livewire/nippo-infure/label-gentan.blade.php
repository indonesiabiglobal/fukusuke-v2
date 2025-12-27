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
					<i class="ri-printer-line"></i> Print Thermal 1.8
				</button>

				{{-- Button Normal --}}
				<button type="button"
					class="btn btn-outline-secondary btn-print mb-2"
					wire:click="printNormal"
					{{ !$statusPrint ? 'disabled' : '' }}>
					<i class="ri-printer-line"></i> Print Normal
				</button>

                {{-- <button type="button"
                    class="btn btn-info btn-sm me-2 mb-2"
                    onclick="scanPrinterUUID()">
                    🔬 Scan UUID Epson
                </button> --}}

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
<script>
// ===== DEBUG LOGGER VISIBLE =====
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
    } else {
        debugDiv.style.display = 'none';
    }
};

(function() {
    if (typeof window === 'undefined' || typeof navigator === 'undefined') {
        console.error('Critical: Window/Navigator not available');
        return;
    }

    if (window.thermalPrinterLoaded) {
        console.log('✅ Thermal module already loaded');
        return;
    }

    try {
        window.thermalPrinterLoaded = true;

        const hasBluetoothAPI = 'bluetooth' in navigator;

        if (!hasBluetoothAPI) {
            console.warn('⚠️ Bluetooth API not available');
            window.handleThermalPrint = function() {
                alert('Thermal print tidak tersedia di aplikasi ini.\n\nGunakan Print Normal.');
            };
            return;
        }

        // UUID EPSON TM-P20II
        window.THERMAL_UUID_CONFIGS = [
            {
                name: 'Epson TM-P20II',
                serviceUUID: '49535343-fe7d-4ae5-8fa9-9fafd205e455',
                characteristicUUID: '49535343-1e4d-4bd9-ba61-23c647249616',
            },
        ];

        // ===== INITIALIZE VARIABLES =====
        window.connectedDevice = null;
        window.printerCharacteristic = null;
        window.savedDeviceId = null;
        window.savedConfigIndex = 0;

        // Generate ESC/POS commands
        window.generateEscPosCommands = function(data) {
            const ESC = '\x1B';
            const GS = '\x1D';
            let cmd = '';

            // Initialize
            cmd += ESC + '@';
            cmd += ESC + 'R' + String.fromCharCode(0);

            // ========== GENTAN NO (TRIPLE SIZE) - KIRI ==========
            cmd += ESC + 'a' + String.fromCharCode(0); // Left align
            cmd += GS + '!' + String.fromCharCode(0x33); // Triple
            cmd += String(data.gentan_no || '0') + '\n';
            cmd += GS + '!' + String.fromCharCode(0); // Reset
            cmd += '\n';

            // ========== QR CODE (LPK NO) - CENTER ==========
            cmd += ESC + 'a' + String.fromCharCode(1); // Center align

            const qrData = String(data.lpk_no || '000000-000');

            // QR Code Model 2
            cmd += GS + '(k' + String.fromCharCode(4, 0, 49, 65, 50, 0);

            // QR Code Size (6 = medium)
            cmd += GS + '(k' + String.fromCharCode(3, 0, 49, 67, 6);

            // QR Code Error Correction (M level)
            cmd += GS + '(k' + String.fromCharCode(3, 0, 49, 69, 49);

            // Store QR data
            const qrLen = qrData.length + 3;
            const pL = qrLen % 256;
            const pH = Math.floor(qrLen / 256);
            cmd += GS + '(k' + String.fromCharCode(pL, pH, 49, 80, 48) + qrData;

            // Print QR Code
            cmd += GS + '(k' + String.fromCharCode(3, 0, 49, 81, 48);

            cmd += '\n\n';
            cmd += ESC + 'a' + String.fromCharCode(0); // Back to left align

            // ========== LPK NO (DOUBLE SIZE) ==========
            cmd += '================================\n';
            cmd += ESC + 'a' + String.fromCharCode(1); // Center
            cmd += GS + '!' + String.fromCharCode(0x11); // Double
            cmd += String(data.lpk_no || '-') + '\n';
            cmd += GS + '!' + String.fromCharCode(0); // Reset
            cmd += ESC + 'a' + String.fromCharCode(0); // Left
            cmd += '================================\n';

            // ========== PRODUCT NAME (BOLD, NORMAL SIZE) ==========
            cmd += ESC + 'E' + String.fromCharCode(1); // Bold ON
            cmd += String(data.product_name || '-') + '\n';
            cmd += ESC + 'E' + String.fromCharCode(0); // Bold OFF
            cmd += '--------------------------------\n';

            // ========== DETAIL (NORMAL SIZE) ==========
            cmd += 'No. Order   : ' + String(data.code || '-') + '\n';
            cmd += 'Kode        : ' + String(data.code_alias || '-') + '\n';
            cmd += 'Tgl Prod    : ' + String(data.production_date || '-') + '\n';
            cmd += 'Jam         : ' + String(data.work_hour || '-') + '\n';
            cmd += 'Shift       : ' + String(data.work_shift || '-') + '\n';
            cmd += 'Mesin       : ' + String(data.machineno || '-') + '\n';
            cmd += '--------------------------------\n';

            // ========== BERAT & PANJANG (BOLD) ==========
            cmd += ESC + 'E' + String.fromCharCode(1); // Bold ON
            cmd += 'Berat       : ' + String(data.berat_produksi || '0') + '\n';
            cmd += 'Panjang     : ' + String(data.panjang_produksi || '0') + '\n';
            cmd += ESC + 'E' + String.fromCharCode(0); // Bold OFF

            cmd += 'Lebih       : ' + String(data.selisih || '0') + '\n';
            cmd += 'No Han      : ' + String(data.nomor_han || '-') + '\n';
            cmd += '--------------------------------\n';

            // ========== NIK & NAMA ==========
            cmd += 'NIK         : ' + String(data.nik || '-') + '\n';
            cmd += 'Nama        : ' + String(data.empname || '-') + '\n';
            cmd += '================================\n';
            cmd += '\n\n\n';

            // Cut
            cmd += GS + 'V' + String.fromCharCode(0);

            return cmd;
        };

        // Reconnect
        // window.reconnectSavedDevice = async function() {
        //     if (!window.savedDeviceId) {
        //         window.debugLog('📍 No saved device', 'info');
        //         return false;
        //     }

        //     try {
        //         window.debugLog('🔄 Reconnecting to saved device...', 'info');

        //         const devices = await navigator.bluetooth.getDevices();
        //         const savedDevice = devices.find(d => d.id === window.savedDeviceId);

        //         if (!savedDevice) {
        //             window.debugLog('❌ Saved device not found in paired devices', 'warn');
        //             window.savedDeviceId = null;
        //             localStorage.removeItem('thermal_printer_id');
        //             localStorage.removeItem('thermal_config_index');
        //             return false;
        //         }

        //         window.debugLog('Found device: ' + savedDevice.name, 'success');

        //         if (savedDevice.gatt.connected) {
        //             window.debugLog('Already connected!', 'success');
        //             window.connectedDevice = savedDevice;

        //             const service = await savedDevice.gatt.getPrimaryService('49535343-fe7d-4ae5-8fa9-9fafd205e455');
        //             const characteristic = await service.getCharacteristic('49535343-1e4d-4bd9-ba61-23c647249616');
        //             window.printerCharacteristic = characteristic;

        //             return true;
        //         }

        //         window.debugLog('Connecting to GATT...', 'info');
        //         const server = await savedDevice.gatt.connect();

        //         window.debugLog('Getting service...', 'info');
        //         const service = await server.getPrimaryService('49535343-fe7d-4ae5-8fa9-9fafd205e455');

        //         window.debugLog('Getting characteristic...', 'info');
        //         const characteristic = await service.getCharacteristic('49535343-1e4d-4bd9-ba61-23c647249616');

        //         window.connectedDevice = savedDevice;
        //         window.printerCharacteristic = characteristic;

        //         window.debugLog('✅ Reconnected successfully!', 'success');
        //         return true;

        //     } catch (error) {
        //         window.debugLog('❌ Reconnect failed: ' + error.message, 'error');

        //         window.savedDeviceId = null;
        //         window.printerCharacteristic = null;
        //         window.connectedDevice = null;
        //         localStorage.removeItem('thermal_printer_id');
        //         localStorage.removeItem('thermal_config_index');

        //         return false;
        //     }
        // };

        window.reconnectSavedDevice = async function() {
            if (!window.savedDeviceId) {
                window.debugLog('📍 No saved device', 'info');
                return false;
            }

            try {
                window.debugLog('🔄 Reconnecting to saved device...', 'info');

                // ===== COBA GETDEVICES DULU =====
                let devices = [];
                try {
                    devices = await navigator.bluetooth.getDevices();
                } catch (e) {
                    window.debugLog('⚠️ getDevices() not supported', 'warn');
                }

                const savedDevice = devices.find(d => d.id === window.savedDeviceId);

                if (!savedDevice) {
                    window.debugLog('❌ Device not found, need manual selection', 'warn');

                    // ===== FALLBACK: REQUEST DEVICE LAGI =====
                    // Tapi ini butuh user gesture, jadi kita trigger dari handleThermalPrint
                    return false;
                }

                window.debugLog('Found device: ' + savedDevice.name, 'success');

                // Check if already connected
                if (savedDevice.gatt && savedDevice.gatt.connected) {
                    window.debugLog('Already connected!', 'success');
                    window.connectedDevice = savedDevice;

                    const service = await savedDevice.gatt.getPrimaryService('49535343-fe7d-4ae5-8fa9-9fafd205e455');
                    const characteristic = await service.getCharacteristic('49535343-1e4d-4bd9-ba61-23c647249616');
                    window.printerCharacteristic = characteristic;

                    return true;
                }

                // Reconnect GATT
                window.debugLog('Connecting to GATT...', 'info');
                const server = await savedDevice.gatt.connect();

                window.debugLog('Getting service...', 'info');
                const service = await server.getPrimaryService('49535343-fe7d-4ae5-8fa9-9fafd205e455');

                window.debugLog('Getting characteristic...', 'info');
                const characteristic = await service.getCharacteristic('49535343-1e4d-4bd9-ba61-23c647249616');

                window.connectedDevice = savedDevice;
                window.printerCharacteristic = characteristic;

                window.debugLog('✅ Reconnected successfully!', 'success');
                return true;

            } catch (error) {
                window.debugLog('❌ Reconnect failed: ' + error.message, 'error');

                // JANGAN CLEAR savedDeviceId - BIAR BISA COBA LAGI
                window.printerCharacteristic = null;
                window.connectedDevice = null;

                return false;
            }
        };

        // Connect
        window.connectThermalPrinter = async function() {
            const device = await navigator.bluetooth.requestDevice({
                acceptAllDevices: true,
                optionalServices: ['49535343-fe7d-4ae5-8fa9-9fafd205e455']
            });

            const server = await device.gatt.connect();
            const service = await server.getPrimaryService('49535343-fe7d-4ae5-8fa9-9fafd205e455');
            const characteristic = await service.getCharacteristic('49535343-1e4d-4bd9-ba61-23c647249616');

            window.connectedDevice = device;
            window.printerCharacteristic = characteristic;
            window.savedDeviceId = device.id;
            window.savedConfigIndex = 0;

            localStorage.setItem('thermal_printer_id', device.id);
            localStorage.setItem('thermal_config_index', '0');

            window.debugLog('✅ Printer tersimpan: ' + device.name, 'success');

            return true;
        };

        // Print
        window.printToThermalPrinter = async function(data) {
            window.debugLog('Generating commands...', 'info');
            const commands = window.generateEscPosCommands(data);

            window.debugLog('Command length: ' + commands.length + ' chars', 'info');

            const encoder = new TextEncoder();
            const bytes = encoder.encode(commands);

            window.debugLog('Bytes length: ' + bytes.length, 'info');

            if (!window.printerCharacteristic || !window.connectedDevice) {
                window.debugLog('No connection, trying to reconnect...', 'warn');

                const reconnected = await window.reconnectSavedDevice();

                if (!reconnected) {
                    window.debugLog('Reconnect failed, asking for new device...', 'warn');
                    await window.connectThermalPrinter();
                }
            } else {
                window.debugLog('Using existing connection', 'success');
            }

            const chunkSize = 128;
            const totalChunks = Math.ceil(bytes.length / chunkSize);

            window.debugLog('Sending ' + totalChunks + ' chunks...', 'info');

            for (let i = 0; i < bytes.length; i += chunkSize) {
                const chunk = bytes.slice(i, i + chunkSize);
                await window.printerCharacteristic.writeValue(chunk);
                await new Promise(r => setTimeout(r, 200));
            }

            window.debugLog('All data sent!', 'success');
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

        // Main handler
        window.handleThermalPrint = async function() {
            document.getElementById('debugLog').style.display = 'block';

            window.debugLog('=== MEMULAI PRINT ===', 'warn');

            if (!('bluetooth' in navigator)) {
                window.debugLog('❌ Bluetooth tidak tersedia!', 'error');
                alert('❌ Browser tidak support Bluetooth');
                return;
            }

            try {
                const component = window.Livewire.find(
                    document.querySelector('[wire\\:id]').getAttribute('wire:id')
                );

                window.debugLog('Component found', 'success');

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
                window.debugLog('code: ' + (printData.code || 'KOSONG'), printData.code ? 'success' : 'error');
                window.debugLog('code_alias: ' + (printData.code_alias || 'KOSONG'), printData.code_alias ? 'success' : 'error');
                window.debugLog('production_date: ' + (printData.production_date || 'KOSONG'), printData.production_date ? 'success' : 'error');
                window.debugLog('work_hour: ' + (printData.work_hour || 'KOSONG'), printData.work_hour ? 'success' : 'error');
                window.debugLog('work_shift: ' + (printData.work_shift || 'KOSONG'), printData.work_shift ? 'success' : 'error');
                window.debugLog('machineno: ' + (printData.machineno || 'KOSONG'), printData.machineno ? 'success' : 'error');
                window.debugLog('berat_produksi: ' + (printData.berat_produksi || 'KOSONG'), printData.berat_produksi ? 'success' : 'error');
                window.debugLog('panjang_produksi: ' + (printData.panjang_produksi || 'KOSONG'), printData.panjang_produksi ? 'success' : 'error');
                window.debugLog('selisih: ' + (printData.selisih || 'KOSONG'), printData.selisih ? 'success' : 'error');
                window.debugLog('nomor_han: ' + (printData.nomor_han || 'KOSONG'), printData.nomor_han ? 'success' : 'error');
                window.debugLog('nik: ' + (printData.nik || 'KOSONG'), printData.nik ? 'success' : 'error');
                window.debugLog('empname: ' + (printData.empname || 'KOSONG'), printData.empname ? 'success' : 'error');
                window.debugLog('==============================', 'warn');

                window.debugLog('🖨️ Mulai print...', 'info');
                await window.printToThermalPrinter(printData);
                window.debugLog('✅ Print selesai!', 'success');

            } catch (error) {
                window.debugLog('❌ ERROR: ' + error.message, 'error');
                console.error(error);

                if (confirm('❌ Print error\n\nGunakan Print Normal?')) {
                    component.call('printNormal');
                }
            }
        };

        // ===== LOAD SAVED DEVICE - CUMA SEKALI DI SINI =====
        window.savedDeviceId = localStorage.getItem('thermal_printer_id');
        window.savedConfigIndex = parseInt(localStorage.getItem('thermal_config_index') || '0');

        if (window.savedDeviceId) {
            console.log('✅ Found saved printer:', window.savedDeviceId);
        } else {
            console.log('📍 No saved printer');
        }

        console.log('✅ Thermal module loaded');

    } catch (error) {
        console.error('❌ Init failed:', error);
    }
})();
</script>
