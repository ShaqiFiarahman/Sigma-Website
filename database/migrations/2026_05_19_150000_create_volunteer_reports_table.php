<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('volunteer_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('volunteer_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('disaster_id')->nullable();
            $table->string('skill_type'); // MEDIS, SAR, LOGISTIK, KONSUMSI, PSIKOSOSIAL
            $table->json('report_data');  // Data spesifik per skill
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('disaster_id')->references('id')->on('disasters')->onDelete('set null');
            $table->index(['volunteer_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('volunteer_reports');
    }
};
