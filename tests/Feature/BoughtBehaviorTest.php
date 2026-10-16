<?php

use App\Enums\BoughtBehavior;
use App\Enums\ListRole;
use App\Livewire\Lists\Show;
use App\Models\ListItem;
use App\Models\User;
use App\Services\ListItemService;
use App\Services\ShareLinkService;
use App\Services\ShoppingListService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;

beforeEach(function () {
    if (! Schema::hasColumn('shopping_lists', 'emoji')) {
        Schema::table('shopping_lists', function (Blueprint $table) {
            $table->string('emoji', 16)->nullable();
            $table->boolean('show_emoji')->default(true);
        });
    }

    if (! Schema::hasColumn('list_items', 'emoji')) {
        Schema::table('list_items', function (Blueprint $table) {
            $table->string('emoji', 16)->nullable();
        });
    }
});

it('starts with struck bought lines and best price off', function () {
    $user = User::factory()->create();
    $list = app(ShoppingListService::class)->create($user, ['name' => 'საღამო']);

    expect($list->fresh()->bought_behavior)->toBe(BoughtBehavior::Strike)
        ->and($list->fresh()->fetch_best_price)->toBeFalse();
});

it('hides a checked line and keeps the row', function () {
    $user = User::factory()->create();
    $list = app(ShoppingListService::class)->create($user, ['name' => 'საღამო']);
    $list->update(['bought_behavior' => BoughtBehavior::Hide]);
    $eggs = app(ListItemService::class)->add($user, $list, ['name' => 'კვერცხი']);
    $bread = app(ListItemService::class)->add($user, $list, ['name' => 'პური']);
    app(ListItemService::class)->toggleCheck($user, $list, $eggs->id);

    $this->actingAs($user);

    Livewire::test(Show::class, ['shoppingList' => $list])
        ->assertDontSee('კვერცხი')
        ->assertSee('პური')
        ->assertSee('დარჩა 1')
        ->assertSee('დამალულია 1')
        ->set('panel', 'menu')
        ->assertSee('თავიდან')
        ->assertDontSee('გასუფთავება')
        ->set('showChecked', true)
        ->assertSee('კვერცხი');

    expect($eggs->fresh()->is_checked)->toBeTrue()
        ->and($eggs->fresh()->trashed())->toBeFalse();
});

it('omits a dish once every ingredient is checked', function () {
    $user = User::factory()->create();
    $list = app(ShoppingListService::class)->create($user, ['name' => 'საღამო']);
    $list->update(['bought_behavior' => BoughtBehavior::Hide]);
    $items = app(ListItemService::class);
    $eggs = $items->add($user, $list, ['name' => 'კვერცხი', 'group_name' => 'სალათი']);
    $tomato = $items->add($user, $list, ['name' => 'პომიდორი', 'group_name' => 'სალათი']);
    $items->toggleCheck($user, $list, $eggs->id);
    $items->toggleCheck($user, $list, $tomato->id);

    $this->actingAs($user);

    Livewire::test(Show::class, ['shoppingList' => $list])
        ->assertDontSee('სალათი')
        ->assertDontSee('კვერცხი')
        ->assertDontSee('პომიდორი')
        ->call('toggleCheck', $tomato->id)
        ->assertSee('სალათი')
        ->assertSee('პომიდორი')
        ->assertDontSee('კვერცხი');
});

it('reopens checked lines without deleting them', function () {
    $user = User::factory()->create();
    $list = app(ShoppingListService::class)->create($user, ['name' => 'საღამო']);
    $list->update(['bought_behavior' => BoughtBehavior::Hide]);
    $eggs = app(ListItemService::class)->add($user, $list, ['name' => 'კვერცხი']);
    app(ListItemService::class)->toggleCheck($user, $list, $eggs->id);

    $this->actingAs($user);

    Livewire::test(Show::class, ['shoppingList' => $list])
        ->call('reopenChecked')
        ->assertSee('კვერცხი');

    $fresh = $eggs->fresh();

    expect($fresh->is_checked)->toBeFalse()
        ->and($fresh->checked_by)->toBeNull()
        ->and($fresh->checked_at)->toBeNull()
        ->and($fresh->trashed())->toBeFalse();
});

it('still soft-deletes checked lines when bought lines stay visible', function () {
    $user = User::factory()->create();
    $list = app(ShoppingListService::class)->create($user, ['name' => 'საღამო']);
    $eggs = app(ListItemService::class)->add($user, $list, ['name' => 'კვერცხი']);
    app(ListItemService::class)->toggleCheck($user, $list, $eggs->id);

    $this->actingAs($user);

    Livewire::test(Show::class, ['shoppingList' => $list])
        ->assertSee('კვერცხი')
        ->set('panel', 'menu')
        ->assertSee('მონიშნულის')
        ->call('clearChecked')
        ->assertDontSee('კვერცხი');

    expect(ListItem::query()->find($eggs->id))->toBeNull()
        ->and(ListItem::withTrashed()->find($eggs->id)->trashed())->toBeTrue();
});

