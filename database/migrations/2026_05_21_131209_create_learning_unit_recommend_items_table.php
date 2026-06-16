<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('learning_unit_recommend_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('learning_unit_id')->constrained('learning_units')->cascadeOnDelete();
            $table->foreignId('recommend_item_id')->constrained('recommend_items')->restrictOnDelete();
            $table->tinyInteger('column')->comment('1: StapleFood, 2: MainCourse, 3: Fruit');
            $table->decimal('weight', 10, 2)->nullable();
            $table->string('unit_text', 100)->nullable()
                ->comment('Raw label from master sheet: 公克, 片, 1個 (3個/斤), 中20個');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['learning_unit_id', 'column']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('learning_unit_recommend_items');
    }
};
