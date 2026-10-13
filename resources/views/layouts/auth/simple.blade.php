<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="slip-ui min-h-dvh bg-floor font-slip text-ink antialiased">
        <div class="flex min-h-dvh flex-col items-center justify-center p-3">
            <div class="slip-rise flex w-full max-w-sm flex-col gap-2 rounded-[28px] bg-slip px-6 py-8">
                <a href="{{ route('home') }}" class="mb-2 font-sign text-2xl font-bold leading-tight text-ink" wire:navigate>
                    სია
                    <span class="sr-only">{{ config('app.name', 'Laravel') }}</span>
                </a>
                <div class="flex flex-col gap-6">
                    {{ $slot }}
                </div>
            </div>
        </div>

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
