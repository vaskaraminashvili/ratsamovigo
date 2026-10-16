<x-layouts::auth :title="__('Reset password')">
    <div class="flex min-h-0 flex-1 flex-col gap-6 overflow-y-auto px-5 py-8">
        <x-auth-header :title="__('Reset password')" :description="__('Please enter your new password below')" />

        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('password.update') }}" class="flex flex-col gap-6">
            @csrf
            <input type="hidden" name="token" value="{{ request()->route('token') }}">

            <x-input
                name="email"
                :value="request('email')"
                :label="__('Email')"
                type="email"
                required
                autocomplete="email"
            />

            <x-password
                name="password"
                :label="__('Password')"
                :rules="[]"
                required
                autocomplete="new-password"
                :placeholder="__('Password')"
            />

            <x-password
                name="password_confirmation"
                :label="__('Confirm password')"
                :rules="[]"
                required
                autocomplete="new-password"
                :placeholder="__('Confirm password')"
            />

            <x-button submit class="w-full" data-test="reset-password-button">
                {{ __('Reset password') }}
            </x-button>
        </form>
    </div>
</x-layouts::auth>
