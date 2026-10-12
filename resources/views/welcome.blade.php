<!DOCTYPE html>
<html lang="ka">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name') }}</title>
        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="flex min-h-dvh items-center justify-center bg-emerald-50 px-6 text-zinc-900">
        <main class="w-full max-w-sm text-center">
            <p class="text-5xl">🛒</p>
            <h1 class="mt-4 text-3xl font-semibold">საერთო სია</h1>
            <p class="mt-2 text-zinc-600">შექმენით სია, დაუმატეთ ხალხი და იყიდეთ ერთად.</p>
            <div class="mt-8 flex flex-col gap-3">
                @auth
                    <a href="{{ route('lists.index') }}" class="flex h-12 items-center justify-center rounded-2xl bg-emerald-600 font-semibold text-white">ჩემი სიები</a>
                @else
                    <a href="{{ route('login') }}" class="flex h-12 items-center justify-center rounded-2xl bg-emerald-600 font-semibold text-white">შესვლა</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="flex h-12 items-center justify-center rounded-2xl border border-emerald-700 font-semibold">რეგისტრაცია</a>
                    @endif
                @endauth
            </div>
        </main>
    </body>
</html>
