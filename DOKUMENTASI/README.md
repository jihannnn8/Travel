# 📚 Dokumentasi Project Travel - Flutter + Laravel

Selamat datang di dokumentasi lengkap project **Travel Booking App**!

---

## 🎯 Tentang Project

Aplikasi Travel Booking yang dibangun dengan:
- **Frontend**: Flutter (Mobile App untuk Android/iOS)
- **Backend**: Laravel (RESTful API)
- **Database**: MySQL
- **Payment**: Midtrans

---

## 📖 Daftar Dokumentasi

### 🚀 Getting Started

1. **[SETUP_DOKUMENTASI.md](SETUP_DOKUMENTASI.md)**
   - Panduan lengkap setup project
   - Instalasi software yang diperlukan
   - Setup backend dan frontend
   - Konfigurasi database
   - Troubleshooting umum
   - **👉 Mulai dari sini jika baru pertama kali setup!**

---

### 🏗️ Arsitektur & Struktur

2. **[ARSITEKTUR_PROJECT.md](ARSITEKTUR_PROJECT.md)**
   - Overview arsitektur project
   - Struktur folder lengkap
   - Arsitektur backend (Laravel)
   - Arsitektur frontend (Flutter)
   - Alur komunikasi frontend-backend
   - State management
   - Authentication flow
   - Data flow
   - **👉 Baca ini untuk memahami struktur project!**

3. **[DATABASE_SCHEMA.md](DATABASE_SCHEMA.md)**
   - Struktur database lengkap
   - Relasi antar tabel
   - ERD (Entity Relationship Diagram)
   - Indexes dan optimasi
   - Sample data
   - Query examples
   - **👉 Baca ini untuk memahami database!**

---

### 👤 User Experience

4. **[USER_FLOW.md](USER_FLOW.md)**
   - Alur penggunaan aplikasi
   - Flow diagram lengkap
   - Detail flow per fitur
   - User journey map
   - Screen flow
   - **👉 Baca ini untuk memahami cara kerja aplikasi dari user perspective!**

---

### 🔌 API Documentation

5. **[README_API.md](README_API.md)**
   - Panduan umum penggunaan API
   - Setup API di Flutter
   - Format request & response
   - Authentication dengan token
   - **👉 Baca ini sebagai pengantar API!**

6. **[AUTH_API.md](AUTH_API.md)**
   - API Login
   - API Register
   - API Logout
   - API Profile (Get & Update)
   - Contoh implementasi di Flutter

7. **[HOME_API.md](HOME_API.md)**
   - API Destinations (List & Detail)
   - API Cities
   - API Sliders
   - API Promos
   - Contoh implementasi di Flutter

8. **[DESTINATION_API.md](DESTINATION_API.md)**
   - API Detail Destinasi
   - Struktur data destinasi
   - Rundown/Itinerary
   - Contoh implementasi di Flutter

9. **[BOOKING_API.md](BOOKING_API.md)**
   - API Create Booking
   - API Get Bookings (History)
   - API Booking Detail
   - API Check Payment Status
   - Integrasi Midtrans
   - Contoh implementasi di Flutter

10. **[PROFILE_API.md](PROFILE_API.md)**
    - API Get Profile
    - API Update Profile
    - Contoh implementasi di Flutter

---

### 🖼️ Sistem & Integrasi

11. **[IMAGE_LOADING_SYSTEM.md](IMAGE_LOADING_SYSTEM.md)**
    - Sistem loading gambar
    - Konfigurasi URL gambar
    - Asset management
    - Troubleshooting image loading

12. **[MIDTRANS_SETUP.md](MIDTRANS_SETUP.md)**
    - Setup Midtrans payment gateway
    - Konfigurasi sandbox & production
    - Setup webhook
    - Testing payment
    - **👉 Baca ini jika ingin setup payment!**

---

### ❓ Help & Support

13. **[FAQ.md](FAQ.md)**
    - Pertanyaan yang sering muncul
    - Troubleshooting lengkap
    - Tips & tricks
    - Best practices
    - **👉 Cek ini dulu jika ada masalah!**

---

## 🗺️ Panduan Membaca Dokumentasi

### Untuk Pemula (Baru Belajar):

1. **Mulai dengan**: [SETUP_DOKUMENTASI.md](SETUP_DOKUMENTASI.md)
2. **Lalu baca**: [ARSITEKTUR_PROJECT.md](ARSITEKTUR_PROJECT.md)
3. **Kemudian**: [USER_FLOW.md](USER_FLOW.md)
4. **Terakhir**: [README_API.md](README_API.md) dan dokumentasi API lainnya

### Untuk Developer (Sudah Familiar):

