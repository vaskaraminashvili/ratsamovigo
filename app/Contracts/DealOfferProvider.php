<?php

namespace App\Contracts;

use App\Data\DealOffer;
use App\Models\ShoppingList;
use Illuminate\Support\Collection;

interface DealOfferProvider
{
    /**
     * @return Collection<int, DealOffer>
     */
    public function forList(ShoppingList $list): Collection;
}
