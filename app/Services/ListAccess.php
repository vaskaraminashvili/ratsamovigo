<?php

namespace App\Services;

use App\Enums\ListRole;
use App\Models\ShoppingList;
use App\Models\User;
use App\Repositories\Contracts\ShoppingListRepository;
use Illuminate\Auth\Access\AuthorizationException;

class ListAccess
{
    public function __construct(private ShoppingListRepository $lists) {}

    public function role(User $user, ShoppingList $list): ListRole
    {
        $member = $list->relationLoaded('members')
            ? $list->members->firstWhere('user_id', $user->id)
            : $this->lists->findMember($list, $user);

        if (! $member) {
            throw new AuthorizationException;
        }

        return $member->role;
    }

    public function requireEdit(User $user, ShoppingList $list): void
    {
        if (! $this->role($user, $list)->canEdit()) {
            throw new AuthorizationException;
        }
    }

    public function requireManage(User $user, ShoppingList $list): void
    {
        if (! $this->role($user, $list)->canManage()) {
            throw new AuthorizationException;
        }
    }
}
