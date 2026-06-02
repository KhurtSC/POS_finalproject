@if (session('success') || session('error'))
    <div data-alert class="mb-5 rounded-md border px-4 py-3 text-sm font-semibold {{ session('success') ? 'border-teal-200 bg-teal-50 text-teal-800' : 'border-red-200 bg-red-50 text-red-800' }}">
        {{ session('success') ?? session('error') }}
    </div>
@endif
