<!DOCTYPE html>
<html lang="ka" x-data="tallstackui_darkTheme()">
    <head>
        @include('partials.head')
    </head>
    <body class="slip-ui min-h-dvh bg-floor font-slip text-ink antialiased" x-bind:class="{ 'dark': darkTheme }">
        {{ $slot }}

        @persist('toast')
            <x-toast />
        @endpersist
    </body>
</html>
