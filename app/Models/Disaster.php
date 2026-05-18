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
            self::STATUS_DECLINE  => 'Decline',
            self::STATUS_RESOLVED => 'Resolved',
            self::STATUS_SIAGA_1  => 'Siaga 1',
            self::STATUS_SIAGA_2  => 'Siaga 2',
            self::STATUS_AWAS     => 'Awas',
            default               => $this->status,
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
}
