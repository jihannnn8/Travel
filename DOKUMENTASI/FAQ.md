# ❓ FAQ (Frequently Asked Questions)

Pertanyaan yang sering muncul dan jawabannya untuk project Travel Booking ini.

---

## 📋 Daftar Isi

1. [Setup & Installation](#setup--installation)
2. [Backend (Laravel)](#backend-laravel)
3. [Frontend (Flutter)](#frontend-flutter)
4. [Database](#database)
5. [API & Integration](#api--integration)
6. [Payment (Midtrans)](#payment-midtrans)
7. [Troubleshooting](#troubleshooting)
8. [Development](#development)

---

## 🔧 Setup & Installation

### Q: Software apa saja yang perlu diinstall?

**A:** Lihat dokumentasi [SETUP_DOKUMENTASI.md](SETUP_DOKUMENTASI.md) untuk daftar lengkap. Secara singkat:
- PHP 8.1+
- Composer
- MySQL
- Node.js & NPM
- Flutter SDK
- Android Studio (untuk Android development)

---

### Q: Berapa lama waktu setup pertama kali?

**A:** 
- Install software: 1-2 jam (tergantung koneksi internet)
- Setup project: 15-30 menit
- **Total: 2-3 jam** untuk pertama kali

---

### Q: Apakah bisa di Windows/Mac/Linux?

**A:** Ya, bisa di semua platform. Lihat [SETUP_DOKUMENTASI.md](SETUP_DOKUMENTASI.md) untuk panduan per platform.

---

### Q: Apakah perlu internet untuk development?

**A:** 
- **Pertama kali**: Ya, untuk download dependencies
- **Setelah setup**: Tidak selalu, kecuali untuk:
  - Update packages
  - Download Flutter packages baru
  - Test payment gateway

---

## 🔙 Backend (Laravel)

### Q: Port berapa yang digunakan Laravel?

**A:** Default port **8000**. Bisa diubah dengan:
```bash
php artisan serve --port=8001
```

---

### Q: Bagaimana cara reset database?

**A:** 
```bash
php artisan migrate:fresh --seed
```
**Peringatan:** Ini akan menghapus semua data dan membuat ulang!

---

### Q: Dimana file .env?

**A:** Di folder `backend/.env`. Jika belum ada, copy dari `.env.example`:
```bash
cd backend
copy .env.example .env  # Windows
cp .env.example .env    # Mac/Linux
```

---

### Q: Error "Class not found" di Laravel?

**A:** 
1. Clear cache: `php artisan cache:clear`
2. Rebuild autoload: `composer dump-autoload`
3. Clear config: `php artisan config:clear`

---

### Q: Bagaimana cara menambah endpoint API baru?

**A:** 
1. Buat Controller di `app/Http/Controllers/Api/`
2. Tambahkan route di `routes/api.php`
3. Test dengan Postman atau curl

Contoh:
```php
// routes/api.php
Route::get('/test', [TestController::class, 'index']);

// app/Http/Controllers/Api/TestController.php
public function index() {
    return response()->json(['message' => 'Hello']);
}
```

---

### Q: Bagaimana cara melihat semua routes?

**A:** 
```bash
php artisan route:list
```

---

## 📱 Frontend (Flutter)

### Q: Bagaimana cara connect Flutter ke backend?

**A:** Edit file `lib/config/api_config.dart`:
- **Android Emulator**: `http://10.0.2.2:8000/api`
- **iOS Simulator**: `http://localhost:8000/api`
- **Physical Device**: `http://IP_KOMPUTER:8000/api`

Lihat [SETUP_DOKUMENTASI.md](SETUP_DOKUMENTASI.md) untuk detail.

---

### Q: Error "Connection refused" di Flutter?

**A:** 
1. Pastikan backend Laravel sudah running (`php artisan serve`)
2. Cek API URL di `api_config.dart` sudah benar
3. Untuk physical device: pastikan HP dan komputer dalam WiFi yang sama
4. Cek firewall tidak memblokir port 8000

---

### Q: Bagaimana cara menambah package Flutter baru?

**A:** 
1. Edit `pubspec.yaml`, tambahkan di `dependencies:`
2. Jalankan: `flutter pub get`
3. Import di file yang perlu

---

### Q: Error "Package not found" di Flutter?

**A:** 
1. Pastikan sudah run `flutter pub get`
2. Restart IDE/editor
3. Run `flutter clean` lalu `flutter pub get` lagi

---

### Q: Bagaimana cara build APK?

**A:** 
```bash
flutter build apk --release
```
File APK ada di `build/app/outputs/flutter-apk/app-release.apk`

---

### Q: Bagaimana cara build untuk iOS?

**A:** 
1. Hanya bisa di macOS dengan Xcode
2. `flutter build ios --release`
3. Atau buka di Xcode untuk build

---

## 🗄️ Database

### Q: Bagaimana cara backup database?

**A:** 
```bash
mysqldump -u root -p travel_db > backup.sql
```

---

### Q: Bagaimana cara restore database?

**A:** 
```bash
mysql -u root -p travel_db < backup.sql
```

---

### Q: Bagaimana cara melihat struktur tabel?

**A:** 
1. Via MySQL Workbench: Connect → Browse tables
2. Via command line:
```sql
DESCRIBE table_name;
SHOW CREATE TABLE table_name;
```

---

### Q: Bagaimana cara menambah kolom baru di tabel?

**A:** 
1. Buat migration:
```bash
php artisan make:migration add_column_to_table
```
2. Edit migration file
3. Run: `php artisan migrate`

---

### Q: Error "Table already exists" saat migrate?

**A:** 
1. Rollback: `php artisan migrate:rollback`
2. Atau fresh: `php artisan migrate:fresh` (hapus semua data!)

---

## 🔌 API & Integration

### Q: Bagaimana cara test API tanpa Flutter app?

**A:** Gunakan:
- **Postman**: Import collection
- **curl**: Command line
- **Browser**: Untuk GET requests

Contoh curl:
```bash
curl http://localhost:8000/api/destinations
```

---

### Q: Bagaimana cara dapat token untuk test API?

**A:** 
1. Register atau Login via API
2. Ambil token dari response
3. Gunakan di header: `Authorization: Bearer {token}`

Contoh:
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"user@example.com","password":"password"}'
```

---

### Q: Error 401 Unauthorized?

**A:** 
1. Pastikan token masih valid
2. Pastikan header `Authorization: Bearer {token}` sudah benar
3. Token mungkin sudah expire, login ulang

---

### Q: Error 422 Validation Error?

**A:** 
1. Cek field yang required sudah diisi
2. Cek format data sudah benar (email, date, dll)
3. Lihat response untuk detail error per field

---

### Q: Bagaimana format tanggal untuk API?

**A:** Format: `YYYY-MM-DD` (contoh: `2024-12-01`)

---

## 💳 Payment (Midtrans)

### Q: Apakah perlu setup Midtrans untuk development?

**A:** Tidak wajib, tapi disarankan untuk test flow pembayaran. Lihat [MIDTRANS_SETUP.md](MIDTRANS_SETUP.md).

---

### Q: Bagaimana cara test payment tanpa kartu kredit?

**A:** Gunakan **Sandbox Mode** dengan kartu test:
- Card: `4811 1111 1111 1114`
- CVV: `123`
- Expiry: Bulan/tahun masa depan
- OTP: `112233`

---

### Q: Error saat payment di Midtrans?

**A:** 
1. Cek `MIDTRANS_SERVER_KEY` dan `MIDTRANS_CLIENT_KEY` di `.env`
2. Pastikan `MIDTRANS_IS_PRODUCTION=false` untuk sandbox
3. Cek log di `storage/logs/laravel.log`

---

### Q: Bagaimana cara setup webhook Midtrans?

**A:** Lihat [MIDTRANS_SETUP.md](MIDTRANS_SETUP.md) bagian "Setup Webhook URL".

Untuk local testing, gunakan **ngrok**:
```bash
ngrok http 8000
```
Lalu set webhook URL: `https://your-ngrok-url.ngrok.io/api/payment/notification`

---

## 🐛 Troubleshooting

### Q: Aplikasi Flutter tidak bisa connect ke backend?

**A:** Checklist:
- [ ] Backend Laravel sudah running?
- [ ] API URL di `api_config.dart` sudah benar?
- [ ] Untuk physical device: IP komputer benar dan dalam WiFi yang sama?
- [ ] Firewall tidak memblokir port 8000?
- [ ] Test API di browser: `http://localhost:8000/api/destinations`

---

### Q: Error "Class 'X' not found" di Laravel?

**A:** 
```bash
composer dump-autoload
php artisan config:clear
php artisan cache:clear
```

---

### Q: Error "SQLSTATE[HY000] [2002]" (Connection refused)?

**A:** 
1. Pastikan MySQL sudah running
2. Cek `DB_HOST` di `.env` (default: `127.0.0.1`)
3. Cek `DB_PORT` (default: `3306`)
4. Test koneksi: `mysql -u root -p`

---

### Q: Error "Permission denied" di storage?

**A:** 
```bash
# Linux/Mac
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Windows biasanya tidak ada masalah
```

---

### Q: Flutter app crash saat load image?

**A:** 
1. Cek URL gambar sudah benar
2. Pastikan backend bisa serve images
3. Cek `ApiConfig.fixImageUrl()` sudah dipanggil
4. Lihat [IMAGE_LOADING_SYSTEM.md](IMAGE_LOADING_SYSTEM.md)

---

### Q: Token selalu invalid meski baru login?

**A:** 
1. Cek token tersimpan dengan benar di SharedPreferences
2. Cek format header: `Authorization: Bearer {token}` (ada spasi setelah Bearer)
3. Cek token tidak terpotong saat save
4. Coba logout dan login ulang

---

## 💻 Development

### Q: Bagaimana cara contribute ke project ini?

**A:** 
1. Fork repository
2. Buat branch baru: `git checkout -b feature/nama-fitur`
3. Commit changes: `git commit -m "Add feature"`
4. Push: `git push origin feature/nama-fitur`
5. Buat Pull Request

---

### Q: Bagaimana struktur code yang baik?

**A:** Lihat [ARSITEKTUR_PROJECT.md](ARSITEKTUR_PROJECT.md) untuk detail.

Prinsip:
- Separation of concerns
- DRY (Don't Repeat Yourself)
- Clean code
- Comment untuk logic kompleks

---

### Q: Bagaimana cara debug di Flutter?

**A:** 
1. Gunakan `print()` atau `debugPrint()`
2. Gunakan breakpoint di VS Code/Android Studio
3. Cek console/logcat untuk error
4. Gunakan `flutter logs` untuk melihat logs

---

### Q: Bagaimana cara debug di Laravel?

**A:** 
1. Cek `storage/logs/laravel.log`
2. Gunakan `dd()` atau `dump()` untuk debug
3. Enable debug mode di `.env`: `APP_DEBUG=true`
4. Gunakan Laravel Debugbar (install package)

---

### Q: Bagaimana cara menambah fitur baru?

**A:** 
1. **Backend**: 
   - Buat migration (jika perlu tabel baru)
   - Buat Model
   - Buat Controller
   - Tambahkan routes

2. **Frontend**:
   - Buat Model (jika perlu)
   - Buat/update Service
   - Buat/update Page/Widget
   - Update navigation

---

### Q: Bagaimana cara deploy ke production?

**A:** 
1. **Backend**:
   - Setup server (VPS/Cloud)
   - Install PHP, MySQL, Composer
   - Clone project
   - Setup `.env` production
   - Run migrations
   - Setup web server (Nginx/Apache)

2. **Frontend**:
   - Build APK/IPA
   - Upload ke Play Store/App Store
   - Update API URL ke production

---

### Q: Apakah ada testing?

**A:** 
- **Laravel**: Ada PHPUnit, tapi belum banyak test
- **Flutter**: Ada widget test, tapi belum banyak

Untuk menambah test:
```bash
# Laravel
php artisan test

# Flutter
flutter test
```

---

## 📚 Dokumentasi Lainnya

### Q: Dokumentasi apa saja yang tersedia?

**A:** 
- [SETUP_DOKUMENTASI.md](SETUP_DOKUMENTASI.md) - Setup project
- [ARSITEKTUR_PROJECT.md](ARSITEKTUR_PROJECT.md) - Arsitektur & struktur
- [DATABASE_SCHEMA.md](DATABASE_SCHEMA.md) - Struktur database
- [USER_FLOW.md](USER_FLOW.md) - Alur penggunaan aplikasi
- [README_API.md](README_API.md) - Panduan umum API
- [AUTH_API.md](AUTH_API.md) - API Autentikasi
- [HOME_API.md](HOME_API.md) - API Home
- [DESTINATION_API.md](DESTINATION_API.md) - API Destinasi
- [BOOKING_API.md](BOOKING_API.md) - API Booking
- [PROFILE_API.md](PROFILE_API.md) - API Profile
- [IMAGE_LOADING_SYSTEM.md](IMAGE_LOADING_SYSTEM.md) - Sistem loading gambar
- [MIDTRANS_SETUP.md](MIDTRANS_SETUP.md) - Setup Midtrans

---

## 🆘 Masih Ada Pertanyaan?

Jika pertanyaan Anda belum terjawab:

1. **Cek dokumentasi** yang relevan
2. **Cek error message** dengan detail
3. **Cek log files**:
   - Laravel: `backend/storage/logs/laravel.log`
   - Flutter: Console/Logcat
4. **Search di Google** dengan error message
5. **Buat issue** di repository (jika open source)

---

**Happy Coding! 🚀**

