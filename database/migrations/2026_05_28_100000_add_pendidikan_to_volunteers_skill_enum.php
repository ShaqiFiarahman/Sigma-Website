<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'pgsql') {
            // PostgreSQL: change column type to varchar to allow any value
            // since Laravel doesn't natively support PostgreSQL enums well
            DB::statement("ALTER TABLE volunteers ALTER COLUMN skill TYPE VARCHAR(255)");
        } elseif ($driver === 'mysql') {
            DB::statement("ALTER TABLE volunteers MODIFY COLUMN skill ENUM('MEDIS', 'SAR', 'LOGISTIK', 'KONSUMSI', 'PSIKOSOSIAL', 'PENDIDIKAN')");
        }
        // SQLite doesn't enforce enum constraints
    }

    /**
     * Reverse the migrations.
     */
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
