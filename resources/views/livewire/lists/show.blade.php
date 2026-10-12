@php
    $surface = \App\Support\ListAppearance::preset($list->background_preset);
    $photo = $list->background_path ? \Illuminate\Support\Facades\Storage::url($list->background_path) : null;
    $field = 'h-12 w-full border border-ink/15 bg-slip px-3 text-base text-ink';
@endphp

<div @if ($panel === '') wire:poll.2s @endif class="min-h-dvh {{ $photo ? 'bg-ink bg-cover bg-center text-white' : $surface['class'] }}" @if($photo) style="background-image: linear-gradient(rgba(26,36,32,.45), rgba(26,36,32,.55)), url('{{ $photo }}')" @endif>
    <div class="mx-auto min-h-dvh w-full max-w-lg pb-40">
        <header class="sticky top-0 z-20 bg-slip/95 px-4 py-4 text-ink backdrop-blur">
            <a href="{{ route('lists.index') }}" class="text-sm text-bought" wire:navigate>სიები</a>
            <div class="mt-2 flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <h1 class="truncate font-sign text-3xl leading-tight">{{ $list->emoji }} {{ $list->name }}</h1>
                    <p class="mt-1">
                        <span class="font-sign text-xl leading-none" style="color: {{ $list->accent_color }}">{{ $openItems->count() }}</span>
                        <span class="text-bought">დარჩა</span>
                    </p>
                </div>
                @if ($canManage)
                    <div class="flex shrink-0 gap-4 pt-1 text-sm">
                        <button type="button" wire:click="$set('panel', 'share')">გაზიარება</button>
                        <button type="button" wire:click="$set('panel', 'settings')">სახე</button>
                    </div>
                @endif
            </div>
        </header>

        <div class="space-y-3 px-4 pt-4">
            @foreach ($polls as $poll)
                @php $myVote = $poll->votes->firstWhere('user_id', auth()->id()); @endphp
                <section class="bg-slip p-4 text-ink" style="border-top: 2px solid {{ $list->accent_color }}">
                    <div class="mb-3 flex items-start justify-between gap-3">
                        <h2 class="text-base font-medium">{{ $poll->question }}</h2>
                        @if ($canEdit)
                            <button type="button" wire:click="togglePollPin({{ $poll->id }})" class="shrink-0 text-sm text-bought">{{ $poll->is_pinned ? 'ჩამაგრებულია' : 'ზევით' }}</button>
                        @endif
                    </div>
                    <div>
                        @foreach ($poll->options as $option)
                            <label class="flex min-h-12 items-center gap-3 border-b border-rule py-2">
                                <input type="radio" class="size-5 accent-ink" wire:model="selectedOptions.{{ $poll->id }}" value="{{ $option->id }}" @checked($myVote?->poll_option_id === $option->id)>
                                <span class="flex-1">{{ $option->label }}</span>
                                <span class="font-sign text-lg" style="color: {{ $list->accent_color }}">{{ $option->votes->count() }}</span>
                            </label>
                        @endforeach
                    </div>
                    <div class="mt-3 flex flex-wrap gap-x-4 gap-y-2 text-sm">
                        <button type="button" wire:click="vote({{ $poll->id }})" class="h-11 bg-ink px-4 font-medium text-slip">ხმა</button>
                        @if ($canEdit)
                            <button type="button" wire:click="promoteWinner({{ $poll->id }})" class="h-11">გამარჯვებული ნივთად</button>
                            <button type="button" wire:click="deletePoll({{ $poll->id }})" class="h-11 text-wine">წაშლა</button>
                        @endif
                    </div>
                </section>
            @endforeach

            @if ($openItems->isNotEmpty())
                <div class="bg-slip">
                    @foreach ($openItems as $item)
                        @include('livewire.lists.partials.item', ['item' => $item, 'checked' => false])
                    @endforeach
                </div>
            @endif

            @if ($checkedItems->isNotEmpty())
                <section class="bg-slip text-ink">
                    <button type="button" wire:click="$toggle('showChecked')" class="flex h-12 w-full items-center justify-between px-4 text-sm">
                        <span>მონიშნული ({{ $checkedItems->count() }})</span>
                        <span>{{ $showChecked ? 'დამალვა' : 'ჩვენება' }}</span>
                    </button>
                    @if ($showChecked)
                        <div class="border-t border-rule">
                            @foreach ($checkedItems as $item)
                                @include('livewire.lists.partials.item', ['item' => $item, 'checked' => true])
                            @endforeach
                            @if ($canEdit)
                                <button type="button" wire:click="clearChecked" class="h-11 w-full text-sm text-wine">მონიშნულის გასუფთავება</button>
                            @endif
                        </div>
                    @endif
                </section>
            @endif

            @if ($offers->isNotEmpty())
                <section class="bg-slip">
                    @foreach ($offers as $offer)
                        <a href="{{ $offer->url }}" class="block border-b border-rule px-4 py-3 text-ink last:border-b-0">
                            <p class="text-sm text-bought">{{ $offer->storeName }}</p>
                            <p>{{ $offer->itemName }} — {{ $offer->discount }}</p>
                        </a>
                    @endforeach
                </section>
            @endif

            @if ($openItems->isEmpty() && $checkedItems->isEmpty() && $polls->isEmpty())
                <p class="bg-slip px-4 py-10 text-sm text-bought">{{ $canEdit ? 'დაამატეთ პირველი ნივთი ქვემოთ.' : 'სია ცარიელია.' }}</p>
            @endif
        </div>
    </div>

    @if ($panel === 'settings' && $canManage)
        <div class="fixed inset-0 z-30 overflow-y-auto bg-slip text-ink">
            <form wire:submit="saveAppearance" class="mx-auto max-w-lg space-y-4 px-4 py-4 pb-10">
                <div class="flex items-center justify-between">
                    <h2 class="font-sign text-3xl">სიის სახე</h2>
                    <button type="button" wire:click="$set('panel', '')" class="h-11 text-sm">დახურვა</button>
                </div>
                <label class="sr-only" for="settings-name">სახელი</label>
                <input id="settings-name" wire:model="listName" class="{{ $field }}" placeholder="სახელი">
                @error('listName') <p class="text-sm text-wine">{{ $message }}</p> @enderror
                <label class="sr-only" for="settings-emoji">ემოჯი</label>
                <input id="settings-emoji" wire:model="listEmoji" class="{{ $field }}" placeholder="ემოჯი">
                <div class="flex flex-wrap gap-2">
                    @foreach ($accents as $accent)
                        <button type="button" wire:click="$set('accentColor', '{{ $accent }}')" aria-label="ფერი" class="size-11 border-2" style="background: {{ $accent }}; border-color: {{ $accentColor === $accent ? '#1a2420' : 'transparent' }}"></button>
                    @endforeach
                </div>
                <div class="grid grid-cols-2 gap-2">
                    @foreach ($presets as $key => $preset)
                        <button type="button" wire:click="$set('backgroundPreset', '{{ $key }}')" @class([$preset['class'], 'h-12 border text-sm', 'outline outline-2 outline-offset-2 outline-ink' => $backgroundPreset === $key])>{{ $preset['label'] }}</button>
                    @endforeach
                </div>
                <label class="block text-sm">
                    საკუთარი ფონი
                    <input type="file" wire:model="backgroundUpload" accept="image/*" class="mt-1 block w-full text-sm">
                </label>
                @error('backgroundUpload') <p class="text-sm text-wine">{{ $message }}</p> @enderror
                @if ($list->background_path)
                    <button type="button" wire:click="removeBackground" class="text-sm text-wine">ფონის სურათის წაშლა</button>
                @endif
                <div class="grid grid-cols-3 gap-2">
                    @foreach ($checkboxStyles as $style)
                        <button type="button" wire:click="$set('checkboxStyle', '{{ $style->value }}')" @class(['h-12 border text-sm', 'bg-ink text-slip' => $checkboxStyle === $style->value])>{{ $style->label() }}</button>
                    @endforeach
                </div>
                <label class="flex h-12 items-center justify-between border-b border-rule"><span>ემოჯი</span><input type="checkbox" wire:model="showEmoji" class="size-5 accent-ink"></label>
                <label class="flex h-12 items-center justify-between border-b border-rule"><span>რაოდენობა</span><input type="checkbox" wire:model="showQuantity" class="size-5 accent-ink"></label>
                <label class="flex h-12 items-center justify-between border-b border-rule"><span>მოწონება</span><input type="checkbox" wire:model="showLikes" class="size-5 accent-ink"></label>
                <label class="flex h-12 items-center justify-between border-b border-rule"><span>ავტორი</span><input type="checkbox" wire:model="showAuthor" class="size-5 accent-ink"></label>
                <button type="submit" class="h-12 w-full bg-ink font-medium text-slip">შენახვა</button>
                <button type="button" wire:click="deleteList" wire:confirm="სია წაიშალოს?" class="h-12 w-full text-wine">სიის წაშლა</button>
            </form>
        </div>
    @endif

    @if ($panel === 'share' && $canManage)
        <div class="fixed inset-0 z-30 overflow-y-auto bg-slip px-4 py-4 text-ink">
            <div class="mx-auto max-w-lg space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="font-sign text-3xl">ხალხი</h2>
                    <button type="button" wire:click="$set('panel', '')" class="h-11 text-sm">დახურვა</button>
                </div>
                <button type="button" wire:click="share('viewer')" class="h-12 w-full border border-ink/20">ნახვის ბმულის შეცვლა</button>
                @if ($viewLink)
                    <input readonly value="{{ route('lists.join', $viewLink->token) }}" class="{{ $field }} text-sm">
                @endif
                <button type="button" wire:click="share('editor')" class="h-12 w-full border border-ink/20">რედაქტირების ბმულის შეცვლა</button>
                @if ($editLink)
                    <input readonly value="{{ route('lists.join', $editLink->token) }}" class="{{ $field }} text-sm">
                @endif
                <ul>
                    @foreach ($list->members as $member)
                        <li class="flex min-h-14 items-center justify-between gap-3 border-b border-rule py-2">
                            <span>
                                <span class="block font-medium">{{ $member->user->name }}</span>
                                <span class="text-sm text-bought">{{ $member->role->label() }}</span>
                            </span>
                            @if ($member->role !== \App\Enums\ListRole::Owner)
                                <button type="button" wire:click="removeMember({{ $member->id }})" class="text-sm text-wine">წაშლა</button>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    @if ($panel === 'item' && $canEdit)
        <form wire:submit="saveItem" class="fixed inset-x-0 bottom-0 z-30 mx-auto max-w-lg space-y-2 border-t-2 border-ink bg-slip p-4 text-ink">
            <input wire:model="itemName" class="{{ $field }}" placeholder="სახელი">
            <input wire:model="itemQuantity" class="{{ $field }}" placeholder="რაოდენობა">
            <input wire:model="itemEmoji" class="{{ $field }}" placeholder="ემოჯი">
            <input wire:model="itemNote" class="{{ $field }}" placeholder="შენიშვნა">
            <input type="file" wire:model="itemPhoto" accept="image/*" class="block w-full text-sm">
            @error('name') <p class="text-sm text-wine">{{ $message }}</p> @enderror
            <div class="flex gap-2">
                <button type="submit" class="h-12 flex-1 bg-ink font-medium text-slip">შენახვა</button>
                <button type="button" wire:click="$set('panel', '')" class="h-12 border border-ink/20 px-4">დახურვა</button>
            </div>
        </form>
    @endif

    @if ($panel === 'poll' && $canEdit)
        <form wire:submit="addPoll" class="fixed inset-x-0 bottom-0 z-30 mx-auto max-w-lg space-y-2 border-t-2 border-ink bg-slip p-4 text-ink">
            <input wire:model="pollQuestion" class="{{ $field }}" placeholder="რა ვიყიდოთ?">
            <textarea wire:model="pollOptions" rows="4" class="w-full border border-ink/15 bg-slip px-3 py-3 text-ink" placeholder="კოლა&#10;ფანტა&#10;პეპსი"></textarea>
            @error('question') <p class="text-sm text-wine">{{ $message }}</p> @enderror
            @error('options') <p class="text-sm text-wine">{{ $message }}</p> @enderror
            <div class="flex gap-2">
                <button type="submit" class="h-12 flex-1 bg-ink font-medium text-slip">გამოკითხვა</button>
                <button type="button" wire:click="$set('panel', '')" class="h-12 border border-ink/20 px-4">დახურვა</button>
            </div>
        </form>
    @endif

    @if ($canEdit && $panel === '')
        <form wire:submit="addItem" class="fixed inset-x-0 bottom-0 z-20 mx-auto w-full max-w-lg border-t border-rule bg-slip p-3 pb-[max(0.75rem,env(safe-area-inset-bottom))] text-ink">
            <div class="mb-2 flex gap-1 overflow-x-auto">
                @foreach ($emojiChoices as $choice)
                    <button type="button" wire:click="$set('itemEmoji', '{{ $choice }}')" class="flex size-11 shrink-0 items-center justify-center text-xl {{ $itemEmoji === $choice ? 'border-b-2 border-ink' : '' }}">{{ $choice }}</button>
                @endforeach
            </div>
            <div class="flex gap-2">
                <label class="sr-only" for="item-emoji">ემოჯი</label>
                <input id="item-emoji" wire:model="itemEmoji" class="h-12 w-14 border border-ink/15 bg-slip text-center text-xl" placeholder="🙂">
                <label class="sr-only" for="item-name">ნივთი</label>
                <input id="item-name" wire:model="itemName" class="h-12 min-w-0 flex-1 border border-ink/15 bg-slip px-3" placeholder="ნივთი">
                <label class="sr-only" for="item-quantity">რაოდენობა</label>
                <input id="item-quantity" wire:model="itemQuantity" class="h-12 w-20 border border-ink/15 bg-slip px-2" placeholder="რაოდ.">
                <button type="submit" class="h-12 px-3 text-sm font-medium text-white" style="background: {{ $list->accent_color }}">დამატება</button>
            </div>
            @error('name') <p class="mt-1 text-sm text-wine">{{ $message }}</p> @enderror
            <button type="button" wire:click="$set('panel', 'poll')" class="mt-2 h-10 text-sm text-bought">გამოკითხვის დამატება</button>
        </form>
    @endif
</div>
