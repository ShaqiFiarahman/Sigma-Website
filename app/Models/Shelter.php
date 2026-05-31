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
        'photo_url',
    ];

    protected $casts = [
        'latitude'  => 'float',
        'longitude' => 'float',
        'logistics' => 'array',
        'capacity_current' => 'integer',
        'capacity_max' => 'integer',
    ];

    public function getCapacityLabelAttribute(): string
    {
        return "{$this->capacity_current}/{$this->capacity_max}";
    }

    public function getCapacityPercentAttribute(): int
    {
        if ($this->capacity_max <= 0) return 0;
        return (int) round(($this->capacity_current / $this->capacity_max) * 100);
    }

    public function updateStatusFromCapacity(): void
    {
        $this->status = $this->capacity_current >= $this->capacity_max ? 'Penuh' : 'Tersedia';
        $this->save();
    }

    public function setLogisticsAttribute($value)
    {
        $this->attributes['logistics'] = is_array($value) ? json_encode($value) : $value;
    }

    /**
     * Simple accessor: returns the raw database value as an array.
     * Use getDynamicLogistics() for computed logistics from volunteer reports.
     */
    public function getLogisticsAttribute($value): array
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            return is_array($decoded) ? $decoded : [];
        }

        return is_array($value) ? $value : [];
    }

    /**
     * Get dynamic logistics needs from volunteer reports assigned to this shelter.
     * Call this explicitly when you need computed data (e.g. $shelter->getDynamicLogistics()).
     */
    public function getDynamicLogistics(): array
    {
        $volunteerIds = Volunteer::where('assignment', $this->name)
            ->where('status', Volunteer::STATUS_APPROVED)
            ->pluck('id');

        if ($volunteerIds->isEmpty()) {
            return $this->logistics;
        }

        $reports = VolunteerReport::whereIn('volunteer_id', $volunteerIds)
            ->whereIn('skill_type', ['MEDIS', 'LOGISTIK'])
            ->get();

        $needs = [];
        foreach ($reports as $report) {
            $data = $report->report_data;
            $rawItems = '';

            if ($report->skill_type === 'MEDIS' && !empty($data['kebutuhan_medis'])) {
                $rawItems = $data['kebutuhan_medis'];
            } elseif ($report->skill_type === 'LOGISTIK' && !empty($data['kebutuhan_mendesak'])) {
                $rawItems = $data['kebutuhan_mendesak'];
            }

            if (empty($rawItems)) {
                continue;
            }

            $items = preg_split('/[,;\n\r]+/', $rawItems);
            foreach ($items as $item) {
                $trimmed = trim($item);
                if (!empty($trimmed) && !in_array($trimmed, $needs)) {
                    $needs[] = $trimmed;
                }
            }
        }

        return !empty($needs) ? $needs : $this->logistics;
    }
}
