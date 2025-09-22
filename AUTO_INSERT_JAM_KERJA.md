# Auto Insert Jam Kerja Documentation

## Overview
Fitur ini secara otomatis menginsert data jam kerja untuk semua mesin pada shift sebelumnya dengan data default ketika tidak ada operator yang mengoperasikan mesin.

## Konsep
- **Tujuan**: Mengisi data jam kerja kosong untuk mesin yang tidak dioperasikan pada shift tertentu
- **Data yang diinsert**:
  - `employee_id` = `null` (tidak ada operator)
  - `work_hour` = `00:00:00` (tidak ada jam kerja produktif)
  - `off_hour` = `08:00:00` (mesin mati selama 8 jam)
  - `on_hour` = `00:00:00` (mesin tidak beroperasi)
  - `jam_mati_mesin_id` = `10` (kode reason mesin mati)

## Cara Kerja

### 1. Penentuan Shift Sebelumnya
- Sistem mendeteksi shift saat ini berdasarkan waktu sekarang
- Mencari shift sebelumnya dari shift saat ini
- Jika shift saat ini adalah shift pertama, maka shift sebelumnya adalah shift terakhir dari hari sebelumnya

### 2. Penentuan Tanggal Kerja
- Untuk shift normal: tanggal kerja adalah hari sebelumnya
- Untuk shift malam yang melewati tengah malam: tanggal kerja disesuaikan

### 3. Filter Mesin
- Mengambil semua mesin aktif berdasarkan department (Infure/Seitai)
- Mesin yang sudah memiliki data jam kerja untuk shift dan tanggal tersebut akan di-skip

## Command Usage

### Manual Execution
```bash
# Untuk department Infure
php artisan jamkerja:auto-insert --department=infure

# Untuk department Seitai
php artisan jamkerja:auto-insert --department=seitai

# Dry run mode (lihat apa yang akan diinsert tanpa menginsert)
php artisan jamkerja:auto-insert --department=infure --dry-run
```

### Scheduled Execution (Otomatis)
Scheduler telah dikonfigurasi untuk menjalankan command setiap 2 jam untuk kedua department.

## Konfigurasi Scheduler

### Opsi 1: Setiap 2 Jam (Aktif)
```php
$schedule->command('jamkerja:auto-insert --department=infure')
         ->everyTwoHours()
         ->withoutOverlapping()
         ->runInBackground();
```

### Opsi 2: Pada Waktu Tertentu (Dikomentari)
Untuk menggunakan jadwal pada waktu tertentu, uncomment dan sesuaikan waktu di file `app/Console/Kernel.php`:

```php
// Run at 7:15 AM (after morning shift starts)
$schedule->command('jamkerja:auto-insert --department=infure')
         ->dailyAt('07:15')
         ->withoutOverlapping();
```

## Struktur Data yang Diinsert

### Tabel: `tdjamkerjamesin`
```php
[
    'working_date' => '2024-XX-XX',
    'work_shift' => X, // ID shift sebelumnya
    'machine_id' => X, // ID mesin
    'employee_id' => null,
    'department_id' => X, // ID department
    'work_hour' => '00:00:00',
    'off_hour' => '08:00:00',
    'on_hour' => '00:00:00',
    'created_by' => 'system',
    'updated_by' => 'system'
]
```

### Tabel: `tdjamkerja_jammatimesin`
```php
[
    'jam_kerja_mesin_id' => X, // ID dari record jam kerja mesin
    'jam_mati_mesin_id' => 10, // Fixed ID untuk reason mesin mati
    'off_hour' => '08:00',
    'from' => null,
    'to' => null
]
```

## Logging
Semua aktivitas dicatat dalam log Laravel:
- Successful execution dengan jumlah record yang diinsert/skip
- Error dan exception details

## Setup untuk Production

### 1. Pastikan Cron Job Aktif
```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

### 2. Monitoring
- Check log files secara berkala
- Monitor database untuk memastikan data terinsert dengan benar
- Verifikasi tidak ada duplicate data

## Troubleshooting

### Command Tidak Jalan
1. Pastikan cron job sudah disetup dengan benar
2. Check log error di Laravel log
3. Verifikasi permission file

### Data Tidak Terinsert
1. Pastikan ada shift yang terdefinisi di `msworkingshift`
2. Verifikasi mesin aktif di department yang sesuai
3. Check apakah data sudah ada sebelumnya (akan di-skip)

### Duplicate Data
Sistem sudah memiliki protection terhadap duplicate data berdasarkan:
- `machine_id`
- `working_date`
- `work_shift`

## Customization

Untuk mengubah konfigurasi default, edit file:
`app/Console/Commands/AutoInsertJamKerja.php`

Contoh perubahan yang bisa dilakukan:
- Mengubah `jam_mati_mesin_id` dari 10 ke ID lain
- Mengubah jam default (`work_hour`, `off_hour`, `on_hour`)
- Mengubah logika penentuan shift sebelumnya
- Menambah filter mesin berdasarkan kriteria lain
