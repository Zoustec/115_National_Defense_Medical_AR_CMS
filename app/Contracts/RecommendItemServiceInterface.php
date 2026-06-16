<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Models\RecommendItem;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface RecommendItemServiceInterface
{
    public function list(array $filters = []): LengthAwarePaginator;

    public function getSwappableWithCategory(): Collection;

    public function create(array $data): RecommendItem;

    public function update(RecommendItem $item, array $data): RecommendItem;

    public function delete(RecommendItem $item): void;
}
