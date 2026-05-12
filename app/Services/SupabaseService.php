<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SupabaseService
{
    protected $url;
    protected $key;

    public function __construct()
    {
        $this->url = rtrim(config('services.supabase.url'), '/');
        $this->key = config('services.supabase.key');
    }

    /**
     * Register a new user with Supabase Auth
     */
    public function register($email, $password, $metadata = [])
    {
        $response = Http::withHeaders([
            'apikey' => $this->key,
            'Content-Type' => 'application/json',
        ])->post("{$this->url}/auth/v1/signup", [
            'email' => $email,
            'password' => $password,
            'data' => $metadata,
        ]);

        if ($response->successful()) {
            return $response->json();
        }

        Log::error('Supabase Register Error: ' . $response->body());
        return [
            'error' => $response->json('msg') ?? 'Registration failed',
        ];
    }

    /**
     * Login user with Supabase Auth
     */
    public function login($email, $password)
    {
        $response = Http::withHeaders([
            'apikey' => $this->key,
            'Content-Type' => 'application/json',
        ])->post("{$this->url}/auth/v1/token?grant_type=password", [
            'email' => $email,
            'password' => $password,
        ]);

        if ($response->successful()) {
            return $response->json();
        }

        Log::error('Supabase Login Error: ' . $response->body());
        return [
            'error' => $response->json('error_description') ?? $response->json('msg') ?? 'Login failed',
        ];
    }

    /**
     * Get current user details from Supabase using access token
     */
    public function getUser($accessToken)
    {
        $response = Http::withHeaders([
            'apikey' => $this->key,
            'Authorization' => "Bearer {$accessToken}",
        ])->get("{$this->url}/auth/v1/user");

        if ($response->successful()) {
            return $response->json();
        }

        return null;
    }
}
