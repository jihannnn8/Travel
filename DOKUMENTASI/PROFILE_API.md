# Dokumentasi API - Profil User

Dokumentasi lengkap untuk fitur lihat dan edit profil user.

---

## 📦 Import dan Setup

### Import yang Diperlukan

```dart
import 'dart:convert';                    // Untuk decode/encode JSON
import '../services/api_service.dart';   // Service untuk HTTP request
import '../config/api_config.dart';       // Konfigurasi endpoints
```

**Catatan:** Semua endpoint profile memerlukan login (token akan otomatis ditambahkan oleh ApiService).

---

## 1. Lihat Profil

Mendapatkan informasi profil user yang sedang login.

### Endpoint
```
GET /api/profile
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
GET http://localhost:8000/api/profile
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
  "message": "Profile retrieved successfully",
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

**Data yang Diterima:**
- `id`: ID user
- `name`: Nama lengkap
- `email`: Email user
- `phoneNumber`: Nomor telepon (bisa kosong)

### Response Error (401) - Belum Login

```json
{
  "success": false,
  "message": "User not authenticated"
}
```

---

## 2. Update Profil

Mengubah data profil user yang sedang login.

### Endpoint
```
PUT /api/profile
```
atau
```
PATCH /api/profile
```

*Kedua endpoint sama saja, bisa pakai yang mana.*

### Apakah Perlu Login?
✅ Ya, harus login dulu.

### Header yang Diperlukan

```
Authorization: Bearer {token}
Content-Type: application/json
Accept: application/json
```

### Data yang Bisa Diubah

| Field | Tipe | Wajib? | Keterangan |
|-------|------|--------|------------|
| name | text | ❌ Tidak | Nama lengkap (maksimal 255 karakter) |
| email | email | ❌ Tidak | Email (harus unik jika diubah) |
| phoneNumber | text | ❌ Tidak | Nomor telepon (maksimal 20 karakter) |
| password | text | ❌ Tidak | Password baru (minimal 6 karakter) |

**Catatan Penting:**
- Semua field **tidak wajib** (optional)
- Hanya field yang dikirim yang akan diubah
- Field yang tidak dikirim tetap seperti semula

### Contoh Request

**URL:**
```
PUT http://localhost:8000/api/profile
```

**Header:**
```
Authorization: Bearer 1|abcdefghijklmnopqrstuvwxyz1234567890
Content-Type: application/json
Accept: application/json
```

**Body - Ubah Nama Saja:**
```json
{
  "name": "Budi Santoso Updated"
}
```

**Body - Ubah Email dan Nomor Telepon:**
```json
{
  "email": "budi.new@example.com",
  "phoneNumber": "081999999999"
}
```

**Body - Ubah Semua:**
```json
{
  "name": "Budi Santoso Updated",
  "email": "budi.new@example.com",
  "phoneNumber": "081999999999",
  "password": "newpassword123"
}
```

**Body - Ubah Password Saja:**
```json
{
  "password": "newpassword123"
}
```

### Response Sukses (200)

```json
{
  "success": true,
  "message": "Profile updated successfully",
  "data": {
    "user": {
      "id": "1",
      "name": "Budi Santoso Updated",
      "email": "budi.new@example.com",
      "phoneNumber": "081999999999"
    }
  }
}
```

**Yang Terjadi:**
- Data user di database diupdate
- Response berisi data user yang sudah diupdate
- Update data user di aplikasi dengan data dari response

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
- Email sudah digunakan user lain
- Password terlalu pendek (minimal 6 karakter)
- Format email tidak valid
- Nomor telepon terlalu panjang (maksimal 20 karakter)

### Response Error (401) - Belum Login

```json
{
  "success": false,
  "message": "User not authenticated"
}
```

### Response Error (500) - Server Error

```json
{
  "success": false,
  "message": "Failed to update profile",
  "error": "Pesan error detail"
}
```

---

## Tips Penggunaan

### 1. Update Parsial
Tidak perlu mengirim semua field. Hanya kirim field yang ingin diubah:

**Benar:**
```json
{
  "name": "Nama Baru"
}
```

**Tidak Perlu:**
```json
{
  "name": "Nama Baru",
  "email": "email@example.com",  // Tidak perlu jika tidak diubah
  "phoneNumber": "081234567890"   // Tidak perlu jika tidak diubah
}
```

### 2. Validasi di Aplikasi
Sebelum mengirim request, validasi dulu di aplikasi:
- Email harus format valid
- Password minimal 6 karakter
- Nomor telepon maksimal 20 karakter

### 3. Update UI Setelah Berhasil
Setelah update berhasil, update data user di aplikasi dengan data dari response agar UI langsung terupdate.

### 4. Handle Error dengan Baik
Tampilkan pesan error yang user-friendly:
- "Email sudah digunakan" (bukan "The email has already been taken")
- "Password minimal 6 karakter" (bukan "The password must be at least 6 characters")

---

## Contoh Kode Flutter Lengkap

### Lihat Profil

```dart
import 'dart:convert';
import '../services/api_service.dart';
import '../config/api_config.dart';

Future<Map<String, dynamic>?> getProfile() async {
  try {
    // 1. Panggil API profile (token otomatis ditambahkan)
    final response = await ApiService.get(ApiConfig.profile);

    // 2. Decode response JSON
    final responseData = jsonDecode(response.body);

    // 3. Cek apakah sukses
    if (response.statusCode == 200 && responseData['success'] == true) {
      // 4. Ambil data user
      final user = responseData['data']['user'];
      return user;
    } else {
      print('Gagal ambil profil: ${responseData['message']}');
      return null;
    }
  } catch (e) {
    print('Error get profile: $e');
    return null;
  }
}

