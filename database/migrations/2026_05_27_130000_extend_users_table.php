<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('emp_id', 100)->nullable()->unique()->after('hash_id')
                ->comment('External EmpID from HR API — used to look up the user on subsequent SSO logins');
            $table->timestamp('last_login_at')->nullable()->after('is_active')
                ->comment('Last successful login — drives 連續天數 streak');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['emp_id']);
            $table->dropColumn(['emp_id', 'last_login_at']);
        });
    }
};
