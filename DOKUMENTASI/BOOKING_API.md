# Dokumentasi API - Booking dan Riwayat Pemesanan

Dokumentasi lengkap untuk membuat booking, melihat riwayat, dan cek status pembayaran.

---

## 📦 Import dan Setup

### Import yang Diperlukan

```dart
import 'dart:convert';                    // Untuk decode/encode JSON
import '../services/api_service.dart';   // Service untuk HTTP request
import '../config/api_config.dart';       // Konfigurasi endpoints
```

**Catatan:** Semua endpoint booking memerlukan login (token akan otomatis ditambahkan oleh ApiService).

---

## 1. Buat Booking

Membuat booking baru untuk sebuah paket wisata.

### Endpoint
```
POST /api/bookings
```

### Apakah Perlu Login?
✅ Ya, harus login dulu.

### Header yang Diperlukan

```
Authorization: Bearer {token}
Content-Type: application/json
Accept: application/json
```

### Data yang Dikirim

| Field | Tipe | Wajib? | Keterangan |
|-------|------|--------|------------|
| destination_id | text | ✅ Ya | ID destinasi yang akan dibooking |
| customer_name | text | ✅ Ya | Nama pelanggan (maksimal 255 karakter) |
| tanggal_keberangkatan | date | ✅ Ya | Tanggal keberangkatan (format: YYYY-MM-DD) |
| waktu_keberangkatan | text | ✅ Ya | Waktu keberangkatan (contoh: "08:00") |
| lokasi_penjemputan | text | ✅ Ya | Lokasi penjemputan: `bandara` atau `terminal` |
| metode_pembayaran | text | ✅ Ya | Metode pembayaran: `bank_transfer`, `e-wallet`, `credit_card`, `transfer`, `echannel` |

### Contoh Request

**URL:**
```
POST http://localhost:8000/api/bookings
```

**Header:**
```
Authorization: Bearer 1|abcdefghijklmnopqrstuvwxyz1234567890
Content-Type: application/json
Accept: application/json
```

**Body:**
```json
{
  "destination_id": "1",
  "customer_name": "Budi Santoso",
  "tanggal_keberangkatan": "2024-02-15",
  "waktu_keberangkatan": "08:00",
  "lokasi_penjemputan": "bandara",
  "metode_pembayaran": "bank_transfer"
}
```

### Response Sukses (201)

```json
{
  "success": true,
  "message": "Booking berhasil dibuat",
  "data": {
    "booking": {
      "id": "1",
      "userId": "1",
      "packageId": "1",
      "packageTitle": "Pantai Lombok",
      "packageImage": "http://localhost:8000/Asset_Travelo/lombok.jpeg",
      "price": 1000000,
      "totalHarga": 1000000,
      "departureDate": "2024-02-15",
      "tanggalKeberangkatan": "2024-02-15",
      "pickupTime": "08:00",
      "waktuKeberangkatan": "08:00",
      "lokasiPenjemputan": "bandara",
      "paymentMethod": "bank_transfer",
      "metodePembayaran": "bank_transfer",
      "status": "Menunggu Pembayaran",
      "paymentStatus": "pending",
      "bookingDate": "2024-01-15",
      "tanggalBooking": "2024-01-15",
      "kodeBooking": "BOOK-ORDER-1705315200-1",
      "orderId": "ORDER-1705315200-1",
      "snapToken": "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx",
      "paymentInfo": "Silakan lakukan pembayaran melalui Midtrans"
    }
  }
}
```

### Penjelasan Data Response

| Field | Tipe | Keterangan |
|-------|------|------------|
| id | text | ID booking |
| userId | text | ID user yang membuat booking |
| packageId | text | ID destinasi |
| packageTitle | text | Judul paket |
| packageImage | text | URL gambar paket |
| price | angka | Harga paket |
| totalHarga | angka | Total harga (sama dengan price) |
| departureDate | text | Tanggal keberangkatan |
| tanggalKeberangkatan | text | Tanggal keberangkatan (alias) |
| pickupTime | text | Waktu keberangkatan |
| waktuKeberangkatan | text | Waktu keberangkatan (alias) |
| lokasiPenjemputan | text | Lokasi penjemputan (`bandara` atau `terminal`) |
| paymentMethod | text | Metode pembayaran |
| metodePembayaran | text | Metode pembayaran (alias) |
| status | text | Status booking ("Menunggu Pembayaran", "Dikonfirmasi", "Dibatalkan") |
| paymentStatus | text | Status pembayaran ("pending", "paid", "failed") |
| bookingDate | text | Tanggal booking dibuat |
| tanggalBooking | text | Tanggal booking (alias) |
| kodeBooking | text | Kode booking unik |
| orderId | text | Midtrans order ID |
| **snapToken** | text | **Token untuk pembayaran Midtrans (PENTING!)** |
| paymentInfo | text | Informasi pembayaran |

