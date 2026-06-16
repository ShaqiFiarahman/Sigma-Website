<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\SupabaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    protected $supabase;

    public function __construct(SupabaseService $supabase)
    {
        $this->supabase = $supabase;
    }

    // Tampilan Autentikasi
    public function showAuth()
    {
        // Kalau user sudah login, langsung alihkan ke dashboard biar tidak perlu login lagi
        if (Auth::check()) {
            $user = Auth::user();
            // Cek role user untuk menentukan halaman dashboard tujuan
            if (strtolower($user->role) === 'admin') {
                return redirect()->route('admin.dashboard');
            }
            return redirect()->route('dashboard');
        }
        return view('auth.authenticate');
    }

    // Proses Login
    public function login(Request $request)
    {
        // Validasi input email dan password dari form login
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $response = $this->supabase->login($credentials['email'], $credentials['password']);

        // Kalau proses login di Supabase error, kembalikan ke halaman login dengan pesan error
        if (isset($response['error'])) {
            return back()->withErrors([
                'email' => $response['error'],
            ])->onlyInput('email');
        }

        $supabaseUser = $this->supabase->getUser($response['access_token']);
        
        // Gagalkan proses login kalau data profil user tidak bisa ditarik dari Supabase
        if (!$supabaseUser) {
            return back()->withErrors(['email' => 'Gagal mengambil data pengguna. Silakan coba login kembali.']);
        }

        // Cari data user lokal berdasarkan email untuk sinkronisasi database
        $user = User::where('email', $credentials['email'])->first();

        // Kalau data user lokal belum ada, buat user baru untuk sinkronisasi
        if (!$user) {
            $user = User::create([
                'id' => $supabaseUser['id'],
                'full_name' => $supabaseUser['user_metadata']['full_name'] ?? $supabaseUser['user_metadata']['name'] ?? 'User',
                'email' => $credentials['email'],
                'password' => Hash::make($credentials['password']),
                'role' => $supabaseUser['user_metadata']['role'] ?? 'Masyarakat',
            ]);
        }

        // Login user ke sistem auth lokal Laravel
        Auth::login($user, $request->has('remember'));

        $request->session()->regenerate();
        $request->session()->put('supabase_token', $response['access_token']);

        // Arahkan ke dashboard admin kalau rolenya memang admin
        if (strtolower($user->role) === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('dashboard');
    }

    // Proses Registrasi
    public function showRegister()
    {
        return redirect()->route('login');
    }

    public function register(Request $request)
    {
        // Validasi data input form registrasi sebelum dikirim ke Supabase
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:profiles'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $response = $this->supabase->register($data['email'], $data['password'], [
            'full_name' => $data['name'],
            'role' => 'Masyarakat',
        ]);

        // Kalau proses registrasi di Supabase gagal, kembalikan dengan pesan error
        if (isset($response['error'])) {
            return back()->withErrors([
                'email' => $response['error'],
            ])->withInput();
        }

        return redirect()->route('login')->with('success', 'Registration successful! Please login.');
    }

    // Proses Keluar
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    // Pembaruan Profil
    public function updateProfile(Request $request)
    {
        // Validasi agar nama lengkap baru wajib diisi dan berupa string
        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
        ]);

        $user = Auth::user();
        $accessToken = session('supabase_token');

        // Kalau token akses Supabase tersedia, perbarui nama lengkap user di Supabase
        if ($accessToken) {
            $response = $this->supabase->updateUser($accessToken, [
                'data' => [
                    'full_name' => $data['full_name'],
                ]
            ]);

            // Kirim response error kalau proses pembaruan di Supabase gagal
            if (isset($response['error'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal memperbarui di Supabase: ' . $response['error'],
                ], 400);
            }
        }

        $user->full_name = $data['full_name'];
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil diperbarui!',
            'full_name' => $user->full_name,
        ]);
    }

    public function updatePassword(Request $request)
    {
        // Validasi input password lama dan konfirmasi password baru
        $data = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = Auth::user();

        // Verifikasi password saat ini secara lokal untuk memastikan keaslian user
        if (!Hash::check($data['current_password'], $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Kata sandi lama yang Anda masukkan salah.',
            ], 400);
        }

        $accessToken = session('supabase_token');

        // Kalau token akses Supabase tersedia, perbarui kata sandi di Supabase
        if ($accessToken) {
            $response = $this->supabase->updateUser($accessToken, [
                'password' => $data['password'],
            ]);

            // Kirim response error jika penggantian password di Supabase gagal
            if (isset($response['error'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal memperbarui kata sandi di Supabase: ' . $response['error'],
                ], 400);
            }
        }

        $user->password = Hash::make($data['password']);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Kata sandi berhasil diperbarui!',
        ]);
    }
}
