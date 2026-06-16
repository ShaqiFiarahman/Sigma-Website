<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{

    public function up(): void
    {
        DB::statement('ALTER TABLE volunteers DROP CONSTRAINT volunteers_skill_check');
        DB::statement("ALTER TABLE volunteers ADD CONSTRAINT volunteers_skill_check CHECK (((skill)::text = ANY (ARRAY[('MEDIS'::character varying)::text, ('SAR'::character varying)::text, ('LOGISTIK'::character varying)::text, ('KONSUMSI'::character varying)::text, ('PSIKOSOSIAL'::character varying)::text, ('PENDIDIKAN'::character varying)::text])))");
    }


    public function down(): void
    {
        DB::statement('ALTER TABLE volunteers DROP CONSTRAINT volunteers_skill_check');
        DB::statement("ALTER TABLE volunteers ADD CONSTRAINT volunteers_skill_check CHECK (((skill)::text = ANY (ARRAY[('MEDIS'::character varying)::text, ('SAR'::character varying)::text, ('LOGISTIK'::character varying)::text, ('KONSUMSI'::character varying)::text, ('PSIKOSOSIAL'::character varying)::text])))");
    }
};
