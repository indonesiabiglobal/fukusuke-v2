# 📱 Panduan Update Otomatis PWA Fukusuke

## 📖 Daftar Isi
- [Pengenalan](#pengenalan)
- [Cara Kerja](#cara-kerja)
- [Struktur File](#struktur-file)
- [Cara Menggunakan](#cara-menggunakan)
- [Update Versi Aplikasi](#update-versi-aplikasi)
- [Kustomisasi](#kustomisasi)
- [Troubleshooting](#troubleshooting)
- [FAQ](#faq)

---

## 🎯 Pengenalan

Fitur update otomatis PWA (Progressive Web App) memungkinkan aplikasi Fukusuke untuk:
- ✅ Mendeteksi update baru secara otomatis
- ✅ Menampilkan notifikasi update yang user-friendly
- ✅ Memberikan pilihan kepada user untuk update sekarang atau nanti
- ✅ Auto-reload aplikasi setelah update
- ✅ Memastikan user selalu menggunakan versi terbaru

---

## ⚙️ Cara Kerja

### 1. **Deteksi Update**
```
Service Worker → Check Update (Setiap 1 menit) → Update Found?
                                                    ↓
                                              Yes → Show Notification
                                              No → Continue
```

### 2. **Flow Update**
```
User Buka Aplikasi → SW Check Update → Update Tersedia
                                        ↓
                            Notifikasi Muncul
                                        ↓
                    User Pilih: [Update Sekarang] atau [Nanti Saja]
                                        ↓
                            [Update Sekarang]
                                        ↓
                    SW Install New Version → skipWaiting() → Auto Reload
```

### 3. **Komponen Utama**

#### **Service Worker (`public/sw.js`)**
- Mengelola caching aplikasi
- Mendeteksi versi baru
- Handle message dari aplikasi untuk skip waiting
- Versioning dengan `CACHE_NAME`

#### **Registration Logic (`master.blade.php`)**
- Register service worker
- Listen untuk event `updatefound`
- Menampilkan notifikasi update
- Handle user action (update/dismiss)

#### **UI Notification (`pwa-update-notification.css`)**
- Styling untuk notifikasi update
- Responsive design
- Animasi slide-in

---

## 📁 Struktur File

```
fukusuke-v2/
│
├── public/
│   ├── sw.js                              # Service Worker (PENTING!)
│   └── css/
│       └── pwa-update-notification.css    # Style notifikasi
│
└── resources/
    └── views/
        └── layouts/
            └── master.blade.php           # Registration & Logic
```

---

## 🚀 Cara Menggunakan

### **Untuk Developer**

#### 1. **Deploy Update Baru**
Setelah melakukan perubahan pada aplikasi:

```bash
# 1. Update versi di service worker
# Edit file: public/sw.js
# Ubah CACHE_NAME ke versi baru

# 2. Commit & Push
git add .
git commit -m "Update aplikasi ke versi X.X.X"
git push

# 3. Deploy ke server
# (Sesuai dengan workflow deployment Anda)
```

#### 2. **User akan otomatis dapat notifikasi**
- User yang membuka aplikasi akan otomatis cek update
- Jika ada versi baru, notifikasi akan muncul
- User bisa pilih update sekarang atau nanti

### **Untuk User**

#### 1. **Ketika Notifikasi Muncul**
![Update Notification](https://via.placeholder.com/400x200/667eea/ffffff?text=Update+Available)

Anda akan melihat notifikasi di pojok kanan bawah:
- 🔄 **"Update Tersedia"**
- Penjelasan update
- Dua tombol pilihan

#### 2. **Pilihan Update**

**Opsi 1: Update Sekarang** ✅
- Klik tombol **"Update Sekarang"**
- Aplikasi akan otomatis update
- Browser akan reload
- Versi baru siap digunakan

**Opsi 2: Nanti Saja** ⏰
- Klik tombol **"Nanti Saja"**
- Notifikasi akan hilang
- Update dapat dilakukan nanti dengan reload manual
- Notifikasi akan muncul lagi setelah 1 menit

---

## 🔄 Update Versi Aplikasi

### **Langkah-langkah Update Versi**

#### **Step 1: Update Service Worker Version**

Edit file: `public/sw.js`

```javascript
// SEBELUM
const CACHE_NAME = "fukusuke-v1.0.1";

// SESUDAH (Tingkatkan versi)
const CACHE_NAME = "fukusuke-v1.0.2";
```

#### **Step 2: Skema Versioning**

Gunakan **Semantic Versioning** (X.Y.Z):

- **X (Major)**: Perubahan besar, breaking changes
  ```javascript
  "fukusuke-v2.0.0" // Redesign total, struktur baru
  ```

- **Y (Minor)**: Fitur baru, tidak breaking
  ```javascript
  "fukusuke-v1.1.0" // Tambah fitur export PDF
  "fukusuke-v1.2.0" // Tambah dashboard analytics
  ```

- **Z (Patch)**: Bug fix, perbaikan kecil
  ```javascript
  "fukusuke-v1.0.1" // Fix bug login
  "fukusuke-v1.0.2" // Perbaiki typo
  ```

#### **Step 3: Test Update**

```bash
# 1. Buka aplikasi di browser
# 2. Buka Developer Tools (F12)
# 3. Ke tab "Application" → "Service Workers"
# 4. Klik "Update" untuk test
# 5. Cek Console untuk log update
```

#### **Step 4: Deploy ke Production**

```bash
# Push ke repository
git add public/sw.js
git commit -m "Bump version to v1.0.2"
git push origin main

# Deploy ke server
# (Sesuai workflow Anda: FTP, SSH, CI/CD, etc.)
```

---

## 🎨 Kustomisasi

### **1. Ubah Interval Check Update**

Edit `master.blade.php`:

```javascript
// Cek update setiap 1 menit (default)
setInterval(() => {
    registration.update();
}, 60000); // 60000 ms = 1 menit

// Ubah ke 5 menit
setInterval(() => {
    registration.update();
}, 300000); // 300000 ms = 5 menit

// Ubah ke 30 detik
setInterval(() => {
    registration.update();
}, 30000); // 30000 ms = 30 detik
```

### **2. Ubah Tampilan Notifikasi**

Edit `public/css/pwa-update-notification.css`:

```css
/* Ubah posisi notifikasi */
#pwa-update-notification {
    bottom: 20px;    /* Jarak dari bawah */
    right: 20px;     /* Jarak dari kanan */
    
    /* Atau pojok kiri bawah */
    left: 20px;
    right: auto;
    
    /* Atau tengah bawah */
    left: 50%;
    transform: translateX(-50%);
}

/* Ubah warna background */
#pwa-update-notification {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    
    /* Atau warna solid */
    background: #4CAF50;
    
    /* Atau gradient lain */
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}
```

### **3. Ubah Teks Notifikasi**

Edit `master.blade.php`:

```html
<div class="notification-body">
    <!-- Teks Default -->
    Versi baru aplikasi tersedia. Update sekarang untuk mendapatkan fitur terbaru dan perbaikan bug.
    
    <!-- Ubah ke teks custom -->
    Ada pembaruan baru! Klik update untuk mendapatkan fitur terbaru.
</div>
```

### **4. Auto-Update Tanpa Konfirmasi**

Jika ingin auto-update tanpa notifikasi:

Edit `master.blade.php`:

```javascript
newWorker.addEventListener('statechange', () => {
    if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
        // OPSI 1: Tampilkan notifikasi (default)
        deferredUpdate = newWorker;
        updateAvailable = true;
        showUpdateNotification();
        
        // OPSI 2: Auto-update langsung (hapus kode di atas, ganti dengan ini)
        newWorker.postMessage({ type: 'SKIP_WAITING' });
        if (typeof toastr !== 'undefined') {
            toastr.info('Aplikasi sedang diupdate...');
        }
    }
});
```

---

## 🔧 Troubleshooting

### **❌ Problem: Notifikasi Tidak Muncul**

**Solusi:**

1. **Cek Service Worker terdaftar**
   ```javascript
   // Buka Console browser, ketik:
   navigator.serviceWorker.getRegistrations().then(regs => {
       console.log('Registered SWs:', regs);
   });
   ```

2. **Cek versi sudah berubah**
   - Pastikan `CACHE_NAME` di `sw.js` sudah diupdate
   - Clear cache browser: `Ctrl + Shift + Delete`

3. **Force update manual**
   - Buka DevTools → Application → Service Workers
   - Klik "Update" atau "Unregister" lalu reload

### **❌ Problem: Update Tidak Berjalan**

**Solusi:**

1. **Cek Console untuk error**
   ```javascript
   // Lihat error di Console browser
   ```

2. **Pastikan file sw.js accessible**
   ```bash
   # Test di browser:
   https://your-domain.com/sw.js
   # Harus return file JavaScript, bukan 404
   ```

3. **Hard reload**
   ```
   Ctrl + Shift + R (Windows/Linux)
   Cmd + Shift + R (Mac)
   ```

### **❌ Problem: Service Worker Conflict**

**Solusi:**

```javascript
// Unregister semua service worker lama
navigator.serviceWorker.getRegistrations().then(function(registrations) {
    for(let registration of registrations) {
        registration.unregister();
    }
}).then(() => {
    window.location.reload();
});
```

### **❌ Problem: Cache Tidak Clear**

**Solusi:**

```javascript
// Clear semua cache secara manual
caches.keys().then(function(names) {
    for (let name of names) {
        caches.delete(name);
    }
}).then(() => {
    window.location.reload();
});
```

---

## ❓ FAQ

### **Q: Berapa lama waktu check update?**
**A:** Default setiap 1 menit. Bisa diubah di `master.blade.php` pada bagian `setInterval`.

### **Q: Apakah user harus online untuk update?**
**A:** Ya, user harus online untuk mendeteksi dan download update baru.

### **Q: Apakah update otomatis berjalan di background?**
**A:** Service worker akan check update di background setiap 1 menit saat aplikasi dibuka.

### **Q: Bagaimana jika user tidak klik update?**
**A:** Notifikasi akan muncul lagi setelah 1 menit, atau user bisa reload manual untuk update.

### **Q: Apakah data user akan hilang saat update?**
**A:** Tidak, update hanya refresh aplikasi. Data di database/session tetap aman.

### **Q: Bagaimana cara force update untuk semua user?**
**A:** 
1. Update versi di `sw.js`
2. Deploy ke production
3. User akan otomatis dapat notifikasi saat buka aplikasi

### **Q: Apakah bisa disable notifikasi?**
**A:** Bisa, edit `master.blade.php` dan comment/hapus fungsi `showUpdateNotification()`.

### **Q: Bagaimana cara test di localhost?**
**A:** Service Worker hanya bekerja di HTTPS atau localhost. Test di:
```bash
php artisan serve
# Buka: http://localhost:8000
```

### **Q: Apakah work di semua browser?**
**A:** Ya, support di:
- ✅ Chrome/Edge (Chromium)
- ✅ Firefox
- ✅ Safari (iOS 11.3+)
- ✅ Opera
- ❌ Internet Explorer (tidak support PWA)

---

## 📊 Best Practices

### **1. Versioning**
```javascript
// ✅ GOOD: Semantic versioning
"fukusuke-v1.0.1"
"fukusuke-v1.2.0"
"fukusuke-v2.0.0"

// ❌ BAD: Random versioning
"fukusuke-new"
"fukusuke-updated"
"fukusuke-fix"
```

### **2. Update Frequency**
```javascript
// ✅ GOOD: Reasonable interval
setInterval(() => registration.update(), 60000);  // 1 menit
setInterval(() => registration.update(), 300000); // 5 menit

// ❌ BAD: Terlalu sering (overload)
setInterval(() => registration.update(), 1000);   // 1 detik
setInterval(() => registration.update(), 5000);   // 5 detik
```

### **3. User Experience**
```javascript
// ✅ GOOD: Beri pilihan ke user
showUpdateNotification(); // User bisa pilih

// ⚠️ CAUTION: Auto-update paksa (bisa ganggu user)
// Gunakan hanya untuk critical security update
newWorker.postMessage({ type: 'SKIP_WAITING' });
```

### **4. Testing**
```bash
# ✅ GOOD: Test sebelum deploy
1. Test di localhost
2. Test update flow
3. Check console untuk error
4. Test di staging environment
5. Deploy ke production

# ❌ BAD: Deploy langsung tanpa test
git push origin main # Langsung production
```

---

## 📝 Changelog Template

Gunakan template ini untuk tracking update:

```markdown
# Changelog

## [1.0.2] - 2026-01-02
### Added
- Fitur export PDF pada laporan
- Dashboard analytics baru

### Fixed
- Bug pada form login
- Perbaikan responsive mobile

### Changed
- Update UI notifikasi

## [1.0.1] - 2025-12-20
### Fixed
- Bug minor pada calculation
```

---

## 🆘 Support

Jika ada pertanyaan atau masalah:

1. **Check dokumentasi ini terlebih dahulu**
2. **Check Console browser untuk error**
3. **Kontak developer team**

---

## 📚 Resources

- [MDN: Service Worker API](https://developer.mozilla.org/en-US/docs/Web/API/Service_Worker_API)
- [Google: Service Worker Lifecycle](https://developers.google.com/web/fundamentals/primers/service-workers/lifecycle)
- [PWA Update Patterns](https://web.dev/service-worker-lifecycle/)

---

## ✅ Checklist Deploy Update

Gunakan checklist ini setiap kali deploy update:

- [ ] Update `CACHE_NAME` di `public/sw.js`
- [ ] Test update flow di localhost
- [ ] Check console untuk error
- [ ] Test notifikasi muncul
- [ ] Test tombol "Update Sekarang" berfungsi
- [ ] Test tombol "Nanti Saja" berfungsi
- [ ] Test auto-reload setelah update
- [ ] Commit & push ke repository
- [ ] Deploy ke staging (jika ada)
- [ ] Test di staging
- [ ] Deploy ke production
- [ ] Monitor user feedback
- [ ] Update changelog

---

**Last Updated:** 2 Januari 2026  
**Version:** 1.0.0  
**Maintained by:** Indonesia BI Global - Fukusuke Team
