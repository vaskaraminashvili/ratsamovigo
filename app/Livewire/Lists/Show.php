<?php

namespace App\Livewire\Lists;

use App\Contracts\DealOfferProvider;
use App\Enums\CheckboxStyle;
use App\Enums\ListRole;
use App\Http\Requests\StoreListItemRequest;
use App\Http\Requests\StorePollRequest;
use App\Http\Requests\UpdateShoppingListRequest;
use App\Models\ShoppingList;
use App\Models\User;
use App\Services\ListAccess;
use App\Services\ListItemService;
use App\Services\PollService;
use App\Services\ShareLinkService;
use App\Services\ShoppingListService;
use App\Support\ListActivity;
use App\Support\ListAppearance;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\FileNotPreviewableException;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

#[Layout('layouts.lists')]
#[Title('სია')]
class Show extends Component
{
    use WithFileUploads;

    public ShoppingList $shoppingList;

    public string $panel = '';

    public string $screen = 'list';

    public string $compose = 'item';

    public bool $showChecked = true;

    public bool $onlyMine = false;

    public string $itemName = '';

    public string $itemQuantity = '';

    public string $itemUnit = 'ცალი';

    public string $itemGroup = '';

    public string $dishName = '';

    public string $itemEmoji = '';

    public string $itemNote = '';

    public ?TemporaryUploadedFile $itemPhoto = null;

    public ?int $editingItemId = null;

    public string $pollQuestion = '';

    public string $pollOptions = '';

    /** @var array<int, int|string> */
    public array $selectedOptions = [];

    public string $listName = '';

    public string $listEmoji = '';

    public string $accentColor = '#059669';

    public string $backgroundPreset = 'sage';

    public string $checkboxStyle = 'box';

    public bool $showEmoji = true;

    public bool $showQuantity = true;

    public bool $showLikes = true;

    public bool $showAuthor = true;

    public ?TemporaryUploadedFile $backgroundUpload = null;

    public function mount(ShoppingList $shoppingList, ShoppingListService $lists): void
    {
        $this->shoppingList = $lists->findForMember($shoppingList->id, $this->user());
        $this->fillAppearance($this->shoppingList);
    }

    public function addItem(ListItemService $items): void
    {
        $items->add($this->user(), $this->shoppingList, $this->preparedItem(), $this->itemPhoto);
        $this->resetItemForm();
        $this->panel = '';
    }

    public function startCreate(): void
    {
        $this->resetItemForm();
        $this->compose = 'item';
        $this->panel = 'create';
    }

    public function startIngredient(string $group): void
    {
        $this->resetItemForm();
        $this->compose = 'dish';
        $this->itemGroup = $group;
        $this->dishName = $group;
        $this->panel = 'create';
    }

    public function bumpQuantity(int $step): void
    {
        if ($this->itemQuantity !== '' && ! ctype_digit($this->itemQuantity)) {
            return;
        }

        $current = $this->itemQuantity === '' ? 1 : (int) $this->itemQuantity;

        $this->itemQuantity = (string) max(1, $current + $step);
    }

    public function editItem(int $itemId): void
    {
        $item = $this->shoppingList->items->firstWhere('id', $itemId);

        if (! $item) {
            return;
        }

        $this->editingItemId = $item->id;
        $this->itemName = $item->name;
        $this->itemGroup = (string) $item->group_name;
        $this->splitQuantity($item->quantity);
        $this->itemEmoji = (string) $item->emoji;
        $this->itemNote = (string) $item->note;
        $this->itemPhoto = null;
        $this->compose = 'item';
        $this->panel = 'item';
    }

    public function saveItem(ListItemService $items): void
    {
        if (! $this->editingItemId) {
            return;
        }

        $items->update($this->user(), $this->shoppingList, $this->editingItemId, $this->preparedItem(), $this->itemPhoto);
        $this->resetItemForm();
        $this->panel = '';
    }

