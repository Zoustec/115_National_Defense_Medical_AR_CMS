<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reminders', function (Blueprint $table) {
            $table->id();
            // One active reminder per student — a teacher pressing 提醒 again
            // overwrites the row (upsert on user_id), so the student banner
            // never stacks. UNIQUE enforces that at the DB level.
            $table->foreignUuid('user_id')->unique()->constrained('users')->cascadeOnDelete();
            // Snapshot of how many days the student had been inactive when the
            // reminder was sent, so the banner text ("您已 N 天未登入") is stable
            // even if the student becomes active right after.
            $table->unsignedInteger('days_inactive')->default(0);
            // Drives the banner's short-lived visibility window (the API only
            // returns a reminder whose reminded_at is within the last minute).
            $table->timestamp('reminded_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reminders');
    }
};
