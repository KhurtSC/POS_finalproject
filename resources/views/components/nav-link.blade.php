@props(['href' => '#', 'active' => false, 'icon' => null])

<a href="{{ $href }}"
   {{ $attributes->merge(['class' => ($active ? 'bg-teal-500/15 text-white ring-1 ring-teal-400/30' : 'text-slate-300 hover:bg-white/10 hover:text-white') . ' group flex items-center gap-3 rounded-md px-3 py-2 text-sm font-semibold transition']) }}>
    @if ($icon)
        <span class="grid h-5 w-5 place-items-center text-base">{{ $icon }}</span>
    @endif
    <span>{{ $slot }}</span>
</a>
