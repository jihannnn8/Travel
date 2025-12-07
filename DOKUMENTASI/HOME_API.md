# Dokumentasi API - Home Page

Dokumentasi lengkap untuk mengambil data yang ditampilkan di halaman home (destinasi, kota, slider, promo).

---

## 📦 Import dan Setup

### Import yang Diperlukan

```dart
import 'dart:convert';                    // Untuk decode JSON
import '../services/api_service.dart';   // Service untuk HTTP request
import '../config/api_config.dart';       // Konfigurasi endpoints
```

---

## 1. Daftar Destinasi (Tour Packages)

Mendapatkan semua paket wisata yang tersedia.

### Endpoint
```
GET /api/destinations
```

### Apakah Perlu Login?
❌ Tidak, semua orang bisa melihat daftar destinasi.

### Contoh Request

**URL:**
```
GET http://localhost:8000/api/destinations
```

**Header:**
```
Accept: application/json
```

### Response Sukses (200)

```json
{
  "success": true,
  "message": "Destinations retrieved successfully",
  "data": {
    "destinations": [
      {
        "id": "1",
        "title": "Pantai Lombok",
        "description": "Nikmati keindahan pantai Lombok dengan pemandangan yang menakjubkan",
        "imageUrl": "http://localhost:8000/Asset_Travelo/lombok.jpeg",
        "price": 1000000,
        "duration": "2 Hari 3 Malam",
        "departureDate": "15 January 2024",
        "rating": 4.8,
        "totalRatings": 120,
        "rundown": [
          "Hari 1: Kedatangan di Lombok, check-in hotel",
          "Hari 2: Tour ke Pantai Kuta, Pantai Tanjung Aan",
          "Hari 3: Tour ke Gili Trawangan, snorkeling",
          "Hari 4: Free time, check-out hotel"
        ],
        "destination": "Lombok, NTB"
      },
      {
        "id": "2",
        "title": "Yogyakarta Heritage",
        "description": "Jelajahi warisan budaya Yogyakarta",
        "imageUrl": "http://localhost:8000/Asset_Travelo/Yogya.jpg",
        "price": 750000,
        "duration": "3 Hari 2 Malam",
        "departureDate": "20 January 2024",
        "rating": 4.6,
        "totalRatings": 95,
        "rundown": [
          "Hari 1: Kedatangan di Yogyakarta, city tour",
          "Hari 2: Candi Borobudur, Candi Prambanan"
        ],
        "destination": "Yogyakarta"
      }
    ]
  }
}
```

### Penjelasan Data

| Field | Tipe | Keterangan |
|-------|------|------------|
| id | text | ID destinasi (untuk detail) |
| title | text | Judul paket wisata |
| description | text | Deskripsi singkat paket |
| imageUrl | text | URL gambar (sudah lengkap, bisa langsung pakai) |
| price | angka | Harga paket (dalam rupiah) |
| duration | text | Durasi paket (contoh: "2 Hari 3 Malam") |
| departureDate | text | Tanggal keberangkatan (format: "15 January 2024") |
| rating | angka | Rating paket (0-5) |
| totalRatings | angka | Jumlah orang yang memberi rating |
| rundown | array | Daftar itinerary (jadwal per hari) |
| destination | text | Lokasi destinasi |

### Kegunaan
- Tampilkan di halaman home sebagai daftar paket wisata
- Bisa diklik untuk lihat detail (gunakan `id` untuk ambil detail)

---

## 2. Daftar Kota

Mendapatkan semua kota yang tersedia.

### Endpoint
```
GET /api/cities
```

### Apakah Perlu Login?
❌ Tidak, semua orang bisa melihat daftar kota.

### Contoh Request

**URL:**
```
GET http://localhost:8000/api/cities
```

**Header:**
```
Accept: application/json
```

### Response Sukses (200)

```json
{
  "success": true,
  "message": "Cities retrieved successfully",
  "data": {
    "cities": [
      {
        "id": "1",
        "name": "Jakarta",
        "imageUrl": "http://localhost:8000/Asset_Travelo/jakarta.jpg"
      },
      {
        "id": "2",
        "name": "Bali",
        "imageUrl": "http://localhost:8000/Asset_Travelo/bali.jpg"
      },
      {
        "id": "3",
        "name": "Yogyakarta",
        "imageUrl": "http://localhost:8000/Asset_Travelo/yogya.jpg"
      }
    ]
  }
}
```

### Penjelasan Data

| Field | Tipe | Keterangan |
|-------|------|------------|
| id | text | ID kota |
| name | text | Nama kota |
| imageUrl | text | URL gambar kota (sudah lengkap) |

### Kegunaan
- Tampilkan di halaman home sebagai pilihan kota
- Bisa diklik untuk filter destinasi berdasarkan kota

---

## 3. Daftar Slider

Mendapatkan semua gambar slider untuk carousel di home page.

### Endpoint
```
GET /api/sliders
```

