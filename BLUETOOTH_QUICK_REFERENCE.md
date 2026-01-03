# 🖨️ Bluetooth Printer - Quick Reference Card

## 📱 Untuk User (Non-Technical)

### ✅ Pertama Kali Pakai
1. Nyalakan printer Bluetooth
2. Klik tombol "Print" di aplikasi
3. **Pilih printer dari daftar** yang muncul
4. Printer tersimpan otomatis ✅

### ✅ Pakai Selanjutnya  
1. Nyalakan printer Bluetooth  
2. Klik tombol "Print" di aplikasi  
3. **Langsung print** (tanpa pilih printer lagi) ✅  

### ⚠️ Kalau Gagal Print
- Cek printer nyala & dalam jangkauan
- Cek Bluetooth HP/Laptop aktif
- Coba refresh halaman browser
- Hubungi IT support

### 🔄 Ganti Printer Lain
1. Buka "Pengaturan Printer" di aplikasi
2. Klik "Hapus Printer"
3. Klik "Pairing Ulang"
4. Pilih printer baru

---

## 💻 Untuk Developer

### 🔧 Fungsi Utama

```javascript
// 1. Cek printer ready
const ready = await window.checkPrinterReady();

// 2. Connect (auto-reconnect jika ada printer tersimpan)
await window.connectThermalPrinter();

// 3. Connect dengan force dialog (skip auto-reconnect)
await window.connectThermalPrinter(true);

// 4. Print dengan auto-reconnect
await window.printToThermalPrinter(data, 2);

// 5. Cek nama printer tersimpan
const name = window.getSavedPrinterName();

// 6. Hapus printer tersimpan
window.forgetThermalPrinter();
```

### 📋 Template Print

```javascript
async function handlePrint() {
    try {
        // Data
        const printData = {
            gentan_no: '123',
            lpk_no: '000001-001',
            product_name: 'PRODUCT NAME',
            code: 'ORD-001',
            code_alias: 'ALIAS',
            production_date: '03/01/2026',
            work_hour: '08:00-16:00',
            work_shift: 'Pagi',
            machineno: 'M-001',
            berat_produksi: '10.5',
            panjang_produksi: '100',
            selisih: '0',
            nomor_han: 'H-001',
            nik: '12345',
            empname: 'EMPLOYEE NAME'
        };

        // Check & Connect
        const ready = await window.checkPrinterReady();
        if (!ready) {
            await window.connectThermalPrinter();
            await new Promise(r => setTimeout(r, 500));
        }

        // Print (2 copies)
        await window.printToThermalPrinter(printData, 2);
        
        console.log('✅ Print success!');
    } catch (error) {
        console.error('❌ Print error:', error);
        alert('Print gagal: ' + error.message);
    }
}
```

### 🎯 Best Practices

1. **Selalu cek ready sebelum print:**
   ```javascript
   const ready = await window.checkPrinterReady();
   if (!ready) await window.connectThermalPrinter();
   ```

2. **Handle errors dengan try-catch:**
   ```javascript
   try {
       await window.printToThermalPrinter(data, 2);
   } catch (error) {
       console.error(error);
       alert('Print gagal: ' + error.message);
   }
   ```

3. **Gunakan auto-reconnect (default behavior):**
   ```javascript
   // ✅ GOOD - Auto-reconnect
   await window.connectThermalPrinter();
   
   // ⚠️ ONLY if user explicitly wants to change printer
   await window.connectThermalPrinter(true);
   ```

4. **Debug dengan console.log:**
   ```javascript
   console.log('Saved printer:', window.getSavedPrinterName());
   console.log('Ready:', await window.checkPrinterReady());
   ```

### 🐛 Troubleshooting Commands

```javascript
// Reset everything
window.forgetThermalPrinter();
location.reload();

// Check localStorage
console.log(localStorage.getItem('thermal_printer_name'));
console.log(localStorage.getItem('thermal_printer_id'));

// Clear localStorage manually
localStorage.removeItem('thermal_printer_name');
localStorage.removeItem('thermal_printer_id');
```

---

## 📊 Browser Compatibility

| Browser | Auto-Reconnect |
|---------|----------------|
| Chrome 90+ | ✅ YES |
| Edge 90+ | ✅ YES |
| Opera 76+ | ✅ YES |
| Samsung Internet 15+ | ✅ YES |
| Firefox | ❌ NO (Web Bluetooth not supported) |
| Safari | ❌ NO (Web Bluetooth not supported) |

---

## 🔍 Console Log Meanings

| Log | Meaning |
|-----|---------|
| `✅ Found: TM-P20II` | Printer detected |
| `✅ Printer already connected` | Already connected, no action needed |
| `🔄 Reconnecting to TM-P20II` | Auto-reconnecting... |
| `✅ Reconnected successfully` | Auto-reconnect success! |
| `✅ Auto-reconnected successfully!` | Connected without dialog |
| `⚠️ No saved printer found` | No printer stored, need pairing |
| `⚠️ getDevices() not supported` | Browser doesn't support auto-reconnect |
| `❌ Auto-reconnect failed` | Failed, will show dialog |

---

## 📞 Support Contacts

**Technical Issues:**  
- Check browser console (F12)  
- Clear cache & reload  
- Try different browser  

**Hardware Issues:**  
- Check printer battery  
- Check Bluetooth pairing  
- Restart printer  

---

## 📝 File Locations

**Main Script:**  
`public/js/thermal-printer-global.js`

**Documentation:**  
`BLUETOOTH_PRINTER_GUIDE.md`  
`AUTO_RECONNECT_IMPLEMENTATION.md`

**UI Component:**  
`resources/views/components/printer-management-ui.blade.php`

---

**Last Updated:** January 3, 2026  
**Version:** 2.0 (Auto-Reconnect)
