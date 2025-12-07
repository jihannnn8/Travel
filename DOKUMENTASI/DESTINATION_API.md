# Dokumentasi API - Detail Destinasi

Dokumentasi lengkap untuk melihat detail lengkap sebuah paket wisata.

---

## 📦 Import dan Setup

### Import yang Diperlukan

```dart
import 'dart:convert';                    // Untuk decode JSON
import '../services/api_service.dart';   // Service untuk HTTP request
import '../config/api_config.dart';       // Konfigurasi endpoints
```

**Catatan:** Endpoint detail destinasi tidak memerlukan login (public).

---

## 1. Detail Destinasi

Mendapatkan informasi lengkap sebuah paket wisata berdasarkan ID.

### Endpoint
```
GET /api/destinations/{id}
```

**Contoh:** `GET /api/destinations/1`

### Apakah Perlu Login?
❌ Tidak, semua orang bisa melihat detail destinasi.

### Parameter URL

| Parameter | Tipe | Wajib? | Keterangan |
|-----------|------|--------|------------|
| id | text | ✅ Ya | ID destinasi (dari daftar destinasi) |

### Contoh Request

**URL:**
```
GET http://localhost:8000/api/destinations/1
```

**Header:**
```
Accept: application/json
```

### Response Sukses (200)

```json
{
  "success": true,
  "message": "Destination retrieved successfully",
  "data": {
    "destination": {
      "id": "1",
      "title": "Pantai Lombok",
      "description": "Nikmati keindahan pantai Lombok dengan pemandangan yang menakjubkan. Paket ini termasuk akomodasi, transportasi, dan tour guide profesional.",
      "imageUrl": "http://localhost:8000/Asset_Travelo/lombok.jpeg",
      "price": 1000000,
      "duration": "2 Hari 3 Malam",
      "departureDate": "15 January 2024",
      "rating": 4.8,
      "totalRatings": 120,
      "rundown": [
        "Hari 1: Kedatangan di Lombok, check-in hotel, welcome dinner",
        "Hari 2: Tour ke Pantai Kuta, Pantai Tanjung Aan, sunset viewing",
        "Hari 3: Tour ke Gili Trawangan, snorkeling, island hopping",
        "Hari 4: Free time, check-out hotel, transfer ke airport"
      ],
      "destination": "Lombok, NTB"
    }
  }
}
```

### Penjelasan Data

| Field | Tipe | Keterangan |
|-------|------|------------|
| id | text | ID destinasi |
| title | text | Judul paket wisata |
| description | text | Deskripsi lengkap paket |
| imageUrl | text | URL gambar (sudah lengkap, bisa langsung pakai) |
| price | angka | Harga paket (dalam rupiah) |
| duration | text | Durasi paket (contoh: "2 Hari 3 Malam") |
| departureDate | text | Tanggal keberangkatan (format: "15 January 2024") |
| rating | angka | Rating paket (0-5) |
| totalRatings | angka | Jumlah orang yang memberi rating |
| rundown | array | Daftar itinerary lengkap (jadwal per hari) |
| destination | text | Lokasi destinasi |

### Kegunaan
- Tampilkan di halaman detail paket wisata
- User bisa lihat informasi lengkap sebelum booking
- Tampilkan harga, rating, dan itinerary

---

## Response Error (404) - Destinasi Tidak Ditemukan

```json
{
  "success": false,
  "message": "Destination not found"
}
```

**Kemungkinan Penyebab:**
- ID destinasi tidak ada di database
- ID yang dikirim salah/typo
- Destinasi sudah dihapus

**Yang Harus Dilakukan:**
- Tampilkan pesan "Paket wisata tidak ditemukan"
- Kembalikan user ke halaman sebelumnya atau daftar destinasi

---

## Tips Penggunaan

### 1. Ambil ID dari Daftar Destinasi

Saat user klik destinasi di home page, ambil `id` dari data destinasi tersebut:

```dart
// Di home page
onTap: () {
  Navigator.push(
    context,
    MaterialPageRoute(
      builder: (context) => PackageDetailPage(
        destinationId: destination['id'],
      ),
    ),
  );
}
```

### 2. Load Data Saat Halaman Dibuka

Saat halaman detail dibuka, langsung ambil data:

```dart
@override
void initState() {
  super.initState();
  _loadDestination();
}
```

### 3. Tampilkan Loading State

Saat data sedang diambil, tampilkan loading indicator:

```dart
if (isLoading) {
  return Center(child: CircularProgressIndicator());
}
```

### 4. Handle Error dengan Baik

Jika destinasi tidak ditemukan atau error, tampilkan pesan yang jelas:

