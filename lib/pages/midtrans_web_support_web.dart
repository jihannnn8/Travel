import 'dart:convert';
import 'dart:html' as html;
import 'dart:ui' as ui;

import 'package:flutter/widgets.dart';

void registerSnapWebView(
  String viewId,
  String snapToken,
  void Function(String type, Map<String, dynamic>? data) onEvent,
) {
  ui.platformViewRegistry.registerViewFactory(
    viewId,
    (int _) {
      final iframe = html.IFrameElement()
        ..style.width = '100%'
        ..style.height = '100%'
        ..style.border = 'none';

      final htmlContent = '''
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { margin: 0; padding: 0; }
    </style>
    <script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js"></script>
</head>
<body>
    <div id="snap-container"></div>
    <script type="text/javascript">
        window.snap.pay('$snapToken', {
            onSuccess: function(result) {
                window.parent.postMessage({type: 'success', data: result}, '*');
            },
            onPending: function(result) {
                window.parent.postMessage({type: 'pending', data: result}, '*');
            },
            onError: function(result) {
                window.parent.postMessage({type: 'error', data: result}, '*');
            },
            onClose: function() {
                window.parent.postMessage({type: 'close'}, '*');
            }
        });
    </script>
</body>
</html>
''';

      iframe.src = Uri.dataFromString(
        htmlContent,
        mimeType: 'text/html',
        encoding: Encoding.getByName('utf-8'),
      ).toString();

      return iframe;
    },
  );

  html.window.onMessage.listen((event) {
    if (event.data is Map) {
      final Map<String, dynamic> payload =
          Map<String, dynamic>.from(event.data as Map);
      final type = payload['type'] as String?;
      if (type != null) {
        final data = payload['data'];
        onEvent(
          type,
          data is Map<String, dynamic> ? data : null,
        );
      }
    }
  });
}

Widget buildSnapHtmlView(String viewId) {
  return HtmlElementView(viewType: viewId);
}



