<?php

namespace App\Livewire\Settings;

use App\Http\Requests\StoreFavoriteDishItemRequest;
use App\Http\Requests\StoreFavoriteDishRequest;
use App\Http\Requests\StoreFavoriteItemRequest;
use App\Http\Requests\UpdateFavoriteDishRequest;
use App\Http\Requests\UpdateFavoriteItemRequest;
use App\Models\FavoriteDishItem;
use App\Models\User;
use App\Services\FavoriteService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('რჩეულები')]
class Favorites extends Component
{
    public string $itemName = '';

    public string $itemQuantity = '';

    public string $itemEmoji = '';

    public string $itemNote = '';

    public ?int $editingItemId = null;

    public string $dishName = '';

    public string $dishEmoji = '';

    public string $dishNote = '';

    public ?int $editingDishId = null;

    public ?int $ingredientDishId = null;

    public string $ingredientName = '';

    public string $ingredientQuantity = '';

    public string $ingredientEmoji = '';

    public string $ingredientNote = '';

    public ?int $editingIngredientId = null;

    public function saveItem(FavoriteService $favorites): void
    {
        $this->itemName = trim($this->itemName);
        $this->itemQuantity = trim($this->itemQuantity);
        $this->itemEmoji = trim($this->itemEmoji);
        $this->itemNote = trim($this->itemNote);

        $request = $this->editingItemId ? new UpdateFavoriteItemRequest : new StoreFavoriteItemRequest;
        $data = $this->validateMapped($request->rules(), $request->messages(), [
            'name' => 'itemName',
            'quantity' => 'itemQuantity',
            'emoji' => 'itemEmoji',
            'note' => 'itemNote',
        ]);

        $favorites->saveItem($this->user(), $data, $this->editingItemId);
        $this->reset('itemName', 'itemQuantity', 'itemEmoji', 'itemNote', 'editingItemId');
    }

    public function editItem(int $itemId, FavoriteService $favorites): void
    {
        $item = $favorites->findItem($this->user(), $itemId);
        $this->editingItemId = $item->id;
        $this->itemName = $item->name;
        $this->itemQuantity = (string) $item->quantity;
        $this->itemEmoji = (string) $item->emoji;
        $this->itemNote = (string) $item->note;
    }

    public function deleteItem(int $itemId, FavoriteService $favorites): void
    {
        $favorites->deleteItem($this->user(), $itemId);

        if ($this->editingItemId === $itemId) {
            $this->reset('itemName', 'itemQuantity', 'itemEmoji', 'itemNote', 'editingItemId');
        }
    }

    public function cancelItem(): void
    {
        $this->reset('itemName', 'itemQuantity', 'itemEmoji', 'itemNote', 'editingItemId');
    }

    public function saveDish(FavoriteService $favorites): void
    {
        $this->dishName = trim($this->dishName);
        $this->dishEmoji = trim($this->dishEmoji);
        $this->dishNote = trim($this->dishNote);

        $request = $this->editingDishId ? new UpdateFavoriteDishRequest : new StoreFavoriteDishRequest;
        $data = $this->validateMapped($request->rules(), $request->messages(), [
            'name' => 'dishName',
            'emoji' => 'dishEmoji',
            'note' => 'dishNote',
        ]);

        $favorites->saveDish($this->user(), [
            'name' => $data['name'],
            'emoji' => $data['emoji'],
            'note' => $data['note'],
        ], $this->editingDishId);

        $this->reset('dishName', 'dishEmoji', 'dishNote', 'editingDishId');
    }

    public function editDish(int $dishId, FavoriteService $favorites): void
    {
        $dish = $favorites->findDish($this->user(), $dishId);
        $this->editingDishId = $dish->id;
        $this->dishName = $dish->name;
        $this->dishEmoji = (string) $dish->emoji;
        $this->dishNote = (string) $dish->note;
    }

    public function deleteDish(int $dishId, FavoriteService $favorites): void
    {
        $favorites->deleteDish($this->user(), $dishId);

        if ($this->editingDishId === $dishId) {
            $this->reset('dishName', 'dishEmoji', 'dishNote', 'editingDishId');
        }

        if ($this->ingredientDishId === $dishId) {
            $this->cancelIngredient();
        }
    }

    public function cancelDish(): void
    {
        $this->reset('dishName', 'dishEmoji', 'dishNote', 'editingDishId');
    }

    public function startIngredient(int $dishId, FavoriteService $favorites): void
    {
        $favorites->findDish($this->user(), $dishId);
        $this->reset('ingredientName', 'ingredientQuantity', 'ingredientEmoji', 'ingredientNote', 'editingIngredientId');
        $this->ingredientDishId = $dishId;
    }

