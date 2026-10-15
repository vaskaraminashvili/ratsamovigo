<?php

namespace App\Repositories\Eloquent;

use App\Models\FavoriteDish;
use App\Models\FavoriteDishItem;
use App\Models\User;
use App\Repositories\Contracts\FavoriteDishRepository as FavoriteDishRepositoryContract;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class FavoriteDishRepository implements FavoriteDishRepositoryContract
{
    public function forUser(User $user): Collection
    {
        return FavoriteDish::query()
            ->where('user_id', $user->id)
            ->with('items')
            ->orderBy('position')
            ->orderBy('id')
            ->get();
    }

    public function findForUser(int $id, User $user): FavoriteDish
    {
        return FavoriteDish::query()
            ->where('user_id', $user->id)
            ->with('items')
            ->findOrFail($id);
    }

    public function findByName(User $user, string $name): ?FavoriteDish
    {
        return FavoriteDish::query()
            ->where('user_id', $user->id)
            ->where('name', $name)
            ->orderBy('position')
            ->orderBy('id')
            ->first();
    }

    public function create(array $attributes): FavoriteDish
    {
        return FavoriteDish::query()->create($attributes);
    }

    public function update(FavoriteDish $dish, array $attributes): FavoriteDish
    {
        $dish->update($attributes);

        return $dish->refresh();
    }

    public function delete(FavoriteDish $dish): void
    {
        DB::transaction(function () use ($dish): void {
            FavoriteDishItem::query()->where('favorite_dish_id', $dish->id)->delete();
            $dish->delete();
        });
    }

    public function nextPosition(User $user): int
    {
        $max = FavoriteDish::query()->where('user_id', $user->id)->max('position');

        return $max === null ? 0 : ((int) $max) + 1;
    }

    public function reorder(User $user, array $ids): void
    {
        $owned = FavoriteDish::query()
            ->where('user_id', $user->id)
            ->whereIn('id', $ids)
            ->pluck('id');

        foreach ($ids as $position => $id) {
            if (! $owned->contains($id)) {
                continue;
            }

            FavoriteDish::query()->whereKey($id)->update(['position' => $position]);
        }
    }

    public function findItem(FavoriteDish $dish, int $itemId): FavoriteDishItem
    {
        return FavoriteDishItem::query()
            ->where('favorite_dish_id', $dish->id)
            ->findOrFail($itemId);
    }

    public function addItem(array $attributes): FavoriteDishItem
    {
        return FavoriteDishItem::query()->create($attributes);
    }

    public function updateItem(FavoriteDishItem $item, array $attributes): FavoriteDishItem
    {
        $item->update($attributes);

        return $item->refresh();
    }

    public function deleteItem(FavoriteDishItem $item): void
    {
        $item->delete();
    }

    public function nextItemPosition(FavoriteDish $dish): int
    {
        $max = FavoriteDishItem::query()->where('favorite_dish_id', $dish->id)->max('position');

        return $max === null ? 0 : ((int) $max) + 1;
    }

    public function reorderItems(FavoriteDish $dish, array $ids): void
    {
        $owned = FavoriteDishItem::query()
            ->where('favorite_dish_id', $dish->id)
            ->whereIn('id', $ids)
            ->pluck('id');

        foreach ($ids as $position => $id) {
            if (! $owned->contains($id)) {
                continue;
            }

            FavoriteDishItem::query()->whereKey($id)->update(['position' => $position]);
        }
    }

    public function replaceItems(FavoriteDish $dish, array $rows): void
    {
        FavoriteDishItem::query()->where('favorite_dish_id', $dish->id)->delete();

        foreach ($rows as $position => $row) {
            FavoriteDishItem::query()->create([
                'favorite_dish_id' => $dish->id,
                'name' => $row['name'],
                'quantity' => $row['quantity'] ?? null,
                'emoji' => $row['emoji'] ?? null,
                'note' => $row['note'] ?? null,
                'position' => $position,
            ]);
        }
    }
}
