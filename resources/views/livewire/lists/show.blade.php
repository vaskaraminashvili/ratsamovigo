@php
    $photo = $list->backgroundUrl();
    $field = 'h-12 w-full rounded-2xl bg-phone px-3 text-base text-ink outline-none ring-1 ring-transparent focus:ring-2 focus:ring-marker';
    $members = $list->members;
    $extraMembers = max(0, $members->count() - 4);
@endphp

<div @if ($panel === '') wire:poll.2s @endif class="aisle-float relative mx-auto flex h-dvh w-full max-w-[430px] flex-col overflow-hidden bg-phone md:my-6 md:h-[calc(100dvh-3rem)] md:rounded-[36px]">
    <div class="min-h-0 flex-1 overflow-y-auto pb-28">
        <header class="relative overflow-hidden rounded-b-[36px] px-5 pb-7 pt-4 text-white" @if ($photo) style="background-image: linear-gradient(rgba(36, 24, 72, .45), rgba(36, 24, 72, .62)), url('{{ $photo }}'); background-size: cover; background-position: center" @else style="background: {{ $list->accent_color }}" @endif>
            @unless ($photo)
                <span class="pointer-events-none absolute -right-10 -top-12 size-44 rounded-full bg-lime/90" aria-hidden="true"></span>
                <span class="pointer-events-none absolute -bottom-10 -left-8 size-32 rounded-full bg-coral" aria-hidden="true"></span>
                <span class="pointer-events-none absolute right-24 top-16 size-16 rounded-full bg-white/15" aria-hidden="true"></span>
            @endunless

            <div class="relative flex items-center justify-between">
                <a href="{{ route('lists.index') }}" wire:navigate class="flex size-10 items-center justify-center rounded-full bg-white/20" aria-label="სიები">
                    <svg viewBox="0 0 24 24" class="size-5" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M15 5 8 12l7 7"></path></svg>
                </a>
                <div class="flex -space-x-2">
                    @foreach ($members->take(4) as $member)
                        @php $tint = \App\Support\ListAppearance::tint((string) $member->user_id); @endphp
                        <span class="flex size-8 items-center justify-center rounded-full border-2 border-white/50 text-[11px] font-medium" style="background: {{ $tint['bg'] }}; color: {{ $tint['ink'] }}">{{ $member->user->initials() }}</span>
                    @endforeach
                    @if ($extraMembers > 0)
                        <span class="flex size-8 items-center justify-center rounded-full border-2 border-white/40 bg-white/25 text-[11px]">+{{ $extraMembers }}</span>
                    @endif
                </div>
                <button type="button" wire:click="$set('panel', 'menu')" class="flex size-10 items-center justify-center rounded-full bg-white/20" aria-label="მეტი">
                    <svg viewBox="0 0 24 24" class="size-5" fill="currentColor" aria-hidden="true"><circle cx="6" cy="12" r="1.4"></circle><circle cx="12" cy="12" r="1.4"></circle><circle cx="18" cy="12" r="1.4"></circle></svg>
                </button>
            </div>

            <p class="relative mt-6 text-sm text-white/85">
                @if ($members->count() < 2)
                    მხოლოდ თქვენ
                @else
                    გაზიარებულია {{ $members->count() }} ადამიანთან
                @endif
            </p>
            <h1 class="relative mt-1 font-sign text-[2rem] font-bold leading-tight">{{ $list->name }}</h1>
            <p class="relative mt-4 flex items-center gap-3 text-sm">
                <span class="rounded-full bg-coral px-3 py-1 font-medium text-white">დარჩა {{ $openCount }}</span>
                <span class="text-white/90">აღებულია {{ $pickedCount }}</span>
            </p>
        </header>

        <div class="mx-5 mt-4 grid grid-cols-2 rounded-2xl bg-[#e6e1f2] p-1 text-sm font-medium">
            <button type="button" wire:click="$set('screen', 'list')" @class(['h-10 rounded-xl', 'bg-white text-ink shadow-sm' => $screen === 'list', 'text-bought' => $screen !== 'list'])>სია</button>
            <button type="button" wire:click="$set('screen', 'activity')" @class(['h-10 rounded-xl', 'bg-white text-ink shadow-sm' => $screen === 'activity', 'text-bought' => $screen !== 'activity'])>აქტივობა</button>
        </div>

        @if ($screen === 'list')
            <div class="space-y-3 px-5 pt-4">
                @foreach ($polls as $poll)
                    @php $myVote = $poll->votes->firstWhere('user_id', auth()->id()); @endphp
                    <section class="rounded-[28px] bg-cream p-4">
                        <div class="mb-3 flex items-start gap-3">
                            <span class="flex size-10 shrink-0 items-center justify-center rounded-2xl bg-[#ffd8c4] text-coral" aria-hidden="true">
                                <svg viewBox="0 0 24 24" class="size-5" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M6 10c0-3.2 2.4-6 6-6s6 2.8 6 6v1H6v-1Z"></path><path d="M5 11h14v2.2A6 6 0 0 1 7.2 18H7a2 2 0 0 1-2-2V11Z"></path></svg>
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="text-xs font-medium text-coral">სწრაფი გამოკითხვა</p>
                                <h2 class="mt-0.5 font-medium leading-snug">{{ $poll->question }}</h2>
                            </div>
                        </div>
                        <div class="space-y-2">
                            @foreach ($poll->options as $option)
                                @php $selected = $myVote?->poll_option_id === $option->id; @endphp
                                <button type="button" wire:click="voteOption({{ $poll->id }}, {{ $option->id }})" class="flex w-full items-center gap-3 rounded-2xl bg-white px-3 py-3 text-left">
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
                                </button>
                            @endforeach
                        </div>
                        @if ($canEdit)
                            <div class="mt-3 flex flex-wrap gap-x-4 gap-y-1 text-sm">
                                <button type="button" wire:click="togglePollPin({{ $poll->id }})" class="text-marker">{{ $poll->is_pinned ? 'ჩამაგრებულია' : 'ზევით' }}</button>
                                <button type="button" wire:click="promoteWinner({{ $poll->id }})" class="text-marker">გამარჯვებული ნივთად</button>
                                <button type="button" wire:click="deletePoll({{ $poll->id }})" class="text-wine">წაშლა</button>
                            </div>
                        @endif
                        @error('selectedOptions') <p class="mt-2 text-sm text-wine">{{ $message }}</p> @enderror
                    </section>
                @endforeach

                @foreach ($dishes as $dishName => $dishItems)
                    @php
                        $done = $dishItems->where('is_checked', true)->count();
                        $dishTotal = $dishItems->count();
                        $dishDone = $dishTotal > 0 && $done === $dishTotal;
                    @endphp
                    <section class="rounded-[28px] bg-white p-4">
                        <div class="mb-1 flex items-center gap-3">
                            <span class="flex size-10 shrink-0 items-center justify-center rounded-2xl bg-[#ffd8c4] text-coral" aria-hidden="true">
                                <svg viewBox="0 0 24 24" class="size-5" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M5 10h14v1.5a7 7 0 0 1-14 0V10Z"></path><path d="M8 10V7.5M12 10V6M16 10V7.5"></path></svg>
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="text-xs text-bought">კერძი · {{ $done }}/{{ $dishTotal }}</p>
                                <h2 @class(['truncate font-medium', 'text-bought line-through' => $dishDone])>{{ $dishName }}</h2>
                            </div>
                            @if ($canEdit)
                                <button type="button" wire:click="toggleGroup({{ \Illuminate\Support\Js::from($dishName) }})" aria-label="{{ $dishDone ? 'კერძის მოხსნა' : 'კერძის მონიშვნა' }}" @class(['flex size-6 shrink-0 items-center justify-center rounded-full border-2 text-white', 'border-[#d9d4e6]' => ! $dishDone]) @if ($dishDone) style="background: {{ $list->accent_color }}; border-color: {{ $list->accent_color }}" @endif>
                                    @if ($dishDone)
                                        <svg viewBox="0 0 16 16" class="size-3.5" fill="none" stroke="currentColor" stroke-width="2.4" aria-hidden="true"><path d="M3.5 8.2 6.4 11 12.5 4.8"></path></svg>
                                    @endif
                                </button>
                            @endif
                        </div>
                        <div class="mt-1 divide-y divide-[#f1eef6]">
                            @foreach ($dishItems as $item)
                                @include('livewire.lists.partials.item', ['item' => $item])
                            @endforeach
                        </div>
                        @if ($canEdit)
                            <button type="button" wire:click="startIngredient({{ \Illuminate\Support\Js::from($dishName) }})" class="mt-2 text-sm font-medium text-marker">+ ინგრედიენტის დამატება</button>
                        @endif
                    </section>
                @endforeach

                @if ($looseItems->isNotEmpty())
                    <section class="rounded-[28px] bg-white px-4 py-2">
                        @if ($dishes->isNotEmpty())
                            <p class="pb-1 pt-3 text-xs text-bought">საყიდლები</p>
                        @endif
                        <div class="divide-y divide-[#f1eef6]">
                            @foreach ($looseItems as $item)
                                @include('livewire.lists.partials.item', ['item' => $item])
                            @endforeach
                        </div>
                    </section>
                @endif

                @if ($offers->isNotEmpty())
                    <section class="rounded-[28px] bg-white px-4 py-2">
                        @foreach ($offers as $offer)
                            <a href="{{ $offer->url }}" class="block border-b border-[#f1eef6] py-3 last:border-b-0">
                                <p class="text-sm text-bought">{{ $offer->storeName }}</p>
                                <p>{{ $offer->itemName }} — {{ $offer->discount }}</p>
                            </a>
                        @endforeach
                    </section>
                @endif

                @if ($polls->isEmpty() && $dishes->isEmpty() && $looseItems->isEmpty())
                    <p class="px-1 py-10 text-sm text-bought">{{ $canEdit ? 'პირველი ნივთი ქვემოთ ემატება.' : 'სია ცარიელია.' }}</p>
                @endif
            </div>
        @else
            <div class="px-5 pt-4">
                @if ($activity->isEmpty())
                    <p class="py-10 text-sm text-bought">ჯერ არაფერი მომხდარა.</p>
                @else
                    <section class="rounded-[28px] bg-white px-4">
                        @foreach ($activity as $event)
                            @php $eventTint = \App\Support\ListAppearance::tint($event['seed']); @endphp
                            <div class="flex items-start gap-3 border-b border-[#f1eef6] py-3 last:border-b-0">
                                <span class="mt-0.5 flex size-9 shrink-0 items-center justify-center rounded-full text-xs font-medium" style="background: {{ $eventTint['bg'] }}; color: {{ $eventTint['ink'] }}">{{ $event['initials'] }}</span>
                                <div class="min-w-0">
                                    <p class="text-[15px] leading-snug"><span class="font-medium">{{ $event['name'] }}</span> {{ $event['text'] }}</p>
                                    <p class="mt-0.5 text-sm text-bought">{{ $event['at']?->locale('ka')->diffForHumans() }}</p>
                                </div>
                            </div>
                        @endforeach
                    </section>
                @endif
            </div>
        @endif
    </div>

    @if ($canEdit && $panel === '')
        <div class="pointer-events-none absolute inset-x-0 bottom-6 z-20 flex justify-center">
            <button type="button" wire:click="startCreate" class="slip-press pointer-events-auto flex h-12 items-center gap-2 rounded-full bg-ink px-5 text-sm font-medium text-white shadow-lg">
                <svg viewBox="0 0 24 24" class="size-5" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M12 5v14M5 12h14"></path></svg>
                დამატება
            </button>
        </div>
    @endif

    @if ($panel === 'menu')
        <div class="slip-scrim fixed inset-0 z-30 bg-ink/45" wire:click="$set('panel', '')"></div>
        <div class="slip-up fixed inset-x-0 bottom-0 z-40 mx-auto w-full max-w-[430px] space-y-2 rounded-t-[28px] bg-white px-5 py-5 text-ink">
            <div class="mb-2 flex items-center justify-between">
                <h2 class="font-sign text-xl font-bold">სია</h2>
                <button type="button" wire:click="$set('panel', '')" class="font-medium text-marker">დახურვა</button>
            </div>
            @if ($canManage)
                <button type="button" wire:click="$set('panel', 'settings')" class="flex h-12 w-full items-center rounded-2xl bg-phone px-4 text-left">სიის სახე</button>
                <button type="button" wire:click="$set('panel', 'share')" class="flex h-12 w-full items-center rounded-2xl bg-phone px-4 text-left">ხალხი</button>
            @endif
            <label class="flex h-12 items-center justify-between rounded-2xl bg-phone px-4">
                <span>მხოლოდ ჩემი</span>
                <input type="checkbox" wire:model.live="onlyMine" class="aisle-switch">
            </label>
            @if ($canEdit && $pickedCount > 0)
                <button type="button" wire:click="clearChecked" class="h-12 w-full text-sm text-wine">მონიშნულის გასუფთავება</button>
            @endif
        </div>
    @endif

    @if (in_array($panel, ['create', 'item'], true) && $canEdit)
        <div class="slip-scrim fixed inset-0 z-30 bg-ink/45" wire:click="$set('panel', '')"></div>
        <div class="slip-up aisle-float fixed inset-x-4 bottom-4 z-40 mx-auto max-h-[88dvh] w-auto max-w-[398px] overflow-y-auto rounded-[28px] bg-white p-5 text-ink">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="font-sign text-xl font-bold">
                    @if ($panel === 'item')
                        დეტალები
                    @elseif ($compose === 'dish')
                        კერძი
                    @elseif ($compose === 'poll')
                        გამოკითხვა
                    @else
                        ნივთი
                    @endif
                </h2>
                <button type="button" wire:click="$set('panel', '')" class="font-medium text-marker">დახურვა</button>
            </div>

            @if ($panel === 'create')
                <div class="mb-4 grid grid-cols-3 rounded-2xl bg-phone p-1 text-sm">
                    <button type="button" wire:click="$set('compose', 'item')" @class(['h-9 rounded-xl', 'bg-white font-medium shadow-sm' => $compose === 'item', 'text-bought' => $compose !== 'item'])>ნივთი</button>
                    <button type="button" wire:click="$set('compose', 'dish')" @class(['h-9 rounded-xl', 'bg-white font-medium shadow-sm' => $compose === 'dish', 'text-bought' => $compose !== 'dish'])>კერძი</button>
                    <button type="button" wire:click="$set('compose', 'poll')" @class(['h-9 rounded-xl', 'bg-white font-medium shadow-sm' => $compose === 'poll', 'text-bought' => $compose !== 'poll'])>გამოკითხვა</button>
                </div>
            @endif

            @if ($panel === 'create' && $compose === 'poll')
                <form wire:submit="addPoll" class="space-y-3">
                    <input wire:model="pollQuestion" class="{{ $field }}" placeholder="რა ვიყიდოთ?">
                    <textarea wire:model="pollOptions" rows="4" class="w-full rounded-2xl bg-phone px-3 py-3 text-ink outline-none focus:ring-2 focus:ring-marker" placeholder="კოლა&#10;ფანტა&#10;პეპსი"></textarea>
                    @error('question') <p class="text-sm text-wine">{{ $message }}</p> @enderror
                    @error('options') <p class="text-sm text-wine">{{ $message }}</p> @enderror
                    <button type="submit" class="slip-press h-12 w-full rounded-full bg-ink font-medium text-white">გამოკითხვა</button>
                </form>
            @else
                <form wire:submit="{{ $panel === 'create' ? 'addItem' : 'saveItem' }}" class="space-y-4">
                    @if ($compose === 'dish' && $panel === 'create')
                        @if ($itemGroup === '')
                            <label class="block text-sm text-bought">
                                კერძი
                                <input wire:model="dishName" class="{{ $field }} mt-1" placeholder="პასტის საღამო">
                            </label>
                            @error('dishName') <p class="text-sm text-wine">{{ $message }}</p> @enderror
                        @else
                            <p class="text-sm text-bought">კერძი <span class="font-medium text-ink">{{ $dishName }}</span></p>
                        @endif
                    @endif

                    <label class="block text-sm text-bought">
                        {{ $compose === 'dish' && $panel === 'create' ? 'ინგრედიენტი' : 'ნივთი' }}
                        <input wire:model="itemName" class="{{ $field }} mt-1" placeholder="პამიდორი">
                    </label>
                    @error('name') <p class="text-sm text-wine">{{ $message }}</p> @enderror

                    <div>
                        <p class="text-sm text-bought">რაოდენობა</p>
                        <div class="mt-1 flex items-center gap-2">
                            <button type="button" wire:click="bumpQuantity(-1)" class="flex size-12 items-center justify-center rounded-2xl bg-phone text-lg" aria-label="ნაკლები">−</button>
                            <input wire:model="itemQuantity" class="{{ $field }} text-center" placeholder="2">
                            <button type="button" wire:click="bumpQuantity(1)" class="flex size-12 items-center justify-center rounded-2xl bg-phone text-lg" aria-label="მეტი">+</button>
                        </div>
                        <div class="mt-2 flex flex-wrap gap-2">
                            @foreach ($units as $unit => $label)
                                <button type="button" wire:click="$set('itemUnit', '{{ $unit }}')" @class(['h-9 rounded-full px-3 text-sm', 'bg-ink text-white' => $itemUnit === $unit, 'bg-phone text-ink' => $itemUnit !== $unit])>{{ $label }}</button>
                            @endforeach
                        </div>
                    </div>

                    @if ($panel === 'item')
                        <label class="block text-sm text-bought">
                            კერძი
                            <input wire:model="itemGroup" class="{{ $field }} mt-1" placeholder="პასტის საღამო">
                        </label>
                        @error('itemGroup') <p class="text-sm text-wine">{{ $message }}</p> @enderror
                    @endif

                    @if ($compose !== 'dish' || $panel === 'item')
                        <label class="block text-sm text-bought">
                            ემოჯი
                            <input wire:model="itemEmoji" class="{{ $field }} mt-1" placeholder="🍅">
                        </label>
                        <label class="block text-sm text-bought">
                            შენიშვნა
                            <input wire:model="itemNote" class="{{ $field }} mt-1" placeholder="შენიშვნა">
                        </label>
                        <label class="block text-sm text-bought">
                            ფოტო
                            <input type="file" wire:model="itemPhoto" accept="image/*" class="mt-1 block w-full text-sm">
                        </label>
                        <p wire:loading wire:target="itemPhoto" class="text-sm text-bought">იტვირთება…</p>
                        @error('photo') <p class="text-sm text-wine">{{ $message }}</p> @enderror
                    @endif

                    @if ($panel === 'item')
                        <button type="button" wire:click="toggleClaim({{ $editingItemId }})" class="flex h-12 w-full items-center justify-between rounded-2xl bg-phone px-3 text-left">
                            <span>მე ვიყიდი</span>
                            <span class="text-sm text-bought">{{ $list->items->firstWhere('id', $editingItemId)?->claimed_by === auth()->id() ? 'ვიყიდი მე' : 'არჩევა' }}</span>
                        </button>
                        @if ($list->show_likes)
                            <button type="button" wire:click="toggleLike({{ $editingItemId }})" class="h-12 w-full rounded-2xl bg-phone px-3 text-left">მოწონება</button>
                        @endif
                        <button type="button" wire:click="togglePin({{ $editingItemId }})" class="h-12 w-full rounded-2xl bg-phone">{{ $list->items->firstWhere('id', $editingItemId)?->is_pinned ? 'ჩამაგრებულია' : 'ზევით' }}</button>
                        <button type="button" wire:click="deleteItem({{ $editingItemId }})" class="h-12 w-full text-wine">წაშლა</button>
                        @if ($list->show_author)
                            <p class="text-sm text-bought">დაამატა {{ $list->items->firstWhere('id', $editingItemId)?->author?->name }}.</p>
                        @endif
                    @endif

                    <button type="submit" class="slip-press h-12 w-full rounded-full bg-ink font-medium text-white">
                        {{ $panel === 'item' ? 'შენახვა' : ($compose === 'dish' ? 'ინგრედიენტის დამატება' : 'დამატება') }}
                    </button>
                </form>
            @endif
        </div>
    @endif

    @if ($panel === 'settings' && $canManage)
        <div class="slip-scrim fixed inset-0 z-30 bg-ink/45" wire:click="$set('panel', '')"></div>
        <form wire:submit="saveAppearance" class="slip-up fixed inset-x-0 bottom-0 z-40 mx-auto max-h-[92dvh] w-full max-w-[430px] space-y-4 overflow-y-auto rounded-t-[28px] bg-white px-5 py-5 text-ink">
            <div class="flex items-center justify-between">
                <h2 class="font-sign text-xl font-bold">სიის სახე</h2>
                <button type="button" wire:click="$set('panel', '')" class="font-medium text-marker">დახურვა</button>
            </div>
            <input id="settings-name" wire:model="listName" class="{{ $field }}" placeholder="სახელი" aria-label="სახელი">
            @error('listName') <p class="text-sm text-wine">{{ $message }}</p> @enderror
            <input id="settings-emoji" wire:model="listEmoji" class="{{ $field }}" placeholder="ემოჯი" aria-label="ემოჯი">
            <div class="flex flex-wrap gap-2">
                @foreach ($accents as $accent)
                    <button type="button" wire:click="$set('accentColor', '{{ $accent }}')" aria-label="ფერი" class="size-11 rounded-full border-2" style="background: {{ $accent }}; border-color: {{ $accentColor === $accent ? '#1c1730' : 'transparent' }}"></button>
                @endforeach
            </div>
            @error('accentColor') <p class="text-sm text-wine">{{ $message }}</p> @enderror
            <div class="grid grid-cols-2 gap-2">
                @foreach ($presets as $key => $preset)
                    <button type="button" wire:click="$set('backgroundPreset', '{{ $key }}')" @class([$preset['class'], 'h-12 rounded-2xl border border-[#ece6f4] text-sm', 'outline outline-2 outline-offset-2 outline-ink' => $backgroundPreset === $key])>{{ $preset['label'] }}</button>
                @endforeach
            </div>
            @error('backgroundPreset') <p class="text-sm text-wine">{{ $message }}</p> @enderror
            <label class="block text-sm text-bought">
                საკუთარი ფონი
                <input type="file" wire:model="backgroundUpload" accept="image/*" class="mt-1 block w-full text-sm">
            </label>
            <p wire:loading wire:target="backgroundUpload" class="text-sm text-bought">იტვირთება…</p>
            @if ($preview = $this->pendingBackgroundUrl())
                <img src="{{ $preview }}" alt="" class="h-28 w-full rounded-2xl object-cover">
            @elseif ($photo)
                <img src="{{ $photo }}" alt="" class="h-28 w-full rounded-2xl object-cover">
            @endif
            @error('backgroundUpload') <p class="text-sm text-wine">{{ $message }}</p> @enderror
            @if ($photo || $backgroundUpload)
                <button type="button" wire:click="removeBackground" class="text-sm text-wine">ფონის სურათის წაშლა</button>
            @endif
            <div class="grid grid-cols-3 gap-2">
                @foreach ($checkboxStyles as $style)
                    <button type="button" wire:click="$set('checkboxStyle', '{{ $style->value }}')" @class(['h-12 rounded-2xl text-sm', 'bg-ink text-white' => $checkboxStyle === $style->value, 'bg-phone' => $checkboxStyle !== $style->value])>{{ $style->label() }}</button>
                @endforeach
            </div>
            <label class="flex h-12 items-center justify-between"><span>ემოჯი</span><input type="checkbox" wire:model.live="showEmoji" class="aisle-switch"></label>
            <label class="flex h-12 items-center justify-between"><span>რაოდენობა</span><input type="checkbox" wire:model.live="showQuantity" class="aisle-switch"></label>
            <label class="flex h-12 items-center justify-between"><span>მოწონება</span><input type="checkbox" wire:model.live="showLikes" class="aisle-switch"></label>
            <label class="flex h-12 items-center justify-between"><span>ავტორი</span><input type="checkbox" wire:model.live="showAuthor" class="aisle-switch"></label>
            <button type="submit" wire:loading.attr="disabled" wire:target="backgroundUpload,saveAppearance" class="slip-press h-12 w-full rounded-full bg-ink font-medium text-white disabled:opacity-50">
                <span wire:loading.remove wire:target="saveAppearance">შენახვა</span>
                <span wire:loading wire:target="saveAppearance">ინახება…</span>
            </button>
            <button type="button" wire:click="deleteList" wire:confirm="სია წაიშალოს?" class="h-12 w-full text-wine">სიის წაშლა</button>
        </form>
    @endif

    @if ($panel === 'share' && $canManage)
        <div class="slip-scrim fixed inset-0 z-30 bg-ink/45" wire:click="$set('panel', '')"></div>
        <div class="slip-up fixed inset-x-0 bottom-0 z-40 mx-auto max-h-[92dvh] w-full max-w-[430px] space-y-4 overflow-y-auto rounded-t-[28px] bg-white px-5 py-5 text-ink">
            <div class="flex items-center justify-between">
                <h2 class="font-sign text-xl font-bold">ხალხი</h2>
                <button type="button" wire:click="$set('panel', '')" class="font-medium text-marker">დახურვა</button>
            </div>
            <button type="button" wire:click="share('viewer')" class="h-12 w-full rounded-full bg-phone">ნახვის ბმულის შეცვლა</button>
            @if ($viewLink)
                <input readonly value="{{ route('lists.join', $viewLink->token) }}" class="{{ $field }} text-sm">
            @endif
            <button type="button" wire:click="share('editor')" class="h-12 w-full rounded-full bg-phone">რედაქტირების ბმულის შეცვლა</button>
            @if ($editLink)
                <input readonly value="{{ route('lists.join', $editLink->token) }}" class="{{ $field }} text-sm">
            @endif
            <ul>
                @foreach ($list->members as $member)
                    @php $memberTint = \App\Support\ListAppearance::tint((string) $member->user_id); @endphp
                    <li class="flex min-h-14 items-center justify-between gap-3 border-b border-[#f1eef6] py-2">
                        <span class="flex items-center gap-3">
                            <span class="flex size-9 items-center justify-center rounded-full text-xs font-medium" style="background: {{ $memberTint['bg'] }}; color: {{ $memberTint['ink'] }}">{{ $member->user->initials() }}</span>
                            <span>
                                <span class="block font-medium">{{ $member->user->name }}</span>
                                <span class="text-sm text-bought">{{ $member->role->label() }}</span>
                            </span>
                        </span>
                        @if ($member->role !== \App\Enums\ListRole::Owner)
                            <button type="button" wire:click="removeMember({{ $member->id }})" class="text-sm text-wine">წაშლა</button>
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
</div>
