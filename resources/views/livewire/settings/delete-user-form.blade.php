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

            <x-password id="delete-password" wire:model="password" label="პაროლი" :rules="[]" autocomplete="current-password" />

            <div class="grid grid-cols-2 gap-2">
                <button type="button" class="h-12 rounded-full bg-phone text-sm" x-on:click="open = false">გაუქმება</button>
                <x-button submit lg round color="rose">წაშლა</x-button>
            </div>
        </form>
    </div>
</div>
