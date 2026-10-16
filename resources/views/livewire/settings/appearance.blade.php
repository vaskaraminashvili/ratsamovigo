<x-settings.shell title="სახე" subtitle="ფერი და შრიფტი">
    <div class="space-y-6">
        <section class="space-y-3">
            <h2 class="px-1 text-sm text-bought">ფერის რეჟიმი</h2>

            <button type="button" class="flex w-full items-center justify-between rounded-[28px] bg-white px-4 py-4 text-left" x-on:click="mode = 'light'">
                <span>
                    <span class="block font-medium">ღია</span>
                    <span class="text-sm text-bought">ყოველთვის ნათელი</span>
                </span>
                <span class="flex size-6 shrink-0 items-center justify-center rounded-full border-2 text-white" :class="mode === 'light' ? 'border-marker bg-marker' : 'border-[#d9d4e6]'">
                    <svg x-show="mode === 'light'" viewBox="0 0 16 16" class="size-3.5" fill="none" stroke="currentColor" stroke-width="2.4" aria-hidden="true"><path d="M3.5 8.2 6.4 11 12.5 4.8"></path></svg>
                </span>
            </button>

            <button type="button" class="flex w-full items-center justify-between rounded-[28px] bg-white px-4 py-4 text-left" x-on:click="mode = 'dark'">
                <span>
                    <span class="block font-medium">მუქი</span>
                    <span class="text-sm text-bought">ყოველთვის მუქი</span>
                </span>
                <span class="flex size-6 shrink-0 items-center justify-center rounded-full border-2 text-white" :class="mode === 'dark' ? 'border-marker bg-marker' : 'border-[#d9d4e6]'">
                    <svg x-show="mode === 'dark'" viewBox="0 0 16 16" class="size-3.5" fill="none" stroke="currentColor" stroke-width="2.4" aria-hidden="true"><path d="M3.5 8.2 6.4 11 12.5 4.8"></path></svg>
                </span>
            </button>

            <button type="button" class="flex w-full items-center justify-between rounded-[28px] bg-white px-4 py-4 text-left" x-on:click="mode = 'system'">
                <span>
                    <span class="block font-medium">სისტემა</span>
                    <span class="text-sm text-bought">ტელეფონის პარამეტრი</span>
                </span>
                <span class="flex size-6 shrink-0 items-center justify-center rounded-full border-2 text-white" :class="mode === 'system' ? 'border-marker bg-marker' : 'border-[#d9d4e6]'">
                    <svg x-show="mode === 'system'" viewBox="0 0 16 16" class="size-3.5" fill="none" stroke="currentColor" stroke-width="2.4" aria-hidden="true"><path d="M3.5 8.2 6.4 11 12.5 4.8"></path></svg>
                </span>
            </button>
        </section>

        <section
            class="space-y-3"
            x-data="{
                font: localStorage.getItem('slip-text-font') === 'mzeqala' ? 'mzeqala' : 'alk',
                choose(font) {
                    this.font = font;

                    if (font === 'mzeqala') {
                        localStorage.setItem('slip-text-font', 'mzeqala');
                        document.documentElement.dataset.textFont = 'mzeqala';
                        return;
                    }

                    localStorage.setItem('slip-text-font', 'alk');
                    delete document.documentElement.dataset.textFont;
                },
            }"
        >
            <h2 class="px-1 text-sm text-bought">ძირითადი ტექსტი</h2>

            <button type="button" class="flex w-full items-center justify-between rounded-[28px] bg-white px-4 py-4 text-left" x-on:click="choose('alk')">
                <span class="font-alk">
                    <span class="block text-base">ALK Life</span>
                    <span class="text-sm text-bought">რძე, პური და კვერცხი</span>
                </span>
                <span class="flex size-6 shrink-0 items-center justify-center rounded-full border-2 text-white" :class="font === 'alk' ? 'border-marker bg-marker' : 'border-[#d9d4e6]'">
                    <svg x-show="font === 'alk'" viewBox="0 0 16 16" class="size-3.5" fill="none" stroke="currentColor" stroke-width="2.4" aria-hidden="true"><path d="M3.5 8.2 6.4 11 12.5 4.8"></path></svg>
                </span>
            </button>

            <button type="button" class="flex w-full items-center justify-between rounded-[28px] bg-white px-4 py-4 text-left" x-on:click="choose('mzeqala')">
                <span class="font-mzeqala">
                    <span class="block text-base">მზექალა</span>
                    <span class="text-sm text-bought">რძე, პური და კვერცხი</span>
                </span>
                <span class="flex size-6 shrink-0 items-center justify-center rounded-full border-2 text-white" :class="font === 'mzeqala' ? 'border-marker bg-marker' : 'border-[#d9d4e6]'">
                    <svg x-show="font === 'mzeqala'" viewBox="0 0 16 16" class="size-3.5" fill="none" stroke="currentColor" stroke-width="2.4" aria-hidden="true"><path d="M3.5 8.2 6.4 11 12.5 4.8"></path></svg>
                </span>
            </button>
        </section>
    </div>
</x-settings.shell>
