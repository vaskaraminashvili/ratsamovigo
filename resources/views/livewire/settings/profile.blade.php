@php
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
        <x-input id="name" wire:model="name" label="სახელი" type="text" required autofocus autocomplete="name" />

        <div>
            <x-input id="email" wire:model="email" label="ელფოსტა" type="email" required autocomplete="email" />

            @if ($this->hasUnverifiedEmail)
                <p class="mt-3 text-sm leading-relaxed text-bought">
                    ელფოსტა ჯერ დაუდასტურებელია.
                    <button type="button" wire:click="resendVerificationNotification" class="font-medium text-marker">ბმულის ხელახლა გაგზავნა</button>
                </p>
            @endif
        </div>

        <x-button submit lg round class="w-full">
            <span wire:loading.remove wire:target="updateProfileInformation">შენახვა</span>
            <span wire:loading wire:target="updateProfileInformation">ინახება…</span>
        </x-button>
    </form>

    @if ($this->showDeleteUser)
        <livewire:settings.delete-user-form />
    @endif
</x-settings.shell>
