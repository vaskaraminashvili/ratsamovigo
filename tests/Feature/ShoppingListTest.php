<?php

use App\Enums\ListRole;
use App\Livewire\Lists\Index;
use App\Livewire\Lists\Show;
use App\Models\User;
use App\Services\ListItemService;
use App\Services\PollService;
use App\Services\ShareLinkService;
use App\Services\ShoppingListService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;

it('creates a list and adds an item', function () {
    $user = User::factory()->create();
    $lists = app(ShoppingListService::class);

    $list = $lists->create($user, ['name' => 'სასურსათო', 'emoji' => '🛒']);

    app(ListItemService::class)->add($user, $list, [
        'name' => 'რძე',
        'quantity' => '2',
        'emoji' => '🥛',
    ]);

    $fresh = $lists->findForMember($list->id, $user);

    expect($fresh->items)->toHaveCount(1)
        ->and($fresh->members->first()->role)->toBe(ListRole::Owner)
        ->and($fresh->items->first()->name)->toBe('რძე');
});

it('lets a viewer like and vote but not add an item', function () {
    $owner = User::factory()->create();
    $viewer = User::factory()->create();
    $lists = app(ShoppingListService::class);
    $list = $lists->create($owner, ['name' => 'საღამო']);

    $link = app(ShareLinkService::class)->replace($owner, $list, ListRole::Viewer);
    app(ShareLinkService::class)->join($viewer, $link->token);

    $shared = $lists->findForMember($list->id, $viewer);

    app(PollService::class)->create($owner, $list, 'რა ვიყიდოთ?', ['კოლა', 'ფანტა']);
    $poll = $list->polls()->with('options')->first();

    app(PollService::class)->vote($viewer, $shared, $poll->id, $poll->options->first()->id);

    expect(fn () => app(ListItemService::class)->add($viewer, $shared, ['name' => 'პური']))
        ->toThrow(AuthorizationException::class);

    $item = app(ListItemService::class)->add($owner, $list, ['name' => 'პური']);
    app(ListItemService::class)->toggleLike($viewer, $shared, $item->id);

    expect($item->fresh()->likes)->toHaveCount(1);
});

it('turns the winning poll option into an item', function () {
    $user = User::factory()->create();
    $list = app(ShoppingListService::class)->create($user, ['name' => 'სასმელი']);
    $poll = app(PollService::class)->create($user, $list, 'რა ვიყიდოთ?', ['კოლა', 'ფანტა']);
    $cola = $poll->options()->where('label', 'კოლა')->first();

    app(PollService::class)->vote($user, $list, $poll->id, $cola->id);
    $item = app(PollService::class)->promoteWinner($user, $list, $poll->id);

    expect($item->name)->toBe('კოლა')
        ->and($list->polls()->count())->toBe(0);
});

it('allows more than two lists while plan limits are off', function () {
    $user = User::factory()->create();
    $lists = app(ShoppingListService::class);

    $lists->create($user, ['name' => 'ერთი']);
    $lists->create($user, ['name' => 'ორი']);
    $lists->create($user, ['name' => 'სამი']);

    expect($lists->listsFor($user))->toHaveCount(3);
});

it('stops a third list when the free plan is enforced', function () {
    config(['plans.enforce' => true]);

    $user = User::factory()->create();
    $lists = app(ShoppingListService::class);
    $lists->create($user, ['name' => 'ერთი']);
    $lists->create($user, ['name' => 'ორი']);

    expect(fn () => $lists->create($user, ['name' => 'სამი']))
        ->toThrow(ValidationException::class);
});

it('shows the list screen to the owner', function () {
    $user = User::factory()->create();
    $list = app(ShoppingListService::class)->create($user, ['name' => 'სასურსათო']);

    $this->actingAs($user);

    Livewire::test(Show::class, ['shoppingList' => $list])
        ->set('itemName', 'პური')
        ->call('addItem')
        ->assertSee('პური');
});

it('renders the lists page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('lists.index'))
        ->assertOk()
        ->assertSee('ახალი სია');

    Livewire::actingAs($user)
        ->test(Index::class)
        ->set('name', 'აფთიაქი')
        ->call('createList')
        ->assertHasNoErrors()
        ->assertRedirect(route('lists.show', 1));
});
