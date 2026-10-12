<?php

namespace App\Services;

use App\Enums\UserPlan;
use App\Models\ShoppingList;
use App\Models\User;
use App\Repositories\Contracts\ShoppingListRepository;

class EntitlementService
{
    public function __construct(private ShoppingListRepository $lists) {}

    public function canCreateList(User $user): bool
    {
        $limit = $this->limit($user, 'owned_lists');

        if ($limit === null) {
            return true;
        }

        return $this->lists->ownedCount($user) < $limit;
    }

    public function canAddMember(ShoppingList $list): bool
    {
        $limit = $this->limit($list->owner, 'members_per_list');

        if ($limit === null) {
            return true;
        }

        return $this->lists->memberCount($list) < $limit;
    }

    public function canUploadBackground(User $owner): bool
    {
        return (bool) $this->flag($owner, 'upload_background');
    }

    public function canUploadItemPhoto(User $owner): bool
    {
        return (bool) $this->flag($owner, 'item_photo');
    }

    private function limit(User $user, string $key): ?int
    {
        if (! config('plans.enforce')) {
            return null;
        }

        $value = config('plans.limits.'.$this->planKey($user).'.'.$key);

        return $value === null ? null : (int) $value;
    }

    private function flag(User $user, string $key): bool
    {
        if (! config('plans.enforce')) {
            return true;
        }

        return (bool) config('plans.limits.'.$this->planKey($user).'.'.$key);
    }

    private function planKey(User $user): string
    {
        $plan = $user->plan instanceof UserPlan ? $user->plan->value : (string) $user->plan;

        return $plan !== '' ? $plan : UserPlan::Free->value;
    }
}
