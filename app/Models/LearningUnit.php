<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class LearningUnit extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'image',
        'description',
        'applicable_objects',
        'dietary_recommendation_title',
        'dietary_recommendations',
        'clinical_note_title',
        'clinical_notes',
        'status',
        'is_locked',
        'sort_order',
    ];

    protected $casts = [
        'applicable_objects' => 'array',
        'status' => 'boolean',
        'is_locked' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected function imageUrl(): Attribute
    {
        return Attribute::get(fn () => $this->image ? Storage::url($this->image) : null);
    }

    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Item::class, 'learning_unit_items')
            ->withPivot('is_default');
    }

    public function defaultItems(): BelongsToMany
    {
        return $this->items()->wherePivot('is_default', true);
    }

    public function recommendItems(): HasMany
    {
        return $this->hasMany(LearningUnitRecommendItem::class);
    }

    public function userProgress(): HasMany
    {
        return $this->hasMany(UserProgress::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }
}