**Yang Paling Penting:**
- **`snapToken`**: Token ini digunakan untuk membuka halaman pembayaran Midtrans
- Simpan semua data booking untuk ditampilkan di halaman pembayaran

### Response Error (422) - Data Tidak Valid

```json
{
  "success": false,
  "message": "Validasi gagal",
  "errors": {
    "destination_id": ["Destinasi wajib dipilih."],
    "tanggal_keberangkatan": ["Tanggal keberangkatan wajib diisi."],
    "metode_pembayaran": ["Metode pembayaran tidak valid."]
  }
}
```

**Kemungkinan Error:**
- `destination_id` tidak ada di database
- Format tanggal salah (harus YYYY-MM-DD)
- Metode pembayaran tidak valid (harus salah satu: `bank_transfer`, `e-wallet`, `credit_card`, `transfer`, `echannel`)

### Response Error (401) - Belum Login

```json
{
  "success": false,
  "message": "Unauthenticated"
}
```

### Response Error (500) - Server Error

```json
{
  "success": false,
  "message": "Terjadi kesalahan saat membuat booking",
  "error": "Pesan error detail"
}
```

---

## 2. Daftar Booking (Riwayat Pemesanan)

Mendapatkan semua booking milik user yang sedang login.

### Endpoint
```
GET /api/bookings
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
GET http://localhost:8000/api/bookings
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
  "message": "Bookings retrieved successfully",
  "data": {
    "bookings": [
      {
        "id": "1",
        "kodeBooking": "BOOK-ORDER-1705315200-1",
        "destination": {
          "id": "1",
          "title": "Pantai Lombok",
          "imageUrl": "http://localhost:8000/Asset_Travelo/lombok.jpeg"
        },
        "tanggalBooking": "2024-01-15",
        "tanggalKeberangkatan": "2024-02-15",
        "waktuKeberangkatan": "08:00",
        "totalHarga": 1000000,
        "status": "Menunggu Pembayaran",
        "paymentStatus": "pending",
        "metodePembayaran": "bank_transfer"
      },
      {
        "id": "2",
        "kodeBooking": "BOOK-ORDER-1705401600-1",
        "destination": {
          "id": "2",
          "title": "Yogyakarta Heritage",
          "imageUrl": "http://localhost:8000/Asset_Travelo/Yogya.jpg"
        },
        "tanggalBooking": "2024-01-16",
        "tanggalKeberangkatan": "2024-02-20",
        "waktuKeberangkatan": "09:00",
        "totalHarga": 750000,
        "status": "Dikonfirmasi",
        "paymentStatus": "paid",
        "metodePembayaran": "e-wallet"
      }
    ]
  }
}
```

### Penjelasan Data

| Field | Tipe | Keterangan |
|-------|------|------------|
| id | text | ID booking |
| kodeBooking | text | Kode booking unik |
| destination | object | Data destinasi |
| destination.id | text | ID destinasi |
| destination.title | text | Judul paket |
| destination.imageUrl | text | URL gambar paket |
| tanggalBooking | text | Tanggal booking dibuat |
| tanggalKeberangkatan | text | Tanggal keberangkatan |
| waktuKeberangkatan | text | Waktu keberangkatan |
| totalHarga | angka | Total harga |
| status | text | Status booking |
| paymentStatus | text | Status pembayaran |
| metodePembayaran | text | Metode pembayaran |

**Status Booking:**
- `"Menunggu Pembayaran"`: Booking dibuat, belum bayar
- `"Dikonfirmasi"`: Sudah bayar, booking dikonfirmasi
- `"Dibatalkan"`: Booking dibatalkan

