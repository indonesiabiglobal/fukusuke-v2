<header id="page-topbar">
    <div class="layout-width">
        <div class="navbar-header">
            <div class="d-flex">
                <!-- LOGO -->
                <div class="navbar-brand-box horizontal-logo">
                    <a href="index" class="logo logo-dark">
                        <span class="logo-sm">
                            <img src="{{ URL::asset('build/images/logo-sm.png') }}" alt="" height="22">
                        </span>
                        <span class="logo-lg">
                            <img src="{{ URL::asset('build/images/logo-dark.png') }}" alt="" height="25">
                        </span>
                    </a>

                    <a href="index" class="logo logo-light">
                        <span class="logo-sm">
                            <img src="{{ URL::asset('build/images/logo-sm.png') }}" alt="" height="22">
                        </span>
                        <span class="logo-lg">
                            <img src="{{ URL::asset('build/images/logo-light.png') }}" alt="" height="25">
                        </span>
                    </a>
                </div>

                <button type="button" class="btn btn-sm px-3 fs-16 header-item vertical-menu-btn topnav-hamburger" id="topnav-hamburger-icon">
                    <span class="hamburger-icon">
                        <span></span>
                        <span></span>
                        <span></span>
                    </span>
                </button>
            </div>

            <div class="d-flex align-items-center">

                <div class="d-md-none topbar-head-dropdown header-item">
                    <button type="button" class="btn btn-icon btn-topbar btn-ghost-dark rounded-circle" id="page-header-search-dropdown" data-bs-toggle="modal" data-bs-target="#searchModal">
                        <i class="bi bi-search fs-16"></i>
                    </button>
                </div>

                <div class="ms-1 header-item d-none d-sm-flex">
                    <button type="button" class="btn btn-icon btn-topbar btn-ghost-dark rounded-circle" data-toggle="fullscreen">
                        <i class='bi bi-arrows-fullscreen fs-16'></i>
                    </button>
                </div>

                <div class="dropdown topbar-head-dropdown ms-1 header-item">
                    <button type="button" class="btn btn-icon btn-topbar btn-ghost-dark rounded-circle mode-layout" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="bi bi-sun align-middle fs-20"></i>
                    </button>
                    <div class="dropdown-menu p-2 dropdown-menu-end" id="light-dark-mode">
                        <a href="#!" class="dropdown-item" data-mode="light"><i class="bi bi-sun align-middle me-2"></i> Default (light mode)</a>
                        <a href="#!" class="dropdown-item" data-mode="dark"><i class="bi bi-moon align-middle me-2"></i> Dark</a>
                        <a href="#!" class="dropdown-item" data-mode="auto"><i class="bi bi-moon-stars align-middle me-2"></i> Auto (system default)</a>
                    </div>
                </div>

                <div class="dropdown ms-sm-3 header-item topbar-user">
                    <button type="button" class="btn" id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="d-flex align-items-center">
                            <img class="rounded-circle header-profile-user" src="@if(Auth::user()->avatar) {{ URL::asset('storage/images/users/'. Auth::user()->avatar) }} @else {{ URL::asset('build/images/fukusuke.png') }} @endif" alt="Header Avatar">
                        </span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end">
                        <!-- item-->
                        <a class="dropdown-item" href="pages-profile"><i class="mdi mdi-account-circle text-muted fs-16 align-middle me-1"></i> <span class="align-middle">{{ Auth::user()->username }}</span></a>
                        <a class="dropdown-item" href="/new-password"><i class="mdi mdi-cog-outline text-muted fs-16 align-middle me-1"></i> <span class="align-middle">Change Password</span></a>
                        <a class="dropdown-item" href="{{ route('printer.settings') }}"><i class="mdi mdi-printer-settings text-muted fs-16 align-middle me-1"></i> <span class="align-middle">Printer Settings</span></a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item " href="{{ url('logout') }}"><i class="mdi mdi-logout text-muted fs-16 align-middle me-1"></i> <span class="align-middle" key="t-logout">logout</span></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
<script>
// Clear saved thermal printer from localStorage
function clearSavedPrinter(event) {
    event.preventDefault();

    const savedPrinterName = localStorage.getItem('thermal_printer_name');

    if (!savedPrinterName) {
        if (typeof Toastify !== 'undefined') {
            Toastify({
                text: '⚠️ No saved printer found',
                duration: 3000,
                gravity: 'top',
                position: 'right',
                backgroundColor: '#ffc107',
            }).showToast();
        } else {
            alert('⚠️ No saved printer found');
        }
        return;
    }

    if (confirm(`Clear saved printer "${savedPrinterName}"?\n\nYou will need to pair again on next print.`)) {
        localStorage.removeItem('thermal_printer_id');
        localStorage.removeItem('thermal_printer_name');

        // Disconnect if currently connected
        if (window.thermalPrinter && window.thermalPrinter.device) {
            try {
                if (window.thermalPrinter.device.gatt.connected) {
                    window.thermalPrinter.device.gatt.disconnect();
                }
            } catch (err) {
                console.warn('Disconnect error:', err);
            }
            window.thermalPrinter.device = null;
            window.thermalPrinter.characteristic = null;
        }

        if (typeof Toastify !== 'undefined') {
            Toastify({
                text: '✅ Saved printer cleared successfully!',
                duration: 3000,
                gravity: 'top',
                position: 'right',
                backgroundColor: '#10b981',
            }).showToast();
        } else {
            alert('✅ Saved printer cleared!');
        }

        console.log('✅ Printer cache cleared');
    }
}
</script>
