<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('navigation_items')->updateOrInsert(
            ['menu_group' => 'header', 'href' => '/'],
            [
                'parent_label' => null,
                'label' => 'Home',
                'icon' => 'ph-house',
                'display_order' => 0,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        );
    }

    public function down(): void
    {
        DB::table('navigation_items')
            ->where('menu_group', 'header')
            ->where('href', '/')
            ->delete();
    }
};