**Status Pembayaran:**
- `"pending"`: Belum dibayar
- `"paid"`: Sudah dibayar
- `"failed"`: Pembayaran gagal

### Kegunaan
- Tampilkan di halaman riwayat pemesanan
- User bisa lihat semua booking yang pernah dibuat
- Bisa klik untuk lihat detail atau lanjutkan pembayaran

---

## 3. Detail Booking

Mendapatkan detail lengkap sebuah booking.

### Endpoint
```
GET /api/bookings/{id}
```

**Contoh:** `GET /api/bookings/1`

### Apakah Perlu Login?
✅ Ya, harus login dulu.

### Header yang Diperlukan

```
Authorization: Bearer {token}
Accept: application/json
```

### Parameter URL

| Parameter | Tipe | Wajib? | Keterangan |
|-----------|------|--------|------------|
| id | text | ✅ Ya | ID booking |

### Contoh Request

**URL:**
```
GET http://localhost:8000/api/bookings/1
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
  "message": "Booking retrieved successfully",
  "data": {
    "booking": {
      "id": "1",
      "kodeBooking": "BOOK-ORDER-1705315200-1",
      "orderId": "ORDER-1705315200-1",
      "snapToken": "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx",
      "destination": {
        "id": "1",
        "title": "Pantai Lombok",
        "imageUrl": "http://localhost:8000/Asset_Travelo/lombok.jpeg",
        "price": 1000000
      },
      "tanggalBooking": "2024-01-15",
      "tanggalKeberangkatan": "2024-02-15",
      "waktuKeberangkatan": "08:00",
      "totalHarga": 1000000,
      "status": "Menunggu Pembayaran",
      "paymentStatus": "pending",
      "metodePembayaran": "bank_transfer"
    }
  }
}
```

**Catatan:** Response ini juga berisi `snapToken` yang bisa digunakan untuk pembayaran jika belum dibayar.

### Response Error (404) - Booking Tidak Ditemukan

```json
{
  "success": false,
  "message": "Booking tidak ditemukan"
}
```

---

## 4. Cek Status Pembayaran

Mengecek status pembayaran sebuah booking.

### Endpoint
```
GET /api/bookings/{id}/status
```

**Contoh:** `GET /api/bookings/1/status`

### Apakah Perlu Login?
✅ Ya, harus login dulu.

### Header yang Diperlukan

```
Authorization: Bearer {token}
Accept: application/json
```

### Parameter URL

| Parameter | Tipe | Wajib? | Keterangan |
|-----------|------|--------|------------|
| id | text | ✅ Ya | ID booking |

### Contoh Request

**URL:**
```
GET http://localhost:8000/api/bookings/1/status
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
  "message": "Status retrieved successfully",
  "data": {
    "booking": {
      "id": "1",
      "status": "Dikonfirmasi",
      "paymentStatus": "paid"
    },
    "transaction": {
      "transaction_status": "settlement",
      "order_id": "ORDER-1705315200-1",
      "gross_amount": "1000000",
      "transaction_time": "2024-01-15 10:00:00"
    }
  }
}
```

### Status Transaksi Midtrans

| Status | Arti |
|--------|------|
| `settlement` | Pembayaran berhasil |
| `capture` | Pembayaran berhasil |
| `pending` | Menunggu pembayaran |
| `deny` | Pembayaran ditolak |
| `expire` | Pembayaran kadaluarsa |
| `cancel` | Pembayaran dibatalkan |

**Kegunaan:**
- Cek status pembayaran setelah user kembali dari halaman pembayaran
- Update status booking di aplikasi
- Refresh halaman riwayat setelah pembayaran

---

## Tips Penggunaan

### 1. Flow Pembayaran

1. User buat booking → dapat `snapToken`
2. Buka halaman pembayaran Midtrans dengan `snapToken`
3. User bayar di Midtrans
4. Setelah kembali ke aplikasi, cek status dengan endpoint `/bookings/{id}/status`
5. Update UI berdasarkan status terbaru

### 2. Format Tanggal

Tanggal dari server format `YYYY-MM-DD`, format untuk ditampilkan:

```dart
String formatDate(String dateStr) {
  try {
    DateTime date = DateTime.parse(dateStr);
    return DateFormat('d MMMM y', 'id_ID').format(date);
    // Contoh: "15 Februari 2024"
  } catch (e) {
    return dateStr;
  }
}
```

