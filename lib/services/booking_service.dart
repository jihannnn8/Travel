import 'package:shared_preferences/shared_preferences.dart';
import 'dart:convert';
import '../models/booking.dart';

class BookingService {
  static const String _bookingsKey = 'bookings';

  static Future<List<Booking>> getBookings() async {
    final prefs = await SharedPreferences.getInstance();
    final bookingsJson = prefs.getString(_bookingsKey);
    if (bookingsJson != null) {
      final List<dynamic> bookingsList = json.decode(bookingsJson);
      return bookingsList.map((json) => Booking.fromJson(json)).toList();
    }
    return [];
  }

  static Future<void> addBooking(Booking booking) async {
    final bookings = await getBookings();
    bookings.add(booking);
    await _saveBookings(bookings);
  }

  static Future<void> updateBookingStatus(String bookingId, String status) async {
    final bookings = await getBookings();
    final index = bookings.indexWhere((booking) => booking.id == bookingId);
    if (index != -1) {
      bookings[index] = bookings[index].copyWith(status: status);
      await _saveBookings(bookings);
    }
  }

  static Future<void> _saveBookings(List<Booking> bookings) async {
    final prefs = await SharedPreferences.getInstance();
    final bookingsJson = json.encode(bookings.map((booking) => booking.toJson()).toList());
    await prefs.setString(_bookingsKey, bookingsJson);
  }
}
