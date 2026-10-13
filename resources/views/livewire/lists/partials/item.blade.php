@php
    $checked = $item->is_checked;
    $shape = match ($list->checkbox_style->value) {
        'box' => 'rounded-[7px]',
        'plain' => 'border-transparent',
        default => 'rounded-full',
    };
    $filled = $checked && $list->checkbox_style->value !== 'plain';
    $tint = $item->claimer ? \App\Support\ListAppearance::tint((string) $item->claimer->id) : null;
    $itemPhoto = $item->photoUrl();
@endphp

<div class="flex items-center gap-3 py-2">
    @if ($canEdit)
        <button
            type="button"
            wire:click="toggleCheck({{ $item->id }})"
            aria-label="{{ $checked ? 'მონიშვნის მოხსნა' : 'მონიშვნა' }}"
            @class(['slip-box flex size-6 shrink-0 items-center justify-center border-2 text-white', $shape, 'border-[#d9d4e6] bg-white' => ! $filled])
            @if ($filled) style="background: {{ $list->accent_color }}; border-color: {{ $list->accent_color }}" @endif
        >
            @if ($checked)
                <svg viewBox="0 0 16 16" class="size-3.5" fill="none" stroke="currentColor" stroke-width="2.4" aria-hidden="true"><path d="M3.5 8.2 6.4 11 12.5 4.8"></path></svg>
            @endif
        </button>
    @else
        <span
            role="img"
            aria-label="{{ $checked ? 'მონიშნულია' : 'მონიშნული არ არის' }}"
            @class(['flex size-6 shrink-0 items-center justify-center border-2 text-white', $shape, 'border-[#d9d4e6] bg-white' => ! $filled])
            @if ($filled) style="background: {{ $list->accent_color }}; border-color: {{ $list->accent_color }}" @endif
        >
            @if ($checked)
                <svg viewBox="0 0 16 16" class="size-3.5" fill="none" stroke="currentColor" stroke-width="2.4" aria-hidden="true"><path d="M3.5 8.2 6.4 11 12.5 4.8"></path></svg>
            @endif
        </span>
    @endif

    @if ($itemPhoto)
        <img src="{{ $itemPhoto }}" alt="" class="size-9 shrink-0 rounded-full object-cover">
    @endif

    @if ($canEdit)
        <button type="button" wire:click="editItem({{ $item->id }})" class="min-w-0 flex-1 text-left">
            <span @class(['block truncate text-[15px] font-medium', 'text-bought line-through' => $checked])>
                @if ($list->show_emoji && $item->emoji)
                    <span class="mr-1">{{ $item->emoji }}</span>
                @endif
                {{ $item->name }}
            </span>
            @if ($list->show_quantity && $item->quantity)
                <span class="mt-0.5 block text-sm text-bought">{{ $item->quantity }}</span>
            @endif
        </button>
    @else
        <div class="min-w-0 flex-1">
            <p @class(['truncate text-[15px] font-medium', 'text-bought line-through' => $checked])>
                @if ($list->show_emoji && $item->emoji)
                    <span class="mr-1">{{ $item->emoji }}</span>
                @endif
                {{ $item->name }}
            </p>
            @if ($list->show_quantity && $item->quantity)
                <p class="mt-0.5 text-sm text-bought">{{ $item->quantity }}</p>
            @endif
        </div>
    @endif

    @if ($item->claimer && $tint)
        @if ($canEdit && $item->claimed_by === auth()->id())
            <button type="button" wire:click="toggleClaim({{ $item->id }})" class="flex h-8 max-w-[9.5rem] shrink-0 items-center gap-1.5 rounded-full px-1.5 pr-3 text-sm font-medium" style="background: {{ $tint['bg'] }}; color: {{ $tint['ink'] }}" aria-label="აღარ ვიყიდი">
                <span class="flex size-6 items-center justify-center rounded-full bg-white/70 text-[11px]">{{ $item->claimer->initials() }}</span>
                <span class="truncate">{{ $item->claimer->name }}</span>
            </button>
        @else
            <span class="flex h-8 max-w-[9.5rem] shrink-0 items-center gap-1.5 rounded-full px-1.5 pr-3 text-sm font-medium" style="background: {{ $tint['bg'] }}; color: {{ $tint['ink'] }}">
                <span class="flex size-6 items-center justify-center rounded-full bg-white/70 text-[11px]">{{ $item->claimer->initials() }}</span>
                <span class="truncate">{{ $item->claimer->name }}</span>
            </span>
        @endif
    @elseif ($canEdit)
        <button type="button" wire:click="toggleClaim({{ $item->id }})" class="h-8 shrink-0 rounded-full border border-[#e4deef] bg-white px-3 text-sm text-bought">ვიყიდი</button>
    @endif
</div>
