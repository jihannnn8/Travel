class TourPackage {
  final String id;
  final String title;
  final String description;
  final String imageUrl;
  final double price;
  final String duration;
  final String departureDate;
  final double rating;
  final int totalRatings;
  final List<String> rundown;
  final String destination;

  TourPackage({
    required this.id,
    required this.title,
    required this.description,
    required this.imageUrl,
    required this.price,
    required this.duration,
    required this.departureDate,
    required this.rating,
    required this.totalRatings,
    required this.rundown,
    required this.destination,
  });

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'title': title,
      'description': description,
      'imageUrl': imageUrl,
      'price': price,
      'duration': duration,
      'departureDate': departureDate,
      'rating': rating,
      'totalRatings': totalRatings,
      'rundown': rundown,
      'destination': destination,
    };
  }

  factory TourPackage.fromJson(Map<String, dynamic> json) {
    // Handle both camelCase and snake_case field names
    final imageUrl = json['imageUrl'] ?? json['image_url'] ?? json['image'] ?? '';
    
    return TourPackage(
      id: json['id']?.toString() ?? '',
      title: json['title'] ?? '',
      description: json['description'] ?? '',
      imageUrl: imageUrl,
      price: (json['price'] is num) ? json['price'].toDouble() : double.tryParse(json['price']?.toString() ?? '0') ?? 0.0,
      duration: json['duration'] ?? '',
      departureDate: json['departureDate'] ?? json['departure_date'] ?? '',
      rating: (json['rating'] is num) ? json['rating'].toDouble() : double.tryParse(json['rating']?.toString() ?? '0') ?? 0.0,
      totalRatings: json['totalRatings'] ?? json['total_ratings'] ?? 0,
      rundown: json['rundown'] != null 
          ? List<String>.from(json['rundown']) 
          : (json['rundown_list'] != null ? List<String>.from(json['rundown_list']) : []),
      destination: json['destination'] ?? '',
    );
  }
}
