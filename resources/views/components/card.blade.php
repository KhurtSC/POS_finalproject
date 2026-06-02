@props(['label', 'value', 'tone' => 'teal'])

<div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
    <p class="text-sm font-semibold text-slate-500">{{ $label }}</p>
    <div class="mt-3 flex items-end justify-between gap-4">
        <p class="text-3xl font-black text-slate-950">{{ $value }}</p>
        <span class="h-3 w-12 rounded-full bg-{{ $tone }}-400"></span>
    </div>
</div>
