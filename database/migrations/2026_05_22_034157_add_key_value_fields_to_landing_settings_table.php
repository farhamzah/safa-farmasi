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
            $table->string('key')->nullable()->after('id');
            $table->string('group')->default('general')->after('key');
            $table->string('type')->default('text')->after('group');
            $table->text('value')->nullable()->after('type');
            $table->unique('key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('landing_settings', function (Blueprint $table) {
            $table->dropUnique(['key']);
            $table->dropColumn(['key', 'group', 'type', 'value']);
        });
    }
};
