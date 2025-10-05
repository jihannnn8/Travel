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
    return TourPackage(
      id: json['id'],
      title: json['title'],
      description: json['description'],
      imageUrl: json['imageUrl'],
      price: json['price'].toDouble(),
      duration: json['duration'],
      departureDate: json['departureDate'],
      rating: json['rating'].toDouble(),
      totalRatings: json['totalRatings'],
      rundown: List<String>.from(json['rundown']),
      destination: json['destination'],
    );
  }
}
