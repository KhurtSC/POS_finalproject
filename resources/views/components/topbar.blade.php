<header class="sticky top-0 z-20 border-b border-slate-200 bg-white/95 backdrop-blur">
    <div class="mx-auto flex h-16 max-w-7xl items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
        <div>
            <p class="text-xs font-bold uppercase tracking-wide text-slate-400">{{ now()->format('l, M j') }}</p>
            <h1 class="text-lg font-extrabold text-slate-950">{{ $title ?? 'POS System' }}</h1>
        </div>
        <div class="flex items-center gap-3">
            @auth
                @if (auth()->user()->role === 'cashier')
                    <a href="{{ route('cashier.dashboard') }}" class="rounded-md bg-teal-500 px-4 py-2 text-sm font-bold text-white shadow-sm shadow-teal-500/20 hover:bg-teal-600">New Sale</a>
                @else
                    <a href="{{ route('admin.users.create') }}" class="rounded-md bg-teal-500 px-4 py-2 text-sm font-bold text-white shadow-sm shadow-teal-500/20 hover:bg-teal-600">Add User</a>
                @endif
            @endauth
            <div class="hidden text-right sm:block">
                <p class="text-sm font-bold text-slate-900">{{ auth()->user()->name ?? 'Demo User' }}</p>
                <p class="text-xs font-medium text-slate-500">{{ ucfirst(auth()->user()->role ?? 'admin') }}</p>
            </div>
        </div>
    </div>
</header>
