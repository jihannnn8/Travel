<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Destination;
use Illuminate\Http\Request;

class DestinationController extends Controller
{
    /**
     * Get all destinations (tour packages)
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $destinations = Destination::orderBy('created_at', 'desc')->get();

            // Format data untuk mobile app (camelCase)
            $formattedDestinations = $destinations->map(function ($destination) {
                return $this->formatDestination($destination);
            });

            return response()->json([
                'success' => true,
                'message' => 'Destinations retrieved successfully',
                'data' => [
                    'destinations' => $formattedDestinations,
                ],
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve destinations',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get single destination by ID
     * 
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(string $id)
    {
        try {
            $destination = Destination::find($id);

            if (!$destination) {
                return response()->json([
                    'success' => false,
                    'message' => 'Destination not found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Destination retrieved successfully',
                'data' => [
                    'destination' => $this->formatDestination($destination),
                ],
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve destination',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Format destination data untuk mobile app (camelCase)
     * 
     * @param Destination $destination
     * @return array
     */
    private function formatDestination(Destination $destination): array
    {
        // Get image URL dan convert ke format /api/asset/ atau /api/storage/
        $imageUrl = $this->formatImageUrl($destination->image_url);

        return [
            'id' => (string) $destination->id,
            'title' => $destination->title,
            'description' => $destination->description ?? '',
            'imageUrl' => $imageUrl,
            'price' => (float) $destination->price,
            'duration' => $destination->duration,
            'departureDate' => $destination->departure_date 
                ? $destination->departure_date->format('d F Y') 
                : '',
            'rating' => (float) $destination->rating,
            'totalRatings' => (int) $destination->total_ratings,
            'rundown' => $destination->rundown ?? [],
            'destination' => $destination->destination,
        ];
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
        // karena ini adalah path dari storage upload
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