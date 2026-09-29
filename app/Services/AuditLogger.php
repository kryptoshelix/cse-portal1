<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request as RequestFacade;

class AuditLogger
{
    /**
     * Record a sensitive action. Never pass secrets/passwords/tokens in $meta.
     */
    public function log(string $action, string $description, ?Model $target = null, array $meta = []): AuditLog
    {
        $user = Auth::user();
        $request = request();

        return AuditLog::create([
            'actor_user_id' => $user?->id,
            'actor_name' => $user?->name ?? 'guest',
            'action' => $action,
            'auditable_type' => $target ? get_class($target) : null,
            'auditable_id' => $target?->getKey(),
            'description' => mb_substr($description, 0, 255),
            'meta' => $meta ?: null,
            'ip_address' => $request?->ip(),
            'user_agent' => mb_substr((string) $request?->userAgent(), 0, 255),
        ]);
    }
}
