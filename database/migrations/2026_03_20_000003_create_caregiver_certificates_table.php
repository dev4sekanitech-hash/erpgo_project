<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('caregiver_certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('caregiver_profile_id')->constrained('caregiver_profiles')->onDelete('cascade');
            $table->string('type', 255);
            $table->string('issuing_organization', 255);
            $table->string('city', 100)->nullable();
            $table->date('date_obtained')->nullable();
            $table->string('certificate_file_url', 500)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('caregiver_certificates');
    }
};
