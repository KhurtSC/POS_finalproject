<aside class="sticky top-0 z-30 hidden h-screen w-72 shrink-0 bg-slate-950 text-white shadow-2xl shadow-slate-950/20 lg:flex lg:flex-col">
    <div class="flex h-24 items-center px-5">
        <img src="{{ asset('assets/images/brand/pointsale-logo.svg') }}" alt="Cafe POS" class="h-14 w-auto rounded-lg">
    </div>

    <nav class="flex-1 space-y-8 overflow-y-auto px-4 pb-6">
        @if ((auth()->user()->role ?? 'admin') === 'admin')
            <div>
                <p class="px-3 pb-2 text-xs font-bold uppercase tracking-wide text-slate-500">Admin</p>
                <div class="space-y-1">
                    <x-nav-link href="{{ route('admin.dashboard') }}"        :active="request()->routeIs('admin.dashboard')"    icon="D">Dashboard</x-nav-link>
                    <x-nav-link href="{{ route('admin.products.index') }}"   :active="request()->routeIs('admin.products.*')"   icon="P">Products</x-nav-link>
                    <x-nav-link href="{{ route('admin.categories.index') }}" :active="request()->routeIs('admin.categories.*')" icon="C">Categories</x-nav-link>
                    <x-nav-link href="{{ route('admin.users.index') }}"      :active="request()->routeIs('admin.users.*')"      icon="U">Users</x-nav-link>
                    <x-nav-link href="{{ route('admin.sales.index') }}"      :active="request()->routeIs('admin.sales.*')"      icon="S">Sales</x-nav-link>
                    <x-nav-link href="{{ route('admin.reports.index') }}"    :active="request()->routeIs('admin.reports.*')"    icon="R">Reports</x-nav-link>
                    <x-nav-link href="{{ route('admin.logs.index') }}"       :active="request()->routeIs('admin.logs.*')"       icon="L">Activity Logs</x-nav-link>
                </div>
            </div>
        @endif

        @if ((auth()->user()->role ?? null) === 'cashier')
            <div>
                <p class="px-3 pb-2 text-xs font-bold uppercase tracking-wide text-slate-500">Cashier</p>
                <div class="space-y-1">
                    <x-nav-link href="{{ route('cashier.dashboard') }}" :active="request()->routeIs('cashier.dashboard')" icon="+">POS / New Sale</x-nav-link>
                    <x-nav-link href="{{ route('cashier.cart') }}"      :active="request()->routeIs('cashier.cart')"      icon="C">Cart</x-nav-link>
                </div>
            </div>
        @endif
    </nav>

    <div class="border-t border-white/10 p-4">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="w-full rounded-md bg-white/10 px-4 py-2 text-left text-sm font-semibold text-slate-200 transition hover:bg-red-500 hover:text-white">Logout</button>
        </form>
    </div>
</aside>