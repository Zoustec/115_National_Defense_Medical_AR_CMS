<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * AR is now a single build (`/ar/115_HealthyPlate/`) that loads a per-unit
 * model config JSON named after `learning_units.code` (e.g. `#1_Diabetes`
 * → `public/ar/#1_Diabetes.json` on the frontend). `3d_path` is therefore
 * obsolete: the FE derives everything from `code`.
 *
 * Codes are renamed to match the AR config files delivered by the client
 * (frontend `public/ar/#N_*.json`).
 */
return new class extends Migration
{
    /** old code => new code (AR config name without `.json`). */
    private const CODE_MAP = [
        '115_HealthyPlate' => '#1_Diabetes',
        'CARDIO'           => '#2_Cardiovascular',
        'DYSPHAGIA'        => '#3_Dysphagia',
        'LIVER'            => '#4_LiverFailure',
        'GOUT'             => '#5_Epidemic',
        'CANCER'           => '#6_Cancer',
        'KIDNEY'           => '#7_Kidney',
    ];

    public function up(): void
    {
        foreach (self::CODE_MAP as $old => $new) {
            DB::table('learning_units')->where('code', $old)->update(['code' => $new]);
        }

        Schema::table('learning_units', function (Blueprint $table) {
            $table->dropColumn('3d_path');
        });
    }

    public function down(): void
    {
        Schema::table('learning_units', function (Blueprint $table) {
            $table->string('3d_path', 255)->nullable()->after('clinical_notes');
        });

        foreach (self::CODE_MAP as $old => $new) {
            DB::table('learning_units')->where('code', $new)->update(['code' => $old]);
        }

        // Only unit #1 ever had an AR build path before this migration.
        DB::table('learning_units')
            ->where('code', '115_HealthyPlate')
            ->update(['3d_path' => '/ar/115_HealthyPlate/']);
    }
};
