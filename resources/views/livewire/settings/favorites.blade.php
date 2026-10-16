<x-settings.shell title="რჩეულები" subtitle="სიაში ერთი შეხებით">
    <div class="space-y-10">
            <section class="space-y-4">
                <h2 class="font-sign text-xl font-bold">ნივთები</h2>

                <form wire:submit="saveItem" class="space-y-3 rounded-[28px] bg-white p-4 text-ink">
                    <x-input wire:model="itemName" label="სახელი" maxlength="120" placeholder="ღვინო" />
                    <x-input wire:model="itemQuantity" label="რაოდენობა" maxlength="40" placeholder="1 ბოთლი" />
                    <x-input wire:model="itemEmoji" label="ემოჯი" maxlength="8" placeholder="🍷" />
                    <x-textarea wire:model="itemNote" label="შენიშვნა" rows="3" maxlength="200" placeholder="საუკეთესო ბოთლი ამ მისამართზეა" />

                    <div class="flex items-center gap-3">
                        <x-button submit lg round>
                            {{ $editingItemId ? 'შენახვა' : 'დამატება' }}
                        </x-button>
                        @if ($editingItemId)
                            <button type="button" wire:click="cancelItem" class="h-12 text-sm text-bought">გაუქმება</button>
                        @endif
                    </div>
                </form>

                @forelse ($items as $item)
                    <article wire:key="favorite-item-{{ $item->id }}" class="rounded-[28px] bg-white p-4 text-ink">
                        <p class="font-medium">
                            @if ($item->emoji)
                                <span class="mr-1">{{ $item->emoji }}</span>
                            @endif
                            {{ $item->name }}
                        </p>
                        @if ($item->quantity)
                            <p class="mt-1 text-sm text-bought">{{ $item->quantity }}</p>
                        @endif
                        @if ($item->note)
                            <p class="mt-2 whitespace-pre-wrap break-words text-sm leading-relaxed">{{ $item->note }}</p>
                        @endif
                        <div class="mt-3 flex gap-4 text-sm">
                            <button type="button" wire:click="editItem({{ $item->id }})" class="font-medium text-marker">რედაქტირება</button>
                            <button type="button" wire:click="deleteItem({{ $item->id }})" class="text-wine">წაშლა</button>
                        </div>
                    </article>
                @empty
                    <p class="text-sm leading-relaxed text-bought">ჯერ არაფერი გაქვს შენახული. დაამატე ღვინო, რძე, ან სხვა რაც ხშირად გჭირდება.</p>
                @endforelse
            </section>

            <section class="space-y-4">
                <h2 class="font-sign text-xl font-bold">კერძები</h2>

                <form wire:submit="saveDish" class="space-y-3 rounded-[28px] bg-white p-4 text-ink">
                    <x-input wire:model="dishName" label="კერძი" maxlength="120" placeholder="პასტა" />
                    <x-input wire:model="dishEmoji" label="ემოჯი" maxlength="8" placeholder="🍝" />
                    <x-textarea wire:model="dishNote" label="შენიშვნა" rows="2" maxlength="200" placeholder="შენიშვნა" />

                    <div class="flex items-center gap-3">
                        <x-button submit lg round>
                            {{ $editingDishId ? 'შენახვა' : 'კერძის დამატება' }}
                        </x-button>
                        @if ($editingDishId)
                            <button type="button" wire:click="cancelDish" class="h-12 text-sm text-bought">გაუქმება</button>
                        @endif
                    </div>
                </form>

                @forelse ($dishes as $dish)
                    <article wire:key="favorite-dish-{{ $dish->id }}" class="rounded-[28px] bg-white p-4 text-ink">
                        <p class="text-xs text-bought">კერძი</p>
                        <h4 class="font-medium">
                            @if ($dish->emoji)
                                <span class="mr-1">{{ $dish->emoji }}</span>
                            @endif
                            {{ $dish->name }}
                        </h4>
                        @if ($dish->note)
                            <p class="mt-2 whitespace-pre-wrap break-words text-sm leading-relaxed">{{ $dish->note }}</p>
                        @endif
                        <div class="mt-3 flex flex-wrap gap-x-4 gap-y-1 text-sm">
                            <button type="button" wire:click="editDish({{ $dish->id }})" class="font-medium text-marker">რედაქტირება</button>
                            <button type="button" wire:click="startIngredient({{ $dish->id }})" class="font-medium text-marker">ინგრედიენტის დამატება</button>
                            <button type="button" wire:click="deleteDish({{ $dish->id }})" class="text-wine">წაშლა</button>
                        </div>

                        @if ($dish->items->isNotEmpty())
                            <ul class="mt-3 divide-y divide-[#f1eef6]">
                                @foreach ($dish->items as $ingredient)
                                    <li wire:key="favorite-ingredient-{{ $ingredient->id }}" class="py-3">
                                        <p class="font-medium">
                                            @if ($ingredient->emoji)
                                                <span class="mr-1">{{ $ingredient->emoji }}</span>
                                            @endif
                                            {{ $ingredient->name }}
                                        </p>
                                        @if ($ingredient->quantity)
                                            <p class="mt-0.5 text-sm text-bought">{{ $ingredient->quantity }}</p>
                                        @endif
                                        @if ($ingredient->note)
                                            <p class="mt-1 whitespace-pre-wrap break-words text-sm leading-relaxed">{{ $ingredient->note }}</p>
                                        @endif
                                        <div class="mt-2 flex flex-wrap gap-x-4 gap-y-1 text-sm">
                                            @if (! $loop->first)
                                                <button type="button" wire:click="moveIngredient({{ $dish->id }}, {{ $ingredient->id }}, 'up')" class="text-marker" aria-label="ზემოთ">ზემოთ</button>
                                            @endif
                                            @if (! $loop->last)
                                                <button type="button" wire:click="moveIngredient({{ $dish->id }}, {{ $ingredient->id }}, 'down')" class="text-marker" aria-label="ქვემოთ">ქვემოთ</button>
                                            @endif
                                            <button type="button" wire:click="editIngredient({{ $dish->id }}, {{ $ingredient->id }})" class="text-marker">რედაქტირება</button>
                                            <button type="button" wire:click="deleteIngredient({{ $dish->id }}, {{ $ingredient->id }})" class="text-wine">წაშლა</button>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @endif

                        @if ($ingredientDishId === $dish->id)
                            <form wire:submit="saveIngredient({{ $dish->id }})" class="mt-3 space-y-3 border-t border-[#f1eef6] pt-3">
                                <x-input wire:model="ingredientName" label="ინგრედიენტი" maxlength="120" placeholder="სოუსი" />
                                <x-input wire:model="ingredientQuantity" label="რაოდენობა" maxlength="40" placeholder="1" />
                                <x-input wire:model="ingredientEmoji" label="ემოჯი" maxlength="8" placeholder="🍅" />
                                <x-input wire:model="ingredientNote" label="შენიშვნა" maxlength="200" placeholder="შენიშვნა" />

                                <div class="flex items-center gap-3">
                                    <x-button submit lg round>
                                        {{ $editingIngredientId ? 'შენახვა' : 'ინგრედიენტის დამატება' }}
                                    </x-button>
                                    <button type="button" wire:click="cancelIngredient" class="h-12 text-sm text-bought">გაუქმება</button>
                                </div>
                            </form>
                        @endif
                    </article>
                @empty
                    <p class="text-sm leading-relaxed text-bought">შეინახე კერძი ინგრედიენტებით, რომ სიაში ერთი შეხებით ჩასვა.</p>
                @endforelse
            </section>
    </div>
</x-settings.shell>
