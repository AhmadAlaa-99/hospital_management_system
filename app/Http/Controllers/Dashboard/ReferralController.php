<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Referral;

class ReferralController extends Controller
{
    public function index()
    {
        $referrals = Referral::with(['patient', 'fromDoctor', 'toDoctor', 'fromSection', 'toSection'])
            ->latest()
            ->paginate(20);

        return view('Dashboard.Referrals.index', compact('referrals'));
    }
}
