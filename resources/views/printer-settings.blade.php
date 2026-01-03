@extends('layouts.master')
@section('title')
    Printer Settings
@endsection
@section('css')
    <style>
        .printer-card {
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .printer-card:hover {
            box-shadow: 0 8px 12px rgba(0, 0, 0, 0.15);
            transform: translateY(-2px);
        }

        .status-indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 8px;
        }

        .status-connected {
            background: #10b981;
            box-shadow: 0 0 8px rgba(16, 185, 129, 0.5);
        }

        .status-disconnected {
            background: #ef4444;
        }

        .status-checking {
            background: #3b82f6;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .printer-icon {
            font-size: 48px;
            color: #667eea;
        }

        .action-btn {
            transition: all 0.2s ease;
        }

        .action-btn:hover {
            transform: scale(1.05);
        }
    </style>
@endsection
@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Settings
        @endslot
        @slot('title')
            Printer Settings
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-12">
            <div class="card printer-card">
                <div class="card-header bg-gradient-primary text-white">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <i class="mdi mdi-printer printer-icon me-3"></i>
                            <div>
                                <h4 class="card-title mb-1">🖨️ Bluetooth Printer Management</h4>
                                <p class="mb-0 fs-13 opacity-75">Kelola koneksi printer Bluetooth thermal Anda</p>
                            </div>
                        </div>
                        <span id="printerStatusIndicator" class="status-indicator status-checking"></span>
                    </div>
                </div>

                <div class="card-body p-4">
                    <!-- Status Section -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="p-4 bg-light rounded-3">
                                <div class="d-flex align-items-center mb-3">
                                    <i class="mdi mdi-information-outline text-primary fs-20 me-2"></i>
                                    <h5 class="mb-0">Status Printer</h5>
                                </div>
                                <div id="printerStatus" class="p-3 bg-white rounded border">
                                    <div class="d-flex align-items-center">
                                        <div class="spinner-border spinner-border-sm text-primary me-2" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <span id="statusText" class="text-muted">Mengecek status printer...</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="p-4 bg-light rounded-3">
                                <div class="d-flex align-items-center mb-3">
                                    <i class="mdi mdi-printer-check text-success fs-20 me-2"></i>
                                    <h5 class="mb-0">Printer Tersimpan</h5>
                                </div>
                                <div id="printerInfo" class="p-3 bg-white rounded border" style="display: none;">
                                    <div class="d-flex align-items-center">
                                        <i class="mdi mdi-bluetooth text-primary fs-24 me-3"></i>
                                        <div>
                                            <div class="text-muted small">Nama Printer</div>
                                            <div class="fw-bold fs-16" id="savedPrinterName">-</div>
                                        </div>
                                    </div>
                                </div>
                                <div id="noPrinterInfo" class="p-3 bg-white rounded border">
                                    <div class="text-center text-muted">
                                        <i class="mdi mdi-printer-off fs-24 d-block mb-2"></i>
                                        Belum ada printer tersimpan
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="row g-3">
                        <div class="col-md-3 col-6">
                            <button onclick="refreshPrinterStatus()" class="btn btn-soft-info w-100 action-btn" id="btnRefresh">
                                <i class="mdi mdi-refresh me-2"></i>
                                Refresh Status
                            </button>
                        </div>
                        <div class="col-md-3 col-6">
                            <button onclick="connectPrinter()" class="btn btn-soft-primary w-100 action-btn" id="btnConnect">
                                <i class="mdi mdi-bluetooth-connect me-2"></i>
                                Connect Printer
                            </button>
                        </div>
                        <div class="col-md-3 col-6">
                            <button onclick="testPrint()" class="btn btn-soft-success w-100 action-btn" id="btnTest">
                                <i class="mdi mdi-printer-check me-2"></i>
                                Test Print
                            </button>
                        </div>
                        <div class="col-md-3 col-6">
                            <button onclick="forgetPrinter()" class="btn btn-soft-danger w-100 action-btn" id="btnForget">
                                <i class="mdi mdi-delete-outline me-2"></i>
                                Hapus Printer
                            </button>
                        </div>
                    </div>

                    <!-- Help Section -->
                    <div class="alert alert-info border-0 mt-4" role="alert">
                        <div class="d-flex align-items-start">
                            <i class="mdi mdi-information fs-20 me-3"></i>
                            <div>
                                <h5 class="alert-heading mb-2">💡 Tips Penggunaan</h5>
                                <ul class="mb-0 ps-3">
                                    <li>Pertama kali: Klik "Connect Printer" untuk pairing</li>
                                    <li>Selanjutnya: Printer akan otomatis terhubung saat print</li>
                                    <li>Jika gagal: Gunakan "Refresh Status" atau "Hapus Printer" untuk pairing ulang</li>
                                    <li>Test Print: Gunakan untuk memastikan printer berfungsi dengan baik</li>
                                </ul>
                                <div class="mt-3">
                                    <a href="{{ url('/docs/BLUETOOTH_PRINTER_GUIDE') }}" class="btn btn-sm btn-outline-info" target="_blank">
                                        <i class="mdi mdi-book-open-variant me-1"></i>
                                        Panduan Lengkap
                                    </a>
                                    <a href="{{ url('/docs/BLUETOOTH_QUICK_REFERENCE') }}" class="btn btn-sm btn-outline-secondary" target="_blank">
                                        <i class="mdi mdi-file-document-outline me-1"></i>
                                        Quick Reference
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
        // Variables
        let isProcessing = false;

        // Check if thermal printer functions are available
        function checkThermalPrinterAvailable() {
            if (typeof window.getSavedPrinterName !== 'function') {
                console.error('Thermal printer functions not available!');
                if (typeof Toastify !== 'undefined') {
                    Toastify({
                        text: "❌ Thermal printer script belum ter-load. Silakan refresh halaman.",
                        duration: 5000,
                        gravity: "top",
                        position: "right",
                        style: {
                            background: "linear-gradient(to right, #ef4444, #dc2626)",
                        },
                    }).showToast();
                }
                return false;
            }
            return true;
        }

        // Auto check status on load
        document.addEventListener('DOMContentLoaded', function() {
            console.log('=== Printer Settings Page Loaded ===');
            console.log('Checking thermal printer availability...');
            console.log('window.getSavedPrinterName exists?', typeof window.getSavedPrinterName);
            console.log('window.thermalPrinterGlobalLoaded:', window.thermalPrinterGlobalLoaded);

            // Script already loaded in master layout, no need to wait
            if (checkThermalPrinterAvailable()) {
                console.log('✅ Thermal printer functions available!');
                refreshPrinterStatus();
            } else {
                console.error('❌ Thermal printer functions NOT available!');
                document.getElementById('statusText').innerHTML =
                    '<span class="text-danger">❌ Script belum ter-load. Silakan refresh halaman.</span>';
            }
        });

        // Refresh Printer Status
        async function refreshPrinterStatus() {
            if (!checkThermalPrinterAvailable()) return;

            const statusText = document.getElementById('statusText');
            const printerInfo = document.getElementById('printerInfo');
            const noPrinterInfo = document.getElementById('noPrinterInfo');
            const printerName = document.getElementById('savedPrinterName');
            const statusIndicator = document.getElementById('printerStatusIndicator');
            const btnRefresh = document.getElementById('btnRefresh');

            try {
                btnRefresh.disabled = true;
                btnRefresh.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Checking...';
                statusIndicator.className = 'status-indicator status-checking';

                const savedName = window.getSavedPrinterName();

                if (savedName) {
                    printerName.textContent = savedName;
                    printerInfo.style.display = 'block';
                    noPrinterInfo.style.display = 'none';

                    const isReady = await window.checkPrinterReady();

                    if (isReady) {
                        statusText.innerHTML = '<span class="text-success fw-semibold">Printer Connected & Ready</span>';
                        statusIndicator.className = 'status-indicator status-connected';
                    } else {
                        statusText.innerHTML = '<i class="mdi mdi-alert-circle text-warning me-2"></i><span class="text-warning fw-semibold">Printer Tersimpan (Disconnected)</span>';
                        statusIndicator.className = 'status-indicator status-disconnected';
                    }
                } else {
                    printerInfo.style.display = 'none';
                    noPrinterInfo.style.display = 'block';
                    statusText.innerHTML = '<i class="mdi mdi-close-circle text-danger me-2"></i><span class="text-danger fw-semibold">Belum Ada Printer Tersimpan</span>';
                    statusIndicator.className = 'status-indicator status-disconnected';
                }
            } catch (error) {
                console.error('Error checking status:', error);
                statusText.innerHTML = '<i class="mdi mdi-alert text-danger me-2"></i><span class="text-danger">Error: ' + error.message + '</span>';
                statusIndicator.className = 'status-indicator status-disconnected';
            } finally {
                btnRefresh.disabled = false;
                btnRefresh.innerHTML = '<i class="mdi mdi-refresh me-2"></i>Refresh Status';
            }
        }

        // Connect Printer
        async function connectPrinter() {
            if (!checkThermalPrinterAvailable()) return;
            if (isProcessing) return;

            const btn = document.getElementById('btnConnect');
            const originalHtml = btn.innerHTML;

            try {
                isProcessing = true;
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Connecting...';

                const savedName = window.getSavedPrinterName();

                // Jika ada printer tersimpan, coba reconnect dulu tanpa dialog
                if (savedName) {
                    console.log('Attempting auto-reconnect to saved printer...');
                    const reconnected = await window.reconnectToSavedPrinter();

                    if (reconnected) {
                        if (typeof Toastify !== 'undefined') {
                            Toastify({
                                text: "✅ Printer berhasil terhubung kembali",
                                duration: 3000,
                                gravity: "top",
                                position: "right",
                                style: {
                                    background: "linear-gradient(to right, #10b981, #059669)",
                                },
                            }).showToast();
                        }
                        setTimeout(() => refreshPrinterStatus(), 1000);
                        return;
                    }

                    console.log('Auto-reconnect failed, showing pairing dialog...');
                }

                // Jika tidak ada saved printer atau reconnect gagal, tampilkan dialog
                await window.connectThermalPrinter(true); // forceDialog = true

                if (typeof Toastify !== 'undefined') {
                    Toastify({
                        text: "✅ Printer berhasil terhubung",
                        duration: 3000,
                        gravity: "top",
                        position: "right",
                        style: {
                            background: "linear-gradient(to right, #10b981, #059669)",
                        },
                    }).showToast();
                }

                setTimeout(() => refreshPrinterStatus(), 1000);
            } catch (error) {
                console.error('Connect error:', error);
                if (typeof Toastify !== 'undefined') {
                    Toastify({
                        text: "❌ Gagal Connect: " + error.message,
                        duration: 5000,
                        gravity: "top",
                        position: "right",
                        style: {
                            background: "linear-gradient(to right, #ef4444, #dc2626)",
                        },
                    }).showToast();
                }
            } finally {
                isProcessing = false;
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            }
        }

        // Test Print
        async function testPrint() {
            if (!checkThermalPrinterAvailable()) return;
            if (isProcessing) return;

            const btn = document.getElementById('btnTest');
            const originalHtml = btn.innerHTML;

            try {
                isProcessing = true;
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Printing...';

                const testData = {
                    gentan_no: '999',
                    lpk_no: 'TEST-{{ date("Ymd-His") }}',
                    product_name: 'TEST PRINT',
                    code: 'TEST-ORDER',
                    code_alias: 'TEST',
                    production_date: '{{ date("d/m/Y") }}',
                    work_hour: '{{ date("H:i") }}',
                    work_shift: 'Test',
                    machineno: 'M-TEST',
                    berat_produksi: '10.5',
                    panjang_produksi: '100',
                    selisih: '0',
                    nomor_han: 'H-001',
                    nik: '{{ Auth::user()->nik ?? "12345" }}',
                    empname: '{{ Auth::user()->first_name ?? "Test User" }}'
                };

                await window.printToThermalPrinter(testData, 1);

                if (typeof Toastify !== 'undefined') {
                    Toastify({
                        text: "✅ Test Print Berhasil! Label test telah dicetak",
                        duration: 3000,
                        gravity: "top",
                        position: "right",
                        style: {
                            background: "linear-gradient(to right, #10b981, #059669)",
                        },
                    }).showToast();
                }
            } catch (error) {
                console.error('Print error:', error);

                // Tampilkan error message
                if (typeof Toastify !== 'undefined') {
                    Toastify({
                        text: "❌ Print Gagal: " + error.message,
                        duration: 5000,
                        gravity: "top",
                        position: "right",
                        style: {
                            background: "linear-gradient(to right, #ef4444, #dc2626)",
                        },
                    }).showToast();
                }
            } finally {
                isProcessing = false;
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            }
        }

        // Forget Printer
        function forgetPrinter() {
            if (!checkThermalPrinterAvailable()) return;

            if (confirm('Hapus Printer?\n\nAnda perlu melakukan pairing ulang setelah ini')) {
                window.forgetThermalPrinter();

                if (typeof Toastify !== 'undefined') {
                    Toastify({
                        text: "✅ Printer berhasil dihapus dari memory",
                        duration: 3000,
                        gravity: "top",
                        position: "right",
                        style: {
                            background: "linear-gradient(to right, #10b981, #059669)",
                        },
                    }).showToast();
                }

                setTimeout(() => refreshPrinterStatus(), 500);
            }
        }
    </script>
@endpush
