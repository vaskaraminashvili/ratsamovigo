@props([
    'status',
])

@if ($status)
    <div {{ $attributes->merge(['class' => 'text-sm font-medium text-marker']) }}>
        {{ $status }}
    </div>
@endif
