// ===== GLOBAL THERMAL PRINTER MODULE - WITH FALLBACK =====
//
// FITUR AUTO-RECONNECT:
// - Printer yang sudah dipasangkan akan tersimpan di localStorage
// - Saat connectThermalPrinter() dipanggil, akan otomatis mencoba reconnect tanpa dialog
// - Jika auto-reconnect gagal, baru akan menampilkan dialog pairing
//
// FUNGSI TERSEDIA:
// - window.checkPrinterReady() - Cek apakah printer sudah ready
// - window.reconnectToSavedPrinter() - Auto-reconnect tanpa dialog (internal)
// - window.connectThermalPrinter(forceDialog) - Connect dengan atau tanpa dialog
// - window.forgetThermalPrinter() - Hapus printer tersimpan
// - window.getSavedPrinterName() - Dapatkan nama printer tersimpan
// - window.printToThermalPrinter(data, copies) - Print dengan auto-reconnect
//
(function () {
    "use strict";

    // Check if already loaded (tapi tetap define fungsi untuk safety)
    if (window.thermalPrinterGlobalLoaded) {
        console.log("✅ Thermal printer global already loaded (re-export functions)");
        // TIDAK return - biarkan fungsi terdefinisi ulang untuk safety
    } else {
        console.log("✅ Initializing global thermal printer module");
        window.thermalPrinterGlobalLoaded = true;
    }

    if (!("bluetooth" in navigator)) {
        console.warn("⚠️ Bluetooth API not available");
        // Masih define fallback functions
    }

    // UUID EPSON TM-P20II
    window.THERMAL_UUID_CONFIGS = [
        {
            name: "Epson TM-P20II",
            serviceUUID: "49535343-fe7d-4ae5-8fa9-9fafd205e455",
            characteristicUUID: "49535343-1e4d-4bd9-ba61-23c647249616",
        },
    ];

    window.thermalPrinter = {
        device: null,
        characteristic: null,
        // Keep reference to connected device for reconnection
        lastConnectedDevice: null,
    };

    // ===== CEK PRINTER READY - SUPPORT TM-P20 & TM-P20II =====
    window.checkPrinterReady = async function () {
        try {
            console.log("🔍 Checking printer status...");

            // ===== CEK APAKAH GETDEVICES SUPPORT =====
            if (typeof navigator.bluetooth.getDevices !== "function") {
                console.warn("⚠️ getDevices() not supported, using cache");

                // Fallback: Cek localStorage
                const savedDeviceName = localStorage.getItem(
                    "thermal_printer_name"
                );

                if (!savedDeviceName) {
                    console.log("📍 No saved printer in cache");
                    return false;
                }

                console.log("✅ Found cached printer:", savedDeviceName);
                return true;
            }

            // ===== JIKA GETDEVICES SUPPORT =====
            const devices = await navigator.bluetooth.getDevices();

            // ✅ CARI TM-P20II ATAU TM-P20 (TANPA II)
            const epsonPrinter = devices.find(
                (d) =>
                    d.name &&
                    (d.name.includes("TM-P20II") || d.name.includes("TM-P20"))
            );

            if (!epsonPrinter) {
                console.log("⚠️ Epson TM-P20/TM-P20II not paired");
                return false;
            }

            console.log("✅ Found:", epsonPrinter.name);

            // Cek apakah connected
            if (epsonPrinter.gatt && epsonPrinter.gatt.connected) {
                console.log("✅ Printer already connected");

                window.thermalPrinter.device = epsonPrinter;
                window.thermalPrinter.lastConnectedDevice = epsonPrinter; // Cache

                try {
                    const service = await epsonPrinter.gatt.getPrimaryService(
                        "49535343-fe7d-4ae5-8fa9-9fafd205e455"
                    );
                    const characteristic = await service.getCharacteristic(
                        "49535343-1e4d-4bd9-ba61-23c647249616"
                    );
                    window.thermalPrinter.characteristic = characteristic;

                    // Save to cache
                    localStorage.setItem("thermal_printer_id", epsonPrinter.id);
                    localStorage.setItem(
                        "thermal_printer_name",
                        epsonPrinter.name
                    );

                    return true;
                } catch (err) {
                    console.error("Service/Characteristic error:", err);
                    return false;
                }
            }

            // Jika paired tapi disconnected, reconnect
            console.log("🔄 Reconnecting to", epsonPrinter.name);
            const server = await epsonPrinter.gatt.connect();
            const service = await server.getPrimaryService(
                "49535343-fe7d-4ae5-8fa9-9fafd205e455"
            );
            const characteristic = await service.getCharacteristic(
                "49535343-1e4d-4bd9-ba61-23c647249616"
            );

            window.thermalPrinter.device = epsonPrinter;
            window.thermalPrinter.characteristic = characteristic;
            window.thermalPrinter.lastConnectedDevice = epsonPrinter; // Cache

            // Save to cache
            localStorage.setItem("thermal_printer_id", epsonPrinter.id);
            localStorage.setItem("thermal_printer_name", epsonPrinter.name);

            console.log("✅ Reconnected successfully");
            return true;
        } catch (error) {
            console.error("❌ Check printer error:", error.message);
            return false;
        }
    };

    // ===== GENERATE BITMAP IMAGE (GENTAN NO + QR CODE SIDE BY SIDE) =====
    window.generateSideBySideBitmap = async function(gentanNo, lpkNo) {
        return new Promise((resolve) => {
            // Import QRCode library dinamis jika belum ada
            if (typeof QRCode === 'undefined') {
                const script = document.createElement('script');
                script.src = 'https://cdn.jsdelivr.net/npm/qrcode/build/qrcode.min.js';
                script.onload = () => generateImage();
                document.head.appendChild(script);
            } else {
                generateImage();
            }

            function generateImage() {
                // Canvas setup - lebar thermal printer 58mm = ~384px, 80mm = ~576px
                const canvas = document.createElement('canvas');
                const ctx = canvas.getContext('2d');

                // Gunakan lebar 384px untuk thermal 58mm (paling umum)
                canvas.width = 384;
                canvas.height = 150; // Height untuk gentan + QR yang lebih besar

                // Background putih
                ctx.fillStyle = '#FFFFFF';
                ctx.fillRect(0, 0, canvas.width, canvas.height);

                // ===== GENTAN NO DI KIRI =====
                ctx.fillStyle = '#000000';
                ctx.font = 'bold 64px Arial'; // Font lebih besar untuk gentan
                ctx.textAlign = 'left';
                ctx.textBaseline = 'middle';

                // Draw gentan no di posisi kiri
                ctx.fillText(gentanNo, 10, 75);

                // ===== QR CODE DI KANAN =====
                // Generate QR code ke canvas temporary
                QRCode.toCanvas(lpkNo, {
                    errorCorrectionLevel: 'M',
                    width: 130, // QR size 130x130px (lebih besar)
                    margin: 0,
                    color: {
                        dark: '#000000',
                        light: '#FFFFFF'
                    }
                }, (err, qrCanvas) => {
                    if (err) {
                        console.error('QR generation error:', err);
                        resolve(null);
                        return;
                    }

                    // Draw QR code di sebelah kanan gentan no
                    // Posisi: canvas.width - 140 (QR width + margin)
                    ctx.drawImage(qrCanvas, canvas.width - 140, 10, 130, 130);

                    // Convert canvas to bitmap data
                    const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                    resolve({
                        canvas: canvas,
                        imageData: imageData,
                        width: canvas.width,
                        height: canvas.height
                    });
                });
            }
        });
    };

    // ===== CONVERT IMAGEDATA TO ESC/POS RASTER COMMAND =====
    window.imageDataToRaster = function(imageData, width, height) {
        // Convert to monochrome bitmap
        const pixels = imageData.data;
        const bytesPerLine = Math.ceil(width / 8);
        const bitmap = [];

        for (let y = 0; y < height; y++) {
            const line = [];
            for (let x = 0; x < width; x++) {
                const idx = (y * width + x) * 4;
                const r = pixels[idx];
                const g = pixels[idx + 1];
                const b = pixels[idx + 2];

                // Convert to grayscale and threshold
                const gray = (r + g + b) / 3;
                const bit = gray < 128 ? 1 : 0; // Black pixel = 1

                const byteIdx = Math.floor(x / 8);
                const bitIdx = 7 - (x % 8);

                if (!line[byteIdx]) line[byteIdx] = 0;
                line[byteIdx] |= (bit << bitIdx);
            }
            bitmap.push(line);
        }

        // ESC/POS raster command: GS v 0 m xL xH yL yH d1...dk
        // Build as Uint8Array directly (NOT string!)
        const headerSize = 8;
        const dataSize = height * bytesPerLine;
        const totalSize = headerSize + dataSize;
        const cmd = new Uint8Array(totalSize);

        let pos = 0;

        // GS v 0
        cmd[pos++] = 0x1D; // GS
        cmd[pos++] = 0x76; // v
        cmd[pos++] = 0x30; // 0
        cmd[pos++] = 0x00; // m (normal mode)

        // Width in bytes (little endian)
        cmd[pos++] = bytesPerLine & 0xFF;
        cmd[pos++] = (bytesPerLine >> 8) & 0xFF;

        // Height in dots (little endian)
        cmd[pos++] = height & 0xFF;
        cmd[pos++] = (height >> 8) & 0xFF;

        // Bitmap data
        for (let y = 0; y < height; y++) {
            for (let x = 0; x < bytesPerLine; x++) {
                cmd[pos++] = bitmap[y][x] || 0;
            }
        }

        return cmd; // Return Uint8Array, NOT string
    };

    // ===== WORD WRAP HELPER FUNCTION =====
    window.wrapText = function(text, maxWidth) {
        if (!text || text.length <= maxWidth) {
            return [text || ""];
        }

        const words = text.split(' ');
        const lines = [];
        let currentLine = '';

        for (const word of words) {
            // Jika kata sendiri lebih panjang dari maxWidth, potong paksa
            if (word.length > maxWidth) {
                if (currentLine) {
                    lines.push(currentLine.trim());
                    currentLine = '';
                }
                // Potong kata panjang per maxWidth karakter
                for (let i = 0; i < word.length; i += maxWidth) {
                    lines.push(word.substring(i, i + maxWidth));
                }
                continue;
            }

            const testLine = currentLine ? currentLine + ' ' + word : word;

            if (testLine.length <= maxWidth) {
                currentLine = testLine;
            } else {
                if (currentLine) {
                    lines.push(currentLine.trim());
                }
                currentLine = word;
            }
        }

        if (currentLine) {
            lines.push(currentLine.trim());
        }

        return lines.length > 0 ? lines : [""];
    };

    // Generate ESC/POS Commands
    window.generateEscPosCommands = async function (data) {
        const ESC = "\x1B";
        const GS = "\x1D";

        // Build text commands as string
        let textCmd = "";

        // Initialize
        textCmd += ESC + "@";
        textCmd += ESC + "R" + String.fromCharCode(0);

        const gentanNo = String(data.gentan_no || "0");
        const qrData = String(data.lpk_no || "000000-000");

        // Generate bitmap (async) - returns Uint8Array
        const bitmap = await window.generateSideBySideBitmap(gentanNo, qrData);

        let rasterBytes = null;
        if (bitmap) {
            // Get raster command as Uint8Array
            rasterBytes = window.imageDataToRaster(
                bitmap.imageData,
                bitmap.width,
                bitmap.height
            );
        } else {
            // Fallback ke text mode jika bitmap gagal
            textCmd += ESC + "a" + String.fromCharCode(1); // Center
            textCmd += GS + "!" + String.fromCharCode(0x11); // Double size
            textCmd += "G:" + gentanNo + "\n";
            textCmd += GS + "!" + String.fromCharCode(0);
        }

        textCmd += "------------------------------------------\n";

        // ========== LPK NO (DOUBLE SIZE + BOLD + CENTER) ==========
        textCmd += ESC + "a" + String.fromCharCode(1); // Center align
        textCmd += ESC + "E" + String.fromCharCode(1); // Bold ON
        textCmd += GS + "!" + String.fromCharCode(0x11); // Double size
        textCmd += String(data.lpk_no || "-") + "\n";
        textCmd += GS + "!" + String.fromCharCode(0); // Reset size
        textCmd += ESC + "E" + String.fromCharCode(0); // Bold OFF
        textCmd += ESC + "a" + String.fromCharCode(0); // Back to left
        textCmd += "------------------------------------------\n";

        // ========== PRODUCT NAME (MEDIUM - TALL ONLY) WITH WORD WRAP ==========
        textCmd += ESC + "a" + String.fromCharCode(1); // Center align
        textCmd += GS + "!" + String.fromCharCode(0x10);

        // Wrap product name (max 16 chars per line for tall font)
        const productLines = window.wrapText(String(data.product_name || "-"), 16);
        productLines.forEach(line => {
            textCmd += line + "\n";
        });

        textCmd += GS + "!" + String.fromCharCode(0); // Reset
        textCmd += ESC + "a" + String.fromCharCode(0); // Back to left

        // Garis pemisah
        textCmd += "------------------------------------------\n";

        // ========== DETAIL INFO (WIDE FONT - SEDIKIT LEBIH BESAR) WITH WORD WRAP ==========
        textCmd += GS + "!" + String.fromCharCode(0x10); // Wide only

        textCmd += "No. Order: " + String(data.code || "-") + "\n";

        // Wrap code_alias jika panjang (max 16 chars for wide font)
        const codeAliasLines = window.wrapText(String(data.code_alias || "-"), 16);
        textCmd += "Kode     : " + codeAliasLines[0] + "\n";
        if (codeAliasLines.length > 1) {
            for (let i = 1; i < codeAliasLines.length; i++) {
                textCmd += "           " + codeAliasLines[i] + "\n"; // Indent untuk line berikutnya
            }
        }

        textCmd += GS + "!" + String.fromCharCode(0); // ← Reset DULU sebelum garis
        textCmd += "------------------------------------------\n"; // ← Garis normal
        textCmd += GS + "!" + String.fromCharCode(0x10); // ← Wide lagi untuk text berikutnya

        textCmd += "Tgl Prod : " + String(data.production_date || "-") + "\n";
        textCmd += "Jam      : " + String(data.work_hour || "-") + "\n";
        textCmd += "Shift    : " + String(data.work_shift || "-") + "\n";
        textCmd += "Mesin    : " + String(data.machineno || "-") + "\n";

        textCmd += GS + "!" + String.fromCharCode(0); // Reset size
        textCmd += "------------------------------------------\n";

        // ========== BERAT & PANJANG (WIDE FONT) ==========
        textCmd += GS + "!" + String.fromCharCode(0x10); // Wide only

        textCmd += "Berat    : " + String(data.berat_produksi || "0") + " kg\n";
        textCmd += "Panjang  : " + String(data.panjang_produksi || "0") + " m\n";

        // Selisih (Lebih/Kurang)
        const selisih = parseFloat(data.selisih || 0);
        if (selisih >= 0) {
            textCmd += "Lebih    : " + String(data.selisih || "0") + " m\n";
        } else {
            textCmd += "Kurang   : -" + String(Math.abs(selisih)) + " m\n";
        }

        textCmd += "No Han   : " + String(data.nomor_han || "-") + "\n";

        textCmd += GS + "!" + String.fromCharCode(0); // Reset size
        textCmd += "------------------------------------------\n";

        // ========== NIK & NAMA (WIDE FONT) WITH WORD WRAP ==========
        textCmd += GS + "!" + String.fromCharCode(0x10); // Wide only

        textCmd += "NIK      : " + String(data.nik || "-") + "\n";

        // Wrap employee name jika panjang (max 16 chars for wide font)
        const empNameLines = window.wrapText(String(data.empname || "-"), 10);
        textCmd += "Nama     : " + empNameLines[0] + "\n";
        if (empNameLines.length > 1) {
            for (let i = 1; i < empNameLines.length; i++) {
                textCmd += "           " + empNameLines[i] + "\n"; // Indent untuk line berikutnya
            }
        }

        textCmd += GS + "!" + String.fromCharCode(0); // Reset size
        textCmd += "------------------------------------------\n";
        textCmd += "\n\n\n";

        // Cut paper
        textCmd += GS + "V" + String.fromCharCode(0);

        // ===== COMBINE TEXT + RASTER BINARY DATA =====
        // Convert text commands to Uint8Array
        const encoder = new TextEncoder();
        const textBytes = encoder.encode(textCmd);

        // Combine: [text commands] + [raster bitmap] + [rest of text]
        if (rasterBytes) {
            // Create combined array
            const combined = new Uint8Array(rasterBytes.length + textBytes.length);
            combined.set(rasterBytes, 0); // Raster first
            combined.set(textBytes, rasterBytes.length); // Text after
            return combined;
        } else {
            // No raster, just return text
            return textBytes;
        }
    };

    // ===== AUTO RECONNECT TO SAVED PRINTER (TANPA DIALOG) =====
    window.reconnectToSavedPrinter = async function () {
        try {
            // Cek apakah ada printer tersimpan
            const savedDeviceName = localStorage.getItem("thermal_printer_name");
            if (!savedDeviceName) {
                console.log("⚠️ No saved printer found");
                return false;
            }

            console.log("🔄 Auto-reconnecting to:", savedDeviceName);

            // ===== STRATEGI 1: Gunakan device yang masih di memory (paling cepat) =====
            if (window.thermalPrinter.lastConnectedDevice) {
                console.log("📱 Using cached device reference...");
                try {
                    const device = window.thermalPrinter.lastConnectedDevice;

                    // Cek apakah device masih valid
                    if (device.name === savedDeviceName) {
                        console.log("📡 Connecting to cached device:", device.name);
                        const server = await device.gatt.connect();
                        const service = await server.getPrimaryService(
                            "49535343-fe7d-4ae5-8fa9-9fafd205e455"
                        );
                        const characteristic = await service.getCharacteristic(
                            "49535343-1e4d-4bd9-ba61-23c647249616"
                        );

                        window.thermalPrinter.device = device;
                        window.thermalPrinter.characteristic = characteristic;

                        console.log("✅ Auto-reconnected via cached device!");
                        return true;
                    }
                } catch (err) {
                    console.warn("⚠️ Cached device reconnect failed:", err.message);
                    // Continue to strategy 2
                }
            }

            // ===== STRATEGI 2: Gunakan getDevices() jika tersedia =====
            if (typeof navigator.bluetooth.getDevices === "function") {
                console.log("📱 Using getDevices() API...");

                const devices = await navigator.bluetooth.getDevices();
                const savedDevice = devices.find(
                    (d) => d.name && d.name === savedDeviceName
                );

                if (savedDevice) {
                    console.log("📡 Connecting via getDevices():", savedDevice.name);
                    const server = await savedDevice.gatt.connect();
                    const service = await server.getPrimaryService(
                        "49535343-fe7d-4ae5-8fa9-9fafd205e455"
                    );
                    const characteristic = await service.getCharacteristic(
                        "49535343-1e4d-4bd9-ba61-23c647249616"
                    );

                    window.thermalPrinter.device = savedDevice;
                    window.thermalPrinter.characteristic = characteristic;
                    window.thermalPrinter.lastConnectedDevice = savedDevice; // Cache for next time

                    console.log("✅ Auto-reconnected via getDevices()!");
                    return true;
                }
            }

            // Jika semua strategi gagal
            console.log("⚠️ Auto-reconnect not possible, need user interaction");
            return false;
        } catch (error) {
            console.error("❌ Auto-reconnect failed:", error.message);
            return false;
        }
    };

    // Connect to saved printer (or pairing baru) - DENGAN DIALOG
    window.connectThermalPrinter = async function (forceDialog = false) {
        // Jika tidak force dialog, coba auto-reconnect dulu
        if (!forceDialog) {
            const reconnected = await window.reconnectToSavedPrinter();
            if (reconnected) {
                return true;
            }
            console.log("Auto-reconnect failed, showing pairing dialog...");
        }

        const savedDeviceName = localStorage.getItem("thermal_printer_name");

        let device;

        if (savedDeviceName && !forceDialog) {
            // Jika sudah ada savedDevice, pakai nama tersebut
            console.log("Connecting to saved printer:", savedDeviceName);

            try {
                device = await navigator.bluetooth.requestDevice({
                    filters: [{ name: savedDeviceName }],
                    optionalServices: ["49535343-fe7d-4ae5-8fa9-9fafd205e455"],
                });
            } catch (err) {
                console.warn("Failed to connect to saved printer, showing all devices...");
                // Jika gagal, fallback ke pairing baru
                device = await navigator.bluetooth.requestDevice({
                    acceptAllDevices: true,
                    optionalServices: ["49535343-fe7d-4ae5-8fa9-9fafd205e455"],
                });
            }
        } else {
            // Jika belum ada savedDevice atau force dialog, tampilkan semua device untuk pairing
            console.log("No saved printer, requesting pairing...");

            device = await navigator.bluetooth.requestDevice({
                acceptAllDevices: true,
                optionalServices: ["49535343-fe7d-4ae5-8fa9-9fafd205e455"],
            });
        }

        const server = await device.gatt.connect();
        const service = await server.getPrimaryService(
            "49535343-fe7d-4ae5-8fa9-9fafd205e455"
        );
        const characteristic = await service.getCharacteristic(
            "49535343-1e4d-4bd9-ba61-23c647249616"
        );

        window.thermalPrinter.device = device;
        window.thermalPrinter.characteristic = characteristic;
        window.thermalPrinter.lastConnectedDevice = device; // Cache device untuk reconnect

        // Simpan ke localStorage untuk next time
        localStorage.setItem("thermal_printer_id", device.id);
        localStorage.setItem("thermal_printer_name", device.name);

        console.log("✅ Printer connected & saved:", device.name);
        return true;
    };

    // ===== HAPUS PRINTER TERSIMPAN (UNTUK PAIRING ULANG) =====
    window.forgetThermalPrinter = function () {
        localStorage.removeItem("thermal_printer_id");
        localStorage.removeItem("thermal_printer_name");
        window.thermalPrinter.device = null;
        window.thermalPrinter.characteristic = null;
        window.thermalPrinter.lastConnectedDevice = null; // Clear cache
        console.log("✅ Printer berhasil dihapus dari memory");
    };

    // ===== CEK NAMA PRINTER TERSIMPAN =====
    window.getSavedPrinterName = function () {
        return localStorage.getItem("thermal_printer_name") || null;
    };

    // Print Function - DENGAN AUTO RECONNECT & COPIES
    window.printToThermalPrinter = async function (data, copies = 1) {
        console.log("📝 Generating print commands...");

        // Generate commands (async, returns Uint8Array directly)
        const bytes = await window.generateEscPosCommands(data);

        // ===== AUTO RECONNECT JIKA BELUM CONNECT =====
        if (!window.thermalPrinter.characteristic) {
            console.log("⚠️ No characteristic, trying to connect...");

            // Strategy 1: Coba auto-reconnect tanpa dialog (jika ada saved printer)
            const savedName = localStorage.getItem("thermal_printer_name");

            if (savedName) {
                console.log("Attempting auto-reconnect to:", savedName);
                const reconnected = await window.reconnectToSavedPrinter();

                if (reconnected) {
                    console.log("✅ Auto-reconnect successful, proceeding to print...");
                }
            }

            // Strategy 2: Jika auto-reconnect gagal atau tidak ada saved printer, tampilkan dialog
            if (!window.thermalPrinter.characteristic) {
                console.log("🔔 Showing pairing dialog...");

                try {
                    // Tampilkan dialog pairing (user interaction required)
                    await window.connectThermalPrinter(true); // forceDialog = true
                    console.log("✅ Printer connected via dialog, proceeding to print...");
                } catch (err) {
                    // User cancelled atau error saat pairing
                    console.error("❌ Pairing cancelled or failed:", err.message);
                    throw new Error("Printer pairing cancelled. Please try again.");
                }
            }
        }

        // Final check
        if (!window.thermalPrinter.characteristic) {
            throw new Error("Printer not connected after pairing attempt");
        }

        const chunkSize = 128;

        // ===== LOOP UNTUK COPIES =====
        for (let copy = 1; copy <= copies; copy++) {
            console.log(`📄 Printing copy ${copy}/${copies}...`);

            for (let i = 0; i < bytes.length; i += chunkSize) {
                const chunk = bytes.slice(i, i + chunkSize);
                await window.thermalPrinter.characteristic.writeValue(chunk);
                await new Promise((r) => setTimeout(r, 200));
            }

            console.log(`✅ Copy ${copy} complete!`);

            // Delay antar copy (biar tidak overlap)
            if (copy < copies) {
                await new Promise((r) => setTimeout(r, 1500));
            }
        }

        await new Promise((r) => setTimeout(r, 1000));

        if (typeof Toastify !== "undefined") {
            Toastify({
                text: `✅ ${copies} label berhasil dicetak!`,
                duration: 3000,
                gravity: "top",
                position: "right",
                backgroundColor: "#10b981",
            }).showToast();
        }
    };

    console.log("✅ Global thermal printer module loaded");
})();
