<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const PRIMARY_ITEMS = [
        ['Home', '/', 'ph-house'],
        ['About Us', '/our-school/about', 'ph-info'],
        ['Resources', '/gallery', 'ph-folder-open'],
        ['Updates', '/updates/news', 'ph-megaphone'],
        ['Academic', '/academics', 'ph-books'],
        ['Levels', '/academics', 'ph-graduation-cap'],
        ['Contact', '/contact', 'ph-phone'],
    ];

    public function up(): void
    {
        Schema::table('navigation_items', function (Blueprint $table): void {
            $table->boolean('is_primary')->default(false)->after('menu_group')->index();
        });

        foreach (self::PRIMARY_ITEMS as $order => [$label, $href, $icon]) {
            DB::table('navigation_items')->updateOrInsert(
                ['menu_group' => 'header', 'label' => $label, 'parent_label' => null],
                [
                    'href' => $href,
                    'icon' => $icon,
                    'display_order' => $order,
                    'is_primary' => true,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            );
        }
    }

    public function down(): void
    {
        DB::table('navigation_items')
            ->where('menu_group', 'header')
            ->where('is_primary', true)
            ->whereNotIn('label', ['Home', 'Contact'])
            ->delete();

        Schema::table('navigation_items', function (Blueprint $table): void {
            $table->dropColumn('is_primary');
        });
    }
};
