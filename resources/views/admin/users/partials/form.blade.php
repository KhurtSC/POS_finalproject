<form method="POST" action="{{ $action }}" class="mx-auto max-w-3xl rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
    @csrf
    @if ($method === 'PUT') @method('PUT') @endif
    <div class="grid gap-5 sm:grid-cols-2">
        <label class="block"><span class="text-sm font-bold">Name</span><input name="name" value="{{ old('name', $user->name ?? '') }}" class="mt-2 w-full rounded-md border border-slate-300 px-3 py-2"></label>
        <label class="block"><span class="text-sm font-bold">Email</span><input name="email" type="email" value="{{ old('email', $user->email ?? '') }}" class="mt-2 w-full rounded-md border border-slate-300 px-3 py-2"></label>
        <label class="block"><span class="text-sm font-bold">Password</span><input name="password" type="password" class="mt-2 w-full rounded-md border border-slate-300 px-3 py-2"></label>
        <label class="block"><span class="text-sm font-bold">Confirm Password</span><input name="password_confirmation" type="password" class="mt-2 w-full rounded-md border border-slate-300 px-3 py-2"></label>
        <label class="block sm:col-span-2"><span class="text-sm font-bold">Role</span><select name="role" class="mt-2 w-full rounded-md border border-slate-300 px-3 py-2"><option value="admin" @selected(old('role', $user->role ?? '') === 'admin')>Admin</option><option value="cashier" @selected(old('role', $user->role ?? '') === 'cashier')>Cashier</option></select></label>
    </div>
    <div class="mt-6 flex justify-end gap-3"><a href="{{ route('admin.users.index') }}" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-bold">Cancel</a><button class="rounded-md bg-teal-500 px-4 py-2 text-sm font-bold text-white">Save User</button></div>
</form>