### 3. Tampilkan Status dengan Badge

```dart
Widget buildStatusBadge(String status) {
  Color color;
  IconData icon;
  
  switch (status) {
    case 'Dikonfirmasi':
      color = Colors.green;
      icon = Icons.check_circle;
      break;
    case 'Menunggu Pembayaran':
      color = Colors.orange;
      icon = Icons.pending;
      break;
    case 'Dibatalkan':
      color = Colors.red;
      icon = Icons.cancel;
      break;
    default:
      color = Colors.grey;
      icon = Icons.help;
  }
  
  return Container(
    padding: EdgeInsets.symmetric(horizontal: 12, vertical: 6),
    decoration: BoxDecoration(
      color: color.withOpacity(0.1),
      borderRadius: BorderRadius.circular(20),
      border: Border.all(color: color),
    ),
    child: Row(
      mainAxisSize: MainAxisSize.min,
      children: [
        Icon(icon, size: 16, color: color),
        SizedBox(width: 4),
        Text(
          status,
          style: TextStyle(color: color, fontWeight: FontWeight.bold),
        ),
      ],
    ),
  );
}
```

### 4. Refresh Data Setelah Pembayaran

Setelah user kembali dari halaman pembayaran, refresh data booking:

```dart
Future<void> refreshBooking(String bookingId) async {
  // Cek status terbaru
  var statusResponse = await ApiService.get(
    '/bookings/$bookingId/status',
    token: token,
  );
  
  if (statusResponse['success']) {
    // Update data booking di aplikasi
    // Refresh halaman riwayat
    await loadBookings();
  }
}
```

---

## Contoh Kode Flutter Lengkap

### Buat Booking

```dart
import 'dart:convert';
import '../services/api_service.dart';
import '../config/api_config.dart';

Future<Map<String, dynamic>?> createBooking({
  required String destinationId,
  required String tanggalKeberangkatan,
  required String waktuKeberangkatan,
  required String metodePembayaran,
}) async {
  try {
    // 1. Panggil API create booking
    final response = await ApiService.post(
      ApiConfig.bookings,
      body: {
        'destination_id': destinationId,
        'tanggal_keberangkatan': tanggalKeberangkatan,
        'waktu_keberangkatan': waktuKeberangkatan,
        'metode_pembayaran': metodePembayaran,
      },
    );

    // 2. Decode response JSON
    final responseData = jsonDecode(response.body);

    // 3. Cek apakah sukses
    if (response.statusCode == 201 && responseData['success'] == true) {
      // 4. Ambil data booking
      final booking = responseData['data']['booking'];
      
      print('Booking berhasil dibuat!');
      print('Kode Booking: ${booking['kodeBooking']}');
      print('Snap Token: ${booking['snapToken']}');
      
      return booking;
    } else {
      // Handle error
      print('Gagal buat booking: ${responseData['message']}');
      
      if (responseData['errors'] != null) {
        final errors = responseData['errors'] as Map<String, dynamic>;
        errors.forEach((field, messages) {
          print('$field: ${messages.join(", ")}');
        });
      }
      
      return null;
    }
  } catch (e) {
    print('Error create booking: $e');
    return null;
  }
}

// Cara menggunakan:
Future<void> _handleBooking() async {
  // Tampilkan loading
  showDialog(
    context: context,
    barrierDismissible: false,
    builder: (context) => Center(child: CircularProgressIndicator()),
  );

  final booking = await createBooking(
    destinationId: '1',
    customerName: 'Budi Santoso',
    tanggalKeberangkatan: '2024-02-15',
    waktuKeberangkatan: '08:00',
    lokasiPenjemputan: 'bandara',
    metodePembayaran: 'bank_transfer',
  );

  // Tutup loading
  Navigator.pop(context);

  if (booking != null) {
    // Booking berhasil, buka halaman pembayaran
    Navigator.push(
      context,
      MaterialPageRoute(
        builder: (context) => PaymentPage(
          booking: booking,
          snapToken: booking['snapToken'],
        ),
      ),
    );
  } else {
    // Tampilkan error
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(content: Text('Gagal membuat booking')),
    );
  }
}
```

