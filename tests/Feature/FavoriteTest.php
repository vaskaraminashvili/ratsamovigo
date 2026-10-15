<?php

use App\Enums\ListRole;
use App\Livewire\Lists\Show;
use App\Livewire\Settings\Favorites;
use App\Models\FavoriteDish;
use App\Models\FavoriteDishItem;
use App\Models\FavoriteItem;
use App\Models\ListItem;
use App\Models\User;
use App\Services\FavoriteApplyService;
use App\Services\FavoriteService;
use App\Services\ListItemService;
use App\Services\ShareLinkService;
use App\Services\ShoppingListService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Livewire\Livewire;

it('stores an item with a note and a dish with ingredients for the owner', function () {
    $user = User::factory()->create();
    $favorites = app(FavoriteService::class);

    $item = $favorites->saveItem($user, [
        'name' => 'ღვინო',
        'quantity' => '1 ბოთლი',
        'emoji' => '🍷',
        'note' => 'საუკეთესო ბოთლი ამ მისამართზეა',
    ]);

    $dish = $favorites->saveDish($user, [
        'name' => 'პასტა',
        'emoji' => '🍝',
        'note' => 'საღამოს',
    ]);
    $pasta = $favorites->saveDishItem($user, $dish->id, ['name' => 'პასტა', 'quantity' => '500 გ']);
    $sauce = $favorites->saveDishItem($user, $dish->id, ['name' => 'სოუსი']);

    $library = $favorites->library($user);

    expect($item->note)->toBe('საუკეთესო ბოთლი ამ მისამართზეა')
        ->and($library['items'])->toHaveCount(1)
        ->and($library['dishes'])->toHaveCount(1)
        ->and($library['dishes']->first()->items->pluck('id')->all())->toBe([$pasta->id, $sauce->id]);
});

it('refuses another user access to those favorites', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $favorites = app(FavoriteService::class);
    $item = $favorites->saveItem($owner, ['name' => 'ღვინო', 'note' => 'საიდუმლო მისამართი']);
    $dish = $favorites->saveDish($owner, ['name' => 'პასტა']);
    $favorites->saveDishItem($owner, $dish->id, ['name' => 'სოუსი']);

    expect($favorites->library($other)['items'])->toHaveCount(0)
        ->and($favorites->library($other)['dishes'])->toHaveCount(0)
        ->and(fn () => $favorites->findItem($other, $item->id))->toThrow(ModelNotFoundException::class)
        ->and(fn () => $favorites->saveItem($other, ['name' => 'სხვა'], $item->id))->toThrow(ModelNotFoundException::class)
        ->and(fn () => $favorites->deleteItem($other, $item->id))->toThrow(ModelNotFoundException::class)
        ->and(fn () => $favorites->findDish($other, $dish->id))->toThrow(ModelNotFoundException::class)
        ->and(fn () => $favorites->deleteDish($other, $dish->id))->toThrow(ModelNotFoundException::class);

    Livewire::actingAs($other)
        ->test(Favorites::class)
        ->call('deleteItem', $item->id)
        ->assertNotFound();

    $this->actingAs($other)
        ->get(route('profile.favorites'))
        ->assertOk()
        ->assertDontSee('საიდუმლო მისამართი');
});

it('copies a favorite item onto a list unchecked and ungrouped', function () {
    $user = User::factory()->create();
    $list = app(ShoppingListService::class)->create($user, ['name' => 'საღამო']);
    $item = app(FavoriteService::class)->saveItem($user, [
        'name' => 'ღვინო',
        'quantity' => '1 ბოთლი',
        'emoji' => '🍷',
        'note' => 'საუკეთესო ბოთლი ამ მისამართზეა',
    ]);

    app(FavoriteApplyService::class)->applyItem($user, $list, $item);
    $line = $list->items()->first();

    expect($line->name)->toBe('ღვინო')
        ->and($line->quantity)->toBe('1 ბოთლი')
        ->and($line->emoji)->toBe('🍷')
        ->and($line->note)->toBe('საუკეთესო ბოთლი ამ მისამართზეა')
        ->and($line->is_checked)->toBeFalse()
        ->and($line->group_name)->toBeNull()
        ->and($line->user_id)->toBe($user->id)
        ->and($line->is_pinned)->toBeFalse()
        ->and($line->claimed_by)->toBeNull();
});

