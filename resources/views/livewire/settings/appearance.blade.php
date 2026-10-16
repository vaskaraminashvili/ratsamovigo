<x-settings.shell title="სახე" subtitle="ფერის რეჟიმი">
    <div class="space-y-3" x-data>
        <button type="button" class="flex w-full items-center justify-between rounded-[28px] bg-white px-4 py-4 text-left" x-on:click="$flux.appearance = 'light'">
            <span>
                <span class="block font-medium">ღია</span>
                <span class="text-sm text-bought">ყოველთვის ნათელი</span>
            </span>
            <span class="flex size-6 shrink-0 items-center justify-center rounded-full border-2 text-white" :class="$flux.appearance === 'light' ? 'border-marker bg-marker' : 'border-[#d9d4e6]'">
                <svg x-show="$flux.appearance === 'light'" viewBox="0 0 16 16" class="size-3.5" fill="none" stroke="currentColor" stroke-width="2.4" aria-hidden="true"><path d="M3.5 8.2 6.4 11 12.5 4.8"></path></svg>
            </span>
        </button>

        <button type="button" class="flex w-full items-center justify-between rounded-[28px] bg-white px-4 py-4 text-left" x-on:click="$flux.appearance = 'dark'">
            <span>
                <span class="block font-medium">მუქი</span>
                <span class="text-sm text-bought">ყოველთვის მუქი</span>
            </span>
            <span class="flex size-6 shrink-0 items-center justify-center rounded-full border-2 text-white" :class="$flux.appearance === 'dark' ? 'border-marker bg-marker' : 'border-[#d9d4e6]'">
                <svg x-show="$flux.appearance === 'dark'" viewBox="0 0 16 16" class="size-3.5" fill="none" stroke="currentColor" stroke-width="2.4" aria-hidden="true"><path d="M3.5 8.2 6.4 11 12.5 4.8"></path></svg>
            </span>
        </button>

        <button type="button" class="flex w-full items-center justify-between rounded-[28px] bg-white px-4 py-4 text-left" x-on:click="$flux.appearance = 'system'">
            <span>
                <span class="block font-medium">სისტემა</span>
                <span class="text-sm text-bought">ტელეფონის პარამეტრი</span>
            </span>
            <span class="flex size-6 shrink-0 items-center justify-center rounded-full border-2 text-white" :class="$flux.appearance === 'system' ? 'border-marker bg-marker' : 'border-[#d9d4e6]'">
                <svg x-show="$flux.appearance === 'system'" viewBox="0 0 16 16" class="size-3.5" fill="none" stroke="currentColor" stroke-width="2.4" aria-hidden="true"><path d="M3.5 8.2 6.4 11 12.5 4.8"></path></svg>
            </span>
        </button>
    </div>
</x-settings.shell>
