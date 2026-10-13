<?php

namespace App\Services;

use App\Enums\ListRole;
use App\Models\ShareLink;
use App\Models\ShoppingList;
use App\Models\User;
use App\Repositories\Contracts\ShoppingListRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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
        $this->assertShareRole($role);

        return $this->lists->replaceShareLink($list, $user, $role);
    }

    public function ensure(User $user, ShoppingList $list, ListRole $role): ShareLink
    {
        $this->access->requireManage($user, $list);
        $this->assertShareRole($role);

        return $this->lists->latestShareLink($list, $role)
            ?? $this->lists->replaceShareLink($list, $user, $role);
    }

    public function find(string $token): ShareLink
    {
        $link = $this->lists->findShareLink($token);

        if ($link->list === null) {
            throw (new ModelNotFoundException)->setModel(ShareLink::class);
        }

        return $link;
    }

    public function preview(string $token): ShoppingList
    {
        $link = $this->find($token);

        if ($link->role !== ListRole::Viewer) {
            throw (new ModelNotFoundException)->setModel(ShareLink::class);
        }

        return $this->lists->findByShareToken($token);
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

    private function assertShareRole(ListRole $role): void
    {
        if ($role === ListRole::Owner) {
            throw ValidationException::withMessages([
                'role' => 'ბმული შეიძლება იყოს ნახვა ან რედაქტირება.',
            ]);
        }
    }
}
