import 'package:flutter/material.dart';
import 'package:intl/intl.dart';

import '../models/booking.dart';
import '../models/tour_package.dart';
import '../services/booking_service.dart';
import '../services/data_service.dart';
import '../widgets/rating_widget.dart';
import '../config/api_config.dart';
import 'payment_page.dart';

class PackageDetailPage extends StatefulWidget {
  final TourPackage package;

  const PackageDetailPage({super.key, required this.package});

  @override
  State<PackageDetailPage> createState() => _PackageDetailPageState();
}

class _PackageDetailPageState extends State<PackageDetailPage> {
  String? _selectedPickupTime;
  String? _selectedPaymentMethod;
  String? _selectedLokasiPenjemputan;
  bool _isLoading = false;
  bool get _isFormComplete =>
      _nameController.text.trim().isNotEmpty &&
      _selectedPickupDate != null &&
      _selectedPickupTime != null &&
      _selectedLokasiPenjemputan != null &&
      _selectedPaymentMethod != null;
  double _userRating = 0.0;
  DateTime? _selectedPickupDate;

  final TextEditingController _nameController = TextEditingController();
  final TextEditingController _pickupDateController = TextEditingController();
  final formatter = NumberFormat('#,###');

  @override
  void initState() {
    super.initState();
    // Default penjemputan: gunakan tanggal hari ini (real-time)
    _selectedPickupDate = DateTime.now();
    _pickupDateController.text = _formatDate(_selectedPickupDate!);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      appBar: AppBar(
        backgroundColor: Colors.transparent,
        elevation: 0,
        leading: IconButton(
          icon: Icon(Icons.arrow_back, color: Colors.blue.shade600),
          onPressed: () => Navigator.pop(context),
        ),
        title: Text(
          'Detail Paket',
          style: TextStyle(
            color: Colors.blue.shade600,
            fontWeight: FontWeight.bold,
          ),
        ),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(20),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // ===== Gambar paket =====
            ClipRRect(
              borderRadius: BorderRadius.circular(12),
              child: _buildImage(
                widget.package.imageUrl,
                width: double.infinity,
                height: 250,
                fit: BoxFit.cover,
              ),
            ),
            const SizedBox(height: 20),

            // ===== Judul & lokasi =====
            Text(
              widget.package.title,
              style: const TextStyle(fontSize: 24, fontWeight: FontWeight.bold),
            ),
            const SizedBox(height: 8),
            Text(
              widget.package.destination,
              style: TextStyle(fontSize: 16, color: Colors.grey.shade600),
            ),
            const SizedBox(height: 16),

            // ===== Rating =====
            Row(
              children: [
                Icon(Icons.star, color: Colors.amber.shade600, size: 20),
                const SizedBox(width: 4),
                Text(
                  '${widget.package.rating} (${widget.package.totalRatings} ulasan)',
                  style: TextStyle(fontSize: 16, color: Colors.grey.shade700),
                ),
              ],
            ),
            const SizedBox(height: 20),

            // ===== Beri Rating =====
            Container(
              padding: const EdgeInsets.all(16),
              decoration: BoxDecoration(
                color: Colors.amber.shade50,
                borderRadius: BorderRadius.circular(12),
                border: Border.all(color: Colors.amber.shade200),
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    'Berikan Rating',
                    style: TextStyle(
                      fontSize: 16,
                      fontWeight: FontWeight.bold,
                      color: Colors.amber.shade800,
                    ),
                  ),
                  const SizedBox(height: 8),
                  RatingWidget(
                    initialRating: _userRating,
                    onRatingChanged:
                        (rating) => setState(() => _userRating = rating),
                  ),
                  if (_userRating > 0)
                    Padding(
                      padding: const EdgeInsets.only(top: 8),
                      child: Text(
                        'Terima kasih atas rating Anda!',
                        style: TextStyle(
                          fontSize: 12,
                          color: Colors.green.shade600,
                        ),
                      ),
                    ),
                ],
              ),
            ),
            const SizedBox(height: 20),

