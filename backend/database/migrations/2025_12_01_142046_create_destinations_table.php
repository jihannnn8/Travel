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
        Schema::create('destinations', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('destination'); // Lokasi destinasi (Lombok, Yogyakarta, dll)
            $table->decimal('price', 10, 2);
            $table->string('duration');
            $table->date('departure_date')->nullable();
            $table->decimal('rating', 3, 2)->default(0); // Rating (0.00 - 5.00)
            $table->integer('total_ratings')->default(0);
            $table->json('rundown')->nullable();
            $table->string('image_url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('destinations');
    }
};
