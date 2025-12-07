import 'package:flutter/widgets.dart';

/// Stub implementations so mobile/desktop builds can compile without `dart:html`.
void registerSnapWebView(
  String viewId,
  String snapToken,
  void Function(String type, Map<String, dynamic>? data) onEvent,
) {
  throw UnsupportedError('Midtrans web view only available on web builds');
}

Widget buildSnapHtmlView(String viewId) {
  throw UnsupportedError('Midtrans web view only available on web builds');
}



