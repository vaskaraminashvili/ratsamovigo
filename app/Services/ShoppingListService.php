<?php

namespace App\Services;

use App\Enums\CheckboxStyle;
use App\Enums\ListRole;
use App\Models\ShoppingList;
use App\Models\User;
use App\Repositories\Contracts\ShoppingListRepository;
use App\Support\ListAppearance;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class ShoppingListService
{
    public function __construct(
        private ShoppingListRepository $lists,
        private EntitlementService $entitlements,
        private ListAccess $access,
    ) {}

    /**
     * @return Collection<int, ShoppingList>
     */
    public function listsFor(User $user): Collection
    {
        return $this->lists->memberLists($user);
    }

    public function findForMember(int $id, User $user): ShoppingList
    {
        return $this->lists->findForMember($id, $user);
    }

    /**
     * @param  array{name: string, emoji?: string|null}  $data
     */
    public function create(User $user, array $data): ShoppingList
    {
        if (! $this->entitlements->canCreateList($user)) {
            throw ValidationException::withMessages([
                'name' => 'უფასო გეგმაზე შეგიძლიათ მხოლოდ 2 სია.',
            ]);
        }

        $list = $this->lists->create([
            'user_id' => $user->id,
            'name' => $data['name'],
            'emoji' => $this->blankToNull($data['emoji'] ?? null),
            'accent_color' => ListAppearance::ACCENTS[0],
            'checkbox_style' => CheckboxStyle::Circle,
        ]);

        $this->lists->addMember($list, $user, ListRole::Owner);

        return $list;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function updateAppearance(User $user, ShoppingList $list, array $data, ?UploadedFile $background = null): ShoppingList
    {
        $this->access->requireManage($user, $list);

        $preset = $data['background_preset'] ?? $list->background_preset;

        if (! array_key_exists($preset, ListAppearance::PRESETS)) {
            throw ValidationException::withMessages([
                'background_preset' => 'აირჩიეთ ფონი სიიდან.',
            ]);
        }

        if ($background) {
            $owner = $list->owner ?? $list->owner()->first();

            if (! $owner || ! $this->entitlements->canUploadBackground($owner)) {
                throw ValidationException::withMessages([
                    'backgroundUpload' => 'საკუთარი ფონი ფასიან გეგმაზეა.',
                ]);
            }
        }

        $attributes = [
            'name' => $data['name'],
            'emoji' => $this->blankToNull($data['emoji'] ?? null),
            'accent_color' => $data['accent_color'],
            'background_preset' => $preset,
            'checkbox_style' => CheckboxStyle::from($data['checkbox_style']),
            'show_emoji' => (bool) $data['show_emoji'],
            'show_quantity' => (bool) $data['show_quantity'],
            'show_likes' => (bool) $data['show_likes'],
            'show_author' => (bool) $data['show_author'],
        ];

        $updated = $this->lists->update($list, $attributes);

        if ($background) {
            $this->lists->replaceBackground($updated, $background);
        }

        return $updated;
    }

    public function removeBackground(User $user, ShoppingList $list): void
    {
        $this->access->requireManage($user, $list);
        $this->lists->clearBackground($list);
    }

    public function delete(User $user, ShoppingList $list): void
    {
        $this->access->requireManage($user, $list);
        $this->lists->delete($list);
    }

    public function removeMember(User $actor, ShoppingList $list, int $memberId): void
    {
        $this->access->requireManage($actor, $list);

        $member = $list->members->firstWhere('id', $memberId);

        if (! $member || $member->role === ListRole::Owner) {
            throw ValidationException::withMessages([
                'member' => 'მფლობელს ვერ წაშლით.',
            ]);
        }

        $this->lists->removeMember($member);
    }

    private function blankToNull(?string $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
