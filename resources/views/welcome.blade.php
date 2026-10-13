<!DOCTYPE html>
<html lang="ka">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name') }}</title>
        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="slip-ui min-h-dvh bg-floor font-slip text-ink antialiased">
        <main class="aisle-float mx-auto flex min-h-dvh w-full max-w-[430px] flex-col overflow-hidden bg-phone md:my-6 md:min-h-[calc(100dvh-3rem)] md:rounded-[36px]">
            <header class="relative overflow-hidden rounded-b-[36px] bg-marker px-6 pb-8 pt-10 text-white">
                <span class="pointer-events-none absolute -right-8 -top-10 size-40 rounded-full bg-lime/90" aria-hidden="true"></span>
                <span class="pointer-events-none absolute -bottom-8 -left-6 size-28 rounded-full bg-coral" aria-hidden="true"></span>
                <h1 class="relative font-sign text-[2.4rem] font-bold leading-tight">საერთო სია</h1>
                <p class="relative mt-2 max-w-[24ch] text-sm leading-relaxed text-white/85">შექმენით სია, დაუმატეთ ხალხი და იყიდეთ ერთად.</p>
            </header>

            <div class="flex flex-1 flex-col px-5 py-5">
                <section class="rounded-[28px] bg-white px-4 py-2">
                    <div class="flex items-center gap-3 py-3">
                        <span class="size-6 rounded-full border-2 border-[#d9d4e6]"></span>
                        <span>
                            <span class="block font-medium">რძე</span>
                            <span class="text-sm text-bought">1 ლ</span>
                        </span>
                    </div>
                    <div class="flex items-center gap-3 border-t border-[#f1eef6] py-3">
                        <span class="size-6 rounded-full border-2 border-[#d9d4e6]"></span>
                        <span>
                            <span class="block font-medium">პური</span>
                            <span class="text-sm text-bought">2 ცალი</span>
                        </span>
                    </div>
                    <div class="flex items-center gap-3 border-t border-[#f1eef6] py-3 text-bought">
                        <span class="flex size-6 items-center justify-center rounded-full border-2 border-marker bg-marker text-white">
                            <svg viewBox="0 0 16 16" class="size-3.5" fill="none" stroke="currentColor" stroke-width="2.4" aria-hidden="true"><path d="M3.5 8.2 6.4 11 12.5 4.8"></path></svg>
                        </span>
                        <span>
                            <span class="block font-medium line-through">კვერცხი</span>
                            <span class="text-sm">10 ცალი</span>
                        </span>
                    </div>
                </section>

                <div class="mt-auto flex flex-col gap-3 pt-8">
                    @auth
                        <a href="{{ route('lists.index') }}" class="slip-press flex h-12 items-center justify-center rounded-full bg-ink font-medium text-white">ჩემი სიები</a>
                    @else
                        <a href="{{ route('login') }}" class="slip-press flex h-12 items-center justify-center rounded-full bg-ink font-medium text-white">შესვლა</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="slip-press flex h-12 items-center justify-center rounded-full bg-white font-medium">რეგისტრაცია</a>
                        @endif
                    @endauth
                </div>
            </div>
        </main>
    </body>
</html>
