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

    // Konstruktor Layanan Unggah
    public function __construct()
    {
        $this->supabaseUrl = rtrim(config('services.supabase.url'), '/');
        $this->supabaseKey = config('services.supabase.service_key') ?? config('services.supabase.key') ?? '';
    }

    // Unggah Berkas Gambar
    public function upload(UploadedFile $file, string $bucket = 'laporan', string $disk = 'public', int $quality = 60): string
    {
        // Gunakan folder temporary sistem yang dapat ditulisi (seperti /tmp di Vercel/Lambda)
        $tempDir = sys_get_temp_dir();
        $filename = uniqid('upload_', true) . '.' . $file->getClientOriginalExtension();
        
        // Pindahkan berkas ke folder temporary
        $file->move($tempDir, $filename);
        $absolutePath = $tempDir . DIRECTORY_SEPARATOR . $filename;

        // Kompres ukuran gambar di folder temporary
        $this->compressImage($absolutePath, $quality);

        // Coba unggah ke Supabase
        // Kirim gambar ke storage Supabase jika konfigurasi API key dan URL sudah siap
        if ($this->supabaseUrl && $this->supabaseKey) {
            $url = $this->uploadToSupabase($absolutePath, $bucket);
            // Jika upload ke Supabase berhasil, hapus berkas lokal dan gunakan URL online
            if ($url) {
                @unlink($absolutePath);
                return $url;
            }
        }

        // Gunakan penyimpanan lokal sebagai cadangan jika Supabase gagal atau tidak dikonfigurasi
        try {
            $path = Storage::disk($disk)->putFileAs($bucket, new \Illuminate\Http\File($absolutePath), $filename);
            @unlink($absolutePath);
            return Storage::url($path);
        } catch (\Exception $e) {
            @unlink($absolutePath);
            Log::error("Gagal menyimpan file ke penyimpanan lokal cadangan: " . $e->getMessage());
            throw $e;
        }
    }

    // Integrasi API Supabase
    // Hubungkan dan Unggah ke Supabase
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

            // Jika upload ke Supabase Storage berhasil, kembalikan URL publik gambar
            if ($response->successful()) {
                return "{$this->supabaseUrl}/storage/v1/object/public/{$bucket}/{$filename}";
            }

            Log::warning("Supabase upload failed for {$filename}: " . $response->body());
        } catch (\Exception $e) {
            Log::warning("Supabase upload exception for {$filename}: " . $e->getMessage());
        }

        return null;
    }

    // Utilitas Kompresi Gambar
    // Kompres Berkas Gambar
    private function compressImage(string $filePath, int $quality = 60): bool
    {
        $info = @getimagesize($filePath);
        // Jangan lanjutkan proses jika berkas yang diunggah bukan gambar yang valid
        if (!$info) {
            return false;
        }

        $image = match ($info['mime']) {
            'image/jpeg' => imagecreatefromjpeg($filePath),
            'image/png'  => imagecreatefrompng($filePath),
            'image/webp' => imagecreatefromwebp($filePath),
            default      => null,
        };

        // Gagalkan kompresi jika resource gambar tidak berhasil dibuat
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
