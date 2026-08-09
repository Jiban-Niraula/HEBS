<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hero_slides', function (Blueprint $table): void {
            $table->id();
            $table->string('kicker')->nullable();
            $table->string('title');
            $table->text('text')->nullable();
            $table->string('image_path');
            $table->unsignedInteger('display_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('navigation_items', function (Blueprint $table): void {
            $table->id();
            $table->string('menu_group')->default('header')->index();
            $table->string('parent_label')->nullable()->index();
            $table->string('label');
            $table->string('href');
            $table->string('icon')->nullable();
            $table->unsignedInteger('display_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('pages', function (Blueprint $table): void {
            $table->string('eyebrow')->nullable()->after('template');
            $table->string('image_path')->nullable()->after('content');
        });
    }

    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table): void {
            $table->dropColumn(['eyebrow', 'image_path']);
        });
        Schema::dropIfExists('navigation_items');
        Schema::dropIfExists('hero_slides');
    }
};
