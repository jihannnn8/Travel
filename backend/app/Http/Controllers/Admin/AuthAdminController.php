<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

/**
 * Controller untuk mengelola autentikasi admin (login, logout)
 * Hanya user dengan role 'admin' yang dapat login
 */
class AuthAdminController extends Controller
{
    /**
     * Menampilkan halaman login admin
     */
    public function login()
    {
        // Jika sudah login sebagai admin, redirect ke dashboard
        if (Auth::check() && Auth::user()->role === 'admin') {
            return redirect()->route('dashboard');
        }
        
        return view('admin.auth.login');
    }

    /**
     * Proses login admin
     * Hanya user dengan role 'admin' yang dapat login
     */
    public function loginAction(Request $request)
    {
        // Validasi input dari form login
        Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ])->validate();

        // Cek apakah user dengan email tersebut ada dan role-nya admin
        $user = User::where('email', $request->email)->first();

        // Jika user tidak ada atau bukan admin, tampilkan error
        if (!$user || $user->role !== 'admin') {
            throw ValidationException::withMessages([
                'email' => 'These credentials do not match our records or you do not have admin access.'
            ]);
        }

        // Coba login dengan email dan password
        if (!Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => trans('auth.failed')
            ]);
        }

        // Pastikan user yang login adalah admin
        $authenticatedUser = Auth::user();
        if ($authenticatedUser->role !== 'admin') {
            // Jika bukan admin, logout dan redirect kembali
            Auth::logout();
            $request->session()->invalidate();
            
            throw ValidationException::withMessages([
                'email' => 'You do not have admin access.'
            ]);
        }

        // Regenerate session untuk keamanan
        $request->session()->regenerate();

        // Redirect ke dashboard
        return redirect()->route('dashboard');
    }

    /**
     * Proses logout admin
     */
    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
