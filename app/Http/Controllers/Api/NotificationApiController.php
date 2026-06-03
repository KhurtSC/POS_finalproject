<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $afterId = (int) $request->query('after_id', 0);

        $logs = ActivityLog::with('user:id,name,role')
            ->when($afterId > 0, fn ($query) => $query->where('id', '>', $afterId))
            ->latest('id')
            ->limit(10)
            ->get()
            ->map(fn (ActivityLog $log) => [
                'id' => $log->id,
                'event' => $log->event,
                'description' => $log->description,
                'user' => $log->user?->name,
                'role' => $log->user?->role,
                'created_at' => $log->created_at?->diffForHumans(),
            ]);

        return response()->json([
            'latest_id' => $logs->max('id') ?? $afterId,
            'notifications' => $logs,
        ]);
    }
}
