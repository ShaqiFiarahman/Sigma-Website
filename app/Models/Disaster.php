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

    // Status constants (sesuai Android ReportStatus enum)
    const STATUS_PENDING  = 'PENDING';
    const STATUS_DECLINE  = 'DECLINE';
    const STATUS_RESOLVED = 'RESOLVED';
    const STATUS_SIAGA_1  = 'SIAGA_1';
    const STATUS_SIAGA_2  = 'SIAGA_2';
    const STATUS_AWAS     = 'AWAS';

    /**
     * Display metadata (icon, color, name) per canonical disaster type.
     */
    private const TYPE_META = [
        'flood'      => ['icon' => 'bi-water',                 'color' => 'text-blue-500',    'name' => 'Banjir'],
        'fire'       => ['icon' => 'bi-fire',                  'color' => 'text-red-500',     'name' => 'Kebakaran'],
        'earthquake' => ['icon' => 'bi-house-exclamation',     'color' => 'text-emerald-500', 'name' => 'Gempa Bumi'],
        'landslide'  => ['icon' => 'bi-mountain',              'color' => 'text-amber-600',   'name' => 'Tanah Longsor'],
        'storm'      => ['icon' => 'bi-lightning-charge-fill', 'color' => 'text-cyan-500',    'name' => 'Badai/Angin Topan'],
        'tsunami'    => ['icon' => 'bi-water',                 'color' => 'text-cyan-500',    'name' => 'Tsunami'],
    ];

    /**
     * Fallback metadata when the type cannot be determined.
     */
    private const TYPE_FALLBACK = [
        'icon' => 'bi-exclamation-triangle',
        'color' => 'text-slate-500',
        'name' => 'Lainnya / Tidak Diketahui',
    ];

    /**
     * Keyword → canonical type, used to guess the type from the title
     * when disaster_type is "unknown". Order matters (first match wins).
     */
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
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
     * Resolve display metadata (icon, color, name) for this disaster.
     * Uses disaster_type when known, otherwise guesses from the title.
     */
    private function typeMeta(): array
    {
        $type = strtolower($this->disaster_type ?? 'unknown');

        if ($type === 'unknown') {
            $type = $this->guessTypeFromTitle();
        }

        return self::TYPE_META[$type] ?? self::TYPE_FALLBACK;
    }

    /**
     * Guess the canonical type from keywords in the title.
     */
    private function guessTypeFromTitle(): string
    {
        $title = strtolower($this->title ?? '');

        foreach (self::TITLE_KEYWORDS as $keyword => $type) {
            if (str_contains($title, $keyword)) {
                return $type;
            }
        }

        return 'unknown';
    }

    /**
     * Get disaster type icon class
     */
    public function getTypeIconAttribute(): string
    {
        return $this->typeMeta()['icon'];
    }

    /**
     * Get disaster type color class
     */
    public function getTypeColorAttribute(): string
    {
        return $this->typeMeta()['color'];
    }

    /**
     * Get disaster type label in Indonesian
     */
    public function getTypeNameAttribute(): string
    {
        return $this->typeMeta()['name'];
    }
}
