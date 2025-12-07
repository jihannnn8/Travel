import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import '../models/booking.dart';
import 'midtrans_snap_page.dart';

class PaymentPage extends StatelessWidget {
  final Booking booking;

  const PaymentPage({super.key, required this.booking});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Pembayaran'),
        backgroundColor: Colors.blue.shade600,
      ),
      body: Padding(
        padding: const EdgeInsets.all(20),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(
              'Informasi Pembayaran',
              style: TextStyle(
                fontSize: 20,
                fontWeight: FontWeight.bold,
                color: Colors.blue.shade600,
              ),
            ),
            const SizedBox(height: 24),

            // ===== Detail Booking =====
            if (booking.customerName != null && booking.customerName!.isNotEmpty)
              _buildRow('Nama Pemesan', booking.customerName!),
            _buildRow('Paket Wisata', booking.packageTitle),
            _buildRow('Harga', 'Rp ${booking.price.toStringAsFixed(0)}'),
            _buildRow(
              'Tanggal Jemput',
              booking.pickupDate != null 
                  ? '${booking.pickupDate} - ${booking.pickupTime}'
                  : booking.pickupTime,
            ),
            if (booking.lokasiPenjemputan != null && booking.lokasiPenjemputan!.isNotEmpty)
              _buildRow('Lokasi Penjemputan', _getLokasiPenjemputanDisplayName(booking.lokasiPenjemputan!)),
            _buildRow('Metode Pembayaran', _getPaymentMethodDisplayName(booking.paymentMethod)),
            const Divider(height: 30, thickness: 1),

            // ===== Midtrans Payment atau Manual Payment =====
            if (booking.snapToken != null && booking.snapToken!.isNotEmpty)
              _buildMidtransPayment(context, booking)
            else
              _buildManualPayment(context, booking),

            const Spacer(),

            // ===== Tombol Selesai =====
            SizedBox(
              width: double.infinity,
              height: 50,
              child: ElevatedButton(
                onPressed: () => Navigator.pop(context),
                style: ElevatedButton.styleFrom(
                  backgroundColor: const Color(0xFF2196F3),
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(12),
                  ),
                ),
                child: const Text(
                  'Selesai',
                  style: TextStyle(
                    fontSize: 16,
                    fontWeight: FontWeight.bold,
                    color: Colors.white, // ✅ teks putih
                  ),
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  // ================== Widget Helper ==================

  Widget _buildRow(String label, String value) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 8),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Expanded(
            flex: 2,
            child: Text(
              label,
              style: const TextStyle(fontSize: 15, color: Colors.black54),
            ),
          ),
          Expanded(
            flex: 3,
            child: Text(
              value,
              textAlign: TextAlign.right,
              style: const TextStyle(fontSize: 15, fontWeight: FontWeight.bold),
            ),
          ),
        ],
      ),
    );
  }

  // Widget untuk menampilkan info rekening & tombol copy
  Widget _buildAccountInfo(BuildContext context, String method) {
    final accountNumber = _getAccountNumber(method);

    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.grey.shade100,
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: Colors.grey.shade300),
      ),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Expanded(
            child: Text(
              accountNumber,
              style: const TextStyle(fontSize: 16, fontWeight: FontWeight.bold),
            ),
          ),
          IconButton(
            icon: const Icon(Icons.copy, color: Color(0xFF2196F3)),
            onPressed: () {
              Clipboard.setData(ClipboardData(text: accountNumber));
              ScaffoldMessenger.of(context).showSnackBar(
                const SnackBar(
                  content: Text('Nomor rekening berhasil disalin!'),
                  backgroundColor: Color(0xFF2196F3),
                  duration: Duration(seconds: 2),
                ),
              );
            },
          ),
        ],
      ),
    );
  }

  // Helper untuk convert backend format ke display name
  String _getPaymentMethodDisplayName(String? backendValue) {
    switch (backendValue) {
      case 'bank_transfer':
        return 'Bank Transfer';
      case 'e-wallet':
        return 'E-Wallet';
      case 'credit_card':
        return 'Credit Card';
      case 'transfer':
        return 'Transfer';
      case 'echannel':
        return 'E-Channel';
      default:
        return backendValue ?? 'Tidak diketahui';
    }
  }

  // Helper untuk convert lokasi penjemputan ke display name
  String _getLokasiPenjemputanDisplayName(String? backendValue) {
    switch (backendValue) {
      case 'bandara':
        return 'Bandara';
      case 'terminal':
        return 'Terminal';
      default:
        return backendValue ?? 'Tidak diketahui';
    }
  }

  // Nomor rekening / VA bank (untuk fallback manual payment)
  String _getAccountNumber(String method) {
    switch (method) {
      case 'Bank Transfer':
      case 'bank_transfer':
      case 'transfer':
        return 'Bank Transfer - Silakan gunakan Midtrans untuk pembayaran';
      case 'E-Wallet':
      case 'e-wallet':
        return 'E-Wallet - Silakan gunakan Midtrans untuk pembayaran';
      case 'Credit Card':
      case 'credit_card':
        return 'Credit Card - Silakan gunakan Midtrans untuk pembayaran';
      default:
        return 'Silakan gunakan Midtrans untuk pembayaran';
    }
  }

  // Widget untuk Midtrans Payment
  Widget _buildMidtransPayment(BuildContext context, Booking booking) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Container(
          padding: const EdgeInsets.all(16),
          decoration: BoxDecoration(
            color: Colors.blue.shade50,
            borderRadius: BorderRadius.circular(12),
            border: Border.all(color: Colors.blue.shade200),
          ),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                children: [
                  Icon(Icons.payment, color: Colors.blue.shade600),
                  const SizedBox(width: 8),
                  Text(
                    'Pembayaran via Midtrans',
                    style: TextStyle(
                      fontSize: 16,
                      fontWeight: FontWeight.bold,
                      color: Colors.blue.shade600,
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 12),
              Text(
                'Klik tombol di bawah untuk melakukan pembayaran melalui Midtrans. Anda dapat memilih berbagai metode pembayaran seperti Bank Transfer, E-Wallet, atau Credit Card.',
                style: TextStyle(
                  fontSize: 14,
                  color: Colors.grey.shade700,
                ),
              ),
            ],
          ),
        ),
        const SizedBox(height: 16),
        SizedBox(
          width: double.infinity,
          height: 50,
          child: ElevatedButton.icon(
            onPressed: () {
              Navigator.push(
                context,
                MaterialPageRoute(
                  builder: (context) => MidtransSnapPage(
                    snapToken: booking.snapToken!,
                  ),
                ),
              ).then((_) {
                Navigator.pop(context);
              });
            },
            icon: const Icon(Icons.payment, color: Colors.white),
            label: const Text(
              'Bayar via Midtrans',
              style: TextStyle(
                fontSize: 16,
                fontWeight: FontWeight.bold,
                color: Colors.white,
              ),
            ),
            style: ElevatedButton.styleFrom(
              backgroundColor: Colors.blue.shade600,
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(12),
              ),
            ),
          ),
        ),
      ],
    );
  }

  // Widget untuk Manual Payment (fallback)
  Widget _buildManualPayment(BuildContext context, Booking booking) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          'Silakan lakukan pembayaran ke rekening berikut:',
          style: TextStyle(fontSize: 16, color: Colors.grey.shade700),
        ),
        const SizedBox(height: 12),
        _buildAccountInfo(context, booking.paymentMethod),
      ],
    );
  }
}
