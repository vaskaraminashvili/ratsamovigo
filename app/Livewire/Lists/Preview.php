<?php

namespace App\Livewire\Lists;

use App\Contracts\DealOfferProvider;
use App\Enums\BoughtBehavior;
use App\Enums\ListRole;
use App\Services\ShareLinkService;
use App\Support\ListActivity;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.lists')]
#[Title('სია')]
class Preview extends Component
{
    #[Locked]
    public string $token = '';

    public string $screen = 'list';

    public bool $showChecked = false;

    public function mount(string $token, ShareLinkService $links): void
    {
        $link = $links->find($token);

        if ($link->role === ListRole::Editor) {
            $this->redirect(route('lists.join', ['token' => $token]));

            return;
        }

        if ($link->role !== ListRole::Viewer) {
            throw (new ModelNotFoundException)->setModel($link::class);
        }

        $this->token = $token;
    }

    public function render(ShareLinkService $links, DealOfferProvider $offers): View
    {
        $list = $links->preview($this->token);
        $hidesBought = $list->bought_behavior === BoughtBehavior::Hide;
        $visible = $hidesBought && ! $this->showChecked
            ? $list->items->where('is_checked', false)->values()
            : $list->items;
        $sort = fn ($item): string => ($item->is_checked ? '1' : '0').($item->is_pinned ? '0' : '1').$item->name;

        return view('livewire.lists.show', [
            'list' => $list,
            'readonly' => true,
            'canEdit' => false,
            'canManage' => false,
            'panel' => '',
            'offers' => $offers->forList($list),
            'openCount' => $list->items->where('is_checked', false)->count(),
            'pickedCount' => $list->items->where('is_checked', true)->count(),
            'hidesBought' => $hidesBought,
            'hiddenCount' => $hidesBought ? $list->items->where('is_checked', true)->count() : 0,
            'looseItems' => $visible->filter(fn ($item) => blank($item->group_name))->sortBy($sort)->values(),
            'dishes' => $visible->filter(fn ($item) => filled($item->group_name))->groupBy('group_name')->map(fn ($items) => $items->sortBy($sort)->values()),
            'polls' => $list->polls->sortByDesc('is_pinned')->values(),
            'activity' => ListActivity::events($list),
        ])->title($list->name);
    }
}
