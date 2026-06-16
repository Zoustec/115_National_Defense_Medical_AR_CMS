<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Admin;

class AdminPolicy
{
    public function update(Admin $currentAdmin, Admin $targetAdmin): bool
    {
        return true;
    }

    public function delete(Admin $currentAdmin, Admin $targetAdmin): bool
    {
        return $currentAdmin->id !== $targetAdmin->id
            && Admin::count() > 1;
    }
}
