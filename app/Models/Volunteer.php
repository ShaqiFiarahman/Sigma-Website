<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Volunteer extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'skill',
        'address',
        'phone_number',
        'status',
        'assignment',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Status constants
    const STATUS_PENDING  = 'PENDING';
    const STATUS_APPROVED = 'APPROVED';
    const STATUS_REJECTED = 'REJECTED';

    // Skills enum (sesuai Android SkillsVolunteer enum)
    const SKILL_MEDIS       = 'MEDIS';
    const SKILL_SAR         = 'SAR';
    const SKILL_LOGISTIK    = 'LOGISTIK';
    const SKILL_KONSUMSI    = 'KONSUMSI';
    const SKILL_PSIKOSOSIAL = 'PSIKOSOSIAL';

    public static function getSkillOptions(): array
    {
        return [
            self::SKILL_MEDIS       => 'MEDIS',
            self::SKILL_SAR         => 'SAR',
            self::SKILL_LOGISTIK    => 'LOGISTIK',
            self::SKILL_KONSUMSI    => 'KONSUMSI',
            self::SKILL_PSIKOSOSIAL => 'PSIKOSOSIAL',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get human-readable status
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING  => 'Pending',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_REJECTED => 'Rejected',
            default               => $this->status,
        };
    }
}
