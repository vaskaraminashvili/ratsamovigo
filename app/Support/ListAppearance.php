<?php

namespace App\Support;

class ListAppearance
{
    /**
     * @var array<string, array{label: string, class: string}>
     */
    public const PRESETS = [
        'sage' => ['label' => 'ბალახი', 'class' => 'bg-[#e7f5ee] text-zinc-900'],
        'sand' => ['label' => 'ქვიშა', 'class' => 'bg-[#f8f1e3] text-zinc-900'],
        'berry' => ['label' => 'კენკრა', 'class' => 'bg-[#fde8ef] text-zinc-900'],
        'sea' => ['label' => 'ზღვა', 'class' => 'bg-[#e7f3fb] text-zinc-900'],
        'night' => ['label' => 'ღამე', 'class' => 'bg-[#1c1917] text-white'],
    ];

    /**
     * @var list<string>
     */
    public const ACCENTS = [
        '#059669',
        '#d97706',
        '#e11d48',
        '#0284c7',
        '#7c3aed',
        '#3f3f46',
    ];

    /**
     * @var list<string>
     */
    public const EMOJI = ['🥛', '🍞', '🥚', '🧀', '🍎', '🥕', '🍗', '🧼', '💊', '🧴'];

    /**
     * @return array{label: string, class: string}
     */
    public static function preset(string $key): array
    {
        return self::PRESETS[$key] ?? self::PRESETS['sage'];
    }
}
