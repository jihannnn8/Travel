class ApiConfig {
  // IMPORTANT: Update baseUrl sesuai dengan environment:
  // - Android Emulator: 'http://10.0.2.2:8000/api'
  // - iOS Simulator: 'http://localhost:8000/api'
  // - Physical Device: 'http://YOUR_COMPUTER_IP:8000/api' (e.g., 'http://192.168.1.100:8000/api')
  // Untuk mendapatkan IP komputer: ipconfig (Windows) atau ifconfig (Mac/Linux)
  static const String baseUrl = 'http://localhost:8000/api';
  // static const String baseUrl = 'http://10.0.2.2:8000/api'; // Uncomment untuk Android Emulator
  // static const String baseUrl = 'http://192.168.1.xxx:8000/api'; // Uncomment untuk Physical Device

  // Base URL untuk static files (tanpa /api)
  static String get baseUrlForAssets {
    // Extract base URL tanpa /api
    if (baseUrl.endsWith('/api')) {
      return baseUrl.substring(0, baseUrl.length - 4);
    }
    return baseUrl.replaceAll('/api', '');
  }

  // Helper untuk mengonversi image URL dari backend ke URL yang bisa diakses mobile
  static String fixImageUrl(String? imageUrl) {
    if (imageUrl == null || imageUrl.isEmpty) return '';
    
    final baseUri = Uri.tryParse(baseUrl);
    if (baseUri == null) {
      print('ApiConfig.fixImageUrl - Error parsing baseUrl: $baseUrl');
      return imageUrl;
    }
    
    final correctHost = baseUri.host;
    final correctPort = baseUri.port;
    final scheme = baseUri.scheme;
    
    print('ApiConfig.fixImageUrl - baseUrl: $baseUrl');
    print('ApiConfig.fixImageUrl - correctHost: $correctHost, correctPort: $correctPort');
    print('ApiConfig.fixImageUrl - Original imageUrl: $imageUrl');
    
    // Jika URL sudah full URL dengan http/https
    if (imageUrl.startsWith('http://') || imageUrl.startsWith('https://')) {
      final uri = Uri.tryParse(imageUrl);
      if (uri != null) {
        // Jika URL sudah dalam format /api/asset/ atau /api/storage/, pastikan host/port sesuai
        if (uri.path.contains('/api/asset/') || uri.path.contains('/api/storage/')) {
          // Jika host/port sudah sesuai dengan baseUrl, return as is
          if (uri.host == correctHost && uri.port == correctPort) {
            print('ApiConfig.fixImageUrl - URL already in correct format, returning as is');
            return imageUrl;
          }
          // Jika host/port berbeda, fix dengan host/port dari baseUrl
          final fixedUrl = '$scheme://$correctHost:$correctPort${uri.path}${uri.query.isNotEmpty ? '?${uri.query}' : ''}';
          print('ApiConfig.fixImageUrl - Fixed host/port: $fixedUrl');
          return fixedUrl;
        }
        
        // Jika URL mengandung localhost/127.0.0.1 dan baseUrl menggunakan IP berbeda
        if ((imageUrl.contains('localhost') || imageUrl.contains('127.0.0.1')) &&
            correctHost != 'localhost' && correctHost != '127.0.0.1') {
          // Ganti localhost dengan IP dari baseUrl
          final fixedUrl = '$scheme://$correctHost:$correctPort${uri.path}${uri.query.isNotEmpty ? '?${uri.query}' : ''}';
          print('ApiConfig.fixImageUrl - Fixed localhost to IP: $fixedUrl');
          return fixedUrl;
        }
        
        // Jika URL sudah valid dan tidak perlu diubah
        print('ApiConfig.fixImageUrl - URL already valid, returning as is');
        return imageUrl;
      }
    }
    
    // Jika relative path dengan /api/asset/ atau /api/storage/
    if (imageUrl.startsWith('/api/asset/') || imageUrl.startsWith('/api/storage/')) {
      final fixedUrl = '$scheme://$correctHost:$correctPort$imageUrl';
      print('ApiConfig.fixImageUrl - Relative API path fixed: $fixedUrl');
      return fixedUrl;
    }
    
    // Jika relative path tanpa /api/, tambahkan baseUrl (dengan /api)
    if (imageUrl.startsWith('/')) {
      // Jika path seperti /Asset_Travelo/ atau /storage/, convert ke /api/asset/ atau /api/storage/
      if (imageUrl.startsWith('/Asset_Travelo/')) {
        final relativePath = imageUrl.replaceFirst('/Asset_Travelo/', '');
        final fixedUrl = '$scheme://$correctHost:$correctPort/api/asset/$relativePath';
        print('ApiConfig.fixImageUrl - Converted Asset_Travelo to /api/asset/: $fixedUrl');
        return fixedUrl;
      } else if (imageUrl.startsWith('/storage/')) {
        final relativePath = imageUrl.replaceFirst('/storage/', '');
        final fixedUrl = '$scheme://$correctHost:$correctPort/api/storage/$relativePath';
        print('ApiConfig.fixImageUrl - Converted /storage/ to /api/storage/: $fixedUrl');
        return fixedUrl;
      }
      
      // Path lain, tambahkan baseUrl (dengan /api)
      final fixedUrl = '$scheme://$correctHost:$correctPort/api$imageUrl';
      print('ApiConfig.fixImageUrl - Relative path fixed: $fixedUrl');
      return fixedUrl;
    }
    
    // Jika path tanpa prefix, assume dari Asset_Travelo dan convert ke /api/asset/
    final fixedUrl = '$scheme://$correctHost:$correctPort/api/asset/$imageUrl';
    print('ApiConfig.fixImageUrl - Path without prefix fixed: $fixedUrl');
    return fixedUrl;
  }

  // Endpoints
  static const String register = '/register';
  static const String login = '/login';
  static const String logout = '/logout';
  static const String profile = '/profile';
  static const String me = '/me';
  
  // Destination endpoints
  static const String destinations = '/destinations';
  static String destinationById(String id) => '/destinations/$id';
  
  // City endpoints
  static const String cities = '/cities';
  
  // Slider endpoints
  static const String sliders = '/sliders';
  
  // Promo endpoints
  static const String promos = '/promos';
  
  // Booking endpoints
  static const String bookings = '/bookings';
  static String bookingById(String id) => '/bookings/$id';
  static String bookingStatus(String id) => '/bookings/$id/status';
}