### Apakah Perlu Login?
❌ Tidak, semua orang bisa melihat slider.

### Contoh Request

**URL:**
```
GET http://localhost:8000/api/sliders
```

**Header:**
```
Accept: application/json
```

### Response Sukses (200)

```json
{
  "success": true,
  "message": "Sliders retrieved successfully",
  "data": {
    "sliders": [
      "http://localhost:8000/Asset_Travelo/slider1.jpg",
      "http://localhost:8000/Asset_Travelo/slider2.jpg",
      "http://localhost:8000/Asset_Travelo/slider3.jpg"
    ]
  }
}
```

### Penjelasan Data

| Field | Tipe | Keterangan |
|-------|------|------------|
| sliders | array | Array berisi URL gambar slider (langsung array string) |

**Catatan:** Response langsung array string, bukan array object.

### Kegunaan
- Tampilkan di carousel/slider di bagian atas home page
- Bisa auto-slide dengan interval tertentu

---

## 4. Daftar Promo

Mendapatkan semua gambar promo yang aktif.

### Endpoint
```
GET /api/promos
```

### Apakah Perlu Login?
❌ Tidak, semua orang bisa melihat promo.

### Contoh Request

**URL:**
```
GET http://localhost:8000/api/promos
```

**Header:**
```
Accept: application/json
```

### Response Sukses (200)

```json
{
  "success": true,
  "message": "Promos retrieved successfully",
  "data": {
    "promos": [
      "http://localhost:8000/Asset_Travelo/promo1.jpg",
      "http://localhost:8000/Asset_Travelo/promo2.jpg",
      "http://localhost:8000/Asset_Travelo/promo3.jpg"
    ]
  }
}
```

### Penjelasan Data

| Field | Tipe | Keterangan |
|-------|------|------------|
| promos | array | Array berisi URL gambar promo (langsung array string) |

**Catatan:** Response langsung array string, bukan array object.

### Kegunaan
- Tampilkan di bagian promo di home page
- Bisa horizontal scroll atau grid

---

## Tips Penggunaan

### 1. Load Data Saat Home Page Dibuka

Saat user buka home page, ambil semua data sekaligus:
- Destinasi
- Kota
- Slider
- Promo

Bisa dilakukan secara paralel (simultaneous) untuk lebih cepat.

### 2. Caching Data

Simpan data di cache agar tidak perlu request ulang setiap kali buka home page. Update cache jika ada perubahan.

### 3. Handle Loading State

Tampilkan loading indicator saat data sedang diambil. Jangan biarkan halaman kosong.

### 4. Handle Error

Jika request gagal, tampilkan pesan error dan tombol retry.

### 5. URL Gambar

Semua URL gambar sudah lengkap (full URL), bisa langsung digunakan di Flutter:
```dart
Image.network(destination['imageUrl'])
```

---

## Contoh Kode Flutter Lengkap

### Load Daftar Destinasi

```dart
import 'dart:convert';
import '../services/api_service.dart';
import '../config/api_config.dart';

Future<List<dynamic>?> getDestinations() async {
  try {
    // 1. Panggil API destinations
    final response = await ApiService.get(ApiConfig.destinations);

    // 2. Decode response JSON
    final responseData = jsonDecode(response.body);

    // 3. Cek apakah sukses
    if (response.statusCode == 200 && responseData['success'] == true) {
      // 4. Ambil array destinations
      final destinations = responseData['data']['destinations'] as List;
      return destinations;
    } else {
      print('Gagal ambil destinasi: ${responseData['message']}');
      return null;
    }
  } catch (e) {
    print('Error get destinations: $e');
    return null;
  }
}

// Cara menggunakan:
Future<void> loadDestinations() async {
  final destinations = await getDestinations();
  if (destinations != null) {
    setState(() {
      _destinations = destinations;
    });
  }
}
```

### Load Daftar Kota

```dart
import 'dart:convert';
import '../services/api_service.dart';
import '../config/api_config.dart';

Future<List<dynamic>?> getCities() async {
  try {
    final response = await ApiService.get(ApiConfig.cities);
    final responseData = jsonDecode(response.body);

    if (response.statusCode == 200 && responseData['success'] == true) {
      final cities = responseData['data']['cities'] as List;
      return cities;
    } else {
      print('Gagal ambil kota: ${responseData['message']}');
      return null;
    }
  } catch (e) {
    print('Error get cities: $e');
    return null;
  }
}
```

### Load Daftar Slider

```dart
import 'dart:convert';
import '../services/api_service.dart';
import '../config/api_config.dart';

Future<List<String>?> getSliders() async {
  try {
    final response = await ApiService.get(ApiConfig.sliders);
    final responseData = jsonDecode(response.body);

    if (response.statusCode == 200 && responseData['success'] == true) {
      // Sliders adalah array of strings langsung
      final sliders = List<String>.from(responseData['data']['sliders']);
      return sliders;
    } else {
      print('Gagal ambil slider: ${responseData['message']}');
      return null;
    }
  } catch (e) {
    print('Error get sliders: $e');
    return null;
  }
}
```

