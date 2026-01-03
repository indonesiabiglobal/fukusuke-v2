<!--
    CONTOH IMPLEMENTASI TOMBOL BLUETOOTH PRINTER
    Copy & paste code ini ke halaman blade yang membutuhkan
-->

<!-- STYLE: Tambahkan di section <style> atau file CSS -->
<style>
.printer-btn-group {
    display: flex;
    gap: 10px;
    margin: 10px 0;
    flex-wrap: wrap;
}

.printer-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 16px;
    border: none;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.printer-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
}

.printer-btn:active {
    transform: translateY(0);
}

.printer-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.printer-btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.printer-btn-success {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
}

.printer-btn-danger {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    color: white;
}

.printer-btn-info {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    color: white;
}

.printer-status-badge {
    display: inline-block;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 500;
    margin-left: 8px;
}

.status-connected {
    background: #d1fae5;
    color: #065f46;
}

.status-disconnected {
    background: #fee2e2;
    color: #991b1b;
}

.status-checking {
    background: #dbeafe;
    color: #1e40af;
}
</style>

<!-- HTML: Tambahkan di halaman -->
<div class="printer-control-panel" style="padding: 15px; background: #f9fafb; border-radius: 8px; margin-bottom: 20px;">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 15px;">
        <h5 style="margin: 0; font-size: 16px;">
            🖨️ Bluetooth Printer
        </h5>
        <div>
            <span style="font-size: 13px; color: #6b7280;">Status:</span>
            <span id="printerStatusBadge" class="printer-status-badge status-checking">
                Checking...
            </span>
        </div>
    </div>

    <div id="printerInfoBox" style="background: white; padding: 12px; border-radius: 6px; margin-bottom: 15px; display: none;">
        <div style="display: flex; align-items: center; gap: 10px;">
            <span style="font-size: 24px;">📱</span>
            <div>
                <div style="font-size: 12px; color: #6b7280;">Printer Tersimpan</div>
                <div style="font-weight: 600; color: #111827;" id="savedPrinterName">-</div>
            </div>
        </div>
    </div>

    <div class="printer-btn-group">
        <button onclick="quickPrint()" class="printer-btn printer-btn-success" id="btnPrint">
            <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                <path d="M5 1a2 2 0 0 0-2 2v1h10V3a2 2 0 0 0-2-2H5zm6 8H5a1 1 0 0 0-1 1v3a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1v-3a1 1 0 0 0-1-1z"/>
                <path d="M0 7a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2h-1v-2a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v2H2a2 2 0 0 1-2-2V7zm2.5 1a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z"/>
            </svg>
            Print Label
        </button>

        <button onclick="refreshPrinterStatus()" class="printer-btn printer-btn-info" id="btnRefresh">
            <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2v1z"/>
                <path d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466z"/>
            </svg>
            Refresh
        </button>

        <button onclick="reconnectPrinter()" class="printer-btn printer-btn-primary" id="btnConnect">
            <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                <path d="M15.698 3.470 8.023.302a.499.499 0 0 0-.464 0L.084 3.470A.5.5 0 0 0 .084 4.44l7.674 3.167a.499.499 0 0 0 .464 0l7.675-3.166a.5.5 0 0 0 0-.971z"/>
            </svg>
            Connect
        </button>

        <button onclick="forgetSavedPrinter()" class="printer-btn printer-btn-danger" id="btnForget">
            <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
            </svg>
            Reset
        </button>
    </div>
</div>

<!-- JAVASCRIPT: Tambahkan di section <script> -->
<script>
// ===== GLOBAL VARIABLES =====
let isPrinting = false;

// ===== AUTO CHECK STATUS ON LOAD =====
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(() => refreshPrinterStatus(), 500);
});

// ===== REFRESH PRINTER STATUS =====
async function refreshPrinterStatus() {
    const badge = document.getElementById('printerStatusBadge');
    const infoBox = document.getElementById('printerInfoBox');
    const printerName = document.getElementById('savedPrinterName');

    try {
        badge.textContent = 'Checking...';
        badge.className = 'printer-status-badge status-checking';

        const savedName = window.getSavedPrinterName();

        if (savedName) {
            printerName.textContent = savedName;
            infoBox.style.display = 'block';

            const isReady = await window.checkPrinterReady();

            if (isReady) {
                badge.textContent = '✅ Connected';
                badge.className = 'printer-status-badge status-connected';
            } else {
                badge.textContent = '⚠️ Disconnected';
                badge.className = 'printer-status-badge status-disconnected';
            }
        } else {
            badge.textContent = '❌ Not Paired';
            badge.className = 'printer-status-badge status-disconnected';
            infoBox.style.display = 'none';
        }
    } catch (error) {
        console.error('Error checking status:', error);
        badge.textContent = '❌ Error';
        badge.className = 'printer-status-badge status-disconnected';
    }
}

