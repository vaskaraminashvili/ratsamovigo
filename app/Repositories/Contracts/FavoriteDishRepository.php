<?php

namespace App\Repositories\Contracts;

use App\Models\FavoriteDish;
use App\Models\FavoriteDishItem;
use App\Models\User;
use Illuminate\Support\Collection;

interface FavoriteDishRepository
{
    /**
     * @return Collection<int, FavoriteDish>
     */
    public function forUser(User $user): Collection;

    public function findForUser(int $id, User $user): FavoriteDish;

    public function findByName(User $user, string $name): ?FavoriteDish;

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function create(array $attributes): FavoriteDish;

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function update(FavoriteDish $dish, array $attributes): FavoriteDish;

    public function delete(FavoriteDish $dish): void;

    public function nextPosition(User $user): int;

    /**
     * @param  list<int>  $ids
     */
    public function reorder(User $user, array $ids): void;

    public function findItem(FavoriteDish $dish, int $itemId): FavoriteDishItem;

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function addItem(array $attributes): FavoriteDishItem;

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function updateItem(FavoriteDishItem $item, array $attributes): FavoriteDishItem;

    public function deleteItem(FavoriteDishItem $item): void;

    public function nextItemPosition(FavoriteDish $dish): int;

    /**
     * @param  list<int>  $ids
     */
    public function reorderItems(FavoriteDish $dish, array $ids): void;

    /**
     * @param  list<array{name: string, quantity?: string|null, emoji?: string|null, note?: string|null}>  $rows
     */
    public function replaceItems(FavoriteDish $dish, array $rows): void;
}
