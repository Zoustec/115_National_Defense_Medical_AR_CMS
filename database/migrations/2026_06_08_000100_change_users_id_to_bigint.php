<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Đổi users.id (và các user_id liên quan) từ UUID char(36) sang BIGINT
 * AUTO_INCREMENT. Migration chỉ đổi KIỂU cột; việc chuyển dữ liệu UUID hiện có
 * sang số được xử lý riêng bằng database/sql/convert_users_id_to_bigint.sql.
 *
 * Phải gỡ khoá ngoại trước khi đổi kiểu PK đang được tham chiếu, rồi tạo lại.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });
        Schema::table('reminders', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });
        Schema::table('user_progress', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->bigIncrements('id')->change();
        });

        Schema::table('activity_logs', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->change();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
        Schema::table('reminders', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->change();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
        Schema::table('user_progress', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->change();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });
        Schema::table('reminders', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });
        Schema::table('user_progress', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->uuid('id')->change();
        });

        Schema::table('activity_logs', function (Blueprint $table) {
            $table->uuid('user_id')->change();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
        Schema::table('reminders', function (Blueprint $table) {
            $table->uuid('user_id')->change();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
        Schema::table('user_progress', function (Blueprint $table) {
            $table->uuid('user_id')->change();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }
};
