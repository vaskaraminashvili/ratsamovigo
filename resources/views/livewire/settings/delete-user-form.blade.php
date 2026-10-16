<div x-data="{ open: {{ $errors->has('password') ? 'true' : 'false' }} }">
    <section class="mt-4 rounded-[28px] bg-white p-4">
        <h2 class="font-sign text-xl font-bold">ანგარიშის წაშლა</h2>
        <p class="mt-1 text-sm leading-relaxed text-bought">ანგარიში და მასთან დაკავშირებული მონაცემები სამუდამოდ წაიშლება.</p>
        <button type="button" class="mt-4 text-sm text-wine" x-on:click="open = true">ანგარიშის წაშლა</button>
    </section>

    <div x-show="open" x-cloak class="slip-scrim fixed inset-0 z-30 bg-ink/45" x-on:click="open = false"></div>
    <div x-show="open" x-cloak class="slip-up fixed inset-x-0 bottom-0 z-40 mx-auto max-h-[92dvh] w-full max-w-[430px] overflow-y-auto rounded-t-[28px] bg-white px-5 py-5 text-ink">
        <form wire:submit="deleteUser" class="space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="font-sign text-xl font-bold">ანგარიში წაიშალოს?</h2>
                <button type="button" class="font-medium text-marker" x-on:click="open = false">დახურვა</button>
            </div>
            <p class="text-sm leading-relaxed text-bought">წასაშლელად ჩაწერე პაროლი. ამის შემდეგ ანგარიშზე დაბრუნება ვეღარ მოხერხდება.</p>

            <div>
                <label for="delete-password" class="text-sm font-medium text-marker">პაროლი</label>
                <input id="delete-password" wire:model="password" type="password" autocomplete="current-password" class="mt-2 h-12 w-full rounded-2xl bg-phone px-4 text-base text-ink outline-none ring-1 ring-transparent focus:ring-2 focus:ring-marker">
                @error('password')
                    <p class="mt-2 text-sm text-wine">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-2 gap-2">
                <button type="button" class="h-12 rounded-full bg-phone text-sm" x-on:click="open = false">გაუქმება</button>
                <button type="submit" class="slip-press h-12 rounded-full bg-wine text-sm font-medium text-white">წაშლა</button>
            </div>
        </form>
    </div>
</div>
