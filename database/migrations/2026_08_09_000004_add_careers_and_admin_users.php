<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('role')->default('editor')->after('password');
            $table->boolean('is_active')->default(true)->after('role');
        });

        Schema::create('career_openings', function (Blueprint $table): void {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('department')->nullable();
            $table->string('job_type')->default('full_time');
            $table->string('location')->nullable();
            $table->text('summary')->nullable();
            $table->longText('description');
            $table->json('requirements')->nullable();
            $table->longText('application_instructions')->nullable();
            $table->date('application_deadline')->nullable();
            $table->string('status')->default('draft')->index();
            $table->dateTime('published_at')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('career_openings');
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn(['role', 'is_active']);
        });
    }
};
