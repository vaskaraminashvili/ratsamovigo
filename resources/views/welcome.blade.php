<!DOCTYPE html>
<html lang="ka">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name') }}</title>
        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="slip-ui min-h-dvh bg-crate font-slip text-ink antialiased">
        <main class="mx-auto flex min-h-dvh w-full max-w-lg flex-col px-5 py-8">
            <h1 class="font-sign text-4xl leading-tight">საერთო სია</h1>
            <p class="mt-3 max-w-[28ch] text-base leading-relaxed text-bought">შექმენით სია, დაუმატეთ ხალხი და იყიდეთ ერთად.</p>

            <div class="mt-8 bg-slip px-5 py-2">
                <ul>
                    <li class="flex items-baseline justify-between gap-4 border-b border-rule py-3">
                        <span>რძე</span>
                        <span class="text-sm text-bought">1 ლ</span>
                    </li>
                    <li class="flex items-baseline justify-between gap-4 border-b border-rule py-3">
                        <span>პური</span>
                        <span class="text-sm text-bought">2</span>
                    </li>
                    <li class="flex items-baseline justify-between gap-4 py-3 text-bought line-through">
                        <span>კვერცხი</span>
                        <span class="text-sm">10</span>
                    </li>
                </ul>
            </div>

            <div class="flex flex-col gap-3">
                @auth
                    <a href="{{ route('lists.index') }}" class="flex h-12 items-center justify-center bg-tomato font-medium text-white">ჩემი სიები</a>
                @else
                    <a href="{{ route('login') }}" class="flex h-12 items-center justify-center bg-tomato font-medium text-white">შესვლა</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="flex h-12 items-center justify-center border border-ink/20 bg-slip font-medium">რეგისტრაცია</a>
                    @endif
                @endauth
            </div>
        </main>
    </body>
</html>
