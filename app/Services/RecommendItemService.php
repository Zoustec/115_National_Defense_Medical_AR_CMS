<?php

declare(strict_types=1);

namespace App\Services;

use App\Constants\Pagination;
use App\Contracts\RecommendItemServiceInterface;
use App\Models\RecommendItem;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class RecommendItemService implements RecommendItemServiceInterface
{
    public function list(array $filters = []): LengthAwarePaginator
    {
        $query = RecommendItem::query()->with('category:id,name');

        if (! empty($filters['keyword'])) {
            $keyword = $filters['keyword'];
            $query->where('name', 'like', "%{$keyword}%");
        }

        if (! empty($filters['category_id'])) {
            $query->where('category_id', (int) $filters['category_id']);
        }

        if (isset($filters['status']) && $filters['status'] !== '') {
            $query->where('status', (bool) $filters['status']);
        }

        return $query->orderBy('id', 'desc')
            ->paginate($filters['per_page'] ?? Pagination::PER_PAGE);
    }

    public function getSwappableWithCategory(): Collection
    {
        // Any category can host replacement items — no fixed group restriction.
        return RecommendItem::with('category')
            ->orderBy('category_id')
            ->orderBy('id')
            ->get();
    }

    public function create(array $data): RecommendItem
    {
        return RecommendItem::create($data);
    }

    public function update(RecommendItem $item, array $data): RecommendItem
    {
        $item->update($data);

        return $item;
    }

    public function delete(RecommendItem $item): void
    {
        $item->delete();
    }
}
