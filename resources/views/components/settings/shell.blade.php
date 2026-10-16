@props([
    'title',
    'subtitle' => '',
])

@php
    $links = [
        ['route' => 'profile.edit', 'label' => 'პროფილი', 'active' => request()->routeIs('profile.edit')],
        ['route' => 'profile.favorites', 'label' => 'რჩეულები', 'active' => request()->routeIs('profile.favorites')],
        ['route' => 'security.edit', 'label' => 'უსაფრთხოება', 'active' => request()->routeIs('security.edit')],
        ['route' => 'appearance.edit', 'label' => 'სახე', 'active' => request()->routeIs('appearance.edit')],
    ];
@endphp

<div class="aisle-float mx-auto flex h-dvh w-full max-w-[430px] flex-col overflow-hidden bg-phone md:my-6 md:h-[calc(100dvh-3rem)] md:rounded-[36px]">
    <header class="relative shrink-0 overflow-hidden rounded-b-[36px] bg-marker px-5 pb-6 pt-7 text-white">
        <span class="pointer-events-none absolute -right-10 -top-12 size-44 rounded-full bg-lime/90" aria-hidden="true"></span>
        <span class="pointer-events-none absolute -bottom-16 -left-12 size-32 rounded-full bg-coral" aria-hidden="true"></span>
        <span class="pointer-events-none absolute right-24 top-16 size-16 rounded-full bg-white/15" aria-hidden="true"></span>

        <p class="relative text-sm text-white/85">ანგარიში</p>
        <h1 class="relative mt-1 font-sign text-[2rem] font-bold leading-tight">{{ $title }}</h1>
        @if ($subtitle !== '')
            <p class="relative mt-2 text-sm text-white/90">{{ $subtitle }}</p>
        @endif
    </header>

    <nav class="grid shrink-0 grid-cols-2 gap-2 px-5 pt-4" aria-label="პარამეტრები">
        @foreach ($links as $link)
            <a
                href="{{ route($link['route']) }}"
                wire:navigate
                @class([
                    'slip-press flex h-11 items-center justify-center rounded-2xl text-sm font-medium',
                    'bg-ink text-white' => $link['active'],
                    'bg-white text-ink' => ! $link['active'],
                ])
            >{{ $link['label'] }}</a>
        @endforeach
    </nav>

    <div class="min-h-0 flex-1 overflow-y-auto px-5 py-4">
        {{ $slot }}
    </div>

    @include('livewire.lists.partials.nav')
</div>
