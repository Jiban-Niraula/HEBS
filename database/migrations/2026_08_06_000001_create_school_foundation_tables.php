<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('theme_palettes', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->json('colors');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('school_settings', function (Blueprint $table): void {
            $table->id();
            $table->string('official_name');
            $table->string('short_name')->nullable();
            $table->string('local_name')->nullable();
            $table->string('motto')->nullable();
            $table->string('establishment_year')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('favicon_path')->nullable();
            $table->string('address')->nullable();
            $table->string('main_phone')->nullable();
            $table->string('alternate_phone')->nullable();
            $table->string('official_email')->nullable();
            $table->string('admission_email')->nullable();
            $table->string('office_hours')->nullable();
            $table->text('google_map_url')->nullable();
            $table->text('school_app_url')->nullable();
            $table->json('social_links')->nullable();
            $table->foreignId('theme_palette_id')->nullable()->constrained()->nullOnDelete();
            $table->string('copyright_text')->nullable();
            $table->timestamps();
        });

        Schema::create('academic_programs', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('grade_or_age_range')->nullable();
            $table->text('short_description')->nullable();
            $table->longText('full_introduction')->nullable();
            $table->json('learning_objectives')->nullable();
            $table->json('curriculum_overview')->nullable();
            $table->text('teaching_approach')->nullable();
            $table->text('assessment_approach')->nullable();
            $table->text('student_support')->nullable();
            $table->json('activities')->nullable();
            $table->string('featured_image_path')->nullable();
            $table->unsignedInteger('display_order')->default(0);
            $table->boolean('is_published')->default(false);
            $table->json('seo')->nullable();
            $table->timestamps();
        });

        Schema::create('notices', function (Blueprint $table): void {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('notice_number')->nullable();
            $table->string('category')->index();
            $table->text('summary')->nullable();
            $table->longText('content')->nullable();
            $table->string('featured_image_path')->nullable();
            $table->string('attachment_path')->nullable();
            $table->dateTime('published_at')->nullable();
            $table->dateTime('starts_at')->nullable();
            $table->dateTime('expires_at')->nullable();
            $table->string('priority')->default('normal')->index();
            $table->boolean('is_pinned')->default(false);
            $table->boolean('show_on_homepage')->default(false);
            $table->boolean('show_in_announcement')->default(false);
            $table->boolean('show_as_popup')->default(false);
            $table->string('target_audience')->default('public');
            $table->string('status')->default('draft')->index();
            $table->json('seo')->nullable();
            $table->timestamps();
        });

        Schema::create('popup_notices', function (Blueprint $table): void {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('summary')->nullable();
            $table->longText('content')->nullable();
            $table->string('notice_type')->default('general')->index();
            $table->string('priority')->default('normal')->index();
            $table->string('image_path')->nullable();
            $table->string('attachment_path')->nullable();
            $table->string('button_label')->nullable();
            $table->text('button_url')->nullable();
            $table->string('display_frequency')->default('once_per_session');
            $table->string('target_scope')->default('homepage');
            $table->json('target_pages')->nullable();
            $table->dateTime('starts_at')->nullable();
            $table->dateTime('ends_at')->nullable();
            $table->boolean('show_close_button')->default(true);
            $table->boolean('allow_do_not_show_again')->default(true);
            $table->boolean('is_active')->default(false);
            $table->foreignId('created_by')->nullable();
            $table->foreignId('updated_by')->nullable();
            $table->timestamps();
        });

        Schema::create('admission_enquiries', function (Blueprint $table): void {
            $table->id();
            $table->string('student_name');
            $table->string('guardian_name');
            $table->string('telephone');
            $table->string('email')->nullable();
            $table->string('desired_program')->nullable();
            $table->string('current_grade')->nullable();
            $table->string('current_school')->nullable();
            $table->string('preferred_contact_method')->default('phone');
            $table->text('message')->nullable();
            $table->boolean('privacy_consent')->default(false);
            $table->string('status')->default('new')->index();
            $table->json('internal_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admission_enquiries');
        Schema::dropIfExists('popup_notices');
        Schema::dropIfExists('notices');
        Schema::dropIfExists('academic_programs');
        Schema::dropIfExists('school_settings');
        Schema::dropIfExists('theme_palettes');
    }
};
