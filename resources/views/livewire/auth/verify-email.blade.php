<x-layouts::auth :title="__('Email verification')">
    <div class="flex min-h-0 flex-1 flex-col gap-6 overflow-y-auto px-5 py-8">
        <p class="text-center text-sm text-ink">
            {{ __('Please verify your email address by clicking on the link we just emailed to you.') }}
        </p>

        @if (session('status') == 'verification-link-sent')
            <p class="text-center text-sm font-medium text-green-600 dark:text-green-400">
                {{ __('A new verification link has been sent to the email address you provided during registration.') }}
            </p>
        @endif

        <div class="flex flex-col items-center justify-between space-y-3">
            <form method="POST" action="{{ route('verification.send') }}" class="w-full">
                @csrf
                <x-button submit class="w-full">
                    {{ __('Resend verification email') }}
                </x-button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <x-button submit flat class="cursor-pointer text-sm" data-test="logout-button">
                    {{ __('Log out') }}
                </x-button>
            </form>
        </div>
    </div>
</x-layouts::auth>
