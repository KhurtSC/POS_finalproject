<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register | POS System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
</head>
<body class="min-h-screen bg-slate-100 font-sans text-slate-900">
    <main class="grid min-h-screen place-items-center px-4 py-10">
        <section class="w-full max-w-2xl rounded-lg border border-slate-200 bg-white p-8 shadow-xl shadow-slate-200/80">
            <div class="mb-8 flex items-start justify-between gap-4">
                <div>
                    <p class="text-sm font-black uppercase tracking-wide text-teal-600">Create access</p>
                    <h1 class="mt-2 text-3xl font-black text-slate-950">Register a POS User</h1>
                    <p class="mt-2 text-sm font-medium text-slate-500">Choose Admin for management access or Cashier for checkout access.</p>
                </div>
                <img src="{{ asset('assets/images/brand/pointsale-logo.svg') }}" alt="Cafe POS" class="h-14 w-auto shrink-0 rounded-lg">
            </div>

            @if ($errors->any())
                <div class="mb-5 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-800">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('register.store') }}" class="space-y-5">
                @csrf
                <div class="grid gap-5 sm:grid-cols-2">
                    <label class="block">
                        <span class="text-sm font-bold text-slate-700">Name</span>
                        <input name="name" value="{{ old('name') }}" required class="mt-2 w-full rounded-md border border-slate-300 px-4 py-3 text-sm outline-none focus:border-teal-500 focus:ring-4 focus:ring-teal-500/10">
                    </label>
                    <label class="block">
                        <span class="text-sm font-bold text-slate-700">Email</span>
                        <input name="email" type="email" value="{{ old('email') }}" required class="mt-2 w-full rounded-md border border-slate-300 px-4 py-3 text-sm outline-none focus:border-teal-500 focus:ring-4 focus:ring-teal-500/10">
                    </label>
                    <label class="block">
                        <span class="text-sm font-bold text-slate-700">Password</span>
                        <input name="password" type="password" required class="mt-2 w-full rounded-md border border-slate-300 px-4 py-3 text-sm outline-none focus:border-teal-500 focus:ring-4 focus:ring-teal-500/10">
                    </label>
                    <label class="block">
                        <span class="text-sm font-bold text-slate-700">Confirm Password</span>
                        <input name="password_confirmation" type="password" required class="mt-2 w-full rounded-md border border-slate-300 px-4 py-3 text-sm outline-none focus:border-teal-500 focus:ring-4 focus:ring-teal-500/10">
                    </label>
                </div>
                <label class="block">
                    <span class="text-sm font-bold text-slate-700">Role</span>
                    <select name="role" required class="mt-2 w-full rounded-md border border-slate-300 px-4 py-3 text-sm outline-none focus:border-teal-500 focus:ring-4 focus:ring-teal-500/10">
                        <option value="admin" @selected(old('role') === 'admin')>Admin</option>
                        <option value="cashier" @selected(old('role') === 'cashier')>Cashier</option>
                    </select>
                </label>
                <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-between">
                    <a href="{{ route('login') }}" class="rounded-md border border-slate-300 px-4 py-3 text-center text-sm font-black text-slate-700 hover:bg-slate-50">Back to Login</a>
                    <button class="rounded-md bg-teal-500 px-5 py-3 text-sm font-black text-white shadow-lg shadow-teal-500/20 hover:bg-teal-600">Create Account</button>
                </div>
            </form>
        </section>
    </main>
</body>
</html>
