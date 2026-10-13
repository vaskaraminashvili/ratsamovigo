<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="slip-ui min-h-dvh bg-floor font-slip text-ink antialiased">
        <div class="aisle-float mx-auto flex h-dvh w-full max-w-[430px] flex-col overflow-hidden bg-phone md:my-6 md:h-[calc(100dvh-3rem)] md:rounded-[36px]">
            {{ $slot }}
        </div>

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
