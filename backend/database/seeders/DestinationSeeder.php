<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Destination;
use Carbon\Carbon;

class DestinationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Data destinations sesuai dengan mobile app
        $destinations = [
            [
                'title' => 'Pantai Lombok',
                'description' => 'Nikmati keindahan pantai Lombok dengan pemandangan yang menakjubkan',
                'destination' => 'Lombok, NTB',
                'price' => 1000000,
                'duration' => '2 Hari 3 Malam',
                'departure_date' => Carbon::parse('2024-01-15'),
                'rating' => 4.8,
                'total_ratings' => 120,
                'rundown' => [
                    'Hari 1: Kedatangan di Lombok, check-in hotel',
                    'Hari 2: Tour ke Pantai Kuta, Pantai Tanjung Aan',
                    'Hari 3: Tour ke Gili Trawangan, snorkeling',
                    'Hari 4: Free time, check-out hotel'
                ],
                'image_url' => 'Asset_Travelo/lombok.jpeg', // Default logo untuk Lombok
            ],
            [
                'title' => 'Yogyakarta Heritage',
                'description' => 'Jelajahi warisan budaya Yogyakarta yang kaya akan sejarah',
                'destination' => 'Yogyakarta',
                'price' => 750000,
                'duration' => '3 Hari 2 Malam',
                'departure_date' => Carbon::parse('2024-01-20'),
                'rating' => 4.6,
                'total_ratings' => 95,
                'rundown' => [
                    'Hari 1: Kedatangan di Yogyakarta, city tour',
                    'Hari 2: Candi Borobudur, Candi Prambanan',
                    'Hari 3: Malioboro, Keraton Yogyakarta'
                ],
                'image_url' => 'Asset_Travelo/Yogya.jpg',
            ],
            [
                'title' => 'Bali Adventure',
                'description' => 'Petualangan seru di Pulau Dewata dengan berbagai aktivitas menarik',
                'destination' => 'Bali',
                'price' => 1200000,
                'duration' => '4 Hari 3 Malam',
                'departure_date' => Carbon::parse('2024-01-25'),
                'rating' => 4.9,
                'total_ratings' => 150,
                'rundown' => [
                    'Hari 1: Kedatangan di Bali, check-in hotel',
                    'Hari 2: Tanah Lot, Uluwatu Temple',
                    'Hari 3: Ubud, Tegallalang Rice Terrace',
                    'Hari 4: Water sports, free time'
                ],
                'image_url' => 'Asset_Travelo/Kuta.jpg', // Kuta adalah pantai terkenal di Bali
            ],
            [
                'title' => 'Raja Ampat Paradise',
                'description' => 'Surga bawah laut terbaik di dunia dengan keindahan yang memukau',
                'destination' => 'Raja Ampat, Papua',
                'price' => 2500000,
                'duration' => '5 Hari 4 Malam',
                'departure_date' => Carbon::parse('2024-01-30'),
                'rating' => 4.7,
                'total_ratings' => 80,
                'rundown' => [
                    'Hari 1: Kedatangan di Sorong, transfer ke Waisai',
                    'Hari 2: Snorkeling di Wayag',
                    'Hari 3: Diving di Cape Kri',
                    'Hari 4: Island hopping',
                    'Hari 5: Free time, departure'
                ],
                'image_url' => 'Asset_Travelo/raja_ampat.jpeg', // Default logo untuk Raja Ampat
            ],
            [
                'title' => 'Bromo Tengger',
                'description' => 'Menyaksikan matahari terbit dari Gunung Bromo yang legendaris',
                'destination' => 'Bromo, Jawa Timur',
                'price' => 600000,
                'duration' => '2 Hari 1 Malam',
                'departure_date' => Carbon::parse('2024-02-05'),
                'rating' => 4.5,
                'total_ratings' => 200,
                'rundown' => [
                    'Hari 1: Kedatangan di Probolinggo, transfer ke Bromo',
                    'Hari 2: Sunrise di Penanjakan, kawah Bromo'
                ],
                'image_url' => 'Asset_Travelo/Bromo.jpg',
            ],
        ];

        // Insert destinations
        foreach ($destinations as $destination) {
            Destination::create($destination);
        }

        $this->command->info('Destination seeder berhasil dijalankan!');
        $this->command->info('Total ' . count($destinations) . ' destinations berhasil dibuat.');
    }
}

