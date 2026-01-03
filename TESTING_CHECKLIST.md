# ✅ Testing Checklist - Bluetooth Printer Auto-Reconnect

## 📋 Pre-Testing Setup

- [ ] Pastikan printer Bluetooth (TM-P20/TM-P20II) nyala dan charged
- [ ] Pastikan Bluetooth laptop/HP aktif
- [ ] Buka browser Chrome/Edge (versi 90+)
- [ ] Buka Developer Console (F12)
- [ ] Clear cache browser (Ctrl+Shift+Delete)
- [ ] Hapus localStorage printer: `localStorage.removeItem('thermal_printer_name')`

---

## 🧪 Test Case 1: Pairing Pertama Kali

**Langkah:**
1. Buka halaman yang ada fitur print (contoh: Label Gentan)
2. Klik tombol "Print"
3. Dialog Bluetooth pairing akan muncul
4. Pilih printer dari list
5. Klik "Pair"

**Expected Result:**
- ✅ Dialog Bluetooth muncul
- ✅ Printer berhasil terhubung
- ✅ Print berhasil
- ✅ Console log: `✅ Printer connected & saved: TM-P20II`
- ✅ localStorage berisi: `thermal_printer_name` = "TM-P20II"

**Actual Result:**
- [ ] PASS
- [ ] FAIL (Describe issue): _________________

---

## 🧪 Test Case 2: Auto-Reconnect (Happy Path)

**Langkah:**
1. Tutup browser (atau reload halaman)
2. Pastikan printer masih nyala
3. Buka halaman print lagi
4. Klik tombol "Print"

**Expected Result:**
- ✅ **TIDAK ADA DIALOG** yang muncul
- ✅ Auto-connect dalam 1-2 detik
- ✅ Print langsung jalan
- ✅ Console log: `🔄 Auto-reconnecting to: TM-P20II`
- ✅ Console log: `✅ Auto-reconnected successfully!`

**Actual Result:**
- [ ] PASS
- [ ] FAIL (Describe issue): _________________

---

## 🧪 Test Case 3: Auto-Reconnect Gagal (Printer Mati)

**Langkah:**
1. Matikan printer atau matikan Bluetooth printer
2. Reload halaman
3. Klik tombol "Print"

**Expected Result:**
- ✅ Auto-reconnect mencoba tapi gagal
- ✅ Console log: `❌ Auto-reconnect failed`
- ✅ Fallback: Dialog pairing muncul
- ✅ User bisa pilih printer lagi atau cancel

**Actual Result:**
- [ ] PASS
- [ ] FAIL (Describe issue): _________________

---

## 🧪 Test Case 4: Forget Printer

**Langkah:**
1. Jalankan di console: `window.forgetThermalPrinter()`
2. Cek di console: `window.getSavedPrinterName()`
3. Klik tombol "Print"

**Expected Result:**
- ✅ Console log: `✅ Printer berhasil dihapus dari memory`
- ✅ `getSavedPrinterName()` return `null`
- ✅ Dialog pairing muncul lagi
- ✅ localStorage `thermal_printer_name` dihapus

**Actual Result:**
- [ ] PASS
- [ ] FAIL (Describe issue): _________________

---

## 🧪 Test Case 5: Force Dialog (Bypass Auto-Reconnect)

**Langkah:**
1. Pastikan ada printer tersimpan
2. Jalankan di console: `await window.connectThermalPrinter(true)`

**Expected Result:**
- ✅ Dialog pairing **langsung muncul**
- ✅ Tidak ada attempt auto-reconnect
- ✅ Console log: TIDAK ada `🔄 Auto-reconnecting`

**Actual Result:**
- [ ] PASS
- [ ] FAIL (Describe issue): _________________

---

## 🧪 Test Case 6: Get Saved Printer Name

**Langkah:**
1. Pastikan sudah ada printer tersimpan
2. Jalankan di console: `window.getSavedPrinterName()`

**Expected Result:**
- ✅ Return nama printer (contoh: "TM-P20II")
- ✅ Jika belum ada, return `null`

**Actual Result:**
- [ ] PASS
- [ ] FAIL (Describe issue): _________________

---

## 🧪 Test Case 7: Check Printer Ready