it('copies only the selected ingredients and names the dish group', function () {
    $user = User::factory()->create();
    $list = app(ShoppingListService::class)->create($user, ['name' => 'საღამო']);
    $favorites = app(FavoriteService::class);
    $dish = $favorites->saveDish($user, ['name' => 'პასტა']);
    $pasta = $favorites->saveDishItem($user, $dish->id, ['name' => 'პასტა', 'note' => 'სპაგეტი']);
    $sauce = $favorites->saveDishItem($user, $dish->id, ['name' => 'სოუსი', 'quantity' => '1']);
    $cheese = $favorites->saveDishItem($user, $dish->id, ['name' => 'ყველი']);
    $other = $favorites->saveDish($user, ['name' => 'სალათი']);
    $leaf = $favorites->saveDishItem($user, $other->id, ['name' => 'სალათის ფოთოლი']);

    app(FavoriteApplyService::class)->applyDish($user, $list, $dish, [$sauce->id, $cheese->id, $leaf->id]);

    $lines = $list->items()->orderBy('id')->get();

    expect($lines)->toHaveCount(2)
        ->and($lines->pluck('name')->all())->toBe(['სოუსი', 'ყველი'])
        ->and($lines->every(fn (ListItem $line): bool => $line->group_name === 'პასტა' && ! $line->is_checked))->toBeTrue()
        ->and($lines->firstWhere('name', 'სოუსი')->quantity)->toBe('1')
        ->and($list->items()->where('name', 'პასტა')->exists())->toBeFalse()
        ->and($list->items()->where('name', 'სალათის ფოთოლი')->exists())->toBeFalse()
        ->and($pasta->fresh()->name)->toBe('პასტა');
});

it('adds a dish into an existing group without merging the same name', function () {
    $user = User::factory()->create();
    $lists = app(ShoppingListService::class);
    $list = $lists->create($user, ['name' => 'საღამო']);
    app(ListItemService::class)->add($user, $list, [
        'name' => 'სოუსი',
        'quantity' => '1',
        'group_name' => 'პასტა',
    ]);

    $favorites = app(FavoriteService::class);
    $dish = $favorites->saveDish($user, ['name' => 'პასტა']);
    $sauce = $favorites->saveDishItem($user, $dish->id, ['name' => 'სოუსი', 'quantity' => '2']);

    app(FavoriteApplyService::class)->applyDish($user, $list, $dish, [$sauce->id]);

    $lines = $list->items()->where('group_name', 'პასტა')->where('name', 'სოუსი')->orderBy('id')->get();

    expect($lines)->toHaveCount(2)
        ->and($lines->pluck('quantity')->all())->toBe(['1', '2']);
});

it('rejects a viewer and writes nothing', function () {
    $owner = User::factory()->create();
    $viewer = User::factory()->create();
    $lists = app(ShoppingListService::class);
    $list = $lists->create($owner, ['name' => 'საღამო']);
    $link = app(ShareLinkService::class)->replace($owner, $list, ListRole::Viewer);
    app(ShareLinkService::class)->join($viewer, $link->token);
    $shared = $lists->findForMember($list->id, $viewer);

    $favorites = app(FavoriteService::class);
    $item = $favorites->saveItem($viewer, ['name' => 'პური', 'note' => 'თბილი']);
    $dish = $favorites->saveDish($viewer, ['name' => 'პასტა']);
    $sauce = $favorites->saveDishItem($viewer, $dish->id, ['name' => 'სოუსი']);
    $cheese = $favorites->saveDishItem($viewer, $dish->id, ['name' => 'ყველი']);
    $apply = app(FavoriteApplyService::class);

    expect(fn () => $apply->applyItem($viewer, $shared, $item))->toThrow(AuthorizationException::class)
        ->and(fn () => $apply->applyDish($viewer, $shared, $dish, [$sauce->id, $cheese->id]))->toThrow(AuthorizationException::class)
        ->and($list->items()->count())->toBe(0);
});

it('does not let an editor apply someone else\'s favorite', function () {
    $owner = User::factory()->create();
    $editor = User::factory()->create();
    $lists = app(ShoppingListService::class);
    $list = $lists->create($owner, ['name' => 'საღამო']);
    $link = app(ShareLinkService::class)->replace($owner, $list, ListRole::Editor);
    app(ShareLinkService::class)->join($editor, $link->token);
    $shared = $lists->findForMember($list->id, $editor);
    $item = app(FavoriteService::class)->saveItem($owner, ['name' => 'ღვინო']);

    expect(fn () => app(FavoriteApplyService::class)->applyItem($editor, $shared, $item))
        ->toThrow(ModelNotFoundException::class)
        ->and($list->items()->count())->toBe(0);
});

