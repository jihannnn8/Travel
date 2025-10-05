class Booking {
  final String id;
  final String userId;
  final String packageId;
  final String packageTitle;
  final String packageImage;
  final double price;
  final String departureDate;
  final String pickupTime;
  final String paymentMethod;
  final String status;
  final DateTime bookingDate;
  final String paymentInfo;

  Booking({
    required this.id,
    required this.userId,
    required this.packageId,
    required this.packageTitle,
    required this.packageImage,
    required this.price,
    required this.departureDate,
    required this.pickupTime,
    required this.paymentMethod,
    required this.status,
    required this.bookingDate,
    required this.paymentInfo,
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
      'pickupTime': pickupTime,
      'paymentMethod': paymentMethod,
      'status': status,
      'bookingDate': bookingDate.toIso8601String(),
      'paymentInfo': paymentInfo,
    };
  }

  factory Booking.fromJson(Map<String, dynamic> json) {
    return Booking(
      id: json['id'],
      userId: json['userId'],
      packageId: json['packageId'],
      packageTitle: json['packageTitle'],
      packageImage: json['packageImage'],
      price: json['price'].toDouble(),
      departureDate: json['departureDate'],
      pickupTime: json['pickupTime'],
      paymentMethod: json['paymentMethod'],
      status: json['status'],
      bookingDate: DateTime.parse(json['bookingDate']),
      paymentInfo: json['paymentInfo'],
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
    String? pickupTime,
    String? paymentMethod,
    String? status,
    DateTime? bookingDate,
    String? paymentInfo,
  }) {
    return Booking(
      id: id ?? this.id,
      userId: userId ?? this.userId,
      packageId: packageId ?? this.packageId,
      packageTitle: packageTitle ?? this.packageTitle,
      packageImage: packageImage ?? this.packageImage,
      price: price ?? this.price,
      departureDate: departureDate ?? this.departureDate,
      pickupTime: pickupTime ?? this.pickupTime,
      paymentMethod: paymentMethod ?? this.paymentMethod,
      status: status ?? this.status,
      bookingDate: bookingDate ?? this.bookingDate,
      paymentInfo: paymentInfo ?? this.paymentInfo,
    );
  }
}
