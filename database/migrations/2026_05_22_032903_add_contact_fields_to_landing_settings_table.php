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
        Schema::table('landing_settings', function (Blueprint $table) {
            $table->string('hero_title')->nullable()->after('headline');
            $table->text('hero_description')->nullable()->after('hero_title');
            $table->string('contact_email')->nullable()->after('footer_text');
            $table->string('contact_whatsapp')->nullable()->after('contact_email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('landing_settings', function (Blueprint $table) {
            $table->dropColumn([
                'hero_title',
                'hero_description',
                'contact_email',
                'contact_whatsapp',
            ]);
        });
    }
};