it('keeps nothing when a dish copy fails midway', function () {
    $user = User::factory()->create();
    $list = app(ShoppingListService::class)->create($user, ['name' => 'საღამო']);
    $favorites = app(FavoriteService::class);
    $dish = $favorites->saveDish($user, ['name' => 'პასტა']);
    $sauce = $favorites->saveDishItem($user, $dish->id, ['name' => 'სოუსი']);
    $cheese = $favorites->saveDishItem($user, $dish->id, ['name' => 'ყველი']);
    $calls = 0;

    $this->mock(ListItemService::class, function ($mock) use (&$calls, $user, $list): void {
        $mock->shouldReceive('add')->twice()->andReturnUsing(function () use (&$calls, $user, $list) {
            $calls++;

            if ($calls === 2) {
                throw new AuthorizationException;
            }

            return ListItem::query()->create([
                'shopping_list_id' => $list->id,
                'user_id' => $user->id,
                'name' => 'სოუსი',
            ]);
        });
    });

    expect(fn () => app(FavoriteApplyService::class)->applyDish($user, $list, $dish, [$sauce->id, $cheese->id]))
        ->toThrow(AuthorizationException::class)
        ->and($list->items()->count())->toBe(0);
});

it('remembers a list item without changing that line', function () {
    $user = User::factory()->create();
    $list = app(ShoppingListService::class)->create($user, ['name' => 'საღამო']);
    $item = app(ListItemService::class)->add($user, $list, [
        'name' => 'ღვინო',
        'quantity' => '1 ბოთლი',
        'emoji' => '🍷',
        'note' => 'საუკეთესო ბოთლი ამ მისამართზეა',
    ]);
    $item->refresh();
    $before = $item->only(['name', 'quantity', 'emoji', 'note', 'is_checked', 'group_name']);
    $updatedAt = $item->updated_at;

    $saved = app(FavoriteService::class)->rememberItem($user, $item);
    $fresh = $item->fresh();

    expect($saved)->toBeInstanceOf(FavoriteItem::class)
        ->and($saved->name)->toBe('ღვინო')
        ->and($saved->note)->toBe('საუკეთესო ბოთლი ამ მისამართზეა')
        ->and($fresh->only(['name', 'quantity', 'emoji', 'note', 'is_checked', 'group_name']))->toBe($before)
        ->and($fresh->updated_at?->equalTo($updatedAt))->toBeTrue();
});

it('replaces a favorite dish from the list and leaves the list alone', function () {
    $user = User::factory()->create();
    $list = app(ShoppingListService::class)->create($user, ['name' => 'საღამო']);
    $items = app(ListItemService::class);
    $items->add($user, $list, ['name' => 'სპაგეტი', 'group_name' => 'პასტა', 'note' => 'ალ დენტე']);
    $items->add($user, $list, ['name' => 'ყველი', 'group_name' => 'პასტა']);

    $favorites = app(FavoriteService::class);
    $dish = $favorites->saveDish($user, ['name' => 'პასტა', 'emoji' => '🍝', 'note' => 'ჩემი']);
    $favorites->saveDishItem($user, $dish->id, ['name' => 'ძველი სოუსი']);

    $saved = $favorites->rememberDish($user, $list, 'პასტა', true);

    expect($saved->id)->toBe($dish->id)
        ->and($saved->emoji)->toBe('🍝')
        ->and($saved->note)->toBe('ჩემი')
        ->and($saved->items->pluck('name')->all())->toBe(['სპაგეტი', 'ყველი'])
        ->and($saved->items->first()->note)->toBe('ალ დენტე')
        ->and($list->items()->count())->toBe(2)
        ->and(FavoriteDishItem::query()->where('name', 'ძველი სოუსი')->exists())->toBeFalse();
});

it('deletes a dish together with its ingredients', function () {
    $user = User::factory()->create();
    $favorites = app(FavoriteService::class);
    $dish = $favorites->saveDish($user, ['name' => 'პასტა']);
    $favorites->saveDishItem($user, $dish->id, ['name' => 'სოუსი']);
    $favorites->saveDishItem($user, $dish->id, ['name' => 'ყველი']);

    $favorites->deleteDish($user, $dish->id);

    expect(FavoriteDish::query()->find($dish->id))->toBeNull()
        ->and(FavoriteDishItem::query()->where('favorite_dish_id', $dish->id)->count())->toBe(0)
        ->and(FavoriteDish::withTrashed()->find($dish->id))->not->toBeNull()
        ->and(FavoriteDishItem::withTrashed()->where('favorite_dish_id', $dish->id)->count())->toBe(2);
});

