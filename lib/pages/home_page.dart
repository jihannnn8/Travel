import 'dart:async';
import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import '../services/data_service.dart';
import 'package_detail_page.dart';
import 'order_history_page.dart';
import 'profile_page.dart';
import '../widgets/promo_carousel.dart';
import 'package:intl/intl.dart';
import '../models/tour_package.dart';
import '../models/city.dart';
import '../config/api_config.dart';

class HomePage extends StatefulWidget {
  const HomePage({super.key});

  @override
  State<HomePage> createState() => _HomePageState();
}

class _HomePageState extends State<HomePage> {
  int _currentIndex = 0;
  final formatter = NumberFormat('#,###');

  // State untuk carousel destinasi
  final ValueNotifier<int> _destinasiCarouselIndexNotifier = ValueNotifier<int>(0);
  late PageController _destinasiCarouselController;
  Timer? _destinasiCarouselTimer;
  bool _destinasiAutoSlideStarted = false;
  bool _isUserScrolling = false;
  bool _isAnimating = false;

  // Future variables for async data
  late Future<List<TourPackage>> _tourPackagesFuture;
  late Future<List<City>> _citiesFuture;
  late Future<List<String>> _sliderImagesFuture;
  late Future<List<String>> _promoImagesFuture;

  @override
  void initState() {
    super.initState();
    _destinasiCarouselController = PageController();
    _loadData();
  }

  void _loadData() {
    _tourPackagesFuture = DataService.getTourPackagesAsync();
    _citiesFuture = DataService.getCitiesAsync();
    _sliderImagesFuture = DataService.getSliderImagesAsync();
    _promoImagesFuture = DataService.getPromoImagesAsync();
  }

  void _startDestinasiAutoSlide(List<String> sliderImages) {
    if (sliderImages.isEmpty || _destinasiAutoSlideStarted) return;
    
    // Pastikan timer tidak di-reset jika sudah berjalan
    if (_destinasiCarouselTimer != null && _destinasiCarouselTimer!.isActive) {
      return;
    }
    
    _destinasiAutoSlideStarted = true;
    _destinasiCarouselTimer?.cancel();
    _destinasiCarouselTimer = Timer.periodic(const Duration(seconds: 4), (timer) {
      if (!mounted) {
        timer.cancel();
        return;
      }
      // Jangan auto-play jika user sedang scroll manual atau animasi sedang berjalan
      if (_isUserScrolling || _isAnimating) return;
      
      if (_destinasiCarouselController.hasClients && sliderImages.isNotEmpty) {
        final currentIndex = _destinasiCarouselIndexNotifier.value;
        final nextPage = (currentIndex + 1) % sliderImages.length;
        // Skip jika sudah di halaman yang sama
        if (nextPage == currentIndex) return;
        
        _isAnimating = true;
        _destinasiCarouselController.animateToPage(
          nextPage,
          duration: const Duration(milliseconds: 600),
          curve: Curves.easeInOut,
        ).then((_) {
          // Reset flag setelah animasi selesai dengan delay kecil
          Future.delayed(const Duration(milliseconds: 100), () {
            if (mounted) {
              _isAnimating = false;
            }
          });
        }).catchError((error) {
          // Handle error jika animasi gagal
          if (mounted) {
            _isAnimating = false;
          }
        });
      }
    });
  }
  
  void _pauseAutoSlide() {
    if (mounted) {
      _isUserScrolling = true;
      _isAnimating = false; // Reset animating flag saat pause
    }
  }
  
  void _resumeAutoSlide() {
    // Resume setelah delay untuk menghindari konflik
    Future.delayed(const Duration(milliseconds: 1000), () {
      if (mounted) {
        _isUserScrolling = false;
        _isAnimating = false;
      }
    });
  }

