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

    // Aksesor & Mutator
    public function getCapacityLabelAttribute(): string
    {
        return "{$this->capacity_current}/{$this->capacity_max}";
    }

    public function getCapacityPercentAttribute(): int
    {
        // Kalau kapasitas maksimal kurang dari atau sama dengan nol, kembalikan persentase nol
        if ($this->capacity_max <= 0) return 0;
        return (int) round(($this->capacity_current / $this->capacity_max) * 100);
    }

    // Metode Pembantu
    public function updateStatusFromCapacity(): void
    {
        // Update status posko menjadi Penuh jika kapasitas saat ini melebihi atau sama dengan kapasitas maksimal
        $this->status = $this->capacity_current >= $this->capacity_max ? 'Penuh' : 'Tersedia';
        $this->save();
    }

    public function setLogisticsAttribute($value)
    {
        // Jika nilai logistik adalah array, ubah menjadi format JSON string
        $this->attributes['logistics'] = is_array($value) ? json_encode($value) : $value;
    }

    // Aksesor logistik sederhana: mengembalikan data mentah database sebagai array
    public function getLogisticsAttribute($value): array
    {
        // Jika nilai logistik dari database berupa string, decode menjadi array
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            return is_array($decoded) ? $decoded : [];
        }

        return is_array($value) ? $value : [];
    }

    // Dapatkan kebutuhan logistik dinamis dari laporan relawan
    public function getDynamicLogistics(): array
    {
        // Ambil daftar ID relawan aktif yang ditugaskan ke posko ini
        $volunteerIds = Volunteer::where('assignment', $this->name)
            ->where('status', Volunteer::STATUS_APPROVED)
            ->pluck('id');

        // Jika tidak ada relawan yang ditugaskan ke posko ini, kembalikan array kosong
        if ($volunteerIds->isEmpty()) {
            return [];
        }

        // Ambil semua laporan medis dan logistik dari relawan-relawan tersebut
        $reports = VolunteerReport::whereIn('volunteer_id', $volunteerIds)
            ->whereIn('skill_type', ['MEDIS', 'LOGISTIK'])
            ->get();

        $needs = [];
        // Iterasi setiap laporan untuk mengumpulkan kebutuhan barang darurat
        foreach ($reports as $report) {
            $data = $report->report_data;
            // Jika data laporan berbentuk string JSON, ubah ke array
            if (is_string($data)) {
                $data = json_decode($data, true);
            }
            // Jika data laporan bukan array valid, lewati ke laporan berikutnya
            if (!is_array($data)) {
                continue;
            }
            $rawItems = '';

            // Ekstrak kebutuhan medis atau kebutuhan mendesak logistik dari data laporan
            if ($report->skill_type === 'MEDIS' && !empty($data['kebutuhan_medis'])) {
                $rawItems = $data['kebutuhan_medis'];
            } elseif ($report->skill_type === 'LOGISTIK' && !empty($data['kebutuhan_mendesak'])) {
                $rawItems = $data['kebutuhan_mendesak'];
            }

            // Jika barang kebutuhan kosong, lewati ke laporan berikutnya
            if (empty($rawItems)) {
                continue;
            }

            $items = preg_split('/[,;\n\r]+/', $rawItems);
            // Iterasi dan tambahkan barang kebutuhan unik yang belum ada di daftar
            foreach ($items as $item) {
                $trimmed = trim($item);
                if (!empty($trimmed) && !in_array($trimmed, $needs)) {
                    $needs[] = $trimmed;
                }
            }
        }

        return !empty($needs) ? $needs : [];
    }
}
