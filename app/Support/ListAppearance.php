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
        '#7b5cf6',
        '#7a3ff2',
        '#c43122',
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
     * @var array<string, string>
     */
    public const UNITS = [
        'ცალი' => 'ცალი',
        'გ' => 'გრამი',
        'კგ' => 'კილოგრამი',
        'მლ' => 'მილილიტრი',
        'ლ' => 'ლიტრი',
        'შეკვრა' => 'შეკვრა',
        'კონა' => 'კონა',
    ];

    /**
     * @var list<array{bg: string, ink: string}>
     */
    public const TINTS = [
        ['bg' => '#ffd9cf', 'ink' => '#c4533a'],
        ['bg' => '#ffd6ea', 'ink' => '#c43b78'],
        ['bg' => '#e4dcff', 'ink' => '#5b3fd4'],
        ['bg' => '#e7f7b8', 'ink' => '#4d7a12'],
        ['bg' => '#d9f0ff', 'ink' => '#1d6f9a'],
    ];

    /**
     * @return array{label: string, class: string}
     */
    public static function preset(string $key): array
    {
        return self::PRESETS[$key] ?? self::PRESETS['sage'];
    }

    /**
     * @return array{bg: string, ink: string}
     */
    public static function tint(string $seed): array
    {
        return self::TINTS[abs(crc32($seed)) % count(self::TINTS)];
    }
}
