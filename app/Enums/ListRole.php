<?php

namespace App\Enums;

enum ListRole: string
{
    case Owner = 'owner';
    case Editor = 'editor';
    case Viewer = 'viewer';

    public function label(): string
    {
        return match ($this) {
            self::Owner => 'მფლობელი',
            self::Editor => 'რედაქტორი',
            self::Viewer => 'მნახველი',
        };
    }

    public function canEdit(): bool
    {
        return $this === self::Owner || $this === self::Editor;
    }

    public function canManage(): bool
    {
        return $this === self::Owner;
    }
}
