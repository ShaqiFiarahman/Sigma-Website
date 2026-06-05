<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ImageUploadService
{
    protected string $supabaseUrl;
    protected string $supabaseKey;

    public function __construct()
    {
        $this->supabaseUrl = rtrim(config('services.supabase.url'), '/');
        $this->supabaseKey = config('services.supabase.service_key') ?? config('services.supabase.key') ?? '';
    }

    /**
     * Upload a file to Supabase Storage with local fallback.
     * Compresses the image before uploading.
     *
     * @param UploadedFile $file
     * @param string $bucket  Supabase bucket name
     * @param string $disk    Local storage disk for fallback
     * @param int $quality    Compression quality (1-100)
     * @return string         Public URL of the uploaded file
     */
    public function upload(UploadedFile $file, string $bucket = 'laporan', string $disk = 'public', int $quality = 60): string
    {
        // Store locally first for compression
        $path = $file->store($bucket, $disk);
        $absolutePath = storage_path('app/public/' . $path);

        // Compress the image
        $this->compressImage($absolutePath, $quality);

        // Attempt Supabase upload
        if ($this->supabaseUrl && $this->supabaseKey) {
            $url = $this->uploadToSupabase($absolutePath, $bucket);
            if ($url) {
                @unlink($absolutePath);
                return $url;
            }
        }

        // Fallback to local storage URL
        return Storage::url($path);
    }

    /**
     * Upload a file to Supabase Storage bucket.
     *
     * @param string $absolutePath  Full path to the local file
     * @param string $bucket        Supabase bucket name
     * @return string|null          Public URL on success, null on failure
     */
    private function uploadToSupabase(string $absolutePath, string $bucket): ?string
    {
        $filename = basename($absolutePath);
        $mimeType = mime_content_type($absolutePath);
        $fileContent = file_get_contents($absolutePath);

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->supabaseKey,
                'Content-Type' => $mimeType,
            ])->withBody($fileContent, $mimeType)
              ->post("{$this->supabaseUrl}/storage/v1/object/{$bucket}/{$filename}");

            if ($response->successful()) {
                return "{$this->supabaseUrl}/storage/v1/object/public/{$bucket}/{$filename}";
            }

            Log::warning("Supabase upload failed for {$filename}: " . $response->body());
        } catch (\Exception $e) {
            Log::warning("Supabase upload exception for {$filename}: " . $e->getMessage());
        }

        return null;
    }

    /**
     * Compress an image file in place.
     *
     * @param string $filePath  Absolute path to the image
     * @param int $quality      Compression quality (1-100)
     * @return bool
     */
    private function compressImage(string $filePath, int $quality = 60): bool
    {
        $info = @getimagesize($filePath);
        if (!$info) {
            return false;
        }

        $image = match ($info['mime']) {
            'image/jpeg' => imagecreatefromjpeg($filePath),
            'image/png'  => imagecreatefrompng($filePath),
            'image/webp' => imagecreatefromwebp($filePath),
            default      => null,
        };

        if (!$image) {
            return false;
        }

        $result = match ($info['mime']) {
            'image/jpeg' => imagejpeg($image, $filePath, $quality),
            'image/png'  => imagepng($image, $filePath, 6),
            'image/webp' => imagewebp($image, $filePath, $quality),
            default      => false,
        };

        imagedestroy($image);
        return $result;
    }
}
