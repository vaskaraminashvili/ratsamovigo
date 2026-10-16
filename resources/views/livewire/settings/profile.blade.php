@php
    $field = 'mt-2 h-12 w-full rounded-2xl bg-phone px-4 text-base text-ink outline-none ring-1 ring-transparent focus:ring-2 focus:ring-marker';
    $tint = \App\Support\ListAppearance::tint((string) auth()->id());
@endphp

<x-settings.shell title="პროფილი" subtitle="სახელი და ელფოსტა">
    <div class="flex items-center gap-3 rounded-[28px] bg-white px-4 py-4">
        <span class="flex size-12 shrink-0 items-center justify-center rounded-full text-sm font-medium" style="background: {{ $tint['bg'] }}; color: {{ $tint['ink'] }}">{{ auth()->user()->initials() }}</span>
        <span class="min-w-0">
            <span class="block truncate font-medium">{{ auth()->user()->name }}</span>
            <span class="block truncate text-sm text-bought">{{ auth()->user()->email }}</span>
        </span>
    </div>

    <form wire:submit="updateProfileInformation" class="mt-4 space-y-4 rounded-[28px] bg-white p-4">
        <div>
            <label for="name" class="text-sm font-medium text-marker">სახელი</label>
            <input id="name" wire:model="name" type="text" required autofocus autocomplete="name" class="{{ $field }}">
            @error('name')
                <p class="mt-2 text-sm text-wine">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="email" class="text-sm font-medium text-marker">ელფოსტა</label>
            <input id="email" wire:model="email" type="email" required autocomplete="email" class="{{ $field }}">
            @error('email')
                <p class="mt-2 text-sm text-wine">{{ $message }}</p>
            @enderror

            @if ($this->hasUnverifiedEmail)
                <p class="mt-3 text-sm leading-relaxed text-bought">
                    ელფოსტა ჯერ დაუდასტურებელია.
                    <button type="button" wire:click="resendVerificationNotification" class="font-medium text-marker">ბმულის ხელახლა გაგზავნა</button>
                </p>
            @endif
        </div>

        <button type="submit" class="slip-press h-12 w-full rounded-full bg-ink text-sm font-medium text-white">
            <span wire:loading.remove wire:target="updateProfileInformation">შენახვა</span>
            <span wire:loading wire:target="updateProfileInformation">ინახება…</span>
        </button>
    </form>

    @if ($this->showDeleteUser)
        <livewire:settings.delete-user-form />
    @endif
</x-settings.shell>
