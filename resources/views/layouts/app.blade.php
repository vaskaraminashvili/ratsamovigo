<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="tallstackui_darkTheme()">
    <head>
        @include('partials.head')
    </head>
    <body class="slip-ui min-h-dvh bg-floor font-slip text-ink antialiased" x-bind:class="{ 'dark': darkTheme }">
        {{ $slot }}

        <x-toast />
    </body>
</html>
