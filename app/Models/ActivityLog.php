<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    use HasFactory;

    public const ACTION_LOGIN = 'login';

    public const ACTION_LOGOUT = 'logout';

    public const ACTION_AR_OPEN = 'ar_open';

    public const ACTION_VIRTUAL_PATIENT_OPEN = 'virtual_patient_open';

    public const ACTION_SMART_QA_OPEN = 'smart_qa_open';

    public const ACTIONS = [
        self::ACTION_LOGIN,
        self::ACTION_LOGOUT,
        self::ACTION_AR_OPEN,
        self::ACTION_VIRTUAL_PATIENT_OPEN,
        self::ACTION_SMART_QA_OPEN,
    ];

    // Login and logout are surfaced together (one filter option, one dashboard
    // card). This pseudo-action value drives the combined filter; the actions it
    // expands to live in ACTION_GROUP_LOGIN.
    public const FILTER_LOGIN_LOGOUT = 'login_logout';

    public const ACTION_GROUP_LOGIN = [
        self::ACTION_LOGIN,
        self::ACTION_LOGOUT,
    ];

    // Pseudo-action for the filter dropdown only. Selecting it swaps the
    // activity feed for the learning-behaviour table sourced from
    // user_progress_detail (not from this table).
    public const FILTER_LEARNING_BEHAVIOR = 'learning_behavior';

    protected $table = 'activity_logs';

    // Single created_at column — no updated_at, rows are append-only.
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'action',
        'metadata',
        'created_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
