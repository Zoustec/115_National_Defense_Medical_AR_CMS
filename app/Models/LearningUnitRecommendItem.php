<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class LearningUnitRecommendItem extends Model
{
    use HasFactory, SoftDeletes;

    public const COLUMN_STAPLE = 1;

    public const COLUMN_MAIN = 2;

    public const COLUMN_FRUIT = 3;

    protected $fillable = [
        'learning_unit_id',
        'recommend_item_id',
        'column',
        'weight',
        'unit_text',
    ];

    protected $casts = [
        'column' => 'integer',
        'weight' => 'decimal:2',
    ];

    public function learningUnit(): BelongsTo
    {
        return $this->belongsTo(LearningUnit::class);
    }

    public function recommendItem(): BelongsTo
    {
        return $this->belongsTo(RecommendItem::class);
    }
}