    public function deleteItem(int $itemId, ListItemService $items): void
    {
        $items->delete($this->user(), $this->shoppingList, $itemId);
        $this->resetItemForm();
        $this->panel = '';
    }

    public function toggleCheck(int $itemId, ListItemService $items): void
    {
        $items->toggleCheck($this->user(), $this->shoppingList, $itemId);
    }

    public function toggleGroup(string $groupName, ListItemService $items): void
    {
        $items->toggleGroup($this->user(), $this->shoppingList, $groupName);
    }

    public function toggleLike(int $itemId, ListItemService $items): void
    {
        $items->toggleLike($this->user(), $this->shoppingList, $itemId);
    }

    public function toggleClaim(int $itemId, ListItemService $items): void
    {
        $items->toggleClaim($this->user(), $this->shoppingList, $itemId);
    }

    public function togglePin(int $itemId, ListItemService $items): void
    {
        $items->togglePin($this->user(), $this->shoppingList, $itemId);
    }

    public function clearChecked(ListItemService $items): void
    {
        $items->clearChecked($this->user(), $this->shoppingList);
    }

    public function addPoll(PollService $polls): void
    {
        $validated = validator(
            [
                'question' => $this->pollQuestion,
                'options' => $this->pollOptions,
            ],
            (new StorePollRequest)->rules(),
            (new StorePollRequest)->messages(),
        )->validate();

        $polls->create(
            $this->user(),
            $this->shoppingList,
            $validated['question'],
            preg_split('/\r\n|\r|\n/', $validated['options']) ?: [],
        );

        $this->reset('pollQuestion', 'pollOptions');
        $this->panel = '';
    }

    public function vote(int $pollId, PollService $polls): void
    {
        $optionId = $this->selectedOptions[$pollId] ?? null;

        if (! $optionId) {
            throw ValidationException::withMessages([
                'selectedOptions' => 'აირჩიეთ ვარიანტი.',
            ]);
        }

        $polls->vote($this->user(), $this->shoppingList, $pollId, (int) $optionId);
    }

    public function voteOption(int $pollId, int $optionId, PollService $polls): void
    {
        $this->selectedOptions[$pollId] = $optionId;
        $polls->vote($this->user(), $this->shoppingList, $pollId, $optionId);
    }

    public function promoteWinner(int $pollId, PollService $polls): void
    {
        $polls->promoteWinner($this->user(), $this->shoppingList, $pollId);
    }

    public function deletePoll(int $pollId, PollService $polls): void
    {
        $polls->delete($this->user(), $this->shoppingList, $pollId);
    }

    public function togglePollPin(int $pollId, PollService $polls): void
    {
        $polls->togglePin($this->user(), $this->shoppingList, $pollId);
    }

    public function saveAppearance(ShoppingListService $lists): void
    {
        $this->showEmoji = (bool) $this->showEmoji;
        $this->showQuantity = (bool) $this->showQuantity;
        $this->showLikes = (bool) $this->showLikes;
        $this->showAuthor = (bool) $this->showAuthor;

        if (! $this->backgroundUpload instanceof TemporaryUploadedFile) {
            $this->backgroundUpload = null;
        }

        $rules = (new UpdateShoppingListRequest)->rules();
        $validated = $this->validate([
            'listName' => $rules['name'],
            'listEmoji' => $rules['emoji'],
            'accentColor' => $rules['accent_color'],
            'backgroundPreset' => $rules['background_preset'],
            'checkboxStyle' => $rules['checkbox_style'],
            'showEmoji' => $rules['show_emoji'],
            'showQuantity' => $rules['show_quantity'],
            'showLikes' => $rules['show_likes'],
            'showAuthor' => $rules['show_author'],
            'backgroundUpload' => $rules['background'],
        ], [
            'listName.required' => 'სიის სახელი სავალდებულოა.',
            'accentColor.in' => 'აირჩიეთ ფერი.',
            'backgroundPreset.in' => 'აირჩიეთ ფონი.',
            'backgroundUpload.image' => 'ფონი უნდა იყოს სურათი.',
            'backgroundUpload.max' => 'ფონი არ უნდა აღემატებოდეს 2 მბ-ს.',
        ]);

        $lists->updateAppearance($this->user(), $this->shoppingList, [
            'name' => $validated['listName'],
            'emoji' => $validated['listEmoji'] ?? null,
            'accent_color' => $validated['accentColor'],
            'background_preset' => $validated['backgroundPreset'],
            'checkbox_style' => $validated['checkboxStyle'],
            'show_emoji' => $validated['showEmoji'],
            'show_quantity' => $validated['showQuantity'],
            'show_likes' => $validated['showLikes'],
            'show_author' => $validated['showAuthor'],
        ], $this->backgroundUpload);

        $this->reset('backgroundUpload');
        $this->panel = '';
    }

