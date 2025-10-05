import '../models/tour_package.dart';
import '../models/city.dart';

class DataService {
  static List<TourPackage> getTourPackages() {
    return [
      TourPackage(
        id: '1',
        title: 'Pantai Lombok',
        description: 'Nikmati keindahan pantai Lombok dengan pemandangan yang menakjubkan',
        imageUrl: 'assets/images/lombok.jpg',
        price: 1000000,
        duration: '2 Hari 3 Malam',
        departureDate: '15 Januari 2024',
        rating: 4.8,
        totalRatings: 120,
        destination: 'Lombok, NTB',
        rundown: [
          'Hari 1: Kedatangan di Lombok, check-in hotel',
          'Hari 2: Tour ke Pantai Kuta, Pantai Tanjung Aan',
          'Hari 3: Tour ke Gili Trawangan, snorkeling',
          'Hari 4: Free time, check-out hotel'
        ],
      ),
      TourPackage(
        id: '2',
        title: 'Yogyakarta Heritage',
        description: 'Jelajahi warisan budaya Yogyakarta yang kaya akan sejarah',
        imageUrl: 'assets/images/yogyakarta.jpg',
        price: 750000,
        duration: '3 Hari 2 Malam',
        departureDate: '20 Januari 2024',
        rating: 4.6,
        totalRatings: 95,
        destination: 'Yogyakarta',
        rundown: [
          'Hari 1: Kedatangan di Yogyakarta, city tour',
          'Hari 2: Candi Borobudur, Candi Prambanan',
          'Hari 3: Malioboro, Keraton Yogyakarta'
        ],
      ),
      TourPackage(
        id: '3',
        title: 'Bali Adventure',
        description: 'Petualangan seru di Pulau Dewata dengan berbagai aktivitas menarik',
        imageUrl: 'assets/images/bali.jpg',
        price: 1200000,
        duration: '4 Hari 3 Malam',
        departureDate: '25 Januari 2024',
        rating: 4.9,
        totalRatings: 150,
        destination: 'Bali',
        rundown: [
          'Hari 1: Kedatangan di Bali, check-in hotel',
          'Hari 2: Tanah Lot, Uluwatu Temple',
          'Hari 3: Ubud, Tegallalang Rice Terrace',
          'Hari 4: Water sports, free time'
        ],
      ),
      TourPackage(
        id: '4',
        title: 'Raja Ampat Paradise',
        description: 'Surga bawah laut terbaik di dunia dengan keindahan yang memukau',
        imageUrl: 'assets/images/raja_ampat.jpg',
        price: 2500000,
        duration: '5 Hari 4 Malam',
        departureDate: '30 Januari 2024',
        rating: 4.7,
        totalRatings: 80,
        destination: 'Raja Ampat, Papua',
        rundown: [
          'Hari 1: Kedatangan di Sorong, transfer ke Waisai',
          'Hari 2: Snorkeling di Wayag',
          'Hari 3: Diving di Cape Kri',
          'Hari 4: Island hopping',
          'Hari 5: Free time, departure'
        ],
      ),
      TourPackage(
        id: '5',
        title: 'Bromo Tengger',
        description: 'Menyaksikan matahari terbit dari Gunung Bromo yang legendaris',
        imageUrl: 'assets/images/bromo.jpg',
        price: 600000,
        duration: '2 Hari 1 Malam',
        departureDate: '5 Februari 2024',
        rating: 4.5,
        totalRatings: 200,
        destination: 'Bromo, Jawa Timur',
        rundown: [
          'Hari 1: Kedatangan di Probolinggo, transfer ke Bromo',
          'Hari 2: Sunrise di Penanjakan, kawah Bromo'
        ],
      ),
    ];
  }

  static List<City> getCities() {
    return [
      City(
        id: '1',
        name: 'D.I.Y Yogyakarta',
        imageUrl: 'assets/images/yogyakarta_icon.jpg',
      ),
      City(
        id: '2',
        name: 'Bali',
        imageUrl: 'assets/images/bali_icon.jpg',
      ),
      City(
        id: '3',
        name: 'Lombok',
        imageUrl: 'assets/images/lombok_icon.jpg',
      ),
      City(
        id: '4',
        name: 'Bromo',
        imageUrl: 'assets/images/bromo_icon.jpg',
      ),
      City(
        id: '5',
        name: 'Raja Ampat',
        imageUrl: 'assets/images/raja_ampat_icon.jpg',
      ),
    ];
  }

  static List<String> getSliderImages() {
    return [
      'assets/images/Bromo.jpg',
      'assets/images/Kuta.jpg',
      'assets/images/Yogya.jpg',
    ];
  }

  static List<String> getPickupTimes() {
    return [
      '06:00 WIB',
      '07:00 WIB',
      '08:00 WIB',
      '09:00 WIB',
      '10:00 WIB',
    ];
  }

  static List<String> getPaymentMethods() {
    return [
      'Transfer Bank',
      'E-Wallet (GoPay)',
      'E-Wallet (OVO)',
      'E-Wallet (DANA)',
      'Credit Card',
    ];
  }
}
