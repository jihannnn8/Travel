# 🗄️ Database Schema - Travel Booking App

Dokumentasi lengkap tentang struktur database, relasi tabel, dan ERD.

---

## 📋 Daftar Isi

1. [Overview Database](#overview-database)
2. [Struktur Tabel](#struktur-tabel)
3. [Relasi Antar Tabel](#relasi-antar-tabel)
4. [ERD (Entity Relationship Diagram)](#erd-entity-relationship-diagram)
5. [Indexes](#indexes)
6. [Sample Data](#sample-data)

---

## 🎯 Overview Database

Database menggunakan **MySQL** dengan struktur relasional yang terdiri dari:

- **6 Tabel Utama**: users, destinations, bookings, cities, sliders, promos
- **3 Tabel Sistem**: personal_access_tokens, password_reset_tokens, failed_jobs

### Database Name:
```
travel_db
```

---

## 📊 Struktur Tabel

### 1. Tabel `users`

Menyimpan data user/pengguna aplikasi.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | User ID |
| `name` | VARCHAR(255) | NOT NULL | Nama lengkap user |
| `email` | VARCHAR(255) | UNIQUE, NOT NULL | Email user (untuk login) |
| `phone_number` | VARCHAR(255) | NULLABLE | Nomor telepon |
| `email_verified_at` | TIMESTAMP | NULLABLE | Waktu verifikasi email |
| `password` | VARCHAR(255) | NOT NULL | Password (hashed) |
| `role` | ENUM('admin', 'user') | DEFAULT 'user' | Role user |
| `remember_token` | VARCHAR(100) | NULLABLE | Remember me token |
| `created_at` | TIMESTAMP | NULLABLE | Waktu dibuat |
| `updated_at` | TIMESTAMP | NULLABLE | Waktu diupdate |

**Contoh Data:**
```sql
id: 1
name: "John Doe"
email: "john@example.com"
phone_number: "081234567890"
role: "user"
```

---

### 2. Tabel `destinations`

Menyimpan data paket wisata/destinasi.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Destination ID |
| `title` | VARCHAR(255) | NOT NULL | Judul paket wisata |
| `description` | TEXT | NULLABLE | Deskripsi lengkap |
| `destination` | VARCHAR(255) | NOT NULL | Lokasi (Lombok, Yogyakarta, dll) |
| `price` | DECIMAL(10,2) | NOT NULL | Harga paket |
| `duration` | VARCHAR(255) | NOT NULL | Durasi (contoh: "3D2N") |
| `departure_date` | DATE | NULLABLE | Tanggal keberangkatan |
| `rating` | DECIMAL(3,2) | DEFAULT 0.00 | Rating (0.00 - 5.00) |
| `total_ratings` | INT | DEFAULT 0 | Jumlah rating |
| `rundown` | JSON | NULLABLE | Itinerary/jadwal perjalanan |
| `image_url` | VARCHAR(255) | NULLABLE | URL gambar destinasi |
| `created_at` | TIMESTAMP | NULLABLE | Waktu dibuat |
| `updated_at` | TIMESTAMP | NULLABLE | Waktu diupdate |

**Contoh Data:**
```sql
id: 1
title: "Paket Wisata Lombok 3D2N"
destination: "Lombok"
price: 2500000.00
duration: "3D2N"
rating: 4.50
total_ratings: 120
```

**Rundown (JSON Format):**
```json
[
  {
    "day": 1,
    "time": "08:00",
    "activity": "Pickup dari Bandara",
    "description": "Jemput di Bandara Lombok"
  },
  {
    "day": 1,
    "time": "10:00",
    "activity": "Check-in Hotel",
    "description": "Check-in di hotel pilihan"
  }
]
```

---

### 3. Tabel `bookings`

Menyimpan data pemesanan/booking.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Booking ID |
| `user_id` | BIGINT UNSIGNED | FOREIGN KEY → users.id | ID user yang booking |
| `destination_id` | BIGINT UNSIGNED | FOREIGN KEY → destinations.id | ID destinasi |
| `customer_name` | VARCHAR(255) | NOT NULL | Nama customer |
| `midtrans_order_id` | VARCHAR(100) | UNIQUE, NULLABLE | Order ID dari Midtrans |
| `kode_booking` | VARCHAR(150) | UNIQUE, NULLABLE | Kode booking unik |
| `midtrans_payment_token` | VARCHAR(255) | NULLABLE | Payment token (Snap Token) |
| `payment_status` | ENUM | DEFAULT 'pending' | Status pembayaran |
| `midtrans_response` | JSON | NULLABLE | Response dari Midtrans |
| `tanggal_booking` | DATE | NOT NULL | Tanggal booking dibuat |
| `tanggal_keberangkatan` | DATE | NOT NULL | Tanggal keberangkatan |
| `waktu_keberangkatan` | VARCHAR(20) | NOT NULL | Waktu keberangkatan |
| `lokasi_penjemputan` | ENUM('bandara', 'terminal') | DEFAULT 'bandara' | Lokasi penjemputan |
| `status` | VARCHAR(50) | DEFAULT 'Menunggu Pembayaran' | Status booking |
| `metode_pembayaran` | VARCHAR(50) | DEFAULT 'transfer' | Metode pembayaran |
| `total_harga` | DECIMAL(15,2) | NOT NULL | Total harga booking |
| `created_at` | TIMESTAMP | NULLABLE | Waktu dibuat |
| `updated_at` | TIMESTAMP | NULLABLE | Waktu diupdate |

**Payment Status Values:**
- `pending` - Menunggu pembayaran
- `paid` - Sudah dibayar
- `failed` - Gagal
- `expired` - Kadaluarsa
- `cancelled` - Dibatalkan

**Booking Status Values:**
- `Menunggu Pembayaran` - Booking dibuat, menunggu pembayaran
- `Dikonfirmasi` - Pembayaran berhasil, booking dikonfirmasi
- `Selesai` - Booking selesai
- `Dibatalkan` - Booking dibatalkan

**Contoh Data:**
```sql
id: 1
user_id: 1
destination_id: 1
customer_name: "John Doe"
kode_booking: "BK-20241201-001"
payment_status: "paid"
status: "Dikonfirmasi"
total_harga: 2500000.00
```

---

### 4. Tabel `cities`

Menyimpan data kota untuk ditampilkan di home page.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | City ID |
| `name` | VARCHAR(255) | NOT NULL | Nama kota |
| `image_url` | VARCHAR(255) | NULLABLE | URL gambar kota |
| `order` | INT | DEFAULT 0 | Urutan tampil (untuk sorting) |
| `is_active` | BOOLEAN | DEFAULT true | Status aktif/tidak |
| `created_at` | TIMESTAMP | NULLABLE | Waktu dibuat |
| `updated_at` | TIMESTAMP | NULLABLE | Waktu diupdate |

**Contoh Data:**
```sql
id: 1
name: "D.I.Y Yogyakarta"
image_url: "/api/asset/yogyakarta.jpg"
order: 1
is_active: true
```

---

### 5. Tabel `sliders`

Menyimpan data slider images untuk home page.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Slider ID |
| `title` | VARCHAR(255) | NULLABLE | Judul slider (opsional) |
| `image_url` | VARCHAR(255) | NOT NULL | URL gambar slider |
| `order` | INT | DEFAULT 0 | Urutan tampil |
| `is_active` | BOOLEAN | DEFAULT true | Status aktif/tidak |
| `created_at` | TIMESTAMP | NULLABLE | Waktu dibuat |
| `updated_at` | TIMESTAMP | NULLABLE | Waktu diupdate |

---

### 6. Tabel `promos`

Menyimpan data promo images untuk home page.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Promo ID |
| `image_url` | VARCHAR(255) | NOT NULL | URL gambar promo |
| `order` | INT | DEFAULT 0 | Urutan tampil |
| `is_active` | BOOLEAN | DEFAULT true | Status aktif/tidak |
| `created_at` | TIMESTAMP | NULLABLE | Waktu dibuat |
| `updated_at` | TIMESTAMP | NULLABLE | Waktu diupdate |

---

### 7. Tabel `personal_access_tokens`

Tabel sistem Laravel Sanctum untuk menyimpan authentication tokens.

| Column | Type | Description |
|--------|------|-------------|
| `id` | BIGINT UNSIGNED | Token ID |
| `tokenable_type` | VARCHAR(255) | Model type (App\Models\User) |
| `tokenable_id` | BIGINT UNSIGNED | User ID |
| `name` | VARCHAR(255) | Token name |
| `token` | VARCHAR(64) | Hashed token |
| `abilities` | TEXT | Token abilities |
| `last_used_at` | TIMESTAMP | Last usage time |
| `expires_at` | TIMESTAMP | Expiration time |
| `created_at` | TIMESTAMP | Created time |
| `updated_at` | TIMESTAMP | Updated time |

---

## 🔗 Relasi Antar Tabel

### 1. Users → Bookings (One-to-Many)

```
users (1) ──────< (many) bookings
```

- Satu user bisa punya banyak bookings
- Foreign key: `bookings.user_id` → `users.id`
- On Delete: `CASCADE` (jika user dihapus, bookings juga dihapus)

**Laravel:**
```php
// User Model
public function bookings() {
    return $this->hasMany(Booking::class);
}

// Booking Model
public function user() {
    return $this->belongsTo(User::class);
}
```

---

### 2. Destinations → Bookings (One-to-Many)

```
destinations (1) ──────< (many) bookings
```

- Satu destinasi bisa dipesan banyak kali
- Foreign key: `bookings.destination_id` → `destinations.id`
- On Delete: `RESTRICT` (tidak bisa hapus destinasi jika sudah ada booking)

**Laravel:**
```php
// Destination Model
public function bookings() {
    return $this->hasMany(Booking::class);
}

// Booking Model
public function destination() {
    return $this->belongsTo(Destination::class);
}
```

---

### 3. Users → Personal Access Tokens (One-to-Many)

```
users (1) ──────< (many) personal_access_tokens
```

- Satu user bisa punya banyak tokens (untuk multiple devices)
- Polymorphic relationship

---

## 📐 ERD (Entity Relationship Diagram)

```
┌─────────────┐
│    users    │
│─────────────│
│ id (PK)     │
│ name        │
│ email       │
│ password    │
│ role        │
└──────┬──────┘
       │
       │ 1
       │
       │ many
       │
       ▼
┌─────────────┐         ┌──────────────┐
│  bookings   │────────│ destinations │
│─────────────│    many │──────────────│
│ id (PK)     │    │ 1 │ id (PK)      │
│ user_id(FK) │    │   │ title        │
│ dest_id(FK) │    │   │ price        │
│ status      │    │   │ rating       │
│ payment_...  │    │   └──────────────┘
└─────────────┘    │
                   │
                   │ 1
                   │
                   ▼
            ┌──────────────┐
            │ destinations │
            └──────────────┘

┌─────────────┐
│   cities    │  (Standalone)
│─────────────│
│ id (PK)     │
│ name        │
│ image_url   │
└─────────────┘

┌─────────────┐
│   sliders   │  (Standalone)
│─────────────│
│ id (PK)     │
│ image_url   │
└─────────────┘

┌─────────────┐
│   promos    │  (Standalone)
│─────────────│
│ id (PK)     │
│ image_url   │
└─────────────┘
```

---

## 🔍 Indexes

Indexes ditambahkan untuk meningkatkan performa query:

### Tabel `bookings`:

```sql
INDEX idx_user_id (user_id)
INDEX idx_destination_id (destination_id)
INDEX idx_status (status)
INDEX idx_payment_status (payment_status)
INDEX idx_tanggal_booking (tanggal_booking)
INDEX idx_tanggal_keberangkatan (tanggal_keberangkatan)
INDEX idx_created_at (created_at)
```

### Tabel `users`:

```sql
UNIQUE INDEX idx_email (email)
```

### Tabel `bookings`:

```sql
UNIQUE INDEX idx_midtrans_order_id (midtrans_order_id)
UNIQUE INDEX idx_kode_booking (kode_booking)
```

---

## 📝 Sample Data

### Users

```sql
INSERT INTO users (name, email, password, phone_number, role) VALUES
('Admin', 'admin@travel.com', '$2y$10$...', '081234567890', 'admin'),
('John Doe', 'john@example.com', '$2y$10$...', '081234567891', 'user'),
('Jane Smith', 'jane@example.com', '$2y$10$...', '081234567892', 'user');
```

### Destinations

```sql
INSERT INTO destinations (title, destination, price, duration, rating, total_ratings) VALUES
('Paket Wisata Lombok 3D2N', 'Lombok', 2500000.00, '3D2N', 4.50, 120),
('Paket Wisata Yogyakarta 2D1N', 'Yogyakarta', 1500000.00, '2D1N', 4.75, 200),
('Paket Wisata Bali 4D3N', 'Bali', 3500000.00, '4D3N', 4.80, 150);
```

### Cities

```sql
INSERT INTO cities (name, image_url, `order`, is_active) VALUES
('D.I.Y Yogyakarta', '/api/asset/yogyakarta.jpg', 1, true),
('Bali', '/api/asset/bali.jpg', 2, true),
('Lombok', '/api/asset/lombok.jpg', 3, true);
```

---

## 🔄 Migration Commands

### Create Migration:
```bash
php artisan make:migration create_destinations_table
```

### Run Migrations:
```bash
php artisan migrate
```

### Rollback:
```bash
php artisan migrate:rollback
```

### Fresh (Drop & Recreate):
```bash
php artisan migrate:fresh
```

### Fresh with Seeder:
```bash
php artisan migrate:fresh --seed
```

---

## 📊 Query Examples

### Get User dengan Bookings:

```sql
SELECT u.*, COUNT(b.id) as total_bookings
FROM users u
LEFT JOIN bookings b ON u.id = b.user_id
GROUP BY u.id;
```

### Get Destinations dengan Rating:

```sql
SELECT 
    d.*,
    COUNT(b.id) as total_bookings
FROM destinations d
LEFT JOIN bookings b ON d.id = b.destination_id
WHERE d.rating >= 4.0
GROUP BY d.id
ORDER BY d.rating DESC;
```

### Get Bookings dengan Detail:

```sql
SELECT 
    b.*,
    u.name as user_name,
    u.email as user_email,
    d.title as destination_title,
    d.price as destination_price
FROM bookings b
JOIN users u ON b.user_id = u.id
JOIN destinations d ON b.destination_id = d.id
WHERE b.status = 'Dikonfirmasi'
ORDER BY b.created_at DESC;
```

---

## 🎯 Kesimpulan

Database schema dirancang untuk:
- ✅ Normalisasi yang baik (tidak ada data duplikat)
- ✅ Relasi yang jelas antar tabel
- ✅ Indexes untuk performa optimal
- ✅ Support untuk payment gateway (Midtrans)
- ✅ Flexible untuk fitur tambahan

Untuk melihat struktur lengkap, cek file migration di `backend/database/migrations/`.

---

**Happy Coding! 🚀**

