<?php

declare(strict_types=1);

namespace App\Contracts;

interface DashboardServiceInterface
{
    public function getStats(): array;
}
