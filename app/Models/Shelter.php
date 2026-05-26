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

    public function getLogisticsAttribute($value)
    {
        // 1. Fetch all volunteer reports with skill type MEDIS or LOGISTIK where the volunteer's assignment matches $this->name
        $volunteerIds = \App\Models\Volunteer::where('assignment', $this->name)
            ->where('status', \App\Models\Volunteer::STATUS_APPROVED)
            ->pluck('id');

        $reports = \App\Models\VolunteerReport::whereIn('volunteer_id', $volunteerIds)
            ->whereIn('skill_type', ['MEDIS', 'LOGISTIK'])
            ->get();

        $needs = [];
        foreach ($reports as $report) {
            $data = $report->report_data;
            $items = [];
            if ($report->skill_type === 'MEDIS' && !empty($data['kebutuhan_medis'])) {
                $items = preg_split('/[,;\n\r]+/', $data['kebutuhan_medis']);
            } elseif ($report->skill_type === 'LOGISTIK' && !empty($data['kebutuhan_mendesak'])) {
                $items = preg_split('/[,;\n\r]+/', $data['kebutuhan_mendesak']);
            }

            foreach ($items as $item) {
                $trimmed = trim($item);
                if (!empty($trimmed) && !in_array($trimmed, $needs)) {
                    $needs[] = $trimmed;
                }
            }
        }

        // If we have dynamic reports, return them!
        if (!empty($needs)) {
            return $needs;
        }

        // Otherwise, return the database value or empty array if it's just dummy
        $dbLogistics = $value;
        if (is_string($dbLogistics)) {
            $dbLogistics = json_decode($dbLogistics, true);
        }
        if (!is_array($dbLogistics)) {
            $dbLogistics = [];
        }

        // Filter out old dummy seeded values to keep it clean.
        $dummyItems = [
            'Sembako', 'Air Mineral', 'Selimut', 'Pakaian Layak Pakai', 
            'Alat Mandi', 'Sleeping Bag', 'Makanan Instan', 'Obat-obatan', 
            'Popok Bayi', 'Susu Formula', 'Tikar'
        ];
        
        $filtered = array_filter($dbLogistics, function($item) use ($dummyItems) {
            return !in_array(trim($item), $dummyItems);
        });

        return array_values($filtered);
    }
}

