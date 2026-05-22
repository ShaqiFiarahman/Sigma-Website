<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Disaster extends Model
{
    protected $fillable = [
        'user_id',
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

    // Status constants (sesuai Android ReportStatus enum)
    const STATUS_PENDING  = 'PENDING';
    const STATUS_DECLINE  = 'DECLINE';
    const STATUS_RESOLVED = 'RESOLVED';
    const STATUS_SIAGA_1  = 'SIAGA_1';
    const STATUS_SIAGA_2  = 'SIAGA_2';
    const STATUS_AWAS     = 'AWAS';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get human-readable status label
     */
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

    /**
     * Get severity level for display (maps to tingkat_bencana)
     */
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

    /**
     * Get disaster type icon class
     */
    public function getTypeIconAttribute(): string
    {
        $t = strtolower($this->disaster_type ?? 'unknown');
        if ($t === 'unknown') {
            $titleLower = strtolower($this->title);
            if (str_contains($titleLower, 'banjir')) return 'bi-water';
            if (str_contains($titleLower, 'kebakaran') || str_contains($titleLower, 'api')) return 'bi-fire';
            if (str_contains($titleLower, 'gempa')) return 'bi-house-exclamation';
            if (str_contains($titleLower, 'longsor')) return 'bi-mountain';
            if (str_contains($titleLower, 'tsunami')) return 'bi-water';
            if (str_contains($titleLower, 'badai') || str_contains($titleLower, 'topan') || str_contains($titleLower, 'angin')) return 'bi-lightning-charge-fill';
            return 'bi-exclamation-triangle';
        }

        return match($t) {
            'flood' => 'bi-water',
            'fire' => 'bi-fire',
            'earthquake' => 'bi-house-exclamation',
            'landslide' => 'bi-mountain',
            'storm' => 'bi-lightning-charge-fill',
            default => 'bi-exclamation-triangle',
        };
    }

    /**
     * Get disaster type color class
     */
    public function getTypeColorAttribute(): string
    {
        $t = strtolower($this->disaster_type ?? 'unknown');
        if ($t === 'unknown') {
            $titleLower = strtolower($this->title);
            if (str_contains($titleLower, 'banjir')) return 'text-blue-500';
            if (str_contains($titleLower, 'kebakaran') || str_contains($titleLower, 'api')) return 'text-red-500';
            if (str_contains($titleLower, 'gempa')) return 'text-emerald-500';
            if (str_contains($titleLower, 'longsor')) return 'text-amber-600';
            if (str_contains($titleLower, 'tsunami')) return 'text-cyan-500';
            if (str_contains($titleLower, 'badai') || str_contains($titleLower, 'topan') || str_contains($titleLower, 'angin')) return 'text-cyan-500';
            return 'text-slate-500';
        }

        return match($t) {
            'flood' => 'text-blue-500',
            'fire' => 'text-red-500',
            'earthquake' => 'text-emerald-500',
            'landslide' => 'text-amber-600',
            'storm' => 'text-cyan-500',
            default => 'text-slate-500',
        };
    }

    /**
     * Get disaster type label in Indonesian
     */
    public function getTypeNameAttribute(): string
    {
        $t = strtolower($this->disaster_type ?? 'unknown');
        if ($t === 'unknown') {
            $titleLower = strtolower($this->title);
            if (str_contains($titleLower, 'banjir')) return 'Banjir';
            if (str_contains($titleLower, 'kebakaran') || str_contains($titleLower, 'api')) return 'Kebakaran';
            if (str_contains($titleLower, 'gempa')) return 'Gempa Bumi';
            if (str_contains($titleLower, 'longsor')) return 'Tanah Longsor';
            if (str_contains($titleLower, 'tsunami')) return 'Tsunami';
            if (str_contains($titleLower, 'badai') || str_contains($titleLower, 'topan') || str_contains($titleLower, 'angin')) return 'Badai/Angin Topan';
            return 'Lainnya / Tidak Diketahui';
        }

        return match($t) {
            'flood' => 'Banjir',
            'fire' => 'Kebakaran',
            'earthquake' => 'Gempa Bumi',
            'landslide' => 'Tanah Longsor',
            'storm' => 'Badai/Angin Topan',
            default => 'Lainnya / Tidak Diketahui',
        };
    }
}
