<?php

namespace App\Repositories\Contracts;

use App\Models\ListItem;
use App\Models\ShoppingList;
use App\Models\User;

interface ListItemRepository
{
    public function findForList(int $id, ShoppingList $list): ListItem;

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function create(array $attributes): ListItem;

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function update(ListItem $item, array $attributes): ListItem;

    public function delete(ListItem $item): void;

    public function clearChecked(ShoppingList $list): void;

    public function unpinAll(ShoppingList $list): void;

    public function toggleLike(ListItem $item, User $user): bool;
}