            // ===== Harga & Durasi =====
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                _buildInfoItem(
                  title: 'Harga',
                  value: 'Rp ${formatter.format(widget.package.price)}',
                  color: Colors.blue.shade600,
                ),
                _buildInfoItem(title: 'Durasi', value: widget.package.duration),
              ],
            ),
            const SizedBox(height: 24),

            // ===== Rundown =====
            Text(
              'Rundown Perjalanan',
              style: TextStyle(
                fontSize: 18,
                fontWeight: FontWeight.bold,
                color: Colors.blue.shade600,
              ),
            ),
            const SizedBox(height: 16),
            ...widget.package.rundown.map(
              (item) => Padding(
                padding: const EdgeInsets.only(bottom: 8),
                child: Row(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Container(
                      width: 6,
                      height: 6,
                      margin: const EdgeInsets.only(top: 6, right: 12),
                      decoration: BoxDecoration(
                        color: Colors.blue.shade600,
                        shape: BoxShape.circle,
                      ),
                    ),
                    Expanded(
                      child: Text(
                        item,
                        style: TextStyle(
                          fontSize: 14,
                          color: Colors.grey.shade700,
                        ),
                      ),
                    ),
                  ],
                ),
              ),
            ),
            const SizedBox(height: 24),

            // ===== FORM PEMESANAN =====
            Text(
              'Detail Pemesanan',
              style: TextStyle(
                fontSize: 18,
                fontWeight: FontWeight.bold,
                color: Colors.blue.shade600,
              ),
            ),
            const SizedBox(height: 20),

            TextField(
              controller: _nameController,
              decoration: InputDecoration(
                labelText: 'Nama Pemesan',
                border: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(12),
                ),
                prefixIcon: const Icon(Icons.person_outline),
              ),
            ),
            const SizedBox(height: 16),

            // ===== Pilih Tanggal Jemput =====
            TextField(
              controller: _pickupDateController,
              readOnly: true,
              decoration: InputDecoration(
                labelText: 'Tanggal Penjemputan',
                border: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(12),
                ),
                prefixIcon: const Icon(Icons.calendar_today_outlined),
              ),
              onTap: _selectPickupDate, // fungsi pilih tanggal
            ),
            const SizedBox(height: 16),

            // ===== Pilih Waktu Jemput =====
            DropdownButtonFormField<String>(
              value: _selectedPickupTime,
              decoration: InputDecoration(
                labelText: 'Waktu Penjemputan',
                border: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(12),
                ),
                prefixIcon: const Icon(Icons.access_time_outlined),
              ),
              hint: const Text('Pilih waktu penjemputan'),
              items:
                  DataService.getPickupTimes()
                      .map(
                        (time) =>
                            DropdownMenuItem(value: time, child: Text(time)),
                      )
                      .toList(),
              onChanged: (value) => setState(() => _selectedPickupTime = value),
            ),
            const SizedBox(height: 16),

            // ===== Pilih Lokasi Penjemputan =====
            DropdownButtonFormField<String>(
              value: _selectedLokasiPenjemputan,
              decoration: InputDecoration(
                labelText: 'Lokasi Penjemputan',
                border: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(12),
                ),
                prefixIcon: const Icon(Icons.location_on_outlined),
              ),
              hint: const Text('Pilih lokasi penjemputan'),
              items: const [
                DropdownMenuItem(
                  value: 'bandara',
                  child: Text('Bandara'),
                ),
                DropdownMenuItem(
                  value: 'terminal',
                  child: Text('Terminal'),
                ),
              ],
              onChanged: (value) => setState(() => _selectedLokasiPenjemputan = value),
            ),
            const SizedBox(height: 24),

            // ===== Metode Pembayaran =====
            Text(
              'Metode Pembayaran',
              style: TextStyle(
                fontSize: 18,
                fontWeight: FontWeight.bold,
                color: Colors.blue.shade600,
              ),
            ),
            const SizedBox(height: 16),

            Column(
              children: [
                _buildPaymentTile('Bank Transfer', 'bank_transfer', Icons.account_balance),
                _buildPaymentTile('E-Wallet', 'e-wallet', Icons.wallet),
                _buildPaymentTile('Credit Card', 'credit_card', Icons.credit_card),
              ],
            ),
            const SizedBox(height: 24),

            // ===== Tombol Pesan =====
            SizedBox(
              width: double.infinity,
              height: 50,
              child: ElevatedButton(
                onPressed:
                    (!_isFormComplete || _isLoading) ? null : _bookPackage,
                style: ElevatedButton.styleFrom(
                  backgroundColor: (!_isFormComplete || _isLoading)
                      ? Colors.grey.shade400
                      : Colors.blue.shade600,
                  foregroundColor: Colors.white,
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(12),
                  ),
                ),
                child:
                    _isLoading
                        ? const CircularProgressIndicator(color: Colors.white)
                        : const Text(
                          'Pesan Sekarang',
                          style: TextStyle(
                            fontSize: 16,
                            fontWeight: FontWeight.bold,
                          ),
                        ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  // ================= HELPER =================

  Future<void> _selectPickupDate() async {
    final DateTime? picked = await showDatePicker(
      context: context,
      initialDate: _selectedPickupDate ?? DateTime.now(),
      firstDate: DateTime.now(),
      lastDate: _parseDate(widget.package.departureDate),
      helpText: 'Pilih tanggal penjemputan',
      locale: const Locale('id', 'ID'),
    );

    if (picked != null) {
      setState(() {
        _selectedPickupDate = picked;
        _pickupDateController.text = _formatDate(picked);
      });
    }
  }

  Widget _buildInfoItem({
    required String title,
    required String value,
    Color? color,
  }) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          title,
          style: TextStyle(fontSize: 14, color: Colors.grey.shade600),
        ),
        Text(
          value,
          style: TextStyle(
            fontSize: 16,
            fontWeight: FontWeight.bold,
            color: color ?? Colors.black,
          ),
        ),
      ],
    );
  }

  Widget _buildPaymentTile(String displayName, String value, IconData icon) {
    return RadioListTile<String>(
      title: Row(
        children: [
          Icon(icon, color: Colors.blue.shade600, size: 24),
          const SizedBox(width: 10),
          Text(displayName),
        ],
      ),
      value: value,
      groupValue: _selectedPaymentMethod,
      activeColor: Colors.blue.shade600,
      onChanged: (value) => setState(() => _selectedPaymentMethod = value),
    );
  }
  
  // Helper untuk convert display name ke backend format
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

  DateTime _parseDate(String dateString) {
    try {
      // Bersihkan string dari karakter tambahan dan whitespace
      String cleanedDate = dateString.trim();
      
      // Hapus bagian "at 3" atau karakter tambahan di akhir jika ada
      // Contoh: "20 January 2024 at 3" -> "20 January 2024"
      if (cleanedDate.contains(' at ')) {
        cleanedDate = cleanedDate.split(' at ')[0].trim();
      }
      
      // Format 1: "20 January 2024" atau "20 Januari 2024" (d MMMM y)
      try {
        final df1 = DateFormat('d MMMM y', 'id');
        return df1.parse(cleanedDate);
      } catch (e) {
        // Format 2: "20 January 2024" (d MMMM y) tanpa locale
        try {
          final df2 = DateFormat('d MMMM y');
          return df2.parse(cleanedDate);
        } catch (e) {
          // Format 3: "20 Januari 2024" (d MMMM y dengan locale Indonesia)
          try {
            final df3 = DateFormat('d MMMM y', 'id_ID');
            return df3.parse(cleanedDate);
          } catch (e) {
            // Format 4: "20 F 2024" (d M y) - format singkat
            try {
              final df4 = DateFormat('d M y', 'id');
              return df4.parse(cleanedDate);
            } catch (e) {
              // Format 5: ISO format "2024-01-20"
              try {
                return DateTime.parse(cleanedDate);
              } catch (e) {
                // Format 6: "20/01/2024"
                try {
                  final df6 = DateFormat('d/M/y');
                  return df6.parse(cleanedDate);
                } catch (e) {
                  // Format 7: "20-01-2024"
                  try {
                    final df7 = DateFormat('d-M-y');
                    return df7.parse(cleanedDate);
                  } catch (e) {
                    // Format 8: "01/20/2024" (format US)
                    try {
                      final df8 = DateFormat('M/d/y');
                      return df8.parse(cleanedDate);
                    } catch (e) {
                      // Jika semua gagal, return tanggal 7 hari dari sekarang sebagai fallback
                      print('Error parsing date: $dateString (cleaned: $cleanedDate)');
                      return DateTime.now().add(const Duration(days: 7));
                    }
                  }
                }
              }
            }
          }
        }
      }
    } catch (e) {
      print('Error parsing date: $dateString, error: $e');
      // Fallback: return tanggal 7 hari dari sekarang
      return DateTime.now().add(const Duration(days: 7));
    }
  }

  String _formatDate(DateTime date) {
    final df = DateFormat('d MMMM y', 'id');
    return df.format(date);
  }

  // ================= BOOKING HANDLER =================

  Future<void> _bookPackage() async {
    if (_nameController.text.trim().isEmpty) {
      _showError('Nama pemesan wajib diisi');
      return;
    }
    if (_selectedPickupDate == null ||
        _selectedPickupTime == null ||
        _selectedLokasiPenjemputan == null ||
        _selectedPaymentMethod == null) {
      _showError('Silakan lengkapi semua data pemesanan');
      return;
    }

    setState(() => _isLoading = true);

    try {
      // Format tanggal keberangkatan ke YYYY-MM-DD
      final tanggalKeberangkatan = DateFormat('yyyy-MM-dd').format(_parseDate(widget.package.departureDate));
      
      // Create booking via API
      final booking = await BookingService.createBooking(
        destinationId: widget.package.id,
        customerName: _nameController.text.trim(),
        tanggalKeberangkatan: tanggalKeberangkatan,
        waktuKeberangkatan: _selectedPickupTime!,
        lokasiPenjemputan: _selectedLokasiPenjemputan!,
        metodePembayaran: _selectedPaymentMethod!,
      );

      setState(() => _isLoading = false);

      if (booking != null && mounted) {
        // Create local booking object with customer name and pickup date for display
        // IMPORTANT: Gunakan customer name dari form input (_nameController), bukan dari user auth
        // Ini memastikan nama yang ditampilkan adalah nama yang diinput user di form
        final displayBooking = booking.copyWith(
          customerName: _nameController.text.trim(), // Nama dari form input
          lokasiPenjemputan: _selectedLokasiPenjemputan, // Lokasi dari form input
          pickupDate: _formatDate(_selectedPickupDate!),
        );

      Navigator.push(
        context,
          MaterialPageRoute(builder: (context) => PaymentPage(booking: displayBooking)),
      );
      } else {
        _showError('Gagal membuat booking. Silakan coba lagi.');
      }
    } catch (e) {
      setState(() => _isLoading = false);
      _showError('Terjadi kesalahan: $e');
    }
  }

  void _showError(String message) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(content: Text(message), backgroundColor: Colors.red),
    );
  }

  // Helper method to build Image widget (supports both asset and network)
  Widget _buildImage(
    String imageUrl, {
    double? width,
    double? height,
    BoxFit fit = BoxFit.cover,
  }) {
    // Check if it's a network image (http/https or /api/asset/ or /api/storage/)
    if (imageUrl.startsWith('http://') || 
        imageUrl.startsWith('https://') ||
        imageUrl.startsWith('/api/asset/') ||
        imageUrl.startsWith('/api/storage/')) {
      // Network image - fix URL if needed (handle all formats)
      final fixedUrl = ApiConfig.fixImageUrl(imageUrl);
      return Image.network(
        fixedUrl,
        width: width,
        height: height,
        fit: fit,
        loadingBuilder: (context, child, loadingProgress) {
          if (loadingProgress == null) return child;
          return Container(
            width: width,
            height: height,
            color: Colors.grey.shade200,
            child: Center(
              child: CircularProgressIndicator(
                value: loadingProgress.expectedTotalBytes != null
                    ? loadingProgress.cumulativeBytesLoaded /
                        loadingProgress.expectedTotalBytes!
                    : null,
              ),
            ),
          );
        },
        errorBuilder: (context, error, stackTrace) {
          return Container(
            width: width,
            height: height,
            color: Colors.grey.shade200,
            child: Icon(
              Icons.broken_image,
              color: Colors.grey.shade400,
              size: 40,
            ),
          );
        },
      );
    } else {
      // Asset image
      final assetPath = imageUrl.startsWith('assets/')
          ? imageUrl
          : 'assets/images/$imageUrl';
      return Image.asset(
        assetPath,
        width: width,
        height: height,
        fit: fit,
        errorBuilder: (context, error, stackTrace) {
          return Container(
            width: width,
            height: height,
            color: Colors.grey.shade200,
            child: Icon(
              Icons.broken_image,
              color: Colors.grey.shade400,
              size: 40,
            ),
          );
        },
      );
    }
  }
}
