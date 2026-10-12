<?php

namespace App\Services;

use App\Models\ListItem;
use App\Models\ShoppingList;
use App\Models\User;
use App\Repositories\Contracts\ListItemRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ListItemService
{
    public function __construct(
        private ListItemRepository $items,
        private EntitlementService $entitlements,
        private ListAccess $access,
    ) {}

    /**
     * @param  array{name: string, quantity?: string|null, emoji?: string|null, note?: string|null}  $data
     */
    public function add(User $user, ShoppingList $list, array $data, ?UploadedFile $photo = null): ListItem
    {
        $this->access->requireEdit($user, $list);

        return $this->items->create([
            'shopping_list_id' => $list->id,
            'user_id' => $user->id,
            'name' => $data['name'],
            'quantity' => $this->blankToNull($data['quantity'] ?? null),
            'emoji' => $this->blankToNull($data['emoji'] ?? null),
            'note' => $this->blankToNull($data['note'] ?? null),
            'photo_path' => $this->storePhoto($list, $photo),
        ]);
    }

    /**
     * @param  array{name: string, quantity?: string|null, emoji?: string|null, note?: string|null}  $data
     */
    public function update(User $user, ShoppingList $list, int $itemId, array $data, ?UploadedFile $photo = null): ListItem
    {
        $this->access->requireEdit($user, $list);

        $item = $this->items->findForList($itemId, $list);
        $attributes = [
            'name' => $data['name'],
            'quantity' => $this->blankToNull($data['quantity'] ?? null),
            'emoji' => $this->blankToNull($data['emoji'] ?? null),
            'note' => $this->blankToNull($data['note'] ?? null),
        ];

        if ($photo) {
            if ($item->photo_path) {
                Storage::disk('public')->delete($item->photo_path);
            }

            $attributes['photo_path'] = $this->storePhoto($list, $photo);
        }

        return $this->items->update($item, $attributes);
    }

    public function delete(User $user, ShoppingList $list, int $itemId): void
    {
        $this->access->requireEdit($user, $list);

        $item = $this->items->findForList($itemId, $list);

        if ($item->photo_path) {
            Storage::disk('public')->delete($item->photo_path);
        }

        $this->items->delete($item);
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

    private function storePhoto(ShoppingList $list, ?UploadedFile $photo): ?string
    {
        if (! $photo) {
            return null;
        }

        $owner = $list->relationLoaded('owner') ? $list->owner : $list->owner()->first();

        if (! $owner || ! $this->entitlements->canUploadItemPhoto($owner)) {
            throw ValidationException::withMessages([
                'photo' => 'ფოტო ფასიან გეგმაზეა.',
            ]);
        }

        return $photo->store('items', 'public');
    }

    private function blankToNull(?string $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
