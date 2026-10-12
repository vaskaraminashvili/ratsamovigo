<?php

namespace App\Repositories\Eloquent;

use App\Models\ItemLike;
use App\Models\ListItem;
use App\Models\Poll;
use App\Models\ShoppingList;
use App\Models\User;
use App\Repositories\Contracts\ListItemRepository as ListItemRepositoryContract;

class ListItemRepository implements ListItemRepositoryContract
{
    public function findForList(int $id, ShoppingList $list): ListItem
    {
        return ListItem::query()
            ->where('shopping_list_id', $list->id)
            ->findOrFail($id);
    }

    public function create(array $attributes): ListItem
    {
        return ListItem::query()->create($attributes);
    }

    public function update(ListItem $item, array $attributes): ListItem
    {
        $item->update($attributes);

        return $item->refresh();
    }

    public function delete(ListItem $item): void
    {
        $item->delete();
    }

    public function clearChecked(ShoppingList $list): void
    {
        ListItem::query()
            ->where('shopping_list_id', $list->id)
            ->where('is_checked', true)
            ->delete();
    }

    public function unpinAll(ShoppingList $list): void
    {
        ListItem::query()
            ->where('shopping_list_id', $list->id)
            ->where('is_pinned', true)
            ->update(['is_pinned' => false]);

        Poll::query()
            ->where('shopping_list_id', $list->id)
            ->where('is_pinned', true)
            ->update(['is_pinned' => false]);
    }

    public function toggleLike(ListItem $item, User $user): bool
    {
        $existing = ItemLike::query()
            ->where('list_item_id', $item->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existing) {
            $existing->delete();

            return false;
        }

        ItemLike::query()->create([
            'list_item_id' => $item->id,
            'user_id' => $user->id,
        ]);

        return true;
    }
}
