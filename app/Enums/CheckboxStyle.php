<?php

namespace App\Enums;

enum CheckboxStyle: string
{
    case Box = 'box';
    case Circle = 'circle';
    case Plain = 'plain';

    public function label(): string
    {
        return match ($this) {
            self::Box => 'კვადრატი',
            self::Circle => 'წრე',
            self::Plain => 'უბრალო',
        };
    }
}