1. **Quick Start**: [SETUP_DOKUMENTASI.md](SETUP_DOKUMENTASI.md) - bagian setup saja
2. **Arsitektur**: [ARSITEKTUR_PROJECT.md](ARSITEKTUR_PROJECT.md)
3. **Database**: [DATABASE_SCHEMA.md](DATABASE_SCHEMA.md)
4. **API**: Dokumentasi API sesuai kebutuhan

### Untuk Troubleshooting:

1. **Cek dulu**: [FAQ.md](FAQ.md)
2. **Jika belum ketemu**: [SETUP_DOKUMENTASI.md](SETUP_DOKUMENTASI.md) - bagian Troubleshooting
3. **Untuk API issues**: Dokumentasi API terkait

---

## 📋 Quick Reference

### Setup Cepat:

```bash
# Backend
cd backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve

# Frontend
cd ..
flutter pub get
# Edit lib/config/api_config.dart
flutter run
```

### Command Penting:

```bash
# Laravel
php artisan serve              # Run server
php artisan migrate            # Run migrations
php artisan db:seed           # Seed database
php artisan route:list        # List all routes

# Flutter
flutter pub get               # Install dependencies
flutter run                   # Run app
flutter build apk             # Build APK
flutter doctor                # Check setup
```

### File Penting:

- **Backend Config**: `backend/.env`
- **Frontend Config**: `lib/config/api_config.dart`
- **API Routes**: `backend/routes/api.php`
- **Database**: `backend/database/migrations/`

---

## 🎯 Fitur Utama

### ✅ Sudah Diimplementasi:

- [x] Authentication (Login, Register, Logout)
- [x] Home Page (Slider, Cities, Promos, Packages)
- [x] Package Detail
- [x] Booking System
- [x] Payment Gateway (Midtrans)
- [x] Order History
- [x] Profile Management
- [x] Image Loading System

### 🚧 Future Enhancements:

- [ ] Search & Filter
- [ ] Rating & Review System
- [ ] Push Notifications
- [ ] Offline Support
- [ ] Social Sharing
- [ ] Wishlist/Favorites
- [ ] Chat Support
- [ ] Maps Integration

---

## 🛠️ Tech Stack

### Frontend:
- **Flutter** 3.7.2+
- **Dart** 3.7.2+
- **Packages**:
  - `http` - HTTP requests
  - `shared_preferences` - Local storage
  - `intl` - Internationalization
  - `google_fonts` - Custom fonts
  - `url_launcher` - Open URLs
  - `webview_flutter` - WebView untuk Midtrans

### Backend:
- **Laravel** 10.10+
- **PHP** 8.1+
- **MySQL** 8.0+
- **Packages**:
  - `laravel/sanctum` - Authentication
  - `midtrans/midtrans-php` - Payment gateway
  - `guzzlehttp/guzzle` - HTTP client

---

## 📞 Support

Jika ada pertanyaan atau masalah:

1. **Cek FAQ**: [FAQ.md](FAQ.md)
2. **Cek Dokumentasi**: File terkait di folder ini
3. **Cek Error Logs**:
   - Laravel: `backend/storage/logs/laravel.log`
   - Flutter: Console/Logcat

---

## 📝 Update Log

### Dokumentasi yang Tersedia:

- ✅ Setup Documentation
- ✅ Architecture Documentation
- ✅ Database Schema
- ✅ User Flow
- ✅ API Documentation (Lengkap)
- ✅ Image Loading System
- ✅ Midtrans Setup
- ✅ FAQ

---

## 🎓 Learning Path

### Untuk Belajar Flutter + Laravel:

1. **Week 1**: Setup & Basic Understanding
   - Setup project
   - Baca arsitektur
   - Pahami struktur folder

2. **Week 2**: Backend Deep Dive
   - Pelajari Laravel basics
   - Pahami API routes
   - Pelajari database schema

3. **Week 3**: Frontend Deep Dive
   - Pelajari Flutter basics
   - Pahami state management
   - Pelajari API integration

4. **Week 4**: Integration & Testing
   - Test semua fitur
   - Debug issues
   - Optimize code

---

## ✅ Checklist untuk Developer Baru

- [ ] Sudah baca [SETUP_DOKUMENTASI.md](SETUP_DOKUMENTASI.md)
- [ ] Sudah setup project dengan sukses
- [ ] Sudah baca [ARSITEKTUR_PROJECT.md](ARSITEKTUR_PROJECT.md)
- [ ] Sudah pahami struktur database
- [ ] Sudah baca [USER_FLOW.md](USER_FLOW.md)
- [ ] Sudah test semua fitur
- [ ] Sudah baca dokumentasi API yang relevan
- [ ] Siap untuk development! 🚀

---

## 🎉 Selamat Belajar!

Dokumentasi ini dibuat untuk memudahkan pembelajaran project ini. Jika ada yang kurang jelas atau ada pertanyaan, silakan cek [FAQ.md](FAQ.md) atau dokumentasi terkait.

**Happy Coding! 💻✨**

---

*Last Updated: December 2024*

