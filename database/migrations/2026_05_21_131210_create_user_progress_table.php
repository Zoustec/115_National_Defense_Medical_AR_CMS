<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('learning_unit_id')->constrained('learning_units')->cascadeOnDelete();
            $table->tinyInteger('status')->default(0)->comment('0: in_progress, 1: completed');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'learning_unit_id']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_progress');
    }
};
