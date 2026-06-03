<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    public function index(Request $request): View
    {
        $logs = ActivityLog::with('user:id,name,role')
            ->when($request->filled('event'), fn ($q) =>
                $q->forEvent($request->event)
            )
            ->when($request->filled('user_id'), fn ($q) =>
                $q->forUser((int) $request->user_id)
            )
            ->latest('created_at')
            ->paginate(50)
            ->withQueryString();

        $events = ActivityLog::select('event')
            ->distinct()
            ->orderBy('event')
            ->pluck('event');

        $users = User::orderBy('name')->get(['id', 'name', 'role']);

        return view('admin.logs.index', compact('logs', 'events', 'users'));
    }
}