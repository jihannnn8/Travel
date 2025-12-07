<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Destination extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'destination',
        'price',
        'duration',
        'departure_date',
        'rating',
        'total_ratings',
        'rundown',
        'image_url',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'rating' => 'decimal:2',
        'departure_date' => 'date',
        'rundown' => 'array',
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'destination_id');
    }
    /**
     * Accessor untuk kompatibilitas dengan field lama
     */
    public function getNamaAttribute()
    {
        return $this->title;
    }

    public function getHargaAttribute()
    {
        return $this->price;
    }

    public function getDurasiAttribute()
    {
        return $this->duration;
    }

    public function getFotoAttribute()
    {
        return $this->image_url;
    }
}
