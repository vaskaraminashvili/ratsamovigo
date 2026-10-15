@if ($panel === 'favorites')
    <div class="slip-scrim fixed inset-0 z-30 bg-ink/45" wire:click="$set('panel', '')"></div>
    <div class="slip-up aisle-float fixed inset-x-4 bottom-4 z-40 mx-auto flex max-h-[88dvh] w-auto max-w-[398px] flex-col overflow-hidden rounded-[28px] bg-white text-ink">
        <div class="flex items-center justify-between px-5 pb-3 pt-5">
            <h2 class="font-sign text-xl font-bold">რჩეულებიდან</h2>
            <button type="button" wire:click="$set('panel', '')" class="font-medium text-marker">დახურვა</button>
        </div>
        <div class="mx-5 mb-3 grid grid-cols-2 rounded-2xl bg-phone p-1 text-sm">
            <button type="button" wire:click="$set('favoriteTab', 'items')" @class(['h-9 rounded-xl', 'bg-white font-medium shadow-sm' => $favoriteTab === 'items', 'text-bought' => $favoriteTab !== 'items'])>ნივთები</button>
            <button type="button" wire:click="$set('favoriteTab', 'dishes')" @class(['h-9 rounded-xl', 'bg-white font-medium shadow-sm' => $favoriteTab === 'dishes', 'text-bought' => $favoriteTab !== 'dishes'])>კერძები</button>
        </div>
        <div class="min-h-0 flex-1 overflow-y-auto px-5 pb-5">
            @if ($favoriteTab === 'dishes')
                @forelse ($favoriteDishes as $dish)
                    <button type="button" wire:key="pick-dish-{{ $dish->id }}" wire:click="openFavoriteDish({{ $dish->id }})" class="flex w-full items-start gap-3 border-b border-[#f1eef6] py-3 text-left last:border-b-0">
                        <span class="min-w-0 flex-1">
                            <span class="block truncate font-medium">
                                @if ($dish->emoji)
                                    <span class="mr-1">{{ $dish->emoji }}</span>
                                @endif
                                {{ $dish->name }}
                            </span>
                            @if ($dish->note)
                                <span class="mt-0.5 line-clamp-2 block text-sm text-bought">{{ $dish->note }}</span>
                            @endif
                            <span class="mt-0.5 block text-sm text-bought">
                                {{ $dish->items->isEmpty() ? 'ინგრედიენტი არ აქვს' : $dish->items->count().' ინგრედიენტი' }}
                            </span>
                        </span>
                    </button>
                @empty
                    <p class="py-6 text-sm leading-relaxed text-bought">შეინახე კერძი ინგრედიენტებით, რომ სიაში ერთი შეხებით ჩასვა.</p>
                @endforelse
            @else
                @forelse ($favoriteItems as $favorite)
                    <button type="button" wire:key="pick-item-{{ $favorite->id }}" wire:click="applyFavoriteItem({{ $favorite->id }})" class="flex w-full items-start gap-3 border-b border-[#f1eef6] py-3 text-left last:border-b-0">
                        <span class="min-w-0 flex-1">
                            <span class="block truncate font-medium">
                                @if ($favorite->emoji)
                                    <span class="mr-1">{{ $favorite->emoji }}</span>
                                @endif
                                {{ $favorite->name }}
                            </span>
                            @if ($favorite->quantity)
                                <span class="mt-0.5 block text-sm text-bought">{{ $favorite->quantity }}</span>
                            @endif
                            @if ($favorite->note)
                                <span class="mt-0.5 line-clamp-2 block text-sm leading-snug text-bought">{{ $favorite->note }}</span>
                            @endif
                        </span>
                    </button>
                @empty
                    <p class="py-6 text-sm leading-relaxed text-bought">ჯერ არაფერი გაქვს შენახული. დაამატე ღვინო, რძე, ან სხვა რაც ხშირად გჭირდება.</p>
                @endforelse
            @endif
        </div>
    </div>
@endif

@if ($panel === 'favorite-dish')
    @php
        $pickingDish = $favoriteDishes->firstWhere('id', $pickingDishId);
    @endphp
    <div class="slip-scrim fixed inset-0 z-30 bg-ink/45" wire:click="$set('panel', 'favorites')"></div>
    <div class="slip-up aisle-float fixed inset-x-4 bottom-4 z-40 mx-auto flex max-h-[88dvh] w-auto max-w-[398px] flex-col overflow-hidden rounded-[28px] bg-white text-ink">
        <div class="flex items-center justify-between px-5 pb-3 pt-5">
            <h2 class="font-sign text-xl font-bold">{{ $pickingDish->name ?? 'კერძი' }}</h2>
            <button type="button" wire:click="$set('panel', 'favorites')" class="font-medium text-marker">უკან</button>
        </div>
        <div class="min-h-0 flex-1 overflow-y-auto px-5 pb-5">
            @if (! $pickingDish || $pickingDish->items->isEmpty())
                <p class="py-6 text-sm leading-relaxed text-bought">ინგრედიენტი ჯერ არ აქვს. სიაში მაშინ ჩაჯდება, როცა ინგრედიენტს დაამატებ.</p>
            @else
                <div class="divide-y divide-[#f1eef6]">
                    @foreach ($pickingDish->items as $ingredient)
                        <label wire:key="pick-ingredient-{{ $ingredient->id }}" class="flex items-start gap-3 py-3">
                            <input type="checkbox" value="{{ $ingredient->id }}" wire:model="selectedIngredientIds" class="mt-1 size-5 accent-marker">
                            <span class="min-w-0">
                                <span class="block font-medium">
                                    @if ($ingredient->emoji)
                                        <span class="mr-1">{{ $ingredient->emoji }}</span>
                                    @endif
                                    {{ $ingredient->name }}
                                </span>
                                @if ($ingredient->quantity)
                                    <span class="mt-0.5 block text-sm text-bought">{{ $ingredient->quantity }}</span>
                                @endif
                                @if ($ingredient->note)
                                    <span class="mt-0.5 line-clamp-2 block text-sm text-bought">{{ $ingredient->note }}</span>
                                @endif
                            </span>
                        </label>
                    @endforeach
                </div>
                @error('ingredient_ids') <p class="mt-2 text-sm text-wine">{{ $message }}</p> @enderror
                @error('ingredient_ids.*') <p class="mt-2 text-sm text-wine">{{ $message }}</p> @enderror
                <button type="button" wire:click="applyFavoriteDish" class="slip-press mt-4 h-12 w-full rounded-full bg-ink font-medium text-white">დაამატე არჩეული ინგრედიენტები</button>
            @endif
        </div>
    </div>
@endif

@if ($panel === 'favorite-replace')
    <div class="slip-scrim fixed inset-0 z-30 bg-ink/45" wire:click="dismissReplace"></div>
    <div class="slip-up fixed inset-x-4 bottom-4 z-40 mx-auto w-auto max-w-[398px] rounded-[28px] bg-white p-5 text-ink">
        <h2 class="font-sign text-xl font-bold">რჩეულებში შენახვა</h2>
        <p class="mt-3 text-sm leading-relaxed text-bought">ასეთი სახელი უკვე გაქვს რჩეულებში. ჩანაცვლდეს?</p>
        <div class="mt-5 grid grid-cols-2 gap-2">
            <button type="button" wire:click="dismissReplace" class="h-12 rounded-full bg-phone text-sm">დატოვება</button>
            <button type="button" wire:click="confirmReplaceFavorite" class="h-12 rounded-full bg-ink text-sm font-medium text-white">ჩანაცვლება</button>
        </div>
    </div>
@endif
