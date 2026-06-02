@props(['id', 'title' => 'Modal'])

<div id="{{ $id }}" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/60 p-4" data-modal>
    <div class="w-full max-w-lg rounded-lg bg-white shadow-2xl">
        <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
            <h2 class="text-lg font-extrabold text-slate-950">{{ $title }}</h2>
            <button type="button" class="rounded-md p-2 text-slate-500 hover:bg-slate-100" data-close-modal>&times;</button>
        </div>
        <div class="p-5">{{ $slot }}</div>
    </div>
</div>
