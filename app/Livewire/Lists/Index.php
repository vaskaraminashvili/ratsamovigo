<?php

namespace App\Livewire\Lists;

use App\Http\Requests\StoreShoppingListRequest;
use App\Models\User;
use App\Services\ShoppingListService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.lists')]
#[Title('სიები')]
class Index extends Component
{
    public string $name = '';

    public string $emoji = '';

    public function createList(ShoppingListService $lists): void
    {
        $validated = $this->validate((new StoreShoppingListRequest)->rules(), (new StoreShoppingListRequest)->messages());

        $list = $lists->create($this->user(), [
            'name' => $validated['name'],
            'emoji' => $validated['emoji'] ?? null,
        ]);

        $this->redirect(route('lists.show', $list), navigate: true);
    }

    public function render(ShoppingListService $lists): View
    {
        return view('livewire.lists.index', [
            'lists' => $lists->listsFor($this->user()),
        ]);
    }

    private function user(): User
    {
        /** @var User $user */
        $user = Auth::user();

        return $user;
    }
}
