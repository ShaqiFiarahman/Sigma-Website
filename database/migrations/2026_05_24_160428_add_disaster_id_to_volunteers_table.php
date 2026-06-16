<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('volunteers', function (Blueprint $table) {
            $table->unsignedBigInteger('disaster_id')->nullable()->after('status');
            $table->foreign('disaster_id')->references('id')->on('disasters')->onDelete('set null');
        });
    }

    
    public function down(): void
    {
        Schema::table('volunteers', function (Blueprint $table) {
            $table->dropForeign(['disaster_id']);
            $table->dropColumn('disaster_id');
        });
    }
};
