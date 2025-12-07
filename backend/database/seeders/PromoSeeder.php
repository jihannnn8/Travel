<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Promo;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PromoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data
        DB::table('promos')->truncate();

        $promos = [
            [
                'image_url' => 'Asset_Travelo/images/InfoMalang.jpg',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'image_url' => 'Asset_Travelo/images/InfoBali.jpg',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'image_url' => 'Asset_Travelo/images/InfoYogya.jpg',
                'order' => 3,
                'is_active' => true,
            ],
            // Tambahkan promo lain jika diperlukan
        ];

        foreach ($promos as $promo) {
            Promo::create($promo);
        }
    }
}