it('reorders ingredients inside a dish', function () {
    $user = User::factory()->create();
    $favorites = app(FavoriteService::class);
    $dish = $favorites->saveDish($user, ['name' => 'პასტა']);
    $pasta = $favorites->saveDishItem($user, $dish->id, ['name' => 'პასტა']);
    $sauce = $favorites->saveDishItem($user, $dish->id, ['name' => 'სოუსი']);

    $favorites->moveDishItem($user, $dish->id, $sauce->id, -1);

    expect($favorites->findDish($user, $dish->id)->items->pluck('name')->all())->toBe(['სოუსი', 'პასტა'])
        ->and($pasta->fresh()->name)->toBe('პასტა');
});

it('manages favorites from the settings screen', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('profile.favorites'))
        ->assertOk()
        ->assertSee('რჩეულები')
        ->assertSee('ჯერ არაფერი გაქვს შენახული');

    Livewire::actingAs($user)
        ->test(Favorites::class)
        ->set('itemName', 'ღვინო')
        ->set('itemQuantity', '1 ბოთლი')
        ->set('itemEmoji', '🍷')
        ->set('itemNote', 'საუკეთესო ბოთლი ამ მისამართზეა')
        ->call('saveItem')
        ->assertHasNoErrors()
        ->assertSee('საუკეთესო ბოთლი ამ მისამართზეა')
        ->set('dishName', 'პასტა')
        ->set('dishEmoji', '🍝')
        ->call('saveDish')
        ->assertHasNoErrors()
        ->assertSee('პასტა');

    $item = FavoriteItem::query()->where('user_id', $user->id)->firstOrFail();
    $dish = FavoriteDish::query()->where('user_id', $user->id)->firstOrFail();

    Livewire::actingAs($user)
        ->test(Favorites::class)
        ->call('editItem', $item->id)
        ->set('itemNote', 'ახალი მისამართი')
        ->call('saveItem')
        ->call('startIngredient', $dish->id)
        ->set('ingredientName', 'სოუსი')
        ->set('ingredientNote', 'ხელნაკეთი')
        ->call('saveIngredient', $dish->id)
        ->set('ingredientName', 'ყველი')
        ->call('saveIngredient', $dish->id)
        ->assertHasNoErrors()
        ->assertSee('ხელნაკეთი');

    $sauce = FavoriteDishItem::query()->where('name', 'სოუსი')->firstOrFail();
    $cheese = FavoriteDishItem::query()->where('name', 'ყველი')->firstOrFail();

    Livewire::actingAs($user)
        ->test(Favorites::class)
        ->call('moveIngredient', $dish->id, $cheese->id, 'up')
        ->call('deleteItem', $item->id);

    expect(FavoriteItem::query()->find($item->id))->toBeNull()
        ->and(app(FavoriteService::class)->findDish($user, $dish->id)->items->pluck('id')->all())->toBe([$cheese->id, $sauce->id]);
});

it('fills a new list from favorites and can do it again on a second list', function () {
    $user = User::factory()->create();
    $favorites = app(FavoriteService::class);
    $wine = $favorites->saveItem($user, [
        'name' => 'ღვინო',
        'quantity' => '1 ბოთლი',
        'emoji' => '🍷',
        'note' => 'საუკეთესო ბოთლი ამ მისამართზეა',
    ]);
    $dish = $favorites->saveDish($user, ['name' => 'პასტა']);
    $pasta = $favorites->saveDishItem($user, $dish->id, ['name' => 'პასტა']);
    $sauce = $favorites->saveDishItem($user, $dish->id, ['name' => 'სოუსი']);
    $lists = app(ShoppingListService::class);
    $first = $lists->create($user, ['name' => 'პირველი']);
    $second = $lists->create($user, ['name' => 'მეორე']);

    $this->actingAs($user);

    Livewire::test(Show::class, ['shoppingList' => $first])
        ->assertSee('რჩეულებიდან')
        ->call('openFavorites')
        ->assertSee('საუკეთესო ბოთლი ამ მისამართზეა')
        ->call('applyFavoriteItem', $wine->id)
        ->assertSee('ღვინო')
        ->call('openFavorites')
        ->set('favoriteTab', 'dishes')
        ->call('openFavoriteDish', $dish->id)
        ->assertSee('დაამატე არჩეული ინგრედიენტები')
        ->set('selectedIngredientIds', [(string) $pasta->id])
        ->call('applyFavoriteDish')
        ->assertSee('პასტა')
        ->assertDontSee('სოუსი');

    $line = $first->items()->where('name', 'ღვინო')->first();

    expect($line->note)->toBe('საუკეთესო ბოთლი ამ მისამართზეა')
        ->and($line->group_name)->toBeNull()
        ->and($line->is_checked)->toBeFalse()
        ->and($first->items()->where('group_name', 'პასტა')->pluck('name')->all())->toBe(['პასტა']);

    Livewire::test(Show::class, ['shoppingList' => $second])
        ->call('applyFavoriteItem', $wine->id)
        ->call('openFavoriteDish', $dish->id)
        ->set('selectedIngredientIds', [(string) $pasta->id, (string) $sauce->id])
        ->call('applyFavoriteDish');

    expect($second->items()->where('name', 'ღვინო')->first()->note)->toBe('საუკეთესო ბოთლი ამ მისამართზეა')
        ->and($second->items()->where('group_name', 'პასტა')->orderBy('id')->pluck('name')->all())->toBe(['პასტა', 'სოუსი'])
        ->and($first->items()->count())->toBe(2)
        ->and($wine->fresh()->note)->toBe('საუკეთესო ბოთლი ამ მისამართზეა');
});

