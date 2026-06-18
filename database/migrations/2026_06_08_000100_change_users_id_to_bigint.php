<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Change users.id (and the related user_id columns) from UUID char(36) to BIGINT
 * AUTO_INCREMENT. This migration only changes the column TYPE; converting existing
 * UUID data into numbers is handled separately by database/sql/convert_users_id_to_bigint.sql.
 *
 * Foreign keys must be dropped before changing the referenced PK type, then recreated.
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
