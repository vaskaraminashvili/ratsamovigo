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

            <div>
                <label for="name" class="text-sm font-medium text-marker">სახელი</label>
                <input
                    id="name"
                    name="name"
                    type="text"
                    value="{{ old('name') }}"
                    required
                    autofocus
                    autocomplete="name"
                    placeholder="სახელი"
                    class="mt-2 h-12 w-full rounded-2xl bg-white px-4 text-base text-ink outline-none ring-1 ring-transparent focus:ring-2 focus:ring-marker"
                >
                @error('name')
                    <p class="mt-2 text-sm text-wine">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="text-sm font-medium text-marker">ელფოსტა</label>
                <input
                    id="email"
                    name="email"
                    type="email"
                    value="{{ old('email') }}"
                    required
                    autocomplete="email"
                    placeholder="you@email.com"
                    class="mt-2 h-12 w-full rounded-2xl bg-white px-4 text-base text-ink outline-none ring-1 ring-transparent focus:ring-2 focus:ring-marker"
                >
                @error('email')
                    <p class="mt-2 text-sm text-wine">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="text-sm font-medium text-marker">პაროლი</label>
                <div class="mt-2 flex items-center gap-2" x-data="{ show: false }">
                    <input
                        id="password"
                        name="password"
                        x-bind:type="show ? 'text' : 'password'"
                        required
                        autocomplete="new-password"
                        placeholder="პაროლი"
                        class="h-12 min-w-0 flex-1 rounded-2xl bg-white px-4 text-base text-ink outline-none ring-1 ring-transparent focus:ring-2 focus:ring-marker"
                    >
                    <button type="button" class="shrink-0 text-sm text-bought" x-on:click="show = !show" x-text="show ? 'დამალვა' : 'ჩვენება'"></button>
                </div>
                @error('password')
                    <p class="mt-2 text-sm text-wine">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="text-sm font-medium text-marker">გაიმეორე პაროლი</label>
                <div class="mt-2 flex items-center gap-2" x-data="{ show: false }">
                    <input
                        id="password_confirmation"
                        name="password_confirmation"
                        x-bind:type="show ? 'text' : 'password'"
                        required
                        autocomplete="new-password"
                        placeholder="გაიმეორე პაროლი"
                        class="h-12 min-w-0 flex-1 rounded-2xl bg-white px-4 text-base text-ink outline-none ring-1 ring-transparent focus:ring-2 focus:ring-marker"
                    >
                    <button type="button" class="shrink-0 text-sm text-bought" x-on:click="show = !show" x-text="show ? 'დამალვა' : 'ჩვენება'"></button>
                </div>
            </div>

            <button type="submit" class="slip-press h-12 w-full rounded-full bg-ink text-sm font-medium text-white" data-test="register-user-button">
                შექმნა
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-bought">
            უკვე გაქვს ანგარიში?
            <a href="{{ route('login') }}" class="font-medium text-marker" wire:navigate>შესვლა</a>
        </p>
    </div>
</x-layouts::auth>
