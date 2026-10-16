@props([
    'title',
    'description',
])

<div class="flex w-full flex-col text-center">
    <h1 class="text-xl font-medium text-ink">{{ $title }}</h1>
    <p class="mt-1 text-sm text-bought">{{ $description }}</p>
</div>
