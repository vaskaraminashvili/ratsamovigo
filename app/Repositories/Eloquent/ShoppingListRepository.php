<?php

namespace App\Repositories\Eloquent;

use App\Enums\ListRole;
use App\Models\ListMember;
use App\Models\ShareLink;
use App\Models\ShoppingList;
use App\Models\User;
use App\Repositories\Contracts\ShoppingListRepository as ShoppingListRepositoryContract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ShoppingListRepository implements ShoppingListRepositoryContract
{
    public function memberLists(User $user): Collection
    {
        return ShoppingList::query()
            ->whereHas('members', fn (Builder $query) => $query->where('user_id', $user->id))
            ->withCount([
                'items as open_items_count' => fn (Builder $query) => $query->where('is_checked', false),
            ])
            ->latest()
            ->get();
    }

    public function findForMember(int $id, User $user): ShoppingList
    {
        return $this->detailedQuery()
            ->whereKey($id)
            ->whereHas('members', fn (Builder $query) => $query->where('user_id', $user->id))
            ->firstOrFail();
    }

    public function findByShareToken(string $token): ShoppingList
    {
        $link = ShareLink::query()->where('token', $token)->firstOrFail();

        return $this->detailedQuery()->findOrFail($link->shopping_list_id);
    }

    public function create(array $attributes): ShoppingList
    {
        return ShoppingList::query()->create($attributes);
    }

    public function update(ShoppingList $list, array $attributes): ShoppingList
    {
        $list->update($attributes);

        return $list->refresh();
    }

    public function delete(ShoppingList $list): void
    {
        $list->delete();
    }

    public function ownedCount(User $user): int
    {
        return ShoppingList::query()->where('user_id', $user->id)->count();
    }

    public function memberCount(ShoppingList $list): int
    {
        return ListMember::query()->where('shopping_list_id', $list->id)->count();
    }

    public function findMember(ShoppingList $list, User $user): ?ListMember
    {
        return ListMember::query()
            ->where('shopping_list_id', $list->id)
            ->where('user_id', $user->id)
            ->first();
    }

    public function addMember(ShoppingList $list, User $user, ListRole $role): ListMember
    {
        return ListMember::query()->create([
            'shopping_list_id' => $list->id,
            'user_id' => $user->id,
            'role' => $role,
        ]);
    }

    public function updateMemberRole(ListMember $member, ListRole $role): void
    {
        $member->update(['role' => $role]);
    }

    public function removeMember(ListMember $member): void
    {
        $member->delete();
    }

    public function replaceShareLink(ShoppingList $list, User $creator, ListRole $role): ShareLink
    {
        ShareLink::query()
            ->where('shopping_list_id', $list->id)
            ->where('role', $role)
            ->delete();

        return ShareLink::query()->create([
            'shopping_list_id' => $list->id,
            'user_id' => $creator->id,
            'token' => Str::lower(Str::random(40)),
            'role' => $role,
        ]);
    }

    public function latestShareLink(ShoppingList $list, ListRole $role): ?ShareLink
    {
        return ShareLink::query()
            ->where('shopping_list_id', $list->id)
            ->where('role', $role)
            ->latest()
            ->first();
    }

    /**
     * @return Builder<ShoppingList>
     */
    private function detailedQuery(): Builder
    {
        return ShoppingList::query()->with([
            'owner',
            'members.user',
            'shareLinks',
            'items.author',
            'items.checker',
            'items.claimer',
            'items.likes',
            'polls.author',
            'polls.options.votes',
            'polls.votes',
        ]);
    }
}
