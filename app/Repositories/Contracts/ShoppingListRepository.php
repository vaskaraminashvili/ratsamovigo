<?php

namespace App\Repositories\Contracts;

use App\Enums\ListRole;
use App\Models\ListMember;
use App\Models\ShareLink;
use App\Models\ShoppingList;
use App\Models\User;
use Illuminate\Support\Collection;

interface ShoppingListRepository
{
    /**
     * @return Collection<int, ShoppingList>
     */
    public function memberLists(User $user): Collection;

    public function findForMember(int $id, User $user): ShoppingList;

    public function findByShareToken(string $token): ShoppingList;

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function create(array $attributes): ShoppingList;

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function update(ShoppingList $list, array $attributes): ShoppingList;

    public function delete(ShoppingList $list): void;

    public function ownedCount(User $user): int;

    public function memberCount(ShoppingList $list): int;

    public function findMember(ShoppingList $list, User $user): ?ListMember;

    public function addMember(ShoppingList $list, User $user, ListRole $role): ListMember;

    public function updateMemberRole(ListMember $member, ListRole $role): void;

    public function removeMember(ListMember $member): void;

    public function replaceShareLink(ShoppingList $list, User $creator, ListRole $role): ShareLink;

    public function latestShareLink(ShoppingList $list, ListRole $role): ?ShareLink;
}
