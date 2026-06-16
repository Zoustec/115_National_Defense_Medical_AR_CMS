<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('learning_units', function (Blueprint $table) {
            $table->text('dietary_recommendations')->nullable()->after('applicable_objects');
            $table->text('clinical_notes')->nullable()->after('dietary_recommendations');
        });
    }

    public function down(): void
    {
        Schema::table('learning_units', function (Blueprint $table) {
            $table->dropColumn(['dietary_recommendations', 'clinical_notes']);
        });
    }
};
