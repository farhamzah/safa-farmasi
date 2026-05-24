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
        Schema::create('portal_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('app_category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('short_name')->nullable();
            $table->text('description')->nullable();
            $table->string('thumbnail_path')->nullable();
            $table->string('url')->nullable();
            $table->string('status')->default('inactive');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('open_in_new_tab')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->timestamps();

            $table->index(['status', 'sort_order']);
            $table->index(['is_featured', 'sort_order']);
        });

        Schema::create('app_category_portal_application', function (Blueprint $table) {
            $table->id();
            $table->foreignId('app_category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('portal_application_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['app_category_id', 'portal_application_id'], 'category_application_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_category_portal_application');
        Schema::dropIfExists('portal_applications');
    }
};
