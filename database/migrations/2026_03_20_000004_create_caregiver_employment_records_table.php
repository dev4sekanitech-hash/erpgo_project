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
        Schema::create('caregiver_employment_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('caregiver_profile_id')->constrained('caregiver_profiles')->onDelete('cascade');
            $table->string('employer', 255);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->boolean('is_current_employer')->default(false);
            $table->boolean('can_be_contacted')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('caregiver_employment_records');
    }
};
