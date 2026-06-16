<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('model')->comment('Item code / AR option key, e.g. rice, water_rice, chicken');
            $table->foreignId('category_id')->constrained('categories')->restrictOnDelete();
            $table->string('name');
            $table->string('image')->nullable();
            $table->text('description')->nullable();
            $table->integer('unit')->default(1)->comment('Portion count: 4 staple / 2 main / 1 fruit');
            $table->integer('display_order')->default(0)->comment('AR selectedIndex 0..N maps 1:1 to this');
            $table->boolean('status')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['category_id', 'display_order']);
            $table->index('model');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
