<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Item extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'model',
        'category_id',
        'name',
        'image',
        'description',
        'unit',
        'display_order',
        'status',
    ];

    protected $casts = [
        'unit' => 'integer',
        'display_order' => 'integer',
        'status' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function learningUnits(): BelongsToMany
    {
        return $this->belongsToMany(LearningUnit::class, 'learning_unit_items')
            ->withPivot('is_default');
    }

    protected function imageUrl(): Attribute
    {
        return Attribute::get(function () {
            if (! $this->image) {
                return null;
            }

            // Seeded icons are static files served straight from public/
            // (e.g. /icons/foods/food-rice.png); uploaded images are relative
            // paths on the public storage disk and need Storage::url().
            return str_starts_with($this->image, '/')
                ? $this->image
                : Storage::url($this->image);
        });
    }
}
