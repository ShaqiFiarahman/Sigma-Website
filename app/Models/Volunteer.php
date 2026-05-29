<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Volunteer extends Model
{
    protected $fillable = [
        'user_id',
        'volunteer_code',
        'name',
        'skill',
        'address',
        'phone_number',
        'status',
        'assignment',
        'disaster_id',
        'availability',
        'assignment_notified_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'assignment_notified_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::created(function ($volunteer) {
            $phone = preg_replace('/\D/', '', $volunteer->phone_number);
            $last4 = substr(str_pad($phone, 4, '0', STR_PAD_LEFT), -4);
            $volunteer->volunteer_code = 'RL-' . $last4 . '-' . $volunteer->id;
            $volunteer->saveQuietly();
        });
    }

    const STATUS_PENDING  = 'PENDING';
    const STATUS_APPROVED = 'APPROVED';
    const STATUS_REJECTED = 'REJECTED';
    const STATUS_FIRED    = 'FIRED';

    const AVAILABILITY_AVAILABLE   = 'available';
    const AVAILABILITY_UNAVAILABLE = 'unavailable';

    const SKILL_MEDIS       = 'MEDIS';
    const SKILL_SAR         = 'SAR';
    const SKILL_LOGISTIK    = 'LOGISTIK';
    const SKILL_KONSUMSI    = 'KONSUMSI';
    const SKILL_PSIKOSOSIAL = 'PSIKOSOSIAL';
    const SKILL_PENDIDIKAN  = 'PENDIDIKAN';

    public static function getSkillOptions(): array
    {
        return [
            self::SKILL_MEDIS       => 'MEDIS',
            self::SKILL_SAR         => 'SAR',
            self::SKILL_LOGISTIK    => 'LOGISTIK',
            self::SKILL_KONSUMSI    => 'KONSUMSI',
            self::SKILL_PSIKOSOSIAL => 'PSIKOSOSIAL',
            self::SKILL_PENDIDIKAN  => 'PENDIDIKAN',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function disaster(): BelongsTo
    {
        return $this->belongsTo(Disaster::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING  => 'Pending',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_REJECTED => 'Rejected',
            self::STATUS_FIRED    => 'Nonaktif',
            default               => $this->status,
        };
    }
}