**Langkah:**
1. Printer nyala dan tersimpan
2. Jalankan: `await window.checkPrinterReady()`
3. Matikan printer
4. Jalankan lagi: `await window.checkPrinterReady()`

**Expected Result:**
- ✅ Printer nyala: return `true`
- ✅ Console: `✅ Found: TM-P20II`
- ✅ Printer mati: return `false`
- ✅ Console: `⚠️ Epson TM-P20/TM-P20II not paired`

**Actual Result:**
- [ ] PASS
- [ ] FAIL (Describe issue): _________________

---

## 🧪 Test Case 8: Print dengan Auto-Reconnect

**Langkah:**
1. Reload halaman (characteristic jadi null)
2. Printer masih nyala dan tersimpan
3. Langsung panggil: `await window.printToThermalPrinter(testData, 1)`

**Expected Result:**
- ✅ Auto-reconnect otomatis dipanggil
- ✅ Console: `Reconnecting to saved printer...`
- ✅ Console: `✅ Auto-reconnected successfully!`
- ✅ Print berhasil tanpa user interaction

**Actual Result:**
- [ ] PASS
- [ ] FAIL (Describe issue): _________________

---

## 🧪 Test Case 9: Multiple Prints (Stress Test)

**Langkah:**
1. Print 5x berturut-turut
2. Tunggu setiap print selesai
3. Cek hasil print semua

**Expected Result:**
- ✅ Semua print berhasil
- ✅ Tidak ada error di console
- ✅ Connection tetap stable
- ✅ Tidak ada memory leak

**Actual Result:**
- [ ] PASS
- [ ] FAIL (Describe issue): _________________

---

## 🧪 Test Case 10: Browser Tidak Support getDevices()

**Langkah:**
1. Test di Firefox atau browser lama
2. Klik tombol "Print"

**Expected Result:**
- ✅ Fallback ke dialog pairing
- ✅ Console: `⚠️ getDevices() not supported`
- ✅ Printer tetap bisa connect dan print
- ✅ Tidak ada fatal error

**Actual Result:**
- [ ] PASS
- [ ] FAIL (Describe issue): _________________

---

## 🧪 Test Case 11: UI Management Component

**Langkah:**
1. Include `printer-management-ui.blade.php` di halaman
2. Test semua tombol:
   - Cek Status
   - Connect/Pairing
   - Pairing Ulang
   - Hapus Printer
   - Test Print

**Expected Result:**
- ✅ Semua tombol berfungsi
- ✅ Status text update sesuai action
- ✅ Printer info show/hide dengan benar
- ✅ Visual feedback (warna) sesuai status

**Actual Result:**
- [ ] PASS
- [ ] FAIL (Describe issue): _________________

---

## 🧪 Test Case 12: Backward Compatibility

**Langkah:**
1. Buka file blade yang SUDAH ada (tanpa modifikasi)
2. File yang menggunakan kode lama:
   ```javascript
   const printerReady = await window.checkPrinterReady();
   if (!printerReady) {
       await window.connectThermalPrinter();
   }
   await window.printToThermalPrinter(printData, 2);
   ```
3. Test print normal

**Expected Result:**
- ✅ Kode lama tetap berfungsi
- ✅ Auto-reconnect tetap bekerja
- ✅ Tidak ada breaking changes

**Actual Result:**
- [ ] PASS
- [ ] FAIL (Describe issue): _________________

---

## 📊 Testing Summary

**Total Test Cases:** 12  
**Passed:** ___  
**Failed:** ___  
**Pass Rate:** ____%  

**Tested By:** _________________  
**Date:** _________________  
**Browser:** _________________  
**Printer Model:** _________________  

---

## 🐛 Known Issues

Issue #1:
- **Description:** _________________
- **Severity:** [ ] Critical [ ] Major [ ] Minor
- **Workaround:** _________________

Issue #2:
- **Description:** _________________
- **Severity:** [ ] Critical [ ] Major [ ] Minor
- **Workaround:** _________________

---

## ✅ Sign-off

**QA Approval:**
- [ ] All critical tests passed
- [ ] Documentation complete
- [ ] Ready for production

**Signature:** _________________  
**Date:** _________________

---

## 📝 Notes

Additional observations or comments:

_____________________________________
_____________________________________
_____________________________________
