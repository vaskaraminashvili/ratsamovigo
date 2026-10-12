@php
    $surface = \App\Support\ListAppearance::preset($list->background_preset);
    $photo = $list->background_path ? \Illuminate\Support\Facades\Storage::url($list->background_path) : null;
@endphp

<div @if ($panel === '') wire:poll.2s @endif class="min-h-dvh {{ $photo ? 'bg-zinc-900 bg-cover bg-center text-white' : $surface['class'] }}" @if($photo) style="background-image: linear-gradient(rgba(0,0,0,.45), rgba(0,0,0,.55)), url('{{ $photo }}')" @endif>
    <div class="mx-auto min-h-dvh w-full max-w-lg pb-36">
        <header class="sticky top-0 z-20 flex items-center gap-3 px-4 py-3 backdrop-blur" style="background: color-mix(in srgb, var(--tw-bg, transparent) 70%, transparent)">
            <a href="{{ route('lists.index') }}" class="flex h-11 w-11 items-center justify-center rounded-full bg-white/80 text-lg text-zinc-900" wire:navigate>←</a>
            <div class="min-w-0 flex-1">
                <p class="truncate text-lg font-semibold">{{ $list->emoji }} {{ $list->name }}</p>
                <p class="text-sm opacity-70">დარჩენილი: {{ $openItems->count() }}</p>
            </div>
            @if ($canManage)
                <button type="button" wire:click="$set('panel', 'share')" class="h-11 rounded-full bg-white/80 px-3 text-sm font-medium text-zinc-900">გაზიარება</button>
                <button type="button" wire:click="$set('panel', 'settings')" class="flex h-11 w-11 items-center justify-center rounded-full bg-white/80 text-zinc-900">✎</button>
            @endif
        </header>

        <div class="space-y-3 px-4">
            @foreach ($polls as $poll)
                @php $myVote = $poll->votes->firstWhere('user_id', auth()->id()); @endphp
                <section class="rounded-3xl bg-white/95 p-4 text-zinc-900 shadow-sm" style="border-top: 4px solid {{ $list->accent_color }}">
                    <div class="mb-3 flex items-start justify-between gap-3">
                        <h2 class="text-base font-semibold">{{ $poll->question }}</h2>
                        @if ($canEdit)
                            <button type="button" wire:click="togglePollPin({{ $poll->id }})" class="text-sm">{{ $poll->is_pinned ? 'ჩამაგრებულია' : 'ზევით' }}</button>
                        @endif
                    </div>
                    <div class="space-y-2">
                        @foreach ($poll->options as $option)
                            <label class="flex min-h-12 items-center gap-3 rounded-2xl border border-zinc-200 px-3">
                                <input type="radio" class="size-5" wire:model="selectedOptions.{{ $poll->id }}" value="{{ $option->id }}" @checked($myVote?->poll_option_id === $option->id)>
                                <span class="flex-1">{{ $option->label }}</span>
                                <span class="text-sm text-zinc-500">{{ $option->votes->count() }}</span>
                            </label>
                        @endforeach
                    </div>
                    <div class="mt-3 flex flex-wrap gap-2">
                        <button type="button" wire:click="vote({{ $poll->id }})" class="h-11 rounded-2xl bg-zinc-900 px-4 text-sm font-semibold text-white">ხმა</button>
                        @if ($canEdit)
                            <button type="button" wire:click="promoteWinner({{ $poll->id }})" class="h-11 rounded-2xl border border-zinc-300 px-4 text-sm">გამარჯვებული ნივთად</button>
                            <button type="button" wire:click="deletePoll({{ $poll->id }})" class="h-11 rounded-2xl px-4 text-sm text-rose-600">წაშლა</button>
                        @endif
                    </div>
                </section>
            @endforeach

            @foreach ($openItems as $item)
                @include('livewire.lists.partials.item', ['item' => $item, 'checked' => false])
            @endforeach

            @if ($checkedItems->isNotEmpty())
                <section class="rounded-3xl bg-white/90 text-zinc-900">
                    <button type="button" wire:click="$toggle('showChecked')" class="flex h-12 w-full items-center justify-between px-4 text-sm font-medium">
                        <span>მონიშნული ({{ $checkedItems->count() }})</span>
                        <span>{{ $showChecked ? '▲' : '▼' }}</span>
                    </button>
                    @if ($showChecked)
                        <div class="space-y-2 px-3 pb-3">
                            @foreach ($checkedItems as $item)
                                @include('livewire.lists.partials.item', ['item' => $item, 'checked' => true])
                            @endforeach
                            @if ($canEdit)
                                <button type="button" wire:click="clearChecked" class="h-11 w-full rounded-2xl text-sm text-rose-600">მონიშნულის გასუფთავება</button>
                            @endif
                        </div>
                    @endif
                </section>
            @endif

            @if ($offers->isNotEmpty())
                <section class="space-y-2">
                    @foreach ($offers as $offer)
                        <a href="{{ $offer->url }}" class="block rounded-3xl bg-amber-50 p-4 text-zinc-900">
                            <p class="text-sm font-semibold">{{ $offer->storeName }}</p>
                            <p>{{ $offer->itemName }} — {{ $offer->discount }}</p>
                        </a>
                    @endforeach
                </section>
            @endif

            @if ($openItems->isEmpty() && $checkedItems->isEmpty() && $polls->isEmpty())
                <p class="py-10 text-center text-sm opacity-70">სია ცარიელია.</p>
            @endif
        </div>
    </div>

    @if ($panel === 'settings' && $canManage)
        <div class="fixed inset-0 z-30 overflow-y-auto bg-white text-zinc-900">
            <form wire:submit="saveAppearance" class="mx-auto max-w-lg space-y-4 px-4 py-4 pb-10">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold">სიის სახე</h2>
                    <button type="button" wire:click="$set('panel', '')" class="h-11 px-3">დახურვა</button>
                </div>
                <input wire:model="listName" class="h-12 w-full rounded-2xl border px-4" placeholder="სახელი">
                @error('listName') <p class="text-sm text-rose-600">{{ $message }}</p> @enderror
                <input wire:model="listEmoji" class="h-12 w-full rounded-2xl border px-4" placeholder="ემოჯი">
                <div class="flex gap-2">
                    @foreach ($accents as $accent)
                        <button type="button" wire:click="$set('accentColor', '{{ $accent }}')" class="size-11 rounded-full border-2" style="background: {{ $accent }}; border-color: {{ $accentColor === $accent ? '#111' : 'transparent' }}"></button>
                    @endforeach
                </div>
                <div class="grid grid-cols-2 gap-2">
                    @foreach ($presets as $key => $preset)
                        <button type="button" wire:click="$set('backgroundPreset', '{{ $key }}')" @class([$preset['class'], 'h-12 rounded-2xl border text-sm', 'ring-2 ring-zinc-900' => $backgroundPreset === $key])>{{ $preset['label'] }}</button>
                    @endforeach
                </div>
                <label class="block text-sm">
                    საკუთარი ფონი
                    <input type="file" wire:model="backgroundUpload" accept="image/*" class="mt-1 block w-full text-sm">
                </label>
                @error('backgroundUpload') <p class="text-sm text-rose-600">{{ $message }}</p> @enderror
                @if ($list->background_path)
                    <button type="button" wire:click="removeBackground" class="text-sm text-rose-600">ფონის სურათის წაშლა</button>
                @endif
                <div class="grid grid-cols-3 gap-2">
                    @foreach ($checkboxStyles as $style)
                        <button type="button" wire:click="$set('checkboxStyle', '{{ $style->value }}')" @class(['h-12 rounded-2xl border text-sm', 'bg-zinc-900 text-white' => $checkboxStyle === $style->value])>{{ $style->label() }}</button>
                    @endforeach
                </div>
                <label class="flex h-12 items-center justify-between"><span>ემოჯი</span><input type="checkbox" wire:model="showEmoji" class="size-5"></label>
                <label class="flex h-12 items-center justify-between"><span>რაოდენობა</span><input type="checkbox" wire:model="showQuantity" class="size-5"></label>
                <label class="flex h-12 items-center justify-between"><span>მოწონება</span><input type="checkbox" wire:model="showLikes" class="size-5"></label>
                <label class="flex h-12 items-center justify-between"><span>ავტორი</span><input type="checkbox" wire:model="showAuthor" class="size-5"></label>
                <button type="submit" class="h-12 w-full rounded-2xl bg-emerald-600 font-semibold text-white">შენახვა</button>
                <button type="button" wire:click="deleteList" wire:confirm="სია წაიშალოს?" class="h-12 w-full rounded-2xl text-rose-600">სიის წაშლა</button>
            </form>
        </div>
    @endif

    @if ($panel === 'share' && $canManage)
        <div class="fixed inset-0 z-30 overflow-y-auto bg-white px-4 py-4 text-zinc-900">
            <div class="mx-auto max-w-lg space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold">ხალხი</h2>
                    <button type="button" wire:click="$set('panel', '')" class="h-11 px-3">დახურვა</button>
                </div>
                <button type="button" wire:click="share('viewer')" class="h-12 w-full rounded-2xl border">ნახვის ბმულის შეცვლა</button>
                @if ($viewLink)
                    <input readonly value="{{ route('lists.join', $viewLink->token) }}" class="h-12 w-full rounded-2xl border px-3 text-sm">
                @endif
                <button type="button" wire:click="share('editor')" class="h-12 w-full rounded-2xl border">რედაქტირების ბმულის შეცვლა</button>
                @if ($editLink)
                    <input readonly value="{{ route('lists.join', $editLink->token) }}" class="h-12 w-full rounded-2xl border px-3 text-sm">
                @endif
                <ul class="space-y-2">
                    @foreach ($list->members as $member)
                        <li class="flex h-14 items-center justify-between rounded-2xl border px-3">
                            <span>
                                <span class="block font-medium">{{ $member->user->name }}</span>
                                <span class="text-sm text-zinc-500">{{ $member->role->label() }}</span>
                            </span>
                            @if ($member->role !== \App\Enums\ListRole::Owner)
                                <button type="button" wire:click="removeMember({{ $member->id }})" class="text-sm text-rose-600">წაშლა</button>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    @if ($panel === 'item' && $canEdit)
        <form wire:submit="saveItem" class="fixed inset-x-0 bottom-0 z-30 mx-auto max-w-lg space-y-2 rounded-t-3xl bg-white p-4 text-zinc-900 shadow-2xl">
            <input wire:model="itemName" class="h-12 w-full rounded-2xl border px-4" placeholder="სახელი">
            <input wire:model="itemQuantity" class="h-12 w-full rounded-2xl border px-4" placeholder="რაოდენობა">
            <input wire:model="itemEmoji" class="h-12 w-full rounded-2xl border px-4" placeholder="ემოჯი">
            <input wire:model="itemNote" class="h-12 w-full rounded-2xl border px-4" placeholder="შენიშვნა">
            <input type="file" wire:model="itemPhoto" accept="image/*" class="block w-full text-sm">
            @error('name') <p class="text-sm text-rose-600">{{ $message }}</p> @enderror
            <div class="flex gap-2">
                <button type="submit" class="h-12 flex-1 rounded-2xl bg-emerald-600 font-semibold text-white">შენახვა</button>
                <button type="button" wire:click="$set('panel', '')" class="h-12 rounded-2xl border px-4">დახურვა</button>
            </div>
        </form>
    @endif

    @if ($panel === 'poll' && $canEdit)
        <form wire:submit="addPoll" class="fixed inset-x-0 bottom-0 z-30 mx-auto max-w-lg space-y-2 rounded-t-3xl bg-white p-4 text-zinc-900 shadow-2xl">
            <input wire:model="pollQuestion" class="h-12 w-full rounded-2xl border px-4" placeholder="რა ვიყიდოთ?">
            <textarea wire:model="pollOptions" rows="4" class="w-full rounded-2xl border px-4 py-3" placeholder="კოლა&#10;ფანტა&#10;პეპსი"></textarea>
            @error('question') <p class="text-sm text-rose-600">{{ $message }}</p> @enderror
            @error('options') <p class="text-sm text-rose-600">{{ $message }}</p> @enderror
            <div class="flex gap-2">
                <button type="submit" class="h-12 flex-1 rounded-2xl bg-emerald-600 font-semibold text-white">გამოკითხვა</button>
                <button type="button" wire:click="$set('panel', '')" class="h-12 rounded-2xl border px-4">დახურვა</button>
            </div>
        </form>
    @endif

    @if ($canEdit && $panel === '')
        <form wire:submit="addItem" class="fixed inset-x-0 bottom-0 z-20 mx-auto w-full max-w-lg border-t border-zinc-200 bg-white p-3 pb-[max(0.75rem,env(safe-area-inset-bottom))] text-zinc-900">
            <div class="mb-2 flex gap-2 overflow-x-auto">
                @foreach ($emojiChoices as $choice)
                    <button type="button" wire:click="$set('itemEmoji', '{{ $choice }}')" class="flex size-11 shrink-0 items-center justify-center rounded-full bg-zinc-100 text-xl">{{ $choice }}</button>
                @endforeach
            </div>
            <div class="flex gap-2">
                <input wire:model="itemEmoji" class="h-12 w-14 rounded-2xl border text-center text-xl" placeholder="🙂">
                <input wire:model="itemName" class="h-12 min-w-0 flex-1 rounded-2xl border px-3" placeholder="ნივთი">
                <input wire:model="itemQuantity" class="h-12 w-20 rounded-2xl border px-2" placeholder="რაოდ.">
                <button type="submit" class="h-12 rounded-2xl bg-emerald-600 px-4 font-semibold text-white">+</button>
            </div>
            @error('name') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
            <button type="button" wire:click="$set('panel', 'poll')" class="mt-2 h-10 text-sm text-zinc-600">გამოკითხვის დამატება</button>
        </form>
    @endif
</div>
