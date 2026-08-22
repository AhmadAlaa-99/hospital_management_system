<?php

namespace App\Http\Controllers\Dashboard_Patient;

use App\Http\Controllers\Controller;
use App\Models\FollowUpPlan;
use Illuminate\Support\Facades\Auth;

class FollowUpPlanController extends Controller
{
    public function index()
    {
        $plans = FollowUpPlan::with(['doctor', 'section'])
            ->where('patient_id', Auth::guard('patient')->id())
            ->orderBy('follow_up_date')
            ->paginate(20);

        return view('Dashboard.dashboard_patient.follow_ups.index', compact('plans'));
    }
}
