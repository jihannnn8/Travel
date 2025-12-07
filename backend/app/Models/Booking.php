<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'destination_id',
        'customer_name',
        'kode_booking',
        'midtrans_order_id',
        'midtrans_payment_token',
        'payment_status',
        'tanggal_booking',
        'status',
        'tanggal_keberangkatan',
        'waktu_keberangkatan',
        'lokasi_penjemputan',
        'metode_pembayaran',
        'total_harga',
        'midtrans_response',
    ];

    protected $casts = [
        'tanggal_booking' => 'date',
        'tanggal_keberangkatan' => 'date',
        'total_harga' => 'decimal:2',
        'midtrans_response' => 'array',
    ];

    /**
     * Boot method untuk auto-generate kode_booking dari midtrans_order_id
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($booking) {
            // Generate kode_booking dari midtrans_order_id jika ada
            if (!empty($booking->midtrans_order_id)) {
                $booking->kode_booking = 'BOOK-' . $booking->midtrans_order_id;
            }
        });

        static::updating(function ($booking) {
            // Update kode_booking jika midtrans_order_id berubah
            if ($booking->isDirty('midtrans_order_id') && !empty($booking->midtrans_order_id)) {
                $booking->kode_booking = 'BOOK-' . $booking->midtrans_order_id;
            }
        });
    }

    /**
     * Accessor untuk kode_booking - auto-generate jika belum ada
     */
    public function getKodeBookingAttribute($value)
    {
        // Jika kode_booking belum ada tapi midtrans_order_id ada, return generated
        if (empty($value) && !empty($this->midtrans_order_id)) {
            return 'BOOK-' . $this->midtrans_order_id;
        }
        return $value;
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function destination()
    {
        return $this->belongsTo(Destination::class, 'destination_id');
    }
}
