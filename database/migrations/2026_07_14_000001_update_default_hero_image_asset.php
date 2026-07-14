<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('landing_settings')
            ->where('key', 'hero_image_url')
            ->where('value', '/images/hero-farmasi-default.svg')
            ->update([
                'value' => '/images/hero-farmasi-lab.webp',
                'updated_at' => now(),
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('landing_settings')
            ->where('key', 'hero_image_url')
            ->where('value', '/images/hero-farmasi-lab.webp')
            ->update([
                'value' => '/images/hero-farmasi-default.svg',
                'updated_at' => now(),
            ]);
    }
};
