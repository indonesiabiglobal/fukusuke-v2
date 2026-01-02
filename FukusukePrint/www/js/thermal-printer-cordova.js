// ===== CORDOVA THERMAL PRINTER MODULE - BLUETOOTH SERIAL =====
// For Epson TM-P20 & TM-P20II
// Uses: cordova-plugin-bluetooth-serial

(function () {
    "use strict";

    if (window.thermalPrinterCordovaLoaded) {
        console.log("✅ Thermal printer Cordova already loaded");
        return;
    }

    window.thermalPrinterCordovaLoaded = true;

    // Check if Cordova & Bluetooth Serial available
    document.addEventListener(
        "deviceready",
        function () {
            console.log("✅ Cordova device ready");

            if (typeof bluetoothSerial === "undefined") {
                console.error("❌ Bluetooth Serial plugin not found!");
                return;
            }

            console.log("✅ Bluetooth Serial plugin loaded");
            initThermalPrinter();
        },
        false
    );

    window.thermalPrinter = {
        device: null,
        connected: false,
        macAddress: null,
    };

    function initThermalPrinter() {
        // Load saved printer
        const savedMac = localStorage.getItem("thermal_printer_mac");
        const savedName = localStorage.getItem("thermal_printer_name");

        if (savedMac && savedName) {
            console.log(`📱 Saved printer: ${savedName} (${savedMac})`);
            window.thermalPrinter.macAddress = savedMac;
            window.thermalPrinter.device = {
                name: savedName,
                address: savedMac,
            };
        }
    }

    // ===== LIST PAIRED BLUETOOTH DEVICES =====
    window.listBluetoothDevices = function () {
        return new Promise((resolve, reject) => {
            bluetoothSerial.list(
                function (devices) {
                    console.log("📱 Found devices:", devices);

                    // Filter Epson printers
                    const epsonDevices = devices.filter(
                        (d) =>
                            d.name &&
                            (d.name.includes("TM-P20") ||
                                d.name.includes("Epson"))
                    );

                    resolve(epsonDevices);
                },
                function (error) {
                    console.error("❌ List error:", error);
                    reject(error);
                }
            );
        });
    };

    // ===== CONNECT TO PRINTER =====
    window.connectThermalPrinter = function (macAddress) {
        return new Promise((resolve, reject) => {
            console.log("🔌 Connecting to:", macAddress);

            bluetoothSerial.connect(
                macAddress,
                function () {
                    console.log("✅ Connected!");
                    window.thermalPrinter.connected = true;
                    window.thermalPrinter.macAddress = macAddress;
                    resolve(true);
                },
                function (error) {
                    console.error("❌ Connection error:", error);
                    window.thermalPrinter.connected = false;
                    reject(error);
                }
            );
        });
    };

    // ===== DISCONNECT PRINTER =====
    window.disconnectThermalPrinter = function () {
        return new Promise((resolve) => {
            if (!window.thermalPrinter.connected) {
                resolve(true);
                return;
            }

            bluetoothSerial.disconnect(
                function () {
                    console.log("🔌 Disconnected");
                    window.thermalPrinter.connected = false;
                    resolve(true);
                },
                function (error) {
                    console.error("⚠️ Disconnect error:", error);
                    window.thermalPrinter.connected = false;
                    resolve(true); // Still resolve
                }
            );
        });
    };

    // ===== CHECK IF CONNECTED =====
    window.checkPrinterConnected = function () {
        return new Promise((resolve) => {
            bluetoothSerial.isConnected(
                function () {
                    console.log("✅ Printer connected");
                    window.thermalPrinter.connected = true;
                    resolve(true);
                },
                function () {
                    console.log("❌ Printer not connected");
                    window.thermalPrinter.connected = false;
                    resolve(false);
                }
            );
        });
    };

    // ===== GENERATE BITMAP IMAGE (GENTAN NO + QR CODE) =====
    window.generateSideBySideBitmap = async function (gentanNo, lpkNo) {
        return new Promise((resolve) => {
            // Import QRCode library jika belum ada
            if (typeof QRCode === "undefined") {
                const script = document.createElement("script");
                script.src =
                    "https://cdn.jsdelivr.net/npm/qrcode/build/qrcode.min.js";
                script.onload = () => generateImage();
                document.head.appendChild(script);
            } else {
                generateImage();
            }

            function generateImage() {
                const canvas = document.createElement("canvas");
                const ctx = canvas.getContext("2d");

                canvas.width = 384;
                canvas.height = 150;

                // Background putih
                ctx.fillStyle = "#FFFFFF";
                ctx.fillRect(0, 0, canvas.width, canvas.height);

                // Gentan NO
                ctx.fillStyle = "#000000";
                ctx.font = "bold 64px Arial";
                ctx.textAlign = "left";
                ctx.textBaseline = "middle";
                ctx.fillText(gentanNo, 10, 75);

                // QR Code
                QRCode.toCanvas(
                    lpkNo,
                    {
                        errorCorrectionLevel: "M",
                        width: 130,
                        margin: 0,
                        color: {
                            dark: "#000000",
                            light: "#FFFFFF",
                        },
                    },
                    (err, qrCanvas) => {
                        if (err) {
                            console.error("QR error:", err);
                            resolve(null);
                            return;
                        }

                        ctx.drawImage(
                            qrCanvas,
                            canvas.width - 140,
                            10,
                            130,
                            130
                        );
                        const imageData = ctx.getImageData(
                            0,
                            0,
                            canvas.width,
                            canvas.height
                        );

                        resolve({
                            canvas: canvas,
                            imageData: imageData,
                            width: canvas.width,
                            height: canvas.height,
                        });
                    }
                );
            }
        });
    };

    // ===== CONVERT IMAGEDATA TO ESC/POS RASTER =====
    window.imageDataToRaster = function (imageData, width, height) {
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

                const gray = (r + g + b) / 3;
                const bit = gray < 128 ? 1 : 0;

                const byteIdx = Math.floor(x / 8);
                const bitIdx = 7 - (x % 8);

                if (!line[byteIdx]) line[byteIdx] = 0;
                line[byteIdx] |= bit << bitIdx;
            }
            bitmap.push(line);
        }

        // ESC/POS raster command
        const headerSize = 8;
        const dataSize = height * bytesPerLine;
        const totalSize = headerSize + dataSize;
        const cmd = new Uint8Array(totalSize);

        let pos = 0;

        cmd[pos++] = 0x1d; // GS
        cmd[pos++] = 0x76; // v
        cmd[pos++] = 0x30; // 0
        cmd[pos++] = 0x00; // m

        cmd[pos++] = bytesPerLine & 0xff;
        cmd[pos++] = (bytesPerLine >> 8) & 0xff;
        cmd[pos++] = height & 0xff;
        cmd[pos++] = (height >> 8) & 0xff;

        for (let y = 0; y < height; y++) {
            for (let x = 0; x < bytesPerLine; x++) {
                cmd[pos++] = bitmap[y][x] || 0;
            }
        }

        return cmd;
    };

    // ===== GENERATE ESC/POS COMMANDS =====
    window.generateEscPosCommands = async function (data) {
        const ESC = "\x1B";
        const GS = "\x1D";

        let textCmd = "";

        // Initialize
        textCmd += ESC + "@";
        textCmd += ESC + "R" + String.fromCharCode(0);

        const gentanNo = String(data.gentan_no || "0");
        const qrData = String(data.lpk_no || "000000-000");

        // Generate bitmap
        const bitmap = await window.generateSideBySideBitmap(gentanNo, qrData);

        let rasterBytes = null;
        if (bitmap) {
            rasterBytes = window.imageDataToRaster(
                bitmap.imageData,
                bitmap.width,
                bitmap.height
            );
        } else {
            textCmd += ESC + "a" + String.fromCharCode(1);
            textCmd += GS + "!" + String.fromCharCode(0x11);
            textCmd += "G:" + gentanNo + "\n";
            textCmd += GS + "!" + String.fromCharCode(0);
        }

        textCmd += "\n";
        textCmd += "------------------------------------------\n";

        // LPK NO
        textCmd += ESC + "a" + String.fromCharCode(1);
        textCmd += ESC + "E" + String.fromCharCode(1);
        textCmd += GS + "!" + String.fromCharCode(0x11);
        textCmd += String(data.lpk_no || "-") + "\n";
        textCmd += GS + "!" + String.fromCharCode(0);
        textCmd += ESC + "E" + String.fromCharCode(0);
        textCmd += ESC + "a" + String.fromCharCode(0);
        textCmd += "------------------------------------------\n";

        // PRODUCT NAME
        textCmd += ESC + "a" + String.fromCharCode(1);
        textCmd += GS + "!" + String.fromCharCode(0x01);
        textCmd += String(data.product_name || "-") + "\n";
        textCmd += GS + "!" + String.fromCharCode(0);
        textCmd += ESC + "a" + String.fromCharCode(0);
        textCmd += "------------------------------------------\n";

        // DETAIL INFO
        textCmd += GS + "!" + String.fromCharCode(0x10);
        textCmd += "No. Order: " + String(data.code || "-") + "\n";
        textCmd += "Kode     : " + String(data.code_alias || "-") + "\n";
        textCmd += GS + "!" + String.fromCharCode(0);
        textCmd += "------------------------------------------\n";
        textCmd += GS + "!" + String.fromCharCode(0x10);
        textCmd += "Tgl Prod : " + String(data.production_date || "-") + "\n";
        textCmd += "Jam      : " + String(data.work_hour || "-") + "\n";
        textCmd += "Shift    : " + String(data.work_shift || "-") + "\n";
        textCmd += "Mesin    : " + String(data.machineno || "-") + "\n";
        textCmd += GS + "!" + String.fromCharCode(0);
        textCmd += "------------------------------------------\n";

        // BERAT & PANJANG
        textCmd += GS + "!" + String.fromCharCode(0x10);
        textCmd += "Berat    : " + String(data.berat_produksi || "0") + " kg\n";
        textCmd +=
            "Panjang  : " + String(data.panjang_produksi || "0") + " m\n";

        const selisih = parseFloat(data.selisih || 0);
        if (selisih >= 0) {
            textCmd += "Lebih    : " + String(data.selisih || "0") + " m\n";
        } else {
            textCmd += "Kurang   : " + String(Math.abs(selisih)) + " m\n";
        }

        textCmd += "No Han   : " + String(data.nomor_han || "-") + "\n";
        textCmd += GS + "!" + String.fromCharCode(0);
        textCmd += "------------------------------------------\n";

        // NIK & NAMA
        textCmd += GS + "!" + String.fromCharCode(0x10);
        textCmd += "NIK      : " + String(data.nik || "-") + "\n";
        textCmd += "Nama     : " + String(data.empname || "-") + "\n";
        textCmd += GS + "!" + String.fromCharCode(0);
        textCmd += "------------------------------------------\n";
        textCmd += "\n\n\n";

        // Cut paper
        textCmd += GS + "V" + String.fromCharCode(0);

        // Combine
        const encoder = new TextEncoder();
        const textBytes = encoder.encode(textCmd);

        if (rasterBytes) {
            const combined = new Uint8Array(
                rasterBytes.length + textBytes.length
            );
            combined.set(rasterBytes, 0);
            combined.set(textBytes, rasterBytes.length);
            return combined;
        } else {
            return textBytes;
        }
    };

    // ===== PRINT TO THERMAL PRINTER =====
    window.printToThermalPrinter = async function (data, copies = 1) {
        console.log("📝 Generating commands...");

        // Check connection
        const isConnected = await window.checkPrinterConnected();

        if (!isConnected) {
            // Try reconnect
            if (window.thermalPrinter.macAddress) {
                console.log("🔄 Reconnecting...");
                await window.connectThermalPrinter(
                    window.thermalPrinter.macAddress
                );
            } else {
                throw new Error("Printer not connected. Please pair first.");
            }
        }

        // Generate ESC/POS commands
        const bytes = await window.generateEscPosCommands(data);

        // Print copies
        for (let copy = 1; copy <= copies; copy++) {
            console.log(`📄 Printing copy ${copy}/${copies}...`);

            // Convert Uint8Array to regular array for bluetoothSerial
            const byteArray = Array.from(bytes);

            await new Promise((resolve, reject) => {
                bluetoothSerial.write(
                    byteArray,
                    function () {
                        console.log(`✅ Copy ${copy} sent`);
                        resolve();
                    },
                    function (error) {
                        console.error(`❌ Print error copy ${copy}:`, error);
                        reject(error);
                    }
                );
            });

            // Delay between copies
            if (copy < copies) {
                await new Promise((r) => setTimeout(r, 2000));
            }
        }

        console.log("✅ Print complete!");
    };

    // ===== UI HELPER: SELECT PRINTER =====
    window.selectPrinterUI = async function () {
        try {
            const devices = await window.listBluetoothDevices();

            if (devices.length === 0) {
                alert(
                    "❌ Tidak ada printer Epson yang paired.\n\nSilakan pair printer di Settings > Bluetooth terlebih dahulu."
                );
                return null;
            }

            // Show selection dialog
            let message = "Pilih Printer:\n\n";
            devices.forEach((d, i) => {
                message += `${i + 1}. ${d.name}\n   ${d.address}\n\n`;
            });

            const choice = prompt(
                message + "Masukkan nomor (1-" + devices.length + "):"
            );

            if (!choice) return null;

            const index = parseInt(choice) - 1;

            if (index < 0 || index >= devices.length) {
                alert("❌ Pilihan tidak valid");
                return null;
            }

            const selected = devices[index];

            // Save to storage
            localStorage.setItem("thermal_printer_mac", selected.address);
            localStorage.setItem("thermal_printer_name", selected.name);

            window.thermalPrinter.device = selected;
            window.thermalPrinter.macAddress = selected.address;

            // Connect
            await window.connectThermalPrinter(selected.address);

            alert(`✅ Printer terpilih:\n${selected.name}`);

            return selected;
        } catch (error) {
            console.error("❌ Select error:", error);
            alert("❌ Error: " + error);
            return null;
        }
    };

    console.log("✅ Cordova thermal printer module loaded");
})();
