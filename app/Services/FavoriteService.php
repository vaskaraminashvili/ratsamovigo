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
use App\Repositories\Contracts\ListItemRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class FavoriteService
{
    public function __construct(
        private FavoriteItemRepository $items,
        private FavoriteDishRepository $dishes,
        private ListItemRepository $listItems,
        private ListAccess $access,
    ) {}

    /**
     * @return array{items: Collection<int, FavoriteItem>, dishes: Collection<int, FavoriteDish>}
     */
    public function library(User $user): array
    {
        return [
            'items' => $this->items->forUser($user),
            'dishes' => $this->dishes->forUser($user),
        ];
    }

    public function findItem(User $user, int $id): FavoriteItem
    {
        return $this->items->findForUser($id, $user);
    }

    public function findDish(User $user, int $id): FavoriteDish
    {
        return $this->dishes->findForUser($id, $user);
    }

    public function itemNamed(User $user, string $name): ?FavoriteItem
    {
        return $this->items->findByName($user, trim($name));
    }

    public function dishNamed(User $user, string $name): ?FavoriteDish
    {
        return $this->dishes->findByName($user, trim($name));
    }

    /**
     * @param  array{name: string, quantity?: string|null, emoji?: string|null, note?: string|null}  $data
     */
    public function saveItem(User $user, array $data, ?int $id = null): FavoriteItem
    {
        $attributes = $this->itemAttributes($data, 'ნივთის სახელი სავალდებულოა.');

        if ($id) {
            return $this->items->update($this->items->findForUser($id, $user), $attributes);
        }

        return $this->items->create([
            ...$attributes,
            'user_id' => $user->id,
            'position' => $this->items->nextPosition($user),
        ]);
    }

    public function deleteItem(User $user, int $id): void
    {
        $this->items->delete($this->items->findForUser($id, $user));
    }

    /**
     * @param  list<int>  $ids
     */
    public function reorderItems(User $user, array $ids): void
    {
        $this->items->reorder($user, $ids);
    }

    /**
     * @param  array{name: string, emoji?: string|null, note?: string|null}  $data
     */
    public function saveDish(User $user, array $data, ?int $id = null): FavoriteDish
    {
        $attributes = $this->dishAttributes($data);

        if ($id) {
            return $this->dishes->update($this->dishes->findForUser($id, $user), $attributes);
        }

        return $this->dishes->create([
            ...$attributes,
            'user_id' => $user->id,
            'position' => $this->dishes->nextPosition($user),
        ]);
    }

    public function deleteDish(User $user, int $id): void
    {
        $this->dishes->delete($this->dishes->findForUser($id, $user));
    }

    /**
     * @param  list<int>  $ids
     */
    public function reorderDishes(User $user, array $ids): void
    {
        $this->dishes->reorder($user, $ids);
    }

    /**
     * @param  array{name: string, quantity?: string|null, emoji?: string|null, note?: string|null}  $data
     */
    public function saveDishItem(User $user, int $dishId, array $data, ?int $itemId = null): FavoriteDishItem
    {
        $dish = $this->dishes->findForUser($dishId, $user);
        $attributes = $this->itemAttributes($data, 'ინგრედიენტის სახელი სავალდებულოა.');

        if ($itemId) {
            return $this->dishes->updateItem($this->dishes->findItem($dish, $itemId), $attributes);
        }

        return $this->dishes->addItem([
            ...$attributes,
            'favorite_dish_id' => $dish->id,
            'position' => $this->dishes->nextItemPosition($dish),
        ]);
    }

    public function deleteDishItem(User $user, int $dishId, int $itemId): void
    {
        $dish = $this->dishes->findForUser($dishId, $user);
        $this->dishes->deleteItem($this->dishes->findItem($dish, $itemId));
    }

    /**
     * @param  list<int>  $ids
     */
    public function reorderDishItems(User $user, int $dishId, array $ids): void
    {
        $dish = $this->dishes->findForUser($dishId, $user);
        $this->dishes->reorderItems($dish, $ids);
    }

    public function moveDishItem(User $user, int $dishId, int $itemId, int $direction): void
    {
        $dish = $this->dishes->findForUser($dishId, $user);
        $ids = [];

        foreach ($dish->items as $item) {
            $ids[] = $item->id;
        }

        $index = array_search($itemId, $ids, true);

        if ($index === false) {
            throw (new ModelNotFoundException)->setModel(FavoriteDishItem::class);
        }

        $target = $index + ($direction < 0 ? -1 : 1);

        if ($target < 0 || $target >= count($ids)) {
            return;
        }

        $ordered = [];

        foreach ($ids as $position => $id) {
            if ($position === $index) {
                $ordered[] = $ids[$target];

                continue;
            }

            if ($position === $target) {
                $ordered[] = $ids[$index];

                continue;
            }

            $ordered[] = $id;
        }

        $this->dishes->reorderItems($dish, $ordered);
    }

    public function rememberItem(User $user, ListItem $item, bool $replace = false): FavoriteItem
    {
        $list = $item->relationLoaded('list') ? $item->list : $item->list()->first();

        if (! $list instanceof ShoppingList) {
            throw (new ModelNotFoundException)->setModel(ShoppingList::class);
        }

        $this->access->requireEdit($user, $list);

        $attributes = $this->itemAttributes([
            'name' => $item->name,
            'quantity' => $item->quantity,
            'emoji' => $item->emoji,
            'note' => $item->note,
        ], 'ნივთის სახელი სავალდებულოა.');

        $existing = $replace ? $this->items->findByName($user, $attributes['name']) : null;

        if ($existing) {
            return $this->items->update($existing, $attributes);
        }

        return $this->saveItem($user, $attributes);
    }

    public function rememberDish(User $user, ShoppingList $list, string $groupName, bool $replace = false): FavoriteDish
    {
        $this->access->requireEdit($user, $list);
        $groupName = trim($groupName);

        if ($groupName === '') {
            throw ValidationException::withMessages([
                'name' => 'კერძის სახელი სავალდებულოა.',
            ]);
        }

        $rows = [];

        foreach ($this->listItems->inGroup($list, $groupName)->sortBy('id') as $item) {
            $rows[] = [
                'name' => $item->name,
                'quantity' => $item->quantity,
                'emoji' => $item->emoji,
                'note' => $item->note,
            ];
        }

        return DB::transaction(function () use ($user, $groupName, $replace, $rows): FavoriteDish {
            $existing = $replace ? $this->dishes->findByName($user, $groupName) : null;

            $dish = $existing
                ? $this->dishes->update($existing, ['name' => $groupName])
                : $this->saveDish($user, ['name' => $groupName]);

            $this->dishes->replaceItems($dish, $rows);

            return $this->dishes->findForUser($dish->id, $user);
        });
    }

    /**
     * @param  array{name: string, quantity?: string|null, emoji?: string|null, note?: string|null}  $data
     * @return array{name: string, quantity: string|null, emoji: string|null, note: string|null}
     */
    private function itemAttributes(array $data, string $nameMessage): array
    {
        $name = trim($data['name']);

        if ($name === '') {
            throw ValidationException::withMessages([
                'name' => $nameMessage,
            ]);
        }

        return [
            'name' => $name,
            'quantity' => $this->blankToNull($data['quantity'] ?? null),
            'emoji' => $this->blankToNull($data['emoji'] ?? null),
            'note' => $this->blankToNull($data['note'] ?? null),
        ];
    }

    /**
     * @param  array{name: string, emoji?: string|null, note?: string|null}  $data
     * @return array{name: string, emoji: string|null, note: string|null}
     */
    private function dishAttributes(array $data): array
    {
        $name = trim($data['name']);

        if ($name === '') {
            throw ValidationException::withMessages([
                'name' => 'კერძის სახელი სავალდებულოა.',
            ]);
        }

        return [
            'name' => $name,
            'emoji' => $this->blankToNull($data['emoji'] ?? null),
            'note' => $this->blankToNull($data['note'] ?? null),
        ];
    }

    private function blankToNull(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $value = trim($value);

        return $value === '' ? null : $value;
    }
}
