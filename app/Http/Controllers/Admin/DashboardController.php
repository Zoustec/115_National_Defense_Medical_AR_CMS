<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Contracts\DashboardServiceInterface;
use App\Http\Controllers\Controller;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(protected DashboardServiceInterface $dashboardService) {}

    public function index(): View
    {
        $stats = $this->dashboardService->getStats();

        return view('admin.dashboard.index', [
            'totalAdmins' => $stats['total_admins'],
            'totalUsers' => $stats['total_users'],
            'totalLearningUnits' => $stats['total_learning_units'],
            'totalCategories' => $stats['total_categories'],
            'totalItems' => $stats['total_items'],
            'totalLogins' => $stats['total_logins'],
            'totalLearningBehaviors' => $stats['total_learning_behaviors'],
            'totalArOpens' => $stats['total_ar_opens'],
            'totalVirtualPatientOpens' => $stats['total_virtual_patient_opens'],
            'totalSmartQaOpens' => $stats['total_smart_qa_opens'],
        ]);
    }
}
