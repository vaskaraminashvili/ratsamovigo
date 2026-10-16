<x-layouts::auth title="შესვლა">
    <header class="relative shrink-0 overflow-hidden rounded-b-[36px] bg-marker px-5 pb-8 pt-8 text-white">
        <span class="pointer-events-none absolute -right-10 -top-12 size-44 rounded-full bg-lime/90" aria-hidden="true"></span>
        <span class="pointer-events-none absolute -bottom-10 -left-8 size-32 rounded-full bg-coral" aria-hidden="true"></span>
        <span class="pointer-events-none absolute right-24 top-16 size-16 rounded-full bg-white/15" aria-hidden="true"></span>

        <p class="relative text-sm text-white/85">სია</p>
        <h1 class="relative mt-1 font-sign text-[2rem] font-bold leading-tight">შესვლა</h1>
        <p class="relative mt-2 text-sm text-white/90">შედი და განაგრძე შენი სიები</p>
    </header>

    <div class="min-h-0 flex-1 overflow-y-auto px-5 py-5">
        <x-auth-session-status class="mb-4 text-center" :status="session('status')" />

        <x-passkey-verify
            label="შესვლა პასკით"
            loading-label="შემოწმება..."
            separator="ან ელფოსტით"
        />

        <form method="POST" action="{{ route('login.store') }}" class="space-y-4">
            @csrf

            <x-input
                id="email"
                name="email"
                label="ელფოსტა"
                type="email"
                :value="old('email')"
                required
                autofocus
                autocomplete="email"
                placeholder="you@email.com"
            />

            <div>
                @if (Route::has('password.request'))
                    <div class="mb-1 flex justify-end">
                        <a href="{{ route('password.request') }}" class="text-sm text-bought" wire:navigate>დაგავიწყდა?</a>
                    </div>
                @endif
                <x-password
                    id="password"
                    name="password"
                    label="პაროლი"
                    :rules="[]"
                    required
                    autocomplete="current-password"
                    placeholder="პაროლი"
                />
            </div>

            <x-checkbox name="remember" value="1" label="დამიმახსოვრე" :checked="(bool) old('remember')" />

            <x-button submit lg round class="w-full" data-test="login-button">
                შესვლა
            </x-button>
        </form>

        <p class="mt-6 text-center text-sm text-bought">
            არ გაქვს ანგარიში?
            <a href="{{ route('register') }}" class="font-medium text-marker" wire:navigate>რეგისტრაცია</a>
        </p>
    </div>
</x-layouts::auth>
