<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Models\Category;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface CategoryServiceInterface
{
    public function list(array $filters = []): LengthAwarePaginator;

    public function getAll(): Collection;

    public function getSwappable(): Collection;

    public function create(array $data): Category;

    public function update(Category $category, array $data): Category;

    public function delete(Category $category): void;
}
