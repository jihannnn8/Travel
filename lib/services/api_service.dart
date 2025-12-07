import 'dart:convert';
import 'package:http/http.dart' as http;
import '../config/api_config.dart';
import 'auth_service.dart';

class ApiService {
  // No longer using cookies - using Bearer token instead

  // No longer saving cookies - using Bearer token instead

  // Helper untuk mendapatkan headers dengan Bearer token
  static Future<Map<String, String>> _getHeaders({
    Map<String, String>? additionalHeaders,
  }) async {
    final headers = {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      ...?additionalHeaders,
    };

    // Get token from AuthService
    final token = await AuthService.getToken();
    if (token != null) {
      headers['Authorization'] = 'Bearer $token';
      print('Sending Bearer token: ${token.substring(0, token.length > 20 ? 20 : token.length)}...');
    } else {
      print('No auth token available');
    }

    return headers;
  }

  // GET request
  static Future<http.Response> get(
    String endpoint, {
    Map<String, String>? headers,
  }) async {
    try {
      final url = Uri.parse('${ApiConfig.baseUrl}$endpoint');
      final allHeaders = await _getHeaders(additionalHeaders: headers);
      final response = await http.get(
        url,
        headers: allHeaders,
      );

      return response;
    } catch (e) {
      rethrow;
    }
  }

  // POST request
  static Future<http.Response> post(
    String endpoint, {
    Map<String, dynamic>? body,
    Map<String, String>? headers,
  }) async {
    try {
      final url = Uri.parse('${ApiConfig.baseUrl}$endpoint');
      final allHeaders = await _getHeaders(additionalHeaders: headers);
      final response = await http.post(
        url,
        headers: allHeaders,
        body: body != null ? jsonEncode(body) : null,
      );

      return response;
    } catch (e) {
      rethrow;
    }
  }

  // PUT request
  static Future<http.Response> put(
    String endpoint, {
    Map<String, dynamic>? body,
    Map<String, String>? headers,
  }) async {
    try {
      final url = Uri.parse('${ApiConfig.baseUrl}$endpoint');
      final allHeaders = await _getHeaders(additionalHeaders: headers);
      final response = await http.put(
        url,
        headers: allHeaders,
        body: body != null ? jsonEncode(body) : null,
      );

      return response;
    } catch (e) {
      rethrow;
    }
  }

  // PATCH request
  static Future<http.Response> patch(
    String endpoint, {
    Map<String, dynamic>? body,
    Map<String, String>? headers,
  }) async {
    try {
      final url = Uri.parse('${ApiConfig.baseUrl}$endpoint');
      final allHeaders = await _getHeaders(additionalHeaders: headers);
      final response = await http.patch(
        url,
        headers: allHeaders,
        body: body != null ? jsonEncode(body) : null,
      );

      return response;
    } catch (e) {
      rethrow;
    }
  }
}
