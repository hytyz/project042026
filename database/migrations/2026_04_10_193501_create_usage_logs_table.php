<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usage_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('action'); // e.g., 'generate', 'save_set', 'view_set'
            $table->json('metadata')->nullable(); // Store additional context
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usage_logs');
    }
};
