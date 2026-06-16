<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserProgress extends Model
{
    use HasFactory, SoftDeletes;

    public const STATUS_IN_PROGRESS = 0;

    public const STATUS_COMPLETED = 1;

    protected $table = 'user_progress';

    protected $fillable = [
        'user_id',
        'learning_unit_id',
        'session_no',
        'status',
        'demo_seen',
        'completed_at',
    ];

    protected $casts = [
        'session_no' => 'integer',
        'status' => 'integer',
        'demo_seen' => 'boolean',
        'completed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function learningUnit(): BelongsTo
    {
        return $this->belongsTo(LearningUnit::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(UserProgressDetail::class);
    }
}
