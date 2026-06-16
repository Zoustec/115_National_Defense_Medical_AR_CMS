<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['id' => 1, 'code' => 'StapleFood', 'name' => '主食',  'description' => '白飯/饅頭/麵/稀飯',                'status' => true],
            ['id' => 2, 'code' => 'MainCourse', 'name' => '主菜',  'description' => '烤雞排/滷豬里肌/蒸巴沙魚/炒牛肉',  'status' => true],
            ['id' => 3, 'code' => 'Fruit',      'name' => '水果',  'description' => '蘋果/香蕉/木瓜/珍珠芭樂',          'status' => true],
            ['id' => 4, 'code' => 'SideDish1',  'name' => '副菜1', 'description' => '家常豆包',                          'status' => true],
            ['id' => 5, 'code' => 'SideDish2',  'name' => '副菜2', 'description' => '菠菜',                              'status' => true],
        ];

        foreach ($categories as $data) {
            Category::updateOrCreate(['id' => $data['id']], $data);
        }
    }
}
