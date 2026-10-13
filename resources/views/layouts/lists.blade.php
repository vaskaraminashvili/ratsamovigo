<!DOCTYPE html>
<html lang="ka">
    <head>
        @include('partials.head')
    </head>
    <body class="slip-ui min-h-dvh bg-floor font-slip text-ink antialiased">
        {{ $slot }}

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
