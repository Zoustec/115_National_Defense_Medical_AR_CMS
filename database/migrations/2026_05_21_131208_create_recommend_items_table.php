<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recommend_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->restrictOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('status')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->index('category_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recommend_items');
    }
};
