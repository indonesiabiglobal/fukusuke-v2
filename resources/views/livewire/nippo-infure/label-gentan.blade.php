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
{{-- Thermal Module - AUTO RECONNECT tanpa pilih device lagi --}}
<script>
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

        // ===== UUID EPSON TM-P20II =====
        window.THERMAL_UUID_CONFIGS = [
            {
                name: 'Epson TM-P20II',
                serviceUUID: '49535343-fe7d-4ae5-8fa9-9fafd205e455',
                characteristicUUID: '49535343-1e4d-4bd9-ba61-23c647249616',
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

        // Generate ESC/POS commands
        window.generateEscPosCommands = function(data) {
            const ESC = '\x1B';
            const GS = '\x1D';
            let cmd = '';

            cmd += ESC + '@'; // Initialize

            // GENTAN NO (BESAR)
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

            // SEPARATOR
            cmd += ESC + 'a' + String.fromCharCode(0);
            cmd += '================================\n';

            // LPK NO
            cmd += ESC + 'a' + String.fromCharCode(1);
            cmd += GS + '!' + String.fromCharCode(0x11);
            cmd += (data.lpk_no || '-') + '\n';
            cmd += GS + '!' + String.fromCharCode(0);
            cmd += ESC + 'a' + String.fromCharCode(0);
            cmd += '================================\n';

            // NAMA PRODUK
            cmd += ESC + 'a' + String.fromCharCode(1);
            cmd += (data.product_name || '-') + '\n';
            cmd += ESC + 'a' + String.fromCharCode(0);
            cmd += '--------------------------------\n';

            // NO ORDER & KODE
            cmd += 'No. Order   : ' + (data.code || '-') + '\n';
            cmd += 'Kode        : ' + (data.code_alias || '-') + '\n';
            cmd += '--------------------------------\n';

            // TANGGAL PRODUKSI
            cmd += 'Tgl Prod    : ' + (data.production_date || '-') + '\n';
            cmd += 'Jam         : ' + (data.work_hour || '-') + '\n';
            cmd += 'Shift       : ' + (data.work_shift || '-') + '\n';
            cmd += 'Mesin       : ' + (data.machineno || '-') + '\n';
            cmd += '--------------------------------\n';

            // BERAT & PANJANG
            cmd += 'Berat       : ' + (data.berat_produksi || '0') + '\n';
            cmd += 'Panjang     : ' + (data.panjang_produksi || '0') + '\n';
            cmd += 'Lebih       : ' + (data.selisih || '0') + '\n';
            cmd += 'No Han      : ' + (data.nomor_han || '-') + '\n';
            cmd += '--------------------------------\n';

            // NIK & NAMA
            cmd += 'NIK         : ' + (data.nik || '-') + '\n';
            cmd += 'Nama        : ' + (data.empname || '-') + '\n';
            cmd += '================================\n\n\n';

            // Cut paper
            cmd += GS + 'V' + String.fromCharCode(66, 0);

            return cmd;
        };

        // Reconnect function
        window.reconnectSavedDevice = async function() {
            if (!window.savedDeviceId) {
                console.log('📍 No saved device');
                return false;
            }

            try {
                console.log('🔄 Trying to reconnect...');
                const devices = await navigator.bluetooth.getDevices();
                const savedDevice = devices.find(d => d.id === window.savedDeviceId);

                if (!savedDevice) {
                    window.savedDeviceId = null;
                    localStorage.removeItem('thermal_printer_id');
                    return false;
                }

                if (savedDevice.gatt.connected) {
                    window.connectedDevice = savedDevice;
                    const config = window.THERMAL_UUID_CONFIGS[0]; // Always use Epson
                    const service = await savedDevice.gatt.getPrimaryService(config.serviceUUID);
                    const characteristic = await service.getCharacteristic(config.characteristicUUID);
                    window.printerCharacteristic = characteristic;
                    return true;
                }

                const server = await savedDevice.gatt.connect();
                const config = window.THERMAL_UUID_CONFIGS[0];
                const service = await server.getPrimaryService(config.serviceUUID);
                const characteristic = await service.getCharacteristic(config.characteristicUUID);

                window.connectedDevice = savedDevice;
                window.printerCharacteristic = characteristic;
                return true;

            } catch (error) {
                window.savedDeviceId = null;
                localStorage.removeItem('thermal_printer_id');
                return false;
            }
        };

        // Connect function
        window.connectThermalPrinter = async function() {
            try {
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
                localStorage.setItem('thermal_printer_id', device.id);

                console.log('✅ Printer connected:', device.name);
                return true;

            } catch (error) {
                console.error('❌ Connection failed:', error);
                throw error;
            }
        };

        // Print function
        window.printToThermalPrinter = async function(data) {
            console.log('🖨️ Printing...');

            const commands = window.generateEscPosCommands(data);
            const encoder = new TextEncoder();
            const bytes = encoder.encode(commands);

            if (!window.printerCharacteristic) {
                const reconnected = await window.reconnectSavedDevice();
                if (!reconnected) {
                    await window.connectThermalPrinter();
                }
            }

            const chunkSize = 512;
            for (let i = 0; i < bytes.length; i += chunkSize) {
                const chunk = bytes.slice(i, i + chunkSize);
                await window.printerCharacteristic.writeValue(chunk);
                await new Promise(r => setTimeout(r, 100));
            }

            console.log('✅ Print complete!');

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

        // Main handler - DENGAN DEBUG LOG LENGKAP
        window.handleThermalPrint = async function() {
            if (!('bluetooth' in navigator)) {
                alert('❌ Browser tidak support Bluetooth.\n\nGunakan Print Normal.');
                return;
            }

            try {
                if (!window.savedDeviceId) {
                    window.savedDeviceId = localStorage.getItem('thermal_printer_id');
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

                // ===== DEBUG LOG - CEK SEMUA DATA =====
                console.log('===================');
                console.log('PRINT DATA CHECK:');
                console.log('gentan_no:', printData.gentan_no);
                console.log('lpk_no:', printData.lpk_no);
                console.log('product_name:', printData.product_name);
                console.log('code:', printData.code);
                console.log('code_alias:', printData.code_alias);
                console.log('production_date:', printData.production_date);
                console.log('work_hour:', printData.work_hour);
                console.log('work_shift:', printData.work_shift);
                console.log('machineno:', printData.machineno);
                console.log('berat_produksi:', printData.berat_produksi);
                console.log('panjang_produksi:', printData.panjang_produksi);
                console.log('selisih:', printData.selisih);
                console.log('nomor_han:', printData.nomor_han);
                console.log('nik:', printData.nik);
                console.log('empname:', printData.empname);
                console.log('===================');

                await window.printToThermalPrinter(printData);

            } catch (error) {
                console.error('❌ Error:', error);

                let errorMsg = '❌ Printer error\n\nGunakan Print Normal?';

                if (confirm(errorMsg)) {
                    component.call('printNormal');
                }
            }
        };

        window.savedDeviceId = localStorage.getItem('thermal_printer_id');

        console.log('✅ Thermal module loaded (Epson TM-P20II)');

    } catch (error) {
        console.error('❌ Init failed:', error);
        window.handleThermalPrint = function() {
            alert('Thermal print error. Gunakan Print Normal.');
        };
    }
})();
</script>
