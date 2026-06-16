<?php

declare(strict_types=1);

namespace App\Providers;

use App\Contracts\ActivityLogServiceInterface;
use App\Contracts\AdminServiceInterface;
use App\Contracts\CategoryServiceInterface;
use App\Contracts\DashboardServiceInterface;
use App\Contracts\ExportServiceInterface;
use App\Contracts\ImportServiceInterface;
use App\Contracts\ItemServiceInterface;
use App\Contracts\LearningUnitServiceInterface;
use App\Contracts\RecommendItemServiceInterface;
use App\Contracts\UserServiceInterface;
use App\Models\Admin;
use App\Models\Item;
use App\Models\LearningUnit;
use App\Models\RecommendItem;
use App\Policies\AdminPolicy;
use App\Policies\ItemPolicy;
use App\Policies\LearningUnitPolicy;
use App\Policies\RecommendItemPolicy;
use App\Services\ActivityLogService;
use App\Services\AdminService;
use App\Services\CategoryService;
use App\Services\DashboardService;
use App\Services\ExportService;
use App\Services\ImportService;
use App\Services\ItemService;
use App\Services\LearningUnitService;
use App\Services\RecommendItemService;
use App\Services\UserService;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(AdminServiceInterface::class, AdminService::class);
        $this->app->bind(DashboardServiceInterface::class, DashboardService::class);
        $this->app->bind(CategoryServiceInterface::class, CategoryService::class);
        $this->app->bind(ItemServiceInterface::class, ItemService::class);
        $this->app->bind(RecommendItemServiceInterface::class, RecommendItemService::class);
        $this->app->bind(LearningUnitServiceInterface::class, LearningUnitService::class);
        $this->app->bind(UserServiceInterface::class, UserService::class);
        $this->app->bind(ExportServiceInterface::class, ExportService::class);
        $this->app->bind(ImportServiceInterface::class, ImportService::class);
        $this->app->bind(ActivityLogServiceInterface::class, ActivityLogService::class);
    }

    public function boot(): void
    {
        Paginator::useBootstrapFour();

        if (config('app.env') !== 'local') {
            URL::forceScheme('https');
        }

        Gate::define('admin', fn () => auth('admin')->check());

        Gate::policy(Admin::class, AdminPolicy::class);
        Gate::policy(Item::class, ItemPolicy::class);
        Gate::policy(RecommendItem::class, RecommendItemPolicy::class);
        Gate::policy(LearningUnit::class, LearningUnitPolicy::class);
    }
}
