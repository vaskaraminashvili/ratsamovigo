<?php

namespace App\Enums;

enum BoughtBehavior: string
{
    case Strike = 'strike';
    case Hide = 'hide';

    public function label(): string
    {
        return match ($this) {
            self::Strike => 'ნაყიდი იხაზება',
            self::Hide => 'ნაყიდი იმალება',
        };
    }
}
