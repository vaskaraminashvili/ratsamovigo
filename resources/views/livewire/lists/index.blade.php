<div class="mx-auto min-h-dvh w-full max-w-lg">
    <header class="sticky top-0 z-20 bg-crate/95 px-4 pb-4 pt-5 backdrop-blur">
        <div class="flex items-end justify-between gap-4">
            <h1 class="font-sign text-4xl leading-none">სიები</h1>
            <div class="flex items-center gap-4 pb-1 text-sm text-bought">
                <a href="{{ route('profile.edit') }}" wire:navigate>პარამეტრები</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit">გასვლა</button>
                </form>
            </div>
        </div>

        <form wire:submit="createList" class="mt-5 flex items-center gap-2">
            <label class="sr-only" for="list-emoji">ემოჯი</label>
            <input id="list-emoji" wire:model="emoji" type="text" maxlength="8" placeholder="🛒" class="h-12 w-14 border border-ink/15 bg-slip text-center text-base">
            <label class="sr-only" for="list-name">სიის სახელი</label>
            <input id="list-name" wire:model="name" type="text" placeholder="სასურსათო" class="h-12 min-w-0 flex-1 border border-ink/15 bg-slip px-3 text-base">
            <button type="submit" class="h-12 shrink-0 bg-tomato px-4 text-sm font-medium text-white">შექმნა</button>
        </form>
        @error('name') <p class="mt-2 text-sm text-wine">{{ $message }}</p> @enderror
    </header>

    <div class="px-4 pb-16">
        <div class="bg-slip">
            @forelse ($lists as $list)
                <a href="{{ route('lists.show', $list) }}" wire:navigate class="flex items-center gap-3 border-b border-rule px-4 py-4 last:border-b-0">
                    <span class="size-2.5 shrink-0" style="background: {{ $list->accent_color }}"></span>
                    <span class="text-2xl leading-none">{{ $list->emoji ?: '📝' }}</span>
                    <span class="min-w-0 flex-1 truncate text-base font-medium">{{ $list->name }}</span>
                    <span class="shrink-0">
                        <span class="font-sign text-xl leading-none text-tomato">{{ $list->open_items_count }}</span>
                        <span class="text-sm text-bought">დარჩა</span>
                    </span>
                </a>
            @empty
                <p class="px-4 py-10 text-bought">პირველი სია აქ დაიწყება.</p>
            @endforelse
        </div>
    </div>
</div>
