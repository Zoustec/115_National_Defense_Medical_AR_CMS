<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserProgressDetail extends Model
{
    use HasFactory, SoftDeletes;

    // 0 = visit (window closed by leaveGroup / pause / complete, or open).
    // 1 = swap (window closed because user committed a switch-item).
    public const STATUS_VISIT = 0;

    public const STATUS_SWAP = 1;

    protected $table = 'user_progress_detail';

    protected $fillable = [
        'user_progress_id',
        'item_id',
        'start_time',
        'end_time',
        'duration',
        'status',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'duration' => 'integer',
        'status' => 'integer',
    ];

    public function progress(): BelongsTo
    {
        return $this->belongsTo(UserProgress::class, 'user_progress_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
