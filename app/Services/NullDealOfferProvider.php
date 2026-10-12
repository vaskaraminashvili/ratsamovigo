<?php

namespace App\Services;

use App\Contracts\DealOfferProvider;
use App\Models\ShoppingList;
use Illuminate\Support\Collection;

class NullDealOfferProvider implements DealOfferProvider
{
    public function forList(ShoppingList $list): Collection
    {
        return collect();
    }
}
