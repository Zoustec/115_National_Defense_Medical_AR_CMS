<?php

declare(strict_types=1);

namespace App\Services;

use App\Constants\Pagination;
use App\Contracts\AdminServiceInterface;
use App\Exceptions\AdminDeletionException;
use App\Models\Admin;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminService implements AdminServiceInterface
{
    public function list(array $filters = []): LengthAwarePaginator
    {
        $perPage = (int) ($filters['per_page'] ?? Pagination::PER_PAGE);
        $keyword = isset($filters['keyword']) ? substr(trim($filters['keyword']), 0, 255) : null;

        return Admin::query()
            ->when($keyword, fn ($q) => $q->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                    ->orWhere('email', 'like', "%{$keyword}%");
            }))
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    public function store(array $data): Admin
    {
        $data['password'] = Hash::make($data['password']);

        return Admin::create($data);
    }

    public function find(int $adminId): ?Admin
    {
        return Admin::find($adminId);
    }

    public function update(Admin $admin, array $data): Admin
    {
        $admin->update($this->normalizePassword($data));

        return $admin->fresh();
    }

    private function normalizePassword(array $data): array
    {
        if (empty($data['password'])) {
            unset($data['password']);

            return $data;
        }

        $data['password'] = Hash::make($data['password']);

        return $data;
    }

    public function delete(Admin $admin): bool
    {
        if ($admin->id === auth('admin')->id()) {
            throw new AdminDeletionException(__('admins.cannot_delete_self'));
        }

        if (Admin::count() <= 1) {
            throw new AdminDeletionException(__('admins.cannot_delete_last'));
        }

        return DB::transaction(fn () => (bool) $admin->delete());
    }

    public function toggleStatus(Admin $admin): bool
    {
        $admin->update(['is_active' => ! $admin->is_active]);

        return $admin->is_active;
    }
}
