<x-layout title="Users">
    <div class="mb-5 flex justify-end"><a href="{{ route('admin.users.create') }}" class="rounded-md bg-teal-500 px-4 py-2 text-sm font-bold text-white">Add User</a></div>
    <section class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-left font-bold text-slate-500"><tr><th class="px-5 py-3">Name</th><th class="px-5 py-3">Email</th><th class="px-5 py-3">Role</th><th class="px-5 py-3">Created At</th><th class="px-5 py-3">Actions</th></tr></thead>
            <tbody class="divide-y divide-slate-100">
                @forelse (($users ?? []) as $user)
                    <tr>
                        <td class="px-5 py-4 font-bold">{{ $user->name }}</td>
                        <td class="px-5 py-4">{{ $user->email }}</td>
                        <td class="px-5 py-4"><span class="rounded-full bg-slate-100 px-2 py-1 text-xs font-bold text-slate-700">{{ ucfirst($user->role) }}</span></td>
                        <td class="px-5 py-4">{{ $user->created_at->format('M j, Y') }}</td>
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <a class="font-bold text-teal-600" href="{{ route('admin.users.edit', $user) }}">Edit</a>
                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button data-confirm="Delete this user?" class="font-bold text-red-600">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td class="px-5 py-4 font-bold">Admin User</td><td class="px-5 py-4">admin@example.com</td><td class="px-5 py-4">Admin</td><td class="px-5 py-4">{{ now()->format('M j, Y') }}</td><td class="px-5 py-4"><a class="font-bold text-teal-600" href="{{ route('admin.users.edit', 1) }}">Edit</a></td></tr>
                    <tr><td class="px-5 py-4 font-bold">Cashier User</td><td class="px-5 py-4">cashier@example.com</td><td class="px-5 py-4">Cashier</td><td class="px-5 py-4">{{ now()->format('M j, Y') }}</td><td class="px-5 py-4"><a class="font-bold text-teal-600" href="{{ route('admin.users.edit', 1) }}">Edit</a></td></tr>
                @endforelse
            </tbody>
        </table>
        @if (isset($users) && method_exists($users, 'links'))
            <div class="border-t border-slate-200 px-5 py-4">{{ $users->links() }}</div>
        @endif
    </section>
</x-layout>
