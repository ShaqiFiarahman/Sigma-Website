<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Sesuai dengan Android VolunteerRegistrationScreen:
     * - name (Nama Lengkap)
     * - skill (Keahlian/Spesialisasi - single)
     * - address (Alamat Domisili)
     * - phone_number (Nomor Telepon)
     */
    public function up(): void
    {
        Schema::create('volunteers', function (Blueprint $table) {
            $table->id();
            $table->uuid('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('profiles')->onDelete('set null');
            $table->string('name');
            $table->enum('skill', ['MEDIS', 'SAR', 'LOGISTIK', 'KONSUMSI', 'PSIKOSOSIAL']);
            $table->text('address');
            $table->string('phone_number');
            $table->enum('status', ['PENDING', 'APPROVED', 'REJECTED'])->default('PENDING');
            $table->string('assignment')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('volunteers');
    }
};