// Cara menggunakan:
Future<void> loadProfile() async {
  final user = await getProfile();
  if (user != null) {
    setState(() {
      _name = user['name'] ?? '';
      _email = user['email'] ?? '';
      _phoneNumber = user['phoneNumber'] ?? '';
    });
  }
}
```

### Update Profil

```dart
import 'dart:convert';
import '../services/api_service.dart';
import '../config/api_config.dart';

Future<Map<String, dynamic>?> updateProfile({
  String? name,
  String? email,
  String? phoneNumber,
  String? password,
}) async {
  try {
    // 1. Siapkan body request (hanya field yang tidak null)
    final body = <String, dynamic>{};
    if (name != null && name.isNotEmpty) body['name'] = name;
    if (email != null && email.isNotEmpty) body['email'] = email;
    if (phoneNumber != null) body['phoneNumber'] = phoneNumber;
    if (password != null && password.isNotEmpty) body['password'] = password;

    // 2. Panggil API update profile (bisa PUT atau PATCH)
    final response = await ApiService.put(
      ApiConfig.profile,
      body: body,
    );

    // 3. Decode response JSON
    final responseData = jsonDecode(response.body);

    // 4. Cek apakah sukses
    if (response.statusCode == 200 && responseData['success'] == true) {
      // 5. Ambil data user yang sudah diupdate
      final updatedUser = responseData['data']['user'];
      
      print('Profil berhasil diupdate!');
      return {
        'success': true,
        'user': updatedUser,
      };
    } else {
      // Handle error
      print('Gagal update profil: ${responseData['message']}');
      
      if (responseData['errors'] != null) {
        final errors = responseData['errors'] as Map<String, dynamic>;
        return {
          'success': false,
          'message': responseData['message'],
          'errors': errors,
        };
      }
      
      return {
        'success': false,
        'message': responseData['message'] ?? 'Gagal update profil',
      };
    }
  } catch (e) {
    print('Error update profile: $e');
    return {
      'success': false,
      'message': 'Terjadi kesalahan: $e',
    };
  }
}

// Cara menggunakan:
Future<void> _saveProfile() async {
  // Tampilkan loading
  showDialog(
    context: context,
    barrierDismissible: false,
    builder: (context) => Center(child: CircularProgressIndicator()),
  );

  final result = await updateProfile(
    name: _nameController.text,
    email: _emailController.text,
    phoneNumber: _phoneController.text,
    password: _passwordController.text.isEmpty 
        ? null 
        : _passwordController.text,
  );

  // Tutup loading
  Navigator.pop(context);

  if (result?['success'] == true) {
    // Update berhasil, refresh data profil
    await loadProfile();
    
    // Tampilkan pesan sukses
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(content: Text('Profil berhasil diupdate!')),
    );
    
    // Kembali ke halaman sebelumnya
    Navigator.pop(context);
  } else {
    // Tampilkan error
    String errorMessage = result?['message'] ?? 'Gagal update profil';
    
    if (result?['errors'] != null) {
      final errors = result!['errors'] as Map<String, dynamic>;
      final firstError = errors.values.first;
      if (firstError is List && firstError.isNotEmpty) {
        errorMessage = firstError.first;
      }
    }
    
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(content: Text(errorMessage)),
    );
  }
}
```

### Contoh Penggunaan di UI

```dart
// Di halaman edit profil
void _saveProfile() async {
  // Tampilkan loading
  showLoading();
  
  bool success = await updateProfile(
    name: _nameController.text,
    email: _emailController.text,
    phoneNumber: _phoneController.text,
    password: _passwordController.text.isEmpty 
        ? null 
        : _passwordController.text,
  );
  
  // Sembunyikan loading
  hideLoading();
  
  if (success) {
    // Tampilkan pesan sukses
    showSnackBar('Profil berhasil diupdate!');
    
    // Refresh data profil
    await loadProfile();
    
    // Kembali ke halaman sebelumnya
    Navigator.pop(context);
  } else {
    // Tampilkan pesan error
    showSnackBar('Gagal update profil. Silakan coba lagi.');
  }
}
```

---

## Skenario Penggunaan

### Skenario 1: User Hanya Ubah Nama

**Request:**
```json
{
  "name": "Nama Baru"
}
```

**Response:**
- Nama diupdate
- Email tetap
- Nomor telepon tetap
- Password tetap

### Skenario 2: User Ubah Email

**Request:**
```json
{
  "email": "email.baru@example.com"
}
```

**Response:**
- Email diupdate
- Nama tetap
- Nomor telepon tetap
- Password tetap

**Penting:** Pastikan email baru belum digunakan user lain, kalau tidak akan error.

### Skenario 3: User Ubah Password

**Request:**
```json
{
  "password": "passwordbaru123"
}
```

**Response:**
- Password diupdate (di-hash di server)
- Nama tetap
- Email tetap
- Nomor telepon tetap

**Penting:** Setelah ubah password, user harus login ulang dengan password baru.

### Skenario 4: User Ubah Semua Data

**Request:**
```json
{
  "name": "Nama Baru",
  "email": "email.baru@example.com",
  "phoneNumber": "081999999999",
  "password": "passwordbaru123"
}
```

**Response:**
- Semua data diupdate

---

**Selanjutnya:** Baca [HOME_API.md](HOME_API.md) untuk data home page.