    public function saveIngredient(int $dishId, FavoriteService $favorites): void
    {
        $this->ingredientName = trim($this->ingredientName);
        $this->ingredientQuantity = trim($this->ingredientQuantity);
        $this->ingredientEmoji = trim($this->ingredientEmoji);
        $this->ingredientNote = trim($this->ingredientNote);

        $request = new StoreFavoriteDishItemRequest;
        $data = $this->validateMapped($request->rules(), $request->messages(), [
            'name' => 'ingredientName',
            'quantity' => 'ingredientQuantity',
            'emoji' => 'ingredientEmoji',
            'note' => 'ingredientNote',
        ]);

        $favorites->saveDishItem($this->user(), $dishId, $data, $this->editingIngredientId);
        $this->reset('ingredientName', 'ingredientQuantity', 'ingredientEmoji', 'ingredientNote', 'editingIngredientId');
        $this->ingredientDishId = $dishId;
    }

    public function editIngredient(int $dishId, int $itemId, FavoriteService $favorites): void
    {
        $dish = $favorites->findDish($this->user(), $dishId);
        $item = $dish->items->firstWhere('id', $itemId);

        if (! $item instanceof FavoriteDishItem) {
            throw (new ModelNotFoundException)->setModel(FavoriteDishItem::class);
        }

        $this->ingredientDishId = $dish->id;
        $this->editingIngredientId = $item->id;
        $this->ingredientName = $item->name;
        $this->ingredientQuantity = (string) $item->quantity;
        $this->ingredientEmoji = (string) $item->emoji;
        $this->ingredientNote = (string) $item->note;
    }

    public function deleteIngredient(int $dishId, int $itemId, FavoriteService $favorites): void
    {
        $favorites->deleteDishItem($this->user(), $dishId, $itemId);

        if ($this->editingIngredientId === $itemId) {
            $this->reset('ingredientName', 'ingredientQuantity', 'ingredientEmoji', 'ingredientNote', 'editingIngredientId');
            $this->ingredientDishId = $dishId;
        }
    }

    public function moveIngredient(int $dishId, int $itemId, string $direction, FavoriteService $favorites): void
    {
        $favorites->moveDishItem($this->user(), $dishId, $itemId, $direction === 'up' ? -1 : 1);
    }

    public function cancelIngredient(): void
    {
        $this->reset('ingredientName', 'ingredientQuantity', 'ingredientEmoji', 'ingredientNote', 'editingIngredientId', 'ingredientDishId');
    }

    public function render(FavoriteService $favorites): View
    {
        $library = $favorites->library($this->user());

        return view('livewire.settings.favorites', [
            'items' => $library['items'],
            'dishes' => $library['dishes'],
        ]);
    }

    /**
     * @param  array<string, mixed>  $rules
     * @param  array<string, string>  $messages
     * @param  array<string, string>  $map
     * @return array{name: string, quantity: string|null, emoji: string|null, note: string|null}
     */
    private function validateMapped(array $rules, array $messages, array $map): array
    {
        $prefixedRules = [];
        $prefixedMessages = [];

        foreach ($map as $field => $property) {
            if (! array_key_exists($field, $rules)) {
                continue;
            }

            $prefixedRules[$property] = $rules[$field];

            foreach ($messages as $messageKey => $message) {
                if (str_starts_with($messageKey, $field.'.')) {
                    $prefixedMessages[$property.substr($messageKey, strlen($field))] = $message;
                }
            }
        }

        /** @var array<string, mixed> $validated */
        $validated = $this->validate($prefixedRules, $prefixedMessages);
        $name = $validated[$map['name']] ?? null;

        if (! is_string($name)) {
            throw ValidationException::withMessages([
                $map['name'] => 'სახელი სავალდებულოა.',
            ]);
        }

        return [
            'name' => $name,
            'quantity' => $this->stringOrNull($validated, $map['quantity'] ?? null),
            'emoji' => $this->stringOrNull($validated, $map['emoji'] ?? null),
            'note' => $this->stringOrNull($validated, $map['note'] ?? null),
        ];
    }

    /**
     * @param  array<string, mixed>  $validated
     */
    private function stringOrNull(array $validated, ?string $key): ?string
    {
        if ($key === null || ! isset($validated[$key]) || ! is_string($validated[$key])) {
            return null;
        }

        return $validated[$key];
    }

    private function user(): User
    {
        /** @var User $user */
        $user = Auth::user();

        return $user;
    }
}
