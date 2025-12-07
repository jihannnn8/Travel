<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Destination;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Create booking dan generate payment token
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'destination_id' => 'required|exists:destinations,id',
            'customer_name' => 'required|string|max:255',
            'tanggal_keberangkatan' => 'required|date',
            'waktu_keberangkatan' => 'required|string|max:255',
            'lokasi_penjemputan' => 'required|in:bandara,terminal',
            'metode_pembayaran' => 'required|in:transfer,e-wallet,credit_card,bank_transfer,echannel',
        ], [
            'destination_id.required' => 'Destinasi wajib dipilih.',
            'destination_id.exists' => 'Destinasi tidak ditemukan.',
            'customer_name.required' => 'Nama pelanggan wajib diisi.',
            'customer_name.string' => 'Nama pelanggan harus berupa string.',
            'customer_name.max' => 'Nama pelanggan maksimal 255 karakter.',
            'tanggal_keberangkatan.required' => 'Tanggal keberangkatan wajib diisi.',
            'tanggal_keberangkatan.date' => 'Tanggal keberangkatan harus valid.',
            'waktu_keberangkatan.required' => 'Waktu keberangkatan wajib diisi.',
            'lokasi_penjemputan.required' => 'Lokasi penjemputan wajib dipilih.',
            'lokasi_penjemputan.in' => 'Lokasi penjemputan harus bandara atau terminal.',
            'metode_pembayaran.required' => 'Metode pembayaran wajib dipilih.',
            'metode_pembayaran.in' => 'Metode pembayaran tidak valid.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $user = Auth::user();
            $destination = Destination::findOrFail($request->destination_id);

            // Generate midtrans order ID
            $orderId = 'ORDER-' . time() . '-' . $user->id;
            
            // Generate kode booking dari order ID
            $kodeBooking = 'BOOK-' . $orderId;

            // Create booking
            $booking = Booking::create([
                'user_id' => $user->id,
                'destination_id' => $destination->id,
                'customer_name' => $request->customer_name ?? '',
                'midtrans_order_id' => $orderId,
                'kode_booking' => $kodeBooking,
                'tanggal_booking' => now()->toDateString(),
                'status' => 'Menunggu Pembayaran',
                'payment_status' => 'pending',
                'customer_name' => $request->customer_name,
                'tanggal_keberangkatan' => $request->tanggal_keberangkatan,
                'waktu_keberangkatan' => $request->waktu_keberangkatan,
                'lokasi_penjemputan' => $request->lokasi_penjemputan,
                'metode_pembayaran' => $request->metode_pembayaran,
                'total_harga' => $destination->price,
            ]);

            // Prepare transaction details untuk Midtrans
            $transactionDetails = [
                'order_id' => $orderId,
                'gross_amount' => (int) $destination->price,
            ];

            // Customer details
            $customerDetails = [
                'first_name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone_number ?? '',
            ];

            // Item details
            $itemDetails = [
                [
                    'id' => $destination->id,
                    'price' => (int) $destination->price,
                    'quantity' => 1,
                    'name' => $destination->title,
                ],
            ];

            // Create payment transaction
            $paymentResult = $this->paymentService->createTransaction(
                $transactionDetails,
                $customerDetails,
                $itemDetails
            );

            if (!$paymentResult['success']) {
                \Log::error('Payment transaction creation failed', [
                    'booking_id' => $booking->id,
                    'order_id' => $orderId,
                    'error' => $paymentResult['error'] ?? null,
                    'message' => $paymentResult['message'] ?? 'Unknown error',
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => $paymentResult['message'] ?? 'Gagal membuat transaksi pembayaran',
                    'error' => $paymentResult['error'] ?? null,
                ], 500);
            }

            // Update booking dengan payment token
            $booking->midtrans_payment_token = $paymentResult['snap_token'];
            $booking->save();

            return response()->json([
                'success' => true,
                'message' => 'Booking berhasil dibuat',
                'data' => [
                    'booking' => [
                        'id' => (string) $booking->id,
                        'userId' => (string) $booking->user_id,
                        'packageId' => (string) $booking->destination_id,
                        'packageTitle' => $destination->title,
                        'packageImage' => $this->formatImageUrl($destination->image_url),
                        'price' => (float) $booking->total_harga,
                        'totalHarga' => (float) $booking->total_harga,
                        'departureDate' => $booking->tanggal_keberangkatan,
                        'tanggalKeberangkatan' => $booking->tanggal_keberangkatan,
                        'pickupTime' => $booking->waktu_keberangkatan,
                        'waktuKeberangkatan' => $booking->waktu_keberangkatan,
                        'customerName' => $booking->customer_name ?? '',
                        'lokasiPenjemputan' => $booking->lokasi_penjemputan,
                        'paymentMethod' => $booking->metode_pembayaran,
                        'metodePembayaran' => $booking->metode_pembayaran,
                        'status' => $booking->status,
                        'paymentStatus' => $booking->payment_status,
                        'bookingDate' => $booking->tanggal_booking,
                        'tanggalBooking' => $booking->tanggal_booking,
                        'kodeBooking' => $booking->kode_booking,
                        'orderId' => $booking->midtrans_order_id,
                        'snapToken' => $booking->midtrans_payment_token,
                        'paymentInfo' => 'Silakan lakukan pembayaran melalui Midtrans',
                    ],
                ],
            ], 201);
        } catch (\Exception $e) {
            \Log::error('Booking API Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat membuat booking',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get user bookings
     */
    public function index()
    {
        try {
            $user = Auth::user();
            $bookings = Booking::with(['destination'])
                ->where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get();

            $formattedBookings = $bookings->map(function ($booking) {
                return [
                    'id' => (string) $booking->id,
                    'userId' => (string) $booking->user_id,
                    'packageId' => (string) $booking->destination_id,
                    'packageTitle' => $booking->destination->title ?? '',
                    'packageImage' => $this->formatImageUrl($booking->destination->image_url ?? ''),
                    'price' => (float) $booking->total_harga,
                    'totalHarga' => (float) $booking->total_harga,
                    'departureDate' => $booking->tanggal_keberangkatan,
                    'tanggalKeberangkatan' => $booking->tanggal_keberangkatan,
                    'pickupTime' => $booking->waktu_keberangkatan,
                    'waktuKeberangkatan' => $booking->waktu_keberangkatan,
                    'customerName' => $booking->customer_name ?? '',
                    'lokasiPenjemputan' => $booking->lokasi_penjemputan ?? '',
                    'paymentMethod' => $booking->metode_pembayaran,
                    'metodePembayaran' => $booking->metode_pembayaran,
                    'status' => $booking->status,
                    'paymentStatus' => $booking->payment_status,
                    'bookingDate' => $booking->tanggal_booking,
                    'tanggalBooking' => $booking->tanggal_booking,
                    'kodeBooking' => $booking->kode_booking,
                    'orderId' => $booking->midtrans_order_id,
                    'snapToken' => $booking->midtrans_payment_token,
                    'paymentInfo' => $booking->payment_info ?? '',
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Bookings retrieved successfully',
                'data' => [
                    'bookings' => $formattedBookings,
                ],
            ], 200);
        } catch (\Exception $e) {
            \Log::error('Get Bookings API Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data bookings',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get single booking
     */
    public function show(string $id)
    {
        try {
            $user = Auth::user();
            $booking = Booking::with(['destination', 'user'])
                ->where('user_id', $user->id)
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Booking retrieved successfully',
                'data' => [
                    'booking' => [
                        'id' => (string) $booking->id,
                        'kodeBooking' => $booking->kode_booking,
                        'customerName' => $booking->customer_name ?? '',
                        'orderId' => $booking->midtrans_order_id,
                        'snapToken' => $booking->midtrans_payment_token,
                        'packageId' => (string) $booking->destination_id,
                        'packageTitle' => $booking->destination->title ?? '',
                        'packageImage' => $this->formatImageUrl($booking->destination->image_url ?? ''),
                        'destination' => [
                            'id' => (string) $booking->destination->id,
                            'title' => $booking->destination->title,
                            'imageUrl' => $this->formatImageUrl($booking->destination->image_url ?? ''),
                            'price' => (float) $booking->destination->price,
                        ],
                        'tanggalBooking' => $booking->tanggal_booking,
                        'tanggalKeberangkatan' => $booking->tanggal_keberangkatan,
                        'waktuKeberangkatan' => $booking->waktu_keberangkatan,
                        'totalHarga' => (float) $booking->total_harga,
                        'status' => $booking->status,
                        'paymentStatus' => $booking->payment_status,
                        'lokasiPenjemputan' => $booking->lokasi_penjemputan,
                        'metodePembayaran' => $booking->metode_pembayaran,
                    ],
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Booking tidak ditemukan',
            ], 404);
        }
    }

    /**
     * Handle Midtrans notification callback (Webhook)
     * Route: POST /api/payment/notification
     * 
     * Midtrans akan mengirim POST request ke endpoint ini setiap kali
     * ada perubahan status transaksi (settlement, pending, cancel, dll)
     */
    public function notification(Request $request)
    {
        try {
            \Log::info('Midtrans Webhook Received', [
                'headers' => $request->headers->all(),
                'body' => $request->all(),
            ]);

            $result = $this->paymentService->handleNotification();

            if ($result['success']) {
                \Log::info('Midtrans Webhook Processed Successfully', [
                    'order_id' => $result['booking']->midtrans_order_id ?? null,
                    'transaction_status' => $result['transaction_status'] ?? null,
                    'booking_status' => $result['booking']->status ?? null,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Notification processed successfully',
                ], 200);
            }

            \Log::warning('Midtrans Webhook Processing Failed', [
                'message' => $result['message'] ?? 'Unknown error',
            ]);

            return response()->json([
                'success' => false,
                'message' => $result['message'] ?? 'Failed to process notification',
            ], 400);
        } catch (\Exception $e) {
            \Log::error('Notification Callback Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error processing notification',
            ], 500);
        }
    }

    /**
     * Check payment status
     */
    public function checkStatus(string $id)
    {
        try {
            $user = Auth::user();
            $booking = Booking::where('user_id', $user->id)
                ->findOrFail($id);

            if (!$booking->midtrans_order_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order ID tidak ditemukan',
                ], 404);
            }

            $status = $this->paymentService->getTransactionStatus($booking->midtrans_order_id);

            if ($status['success']) {
                // Update booking status berdasarkan response
                $transactionStatus = $status['data']->transaction_status ?? null;
                if ($transactionStatus) {
                    if ($transactionStatus == 'settlement' || $transactionStatus == 'capture') {
                        $booking->payment_status = 'paid';
                        $booking->status = 'Dikonfirmasi';
                    } elseif ($transactionStatus == 'pending') {
                        $booking->payment_status = 'pending';
                        $booking->status = 'Menunggu Pembayaran';
                    } elseif (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
                        $booking->payment_status = 'failed';
                        $booking->status = 'Dibatalkan';
                    }
                    $booking->save();
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Status retrieved successfully',
                'data' => [
                    'booking' => [
                        'id' => (string) $booking->id,
                        'status' => $booking->status,
                        'paymentStatus' => $booking->payment_status,
                    ],
                    'transaction' => $status['success'] ? $status['data'] : null,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengecek status pembayaran',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Format image URL ke format /api/asset/ atau /api/storage/
     * 
     * @param string|null $imageUrl
     * @return string
     */
    private function formatImageUrl(?string $imageUrl): string
    {
        // Jika imageUrl kosong, gunakan fallback
        if (empty($imageUrl)) {
            return url('api/asset/Logo.png');
        }

        // Jika sudah full URL dengan /api/asset/ atau /api/storage/, return as is
        if (strpos($imageUrl, '/api/asset/') !== false || strpos($imageUrl, '/api/storage/') !== false) {
            // Jika sudah full URL (dengan http/https), return as is
            if (filter_var($imageUrl, FILTER_VALIDATE_URL)) {
                return $imageUrl;
            }
            // Jika relative path dengan /api/, tambahkan base URL
            return url($imageUrl);
        }

        // Jika sudah full URL (dari asset() atau url()), convert ke format /api/
        if (filter_var($imageUrl, FILTER_VALIDATE_URL)) {
            // Extract path dari full URL
            $parsedUrl = parse_url($imageUrl);
            $path = $parsedUrl['path'] ?? '';
            
            // Convert ke format /api/asset/ atau /api/storage/
            if (strpos($path, '/Asset_Travelo/') !== false) {
                $relativePath = str_replace('/Asset_Travelo/', '', $path);
                return url('api/asset/' . ltrim($relativePath, '/'));
            } elseif (strpos($path, '/storage/') !== false) {
                $relativePath = str_replace('/storage/', '', $path);
                return url('api/storage/' . ltrim($relativePath, '/'));
            }
        }

        // Jika relative path, cek apakah dari Asset_Travelo atau storage
        // Prioritas: cek path yang dimulai dengan destinations/ atau bookings/ terlebih dahulu
        $cleanPath = ltrim($imageUrl, '/');
        if (strpos($cleanPath, 'destinations/') === 0 || strpos($cleanPath, 'bookings/') === 0) {
            // Path seperti "destinations/..." atau "bookings/..." adalah dari storage
            return url('api/storage/' . $cleanPath);
        } elseif (strpos($imageUrl, 'Asset_Travelo/') !== false || strpos($imageUrl, 'Asset_Travelo\\') !== false) {
            // Remove Asset_Travelo/ prefix
            $relativePath = preg_replace('#^.*Asset_Travelo[/\\\\]#', '', $imageUrl);
            return url('api/asset/' . $relativePath);
        } elseif (strpos($imageUrl, 'storage/') !== false || strpos($imageUrl, 'storage\\') !== false) {
            // Remove storage/ prefix
            $relativePath = preg_replace('#^.*storage[/\\\\]#', '', $imageUrl);
            return url('api/storage/' . $relativePath);
        }

        // Default: assume dari Asset_Travelo (untuk backward compatibility)
        return url('api/asset/' . ltrim($imageUrl, '/'));
    }
}