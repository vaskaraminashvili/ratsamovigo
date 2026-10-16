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
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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

    expect($item->fresh()->likes)->toHaveCount(1)
        ->and($item->fresh()->is_checked)->toBeFalse();

    expect(fn () => app(ListItemService::class)->toggleCheck($viewer, $shared, $item->id))
        ->toThrow(AuthorizationException::class);
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

it('adds a poll from the list screen', function () {
    $user = User::factory()->create();
    $list = app(ShoppingListService::class)->create($user, ['name' => 'სასმელი']);

    $this->actingAs($user);

    Livewire::test(Show::class, ['shoppingList' => $list])
        ->set('pollQuestion', 'რა ვიყიდოთ?')
        ->set('pollOptions', "კოლა\nფანტა")
        ->call('addPoll')
        ->assertHasNoErrors()
        ->assertSee('რა ვიყიდოთ?');

    expect($list->polls()->count())->toBe(1);
});

it('saves appearance and stores the background with media library', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    $list = app(ShoppingListService::class)->create($user, ['name' => 'სასურსათო']);

    $this->actingAs($user);

    Livewire::test(Show::class, ['shoppingList' => $list])
        ->set('listName', 'საღამო')
        ->set('listEmoji', '🍷')
        ->set('showEmoji', false)
        ->set('showAuthor', false)
        ->set('backgroundUpload', UploadedFile::fake()->image('kitchen.jpg'))
        ->call('saveAppearance')
        ->assertHasNoErrors()
        ->assertSet('panel', '');

    $fresh = $list->fresh();

    expect($fresh->name)->toBe('საღამო')
        ->and($fresh->emoji)->toBe('🍷')
        ->and($fresh->show_emoji)->toBeFalse()
        ->and($fresh->show_author)->toBeFalse()
        ->and($fresh->getFirstMedia('background'))->not->toBeNull()
        ->and($fresh->backgroundUrl())->toStartWith('/storage/');
});

it('removes a stored background', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    $list = app(ShoppingListService::class)->create($user, ['name' => 'სასურსათო']);

    $this->actingAs($user);

    Livewire::test(Show::class, ['shoppingList' => $list])
        ->set('backgroundUpload', UploadedFile::fake()->image('kitchen.jpg'))
        ->call('saveAppearance')
        ->call('removeBackground')
        ->assertHasNoErrors();

    expect($list->fresh()->getFirstMedia('background'))->toBeNull();
});