    public function removeBackground(ShoppingListService $lists): void
    {
        $lists->removeBackground($this->user(), $this->shoppingList);
        $this->reset('backgroundUpload');
    }

    public function pendingBackgroundUrl(): ?string
    {
        if (! $this->backgroundUpload instanceof TemporaryUploadedFile) {
            return null;
        }

        try {
            return $this->backgroundUpload->temporaryUrl();
        } catch (FileNotPreviewableException) {
            return null;
        }
    }

    public function deleteList(ShoppingListService $lists): void
    {
        $lists->delete($this->user(), $this->shoppingList);

        $this->redirect(route('lists.index'), navigate: true);
    }

    public function openPeople(ShareLinkService $links): void
    {
        $links->ensure($this->user(), $this->shoppingList, ListRole::Viewer);
        $links->ensure($this->user(), $this->shoppingList, ListRole::Editor);
        $this->panel = 'share';
    }

    public function share(string $role, ShareLinkService $links): void
    {
        $links->replace($this->user(), $this->shoppingList, ListRole::from($role));
        $this->panel = 'share';
    }

    public function removeMember(int $memberId, ShoppingListService $lists): void
    {
        $lists->removeMember($this->user(), $this->shoppingList, $memberId);
    }

    public function render(ShoppingListService $lists, ListAccess $access, DealOfferProvider $offers): View
    {
        $list = $lists->findForMember($this->shoppingList->id, $this->user());
        $this->shoppingList = $list;
        $role = $access->role($this->user(), $list);

        $visible = $this->onlyMine
            ? $list->items->where('claimed_by', $this->user()->id)->values()
            : $list->items;

        $sort = fn ($item): string => ($item->is_checked ? '1' : '0').($item->is_pinned ? '0' : '1').$item->name;

        return view('livewire.lists.show', [
            'list' => $list,
            'role' => $role,
            'canEdit' => $role->canEdit(),
            'canManage' => $role->canManage(),
            'offers' => $offers->forList($list),
            'presets' => ListAppearance::PRESETS,
            'accents' => ListAppearance::ACCENTS,
            'emojiChoices' => ListAppearance::EMOJI,
            'units' => ListAppearance::UNITS,
            'checkboxStyles' => CheckboxStyle::cases(),
            'readonly' => false,
            'viewLink' => $list->shareLinks->firstWhere('role', ListRole::Viewer),
            'editLink' => $list->shareLinks->firstWhere('role', ListRole::Editor),
            'openCount' => $list->items->where('is_checked', false)->count(),
            'pickedCount' => $list->items->where('is_checked', true)->count(),
            'looseItems' => $visible->filter(fn ($item) => blank($item->group_name))->sortBy($sort)->values(),
            'dishes' => $visible->filter(fn ($item) => filled($item->group_name))->groupBy('group_name')->map(fn ($items) => $items->sortBy($sort)->values()),
            'polls' => $list->polls->sortByDesc('is_pinned')->values(),
            'activity' => ListActivity::events($list),
        ]);
    }

