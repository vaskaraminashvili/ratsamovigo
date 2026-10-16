<x-layouts::auth title="პაროლის დადასტურება">
    <header class="relative shrink-0 overflow-hidden rounded-b-[36px] bg-marker px-5 pb-8 pt-8 text-white">
        <span class="pointer-events-none absolute -right-10 -top-12 size-44 rounded-full bg-lime/90" aria-hidden="true"></span>
        <span class="pointer-events-none absolute -bottom-10 -left-8 size-32 rounded-full bg-coral" aria-hidden="true"></span>
        <span class="pointer-events-none absolute right-24 top-16 size-16 rounded-full bg-white/15" aria-hidden="true"></span>

        <p class="relative text-sm text-white/85">უსაფრთხოება</p>
        <h1 class="relative mt-1 font-sign text-[2rem] font-bold leading-tight">პაროლის დადასტურება</h1>
        <p class="relative mt-2 text-sm text-white/90">გააგრძელე პაროლით ან პასკით</p>
    </header>

    <div class="min-h-0 flex-1 overflow-y-auto px-5 py-5">
        <x-auth-session-status class="mb-4 text-center" :status="session('status')" />

        <x-passkey-verify
            options-route="passkey.confirm-options"
            submit-route="passkey.confirm"
            label="დადასტურება პასკით"
            loading-label="მოწმდება..."
            separator="ან პაროლით"
        />

        <form method="POST" action="{{ route('password.confirm.store') }}" class="space-y-4">
            @csrf

            <x-password
                id="password"
                name="password"
                label="პაროლი"
                :rules="[]"
                required
                autocomplete="current-password"
                placeholder="პაროლი"
            />

            <x-button submit lg round class="w-full" data-test="confirm-password-button">
                დადასტურება
            </x-button>
        </form>
    </div>
</x-layouts::auth>
