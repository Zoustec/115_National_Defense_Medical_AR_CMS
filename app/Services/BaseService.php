<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;

abstract class BaseService
{
    protected function applyFilters(Builder $query, array $filters, array $filterableFields): Builder
    {
        foreach ($filterableFields as $field) {
            if (! empty($filters[$field])) {
                $query->where($field, 'like', '%'.$filters[$field].'%');
            }
        }

        return $query;
    }

    protected function deleteFile(?string $path): void
    {
        if ($path && ! str_starts_with($path, 'http')) {
            Storage::delete($path);
        }
    }
}
