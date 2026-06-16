<?php

declare(strict_types=1);

namespace App\Services;

use App\Constants\Pagination;
use App\Contracts\UserServiceInterface;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UserService implements UserServiceInterface
{
    public function list(array $filters = []): LengthAwarePaginator
    {
        $perPage = (int) ($filters['per_page'] ?? Pagination::PER_PAGE);
        $keyword = isset($filters['keyword']) ? substr(trim($filters['keyword']), 0, 255) : null;
        $role = $this->resolveRole($filters['tab'] ?? null);
        $status = $filters['status'] ?? null;

        return User::query()
            ->where('role', $role)
            ->when($keyword, fn ($q) => $q->where(function ($q) use ($keyword) {
                $q->where('username', 'like', "%{$keyword}%")
                    ->orWhere('email', 'like', "%{$keyword}%")
                    ->orWhere('cname', 'like', "%{$keyword}%")
                    ->orWhere('emp_id', 'like', "%{$keyword}%");
            }))
            ->when($status !== null && $status !== '', function ($q) use ($status) {
                $q->where('is_active', (bool) $status);
            })
            ->orderByDesc('created_at')
            ->paginate($perPage)
            ->appends(array_filter([
                'tab' => $filters['tab'] ?? null,
                'keyword' => $filters['keyword'] ?? null,
                'status' => $status,
                'per_page' => $filters['per_page'] ?? null,
            ], fn ($v) => $v !== null && $v !== ''));
    }

    private function resolveRole(?string $tab): int
    {
        return $tab === 'teacher' ? User::ROLE_TEACHER : User::ROLE_STUDENT;
    }
}
