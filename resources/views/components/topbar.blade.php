<header class="sticky top-0 z-20 border-b border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-950">
    <div class="mx-auto flex h-16 max-w-7xl items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
        <div>
            <p class="text-xs font-bold uppercase tracking-wide text-slate-400 dark:text-slate-400">{{ now()->format('l, M j') }}</p>
            <h1 class="text-lg font-extrabold text-slate-950 dark:text-white">{{ $title ?? 'POS System' }}</h1>
        </div>
        <div class="flex items-center gap-3">
            @auth
                <div class="relative" data-notifications>
                    <button type="button" data-notification-toggle
                        class="relative rounded-md border border-slate-200 px-3 py-2 text-sm font-black text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">
                        Alerts
                        <span data-notification-count class="absolute -right-2 -top-2 hidden min-w-5 rounded-full bg-red-500 px-1.5 py-0.5 text-center text-[10px] font-black text-white">0</span>
                    </button>
                    <div data-notification-panel class="absolute right-0 mt-2 hidden w-80 overflow-hidden rounded-lg border border-slate-200 bg-white shadow-xl dark:border-slate-700 dark:bg-slate-900">
                        <div class="border-b border-slate-200 px-4 py-3 dark:border-slate-700">
                            <p class="text-sm font-black text-slate-950 dark:text-white">Live Notifications</p>
                            <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">Recent sales, products, and system activity</p>
                        </div>
                        <div data-notification-list class="max-h-80 overflow-y-auto p-2 text-sm">
                            <p class="p-3 text-sm font-semibold text-slate-500 dark:text-slate-400">Loading notifications...</p>
                        </div>
                    </div>
                </div>

                {{-- Theme toggle: label updates instantly via JS --}}
                <button type="button" data-theme-toggle
                    class="rounded-md border border-slate-200 px-3 py-2 text-sm font-black text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">
                    <span data-theme-label>Theme</span>
                </button>
            @endauth

            @auth
                @if (auth()->user()->role === 'cashier')
                    <a href="{{ route('cashier.dashboard') }}" class="rounded-md bg-teal-500 px-4 py-2 text-sm font-bold text-white shadow-sm shadow-teal-500/20 hover:bg-teal-600">New Sale</a>
                @else
                    <a href="{{ route('admin.users.create') }}" class="rounded-md bg-teal-500 px-4 py-2 text-sm font-bold text-white shadow-sm shadow-teal-500/20 hover:bg-teal-600">Add User</a>
                @endif
            @endauth
            <div class="hidden text-right sm:block">
                <p class="text-sm font-bold text-slate-900 dark:text-white">{{ auth()->user()->name ?? 'Demo User' }}</p>
                <p class="text-xs font-medium text-slate-500 dark:text-slate-400">{{ ucfirst(auth()->user()->role ?? 'admin') }}</p>
            </div>
        </div>
    </div>
</header>