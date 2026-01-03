<!-- CONTOH UI UNTUK MANAGE BLUETOOTH PRINTER -->
<div class="printer-management-ui" style="padding: 20px; background: #f5f5f5; border-radius: 8px; margin: 10px 0;">
    <h4 style="margin-bottom: 15px;">🖨️ Pengaturan Printer Bluetooth</h4>

    <!-- Status Printer -->
    <div id="printerStatus" style="margin-bottom: 15px; padding: 10px; background: white; border-radius: 5px;">
        <strong>Status:</strong> <span id="statusText">Mengecek...</span>
    </div>

    <!-- Printer Info -->
    <div id="printerInfo" style="margin-bottom: 15px; padding: 10px; background: white; border-radius: 5px; display: none;">
        <strong>Printer Tersimpan:</strong> <span id="printerName">-</span>
    </div>

    <!-- Action Buttons -->
    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
        <button onclick="checkPrinterStatus()" class="btn btn-info btn-sm">
            🔍 Cek Status
        </button>

        <button onclick="connectPrinter()" class="btn btn-primary btn-sm">
            🔗 Connect/Pairing
        </button>

        <button onclick="forceNewPairing()" class="btn btn-warning btn-sm">
            🔄 Pairing Ulang
        </button>

        <button onclick="forgetPrinter()" class="btn btn-danger btn-sm">
            🗑️ Hapus Printer
        </button>

        <button onclick="testPrint()" class="btn btn-success btn-sm">
            🖨️ Test Print
        </button>
    </div>
</div>

<script>
// ===== CEK STATUS PRINTER =====
async function checkPrinterStatus() {
    try {
        const statusText = document.getElementById('statusText');
        const printerInfo = document.getElementById('printerInfo');
        const printerName = document.getElementById('printerName');

        statusText.textContent = 'Mengecek...';
        statusText.style.color = '#666';

        // Cek nama printer tersimpan
        const savedName = window.getSavedPrinterName();

        if (savedName) {
            printerName.textContent = savedName;
            printerInfo.style.display = 'block';

            // Cek apakah printer ready
            const isReady = await window.checkPrinterReady();

            if (isReady) {
                statusText.textContent = '✅ Printer Ready & Connected';
                statusText.style.color = '#10b981';
            } else {
                statusText.textContent = '⚠️ Printer Tersimpan (Disconnected)';
                statusText.style.color = '#f59e0b';
            }
        } else {
            printerInfo.style.display = 'none';
            statusText.textContent = '❌ Belum Ada Printer Tersimpan';
            statusText.style.color = '#ef4444';
        }
    } catch (error) {
        console.error('Error checking status:', error);
        document.getElementById('statusText').textContent = '❌ Error: ' + error.message;
        document.getElementById('statusText').style.color = '#ef4444';
    }
}

// ===== CONNECT PRINTER (AUTO-RECONNECT) =====
async function connectPrinter() {
    try {
        const statusText = document.getElementById('statusText');
        statusText.textContent = 'Connecting...';
        statusText.style.color = '#3b82f6';

        // Ini akan auto-reconnect jika ada printer tersimpan
        // Jika gagal atau belum ada, akan tampilkan dialog
        await window.connectThermalPrinter();

        statusText.textContent = '✅ Berhasil Connect!';
        statusText.style.color = '#10b981';

        // Update status setelah connect
        setTimeout(() => checkPrinterStatus(), 1000);
    } catch (error) {
        console.error('Connect error:', error);
        document.getElementById('statusText').textContent = '❌ Gagal Connect: ' + error.message;
        document.getElementById('statusText').style.color = '#ef4444';
    }
}

// ===== FORCE PAIRING BARU (TAMPILKAN DIALOG) =====
async function forceNewPairing() {
    try {
        const statusText = document.getElementById('statusText');
        statusText.textContent = 'Menampilkan dialog pairing...';
        statusText.style.color = '#3b82f6';

        // Force menampilkan dialog pairing
        await window.connectThermalPrinter(true);

        statusText.textContent = '✅ Berhasil Pairing!';
        statusText.style.color = '#10b981';

        // Update status setelah pairing
        setTimeout(() => checkPrinterStatus(), 1000);
    } catch (error) {
        console.error('Pairing error:', error);
        document.getElementById('statusText').textContent = '❌ Gagal Pairing: ' + error.message;
        document.getElementById('statusText').style.color = '#ef4444';
    }
}

// ===== HAPUS PRINTER TERSIMPAN =====
function forgetPrinter() {
    if (confirm('Yakin ingin menghapus printer tersimpan?\n\nAnda perlu melakukan pairing ulang setelah ini.')) {
        window.forgetThermalPrinter();

        document.getElementById('statusText').textContent = '✅ Printer berhasil dihapus';
        document.getElementById('statusText').style.color = '#10b981';
        document.getElementById('printerInfo').style.display = 'none';

        setTimeout(() => checkPrinterStatus(), 1000);
    }
}

// ===== TEST PRINT =====
async function testPrint() {
    try {
        const statusText = document.getElementById('statusText');
        statusText.textContent = 'Printing test...';
        statusText.style.color = '#3b82f6';

        // Data test print
        const testData = {
            gentan_no: '999',
            lpk_no: 'TEST-001',
            product_name: 'TEST PRINT',
            code: 'TEST-ORDER',
            code_alias: 'TEST',
            production_date: new Date().toLocaleDateString('id-ID'),
            work_hour: '08:00-16:00',
            work_shift: 'Pagi',
            machineno: 'M-001',
            berat_produksi: '10.5',
            panjang_produksi: '100',
            selisih: '0',
            nomor_han: 'H-001',
            nik: '12345',
            empname: 'Test User'
        };

        // Print dengan auto-reconnect
        await window.printToThermalPrinter(testData, 1);

        statusText.textContent = '✅ Test print berhasil!';
        statusText.style.color = '#10b981';
    } catch (error) {
        console.error('Print error:', error);
        document.getElementById('statusText').textContent = '❌ Print gagal: ' + error.message;
        document.getElementById('statusText').style.color = '#ef4444';
    }
}

// Auto-check status saat halaman load
document.addEventListener('DOMContentLoaded', function() {
    // Delay sedikit untuk memastikan script thermal-printer-global.js sudah load
    setTimeout(() => checkPrinterStatus(), 500);
});
</script>

<style>
.printer-management-ui .btn {
    font-size: 13px;
    padding: 8px 12px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: all 0.2s;
}

.printer-management-ui .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.printer-management-ui .btn:active {
    transform: translateY(0);
}

.printer-management-ui .btn-info {
    background: #3b82f6;
    color: white;
}

.printer-management-ui .btn-primary {
    background: #10b981;
    color: white;
}

.printer-management-ui .btn-warning {
    background: #f59e0b;
    color: white;
}

.printer-management-ui .btn-danger {
    background: #ef4444;
    color: white;
}

.printer-management-ui .btn-success {
    background: #8b5cf6;
    color: white;
}
</style>
