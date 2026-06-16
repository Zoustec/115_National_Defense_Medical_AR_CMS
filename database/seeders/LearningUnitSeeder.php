<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\LearningUnit;
use App\Models\LearningUnitItem;
use App\Models\LearningUnitRecommendItem;
use App\Support\LearningUnitTemplate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * Seeds the diet learning units (lessons) and their item links.
 * Every unit gets the same composition (via LearningUnitTemplate):
 * - learning_unit_items: 14 items (4 staple + 4 main + 4 fruit + 1 bean + 1 spinach).
 *   is_default = true for the 5 items shown in default plate composition.
 * - learning_unit_recommend_items: 36 entries (12 per swappable group × 3).
 *
 * Unit #1 (糖尿病飲食) is the fully-detailed, unlocked AR lesson. Units #2–#7 are
 * locked placeholders carrying only name / code / cover image.
 */
class LearningUnitSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->units() as $data) {
            $unit = LearningUnit::updateOrCreate(
                ['id' => $data['id']],
                array_merge($data['attributes'], [
                    'image' => $this->publishImage($data['id'], $data['image']),
                ])
            );

            $this->syncComposition($unit->id);
        }
    }

    /**
     * @return array<int, array{id:int, image:string, attributes:array<string, mixed>}>
     */
    private function units(): array
    {
        $dietaryRecommendations = '<ul><li>糖尿病是因為體內胰島素分泌不正常或功能減低，導致血糖過高。&nbsp;</li><li>血糖可藉由飲食、運動、藥物三方面配合控制，如果飲食、運動、藥物三方面控制得當，藥物用量可以減少，臨床症狀往往能改善許多，並且預防不良的合併症。&nbsp;</li><li>糖尿病飲食是均衡、健康的。飲食中已不再區分「醣類」的來源是否為複合糖或精緻糖，而是強調總醣量的攝取。</li><li>&nbsp;醣類分別來自六大類食物中的全穀雜糧類、奶類及水果類，應能依餐次做適當的分配，使血糖趨於平穩。</li></ul>';
        $clinicalNotes = '<p>糖尿病飲食是均衡的、健康的，全家人都可以配合一起享用，但唯一要注意的就是最好將所能吃的份量先撥在自助餐盤中，以免與家人談笑間不知不覺吃過量。</p>';

        return [
            // Fully-detailed, unlocked AR lesson.
            [
                'id' => 1,
                'image' => 'diet-for-diabetes.png',
                'attributes' => [
                    'name' => '糖尿病飲食',
                    // Code doubles as the AR model-config name: the FE loads
                    // `public/ar/{code}.json` into the shared AR build.
                    'code' => '#1_Diabetes',
                    'description' => '認識糖尿病飲食原則，學習六大類食物的代換與份量控制。',
                    'applicable_objects' => ['糖尿病患者', '血糖值異常'],
                    'dietary_recommendation_title' => '糖尿病飲食治療原則',
                    'dietary_recommendations' => $dietaryRecommendations,
                    'clinical_note_title' => '臨床小提醒',
                    'clinical_notes' => $clinicalNotes,
                    'status' => true,
                    'is_locked' => false,
                ],
            ],

            // Locked placeholders: name + code + cover only.
            ['id' => 2, 'image' => 'diet-for-cardiovascular-disease.png', 'attributes' => ['name' => '心血管疾病飲食', 'code' => '#2_Cardiovascular', 'status' => true, 'is_locked' => true]],
            ['id' => 3, 'image' => 'diet-for-dysphagia.png',             'attributes' => ['name' => '呑嚥困難飲食',   'code' => '#3_Dysphagia',      'status' => true, 'is_locked' => true]],
            ['id' => 4, 'image' => 'diet-for-liver-failure.png',         'attributes' => ['name' => '肝衰竭飲食',     'code' => '#4_LiverFailure',   'status' => true, 'is_locked' => true]],
            ['id' => 5, 'image' => 'diet-for-gout.png',                  'attributes' => ['name' => '病風飲食',       'code' => '#5_Epidemic',       'status' => true, 'is_locked' => true]],
            ['id' => 6, 'image' => 'diet-for-cancer-patients.png',       'attributes' => ['name' => '癌症飲食',       'code' => '#6_Cancer',         'status' => true, 'is_locked' => true]],
            ['id' => 7, 'image' => 'diet-for-kidney-disease.png',        'attributes' => ['name' => '腎臟病飲食',     'code' => '#7_Kidney',         'status' => true, 'is_locked' => true]],
        ];
    }

    /**
     * Attach the shared item / recommend-item composition to a unit
     * (re-syncs on every run so the links stay consistent with the template).
     */
    private function syncComposition(int $unitId): void
    {
        // Map all 14 items to the unit. is_default flags the 5 plate items.
        // Default composition: 乾飯(101), 烤雞排(201), 蘋果(301), 家常豆包(401), 菠菜(501)
        DB::table('learning_unit_items')->where('learning_unit_id', $unitId)->delete();
        foreach (LearningUnitTemplate::ITEM_IDS as $itemId) {
            LearningUnitItem::create([
                'learning_unit_id' => $unitId,
                'item_id' => $itemId,
                'is_default' => in_array($itemId, LearningUnitTemplate::DEFAULT_ITEM_IDS, true),
            ]);
        }

        // Replacement pool per group: weight (numeric) + unit_text (raw label).
        // column: 1=Staple, 2=MainCourse, 3=Fruit
        DB::table('learning_unit_recommend_items')->where('learning_unit_id', $unitId)->delete();
        foreach (LearningUnitTemplate::recommendLinks() as $link) {
            LearningUnitRecommendItem::create(array_merge($link, [
                'learning_unit_id' => $unitId,
            ]));
        }
    }

    /**
     * Copy public/icons/units/{filename} onto the public storage disk at
     * learning-units/{id}/{filename}, mirroring how uploaded covers are stored,
     * and return the disk-relative path (or null if the source is missing).
     * Idempotent: re-running overwrites the same target file.
     */
    private function publishImage(int $unitId, string $filename): ?string
    {
        $sourcePath = public_path("icons/units/{$filename}");

        if (! is_file($sourcePath)) {
            return null;
        }

        $targetPath = "learning-units/{$unitId}/{$filename}";
        Storage::disk('public')->put($targetPath, file_get_contents($sourcePath));

        return $targetPath;
    }
}
