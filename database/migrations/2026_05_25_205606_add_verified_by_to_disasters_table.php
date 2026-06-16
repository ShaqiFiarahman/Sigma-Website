<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::table('disasters', function (Blueprint $table) {
            $table->uuid('verified_by')->nullable()->after('user_id');
            $table->foreign('verified_by')->references('id')->on('profiles')->onDelete('set null');
        });
    }

    
    public function down(): void
    {
        Schema::table('disasters', function (Blueprint $table) {
            $table->dropForeign(['verified_by']);
            $table->dropColumn('verified_by');
        });
    }
};
