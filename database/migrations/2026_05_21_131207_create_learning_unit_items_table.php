<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('learning_unit_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('learning_unit_id')->constrained('learning_units')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('items')->restrictOnDelete();
            $table->boolean('is_default')->default(false)
                ->comment('true: item belongs to default plate composition; false: swap option');

            $table->unique(['learning_unit_id', 'item_id']);
            $table->index(['learning_unit_id', 'is_default']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('learning_unit_items');
    }
};
