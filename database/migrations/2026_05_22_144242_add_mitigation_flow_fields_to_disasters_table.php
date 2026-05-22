<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('disasters', function (Blueprint $table) {
            $table->string('disaster_type')->nullable()->default('unknown')->after('reporter_name');
        });

        // Deteksi jenis bencana secara otomatis dari judul untuk data lama
        \Illuminate\Support\Facades\DB::table('disasters')->get()->each(function ($row) {
            $titleLower = strtolower($row->title);
            $disasterType = 'unknown';
            if (str_contains($titleLower, 'banjir')) { $disasterType = 'flood'; }
            elseif (str_contains($titleLower, 'kebakaran') || str_contains($titleLower, 'api')) { $disasterType = 'fire'; }
            elseif (str_contains($titleLower, 'gempa')) { $disasterType = 'earthquake'; }
            elseif (str_contains($titleLower, 'longsor')) { $disasterType = 'landslide'; }
            elseif (str_contains($titleLower, 'badai') || str_contains($titleLower, 'topan') || str_contains($titleLower, 'angin')) { $disasterType = 'storm'; }

            \Illuminate\Support\Facades\DB::table('disasters')
                ->where('id', $row->id)
                ->update([
                    'disaster_type' => $disasterType,
                ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('disasters', function (Blueprint $table) {
            $table->dropColumn(['disaster_type']);
        });
    }
};
