<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LearningUnitItem extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'learning_unit_id',
        'item_id',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function learningUnit(): BelongsTo
    {
        return $this->belongsTo(LearningUnit::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
