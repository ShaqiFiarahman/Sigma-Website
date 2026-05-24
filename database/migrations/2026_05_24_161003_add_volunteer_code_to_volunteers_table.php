<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('volunteers', function (Blueprint $table) {
            $table->string('volunteer_code')->nullable()->after('user_id');
        });

        // Populate existing volunteers
        $volunteers = \Illuminate\Support\Facades\DB::table('volunteers')->orderBy('id')->get();
        foreach ($volunteers as $v) {
            $phone = preg_replace('/\D/', '', $v->phone_number);
            $last4 = substr(str_pad($phone, 4, '0', STR_PAD_LEFT), -4);
            $code = 'RL-' . $last4 . '-' . $v->id;

            \Illuminate\Support\Facades\DB::table('volunteers')
                ->where('id', $v->id)
                ->update(['volunteer_code' => $code]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('volunteers', function (Blueprint $table) {
            $table->dropColumn('volunteer_code');
        });
    }
};
