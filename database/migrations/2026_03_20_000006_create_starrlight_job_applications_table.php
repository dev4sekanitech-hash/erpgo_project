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
        Schema::create('starrlight_job_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained('starrlight_jobs')->onDelete('cascade');
            $table->foreignId('caregiver_profile_id')->constrained('caregiver_profiles')->onDelete('cascade');
            $table->enum('status', ['submitted', 'under_review', 'interview', 'rejected', 'hired'])->default('submitted');
            $table->timestamp('applied_at')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('starrlight_job_applications');
    }
};
