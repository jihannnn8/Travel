<?php

namespace App\Services;

use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Transaction;
use Midtrans\Notification;
use Exception;

class PaymentService
{
    public function __construct()
    {
        // Setup Midtrans Config
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');
    }

    /**
     * Create Snap Token untuk payment
     * 
     * @param array $params
     * @return string|null
     */
    public function createSnapToken(array $params): ?string
    {
        try {
            // Set timeout untuk menghindari SSL timeout
            $snapToken = Snap::getSnapToken($params);
            return $snapToken;
        } catch (Exception $e) {
            \Log::error('Midtrans Snap Token Error: ' . $e->getMessage(), [
                'error_type' => get_class($e),
                'trace' => $e->getTraceAsString(),
            ]);
            
            // Jika error SSL, coba lagi dengan konfigurasi yang lebih longgar
            if (strpos($e->getMessage(), 'SSL') !== false || strpos($e->getMessage(), 'CURL') !== false) {
                \Log::warning('Midtrans SSL Error detected, check server SSL configuration');
            }
            
            return null;
        }
    }

    /**
     * Create payment transaction
     * 
     * @param array $transactionDetails
     * @param array $customerDetails
     * @param array $itemDetails
     * @return array
     */
    public function createTransaction(
        array $transactionDetails,
        array $customerDetails,
        array $itemDetails
    ): array {
        try {
            $params = [
                'transaction_details' => $transactionDetails,
                'customer_details' => $customerDetails,
                'item_details' => $itemDetails,
            ];

            $snapToken = $this->createSnapToken($params);

            if (!$snapToken) {
                return [
                    'success' => false,
                    'message' => 'Gagal membuat transaksi pembayaran',
                ];
            }

            return [
                'success' => true,
                'snap_token' => $snapToken,
                'message' => 'Transaksi berhasil dibuat',
            ];
        } catch (Exception $e) {
            \Log::error('Midtrans Create Transaction Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat membuat transaksi',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Handle notification dari Midtrans
     * 
     * @return array
     */
    public function handleNotification(): array
    {
        try {
            // Get raw request body untuk validasi
            $rawBody = file_get_contents('php://input');
            $notificationData = json_decode($rawBody, true);

            // Validasi: hanya proses payment notification yang memiliki order_id dan transaction_status
            if (!isset($notificationData['order_id']) || !isset($notificationData['transaction_status'])) {
                \Log::info('Midtrans Notification Skipped - Not a payment notification', [
                    'notification_type' => $notificationData['status_message'] ?? 'unknown',
                ]);
                return [
                    'success' => true,
                    'message' => 'Notification skipped - not a payment notification',
                ];
            }

            $notification = new Notification();

            $transaction = $notification->transaction_status;
            $type = $notification->payment_type;
            $orderId = $notification->order_id;
            $fraud = $notification->fraud_status;

            \Log::info('Midtrans Payment Notification Received', [
                'order_id' => $orderId,
                'transaction_status' => $transaction,
                'payment_type' => $type,
                'fraud_status' => $fraud,
            ]);

            // Cari booking berdasarkan order_id
            $booking = \App\Models\Booking::where('midtrans_order_id', $orderId)->first();

            if (!$booking) {
                \Log::warning('Booking not found for Midtrans notification', [
                    'order_id' => $orderId,
                ]);
                return [
                    'success' => false,
                    'message' => 'Booking tidak ditemukan',
                ];
            }

            // Handle transaction status
            // Mapping status sesuai dengan yang digunakan di mobile app:
            // - Menunggu Pembayaran (pending)
            // - Dikonfirmasi (settlement/capture success)
            // - Selesai (completed)
            // - Dibatalkan (cancelled/deny/expire)
            
            if ($transaction == 'capture') {
                // For credit card transaction, we need to check whether transaction is challenge by FDS or not
                if ($type == 'credit_card') {
                    if ($fraud == 'challenge') {
                        // Set status to pending
                        $booking->payment_status = 'pending';
                        $booking->status = 'Menunggu Pembayaran';
                    } else {
                        // Set status to success - Dikonfirmasi
                        $booking->payment_status = 'paid';
                        $booking->status = 'Dikonfirmasi';
                    }
                } else {
                    // Other payment types - Dikonfirmasi
                    $booking->payment_status = 'paid';
                    $booking->status = 'Dikonfirmasi';
                }
            } elseif ($transaction == 'settlement') {
                // Payment success - Dikonfirmasi
                $booking->payment_status = 'paid';
                $booking->status = 'Dikonfirmasi';
            } elseif ($transaction == 'pending') {
                // Payment pending - Menunggu Pembayaran
                $booking->payment_status = 'pending';
                $booking->status = 'Menunggu Pembayaran';
            } elseif ($transaction == 'deny') {
                // Payment denied - Dibatalkan
                $booking->payment_status = 'failed';
                $booking->status = 'Dibatalkan';
            } elseif ($transaction == 'expire') {
                // Payment expired - Dibatalkan
                $booking->payment_status = 'expired';
                $booking->status = 'Dibatalkan';
            } elseif ($transaction == 'cancel') {
                // Payment cancelled - Dibatalkan
                $booking->payment_status = 'cancelled';
                $booking->status = 'Dibatalkan';
            }

            // Simpan response dari Midtrans
            // Convert notification object ke array untuk disimpan
            $notificationArray = [
                'transaction_status' => $transaction,
                'payment_type' => $type,
                'order_id' => $orderId,
                'fraud_status' => $fraud,
                'gross_amount' => $notification->gross_amount ?? null,
                'transaction_time' => $notification->transaction_time ?? null,
                'status_code' => $notification->status_code ?? null,
                'status_message' => $notification->status_message ?? null,
            ];
            $booking->midtrans_response = $notificationArray;
            $booking->save();

            \Log::info('Booking status updated via Midtrans webhook', [
                'booking_id' => $booking->id,
                'order_id' => $orderId,
                'old_status' => $booking->getOriginal('status'),
                'new_status' => $booking->status,
                'payment_status' => $booking->payment_status,
                'transaction_status' => $transaction,
            ]);

            return [
                'success' => true,
                'booking' => $booking,
                'transaction_status' => $transaction,
            ];
        } catch (Exception $e) {
            \Log::error('Midtrans Notification Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat memproses notifikasi',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get transaction status
     * 
     * @param string $orderId
     * @return array
     */
    public function getTransactionStatus(string $orderId): array
    {
        try {
            $status = Transaction::status($orderId);
            return [
                'success' => true,
                'data' => $status,
            ];
        } catch (Exception $e) {
            \Log::error('Midtrans Get Status Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Gagal mendapatkan status transaksi',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Cancel transaction
     * 
     * @param string $orderId
     * @return array
     */
    public function cancelTransaction(string $orderId): array
    {
        try {
            $cancel = Transaction::cancel($orderId);
            return [
                'success' => true,
                'data' => $cancel,
            ];
        } catch (Exception $e) {
            \Log::error('Midtrans Cancel Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Gagal membatalkan transaksi',
                'error' => $e->getMessage(),
            ];
        }
    }
}

