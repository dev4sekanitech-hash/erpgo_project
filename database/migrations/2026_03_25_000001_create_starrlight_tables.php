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
        // Starrlight Caregiver Profiles
        Schema::create('starrlight_caregiver_profiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('company_id');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('phone_number');
            $table->string('email');
            $table->string('city');
            $table->string('province', 2);
            $table->text('short_bio')->nullable();
            $table->text('caregiver_motivation')->nullable();
            $table->string('profile_photo_url')->nullable();
            $table->integer('step_completed')->default(0);
            $table->boolean('is_submitted')->default(false);
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();

            $table->index(['user_id']);
            $table->index(['company_id']);
        });

        // Caregiver Languages
        Schema::create('starrlight_caregiver_languages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('profile_id');
            $table->string('language');
            $table->timestamps();

            $table->foreign('profile_id')->references('id')->on('starrlight_caregiver_profiles')->onDelete('cascade');
        });

        // Caregiver Certificates
        Schema::create('starrlight_caregiver_certificates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('profile_id');
            $table->string('type');
            $table->string('issuing_organization');
            $table->string('city');
            $table->date('date_obtained');
            $table->string('certificate_file_url');
            $table->timestamps();

            $table->foreign('profile_id')->references('id')->on('starrlight_caregiver_profiles')->onDelete('cascade');
        });

        // Caregiver Work History
        Schema::create('starrlight_caregiver_work_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('profile_id');
            $table->string('employer');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->boolean('is_current_employer')->default(false);
            $table->boolean('can_be_contacted')->default(false);
            $table->timestamps();

            $table->foreign('profile_id')->references('id')->on('starrlight_caregiver_profiles')->onDelete('cascade');
        });

        // Staff Requests (Book Healthcare Staff)
        Schema::create('starrlight_staff_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('phone_number');
            $table->string('email');
            $table->string('city');
            $table->string('province', 2);
            $table->text('additional_information')->nullable();
            $table->string('status', 50)->default('pending');
            $table->timestamps();

            $table->index(['company_id']);
        });

        // Career Applications
        Schema::create('starrlight_career_applications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('phone_number');
            $table->string('email');
            $table->string('city');
            $table->string('province', 2);
            $table->string('resume_url');
            $table->text('additional_information')->nullable();
            $table->string('status', 50)->default('pending');
            $table->timestamps();

            $table->index(['company_id']);
        });

        // Contact Submissions
        Schema::create('starrlight_contact_submissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('phone_number');
            $table->string('email');
            $table->string('subject');
            $table->text('message');
            $table->string('status', 50)->default('unread');
            $table->timestamps();

            $table->index(['company_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('starrlight_contact_submissions');
        Schema::dropIfExists('starrlight_career_applications');
        Schema::dropIfExists('starrlight_staff_requests');
        Schema::dropIfExists('starrlight_caregiver_work_history');
        Schema::dropIfExists('starrlight_caregiver_certificates');
        Schema::dropIfExists('starrlight_caregiver_languages');
        Schema::dropIfExists('starrlight_caregiver_profiles');
    }
};
