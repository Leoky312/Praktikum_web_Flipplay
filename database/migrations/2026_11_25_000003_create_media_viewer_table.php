<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media_viewer', function (Blueprint $table) {
            $table->id();
            $table->foreignId('viewer_id')->constrained('viewers')->cascadeOnDelete();
            $table->foreignId('media_id')->constrained('media')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['viewer_id', 'media_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media_viewer');
    }
};

