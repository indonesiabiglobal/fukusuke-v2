{{-- FORM ASLI - JANGAN DIUBAH SAMA SEKALI --}}
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
		<div class="form-group">
			<div class="input-group">
				{{-- Tambah button thermal --}}
				<button type="button"
					class="btn btn-success btn-print me-2"
					onclick="handleThermalPrint()"
					{{ !$statusPrint ? 'disabled' : '' }}>
					<i class="ri-printer-line"></i> Print Thermal
				</button>

				{{-- Button asli tetap --}}
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

{{-- Script asli TETAP --}}
@script
<script>
	$wire.on('redirectToPrint', (produk_asemblyid) => {
		var printUrl = '{{ route('report-gentan') }}?produk_asemblyid=' + produk_asemblyid
		window.open(printUrl, '_blank');
	});
</script>
@endscript

{{-- Thermal Module - Tambahkan di AKHIR --}}
<script>
(function() {
    if (window.thermalPrinterLoaded) return;
    window.thermalPrinterLoaded = true;

    window.THERMAL_CONFIG = {
        serviceUUID: '000018f0-0000-1000-8000-00805f9b34fb',
        characteristicUUID: '00002af1-0000-1000-8000-00805f9b34fb',
    };

    window.connectedDevice = null;
    window.printerCharacteristic = null;

    window.generateEscPosCommands = function(data) {
        const ESC = '\x1B';
        const GS = '\x1D';
        let cmd = '';

        cmd += ESC + '@';
        cmd += ESC + 'a' + String.fromCharCode(1);
        cmd += GS + '!' + String.fromCharCode(0x11);
        cmd += 'LABEL GENTAN\n';
        cmd += GS + '!' + String.fromCharCode(0);
        cmd += ESC + 'a' + String.fromCharCode(0);
        cmd += '================================\n';
        cmd += 'LPK No      : ' + (data.lpk_no || '-') + '\n';
        cmd += 'Gentan No   : ' + (data.gentan_no || '-') + '\n';
        cmd += 'No Order    : ' + (data.code || '-') + '\n';
        cmd += '--------------------------------\n';
        cmd += 'Produk      : ' + (data.product_name || '-') + '\n';
        cmd += '--------------------------------\n';
        cmd += 'Panjang     : ' + (data.panjang_produksi || '0') + ' m\n';
        cmd += 'Berat       : ' + (data.berat_produksi || '0') + ' kg\n';
        cmd += 'Berat Std   : ' + (data.berat_standard || '0') + ' kg\n';
        cmd += '--------------------------------\n';
        cmd += 'Tgl LPK     : ' + (data.lpk_date || '-') + '\n';
        cmd += 'Qty LPK     : ' + (data.qty_lpk || '0') + '\n';
        cmd += '================================\n\n\n';
        cmd += GS + 'V' + String.fromCharCode(66) + String.fromCharCode(0);

        return cmd;
    };

    window.connectThermalPrinter = async function() {
        const device = await navigator.bluetooth.requestDevice({
            filters: [
                { services: [window.THERMAL_CONFIG.serviceUUID] },
                { name: 'BlueTooth Printer' },
                { namePrefix: 'BT' },
            ],
            optionalServices: [window.THERMAL_CONFIG.serviceUUID]
        });

        const server = await device.gatt.connect();
        const service = await server.getPrimaryService(window.THERMAL_CONFIG.serviceUUID);
        const characteristic = await service.getCharacteristic(window.THERMAL_CONFIG.characteristicUUID);

        window.connectedDevice = device;
        window.printerCharacteristic = characteristic;
    };

    window.printToThermalPrinter = async function(data) {
        const commands = window.generateEscPosCommands(data);
        const encoder = new TextEncoder();
        const bytes = encoder.encode(commands);

        if (!window.printerCharacteristic) {
            await window.connectThermalPrinter();
        }

        const chunkSize = 512;
        for (let i = 0; i < bytes.length; i += chunkSize) {
            const chunk = bytes.slice(i, i + chunkSize);
            await window.printerCharacteristic.writeValue(chunk);
            await new Promise(r => setTimeout(r, 100));
        }

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

    // Handler untuk button thermal
    window.handleThermalPrint = async function() {
        if (!('bluetooth' in navigator)) {
            alert('❌ Browser tidak support Bluetooth');
            return;
        }

        // Ambil data dari Livewire component
        const component = window.Livewire.find(document.querySelector('[wire\\:id]').getAttribute('wire:id'));

        const printData = {
            lpk_no: component.get('lpk_no'),
            gentan_no: component.get('gentan_no'),
            code: component.get('code'),
            product_name: component.get('product_name'),
            panjang_produksi: component.get('product_panjang'),
            berat_produksi: component.get('berat_produksi'),
            berat_standard: component.get('berat_standard'),
            lpk_date: component.get('lpk_date'),
            qty_lpk: component.get('qty_lpk'),
            timestamp: new Date().toLocaleString('id-ID'),
        };

        try {
            await window.printToThermalPrinter(printData);
        } catch (error) {
            console.error('Print error:', error);
            if (confirm('Print thermal gagal. Print normal?')) {
                component.call('printNormal');
            }
        }
    };

    window.addEventListener('beforeunload', () => {
        if (window.connectedDevice?.gatt?.connected) {
            window.connectedDevice.gatt.disconnect();
        }
    });
})();
</script>
