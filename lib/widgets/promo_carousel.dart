import 'dart:async';
import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import '../config/api_config.dart';

class PromoCarousel extends StatefulWidget {
  final List<String> images;
  final Duration autoPlayInterval;

  const PromoCarousel({
    super.key,
    required this.images,
    this.autoPlayInterval = const Duration(seconds: 3),
  });

  @override
  State<PromoCarousel> createState() => _PromoCarouselState();
}

class _PromoCarouselState extends State<PromoCarousel> {
  late final PageController _controller;
  Timer? _timer;
  int _current = 0;

  @override
  void initState() {
    super.initState();
    _controller = PageController();
    _startAutoPlay();
  }

  void _startAutoPlay() {
    _timer?.cancel();
    if (widget.images.isEmpty) return;
    _timer = Timer.periodic(widget.autoPlayInterval, (timer) {
      if (!mounted) return;
      final next = (_current + 1) % widget.images.length;
      _controller.animateToPage(
        next,
        duration: const Duration(milliseconds: 350),
        curve: Curves.easeInOut,
      );
    });
  }

  @override
  void dispose() {
    _timer?.cancel();
    _controller.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final themeColor = Colors.blue.shade600;

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        // Carousel full width sesuai card lainnya
        SizedBox(
          height: 230,
          width: double.infinity,
          child: PageView.builder(
            controller: _controller,
            itemCount: widget.images.length,
            onPageChanged: (index) {
              setState(() {
                _current = index;
              });
            },
            itemBuilder: (context, index) {
              final imagePath = widget.images[index];
              return Padding(
                padding: const EdgeInsets.symmetric(horizontal: 20),
                child: ClipRRect(
                  borderRadius: BorderRadius.circular(12),
                  child: Container(
                    color: Colors.blue.shade50,
                    child: _buildImage(imagePath),
                  ),
                ),
              );
            },
          ),
        ),
        const SizedBox(height: 10),
        // Footer indicator full width
        Container(
          width: double.infinity,
          padding: const EdgeInsets.symmetric(vertical: 8),
          child: Row(
            mainAxisAlignment: MainAxisAlignment.center,
            children: List.generate(
              widget.images.length,
              (index) => Container(
                margin: const EdgeInsets.symmetric(horizontal: 4),
                width: 8,
                height: 8,
                decoration: BoxDecoration(
                  shape: BoxShape.circle,
                  color: _current == index ? themeColor : Colors.grey.shade300,
                ),
              ),
            ),
          ),
        ),
      ],
    );
  }

  Widget _buildImage(String imageUrl) {
    if (imageUrl.startsWith('http://') || 
        imageUrl.startsWith('https://') ||
        imageUrl.startsWith('/api/asset/') ||
        imageUrl.startsWith('/api/storage/')) {
      // Network image - fix URL if needed
      final fixedUrl = ApiConfig.fixImageUrl(imageUrl);
      return Image.network(
        fixedUrl,
        width: double.infinity,
        height: double.infinity,
        fit: BoxFit.cover,
        loadingBuilder: (context, child, loadingProgress) {
          if (loadingProgress == null) return child;
          return Container(
            width: double.infinity,
            height: double.infinity,
            color: Colors.grey.shade200,
            child: Center(
              child: CircularProgressIndicator(
                value: loadingProgress.expectedTotalBytes != null
                    ? loadingProgress.cumulativeBytesLoaded /
                        loadingProgress.expectedTotalBytes!
                    : null,
              ),
            ),
          );
        },
        errorBuilder: (context, error, stackTrace) {
          return Container(
            width: double.infinity,
            height: double.infinity,
            color: Colors.grey.shade200,
            child: Icon(
              Icons.broken_image,
              color: Colors.grey.shade400,
              size: 40,
            ),
          );
        },
      );
    } else {
      // Asset image
      final assetPath = imageUrl.startsWith('assets/')
          ? imageUrl
          : 'assets/images/$imageUrl';
      return Image.asset(
        assetPath,
        width: double.infinity,
        height: double.infinity,
        fit: BoxFit.cover,
        errorBuilder: (context, error, stackTrace) {
          return Container(
            width: double.infinity,
            height: double.infinity,
            color: Colors.grey.shade200,
            child: Icon(
              Icons.broken_image,
              color: Colors.grey.shade400,
              size: 40,
            ),
          );
        },
      );
    }
  }
}


