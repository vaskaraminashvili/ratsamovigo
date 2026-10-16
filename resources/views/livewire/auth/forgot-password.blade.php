<x-layouts::auth :title="__('Forgot password')">
    <div class="flex min-h-0 flex-1 flex-col gap-6 overflow-y-auto px-5 py-8">
        <x-auth-header :title="__('Forgot password')" :description="__('Enter your email to receive a password reset link')" />

        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}" class="flex flex-col gap-6">
            @csrf

            <x-input
                name="email"
                :label="__('Email address')"
                type="email"
                required
                autofocus
                placeholder="email@example.com"
            />

            <x-button submit class="w-full" data-test="email-password-reset-link-button">
                {{ __('Email password reset link') }}
            </x-button>
        </form>

        <div class="space-x-1 text-center text-sm text-bought rtl:space-x-reverse">
            <span>{{ __('Or, return to') }}</span>
            <x-link :href="route('login')" navigate>{{ __('log in') }}</x-link>
        </div>
    </div>
</x-layouts::auth>
