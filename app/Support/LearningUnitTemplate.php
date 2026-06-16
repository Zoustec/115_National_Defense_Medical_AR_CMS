<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\LearningUnitItem;
use App\Models\LearningUnitRecommendItem;
use Illuminate\Support\Collection;

/**
 * Single source of truth for the default learning-unit composition
 * (the 糖尿病飲食 sample). Used by both LearningUnitSeeder (to seed unit #1)
 * and the create form (to pre-fill new units), so a new unit always starts
 * from the same item/recommend set as the seeder — independent of any edits
 * a user may have made to the seeded unit.
 */
class LearningUnitTemplate
{
    /** Item ids flagged as the default plate composition. */
    public const DEFAULT_ITEM_IDS = [101, 201, 301, 401, 501];

    /** All item ids linked to a fresh unit (4 staple + 4 main + 4 fruit + 1 bean + 1 spinach). */
    public const ITEM_IDS = [
        101, 102, 103, 104,
        201, 202, 203, 204,
        301, 302, 303, 304,
        401, 501,
    ];

    /**
     * Recommend-item links: recommend_item_id => column (1=Staple, 2=Main, 3=Fruit),
     * weight (numeric|null) and unit_text (raw label).
     *
     * @return array<int, array{recommend_item_id:int, column:int, weight:int|null, unit_text:string}>
     */
    public static function recommendLinks(): array
    {
        return [
            // Staple (column=1)
            ['recommend_item_id' => 1001, 'column' => 1, 'weight' => 100, 'unit_text' => '公克'],
            ['recommend_item_id' => 1002, 'column' => 1, 'weight' => 80,  'unit_text' => '公克(12匙)'],
            ['recommend_item_id' => 1003, 'column' => 1, 'weight' => 220, 'unit_text' => '公克(2個)'],
            ['recommend_item_id' => 1004, 'column' => 1, 'weight' => 220, 'unit_text' => '公克(⅘個)'],
            ['recommend_item_id' => 1005, 'column' => 1, 'weight' => 120, 'unit_text' => '公克(2片)'],
            ['recommend_item_id' => 1006, 'column' => 1, 'weight' => 200, 'unit_text' => '公克(4塊)'],
            ['recommend_item_id' => 1007, 'column' => 1, 'weight' => 340, 'unit_text' => '公克'],
            ['recommend_item_id' => 1008, 'column' => 1, 'weight' => 100, 'unit_text' => '公克(8匙)'],
            ['recommend_item_id' => 1009, 'column' => 1, 'weight' => 120, 'unit_text' => '公克(12張)'],
            ['recommend_item_id' => 1010, 'column' => 1, 'weight' => 340, 'unit_text' => '公克(2 ⅔ 根)'],
            ['recommend_item_id' => 1011, 'column' => 1, 'weight' => 360, 'unit_text' => '公克'],
            ['recommend_item_id' => 1012, 'column' => 1, 'weight' => 100, 'unit_text' => '公克(8匙)'],

            // Main course (column=2)
            ['recommend_item_id' => 2001, 'column' => 2, 'weight' => 100, 'unit_text' => '公克(12隻)'],
            ['recommend_item_id' => 2002, 'column' => 2, 'weight' => 70,  'unit_text' => '公克'],
            ['recommend_item_id' => 2003, 'column' => 2, 'weight' => 100, 'unit_text' => '公克'],
            ['recommend_item_id' => 2004, 'column' => 2, 'weight' => 140, 'unit_text' => '公克(1塊)'],
            ['recommend_item_id' => 2005, 'column' => 2, 'weight' => 320, 'unit_text' => '公克(中40個)'],
            ['recommend_item_id' => 2006, 'column' => 2, 'weight' => 80,  'unit_text' => '公克(1隻)'],
            ['recommend_item_id' => 2007, 'column' => 2, 'weight' => 380, 'unit_text' => 'cc'],
            ['recommend_item_id' => 2008, 'column' => 2, 'weight' => 160, 'unit_text' => '公克'],
            ['recommend_item_id' => 2009, 'column' => 2, 'weight' => 70,  'unit_text' => '公克'],
            ['recommend_item_id' => 2010, 'column' => 2, 'weight' => 110, 'unit_text' => '公克(2個)'],
            ['recommend_item_id' => 2011, 'column' => 2, 'weight' => 70,  'unit_text' => '公克(3塊)'],
            ['recommend_item_id' => 2012, 'column' => 2, 'weight' => 280, 'unit_text' => '公克(2盒)'],

            // Fruit (column=3) — many fruits use count-only display, weight may be null
            ['recommend_item_id' => 3001, 'column' => 3, 'weight' => null, 'unit_text' => '1個'],
            ['recommend_item_id' => 3002, 'column' => 3, 'weight' => null, 'unit_text' => '⅔個'],
            ['recommend_item_id' => 3003, 'column' => 3, 'weight' => null, 'unit_text' => '1個'],
            ['recommend_item_id' => 3004, 'column' => 3, 'weight' => 140,  'unit_text' => '公克(2顆)'],
            ['recommend_item_id' => 3005, 'column' => 3, 'weight' => null, 'unit_text' => '1個(3個/斤)'],
            ['recommend_item_id' => 3006, 'column' => 3, 'weight' => null, 'unit_text' => '1片(半斤)'],
            ['recommend_item_id' => 3007, 'column' => 3, 'weight' => 80,   'unit_text' => '公克(9個)'],
            ['recommend_item_id' => 3008, 'column' => 3, 'weight' => null, 'unit_text' => '2個'],
            ['recommend_item_id' => 3009, 'column' => 3, 'weight' => null, 'unit_text' => '¼個'],
            ['recommend_item_id' => 3010, 'column' => 3, 'weight' => 220,  'unit_text' => '公克(23顆)'],
            ['recommend_item_id' => 3011, 'column' => 3, 'weight' => null, 'unit_text' => '13粒'],
            ['recommend_item_id' => 3012, 'column' => 3, 'weight' => 110,  'unit_text' => '公克'],
        ];
    }

    /**
     * Item assignments pre-filled for the create form, keyed by item_id and
     * shaped like LearningUnitService::getItemAssignments() (each carries is_default).
     */
    public static function itemAssignments(): Collection
    {
        return collect(self::ITEM_IDS)->mapWithKeys(fn (int $itemId) => [
            $itemId => new LearningUnitItem([
                'item_id' => $itemId,
                'is_default' => in_array($itemId, self::DEFAULT_ITEM_IDS, true),
            ]),
        ]);
    }

    /**
     * Recommend assignments pre-filled for the create form, keyed by
     * recommend_item_id and shaped like LearningUnitService::getRecommendAssignments().
     */
    public static function recommendAssignments(): Collection
    {
        return collect(self::recommendLinks())->mapWithKeys(fn (array $link) => [
            $link['recommend_item_id'] => new LearningUnitRecommendItem([
                'recommend_item_id' => $link['recommend_item_id'],
                'column' => $link['column'],
                'weight' => $link['weight'],
                'unit_text' => $link['unit_text'],
            ]),
        ]);
    }
}
