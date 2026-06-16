<?php

declare(strict_types=1);

namespace App\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface UserServiceInterface
{
    public function list(array $filters = []): LengthAwarePaginator;
}
