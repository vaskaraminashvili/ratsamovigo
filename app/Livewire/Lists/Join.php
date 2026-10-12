<?php

namespace App\Livewire\Lists;

use App\Models\User;
use App\Services\ShareLinkService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.lists')]
#[Title('მოწვევა')]
class Join extends Component
{
    public function mount(string $token, ShareLinkService $links): void
    {
        $list = $links->join($this->user(), $token);

        $this->redirect(route('lists.show', $list), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.lists.join');
    }

    private function user(): User
    {
        /** @var User $user */
        $user = Auth::user();

        return $user;
    }
}
