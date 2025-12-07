<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Promo;
use Illuminate\Http\Request;

class PromosController extends Controller
{
    /**
     * Get all promos
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $promos = Promo::active()->ordered()->get();

            // Format data untuk mobile app - hanya return array of image URLs
            // Flutter app mengharapkan array langsung di dalam 'promos'
            $promoImages = $promos->map(function ($promo) {
                return $this->formatImageUrl($promo->image_url);
            })->filter()->values()->toArray();

            return response()->json([
                'success' => true,
                'message' => 'Promos retrieved successfully',
                'data' => [
                    'promos' => $promoImages,
                ],
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve promos',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get single promo by ID
     * 
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(string $id)
    {
        try {
            $promo = Promo::find($id);

            if (!$promo) {
                return response()->json([
                    'success' => false,
                    'message' => 'Promo not found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Promo retrieved successfully',
                'data' => [
                    'promo' => $this->formatPromo($promo),
                ],
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve promo',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Format promo data untuk mobile app (camelCase)
     * 
     * @param Promo $promo
     * @return array
     */
    private function formatPromo(Promo $promo): array
    {
        return [
            'id' => (string) $promo->id,
            'imageUrl' => $this->formatImageUrl($promo->image_url),
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
        // Jika imageUrl kosong, return empty string
        if (empty($imageUrl)) {
            return '';
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
        if (strpos($imageUrl, 'Asset_Travelo/') !== false || strpos($imageUrl, 'Asset_Travelo\\') !== false) {
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

