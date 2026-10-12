<article class="rounded-3xl bg-white/95 p-3 text-zinc-900 shadow-sm" style="border-left: 6px solid {{ $list->accent_color }}">
    <div class="flex items-start gap-3">
        @if ($canEdit)
            <button type="button" wire:click="toggleCheck({{ $item->id }})" class="mt-1 flex size-11 shrink-0 items-center justify-center border-zinc-400 text-lg {{ $list->checkbox_style->value === 'circle' ? 'rounded-full border-2' : ($list->checkbox_style->value === 'plain' ? '' : 'rounded-lg border-2') }}">
                @if ($item->is_checked)
                    ✓
                @endif
            </button>
        @endif
        <div class="min-w-0 flex-1">
            <p @class(['text-base font-medium', 'line-through opacity-60' => $checked])>
                @if ($list->show_emoji && $item->emoji)
                    <span>{{ $item->emoji }}</span>
                @endif
                {{ $item->name }}
                @if ($list->show_quantity && $item->quantity)
                    <span class="text-zinc-500">· {{ $item->quantity }}</span>
                @endif
            </p>
            @if ($item->note)
                <p class="text-sm text-zinc-500">{{ $item->note }}</p>
            @endif
            @if ($item->photo_path)
                <img src="{{ \Illuminate\Support\Facades\Storage::url($item->photo_path) }}" alt="" class="mt-2 h-24 w-full rounded-2xl object-cover">
            @endif
            <p class="mt-1 text-xs text-zinc-500">
                @if ($list->show_author)
                    დაამატა {{ $item->author->name }}
                @endif
                @if ($item->is_checked && $item->checker)
                    · მონიშნა {{ $item->checker->name }}
                @endif
                @if ($item->claimer)
                    · ყიდულობს {{ $item->claimer->name }}
                @endif
            </p>
        </div>
    </div>
    <div class="mt-2 flex flex-wrap gap-2">
        @if ($list->show_likes)
            <button type="button" wire:click="toggleLike({{ $item->id }})" class="h-10 rounded-full bg-zinc-100 px-3 text-sm">
                {{ $item->likes->contains('user_id', auth()->id()) ? '♥' : '♡' }} {{ $item->likes->count() }}
            </button>
        @endif
        @if ($canEdit)
            <button type="button" wire:click="toggleClaim({{ $item->id }})" class="h-10 rounded-full bg-zinc-100 px-3 text-sm">
                {{ $item->claimed_by === auth()->id() ? 'ვიყიდი მე' : 'მე ვიყიდი' }}
            </button>
            <button type="button" wire:click="togglePin({{ $item->id }})" class="h-10 rounded-full bg-zinc-100 px-3 text-sm">{{ $item->is_pinned ? 'ჩამაგრებულია' : 'ზევით' }}</button>
            <button type="button" wire:click="editItem({{ $item->id }})" class="h-10 rounded-full bg-zinc-100 px-3 text-sm">შესწორება</button>
            <button type="button" wire:click="deleteItem({{ $item->id }})" class="h-10 rounded-full bg-zinc-100 px-3 text-sm text-rose-600">წაშლა</button>
        @endif
    </div>
</article>
