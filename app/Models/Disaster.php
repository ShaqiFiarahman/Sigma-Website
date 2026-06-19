<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Disaster extends Model
{
    protected $fillable = [
        'user_id',
        'verified_by',
        'title',
        'description',
        'photo_url',
        'latitude',
        'longitude',
        'location',
        'status',
        'reporter_name',
        'disaster_type',
    ];

    protected $casts = [
        'latitude'   => 'float',
        'longitude'  => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Konstanta Status
    const STATUS_PENDING  = 'PENDING';
    const STATUS_DECLINE  = 'DECLINE';
    const STATUS_RESOLVED = 'RESOLVED';
    const STATUS_SIAGA_1  = 'SIAGA_1';
    const STATUS_SIAGA_2  = 'SIAGA_2';
    const STATUS_AWAS     = 'AWAS';

    // Metadata tampilan untuk setiap jenis bencana
    private const TYPE_META = [
        'flood'      => ['icon' => 'bi-water',                 'color' => 'text-blue-500',    'name' => 'Banjir'],
        'fire'       => ['icon' => 'bi-fire',                  'color' => 'text-red-500',     'name' => 'Kebakaran'],
        'earthquake' => ['icon' => 'bi-house-exclamation',     'color' => 'text-emerald-500', 'name' => 'Gempa Bumi'],
        'landslide'  => ['icon' => 'bi-mountain',              'color' => 'text-amber-600',   'name' => 'Tanah Longsor'],
        'storm'      => ['icon' => 'bi-lightning-charge-fill', 'color' => 'text-cyan-500',    'name' => 'Badai/Angin Topan'],
        'tsunami'    => ['icon' => 'bi-water',                 'color' => 'text-cyan-500',    'name' => 'Tsunami'],
    ];

    // Metadata cadangan jika jenis bencana tidak dapat ditentukan
    private const TYPE_FALLBACK = [
        'icon' => 'bi-exclamation-triangle',
        'color' => 'text-slate-500',
        'name' => 'Lainnya / Tidak Diketahui',
    ];

    // Kata kunci untuk menebak jenis bencana dari judul jika tidak diketahui
    private const TITLE_KEYWORDS = [
        'banjir'    => 'flood',
        'kebakaran' => 'fire',
        'api'       => 'fire',
        'gempa'     => 'earthquake',
        'longsor'   => 'landslide',
        'tsunami'   => 'tsunami',
        'badai'     => 'storm',
        'topan'     => 'storm',
        'angin'     => 'storm',
    ];

    // Hubungan Database
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    // Accessors & Mutators
    // Dapatkan label status yang mudah dibaca
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING  => 'Pending',
            self::STATUS_DECLINE  => 'Ditolak',
            self::STATUS_RESOLVED => 'Resolved',
            self::STATUS_SIAGA_1  => 'Siaga 1',
            self::STATUS_SIAGA_2  => 'Siaga 2',
            self::STATUS_AWAS     => 'Awas',
            default               => ucfirst(strtolower($this->status)),
        };
    }

    // Dapatkan tingkat keparahan bencana untuk tampilan
    public function getTingkatAttribute(): ?string
    {
        return match($this->status) {
            self::STATUS_AWAS     => 'Awas',
            self::STATUS_SIAGA_1  => 'Siaga 1',
            self::STATUS_SIAGA_2  => 'Siaga 2',
            self::STATUS_RESOLVED => 'Resolved',
            default               => null,
        };
    }

    // Metode Pembantu
    // Tentukan metadata tampilan berdasarkan jenis bencana atau tebakan judul
    private function typeMeta(): array
    {
        $type = strtolower($this->disaster_type ?? 'unknown');

        // Jika tipe bencana belum diketahui, tebak tipe berdasarkan kata kunci di judul
        if ($type === 'unknown') {
            $type = $this->guessTypeFromTitle();
        }

        return self::TYPE_META[$type] ?? self::TYPE_FALLBACK;
    }

    // Tebak jenis bencana berdasarkan kata kunci pada judul
    private function guessTypeFromTitle(): string
    {
        $title = strtolower($this->title ?? '');

        // Cari kecocokan kata kunci dalam judul untuk menentukan kategori bencana
        foreach (self::TITLE_KEYWORDS as $keyword => $type) {
            if (str_contains($title, $keyword)) {
                return $type;
            }
        }

        return 'unknown';
    }

    // Dapatkan kelas ikon jenis bencana
    public function getTypeIconAttribute(): string
    {
        return $this->typeMeta()['icon'];
    }

    // Dapatkan kelas warna jenis bencana
    public function getTypeColorAttribute(): string
    {
        return $this->typeMeta()['color'];
    }

    // Dapatkan nama label jenis bencana dalam Bahasa Indonesia
    public function getTypeNameAttribute(): string
    {
        return $this->typeMeta()['name'];
    }
}
