<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Models\Admin;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface AdminServiceInterface
{
    public function list(array $filters = []): LengthAwarePaginator;

    public function store(array $data): Admin;

    public function find(int $adminId): ?Admin;

    public function update(Admin $admin, array $data): Admin;

    public function delete(Admin $admin): bool;

    public function toggleStatus(Admin $admin): bool;
}
