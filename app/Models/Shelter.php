<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shelter extends Model
{
    protected $fillable = [
        'name',
        'address',
        'latitude',
        'longitude',
        'capacity_current',
        'capacity_max',
        'status',
        'logistics',
        'contact_phone',
    ];

    protected $casts = [
        'latitude'  => 'float',
        'longitude' => 'float',
        'logistics' => 'array',
        'capacity_current' => 'integer',
        'capacity_max' => 'integer',
    ];

    /**
     * Get capacity as formatted string "80/100"
     */
    public function getCapacityLabelAttribute(): string
    {
        return "{$this->capacity_current}/{$this->capacity_max}";
    }

    /**
     * Get capacity percentage
     */
    public function getCapacityPercentAttribute(): int
    {
        if ($this->capacity_max <= 0) return 0;
        return (int) round(($this->capacity_current / $this->capacity_max) * 100);
    }

    /**
     * Auto-update status based on capacity
     */
    public function updateStatusFromCapacity(): void
    {
        $this->status = $this->capacity_current >= $this->capacity_max ? 'Penuh' : 'Tersedia';
        $this->save();
    }
}
