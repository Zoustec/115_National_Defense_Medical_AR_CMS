<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\ActivityLogServiceInterface;
use App\Contracts\DashboardServiceInterface;
use App\Models\ActivityLog;
use App\Models\Admin;
use App\Models\Category;
use App\Models\Item;
use App\Models\LearningUnit;
use App\Models\User;

class DashboardService implements DashboardServiceInterface
{
    public function __construct(protected ActivityLogServiceInterface $activityLogService) {}

    public function getStats(): array
    {
        $actions = $this->activityLogService->getActionCounts();

        return [
            'total_admins' => Admin::count(),
            'total_users' => User::count(),
            'total_learning_units' => LearningUnit::count(),
            'total_categories' => Category::count(),
            'total_items' => Item::count(),
            // Activity counters surfaced as dashboard cards. Login and logout
            // share a single card, so total_logins is their combined count.
            'total_logins' => ($actions[ActivityLog::ACTION_LOGIN] ?? 0)
                + ($actions[ActivityLog::ACTION_LOGOUT] ?? 0),
            'total_logouts' => $actions[ActivityLog::ACTION_LOGOUT] ?? 0,
            'total_learning_behaviors' => $this->activityLogService->getLearningBehaviorCount(),
            'total_ar_opens' => $actions[ActivityLog::ACTION_AR_OPEN] ?? 0,
            'total_virtual_patient_opens' => $actions[ActivityLog::ACTION_VIRTUAL_PATIENT_OPEN] ?? 0,
            'total_smart_qa_opens' => $actions[ActivityLog::ACTION_SMART_QA_OPEN] ?? 0,
        ];
    }
}
