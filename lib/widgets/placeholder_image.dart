import 'package:flutter/material.dart';

class PlaceholderImage extends StatelessWidget {
  final String? imageUrl;
  final double? width;
  final double? height;
  final IconData icon;
  final Color? backgroundColor;
  final Color? iconColor;

  const PlaceholderImage({
    super.key,
    this.imageUrl,
    this.width,
    this.height,
    this.icon = Icons.image,
    this.backgroundColor,
    this.iconColor,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      width: width,
      height: height,
      decoration: BoxDecoration(
        color: backgroundColor ?? Colors.blue.shade100,
        borderRadius: BorderRadius.circular(8),
      ),
      child: Center(
        child: Icon(
          icon,
          color: iconColor ?? Colors.blue.shade600,
          size: (height ?? 100) * 0.4,
        ),
      ),
    );
  }
}

