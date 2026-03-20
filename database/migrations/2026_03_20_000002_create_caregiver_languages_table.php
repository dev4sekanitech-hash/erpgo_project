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
        Schema::create('caregiver_languages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('caregiver_profile_id')->constrained('caregiver_profiles')->onDelete('cascade');
            $table->string('language', 100);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('caregiver_languages');
    }
};
