<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\RecommendItem;
use Illuminate\Database\Seeder;

/**
 * Seeds the 12-item exchange pool per swappable food group (NDMA spec §3.1).
 * Source: 0508 單位提交食物代換表.
 */
class RecommendItemSeeder extends Seeder
{
    public function run(): void
    {
        $recommends = [
            // Staple (category_id=1)
            ['id' => 1001, 'category_id' => 1, 'name' => '麵線(乾)',   'status' => true],
            ['id' => 1002, 'category_id' => 1, 'name' => '麥片',       'status' => true],
            ['id' => 1003, 'category_id' => 1, 'name' => '蕃薯(小)',   'status' => true],
            ['id' => 1004, 'category_id' => 1, 'name' => '芋頭',       'status' => true],
            ['id' => 1005, 'category_id' => 1, 'name' => '吐司(小)',   'status' => true],
            ['id' => 1006, 'category_id' => 1, 'name' => '蓮藕糕(小)', 'status' => true],
            ['id' => 1007, 'category_id' => 1, 'name' => '南瓜',       'status' => true],
            ['id' => 1008, 'category_id' => 1, 'name' => '綠豆',       'status' => true],
            ['id' => 1009, 'category_id' => 1, 'name' => '餃子皮',     'status' => true],
            ['id' => 1010, 'category_id' => 1, 'name' => '玉米',       'status' => true],
            ['id' => 1011, 'category_id' => 1, 'name' => '馬鈴薯',     'status' => true],
            ['id' => 1012, 'category_id' => 1, 'name' => '紅豆',       'status' => true],

            // Main course (category_id=2)
            ['id' => 2001, 'category_id' => 2, 'name' => '蝦仁',       'status' => true],
            ['id' => 2002, 'category_id' => 2, 'name' => '牛腱',       'status' => true],
            ['id' => 2003, 'category_id' => 2, 'name' => '毛豆',       'status' => true],
            ['id' => 2004, 'category_id' => 2, 'name' => '黃豆干',     'status' => true],
            ['id' => 2005, 'category_id' => 2, 'name' => '蛤蜊',       'status' => true],
            ['id' => 2006, 'category_id' => 2, 'name' => '棒棒腿',     'status' => true],
            ['id' => 2007, 'category_id' => 2, 'name' => '無糖豆漿',   'status' => true],
            ['id' => 2008, 'category_id' => 2, 'name' => '豆腐',       'status' => true],
            ['id' => 2009, 'category_id' => 2, 'name' => '豬大里肌',   'status' => true],
            ['id' => 2010, 'category_id' => 2, 'name' => '雞蛋',       'status' => true],
            ['id' => 2011, 'category_id' => 2, 'name' => '五香豆干',   'status' => true],
            ['id' => 2012, 'category_id' => 2, 'name' => '嫩豆腐',     'status' => true],

            // Fruit (category_id=3)
            ['id' => 3001, 'category_id' => 3, 'name' => '柳丁',       'status' => true],
            ['id' => 3002, 'category_id' => 3, 'name' => '香瓜',       'status' => true],
            ['id' => 3003, 'category_id' => 3, 'name' => '梨(小)',     'status' => true],
            ['id' => 3004, 'category_id' => 3, 'name' => '百香果',     'status' => true],
            ['id' => 3005, 'category_id' => 3, 'name' => '橘子',       'status' => true],
            ['id' => 3006, 'category_id' => 3, 'name' => '西瓜',       'status' => true],
            ['id' => 3007, 'category_id' => 3, 'name' => '櫻桃',       'status' => true],
            ['id' => 3008, 'category_id' => 3, 'name' => '蓮霧',       'status' => true],
            ['id' => 3009, 'category_id' => 3, 'name' => '哈密瓜',     'status' => true],
            ['id' => 3010, 'category_id' => 3, 'name' => '聖女蕃茄',   'status' => true],
            ['id' => 3011, 'category_id' => 3, 'name' => '葡萄',       'status' => true],
            ['id' => 3012, 'category_id' => 3, 'name' => '火龍果',     'status' => true],
        ];

        foreach ($recommends as $data) {
            RecommendItem::updateOrCreate(['id' => $data['id']], $data);
        }
    }
}
