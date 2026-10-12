<?php

namespace App\Services;

use App\Enums\ListRole;
use App\Models\ShareLink;
use App\Models\ShoppingList;
use App\Models\User;
use App\Repositories\Contracts\ShoppingListRepository;
use Illuminate\Validation\ValidationException;

class ShareLinkService
{
    public function __construct(
        private ShoppingListRepository $lists,
        private EntitlementService $entitlements,
        private ListAccess $access,
    ) {}

    public function replace(User $user, ShoppingList $list, ListRole $role): ShareLink
    {
        $this->access->requireManage($user, $list);

        if ($role === ListRole::Owner) {
            throw ValidationException::withMessages([
                'role' => 'ბმული შეიძლება იყოს ნახვა ან რედაქტირება.',
            ]);
        }

        return $this->lists->replaceShareLink($list, $user, $role);
    }

    public function join(User $user, string $token): ShoppingList
    {
        $list = $this->lists->findByShareToken($token);
        $link = $list->shareLinks->firstWhere('token', $token);

        if (! $link) {
            throw ValidationException::withMessages([
                'token' => 'ბმული აღარ მოქმედებს.',
            ]);
        }

        $member = $this->lists->findMember($list, $user);

        if ($member?->role === ListRole::Owner) {
            return $this->lists->findForMember($list->id, $user);
        }

        if ($member && $member->role->canEdit() && ! $link->role->canEdit()) {
            return $this->lists->findForMember($list->id, $user);
        }

        if (! $member && ! $this->entitlements->canAddMember($list)) {
            throw ValidationException::withMessages([
                'token' => 'ამ სიაზე წევრების ლიმიტი შევსებულია.',
            ]);
        }

        if (! $member) {
            $this->lists->addMember($list, $user, $link->role);
        } elseif ($link->role->canEdit() && $member->role === ListRole::Viewer) {
            $this->lists->updateMemberRole($member, ListRole::Editor);
        }

        return $this->lists->findForMember($list->id, $user);
    }
}
