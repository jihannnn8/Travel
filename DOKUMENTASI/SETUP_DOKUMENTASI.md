# 📚 Dokumentasi Setup Project Travel - Flutter + Laravel

Dokumentasi lengkap untuk setup dan menjalankan aplikasi Travel Booking yang menggunakan **Flutter** (mobile app) dan **Laravel** (backend API).

---

## 📋 Daftar Isi

1. [Tentang Project](#tentang-project)
2. [Persyaratan Sistem](#persyaratan-sistem)
3. [Instalasi Software yang Diperlukan](#instalasi-software-yang-diperlukan)
4. [Setup Backend (Laravel)](#setup-backend-laravel)
5. [Setup Frontend (Flutter)](#setup-frontend-flutter)
6. [Menjalankan Aplikasi](#menjalankan-aplikasi)
7. [Konfigurasi API URL](#konfigurasi-api-url)
8. [Troubleshooting](#troubleshooting)

---

## 🎯 Tentang Project

Project ini adalah aplikasi **Travel Booking** yang terdiri dari:

- **Frontend**: Aplikasi mobile Flutter untuk Android/iOS
- **Backend**: API Laravel untuk mengelola data, autentikasi, dan pembayaran
- **Database**: MySQL untuk menyimpan data
- **Payment Gateway**: Midtrans untuk pembayaran

### Fitur Utama:
- ✅ Login & Register
- ✅ Browse destinasi wisata
- ✅ Booking paket wisata
- ✅ History pemesanan
- ✅ Profile management
- ✅ Payment gateway (Midtrans)

---

## 💻 Persyaratan Sistem

Sebelum memulai, pastikan komputer Anda memenuhi persyaratan berikut:

### Minimum Requirements:
- **OS**: Windows 10/11, macOS, atau Linux
- **RAM**: Minimal 4GB (disarankan 8GB)
- **Storage**: Minimal 10GB ruang kosong
- **Internet**: Koneksi internet untuk download dependencies

---

## 🔧 Instalasi Software yang Diperlukan

### 1. Install PHP (Versi 8.1 atau lebih tinggi)

#### Windows:
1. Download PHP dari: https://windows.php.net/download/
2. Pilih versi **PHP 8.1** atau lebih tinggi (Thread Safe)
3. Extract ke folder `C:\php`
4. Tambahkan `C:\php` ke **PATH Environment Variable**:
   - Buka **System Properties** > **Environment Variables**
   - Edit **Path** > Tambahkan `C:\php`
5. Verifikasi dengan buka CMD/PowerShell:
   ```bash
   php -v
   ```

#### macOS:
```bash
# Menggunakan Homebrew
brew install php@8.1
brew link php@8.1
```

#### Linux (Ubuntu/Debian):
```bash
sudo apt update
sudo apt install php8.1 php8.1-cli php8.1-mysql php8.1-mbstring php8.1-xml php8.1-curl php8.1-zip
```

### 2. Install Composer (PHP Package Manager)

1. Download dari: https://getcomposer.org/download/
2. **Windows**: Download `Composer-Setup.exe` dan install
3. **macOS/Linux**: Jalankan di terminal:
   ```bash
   curl -sS https://getcomposer.org/installer | php
   sudo mv composer.phar /usr/local/bin/composer
   ```
4. Verifikasi:
   ```bash
   composer --version
   ```

### 3. Install MySQL

#### Windows:
1. Download MySQL Installer dari: https://dev.mysql.com/downloads/installer/
2. Install MySQL Server dan MySQL Workbench
3. Set password untuk root user (ingat password ini!)

#### macOS:
```bash
brew install mysql
brew services start mysql
```

#### Linux:
```bash
sudo apt install mysql-server
sudo mysql_secure_installation
```

### 4. Install Node.js & NPM

1. Download dari: https://nodejs.org/
2. Install versi **LTS** (Long Term Support)
3. Verifikasi:
   ```bash
   node -v
   npm -v
   ```

### 5. Install Flutter SDK

1. Download Flutter SDK dari: https://flutter.dev/docs/get-started/install
2. Extract ke folder (contoh: `C:\flutter` atau `~/flutter`)
3. Tambahkan ke **PATH Environment Variable**
4. Verifikasi:
   ```bash
   flutter --version
   flutter doctor
   ```

### 6. Install Android Studio (untuk Android Development)

1. Download dari: https://developer.android.com/studio
2. Install Android Studio
3. Buka Android Studio > **More Actions** > **SDK Manager**
4. Install:
   - Android SDK
   - Android SDK Platform-Tools
   - Android Emulator
5. Set **ANDROID_HOME** environment variable:
   - Windows: `C:\Users\YourName\AppData\Local\Android\Sdk`
   - macOS/Linux: `~/Library/Android/sdk` atau `~/Android/Sdk`

### 7. Install VS Code (Editor - Opsional tapi Disarankan)

1. Download dari: https://code.visualstudio.com/
2. Install extension:
   - **Flutter** (Dart & Flutter)
   - **PHP Intelephense**
   - **Laravel Extension Pack**

---

## 🚀 Setup Backend (Laravel)

### Langkah 1: Masuk ke Folder Backend

```bash
cd backend
```

### Langkah 2: Install Dependencies PHP

```bash
composer install
```

**Catatan**: Jika ada error, pastikan PHP sudah terinstall dengan benar.

### Langkah 3: Setup Environment File

1. Copy file `.env.example` menjadi `.env`:
   ```bash
   # Windows
   copy .env.example .env
   
   # macOS/Linux
   cp .env.example .env
   ```

2. Edit file `.env` dengan text editor (VS Code, Notepad++, dll)

3. Konfigurasi database di `.env`:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=travel_db
   DB_USERNAME=root
   DB_PASSWORD=password_anda_disini
   ```
   
   **Ganti**:
   - `DB_DATABASE`: Nama database (bisa diganti sesuai keinginan)
   - `DB_PASSWORD`: Password MySQL Anda

4. Konfigurasi aplikasi:
   ```env
   APP_NAME="Travel App"
   APP_ENV=local
   APP_KEY=
   APP_DEBUG=true
   APP_URL=http://localhost:8000
   ```

### Langkah 4: Generate Application Key

```bash
php artisan key:generate
```

### Langkah 5: Buat Database MySQL

1. Buka **MySQL Workbench** atau **phpMyAdmin**
2. Buat database baru:
   ```sql
   CREATE DATABASE travel_db;
   ```
   
   Atau via command line:
   ```bash
   mysql -u root -p
   CREATE DATABASE travel_db;
   exit;
   ```

### Langkah 6: Jalankan Migration

```bash
php artisan migrate
```

Ini akan membuat semua tabel di database.

### Langkah 7: Jalankan Seeder (Data Awal)

```bash
php artisan db:seed
```

Ini akan mengisi database dengan data contoh (destinasi, user, dll).

### Langkah 8: Setup Storage Link (untuk gambar)

```bash
php artisan storage:link
```

### Langkah 9: Jalankan Server Laravel

```bash
php artisan serve
```

Server akan berjalan di: **http://localhost:8000**

**Catatan**: Biarkan terminal ini terbuka, jangan ditutup!

---

## 📱 Setup Frontend (Flutter)

### Langkah 1: Masuk ke Root Folder Project

```bash
cd ..  # Keluar dari folder backend
# Atau langsung ke root folder Travel
```

### Langkah 2: Install Dependencies Flutter

```bash
flutter pub get
```

### Langkah 3: Konfigurasi API URL

1. Buka file: `lib/config/api_config.dart`
2. Sesuaikan `baseUrl` sesuai dengan device yang digunakan:

   **Untuk Android Emulator:**
   ```dart
   static const String baseUrl = 'http://10.0.2.2:8000/api';
   ```

   **Untuk iOS Simulator:**
   ```dart
   static const String baseUrl = 'http://localhost:8000/api';
   ```

   **Untuk Physical Device (HP/Tablet):**
   ```dart
   static const String baseUrl = 'http://192.168.1.xxx:8000/api';
   ```
   
   **Cara mendapatkan IP komputer:**
   - Windows: Buka CMD, ketik `ipconfig`, cari **IPv4 Address**
   - macOS/Linux: Buka Terminal, ketik `ifconfig`, cari **inet**

### Langkah 4: Verifikasi Setup

```bash
flutter doctor
```

Pastikan semua checklist hijau (✓).

---

## 🎮 Menjalankan Aplikasi

### Menjalankan Backend (Laravel)

1. Pastikan MySQL sudah running
2. Masuk ke folder `backend`:
   ```bash
   cd backend
   ```
3. Jalankan server:
   ```bash
   php artisan serve
   ```
4. Server akan berjalan di **http://localhost:8000**
5. Test API dengan buka browser: http://localhost:8000/api/destinations

### Menjalankan Frontend (Flutter)

1. Pastikan backend Laravel sudah running
2. Masuk ke root folder project
3. List device yang tersedia:
   ```bash
   flutter devices
   ```
4. Jalankan aplikasi:
   ```bash
   # Untuk Android
   flutter run
   
   # Atau pilih device tertentu
   flutter run -d <device-id>
   ```

### Menjalankan di Emulator/Simulator

#### Android Emulator:
1. Buka **Android Studio**
2. Klik **Device Manager** > **Create Device**
3. Pilih device (contoh: Pixel 5)
4. Pilih system image (contoh: Android 11)
5. Klik **Finish**
6. Start emulator
7. Jalankan `flutter run`

#### iOS Simulator (macOS only):
1. Buka **Xcode**
2. **Xcode** > **Open Developer Tool** > **Simulator**
3. Pilih device (contoh: iPhone 14)
4. Jalankan `flutter run`

---

## 🔗 Konfigurasi API URL

### Untuk Android Emulator

Edit `lib/config/api_config.dart`:
```dart
static const String baseUrl = 'http://10.0.2.2:8000/api';
```

### Untuk iOS Simulator

Edit `lib/config/api_config.dart`:
```dart
static const String baseUrl = 'http://localhost:8000/api';
```

### Untuk Physical Device (HP/Tablet)

1. Pastikan HP dan komputer dalam **WiFi yang sama**
2. Cari IP komputer:
   - Windows: `ipconfig` di CMD
   - macOS/Linux: `ifconfig` di Terminal
3. Edit `lib/config/api_config.dart`:
   ```dart
   static const String baseUrl = 'http://192.168.1.100:8000/api';
   ```
   (Ganti `192.168.1.100` dengan IP komputer Anda)
4. Pastikan firewall tidak memblokir port 8000

---

## 🛠️ Troubleshooting

### Error: "composer: command not found"

**Solusi**: 
- Pastikan Composer sudah terinstall
- Tambahkan Composer ke PATH environment variable
- Restart terminal/CMD

### Error: "php artisan: command not found"

**Solusi**:
- Pastikan PHP sudah terinstall
- Pastikan berada di folder `backend`
- Cek dengan `php -v`

### Error: "Access denied for user 'root'@'localhost'"

**Solusi**:
- Cek username dan password MySQL di file `.env`
- Pastikan MySQL sudah running
- Coba reset password MySQL:
  ```sql
  ALTER USER 'root'@'localhost' IDENTIFIED BY 'password_baru';
  ```

### Error: "Database connection failed"

**Solusi**:
1. Pastikan MySQL sudah running
2. Cek konfigurasi di `.env`:
   - `DB_HOST=127.0.0.1`
   - `DB_PORT=3306`
   - `DB_DATABASE=travel_db` (pastikan database sudah dibuat)
   - `DB_USERNAME=root`
   - `DB_PASSWORD=password_anda`
3. Test koneksi:
   ```bash
   mysql -u root -p
   ```

### Error: "Flutter: No devices found"

**Solusi**:
1. Untuk Android: Pastikan emulator sudah running atau HP terhubung via USB dengan **USB Debugging** enabled
2. Untuk iOS: Hanya bisa di macOS, pastikan Xcode sudah terinstall
3. Cek dengan: `flutter devices`

### Error: "Connection refused" di Flutter App

**Solusi**:
1. Pastikan backend Laravel sudah running (`php artisan serve`)
2. Cek API URL di `lib/config/api_config.dart` sesuai dengan device
3. Untuk physical device: Pastikan IP benar dan dalam WiFi yang sama
4. Test API di browser: http://localhost:8000/api/destinations

### Error: "Port 8000 already in use"

**Solusi**:
1. Tutup aplikasi yang menggunakan port 8000
2. Atau gunakan port lain:
   ```bash
   php artisan serve --port=8001
   ```
3. Update `baseUrl` di Flutter sesuai port baru

### Error: "Migration failed"

**Solusi**:
1. Pastikan database sudah dibuat
2. Pastikan kredensial database di `.env` benar
3. Coba reset database:
   ```bash
   php artisan migrate:fresh
   php artisan db:seed
   ```

### Error: "Gradle build failed" (Android)

**Solusi**:
1. Update Gradle:
   ```bash
   cd android
   ./gradlew wrapper --gradle-version=8.0
   ```
2. Clean build:
   ```bash
   flutter clean
   flutter pub get
   flutter run
   ```

---

## 📝 Checklist Setup

Gunakan checklist ini untuk memastikan semua sudah terinstall:

- [ ] PHP 8.1+ terinstall (`php -v`)
- [ ] Composer terinstall (`composer --version`)
- [ ] MySQL terinstall dan running
- [ ] Node.js & NPM terinstall (`node -v`, `npm -v`)
- [ ] Flutter SDK terinstall (`flutter --version`)
- [ ] Android Studio terinstall (untuk Android)
- [ ] Database `travel_db` sudah dibuat
- [ ] File `.env` sudah dikonfigurasi
- [ ] Migration sudah dijalankan (`php artisan migrate`)
- [ ] Seeder sudah dijalankan (`php artisan db:seed`)
- [ ] Backend Laravel bisa diakses (http://localhost:8000)
- [ ] API URL di Flutter sudah dikonfigurasi
- [ ] Flutter app bisa connect ke backend

---

## 🔐 Setup Midtrans (Payment Gateway) - Opsional

Jika ingin menggunakan fitur pembayaran, ikuti langkah di file: `backend/MIDTRANS_SETUP.md`

---

## 📚 Dokumentasi API

Dokumentasi lengkap API tersedia di:
- `AUTH_API.md` - API Autentikasi (Login, Register, Logout)
- `DESTINATION_API.md` - API Destinasi Wisata
- `BOOKING_API.md` - API Booking & Pemesanan
- `PROFILE_API.md` - API Profile User

---

## 🆘 Butuh Bantuan?

Jika masih ada masalah:

1. **Cek dokumentasi resmi**:
   - Flutter: https://flutter.dev/docs
   - Laravel: https://laravel.com/docs

2. **Cek error message** dengan detail di terminal/console

3. **Pastikan semua software sudah terinstall** dengan benar

4. **Restart** terminal/CMD setelah install software baru

---

## ✅ Selesai!

Jika semua checklist sudah terpenuhi, aplikasi Anda siap digunakan! 🎉

**Selamat coding!** 💻✨

