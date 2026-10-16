<div class="mt-4 rounded-[28px] bg-phone p-4" wire:cloak x-data="{ showRecoveryCodes: false }">
    <h3 class="font-medium">სარეზერვო კოდები</h3>
    <p class="mt-1 text-sm leading-relaxed text-bought">თუ ტელეფონი დაგეკარგა, ამ კოდებით შეხვალ. შეინახე ისინი საიმედო ადგილას.</p>

    <div class="mt-4 flex flex-wrap gap-2">
        <button type="button" x-show="!showRecoveryCodes" x-on:click="showRecoveryCodes = true" class="h-12 rounded-full bg-ink px-5 text-sm font-medium text-white" aria-expanded="false" aria-controls="recovery-codes-section">ნახვა</button>
        <button type="button" x-show="showRecoveryCodes" x-cloak x-on:click="showRecoveryCodes = false" class="h-12 rounded-full bg-white px-5 text-sm" aria-expanded="true" aria-controls="recovery-codes-section">დამალვა</button>
        @if (filled($recoveryCodes))
            <button type="button" x-show="showRecoveryCodes" x-cloak wire:click="regenerateRecoveryCodes" class="h-12 rounded-full bg-white px-5 text-sm">ახალი კოდები</button>
        @endif
    </div>

    <div x-show="showRecoveryCodes" x-cloak id="recovery-codes-section" class="mt-4">
        @error('recoveryCodes')
            <p class="text-sm text-wine">{{ $message }}</p>
        @enderror

        @if (filled($recoveryCodes))
            <ul class="space-y-1 rounded-2xl bg-white p-4 text-sm" aria-label="სარეზერვო კოდები">
                @foreach ($recoveryCodes as $code)
                    <li class="select-text" wire:loading.class="opacity-50">{{ $code }}</li>
                @endforeach
            </ul>
            <p class="mt-3 text-sm leading-relaxed text-bought">თითო კოდი ერთხელ მუშაობს. თუ ამოიწურა, დააჭირე „ახალი კოდები“.</p>
        @endif
    </div>
</div>
