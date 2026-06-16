<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Models\Item;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;

interface ItemServiceInterface
{
    public function list(array $filters = []): LengthAwarePaginator;

    public function getAllWithCategory(): Collection;

    public function create(array $data, ?UploadedFile $image = null): Item;

    public function update(Item $item, array $data, ?UploadedFile $image = null, bool $removeImage = false): Item;

    public function delete(Item $item): void;
}
