<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('learning_units', function (Blueprint $table) {
            $table->string('code', 50)->unique()->after('name');
            $table->string('dietary_recommendation_title')->nullable()->after('applicable_objects');
            $table->string('clinical_note_title')->nullable()->after('dietary_recommendations');
            $table->integer('sort_order')->default(0)->after('is_locked');
            $table->string('3d_path', 255)->nullable()->after('clinical_notes');
        });
    }

    public function down(): void
    {
        Schema::table('learning_units', function (Blueprint $table) {
            $table->dropUnique(['code']);
            $table->dropColumn([
                'code',
                'dietary_recommendation_title',
                'clinical_note_title',
                'sort_order',
                '3d_path',
            ]);
        });
    }
};
