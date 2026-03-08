<p align="center">
  <img src="public/images/logo.png" alt="Logo PT Multyartha Indo Daya" width="120">
</p>

<h1 align="center">Sistem Manajemen Gudang</h1>
<h4 align="center">PT MULTYARTHA INDO DAYA — Warehouse Management ERP</h4>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 12">
  <img src="https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.2+">
  <img src="https://img.shields.io/badge/Tailwind_CSS-4-06B6D4?style=for-the-badge&logo=tailwindcss&logoColor=white" alt="Tailwind CSS">
  <img src="https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL">
  <img src="https://img.shields.io/badge/Telegram_Bot-API-26A5E4?style=for-the-badge&logo=telegram&logoColor=white" alt="Telegram Bot">
</p>

<p align="center">
  Sistem ERP terintegrasi untuk manajemen gudang, <em>purchase order</em>, stok barang, dan <strong>ChatOps via Telegram Bot</strong>.<br>
  Dirancang khusus untuk kebutuhan operasional PT Multyartha Indo Daya.
</p>

---

## 📋 Daftar Isi

- [Tentang Proyek](#-tentang-proyek)
- [Fitur Utama](#-fitur-utama)
- [Fitur Unggulan: ChatOps Telegram Bot](#-fitur-unggulan-chatops-telegram-bot)
- [Tech Stack](#-tech-stack)
- [Arsitektur & Struktur Proyek](#-arsitektur--struktur-proyek)
- [Sistem Role & Hak Akses](#-sistem-role--hak-akses)
- [Instalasi & Setup](#-instalasi--setup)
- [Konfigurasi Telegram Bot](#-konfigurasi-telegram-bot)
- [Perintah Artisan Kustom](#-perintah-artisan-kustom)
- [Default User & Akses](#-default-user--akses)
- [Screenshot](#-screenshot)
- [Lisensi](#-lisensi)

---

## 🏢 Tentang Proyek

**Sistem Manajemen Gudang** adalah aplikasi web berbasis ERP (_Enterprise Resource Planning_) yang dikembangkan secara khusus untuk **PT Multyartha Indo Daya**, sebuah perusahaan distribusi bahan bangunan yang berlokasi di Surya Asri Residence 9A, Cipagalo Bojongsoang, Kabupaten Bandung.

Sistem ini menangani seluruh siklus operasional gudang mulai dari:
- Pencatatan data master (Vendor, Divisi, Satuan, Barang)
- Pembuatan dan pelacakan _Purchase Order_ (PO)
- Transaksi masuk/keluar barang di gudang
- Pemantauan stok real-time
- Ekspor laporan dalam format PDF dan Excel
- **Notifikasi & input data secara real-time via Telegram Bot** (ChatOps)

Aplikasi ini dibangun dengan pendekatan _mobile-first responsive design_ sehingga dapat diakses secara optimal dari desktop, tablet, maupun smartphone.

---

## ✨ Fitur Utama

### 📦 Manajemen Purchase Order (PO)
- Buat PO lengkap dengan detail barang, vendor, dan jumlah
- Lifecycle PO: **Draft → Sampai → Lunas → Dibatalkan**
- Mekanisme pembatalan dengan pilihan **Void** (hapus stok) atau **Refund** (kembalikan stok)
- Pencatatan DP (_Down Payment_) dan Tempo pembayaran
- Tanggal jatuh tempo untuk monitoring pembayaran

### 🏪 Master Data
- **Vendor**: CRUD lengkap dengan data kontak dan alamat
- **Divisi**: Kategori departemen/divisi perusahaan
- **Satuan (UOM)**: Unit of Measurement untuk barang
- **Barang**: Master item lengkap dengan kode, kategori, harga, stok, dan QR Code

### 📊 Transaksi & Stok Gudang
- Pencatatan transaksi **Masuk** dan **Keluar** barang
- Stok otomatis ter-update via Observer pattern
- Stock Opname real-time
- Riwayat transaksi lengkap dengan filter & pencarian

### 📄 Ekspor Laporan
- **PDF**: Laporan dengan _kop surat_ resmi perusahaan (logo + alamat)
- **Excel**: Export data barang dan transaksi
- **QR Code**: Cetak label QR Code per barang

### 👥 Manajemen User (4 Role)
- CRUD user dengan pembagian hak akses
- Ubah password sendiri via halaman Profil
- Koneksi & reset Telegram ID per user
- Show/hide password toggle di semua form

### 🛡️ Keamanan
- Autentikasi berbasis **username** (bukan email)
- Role-based access control (RBAC) dengan custom middleware
- Session-based authentication
- Bcrypt password hashing (12 rounds)

---

## 🤖 Fitur Unggulan: ChatOps Telegram Bot

Sistem ini memiliki fitur unggulan berupa **integrasi penuh dengan Telegram Bot** yang memungkinkan tim operasional berinteraksi dengan sistem gudang langsung dari aplikasi Telegram — tanpa perlu membuka browser.

### Fase 1 — Autentikasi
| Perintah | Fungsi |
|----------|--------|
| `/start` | Memulai bot dan melihat panduan |
| `/login [username] [password]` | Login & tautkan akun ke Telegram |
| `/logout` | Lepas tautan akun Telegram |
| `/status` | Cek status login saat ini |

### Fase 2 — Read-Only Data
| Perintah | Fungsi |
|----------|--------|
| `/menu` | Tampilkan semua perintah yang tersedia |
| `/cek_stok` | Lihat ringkasan stok barang |
| `/cek_stok [keyword]` | Cari stok barang tertentu |
| `/cek_po` | Lihat daftar PO aktif |
| `/cek_po [nomor]` | Detail PO berdasarkan nomor |

### Fase 3 — Input Transaksi via Chatbot
| Perintah | Fungsi |
|----------|--------|
| `/input_transaksi` | Mulai wizard input transaksi gudang |
| Pilih jenis | Masuk / Keluar (via tombol inline) |
| Pilih barang | Daftar barang tersedia (via tombol) |
| Input jumlah | Ketik angka jumlah barang |
| Konfirmasi | Review & konfirmasi / batal (via tombol) |

### 📅 Daily Report Terjadwal
- **Otomatis setiap pukul 08:00 WIB** dikirim ke group Telegram
- Berisi: PO jatuh tempo H-3 dan stok kritis (≤ 10 unit)
- Dapat dijalankan manual via `php artisan app:send-daily-gudang-report`

### 🔔 Notifikasi Real-time
- Notifikasi otomatis saat PO dibuat, PO sampai, PO lunas, PO dibatalkan
- Notifikasi transaksi masuk/keluar barang
- Semua notifikasi dikirim ke group Telegram perusahaan

---

## 🛠️ Tech Stack

| Kategori | Teknologi |
|----------|-----------|
| **Framework** | Laravel 12 (PHP 8.2+) |
| **Frontend** | Tailwind CSS 4 (CDN), Vanilla JavaScript |
| **Database** | MySQL 8.0 / MariaDB 10.x |
| **PDF Export** | barryvdh/laravel-dompdf ^3.1 |
| **Excel Export** | maatwebsite/excel ^3.1 |
| **QR Code** | simplesoftwareio/simple-qrcode ^4.2 |
| **Telegram Bot** | Native HTTP Client (Laravel `Http` facade), Webhook mode |
| **Tunneling** | Ngrok (untuk expose webhook ke internet) |
| **UI Components** | SweetAlert2 v11, Tom Select 2.4.3, Flatpickr |
| **Server** | Laragon (Windows) / Apache / Nginx |
| **Timezone** | Asia/Jakarta (WIB) |

---

## 📁 Arsitektur & Struktur Proyek

```
sistem-gudang-mid/
├── app/
│   ├── Console/Commands/
│   │   └── SendDailyGudangReport.php    # Scheduled daily Telegram report
│   ├── Exports/
│   │   ├── ItemsExport.php              # Excel export: master barang
│   │   └── TransactionsExport.php       # Excel export: transaksi
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php       # Login/logout (username-based)
│   │   │   ├── DivisiController.php     # CRUD divisi
│   │   │   ├── ItemController.php       # CRUD barang + QR + export
│   │   │   ├── ProfileController.php    # Ubah password sendiri
│   │   │   ├── PurchaseOrderController.php  # PO lifecycle
│   │   │   ├── TelegramSetupController.php  # Webhook setup helper
│   │   │   ├── TelegramWebhookController.php # Telegram Bot (22 methods)
│   │   │   ├── TransactionController.php    # Transaksi gudang
│   │   │   ├── UserController.php       # CRUD manajemen user
│   │   │   └── VendorController.php     # CRUD vendor
│   │   └── Middleware/
│   │       └── RoleMiddleware.php       # Custom RBAC middleware
│   ├── Models/                          # 8 Eloquent models
│   ├── Observers/
│   │   ├── PurchaseOrderDetailObserver.php  # Auto-update stok saat PO
│   │   └── TransactionObserver.php          # Auto-update stok saat transaksi
│   └── Providers/
│       └── AppServiceProvider.php
├── database/
│   ├── migrations/                      # 16 migration files
│   └── seeders/
│       ├── DatabaseSeeder.php           # Main seeder
│       ├── UserSeeder.php               # 3 default users
│       ├── PutriAdminSeeder.php         # Admin user
│       └── UomSeeder.php                # 12 unit satuan
├── resources/views/
│   ├── auth/                            # Login page
│   ├── dashboard.blade.php              # Dashboard super_admin
│   ├── divisis/                         # Views divisi (index, create, edit)
│   ├── items/                           # Views barang (index, create, edit, qr)
│   ├── layouts/app.blade.php            # Master layout + responsive navbar
│   ├── profile/                         # Halaman profil
│   ├── purchase_orders/                 # Views PO (index, create, show)
│   ├── transactions/                    # Views transaksi (index, create)
│   ├── users/                           # Views manajemen user
│   └── vendors/                         # Views vendor (index, create, edit)
├── routes/
│   ├── web.php                          # 60+ route definitions
│   └── console.php                      # Scheduler: daily report 08:00 WIB
└── public/images/
    └── logo.png                         # Logo perusahaan
```

---

## 🔐 Sistem Role & Hak Akses

| Fitur | Super Admin | Admin | Purchase | Gudang |
|-------|:-----------:|:-----:|:--------:|:------:|
| Dashboard | ✅ | ❌ | ❌ | ❌ |
| Manajemen User | ✅ | ✅ | ❌ | ❌ |
| Master Vendor | ✅ (CRUD) | ❌ | ✅ (CRUD) | 👁️ Read |
| Master Divisi | ✅ (CRUD) | ❌ | ✅ (CRUD) | 👁️ Read |
| Master Barang | ✅ (CRUD) | ❌ | ✅ (CRUD) | 👁️ Read |
| Purchase Order | ✅ | ❌ | ✅ | ❌ |
| Transaksi Gudang | ✅ | ❌ | ✅ | ✅ |
| Export PDF/Excel | ✅ | ❌ | ✅ | ✅ |
| Profil (Ubah PW) | ✅ | ✅ | ✅ | ✅ |
| Telegram Bot | ✅ | ✅ | ✅ | ✅ |

---

## 🚀 Instalasi & Setup

### Prasyarat
- PHP ≥ 8.2 dengan ekstensi: `mbstring`, `xml`, `curl`, `mysql`, `gd`, `zip`
- Composer 2.x
- MySQL 8.0+ / MariaDB 10.x+
- Node.js ≥ 18 & npm (opsional, untuk build asset)
- Git

### Langkah Instalasi

```bash
# 1. Clone repository
git clone https://github.com/username/sistem-gudang-mid.git
cd sistem-gudang-mid

# 2. Install dependencies PHP
composer install

# 3. Salin file environment
cp .env.example .env

# 4. Generate application key
php artisan key:generate

# 5. Buat database MySQL
#    Buka MySQL client dan jalankan:
#    CREATE DATABASE db_gudang_mid CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# 6. Konfigurasi .env — sesuaikan bagian berikut:
#    DB_CONNECTION=mysql
#    DB_HOST=127.0.0.1
#    DB_PORT=3306
#    DB_DATABASE=db_gudang_mid
#    DB_USERNAME=root
#    DB_PASSWORD=
#
#    TELEGRAM_BOT_TOKEN=your_bot_token_here
#    TELEGRAM_CHAT_ID=your_group_chat_id_here

# 7. Jalankan migrasi database
php artisan migrate

# 8. Jalankan seeder (buat user default & satuan)
php artisan db:seed

# 9. Jalankan seeder tambahan (user admin)
php artisan db:seed --class=PutriAdminSeeder

# 10. (Opsional) Install & build frontend assets
npm install && npm run build

# 11. Jalankan server development
php artisan serve
```

Aplikasi akan tersedia di `http://localhost:8000`.

---

## 🤖 Konfigurasi Telegram Bot

### 1. Buat Bot via BotFather
1. Buka Telegram, cari **@BotFather**
2. Kirim `/newbot`, ikuti instruksi, dan catat **Bot Token**
3. Masukkan token ke `.env`: `TELEGRAM_BOT_TOKEN=your_token`

### 2. Dapatkan Chat ID Group
1. Tambahkan bot ke group Telegram perusahaan
2. Kirim pesan di group, lalu buka:
   ```
   https://api.telegram.org/bot<TOKEN>/getUpdates
   ```
3. Cari `chat.id` (biasanya diawali `-100...`)
4. Masukkan ke `.env`: `TELEGRAM_CHAT_ID=-100xxxxxxxxxx`

### 3. Set Webhook (via Ngrok)
```bash
# Jalankan ngrok untuk expose port 8000
ngrok http 8000

# Salin URL HTTPS dari ngrok (contoh: https://xxxx.ngrok-free.app)
# Buka browser dan akses:
http://localhost:8000/telegram/set-webhook?url=https://xxxx.ngrok-free.app/telegram/webhook
```

### 4. Verifikasi
```bash
# Cek status webhook
http://localhost:8000/telegram/webhook-info
```

---

## ⚙️ Perintah Artisan Kustom

```bash
# Kirim laporan harian gudang ke Telegram (manual)
php artisan app:send-daily-gudang-report

# Scheduler (otomatis setiap hari 08:00 WIB)
# Pastikan scheduler berjalan:
php artisan schedule:work        # Development (foreground)
# Atau via cron (production):
# * * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

---

## 👤 Default User & Akses

Setelah menjalankan `php artisan db:seed` dan `PutriAdminSeeder`:

| Nama | Username | Password | Role |
|------|----------|----------|------|
| Pak Yudi | `yudi` | `ptmid2026` | Super Admin (Direktur) |
| Mbak Putri | `putri` | `ptmid2026` | Admin |
| Mbak Mey | `mey` | `ptmid2026` | Purchase |
| Pak Hadi | `hadi` | `ptmid2026` | Gudang |

> ⚠️ **Penting**: Segera ubah password default setelah deployment ke production!

---

## 📸 Screenshot

> _Screenshot akan ditambahkan setelah deployment._

<!-- Uncomment dan ganti path saat screenshot sudah tersedia:
### Login
![Login](docs/screenshots/login.png)

### Dashboard
![Dashboard](docs/screenshots/dashboard.png)

### Purchase Order
![PO](docs/screenshots/purchase-order.png)

### Telegram Bot
![Telegram](docs/screenshots/telegram-bot.png)
-->

---

## 📄 Lisensi

Proyek ini bersifat **proprietary** dan dikembangkan secara eksklusif untuk **PT Multyartha Indo Daya**.
Tidak diperkenankan untuk didistribusikan, disalin, atau digunakan oleh pihak lain tanpa izin tertulis.

---

<p align="center">
  Dikembangkan dengan ❤️ untuk <strong>PT Multyartha Indo Daya</strong><br>
  <em>Surya Asri Residence 9A, Cipagalo Bojongsoang, Kabupaten Bandung</em><br>
  <sub>© 2026 — All rights reserved.</sub>
</p>
