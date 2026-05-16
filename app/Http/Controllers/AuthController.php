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
            if (in_array(strtolower($user->role), ['admin', 'bnpb'])) {
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
            return back()->withErrors(['email' => 'Failed to retrieve user data from Supabase.']);
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

        if (in_array(strtolower($user->role), ['admin', 'bnpb'])) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->intended(route('dashboard'));
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
}
