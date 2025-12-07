# Dokumentasi Sistem Booking

Dokumentasi lengkap untuk sistem booking/pemesanan paket wisata, termasuk API endpoints, model Flutter, service, dan implementasi UI.

---

## 📋 Daftar Isi

1. [Overview](#overview)
2. [API Endpoints](#api-endpoints)
3. [Model Booking (Flutter)](#model-booking-flutter)
4. [BookingService (Flutter)](#bookingservice-flutter)
5. [Order History Page](#order-history-page)
6. [Flow Booking](#flow-booking)
7. [Status Booking](#status-booking)
8. [Error Handling](#error-handling)
9. [Troubleshooting](#troubleshooting)

---

## Overview

Sistem booking memungkinkan user untuk:
- Membuat pemesanan paket wisata
- Melihat riwayat pemesanan
- Melakukan pembayaran via Midtrans
- Melihat detail pemesanan lengkap
- Membatalkan pemesanan (jika sudah dikonfirmasi)

*Catatan Penting:*
- ✅ Semua endpoint booking *MEMERLUKAN AUTHENTICATION* (harus login)
- ✅ Menggunakan Laravel Sanctum untuk authentication
- ✅ Terintegrasi dengan Midtrans untuk payment gateway
- ✅ Support offline storage dengan SharedPreferences

---

## API Endpoints

### 1. Create Booking

Membuat pemesanan baru dan mendapatkan snap token untuk pembayaran.

#### Endpoint

POST /api/bookings


#### Apakah Perlu Login?
✅ *Ya*, wajib login (menggunakan Sanctum token).

#### Headers

Authorization: Bearer {token}
Accept: application/json
Content-Type: application/json


#### Request Body

| Field | Tipe | Wajib? | Keterangan |
|-------|------|--------|------------|
| destination_id | integer | ✅ Ya | ID destinasi/paket yang dipesan |
| customer_name | string | ✅ Ya | Nama pemesan (maks 255 karakter) |
| tanggal_keberangkatan | date | ✅ Ya | Tanggal keberangkatan (format: YYYY-MM-DD) |
| waktu_keberangkatan | string | ✅ Ya | Waktu penjemputan (contoh: "08:00", "14:30") |
| lokasi_penjemputan | string | ✅ Ya | Lokasi penjemputan: bandara atau terminal |
| metode_pembayaran | string | ✅ Ya | Metode pembayaran: transfer, e-wallet, credit_card, bank_transfer, atau echannel |

#### Contoh Request

json
{
  "destination_id": 1,
  "customer_name": "John Doe",
  "tanggal_keberangkatan": "2024-02-15",
  "waktu_keberangkatan": "08:00",
  "lokasi_penjemputan": "bandara",
  "metode_pembayaran": "bank_transfer"
}


#### Response Sukses (200/201)

json
{
  "success": true,
  "message": "Booking created successfully",
  "data": {
    "booking": {
      "id": "1",
      "userId": "1",
      "packageId": "1",
      "packageTitle": "Pantai Lombok",
      "packageImage": "http://localhost:8000/api/asset/images/lombok.jpeg",
      "price": 1000000,
      "departureDate": "2024-02-15",
      "pickupTime": "08:00",
      "customerName": "John Doe",
      "lokasiPenjemputan": "bandara",
      "paymentMethod": "bank_transfer",
      "status": "Menunggu Pembayaran",
      "bookingDate": "2024-01-20T10:30:00.000000Z",
      "paymentInfo": "",
      "kodeBooking": "BOOK-ORDER-1705743000-1",
      "orderId": "ORDER-1705743000-1",
      "snapToken": "abc123xyz...",
      "paymentStatus": "pending"
    }
  }
}


#### Response Error (422 - Validation Error)

json
{
  "success": false,
  "message": "Validasi gagal",
  "errors": {
    "destination_id": ["Destinasi wajib dipilih."],
    "customer_name": ["Nama pelanggan wajib diisi."],
    "tanggal_keberangkatan": ["Tanggal keberangkatan wajib diisi."]
  }
}


#### Penjelasan Response

| Field | Tipe | Keterangan |
|-------|------|------------|
| id | string | ID booking |
| userId | string | ID user yang membuat booking |
| packageId | string | ID destinasi/paket |
| packageTitle | string | Judul paket wisata |
| packageImage | string | URL gambar paket (sudah diformat) |
| price | number | Total harga paket |
| departureDate | string | Tanggal keberangkatan |
| pickupTime | string | Waktu penjemputan |
| customerName | string | Nama pemesan |
| lokasiPenjemputan | string | Lokasi penjemputan (bandara/terminal) |
| paymentMethod | string | Metode pembayaran |
| status | string | Status booking (lihat [Status Booking](#status-booking)) |
| bookingDate | string | Tanggal pemesanan (ISO 8601) |
| kodeBooking | string | Kode booking unik |
| orderId | string | Order ID dari Midtrans |
| snapToken | string | Token untuk Midtrans Snap (untuk payment) |
| paymentStatus | string | Status pembayaran dari Midtrans |

---

### 2. Get All Bookings

Mendapatkan daftar semua pemesanan milik user yang sedang login.

#### Endpoint

GET /api/bookings


#### Apakah Perlu Login?
✅ *Ya*, wajib login.

#### Headers

Authorization: Bearer {token}
Accept: application/json


#### Response Sukses (200)

json
{
  "success": true,
  "message": "Bookings retrieved successfully",
  "data": {
    "bookings": [
      {
        "id": "1",
        "userId": "1",
        "packageId": "1",
        "packageTitle": "Pantai Lombok",
        "packageImage": "http://localhost:8000/api/asset/images/lombok.jpeg",
        "price": 1000000,
        "departureDate": "2024-02-15",
        "pickupTime": "08:00",
        "customerName": "John Doe",
        "lokasiPenjemputan": "bandara",
        "paymentMethod": "bank_transfer",
        "status": "Menunggu Pembayaran",
        "bookingDate": "2024-01-20T10:30:00.000000Z",
        "kodeBooking": "BOOK-ORDER-1705743000-1",
        "orderId": "ORDER-1705743000-1",
        "snapToken": "abc123xyz...",
        "paymentStatus": "pending"
      }
    ]
  }
}


---

### 3. Get Booking by ID

Mendapatkan detail lengkap sebuah pemesanan berdasarkan ID.

#### Endpoint

GET /api/bookings/{id}


#### Apakah Perlu Login?
✅ *Ya*, wajib login.

#### Parameter URL

| Parameter | Tipe | Wajib? | Keterangan |
|-----------|------|--------|------------|
| id | integer | ✅ Ya | ID booking |

#### Response Sukses (200)

json
{
  "success": true,
  "message": "Booking retrieved successfully",
  "data": {
    "booking": {
      "id": "1",
      "kodeBooking": "BOOK-ORDER-1705743000-1",
      "customerName": "John Doe",
      "orderId": "ORDER-1705743000-1",
      "snapToken": "abc123xyz...",
      "packageId": "1",
      "packageTitle": "Pantai Lombok",
      "packageImage": "http://localhost:8000/api/asset/images/lombok.jpeg",
      "destination": {
        "id": "1",
        "title": "Pantai Lombok",
        "imageUrl": "http://localhost:8000/api/asset/images/lombok.jpeg",
        "price": 1000000
      },
      "tanggalBooking": "2024-01-20",
      "tanggalKeberangkatan": "2024-02-15",
      "waktuKeberangkatan": "08:00",
      "lokasiPenjemputan": "bandara",
      "metodePembayaran": "bank_transfer",
      "status": "Menunggu Pembayaran",
      "paymentStatus": "pending",
      "totalHarga": 1000000
    }
  }
}


---

### 4. Check Booking Status

Mengecek status pembayaran booking.

#### Endpoint

GET /api/bookings/{id}/status


#### Apakah Perlu Login?
✅ *Ya*, wajib login.

#### Response Sukses (200)

json
{
  "success": true,
  "message": "Booking status retrieved successfully",
  "data": {
    "paymentStatus": "settlement",
    "status": "Dikonfirmasi"
  }
}


---

### 5. Payment Notification (Callback)

Endpoint untuk menerima notifikasi dari Midtrans setelah pembayaran.

#### Endpoint

POST /api/payment/notification


#### Apakah Perlu Login?
❌ *Tidak*, ini adalah callback dari Midtrans (public endpoint).

*Catatan:* Endpoint ini dipanggil otomatis oleh Midtrans, tidak perlu dipanggil manual dari Flutter app.

---

## Model Booking (Flutter)

Model Booking digunakan untuk representasi data pemesanan di Flutter app.

### Lokasi File

lib/models/booking.dart


### Properties

| Property | Tipe | Nullable? | Keterangan |
|----------|------|-----------|------------|
| id | String | ❌ | ID booking |
| userId | String | ❌ | ID user |
| packageId | String | ❌ | ID paket/destinasi |
| packageTitle | String | ❌ | Judul paket |
| packageImage | String | ❌ | URL gambar paket |
| price | double | ❌ | Total harga |
| departureDate | String | ❌ | Tanggal keberangkatan |
| pickupDate | String? | ✅ | Tanggal penjemputan (opsional) |
| pickupTime | String | ❌ | Waktu penjemputan |
| customerName | String? | ✅ | Nama pemesan |
| lokasiPenjemputan | String? | ✅ | Lokasi penjemputan |
| paymentMethod | String | ❌ | Metode pembayaran |
| status | String | ❌ | Status booking |
| bookingDate | DateTime | ❌ | Tanggal pemesanan |
| paymentInfo | String | ❌ | Informasi pembayaran |
| kodeBooking | String? | ✅ | Kode booking |
| orderId | String? | ✅ | Order ID Midtrans |
| snapToken | String? | ✅ | Snap token untuk payment |
| paymentStatus | String? | ✅ | Status pembayaran |

### Contoh Penggunaan

dart
import '../models/booking.dart';

// Parse dari JSON
final booking = Booking.fromJson(jsonData);

// Convert ke JSON
final json = booking.toJson();

// Copy dengan perubahan
final updatedBooking = booking.copyWith(
  status: 'Dikonfirmasi',
  paymentStatus: 'settlement',
);


---

## BookingService (Flutter)

Service untuk berinteraksi dengan booking API.

### Lokasi File

lib/services/booking_service.dart


### Methods

#### 1. createBooking()

Membuat booking baru dan mendapatkan snap token.

dart
static Future<Booking?> createBooking({
  required String destinationId,
  required String customerName,
  required String tanggalKeberangkatan,
  required String waktuKeberangkatan,
  required String lokasiPenjemputan,
  required String metodePembayaran,
}) async


*Contoh Penggunaan:*

dart
final booking = await BookingService.createBooking(
  destinationId: '1',
  customerName: 'John Doe',
  tanggalKeberangkatan: '2024-02-15',
  waktuKeberangkatan: '08:00',
  lokasiPenjemputan: 'bandara',
  metodePembayaran: 'bank_transfer',
);

if (booking != null && booking.snapToken != null) {
  // Buka Midtrans Snap untuk payment
  Navigator.push(
    context,
    MaterialPageRoute(
      builder: (context) => MidtransSnapPage(snapToken: booking.snapToken!),
    ),
  );
}


#### 2. getBookings()

Mendapatkan semua booking milik user.

dart
static Future<List<Booking>> getBookings() async


*Contoh Penggunaan:*

dart
final bookings = await BookingService.getBookings();
// bookings akan otomatis disimpan ke local storage


#### 3. getBookingById()

Mendapatkan detail booking berdasarkan ID.

dart
static Future<Booking?> getBookingById(String id) async


#### 4. checkBookingStatus()

Mengecek status pembayaran booking.

dart
static Future<String?> checkBookingStatus(String id) async


#### 5. updateBookingStatus()

Update status booking di local storage (untuk offline support).

dart
static Future<void> updateBookingStatus(String bookingId, String status) async


### Offline Support

BookingService menggunakan SharedPreferences untuk menyimpan booking secara lokal. Jika API gagal, app akan fallback ke data lokal.

---

## Order History Page

Halaman untuk menampilkan riwayat pemesanan user.

### Lokasi File

lib/pages/order_history_page.dart


### Fitur

1. *Daftar Booking*: Menampilkan semua booking dengan informasi lengkap
2. *Detail Lengkap*: 
   - Informasi Pemesan (nama pemesan)
   - Detail Perjalanan (tanggal keberangkatan, waktu penjemputan, lokasi)
   - Informasi Pembayaran (total harga, metode, status)
   - Informasi Pemesanan (tanggal pemesanan, kode booking, order ID)
3. *Status Badge*: Badge warna-warni untuk status booking
4. *Action Buttons*: 
   - "Lihat Info Pembayaran" untuk booking yang menunggu pembayaran
   - "Batalkan" untuk booking yang sudah dikonfirmasi
5. *Refresh*: Pull-to-refresh dan tombol refresh manual
6. *Empty State*: Tampilan ketika belum ada booking

### Struktur Informasi

#### 1. Informasi Pemesan
- Nama Pemesan (jika ada)

#### 2. Detail Perjalanan
- Tanggal Keberangkatan
- Tanggal Penjemputan (jika ada)
- Waktu Penjemputan
- Lokasi Penjemputan (jika ada)

#### 3. Informasi Pembayaran
- Total Harga (highlighted)
- Metode Pembayaran
- Status Pembayaran (jika ada)

#### 4. Informasi Pemesanan
- Tanggal Pemesanan
- Kode Booking (highlighted)
- Order ID (jika ada)

### Contoh Penggunaan

dart
// Navigate ke order history page
Navigator.push(
  context,
  MaterialPageRoute(
    builder: (context) => OrderHistoryPage(),
  ),
);

// Dengan snap token (setelah create booking)
Navigator.push(
  context,
  MaterialPageRoute(
    builder: (context) => OrderHistoryPage(
      snapToken: booking.snapToken,
      bookingId: booking.id,
    ),
  ),
);


---

## Flow Booking

### 1. User Memilih Paket
- User melihat daftar paket di home page
- User klik paket untuk melihat detail
- User memutuskan untuk booking

### 2. Form Booking
- User mengisi form:
  - Nama pemesan
  - Tanggal keberangkatan
  - Waktu penjemputan
  - Lokasi penjemputan
  - Metode pembayaran
- User submit form

### 3. Create Booking
dart
final booking = await BookingService.createBooking(
  destinationId: destinationId,
  customerName: customerName,
  tanggalKeberangkatan: tanggalKeberangkatan,
  waktuKeberangkatan: waktuKeberangkatan,
  lokasiPenjemputan: lokasiPenjemputan,
  metodePembayaran: metodePembayaran,
);


### 4. Payment (Midtrans Snap)
- Jika booking berhasil dan ada snapToken:
  - Buka halaman Midtrans Snap
  - User melakukan pembayaran
  - Midtrans mengirim callback ke backend
  - Backend update status booking

### 5. Redirect ke Order History
- Setelah create booking, redirect ke OrderHistoryPage
- Jika ada snapToken, otomatis buka Midtrans Snap
- User bisa lihat detail booking lengkap

### 6. Status Update
- Backend menerima callback dari Midtrans
- Status booking diupdate otomatis
- User bisa refresh untuk melihat status terbaru

---

## Status Booking

### Status yang Tersedia

| Status | Warna | Keterangan |
|--------|-------|------------|
| Menunggu Pembayaran | Orange | Booking dibuat, menunggu pembayaran |
| Dikonfirmasi | Blue | Pembayaran berhasil, booking dikonfirmasi |
| Selesai | Green | Perjalanan selesai |
| Dibatalkan | Red | Booking dibatalkan |

### Payment Status (Midtrans)

| Status | Keterangan |
|--------|------------|
| pending | Menunggu pembayaran |
| settlement | Pembayaran berhasil |
| capture | Pembayaran berhasil |
| failed | Pembayaran gagal |
| deny | Pembayaran ditolak |
| expire | Token kedaluwarsa |
| cancel | Pembayaran dibatalkan |

### Mapping Status

- pending → Status: "Menunggu Pembayaran"
- settlement / capture → Status: "Dikonfirmasi"
- failed / deny / expire / cancel → Status: "Dibatalkan"

---

## Error Handling

### 1. Validation Error (422)

*Penyebab:* Data yang dikirim tidak valid.

*Contoh:*
json
{
  "success": false,
  "message": "Validasi gagal",
  "errors": {
    "destination_id": ["Destinasi wajib dipilih."],
    "customer_name": ["Nama pelanggan wajib diisi."]
  }
}


*Penanganan:*
dart
try {
  final booking = await BookingService.createBooking(...);
} catch (e) {
  // Tampilkan error message ke user
  ScaffoldMessenger.of(context).showSnackBar(
    SnackBar(content: Text('Error: ${e.toString()}')),
  );
}


### 2. Authentication Error (401)

*Penyebab:* Token tidak valid atau expired.

*Penanganan:*
- Redirect ke login page
- Clear token dan local storage
- Minta user login ulang

### 3. Not Found Error (404)

*Penyebab:* Booking tidak ditemukan.

*Penanganan:*
- Tampilkan pesan error
- Redirect ke order history atau home page

### 4. Server Error (500)

*Penyebab:* Error di server.

*Penanganan:*
- Tampilkan pesan error umum
- Fallback ke local storage jika memungkinkan
- Log error untuk debugging

### 5. Network Error

*Penyebab:* Tidak ada koneksi internet.

*Penanganan:*
- Tampilkan pesan "Tidak ada koneksi internet"
- Fallback ke local storage
- Retry ketika koneksi kembali

---

## Troubleshooting

### 1. Booking tidak muncul di Order History

*Kemungkinan Penyebab:*
- User belum login
- Token expired
- API error

*Solusi:*
1. Pastikan user sudah login
2. Check token di SharedPreferences
3. Check log error di console
4. Refresh halaman

### 2. Snap Token tidak muncul

*Kemungkinan Penyebab:*
- Midtrans configuration error
- Payment service error

*Solusi:*
1. Check Midtrans credentials di backend
2. Check log error di Laravel
3. Pastikan PaymentService berfungsi dengan baik

### 3. Status tidak update setelah pembayaran

*Kemungkinan Penyebab:*
- Callback dari Midtrans tidak diterima
- Error saat update status di backend

*Solusi:*
1. Check log callback di Laravel
2. Manual refresh di order history page
3. Check payment/notification endpoint

### 4. Image tidak muncul

*Kemungkinan Penyebab:*
- URL image tidak valid
- CORS error
- Image tidak ada di server

*Solusi:*
1. Check URL image di response API
2. Pastikan menggunakan ApiConfig.fixImageUrl()
3. Check image route di routes/api.php

### 5. Local storage tidak sync dengan API

*Kemungkinan Penyebab:*
- Data lokal lebih lama dari data API
- Error saat save ke local storage

*Solusi:*
1. Clear local storage
2. Refresh dari API
3. Check SharedPreferences permission

---

## Best Practices

### 1. Error Handling
- Selalu handle error dengan try-catch
- Tampilkan pesan error yang user-friendly
- Log error untuk debugging

### 2. Loading State
- Tampilkan loading indicator saat create booking
- Tampilkan loading saat fetch bookings
- Disable button saat proses berlangsung

### 3. Offline Support
- Gunakan local storage untuk offline access
- Sync dengan API ketika online
- Tampilkan indicator jika data dari cache

### 4. User Experience
- Tampilkan konfirmasi sebelum cancel booking
- Beri feedback jelas setelah action
- Auto-refresh setelah payment

### 5. Security
- Jangan simpan token di plain text
- Validasi input di frontend dan backend
- Gunakan HTTPS untuk production

---

## Contoh Implementasi Lengkap

### Create Booking dengan Error Handling

dart
Future<void> _createBooking() async {
  // Show loading
  setState(() => _isLoading = true);
  
  try {
    final booking = await BookingService.createBooking(
      destinationId: _destinationId,
      customerName: _customerName,
      tanggalKeberangkatan: _tanggalKeberangkatan,
      waktuKeberangkatan: _waktuKeberangkatan,
      lokasiPenjemputan: _lokasiPenjemputan,
      metodePembayaran: _metodePembayaran,
    );
    
    if (booking != null) {
      // Success - navigate to order history
      Navigator.pushReplacement(
        context,
        MaterialPageRoute(
          builder: (context) => OrderHistoryPage(
            snapToken: booking.snapToken,
            bookingId: booking.id,
          ),
        ),
      );
    } else {
      // Error creating booking
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Gagal membuat booking. Silakan coba lagi.'),
          backgroundColor: Colors.red,
        ),
      );
    }
  } catch (e) {
    // Handle error
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text('Error: ${e.toString()}'),
        backgroundColor: Colors.red,
      ),
    );
  } finally {
    // Hide loading
    setState(() => _isLoading = false);
  }
}


### Display Booking List

dart
FutureBuilder<List<Booking>>(
  future: BookingService.getBookings(),
  builder: (context, snapshot) {
    if (snapshot.connectionState == ConnectionState.waiting) {
      return const Center(child: CircularProgressIndicator());
    }
    
    if (snapshot.hasError) {
      return Center(
        child: Text('Error: ${snapshot.error}'),
      );
    }
    
    final bookings = snapshot.data ?? [];
    
    if (bookings.isEmpty) {
      return const Center(
        child: Text('Belum ada booking'),
      );
    }
    
    return ListView.builder(
      itemCount: bookings.length,
      itemBuilder: (context, index) {
        final booking = bookings[index];
        return BookingCard(booking: booking);
      },
    );
  },
)


---

## Referensi

- [Laravel Sanctum Documentation](https://laravel.com/docs/sanctum)
- [Midtrans Documentation](https://docs.midtrans.com/)
- [Flutter SharedPreferences](https://pub.dev/packages/shared_preferences)
- [DESTINATION_API.md](./DESTINATION_API.md) - Dokumentasi API Destinasi
- [IMAGE_LOADING_SYSTEM.md](./IMAGE_LOADING_SYSTEM.md) - Dokumentasi Image Loading

---

*Last Updated:* 2024-01-20
*Version:* 1.0.0