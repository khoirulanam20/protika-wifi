{{-- Mobile bottom sheet for filters. Must be inside the same GET form. --}}
<div x-show="filterOpen" x-cloak class="md:hidden fixed inset-0 z-[60]" style="display: none;">
    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-black/50"
        x-show="filterOpen"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="filterOpen = false">
    </div>

    {{-- Sheet --}}
    <div class="absolute bottom-0 left-0 right-0 bg-white rounded-t-2xl shadow-xl border-t border-border flex flex-col max-h-[85vh] pb-safe"
        x-show="filterOpen"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="transform translate-y-full"
        x-transition:enter-end="transform translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="transform translate-y-0"
        x-transition:leave-end="transform translate-y-full"
        @click.stop>

        {{-- Drag handle --}}
        <div class="flex justify-center pt-3 pb-1 flex-shrink-0">
            <div class="w-12 h-1.5 bg-gray-300 rounded-full"></div>
        </div>

        {{-- Header --}}
        <div class="flex items-center justify-between px-5 py-3 border-b border-border flex-shrink-0">
            <h3 class="text-base font-semibold text-content-primary">Filter</h3>
            <button type="button" @click="filterOpen = false"
                class="p-2 text-content-tertiary hover:bg-gray-100 rounded-full transition-colors"
                aria-label="Tutup filter">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- Scrollable fields --}}
        <div class="flex-1 overflow-y-auto px-5 py-4 space-y-4">
            {{ $slot }}
        </div>

        {{-- Sticky footer --}}
        <div class="flex-shrink-0 px-5 py-4 border-t border-border bg-white flex gap-3">
            <button type="submit" class="btn-primary flex-1 justify-center" @click="filterOpen = false">
                Terapkan
            </button>
            @if($showReset ?? false)
                <a href="{{ $resetUrl }}" class="btn-secondary flex-1 justify-center text-center">
                    Reset
                </a>
            @endif
        </div>
    </div>
</div>
