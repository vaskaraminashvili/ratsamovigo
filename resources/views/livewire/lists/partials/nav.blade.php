<nav class="mt-auto flex h-[4.25rem] items-start justify-around bg-white px-6 pt-2 text-xs">
    <a href="{{ route('lists.index') }}" wire:navigate @class(['flex flex-col items-center gap-1', 'text-marker' => request()->routeIs('lists.*'), 'text-bought' => ! request()->routeIs('lists.*')])>
        <svg viewBox="0 0 24 24" class="size-6" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
            <rect x="4" y="4" width="6.5" height="6.5" rx="1.5"></rect>
            <rect x="13.5" y="4" width="6.5" height="6.5" rx="1.5"></rect>
            <rect x="4" y="13.5" width="6.5" height="6.5" rx="1.5"></rect>
            <rect x="13.5" y="13.5" width="6.5" height="6.5" rx="1.5"></rect>
        </svg>
        სიები
    </a>
    <a href="{{ route('profile.edit') }}" wire:navigate @class(['flex flex-col items-center gap-1', 'text-marker' => request()->routeIs('profile.*'), 'text-bought' => ! request()->routeIs('profile.*')])>
        <svg viewBox="0 0 24 24" class="size-6" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
            <circle cx="12" cy="8" r="3.2"></circle>
            <path d="M5.5 19.2c1.4-2.8 3.7-4.2 6.5-4.2s5.1 1.4 6.5 4.2"></path>
        </svg>
        პროფილი
    </a>
</nav>
