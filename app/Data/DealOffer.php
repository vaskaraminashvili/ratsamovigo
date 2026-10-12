<?php

namespace App\Data;

readonly class DealOffer
{
    public function __construct(
        public string $storeName,
        public string $itemName,
        public string $discount,
        public string $url,
    ) {}
}