    /**
     * @return array{name: string, quantity: ?string, emoji: ?string, note: ?string, group_name: ?string}
     */
    private function preparedItem(): array
    {
        $validated = $this->validateItem();
        $validated['quantity'] = $this->composedQuantity();
        $validated['group_name'] = $this->resolvedGroup();

        return $validated;
    }

    private function resolvedGroup(): ?string
    {
        if ($this->panel === 'item') {
            $group = trim($this->itemGroup);

            if (mb_strlen($group) > 120) {
                throw ValidationException::withMessages([
                    'itemGroup' => 'კერძის სახელი ძალიან გრძელია.',
                ]);
            }

            return $group === '' ? null : $group;
        }

        if ($this->compose !== 'dish') {
            return null;
        }

        $group = trim($this->itemGroup !== '' ? $this->itemGroup : $this->dishName);

        if ($group === '') {
            throw ValidationException::withMessages([
                'dishName' => 'კერძის სახელი სავალდებულოა.',
            ]);
        }

        if (mb_strlen($group) > 120) {
            throw ValidationException::withMessages([
                'dishName' => 'კერძის სახელი ძალიან გრძელია.',
            ]);
        }

        return $group;
    }

    private function composedQuantity(): ?string
    {
        $quantity = trim($this->itemQuantity);

        if ($quantity === '') {
            return null;
        }

        $unit = array_key_exists($this->itemUnit, ListAppearance::UNITS) ? $this->itemUnit : 'ცალი';

        return $quantity.' '.$unit;
    }

    private function splitQuantity(?string $quantity): void
    {
        $quantity = trim((string) $quantity);
        $units = array_keys(ListAppearance::UNITS);
        usort($units, fn (string $a, string $b): int => mb_strlen($b) <=> mb_strlen($a));

        foreach ($units as $unit) {
            $suffix = ' '.$unit;

            if (str_ends_with($quantity, $suffix)) {
                $this->itemUnit = $unit;
                $this->itemQuantity = trim(mb_substr($quantity, 0, mb_strlen($quantity) - mb_strlen($suffix)));

                return;
            }
        }

        $this->itemUnit = 'ცალი';
        $this->itemQuantity = $quantity;
    }

    /**
     * @return array{name: string, quantity: ?string, emoji: ?string, note: ?string}
     */
    private function validateItem(): array
    {
        $validated = validator(
            [
                'name' => $this->itemName,
                'quantity' => $this->itemQuantity,
                'emoji' => $this->itemEmoji,
                'note' => $this->itemNote,
                'photo' => $this->itemPhoto instanceof TemporaryUploadedFile ? $this->itemPhoto : null,
            ],
            (new StoreListItemRequest)->rules(),
            (new StoreListItemRequest)->messages(),
        )->validate();

        return $validated;
    }

    private function fillAppearance(ShoppingList $list): void
    {
        $this->listName = $list->name;
        $this->listEmoji = (string) $list->emoji;
        $this->accentColor = in_array($list->accent_color, ListAppearance::ACCENTS, true)
            ? $list->accent_color
            : ListAppearance::ACCENTS[0];
        $this->backgroundPreset = array_key_exists($list->background_preset, ListAppearance::PRESETS)
            ? $list->background_preset
            : 'sage';
        $this->checkboxStyle = $list->checkbox_style->value;
        $this->showEmoji = $list->show_emoji;
        $this->showQuantity = $list->show_quantity;
        $this->showLikes = $list->show_likes;
        $this->showAuthor = $list->show_author;
    }

    private function resetItemForm(): void
    {
        $this->reset('itemName', 'itemQuantity', 'itemEmoji', 'itemNote', 'itemPhoto', 'editingItemId', 'itemGroup', 'dishName');
        $this->itemUnit = 'ცალი';
        $this->compose = 'item';
    }

    private function user(): User
    {
        /** @var User $user */
        $user = Auth::user();

        return $user;
    }
}
