<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::table('disasters', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('verified_by');
        });

        Schema::table('volunteers', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('disaster_id');
            $table->index('assigned_by');
            $table->index('status');
        });

        Schema::table('volunteer_reports', function (Blueprint $table) {
            $table->index('disaster_id');
        });

        Schema::table('news', function (Blueprint $table) {
            $table->index('published_at');
            $table->index(['source', 'published_at']);
        });
    }

    
    public function down(): void
    {
        Schema::table('news', function (Blueprint $table) {
            $table->dropIndex(['source', 'published_at']);
            $table->dropIndex(['published_at']);
        });

        Schema::table('volunteer_reports', function (Blueprint $table) {
            $table->dropIndex(['disaster_id']);
        });

        Schema::table('volunteers', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['assigned_by']);
            $table->dropIndex(['disaster_id']);
            $table->dropIndex(['user_id']);
        });

        Schema::table('disasters', function (Blueprint $table) {
            $table->dropIndex(['verified_by']);
            $table->dropIndex(['user_id']);
        });
    }
};
