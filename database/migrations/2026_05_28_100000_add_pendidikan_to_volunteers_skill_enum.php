<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{

    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'pgsql') {
            // PostgreSQL: ubah tipe kolom ke varchar untuk mengizinkan nilai apa pun
            // karena Laravel tidak mendukung enum PostgreSQL dengan baik secara bawaan
            DB::statement("ALTER TABLE volunteers ALTER COLUMN skill TYPE VARCHAR(255)");
        } elseif ($driver === 'mysql') {
            DB::statement("ALTER TABLE volunteers MODIFY COLUMN skill ENUM('MEDIS', 'SAR', 'LOGISTIK', 'KONSUMSI', 'PSIKOSOSIAL', 'PENDIDIKAN')");
        }
        // SQLite tidak memaksakan batasan enum
    }


    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'pgsql') {
            DB::statement("ALTER TABLE volunteers ALTER COLUMN skill TYPE VARCHAR(255)");
        } elseif ($driver === 'mysql') {
            DB::statement("ALTER TABLE volunteers MODIFY COLUMN skill ENUM('MEDIS', 'SAR', 'LOGISTIK', 'KONSUMSI', 'PSIKOSOSIAL')");
        }
    }
};
