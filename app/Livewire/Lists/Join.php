<?php

namespace App\Livewire\Lists;

use App\Enums\ListRole;
use App\Models\User;
use App\Services\ShareLinkService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.lists')]
#[Title('მოწვევა')]
class Join extends Component
{
    public string $listName = '';

    public string $error = '';

    public function mount(string $token, ShareLinkService $links): void
    {
        $link = $links->find($token);

        if ($link->role !== ListRole::Editor) {
            $this->redirect(route('lists.view', ['token' => $token]));

            return;
        }

        $this->listName = $link->list->name;
        session()->put('url.intended', route('lists.join', ['token' => $token]));

        $user = Auth::user();

        if (! $user instanceof User) {
            return;
        }

        if (! $user->hasVerifiedEmail()) {
            $this->redirect(route('verification.notice'));

            return;
        }

        try {
            $list = $links->join($user, $token);
        } catch (ValidationException $exception) {
            $this->error = $exception->validator->errors()->first() ?? 'ბმული აღარ მოქმედებს.';

            return;
        }

        session()->forget('url.intended');

        $this->redirect(route('lists.show', $list));
    }

    public function render(): View
    {
        return view('livewire.lists.join')->title($this->listName !== '' ? $this->listName : 'მოწვევა');
    }
}