### Ambil Daftar Booking (Riwayat Pemesanan)

```dart
import 'dart:convert';
import '../services/api_service.dart';
import '../config/api_config.dart';

Future<List<dynamic>?> getBookings() async {
  try {
    // 1. Panggil API bookings (token otomatis ditambahkan)
    final response = await ApiService.get(ApiConfig.bookings);

    // 2. Decode response JSON
    final responseData = jsonDecode(response.body);

    // 3. Cek apakah sukses
    if (response.statusCode == 200 && responseData['success'] == true) {
      // 4. Ambil array bookings
      final bookings = responseData['data']['bookings'] as List;
      return bookings;
    } else {
      print('Gagal ambil bookings: ${responseData['message']}');
      return null;
    }
  } catch (e) {
    print('Error get bookings: $e');
    return null;
  }
}

// Cara menggunakan:
Future<void> loadBookings() async {
  final bookings = await getBookings();
  if (bookings != null) {
    setState(() {
      _bookings = bookings;
    });
  }
}
```

### Ambil Detail Booking

```dart
import 'dart:convert';
import '../services/api_service.dart';
import '../config/api_config.dart';

Future<Map<String, dynamic>?> getBookingDetail(String bookingId) async {
  try {
    // 1. Panggil API booking detail
    final response = await ApiService.get(
      ApiConfig.bookingById(bookingId),
    );

    // 2. Decode response JSON
    final responseData = jsonDecode(response.body);

    // 3. Cek apakah sukses
    if (response.statusCode == 200 && responseData['success'] == true) {
      // 4. Ambil data booking
      final booking = responseData['data']['booking'];
      return booking;
    } else {
      print('Gagal ambil detail booking: ${responseData['message']}');
      return null;
    }
  } catch (e) {
    print('Error get booking detail: $e');
    return null;
  }
}
```

### Cek Status Pembayaran

```dart
import 'dart:convert';
import '../services/api_service.dart';
import '../config/api_config.dart';

Future<Map<String, dynamic>?> checkPaymentStatus(String bookingId) async {
  try {
    // 1. Panggil API check status
    final response = await ApiService.get(
      ApiConfig.bookingStatus(bookingId),
    );

    // 2. Decode response JSON
    final responseData = jsonDecode(response.body);

    // 3. Cek apakah sukses
    if (response.statusCode == 200 && responseData['success'] == true) {
      // 4. Ambil data status
      final statusData = responseData['data'];
      
      print('Status Booking: ${statusData['booking']['status']}');
      print('Payment Status: ${statusData['booking']['paymentStatus']}');
      
      return statusData;
    } else {
      print('Gagal cek status: ${responseData['message']}');
      return null;
    }
  } catch (e) {
    print('Error check payment status: $e');
    return null;
  }
}

// Cara menggunakan setelah user kembali dari pembayaran:
Future<void> refreshBookingStatus(String bookingId) async {
  final statusData = await checkPaymentStatus(bookingId);
  
  if (statusData != null) {
    final bookingStatus = statusData['booking']['status'];
    final paymentStatus = statusData['booking']['paymentStatus'];
    
    setState(() {
      _bookingStatus = bookingStatus;
      _paymentStatus = paymentStatus;
    });
    
    // Jika sudah dibayar, refresh daftar booking
    if (paymentStatus == 'paid') {
      await loadBookings();
    }
  }
}
```

---

## Skenario Penggunaan

### Skenario 1: User Buat Booking

1. User pilih paket wisata
2. User isi form booking (tanggal, waktu, metode pembayaran)
3. Submit booking
4. Dapat `snapToken`
5. Buka halaman pembayaran Midtrans dengan `snapToken`

### Skenario 2: User Lihat Riwayat

1. User buka halaman riwayat pemesanan
2. Ambil daftar booking
3. Tampilkan semua booking dengan status
4. User bisa klik untuk lihat detail atau lanjutkan pembayaran

### Skenario 3: User Cek Status Setelah Pembayaran

1. User kembali dari halaman pembayaran Midtrans
2. Cek status pembayaran dengan endpoint `/bookings/{id}/status`
3. Update status di aplikasi
4. Refresh halaman riwayat

---

**Selesai!** Semua dokumentasi API sudah lengkap. Selamat coding! 🚀

