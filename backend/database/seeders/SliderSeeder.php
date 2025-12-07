<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Slider;
use Illuminate\Support\Facades\DB;

class SliderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data
        DB::table('sliders')->truncate();

        $sliders = [
            [
                'image_url' => 'Asset_Travelo/Bromo.jpg',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'image_url' => 'Asset_Travelo/Kuta.jpg',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'image_url' => 'Asset_Travelo/Yogya.jpg',
                'order' => 3,
                'is_active' => true,
            ],
        ];

        foreach ($sliders as $slider) {
            Slider::create($slider);
        }
    }
}

