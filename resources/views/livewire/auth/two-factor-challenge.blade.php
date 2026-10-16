<x-layouts::auth :title="__('Two-factor authentication')">
    <div class="flex min-h-0 flex-1 flex-col gap-6 overflow-y-auto px-5 py-8">
        <div
            class="relative h-auto w-full"
            x-cloak
            x-data="{
                showRecoveryInput: @js($errors->has('recovery_code')),
                toggleInput() {
                    this.showRecoveryInput = ! this.showRecoveryInput;
                },
            }"
        >
            <div x-show="!showRecoveryInput">
                <x-auth-header
                    :title="__('Authentication code')"
                    :description="__('Enter the authentication code provided by your authenticator application.')"
                />
            </div>

            <div x-show="showRecoveryInput">
                <x-auth-header
                    :title="__('Recovery code')"
                    :description="__('Please confirm access to your account by entering one of your emergency recovery codes.')"
                />
            </div>

            <form method="POST" action="{{ route('two-factor.login.store') }}">
                @csrf

                <div class="space-y-5 text-center">
                    <template x-if="!showRecoveryInput">
                        <div class="my-5 flex items-center justify-center">
                            <x-pin length="6" name="code" numbers label="OTP Code" />
                        </div>
                    </template>

                    <template x-if="showRecoveryInput">
                        <div class="my-5">
                            <x-input
                                type="text"
                                name="recovery_code"
                                autocomplete="one-time-code"
                                required
                            />
                        </div>
                    </template>

                    @error('recovery_code')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror

                    <x-button submit class="w-full">
                        {{ __('Continue') }}
                    </x-button>
                </div>

                <div class="mt-5 space-x-0.5 text-center text-sm leading-5">
                    <span class="opacity-50">{{ __('or you can') }}</span>
                    <div class="inline cursor-pointer font-medium underline opacity-80">
                        <span x-show="!showRecoveryInput" @click="toggleInput()">{{ __('login using a recovery code') }}</span>
                        <span x-show="showRecoveryInput" @click="toggleInput()">{{ __('login using an authentication code') }}</span>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-layouts::auth>
