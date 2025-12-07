# Dokumentasi API - Autentikasi

Dokumentasi lengkap untuk fitur login, register, dan logout.

---

## 📦 Import dan Setup

### Import yang Diperlukan

```dart
import 'dart:convert';                    // Untuk decode/encode JSON
import '../services/api_service.dart';   // Service untuk HTTP request
import '../config/api_config.dart';       // Konfigurasi endpoints
import 'package:shared_preferences/shared_preferences.dart'; // Untuk simpan token
```

### Setup AuthService (Opsional)

Jika ingin membuat service terpisah untuk authentication, buat file `lib/services/auth_service.dart`:

```dart
import 'package:shared_preferences/shared_preferences.dart';
import 'dart:convert';
import '../config/api_config.dart';
import 'api_service.dart';

class AuthService {
  static const String _tokenKey = 'auth_token';
  static const String _userKey = 'current_user';

  // Simpan token
  static Future<void> saveToken(String token) async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString(_tokenKey, token);
  }

  // Ambil token
  static Future<String?> getToken() async {
    final prefs = await SharedPreferences.getInstance();
    return prefs.getString(_tokenKey);
  }

  // Hapus token (untuk logout)
  static Future<void> deleteToken() async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove(_tokenKey);
    await prefs.remove(_userKey);
  }
}
```

**Catatan:** `ApiService` sudah otomatis mengambil token dari `AuthService.getToken()`, jadi tidak perlu menambahkan token manual di setiap request.

---

## 1. Register (Pendaftaran)

Mendaftarkan user baru ke sistem.

### Endpoint
```
POST /api/register
```

### Apakah Perlu Login?
❌ Tidak, endpoint ini bisa diakses tanpa login.

### Data yang Dikirim

| Field | Tipe | Wajib? | Keterangan |
|-------|------|--------|------------|
| name | text | ✅ Ya | Nama lengkap (maksimal 255 karakter) |
| email | email | ✅ Ya | Email (harus unik, format email valid) |
| password | text | ✅ Ya | Password (minimal 6 karakter) |
| phone_number | text | ❌ Tidak | Nomor telepon (maksimal 20 karakter) |

### Contoh Request

**URL:**
```
POST http://localhost:8000/api/register
```

**Header:**
```
Content-Type: application/json
Accept: application/json
```

**Body:**
```json
{
  "name": "Budi Santoso",
  "email": "budi@example.com",
  "password": "password123",
  "phone_number": "081234567890"
}
```

### Response Sukses (201)

```json
{
  "success": true,
  "message": "Registration successful",
  "data": {
    "user": {
      "id": "1",
      "name": "Budi Santoso",
      "email": "budi@example.com",
      "phoneNumber": "081234567890"
    },
    "token": "1|abcdefghijklmnopqrstuvwxyz1234567890"
  }
}
```

**Yang Penting:**
- Simpan `token` yang ada di response
- Token ini digunakan untuk semua request yang memerlukan login
- Data `user` berisi informasi user yang baru terdaftar

### Response Error (422) - Data Tidak Valid

```json
{
  "success": false,
  "message": "Validation error",
  "errors": {
    "email": ["The email has already been taken."],
    "password": ["The password must be at least 6 characters."]
  }
}
```

**Kemungkinan Error:**
- Email sudah terdaftar
- Password terlalu pendek (minimal 6 karakter)
- Format email tidak valid
- Nama tidak diisi

### Response Error (500) - Server Error

```json
{
  "success": false,
  "message": "Registration failed",
  "error": "Pesan error detail"
}
```

---

## 2. Login

Login ke sistem menggunakan email dan password.

### Endpoint
```
POST /api/login
```

### Apakah Perlu Login?
❌ Tidak, endpoint ini bisa diakses tanpa login.

### Data yang Dikirim

**Opsi 1: Login dengan Email**
```json
{
  "email": "budi@example.com",
  "password": "password123"
}
```

**Opsi 2: Login dengan Nama**
```json
{
  "name": "Budi Santoso",
  "password": "password123"
}
```

