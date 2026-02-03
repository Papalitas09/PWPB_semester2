<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // 1. Tampilkan Form Login
    public function showLoginForm()
    {
        return view('Auth.login');
    }

    // 2. Proses Data Login (Si Satpam)
    public function login(Request $request)
    {
        // Validasi input dulu
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);
        // Cek ke Database (Satpam Bekerja)
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate(); // Buat
            // Cek Role: Jika Admin ke Dashboard, Jika Warga ke Laporan
            if (Auth::user()->role == 'admin') {
                return redirect()->route('admin.dashboard');
            } else {
                return redirect()->route('user.lapor'); // <-- Revisi sedikit biar lgsg kemenu lapor
            }
        }

        // Jika Gagal Login
        return back()->withErrors([
            'email' => 'Email atau Password salah!',
        ]);
    }

    // 3. Proses Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    public function dashboard()
    {
        // LOGIKA: Ambil SEMUA laporan, urutkan dari yang terbaru
        $reports = Report::orderBy('created_at', 'desc')->get();

        // Kirim data '$reports' ke View
        return view('Admin.dashboard', compact('reports'));
    }
}
