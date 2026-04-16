<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('flashcard_set_tags', function (Blueprint $table) {
            $table->id();
            $table->foreignId('flashcard_set_id')->constrained()->onDelete('cascade');
            $table->foreignId('tag_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['flashcard_set_id', 'tag_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('flashcard_set_tags');
    }
};
