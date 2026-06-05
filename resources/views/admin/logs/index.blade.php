<x-layout title="Activity Logs">

    {{-- Filters --}}
    <form method="GET" action="{{ route('admin.logs.index') }}"
          class="mb-5 grid gap-3 rounded-lg border border-slate-200 bg-white p-4 shadow-sm
                 md:grid-cols-[1fr_1fr_auto]">

        <select name="event" class="rounded-md border border-slate-300 px-3 py-2 text-sm">
            <option value="">All events</option>
            @foreach ($events as $event)
                <option value="{{ $event }}" @selected(request('event') === $event)>
                    {{ $event }}
                </option>
            @endforeach
        </select>

        <select name="user_id" class="rounded-md border border-slate-300 px-3 py-2 text-sm">
            <option value="">All users</option>
            @foreach ($users as $user)
                <option value="{{ $user->id }}" @selected(request('user_id') == $user->id)>
                    {{ $user->name }} ({{ $user->role }})
                </option>
            @endforeach
        </select>

        <div class="flex gap-2">
            <button class="flex-1 rounded-md bg-slate-950 px-4 py-2 text-sm font-bold text-white hover:bg-slate-800">
                Filter
            </button>
            @if (request()->hasAny(['event', 'user_id']))
                <a href="{{ route('admin.logs.index') }}"
                   class="rounded-md border border-slate-300 px-4 py-2 text-sm font-bold hover:bg-slate-50">
                    Clear
                </a>
            @endif
        </div>
    </form>

    <section class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left font-bold text-slate-500">
                    <tr>
                        <th class="px-5 py-3">Event</th>
                        <th class="px-5 py-3">User</th>
                        <th class="px-5 py-3">Description</th>
                        <th class="px-5 py-3">Time</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($logs as $log)
                        <tr>
                            <td class="px-5 py-3">
                                @php
                                    $color = match(true) {
                                        str_contains($log->event, 'login')   => 'bg-sky-50 text-sky-700',
                                        str_contains($log->event, 'sale')    => 'bg-emerald-50 text-emerald-700',
                                        str_contains($log->event, 'deleted') => 'bg-red-50 text-red-700',
                                        str_contains($log->event, 'voided')  => 'bg-red-50 text-red-700',
                                        default                              => 'bg-slate-100 text-slate-600',
                                    };
                                @endphp
                                <span class="rounded-full px-2 py-0.5 text-xs font-bold {{ $color }}">
                                    {{ $log->event }}
                                </span>
                            </td>
                            <td class="px-5 py-3">
                                @if ($log->user)
                                    <span class="font-medium">{{ $log->user->name }}</span>
                                    <span class="ml-1 text-xs text-slate-400">({{ $log->user->role }})</span>
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-slate-700">{{ $log->description }}</td>
                            <td class="px-5 py-3 text-slate-500">
                                {{ $log->created_at->format('M j, Y g:i A') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-8 text-center text-slate-400">
                                No activity logs found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-slate-200 px-5 py-4">
            {{ $logs->links() }}
        </div>
    </section>

</x-layout>