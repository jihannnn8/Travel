class Booking {
  final String id;
  final String userId;
  final String packageId;
  final String packageTitle;
  final String packageImage;
  final double price;
  final String departureDate;
  final String? pickupDate;
  final String pickupTime;
  final String? customerName;
  final String? lokasiPenjemputan;
  final String paymentMethod;
  final String status;
  final DateTime bookingDate;
  final String paymentInfo;
  
  // Midtrans fields
  final String? kodeBooking;
  final String? orderId;
  final String? snapToken;
  final String? paymentStatus;

  Booking({
    required this.id,
    required this.userId,
    required this.packageId,
    required this.packageTitle,
    required this.packageImage,
    required this.price,
    required this.departureDate,
    this.pickupDate,
    required this.pickupTime,
    this.customerName,
    this.lokasiPenjemputan,
    required this.paymentMethod,
    required this.status,
    required this.bookingDate,
    required this.paymentInfo,
    this.kodeBooking,
    this.orderId,
    this.snapToken,
    this.paymentStatus,
  });

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'userId': userId,
      'packageId': packageId,
      'packageTitle': packageTitle,
      'packageImage': packageImage,
      'price': price,
      'departureDate': departureDate,
      'pickupDate': pickupDate,
      'pickupTime': pickupTime,
      'customerName': customerName,
      'lokasiPenjemputan': lokasiPenjemputan,
      'paymentMethod': paymentMethod,
      'status': status,
      'bookingDate': bookingDate.toIso8601String(),
      'paymentInfo': paymentInfo,
      'kodeBooking': kodeBooking,
      'orderId': orderId,
      'snapToken': snapToken,
      'paymentStatus': paymentStatus,
    };
  }

  factory Booking.fromJson(Map<String, dynamic> json) {
    // Helper untuk convert ke string dengan null safety
    String toStringOrEmpty(dynamic value) {
      if (value == null) return '';
      return value.toString();
    }
    
    // Helper untuk convert ke double dengan null safety
    double toDoubleOrZero(dynamic value) {
      if (value == null) return 0.0;
      if (value is double) return value;
      if (value is int) return value.toDouble();
      if (value is String) {
        try {
          return double.parse(value);
        } catch (e) {
          return 0.0;
        }
      }
      return 0.0;
    }
    
    return Booking(
      id: toStringOrEmpty(json['id']),
      userId: toStringOrEmpty(json['userId'] ?? json['user_id']),
      packageId: toStringOrEmpty(json['packageId'] ?? json['package_id'] ?? json['destinationId'] ?? json['destination_id']),
      packageTitle: toStringOrEmpty(json['packageTitle'] ?? json['package_title'] ?? json['destinationTitle'] ?? json['destination_title']),
      packageImage: toStringOrEmpty(json['packageImage'] ?? json['package_image'] ?? json['destinationImage'] ?? json['destination_image']),
      price: toDoubleOrZero(json['price'] ?? json['totalHarga'] ?? json['total_harga']),
      departureDate: toStringOrEmpty(json['departureDate'] ?? json['departure_date'] ?? json['tanggalKeberangkatan'] ?? json['tanggal_keberangkatan']),
      pickupDate: json['pickupDate']?.toString() ?? json['pickup_date']?.toString(),
      pickupTime: toStringOrEmpty(json['pickupTime'] ?? json['pickup_time'] ?? json['waktuKeberangkatan'] ?? json['waktu_keberangkatan']),
      customerName: json['customerName']?.toString() ?? json['customer_name']?.toString(),
      lokasiPenjemputan: json['lokasiPenjemputan']?.toString() ?? json['lokasi_penjemputan']?.toString(),
      paymentMethod: toStringOrEmpty(json['paymentMethod'] ?? json['payment_method'] ?? json['metodePembayaran'] ?? json['metode_pembayaran']),
      status: toStringOrEmpty(json['status'] ?? 'pending'),
      bookingDate: json['bookingDate'] != null || json['booking_date'] != null || json['tanggalBooking'] != null || json['tanggal_booking'] != null
          ? DateTime.tryParse(toStringOrEmpty(json['bookingDate'] ?? json['booking_date'] ?? json['tanggalBooking'] ?? json['tanggal_booking'])) ?? DateTime.now()
          : DateTime.now(),
      paymentInfo: toStringOrEmpty(json['paymentInfo'] ?? json['payment_info'] ?? ''),
      kodeBooking: json['kodeBooking']?.toString() ?? json['kode_booking']?.toString(),
      orderId: json['orderId']?.toString() ?? json['midtrans_order_id']?.toString(),
      snapToken: json['snapToken']?.toString() ?? json['midtrans_payment_token']?.toString(),
      paymentStatus: json['paymentStatus']?.toString() ?? json['payment_status']?.toString(),
    );
  }

  Booking copyWith({
    String? id,
    String? userId,
    String? packageId,
    String? packageTitle,
    String? packageImage,
    double? price,
    String? departureDate,
    String? pickupDate,
    String? pickupTime,
    String? customerName,
    String? lokasiPenjemputan,
    String? paymentMethod,
    String? status,
    DateTime? bookingDate,
    String? paymentInfo,
    String? kodeBooking,
    String? orderId,
    String? snapToken,
    String? paymentStatus,
  }) {
    return Booking(
      id: id ?? this.id,
      userId: userId ?? this.userId,
      packageId: packageId ?? this.packageId,
      packageTitle: packageTitle ?? this.packageTitle,
      packageImage: packageImage ?? this.packageImage,
      price: price ?? this.price,
      departureDate: departureDate ?? this.departureDate,
      pickupDate: pickupDate ?? this.pickupDate,
      pickupTime: pickupTime ?? this.pickupTime,
      customerName: customerName ?? this.customerName,
      lokasiPenjemputan: lokasiPenjemputan ?? this.lokasiPenjemputan,
      paymentMethod: paymentMethod ?? this.paymentMethod,
      status: status ?? this.status,
      bookingDate: bookingDate ?? this.bookingDate,
      paymentInfo: paymentInfo ?? this.paymentInfo,
      kodeBooking: kodeBooking ?? this.kodeBooking,
      orderId: orderId ?? this.orderId,
      snapToken: snapToken ?? this.snapToken,
      paymentStatus: paymentStatus ?? this.paymentStatus,
    );
  }
}
