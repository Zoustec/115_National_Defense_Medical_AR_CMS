<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Item;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

/**
 * Seeds base food items (item_type=0 equivalent) that appear as AR chips.
 * Order within a category MUST match AR model-config.json (display_order = selectedIndex).
 *
 * Each item's emoji icon (rendered from AR model-config.json) lives under
 * public/icons/foods/food-{model}.png. The seeder copies it onto the public
 * storage disk at items/{id}/... — exactly like an admin upload — so the stored
 * image path and URL behave identically to user-uploaded images.
 */
class ItemSeeder extends Seeder
{
    /** Source folder for the seed icons (web path under public/). */
    private const ICON_SOURCE_DIR = 'icons/foods';

    public function run(): void
    {
        $items = [
            // StapleFood (category_id=1) — unit=4
            ['id' => 101, 'model' => 'rice',       'category_id' => 1, 'name' => '乾飯', 'unit' => 4, 'display_order' => 0, 'status' => true],
            ['id' => 102, 'model' => 'water_rice', 'category_id' => 1, 'name' => '稀飯', 'unit' => 4, 'display_order' => 1, 'status' => true],
            ['id' => 103, 'model' => 'noodle',     'category_id' => 1, 'name' => '麵食', 'unit' => 4, 'display_order' => 2, 'status' => true],
            ['id' => 104, 'model' => 'bun',        'category_id' => 1, 'name' => '饅頭', 'unit' => 4, 'display_order' => 3, 'status' => true],

            // MainCourse (category_id=2) — unit=2
            ['id' => 201, 'model' => 'chicken', 'category_id' => 2, 'name' => '烤雞排',   'unit' => 2, 'display_order' => 0, 'status' => true],
            ['id' => 202, 'model' => 'fish',    'category_id' => 2, 'name' => '蒸巴沙魚', 'unit' => 2, 'display_order' => 1, 'status' => true],
            ['id' => 203, 'model' => 'pork',    'category_id' => 2, 'name' => '滷豬里肌', 'unit' => 2, 'display_order' => 2, 'status' => true],
            ['id' => 204, 'model' => 'beef',    'category_id' => 2, 'name' => '炒牛肉',   'unit' => 2, 'display_order' => 3, 'status' => true],

            // Fruit (category_id=3) — unit=1
            ['id' => 301, 'model' => 'apple',  'category_id' => 3, 'name' => '蘋果',     'unit' => 1, 'display_order' => 0, 'status' => true],
            ['id' => 302, 'model' => 'banana', 'category_id' => 3, 'name' => '香蕉',     'unit' => 1, 'display_order' => 1, 'status' => true],
            ['id' => 303, 'model' => 'papaya', 'category_id' => 3, 'name' => '木瓜',     'unit' => 1, 'display_order' => 2, 'status' => true],
            ['id' => 304, 'model' => 'guava',  'category_id' => 3, 'name' => '珍珠芭樂', 'unit' => 1, 'display_order' => 3, 'status' => true],

            // Fixed side dishes (no swap)
            ['id' => 401, 'model' => 'bean',    'category_id' => 4, 'name' => '家常豆包', 'unit' => 1, 'display_order' => 0, 'status' => true],
            ['id' => 501, 'model' => 'spinach', 'category_id' => 5, 'name' => '菠菜',     'unit' => 1, 'display_order' => 0, 'status' => true],
        ];

        foreach ($items as $data) {
            $data['image'] = $this->publishIcon($data['id'], $data['model']);

            Item::updateOrCreate(['id' => $data['id']], $data);
        }
    }

    /**
     * Copy public/icons/foods/food-{model}.png onto the public storage disk at
     * items/{id}/food-{model}.png, mirroring how uploaded images are stored, and
     * return the disk-relative path to save on the item. Idempotent: re-running
     * overwrites the same target file (no random hash, so no orphan build-up).
     */
    private function publishIcon(int $itemId, string $model): ?string
    {
        $filename = "food-{$model}.png";
        $sourcePath = public_path(self::ICON_SOURCE_DIR . '/' . $filename);

        if (! is_file($sourcePath)) {
            return null;
        }

        $targetPath = "items/{$itemId}/{$filename}";
        Storage::disk('public')->put($targetPath, file_get_contents($sourcePath));

        return $targetPath;
    }
}
