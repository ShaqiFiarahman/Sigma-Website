<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VolunteerReport extends Model
{
    protected $fillable = [
        'volunteer_id',
        'disaster_id',
        'skill_type',
        'report_data',
        'notes',
    ];

    protected $casts = [
        'report_data' => 'array',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
    ];

    public function volunteer(): BelongsTo
    {
        return $this->belongsTo(Volunteer::class);
    }

    public function disaster(): BelongsTo
    {
        return $this->belongsTo(Disaster::class);
    }

    /**
     * Get form fields definition per skill type
     */
    public static function getFieldsForSkill(string $skill): array
    {
        return match ($skill) {
            'MEDIS' => [
                ['name' => 'total_korban',     'label' => 'Total Korban',          'type' => 'number'],
                ['name' => 'selamat',          'label' => 'Selamat',               'type' => 'number'],
                ['name' => 'luka_ringan',      'label' => 'Luka Ringan',           'type' => 'number'],
                ['name' => 'luka_berat',       'label' => 'Luka Berat',            'type' => 'number'],
                ['name' => 'kritis',           'label' => 'Kritis',                'type' => 'number'],
                ['name' => 'meninggal',        'label' => 'Meninggal',             'type' => 'number'],
                ['name' => 'kebutuhan_medis',  'label' => 'Kebutuhan Medis',       'type' => 'textarea'],
            ],
            'SAR' => [
                ['name' => 'total_dievakuasi', 'label' => 'Total Dievakuasi',      'type' => 'number'],
                ['name' => 'masih_dicari',     'label' => 'Masih Dicari',          'type' => 'number'],
                ['name' => 'lokasi_evakuasi',  'label' => 'Lokasi Evakuasi',       'type' => 'text'],
                ['name' => 'kendala',          'label' => 'Kendala di Lapangan',   'type' => 'textarea'],
                ['name' => 'status_pencarian', 'label' => 'Status Pencarian',      'type' => 'select', 'options' => ['Dalam proses', 'Selesai', 'Terhenti sementara']],
            ],
            'LOGISTIK' => [
                ['name' => 'jenis_bantuan',      'label' => 'Jenis Bantuan',         'type' => 'text'],
                ['name' => 'jumlah_disalurkan',  'label' => 'Jumlah Disalurkan',     'type' => 'number'],
                ['name' => 'stok_tersisa',       'label' => 'Stok Tersisa',          'type' => 'number'],
                ['name' => 'kebutuhan_mendesak', 'label' => 'Kebutuhan Mendesak',    'type' => 'textarea'],
            ],
            'KONSUMSI' => [
                ['name' => 'jumlah_porsi',       'label' => 'Jumlah Porsi',          'type' => 'number'],
                ['name' => 'menu',               'label' => 'Menu Hari Ini',         'type' => 'text'],
                ['name' => 'pengungsi_dilayani', 'label' => 'Pengungsi Dilayani',    'type' => 'number'],
                ['name' => 'kebutuhan_bahan',    'label' => 'Kebutuhan Bahan',       'type' => 'textarea'],
            ],
            'PSIKOSOSIAL' => [
                ['name' => 'jumlah_didampingi',  'label' => 'Jumlah Didampingi',     'type' => 'number'],
                ['name' => 'kondisi_umum',       'label' => 'Kondisi Psikologis Umum', 'type' => 'textarea'],
                ['name' => 'kasus_khusus',       'label' => 'Kasus Khusus',          'type' => 'number'],
                ['name' => 'rekomendasi',        'label' => 'Rekomendasi',           'type' => 'textarea'],
            ],
            default => [],
        };
    }
}
