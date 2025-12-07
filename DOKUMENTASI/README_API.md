# Panduan API Travel Mobile App

Selamat datang di dokumentasi API untuk aplikasi Travel Mobile! Dokumen ini akan membantu Anda memahami cara menggunakan API backend untuk aplikasi mobile.

## 📋 Daftar Isi

1. [Setup Awal](#setup-awal)
2. [Pengenalan](#pengenalan)
3. [Cara Menggunakan API](#cara-menggunakan-api)
4. [Alamat Server](#alamat-server)
5. [Cara Login dan Mendapatkan Token](#cara-login-dan-mendapatkan-token)
6. [Dokumentasi Lengkap](#dokumentasi-lengkap)

---

## Setup Awal

### 1. Install Dependencies

Tambahkan package berikut ke file `pubspec.yaml`:

```yaml
dependencies:
  flutter:
    sdk: flutter
  http: ^1.1.0              # Untuk HTTP request
  shared_preferences: ^2.2.2  # Untuk menyimpan token dan data user
  intl: ^0.19.0             # Untuk format tanggal dan angka
```

Kemudian jalankan:
```bash
flutter pub get
```

### 2. Struktur File yang Perlu Dibuat

Pastikan struktur folder seperti ini:

```
lib/
├── config/
│   └── api_config.dart          # Konfigurasi base URL dan endpoints
├── services/
│   ├── api_service.dart         # Service untuk HTTP request
│   ├── auth_service.dart        # Service untuk authentication
│   ├── booking_service.dart     # Service untuk booking
│   └── data_service.dart         # Service untuk data (destinasi, dll)
├── models/
│   ├── user.dart                # Model User
│   └── booking.dart             # Model Booking
└── pages/
    └── ...                      # Halaman aplikasi
```

### 3. File ApiConfig

Buat file `lib/config/api_config.dart`:

```dart
class ApiConfig {
  // Sesuaikan dengan environment Anda
  static const String baseUrl = 'http://localhost:8000/api';
  // Untuk Android Emulator: 'http://10.0.2.2:8000/api'
  // Untuk Physical Device: 'http://YOUR_IP:8000/api'
  
  // Endpoints
  static const String register = '/register';
  static const String login = '/login';
  static const String logout = '/logout';
  static const String profile = '/profile';
  static const String destinations = '/destinations';
  static const String cities = '/cities';
  static const String sliders = '/sliders';
  static const String promos = '/promos';
  static const String bookings = '/bookings';
  
  // Helper methods
  static String destinationById(String id) => '/destinations/$id';
  static String bookingById(String id) => '/bookings/$id';
  static String bookingStatus(String id) => '/bookings/$id/status';
}
```

### 4. File ApiService

Buat file `lib/services/api_service.dart`:

```dart
import 'dart:convert';
import 'package:http/http.dart' as http;
import '../config/api_config.dart';
import 'auth_service.dart';

class ApiService {
  // Helper untuk mendapatkan headers dengan token
  static Future<Map<String, String>> _getHeaders({
    Map<String, String>? additionalHeaders,
  }) async {
    final headers = {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      ...?additionalHeaders,
    };

    // Ambil token dari AuthService
    final token = await AuthService.getToken();
    if (token != null) {
      headers['Authorization'] = 'Bearer $token';
    }

    return headers;
  }

  // GET request
  static Future<http.Response> get(
    String endpoint, {
    Map<String, String>? headers,
  }) async {
    final url = Uri.parse('${ApiConfig.baseUrl}$endpoint');
    final allHeaders = await _getHeaders(additionalHeaders: headers);
    final response = await http.get(url, headers: allHeaders);
    return response;
  }

  // POST request
  static Future<http.Response> post(
    String endpoint, {
    Map<String, dynamic>? body,
    Map<String, String>? headers,
  }) async {
    final url = Uri.parse('${ApiConfig.baseUrl}$endpoint');
    final allHeaders = await _getHeaders(additionalHeaders: headers);
    final response = await http.post(
      url,
      headers: allHeaders,
      body: body != null ? jsonEncode(body) : null,
    );
    return response;
  }

  // PUT request
  static Future<http.Response> put(
    String endpoint, {
    Map<String, dynamic>? body,
    Map<String, String>? headers,
  }) async {
    final url = Uri.parse('${ApiConfig.baseUrl}$endpoint');
    final allHeaders = await _getHeaders(additionalHeaders: headers);
    final response = await http.put(
      url,
      headers: allHeaders,
      body: body != null ? jsonEncode(body) : null,
    );
    return response;
  }

  // PATCH request
  static Future<http.Response> patch(
    String endpoint, {
    Map<String, dynamic>? body,
    Map<String, String>? headers,
  }) async {
    final url = Uri.parse('${ApiConfig.baseUrl}$endpoint');
    final allHeaders = await _getHeaders(additionalHeaders: headers);
    final response = await http.patch(
      url,
      headers: allHeaders,
      body: body != null ? jsonEncode(body) : null,
    );
    return response;
  }
}
```

### 5. Cara Menggunakan ApiService

**Import yang diperlukan:**
```dart
import 'dart:convert';                    // Untuk jsonDecode/jsonEncode
import '../services/api_service.dart';   // ApiService
import '../config/api_config.dart';       // ApiConfig untuk endpoints
```

**Contoh sederhana memanggil API:**

```dart
// GET request (tidak perlu login)
Future<void> getDestinations() async {
  try {
    final response = await ApiService.get(ApiConfig.destinations);
    final data = jsonDecode(response.body);
    
    if (data['success'] == true) {
      print('Destinasi: ${data['data']['destinations']}');
    }
  } catch (e) {
    print('Error: $e');
  }
}

// POST request (perlu login - token otomatis ditambahkan)
Future<void> createBooking() async {
  try {
    final response = await ApiService.post(
      ApiConfig.bookings,
      body: {
        'destination_id': '1',
        'tanggal_keberangkatan': '2024-02-15',
        'waktu_keberangkatan': '08:00',
        'metode_pembayaran': 'bank_transfer',
      },
    );
    
    final data = jsonDecode(response.body);
    
    if (data['success'] == true) {
      print('Booking berhasil: ${data['data']['booking']}');
    } else {
      print('Error: ${data['message']}');
    }
  } catch (e) {
    print('Error: $e');
  }
}
```

---

## Pengenalan

API ini digunakan untuk menghubungkan aplikasi mobile Flutter dengan server backend Laravel. Semua data seperti destinasi wisata, booking, dan profil user disimpan di server dan bisa diakses melalui API ini.

### Fitur Utama

- ✅ **Autentikasi**: Login, Register, Logout
- ✅ **Profil User**: Lihat dan edit profil
- ✅ **Home Page**: Destinasi, kota, slider, promo
- ✅ **Detail Paket**: Informasi lengkap paket wisata
- ✅ **Booking**: Buat booking dan lihat riwayat
- ✅ **Pembayaran**: Integrasi dengan Midtrans

---

## Cara Menggunakan API

### 1. Alamat Server

Alamat server berbeda tergantung di mana aplikasi dijalankan:

| Platform | Alamat Server |
|----------|---------------|
| **Android Emulator** | `http://10.0.2.2:8000/api` |
| **iOS Simulator** | `http://localhost:8000/api` |
| **Device Fisik** | `http://IP_KOMPUTER_ANDA:8000/api` |
| **Flutter Web** | `http://localhost:8000/api` |

**Cara cari IP komputer:**
- Windows: Buka CMD, ketik `ipconfig`, cari "IPv4 Address"
- Mac/Linux: Buka Terminal, ketik `ifconfig`, cari "inet"

### 2. Format Request

Semua request ke API harus menggunakan format JSON dan menyertakan header berikut:

```
Content-Type: application/json
Accept: application/json
```

Untuk request yang memerlukan login, tambahkan header:

```
Authorization: Bearer {token}
```

### 3. Format Response

Semua response dari API menggunakan format JSON dengan struktur:

**Sukses:**
```json
{
  "success": true,
  "message": "Pesan sukses",
  "data": {
    // Data yang diminta
  }
}
```

**Error:**
```json
{
  "success": false,
  "message": "Pesan error",
  "errors": {
    "field": ["Detail error"]
  }
}
```

---

## Cara Login dan Mendapatkan Token

### Langkah 1: Register (Pendaftaran)

Jika belum punya akun, daftar dulu:

**Request:**
```
POST http://localhost:8000/api/register
```

**Body:**
```json
{
  "name": "Nama Anda",
  "email": "email@example.com",
  "password": "password123",
  "phone_number": "081234567890"
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "user": { ... },
    "token": "1|xxxxxxxxxxxxx"  // INI TOKENNYA!
  }
}
```

**Simpan token ini!** Token ini digunakan untuk semua request yang memerlukan login.

### Langkah 2: Login (Jika Sudah Punya Akun)

**Request:**
```
POST http://localhost:8000/api/login
```

**Body:**
```json
{
  "email": "email@example.com",
  "password": "password123"
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "token": "1|xxxxxxxxxxxxx"  // INI TOKENNYA!
  }
}
```

### Langkah 3: Gunakan Token

Setelah dapat token, tambahkan di header setiap request:

```
Authorization: Bearer 1|xxxxxxxxxxxxx
```

---

## Kode Status HTTP

| Kode | Arti |
|------|------|
| 200 | Sukses |
| 201 | Berhasil dibuat |
| 401 | Belum login / Token tidak valid |
| 404 | Data tidak ditemukan |
| 422 | Data yang dikirim tidak valid |
| 500 | Error di server |

---

## Dokumentasi Lengkap

Dokumentasi lengkap dibagi menjadi beberapa file:

1. **[AUTH_API.md](AUTH_API.md)** - Login, Register, Logout
2. **[PROFILE_API.md](PROFILE_API.md)** - Lihat dan Edit Profil
3. **[HOME_API.md](HOME_API.md)** - Data untuk Home Page (Destinasi, Kota, Slider, Promo)
4. **[DESTINATION_API.md](DESTINATION_API.md)** - Detail Paket Wisata
5. **[BOOKING_API.md](BOOKING_API.md)** - Booking dan Riwayat Pemesanan

Silakan buka file-file tersebut untuk panduan lengkap setiap fitur.

---

## Tips Penting

### 1. Simpan Token dengan Aman
Token adalah kunci untuk mengakses data user. Simpan dengan aman dan jangan share ke orang lain.

### 2. Token Expire
Jika token sudah tidak valid (error 401), user harus login ulang untuk mendapatkan token baru.

### 3. Format Tanggal
- Format tanggal untuk input: `YYYY-MM-DD` (contoh: `2024-01-15`)
- Format tanggal dari server: `d F Y` (contoh: `15 January 2024`)

### 4. URL Gambar
Semua URL gambar dari server sudah lengkap (full URL), bisa langsung digunakan di Flutter:
```
http://localhost:8000/Asset_Travelo/nama_file.jpg
```

### 5. Error Handling
Selalu cek field `success` di response. Jika `false`, tampilkan pesan error ke user.

---

## Contoh Penggunaan

### Contoh 1: Login dan Ambil Data Destinasi

```dart
// 1. Login dulu
var loginResponse = await ApiService.post('/login', {
  'email': 'user@example.com',
  'password': 'password123'
});

// 2. Simpan token
String token = loginResponse['data']['token'];

// 3. Ambil data destinasi (tidak perlu token karena public)
var destinations = await ApiService.get('/destinations');

// 4. Ambil data booking (perlu token)
var bookings = await ApiService.get('/bookings', token: token);
```

### Contoh 2: Buat Booking

```dart
// Pastikan sudah login dan punya token
var bookingResponse = await ApiService.post('/bookings', {
  'destination_id': '1',
  'tanggal_keberangkatan': '2024-02-15',
  'waktu_keberangkatan': '08:00',
  'metode_pembayaran': 'bank_transfer'
}, token: token);

// Response berisi snapToken untuk pembayaran
String snapToken = bookingResponse['data']['booking']['snapToken'];
```

---

## Bantuan

Jika ada pertanyaan atau masalah:
1. Cek dokumentasi di file-file terpisah
2. Pastikan alamat server sudah benar
3. Pastikan token masih valid
4. Cek format data yang dikirim sudah benar

---

**Selamat coding! 🚀**

