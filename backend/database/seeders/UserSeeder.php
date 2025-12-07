<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hapus data user yang sudah ada (optional, untuk fresh start)
        // User::truncate();

        // Buat Admin User
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@travelo.com',
            'phone_number' => '081234567890',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // Buat Admin kedua (untuk testing)
        User::create([
            'name' => 'Admin Travelo',
            'email' => 'admin@travelo.id',
            'phone_number' => '081234567891',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // Buat User biasa
        User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone_number' => '081234567892',
            'password' => Hash::make('password123'),
            'role' => 'user',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'phone_number' => '081234567893',
            'password' => Hash::make('password123'),
            'role' => 'user',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'phone_number' => '081234567894',
            'password' => Hash::make('password123'),
            'role' => 'user',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Siti Nurhaliza',
            'email' => 'siti@example.com',
            'phone_number' => '081234567895',
            'password' => Hash::make('password123'),
            'role' => 'user',
            'email_verified_at' => now(),
        ]);

        $this->command->info('User seeder berhasil dijalankan!');
        $this->command->info('Admin credentials:');
        $this->command->info('  Email: admin@travelo.com');
        $this->command->info('  Password: admin123');
    }
}

