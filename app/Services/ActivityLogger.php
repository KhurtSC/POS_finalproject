<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request as RequestFacade;

class ActivityLogger
{
    /**
     * Log an activity event.
     *
     * @param  string       $event       e.g. 'login', 'sale.created', 'sale.voided', 'product.updated'
     * @param  string|null  $description Human-readable sentence describing what happened.
     * @param  Model|null   $subject     The Eloquent model the event relates to (optional).
     * @param  array        $context     Any extra JSON payload (old/new values, reasons, etc.).
     */
    public static function log(
        string $event,
        ?string $description = null,
        ?Model $subject = null,
        array $context = []
    ): ActivityLog {
        return ActivityLog::create([
            'user_id'      => Auth::id(),
            'event'        => $event,
            'subject_type' => $subject ? get_class($subject) : null,
            'subject_id'   => $subject?->getKey(),
            'description'  => $description,
            'context'      => $context ?: null,
            'ip_address'   => RequestFacade::ip(),
            'user_agent'   => RequestFacade::userAgent(),
        ]);
    }
}