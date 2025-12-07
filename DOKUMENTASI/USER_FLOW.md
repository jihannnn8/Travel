# 👤 User Flow - Travel Booking App

Dokumentasi lengkap tentang alur penggunaan aplikasi dari perspektif user.

---

## 📋 Daftar Isi

1. [Overview User Flow](#overview-user-flow)
2. [Flow Diagram](#flow-diagram)
3. [Detail Flow per Fitur](#detail-flow-per-fitur)
4. [User Journey Map](#user-journey-map)
5. [Screen Flow](#screen-flow)

---

## 🎯 Overview User Flow

Aplikasi Travel Booking memiliki beberapa flow utama:

1. **Authentication Flow**: Register → Login → Home
2. **Browse Flow**: Home → View Destinations → Detail Package
3. **Booking Flow**: Detail → Booking Form → Payment → Confirmation
4. **History Flow**: Home → Order History → View Details
5. **Profile Flow**: Home → Profile → Edit Profile

---

## 📊 Flow Diagram

### Main Flow:

```
┌─────────────┐
│   Landing   │
│    Page     │
└──────┬──────┘
       │
       ├───► Register ──► Login ──► Home
       │
       └───► Login ──────► Home
                        │
                        ├───► Browse Destinations
                        │
                        ├───► Package Detail
                        │         │
                        │         ├───► Booking Form
                        │         │         │
                        │         │         └───► Payment (Midtrans)
                        │         │                   │
                        │         │                   └───► Confirmation
                        │         │
                        │         └───► Back to Home
                        │
                        ├───► Order History
                        │         │
                        │         └───► View Booking Details
                        │
                        └───► Profile
                                  │
                                  └───► Edit Profile
```

---

## 🔄 Detail Flow per Fitur

### 1. Authentication Flow

#### 1.1 Register Flow

```
User membuka aplikasi
    ↓
Landing Page muncul
    ↓
User klik "Daftar"
    ↓
Register Page
    ↓
User input:
  - Nama
  - Email
  - Password
  - Nomor Telepon
    ↓
User klik "Daftar"
    ↓
Validasi input (client-side)
    ↓
POST /api/register
    ↓
Backend validasi & create user
    ↓
Return: { user, token }
    ↓
Save token (SharedPreferences)
    ↓
Auto redirect ke Login Page
    ↓
User bisa langsung login
```

**Screens:**
1. Landing Page
2. Register Page
3. Login Page (auto redirect)

---

#### 1.2 Login Flow

```
User di Landing Page
    ↓
User klik "Masuk"
    ↓
Login Page
    ↓
User input:
  - Email
  - Password
    ↓
User klik "Masuk"
    ↓
Validasi input
    ↓
POST /api/login
    ↓
Backend validasi credentials
    ↓
Generate token (Sanctum)
    ↓
Return: { token, user }
    ↓
Save token & user data (SharedPreferences)
    ↓
Redirect ke Home Page
```

**Screens:**
1. Landing Page
2. Login Page
3. Home Page

---

#### 1.3 Auto Login (Token Check)

```
App dibuka
    ↓
main.dart → AuthWrapper
    ↓
Check token di SharedPreferences
    ↓
Token ada?
    ├─── YES ──► Verify token (GET /api/me)
    │              │
    │              ├─── Valid ──► Home Page
    │              │
    │              └─── Invalid ──► Landing Page
    │
    └─── NO ──► Landing Page
```

---

### 2. Browse & Discovery Flow

#### 2.1 Home Page Flow

```
User masuk ke Home Page
    ↓
Load data secara paralel:
  ├─── GET /api/sliders
  ├─── GET /api/cities
  ├─── GET /api/promos
  └─── GET /api/destinations
    ↓
Display:
  - Image Slider (top)
  - City Icons (horizontal scroll)
  - Promo Carousel (horizontal scroll)
  - Tour Packages (horizontal scroll)
    ↓
User bisa:
  - Scroll untuk lihat lebih banyak
  - Tap city icon untuk filter (future)
  - Tap package untuk lihat detail
```

**Screens:**
- Home Page (dengan bottom navigation)

**Components:**
- Slider Widget
- City Icons List
- Promo Carousel
- Package Cards List

---

#### 2.2 Package Detail Flow

```
User tap package di Home
    ↓
Package Detail Page
    ↓
Load detail:
  GET /api/destinations/{id}
    ↓
Display:
  - Hero Image
  - Title & Rating
  - Price & Duration
  - Description
  - Rundown/Itinerary
  - Departure Date
    ↓
User scroll untuk lihat semua info
    ↓
User klik "Pesan Sekarang"
    ↓
Check: User sudah login?
    ├─── YES ──► Booking Form
    └─── NO ──► Redirect ke Login
```

**Screens:**
- Home Page
- Package Detail Page
- Login Page (jika belum login)
- Booking Form

---

### 3. Booking Flow

#### 3.1 Create Booking Flow

```
User di Package Detail Page
    ↓
User klik "Pesan Sekarang"
    ↓
Booking Form Page
    ↓
Form auto-filled:
  - Nama: dari user profile
  - Email: dari user profile
  - Nomor Telepon: dari user profile
  - Destinasi: dari package detail
  - Tanggal Keberangkatan: fixed dari package
    ↓
User input/edit:
  - Waktu Keberangkatan: pilih dari dropdown
  - Lokasi Penjemputan: pilih (bandara/terminal)
  - Metode Pembayaran: pilih
    ↓
User klik "Lanjutkan Pembayaran"
    ↓
Validasi form
    ↓
POST /api/bookings
  Body: {
    destination_id,
    tanggal_keberangkatan,
    waktu_keberangkatan,
    metode_pembayaran
  }
    ↓
Backend:
  - Create booking
  - Generate kode_booking
  - Create Midtrans transaction
  - Get snapToken
    ↓
Return: { booking, snapToken }
    ↓
Navigate ke Payment Page (Midtrans Snap)
```

**Screens:**
- Package Detail Page
- Booking Form Page
- Payment Page (Midtrans)

---

#### 3.2 Payment Flow

```
User di Payment Page
    ↓
Load Midtrans Snap WebView
    ↓
Display payment options:
  - Credit Card
  - Bank Transfer
  - E-Wallet (GoPay, OVO, DANA)
    ↓
User pilih metode pembayaran
    ↓
User input payment details
    ↓
User klik "Bayar"
    ↓
Midtrans process payment
    ↓
Payment Status:
    ├─── Success ──► Midtrans webhook
    │                  │
    │                  └───► Update booking status
    │                            │
    │                            └───► "Dikonfirmasi"
    │
    ├─── Pending ──► Status: "Menunggu Pembayaran"
    │
    └─── Failed ──► Status: "Dibatalkan"
    ↓
User kembali ke app
    ↓
Show payment result
    ↓
Redirect ke Order History
```

**Screens:**
- Payment Page (Midtrans Snap WebView)
- Order History Page

**Webhook Flow:**
```
Midtrans → POST /api/payment/notification
    ↓
Backend update booking:
  - payment_status
  - status (Dikonfirmasi/Dibatalkan)
    ↓
User bisa lihat update di Order History
```

---

### 4. Order History Flow

#### 4.1 View History Flow

```
User di Home Page
    ↓
User tap "History" di bottom nav
    ↓
Order History Page
    ↓
Load bookings:
  GET /api/bookings
    ↓
Display list bookings:
  - Kode Booking
  - Destinasi
  - Tanggal Keberangkatan
  - Status (badge color)
  - Total Harga
    ↓
User scroll untuk lihat semua
    ↓
User tap booking untuk lihat detail
```

**Screens:**
- Home Page
- Order History Page
- Booking Detail (modal atau page)

---

#### 4.2 Booking Detail Flow

```
User tap booking di history list
    ↓
Booking Detail (modal/page)
    ↓
Display:
  - Kode Booking
  - Status & Payment Status
  - Destinasi Info
  - Customer Info
  - Tanggal & Waktu
  - Metode Pembayaran
  - Total Harga
  - Payment Info (jika sudah bayar)
    ↓
User bisa:
  - Lihat detail lengkap
  - Copy kode booking
  - Check payment status
```

**Status Badge Colors:**
- `Menunggu Pembayaran` → Orange/Yellow
- `Dikonfirmasi` → Green
- `Selesai` → Blue
- `Dibatalkan` → Red

---

### 5. Profile Flow

#### 5.1 View Profile Flow

```
User di Home Page
    ↓
User tap "Profile" di bottom nav
    ↓
Profile Page
    ↓
Load user data:
  GET /api/profile
    ↓
Display:
  - Avatar/Photo
  - Nama
  - Email
  - Nomor Telepon
  - Statistics:
    - Total Orders
    - Total Ratings
    - Points (jika ada)
  - Menu:
    - Edit Profile
    - Order History
    - Logout
    ↓
User bisa tap menu untuk aksi
```

**Screens:**
- Home Page
- Profile Page

---

#### 5.2 Edit Profile Flow

```
User di Profile Page
    ↓
User tap "Edit Profile"
    ↓
Edit Profile Form
    ↓
Form fields (editable):
  - Nama
  - Email
  - Nomor Telepon
    ↓
User edit data
    ↓
User klik "Simpan"
    ↓
Validasi input
    ↓
PUT /api/profile
  Body: {
    name,
    email,
    phone_number
  }
    ↓
Backend update user
    ↓
Return: { user }
    ↓
Update local state
    ↓
Show success message
    ↓
Back to Profile Page (updated)
```

**Screens:**
- Profile Page
- Edit Profile Form
- Profile Page (updated)

---

#### 5.3 Logout Flow

```
User di Profile Page
    ↓
User tap "Logout"
    ↓
Show confirmation dialog
    ↓
User confirm
    ↓
POST /api/logout
    ↓
Backend revoke token
    ↓
Delete token & user data (SharedPreferences)
    ↓
Clear app state
    ↓
Redirect ke Landing Page
```

**Screens:**
- Profile Page
- Confirmation Dialog
- Landing Page

---

## 🗺️ User Journey Map

### Complete User Journey:

```
1. First Time User
   │
   ├─► Download App
   ├─► Open App
   ├─► See Landing Page
   ├─► Register Account
   ├─► Login
   │
2. Browse & Discover
   │
   ├─► See Home Page
   ├─► Browse Sliders
   ├─► Browse Cities
   ├─► Browse Packages
   ├─► Tap Package → See Detail
   │
3. Make Booking
   │
   ├─► Tap "Pesan Sekarang"
   ├─► Fill Booking Form
   ├─► Choose Payment Method
   ├─► Process Payment
   ├─► Get Confirmation
   │
4. Manage Booking
   │
   ├─► View Order History
   ├─► Check Booking Status
   ├─► View Booking Details
   │
5. Manage Profile
   │
   ├─► View Profile
   ├─► Edit Profile
   ├─► Update Information
   │
6. Return User
   │
   ├─► Auto Login (if token valid)
   ├─► Continue browsing
   └─► Make more bookings
```

---

## 📱 Screen Flow

### Navigation Structure:

```
┌─────────────────────────────────────┐
│         Landing Page                 │
│  (If not logged in)                  │
└─────────────────────────────────────┘
         │
         ├───► Register Page
         │
         └───► Login Page
                  │
                  └───► Home Page (Bottom Nav)
                           │
                           ├───► [Home Tab]
                           │       │
                           │       ├───► Package Detail
                           │       │       │
                           │       │       └───► Booking Form
                           │       │               │
                           │       │               └───► Payment Page
                           │       │                       │
                           │       │                       └───► Order History
                           │       │
                           │       └───► (Stay on Home)
                           │
                           ├───► [History Tab]
                           │       │
                           │       └───► Booking Detail
                           │
                           └───► [Profile Tab]
                                   │
                                   ├───► Edit Profile
                                   │
                                   └───► Logout → Landing
```

---

## 🎯 Key User Actions

### Primary Actions:

1. **Register** → Create account
2. **Login** → Access app
3. **Browse** → Discover packages
4. **Book** → Make reservation
5. **Pay** → Complete payment
6. **Track** → Check booking status
7. **Manage** → Edit profile

### Secondary Actions:

1. **View Detail** → See package info
2. **Filter** → Search by city (future)
3. **Rate** → Rate packages (future)
4. **Share** → Share packages (future)

---

## 🔄 State Transitions

### Booking Status Flow:

```
Menunggu Pembayaran
    │
    ├─── Payment Success ──► Dikonfirmasi
    │
    ├─── Payment Failed ──► Dibatalkan
    │
    └─── Payment Expired ──► Dibatalkan

Dikonfirmasi
    │
    └─── User Complete ──► Selesai (manual update)
```

### Payment Status Flow:

```
pending
    │
    ├─── Payment Success ──► paid
    │
    ├─── Payment Failed ──► failed
    │
    ├─── Payment Expired ──► expired
    │
    └─── User Cancel ──► cancelled
```

---

## 📝 Notes

### Error Handling:

- **Network Error**: Show retry button
- **Validation Error**: Show field-specific errors
- **Auth Error**: Redirect to login
- **Payment Error**: Show error message, allow retry

### Loading States:

- Show loading indicator saat fetch data
- Disable buttons saat processing
- Show skeleton screens untuk better UX

### Success Feedback:

- Show success message setelah action
- Auto refresh data setelah update
- Visual feedback (animations, colors)

---

## ✅ Best Practices

1. **Always check authentication** sebelum aksi yang perlu login
2. **Show loading states** untuk semua async operations
3. **Handle errors gracefully** dengan user-friendly messages
4. **Auto-save form data** untuk prevent data loss
5. **Validate input** sebelum submit
6. **Provide feedback** untuk setiap user action

---

**Happy User Experience! 🎉**

