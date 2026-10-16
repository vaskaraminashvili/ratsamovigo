<x-layouts::auth title="რეგისტრაცია">
    <header class="relative shrink-0 overflow-hidden rounded-b-[36px] bg-marker px-5 pb-8 pt-8 text-white">
        <span class="pointer-events-none absolute -right-10 -top-12 size-44 rounded-full bg-lime/90" aria-hidden="true"></span>
        <span class="pointer-events-none absolute -bottom-10 -left-8 size-32 rounded-full bg-coral" aria-hidden="true"></span>
        <span class="pointer-events-none absolute right-24 top-16 size-16 rounded-full bg-white/15" aria-hidden="true"></span>

        <p class="relative text-sm text-white/85">სია</p>
        <h1 class="relative mt-1 font-sign text-[2rem] font-bold leading-tight">რეგისტრაცია</h1>
        <p class="relative mt-2 text-sm text-white/90">ახალი ანგარიში შენი სიებისთვის</p>
    </header>

    <div class="min-h-0 flex-1 overflow-y-auto px-5 py-5">
        <x-auth-session-status class="mb-4 text-center" :status="session('status')" />

        <form method="POST" action="{{ route('register.store') }}" class="space-y-4">
            @csrf

            <x-input
                id="name"
                name="name"
                label="სახელი"
                type="text"
                :value="old('name')"
                required
                autofocus
                autocomplete="name"
                placeholder="სახელი"
            />

            <x-input
                id="email"
                name="email"
                label="ელფოსტა"
                type="email"
                :value="old('email')"
                required
                autocomplete="email"
                placeholder="you@email.com"
            />

            <x-password
                id="password"
                name="password"
                label="პაროლი"
                :rules="[]"
                required
                autocomplete="new-password"
                placeholder="პაროლი"
            />

            <x-password
                id="password_confirmation"
                name="password_confirmation"
                label="გაიმეორე პაროლი"
                :rules="[]"
                required
                autocomplete="new-password"
                placeholder="გაიმეორე პაროლი"
            />

            <x-button submit lg round class="w-full" data-test="register-user-button">
                შექმნა
            </x-button>
        </form>

        <p class="mt-6 text-center text-sm text-bought">
            უკვე გაქვს ანგარიში?
            <a href="{{ route('login') }}" class="font-medium text-marker" wire:navigate>შესვლა</a>
        </p>
    </div>
</x-layouts::auth>
