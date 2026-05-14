@if(session('success'))
<div x-data="{ show: true }" x-show="show" x-transition
     x-init="setTimeout(() => show = false, 4000)"
     class="flex items-center justify-between gap-3 px-5 py-4 rounded-xl mb-5
            bg-emerald-500/15 border border-emerald-500/30">
    <div class="flex items-center gap-3">
        <svg class="w-5 h-5 text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <p class="text-emerald-300 text-sm font-medium">{{ session('success') }}</p>
    </div>
    <button @click="show = false" class="text-emerald-400/60 hover:text-emerald-400">✕</button>
</div>
@endif

@if(session('error'))
<div x-data="{ show: true }" x-show="show" x-transition
     x-init="setTimeout(() => show = false, 5000)"
     class="flex items-center justify-between gap-3 px-5 py-4 rounded-xl mb-5
            bg-red-500/15 border border-red-500/30">
    <div class="flex items-center gap-3">
        <svg class="w-5 h-5 text-red-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <p class="text-red-300 text-sm font-medium">{{ session('error') }}</p>
    </div>
    <button @click="show = false" class="text-red-400/60 hover:text-red-400">✕</button>
</div>
@endif