it('stores an item photo with the media library', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    $list = app(ShoppingListService::class)->create($user, ['name' => 'სასურსათო']);

    $this->actingAs($user);

    Livewire::test(Show::class, ['shoppingList' => $list])
        ->set('itemName', 'პური')
        ->set('itemPhoto', UploadedFile::fake()->image('bread.jpg'))
        ->call('addItem')
        ->assertHasNoErrors();

    expect($list->items()->first()->getFirstMedia('photo'))->not->toBeNull();
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

it('shows a read only list from the view link', function () {
    $owner = User::factory()->create();
    $list = app(ShoppingListService::class)->create($owner, ['name' => 'სასურსათო']);
    $item = app(ListItemService::class)->add($owner, $list, ['name' => 'რძე']);
    app(ListItemService::class)->toggleCheck($owner, $list, $item->id);
    app(PollService::class)->create($owner, $list, 'რა ვიყიდოთ?', ['კოლა', 'ფანტა']);

    $view = app(ShareLinkService::class)->replace($owner, $list, ListRole::Viewer);

    $this->get(route('lists.view', $view->token))
        ->assertOk()
        ->assertSee('სასურსათო')
        ->assertSee('რძე')
        ->assertSee('კოლა')
        ->assertSee('მხოლოდ ნახვა')
        ->assertSee('მონიშნულია')
        ->assertDontSee('toggleCheck', false)
        ->assertDontSee('voteOption', false)
        ->assertDontSee('ვიყიდი')
        ->assertDontSee('დამატება');

    expect($list->members()->count())->toBe(1)
        ->and($item->fresh()->is_checked)->toBeTrue();
});

it('turns an edit link into membership after login', function () {
    $owner = User::factory()->create();
    $friend = User::factory()->create();
    $list = app(ShoppingListService::class)->create($owner, ['name' => 'საღამო']);
    $edit = app(ShareLinkService::class)->replace($owner, $list, ListRole::Editor);

    $this->get(route('lists.join', $edit->token))
        ->assertOk()
        ->assertSee('საღამო')
        ->assertSee('რეგისტრაცია')
        ->assertSee('შესვლა');

    $this->post(route('login.store'), [
        'email' => $friend->email,
        'password' => 'password',
    ])->assertRedirect(route('lists.join', $edit->token));

    $this->get(route('lists.join', $edit->token))
        ->assertRedirect(route('lists.show', $list));

    $member = $list->members()->where('user_id', $friend->id)->first();

    expect($member->role)->toBe(ListRole::Editor);

    $shared = app(ShoppingListService::class)->findForMember($list->id, $friend);
    app(ListItemService::class)->add($friend, $shared, ['name' => 'პური']);

    expect($list->items()->where('name', 'პური')->exists())->toBeTrue();
});

it('keeps an edit invite until a new account verifies its email', function () {
    $owner = User::factory()->create();
    $list = app(ShoppingListService::class)->create($owner, ['name' => 'საღამო']);
    $edit = app(ShareLinkService::class)->replace($owner, $list, ListRole::Editor);

    $this->get(route('lists.join', $edit->token));

    $this->post(route('register.store'), [
        'name' => 'Friend',
        'email' => 'friend@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertRedirect(route('lists.join', $edit->token));

    $friend = User::query()->where('email', 'friend@example.com')->first();

    $this->get(route('lists.join', $edit->token))
        ->assertRedirect(route('verification.notice'));

    expect($list->members()->where('user_id', $friend->id)->exists())->toBeFalse();

    $friend->markEmailAsVerified();

    $this->actingAs($friend->fresh())
        ->get(route('lists.join', $edit->token))
        ->assertRedirect(route('lists.show', $list));

    expect($list->members()->where('user_id', $friend->id)->first()->role)->toBe(ListRole::Editor);
});

it('sends a view token on the edit invite path to the read only page', function () {
    $owner = User::factory()->create();
    $list = app(ShoppingListService::class)->create($owner, ['name' => 'სასურსათო']);
    $view = app(ShareLinkService::class)->replace($owner, $list, ListRole::Viewer);
    $edit = app(ShareLinkService::class)->replace($owner, $list, ListRole::Editor);

    $this->get(route('lists.join', $view->token))
        ->assertRedirect(route('lists.view', $view->token));

    $this->get(route('lists.view', $edit->token))
        ->assertRedirect(route('lists.join', $edit->token));

    $this->get(route('lists.view', 'missing-token'))->assertNotFound();
    $this->get(route('lists.join', 'missing-token'))->assertNotFound();

    expect($list->members()->count())->toBe(1);
});

it('gives the owner a view link and an edit link to copy', function () {
    $owner = User::factory()->create();
    $list = app(ShoppingListService::class)->create($owner, ['name' => 'სასურსათო']);

    $this->actingAs($owner);

    $component = Livewire::test(Show::class, ['shoppingList' => $list])
        ->call('openPeople')
        ->assertSet('panel', 'share')
        ->assertSee('ნახვის ბმული')
        ->assertSee('რედაქტირების ბმული');

    $view = $list->shareLinks()->where('role', ListRole::Viewer)->first();
    $edit = $list->shareLinks()->where('role', ListRole::Editor)->first();

    $component
        ->assertSee(route('lists.view', $view->token), false)
        ->assertSee(route('lists.join', $edit->token), false)
        ->call('share', 'viewer');

    $this->get(route('lists.view', $view->token))->assertNotFound();
    expect($list->shareLinks()->where('role', ListRole::Viewer)->where('token', $view->token)->exists())->toBeFalse();
});

it('asks the owner to confirm before deleting a list from the menu', function () {
    $owner = User::factory()->create();
    $list = app(ShoppingListService::class)->create($owner, ['name' => 'სასურსათო']);

    $this->actingAs($owner);

    Livewire::test(Show::class, ['shoppingList' => $list])
        ->set('panel', 'menu')
        ->assertSee('სიის წაშლა')
        ->set('panel', 'settings')
        ->assertDontSee('სიის წაშლა')
        ->set('panel', 'delete')
        ->assertSee('დაბრუნება შეუძლებელია')
        ->call('deleteList')
        ->assertRedirect(route('lists.index'));

    expect($list->fresh()->trashed())->toBeTrue();
});

it('keeps list deletion away from an editor', function () {
    $owner = User::factory()->create();
    $editor = User::factory()->create();
    $list = app(ShoppingListService::class)->create($owner, ['name' => 'სასურსათო']);
    $link = app(ShareLinkService::class)->replace($owner, $list, ListRole::Editor);
    app(ShareLinkService::class)->join($editor, $link->token);

    $this->actingAs($editor);

    Livewire::test(Show::class, ['shoppingList' => $list])
        ->set('panel', 'menu')
        ->assertDontSee('სიის წაშლა')
        ->set('panel', 'delete')
        ->assertDontSee('დაბრუნება შეუძლებელია')
        ->call('deleteList')
        ->assertForbidden();

    expect($list->fresh()->trashed())->toBeFalse();
});
