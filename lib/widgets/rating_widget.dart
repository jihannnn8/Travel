import 'package:flutter/material.dart';

class RatingWidget extends StatefulWidget {
  final double initialRating;
  final Function(double) onRatingChanged;
  final bool isReadOnly;

  const RatingWidget({
    super.key,
    required this.initialRating,
    required this.onRatingChanged,
    this.isReadOnly = false,
  });

  @override
  State<RatingWidget> createState() => _RatingWidgetState();
}

class _RatingWidgetState extends State<RatingWidget> {
  late double _currentRating;

  @override
  void initState() {
    super.initState();
    _currentRating = widget.initialRating;
  }

  @override
  Widget build(BuildContext context) {
    return Row(
      mainAxisSize: MainAxisSize.min,
      children: List.generate(5, (index) {
        return GestureDetector(
          onTap: widget.isReadOnly
              ? null
              : () {
                  setState(() {
                    _currentRating = index + 1.0;
                  });
                  widget.onRatingChanged(_currentRating);
                },
          child: Icon(
            index < _currentRating ? Icons.star : Icons.star_border,
            color: Colors.amber.shade600,
            size: 24,
          ),
        );
      }),
    );
  }
}

