<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('news_posts', function (Blueprint $table): void {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('category')->nullable();
            $table->text('summary')->nullable();
            $table->longText('content')->nullable();
            $table->string('featured_image_path')->nullable();
            $table->dateTime('published_at')->nullable();
            $table->boolean('show_on_homepage')->default(false);
            $table->string('status')->default('draft')->index();
            $table->json('seo')->nullable();
            $table->timestamps();
        });

        Schema::create('events', function (Blueprint $table): void {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->dateTime('starts_at');
            $table->dateTime('ends_at')->nullable();
            $table->string('venue')->nullable();
            $table->text('map_url')->nullable();
            $table->string('status')->default('upcoming')->index();
            $table->boolean('show_on_homepage')->default(false);
            $table->timestamps();
        });

        Schema::create('pages', function (Blueprint $table): void {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('template')->default('standard');
            $table->text('summary')->nullable();
            $table->longText('content')->nullable();
            $table->json('sections')->nullable();
            $table->json('seo')->nullable();
            $table->string('status')->default('draft')->index();
            $table->dateTime('published_at')->nullable();
            $table->timestamps();
        });

        Schema::create('people', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('group_name')->index();
            $table->string('position')->nullable();
            $table->string('department')->nullable();
            $table->text('biography')->nullable();
            $table->string('photo_path')->nullable();
            $table->unsignedInteger('display_order')->default(0);
            $table->boolean('is_published')->default(false);
            $table->timestamps();
        });

        Schema::create('facilities', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('category')->nullable();
            $table->text('description')->nullable();
            $table->string('image_path')->nullable();
            $table->unsignedInteger('display_order')->default(0);
            $table->boolean('is_published')->default(false);
            $table->timestamps();
        });

        Schema::create('gallery_albums', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('cover_image_path')->nullable();
            $table->date('album_date')->nullable();
            $table->string('category')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamps();
        });

        Schema::create('documents', function (Blueprint $table): void {
            $table->id();
            $table->string('title');
            $table->string('category')->index();
            $table->text('description')->nullable();
            $table->string('file_path')->nullable();
            $table->string('file_type')->nullable();
            $table->unsignedBigInteger('download_count')->default(0);
            $table->dateTime('published_at')->nullable();
            $table->dateTime('expires_at')->nullable();
            $table->string('status')->default('draft')->index();
            $table->timestamps();
        });

        Schema::create('general_enquiries', function (Blueprint $table): void {
            $table->id();
            $table->string('full_name');
            $table->string('telephone');
            $table->string('email')->nullable();
            $table->string('subject')->nullable();
            $table->text('message');
            $table->boolean('privacy_consent')->default(false);
            $table->string('status')->default('new')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('general_enquiries');
        Schema::dropIfExists('documents');
        Schema::dropIfExists('gallery_albums');
        Schema::dropIfExists('facilities');
        Schema::dropIfExists('people');
        Schema::dropIfExists('pages');
        Schema::dropIfExists('events');
        Schema::dropIfExists('news_posts');
    }
};
