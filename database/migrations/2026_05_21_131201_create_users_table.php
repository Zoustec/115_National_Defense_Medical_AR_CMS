<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('username', 100)->unique();
            $table->string('email', 255)->unique()->nullable();
            $table->string('hash_id', 100)->unique()->nullable();
            $table->tinyInteger('role')->default(0)->comment('0: student, 1: teacher');
            $table->string('cname', 100)->nullable();
            $table->string('unit_label', 255)->nullable();
            $table->string('job_title', 100)->nullable();
            $table->boolean('is_active')->default(true);
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->softDeletes();

            $table->index('role');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
