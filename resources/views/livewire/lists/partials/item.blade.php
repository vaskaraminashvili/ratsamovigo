<article class="border-b border-rule px-4 py-3 text-ink last:border-b-0">
    <div class="flex items-start gap-3">
        @if ($canEdit)
            <button
                type="button"
                wire:click="toggleCheck({{ $item->id }})"
                aria-label="{{ $item->is_checked ? 'მონიშვნის მოხსნა' : 'მონიშვნა' }}"
                class="mt-0.5 flex size-11 shrink-0 items-center justify-center border-ink text-lg text-ink {{ $list->checkbox_style->value === 'circle' ? 'rounded-full border-2' : ($list->checkbox_style->value === 'plain' ? 'border-0' : 'border-2') }}"
            >
                <span class="slip-mark {{ $item->is_checked ? 'opacity-100' : 'opacity-0' }}" aria-hidden="true">✓</span>
            </button>
        @endif
        <div class="min-w-0 flex-1">
            <div class="flex items-baseline gap-3">
                <p @class(['min-w-0 flex-1 text-base font-medium', 'text-bought line-through' => $checked])>
                    @if ($list->show_emoji && $item->emoji)
                        <span>{{ $item->emoji }}</span>
                    @endif
                    {{ $item->name }}
                </p>
                @if ($list->show_quantity && $item->quantity)
                    <p class="shrink-0 text-sm text-bought">{{ $item->quantity }}</p>
                @endif
            </div>
            @if ($item->note)
                <p class="mt-1 text-sm text-bought">{{ $item->note }}</p>
            @endif
            @if ($item->photo_path)
                <img src="{{ \Illuminate\Support\Facades\Storage::url($item->photo_path) }}" alt="" class="mt-2 h-24 w-full object-cover">
            @endif
            @if ($list->show_author || ($item->is_checked && $item->checker) || $item->claimer)
                <p class="mt-1 text-sm leading-relaxed text-bought">
                    @if ($list->show_author)
                        დაამატა {{ $item->author->name }}.
                    @endif
                    @if ($item->is_checked && $item->checker)
                        მონიშნა {{ $item->checker->name }}.
                    @endif
                    @if ($item->claimer)
                        ყიდულობს {{ $item->claimer->name }}.
                    @endif
                </p>
            @endif
        </div>
    </div>
    <div class="mt-2 flex flex-wrap gap-x-4 gap-y-1 text-sm">
        @if ($list->show_likes)
            <button type="button" wire:click="toggleLike({{ $item->id }})" class="h-10">
                {{ $item->likes->contains('user_id', auth()->id()) ? '♥' : '♡' }} {{ $item->likes->count() }}
            </button>
        @endif
        @if ($canEdit)
            <button type="button" wire:click="toggleClaim({{ $item->id }})" class="h-10">
                {{ $item->claimed_by === auth()->id() ? 'ვიყიდი მე' : 'მე ვიყიდი' }}
            </button>
            <button type="button" wire:click="togglePin({{ $item->id }})" class="h-10">{{ $item->is_pinned ? 'ჩამაგრებულია' : 'ზევით' }}</button>
            <button type="button" wire:click="editItem({{ $item->id }})" class="h-10">შესწორება</button>
            <button type="button" wire:click="deleteItem({{ $item->id }})" class="h-10 text-wine">წაშლა</button>
        @endif
    </div>
</article>