```dart
if (error != null) {
  return Center(
    child: Column(
      mainAxisAlignment: MainAxisAlignment.center,
      children: [
        Icon(Icons.error_outline, size: 64, color: Colors.red),
        SizedBox(height: 16),
        Text('Paket wisata tidak ditemukan'),
        SizedBox(height: 16),
        ElevatedButton(
          onPressed: () => Navigator.pop(context),
          child: Text('Kembali'),
        ),
      ],
    ),
  );
}
```

### 5. Format Harga

Format harga dengan separator ribuan:

```dart
String formatPrice(double price) {
  return NumberFormat.currency(
    locale: 'id_ID',
    symbol: 'Rp ',
    decimalDigits: 0,
  ).format(price);
}

// Contoh: 1000000 -> "Rp 1.000.000"
```

### 6. Format Tanggal

Tanggal dari server format "15 January 2024", bisa ditampilkan langsung atau di-parse untuk format lain.

### 7. Tampilkan Rating

Tampilkan rating dengan bintang:

```dart
Row(
  children: [
    Icon(Icons.star, color: Colors.amber, size: 20),
    SizedBox(width: 4),
    Text('${destination['rating']}'),
    SizedBox(width: 8),
    Text('(${destination['totalRatings']} ulasan)'),
  ],
)
```

---

## Contoh Kode Flutter

### Load Detail Destinasi

```dart
import 'dart:convert';
import '../services/api_service.dart';
import '../config/api_config.dart';

Future<Map<String, dynamic>?> getDestinationDetail(String destinationId) async {
  try {
    // 1. Panggil API destination detail
    final response = await ApiService.get(
      ApiConfig.destinationById(destinationId),
    );

    // 2. Decode response JSON
    final responseData = jsonDecode(response.body);

    // 3. Cek apakah sukses
    if (response.statusCode == 200 && responseData['success'] == true) {
      // 4. Ambil data destination
      final destination = responseData['data']['destination'];
      return destination;
    } else {
      print('Gagal ambil detail destinasi: ${responseData['message']}');
      return null;
    }
  } catch (e) {
    print('Error get destination detail: $e');
    return null;
  }
}

// Cara menggunakan dengan model class:
class DestinationDetail {
  String? id;
  String? title;
  String? description;
  String? imageUrl;
  double? price;
  String? duration;
  String? departureDate;
  double? rating;
  int? totalRatings;
  List<String> rundown = [];
  String? destination;
  bool isLoading = true;
  String? error;
}

Future<DestinationDetail> loadDestinationDetail(String id) async {
  DestinationDetail detail = DestinationDetail();
  
  try {
    // 1. Panggil API
    final response = await ApiService.get(
      ApiConfig.destinationById(id),
    );
    
    // 2. Decode response
    final responseData = jsonDecode(response.body);
    
    // 3. Cek sukses
    if (response.statusCode == 200 && responseData['success'] == true) {
      // 4. Parse data
      final data = responseData['data']['destination'];
      
      detail.id = data['id']?.toString();
      detail.title = data['title'];
      detail.description = data['description'];
      detail.imageUrl = data['imageUrl'];
      detail.price = (data['price'] is num) 
          ? data['price'].toDouble() 
          : double.tryParse(data['price']?.toString() ?? '0') ?? 0.0;
      detail.duration = data['duration'];
      detail.departureDate = data['departureDate'];
      detail.rating = (data['rating'] is num) 
          ? data['rating'].toDouble() 
          : double.tryParse(data['rating']?.toString() ?? '0') ?? 0.0;
      detail.totalRatings = data['totalRatings'] ?? 0;
      detail.rundown = List<String>.from(data['rundown'] ?? []);
      detail.destination = data['destination'];
      
      detail.isLoading = false;
    } else {
      detail.error = responseData['message'] ?? 'Gagal memuat detail destinasi';
      detail.isLoading = false;
    }
  } catch (e) {
    detail.error = 'Error: $e';
    detail.isLoading = false;
  }
  
  return detail;
}
```

### Tampilkan di UI

