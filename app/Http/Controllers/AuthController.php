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

    public function showAuth()
    {
        if (Auth::check()) {
            $user = Auth::user();
            if (strtolower($user->role) === 'admin') {
                return redirect()->route('admin.dashboard');
            }
            return redirect()->route('dashboard');
        }
        return view('auth.authenticate');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $response = $this->supabase->login($credentials['email'], $credentials['password']);

        if (isset($response['error'])) {
            return back()->withErrors([
                'email' => $response['error'],
            ])->onlyInput('email');
        }

        // Successfully logged into Supabase
        // Now sync with local user
        $supabaseUser = $this->supabase->getUser($response['access_token']);
        
        if (!$supabaseUser) {
            return back()->withErrors(['email' => 'Gagal mengambil data pengguna. Silakan coba login kembali.']);
        }

        $user = User::where('email', $credentials['email'])->first();

        if (!$user) {
            // This should ideally not happen if they are registered, 
            // but just in case we sync it here.
            $user = User::create([
                'id' => $supabaseUser['id'],
                'full_name' => $supabaseUser['user_metadata']['full_name'] ?? $supabaseUser['user_metadata']['name'] ?? 'User',
                'email' => $credentials['email'],
                'password' => Hash::make($credentials['password']),
                'role' => $supabaseUser['user_metadata']['role'] ?? 'Masyarakat',
            ]);
        }

        Auth::login($user, $request->has('remember'));

        $request->session()->regenerate();
        $request->session()->put('supabase_token', $response['access_token']);

        if (strtolower($user->role) === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('dashboard');
    }

    public function showRegister()
    {
        return redirect()->route('login');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:profiles'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $response = $this->supabase->register($data['email'], $data['password'], [
            'full_name' => $data['name'],
            'role' => 'Masyarakat',
        ]);

        if (isset($response['error'])) {
            return back()->withErrors([
                'email' => $response['error'],
            ])->withInput();
        }

        // We don't need to manually create the user locally here anymore 
        // if the Supabase Trigger is already doing it in public.profiles.
        // However, we should check if we need to set a local password if we ever use it.

        return redirect()->route('login')->with('success', 'Registration successful! Please login.');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Update authenticated user profile details
     */
    public function updateProfile(Request $request)
    {
        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
        ]);

        $user = Auth::user();
        $accessToken = session('supabase_token');

        if ($accessToken) {
            // Update in Supabase
            $response = $this->supabase->updateUser($accessToken, [
                'data' => [
                    'full_name' => $data['full_name'],
                ]
            ]);

            if (isset($response['error'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal memperbarui di Supabase: ' . $response['error'],
                ], 400);
            }
        }

        // Update locally
        $user->full_name = $data['full_name'];
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil diperbarui!',
            'full_name' => $user->full_name,
        ]);
    }

    /**
     * Update authenticated user password
     */
    public function updatePassword(Request $request)
    {
        $data = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = Auth::user();

        // Verify current password locally
        if (!Hash::check($data['current_password'], $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Kata sandi lama yang Anda masukkan salah.',
            ], 400);
        }

        $accessToken = session('supabase_token');

        if ($accessToken) {
            // Update password in Supabase
            $response = $this->supabase->updateUser($accessToken, [
                'password' => $data['password'],
            ]);

            if (isset($response['error'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal memperbarui kata sandi di Supabase: ' . $response['error'],
                ], 400);
            }
        }

        // Update locally
        $user->password = Hash::make($data['password']);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Kata sandi berhasil diperbarui!',
        ]);
    }
}
