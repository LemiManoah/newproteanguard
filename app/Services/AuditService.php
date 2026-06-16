<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;
use InvalidArgumentException;

class AuditService
{
    public function record(string $action, ?User $user = null, ?int $businessId = null): AuditLog
    {
        $resolvedBusinessId = $businessId ?? $user?->businessId;

        if (! $resolvedBusinessId) {
            throw new InvalidArgumentException('A business context is required to write an audit log.');
        }

        $log = new AuditLog;
        $log->forceFill([
            'action' => $action,
            'userId' => $user?->getKey(),
            'businessId' => $resolvedBusinessId,
            'status' => true,
        ]);
        $log->save();

        return $log;
    }
}
