<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            // Primary key
            $table->id();
            
            // Foreign keys
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('destination_id')->constrained('destinations')->onDelete('restrict');
            $table->string('customer_name');
            
            // Booking identification
            $table->string('midtrans_order_id', 100)->nullable()->unique();
            $table->string('kode_booking', 150)->nullable()->unique();
            
            // Payment information (Midtrans)
            $table->string('midtrans_payment_token')->nullable();
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'expired', 'cancelled'])->default('pending');
            $table->json('midtrans_response')->nullable();
            
            // Booking dates
            $table->date('tanggal_booking');
            $table->date('tanggal_keberangkatan');
            $table->string('waktu_keberangkatan', 20);
            $table->enum('lokasi_penjemputan', ['bandara', 'terminal'])->default('bandara');
            
            // Booking status
            // Menggunakan VARCHAR untuk mendukung status dalam bahasa Indonesia
            // Status: Menunggu Pembayaran, Dikonfirmasi, Selesai, Dibatalkan
            $table->string('status', 50)->default('Menunggu Pembayaran');
            
            // Payment details
            $table->string('metode_pembayaran', 50)->default('transfer');
            $table->decimal('total_harga', 15, 2);
            
            // Timestamps
            $table->timestamps();
            
            // Indexes for better performance
            $table->index('user_id');
            $table->index('destination_id');
            $table->index('status');
            $table->index('payment_status');
            $table->index('tanggal_booking');
            $table->index('tanggal_keberangkatan');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
