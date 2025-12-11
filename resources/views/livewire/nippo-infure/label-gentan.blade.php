<div class="row mt-3">
	<div class="col-lg-2"></div>
	<div class="col-lg-8">
		<div class="form-group">
			<div class="input-group">
				<label class="control-label col-12 col-lg-3 text-muted fw-bold">Nomor LPK</label>
				{{-- <input type="text" wire:model.change="lpk_no" class="form-control" placeholder="000000-000"/> --}}
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
				<input type="text"  wire:model.change="gentan_no" class="form-control" placeholder="..." />
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
				<span class="input-group-text">
					meter
				</span>
			</div>
		</div>
		<div class="form-group mt-1">
			<div class="input-group">
				<label class="control-label col-12 col-lg-3 text-muted fw-bold">Berat Gentan</label>
				<input type="text" wire:model="berat_produksi" class="form-control readonly currency bg-light" readonly="readonly" />
				<span class="input-group-text">
					kg
				</span>
			</div>
		</div>
		<div class="form-group mt-1">
			<div class="input-group">
				<label class="control-label col-12 col-lg-3 text-muted fw-bold">Berat Standard</label>
				<input type="text" wire:model="berat_standard" class="form-control readonly currency bg-light" readonly="readonly" />
				<span class="input-group-text">
					kg
				</span>
			</div>
		</div>
		<div class="form-group mt-1">
			<div class="input-group">
				<label class="control-label col-12 col-lg-3 text-muted fw-bold">Tanggal LPK</label>
				{{-- <input class="form-control readonly datepicker-input bg-light" readonly="readonly" type="date" wire:model="lpk_date" /> --}}
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
		<div class="form-group">
			<div class="input-group">
				<!-- Thermal Printer Button (Primary) -->
				<button type="button"
					class="btn btn-success btn-print me-2"
					wire:click="print"
					{{ !$statusPrint ? 'disabled' : '' }}>
					<i class="ri-printer-line"></i> Print Thermal
				</button>

				<!-- Fallback Normal Print Button -->
				<button type="button"
					class="btn btn-outline-secondary btn-print"
					wire:click="printNormal"
					{{ !$statusPrint ? 'disabled' : '' }}>
					<i class="ri-printer-line"></i> Print Normal
				</button>

				<div style="float:right" class="text-danger">
					Thermal Printer (58mm/80mm) atau A4-Portrait
				</div>
			</div>
		</div>
	</div>
	<div class="col-lg-2"></div>
</div>
@script
<script>
// ============================================
// THERMAL PRINTER - BLUETOOTH AUTO PRINT
// ============================================

// Konfigurasi Thermal Printer
const THERMAL_CONFIG = {
    // UUID standar untuk thermal printer (ESC/POS compatible)
    serviceUUID: '000018f0-0000-1000-8000-00805f9b34fb',
    characteristicUUID: '00002af1-0000-1000-8000-00805f9b34fb',

    // Alternatif UUID (jika printer Anda berbeda)
    // serviceUUID: '49535343-fe7d-4ae5-8fa9-9fafd205e455',
    // characteristicUUID: '49535343-8841-43f4-a8d4-ecbe34729bb3',
};

// Menyimpan koneksi printer
let connectedDevice = null;
let printerCharacteristic = null;

// ============================================
// GENERATE ESC/POS COMMANDS
// ============================================
function generateEscPosCommands(data) {
    const ESC = '\x1B';
    const GS = '\x1D';

    let commands = '';

    // Initialize printer
    commands += ESC + '@';

    // Header - Bold & Center
    commands += ESC + 'a' + '\x01'; // Center align
    commands += GS + '!' + '\x11';   // Double size
    commands += 'LABEL GENTAN\n';
    commands += GS + '!' + '\x00';   // Normal size

    // Separator
    commands += ESC + 'a' + '\x00'; // Left align
    commands += '================================\n';

    // Content
    commands += 'LPK No      : ' + data.lpk_no + '\n';
    commands += 'Gentan No   : ' + data.gentan_no + '\n';
    commands += 'No Order    : ' + data.code + '\n';
    commands += '--------------------------------\n';
    commands += 'Produk      : ' + data.product_name + '\n';
    commands += '--------------------------------\n';
    commands += 'Panjang     : ' + data.panjang_produksi + ' m\n';
    commands += 'Berat       : ' + data.berat_produksi + ' kg\n';
    commands += 'Berat Std   : ' + data.berat_standard + ' kg\n';
    commands += '--------------------------------\n';
    commands += 'Tgl LPK     : ' + data.lpk_date + '\n';
    commands += 'Qty LPK     : ' + data.qty_lpk + '\n';
    commands += '--------------------------------\n';
    commands += 'Printed     : ' + data.timestamp + '\n';
    commands += '================================\n';

    // Feed & Cut
    commands += '\n\n\n';
    commands += GS + 'V' + '\x42' + '\x00'; // Cut paper (partial cut)

    return commands;
}

