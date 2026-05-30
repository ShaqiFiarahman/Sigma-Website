<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('volunteers', function (Blueprint $table) {
            $table->enum('assignment_status', ['pending', 'accepted', 'rejected'])->nullable()->after('assignment_notified_at');
            $table->text('assignment_rejection_reason')->nullable()->after('assignment_status');
        });
    }

    public function down(): void
    {
        Schema::table('volunteers', function (Blueprint $table) {
            $table->dropColumn(['assignment_status', 'assignment_rejection_reason']);
        });
    }
};
