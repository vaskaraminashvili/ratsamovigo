<!DOCTYPE html>
<html lang="ka">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name') }}</title>
        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="slip-ui min-h-dvh bg-floor font-slip text-ink antialiased">
        <main class="aisle-float relative mx-auto min-h-dvh w-full max-w-[430px] overflow-hidden bg-phone md:my-6 md:min-h-[calc(100dvh-3rem)] md:rounded-[36px]">
            <div class="pointer-events-none absolute -right-28 top-32 size-80 rounded-full bg-lime/25 blur-3xl" aria-hidden="true"></div>
            <div class="pointer-events-none absolute -left-32 top-[520px] size-72 rounded-full bg-coral/15 blur-3xl" aria-hidden="true"></div>

            <nav class="relative z-20 flex w-full items-center justify-between px-5 py-5">
                <a href="{{ auth()->check() ? route('lists.index') : route('home') }}" class="flex items-center gap-2.5" aria-label="სია">
                    <span class="relative grid size-10 place-items-center overflow-hidden rounded-[14px] bg-marker text-white shadow-[0_8px_18px_rgba(123,92,246,.22)]">
                        <span class="absolute -right-2 -top-2 size-5 rounded-full bg-lime"></span>
                        <svg viewBox="0 0 24 24" class="relative size-5" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M7 8.5h10M7 12h7M7 15.5h5"></path>
                        </svg>
                    </span>
                    <span class="font-sign text-lg font-bold tracking-[-0.03em]">სია</span>
                </a>

                @auth
                    <a href="{{ route('lists.index') }}" class="slip-press rounded-full bg-ink px-4 py-2.5 text-xs font-bold text-white shadow-[0_8px_22px_rgba(28,23,48,.18)]">ჩემი სიები</a>
                @else
                    <div class="flex items-center gap-2">
                        @if (Route::has('login'))
                            <button type="button" onclick="openAuth('login')" class="rounded-full px-3 py-2.5 text-xs font-bold text-marker transition hover:bg-marker/10">შესვლა</button>
                        @endif
                        @if (Route::has('register'))
                            <button type="button" onclick="openAuth('register')" class="slip-press rounded-full bg-ink px-4 py-2.5 text-xs font-bold text-white shadow-[0_8px_22px_rgba(28,23,48,.18)]">დაწყება</button>
                        @endif
                    </div>
                @endauth
            </nav>

            <section class="relative grid w-full gap-14 px-5 pb-20 pt-9">
                <div class="relative z-10 text-center">
                    <div class="mx-auto inline-flex items-center gap-2 rounded-full bg-marker/10 px-3 py-2 text-[10px] font-bold tracking-[.12em] text-marker">
                        <span class="size-2 rounded-full bg-coral"></span>
                        საყიდლები, ერთად
                    </div>
                    <h1 class="mx-auto mt-6 font-sign text-[2.75rem] font-bold leading-[1.02] tracking-[-0.055em]">
                        ერთი სია.<br>
                        <span class="text-marker">ყველა</span> ერთად.
                    </h1>
                    <p class="mx-auto mt-6 text-base font-medium leading-7 text-ink/70">
                        დაგეგმე საყიდლები, გააზიარე სია და ნახე ვინ რას იღებს — რომ ყველაფერი იყიდოს და არაფერი გაიმეოროს.
                    </p>

                    <div class="mt-8 flex flex-col justify-center gap-3">
                        @auth
                            <a href="{{ route('lists.index') }}" class="slip-press flex h-14 items-center justify-center gap-2 rounded-2xl bg-marker px-7 text-sm font-bold text-white shadow-[0_12px_28px_rgba(123,92,246,.25)]">
                                ჩემი სიები
                                <svg viewBox="0 0 24 24" class="size-4" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m9 18 6-6-6-6"></path></svg>
                            </a>
                        @else
                            @if (Route::has('register'))
                                <button type="button" onclick="openAuth('register')" class="slip-press group flex h-14 items-center justify-center gap-2 rounded-2xl bg-marker px-7 text-sm font-bold text-white shadow-[0_12px_28px_rgba(123,92,246,.25)] transition hover:-translate-y-0.5">
                                    შექმენი პირველი სია
                                    <svg viewBox="0 0 24 24" class="size-4 transition group-hover:translate-x-0.5" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m9 18 6-6-6-6"></path></svg>
                                </button>
                            @endif
                            @if (Route::has('login'))
                                <button type="button" onclick="openAuth('login')" class="slip-press h-14 rounded-2xl border border-rule bg-white px-7 text-sm font-bold text-ink transition hover:bg-white/70">
                                    უკვე მაქვს ანგარიში
                                </button>
                            @endif
                        @endauth
                    </div>

                    <div class="mt-8 flex items-center justify-center gap-4 text-[11px] font-semibold text-bought">
                        <span class="flex items-center gap-1.5">
                            <svg viewBox="0 0 24 24" class="size-4 text-marker" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m5 12 4 4L19 6"></path></svg>
                            უფასოა
                        </span>
                        <span class="size-1 rounded-full bg-bought/50"></span>
                        <span>ბარათი არ სჭირდება</span>
                    </div>
                </div>

                <div class="relative px-4 pb-4" aria-hidden="true">
                    <div class="relative mx-auto w-full max-w-[390px]">
                        <div class="absolute -left-8 top-24 size-20 rounded-full bg-coral/30 blur-2xl"></div>
                        <div class="absolute -right-7 bottom-20 size-24 rounded-full bg-lime/60 blur-2xl"></div>

                        <div class="relative rotate-[1.5deg] rounded-[32px] border-[6px] border-ink bg-phone p-2 shadow-[0_34px_70px_rgba(28,23,48,.24)]">
                            <div class="overflow-hidden rounded-[23px]">
                                <div class="relative overflow-hidden bg-marker px-5 pb-6 pt-4 text-white">
                                    <div class="absolute -right-7 -top-8 size-24 rounded-full border-[20px] border-lime"></div>
                                    <div class="absolute -bottom-8 -left-7 size-16 rounded-full bg-coral"></div>
                                    <div class="relative flex items-center justify-between">
                                        <span class="rounded-full bg-white/15 px-3 py-1.5 text-[10px] font-bold">საერთო სია</span>
                                        <div class="flex -space-x-2">
                                            <span class="grid size-7 place-items-center rounded-full border-2 border-marker bg-blush text-[9px] font-bold text-blush-ink">მ</span>
                                            <span class="grid size-7 place-items-center rounded-full border-2 border-marker bg-white text-[9px] font-bold text-marker">ლ</span>
                                            <span class="grid size-7 place-items-center rounded-full border-2 border-marker bg-lime text-[9px] font-bold text-ink">ი</span>
                                        </div>
                                    </div>
                                    <div class="relative mt-7">
                                        <p class="text-[10px] font-semibold text-white/65">შაბათის ვახშამი</p>
                                        <p class="mt-1 font-sign text-2xl font-bold tracking-[-0.04em]">შაბათ-კვირის საყიდლები</p>
                                        <div class="mt-3 flex items-center gap-2">
                                            <span class="rounded-full bg-lime px-2.5 py-1 text-[9px] font-bold text-ink">დარჩა 5</span>
                                            <span class="text-[9px] font-semibold text-white/65">აღებულია 3</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="space-y-3 bg-phone p-3">
                                    <div class="rounded-[20px] bg-cream p-3.5">
                                        <div class="flex items-center gap-3">
                                            <span class="grid size-9 place-items-center rounded-xl bg-coral text-white">
                                                <svg viewBox="0 0 24 24" class="size-4" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M4 7h16M6 12h8M6 17h5"></path></svg>
                                            </span>
                                            <div class="min-w-0 flex-1">
                                                <p class="text-[9px] font-bold tracking-[.13em] text-blush-ink">სწრაფი გამოკითხვა</p>
                                                <p class="mt-0.5 truncate text-xs font-bold">რომელი პური ავიღოთ?</p>
                                            </div>
                                            <span class="text-[9px] font-bold text-bought">3 ხმა</span>
                                        </div>
                                    </div>

                                    <div class="rounded-[20px] bg-white p-3.5 shadow-[0_7px_24px_rgba(28,23,48,.06)]">
                                        <div class="flex items-center gap-3 border-b border-rule pb-3">
                                            <span class="grid size-9 place-items-center rounded-xl bg-blush text-blush-ink">
                                                <svg viewBox="0 0 24 24" class="size-4" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M5 11h14l-1 9H6l-1-9ZM8 11a4 4 0 0 1 8 0"></path></svg>
                                            </span>
                                            <div class="flex-1">
                                                <p class="text-[9px] font-bold tracking-[.13em] text-bought">ვახშამი · 2/4</p>
                                                <p class="mt-0.5 text-xs font-bold">პასტის საღამო</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-3 border-b border-rule py-2.5">
                                            <span class="grid size-5 place-items-center rounded-full border-2 border-marker bg-marker text-white">
                                                <svg viewBox="0 0 24 24" class="size-3" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 4 4L19 6"></path></svg>
                                            </span>
                                            <div class="flex-1">
                                                <p class="text-[11px] font-bold text-bought line-through">სპაგეტი</p>
                                                <p class="mt-0.5 text-[8px] font-semibold text-bought">500 გ</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-3 border-b border-rule py-2.5">
                                            <span class="grid size-5 place-items-center rounded-full border-2 border-bought/40"></span>
                                            <div class="flex-1">
                                                <p class="text-[11px] font-bold">ჩერი პომიდორი</p>
                                                <p class="mt-0.5 text-[8px] font-semibold text-bought">300 გ</p>
                                            </div>
                                            <span class="rounded-full bg-marker/10 px-2 py-1 text-[8px] font-bold text-marker">ვიყიდი</span>
                                        </div>
                                        <div class="flex items-center gap-3 py-2.5">
                                            <span class="grid size-5 place-items-center rounded-full border-2 border-bought/40"></span>
                                            <div class="flex-1">
                                                <p class="text-[11px] font-bold">რეჰანი</p>
                                                <p class="mt-0.5 text-[8px] font-semibold text-bought">1 კონა</p>
                                            </div>
                                            <span class="rounded-full bg-marker/10 px-2 py-1 text-[8px] font-bold text-marker">ვიყიდი</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="absolute -left-3 top-[42%] -rotate-6 rounded-2xl bg-white p-3 shadow-[0_14px_32px_rgba(28,23,48,.16)]">
                            <div class="flex items-center gap-2">
                                <span class="grid size-8 place-items-center rounded-full bg-lime text-ink">
                                    <svg viewBox="0 0 24 24" class="size-4" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 4 4L19 6"></path></svg>
                                </span>
                                <div>
                                    <p class="text-[10px] font-bold">რძე აღებულია</p>
                                    <p class="text-[8px] font-semibold text-bought">ახლახანს</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="relative bg-ink px-5 py-20 text-white">
                <div class="pointer-events-none absolute right-[8%] top-0 size-20 -translate-y-1/2 rounded-full bg-coral" aria-hidden="true"></div>
                <p class="text-[10px] font-bold tracking-[.16em] text-lime">ყოველდღიური ცხოვრებისთვის</p>
                <h2 class="mt-3 font-sign text-3xl font-bold tracking-[-0.04em]">ნაკლები შეთანხმება. მეტი საქმე.</h2>
                <div class="mt-10 grid gap-4">
                    <article class="rounded-[26px] bg-white/7 p-6 ring-1 ring-white/10">
                        <div class="flex items-start justify-between">
                            <span class="grid size-12 place-items-center rounded-2xl bg-marker">
                                <svg viewBox="0 0 24 24" class="size-5" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M8 6h13M8 12h13M8 18h13"></path><path d="M3 6h.01M3 12h.01M3 18h.01"></path></svg>
                            </span>
                            <span class="text-xs font-bold text-white/30">01</span>
                        </div>
                        <h3 class="mt-6 text-lg font-bold">ერთი საერთო სია</h3>
                        <p class="mt-3 text-sm font-medium leading-6 text-white/55">დაამატე პროდუქტები ან დააჯგუფე ინგრედიენტები კერძის მიხედვით, რომ გეგმა ადვილად იკითხებოდეს.</p>
                    </article>
                    <article class="rounded-[26px] bg-white/7 p-6 ring-1 ring-white/10">
                        <div class="flex items-start justify-between">
                            <span class="grid size-12 place-items-center rounded-2xl bg-coral">
                                <svg viewBox="0 0 24 24" class="size-5" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="8" cy="8" r="3"></circle><circle cx="16" cy="9" r="3"></circle><path d="M3 20a5 5 0 0 1 10 0M12 20a5 5 0 0 1 9 0"></path></svg>
                            </span>
                            <span class="text-xs font-bold text-white/30">02</span>
                        </div>
                        <h3 class="mt-6 text-lg font-bold">მოიწვიე შენი ხალხი</h3>
                        <p class="mt-3 text-sm font-medium leading-6 text-white/55">გააზიარე ოჯახთან, თანამოსახლეებთან ან მეგობრებთან. ცვლილებებს ყველა მაშინვე ხედავს.</p>
                    </article>
                    <article class="rounded-[26px] bg-white/7 p-6 ring-1 ring-white/10">
                        <div class="flex items-start justify-between">
                            <span class="grid size-12 place-items-center rounded-2xl bg-lime text-ink">
                                <svg viewBox="0 0 24 24" class="size-5" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m5 12 4 4L19 6"></path><path d="M12 3a9 9 0 1 1-9 9"></path></svg>
                            </span>
                            <span class="text-xs font-bold text-white/30">03</span>
                        </div>
                        <h3 class="mt-6 text-lg font-bold">მონიშნე და წადი</h3>
                        <p class="mt-3 text-sm font-medium leading-6 text-white/55">აირჩიე რას იყიდი, მაღაზიაში მონიშნე და ერთი და იგივე აღარ იყიდება ორჯერ.</p>
                    </article>
                </div>
            </section>

            <section class="bg-lime px-5 py-16">
                <div class="mx-auto flex flex-col items-center text-center">
                    <span class="grid size-12 place-items-center rounded-2xl bg-ink text-white">
                        <svg viewBox="0 0 24 24" class="size-5" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 5v14M5 12h14"></path></svg>
                    </span>
                    <h2 class="mt-5 font-sign text-3xl font-bold tracking-[-0.045em]">შენი შემდეგი საყიდლები აქ იწყება.</h2>
                    <p class="mt-3 max-w-lg text-sm font-semibold leading-6 text-ink/70">სია წამებში იქმნება. მოიწვიე ყველა, ვინც უნდა დაგეხმაროს.</p>
                    @auth
                        <a href="{{ route('lists.index') }}" class="slip-press mt-7 rounded-2xl bg-ink px-7 py-4 text-sm font-bold text-white shadow-[0_12px_26px_rgba(28,23,48,.2)]">ჩემი სიები</a>
                    @else
                        @if (Route::has('register'))
                            <button type="button" onclick="openAuth('register')" class="slip-press mt-7 rounded-2xl bg-ink px-7 py-4 text-sm font-bold text-white shadow-[0_12px_26px_rgba(28,23,48,.2)] transition hover:-translate-y-0.5">შექმენი უფასო ანგარიში</button>
                        @endif
                    @endauth
                </div>
            </section>

            <footer class="bg-phone px-5 py-7">
                <div class="flex flex-col items-center justify-between gap-4">
                    <span class="flex items-center gap-2.5">
                        <span class="relative grid size-10 place-items-center overflow-hidden rounded-[14px] bg-marker text-white">
                            <span class="absolute -right-2 -top-2 size-5 rounded-full bg-lime"></span>
                            <svg viewBox="0 0 24 24" class="relative size-5" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 8.5h10M7 12h7M7 15.5h5"></path></svg>
                        </span>
                        <span class="font-sign text-lg font-bold tracking-[-0.03em]">სია</span>
                    </span>
                    <p class="text-[11px] font-semibold text-bought">საერთო საყიდლები, მარტივად.</p>
                </div>
            </footer>
        </main>

        @guest
            <dialog id="auth-dialog" class="auth-sheet" aria-labelledby="auth-title">
                <div class="max-h-[calc(100dvh-1.5rem)] w-full max-w-[406px] overflow-y-auto rounded-[30px] bg-phone p-6 shadow-[0_32px_80px_rgba(28,23,48,.3)]" onclick="event.stopPropagation()">
                    <div id="auth-login" @class(['hidden' => old('intent') === 'register'])>
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-[10px] font-bold tracking-[.15em] text-marker">კეთილი იყოს დაბრუნება</p>
                                <p id="auth-title" class="mt-2 font-sign text-2xl font-bold tracking-[-0.04em]">შედი შენს სიებში</p>
                            </div>
                            <button type="button" onclick="document.getElementById('auth-dialog').close()" aria-label="დახურვა" class="grid size-10 shrink-0 place-items-center rounded-full bg-rule text-bought">
                                <svg viewBox="0 0 24 24" class="size-4" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m6 6 12 12M18 6 6 18"></path></svg>
                            </button>
                        </div>

                        <x-auth-session-status class="mt-4 text-center" :status="session('status')" />

                        <form method="POST" action="{{ route('login.store') }}" class="mt-6 space-y-4">
                            @csrf
                            <input type="hidden" name="intent" value="login">
                            <label class="block text-[11px] font-bold tracking-[.12em] text-bought" for="login-email">
                                ელფოსტა
                                <input id="login-email" name="email" type="email" value="{{ old('intent') === 'login' ? old('email') : '' }}" required autocomplete="email" placeholder="you@email.com" class="mt-2 h-[52px] w-full rounded-2xl border border-rule bg-white px-4 text-sm font-semibold normal-case tracking-normal text-ink">
                            </label>
                            @error('email')
                                @if (old('intent') === 'login')
                                    <p class="text-sm text-wine">{{ $message }}</p>
                                @endif
                            @enderror
                            <label class="block text-[11px] font-bold tracking-[.12em] text-bought" for="login-password">
                                <span class="flex items-center justify-between gap-3">
                                    პაროლი
                                    @if (Route::has('password.request'))
                                        <a href="{{ route('password.request') }}" class="text-[11px] font-semibold normal-case tracking-normal text-marker">დაგავიწყდა?</a>
                                    @endif
                                </span>
                                <input id="login-password" name="password" type="password" required autocomplete="current-password" placeholder="პაროლი" class="mt-2 h-[52px] w-full rounded-2xl border border-rule bg-white px-4 text-sm font-semibold normal-case tracking-normal text-ink">
                            </label>
                            @error('password')
                                @if (old('intent') === 'login')
                                    <p class="text-sm text-wine">{{ $message }}</p>
                                @endif
                            @enderror
                            <button type="submit" class="slip-press h-14 w-full rounded-2xl bg-marker text-sm font-bold text-white shadow-[0_10px_24px_rgba(123,92,246,.25)]">შესვლა</button>
                        </form>
                        @if (Route::has('register'))
                            <p class="mt-5 text-center text-xs font-medium text-bought">
                                ახალი ხარ?
                                <button type="button" onclick="openAuth('register')" class="font-bold text-marker">რეგისტრაცია</button>
                            </p>
                        @endif
                    </div>

                    @if (Route::has('register'))
                        <div id="auth-register" @class(['hidden' => old('intent') !== 'register'])>
                            <div class="flex items-start justify-between">
                                <div>
                                    <p class="text-[10px] font-bold tracking-[.15em] text-marker">შემოგვიერთდი</p>
                                    <p class="mt-2 font-sign text-2xl font-bold tracking-[-0.04em]">შექმენი ანგარიში</p>
                                </div>
                                <button type="button" onclick="document.getElementById('auth-dialog').close()" aria-label="დახურვა" class="grid size-10 shrink-0 place-items-center rounded-full bg-rule text-bought">
                                    <svg viewBox="0 0 24 24" class="size-4" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m6 6 12 12M18 6 6 18"></path></svg>
                                </button>
                            </div>

                            <form method="POST" action="{{ route('register.store') }}" class="mt-6 space-y-4">
                                @csrf
                                <input type="hidden" name="intent" value="register">
                                <label class="block text-[11px] font-bold tracking-[.12em] text-bought" for="register-name">
                                    სახელი
                                    <input id="register-name" name="name" type="text" value="{{ old('intent') === 'register' ? old('name') : '' }}" required autocomplete="name" placeholder="სახელი" class="mt-2 h-[52px] w-full rounded-2xl border border-rule bg-white px-4 text-sm font-semibold normal-case tracking-normal text-ink">
                                </label>
                                @error('name')
                                    <p class="text-sm text-wine">{{ $message }}</p>
                                @enderror
                                <label class="block text-[11px] font-bold tracking-[.12em] text-bought" for="register-email">
                                    ელფოსტა
                                    <input id="register-email" name="email" type="email" value="{{ old('intent') === 'register' ? old('email') : '' }}" required autocomplete="email" placeholder="you@email.com" class="mt-2 h-[52px] w-full rounded-2xl border border-rule bg-white px-4 text-sm font-semibold normal-case tracking-normal text-ink">
                                </label>
                                @error('email')
                                    @if (old('intent') === 'register')
                                        <p class="text-sm text-wine">{{ $message }}</p>
                                    @endif
                                @enderror
                                <label class="block text-[11px] font-bold tracking-[.12em] text-bought" for="register-password">
                                    პაროლი
                                    <input id="register-password" name="password" type="password" required autocomplete="new-password" placeholder="მინიმუმ 8 სიმბოლო" class="mt-2 h-[52px] w-full rounded-2xl border border-rule bg-white px-4 text-sm font-semibold normal-case tracking-normal text-ink">
                                </label>
                                @error('password')
                                    @if (old('intent') === 'register')
                                        <p class="text-sm text-wine">{{ $message }}</p>
                                    @endif
                                @enderror
                                <label class="block text-[11px] font-bold tracking-[.12em] text-bought" for="register-password-confirmation">
                                    გაიმეორე პაროლი
                                    <input id="register-password-confirmation" name="password_confirmation" type="password" required autocomplete="new-password" placeholder="გაიმეორე პაროლი" class="mt-2 h-[52px] w-full rounded-2xl border border-rule bg-white px-4 text-sm font-semibold normal-case tracking-normal text-ink">
                                </label>
                                <button type="submit" class="slip-press h-14 w-full rounded-2xl bg-marker text-sm font-bold text-white shadow-[0_10px_24px_rgba(123,92,246,.25)]">უფასო ანგარიშის შექმნა</button>
                            </form>
                            @if (Route::has('login'))
                                <p class="mt-5 text-center text-xs font-medium text-bought">
                                    უკვე გაქვს ანგარიში?
                                    <button type="button" onclick="openAuth('login')" class="font-bold text-marker">შესვლა</button>
                                </p>
                            @endif
                        </div>
                    @endif
                </div>
            </dialog>

            <style>
                .auth-sheet {
                    margin: 0;
                    width: 100%;
                    max-width: none;
                    height: 100%;
                    max-height: none;
                    border: 0;
                    padding: 12px;
                    background: transparent;
                }

                .auth-sheet[open] {
                    display: grid;
                    align-items: end;
                    justify-items: center;
                }

                .auth-sheet::backdrop {
                    background: rgba(28, 23, 48, 0.45);
                    backdrop-filter: blur(4px);
                }
            </style>

            <script>
                function openAuth(mode) {
                    const dialog = document.getElementById('auth-dialog');
                    const login = document.getElementById('auth-login');
                    const register = document.getElementById('auth-register');

                    login?.classList.toggle('hidden', mode !== 'login');
                    register?.classList.toggle('hidden', mode !== 'register');

                    if (!dialog.open) {
                        dialog.showModal();
                    }

                    document.getElementById(mode === 'login' ? 'login-email' : 'register-name')?.focus();
                }

                document.getElementById('auth-dialog')?.addEventListener('click', (event) => {
                    if (event.target.id === 'auth-dialog') {
                        event.target.close();
                    }
                });
            </script>

            @if (in_array(old('intent'), ['login', 'register'], true))
                <script>
                    openAuth(@json(old('intent')));
                </script>
            @endif
        @endguest
    </body>
</html>
