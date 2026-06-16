<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Contracts\AdminServiceInterface;
use App\Exceptions\AdminDeletionException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminRequest;
use App\Models\Admin;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function __construct(protected AdminServiceInterface $adminService) {}

    public function index(Request $request): View
    {
        $admins = $this->adminService->list($request->all());

        return view('admin.admins.index', compact('admins'));
    }

    public function create(): View
    {
        return view('admin.admins.create');
    }

    public function store(AdminRequest $request): RedirectResponse
    {
        $this->adminService->store($request->validated());

        return redirect()->route('admin.admins.index')
            ->with('success', __('admins.created_successfully'));
    }

    public function edit(Admin $admin): View
    {
        return view('admin.admins.edit', compact('admin'));
    }

    public function update(AdminRequest $request, Admin $admin): RedirectResponse
    {
        $this->adminService->update($admin, $request->validated());

        return redirect()->route('admin.admins.index')
            ->with('success', __('admins.updated_successfully'));
    }

    public function destroy(Admin $admin): RedirectResponse
    {
        try {
            $this->adminService->delete($admin);
        } catch (AdminDeletionException $e) {
            return redirect()->route('admin.admins.index')
                ->with('error', $e->getMessage());
        }

        return redirect()->route('admin.admins.index')
            ->with('success', __('admins.deleted_successfully'));
    }

    public function toggleStatus(Admin $admin): JsonResponse
    {
        $this->adminService->toggleStatus($admin);

        return response()->json(['success' => true]);
    }
}
