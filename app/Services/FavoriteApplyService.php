<?php

namespace App\Services;

use App\Models\FavoriteDish;
use App\Models\FavoriteDishItem;
use App\Models\FavoriteItem;
use App\Models\ListItem;
use App\Models\ShoppingList;
use App\Models\User;
use App\Repositories\Contracts\FavoriteDishRepository;
use App\Repositories\Contracts\FavoriteItemRepository;
use Illuminate\Support\Facades\DB;

class FavoriteApplyService
{
    public function __construct(
        private FavoriteItemRepository $items,
        private FavoriteDishRepository $dishes,
        private ListItemService $listItems,
    ) {}

    public function applyItem(User $user, ShoppingList $list, FavoriteItem $item): ListItem
    {
        $item = $this->items->findForUser($item->id, $user);

        return DB::transaction(fn (): ListItem => $this->listItems->add($user, $list, [
            'name' => $item->name,
            'quantity' => $item->quantity,
            'emoji' => $item->emoji,
            'note' => $item->note,
        ]));
    }

    /**
     * @param  list<int>  $ingredientIds
     */
    public function applyDish(User $user, ShoppingList $list, FavoriteDish $dish, array $ingredientIds): void
    {
        $dish = $this->dishes->findForUser($dish->id, $user);
        $allowed = array_map(intval(...), $ingredientIds);
        $selected = $dish->items->filter(
            fn (FavoriteDishItem $item): bool => in_array($item->id, $allowed, true),
        )->values();

        DB::transaction(function () use ($user, $list, $dish, $selected): void {
            foreach ($selected as $ingredient) {
                $this->listItems->add($user, $list, [
                    'name' => $ingredient->name,
                    'quantity' => $ingredient->quantity,
                    'emoji' => $ingredient->emoji,
                    'note' => $ingredient->note,
                    'group_name' => $dish->name,
                ]);
            }
        });
    }
}
