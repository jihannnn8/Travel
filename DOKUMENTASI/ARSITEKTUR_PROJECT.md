# 🏗️ Arsitektur Project Travel - Flutter + Laravel

Dokumentasi lengkap tentang struktur dan arsitektur project Travel Booking ini.

---

## 📋 Daftar Isi

1. [Overview Arsitektur](#overview-arsitektur)
2. [Struktur Folder](#struktur-folder)
3. [Arsitektur Backend (Laravel)](#arsitektur-backend-laravel)
4. [Arsitektur Frontend (Flutter)](#arsitektur-frontend-flutter)
5. [Alur Komunikasi Frontend-Backend](#alur-komunikasi-frontend-backend)
6. [State Management](#state-management)
7. [Authentication Flow](#authentication-flow)
8. [Data Flow](#data-flow)

---

## 🎯 Overview Arsitektur

Project ini menggunakan **Client-Server Architecture** dengan:

```
┌─────────────────┐         HTTP/JSON API         ┌─────────────────┐
│                 │ ◄──────────────────────────► │                 │
│  Flutter App    │                               │  Laravel API    │
│  (Frontend)     │                               │  (Backend)      │
│                 │                               │                 │
└─────────────────┘                               └─────────────────┘
                                                          │
                                                          │
                                                          ▼
                                                  ┌─────────────────┐
                                                  │   MySQL DB       │
                                                  └─────────────────┘
```

### Komponen Utama:

1. **Frontend (Flutter)**: Aplikasi mobile untuk Android/iOS
2. **Backend (Laravel)**: RESTful API server
3. **Database (MySQL)**: Penyimpanan data
4. **Payment Gateway (Midtrans)**: Integrasi pembayaran

---

## 📁 Struktur Folder

### Root Project Structure

```
Travel/
├── lib/                          # Flutter source code
│   ├── config/                   # Konfigurasi
│   ├── models/                   # Data models
│   ├── services/                 # API services
│   ├── pages/                    # UI pages/screens
│   ├── widgets/                  # Reusable widgets
│   └── main.dart                 # Entry point
│
├── backend/                      # Laravel backend
│   ├── app/
│   │   ├── Http/
│   │   │   └── Controllers/
│   │   │       └── Api/          # API Controllers
│   │   ├── Models/               # Eloquent Models
│   │   └── Services/             # Business logic
│   ├── database/
│   │   ├── migrations/           # Database migrations
│   │   └── seeders/              # Database seeders
│   ├── routes/
│   │   └── api.php               # API routes
│   └── config/                   # Configuration files
│
├── assets/                        # Flutter assets (images)
├── android/                      # Android specific files
├── ios/                          # iOS specific files
└── DOKUMENTASI/                  # Dokumentasi project
```

---

## 🔧 Arsitektur Backend (Laravel)

### MVC Pattern

Laravel menggunakan **MVC (Model-View-Controller)** pattern:

```
Request → Route → Controller → Model → Database
                ↓
            Response (JSON)
```

### Struktur Backend:

```
backend/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       └── Api/
│   │           ├── AuthController.php        # Login, Register, Logout
│   │           ├── DestinationController.php # Destinasi wisata
│   │           ├── BookingController.php     # Booking & Payment
│   │           ├── CitiesController.php      # Data kota
│   │           ├── SlidersController.php     # Slider images
│   │           └── PromosController.php      # Promo images
│   │
│   ├── Models/
│   │   ├── User.php              # User model
│   │   ├── Destination.php       # Destination model
│   │   ├── Booking.php           # Booking model
│   │   ├── City.php              # City model
│   │   ├── Slider.php            # Slider model
│   │   └── Promo.php             # Promo model
│   │
│   └── Services/
│       └── MidtransService.php   # Midtrans payment service
│
├── routes/
│   └── api.php                   # Semua API routes
│
└── database/
    ├── migrations/               # Database schema
    └── seeders/                  # Sample data
```

### API Routes Structure:

```php
// Public Routes (No Auth)
POST   /api/register
POST   /api/login
GET    /api/destinations
GET    /api/destinations/{id}
GET    /api/cities
GET    /api/sliders
GET    /api/promos
POST   /api/payment/notification  # Midtrans webhook

// Protected Routes (Auth Required)
POST   /api/logout
GET    /api/me
GET    /api/profile
PUT    /api/profile
GET    /api/bookings
POST   /api/bookings
GET    /api/bookings/{id}
GET    /api/bookings/{id}/status
```

### Authentication:

- **Laravel Sanctum**: Token-based authentication
- Token disimpan di `personal_access_tokens` table
- Token dikirim via `Authorization: Bearer {token}` header

---

## 📱 Arsitektur Frontend (Flutter)

### Layer Architecture:

```
┌─────────────────────────────────────┐
│         UI Layer (Pages)            │
│  (Landing, Login, Home, Profile)   │
└─────────────────────────────────────┘
              │
              ▼
┌─────────────────────────────────────┐
│      Widget Layer (Reusable)        │
│  (RatingWidget, PromoCarousel)      │
└─────────────────────────────────────┘
              │
              ▼
┌─────────────────────────────────────┐
│      Service Layer (API Calls)      │
│  (AuthService, BookingService)      │
└─────────────────────────────────────┘
              │
              ▼
┌─────────────────────────────────────┐
│      Model Layer (Data)              │
│  (User, Booking, Destination)        │
└─────────────────────────────────────┘
```

### Struktur Frontend:

```
lib/
├── config/
│   └── api_config.dart              # API base URL & endpoints
│
├── models/
│   ├── user.dart                    # User model
│   ├── booking.dart                 # Booking model
│   ├── tour_package.dart           # Destination model
│   └── city.dart                    # City model
│
├── services/
│   ├── api_service.dart             # Base HTTP service
│   ├── auth_service.dart            # Authentication service
│   ├── booking_service.dart         # Booking service
│   ├── data_service.dart            # Home data service
│   └── destination_service.dart     # Destination service
│
├── pages/
│   ├── landing_page.dart            # Landing/Intro page
│   ├── login_page.dart              # Login screen
│   ├── register_page.dart           # Register screen
│   ├── home_page.dart               # Home dengan bottom nav
│   ├── package_detail_page.dart     # Detail paket wisata
│   ├── order_history_page.dart      # History booking
│   ├── profile_page.dart            # Profile user
│   ├── payment_page.dart            # Payment page
│   └── midtrans_snap_page.dart      # Midtrans payment
│
├── widgets/
│   ├── rating_widget.dart           # Star rating widget
│   └── promo_carousel.dart          # Promo carousel
│
└── main.dart                         # App entry point
```

### State Management:

Project ini menggunakan **StatefulWidget** untuk state management lokal (tidak menggunakan provider/bloc).

**Keuntungan:**
- Simple dan mudah dipahami
- Cocok untuk project kecil-menengah
- Tidak perlu dependency tambahan

**Kekurangan:**
- Bisa kompleks untuk state yang besar
- Tidak ada state sharing antar widget

**Contoh State Management:**

```dart
class HomePage extends StatefulWidget {
  @override
  _HomePageState createState() => _HomePageState();
}

class _HomePageState extends State<HomePage> {
  List<Destination> _destinations = [];
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _loadData();
  }

  Future<void> _loadData() async {
    // Load data dari API
    setState(() {
      _isLoading = false;
    });
  }
}
```

---

## 🔄 Alur Komunikasi Frontend-Backend

### 1. Request Flow:

```
User Action (Flutter)
    ↓
Service Layer (auth_service.dart)
    ↓
API Service (api_service.dart)
    ↓
HTTP Request (http package)
    ↓
Laravel Route (api.php)
    ↓
Controller (AuthController.php)
    ↓
Model (User.php)
    ↓
Database (MySQL)
    ↓
Response (JSON)
    ↓
Flutter App
```

### 2. Contoh: Login Flow

```
1. User input email & password
   ↓
2. login_page.dart → AuthService.login()
   ↓
3. AuthService → ApiService.post('/login', body)
   ↓
4. Laravel: POST /api/login
   ↓
5. AuthController@login
   ↓
6. Validasi credentials
   ↓
7. Generate token (Sanctum)
   ↓
8. Return JSON: { token, user }
   ↓
9. AuthService.saveToken(token)
   ↓
10. Navigate ke HomePage
```

### 3. Contoh: Get Destinations Flow

```
1. HomePage initState()
   ↓
2. DataService.getDestinations()
   ↓
3. ApiService.get('/destinations')
   ↓
4. Laravel: GET /api/destinations
   ↓
5. DestinationController@index
   ↓
6. Destination::all()
   ↓
7. Return JSON: { destinations: [...] }
   ↓
8. Parse ke List<Destination>
   ↓
9. setState() → Update UI
```

---

## 🔐 Authentication Flow

### Login Process:

```
┌─────────────┐
│  User Input │
│  Email/Pass │
└──────┬──────┘
       │
       ▼
┌─────────────────┐
│  AuthService    │
│  .login()       │
└──────┬──────────┘
       │
       ▼
┌─────────────────┐
│  POST /login    │
│  (No Auth)      │
└──────┬──────────┘
       │
       ▼
┌─────────────────┐
│  Laravel        │
│  AuthController │
└──────┬──────────┘
       │
       ▼
┌─────────────────┐
│  Validate &     │
│  Generate Token │
└──────┬──────────┘
       │
       ▼
┌─────────────────┐
│  Save Token     │
│  (SharedPrefs)  │
└──────┬──────────┘
       │
       ▼
┌─────────────────┐
│  Navigate to    │
│  HomePage       │
└─────────────────┘
```

### Protected Request Flow:

```
┌─────────────────┐
│  Flutter App    │
│  (Need Auth)    │
└──────┬──────────┘
       │
       ▼
┌─────────────────┐
│  Get Token      │
│  (SharedPrefs)  │
└──────┬──────────┘
       │
       ▼
┌─────────────────┐
│  Add Header:    │
│  Authorization: │
│  Bearer {token} │
└──────┬──────────┘
       │
       ▼
┌─────────────────┐
│  Laravel        │
│  auth:sanctum   │
│  Middleware     │
└──────┬──────────┘
       │
       ▼
┌─────────────────┐
│  Validate Token │
└──────┬──────────┘
       │
       ▼
┌─────────────────┐
│  Process        │
│  Request        │
└─────────────────┘
```

---

## 📊 Data Flow

### 1. Reading Data (GET):

```
UI Widget
    ↓
Service Method
    ↓
API Call (GET)
    ↓
Parse JSON → Model
    ↓
setState() → Update UI
```

### 2. Writing Data (POST/PUT):

```
User Input
    ↓
Form Validation
    ↓
Service Method
    ↓
API Call (POST/PUT)
    ↓
Success Response
    ↓
Update Local State
    ↓
Refresh UI
```

### 3. Image Loading Flow:

```
Backend: Image URL
    ↓
ApiConfig.fixImageUrl()
    ↓
Full URL: http://localhost:8000/api/asset/image.jpg
    ↓
Flutter: Image.network()
    ↓
Display Image
```

---

## 🎨 Design Pattern yang Digunakan

### 1. **Service Pattern**
- Semua API calls di-encapsulate dalam Service classes
- Memudahkan maintenance dan testing

### 2. **Repository Pattern** (Implicit)
- Models berfungsi sebagai data containers
- Services sebagai repository layer

### 3. **Singleton Pattern**
- ApiService menggunakan static methods
- SharedPreferences sebagai singleton

### 4. **Factory Pattern**
- Model.fromJson() untuk create object dari JSON

---

## 🔄 Data Persistence

### Local Storage (Flutter):

- **SharedPreferences**: Menyimpan token dan user data
- **In-Memory**: State di StatefulWidget

### Server Storage (Laravel):

- **MySQL Database**: Semua data persistent
- **File Storage**: Images di `public/Asset_Travelo/` dan `storage/`

---

## 🚀 Best Practices yang Diterapkan

1. **Separation of Concerns**
   - UI terpisah dari business logic
   - Services terpisah dari UI

2. **Error Handling**
   - Try-catch di semua API calls
   - User-friendly error messages

3. **Code Reusability**
   - Widgets untuk komponen yang dipakai ulang
   - Services untuk logic yang sama

4. **Security**
   - Token-based authentication
   - Input validation di backend
   - SQL injection protection (Eloquent ORM)

---

## 📝 Kesimpulan

Arsitektur project ini dirancang untuk:
- ✅ Mudah dipahami dan dipelajari
- ✅ Scalable untuk fitur tambahan
- ✅ Maintainable dengan struktur yang jelas
- ✅ Secure dengan authentication yang proper

Untuk detail implementasi, lihat dokumentasi API dan kode source masing-masing komponen.

---

**Selamat belajar! 🎓**

