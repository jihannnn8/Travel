import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:intl/date_symbol_data_local.dart';
import '../services/booking_service.dart';
import '../models/booking.dart';
import '../config/api_config.dart';
import 'midtrans_snap_page.dart';

class OrderHistoryPage extends StatefulWidget {
  final String? snapToken;
  final String? bookingId;
  
  const OrderHistoryPage({
    super.key,
    this.snapToken,
    this.bookingId,
  });

  @override
  State<OrderHistoryPage> createState() => _OrderHistoryPageState();
}

class _OrderHistoryPageState extends State<OrderHistoryPage> {
  List<Booking> _bookings = [];
  bool _isLoading = true;
  bool _hasOpenedSnap = false;
  bool _localeInitialized = false;

  @override
  void initState() {
    super.initState();
    _initializeLocale().then((_) {
      _loadBookings();
    });
  }
  
  Future<void> _initializeLocale() async {
    try {
      await initializeDateFormatting('id_ID', null);
      if (mounted) {
        setState(() => _localeInitialized = true);
      }
    } catch (e) {
      if (mounted) {
        setState(() => _localeInitialized = true);
      }
    }
  }

  @override
  void didChangeDependencies() {
    super.didChangeDependencies();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      if (!_isLoading && mounted) {
        _loadBookings();
      }
    });
  }

  Future<void> _loadBookings() async {
    setState(() => _isLoading = true);

    final bookings = await BookingService.getBookings();
    if (mounted) {
      setState(() {
        _bookings = bookings;
        _isLoading = false;
      });

      if (widget.snapToken != null &&
          widget.snapToken!.isNotEmpty &&
          !_hasOpenedSnap) {
        _hasOpenedSnap = true;

        await Future.delayed(const Duration(milliseconds: 500));
        if (mounted) {
          _openMidtransSnap(widget.snapToken!);
        }
      }
    }
  }
  
  void _openMidtransSnap(String snapToken) {
    Navigator.push(
      context,
      MaterialPageRoute(
        builder: (context) => MidtransSnapPage(snapToken: snapToken),
      ),
    ).then((_) => _loadBookings());
  }

  Color _getStatusColor(String status) {
    switch (status.toLowerCase()) {
      case 'menunggu pembayaran':
        return Colors.orange;
      case 'dikonfirmasi':
        return Colors.blue;
      case 'selesai':
        return Colors.green;
      case 'dibatalkan':
        return Colors.red;
      default:
        return Colors.grey;
    }
  }
  
  String _formatDate(String dateString) {
    try {
      DateTime date = DateTime.parse(dateString);

      if (_localeInitialized) {
        try {
          return DateFormat('EEEE, dd MMMM yyyy', 'id_ID').format(date);
        } catch (e) {}
      }
      return DateFormat('EEEE, dd MMMM yyyy').format(date);
    } catch (e) {
      return dateString;
    }
  }
  
  String _formatTime(String timeString) {
    return timeString;
  }
  
  String _formatBookingDate(DateTime date) {
    if (_localeInitialized) {
      try {
        return DateFormat('EEEE, dd MMMM yyyy', 'id_ID').format(date);
      } catch (e) {}
    }
    return DateFormat('EEEE, dd MMMM yyyy').format(date);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      appBar: AppBar(
        backgroundColor: Colors.transparent,
        elevation: 0,
        title: Text(
          'Riwayat Pesanan',
          style: TextStyle(
            color: Colors.blue.shade600,
            fontWeight: FontWeight.bold,
          ),
        ),
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh),
            color: Colors.blue.shade600,
            onPressed: _loadBookings,
          ),
        ],
      ),
      body: RefreshIndicator(
        onRefresh: _loadBookings,
        child: _isLoading
            ? const Center(child: CircularProgressIndicator())
            : _bookings.isEmpty
                ? _buildEmptyState()
                : _buildBookingsList(),
      ),
    );
  }

  Widget _buildEmptyState() {
    return Center(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Icon(Icons.history, size: 80, color: Colors.grey.shade400),
          const SizedBox(height: 16),
          Text(
            'Belum ada pesanan',
            style: TextStyle(
              fontSize: 18,
              fontWeight: FontWeight.bold,
              color: Colors.grey.shade600,
            ),
          ),
          const SizedBox(height: 8),
          Text(
            'Mulai jelajahi paket wisata yang menarik',
            style: TextStyle(fontSize: 14, color: Colors.grey.shade500),
          ),
        ],
      ),
    );
  }

  Widget _buildBookingsList() {
    return ListView.builder(
      padding: const EdgeInsets.all(16),
      itemCount: _bookings.length,
      itemBuilder: (context, index) {
        final booking = _bookings[index];

        return Container(
          margin: const EdgeInsets.only(bottom: 16),
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(16),
            boxShadow: [
              BoxShadow(
                color: Colors.grey.withValues(alpha: 0.1),
                spreadRadius: 1,
                blurRadius: 8,
                offset: const Offset(0, 2),
              ),
            ],
          ),
          child: _buildBookingCard(booking),
        );
      },
    );
  }

  Widget _buildBookingCard(Booking booking) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        // --- IMAGE + TITLE ---
        Container(
          height: 140,
          child: Row(
            children: [
              ClipRRect(
                borderRadius: const BorderRadius.only(
                  topLeft: Radius.circular(16),
                ),
                child: _buildPackageImage(booking.packageImage),
              ),
              Expanded(
                child: Padding(
                  padding: const EdgeInsets.all(16),
                  child: Column(
                    mainAxisAlignment: MainAxisAlignment.center,
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        booking.packageTitle,
                        style: const TextStyle(
                          fontSize: 16,
                          fontWeight: FontWeight.bold,
                          color: Colors.black87,
                        ),
                        maxLines: 2,
                        overflow: TextOverflow.ellipsis,
                      ),
                      const SizedBox(height: 8),
                      Text(
                        'Rp ${_formatPrice(booking.price)}',
                        style: TextStyle(
                          fontSize: 16,
                          color: Colors.blue.shade600,
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                    ],
                  ),
                ),
              ),
            ],
          ),
        ),

        // --- DETAILS ---
        Padding(
          padding: const EdgeInsets.all(16),
          child: _buildBookingDetails(booking),
        ),
      ],
    );
  }

  Widget _buildPackageImage(String? imageUrl) {
    if (imageUrl == null || imageUrl.isEmpty) {
      return Container(
        width: 140,
        height: 140,
        color: Colors.blue.shade100,
        child: Icon(Icons.travel_explore, color: Colors.blue.shade600, size: 40),
      );
    }

    final fixedUrl = ApiConfig.fixImageUrl(imageUrl);
    return _buildNetworkImage(fixedUrl);
  }

  Widget _buildNetworkImage(String imageUrl) {
    return Image.network(
      imageUrl,
      width: 140,
      height: 140,
      fit: BoxFit.cover,
      loadingBuilder: (context, child, progress) {
        if (progress == null) return child;

        return Container(
          width: 140,
          height: 140,
          color: Colors.blue.shade100,
          child: Center(
            child: CircularProgressIndicator(
              value: progress.expectedTotalBytes != null
                  ? progress.cumulativeBytesLoaded /
                      progress.expectedTotalBytes!
                  : null,
            ),
          ),
        );
      },
      errorBuilder: (_, __, ___) {
        return Container(
          width: 140,
          height: 140,
          color: Colors.blue.shade100,
          child: Icon(Icons.travel_explore, color: Colors.blue.shade600, size: 40),
        );
      },
      headers: const {'Accept': 'image/*'},
    );
  }

  Widget _buildBookingDetails(Booking booking) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        // ------------ STATUS ------------
        Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            Text('Status', style: TextStyle(fontSize: 14, color: Colors.grey.shade600)),
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 4),
              decoration: BoxDecoration(
                color: _getStatusColor(booking.status).withValues(alpha: 0.1),
                borderRadius: BorderRadius.circular(12),
                border: Border.all(color: _getStatusColor(booking.status)),
              ),
              child: Text(
                booking.status,
                style: TextStyle(
                  fontSize: 12,
                  fontWeight: FontWeight.bold,
                  color: _getStatusColor(booking.status),
                ),
              ),
            ),
          ],
        ),

        const SizedBox(height: 12),
        Divider(color: Colors.grey.shade300),
        const SizedBox(height: 12),

        // ------------ INFORMASI PEMESAN ------------
        Text('Informasi Pemesan',
            style: TextStyle(fontSize: 14, fontWeight: FontWeight.bold)),
        const SizedBox(height: 8),

        if (booking.customerName != null && booking.customerName!.isNotEmpty)
          _buildDetailRow(Icons.person_outline, 'Nama Pemesan', booking.customerName!),

        const SizedBox(height: 12),
        Divider(color: Colors.grey.shade300),
        const SizedBox(height: 12),

        // ------------ DETAIL PERJALANAN ------------
        Text('Detail Perjalanan',
            style: TextStyle(fontSize: 14, fontWeight: FontWeight.bold)),
        const SizedBox(height: 8),

        _buildDetailRow(Icons.calendar_today, 'Tanggal Keberangkatan',
            _formatDate(booking.departureDate)),
        const SizedBox(height: 12),

        if (booking.pickupDate != null && booking.pickupDate!.isNotEmpty)
          _buildDetailRow(Icons.event, 'Tanggal Penjemputan',
              _formatDate(booking.pickupDate!)),

        const SizedBox(height: 12),

        _buildDetailRow(
            Icons.access_time, 'Waktu Penjemputan', _formatTime(booking.pickupTime)),
        const SizedBox(height: 12),

        if (booking.lokasiPenjemputan != null &&
            booking.lokasiPenjemputan!.isNotEmpty)
          _buildDetailRow(Icons.location_on, 'Lokasi Penjemputan',
              _formatLokasiPenjemputan(booking.lokasiPenjemputan!)),

        const SizedBox(height: 12),
        Divider(color: Colors.grey.shade300),
        const SizedBox(height: 12),

        // ------------ PEMBAYARAN ------------
        Text('Informasi Pembayaran',
            style: TextStyle(fontSize: 14, fontWeight: FontWeight.bold)),
        const SizedBox(height: 8),

        _buildDetailRow(Icons.attach_money, 'Total Harga',
            'Rp ${_formatPrice(booking.price)}',
            isHighlight: true),
        const SizedBox(height: 12),

        _buildDetailRow(Icons.payment, 'Metode Pembayaran',
            _formatPaymentMethod(booking.paymentMethod)),
        const SizedBox(height: 12),

        if (booking.paymentStatus != null && booking.paymentStatus!.isNotEmpty)
          _buildDetailRow(Icons.info_outline, 'Status Pembayaran',
              _formatPaymentStatus(booking.paymentStatus!)),

        const SizedBox(height: 12),
        Divider(color: Colors.grey.shade300),
        const SizedBox(height: 12),

        // ------------ PEMESANAN ------------
        Text('Informasi Pemesanan',
            style: TextStyle(fontSize: 14, fontWeight: FontWeight.bold)),
        const SizedBox(height: 8),

        _buildDetailRow(Icons.history, 'Tanggal Pemesanan',
            _formatBookingDate(booking.bookingDate)),

        if (booking.kodeBooking != null && booking.kodeBooking!.isNotEmpty) ...[
          const SizedBox(height: 12),
          _buildDetailRow(Icons.receipt, 'Kode Booking', booking.kodeBooking!,
              isHighlight: true),
        ],

        if (booking.orderId != null && booking.orderId!.isNotEmpty) ...[
          const SizedBox(height: 12),
          _buildDetailRow(Icons.tag, 'Order ID', booking.orderId!),
        ],

        // ------------ BUTTON AKSI ------------
        if (booking.status == 'Menunggu Pembayaran') ...[
          const SizedBox(height: 16),
          SizedBox(
            width: double.infinity,
            child: ElevatedButton(
              onPressed: () => _showPaymentInfo(booking),
              style: ElevatedButton.styleFrom(
                backgroundColor: Colors.blue.shade600,
                foregroundColor: Colors.white,
              ),
              child: const Text('Lihat Info Pembayaran'),
            ),
          ),
        ],

        if (booking.status == 'Dikonfirmasi') ...[
          const SizedBox(height: 16),
          SizedBox(
            width: double.infinity,
            child: OutlinedButton(
              onPressed: () => _cancelBooking(booking),
              style: OutlinedButton.styleFrom(
                foregroundColor: Colors.red,
                side: const BorderSide(color: Colors.red),
              ),
              child: const Text('Batalkan'),
            ),
          ),
        ],
      ],
    );
  }

  Widget _buildDetailRow(IconData icon, String label, String value,
      {bool isHighlight = false}) {
    return Row(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Container(
          padding: const EdgeInsets.all(6),
          decoration: BoxDecoration(
            color: isHighlight ? Colors.blue.shade100 : Colors.blue.shade50,
            borderRadius: BorderRadius.circular(8),
          ),
          child: Icon(
            icon,
            size: 16,
            color: isHighlight ? Colors.blue.shade700 : Colors.blue.shade600,
          ),
        ),
        const SizedBox(width: 12),
        Expanded(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(label,
                  style: TextStyle(
                      fontSize: 11, color: Colors.grey.shade600)),
              const SizedBox(height: 4),
              Text(
                value,
                style: TextStyle(
                  fontSize: 14,
                  fontWeight:
                      isHighlight ? FontWeight.bold : FontWeight.w600,
                  color:
                      isHighlight ? Colors.blue.shade700 : Colors.black87,
                ),
              ),
            ],
          ),
        ),
      ],
    );
  }
  
  String _formatPrice(double price) {
    return NumberFormat('#,###', 'id_ID').format(price);
  }
  
  String _formatPaymentMethod(String method) {
    final map = {
      'bank_transfer': 'Transfer Bank',
      'e-wallet': 'E-Wallet',
      'credit_card': 'Kartu Kredit',
      'transfer': 'Transfer',
      'echannel': 'Mandiri Virtual Account',
    };
    return map[method.toLowerCase()] ?? method;
  }

  String _formatLokasiPenjemputan(String lokasi) {
    final map = {
      'bandara': 'Bandara',
      'terminal': 'Terminal',
    };
    return map[lokasi.toLowerCase()] ?? lokasi;
  }

  String _formatPaymentStatus(String status) {
    final map = {
      'pending': 'Menunggu Pembayaran',
      'paid': 'Sudah Dibayar',
      'settlement': 'Sudah Dibayar',
      'capture': 'Sudah Dibayar',
      'failed': 'Gagal',
      'deny': 'Ditolak',
      'expire': 'Kedaluwarsa',
      'cancel': 'Dibatalkan',
    };
    return map[status.toLowerCase()] ?? status;
  }

  void _showPaymentInfo(Booking booking) {
    if (booking.snapToken != null && booking.snapToken!.isNotEmpty) {
      _openMidtransSnap(booking.snapToken!);
      return;
    }

    showDialog(
      context: context,
      builder: (_) => AlertDialog(
        title: const Text('Informasi Pembayaran'),
        content: Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text('Paket: ${booking.packageTitle}'),
            Text('Harga: Rp ${_formatPrice(booking.price)}'),
            Text('Metode: ${_formatPaymentMethod(booking.paymentMethod)}'),
            if (booking.kodeBooking != null)
              Text('Kode Booking: ${booking.kodeBooking}'),
            const SizedBox(height: 8),
            Text(
              booking.paymentInfo.isNotEmpty
                  ? booking.paymentInfo
                  : 'Silakan lakukan pembayaran melalui Midtrans',
              style: TextStyle(fontSize: 12, color: Colors.grey.shade600),
            ),
          ],
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context),
            child: const Text('OK'),
          ),
        ],
      ),
    );
  }

  Future<void> _cancelBooking(Booking booking) async {
    final confirm = await showDialog<bool>(
      context: context,
      builder: (_) => AlertDialog(
        title: const Text('Batalkan Pesanan?'),
        content: const Text(
          'Jika Anda membatalkan pesanan, uang yang sudah dibayarkan tidak dapat dikembalikan. Tetap lanjutkan?',
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context, false),
            child: const Text('Tidak'),
          ),
          TextButton(
            onPressed: () => Navigator.pop(context, true),
            style: TextButton.styleFrom(foregroundColor: Colors.red),
            child: const Text('Ya, Batalkan'),
          ),
        ],
      ),
    );

    if (confirm != true) return;

    await BookingService.updateBookingStatus(booking.id, 'Dibatalkan');

    if (!mounted) return;

    setState(() {
      _bookings.removeWhere((b) => b.id == booking.id);
    });

    ScaffoldMessenger.of(context).showSnackBar(
      const SnackBar(
        content: Text('Pesanan dibatalkan'),
        backgroundColor: Colors.red,
      ),
    );
  }
}
