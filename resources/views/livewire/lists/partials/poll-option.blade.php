<span @class(['flex size-5 shrink-0 items-center justify-center rounded-full border-2', 'border-marker' => $selected, 'border-[#d5d0e2]' => ! $selected])>
    @if ($selected)
        <span class="size-2.5 rounded-full bg-marker"></span>
    @endif
</span>
<span class="min-w-0 flex-1 truncate font-medium">{{ $option->label }}</span>
<span class="text-sm text-bought">{{ $option->votes->count() }} ხმა</span>
<span class="flex -space-x-1.5">
    @foreach ($option->votes->take(3) as $vote)
        @php $voteTint = \App\Support\ListAppearance::tint((string) $vote->user_id); @endphp
        <span class="flex size-6 items-center justify-center rounded-full text-[10px] font-medium" style="background: {{ $voteTint['bg'] }}; color: {{ $voteTint['ink'] }}">{{ $vote->user?->initials() }}</span>
    @endforeach
</span>
