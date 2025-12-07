import 'dart:convert';
import '../models/tour_package.dart';
import '../config/api_config.dart';
import 'api_service.dart';

class DestinationService {
  /// Get all destinations (tour packages) from API
  /// Returns list of TourPackage or null if error
  static Future<List<TourPackage>?> getDestinations() async {
    try {
      final response = await ApiService.get(ApiConfig.destinations);

      if (response.statusCode == 200) {
        final responseData = json.decode(response.body);

        if (responseData['success'] == true) {
          final destinationsData = responseData['data']['destinations'] as List;
          
          // Debug: print first destination untuk melihat format data
          if (destinationsData.isNotEmpty) {
            print('DestinationService - First destination data: ${destinationsData.first}');
            print('DestinationService - ImageUrl: ${destinationsData.first['imageUrl']}');
          }
          
          return destinationsData.map((destination) {
            // Fix image URL - handle semua format (http, /api/asset/, /api/storage/, assets/)
            if (destination['imageUrl'] != null || destination['image_url'] != null) {
              final imageUrl = destination['imageUrl'] ?? destination['image_url'] ?? '';
              if (imageUrl.startsWith('assets/')) {
                destination['imageUrl'] = imageUrl; // Asset images tidak perlu di-fix
              } else {
                // Gunakan fixImageUrl untuk semua URL lainnya (termasuk /api/asset/ dan /api/storage/)
                destination['imageUrl'] = ApiConfig.fixImageUrl(imageUrl);
              }
            }
            final package = TourPackage.fromJson(destination);
            print('DestinationService - Parsed package imageUrl: ${package.imageUrl}');
            return package;
          }).toList();
        } else {
          print('API Error: ${responseData['message']}');
          return null;
        }
      } else if (response.statusCode == 401) {
        // Unauthorized - user not logged in
        print('Unauthorized: Please login first');
        return null;
      } else {
        print('Failed to fetch destinations: ${response.statusCode}');
        return null;
      }
    } catch (e) {
      print('Error fetching destinations: $e');
      return null;
    }
  }

  /// Get single destination by ID from API
  /// Returns TourPackage or null if error
  static Future<TourPackage?> getDestinationById(String id) async {
    try {
      final response = await ApiService.get(ApiConfig.destinationById(id));

      if (response.statusCode == 200) {
        final responseData = json.decode(response.body);

        if (responseData['success'] == true) {
          final destinationData = responseData['data']['destination'];
          // Fix image URL - handle semua format (http, /api/asset/, /api/storage/, assets/)
          if (destinationData['imageUrl'] != null || destinationData['image_url'] != null) {
            final imageUrl = destinationData['imageUrl'] ?? destinationData['image_url'] ?? '';
            if (imageUrl.startsWith('assets/')) {
              destinationData['imageUrl'] = imageUrl; // Asset images tidak perlu di-fix
            } else {
              // Gunakan fixImageUrl untuk semua URL lainnya (termasuk /api/asset/ dan /api/storage/)
              destinationData['imageUrl'] = ApiConfig.fixImageUrl(imageUrl);
            }
          }
          return TourPackage.fromJson(destinationData);
        } else {
          print('API Error: ${responseData['message']}');
          return null;
        }
      } else if (response.statusCode == 401) {
        // Unauthorized - user not logged in
        print('Unauthorized: Please login first');
        return null;
      } else if (response.statusCode == 404) {
        // Destination not found
        print('Destination not found');
        return null;
      } else {
        print('Failed to fetch destination: ${response.statusCode}');
        return null;
      }
    } catch (e) {
      print('Error fetching destination: $e');
      return null;
    }
  }
}
