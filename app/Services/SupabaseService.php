<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SupabaseService
{
    protected $url;
    protected $key;

    // Autentikasi & Manajemen Pengguna Supabase
    public function __construct()
    {
        $this->url = rtrim(config('services.supabase.url'), '/');
        $this->key = config('services.supabase.key');
    }

    public function register($email, $password, $metadata = [])
    {
        // Kirim POST request ke endpoint signup Supabase untuk mendaftarkan user baru
        $response = Http::withHeaders([
            'apikey' => $this->key,
            'Content-Type' => 'application/json',
        ])->post("{$this->url}/auth/v1/signup", [
            'email' => $email,
            'password' => $password,
            'data' => $metadata,
        ]);

        // Jika pendaftaran berhasil, kembalikan response data user dari Supabase
        if ($response->successful()) {
            return $response->json();
        }

        Log::error('Supabase Register Error: ' . $response->body());
        return [
            'error' => $response->json('msg') ?? 'Registration failed',
        ];
    }

    public function login($email, $password)
    {
        // Kirim POST request ke endpoint token Supabase untuk autentikasi user
        $response = Http::withHeaders([
            'apikey' => $this->key,
            'Content-Type' => 'application/json',
        ])->post("{$this->url}/auth/v1/token?grant_type=password", [
            'email' => $email,
            'password' => $password,
        ]);

        // Jika login berhasil, kembalikan data token akses user
        if ($response->successful()) {
            return $response->json();
        }

        Log::error('Supabase Login Error: ' . $response->body());

        $rawError = strtolower(
            $response->json('error_description') ?? $response->json('msg') ?? $response->json('error') ?? ''
        );

        $message = $this->translateLoginError($rawError);

        return ['error' => $message];
    }

    // Metode Pembantu
    protected function translateLoginError(string $raw): string
    {
        // Saring pesan error dari Supabase untuk diterjemahkan ke bahasa Indonesia yang ramah
        if (str_contains($raw, 'invalid login credentials') || str_contains($raw, 'invalid password')) {
            return 'Email atau kata sandi yang Anda masukkan salah. Silakan periksa kembali.';
        }

        if (str_contains($raw, 'email not confirmed')) {
            return 'Akun Anda belum diverifikasi. Silakan cek email untuk mengkonfirmasi akun.';
        }

        if (str_contains($raw, 'user not found') || str_contains($raw, 'no user found')) {
            return 'Akun dengan email tersebut tidak ditemukan. Pastikan email sudah terdaftar.';
        }

        if (str_contains($raw, 'too many requests') || str_contains($raw, 'rate limit')) {
            return 'Terlalu banyak percobaan login. Silakan tunggu beberapa saat sebelum mencoba lagi.';
        }

        if (str_contains($raw, 'account disabled') || str_contains($raw, 'user is disabled') || str_contains($raw, 'banned')) {
            return 'Akun Anda telah dinonaktifkan. Silakan hubungi administrator.';
        }

        if (str_contains($raw, 'network') || str_contains($raw, 'timeout') || str_contains($raw, 'connection')) {
            return 'Gagal terhubung ke server. Periksa koneksi internet Anda dan coba lagi.';
        }

        return 'Login gagal. Silakan coba lagi atau hubungi administrator jika masalah berlanjut.';
    }

    public function getUser($accessToken)
    {
        // Kirim GET request ke endpoint user Supabase untuk mengambil info profil user aktif
        $response = Http::withHeaders([
            'apikey' => $this->key,
            'Authorization' => "Bearer {$accessToken}",
        ])->get("{$this->url}/auth/v1/user");

        // Jika pengambilan berhasil, kembalikan data info user
        if ($response->successful()) {
            return $response->json();
        }

        return null;
    }

    public function updateUser($accessToken, $data)
    {
        // Kirim PUT request ke endpoint user Supabase untuk memperbarui detail user
        $response = Http::withHeaders([
            'apikey' => $this->key,
            'Authorization' => "Bearer {$accessToken}",
            'Content-Type' => 'application/json',
        ])->put("{$this->url}/auth/v1/user", $data);

        // Jika pembaruan berhasil, kembalikan response data user terbaru
        if ($response->successful()) {
            return $response->json();
        }

        Log::error('Supabase Update User Error: ' . $response->body());
        return [
            'error' => $response->json('msg') ?? 'Update failed',
        ];
    }
}
