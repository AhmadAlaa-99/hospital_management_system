<?php

namespace App\Services;

use App\Models\AmbulanceRequest;
use App\Models\AmbulanceRequestTimeline;

class AmbulanceWorkflowService
{
    public static function recordTimeline(AmbulanceRequest $request, string $status, ?string $notes = null): AmbulanceRequestTimeline
    {
        $request->update(['status' => $status]);

        return AmbulanceRequestTimeline::create([
            'ambulance_request_id' => $request->id,
            'status' => $status,
            'notes' => $notes,
            'recorded_at' => now(),
        ]);
    }

    public static function initialTimeline(AmbulanceRequest $request): void
    {
        AmbulanceRequestTimeline::create([
            'ambulance_request_id' => $request->id,
            'status' => 'pending',
            'notes' => 'تم استلام الطلب',
            'recorded_at' => now(),
        ]);
    }
}
