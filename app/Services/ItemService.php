<?php

declare(strict_types=1);

namespace App\Services;

use App\Constants\Pagination;
use App\Contracts\ItemServiceInterface;
use App\Models\Item;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ItemService implements ItemServiceInterface
{
    public function list(array $filters = []): LengthAwarePaginator
    {
        $query = Item::query()->with('category:id,name');

        if (! empty($filters['keyword'])) {
            $keyword = $filters['keyword'];
            $query->where(function ($q) use ($keyword) {
                $q->where('model', 'like', "%{$keyword}%")
                    ->orWhere('name', 'like', "%{$keyword}%");
            });
        }

        if (! empty($filters['category_id'])) {
            $query->where('category_id', (int) $filters['category_id']);
        }

        if (isset($filters['status']) && $filters['status'] !== '') {
            $query->where('status', (bool) $filters['status']);
        }

        if (isset($filters['unit']) && $filters['unit'] !== '') {
            $query->where('unit', (int) $filters['unit']);
        }

        return $query->orderBy('id', 'desc')
            ->paginate($filters['per_page'] ?? Pagination::PER_PAGE);
    }

    public function getAllWithCategory(): Collection
    {
        return Item::with('category')
            ->orderBy('category_id')
            ->orderBy('display_order')
            ->get();
    }

    public function create(array $data, ?UploadedFile $image = null): Item
    {
        $item = Item::create($data);

        if ($image !== null) {
            $item->update(['image' => $this->storeImage($image, $item->id)]);
        }

        return $item->fresh();
    }

    public function update(Item $item, array $data, ?UploadedFile $image = null, bool $removeImage = false): Item
    {
        if ($image !== null) {
            $this->deleteImage($item->image);
            $data['image'] = $this->storeImage($image, $item->id);
        } elseif ($removeImage) {
            $this->deleteImage($item->image);
            $data['image'] = null;
        }

        $item->update($data);

        return $item;
    }

    public function delete(Item $item): void
    {
        $this->deleteImage($item->image);
        $item->delete();
    }

    private function storeImage(UploadedFile $file, int $itemId): string
    {
        // Use getPathname() (string) instead of the UploadedFile object so the
        // filesystem adapter calls fopen() on the path directly, avoiding the
        // realpath() call that returns false for PHP temp uploads on Windows.
        return Storage::putFileAs(
            "items/{$itemId}",
            $file->getPathname(),
            $file->hashName(),
        );
    }

    private function deleteImage(?string $path): void
    {
        if ($path !== null && $path !== '' && Storage::exists($path)) {
            Storage::delete($path);
        }
    }
}
