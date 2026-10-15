<?php

namespace App\Repositories\Eloquent;

use App\Models\FavoriteItem;
use App\Models\User;
use App\Repositories\Contracts\FavoriteItemRepository as FavoriteItemRepositoryContract;
use Illuminate\Support\Collection;

class FavoriteItemRepository implements FavoriteItemRepositoryContract
{
    public function forUser(User $user): Collection
    {
        return FavoriteItem::query()
            ->where('user_id', $user->id)
            ->orderBy('position')
            ->orderBy('id')
            ->get();
    }

    public function findForUser(int $id, User $user): FavoriteItem
    {
        return FavoriteItem::query()
            ->where('user_id', $user->id)
            ->findOrFail($id);
    }

    public function findByName(User $user, string $name): ?FavoriteItem
    {
        return FavoriteItem::query()
            ->where('user_id', $user->id)
            ->where('name', $name)
            ->orderBy('position')
            ->orderBy('id')
            ->first();
    }

    public function create(array $attributes): FavoriteItem
    {
        return FavoriteItem::query()->create($attributes);
    }

    public function update(FavoriteItem $item, array $attributes): FavoriteItem
    {
        $item->update($attributes);

        return $item->refresh();
    }

    public function delete(FavoriteItem $item): void
    {
        $item->delete();
    }

    public function nextPosition(User $user): int
    {
        $max = FavoriteItem::query()->where('user_id', $user->id)->max('position');

        return $max === null ? 0 : ((int) $max) + 1;
    }

    public function reorder(User $user, array $ids): void
    {
        $owned = FavoriteItem::query()
            ->where('user_id', $user->id)
            ->whereIn('id', $ids)
            ->pluck('id');

        foreach ($ids as $position => $id) {
            if (! $owned->contains($id)) {
                continue;
            }

            FavoriteItem::query()->whereKey($id)->update(['position' => $position]);
        }
    }
}
