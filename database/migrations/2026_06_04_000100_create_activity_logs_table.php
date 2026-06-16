<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('action', 50)
                ->comment('login, logout, ar_open, virtual_patient_open, smart_qa_open');
            $table->json('metadata')->nullable()
                ->comment('Optional context — e.g. learning_unit_id for ar_open');
            $table->timestamp('created_at')->useCurrent();

            $table->index(['user_id', 'action']);
            $table->index(['action', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
