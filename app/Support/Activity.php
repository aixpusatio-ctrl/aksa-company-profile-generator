<?php

namespace App\Support;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Throwable;

class Activity
{
    /**
     * Record an entry in the activity log (Admin → System Logs).
     */
    public static function log(string $action, string $description, ?Model $subject = null, array $properties = [], ?int $userId = null): ?ActivityLog
    {
        try {
            $request = request();

            return ActivityLog::query()->create([
                'user_id' => $userId ?? Auth::id(),
                'subject_type' => $subject?->getMorphClass(),
                'subject_id' => $subject?->getKey(),
                'action' => $action,
                'description' => Str::limit($description, 250),
                'properties' => $properties ?: null,
                'ip_address' => $request?->ip(),
                'user_agent' => Str::limit((string) $request?->userAgent(), 250, ''),
            ]);
        } catch (Throwable $e) {
            report($e);

            return null;
        }
    }
}
