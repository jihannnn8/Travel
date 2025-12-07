import 'package:flutter/material.dart';
import 'package:flutter/foundation.dart' show kIsWeb;
import 'package:webview_flutter/webview_flutter.dart';
import 'order_history_page.dart';
import 'midtrans_web_support_stub.dart'
    if (dart.library.html) 'midtrans_web_support_web.dart' as midtrans_web;

class MidtransSnapPage extends StatefulWidget {
  final String snapToken;

  const MidtransSnapPage({super.key, required this.snapToken});

  @override
  State<MidtransSnapPage> createState() => _MidtransSnapPageState();
}

class _MidtransSnapPageState extends State<MidtransSnapPage> {
  late final WebViewController _controller;
  bool _isLoading = true;
  String? _webViewId;

  @override
  void initState() {
    super.initState();
    if (kIsWeb) {
      _registerWebView();
    } else {
      _initializeWebView();
    }
  }
  
  void _registerWebView() {
    _webViewId = 'midtrans-snap-${widget.snapToken.hashCode}';

    midtrans_web.registerSnapWebView(
      _webViewId!,
      widget.snapToken,
      (type, _) {
        if (!mounted) return;
        if (type == 'success') {
          _navigateToOrderHistory(context, 'Pembayaran berhasil!');
        } else if (type == 'pending') {
          _navigateToOrderHistory(
            context,
            'Pembayaran sedang diproses. Silakan cek status pembayaran.',
          );
        } else if (type == 'error') {
          Navigator.pop(context);
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(
              content: Text('Terjadi kesalahan saat pembayaran. Silakan coba lagi.'),
              backgroundColor: Colors.red,
            ),
          );
        } else if (type == 'close') {
          Navigator.pop(context);
        }
      },
    );
  }

  void _initializeWebView() {
    // HTML untuk Midtrans Snap
    final htmlContent = '''
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <style>
        body {
            margin: 0;
            padding: 0;
            overflow: hidden;
        }
        #snap-container {
            width: 100%;
            height: 100vh;
        }
    </style>
    <script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js"></script>
</head>
<body>
    <div id="snap-container"></div>
    <script type="text/javascript">
        window.snap.pay('${widget.snapToken}', {
            onSuccess: function(result) {
                // Payment success
                paymentCallback.postMessage(JSON.stringify({type: 'success', data: result}));
            },
            onPending: function(result) {
                // Payment pending
                paymentCallback.postMessage(JSON.stringify({type: 'pending', data: result}));
            },
            onError: function(result) {
                // Payment error
                paymentCallback.postMessage(JSON.stringify({type: 'error', data: result}));
            },
            onClose: function() {
                // User closed payment page
                paymentCallback.postMessage(JSON.stringify({type: 'close'}));
            }
        });
    </script>
</body>
</html>
    ''';

    _controller = WebViewController()
      ..setJavaScriptMode(JavaScriptMode.unrestricted)
      ..addJavaScriptChannel(
        'paymentCallback',
        onMessageReceived: (JavaScriptMessage message) {
          _handlePaymentCallback(message.message);
        },
      )
      ..setNavigationDelegate(
        NavigationDelegate(
          onPageStarted: (String url) {
            setState(() {
              _isLoading = true;
            });
          },
          onPageFinished: (String url) {
            setState(() {
              _isLoading = false;
            });
          },
          onWebResourceError: (WebResourceError error) {
            print('WebView error: ${error.description}');
            setState(() {
              _isLoading = false;
            });
          },
        ),
      )
      ..loadHtmlString(htmlContent);
  }

  void _handlePaymentCallback(String message) {
    try {
      // Parse JSON message
      final data = message;
      
      if (data.contains('"type":"success"')) {
        // Payment success - redirect ke OrderHistoryPage
        _navigateToOrderHistory(context, 'Pembayaran berhasil!');
      } else if (data.contains('"type":"pending"')) {
        // Payment pending - redirect ke OrderHistoryPage
        _navigateToOrderHistory(context, 'Pembayaran sedang diproses. Silakan cek status pembayaran.');
      } else if (data.contains('"type":"error"')) {
        Navigator.pop(context);
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            content: Text('Terjadi kesalahan saat pembayaran. Silakan coba lagi.'),
            backgroundColor: Colors.red,
          ),
        );
      } else if (data.contains('"type":"close"')) {
        Navigator.pop(context);
      }
    } catch (e) {
      print('Error handling payment callback: $e');
    }
  }

  void _handleMidtransCallback(String url) {
    if (url.contains('midtrans://success')) {
      // Payment success - redirect ke OrderHistoryPage
      _navigateToOrderHistory(context, 'Pembayaran berhasil!');
    } else if (url.contains('midtrans://pending')) {
      // Payment pending - redirect ke OrderHistoryPage
      _navigateToOrderHistory(context, 'Pembayaran sedang diproses. Silakan cek status pembayaran.');
    } else if (url.contains('midtrans://error')) {
      // Payment error
      Navigator.pop(context);
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Terjadi kesalahan saat pembayaran. Silakan coba lagi.'),
          backgroundColor: Colors.red,
        ),
      );
    } else if (url.contains('midtrans://close')) {
      // User closed
      Navigator.pop(context);
    }
  }

  // Helper method untuk navigate ke OrderHistoryPage
  void _navigateToOrderHistory(BuildContext context, String message) {
    // Pop halaman MidtransSnapPage
    Navigator.pop(context);
    
    // Tunggu sedikit untuk memastikan pop selesai
    Future.delayed(const Duration(milliseconds: 200), () {
      if (!mounted) return;
      
      // Pop semua halaman sampai ke root
      Navigator.of(context).popUntil((route) => route.isFirst);
      
      // Navigate langsung ke OrderHistoryPage dengan replace
      Navigator.pushReplacement(
        context,
        MaterialPageRoute(
          builder: (context) => const OrderHistoryPage(),
        ),
      );
      
      // Show success message setelah navigasi
      Future.delayed(const Duration(milliseconds: 300), () {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: Text(message),
              backgroundColor: message.contains('berhasil') ? Colors.green : Colors.orange,
              duration: Duration(seconds: message.contains('berhasil') ? 2 : 3),
            ),
          );
        }
      });
    });
  }

  @override
  Widget build(BuildContext context) {
    // Untuk Flutter Web, gunakan iframe atau window baru
    if (kIsWeb) {
      return Scaffold(
        appBar: AppBar(
          title: const Text('Pembayaran'),
          backgroundColor: Colors.blue.shade600,
          foregroundColor: Colors.white,
        ),
        body: _buildWebView(),
      );
    }
    
    // Untuk mobile, gunakan WebView
    return Scaffold(
      appBar: AppBar(
        title: const Text('Pembayaran'),
        backgroundColor: Colors.blue.shade600,
        foregroundColor: Colors.white,
      ),
      body: Stack(
        children: [
          WebViewWidget(controller: _controller),
          if (_isLoading)
            const Center(
              child: CircularProgressIndicator(),
            ),
        ],
      ),
    );
  }
  
  Widget _buildWebView() {
    if (_webViewId == null) {
      return const Center(child: CircularProgressIndicator());
    }
    return midtrans_web.buildSnapHtmlView(_webViewId!);
  }
}

