<?php

use App\Constants\Locale;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ExportController;
use App\Http\Controllers\Admin\ItemController;
use App\Http\Controllers\Admin\LearningUnitController;
use App\Http\Controllers\Admin\RecommendItemController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

// Root redirect to admin login
Route::get('/', function () {
    return redirect()->route('admin.login');
});

// Admin auth (unauthenticated)
Route::prefix('admin')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
});

// Admin (authenticated)
Route::prefix('admin')
    ->middleware('auth:admin')
    ->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('admin.logout');
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

        // Activity log (system log) — login/logout & AR/Virtual-Patient/Smart-Q&A click stats
        Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('admin.activity-logs.index');
        Route::get('/activity-logs/users/{user}', [ActivityLogController::class, 'userHistory'])->name('admin.activity-logs.user-history');

        // Admins management
        Route::post('/admins/{admin}/toggle-status', [AdminController::class, 'toggleStatus'])->name('admin.admins.toggle-status');
        Route::resource('admins', AdminController::class)->names([
            'index' => 'admin.admins.index',
            'create' => 'admin.admins.create',
            'store' => 'admin.admins.store',
            'show' => 'admin.admins.show',
            'edit' => 'admin.admins.edit',
            'update' => 'admin.admins.update',
            'destroy' => 'admin.admins.destroy',
        ]);

        // ── App content modules ─────────────────────────────────────
        Route::resource('categories', CategoryController::class)
            ->except(['show'])
            ->names('admin.categories');

        Route::resource('items', ItemController::class)
            ->except(['show'])
            ->names('admin.items');

        Route::resource('recommend-items', RecommendItemController::class)
            ->except(['show'])
            ->names('admin.recommend-items')
            ->parameters(['recommend-items' => 'recommendItem']);

        Route::get('/learning-units/export', [ExportController::class, 'learningUnits'])->name('admin.learning-units.export');
        Route::get('/learning-units/import-template', [ExportController::class, 'learningUnitTemplate'])
            ->name('admin.learning-units.import-template');
        Route::post('/learning-units/import', [ExportController::class, 'importLearningUnit'])
            ->name('admin.learning-units.import');
        Route::get('/learning-units/{learningUnit}/export', [ExportController::class, 'learningUnit'])
            ->name('admin.learning-units.export-single');
        Route::resource('learning-units', LearningUnitController::class)
            ->except(['show'])
            ->names('admin.learning-units')
            ->parameters(['learning-units' => 'learningUnit']);

        // Users (students + teachers) — read-only, SSO-synced
        Route::get('/users/export', [ExportController::class, 'users'])->name('admin.users.export');
        Route::resource('users', UserController::class)
            ->only(['index', 'show'])
            ->names('admin.users');
    });

// Language switcher
Route::get('lang/{locale}', function (string $locale) {
    if (in_array($locale, Locale::ALLOWED, true)) {
        session(['locale' => $locale]);
        app()->setLocale($locale);
    }

    $referer = request()->header('referer', '');
    $appHost = parse_url(config('app.url'), PHP_URL_HOST);
    $refHost = $referer ? parse_url($referer, PHP_URL_HOST) : null;

    return ($refHost && $refHost === $appHost)
        ? redirect()->to($referer)
        : redirect()->route('admin.login');
})->name('lang.switch');
