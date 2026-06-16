<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_progress_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_progress_id')->constrained('user_progress')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('items')->restrictOnDelete();
            $table->timestamp('start_time')->nullable();
            $table->timestamp('end_time')->nullable();
            $table->bigInteger('duration')->default(0)->comment('Seconds — drives food focus aggregates');
            $table->tinyInteger('status')->default(0)->comment('0: in_progress, 1: completed');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_progress_id', 'item_id']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_progress_detail');
    }
};