// ============================================
// BLUETOOTH CONNECTION
// ============================================
async function connectThermalPrinter() {
    try {
        console.log('🔍 Mencari thermal printer...');

        // Request Bluetooth device
        const device = await navigator.bluetooth.requestDevice({
            filters: [
                { services: [THERMAL_CONFIG.serviceUUID] },
                { name: 'BlueTooth Printer' },
                { namePrefix: 'BT' },
            ],
            optionalServices: [THERMAL_CONFIG.serviceUUID]
        });

        console.log('✅ Printer ditemukan:', device.name);

        // Connect to GATT Server
        const server = await device.gatt.connect();
        console.log('🔗 Terhubung ke GATT server');

        // Get Service
        const service = await server.getPrimaryService(THERMAL_CONFIG.serviceUUID);
        console.log('📡 Service ditemukan');

        // Get Characteristic
        const characteristic = await service.getCharacteristic(THERMAL_CONFIG.characteristicUUID);
        console.log('✅ Characteristic ready');

        // Save connection
        connectedDevice = device;
        printerCharacteristic = characteristic;

        return { device, characteristic };

    } catch (error) {
        console.error('❌ Bluetooth error:', error);
        throw error;
    }
}

// ============================================
// PRINT FUNCTION
// ============================================
async function printToThermalPrinter(data) {
    try {
        // Generate ESC/POS commands
        const escPosCommands = generateEscPosCommands(data);

        // Convert to bytes
        const encoder = new TextEncoder();
        const bytes = encoder.encode(escPosCommands);

        // Cek apakah sudah terkoneksi
        if (!printerCharacteristic) {
            console.log('🔄 Belum terkoneksi, connecting...');
            const connection = await connectThermalPrinter();
            printerCharacteristic = connection.characteristic;
        }

        // Kirim data ke printer (split jika terlalu besar)
        const chunkSize = 512; // Max bytes per transmission
        for (let i = 0; i < bytes.length; i += chunkSize) {
            const chunk = bytes.slice(i, i + chunkSize);
            await printerCharacteristic.writeValue(chunk);

            // Delay kecil antar chunk
            await new Promise(resolve => setTimeout(resolve, 100));
        }

        console.log('✅ Print berhasil!');

        // Notifikasi sukses
        window.dispatchEvent(new CustomEvent('notification', {
            detail: [{
                type: 'success',
                message: 'Label berhasil dicetak!'
            }]
        }));

        return true;

    } catch (error) {
        console.error('❌ Print error:', error);

        // Reset connection jika error
        connectedDevice = null;
        printerCharacteristic = null;

        // Notifikasi error
        window.dispatchEvent(new CustomEvent('notification', {
            detail: [{
                type: 'error',
                message: 'Gagal mencetak: ' + error.message
            }]
        }));

        return false;
    }
}

// ============================================
// LIVEWIRE EVENT LISTENER
// ============================================
document.addEventListener('livewire:initialized', () => {
    // Listen untuk event print dari Livewire
    Livewire.on('printThermalLabel', async (printData) => {
        console.log('📝 Data print diterima:', printData);

        // Cek support Bluetooth
        if (!('bluetooth' in navigator)) {
            alert('Browser Anda tidak support Web Bluetooth API.\nGunakan Chrome/Edge di Android atau Chrome di Desktop.');
            return;
        }

        // Auto print
        try {
            await printToThermalPrinter(printData[0]); // Livewire kirim array
        } catch (error) {
            console.error('Print gagal:', error);

            // Fallback: tanya user mau coba lagi atau print normal
            if (confirm('Print thermal gagal. Coba lagi?\n\nKlik Cancel untuk print normal.')) {
                await printToThermalPrinter(printData[0]);
            } else {
                // Fallback ke print normal
                $wire.printNormal();
            }
        }
    });
});

// ============================================
// FALLBACK PRINT NORMAL
// ============================================
$wire.on('redirectToPrint', (produk_asemblyid) => {
    var printUrl = '{{ route('report-gentan') }}?produk_asemblyid=' + produk_asemblyid;
    window.open(printUrl, '_blank');
});

// ============================================
// DISCONNECT ON PAGE UNLOAD
// ============================================
window.addEventListener('beforeunload', () => {
    if (connectedDevice && connectedDevice.gatt.connected) {
        connectedDevice.gatt.disconnect();
        console.log('🔌 Printer disconnected');
    }
});
</script>
@endscript

