<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | POS System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
</head>
<body class="min-h-screen bg-slate-950 font-sans text-slate-900">
    <main class="grid min-h-screen lg:grid-cols-[1.05fr_0.95fr]">
        <section class="hidden min-h-screen flex-col justify-between bg-slate-950 px-12 py-10 text-white lg:flex">
            <img src="{{ asset('assets/images/brand/pointsale-logo.svg') }}" alt="Cafe POS" class="h-16 w-auto rounded-lg">

            <div class="max-w-xl">
                <p class="mb-4 inline-flex rounded-full border border-teal-300/30 px-3 py-1 text-xs font-black uppercase tracking-wide text-teal-200">Fast checkout. Clean admin.</p>
                <h1 class="text-5xl font-black leading-tight">Run sales, users, inventory, and reports from one calm dashboard.</h1>
                <div class="mt-8 grid grid-cols-3 gap-4">
                    <div class="rounded-lg border border-white/10 bg-white/5 p-4"><p class="text-3xl font-black text-teal-300">8s</p><p class="mt-1 text-sm font-semibold text-slate-300">Average checkout</p></div>
                    <div class="rounded-lg border border-white/10 bg-white/5 p-4"><p class="text-3xl font-black text-teal-300">24/7</p><p class="mt-1 text-sm font-semibold text-slate-300">Sales tracking</p></div>
                    <div class="rounded-lg border border-white/10 bg-white/5 p-4"><p class="text-3xl font-black text-teal-300">2</p><p class="mt-1 text-sm font-semibold text-slate-300">Role portals</p></div>
                </div>
            </div>

            <p class="text-sm font-semibold text-slate-400">Admin and cashier access is stored securely in your Laravel users table.</p>
        </section>

        <section class="grid min-h-screen place-items-center bg-slate-100 px-4 py-10">
            <div class="w-full max-w-md rounded-lg border border-slate-200 bg-white p-8 shadow-xl shadow-slate-200/80">
                <div class="mb-8">
                    <img src="{{ asset('assets/images/brand/pointsale-logo.svg') }}" alt="Cafe POS" class="mb-4 h-14 w-auto rounded-lg lg:hidden">
                    <p class="text-sm font-black uppercase tracking-wide text-teal-600">Welcome back</p>
                    <h1 class="mt-2 text-3xl font-black text-slate-950">Sign in to Cafe POS</h1>
                    <p class="mt-2 text-sm font-medium text-slate-500">Use your cashier or admin account.</p>
                </div>

            @if (session('error'))
                <div class="mb-5 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-800">{{ session('error') }}</div>
            @endif
            @if (session('success'))
                <div class="mb-5 rounded-md border border-teal-200 bg-teal-50 px-4 py-3 text-sm font-semibold text-teal-800">{{ session('success') }}</div>
            @endif
            @if ($errors->any())
                <div class="mb-5 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-800">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('login.store') }}" class="space-y-5">
                @csrf
                <label class="block">
                    <span class="text-sm font-bold text-slate-700">Email</span>
                    <input name="email" type="email" value="{{ old('email') }}" required class="mt-2 w-full rounded-md border border-slate-300 px-4 py-3 text-sm outline-none focus:border-teal-500 focus:ring-4 focus:ring-teal-500/10">
                </label>
                <label class="block">
                    <span class="text-sm font-bold text-slate-700">Password</span>
                    <input name="password" type="password" required class="mt-2 w-full rounded-md border border-slate-300 px-4 py-3 text-sm outline-none focus:border-teal-500 focus:ring-4 focus:ring-teal-500/10">
                </label>
                <label class="flex items-center gap-2 text-sm font-semibold text-slate-600">
                    <input name="remember" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-teal-500">
                    Remember me
                </label>
                <button class="w-full rounded-md bg-teal-500 px-4 py-3 text-sm font-black text-white shadow-lg shadow-teal-500/20 hover:bg-teal-600">Login</button>
            </form>
            </div>
        </section>
    </main>
</body>
</html>
