<?php

namespace App\Services;

use App\Enums\BoughtBehavior;
use App\Models\ListItem;
use App\Models\ShoppingList;
use App\Models\User;
use App\Repositories\Contracts\ListItemRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;

class ListItemService
{
    public function __construct(
        private ListItemRepository $items,
        private EntitlementService $entitlements,
        private ListAccess $access,
    ) {}

    /**
     * @param  array{name: string, quantity?: string|null, emoji?: string|null, note?: string|null, group_name?: string|null}  $data
     */
    public function add(User $user, ShoppingList $list, array $data, ?UploadedFile $photo = null): ListItem
    {
        $this->access->requireEdit($user, $list);
        $this->guardPhoto($list, $photo);

        $item = $this->items->create([
            'shopping_list_id' => $list->id,
            'user_id' => $user->id,
            'name' => $data['name'],
            'group_name' => $this->blankToNull($data['group_name'] ?? null),
            'quantity' => $this->blankToNull($data['quantity'] ?? null),
            'emoji' => $this->blankToNull($data['emoji'] ?? null),
            'note' => $this->blankToNull($data['note'] ?? null),
        ]);

        if ($photo) {
            $this->items->replacePhoto($item, $photo);
        }

        return $item;
    }

    /**
     * @param  array{name: string, quantity?: string|null, emoji?: string|null, note?: string|null, group_name?: string|null}  $data
     */
    public function update(User $user, ShoppingList $list, int $itemId, array $data, ?UploadedFile $photo = null): ListItem
    {
        $this->access->requireEdit($user, $list);
        $this->guardPhoto($list, $photo);

        $item = $this->items->findForList($itemId, $list);
        $attributes = [
            'name' => $data['name'],
            'quantity' => $this->blankToNull($data['quantity'] ?? null),
            'emoji' => $this->blankToNull($data['emoji'] ?? null),
            'note' => $this->blankToNull($data['note'] ?? null),
        ];

        if (array_key_exists('group_name', $data)) {
            $attributes['group_name'] = $this->blankToNull($data['group_name']);
        }

        $item = $this->items->update($item, $attributes);

        if ($photo) {
            $this->items->replacePhoto($item, $photo);
        }

        return $item;
    }

    public function delete(User $user, ShoppingList $list, int $itemId): void
    {
        $this->access->requireEdit($user, $list);

        $this->items->delete($this->items->findForList($itemId, $list));
    }

    public function toggleCheck(User $user, ShoppingList $list, int $itemId): void
    {
        $this->access->requireEdit($user, $list);

        $item = $this->items->findForList($itemId, $list);
        $checked = ! $item->is_checked;

        $this->items->update($item, [
            'is_checked' => $checked,
            'checked_by' => $checked ? $user->id : null,
            'checked_at' => $checked ? now() : null,
        ]);
    }

    public function toggleLike(User $user, ShoppingList $list, int $itemId): void
    {
        $this->access->role($user, $list);

        $item = $this->items->findForList($itemId, $list);
        $this->items->toggleLike($item, $user);
    }

    public function toggleClaim(User $user, ShoppingList $list, int $itemId): void
    {
        $this->access->requireEdit($user, $list);

        $item = $this->items->findForList($itemId, $list);
        $claimedBy = $item->claimed_by === $user->id ? null : $user->id;

        $this->items->update($item, ['claimed_by' => $claimedBy]);
    }

    public function togglePin(User $user, ShoppingList $list, int $itemId): void
    {
        $this->access->requireEdit($user, $list);

        $item = $this->items->findForList($itemId, $list);
        $pinned = ! $item->is_pinned;

        if ($pinned) {
            $this->items->unpinAll($list);
        }

        $this->items->update($item, ['is_pinned' => $pinned]);
    }

    public function clearChecked(User $user, ShoppingList $list): void
    {
        $this->access->requireEdit($user, $list);
        $this->items->clearChecked($list);
    }

    public function reopenChecked(User $user, ShoppingList $list): void
    {
        $this->access->requireEdit($user, $list);

        if ($list->bought_behavior !== BoughtBehavior::Hide) {
            return;
        }

        $this->items->reopenChecked($list);
    }

    public function toggleGroup(User $user, ShoppingList $list, string $groupName): void
    {
        $this->access->requireEdit($user, $list);

        $group = $this->items->inGroup($list, $groupName);

        if ($group->isEmpty()) {
            return;
        }

        $check = $group->contains(fn (ListItem $item): bool => ! $item->is_checked);

        foreach ($group as $item) {
            $this->items->update($item, [
                'is_checked' => $check,
                'checked_by' => $check ? $user->id : null,
                'checked_at' => $check ? now() : null,
            ]);
        }
    }

    private function guardPhoto(ShoppingList $list, ?UploadedFile $photo): void
    {
        if (! $photo) {
            return;
        }

        $owner = $list->relationLoaded('owner') ? $list->owner : $list->owner()->first();

        if (! $owner || ! $this->entitlements->canUploadItemPhoto($owner)) {
            throw ValidationException::withMessages([
                'photo' => 'ფოტო ფასიან გეგმაზეა.',
            ]);
        }
    }

    private function blankToNull(?string $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
