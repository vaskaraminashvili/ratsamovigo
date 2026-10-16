@assets
@vite('resources/js/passkeys.js')
@endassets

<div
    x-data="{
        supported: false,
        showForm: false,
        name: '',
        loading: false,
        error: null,
        updateSupport() {
            this.supported = Boolean(window.Passkeys?.isSupported());
        },
        getDefaultPasskeyName() {
            const ua = navigator.userAgent;

            const browser = [
                { pattern: /Edg|Edge/, name: 'Edge' },
                { pattern: /OPR|Opera|OPiOS/, name: 'Opera' },
                { pattern: /Firefox|FxiOS/, name: 'Firefox' },
                { pattern: /Chrome|CriOS/, name: 'Chrome' },
                { pattern: /Safari/, name: 'Safari' },
            ].find(({ pattern }) => pattern.test(ua))?.name;

            const os = [
                { pattern: /iPhone/, name: 'iPhone' },
                { pattern: /iPad|Macintosh(?=.*Mobile)/, name: 'iPad' },
                { pattern: /Android/, name: 'Android' },
                { pattern: /Mac/, name: 'Mac' },
                { pattern: /Windows/, name: 'Windows' },
            ].find(({ pattern }) => pattern.test(ua))?.name;

            return [browser, os].filter(Boolean).join(' on ') || '';
        },
        init() {
            this.name = this.getDefaultPasskeyName();
            this.updateSupport();

            window.addEventListener('passkeys:ready', () => this.updateSupport(), { once: true });
        },
        async register() {
            if (!this.name.trim()) return;

            this.loading = true;
            this.error = null;

            try {
                await window.Passkeys.register({ name: this.name });
                this.name = '';
                this.showForm = false;
                await $wire.loadPasskeys();
            } catch (e) {
                if (e.constructor?.name !== 'UserCancelledError') {
                    this.error = e.message;
                }
            } finally {
                this.loading = false;
            }
        },
        cancel() {
            this.showForm = false;
            this.name = '';
            this.error = null;
        },
    }"
>
    <template x-if="!supported">
        <p class="text-sm text-bought">ეს ბრაუზერი პასკებს არ იჭერს.</p>
    </template>

    <template x-if="supported && !showForm">
        <button type="button" class="slip-press h-12 rounded-full bg-ink px-5 text-sm font-medium text-white" x-on:click="showForm = true">პასკის დამატება</button>
    </template>

    <template x-if="supported && showForm">
        <div class="space-y-3 rounded-2xl bg-phone p-4">
            <x-input
                label="სახელი"
                x-model="name"
                placeholder="მაგალითად, ტელეფონი"
                x-on:keydown.enter.prevent="register()"
                x-ref="passkeyNameInput"
                x-init="$nextTick(() => $refs.passkeyNameInput?.focus())"
            />
            <p class="text-sm text-bought">სახელი გაგახსენებს, რომელ მოწყობილობას ეკუთვნის.</p>
            <p x-show="error" x-text="error" x-cloak class="text-sm text-wine"></p>
            <div class="flex items-center gap-3">
                <button type="button" class="slip-press h-12 rounded-full bg-ink px-5 text-sm font-medium text-white disabled:opacity-50" x-on:click="register()" x-bind:disabled="loading || !name.trim()">
                    <span x-show="!loading">დამატება</span>
                    <span x-show="loading" x-cloak>ინახება…</span>
                </button>
                <button type="button" class="h-12 text-sm text-bought" x-on:click="cancel()">გაუქმება</button>
            </div>
        </div>
    </template>
</div>
