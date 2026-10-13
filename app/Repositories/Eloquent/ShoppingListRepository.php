<?php

namespace App\Repositories\Eloquent;

use App\Enums\ListRole;
use App\Models\ListMember;
use App\Models\ShareLink;
use App\Models\ShoppingList;
use App\Models\User;
use App\Repositories\Contracts\ShoppingListRepository as ShoppingListRepositoryContract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ShoppingListRepository implements ShoppingListRepositoryContract
{
    public function memberLists(User $user): Collection
    {
        return ShoppingList::query()
            ->whereHas('members', fn (Builder $query) => $query->where('user_id', $user->id))
            ->with(['members.user'])
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

    public function findShareLink(string $token): ShareLink
    {
        return ShareLink::query()
            ->with('list')
            ->where('token', $token)
            ->firstOrFail();
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

    public function replaceBackground(ShoppingList $list, UploadedFile $file): void
    {
        $this->replaceMedia($list, $file, 'background');
    }

    public function clearBackground(ShoppingList $list): void
    {
        $list->clearMediaCollection('background');
    }

    public function delete(ShoppingList $list): void
    {
        $list->loadMissing('items');

        foreach ($list->items as $item) {
            $item->clearMediaCollection('photo');
        }

        $list->clearMediaCollection('background');
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

    private function replaceMedia(ShoppingList $list, UploadedFile $file, string $collection): void
    {
        $list->addMedia($file->getRealPath() ?: $file)
            ->usingFileName((string) Str::uuid().'.'.($file->guessExtension() ?: 'jpg'))
            ->toMediaCollection($collection);
    }

    /**
     * @return Builder<ShoppingList>
     */
    private function detailedQuery(): Builder
    {
        return ShoppingList::query()->with([
            'owner',
            'media',
            'members.user',
            'shareLinks',
            'items.media',
            'items.author',
            'items.checker',
            'items.claimer',
            'items.likes',
            'polls.author',
            'polls.options.votes.user',
            'polls.votes.user',
        ]);
    }
}
