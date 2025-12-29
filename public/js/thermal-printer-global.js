// ===== GLOBAL THERMAL PRINTER MODULE - WITH FALLBACK =====
(function () {
    "use strict";

    if (window.thermalPrinterGlobalLoaded) {
        console.log("✅ Thermal printer global already loaded");
        return;
    }

    window.thermalPrinterGlobalLoaded = true;

    if (!("bluetooth" in navigator)) {
        console.warn("⚠️ Bluetooth API not available");
        return;
    }

    console.log("✅ Initializing global thermal printer module");

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
    };

    // ===== CEK PRINTER READY - DENGAN FALLBACK =====
    window.checkPrinterReady = async function () {
        try {
            console.log("🔍 Checking printer status...");

            // ===== CEK APAKAH GETDEVICES SUPPORT =====
            if (typeof navigator.bluetooth.getDevices !== "function") {
                console.warn("⚠️ getDevices() not supported, using cache");

                // Fallback: Cek localStorage
                const savedDeviceId =
                    localStorage.getItem("thermal_printer_id");
                const savedDeviceName = localStorage.getItem(
                    "thermal_printer_name"
                );

                if (!savedDeviceId || !savedDeviceName) {
                    console.log("📍 No saved printer in cache");
                    return false;
                }

                console.log("✅ Found cached printer:", savedDeviceName);

                // Anggap printer ready jika ada di cache
                // Actual connection akan dicoba saat print
                return true;
            }

            // ===== JIKA GETDEVICES SUPPORT =====
            const devices = await navigator.bluetooth.getDevices();
            const epsonPrinter = devices.find(
                (d) => d.name && d.name.includes("TM-P20II")
            );

            if (!epsonPrinter) {
                console.log("⚠️ TM-P20II not paired");
                return false;
            }

            // Cek apakah connected
            if (epsonPrinter.gatt && epsonPrinter.gatt.connected) {
                console.log("✅ Printer already connected");

                window.thermalPrinter.device = epsonPrinter;

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

    // Generate ESC/POS Commands
    window.generateEscPosCommands = function (data) {
        const ESC = "\x1B";
        const GS = "\x1D";
        let cmd = "";

        cmd += ESC + "@";
        cmd += ESC + "R" + String.fromCharCode(0);

        // GENTAN NO (DOUBLE SIZE) - CENTER
        cmd += ESC + "a" + String.fromCharCode(1);
        cmd += GS + "!" + String.fromCharCode(0x11);
        cmd += String(data.gentan_no || "0") + "\n";
        cmd += GS + "!" + String.fromCharCode(0);
        cmd += "\n";

        // QR CODE (LPK NO) - CENTER
        const qrData = String(data.lpk_no || "000000-000");
        cmd += GS + "(k" + String.fromCharCode(4, 0, 49, 65, 50, 0);
        cmd += GS + "(k" + String.fromCharCode(3, 0, 49, 67, 6);
        cmd += GS + "(k" + String.fromCharCode(3, 0, 49, 69, 49);

        const qrLen = qrData.length + 3;
        const pL = qrLen % 256;
        const pH = Math.floor(qrLen / 256);
        cmd += GS + "(k" + String.fromCharCode(pL, pH, 49, 80, 48) + qrData;
        cmd += GS + "(k" + String.fromCharCode(3, 0, 49, 81, 48);

        cmd += "\n\n";
        cmd += ESC + "a" + String.fromCharCode(0);

        // LPK NO (DOUBLE SIZE)
        cmd += "================================\n";
        cmd += ESC + "a" + String.fromCharCode(1);
        cmd += GS + "!" + String.fromCharCode(0x11);
        cmd += String(data.lpk_no || "-") + "\n";
        cmd += GS + "!" + String.fromCharCode(0);
        cmd += ESC + "a" + String.fromCharCode(0);
        cmd += "================================\n";

        // PRODUCT NAME (BOLD)
        cmd += ESC + "E" + String.fromCharCode(1);
        cmd += String(data.product_name || "-") + "\n";
        cmd += ESC + "E" + String.fromCharCode(0);
        cmd += "--------------------------------\n";

        // DETAIL
        cmd += "No. Order   : " + String(data.code || "-") + "\n";
        cmd += "Kode        : " + String(data.code_alias || "-") + "\n";
        cmd += "Tgl Prod    : " + String(data.production_date || "-") + "\n";
        cmd += "Jam         : " + String(data.work_hour || "-") + "\n";
        cmd += "Shift       : " + String(data.work_shift || "-") + "\n";
        cmd += "Mesin       : " + String(data.machineno || "-") + "\n";
        cmd += "--------------------------------\n";

        // BERAT & PANJANG (BOLD)
        cmd += ESC + "E" + String.fromCharCode(1);
        cmd += "Berat       : " + String(data.berat_produksi || "0") + "\n";
        cmd += "Panjang     : " + String(data.panjang_produksi || "0") + "\n";
        cmd += ESC + "E" + String.fromCharCode(0);

        cmd += "Lebih       : " + String(data.selisih || "0") + "\n";
        cmd += "No Han      : " + String(data.nomor_han || "-") + "\n";
        cmd += "--------------------------------\n";

        // NIK & NAMA
        cmd += "NIK         : " + String(data.nik || "-") + "\n";
        cmd += "Nama        : " + String(data.empname || "-") + "\n";
        cmd += "================================\n";
        cmd += "\n\n\n";

        // Cut
        cmd += GS + "V" + String.fromCharCode(0);

        return cmd;
    };

    // Manual Connect (FIRST TIME ONLY)
    window.connectThermalPrinter = async function () {
        console.log("🔍 Requesting printer...");

        const device = await navigator.bluetooth.requestDevice({
            acceptAllDevices: true,
            optionalServices: ["49535343-fe7d-4ae5-8fa9-9fafd205e455"],
        });

        console.log("Connecting to:", device.name);

        const server = await device.gatt.connect();
        const service = await server.getPrimaryService(
            "49535343-fe7d-4ae5-8fa9-9fafd205e455"
        );
        const characteristic = await service.getCharacteristic(
            "49535343-1e4d-4bd9-ba61-23c647249616"
        );

        window.thermalPrinter.device = device;
        window.thermalPrinter.characteristic = characteristic;

        // ===== SAVE TO LOCALSTORAGE =====
        localStorage.setItem("thermal_printer_id", device.id);
        localStorage.setItem("thermal_printer_name", device.name);

        console.log("✅ Printer connected & saved:", device.name);
        return true;
    };

    // Print Function - DENGAN AUTO RECONNECT
    window.printToThermalPrinter = async function (data) {
        console.log("📝 Generating print commands...");

        const commands = window.generateEscPosCommands(data);
        const encoder = new TextEncoder();
        const bytes = encoder.encode(commands);

        // ===== AUTO RECONNECT JIKA BELUM CONNECT =====
        if (!window.thermalPrinter.characteristic) {
            console.log("⚠️ No characteristic, trying to connect...");

            const savedName = localStorage.getItem("thermal_printer_name");

            if (!savedName) {
                throw new Error("No saved printer, please pair first");
            }

            // Request device lagi (akan langsung connect ke saved device)
            console.log("Reconnecting to saved printer...");
            await window.connectThermalPrinter();
        }

        if (!window.thermalPrinter.characteristic) {
            throw new Error("Printer not connected");
        }

        const chunkSize = 128;
        const totalChunks = Math.ceil(bytes.length / chunkSize);

        console.log(`Sending ${totalChunks} chunks...`);

        for (let i = 0; i < bytes.length; i += chunkSize) {
            const chunk = bytes.slice(i, i + chunkSize);
            await window.thermalPrinter.characteristic.writeValue(chunk);
            await new Promise((r) => setTimeout(r, 200));
        }

        console.log("✅ Print complete!");
        await new Promise((r) => setTimeout(r, 1000));

        if (typeof Toastify !== "undefined") {
            Toastify({
                text: "✅ Label berhasil dicetak!",
                duration: 3000,
                gravity: "top",
                position: "right",
                backgroundColor: "#10b981",
            }).showToast();
        }
    };

    console.log("✅ Global thermal printer module loaded");
})();
