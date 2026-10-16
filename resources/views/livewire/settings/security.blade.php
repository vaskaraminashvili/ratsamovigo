@php
    $field = 'mt-2 h-12 w-full rounded-2xl bg-phone px-4 text-base text-ink outline-none ring-1 ring-transparent focus:ring-2 focus:ring-marker';
@endphp

<x-settings.shell title="უსაფრთხოება" subtitle="პაროლი, კოდი და პასკი">
    <form method="POST" wire:submit="updatePassword" class="space-y-4 rounded-[28px] bg-white p-4">
        <h2 class="font-sign text-xl font-bold">პაროლის შეცვლა</h2>

        <div x-data="{ show: false }">
            <div class="flex items-center justify-between gap-3">
                <label for="current_password" class="text-sm font-medium text-marker">ახლანდელი პაროლი</label>
                <button type="button" class="text-sm text-bought" x-on:click="show = !show" x-text="show ? 'დამალვა' : 'ჩვენება'"></button>
            </div>
            <input id="current_password" wire:model="current_password" x-bind:type="show ? 'text' : 'password'" required autocomplete="current-password" class="{{ $field }}">
            @error('current_password')
                <p class="mt-2 text-sm text-wine">{{ $message }}</p>
            @enderror
        </div>

        <div x-data="{ show: false }">
            <div class="flex items-center justify-between gap-3">
                <label for="password" class="text-sm font-medium text-marker">ახალი პაროლი</label>
                <button type="button" class="text-sm text-bought" x-on:click="show = !show" x-text="show ? 'დამალვა' : 'ჩვენება'"></button>
            </div>
            <input id="password" wire:model="password" x-bind:type="show ? 'text' : 'password'" required autocomplete="new-password" passwordrules="{{ \Illuminate\Validation\Rules\Password::defaults()->toPasswordRulesString() }}" class="{{ $field }}">
            @error('password')
                <p class="mt-2 text-sm text-wine">{{ $message }}</p>
            @enderror
        </div>

        <div x-data="{ show: false }">
            <div class="flex items-center justify-between gap-3">
                <label for="password_confirmation" class="text-sm font-medium text-marker">გაიმეორე პაროლი</label>
                <button type="button" class="text-sm text-bought" x-on:click="show = !show" x-text="show ? 'დამალვა' : 'ჩვენება'"></button>
            </div>
            <input id="password_confirmation" wire:model="password_confirmation" x-bind:type="show ? 'text' : 'password'" required autocomplete="new-password" passwordrules="{{ \Illuminate\Validation\Rules\Password::defaults()->toPasswordRulesString() }}" class="{{ $field }}">
            @error('password_confirmation')
                <p class="mt-2 text-sm text-wine">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" data-test="update-password-button" class="slip-press h-12 w-full rounded-full bg-ink text-sm font-medium text-white">
            <span wire:loading.remove wire:target="updatePassword">შენახვა</span>
            <span wire:loading wire:target="updatePassword">ინახება…</span>
        </button>
    </form>

    @if ($canManageTwoFactor)
        <section class="mt-4 rounded-[28px] bg-white p-4">
            <h2 class="font-sign text-xl font-bold">ორფაქტორიანი დაცვა</h2>
            <p class="mt-1 text-sm leading-relaxed text-bought">შესვლისას დამატებითი კოდი გეკითხება ავთენტიფიკატორის აპიდან.</p>

            <div class="mt-4" wire:cloak>
                @if ($twoFactorEnabled)
                    <p class="text-sm font-medium text-marker">ჩართულია</p>
                    <button type="button" wire:click="disable" wire:confirm="ორფაქტორიანი დაცვა გამოირთოს?" class="mt-3 text-sm text-wine">გამორთვა</button>
                    <livewire:settings.two-factor.recovery-codes :$requiresConfirmation />
                @else
                    <button type="button" wire:click="enable" class="slip-press h-12 rounded-full bg-ink px-5 text-sm font-medium text-white">ჩართვა</button>
                @endif
            </div>
        </section>
    @endif

    @if ($canManagePasskeys)
        <section class="mt-4 rounded-[28px] bg-white p-4">
            <h2 class="font-sign text-xl font-bold">პასკები</h2>
            <p class="mt-1 text-sm leading-relaxed text-bought">პაროლის გარეშე შესვლა ამ მოწყობილობიდან.</p>

            <div class="mt-4 space-y-3" wire:cloak>
                @forelse ($passkeys as $passkey)
                    <div class="flex items-center justify-between gap-3 rounded-2xl bg-phone px-4 py-3">
                        <div class="min-w-0">
                            <p class="truncate font-medium">{{ $passkey['name'] }}</p>
                            <p class="mt-0.5 text-sm text-bought">
                                @if ($passkey['authenticator'])
                                    {{ $passkey['authenticator'] }} ·
                                @endif
                                დაემატა {{ $passkey['created_at_diff'] }}
                                @if ($passkey['last_used_at_diff'])
                                    · ბოლოს {{ $passkey['last_used_at_diff'] }}
                                @endif
                            </p>
                        </div>
                        <button type="button" wire:click="confirmDelete({{ $passkey['id'] }})" class="shrink-0 text-sm text-wine">წაშლა</button>
                    </div>
                @empty
                    <p class="text-sm leading-relaxed text-bought">ჯერ პასკი არ გაქვს. დაამატე, რომ პაროლის გარეშე შეხვიდე.</p>
                @endforelse

                <x-passkey-registration />
            </div>
        </section>
    @endif

    @if ($canManageTwoFactor && $showModal)
        <div class="slip-scrim fixed inset-0 z-30 bg-ink/45" wire:click="closeModal"></div>
        <div class="slip-up fixed inset-x-0 bottom-0 z-40 mx-auto max-h-[92dvh] w-full max-w-[430px] space-y-4 overflow-y-auto rounded-t-[28px] bg-white px-5 py-5 text-ink">
            <div class="flex items-center justify-between">
                <h2 class="font-sign text-xl font-bold">{{ $this->modalConfig['title'] }}</h2>
                <button type="button" wire:click="closeModal" class="font-medium text-marker">დახურვა</button>
            </div>
            <p class="text-sm leading-relaxed text-bought">{{ $this->modalConfig['description'] }}</p>

            @if ($showVerificationStep)
                <div x-data x-init="$nextTick(() => $el.querySelector('input')?.focus())">
                    <label for="two-factor-code" class="text-sm font-medium text-marker">კოდი</label>
                    <input id="two-factor-code" wire:model="code" inputmode="numeric" autocomplete="one-time-code" maxlength="6" class="mt-2 h-14 w-full rounded-2xl bg-phone text-center text-2xl tracking-[0.4em] text-ink outline-none ring-1 ring-transparent focus:ring-2 focus:ring-marker">
                    @error('code')
                        <p class="mt-2 text-sm text-wine">{{ $message }}</p>
                    @enderror
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <button type="button" wire:click="resetVerification" class="h-12 rounded-full bg-phone text-sm">უკან</button>
                    <button type="button" wire:click="confirmTwoFactor" class="slip-press h-12 rounded-full bg-ink text-sm font-medium text-white disabled:opacity-50" x-data x-bind:disabled="$wire.code.length < 6">დადასტურება</button>
                </div>
            @else
                @error('setupData')
                    <p class="text-sm text-wine">{{ $message }}</p>
                @enderror

                <div class="mx-auto flex aspect-square w-56 items-center justify-center rounded-[28px] bg-phone p-4">
                    @empty($qrCodeSvg)
                        <p class="text-sm text-bought">იტვირთება…</p>
                    @else
                        <div class="rounded-2xl bg-white p-3">{!! $qrCodeSvg !!}</div>
                    @endempty
                </div>

                <button type="button" wire:click="showVerificationIfNecessary" @disabled($errors->has('setupData')) class="slip-press h-12 w-full rounded-full bg-ink text-sm font-medium text-white disabled:opacity-50">
                    {{ $this->modalConfig['buttonText'] }}
                </button>

                <p class="text-center text-sm text-bought">ან ჩაწერე კოდი ხელით</p>

                <div
                    class="flex items-center gap-2"
                    x-data="{
                        copied: false,
                        async copy() {
                            try {
                                await navigator.clipboard.writeText(@js($manualSetupKey));
                                this.copied = true;
                                setTimeout(() => this.copied = false, 1500);
                            } catch (e) {
                                this.copied = false;
                            }
                        }
                    }"
                >
                    <input type="text" readonly value="{{ $manualSetupKey }}" class="h-12 min-w-0 flex-1 rounded-2xl bg-phone px-4 text-sm text-ink">
                    <button type="button" class="h-12 shrink-0 rounded-full bg-phone px-4 text-sm" x-on:click="copy()" x-text="copied ? 'დაკოპირდა' : 'კოპირება'">კოპირება</button>
                </div>
            @endif
        </div>
    @endif

    @if ($showDeleteModal)
        <div class="slip-scrim fixed inset-0 z-30 bg-ink/45" wire:click="closeDeleteModal"></div>
        <div class="slip-up fixed inset-x-0 bottom-0 z-40 mx-auto w-full max-w-[430px] space-y-4 rounded-t-[28px] bg-white px-5 py-5 text-ink">
            <div class="flex items-center justify-between">
                <h2 class="font-sign text-xl font-bold">პასკის წაშლა</h2>
                <button type="button" wire:click="closeDeleteModal" class="font-medium text-marker">დახურვა</button>
            </div>
            <p class="text-sm leading-relaxed text-bought">„{{ $deletingPasskeyName }}“ წაიშლება და ამ პასკით ვეღარ შეხვალ.</p>
            <div class="grid grid-cols-2 gap-2">
                <button type="button" wire:click="closeDeleteModal" class="h-12 rounded-full bg-phone text-sm">გაუქმება</button>
                <button type="button" wire:click="deletePasskey" class="slip-press h-12 rounded-full bg-wine text-sm font-medium text-white">წაშლა</button>
            </div>
        </div>
    @endif
</x-settings.shell>
