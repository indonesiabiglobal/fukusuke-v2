// ===== GLOBAL THERMAL PRINTER MODULE =====
// File ini di-load SEKALI di layout master dan persist across pages

(function () {
    "use strict";

    if (window.thermalPrinterGlobalLoaded) {
        console.log("✅ Thermal printer global already loaded");
        return;
    }

    window.thermalPrinterGlobalLoaded = true;

    // Check Bluetooth availability
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
        isReady: false,
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

    // Auto Init Printer
    window.initThermalPrinter = async function () {
        try {
            console.log("🔄 Checking for paired printer...");

            const devices = await navigator.bluetooth.getDevices();
            console.log(`Found ${devices.length} paired device(s)`);

            const epsonPrinter = devices.find(
                (d) => d.name && d.name.includes("TM-P20II")
            );

            if (!epsonPrinter) {
                console.warn("⚠️ TM-P20II not paired");
                return false;
            }

            console.log("✅ Found:", epsonPrinter.name);

            let server;
            if (epsonPrinter.gatt && epsonPrinter.gatt.connected) {
                console.log("Already connected");
                server = epsonPrinter.gatt;
            } else {
                console.log("Connecting...");
                server = await epsonPrinter.gatt.connect();
            }

            const service = await server.getPrimaryService(
                "49535343-fe7d-4ae5-8fa9-9fafd205e455"
            );
            const characteristic = await service.getCharacteristic(
                "49535343-1e4d-4bd9-ba61-23c647249616"
            );

            window.thermalPrinter.device = epsonPrinter;
            window.thermalPrinter.characteristic = characteristic;
            window.thermalPrinter.isReady = true;

            console.log("✅ Printer READY!");
            return true;
        } catch (error) {
            console.error("❌ Init error:", error.message);
            return false;
        }
    };

    // Manual Connect
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
        window.thermalPrinter.isReady = true;

        console.log("✅ Printer connected:", device.name);
        return true;
    };

    // Print Function
    window.printToThermalPrinter = async function (data) {
        console.log("📝 Generating print commands...");

        const commands = window.generateEscPosCommands(data);
        const encoder = new TextEncoder();
        const bytes = encoder.encode(commands);

        if (!window.thermalPrinter.isReady) {
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

    // Auto-init on load
    setTimeout(() => {
        window.initThermalPrinter();
    }, 1000);

    console.log("✅ Global thermal printer module loaded");
})();
