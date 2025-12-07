import 'dart:convert';
import '../models/tour_package.dart';
import '../models/city.dart';
import 'api_service.dart';
import '../config/api_config.dart';
import 'destination_service.dart';

class DataService {
  // Static data untuk pickup times (masih digunakan di package_detail_page)
  static List<String> getPickupTimes() {
    return [
      '06:00 WIB',
      '07:00 WIB',
      '08:00 WIB',
      '09:00 WIB',
      '10:00 WIB',
    ];
  }

  // ========== ASYNC API METHODS ==========
  
  /// Get tour packages from API (async)
  static Future<List<TourPackage>> getTourPackagesAsync() async {
    try {
      final packages = await DestinationService.getDestinations();
      if (packages != null && packages.isNotEmpty) {
        return packages;
      }
      // Return empty list if API returns null or empty
      print('No tour packages found from API');
      return [];
    } catch (e) {
      print('Error fetching tour packages: $e');
      // Return empty list if API fails
      return [];
    }
  }

  /// Get cities from API (async)
  static Future<List<City>> getCitiesAsync() async {
  try {
    final response = await ApiService.get(ApiConfig.cities);
    
    print('Cities API Response Status: ${response.statusCode}');
    
    if (response.statusCode == 200) {
      final responseData = json.decode(response.body);
      print('Cities API Response Body: $responseData');
      
      if (responseData['success'] == true) {
        final citiesData = responseData['data']['cities'] as List?;
        
        if (citiesData != null && citiesData.isNotEmpty) {
          print('Found ${citiesData.length} cities');
          
          return citiesData.map((city) {
            // Get image URL dari response
            final imageUrl = city['imageUrl'] ?? city['image_url'] ?? '';
            
            // Fix image URL menggunakan ApiConfig.fixImageUrl
            final fixedImageUrl = ApiConfig.fixImageUrl(imageUrl);
            
            print('City: ${city['name']}, Original: $imageUrl, Fixed: $fixedImageUrl');
            
            return City(
              id: city['id']?.toString() ?? '',
              name: city['name'] ?? '',
              imageUrl: fixedImageUrl,
            );
          }).toList();
        } else {
          print('Cities data is empty or null');
        }
      } else {
        print('API success flag is false');
      }
    } else {
      print('API returned non-200 status: ${response.statusCode}');
      print('Response body: ${response.body}');
    }
    
    // Return empty list if API fails
    print('No cities found from API');
    return [];
    
  } catch (e, stackTrace) {
    print('Error fetching cities: $e');
    print('Stack trace: $stackTrace');
    // Return empty list if API fails
    return [];
  }
}

  /// Get slider images from API (async)
  static Future<List<String>> getSliderImagesAsync() async {
    try {
      final response = await ApiService.get(ApiConfig.sliders);
      
      if (response.statusCode == 200) {
        final responseData = json.decode(response.body);
        
        if (responseData['success'] == true) {
          final slidersData = responseData['data']['sliders'] as List?;
          if (slidersData != null && slidersData.isNotEmpty) {
            return slidersData.map((imageUrl) {
              // Backend mengembalikan array of strings langsung
              final url = imageUrl is String ? imageUrl : (imageUrl.toString());
              // Fix image URL - handle semua format (http, /api/asset/, /api/storage/, assets/)
              if (url.startsWith('assets/')) {
                return url; // Asset images tidak perlu di-fix
              }
              // Gunakan fixImageUrl untuk semua URL lainnya (termasuk /api/asset/ dan /api/storage/)
              return ApiConfig.fixImageUrl(url);
            }).toList();
          }
        }
      }
      // Return empty list if API fails
      print('No slider images found from API');
      return [];
    } catch (e) {
      print('Error fetching slider images: $e');
      // Return empty list if API fails
      return [];
    }
  }

  /// Get promo images from API (async)
  static Future<List<String>> getPromoImagesAsync() async {
    try {
      final response = await ApiService.get(ApiConfig.promos);
      
      if (response.statusCode == 200) {
        final responseData = json.decode(response.body);
        
        if (responseData['success'] == true) {
          final promosData = responseData['data']['promos'] as List?;
          if (promosData != null && promosData.isNotEmpty) {
            return promosData.map((imageUrl) {
              // Backend mengembalikan array of strings langsung
              final url = imageUrl is String ? imageUrl : (imageUrl.toString());
              // Fix image URL - handle semua format (http, /api/asset/, /api/storage/, assets/)
              if (url.startsWith('assets/')) {
                return url; // Asset images tidak perlu di-fix
              }
              // Gunakan fixImageUrl untuk semua URL lainnya (termasuk /api/asset/ dan /api/storage/)
              return ApiConfig.fixImageUrl(url);
            }).toList();
          }
        }
      }
      // Return empty list if API fails (promos are optional)
      return [];
    } catch (e) {
      print('Error fetching promo images: $e');
      // Return empty list if error
      return [];
    }
  }
}
