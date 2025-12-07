<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Register a new user
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        try {
            // Validate request
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6',
                'phone_number' => 'nullable|string|max:20',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors(),
                ], 422);
            }

            // Create user (role otomatis 'user' untuk mobile app)
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'password' => Hash::make($request->password),
                'role' => 'user', // Pastikan role selalu 'user' untuk register dari mobile
            ]);

            // Create Sanctum token for API authentication
            $token = $user->createToken('mobile-app')->plainTextToken;

            // Format user data for mobile app (camelCase)
            $userData = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phoneNumber' => $user->phone_number ?? '',
            ];

            return response()->json([
                'success' => true,
                'message' => 'Registration successful',
                'data' => [
                    'user' => $userData,
                    'token' => $token,
                ],
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Registration failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Login user
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        try {
            // Validate request
            $validator = Validator::make($request->all(), [
                'name' => 'nullable|string',
                'email' => 'nullable|string|email',
                'password' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors(),
                ], 422);
            }

            // Check if user provided name or email
            $credentials = [];
            if ($request->filled('email')) {
                $credentials['email'] = $request->email;
            } elseif ($request->filled('name')) {
                // Try to find user by name
                $user = User::where('name', $request->name)->first();
                if ($user) {
                    $credentials['email'] = $user->email;
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid credentials',
                    ], 401);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Please provide either name or email',
                    'errors' => ['name' => ['Name or email is required']],
                ], 422);
            }

            // Find user by email
            $user = User::where('email', $credentials['email'])->first();

            // Check password
            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid credentials',
                ], 401);
            }

            // Create Sanctum token for API authentication
            $token = $user->createToken('mobile-app')->plainTextToken;

            // Format user data for mobile app (camelCase)
            $userData = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phoneNumber' => $user->phone_number ?? '',
            ];

            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'data' => [
                    'user' => $userData,
                    'token' => $token,
                ],
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Login failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Logout user
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        try {
            // Revoke current access token
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Logout successful',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Logout failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get authenticated user
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function me(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated',
                ], 401);
            }

            // Format user data for mobile app (camelCase)
            $userData = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phoneNumber' => $user->phone_number ?? '',
            ];

            return response()->json([
                'success' => true,
                'data' => [
                    'user' => $userData,
                ],
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get user data',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get user profile
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProfile(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated',
                ], 401);
            }

            // Format user data for mobile app (camelCase)
            $userData = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phoneNumber' => $user->phone_number ?? '',
            ];

            return response()->json([
                'success' => true,
                'message' => 'Profile retrieved successfully',
                'data' => [
                    'user' => $userData,
                ],
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get profile',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update user profile
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateProfile(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated',
                ], 401);
            }

            // Handle both camelCase and snake_case for phone number
            $requestData = $request->all();
            if (isset($requestData['phoneNumber'])) {
                $requestData['phone_number'] = $requestData['phoneNumber'];
                unset($requestData['phoneNumber']);
            }

            // Validate request
            $validator = Validator::make($requestData, [
                'name' => 'sometimes|string|max:255',
                'email' => 'sometimes|string|email|max:255|unique:users,email,' . $user->id,
                'phone_number' => 'nullable|string|max:20',
                'password' => 'nullable|string|min:6',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors(),
                ], 422);
            }

            // Update user data
            if (isset($requestData['name']) && $requestData['name'] !== null) {
                $user->name = $requestData['name'];
            }

            if (isset($requestData['email']) && $requestData['email'] !== null) {
                $user->email = $requestData['email'];
            }

            if (isset($requestData['phone_number'])) {
                $user->phone_number = $requestData['phone_number'];
            }

            // Update password if provided
            if (isset($requestData['password']) && !empty($requestData['password'])) {
                $user->password = Hash::make($requestData['password']);
            }

            $user->save();

            // Format user data for mobile app (camelCase)
            $userData = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phoneNumber' => $user->phone_number ?? '',
            ];

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully',
                'data' => [
                    'user' => $userData,
                ],
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update profile',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}

