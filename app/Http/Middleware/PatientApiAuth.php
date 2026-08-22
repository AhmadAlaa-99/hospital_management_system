<?php

namespace App\Http\Middleware;

use App\Models\PatientApiToken;
use Closure;
use Illuminate\Http\Request;

class PatientApiAuth
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken() ?: $request->header('X-Patient-Token');

        if (!$token) {
            return response()->json(['message' => 'Token required'], 401);
        }

        $apiToken = PatientApiToken::where('token', $token)->first();

        if (!$apiToken || !$apiToken->isValid()) {
            return response()->json(['message' => 'Invalid or expired token'], 401);
        }

        $apiToken->update(['last_used_at' => now()]);
        $request->attributes->set('patient', $apiToken->patient);
        $request->attributes->set('patient_id', $apiToken->patient_id);

        return $next($request);
    }
}