// ===== RECONNECT PRINTER =====
async function reconnectPrinter() {
    const btn = document.getElementById('btnConnect');
    const originalText = btn.innerHTML;

    try {
        btn.disabled = true;
        btn.innerHTML = '<span style="display: inline-block; animation: spin 1s linear infinite;">⏳</span> Connecting...';

        await window.connectThermalPrinter();

        if (typeof Toastify !== 'undefined') {
            Toastify({
                text: "✅ Printer berhasil terhubung!",
                duration: 3000,
                gravity: "top",
                position: "right",
                backgroundColor: "#10b981",
            }).showToast();
        }

        setTimeout(() => refreshPrinterStatus(), 1000);
    } catch (error) {
        console.error('Connect error:', error);

        if (typeof Toastify !== 'undefined') {
            Toastify({
                text: "❌ Gagal connect: " + error.message,
                duration: 5000,
                gravity: "top",
                position: "right",
                backgroundColor: "#ef4444",
            }).showToast();
        } else {
            alert('❌ Gagal connect: ' + error.message);
        }
    } finally {
        btn.disabled = false;
        btn.innerHTML = originalText;
    }
}

// ===== FORGET PRINTER =====
function forgetSavedPrinter() {
    if (confirm('Hapus printer tersimpan?\n\nAnda perlu melakukan pairing ulang.')) {
        window.forgetThermalPrinter();

        if (typeof Toastify !== 'undefined') {
            Toastify({
                text: "✅ Printer berhasil dihapus!",
                duration: 3000,
                gravity: "top",
                position: "right",
                backgroundColor: "#10b981",
            }).showToast();
        }

        setTimeout(() => refreshPrinterStatus(), 500);
    }
}

// ===== QUICK PRINT (CUSTOMIZE SESUAI KEBUTUHAN) =====
async function quickPrint() {
    if (isPrinting) {
        alert('Sedang mencetak, mohon tunggu...');
        return;
    }

    const btn = document.getElementById('btnPrint');
    const originalText = btn.innerHTML;

    try {
        isPrinting = true;
        btn.disabled = true;
        btn.innerHTML = '<span style="display: inline-block; animation: spin 1s linear infinite;">⏳</span> Printing...';

        // ===== AMBIL DATA DARI FORM/LIVEWIRE =====
        // CUSTOMIZE ini sesuai dengan data yang ada di halaman
        const printData = {
            gentan_no: @this.get('gentan_no') || '0',
            lpk_no: @this.get('lpk_no') || '000000-000',
            product_name: @this.get('product_name') || '-',
            code: @this.get('code') || '-',
            code_alias: @this.get('code_alias') || '-',
            production_date: @this.get('production_date') || '-',
            work_hour: @this.get('work_hour') || '-',
            work_shift: @this.get('work_shift') || '-',
            machineno: @this.get('machineno') || '-',
            berat_produksi: @this.get('berat_produksi') || '0',
            panjang_produksi: @this.get('product_panjang') || '0',
            selisih: @this.get('selisih') || '0',
            nomor_han: @this.get('nomor_han') || '-',
            nik: @this.get('nik') || '-',
            empname: @this.get('empname') || '-',
        };

        // Check printer ready
        const printerReady = await window.checkPrinterReady();
        if (!printerReady) {
            await window.connectThermalPrinter();
            await new Promise(r => setTimeout(r, 500));
        }

        // Print 2 copies
        await window.printToThermalPrinter(printData, 2);

        if (typeof Toastify !== 'undefined') {
            Toastify({
                text: "✅ Label berhasil dicetak!",
                duration: 3000,
                gravity: "top",
                position: "right",
                backgroundColor: "#10b981",
            }).showToast();
        }
    } catch (error) {
        console.error('Print error:', error);

        if (typeof Toastify !== 'undefined') {
            Toastify({
                text: "❌ Print gagal: " + error.message,
                duration: 5000,
                gravity: "top",
                position: "right",
                backgroundColor: "#ef4444",
            }).showToast();
        } else {
            alert('❌ Print gagal: ' + error.message);
        }
    } finally {
        isPrinting = false;
        btn.disabled = false;
        btn.innerHTML = originalText;
    }
}
</script>

<style>
@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
</style>

<!--
    CARA PAKAI:
    1. Copy semua code ini ke halaman blade Anda
    2. Customize bagian printData di fungsi quickPrint()
    3. Sesuaikan dengan field Livewire/data yang ada
    4. Test di browser Chrome/Edge

    CONTOH CUSTOMIZATION:

    Jika menggunakan form biasa (bukan Livewire):
    const printData = {
        gentan_no: document.getElementById('gentan_no').value,
        lpk_no: document.getElementById('lpk_no').value,
        // ... dst
    };

    Jika menggunakan Alpine.js:
    const printData = {
        gentan_no: $wire.gentan_no,
        lpk_no: $wire.lpk_no,
        // ... dst
    };
-->
