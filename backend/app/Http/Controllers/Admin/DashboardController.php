<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Booking;
use App\Models\Destination;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * Controller untuk halaman dashboard admin
 */
class DashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard admin
     */
    public function index()
    {
        // Statistik umum
        $totalUsers = User::where('role', 'user')->count();
        $totalAdmins = User::where('role', 'admin')->count();
        $totalBookings = Booking::count();
        $totalDestinations = Destination::count();
        
        // Statistik booking berdasarkan status
        $pendingBookings = Booking::where('status', 'pending')->count();
        $confirmedBookings = Booking::where('status', 'confirmed')->count();
        $completedBookings = Booking::where('status', 'completed')->count();
        $cancelledBookings = Booking::where('status', 'cancelled')->count();
        
        // Total revenue (dari booking yang sudah confirmed/completed)
        $totalRevenue = Booking::whereIn('status', ['confirmed', 'completed'])
            ->sum('total_harga');
        
        // Revenue bulan ini
        $revenueThisMonth = Booking::whereIn('status', ['confirmed', 'completed'])
            ->whereMonth('tanggal_booking', now()->month)
            ->whereYear('tanggal_booking', now()->year)
            ->sum('total_harga');
        
        // Booking bulan ini
        $bookingsThisMonth = Booking::whereMonth('tanggal_booking', now()->month)
            ->whereYear('tanggal_booking', now()->year)
            ->count();
        
        // Recent bookings (5 terbaru)
        $recentBookings = Booking::with(['user', 'destination'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // Booking per bulan (untuk chart - 6 bulan terakhir)
        $bookingsPerMonth = Booking::select(
                DB::raw('MONTH(tanggal_booking) as month'),
                DB::raw('YEAR(tanggal_booking) as year'),
                DB::raw('COUNT(*) as total')
            )
            ->where('tanggal_booking', '>=', now()->subMonths(5)->startOfMonth())
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalAdmins',
            'totalBookings',
            'totalDestinations',
            'pendingBookings',
            'confirmedBookings',
            'completedBookings',
            'cancelledBookings',
            'totalRevenue',
            'revenueThisMonth',
            'bookingsThisMonth',
            'recentBookings',
            'bookingsPerMonth'
        ));
    }

    /**
     * Menampilkan halaman profile admin
     */
    public function profile(){
        $user = auth()->user();
        return view('admin.profile', compact('user'));
    }

    /**
     * Update profile admin
     */
    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        // Validasi input
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'phone_number' => 'nullable|string|max:20',
            'current_password' => 'nullable|required_with:new_password',
            'new_password' => 'nullable|string|min:8|confirmed',
        ], [
            'name.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'current_password.required_with' => 'Password lama wajib diisi jika ingin mengubah password.',
            'new_password.min' => 'Password baru minimal 8 karakter.',
            'new_password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Cek password lama jika ingin mengubah password
        if ($request->filled('new_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return redirect()->back()
                    ->withErrors(['current_password' => 'Password lama tidak sesuai.'])
                    ->withInput();
            }
        }

        // Update data user
        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
        ];

        // Update password jika diisi
        if ($request->filled('new_password')) {
            $updateData['password'] = Hash::make($request->new_password);
        }

        $user->update($updateData);

        return redirect()->route('profile')
            ->with('success', 'Profile berhasil diperbarui.');
    }
}

