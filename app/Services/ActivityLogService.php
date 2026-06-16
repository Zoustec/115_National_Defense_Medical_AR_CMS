<?php

declare(strict_types=1);

namespace App\Services;

use App\Constants\Pagination;
use App\Contracts\ActivityLogServiceInterface;
use App\Models\ActivityLog;
use App\Models\Item;
use App\Models\LearningUnit;
use App\Models\User;
use App\Models\UserProgress;
use App\Models\UserProgressDetail;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ActivityLogService implements ActivityLogServiceInterface
{
    public function getActionCounts(array $filters = []): array
    {
        $counts = ActivityLog::query()
            ->when($filters['date_from'] ?? null, fn ($q, $from) => $q->whereDate('created_at', '>=', $from))
            ->when($filters['date_to'] ?? null, fn ($q, $to) => $q->whereDate('created_at', '<=', $to))
            ->selectRaw('action, COUNT(*) as total')
            ->groupBy('action')
            ->pluck('total', 'action')
            ->toArray();

        // Always return every known action so dashboard cards render a 0
        // instead of being absent when nothing has been logged yet.
        $result = [];
        foreach (ActivityLog::ACTIONS as $action) {
            $result[$action] = (int) ($counts[$action] ?? 0);
        }

        return $result;
    }

    public function getLearningBehaviorCount(array $filters = []): int
    {
        return UserProgressDetail::query()
            ->when($filters['date_from'] ?? null, fn ($q, $from) => $q->whereDate('created_at', '>=', $from))
            ->when($filters['date_to'] ?? null, fn ($q, $to) => $q->whereDate('created_at', '<=', $to))
            ->count();
    }

    /**
     * Unified, paginated feed mixing activity_logs rows (login/logout, AR,
     * Virtual Patient, Smart Q&A) with learning-behaviour rows sourced from
     * user_progress_detail. The two tables are UNIONed at the DB level so a
     * single page of results is ordered correctly by time across both sources.
     *
     * The union only selects identity columns (row_type, ref_id, created_at);
     * the heavy detail (user, item, learning unit) is batch-loaded afterwards
     * for just the rows on the current page (see hydrateRows()).
     */
    public function list(array $filters = []): LengthAwarePaginator
    {
        $perPage = (int) ($filters['per_page'] ?? Pagination::PER_PAGE);
        $action = $filters['action'] ?? null;
        $keyword = isset($filters['keyword']) ? substr(trim($filters['keyword']), 0, 255) : null;
        $dateFrom = $filters['date_from'] ?? null;
        $dateTo = $filters['date_to'] ?? null;

        // A keyword should match anything visible in a log row, not just the
        // user. We resolve it once into the id sets each source can filter by —
        // users, learning units, items — plus any action whose label contains
        // it. Each UNION branch then ORs the id sets that apply to its columns,
        // so a row surfaces when the keyword hits its user, its lesson, its
        // food item, or its action label.
        $hasKeyword = $keyword !== null && $keyword !== '';
        $userIds = $learningUnitIds = $itemIds = $matchedActions = null;
        if ($hasKeyword) {
            $like = "%{$keyword}%";

            $userIds = User::query()
                ->where('username', 'like', $like)
                ->orWhere('cname', 'like', $like)
                ->orWhere('emp_id', 'like', $like)
                ->pluck('id');

            $learningUnitIds = LearningUnit::query()
                ->where('name', 'like', $like)
                ->orWhere('code', 'like', $like)
                ->pluck('id');

            // Items match on their own name or their category's name, since the
            // feed shows both ("lesson · item [category]").
            $itemIds = Item::query()
                ->where('name', 'like', $like)
                ->orWhereHas('category', fn ($q) => $q->where('name', 'like', $like))
                ->pluck('id');

            // Let the keyword match the human-readable action label too (e.g.
            // typing "AR" or part of the Chinese label finds those rows).
            $matchedActions = collect(ActivityLog::ACTIONS)
                ->filter(fn ($a) => mb_stripos(__('activity.action_'.$a), $keyword) !== false
                    || mb_stripos($a, $keyword) !== false)
                ->values();
        }

        $includeActivity = $action !== ActivityLog::FILTER_LEARNING_BEHAVIOR;
        $includeBehavior = $action === null || $action === ''
            || $action === ActivityLog::FILTER_LEARNING_BEHAVIOR;

        $union = null;

        if ($includeActivity) {
            $activityQuery = DB::table('activity_logs')
                ->selectRaw("'activity' as row_type, id as ref_id, created_at")
                ->when($action === ActivityLog::FILTER_LOGIN_LOGOUT,
                    fn ($q) => $q->whereIn('action', ActivityLog::ACTION_GROUP_LOGIN),
                    fn ($q) => $q->when($action, fn ($q) => $q->where('action', $action)))
                ->when($dateFrom, fn ($q, $from) => $q->whereDate('created_at', '>=', $from))
                ->when($dateTo, fn ($q, $to) => $q->whereDate('created_at', '<=', $to))
                ->when($hasKeyword, fn ($q) => $q->where(function ($q) use ($userIds, $matchedActions, $learningUnitIds) {
                    $q->whereIn('user_id', $userIds)
                        ->orWhereIn('action', $matchedActions);
                    // AR-open logs reference their lesson via metadata JSON, so a
                    // learning-unit keyword has to match the embedded id.
                    foreach ($learningUnitIds as $luId) {
                        $q->orWhere('metadata->learningUnitId', $luId);
                    }
                }));

            $union = $activityQuery;
        }

        if ($includeBehavior) {
            $behaviorQuery = DB::table('user_progress_detail')
                ->whereNull('deleted_at')
                ->selectRaw("'behavior' as row_type, id as ref_id, created_at")
                ->when($dateFrom, fn ($q, $from) => $q->whereDate('created_at', '>=', $from))
                ->when($dateTo, fn ($q, $to) => $q->whereDate('created_at', '<=', $to))
                ->when($hasKeyword, fn ($q) => $q->where(function ($q) use ($userIds, $learningUnitIds, $itemIds) {
                    // User or learning unit hit → match via the parent progress row.
                    $q->whereIn(
                        'user_progress_id',
                        UserProgress::query()
                            ->where(fn ($p) => $p->whereIn('user_id', $userIds)
                                ->orWhereIn('learning_unit_id', $learningUnitIds))
                            ->select('id')
                    )
                        // Item name / category hit → match the focused food item.
                        ->orWhereIn('item_id', $itemIds);
                }));

            $union = $union === null ? $behaviorQuery : $union->unionAll($behaviorQuery);
        }

        // No source selected (shouldn't happen) — return an empty page.
        if ($union === null) {
            $union = DB::table('activity_logs')
                ->selectRaw("'activity' as row_type, id as ref_id, created_at")
                ->whereRaw('1 = 0');
        }

        /** @var QueryBuilder $wrapped */
        $wrapped = DB::query()->fromSub($union, 'feed')->orderByDesc('created_at');

        $paginator = $wrapped->paginate($perPage)->appends(array_filter([
            'action' => $action,
            'keyword' => $filters['keyword'] ?? null,
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'per_page' => $filters['per_page'] ?? null,
        ], fn ($v) => $v !== null && $v !== ''));

        $paginator->setCollection($this->hydrateRows(collect($paginator->items())));

        return $paginator;
    }

    /**
     * Turns the lightweight union rows (row_type + ref_id) on the current page
     * into a uniform collection of view models, batch-loading each source's
     * detail in one query. Output order matches the page order.
     *
     * Each item: { row_type, model } where model is an ActivityLog or a
     * UserProgressDetail (with item/category/progress eager-loaded).
     *
     * @param  Collection<int, object>  $rows
     * @return Collection<int, object>
     */
    private function hydrateRows(Collection $rows): Collection
    {
        $activityIds = $rows->where('row_type', 'activity')->pluck('ref_id');
        $behaviorIds = $rows->where('row_type', 'behavior')->pluck('ref_id');

        $activities = $activityIds->isEmpty()
            ? collect()
            : ActivityLog::query()
                ->with('user:id,username,cname,emp_id,hash_id,role')
                ->whereIn('id', $activityIds)
                ->get()
                ->keyBy('id');

        $this->attachLearningUnitNames($activities->values());

        $behaviors = $behaviorIds->isEmpty()
            ? collect()
            : UserProgressDetail::query()
                ->with([
                    'item:id,name,category_id',
                    'item.category:id,name',
                    'progress:id,user_id,learning_unit_id,session_no,status',
                    'progress.user:id,username,cname,emp_id,hash_id,role',
                    'progress.learningUnit:id,name,code',
                ])
                ->whereIn('id', $behaviorIds)
                ->get()
                ->keyBy('id');

        return $rows
            ->map(function (object $row) use ($activities, $behaviors) {
                $model = $row->row_type === 'behavior'
                    ? ($behaviors[$row->ref_id] ?? null)
                    : ($activities[$row->ref_id] ?? null);

                return $model === null ? null : (object) [
                    'row_type' => $row->row_type,
                    'model' => $model,
                ];
            })
            ->filter()
            ->values();
    }

    /**
     * Resolves the learning unit name for AR-open logs in one batched query and
     * sets it as a transient `learning_unit_name` attribute so views can show
     * the lesson title instead of a bare id.
     *
     * @param  Collection<int, ActivityLog>  $logs
     */
    private function attachLearningUnitNames(Collection $logs): void
    {
        $unitIds = $logs
            ->map(fn (ActivityLog $log) => $log->metadata['learningUnitId'] ?? null)
            ->filter()
            ->unique()
            ->values();

        if ($unitIds->isEmpty()) {
            return;
        }

        $names = LearningUnit::query()
            ->whereIn('id', $unitIds)
            ->pluck('name', 'id');

        foreach ($logs as $log) {
            $unitId = $log->metadata['learningUnitId'] ?? null;
            if ($unitId !== null) {
                $log->setAttribute('learning_unit_name', $names[$unitId] ?? null);
            }
        }
    }

    public function getUserHistory(User $user, ?string $action = null): array
    {
        // Learning behaviour: every attempt with its per-item focus rows.
        // details.item.category drives the "food recognition" labels; status
        // distinguishes a visit (read) from a committed swap; duration is the
        // dwell time per food. completed_at marks course completion.
        $progress = UserProgress::query()
            ->where('user_id', $user->id)
            ->with([
                'learningUnit:id,name,code',
                'details' => fn ($q) => $q->orderBy('start_time'),
                'details.item:id,name,category_id',
                'details.item.category:id,name',
            ])
            ->orderByDesc('created_at')
            ->get();

        // Login / logout / AR / external-link clicks for this user, optionally
        // narrowed to the action the user clicked through from the log feed.
        $activities = ActivityLog::query()
            ->where('user_id', $user->id)
            ->when($action === ActivityLog::FILTER_LOGIN_LOGOUT,
                fn ($q) => $q->whereIn('action', ActivityLog::ACTION_GROUP_LOGIN),
                fn ($q) => $q->when($action, fn ($q) => $q->where('action', $action)))
            ->orderByDesc('created_at')
            ->limit(200)
            ->get();

        $this->attachLearningUnitNames($activities);

        return [
            'progress' => $progress,
            'activities' => $activities,
        ];
    }
}
