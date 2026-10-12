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
use App\Support\ListAppearance;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.lists')]
#[Title('სია')]
class Show extends Component
{
    use WithFileUploads;

    public ShoppingList $shoppingList;

    public string $panel = '';

    public bool $showChecked = false;

    public string $itemName = '';

    public string $itemQuantity = '';

    public string $itemEmoji = '';

    public string $itemNote = '';

    public mixed $itemPhoto = null;

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

    public mixed $backgroundUpload = null;

    public function mount(ShoppingList $shoppingList, ShoppingListService $lists): void
    {
        $this->shoppingList = $lists->findForMember($shoppingList->id, $this->user());
        $this->fillAppearance($this->shoppingList);
    }

    public function addItem(ListItemService $items): void
    {
        $validated = $this->validateItem();

        $items->add($this->user(), $this->shoppingList, $validated, $this->itemPhoto);
        $this->resetItemForm();
    }

    public function editItem(int $itemId): void
    {
        $item = $this->shoppingList->items->firstWhere('id', $itemId);

        if (! $item) {
            return;
        }

        $this->editingItemId = $item->id;
        $this->itemName = $item->name;
        $this->itemQuantity = (string) $item->quantity;
        $this->itemEmoji = (string) $item->emoji;
        $this->itemNote = (string) $item->note;
        $this->itemPhoto = null;
        $this->panel = 'item';
    }

    public function saveItem(ListItemService $items): void
    {
        if (! $this->editingItemId) {
            return;
        }

        $validated = $this->validateItem();

        $items->update($this->user(), $this->shoppingList, $this->editingItemId, $validated, $this->itemPhoto);
        $this->resetItemForm();
        $this->panel = '';
    }

    public function deleteItem(int $itemId, ListItemService $items): void
    {
        $items->delete($this->user(), $this->shoppingList, $itemId);
        $this->resetItemForm();
    }

    public function toggleCheck(int $itemId, ListItemService $items): void
    {
        $items->toggleCheck($this->user(), $this->shoppingList, $itemId);
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
        $validated = $this->validate(
            (new StorePollRequest)->rules(),
            (new StorePollRequest)->messages(),
        );

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
        $validated = $this->validate([
            'listName' => (new UpdateShoppingListRequest)->rules()['name'],
            'listEmoji' => (new UpdateShoppingListRequest)->rules()['emoji'],
            'accentColor' => (new UpdateShoppingListRequest)->rules()['accent_color'],
            'backgroundPreset' => (new UpdateShoppingListRequest)->rules()['background_preset'],
            'checkboxStyle' => (new UpdateShoppingListRequest)->rules()['checkbox_style'],
            'showEmoji' => (new UpdateShoppingListRequest)->rules()['show_emoji'],
            'showQuantity' => (new UpdateShoppingListRequest)->rules()['show_quantity'],
            'showLikes' => (new UpdateShoppingListRequest)->rules()['show_likes'],
            'showAuthor' => (new UpdateShoppingListRequest)->rules()['show_author'],
            'backgroundUpload' => (new UpdateShoppingListRequest)->rules()['background'],
        ], [
            'listName.required' => 'სიის სახელი სავალდებულოა.',
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

        $this->backgroundUpload = null;
        $this->panel = '';
    }

    public function removeBackground(ShoppingListService $lists): void
    {
        $lists->removeBackground($this->user(), $this->shoppingList);
    }

    public function deleteList(ShoppingListService $lists): void
    {
        $lists->delete($this->user(), $this->shoppingList);

        $this->redirect(route('lists.index'), navigate: true);
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

        return view('livewire.lists.show', [
            'list' => $list,
            'role' => $role,
            'canEdit' => $role->canEdit(),
            'canManage' => $role->canManage(),
            'offers' => $offers->forList($list),
            'presets' => ListAppearance::PRESETS,
            'accents' => ListAppearance::ACCENTS,
            'emojiChoices' => ListAppearance::EMOJI,
            'checkboxStyles' => CheckboxStyle::cases(),
            'viewLink' => $list->shareLinks->firstWhere('role', ListRole::Viewer),
            'editLink' => $list->shareLinks->firstWhere('role', ListRole::Editor),
            'openItems' => $list->items->where('is_checked', false)->sortByDesc('is_pinned')->values(),
            'checkedItems' => $list->items->where('is_checked', true)->values(),
            'polls' => $list->polls->sortByDesc('is_pinned')->values(),
        ]);
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
                'photo' => $this->itemPhoto,
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
        $this->accentColor = $list->accent_color;
        $this->backgroundPreset = $list->background_preset;
        $this->checkboxStyle = $list->checkbox_style->value;
        $this->showEmoji = $list->show_emoji;
        $this->showQuantity = $list->show_quantity;
        $this->showLikes = $list->show_likes;
        $this->showAuthor = $list->show_author;
    }

    private function resetItemForm(): void
    {
        $this->reset('itemName', 'itemQuantity', 'itemEmoji', 'itemNote', 'itemPhoto', 'editingItemId');
    }

    private function user(): User
    {
        /** @var User $user */
        $user = Auth::user();

        return $user;
    }
}
