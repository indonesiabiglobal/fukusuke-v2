# 📘 Panduan Bluetooth Printer - Auto Reconnect

## 🎯 Fitur Baru: Auto Reconnect

Sistem sekarang dapat **otomatis terhubung ke printer Bluetooth yang sudah dipasangkan** tanpa perlu menampilkan dialog pairing ulang.

---

## 🔧 Cara Kerja

### 1. **Pairing Pertama Kali**
- Saat pertama kali menggunakan printer, sistem akan menampilkan dialog untuk memilih printer Bluetooth
- Setelah berhasil terhubung, nama printer akan **otomatis tersimpan** di localStorage browser
- Printer yang tersimpan: `TM-P20II` atau `TM-P20`

### 2. **Penggunaan Selanjutnya**
- Saat tombol print ditekan, sistem akan **otomatis mencoba reconnect** ke printer yang tersimpan
- **TIDAK** akan muncul dialog pairing
- Jika printer dalam jangkauan dan Bluetooth aktif, akan langsung terhubung
- Jika gagal terhubung, baru akan menampilkan dialog pairing

---

## 📝 Fungsi JavaScript yang Tersedia

```javascript
// ✅ Cek apakah printer sudah ready/paired
const isReady = await window.checkPrinterReady();

// 🔄 Auto-reconnect tanpa dialog (dipanggil otomatis oleh connectThermalPrinter)
const reconnected = await window.reconnectToSavedPrinter();

// 🔗 Connect ke printer (auto-reconnect dulu, kalau gagal baru dialog)
await window.connectThermalPrinter();

// 🔗 Force menampilkan dialog pairing (bypass auto-reconnect)
await window.connectThermalPrinter(true); // forceDialog = true

// 🗑️ Hapus printer tersimpan (untuk pairing ulang)
window.forgetThermalPrinter();

// 📋 Lihat nama printer yang tersimpan
const printerName = window.getSavedPrinterName();
console.log('Printer tersimpan:', printerName);

// 🖨️ Print dengan auto-reconnect (jika perlu)
await window.printToThermalPrinter(printData, 2); // 2 copies
```

---

## 🎬 Alur Penggunaan Normal

### Di File Blade/View:

```javascript
try {
    // Data yang akan dicetak
    const printData = {
        gentan_no: '123',
        lpk_no: '000001-001',
        product_name: 'SAMPLE PRODUCT',
        code: 'ORD-001',
        // ... data lainnya
    };

    // Cek printer ready
    const printerReady = await window.checkPrinterReady();
    
    if (!printerReady) {
        // Jika belum ready, connect (auto-reconnect atau dialog)
        await window.connectThermalPrinter();
        await new Promise(r => setTimeout(r, 500));
    }

    // Print 2 copies
    await window.printToThermalPrinter(printData, 2);
    
} catch (error) {
    console.error('Print error:', error);
}
```

---

## 🛠️ Troubleshooting

### ❌ Printer Tidak Auto-Connect
**Kemungkinan Penyebab:**
1. Bluetooth HP/Laptop mati
2. Printer Bluetooth mati atau diluar jangkauan
3. Browser tidak support `getDevices()` API
4. Cache browser penuh

**Solusi:**
```javascript
// Hapus printer tersimpan dan pairing ulang
window.forgetThermalPrinter();
await window.connectThermalPrinter(true); // Force dialog
```

### ❌ Ingin Ganti Printer
```javascript
// Hapus printer lama
window.forgetThermalPrinter();

// Connect ke printer baru (akan muncul dialog)
await window.connectThermalPrinter(true);
```

### ❌ Cek Printer Tersimpan
```javascript
const savedPrinter = window.getSavedPrinterName();
if (savedPrinter) {
    console.log('✅ Printer tersimpan:', savedPrinter);
} else {
    console.log('❌ Belum ada printer tersimpan');
}
```

---

## 🔍 Debug Mode

Untuk melihat log detail di browser console:

```javascript
// Cek status printer
await window.checkPrinterReady();

// Lihat output:
// ✅ Found: TM-P20II
// ✅ Printer already connected
// atau
// 🔄 Reconnecting to TM-P20II
// ✅ Reconnected successfully
```

---

## 📱 Browser Support

### ✅ Support Auto-Reconnect:
- Google Chrome 90+ (Desktop & Android)
- Microsoft Edge 90+
- Opera 76+
- Samsung Internet 15+

### ⚠️ Limited Support (Fallback ke Cache):
- Brave Browser (tergantung versi)
- Browser lama tanpa `getDevices()` API

### ❌ Tidak Support:
- Firefox (Web Bluetooth masih experimental)
- Safari (iOS/macOS tidak support Web Bluetooth)

---

## 📊 Data yang Tersimpan

**localStorage Keys:**
```javascript
localStorage.getItem('thermal_printer_name');  // "TM-P20II"
localStorage.getItem('thermal_printer_id');    // Device ID
```

**Cara Hapus Manual:**
```javascript
localStorage.removeItem('thermal_printer_name');
localStorage.removeItem('thermal_printer_id');
```

---

## 🎉 Keuntungan Fitur Ini

✅ **User Experience Lebih Baik** - Tidak perlu pairing berulang-ulang  
✅ **Lebih Cepat** - Auto-connect hanya 1-2 detik  
✅ **Fallback Otomatis** - Jika gagal, tetap ada dialog sebagai backup  
✅ **Smart Detection** - Otomatis detect printer yang sudah dipasangkan  

---

## 📞 Support

Jika ada masalah, cek:
1. Browser console untuk error message
2. Bluetooth device list di OS
3. Pastikan printer dalam mode pairing
4. Clear cache browser jika perlu

---

**Last Updated:** January 3, 2026  
**Version:** 2.0 (with Auto-Reconnect)
