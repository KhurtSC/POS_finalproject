<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | POS System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
</head>
<body class="min-h-screen bg-slate-100 font-sans text-slate-900">
    <main class="grid min-h-screen place-items-center px-4 py-10">
        <div class="w-full max-w-md rounded-lg border border-slate-200 bg-white p-8 shadow-xl shadow-slate-200/80">

            <div class="mb-8">
                <img src="{{ asset('assets/images/brand/pointsale-logo.svg') }}" alt="Cafe POS" class="mb-4 h-14 w-auto rounded-lg">
                <p class="text-sm font-black uppercase tracking-wide text-teal-600">Welcome back</p>
                <h1 class="mt-2 text-3xl font-black text-slate-950">Sign in to Cafe POS</h1>
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
    </main>
</body>
</html>