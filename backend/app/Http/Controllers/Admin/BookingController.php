<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\User;
use App\Models\Destination;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $bookings = Booking::with(['user', 'destination'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('admin.bookings.index', compact('bookings'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::where('role', 'user')->orderBy('name')->get();
        $destinations = Destination::orderBy('title')->get();
        
        return view('admin.bookings.create', compact('users', 'destinations'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'destination_id' => 'required|exists:destinations,id',
            'tanggal_booking' => 'required|date',
            'status' => 'required|in:pending,completed,cancelled',
            'tanggal_keberangkatan' => 'required|date',
            'waktu_keberangkatan' => 'required|string|max:255',
            'lokasi_penjemputan' => 'required|in:bandara,terminal',
            'metode_pembayaran' => 'required|in:transfer,e-wallet',
            'total_harga' => 'required|numeric|min:0',
        ], [
            'user_id.required' => 'User wajib dipilih.',
            'user_id.exists' => 'User yang dipilih tidak valid.',
            'destination_id.required' => 'Destinasi wajib dipilih.',
            'destination_id.exists' => 'Destinasi yang dipilih tidak valid.',
            'tanggal_booking.required' => 'Tanggal booking wajib diisi.',
            'tanggal_booking.date' => 'Tanggal booking harus valid.',
            'status.required' => 'Status wajib dipilih.',
            'status.in' => 'Status harus pending, completed, atau cancelled.',
            'tanggal_keberangkatan.required' => 'Tanggal keberangkatan wajib diisi.',
            'tanggal_keberangkatan.date' => 'Tanggal keberangkatan harus valid.',
            'waktu_keberangkatan.required' => 'Waktu keberangkatan wajib diisi.',
            'lokasi_penjemputan.required' => 'Lokasi penjemputan wajib dipilih.',
            'lokasi_penjemputan.in' => 'Lokasi penjemputan harus bandara atau terminal.',
            'metode_pembayaran.required' => 'Metode pembayaran wajib dipilih.',
            'metode_pembayaran.in' => 'Metode pembayaran harus transfer atau e-wallet.',
            'total_harga.required' => 'Total harga wajib diisi.',
            'total_harga.numeric' => 'Total harga harus berupa angka.',
            'total_harga.min' => 'Total harga tidak boleh negatif.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Generate midtrans order ID
        $orderId = 'ORDER-' . time() . '-' . $request->user_id;
        
        // Generate kode booking dari order ID
        $kodeBooking = 'BOOK-' . $orderId;

        // Create booking
        $booking = Booking::create([
            'user_id' => $request->user_id,
            'destination_id' => $request->destination_id,
            'midtrans_order_id' => $orderId,
            'kode_booking' => $kodeBooking,
            'tanggal_booking' => $request->tanggal_booking,
            'status' => $request->status,
            'tanggal_keberangkatan' => $request->tanggal_keberangkatan,
            'waktu_keberangkatan' => $request->waktu_keberangkatan,
            'lokasi_penjemputan' => $request->lokasi_penjemputan,
            'metode_pembayaran' => $request->metode_pembayaran,
            'total_harga' => $request->total_harga,
        ]);

        return redirect()->route('bookings.index')
            ->with('success', 'Booking berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $booking = Booking::with(['user', 'destination'])->findOrFail($id);
        return view('admin.bookings.show', compact('booking'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $booking = Booking::with(['user', 'destination'])->findOrFail($id);
        $users = User::where('role', 'user')->orderBy('name')->get();
        $destinations = Destination::orderBy('title')->get();
        
        return view('admin.bookings.edit', compact('booking', 'users', 'destinations'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $booking = Booking::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'destination_id' => 'required|exists:destinations,id',
            'customer_name' => 'required|string|max:255',
            'tanggal_booking' => 'required|date',
            'status' => 'required|in:pending,completed,cancelled',
            'tanggal_keberangkatan' => 'required|date',
            'waktu_keberangkatan' => 'required|string|max:255',
            'lokasi_penjemputan' => 'required|in:bandara,terminal',
            'metode_pembayaran' => 'required|in:transfer,e-wallet',
            'total_harga' => 'required|numeric|min:0',
        ], [
            'user_id.required' => 'User wajib dipilih.',
            'user_id.exists' => 'User yang dipilih tidak valid.',
            'destination_id.required' => 'Destinasi wajib dipilih.',
            'destination_id.exists' => 'Destinasi yang dipilih tidak valid.',
            'customer_name.required' => 'Nama pelanggan wajib diisi.',
            'customer_name.string' => 'Nama pelanggan harus berupa string.',
            'customer_name.max' => 'Nama pelanggan maksimal 255 karakter.',
            'tanggal_booking.required' => 'Tanggal booking wajib diisi.',
            'tanggal_booking.date' => 'Tanggal booking harus valid.',
            'status.required' => 'Status wajib dipilih.',
            'status.in' => 'Status harus pending, completed, atau cancelled.',
            'tanggal_keberangkatan.required' => 'Tanggal keberangkatan wajib diisi.',
            'tanggal_keberangkatan.date' => 'Tanggal keberangkatan harus valid.',
            'waktu_keberangkatan.required' => 'Waktu keberangkatan wajib diisi.',
            'lokasi_penjemputan.required' => 'Lokasi penjemputan wajib dipilih.',
            'lokasi_penjemputan.in' => 'Lokasi penjemputan harus bandara atau terminal.',
            'metode_pembayaran.required' => 'Metode pembayaran wajib dipilih.',
            'metode_pembayaran.in' => 'Metode pembayaran harus transfer atau e-wallet.',
            'total_harga.required' => 'Total harga wajib diisi.',
            'total_harga.numeric' => 'Total harga harus berupa angka.',
            'total_harga.min' => 'Total harga tidak boleh negatif.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Update kode_booking jika midtrans_order_id ada
        $updateData = [
            'user_id' => $request->user_id,
            'destination_id' => $request->destination_id,
            'customer_name' => $request->customer_name,
            'tanggal_booking' => $request->tanggal_booking,
            'status' => $request->status,
            'tanggal_keberangkatan' => $request->tanggal_keberangkatan,
            'waktu_keberangkatan' => $request->waktu_keberangkatan,
            'lokasi_penjemputan' => $request->lokasi_penjemputan,
            'metode_pembayaran' => $request->metode_pembayaran,
            'total_harga' => $request->total_harga,
        ];
        
        // Update kode_booking jika midtrans_order_id ada
        if (!empty($booking->midtrans_order_id)) {
            $updateData['kode_booking'] = 'BOOK-' . $booking->midtrans_order_id;
        }

        // Update booking
        $booking->update($updateData);

        return redirect()->route('bookings.index')
            ->with('success', 'Booking berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $booking = Booking::findOrFail($id);
        
        $booking->delete();
        
        return redirect()->route('bookings.index')
            ->with('success', 'Booking berhasil dihapus.');
    }
}
