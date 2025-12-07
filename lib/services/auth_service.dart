import 'package:shared_preferences/shared_preferences.dart';
import 'dart:convert';
import '../models/user.dart';
import '../config/api_config.dart';
import 'api_service.dart';

class AuthService {
  static const String _userKey = 'current_user';
  static const String _isLoggedInKey = 'is_logged_in';
  static const String _tokenKey = 'auth_token';

  static Future<bool> isLoggedIn() async {
    final prefs = await SharedPreferences.getInstance();
    return prefs.getBool(_isLoggedInKey) ?? false;
  }

  static Future<User?> getCurrentUser() async {
    final prefs = await SharedPreferences.getInstance();
    final userJson = prefs.getString(_userKey);
    if (userJson != null) {
      return User.fromJson(json.decode(userJson));
    }
    return null;
  }

  static Future<bool> login(String name, String password) async {
    try {
      final response = await ApiService.post(
        ApiConfig.login,
        body: {'name': name, 'password': password},
      );

      final responseData = json.decode(response.body);

      if (response.statusCode == 200 && responseData['success'] == true) {
        // Parse user data dan token dari response
        final userData = responseData['data']['user'];
        final token = responseData['data']['token'] as String?;
        
        final user = User.fromJson({
          'id': userData['id'].toString(),
          'name': userData['name'],
          'email': userData['email'],
          'phoneNumber': userData['phoneNumber'] ?? '',
        });

        // Simpan user dan token ke local storage
        await _saveUser(user, token);
        
        // Verify that user was saved
        final savedUser = await getCurrentUser();
        print('Login successful - User saved: ${savedUser != null}');
        print('Login successful - User name: ${savedUser?.name}');
        print('Login successful - Token saved: ${token != null}');
        print('Login successful - isLoggedIn: ${await isLoggedIn()}');
        
        return true;
      } else {
        // Handle error
        return false;
      }
    } catch (e) {
      print('Login error: $e');
      return false;
    }
  }

  static Future<String?> register(
    String name,
    String email,
    String password,
  ) async {
    try {
      final response = await ApiService.post(
        ApiConfig.register,
        body: {'name': name, 'email': email, 'password': password},
      );

      final responseData = json.decode(response.body);

      if (response.statusCode == 201 && responseData['success'] == true) {
        // Parse user data dari response
        final userData = responseData['data']['user'];
        final user = User.fromJson({
          'id': userData['id'].toString(),
          'name': userData['name'],
          'email': userData['email'],
          'phoneNumber': userData['phoneNumber'] ?? '',
        });

        // Parse token dari response
        final token = responseData['data']['token'] as String?;
        
        // Simpan user dan token ke local storage
        await _saveUser(user, token);
        return null; // Success, no error message
      } else {
        // Handle validation errors
        if (responseData['errors'] != null) {
          final errors = responseData['errors'] as Map<String, dynamic>;
          final firstError = errors.values.first;
          if (firstError is List && firstError.isNotEmpty) {
            return firstError.first.toString();
          }
        }
        return responseData['message'] ?? 'Registration failed';
      }
    } catch (e) {
      print('Register error: $e');
      return 'Connection error. Please check your internet connection.';
    }
  }

  static Future<void> _saveUser(User user, String? token) async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString(_userKey, json.encode(user.toJson()));
    await prefs.setBool(_isLoggedInKey, true);
    if (token != null) {
      await prefs.setString(_tokenKey, token);
    }
    print('User saved to SharedPreferences: ${user.name}');
    print('Token saved: ${token != null}');
    print('is_logged_in set to: ${prefs.getBool(_isLoggedInKey)}');
  }

  static Future<String?> getToken() async {
    final prefs = await SharedPreferences.getInstance();
    return prefs.getString(_tokenKey);
  }

  static Future<bool> logout() async {
    try {
      final response = await ApiService.post(ApiConfig.logout);

      if (response.statusCode == 200) {
        // Clear local storage
        final prefs = await SharedPreferences.getInstance();
        await prefs.remove(_userKey);
        await prefs.remove(_tokenKey);
        await prefs.setBool(_isLoggedInKey, false);

        return true;
      }
      return false;
    } catch (e) {
      print('Logout error: $e');
      // Clear local storage anyway
      final prefs = await SharedPreferences.getInstance();
      await prefs.remove(_userKey);
      await prefs.remove(_tokenKey);
      await prefs.setBool(_isLoggedInKey, false);
      return false;
    }
  }

  static Future<User?> fetchProfile() async {
    try {
      final response = await ApiService.get(ApiConfig.profile);

      if (response.statusCode == 200) {
        final responseData = json.decode(response.body);
        if (responseData['success'] == true) {
          final userData = responseData['data']['user'];
          final user = User.fromJson({
            'id': userData['id'].toString(),
            'name': userData['name'],
            'email': userData['email'],
            'phoneNumber': userData['phoneNumber'] ?? '',
          });

          // Update local storage
          await _saveUser(user, null);
          return user;
        }
      }
      return null;
    } catch (e) {
      print('Fetch profile error: $e');
      return null;
    }
  }

  static Future<String?> updateProfile({
    String? name,
    String? email,
    String? phoneNumber,
    String? password,
  }) async {
    try {
      final body = <String, dynamic>{};
      if (name != null) body['name'] = name;
      if (email != null) body['email'] = email;
      if (phoneNumber != null) body['phoneNumber'] = phoneNumber;
      if (password != null && password.isNotEmpty) body['password'] = password;

      final response = await ApiService.put(ApiConfig.profile, body: body);

      final responseData = json.decode(response.body);

      if (response.statusCode == 200 && responseData['success'] == true) {
        // Update user data
        final userData = responseData['data']['user'];
        final user = User.fromJson({
          'id': userData['id'].toString(),
          'name': userData['name'],
          'email': userData['email'],
          'phoneNumber': userData['phoneNumber'] ?? '',
        });

        // Update local storage
        await _saveUser(user, null);
        return null; // Success
      } else {
        // Handle validation errors
        if (responseData['errors'] != null) {
          final errors = responseData['errors'] as Map<String, dynamic>;
          final firstError = errors.values.first;
          if (firstError is List && firstError.isNotEmpty) {
            return firstError.first.toString();
          }
        }
        return responseData['message'] ?? 'Update failed';
      }
    } catch (e) {
      print('Update profile error: $e');
      return 'Connection error. Please check your internet connection.';
    }
  }
}