| Field | Tipe | Wajib? | Keterangan |
|-------|------|--------|------------|
| email | email | ❌* | Email user |
| name | text | ❌* | Nama user |
| password | text | ✅ Ya | Password user |

*Catatan: Harus mengisi salah satu dari `email` atau `name`

### Contoh Request

**URL:**
```
POST http://localhost:8000/api/login
```

**Header:**
```
Content-Type: application/json
Accept: application/json
```

**Body:**
```json
{
  "email": "budi@example.com",
  "password": "password123"
}
```

### Response Sukses (200)

```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "user": {
      "id": "1",
      "name": "Budi Santoso",
      "email": "budi@example.com",
      "phoneNumber": "081234567890"
    },
    "token": "1|abcdefghijklmnopqrstuvwxyz1234567890"
  }
}
```

**Yang Penting:**
- Simpan `token` untuk request selanjutnya
- Data `user` berisi informasi user yang login

### Response Error (401) - Email/Password Salah

```json
{
  "success": false,
  "message": "Invalid credentials"
}
```

**Kemungkinan Penyebab:**
- Email tidak terdaftar
- Password salah
- Kombinasi email/password tidak cocok

### Response Error (422) - Data Tidak Lengkap

```json
{
  "success": false,
  "message": "Validation error",
  "errors": {
    "name": ["Name or email is required"]
  }
}
```

---

## 3. Logout

Keluar dari sistem dan hapus token yang sedang digunakan.

### Endpoint
```
POST /api/logout
```

### Apakah Perlu Login?
✅ Ya, harus login dulu.

### Header yang Diperlukan

```
Authorization: Bearer {token}
Accept: application/json
```

**Contoh:**
```
Authorization: Bearer 1|abcdefghijklmnopqrstuvwxyz1234567890
```

### Contoh Request

**URL:**
```
POST http://localhost:8000/api/logout
```

**Header:**
```
Authorization: Bearer 1|abcdefghijklmnopqrstuvwxyz1234567890
Accept: application/json
```

### Response Sukses (200)

```json
{
  "success": true,
  "message": "Logout successful"
}
```

**Yang Terjadi:**
- Token yang digunakan dihapus dari server
- User tidak bisa lagi menggunakan token tersebut
- Harus login ulang untuk mendapatkan token baru

### Response Error (401) - Belum Login

```json
{
  "success": false,
  "message": "Unauthenticated"
}
```

**Kemungkinan Penyebab:**
- Token tidak dikirim
- Token sudah tidak valid
- Token sudah dihapus (sudah logout sebelumnya)

---

## 4. Get Current User (Cek User yang Login)

Mendapatkan informasi user yang sedang login.

### Endpoint
```
GET /api/me
```

### Apakah Perlu Login?
✅ Ya, harus login dulu.

### Header yang Diperlukan

```
Authorization: Bearer {token}
Accept: application/json
```

### Contoh Request

**URL:**
```
GET http://localhost:8000/api/me
```

**Header:**
```
Authorization: Bearer 1|abcdefghijklmnopqrstuvwxyz1234567890
Accept: application/json
```

### Response Sukses (200)

```json
{
  "success": true,
  "data": {
    "user": {
      "id": "1",
      "name": "Budi Santoso",
      "email": "budi@example.com",
      "phoneNumber": "081234567890"
    }
  }
}
```

**Kegunaan:**
- Cek apakah token masih valid
- Ambil data user tanpa harus login ulang
- Refresh data user di aplikasi

### Response Error (401) - Belum Login

```json
{
  "success": false,
  "message": "Unauthenticated"
}
```

---

## Tips Penggunaan

### 1. Simpan Token dengan Aman
Setelah login/register, simpan token di tempat yang aman (misalnya SharedPreferences di Flutter). Token ini digunakan untuk semua request yang memerlukan login.

### 2. Cek Token Sebelum Request
Sebelum melakukan request yang memerlukan login, pastikan token sudah ada. Jika tidak ada, arahkan user ke halaman login.

