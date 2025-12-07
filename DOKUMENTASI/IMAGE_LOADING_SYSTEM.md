# Dokumentasi Sistem Load Gambar

## 📋 Daftar Isi

1. [Pengenalan](#pengenalan)
2. [Konsep Dasar](#konsep-dasar)
3. [Struktur Folder](#struktur-folder)
4. [Backend (Laravel)](#backend-laravel)
5. [Frontend (Flutter)](#frontend-flutter)
6. [Cara Kerja](#cara-kerja)
7. [Contoh Penggunaan](#contoh-penggunaan)
8. [Troubleshooting](#troubleshooting)

---

## 🎯 Pengenalan

Dokumentasi ini menjelaskan sistem load gambar yang digunakan di aplikasi Travelo. Sistem ini dirancang untuk memudahkan pengelolaan gambar dari berbagai sumber (file seeder dan file yang di-upload) dengan cara yang konsisten dan mudah dipahami.

### Apa yang Dibahas?

- ✅ Cara menyimpan gambar di backend
- ✅ Cara mengakses gambar melalui API
- ✅ Cara menampilkan gambar di aplikasi Flutter
- ✅ Penjelasan URL pattern yang digunakan
- ✅ Troubleshooting masalah umum

---

## 📚 Konsep Dasar

### Dua Jenis Gambar

Aplikasi ini menggunakan *2 jenis gambar*:

1. *Gambar Seeder* (Gambar Default)
   - Gambar yang sudah ada sejak awal (contoh: logo, gambar destinasi default)
   - Disimpan di folder public/Asset_Travelo/
   - Diakses melalui URL: /api/asset/...

2. *Gambar Upload* (Gambar yang Di-upload User/Admin)
   - Gambar yang di-upload oleh admin melalui panel admin
   - Disimpan di folder storage/app/public/ (dengan symlink ke public/storage/)
   - Diakses melalui URL: /api/storage/...
   - Path di database: destinations/filename.jpg atau bookings/filename.jpg

### Mengapa Menggunakan /api/asset/ dan /api/storage/?

- ✅ *Konsisten*: Semua gambar diakses melalui pattern yang sama
- ✅ *Mudah Dikelola*: Backend bisa membedakan sumber gambar dengan mudah
- ✅ *CORS Ready*: Header CORS sudah dikonfigurasi dengan benar
- ✅ *Aman*: Ada validasi keamanan untuk mencegah akses file yang tidak sah

---

## 📁 Struktur Folder

### Di Backend (Laravel)


backend/
├── public/
│   ├── Asset_Travelo/          # Folder untuk gambar seeder/default
│   │   ├── images/             # Subfolder untuk gambar
│   │   │   ├── Logo.png
│   │   │   ├── Bromo.jpg
│   │   │   ├── InfoBali.jpg
│   │   │   └── ...
│   │   ├── Bromo.jpg
│   │   └── ...
│   └── storage/                # Symlink ke storage/app/public
│       ├── destinations/       # Gambar destinasi yang di-upload
│       └── bookings/          # Gambar booking yang di-upload
│
└── storage/
    └── app/
        └── public/            # Actual storage location
            ├── destinations/  # File sebenarnya disimpan di sini
            └── bookings/


### Contoh URL yang Dihasilkan

- *Gambar seeder*: http://localhost:8000/api/asset/images/Logo.png
- *Gambar upload destinasi*: http://localhost:8000/api/storage/destinations/filename.jpg
- *Gambar upload booking*: http://localhost:8000/api/storage/bookings/filename.jpg

---

## 🔧 Backend (Laravel)

### 1. Route API untuk Gambar

File: backend/routes/api.php

php
// Route untuk serve images dari Asset_Travelo (gambar seeder)
Route::get('/asset/{path}', function ($path) {
    // ... kode untuk serve gambar dari Asset_Travelo
})->where('path', '.*');

// Route untuk serve images dari storage (gambar upload)
Route::get('/storage/{path}', function ($path) {
    // ... kode untuk serve gambar dari storage
})->where('path', '.*');


*Penjelasan:*
- Route ini membuat endpoint khusus untuk mengakses gambar
- {path} adalah path file gambar (contoh: images/Logo.png atau destinations/filename.jpg)
- ->where('path', '.*') memungkinkan path dengan subfolder
- Route /storage/ mengecek file di dua lokasi:
  - public/storage/ (symlink)
  - storage/app/public/ (actual storage)

### 2. Controller yang Mengembalikan URL Gambar

Semua controller API (DestinationController, CitiesController, dll) sudah dikonfigurasi untuk mengembalikan URL gambar dalam format yang benar.

*Contoh di DestinationController:*

php
private function formatImageUrl(?string $imageUrl): string
{
    // Prioritas: cek path yang dimulai dengan destinations/ atau bookings/
    $cleanPath = ltrim($imageUrl, '/');
    if (strpos($cleanPath, 'destinations/') === 0 || 
        strpos($cleanPath, 'bookings/') === 0) {
        // Path dari storage upload
        return url('api/storage/' . $cleanPath);
    }
    
    // Jika path mengandung Asset_Travelo, convert ke /api/asset/
    if (strpos($imageUrl, 'Asset_Travelo/') !== false) {
        $relativePath = preg_replace('#^.*Asset_Travelo[/\\\\]#', '', $imageUrl);
        return url('api/asset/' . $relativePath);
    }
    
    // Jika path mengandung storage/, convert ke /api/storage/
    if (strpos($imageUrl, 'storage/') !== false) {
        $relativePath = preg_replace('#^.*storage[/\\\\]#', '', $imageUrl);
        return url('api/storage/' . $relativePath);
    }
    
    // Default: assume dari Asset_Travelo
    return url('api/asset/' . ltrim($imageUrl, '/'));
}


*Penjelasan:*
- Function ini mengubah path file menjadi URL lengkap
- *Prioritas penting*: Path yang dimulai dengan destinations/ atau bookings/ langsung diarahkan ke /api/storage/
- Otomatis mendeteksi apakah gambar dari seeder atau upload
- Mengembalikan URL yang siap digunakan di frontend

### 3. Upload File di Admin Panel

File: backend/app/Http/Controllers/Admin/DestinationController.php

php
// Handle upload image
if ($request->hasFile('image_url')) {
    $imagePath = $request->file('image_url')->store('destinations', 'public');
    // Hasil: "destinations/filename.jpg"
}

// Simpan ke database
$destination->update([
    'image_url' => $imagePath, // "destinations/filename.jpg"
]);


*Penjelasan:*
- File di-upload menggunakan store('destinations', 'public')
- File disimpan di storage/app/public/destinations/
- Path yang disimpan di database: destinations/filename.jpg (tanpa prefix storage/)
- Controller API otomatis mendeteksi path ini dan mengarahkannya ke /api/storage/

### 4. Seeder (Data Awal)

File: backend/database/seeders/DestinationSeeder.php

php
'image_url' => 'Asset_Travelo/images/destination.jpg',


*Penjelasan:*
- Di database, kita simpan path relatif (tanpa public/)
- Controller akan otomatis convert ke URL lengkap saat API dipanggil

---

## 📱 Frontend (Flutter)

### 1. Konfigurasi API

File: lib/config/api_config.dart

dart
class ApiConfig {
  static const String baseUrl = 'http://localhost:8000/api';
  
  // Function untuk memperbaiki URL gambar
  static String fixImageUrl(String? imageUrl) {
    // ... kode untuk fix URL
    // Handle semua format: http, /api/asset/, /api/storage/, relative path
  }
}


*Penjelasan:*
- baseUrl adalah alamat server backend
- fixImageUrl() memastikan URL gambar benar untuk semua environment (web, emulator, device)
- Otomatis handle URL yang sudah dalam format /api/asset/ atau /api/storage/

### 2. Service untuk Load Data

File: lib/services/data_service.dart

dart
static Future<List<TourPackage>> getTourPackagesAsync() async {
  final packages = await DestinationService.getDestinations();
  // URL gambar sudah otomatis di-fix oleh controller backend
  return packages ?? [];
}


*Penjelasan:*
- Service ini memanggil API backend
- Backend sudah mengembalikan URL gambar yang benar
- Tidak perlu fix URL lagi di sini

### 3. Menampilkan Gambar di UI

File: lib/pages/home_page.dart

dart
Widget _buildImage(String imageUrl) {
  // Cek apakah URL network atau asset lokal
  if (imageUrl.startsWith('http://') || 
      imageUrl.startsWith('https://') ||
      imageUrl.startsWith('/api/asset/') ||
      imageUrl.startsWith('/api/storage/')) {
    // Network image - fix URL jika perlu
    final fixedUrl = ApiConfig.fixImageUrl(imageUrl);
    return Image.network(fixedUrl, ...);
  } else {
    // Asset image lokal
    return Image.asset(imageUrl, ...);
  }
}


*Penjelasan:*
- Function ini mengecek jenis gambar (network atau lokal)
- Jika network, gunakan Image.network()
- Jika lokal, gunakan Image.asset()
- URL otomatis di-fix untuk environment yang berbeda

---

## ⚙ Cara Kerja

### Alur Lengkap dari Upload ke Tampilan

#### Untuk Gambar Upload (Admin Panel)


1. Admin upload gambar
   └─> File disimpan: storage/app/public/destinations/filename.jpg
   
2. Database
   └─> image_url: "destinations/filename.jpg"
   
3. Controller (Backend)
   └─> formatImageUrl() mendeteksi path dimulai dengan "destinations/"
       └─> Convert menjadi: "http://localhost:8000/api/storage/destinations/filename.jpg"
   
4. API Response
   └─> {
         "imageUrl": "http://localhost:8000/api/storage/destinations/filename.jpg"
       }
   
5. Flutter App
   └─> ApiConfig.fixImageUrl() memastikan URL benar
       └─> Image.network() menampilkan gambar


#### Untuk Gambar Seeder


1. Database
   └─> image_url: "Asset_Travelo/images/Logo.png"
   
2. Controller (Backend)
   └─> formatImageUrl() mendeteksi path mengandung "Asset_Travelo/"
       └─> Convert menjadi: "http://localhost:8000/api/asset/images/Logo.png"
   
3. API Response
   └─> {
         "imageUrl": "http://localhost:8000/api/asset/images/Logo.png"
       }
   
4. Flutter App
   └─> ApiConfig.fixImageUrl() memastikan URL benar
       └─> Image.network() menampilkan gambar


### Contoh Lengkap

*1. Admin upload gambar destinasi:*
php
// Di Admin/DestinationController
$imagePath = $request->file('image_url')->store('destinations', 'public');
// Hasil: "destinations/tS2l0lGe1haRS7ounuM8IzQBcN9zJSRHvLxKnqkU.jpg"


*2. Path disimpan di database:*
sql
UPDATE destinations SET image_url = 'destinations/tS2l0lGe1haRS7ounuM8IzQBcN9zJSRHvLxKnqkU.jpg'


*3. Controller mengubah ke URL lengkap:*
php
// Di Api/DestinationController
$imageUrl = 'destinations/tS2l0lGe1haRS7ounuM8IzQBcN9zJSRHvLxKnqkU.jpg';
// formatImageUrl() mendeteksi path dimulai dengan "destinations/"
$formattedUrl = url('api/storage/destinations/tS2l0lGe1haRS7ounuM8IzQBcN9zJSRHvLxKnqkU.jpg');
// Hasil: "http://localhost:8000/api/storage/destinations/tS2l0lGe1haRS7ounuM8IzQBcN9zJSRHvLxKnqkU.jpg"


*4. API mengembalikan JSON:*
json
{
  "success": true,
  "data": {
    "destination": {
      "imageUrl": "http://localhost:8000/api/storage/destinations/tS2l0lGe1haRS7ounuM8IzQBcN9zJSRHvLxKnqkU.jpg"
    }
  }
}


*5. Flutter menampilkan gambar:*
dart
// Di home_page.dart
Image.network(
  'http://localhost:8000/api/storage/destinations/tS2l0lGe1haRS7ounuM8IzQBcN9zJSRHvLxKnqkU.jpg',
  fit: BoxFit.cover,
)


---

## 💡 Contoh Penggunaan

### Contoh 1: Menampilkan Gambar Destinasi

dart
// Di package_detail_page.dart
Widget _buildImage(String imageUrl) {
  return Image.network(
    ApiConfig.fixImageUrl(imageUrl),
    width: double.infinity,
    height: 250,
    fit: BoxFit.cover,
  );
}


### Contoh 2: Menampilkan Gambar di List

dart
// Di home_page.dart
ListView.builder(
  itemBuilder: (context, index) {
    final package = packages[index];
    return Image.network(
      ApiConfig.fixImageUrl(package.imageUrl),
      fit: BoxFit.cover,
    );
  },
)


### Contoh 3: Menampilkan Gambar dengan Error Handling

dart
Image.network(
  ApiConfig.fixImageUrl(imageUrl),
  loadingBuilder: (context, child, loadingProgress) {
    if (loadingProgress == null) return child;
    return CircularProgressIndicator();
  },
  errorBuilder: (context, error, stackTrace) {
    return Icon(Icons.broken_image);
  },
)


---

## 🔍 Troubleshooting

### Masalah 1: Gambar Tidak Muncul (404 Error)

*Gejala:*
- Gambar tidak muncul di aplikasi
- Error 404 di console

*Penyebab:*
- Path file salah di database
- File tidak ada di folder yang benar
- Symlink storage belum dibuat

*Solusi:*
1. Cek path di database:
   sql
   SELECT image_url FROM destinations WHERE id = 1;
   

2. Pastikan file ada di folder:
   - Untuk seeder: backend/public/Asset_Travelo/images/Logo.png
   - Untuk upload: backend/storage/app/public/destinations/filename.jpg

3. Buat symlink storage (jika belum):
   bash
   php artisan storage:link
   

4. Cek URL di browser:
   
   http://localhost:8000/api/storage/destinations/filename.jpg
   http://localhost:8000/api/asset/images/Logo.png
   

### Masalah 2: Error 500 Internal Server Error

*Gejala:*
- Error 500 saat mengakses gambar
- Gambar tidak muncul

*Penyebab:*
- File tidak ditemukan di lokasi yang diharapkan
- Permission issue
- Symlink tidak ada

*Solusi:*
1. Cek log Laravel:
   bash
   tail -f storage/logs/laravel.log
   

2. Pastikan symlink sudah dibuat:
   bash
   php artisan storage:link
   

3. Cek permission folder:
   bash
   chmod -R 775 storage/app/public
   chmod -R 775 public/storage
   

4. Pastikan file ada di lokasi yang benar:
   - storage/app/public/destinations/filename.jpg
   - public/storage/destinations/filename.jpg (symlink)

### Masalah 3: CORS Error

*Gejala:*
- Gambar tidak muncul di Flutter Web
- Error CORS di console browser

*Penyebab:*
- CORS header tidak dikonfigurasi dengan benar

*Solusi:*
1. Cek file backend/config/cors.php:
   php
   'paths' => [
       'api/*',  // Pastikan ini ada
   ],
   

2. Pastikan route API menambahkan CORS header:
   php
   $response->headers->set('Access-Control-Allow-Origin', '*');
   

### Masalah 4: Gambar Muncul di Browser tapi Tidak di App

*Gejala:*
- URL bekerja di browser
- Tidak muncul di Flutter app

*Penyebab:*
- URL menggunakan localhost yang tidak bisa diakses dari device/emulator

*Solusi:*
1. Untuk Android Emulator, gunakan:
   dart
   static const String baseUrl = 'http://10.0.2.2:8000/api';
   

2. Untuk iOS Simulator, gunakan:
   dart
   static const String baseUrl = 'http://localhost:8000/api';
   

3. Untuk Physical Device, gunakan IP komputer:
   dart
   static const String baseUrl = 'http://192.168.1.100:8000/api';
   

### Masalah 5: Path Salah - Gambar Upload Muncul sebagai /api/asset/

*Gejala:*
- Gambar upload muncul dengan URL /api/asset/destinations/...
- Seharusnya /api/storage/destinations/...

*Penyebab:*
- Controller tidak mendeteksi path destinations/ atau bookings/ dengan benar

*Solusi:*
1. Pastikan path di database dimulai dengan destinations/ atau bookings/:
   sql
   SELECT image_url FROM destinations;
   -- Harus: "destinations/filename.jpg"
   -- Bukan: "storage/destinations/filename.jpg"
   

2. Pastikan controller sudah update dengan deteksi path yang benar:
   php
   // Di DestinationController::formatImageUrl()
   if (strpos($cleanPath, 'destinations/') === 0 || 
       strpos($cleanPath, 'bookings/') === 0) {
       return url('api/storage/' . $cleanPath);
   }
   

### Masalah 6: Gambar Terlalu Lambat Load

*Gejala:*
- Gambar lama muncul
- Aplikasi terasa lambat

*Solusi:*
1. Gunakan loading indicator:
   dart
   Image.network(
     url,
     loadingBuilder: (context, child, progress) {
       if (progress == null) return child;
       return CircularProgressIndicator();
     },
   )
   

2. Optimasi ukuran gambar di backend
3. Gunakan cache untuk gambar yang sudah di-load

---

## 📝 Checklist untuk Developer Baru

Saat menambahkan gambar baru:

- [ ] *Untuk gambar seeder*: Simpan di public/Asset_Travelo/ atau subfoldernya
- [ ] *Untuk gambar upload*: Gunakan store('destinations', 'public') atau store('bookings', 'public')
- [ ] *Path di database*: 
  - Seeder: Asset_Travelo/images/filename.jpg
  - Upload: destinations/filename.jpg (tanpa prefix storage/)
- [ ] Pastikan controller mengubah path ke URL lengkap
- [ ] Test URL di browser terlebih dahulu
- [ ] Gunakan ApiConfig.fixImageUrl() di Flutter
- [ ] Tambahkan error handling untuk gambar yang gagal load
- [ ] Pastikan symlink storage sudah dibuat (php artisan storage:link)

---

## 🎓 Kesimpulan

Sistem load gambar ini dirancang untuk:

1. *Mudah Dipahami*: Pattern URL yang konsisten
2. *Fleksibel*: Support berbagai environment (web, mobile)
3. *Aman*: Ada validasi dan security check
4. *Efisien*: CORS sudah dikonfigurasi dengan benar
5. *Otomatis*: Controller otomatis mendeteksi jenis gambar

### Poin Penting

- ✅ Semua gambar diakses melalui /api/asset/ atau /api/storage/
- ✅ **Path yang dimulai dengan destinations/ atau bookings/ otomatis diarahkan ke /api/storage/**
- ✅ Backend otomatis convert path ke URL lengkap
- ✅ Flutter menggunakan ApiConfig.fixImageUrl() untuk memastikan URL benar
- ✅ Error handling sudah disediakan untuk gambar yang gagal load
- ✅ Route handler mengecek file di dua lokasi (symlink dan actual storage)

---

## 📞 Bantuan

Jika masih ada masalah:

1. Cek console log untuk error message
2. Test URL langsung di browser
3. Pastikan file ada di folder yang benar
4. Cek konfigurasi CORS di backend
5. Pastikan baseUrl di Flutter sesuai dengan environment
6. Pastikan symlink storage sudah dibuat
7. Cek log Laravel untuk detail error

---

*Dokumentasi ini dibuat untuk memudahkan pemahaman sistem load gambar di aplikasi Travelo.*