it('asks before replacing a favorite saved from the list', function () {
    $user = User::factory()->create();
    $list = app(ShoppingListService::class)->create($user, ['name' => 'საღამო']);
    $favorites = app(FavoriteService::class);
    $favorites->saveItem($user, ['name' => 'ღვინო', 'note' => 'ძველი']);
    $item = app(ListItemService::class)->add($user, $list, [
        'name' => 'ღვინო',
        'note' => 'საუკეთესო ბოთლი ამ მისამართზეა',
    ]);
    $items = app(ListItemService::class);
    $items->add($user, $list, ['name' => 'სოუსი', 'group_name' => 'პასტა']);
    $favorites->saveDish($user, ['name' => 'პასტა']);

    $this->actingAs($user);

    Livewire::test(Show::class, ['shoppingList' => $list])
        ->call('editItem', $item->id)
        ->assertSee('რჩეულებში შენახვა')
        ->call('rememberListItem', $item->id)
        ->assertSet('panel', 'favorite-replace')
        ->call('dismissReplace');

    expect(FavoriteItem::query()->where('user_id', $user->id)->first()->note)->toBe('ძველი')
        ->and($item->fresh()->note)->toBe('საუკეთესო ბოთლი ამ მისამართზეა');

    Livewire::test(Show::class, ['shoppingList' => $list])
        ->call('rememberListItem', $item->id)
        ->call('confirmReplaceFavorite');

    expect(FavoriteItem::query()->where('user_id', $user->id)->first()->note)->toBe('საუკეთესო ბოთლი ამ მისამართზეა');

    Livewire::test(Show::class, ['shoppingList' => $list])
        ->assertSee('კერძის შენახვა')
        ->call('rememberListDish', 'პასტა')
        ->assertSet('panel', 'favorite-replace')
        ->call('confirmReplaceFavorite');

    expect(FavoriteDish::query()->where('user_id', $user->id)->first()->items->pluck('name')->all())->toBe(['სოუსი']);
});

it('hides the favorites picker from a viewer', function () {
    $owner = User::factory()->create();
    $viewer = User::factory()->create();
    $list = app(ShoppingListService::class)->create($owner, ['name' => 'საღამო']);
    $link = app(ShareLinkService::class)->replace($owner, $list, ListRole::Viewer);
    app(ShareLinkService::class)->join($viewer, $link->token);

    Livewire::actingAs($viewer)
        ->test(Show::class, ['shoppingList' => $list])
        ->assertDontSee('რჩეულებიდან');
});

it('explains an empty favorite dish in the picker', function () {
    $user = User::factory()->create();
    $list = app(ShoppingListService::class)->create($user, ['name' => 'საღამო']);
    $dish = app(FavoriteService::class)->saveDish($user, ['name' => 'პასტა']);

    $this->actingAs($user);

    Livewire::test(Show::class, ['shoppingList' => $list])
        ->call('openFavoriteDish', $dish->id)
        ->assertSee('ინგრედიენტი ჯერ არ აქვს')
        ->assertDontSee('დაამატე არჩეული ინგრედიენტები');

    expect($list->items()->count())->toBe(0);
});
