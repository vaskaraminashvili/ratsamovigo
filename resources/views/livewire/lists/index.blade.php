<div class="aisle-float mx-auto flex h-dvh w-full max-w-[430px] flex-col overflow-hidden bg-phone md:my-6 md:h-[calc(100dvh-3rem)] md:rounded-[36px]">
    <header class="px-5 pb-2 pt-7">
        <div class="flex items-end justify-between gap-4">
            <h1 class="font-sign text-[2rem] font-bold leading-tight">სიები</h1>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="pb-1 text-sm text-bought">გასვლა</button>
            </form>
        </div>
    </header>

    <form wire:submit="createList" class="mx-5 mt-3 rounded-[28px] bg-white p-4">
        <p class="text-sm font-medium text-marker">ახალი სია</p>
        <div class="mt-3 flex items-center gap-2">
            <label class="sr-only" for="list-emoji">ემოჯი</label>
            <input id="list-emoji" wire:model="emoji" type="text" maxlength="8" placeholder="🛒" class="h-12 w-14 rounded-2xl bg-phone text-center text-base">
            <label class="sr-only" for="list-name">სიის სახელი</label>
            <input id="list-name" wire:model="name" type="text" placeholder="სახლის სია" class="h-12 min-w-0 flex-1 rounded-2xl bg-phone px-4 text-base">
            <button type="submit" class="slip-press h-12 shrink-0 rounded-full bg-ink px-4 text-sm font-medium text-white">შექმნა</button>
        </div>
        @error('name') <p class="mt-2 text-sm text-wine">{{ $message }}</p> @enderror
    </form>

    <div class="slip-rise min-h-0 flex-1 space-y-3 overflow-y-auto px-5 py-4">
        @forelse ($lists as $list)
            @php $tint = \App\Support\ListAppearance::tint($list->name); @endphp
            <a href="{{ route('lists.show', $list) }}" wire:navigate class="flex items-center gap-3 rounded-[28px] bg-white px-4 py-3">
                <span class="flex size-12 shrink-0 items-center justify-center rounded-full text-lg" style="background: {{ $tint['bg'] }}; color: {{ $tint['ink'] }}">{{ $list->emoji ?: '•' }}</span>
                <span class="min-w-0 flex-1">
                    <span class="block truncate font-medium">{{ $list->name }}</span>
                    <span class="text-sm text-bought">დარჩა {{ $list->open_items_count }}</span>
                </span>
                <span class="flex -space-x-2">
                    @foreach ($list->members->take(3) as $member)
                        @php $memberTint = \App\Support\ListAppearance::tint((string) $member->user_id); @endphp
                        <span class="flex size-7 items-center justify-center rounded-full border-2 border-white text-[10px] font-medium" style="background: {{ $memberTint['bg'] }}; color: {{ $memberTint['ink'] }}">{{ $member->user->initials() }}</span>
                    @endforeach
                </span>
            </a>
        @empty
            <p class="px-1 py-10 text-sm text-bought">პირველი სია აქ დაიწყება.</p>
        @endforelse
    </div>

    @include('livewire.lists.partials.nav')
</div>
