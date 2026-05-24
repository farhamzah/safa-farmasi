<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('application_clicks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('portal_application_id')->nullable()->constrained()->nullOnDelete();
            $table->string('application_name')->nullable();
            $table->text('target_url')->nullable();
            $table->timestamp('clicked_at')->index();
            $table->string('ip_hash')->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->timestamps();

            $table->index(['portal_application_id', 'clicked_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('application_clicks');
    }
};