  @override
  void dispose() {
    _destinasiCarouselTimer?.cancel();
    _destinasiCarouselController.dispose();
    _destinasiCarouselIndexNotifier.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      appBar: _currentIndex == 0 ? _buildHeader() : null,
      body: IndexedStack(
        index: _currentIndex,
        children: [
          _buildHomeContent(),
          const OrderHistoryPage(),
          const ProfilePage(),
        ],
      ),
      bottomNavigationBar: BottomNavigationBar(
        currentIndex: _currentIndex,
        onTap: (index) {
          setState(() {
            _currentIndex = index;
          });
        },
        type: BottomNavigationBarType.fixed,
        backgroundColor: Colors.white,
        selectedItemColor: Colors.blue.shade600,
        unselectedItemColor: Colors.grey.shade400,
        items: const [
          BottomNavigationBarItem(icon: Icon(Icons.home), label: 'Home'),
          BottomNavigationBarItem(icon: Icon(Icons.history), label: 'Riwayat'),
          BottomNavigationBarItem(icon: Icon(Icons.person), label: 'Profil'),
        ],
      ),
    );
  }

  PreferredSizeWidget _buildHeader() {
    return PreferredSize(
      preferredSize: const Size.fromHeight(88),
      child: Container(
        decoration: BoxDecoration(
          color: Colors.white,
          boxShadow: [
            BoxShadow(
              color: Colors.black.withOpacity(0.05),
              blurRadius: 6,
              offset: const Offset(0, 3),
            ),
          ],
          borderRadius: const BorderRadius.only(
            bottomLeft: Radius.circular(16),
            bottomRight: Radius.circular(16),
          ),
        ),
        child: SafeArea(
          bottom: false,
          child: Padding(
            padding: const EdgeInsets.fromLTRB(20, 16, 20, 12),
            child: Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      'Selamat Datang!',
                      style: GoogleFonts.poppins(
                        color: Colors.grey.shade800,
                        fontSize: 16,
                        fontWeight: FontWeight.w600,
                      ),
                    ),
                    Text(
                      'Jelajahi dunia dengan TRAVELO',
                      style: GoogleFonts.poppins(
                        color: Colors.grey.shade600,
                        fontSize: 13,
                      ),
                    ),
                  ],
                ),
                Container(
                  padding: const EdgeInsets.all(8),
                  decoration: BoxDecoration(
                    color: Colors.blue.shade50,
                    borderRadius: BorderRadius.circular(8),
                  ),
                  child: Icon(
                    Icons.notifications_none_rounded,
                    color: Colors.blue.shade600,
                  ),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildHomeContent() {
    return FutureBuilder<List<dynamic>>(
      future: Future.wait([
        _tourPackagesFuture,
        _citiesFuture,
        _sliderImagesFuture,
        _promoImagesFuture,
      ]),
      builder: (context, snapshot) {
        if (snapshot.connectionState == ConnectionState.waiting) {
          return const Center(
            child: Padding(
              padding: EdgeInsets.all(20.0),
              child: CircularProgressIndicator(),
            ),
          );
        }

        if (snapshot.hasError) {
          return Center(
            child: Padding(
              padding: const EdgeInsets.all(20.0),
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Icon(Icons.error_outline, size: 48, color: Colors.red.shade300),
                  const SizedBox(height: 16),
                  Text(
                    'Gagal memuat data',
                    style: GoogleFonts.poppins(
                      fontSize: 16,
                      fontWeight: FontWeight.w600,
                      color: Colors.grey.shade800,
                    ),
                  ),
                  const SizedBox(height: 8),
                  Text(
                    'Silakan coba lagi',
                    style: GoogleFonts.poppins(
                      fontSize: 14,
                      color: Colors.grey.shade600,
                    ),
                  ),
                  const SizedBox(height: 16),
                  ElevatedButton(
                    onPressed: () {
                      setState(() {
                        _loadData();
                      });
                    },
                    child: const Text('Coba Lagi'),
                  ),
                ],
              ),
            ),
          );
        }

        final tourPackages = snapshot.data![0] as List<TourPackage>;
        final cities = snapshot.data![1] as List<City>;
        final sliderImages = snapshot.data![2] as List<String>;
        final promoImages = snapshot.data![3] as List<String>;

        // Start auto slide untuk carousel destinasi (hanya sekali)
        // Pastikan timer tidak di-reset setiap rebuild
        if (sliderImages.isNotEmpty && !_destinasiAutoSlideStarted) {
          WidgetsBinding.instance.addPostFrameCallback((_) {
            if (mounted && sliderImages.isNotEmpty && !_destinasiAutoSlideStarted) {
              _startDestinasiAutoSlide(sliderImages);
            }
          });
        }

        return _buildHomeContentBody(
          tourPackages: tourPackages,
          cities: cities,
          sliderImages: sliderImages,
          promoImages: promoImages,
        );
      },
    );
  }

  Widget _buildHomeContentBody({
    required List<TourPackage> tourPackages,
    required List<City> cities,
    required List<String> sliderImages,
    required List<String> promoImages,
  }) {
    return SingleChildScrollView(
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // ======== SLIDER DESTINASI DENGAN OVERLAY TEXT ========
          if (sliderImages.isNotEmpty)
            _buildDestinasiCarousel(sliderImages),

          const SizedBox(height: 24),

          // ======== CITY SECTION ========
          Padding(
            padding: const EdgeInsets.symmetric(horizontal: 20),
            child: Text(
              'Kota Wisata',
              style: GoogleFonts.poppins(
                fontSize: 18,
                fontWeight: FontWeight.w600,
                color: Colors.grey.shade800,
              ),
            ),
          ),
          const SizedBox(height: 16),
          SizedBox(
            height: 90,
            child: ListView.builder(
              scrollDirection: Axis.horizontal,
              padding: const EdgeInsets.symmetric(horizontal: 20),
              itemCount: cities.length,
              itemBuilder: (context, index) {
                final city = cities[index];
                return Container(
                  width: 80,
                  margin: const EdgeInsets.only(right: 16),
                  child: Column(
                    children: [
                      CircleAvatar(
                        radius: 28,
                        backgroundColor: Colors.blue.shade50,
                        backgroundImage: city.imageUrl.isNotEmpty
                            ? _getImageProvider(city.imageUrl)
                            : null,
                        child: city.imageUrl.isEmpty
                            ? Icon(
                          Icons.location_city,
                          color: Colors.blue.shade600,
                          size: 26,
                              )
                            : null,
                      ),
                      const SizedBox(height: 6),
                      Text(
                        city.name,
                        style: GoogleFonts.poppins(
                          fontSize: 12,
                          fontWeight: FontWeight.w500,
                          color: Colors.grey.shade700,
                        ),
                        textAlign: TextAlign.center,
                        maxLines: 1,
                        overflow: TextOverflow.ellipsis,
                      ),
                    ],
                  ),
                );
              },
            ),
          ),

          const SizedBox(height: 24),

          // ======== PAKET WISATA (HORIZONTAL SLIDER) ========
          Padding(
            padding: const EdgeInsets.symmetric(horizontal: 20),
            child: Text(
              'Wisata Favorit',
              style: GoogleFonts.poppins(
                fontSize: 18,
                fontWeight: FontWeight.w600,
                color: Colors.grey.shade800,
              ),
            ),
          ),
          const SizedBox(height: 16),
          _buildTourCardList(tourPackages),

          const SizedBox(height: 24),

          // ======== PROMO BERITA ========
          if (promoImages.isNotEmpty)
          Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Padding(
                padding: const EdgeInsets.symmetric(horizontal: 20),
                child: Text(
                  'Berita',
                  style: GoogleFonts.poppins(
                    fontWeight: FontWeight.bold,
                    color: Colors.grey.shade800,
                  ),
                ),
              ),
              const SizedBox(height: 10),
              // Carousel dengan footer full width
              PromoCarousel(images: promoImages),
            ],
          ),

          const SizedBox(height: 24),

          // ======== WISATA FAVORIT (GRID 2 KOLOM) ========
          Padding(
            padding: const EdgeInsets.symmetric(horizontal: 20),
            child: Text(
              'Pilihan Wisata',
              style: GoogleFonts.poppins(
                fontSize: 18,
                fontWeight: FontWeight.w600,
                color: Colors.grey.shade800,
              ),
            ),
          ),
          const SizedBox(height: 16),
          _buildGridTourList(tourPackages),

          const SizedBox(height: 40),
        ],
      ),
    );
  }

  // ======== CAROUSEL DESTINASI DENGAN OVERLAY TEXT ========
  Widget _buildDestinasiCarousel(List<String> sliderImages) {
    return Column(
      children: [
        SizedBox(
          height: 230,
          width: double.infinity,
          child: NotificationListener<ScrollNotification>(
            onNotification: (notification) {
              // Deteksi saat user mulai scroll manual
              if (notification is ScrollStartNotification) {
                _pauseAutoSlide();
              }
              return false;
            },
            child: PageView.builder(
              controller: _destinasiCarouselController,
              itemCount: sliderImages.length,
              allowImplicitScrolling: false,
              onPageChanged: (index) {
                if (mounted) {
                  // Update ValueNotifier tanpa rebuild seluruh widget
                  _destinasiCarouselIndexNotifier.value = index;
                  // Reset flags dan resume auto-play setelah page berubah
                  Future.delayed(const Duration(milliseconds: 200), () {
                    if (mounted) {
                      _isAnimating = false;
                      _resumeAutoSlide();
                    }
                  });
                }
              },
            itemBuilder: (context, index) {
              final imagePath = sliderImages[index];
              return Padding(
                padding: const EdgeInsets.symmetric(horizontal: 20),
                child: Stack(
                  children: [
                    ClipRRect(
                      borderRadius: BorderRadius.circular(12),
                      child: Container(
                        color: Colors.blue.shade50,
                        child: _buildImage(
                          imagePath,
                          width: double.infinity,
                          height: double.infinity,
                          fit: BoxFit.cover,
                        ),
                      ),
                    ),
                    // Overlay gradient
                    Container(
                      decoration: BoxDecoration(
                        borderRadius: BorderRadius.circular(12),
                        gradient: LinearGradient(
                          begin: Alignment.topCenter,
                          end: Alignment.bottomCenter,
                          colors: [
                            Colors.black.withOpacity(0.3),
                            Colors.black.withOpacity(0.6),
                          ],
                        ),
                      ),
                    ),
                    // Overlay text
                    Center(
                      child: Text(
                        'Temukan Destinasi Impian Anda',
                        style: GoogleFonts.poppins(
                          color: Colors.white,
                          fontSize: 20,
                          fontWeight: FontWeight.w600,
                          shadows: [
                            Shadow(
                              color: Colors.black.withOpacity(0.4),
                              blurRadius: 4,
                            ),
                          ],
                        ),
                        textAlign: TextAlign.center,
                      ),
                    ),
                  ],
                ),
              );
            },
            ),
          ),
        ),
        const SizedBox(height: 10),
        // Footer indicator dengan ValueListenableBuilder untuk update tanpa rebuild seluruh widget
        ValueListenableBuilder<int>(
          valueListenable: _destinasiCarouselIndexNotifier,
          builder: (context, currentIndex, child) {
            return Container(
              width: double.infinity,
              padding: const EdgeInsets.symmetric(vertical: 8),
              child: Row(
                mainAxisAlignment: MainAxisAlignment.center,
                children: List.generate(
                  sliderImages.length,
                  (index) => Container(
                    margin: const EdgeInsets.symmetric(horizontal: 4),
                    width: 8,
                    height: 8,
                    decoration: BoxDecoration(
                      shape: BoxShape.circle,
                      color: currentIndex == index
                          ? Colors.blue.shade600
                          : Colors.grey.shade300,
                    ),
                  ),
                ),
              ),
            );
          },
        ),
      ],
    );
  }

  // ======== SLIDER (PAKET WISATA PERTAMA) ========
  Widget _buildTourCardList(List<dynamic> tourPackages) {
    return SizedBox(
      height: 280,
      child: ListView.builder(
        scrollDirection: Axis.horizontal,
        padding: const EdgeInsets.symmetric(horizontal: 20),
        itemCount: tourPackages.length,
        itemBuilder: (context, index) {
          final package = tourPackages[index];
          return Container(
            width: 190,
            margin: const EdgeInsets.only(right: 16),
            decoration: BoxDecoration(
              borderRadius: BorderRadius.circular(16),
              color: Colors.white,
              boxShadow: [
                BoxShadow(
                  color: Colors.black.withOpacity(0.05),
                  blurRadius: 6,
                  offset: const Offset(0, 4),
                ),
              ],
            ),
            child: InkWell(
              borderRadius: BorderRadius.circular(16),
              onTap: () {
                Navigator.push(
                  context,
                  MaterialPageRoute(
                    builder: (context) => PackageDetailPage(package: package),
                  ),
                );
              },
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  ClipRRect(
                    borderRadius: const BorderRadius.vertical(
                      top: Radius.circular(16),
                    ),
                    child: _buildImage(
                      package.imageUrl,
                      width: double.infinity,
                      height: 130,
                      fit: BoxFit.cover,
                    ),
                  ),
                  Padding(
                    padding: const EdgeInsets.all(10),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          package.title,
                          style: GoogleFonts.poppins(
                            fontSize: 14,
                            fontWeight: FontWeight.w600,
                            color: Colors.grey.shade800,
                          ),
                          maxLines: 2,
                          overflow: TextOverflow.ellipsis,
                        ),
                        const SizedBox(height: 4),
                        Text(
                          package.destination,
                          style: GoogleFonts.poppins(
                            fontSize: 12,
                            color: Colors.grey.shade600,
                          ),
                        ),
                        const SizedBox(height: 6),
                        Text(
                          'Rp ${formatter.format(package.price)}',
                          style: GoogleFonts.poppins(
                            fontSize: 14,
                            fontWeight: FontWeight.bold,
                            color: Colors.blue.shade600,
                          ),
                        ),
                        const SizedBox(height: 4),
                        Row(
                          children: [
                            Icon(
                              Icons.star,
                              size: 14,
                              color: Colors.amber.shade600,
                            ),
                            const SizedBox(width: 3),
                            Text(
                              '${package.rating} (${package.totalRatings})',
                              style: GoogleFonts.poppins(
                                fontSize: 12,
                                color: Colors.grey.shade600,
                              ),
                            ),
                          ],
                        ),
                      ],
                    ),
                  ),
                ],
              ),
            ),
          );
        },
      ),
    );
  }

  // ======== GRID (WISATA FAVORIT / BAGIAN KEDUA) ========
  Widget _buildGridTourList(List<dynamic> tourPackages) {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 20),
      child: GridView.builder(
        itemCount: tourPackages.length,
        shrinkWrap: true,
        physics: const NeverScrollableScrollPhysics(),
        gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
          crossAxisCount: 2,
          crossAxisSpacing: 14,
          mainAxisSpacing: 14,
          childAspectRatio: 0.72,
        ),
        itemBuilder: (context, index) {
          final package = tourPackages[index];
          return GestureDetector(
            onTap: () {
              Navigator.push(
                context,
                MaterialPageRoute(
                  builder: (context) => PackageDetailPage(package: package),
                ),
              );
            },
            child: Container(
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(16),
                boxShadow: [
                  BoxShadow(
                    color: Colors.black.withOpacity(0.05),
                    blurRadius: 5,
                    offset: const Offset(0, 3),
                  ),
                ],
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  ClipRRect(
                    borderRadius: const BorderRadius.vertical(
                      top: Radius.circular(16),
                    ),
                    child: _buildImage(
                      package.imageUrl,
                      height: 120,
                      width: double.infinity,
                      fit: BoxFit.cover,
                    ),
                  ),
                  Padding(
                    padding: const EdgeInsets.all(10),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          package.title,
                          maxLines: 1,
                          overflow: TextOverflow.ellipsis,
                          style: GoogleFonts.poppins(
                            fontWeight: FontWeight.w600,
                            fontSize: 14,
                          ),
                        ),
                        const SizedBox(height: 4),
                        Text(
                          'Rp ${formatter.format(package.price)}',
                          style: GoogleFonts.poppins(
                            color: Colors.blueAccent,
                            fontWeight: FontWeight.w600,
                            fontSize: 13,
                          ),
                        ),
                        const SizedBox(height: 6),
                        Row(
                          children: [
                            Icon(
                              Icons.star,
                              size: 14,
                              color: Colors.amber.shade600,
                            ),
                            const SizedBox(width: 4),
                            Text(
                              '${package.rating}',
                              style: GoogleFonts.poppins(
                                fontSize: 12,
                                color: Colors.grey[600],
                              ),
                            ),
                          ],
                        ),
                      ],
                    ),
                  ),
                ],
              ),
            ),
          );
        },
      ),
    );
  }

  // Helper method to get ImageProvider (supports both asset and network)
  ImageProvider _getImageProvider(String imageUrl) {
    // Check if it's a network image (http/https or /api/asset/ or /api/storage/)
    if (imageUrl.startsWith('http://') || 
        imageUrl.startsWith('https://') ||
        imageUrl.startsWith('/api/asset/') ||
        imageUrl.startsWith('/api/storage/')) {
      // Network image - fix URL if needed (handle all formats)
      final fixedUrl = ApiConfig.fixImageUrl(imageUrl);
      return NetworkImage(fixedUrl);
    } else if (imageUrl.startsWith('assets/')) {
      // Asset image
      return AssetImage(imageUrl);
    } else {
      // Assume asset image without 'assets/' prefix
      return AssetImage('assets/images/$imageUrl');
    }
  }

  // Helper method to build Image widget (supports both asset and network)
  Widget _buildImage(
    String imageUrl, {
    double? width,
    double? height,
    BoxFit fit = BoxFit.cover,
  }) {
    // Check if it's a network image (http/https or /api/asset/ or /api/storage/)
    if (imageUrl.startsWith('http://') || 
        imageUrl.startsWith('https://') ||
        imageUrl.startsWith('/api/asset/') ||
        imageUrl.startsWith('/api/storage/')) {
      // Network image - fix URL if needed (handle all formats)
      final fixedUrl = ApiConfig.fixImageUrl(imageUrl);
      return Image.network(
        fixedUrl,
        width: width,
        height: height,
        fit: fit,
        loadingBuilder: (context, child, loadingProgress) {
          if (loadingProgress == null) return child;
          return Container(
            width: width,
            height: height,
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
            width: width,
            height: height,
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
        width: width,
        height: height,
        fit: fit,
        errorBuilder: (context, error, stackTrace) {
          return Container(
            width: width,
            height: height,
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
