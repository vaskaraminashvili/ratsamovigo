<div class="mx-auto flex min-h-dvh w-full max-w-[430px] items-center px-5 py-10">
    <div class="slip-rise w-full rounded-[28px] bg-white px-6 py-8">
        <p class="text-sm font-medium text-marker">რედაქტირების მოწვევა</p>
        <h1 class="mt-2 font-sign text-3xl font-bold leading-tight">{{ $listName }}</h1>
        <p class="mt-3 text-sm leading-relaxed text-bought">ამ სიის შესაცვლელად საჭიროა ანგარიში. რეგისტრაციის ან შესვლის შემდეგ სიას ერთად შეავსებთ. ახალ ანგარიშს ელფოსტის დადასტურებაც სჭირდება.</p>

        @if ($error !== '')
            <p class="mt-4 text-sm text-wine">{{ $error }}</p>
        @endif

        @guest
            <a href="{{ route('register') }}" class="slip-press mt-6 flex h-12 items-center justify-center rounded-full bg-ink font-medium text-white">რეგისტრაცია</a>
            <a href="{{ route('login') }}" class="mt-3 flex h-12 items-center justify-center rounded-full bg-phone font-medium">შესვლა</a>
        @endguest

        @auth
            @if ($error === '')
                <p class="mt-6 text-sm text-bought">სიას ვუერთდებით...</p>
            @else
                <a href="{{ route('lists.index') }}" class="slip-press mt-6 flex h-12 items-center justify-center rounded-full bg-ink font-medium text-white">ჩემს სიებზე</a>
            @endif
        @endauth
    </div>
</div>
