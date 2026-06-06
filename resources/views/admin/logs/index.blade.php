<x-layout title="Activity Logs">

    {{-- Filters --}}
    <form method="GET" action="{{ route('admin.logs.index') }}"
          class="mb-5 grid gap-3 rounded-lg border border-slate-200 bg-white p-4 shadow-sm
                 md:grid-cols-[1fr_1fr_auto] dark:border-slate-700 dark:bg-slate-900">

        <select name="event" class="rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100">
            <option value="">All events</option>
            @foreach ($events as $event)
                <option value="{{ $event }}" @selected(request('event') === $event)>
                    {{ $event }}
                </option>
            @endforeach
        
        </select>

        <select name="user_id" class="rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100">
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
                   class="rounded-md border border-slate-300 px-4 py-2 text-sm font-bold hover:bg-slate-50 dark:border-slate-700 dark:hover:bg-slate-800 dark:text-slate-100">
                    Clear
                </a>
            @endif
        </div>
    </form>

    <section class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-950">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm dark:divide-slate-700">
                <thead class="bg-slate-50 text-left font-bold text-slate-500 dark:bg-slate-800 dark:text-slate-400">
                    <tr>
                        <th class="px-5 py-3">Event</th>
                        <th class="px-5 py-3">User</th>
                        <th class="px-5 py-3">Description</th>
                        <th class="px-5 py-3">Time</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($logs as $log)
                        <tr class="dark:hover:bg-slate-900">
                            <td class="px-5 py-3 dark:text-slate-200">
                                @php
                                    $eventText = strtolower($log->event);
                                    $color = match(true) {
                                        str_contains($eventText, 'login')   => 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-100',
                                        str_contains($eventText, 'sale')    => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-700 dark:text-emerald-100',
                                        str_contains($eventText, 'deleted') => 'bg-red-50 text-red-700 dark:bg-red-700 dark:text-red-100',
                                        str_contains($eventText, 'voided')  => 'bg-red-50 text-red-700 dark:bg-red-700 dark:text-red-100',
                                        default                              => 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-100',
                                    };
                                @endphp
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-bold {{ $color }} dark:text-slate-100">
                                    {{ $log->event }}
                                </span>
                            </td>
                            <td class="px-5 py-3 dark:text-slate-200">
                                @if ($log->user)
                                    <span class="font-medium dark:text-slate-100">{{ $log->user->name }}</span>
                                    <span class="ml-1 text-xs text-slate-400 dark:text-slate-400">({{ $log->user->role }})</span>
                                @else
                                    <span class="text-slate-400 dark:text-slate-400">—</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-slate-700 dark:text-slate-200">{{ $log->description }}</td>
                            <td class="px-5 py-3 text-slate-500 dark:text-slate-400">
                                {{ $log->created_at->format('M j, Y g:i A') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-8 text-center text-slate-400 dark:text-slate-400">
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