```dart
class PackageDetailPage extends StatefulWidget {
  final String destinationId;
  
  PackageDetailPage({required this.destinationId});
  
  @override
  _PackageDetailPageState createState() => _PackageDetailPageState();
}

class _PackageDetailPageState extends State<PackageDetailPage> {
  DestinationDetail? detail;
  
  @override
  void initState() {
    super.initState();
    _loadDetail();
  }
  
  Future<void> _loadDetail() async {
    var data = await loadDestinationDetail(widget.destinationId);
    
    setState(() {
      detail = data;
    });
  }
  
  @override
  Widget build(BuildContext context) {
    if (detail == null || detail!.isLoading) {
      return Scaffold(
        appBar: AppBar(title: Text('Detail Paket')),
        body: Center(child: CircularProgressIndicator()),
      );
    }
    
    if (detail!.error != null) {
      return Scaffold(
        appBar: AppBar(title: Text('Detail Paket')),
        body: Center(
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              Icon(Icons.error_outline, size: 64, color: Colors.red),
              SizedBox(height: 16),
              Text(detail!.error!),
              SizedBox(height: 16),
              ElevatedButton(
                onPressed: () => Navigator.pop(context),
                child: Text('Kembali'),
              ),
            ],
          ),
        ),
      );
    }
    
    return Scaffold(
      appBar: AppBar(title: Text(detail!.title ?? 'Detail Paket')),
      body: SingleChildScrollView(
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Gambar
            if (detail!.imageUrl != null && detail!.imageUrl!.isNotEmpty)
              Image.network(
                detail!.imageUrl!,
                width: double.infinity,
                height: 250,
                fit: BoxFit.cover,
              ),
            
            Padding(
              padding: EdgeInsets.all(16),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  // Judul
                  Text(
                    detail!.title ?? '',
                    style: TextStyle(
                      fontSize: 24,
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                  
                  SizedBox(height: 8),
                  
                  // Rating
                  Row(
                    children: [
                      Icon(Icons.star, color: Colors.amber, size: 20),
                      SizedBox(width: 4),
                      Text('${detail!.rating}'),
                      SizedBox(width: 8),
                      Text('(${detail!.totalRatings} ulasan)'),
                    ],
                  ),
                  
                  SizedBox(height: 16),
                  
                  // Harga
                  Text(
                    formatPrice(detail!.price ?? 0),
                    style: TextStyle(
                      fontSize: 28,
                      fontWeight: FontWeight.bold,
                      color: Colors.blue,
                    ),
                  ),
                  
                  SizedBox(height: 16),
                  
                  // Info
                  _buildInfoRow(Icons.access_time, 'Durasi', detail!.duration ?? ''),
                  _buildInfoRow(Icons.calendar_today, 'Keberangkatan', detail!.departureDate ?? ''),
                  _buildInfoRow(Icons.location_on, 'Lokasi', detail!.destination ?? ''),
                  
                  SizedBox(height: 24),
                  
                  // Deskripsi
                  Text(
                    'Deskripsi',
                    style: TextStyle(
                      fontSize: 20,
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                  SizedBox(height: 8),
                  Text(detail!.description ?? ''),
                  
                  SizedBox(height: 24),
                  
                  // Itinerary
                  Text(
                    'Itinerary',
                    style: TextStyle(
                      fontSize: 20,
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                  SizedBox(height: 8),
                  ...detail!.rundown.map((item) => Padding(
                    padding: EdgeInsets.only(bottom: 8),
                    child: Row(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Icon(Icons.check_circle, color: Colors.green, size: 20),
                        SizedBox(width: 8),
                        Expanded(child: Text(item)),
                      ],
                    ),
                  )),
                  
                  SizedBox(height: 32),
                  
                  // Tombol Booking
                  SizedBox(
                    width: double.infinity,
                    child: ElevatedButton(
                      onPressed: () {
                        // Navigate ke halaman booking
                        Navigator.push(
                          context,
                          MaterialPageRoute(
                            builder: (context) => BookingPage(
                              destinationId: detail!.id!,
                              destinationTitle: detail!.title!,
                              price: detail!.price!,
                            ),
                          ),
                        );
                      },
                      style: ElevatedButton.styleFrom(
                        padding: EdgeInsets.symmetric(vertical: 16),
                      ),
                      child: Text('Pesan Sekarang'),
                    ),
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }
  
  Widget _buildInfoRow(IconData icon, String label, String value) {
    return Padding(
      padding: EdgeInsets.only(bottom: 12),
      child: Row(
        children: [
          Icon(icon, size: 20, color: Colors.grey),
          SizedBox(width: 8),
          Text('$label: '),
          Text(
            value,
            style: TextStyle(fontWeight: FontWeight.bold),
          ),
        ],
      ),
    );
  }
}
```

---

## Skenario Penggunaan

### Skenario 1: User Klik Destinasi di Home

1. User klik salah satu destinasi di home page
2. Ambil `id` dari destinasi yang diklik
3. Navigate ke halaman detail dengan `id`
4. Load data detail destinasi
5. Tampilkan informasi lengkap

### Skenario 2: User Klik Tombol "Pesan Sekarang"

1. User klik tombol "Pesan Sekarang" di halaman detail
2. Bawa data yang diperlukan (id, title, price) ke halaman booking
3. User isi form booking
4. Submit booking (lihat [BOOKING_API.md](BOOKING_API.md))

---

**Selanjutnya:** Baca [BOOKING_API.md](BOOKING_API.md) untuk fitur booking.

