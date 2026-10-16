<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="tallstackui_darkTheme()">
    <head>
        @include('partials.head')
    </head>
    <body class="slip-ui min-h-dvh bg-floor font-slip text-ink antialiased" x-bind:class="{ 'dark': darkTheme }">
        <div class="aisle-float mx-auto flex h-dvh w-full max-w-[430px] flex-col overflow-hidden bg-phone md:my-6 md:h-[calc(100dvh-3rem)] md:rounded-[36px]">
            {{ $slot }}
        </div>

        @persist('toast')
            <x-toast />
        @endpersist
    </body>
</html>
