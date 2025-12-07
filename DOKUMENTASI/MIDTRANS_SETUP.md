# Setup Midtrans Payment Gateway

## 1. Daftar di Midtrans

1. Kunjungi https://dashboard.midtrans.com/
2. Daftar akun baru atau login
3. Pilih **Sandbox** untuk testing atau **Production** untuk live

## 2. Dapatkan API Keys

### Sandbox (Testing):
- Server Key: Dapatkan dari Settings > Access Keys > Server Key
- Client Key: Dapatkan dari Settings > Access Keys > Client Key

### Production (Live):
- Server Key: Dapatkan dari Settings > Access Keys > Server Key (Production)
- Client Key: Dapatkan dari Settings > Access Keys > Client Key (Production)

## 3. Setup .env

Tambahkan konfigurasi berikut ke file `.env`:

```env
# Midtrans Configuration
MIDTRANS_SERVER_KEY=your_server_key_here
MIDTRANS_CLIENT_KEY=your_client_key_here
MIDTRANS_IS_PRODUCTION=false
MIDTRANS_IS_SANITIZED=true
MIDTRANS_IS_3DS=true
```

**Catatan:**
- Untuk testing, set `MIDTRANS_IS_PRODUCTION=false`
- Untuk production, set `MIDTRANS_IS_PRODUCTION=true`
- Ganti `your_server_key_here` dan `your_client_key_here` dengan key dari Midtrans Dashboard

## 4. Run Migration

Jalankan migration untuk menambahkan payment fields:

```bash
php artisan migrate
```

## 5. Setup Webhook URL di Midtrans Dashboard

**PENTING:** Webhook diperlukan agar status booking otomatis terupdate ketika payment berhasil di Midtrans.

### Untuk Production:
1. Login ke Midtrans Dashboard (Production)
2. Pilih **Settings** > **Configuration**
3. Set **Payment Notification URL** ke:
   ```
   https://your-domain.com/api/payment/notification
   ```
4. Pastikan URL dapat diakses dari internet (tidak localhost)

### Untuk Sandbox/Testing:
1. Login ke Midtrans Dashboard (Sandbox)
2. Pilih **Settings** > **Configuration**
3. Set **Payment Notification URL** ke:
   ```
   https://your-domain.com/api/payment/notification
   ```
   Atau gunakan ngrok untuk local testing:
   ```
   https://your-ngrok-url.ngrok.io/api/payment/notification
   ```

**Catatan:**
- Midtrans akan mengirim POST request ke webhook URL setiap kali ada perubahan status transaksi
- Webhook handler akan otomatis update status booking di database
- Pastikan webhook URL dapat diakses dari internet (gunakan ngrok untuk local testing)

## 6. Testing

### Test dengan Sandbox:
- Gunakan kartu kredit test: 4811 1111 1111 1114
- CVV: 123
- Expiry: Bulan/tahun masa depan
- OTP: 112233

## API Endpoints

### Create Booking (dengan payment):
```
POST /api/bookings
Headers: Authorization: Bearer {token}
Body: {
  "destination_id": "1",
  "tanggal_keberangkatan": "2024-01-15",
  "waktu_keberangkatan": "08:00",
  "metode_pembayaran": "credit_card"
}
Response: {
  "success": true,
  "data": {
    "booking": {
      "snapToken": "...",
      "orderId": "..."
    }
  }
}
```

### Get Bookings:
```
GET /api/bookings
Headers: Authorization: Bearer {token}
```

### Check Payment Status:
```
GET /api/bookings/{id}/status
Headers: Authorization: Bearer {token}
```

### Webhook Notification (dari Midtrans):
```
POST /api/payment/notification
```

## Payment Methods yang Didukung

- `credit_card` - Kartu Kredit
- `bank_transfer` - Transfer Bank (BCA, Mandiri, BNI, dll)
- `e-wallet` - E-Wallet (GoPay, OVO, DANA, LinkAja)
- `echannel` - Mandiri Virtual Account
- `transfer` - Transfer Bank (generic)

## Status Payment & Booking

### Payment Status (payment_status):
- `pending` - Menunggu pembayaran
- `paid` - Sudah dibayar
- `failed` - Gagal
- `expired` - Kadaluarsa
- `cancelled` - Dibatalkan

### Booking Status (status):
- `Menunggu Pembayaran` - Booking dibuat, menunggu pembayaran
- `Dikonfirmasi` - Pembayaran berhasil, booking dikonfirmasi
- `Selesai` - Booking selesai (dapat diupdate manual oleh user)
- `Dibatalkan` - Booking dibatalkan (payment failed/expired/cancelled)

## Webhook Flow

1. User membuat booking → Status: `Menunggu Pembayaran`
2. User melakukan payment di Midtrans Snap
3. Midtrans mengirim webhook ke `/api/payment/notification` dengan status:
   - `settlement` atau `capture` → Status booking: `Dikonfirmasi`
   - `pending` → Status booking: `Menunggu Pembayaran`
   - `deny`, `expire`, atau `cancel` → Status booking: `Dibatalkan`
4. Webhook handler otomatis update status booking di database
5. User melihat status terupdate di Order History

