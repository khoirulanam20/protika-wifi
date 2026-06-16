@props([
    'activeCount' => 0,
])

<button type="button"
    @click="filterOpen = true"
    class="relative flex-shrink-0 flex items-center justify-center w-11 h-11 rounded-xl border border-border bg-white text-content-secondary hover:text-primary hover:border-primary/30 hover:bg-primary/5 transition-colors"
    aria-label="Buka filter">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
    </svg>
    @if($activeCount > 0)
        <span class="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 flex items-center justify-center rounded-full bg-primary text-white text-[10px] font-bold leading-none">
            {{ $activeCount > 9 ? '9+' : $activeCount }}
        </span>
    @endif
</button>
