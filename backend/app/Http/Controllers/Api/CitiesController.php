<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\City;
use Illuminate\Http\Request;

class CitiesController extends Controller
{
    /**
     * Get all cities
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $cities = City::active()->ordered()->get();

            // Format data untuk mobile app (camelCase)
            $formattedCities = $cities->map(function ($city) {
                return $this->formatCity($city);
            });

            return response()->json([
                'success' => true,
                'message' => 'Cities retrieved successfully',
                'data' => [
                    'cities' => $formattedCities,
                ],
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve cities',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get single city by ID
     * 
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(string $id)
    {
        try {
            $city = City::find($id);

            if (!$city) {
                return response()->json([
                    'success' => false,
                    'message' => 'City not found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'City retrieved successfully',
                'data' => [
                    'city' => $this->formatCity($city),
                ],
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve city',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Format city data untuk mobile app (camelCase)
     * 
     * @param City $city
     * @return array
     */
    private function formatCity(City $city): array
    {
        return [
            'id' => (string) $city->id,
            'name' => $city->name,
            'imageUrl' => $this->formatImageUrl($city->image_url),
            'description' => $city->description ?? '',
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

        // Default: assume dari Asset_Travelo/images/ (untuk backward compatibility)
        // Handle case seperti 'images/city.jpg' atau '/images/city.jpg'
        $cleanPath = ltrim($imageUrl, '/');
        if (strpos($cleanPath, 'images/') === 0) {
            return url('api/asset/' . $cleanPath);
        }
        
        return url('api/asset/images/' . $cleanPath);
    }
}

