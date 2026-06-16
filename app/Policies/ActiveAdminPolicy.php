<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Admin;

abstract class ActiveAdminPolicy
{
    public function viewAny(Admin $currentAdmin): bool
    {
        return $currentAdmin->is_active;
    }

    public function view(Admin $currentAdmin): bool
    {
        return $currentAdmin->is_active;
    }

    public function create(Admin $currentAdmin): bool
    {
        return $currentAdmin->is_active;
    }

    public function update(Admin $currentAdmin): bool
    {
        return $currentAdmin->is_active;
    }

    public function delete(Admin $currentAdmin): bool
    {
        return $currentAdmin->is_active;
    }
}
