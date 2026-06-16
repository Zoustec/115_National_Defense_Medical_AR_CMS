<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Contracts\ActivityLogServiceInterface;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    public function __construct(protected ActivityLogServiceInterface $activityLogService) {}

    public function index(Request $request): View
    {
        $filters = $request->only(['action', 'keyword', 'date_from', 'date_to', 'per_page']);
        $dateFilters = $request->only(['date_from', 'date_to']);
        $logs = $this->activityLogService->list($filters);
        $counts = $this->activityLogService->getActionCounts($dateFilters);
        $learningBehaviorCount = $this->activityLogService->getLearningBehaviorCount($dateFilters);

        return view('admin.activity-logs.index', compact('logs', 'counts', 'learningBehaviorCount'));
    }

    public function userHistory(Request $request, User $user): View
    {
        $action = $request->query('action');
        $history = $this->activityLogService->getUserHistory($user, is_string($action) ? $action : null);

        return view('admin.activity-logs.user-history', [
            'user' => $user,
            'progress' => $history['progress'],
            'activities' => $history['activities'],
            'action' => $action,
        ]);
    }
}
