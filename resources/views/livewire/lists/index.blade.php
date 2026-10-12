<div class="mx-auto min-h-dvh w-full max-w-lg bg-white">
    <header class="sticky top-0 z-20 flex h-14 items-center justify-between border-b border-zinc-200 bg-white/95 px-4 backdrop-blur">
        <a href="{{ route('lists.index') }}" class="text-lg font-semibold" wire:navigate>სიები</a>
        <div class="flex items-center gap-3 text-sm">
            <a href="{{ route('profile.edit') }}" class="text-zinc-600" wire:navigate>პარამეტრები</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-zinc-600">გასვლა</button>
            </form>
        </div>
    </header>

    <div class="px-4 pb-28 pt-4">
        <form wire:submit="createList" class="space-y-3">
            <label class="block">
                <span class="mb-1 block text-sm font-medium">ახალი სია</span>
                <input wire:model="name" type="text" placeholder="სასურსათო" class="h-12 w-full rounded-2xl border border-zinc-300 px-4 text-base outline-none focus:border-emerald-600">
            </label>
            @error('name') <p class="text-sm text-rose-600">{{ $message }}</p> @enderror
            <label class="block">
                <span class="mb-1 block text-sm font-medium">ემოჯი</span>
                <input wire:model="emoji" type="text" maxlength="8" placeholder="🛒" class="h-12 w-full rounded-2xl border border-zinc-300 px-4 text-base outline-none focus:border-emerald-600">
            </label>
            <button type="submit" class="h-12 w-full rounded-2xl bg-emerald-600 text-base font-semibold text-white">შექმნა</button>
        </form>

        <div class="mt-6 space-y-3">
            @forelse ($lists as $list)
                <a href="{{ route('lists.show', $list) }}" wire:navigate class="flex items-center gap-3 rounded-2xl border border-zinc-200 p-4" style="border-left: 6px solid {{ $list->accent_color }}">
                    <span class="text-2xl">{{ $list->emoji ?: '📝' }}</span>
                    <span class="min-w-0 flex-1">
                        <span class="block truncate text-base font-semibold">{{ $list->name }}</span>
                        <span class="text-sm text-zinc-500">დარჩენილი: {{ $list->open_items_count }}</span>
                    </span>
                </a>
            @empty
                <p class="rounded-2xl bg-zinc-50 px-4 py-8 text-center text-zinc-500">ჯერ სია არ გაქვთ.</p>
            @endforelse
        </div>
    </div>
</div>
