<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\City;
use Illuminate\Support\Facades\DB;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data
        DB::table('cities')->truncate();

        $cities = [
            [
                'name' => 'D.I.Y Yogyakarta',
                'image_url' => 'Asset_Travelo/Yogya.jpg',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Bali',
                'image_url' => 'Asset_Travelo/Kuta.jpg',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Lombok',
                'image_url' => 'Asset_Travelo/lombok.jpeg',
                'order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Bromo',
                'image_url' => 'Asset_Travelo/Bromo.jpg',
                'order' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'Papua',
                'image_url' => 'Asset_Travelo/images/papua.jpg',
                'order' => 5,
                'is_active' => true,
            ],
        ];

        foreach ($cities as $city) {
            City::create($city);
        }
    }
}