### Load Daftar Promo

```dart
import 'dart:convert';
import '../services/api_service.dart';
import '../config/api_config.dart';

Future<List<String>?> getPromos() async {
  try {
    final response = await ApiService.get(ApiConfig.promos);
    final responseData = jsonDecode(response.body);

    if (response.statusCode == 200 && responseData['success'] == true) {
      // Promos adalah array of strings langsung
      final promos = List<String>.from(responseData['data']['promos']);
      return promos;
    } else {
      print('Gagal ambil promo: ${responseData['message']}');
      return null;
    }
  } catch (e) {
    print('Error get promos: $e');
    return null;
  }
}
```

### Load Semua Data Home Page Secara Bersamaan

```dart
import 'dart:convert';
import '../services/api_service.dart';
import '../config/api_config.dart';

class HomePageData {
  List<dynamic> destinations = [];
  List<dynamic> cities = [];
  List<String> sliders = [];
  List<String> promos = [];
  bool isLoading = true;
  String? error;
}

Future<HomePageData> loadHomePageData() async {
  HomePageData data = HomePageData();
  
  try {
    // Load semua data secara paralel (lebih cepat)
    final results = await Future.wait([
      ApiService.get(ApiConfig.destinations),
      ApiService.get(ApiConfig.cities),
      ApiService.get(ApiConfig.sliders),
      ApiService.get(ApiConfig.promos),
    ]);
    
    // Destinasi
    final destResponse = jsonDecode(results[0].body);
    if (destResponse['success'] == true) {
      data.destinations = destResponse['data']['destinations'] as List;
    }
    
    // Kota
    final cityResponse = jsonDecode(results[1].body);
    if (cityResponse['success'] == true) {
      data.cities = cityResponse['data']['cities'] as List;
    }
    
    // Slider
    final sliderResponse = jsonDecode(results[2].body);
    if (sliderResponse['success'] == true) {
      data.sliders = List<String>.from(sliderResponse['data']['sliders']);
    }
    
    // Promo
    final promoResponse = jsonDecode(results[3].body);
    if (promoResponse['success'] == true) {
      data.promos = List<String>.from(promoResponse['data']['promos']);
    }
    
    data.isLoading = false;
  } catch (e) {
    data.error = 'Gagal memuat data: $e';
    data.isLoading = false;
  }
  
  return data;
}
```

### Tampilkan di UI

```dart
class HomePage extends StatefulWidget {
  @override
  _HomePageState createState() => _HomePageState();
}

class _HomePageState extends State<HomePage> {
  HomePageData? homeData;
  
  @override
  void initState() {
    super.initState();
    _loadData();
  }
  
  Future<void> _loadData() async {
    setState(() {
      homeData = null; // Reset
    });
    
    var data = await loadHomePageData();
    
    setState(() {
      homeData = data;
    });
  }
  
  @override
  Widget build(BuildContext context) {
    if (homeData == null || homeData!.isLoading) {
      return Center(child: CircularProgressIndicator());
    }
    
    if (homeData!.error != null) {
      return Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Text(homeData!.error!),
            ElevatedButton(
              onPressed: _loadData,
              child: Text('Coba Lagi'),
            ),
          ],
        ),
      );
    }
    
    return RefreshIndicator(
      onRefresh: _loadData,
      child: ListView(
        children: [
          // Slider
          if (homeData!.sliders.isNotEmpty)
            SliderCarousel(images: homeData!.sliders),
          
          // Kota
          if (homeData!.cities.isNotEmpty)
            CitySection(cities: homeData!.cities),
          
          // Promo
          if (homeData!.promos.isNotEmpty)
            PromoSection(images: homeData!.promos),
          
          // Destinasi
          if (homeData!.destinations.isNotEmpty)
            DestinationList(destinations: homeData!.destinations),
        ],
      ),
    );
  }
}
```

---

## Skenario Penggunaan

### Skenario 1: User Buka Home Page

1. Tampilkan loading indicator
2. Ambil data destinasi, kota, slider, promo secara bersamaan
3. Setelah semua data diterima, tampilkan di UI
4. Jika ada error, tampilkan pesan error dan tombol retry

### Skenario 2: User Pull to Refresh

1. User tarik ke bawah untuk refresh
2. Ambil ulang semua data
3. Update UI dengan data terbaru

### Skenario 3: User Klik Destinasi

1. Ambil `id` dari destinasi yang diklik
2. Navigate ke halaman detail dengan `id` tersebut
3. Gunakan endpoint detail destinasi (lihat [DESTINATION_API.md](DESTINATION_API.md))

---

**Selanjutnya:** Baca [DESTINATION_API.md](DESTINATION_API.md) untuk detail paket wisata.

