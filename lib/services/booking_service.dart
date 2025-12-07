import 'package:shared_preferences/shared_preferences.dart';
import 'dart:convert';
import '../models/booking.dart';
import '../config/api_config.dart';
import 'api_service.dart';

class BookingService {
  static const String _bookingsKey = 'bookings';

  /// Create booking via API and return booking with snap token
  static Future<Booking?> createBooking({
    required String destinationId,
    required String customerName,
    required String tanggalKeberangkatan,
    required String waktuKeberangkatan,
    required String lokasiPenjemputan,
    required String metodePembayaran,
  }) async {
    try {
      final response = await ApiService.post(
        ApiConfig.bookings,
        body: {
          'destination_id': destinationId,
          'customer_name': customerName,
          'tanggal_keberangkatan': tanggalKeberangkatan,
          'waktu_keberangkatan': waktuKeberangkatan,
          'lokasi_penjemputan': lokasiPenjemputan,
          'metode_pembayaran': metodePembayaran,
        },
      );

      final responseData = json.decode(response.body);

      if (response.statusCode == 200 || response.statusCode == 201) {
        if (responseData['success'] == true && responseData['data'] != null) {
          final bookingData = responseData['data']['booking'];
          final booking = Booking.fromJson(bookingData);
          
          // Save to local storage
          await _saveBookingLocally(booking);
          
          return booking;
        } else {
          print('Create booking error: ${responseData['message']}');
          return null;
        }
      } else {
        print('Create booking failed: ${response.statusCode}');
        print('Response: ${response.body}');
        return null;
      }
    } catch (e) {
      print('Create booking exception: $e');
      return null;
    }
  }

  /// Get all bookings for current user
  static Future<List<Booking>> getBookings() async {
    try {
      final response = await ApiService.get(ApiConfig.bookings);
      final responseData = json.decode(response.body);

      if (response.statusCode == 200 && responseData['success'] == true) {
        final List<dynamic> bookingsList = responseData['data']['bookings'] ?? [];
        final bookings = bookingsList.map((json) => Booking.fromJson(json)).toList();
        
        // Save to local storage
        await _saveBookings(bookings);
        
        return bookings;
      } else {
        // Fallback to local storage
        return await _getBookingsFromLocal();
      }
    } catch (e) {
      print('Get bookings exception: $e');
      // Fallback to local storage
      return await _getBookingsFromLocal();
    }
  }

  /// Get booking by ID
  static Future<Booking?> getBookingById(String id) async {
    try {
      final response = await ApiService.get(ApiConfig.bookingById(id));
      final responseData = json.decode(response.body);

      if (response.statusCode == 200 && responseData['success'] == true) {
        final bookingData = responseData['data']['booking'];
        return Booking.fromJson(bookingData);
      }
      return null;
    } catch (e) {
      print('Get booking by ID exception: $e');
      return null;
    }
  }

  /// Check booking payment status
  static Future<String?> checkBookingStatus(String id) async {
    try {
      final response = await ApiService.get(ApiConfig.bookingStatus(id));
      final responseData = json.decode(response.body);

      if (response.statusCode == 200 && responseData['success'] == true) {
        return responseData['data']['paymentStatus'];
      }
      return null;
    } catch (e) {
      print('Check booking status exception: $e');
      return null;
    }
  }

  /// Local storage methods
  static Future<List<Booking>> _getBookingsFromLocal() async {
    final prefs = await SharedPreferences.getInstance();
    final bookingsJson = prefs.getString(_bookingsKey);
    if (bookingsJson != null) {
      final List<dynamic> bookingsList = json.decode(bookingsJson);
      return bookingsList.map((json) => Booking.fromJson(json)).toList();
    }
    return [];
  }

  static Future<void> _saveBookingLocally(Booking booking) async {
    final bookings = await _getBookingsFromLocal();
    final index = bookings.indexWhere((b) => b.id == booking.id);
    if (index != -1) {
      bookings[index] = booking;
    } else {
      bookings.add(booking);
    }
    await _saveBookings(bookings);
  }

  static Future<void> _saveBookings(List<Booking> bookings) async {
    final prefs = await SharedPreferences.getInstance();
    final bookingsJson = json.encode(bookings.map((booking) => booking.toJson()).toList());
    await prefs.setString(_bookingsKey, bookingsJson);
  }

  static Future<void> updateBookingStatus(String bookingId, String status) async {
    final bookings = await _getBookingsFromLocal();
    final index = bookings.indexWhere((booking) => booking.id == bookingId);
    if (index != -1) {
      bookings[index] = bookings[index].copyWith(status: status);
      await _saveBookings(bookings);
    }
  }
}
