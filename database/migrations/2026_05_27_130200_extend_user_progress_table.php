<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_progress', function (Blueprint $table) {
            $table->unsignedInteger('session_no')->default(1)->after('learning_unit_id');
            $table->integer('swap_count')->default(0)->after('status')
                ->comment('Total swap actions across this attempt — drives Course Complete pace.swapCount');
            $table->boolean('demo_seen')->default(false)->after('swap_count');
            $table->timestamp('completed_at')->nullable()->after('updated_at');
            $table->unique(['user_id', 'learning_unit_id', 'session_no'], 'user_progress_user_unit_session_unique');
        });
    }

    public function down(): void
    {
        Schema::table('user_progress', function (Blueprint $table) {
            $table->dropUnique('user_progress_user_unit_session_unique');
            $table->dropColumn(['session_no', 'swap_count', 'demo_seen', 'completed_at']);
        });
    }
};
