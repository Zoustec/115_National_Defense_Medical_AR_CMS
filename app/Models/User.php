<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Model
{
    use HasFactory, SoftDeletes;

    public const int ROLE_STUDENT = 0;

    public const int ROLE_TEACHER = 1;

    public const array ROLES = [self::ROLE_STUDENT, self::ROLE_TEACHER];

    // id là BIGINT AUTO_INCREMENT (mặc định của Eloquent) — bỏ HasUuids.

    protected $fillable = [
        'username',
        'email',
        'hash_id',
        'emp_id',
        'role',
        'cname',
        'unit_label',
        'job_title',
        'is_active',
        'show_demo',
        'sound_enabled',
        'last_login_at',
    ];

    protected $casts = [
        'role' => 'integer',
        'is_active' => 'boolean',
        'show_demo' => 'boolean',
        'sound_enabled' => 'boolean',
        'last_login_at' => 'datetime',
    ];

    public function progress(): HasMany
    {
        return $this->hasMany(UserProgress::class);
    }
}
