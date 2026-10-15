<?php

namespace App\Repositories\Contracts;

use App\Models\FavoriteItem;
use App\Models\User;
use Illuminate\Support\Collection;

interface FavoriteItemRepository
{
    /**
     * @return Collection<int, FavoriteItem>
     */
    public function forUser(User $user): Collection;

    public function findForUser(int $id, User $user): FavoriteItem;

    public function findByName(User $user, string $name): ?FavoriteItem;

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function create(array $attributes): FavoriteItem;

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function update(FavoriteItem $item, array $attributes): FavoriteItem;

    public function delete(FavoriteItem $item): void;

    public function nextPosition(User $user): int;

    /**
     * @param  list<int>  $ids
     */
    public function reorder(User $user, array $ids): void;
}
