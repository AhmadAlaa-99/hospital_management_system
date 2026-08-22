<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;

class AuditLogService
{
    public static function log(string $action, ?Model $model = null, ?array $oldValues = null, ?array $newValues = null): ActivityLog
    {
        $userId = auth()->id();
        $userType = NotificationService::currentUserType();

        if ($userType === 'user' && !auth()->check()) {
            $userId = null;
            $userType = null;
        }

        return ActivityLog::create([
            'user_id' => $userId,
            'user_type' => $userType !== 'user' ? $userType : null,
            'action' => $action,
            'model_type' => $model ? get_class($model) : null,
            'model_id' => $model ? $model->getKey() : null,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => Request::ip(),
        ]);
    }
}