it('keeps checked rows when the list switches back to strikethrough', function () {
    $user = User::factory()->create();
    $list = app(ShoppingListService::class)->create($user, ['name' => 'საღამო']);
    $eggs = app(ListItemService::class)->add($user, $list, ['name' => 'კვერცხი']);
    app(ListItemService::class)->toggleCheck($user, $list, $eggs->id);
    $list->update(['bought_behavior' => BoughtBehavior::Hide]);
    $list->update(['bought_behavior' => BoughtBehavior::Strike]);

    $this->actingAs($user);

    Livewire::test(Show::class, ['shoppingList' => $list])
        ->assertSee('კვერცხი');

    expect($eggs->fresh()->is_checked)->toBeTrue()
        ->and($eggs->fresh()->trashed())->toBeFalse();
});

it('rejects a bought behavior change from a member who is not the owner', function () {
    $owner = User::factory()->create();
    $editor = User::factory()->create();
    $list = app(ShoppingListService::class)->create($owner, ['name' => 'საღამო']);
    $link = app(ShareLinkService::class)->replace($owner, $list, ListRole::Editor);
    app(ShareLinkService::class)->join($editor, $link->token);
    $eggs = app(ListItemService::class)->add($owner, $list, ['name' => 'კვერცხი']);

    $this->actingAs($editor);

    Livewire::test(Show::class, ['shoppingList' => $list])
        ->set('panel', 'menu')
        ->assertDontSee('სწრაფი პარამეტრები')
        ->set('boughtBehavior', BoughtBehavior::Hide->value)
        ->assertForbidden();

    expect($list->fresh()->bought_behavior)->toBe(BoughtBehavior::Strike);

    app(ListItemService::class)->toggleCheck($editor, $list->fresh(), $eggs->id);

    expect($eggs->fresh()->is_checked)->toBeTrue();
});

it('saves best price only when the owner turns the switch on', function () {
    $user = User::factory()->create();
    $list = app(ShoppingListService::class)->create($user, ['name' => 'საღამო']);

    $this->actingAs($user);

    $screen = Livewire::test(Show::class, ['shoppingList' => $list])
        ->assertSet('fetchBestPrice', false)
        ->set('panel', 'menu')
        ->assertSee('სწრაფი პარამეტრები')
        ->assertSee('გაზიარება')
        ->assertSee('სიის წაშლა')
        ->set('boughtBehavior', BoughtBehavior::Hide->value)
        ->set('fetchBestPrice', true)
        ->assertSet('panel', 'menu');

    expect($list->fresh()->bought_behavior)->toBe(BoughtBehavior::Hide)
        ->and($list->fresh()->fetch_best_price)->toBeTrue();

    $screen->set('panel', 'settings')
        ->assertSee('საუკეთესო ფასი')
        ->assertSee('ნაყიდი იხაზება')
        ->call('saveAppearance')
        ->assertHasNoErrors();
});

it('hides checked lines on a view link', function () {
    $owner = User::factory()->create();
    $list = app(ShoppingListService::class)->create($owner, ['name' => 'საღამო']);
    $list->update(['bought_behavior' => BoughtBehavior::Hide]);
    $eggs = app(ListItemService::class)->add($owner, $list, ['name' => 'კვერცხი']);
    app(ListItemService::class)->toggleCheck($owner, $list, $eggs->id);
    $view = app(ShareLinkService::class)->replace($owner, $list, ListRole::Viewer);

    $this->get(route('lists.view', $view->token))
        ->assertOk()
        ->assertDontSee('კვერცხი')
        ->assertSee('დამალულია 1');
});

it('leaves strike lists alone when reopen is called', function () {
    $user = User::factory()->create();
    $list = app(ShoppingListService::class)->create($user, ['name' => 'საღამო']);
    $eggs = app(ListItemService::class)->add($user, $list, ['name' => 'კვერცხი']);
    app(ListItemService::class)->toggleCheck($user, $list, $eggs->id);

    app(ListItemService::class)->reopenChecked($user, $list->fresh());

    expect($eggs->fresh()->is_checked)->toBeTrue();
});

it('blocks a viewer from reopening bought lines', function () {
    $owner = User::factory()->create();
    $viewer = User::factory()->create();
    $list = app(ShoppingListService::class)->create($owner, ['name' => 'საღამო']);
    $list->update(['bought_behavior' => BoughtBehavior::Hide]);
    $link = app(ShareLinkService::class)->replace($owner, $list, ListRole::Viewer);
    app(ShareLinkService::class)->join($viewer, $link->token);

    expect(fn () => app(ListItemService::class)->reopenChecked($viewer, $list->fresh()))
        ->toThrow(AuthorizationException::class);
});