### 3. Handle Token Expire
Jika mendapat error 401, berarti token sudah tidak valid. Arahkan user untuk login ulang.

### 4. Auto Login
Setelah user login, simpan token. Saat aplikasi dibuka lagi, cek apakah token masih valid dengan endpoint `/api/me`. Jika valid, langsung login otomatis.

---

## Contoh Kode Flutter

```dart
// Login
Future<void> login(String email, String password) async {
  try {
    var response = await ApiService.post('/login', {
      'email': email,
      'password': password,
    });
    
    if (response['success']) {
      String token = response['data']['token'];
      // Simpan token
      await saveToken(token);
      
      // Simpan data user
      var user = response['data']['user'];
      await saveUser(user);
      
      print('Login berhasil!');
    } else {
      print('Login gagal: ${response['message']}');
    }
  } catch (e) {
    print('Error: $e');
  }
}

### Logout

```dart
import 'dart:convert';
import '../services/api_service.dart';
import '../config/api_config.dart';
import '../services/auth_service.dart';

Future<bool> logout() async {
  try {
    // 1. Panggil API logout
    final response = await ApiService.post(ApiConfig.logout);

    // 2. Decode response
    final responseData = jsonDecode(response.body);

    // 3. Cek apakah sukses
    if (response.statusCode == 200 && responseData['success'] == true) {
      // 4. Hapus token dari local storage
      await AuthService.deleteToken();

      print('Logout berhasil!');
      return true;
    } else {
      print('Logout gagal: ${responseData['message']}');
      return false;
    }
  } catch (e) {
    print('Error logout: $e');
    // Tetap hapus token meskipun error
    await AuthService.deleteToken();
    return false;
  }
}

// Cara menggunakan:
void _handleLogout() async {
  final success = await logout();
  if (success) {
    // Redirect ke login page
    Navigator.pushReplacementNamed(context, '/login');
  }
}
```

### Cek Apakah Masih Login

```dart
import 'dart:convert';
import '../services/api_service.dart';
import '../config/api_config.dart';
import '../services/auth_service.dart';

Future<bool> checkLogin() async {
  try {
    // 1. Cek apakah ada token
    final token = await AuthService.getToken();
    if (token == null) return false;

    // 2. Cek apakah token masih valid dengan memanggil /me
    final response = await ApiService.get(ApiConfig.me);
    final responseData = jsonDecode(response.body);

    // 3. Jika sukses, berarti masih login
    return response.statusCode == 200 && responseData['success'] == true;
  } catch (e) {
    print('Error check login: $e');
    return false;
  }
}

// Cara menggunakan di initState:
@override
void initState() {
  super.initState();
  _checkLoginStatus();
}

Future<void> _checkLoginStatus() async {
  final isLoggedIn = await checkLogin();
  if (!isLoggedIn) {
    // Redirect ke login
    Navigator.pushReplacementNamed(context, '/login');
  }
}
```

### Get Current User

```dart
import 'dart:convert';
import '../services/api_service.dart';
import '../config/api_config.dart';

Future<Map<String, dynamic>?> getCurrentUser() async {
  try {
    // 1. Panggil API /me
    final response = await ApiService.get(ApiConfig.me);

    // 2. Decode response
    final responseData = jsonDecode(response.body);

    // 3. Cek apakah sukses
    if (response.statusCode == 200 && responseData['success'] == true) {
      return responseData['data']['user'];
    } else {
      print('Gagal ambil user: ${responseData['message']}');
      return null;
    }
  } catch (e) {
    print('Error get current user: $e');
    return null;
  }
}

// Cara menggunakan:
Future<void> loadUserData() async {
  final user = await getCurrentUser();
  if (user != null) {
    setState(() {
      userName = user['name'];
      userEmail = user['email'];
    });
  }
}
```

---

**Selanjutnya:** Baca [PROFILE_API.md](PROFILE_API.md) untuk fitur profil user.

