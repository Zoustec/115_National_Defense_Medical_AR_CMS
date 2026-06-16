<?php

declare(strict_types=1);

namespace App\Services;

use App\Constants\Pagination;
use App\Contracts\CategoryServiceInterface;
use App\Models\Category;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class CategoryService implements CategoryServiceInterface
{
    public function list(array $filters = []): LengthAwarePaginator
    {
        $query = Category::query();

        if (! empty($filters['keyword'])) {
            $keyword = $filters['keyword'];
            $query->where(function ($q) use ($keyword) {
                $q->where('code', 'like', "%{$keyword}%")
                    ->orWhere('name', 'like', "%{$keyword}%");
            });
        }

        return $query->orderBy('id', 'desc')
            ->paginate($filters['per_page'] ?? Pagination::PER_PAGE);
    }

    public function getAll(): Collection
    {
        return Category::orderBy('id')->get();
    }

    public function getSwappable(): Collection
    {
        // Any category can host replacement items — no fixed group restriction.
        return Category::orderBy('id')->get();
    }

    public function create(array $data): Category
    {
        // Categories are always active; status is not editable in the CMS.
        $data['status'] = true;

        return Category::create($data);
    }

    public function update(Category $category, array $data): Category
    {
        $category->update($data);

        return $category;
    }

    public function delete(Category $category): void
    {
        $category->delete();
    }
}
