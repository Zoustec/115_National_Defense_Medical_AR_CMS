<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface ActivityLogServiceInterface
{
    /**
     * Aggregate counts per action type (login, logout, ar_open, …) for the
     * dashboard cards. Optionally bounded by a date range.
     *
     * @return array<string, int>
     */
    public function getActionCounts(array $filters = []): array;

    /**
     * Count of learning-behaviour records (user_progress_detail rows) for the
     * 學習行為紀錄 stat card. Optionally bounded by a date range.
     */
    public function getLearningBehaviorCount(array $filters = []): int;

    /**
     * Paginated, filterable feed for the log screen — UNIONs activity_logs
     * rows with learning-behaviour rows (user_progress_detail) so 全部操作
     * shows both interleaved by time. Each paginated item is an object
     * { row_type: 'activity'|'behavior', model: Model }.
     */
    public function list(array $filters = []): LengthAwarePaginator;

    /**
     * Per-user learning behaviour timeline assembled from user_progress /
     * user_progress_detail (food focus, swaps, completion) plus the user's
     * login/logout & click activity rows.
     *
     * @return array{progress: Collection, activities: Collection}
     */
    public function getUserHistory(User $user, ?string $action = null): array;
}
